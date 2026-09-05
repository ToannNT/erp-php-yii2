<?php

namespace api\modules\v1\admin\media\controllers;

use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\media\models\search\MediaSearch;
use common\models\FileStorageItem;
use Yii;
use yii\filters\AccessControl;
use yii\rest\Controller;
use yii\web\HttpException;

/**
 * Thư viện hình ảnh — liệt kê, xem, xoá file đã upload lên `fileStorage`.
 *
 * Upload vẫn dùng endpoint hiện tại `services/storage/upload`.
 *
 * Khi xoá, controller kiểm tra file có đang được tham chiếu trong các bảng chính không
 * (product, product_variant, category, brand, article, v.v.). Nếu có thì trả cảnh báo kèm
 * danh sách tham chiếu. Client gửi lại với `force=1` để xác nhận xoá.
 */
class GalleryController extends Controller
{
    /**
     * Các bảng & cột chứa tham chiếu ảnh cần kiểm tra trước khi xoá.
     *
     * Mỗi entry: [table, column, label hiển thị cho client].
     * Cột `images` chứa JSON array URL, `icon`/`thumbnail`/`avatar` chứa URL đơn.
     * Tất cả đều search bằng `LIKE '%path%'` trên cột string, không cần parse JSON.
     */
    private const IMAGE_REFERENCES = [
        ['product', 'images', 'Sản phẩm (images)'],
        ['product', 'icon', 'Sản phẩm (icon)'],
        ['product_variant', 'images', 'Biến thể sản phẩm'],
        ['category', 'images', 'Danh mục (images)'],
        ['category', 'icon', 'Danh mục (icon)'],
        ['brand', 'images', 'Thương hiệu (images)'],
        ['brand', 'icon', 'Thương hiệu (icon)'],
        ['supplier', 'images', 'Nhà cung cấp (images)'],
        ['supplier', 'icon', 'Nhà cung cấp (icon)'],
        ['article', 'thumbnail_path', 'Bài viết (thumbnail)'],
        ['banner', 'url', 'Banner'],
        ['comment', 'images', 'Bình luận'],
        ['cms_slider_top', 'thumbnail', 'Slider'],
    ];

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['administrator', 'manager'],
                ],
            ],
        ];
        return $behaviors;
    }

    /**
     * Liệt kê tất cả file trong thư viện.
     *
     * Query params: `name`, `type`, `created_from`, `created_to`, `page`, `per-page`.
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(
            true,
            (new MediaSearch())->search(Yii::$app->request->queryParams)
        );
    }

    /**
     * Xem chi tiết 1 file.
     *
     * @param int $id
     */
    public function actionView(int $id): array
    {
        $model = $this->findModel($id);
        return ResponseBuilder::responseJson(true, [
            'file' => $model->toArray(),
            'url' => rtrim($model->base_url, '/') . '/' . $model->path,
        ]);
    }

    /**
     * Xoá 1 file. Nếu file đang được tham chiếu thì trả cảnh báo.
     * Gửi lại với `force=1` để xác nhận xoá.
     *
     * @param int $id
     */
    public function actionDelete(int $id): array
    {
        $model = $this->findModel($id);
        $force = (bool) Yii::$app->request->get('force', Yii::$app->request->post('force'));

        if (!$force) {
            $references = $this->findReferences($model);
            if ($references) {
                return ResponseBuilder::responseJson(false, [
                    'references' => $references,
                    'file' => $model->toArray(),
                ], 'File đang được sử dụng. Gửi lại với force=1 để xác nhận xoá.', ApiConstant::STATUS_OK);
            }
        }

        return $this->deleteFile($model);
    }

    /**
     * Xoá nhiều file cùng lúc.
     *
     * Body: `{ "ids": [1, 2, 3], "force": false }`
     */
    public function actionBulkDelete(): array
    {
        $ids = (array) Yii::$app->request->post('ids', []);
        $force = (bool) Yii::$app->request->post('force');

        if (empty($ids)) {
            return ResponseBuilder::responseJson(false, null, 'Danh sách ids không được trống', ApiConstant::STATUS_BAD_REQUEST);
        }

        $models = FileStorageItem::find()->where(['id' => $ids])->all();
        if (empty($models)) {
            return ResponseBuilder::responseJson(false, null, 'Không tìm thấy file nào', ApiConstant::STATUS_NOT_FOUND);
        }

        // Nếu chưa confirm, kiểm tra tham chiếu của tất cả file
        if (!$force) {
            $allReferences = [];
            foreach ($models as $model) {
                $refs = $this->findReferences($model);
                if ($refs) {
                    $allReferences[] = [
                        'file_id' => $model->id,
                        'file_name' => $model->name,
                        'references' => $refs,
                    ];
                }
            }
            if ($allReferences) {
                return ResponseBuilder::responseJson(false, [
                    'references' => $allReferences,
                ], 'Một số file đang được sử dụng. Gửi lại với force=1 để xác nhận xoá.', ApiConstant::STATUS_OK);
            }
        }

        $deleted = 0;
        $errors = [];
        foreach ($models as $model) {
            $result = $this->deleteFile($model);
            if ($result['status']) {
                $deleted++;
            } else {
                $errors[] = "File #{$model->id}: {$result['messages']}";
            }
        }

        return ResponseBuilder::responseJson(true, [
            'deleted' => $deleted,
            'errors' => $errors,
        ], "Đã xoá {$deleted}/" . count($models) . ' file');
    }

    /**
     * Tìm tất cả nơi đang tham chiếu đến file này.
     *
     * Dùng `LIKE '%path%'` trên các cột ảnh. Cột `thumbnail_path` của `article` chỉ chứa
     * path tương đối (không có base_url), nên search bằng `path`. Các cột khác chứa full URL
     * nên ghép `base_url/path` rồi search.
     *
     * @return array Mảng các tham chiếu tìm thấy: `[['source' => 'Sản phẩm', 'count' => 3], ...]`
     */
    private function findReferences(FileStorageItem $file): array
    {
        $fullUrl = rtrim($file->base_url, '/') . '/' . $file->path;
        $references = [];

        foreach (self::IMAGE_REFERENCES as [$table, $column, $label]) {
            // article.thumbnail_path lưu path tương đối, không phải full URL
            $searchValue = ($column === 'thumbnail_path') ? $file->path : $fullUrl;

            try {
                $count = (int) Yii::$app->db->createCommand(
                    "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` LIKE :search",
                    [':search' => '%' . $searchValue . '%']
                )->queryScalar();
            } catch (\Exception $e) {
                // Bảng hoặc cột không tồn tại (schema khác) — bỏ qua
                continue;
            }

            if ($count > 0) {
                $references[] = [
                    'source' => $label,
                    'table' => $table,
                    'column' => $column,
                    'count' => $count,
                ];
            }
        }

        return $references;
    }

    /**
     * Xoá file trên storage + record trong DB.
     *
     * `Storage::delete()` trigger `EVENT_AFTER_DELETE` → `FileStorageLogBehavior::afterDelete()`
     * tự động xoá record trong `file_storage_item`. Nên KHÔNG gọi `$model->delete()` thêm lần nữa.
     *
     * Nếu file không tồn tại trên storage (đã bị xoá thủ công) thì xoá record DB trực tiếp.
     */
    private function deleteFile(FileStorageItem $model): array
    {
        /** @var \trntv\filekit\Storage $storage */
        $storage = Yii::$app->get('fileStorage');

        try {
            $deleted = $storage->delete($model->path);
        } catch (\Exception $e) {
            $deleted = false;
        }

        if ($deleted) {
            // Storage::delete() thành công → afterDelete behavior đã xoá record DB rồi
            return ResponseBuilder::responseJson(true, null, 'Xoá file thành công');
        }

        // File không tồn tại trên storage (đã bị xoá thủ công hoặc lỗi) → xoá record DB trực tiếp
        if ($model->delete()) {
            return ResponseBuilder::responseJson(true, null, 'Xoá record thành công (file không tồn tại trên storage)');
        }

        return ResponseBuilder::responseJson(false, null, 'Không thể xoá file', ApiConstant::STATUS_BAD_REQUEST);
    }

    /**
     * @throws HttpException
     */
    private function findModel(int $id): FileStorageItem
    {
        $model = FileStorageItem::findOne($id);
        if ($model === null) {
            throw new HttpException(ApiConstant::STATUS_NOT_FOUND, 'File not found', ApiConstant::STATUS_NOT_FOUND);
        }
        return $model;
    }
}
