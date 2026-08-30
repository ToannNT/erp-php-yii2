<?php

namespace api\modules\v1\admin\product\models\form;

use api\modules\v1\admin\product\models\Product;
use api\modules\v1\admin\product\models\ProductVariant;
use common\models\Brand;
use common\models\Category;
use Throwable;
use Yii;
use yii\helpers\Html;

class ProductImportForm extends ImportForm
{
    /** Cột `attr_<nhãn>` — mỗi cột là 1 dòng trong bảng thông số kỹ thuật. */
    private const PREFIX_ATTR = 'attr_';

    /** Cột `html_<tên khối>` — dán nguyên khối HTML soạn sẵn (CKEditor). */
    private const PREFIX_HTML = 'html_';

    /** Tên khối gom tất cả cột `attr_*`, trùng với tên khối admin đang dùng. */
    private const BLOCK_SPECS = 'specs';

    /** Trần số ảnh lấy cho mỗi dòng — chặn file Excel dán nhầm cả trăm link. */
    private const MAX_IMAGES = 10;

    /** Giới hạn dung lượng 1 ảnh tải về (byte). */
    private const IMAGE_MAX_BYTES = 5242880;

    /** Timeout tải 1 ảnh (giây). */
    private const IMAGE_TIMEOUT = 15;

    private const USER_AGENT = 'erp-product-import/1.0';

    protected array $allowedColumns = ['name'];

    /** Số dòng tạo mới / cập nhật của lần import hiện tại. */
    private int $created = 0;
    private int $updated = 0;

    /** Cảnh báo không đủ nặng để chặn import (vd sản phẩm nhiều biến thể nên không đồng bộ giá). */
    private array $warnings = [];

    /** Lý do lần tải ảnh gần nhất thất bại, để ghép vào warning. */
    private ?string $lastFetchError = null;

    public function import(): array
    {
        $this->created = 0;
        $this->updated = 0;
        $this->warnings = [];

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $result = parent::import();
        } catch (Throwable $e) {
            $transaction->rollBack();
            return [
                'success' => 0, 'created' => 0, 'updated' => 0, 'skipped' => 0,
                'errors' => ['Import lỗi: ' . $e->getMessage()], 'warnings' => [], 'rolled_back' => true,
            ];
        }

        $result['created'] = $this->created;
        $result['updated'] = $this->updated;
        $result['warnings'] = $this->warnings;

        if (!empty($result['errors'])) {
            $transaction->rollBack();
            $result['success'] = 0;
            $result['created'] = 0;
            $result['updated'] = 0;
            $result['rolled_back'] = true;
        } else {
            $transaction->commit();
            $result['rolled_back'] = false;
        }

        return $result;
    }

    /**
     * Tra cứu id của category/brand: ưu tiên theo `code`, nếu trống thì fallback theo `name`.
     * @return int|null|false  int = id tìm thấy; null = không khai báo (bỏ qua); false = có khai báo nhưng không tồn tại.
     */
    private function resolveRef(string $modelClass, string $code, string $name, int $deleteStatus)
    {
        if ($code === '' && $name === '') {
            return null;
        }
        $query = $modelClass::find()->andWhere(['<>', 'status', $deleteStatus]);
        if ($code !== '') {
            $query->andWhere(['code' => $code]);
        } else {
            $query->andWhere(['name' => $name]);
        }
        $model = $query->one();
        return $model ? $model->id : false;
    }

    protected function processRow(array $data, int $rowIndex): ?string
    {
        $name = $data['name'] ?? '';
        if ($name === '') {
            return 'Cột "name" không được trống.';
        }

        $categoryId = $this->resolveRef(
            Category::class,
            $data['category_code'] ?? '',
            $data['category'] ?? '',
            Category::STATUS_DELETE
        );
        if ($categoryId === false) {
            return 'Category không tồn tại (code="' . ($data['category_code'] ?? '') . '", name="' . ($data['category'] ?? '') . '").';
        }

        $brandId = $this->resolveRef(
            Brand::class,
            $data['brand_code'] ?? '',
            $data['brand'] ?? '',
            Brand::STATUS_DELETE
        );
        if ($brandId === false) {
            return 'Brand không tồn tại (code="' . ($data['brand_code'] ?? '') . '", name="' . ($data['brand'] ?? '') . '").';
        }

        // `sku` là khoá nhận diện. Trống ⇒ luôn tạo mới (sku tự sinh).
        $sku = $data['sku'] ?? '';
        $existing = $sku === '' ? null : ProductForm::find()
            ->where(['sku' => $sku])
            ->andWhere(['<>', 'status', Product::STATUS_DELETE])
            // `product.sku` không có unique index, lỡ có 2 bản ghi trùng sku thì không ORDER BY
            // sẽ update trúng bản ghi nào tuỳ MySQL. Chốt luôn bản cũ nhất cho tất định.
            ->orderBy(['id' => SORT_ASC])
            ->one();

        try {
            return $existing
                ? $this->updateProduct($existing, $data, $rowIndex, $categoryId, $brandId)
                : $this->createProduct($data, $rowIndex, $name, $sku, $categoryId, $brandId);
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param int|null $categoryId
     * @param int|null $brandId
     */
    private function createProduct(array $data, int $rowIndex, string $name, string $sku, $categoryId, $brandId): ?string
    {
        if ($sku === '') {
            $sku = $this->generateUnique('sku', 100000000, 999999999);
        }
        $barCode = $data['bar_code'] ?? '';
        if ($barCode === '') {
            $barCode = $this->generateUnique('bar_code', 1000000000, 9999999999);
        }

        $form = new ProductForm();
        $form->setScenario(ProductForm::SCENARIO_CREATE);
        $form->setAttributes([
            'name'              => $name,
            'sku'               => $sku,
            'type'              => Product::TYPE_PRODUCT,
            'bar_code'          => $barCode,
            'category_id'       => $categoryId,
            'brand_id'          => $brandId,
            'unit_price'        => $this->toFloat($data['unit_price'] ?? null),
            'sll_price'         => $this->toFloat($data['sll_price'] ?? null),
            'compare_price'     => $this->toFloat($data['compare_price'] ?? null),
            'import_price'      => $this->toFloat($data['import_price'] ?? null),
            'weight'            => $this->toFloat($data['weight'] ?? null),
            'weight_type'       => $data['weight_type'] ?? '',
            'dimension'         => $data['dimension'] ?? '',
            'short_description' => $data['short_description'] ?? null,
            'description'       => $data['description'] ?? null,
            'tags'              => $this->parseTags($data) ?? [],
            'images'            => $this->resolveImages($data, $rowIndex) ?? [],
            'additional_data'   => $this->buildAdditionalData($data),
            'allow_sell'        => $this->toInt($data['allow_sell'] ?? null, Product::STATUS_INACTIVE),
            'status'            => $this->toInt($data['status'] ?? null, Product::STATUS_ACTIVE),
        ]);

        if (!$form->validate() || !$form->save()) {
            return implode(', ', $form->getFirstErrors());
        }
        $form->updateOrCreateTags();
        if (!$form->initVariants()) {
            return implode(', ', $form->getFirstErrors());
        }

        $this->created++;
        return null;
    }

    /**
     * Cập nhật sản phẩm đã có (trùng `sku`).
     *
     * Quy tắc: **ô trống = giữ nguyên**, chỉ ghi đè cột nào thực sự có giá trị. Người dùng thường
     * chỉ điền vài cột cần sửa, nếu lấy cả ô trống thì giá và mô tả của hàng cũ bị xoá sạch.
     *
     * @param int|null $categoryId
     * @param int|null $brandId
     */
    private function updateProduct(ProductForm $product, array $data, int $rowIndex, $categoryId, $brandId): ?string
    {
        $values = [];
        foreach (['name', 'bar_code', 'weight_type', 'dimension', 'short_description', 'description'] as $column) {
            if (($data[$column] ?? '') !== '') {
                $values[$column] = $data[$column];
            }
        }
        foreach (['unit_price', 'sll_price', 'compare_price', 'import_price', 'weight'] as $column) {
            if (($data[$column] ?? '') !== '') {
                $values[$column] = (float)$data[$column];
            }
        }
        foreach (['allow_sell', 'status'] as $column) {
            if (($data[$column] ?? '') !== '') {
                $values[$column] = (int)$data[$column];
            }
        }
        if ($categoryId !== null) {
            $values['category_id'] = $categoryId;
        }
        if ($brandId !== null) {
            $values['brand_id'] = $brandId;
        }

        $tags = $this->parseTags($data);
        if ($tags !== null) {
            $values['tags'] = $tags;
        }
        $images = $this->resolveImages($data, $rowIndex);
        if ($images !== null) {
            $values['images'] = $images;
        }
        $additionalData = $this->buildAdditionalData($data);
        if ($additionalData) {
            $values['additional_data'] = $additionalData;
        }

        $product->setAttributes($values);
        if (!$product->validate() || !$product->save()) {
            return implode(', ', $product->getFirstErrors());
        }
        if ($tags !== null) {
            $product->updateOrCreateTags();
        }

        // Chỉ những field mà biến thể cũng giữ bản sao.
        $variantValues = array_intersect_key(
            $values,
            array_flip(['unit_price', 'sll_price', 'compare_price', 'import_price', 'images'])
        );
        $error = $this->syncVariant($product, $variantValues, $rowIndex);
        if ($error !== null) {
            return $error;
        }

        $this->updated++;
        return null;
    }

    /**
     * Giá thật khách trả và ảnh đều có bản sao ở `product_variant`, sửa mỗi `product` thì web
     * vẫn hiển thị dữ liệu cũ.
     *
     * Chỉ đồng bộ khi sản phẩm có **đúng 1 biến thể** (hàng do import tạo ra). Nhiều biến thể thì
     * không đoán được nên sửa cái nào — bỏ qua và ghi vào `warnings` để người import biết.
     */
    private function syncVariant(ProductForm $product, array $values, int $rowIndex): ?string
    {
        if (!$values) {
            return null;
        }

        $variants = ProductVariantForm::find()
            ->where(['product_id' => $product->id])
            ->andWhere(['<>', 'status', ProductVariant::STATUS_DELETE])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        if (count($variants) !== 1) {
            $this->warnings[] = sprintf(
                'Dòng %d: sản phẩm "%s" có %d biến thể — chỉ cập nhật giá ở sản phẩm, không đụng biến thể.',
                $rowIndex,
                $product->name,
                count($variants)
            );
            return null;
        }

        $variant = $variants[0];
        $variant->setAttributes($values);
        if (!$variant->validate() || !$variant->save()) {
            return 'Không cập nhật được biến thể: ' . implode(', ', $variant->getFirstErrors());
        }
        return null;
    }

    /**
     * Đọc cột `images` (danh sách URL cách nhau bởi dấu phẩy), tải từng ảnh về rồi đẩy lên
     * `fileStorage` của hệ thống. Trả về mảng URL đầy đủ — đúng shape đang lưu trong DB.
     *
     * Cố tình KHÔNG giữ link gốc: hotlink sang server người khác thì họ đổi/xoá ảnh là trang
     * sản phẩm vỡ. Ảnh vốn đã nằm trên storage của mình thì bỏ qua, không tải lại.
     *
     * Ảnh nào lỗi thì bỏ qua và ghi `warnings` — không làm hỏng cả file import vì 1 link chết.
     *
     * @return string[]|null null = cột `images` không có hoặc trống ⇒ không đụng ảnh hiện có.
     */
    private function resolveImages(array $data, int $rowIndex): ?array
    {
        $raw = $data['images'] ?? '';
        if ($raw === '') {
            return null;
        }
        $urls = array_values(array_unique(array_filter(array_map('trim', explode(',', $raw)))));
        if (!$urls) {
            return null;
        }
        if (count($urls) > self::MAX_IMAGES) {
            $this->warnings[] = sprintf(
                'Dòng %d: có %d ảnh, chỉ lấy %d ảnh đầu.',
                $rowIndex,
                count($urls),
                self::MAX_IMAGES
            );
            $urls = array_slice($urls, 0, self::MAX_IMAGES);
        }

        $stored = [];
        foreach ($urls as $url) {
            $saved = $this->storeRemoteImage($url, $rowIndex);
            if ($saved !== null) {
                $stored[] = $saved;
            }
        }
        return $stored;
    }

    private function storeRemoteImage(string $url, int $rowIndex): ?string
    {
        if (!preg_match('#^https?://#i', $url)) {
            $this->warnings[] = "Dòng {$rowIndex}: bỏ qua ảnh không phải http(s) — {$url}";
            return null;
        }

        $storage = Yii::$app->has('fileStorage') ? Yii::$app->get('fileStorage') : null;
        if ($storage === null) {
            $this->warnings[] = "Dòng {$rowIndex}: chưa cấu hình fileStorage, giữ nguyên link gốc — {$url}";
            return $url;
        }

        $baseUrl = rtrim((string)$storage->baseUrl, '/');
        if ($baseUrl !== '' && strncmp($url, $baseUrl . '/', strlen($baseUrl) + 1) === 0) {
            return $url;
        }

        $content = $this->fetchUrl($url);
        if ($content === null) {
            $reason = $this->lastFetchError !== null ? " ({$this->lastFetchError})" : '';
            $this->warnings[] = "Dòng {$rowIndex}: tải ảnh thất bại{$reason} — {$url}";
            return null;
        }
        if (strlen($content) > self::IMAGE_MAX_BYTES) {
            $this->warnings[] = "Dòng {$rowIndex}: ảnh nặng hơn 5MB, bỏ qua — {$url}";
            return null;
        }

        $size = @getimagesizefromstring($content);
        $extension = $size ? image_type_to_extension($size[2], false) : null;
        if (!$extension) {
            $this->warnings[] = "Dòng {$rowIndex}: file không phải ảnh hợp lệ — {$url}";
            return null;
        }
        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }

        $tempPath = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR
            . uniqid('import_image_', true) . '.' . $extension;
        if (file_put_contents($tempPath, $content) === false) {
            $this->warnings[] = "Dòng {$rowIndex}: không ghi được file tạm cho ảnh — {$url}";
            return null;
        }

        try {
            $path = $storage->save($tempPath);
        } finally {
            @unlink($tempPath);
        }

        if (!$path) {
            $this->warnings[] = "Dòng {$rowIndex}: lưu ảnh lên storage thất bại — {$url}";
            return null;
        }
        // filekit ghép path bằng DIRECTORY_SEPARATOR ⇒ chạy trên Windows sẽ ra "uploads/1\abc.png".
        return $baseUrl . '/' . str_replace('\\', '/', $path);
    }

    /**
     * Tải nội dung 1 URL, ưu tiên cURL (kiểm soát timeout và redirect tốt hơn `file_get_contents`).
     *
     * Lý do thất bại ghi vào {@see $lastFetchError} để warning nói rõ ảnh hỏng vì đâu — "tải ảnh
     * thất bại" trống không thì không debug được: 403 chặn hotlink, timeout, hay SSL đều ra một chữ.
     */
    private function fetchUrl(string $url): ?string
    {
        $this->lastFetchError = null;

        if (function_exists('curl_init')) {
            $curl = curl_init($url);
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 3,
                CURLOPT_TIMEOUT => self::IMAGE_TIMEOUT,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_USERAGENT => self::USER_AGENT,
            ]);
            $content = curl_exec($curl);
            $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($content === false) {
                $this->lastFetchError = curl_error($curl) ?: 'cURL lỗi';
                return null;
            }
            if ($status >= 400) {
                // Nhiều site chặn tải ảnh trực tiếp bằng 403, dù mở bằng trình duyệt vẫn thấy.
                $this->lastFetchError = "HTTP {$status}";
                return null;
            }
            if ($content === '') {
                $this->lastFetchError = 'nội dung rỗng';
                return null;
            }
            return $content;
        }

        $context = stream_context_create(['http' => [
            'timeout' => self::IMAGE_TIMEOUT,
            'user_agent' => self::USER_AGENT,
        ]]);
        $content = @file_get_contents($url, false, $context);
        if ($content === false || $content === '') {
            $this->lastFetchError = 'file_get_contents thất bại';
            return null;
        }
        return $content;
    }

    /**
     * @return string[]|null null = cột `tags` không có hoặc để trống ⇒ không đụng tới tag hiện có.
     */
    private function parseTags(array $data): ?array
    {
        if (empty($data['tags'])) {
            return null;
        }
        return array_values(array_filter(array_map('trim', explode(',', $data['tags']))));
    }

    /**
     * Dựng `additional_data` từ 2 nhóm cột động, đúng cấu trúc admin đang lưu:
     * `[{"name": "specs", "value": "<html>"}, ...]`
     *
     * - `attr_<nhãn>`: mỗi cột là 1 dòng thông số, gom hết thành 1 bảng tên `specs`.
     *   HTML sinh ra theo đúng format CKEditor 5 (`<figure class="table">`) để mở lại sửa được.
     * - `html_<tên khối>`: dán nguyên khối HTML đã soạn sẵn, giữ y nguyên (chỉ lọc mã độc).
     *
     * Không có cột nào ⇒ trả `[]`, giống sản phẩm chưa nhập thông tin bổ sung.
     */
    private function buildAdditionalData(array $data): array
    {
        $blocks = [];

        $rows = '';
        foreach ($data as $column => $value) {
            if ($value === '' || strncmp($column, self::PREFIX_ATTR, strlen(self::PREFIX_ATTR)) !== 0) {
                continue;
            }
            $label = $this->columnLabel($column, self::PREFIX_ATTR);
            $rows .= '<tr><td>' . Html::encode($label) . '</td><td>' . Html::encode($value) . '</td></tr>';
        }
        if ($rows !== '') {
            $blocks[] = [
                'name' => self::BLOCK_SPECS,
                'value' => '<figure class="table"><table><tbody>' . $rows . '</tbody></table></figure>',
            ];
        }

        foreach ($data as $column => $value) {
            if ($value === '' || strncmp($column, self::PREFIX_HTML, strlen(self::PREFIX_HTML)) !== 0) {
                continue;
            }
            $html = $this->sanitizeHtml($value);
            if ($html === '') {
                continue;
            }
            $blocks[] = [
                'name' => $this->columnLabel($column, self::PREFIX_HTML),
                'value' => $html,
            ];
        }

        return $blocks;
    }

    /**
     * Lấy phần sau prefix của tên cột, ưu tiên tên gốc trong file để giữ chữ hoa/thường
     * (`attr_CPU` ⇒ `CPU`, không phải `cpu`).
     */
    private function columnLabel(string $column, string $prefix): string
    {
        $label = substr($this->headerLabels[$column] ?? $column, strlen($prefix));
        return $label !== '' ? $label : $column;
    }

    /**
     * Lọc mã có thể chạy được khỏi HTML dán từ ngoài — nội dung này được render bằng `v-html`.
     *
     * Cố tình KHÔNG dùng `HtmlPurifier`: cấu hình mặc định (doctype HTML 4.01) xoá sạch
     * `<figure>` / `<figcaption>` — đúng thẻ CKEditor 5 dùng để bọc bảng và ảnh, mất luôn định dạng.
     */
    private function sanitizeHtml(string $html): string
    {
        // cặp thẻ nguy hiểm kèm nội dung bên trong, rồi tới thẻ lẻ còn sót
        $html = preg_replace('#<\s*(script|style|iframe|object|embed)\b[^>]*>.*?<\s*/\s*\1\s*>#is', '', $html) ?? $html;
        $html = preg_replace('#<\s*/?\s*(script|style|iframe|object|embed)\b[^>]*>#i', '', $html) ?? $html;

        // Chỉ đụng vào phần BÊN TRONG thẻ. Quét thẳng cả chuỗi sẽ ăn nhầm text hiển thị:
        // một ô thông số ghi "Chế độ once = bật" khớp luôn pattern `\son[a-z]+\s*=`.
        return trim(preg_replace_callback('#<[a-z][^>]*>#i', static function (array $match): string {
            // handler onclick, onerror, onload...
            $tag = preg_replace('#\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $match[0]) ?? $match[0];
            // href/src trỏ tới javascript:
            return preg_replace(
                '#\s(?:href|src)\s*=\s*("\s*javascript:[^"]*"|\'\s*javascript:[^\']*\'|javascript:[^\s>]+)#i',
                '',
                $tag
            ) ?? $tag;
        }, $html) ?? $html);
    }

    private function generateUnique(string $column, int $min, int $max): string
    {
        do {
            $value = (string)random_int($min, $max);
        } while (Product::find()->where([$column => $value])->exists());
        return $value;
    }

    private function toFloat($value): float
    {
        return ($value === null || $value === '') ? 0 : (float)$value;
    }

    private function toInt($value, int $default): int
    {
        return ($value === null || $value === '') ? $default : (int)$value;
    }
}
