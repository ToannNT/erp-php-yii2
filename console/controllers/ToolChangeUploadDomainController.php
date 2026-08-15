<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Đổi domain trong các URL ảnh ĐÃ LƯU trong DB, dùng sau khi đổi domain API.
 *
 * Bối cảnh: `product.images`, `category.icon`, ... lưu URL tuyệt đối
 * (`https://<domain>/uploads/1/abc.png`), nên đổi `UPLOAD_BASE_URL` chỉ ảnh hưởng
 * ảnh upload MỚI — ảnh cũ vẫn trỏ domain cũ và sẽ 404 khi domain cũ bị gỡ.
 *
 * Quét toàn bộ cột kiểu chuỗi/JSON trong DB hiện tại (kể cả bảng CMS động do
 * `system_cms_collection` sinh ra ở runtime) nên không cần liệt kê bảng bằng tay.
 *
 * QUAN TRỌNG — chỉ so khớp phần HOST, không so khớp cả URL:
 * `JsonBehavior::encode()` gọi `json_encode()` không kèm `JSON_UNESCAPED_SLASHES`,
 * nên `product.images` nằm trong DB dưới dạng
 *     ["https:\/\/domain-cu\/uploads\/1\/abc.jpg"]
 * Tìm theo chuỗi `https://domain-cu` sẽ KHÔNG khớp vì dấu `/` đã bị escape.
 * Host không chứa `/` nên khớp theo host đúng cho cả JSON đã escape, HTML trong
 * `product.description`, lẫn cột chuỗi thường.
 *
 * Mặc định DRY-RUN, phải thêm `--apply` mới thực sự ghi.
 *
 *   php console/yii tool-change-upload-domain/scan-hosts
 *   php console/yii tool-change-upload-domain/run --from=https://old.xyz --to=https://abc.xyz
 *   php console/yii tool-change-upload-domain/run --from=https://old.xyz --to=https://abc.xyz --apply
 *
 * Nhớ backup DB trước khi `--apply` (scripts/backup/backup-db.sh).
 */
class ToolChangeUploadDomainController extends Controller
{
    /**
     * Domain cũ. Nhận cả `https://old.xyz/uploads` lẫn `old.xyz` — chỉ phần host được dùng.
     * @var string
     */
    public $from;

    /**
     * Domain mới, cùng định dạng với `--from`.
     * @var string
     */
    public $to;

    /**
     * Không có cờ này thì chỉ dry-run.
     * @var bool
     */
    public $apply = false;

    /**
     * Các kiểu cột có thể chứa URL.
     */
    const SCANNED_TYPES = ['varchar', 'char', 'text', 'tinytext', 'mediumtext', 'longtext', 'json'];

    public function options($actionID)
    {
        return ['from', 'to', 'apply'];
    }

    public function optionAliases()
    {
        return ['f' => 'from', 't' => 'to'];
    }

    /**
     * Liệt kê MỌI host đang xuất hiện trong URL lưu ở DB, kèm số lần và cột chứa nó.
     *
     * Chạy trước `run` để biết thực tế có bao nhiêu domain cần đổi: dự án từng chạy
     * Cloudflare R2 (common/components/filesystem/ClouldflareR2.php) và dev cũng ghi vào
     * cùng DB, nên ảnh cũ có thể trỏ nhiều host khác nhau. `run` chỉ thay host truyền vào.
     *
     *   php console/yii tool-change-upload-domain/scan-hosts
     */
    public function actionScanHosts()
    {
        $db = Yii::$app->db;
        $columns = $this->findScannableColumns();

        $this->stdout("Quét " . count($columns) . " cột tìm URL...\n\n");

        $hosts = [];
        foreach ($columns as $column) {
            $table = $column['TABLE_NAME'];
            $name = $column['COLUMN_NAME'];

            // Lọc thô bằng `http`: `%://%` không bắt được JSON đã escape (`:\/\/`), mà viết
            // pattern cho dạng escaped thì phải nhân đôi backslash hai tầng (string literal +
            // escape của LIKE) rất dễ sai. Cả hai dạng đều chứa `http`, regex bên dưới lọc tiếp.
            $sql = "SELECT `$name` FROM `$table` WHERE `$name` LIKE '%http%'";

            // Đọc theo stream, cột như product.description chứa HTML rất nặng.
            foreach ($db->createCommand($sql)->query() as $row) {
                $value = $this->unescapeSlashes((string)reset($row));
                if (!preg_match_all('~https?://([^/"\'\s\\\\)\]},]+)~i', $value, $matches)) {
                    continue;
                }
                foreach ($matches[1] as $host) {
                    $host = strtolower($host);
                    $hosts[$host]['count'] = ($hosts[$host]['count'] ?? 0) + 1;
                    $hosts[$host]['columns']["$table.$name"] = true;
                }
            }
        }

        if (!$hosts) {
            $this->stdout("Không tìm thấy URL nào trong DB.\n", Console::FG_YELLOW);
            return ExitCode::OK;
        }

        uasort($hosts, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        foreach ($hosts as $host => $info) {
            $this->stdout(sprintf("  %-55s %7d lần\n", $host, $info['count']));
            $this->stdout("      " . implode(', ', array_keys($info['columns'])) . "\n");
        }

        $this->stdout("\nTổng " . count($hosts) . " host. Mỗi host cần đổi phải chạy `run` riêng.\n");

        return ExitCode::OK;
    }

    public function actionRun()
    {
        $from = $this->parseTarget((string)$this->from);
        $to = $this->parseTarget((string)$this->to);

        if (!$from || !$to) {
            $this->stderr("Thiếu hoặc sai --from / --to (vd: --from=https://old.xyz --to=https://abc.xyz)\n", Console::FG_RED);
            return ExitCode::USAGE;
        }
        if ($from['host'] === $to['host'] && $from['scheme'] === $to['scheme']) {
            $this->stderr("--from và --to giống nhau, không có gì để làm\n", Console::FG_RED);
            return ExitCode::USAGE;
        }

        $db = Yii::$app->db;
        $replacements = $this->buildReplacements($from, $to);
        // `_` và `%` là wildcard của LIKE, host có thể chứa `_` → phải escape.
        $like = '%' . addcslashes($from['host'], '%_\\') . '%';

        $this->stdout("DB      : " . $db->createCommand('SELECT DATABASE()')->queryScalar() . "\n");
        $this->stdout("From    : {$from['host']}\n");
        $this->stdout("To      : {$to['host']}\n");
        foreach ($replacements as $pair) {
            $this->stdout("Thay    : {$pair[0]}  ->  {$pair[1]}\n");
        }
        $this->stdout("Mode    : " . ($this->apply ? "APPLY" : "DRY-RUN") . "\n\n");

        $columns = $this->findScannableColumns();
        $this->stdout("Quét " . count($columns) . " cột...\n\n");

        // Đếm trước trên toàn bộ cột để biết phạm vi trước khi ghi bất cứ gì.
        $targets = [];
        $totalRows = 0;
        foreach ($columns as $column) {
            $table = $column['TABLE_NAME'];
            $name = $column['COLUMN_NAME'];
            $count = (int)$db->createCommand(
                "SELECT COUNT(*) FROM `$table` WHERE `$name` LIKE :like",
                [':like' => $like]
            )->queryScalar();

            if ($count > 0) {
                $targets[] = $column + ['count' => $count];
                $totalRows += $count;
                $this->stdout(sprintf("  %-45s %6d dòng\n", "$table.$name", $count));
            }
        }

        if (!$targets) {
            $this->stdout("\nKhông tìm thấy dòng nào chứa host cũ. Không cần làm gì.\n", Console::FG_GREEN);
            return ExitCode::OK;
        }

        $this->stdout("\nTổng: $totalRows dòng trên " . count($targets) . " cột.\n");

        if (!$this->apply) {
            $this->stdout("Dry-run — chưa ghi gì. Thêm --apply để chạy thật.\n", Console::FG_YELLOW);
            return ExitCode::OK;
        }

        $transaction = $db->beginTransaction();
        try {
            $updated = 0;
            $this->stdout("\n");
            foreach ($targets as $target) {
                $updated += $this->replaceInColumn($target, $replacements, $like);
            }
            $transaction->commit();
            $this->stdout("\nXong: đã cập nhật $updated dòng.\n", Console::FG_GREEN);
            return ExitCode::OK;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->stderr("\nLỗi, đã rollback: " . $e->getMessage() . "\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Tách scheme + host từ giá trị người dùng nhập.
     * Chấp nhận `https://old.xyz/uploads`, `https://old.xyz`, `old.xyz`.
     */
    protected function parseTarget(string $value): ?array
    {
        $value = trim($value);
        $scheme = null;

        if (preg_match('~^(https?)://~i', $value, $matches)) {
            $scheme = strtolower($matches[1]);
            $value = substr($value, strlen($matches[0]));
        }

        $host = strtolower(explode('/', $value)[0]);

        return $host === '' ? null : compact('scheme', 'host');
    }

    /**
     * Danh sách cặp [tìm, thay] áp dụng lồng nhau theo thứ tự.
     *
     * Cặp đầu đổi host — host không có `/` nên khớp được cả URL thường lẫn URL
     * đã bị `json_encode()` escape thành `https:\/\/host\/...`.
     * Chỉ khi scheme đổi (http -> https) mới cần thêm cặp cho scheme, và phải xử lý
     * cả hai dạng vì lúc này host đã mang giá trị mới.
     */
    protected function buildReplacements(array $from, array $to): array
    {
        $replacements = [[$from['host'], $to['host']]];

        if ($from['scheme'] && $to['scheme'] && $from['scheme'] !== $to['scheme']) {
            $replacements[] = ["{$from['scheme']}://{$to['host']}", "{$to['scheme']}://{$to['host']}"];
            $replacements[] = ["{$from['scheme']}:\\/\\/{$to['host']}", "{$to['scheme']}:\\/\\/{$to['host']}"];
        }

        return $replacements;
    }

    /**
     * Cột JSON native phải CAST qua CHAR rồi CAST ngược, gán thẳng chuỗi sẽ lỗi kiểu.
     * Các cột `images` hiện tại là TEXT nên đi nhánh thường.
     */
    protected function replaceInColumn(array $column, array $replacements, string $like): int
    {
        $table = $column['TABLE_NAME'];
        $name = $column['COLUMN_NAME'];
        $isJson = $column['DATA_TYPE'] === 'json';

        $expression = $isJson ? "CAST(`$name` AS CHAR)" : "`$name`";
        $params = [':like' => $like];

        foreach ($replacements as $i => $pair) {
            $expression = "REPLACE($expression, :search$i, :replace$i)";
            $params[":search$i"] = $pair[0];
            $params[":replace$i"] = $pair[1];
        }

        if ($isJson) {
            $expression = "CAST($expression AS JSON)";
        }

        $affected = Yii::$app->db->createCommand(
            "UPDATE `$table` SET `$name` = $expression WHERE `$name` LIKE :like",
            $params
        )->execute();

        $this->stdout(sprintf("  %-45s %6d dòng\n", "$table.$name", $affected));

        return $affected;
    }

    /**
     * Gỡ escape của `json_encode()` để regex bóc host chạy được trên giá trị JSON.
     */
    protected function unescapeSlashes(string $value): string
    {
        return str_replace('\\/', '/', $value);
    }

    /**
     * Mọi cột chuỗi/JSON của bảng thật trong DB hiện tại.
     * Bỏ generated column vì không UPDATE trực tiếp được.
     */
    protected function findScannableColumns(): array
    {
        $types = "'" . implode("','", self::SCANNED_TYPES) . "'";

        return Yii::$app->db->createCommand("
            SELECT c.TABLE_NAME, c.COLUMN_NAME, c.DATA_TYPE
            FROM information_schema.COLUMNS c
            JOIN information_schema.TABLES t
              ON t.TABLE_SCHEMA = c.TABLE_SCHEMA AND t.TABLE_NAME = c.TABLE_NAME
            WHERE c.TABLE_SCHEMA = DATABASE()
              AND t.TABLE_TYPE = 'BASE TABLE'
              AND c.DATA_TYPE IN ($types)
              AND c.EXTRA NOT LIKE '%GENERATED%'
            ORDER BY c.TABLE_NAME, c.COLUMN_NAME
        ")->queryAll();
    }
}
