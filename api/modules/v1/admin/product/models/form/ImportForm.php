<?php

namespace api\modules\v1\admin\product\models\form;

use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\base\Model;
use yii\web\UploadedFile;

abstract class ImportForm extends Model
{
    /** @var UploadedFile */
    public $file;

    protected array $allowedColumns = [];

    /**
     * Map tên cột đã lowercase => tên cột gốc trong file Excel.
     *
     * Header bị lowercase để `Name` và `name` đều khớp, nhưng có loại cột mà chính tên cột là
     * nhãn hiển thị (vd `attr_CPU`, `attr_SIM Slot`) — chỗ đó cần chữ hoa/thường nguyên bản.
     */
    protected array $headerLabels = [];

    public function rules(): array
    {
        return [
            [['file'], 'required'],
            [['file'], 'file', 'extensions' => ['xlsx', 'xls'], 'maxSize' => 5 * 1024 * 1024],
        ];
    }

    public function import(): array
    {
        $result = ['success' => 0, 'skipped' => 0, 'errors' => []];

        $spreadsheet = IOFactory::load($this->file->tempName);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (empty($rows)) {
            $result['errors'][] = 'File Excel trống.';
            return $result;
        }

        // Ô tiêu đề trống thì PhpSpreadsheet trả NULL, mà `trim(null)` là deprecated ở PHP 8.1 —
        // Yii biến deprecation thành ErrorException nên cả file import chết với thông báo
        // "trim(): Passing null to parameter #1". Xảy ra khi bảng tính có cột rộng hơn hàng tiêu đề:
        // dán giá trị vào cột chưa đặt tên, hoặc xoá tên cột mà còn để lại dữ liệu.
        $rawHeader = array_map(static function ($value): string {
            return trim((string)$value);
        }, array_shift($rows));
        $header = array_map('strtolower', $rawHeader);
        $this->headerLabels = array_combine($header, $rawHeader);
        $columnMap = array_flip($header);

        // Cột không có tiêu đề thì bỏ hẳn, đừng sinh key rỗng trong $data.
        unset($this->headerLabels[''], $columnMap['']);
        foreach ($this->allowedColumns as $col) {
            if (!isset($columnMap[$col])) {
                $result['errors'][] = "File Excel thiếu cột bắt buộc: \"{$col}\".";
                return $result;
            }
        }

        $rowIndex = 1;
        foreach ($rows as $row) {
            $rowIndex++;
            $data = [];
            foreach ($columnMap as $field => $letter) {
                $data[$field] = trim((string)($row[$letter] ?? ''));
            }

            if (empty(array_filter($data))) {
                $result['skipped']++;
                continue;
            }

            $error = $this->processRow($data, $rowIndex);
            if ($error) {
                $result['errors'][] = "Dòng {$rowIndex}: {$error}";
            } else {
                $result['success']++;
            }
        }

        return $result;
    }

    abstract protected function processRow(array $data, int $rowIndex): ?string;
}
