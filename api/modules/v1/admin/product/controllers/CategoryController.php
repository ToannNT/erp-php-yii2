<?php

namespace api\modules\v1\admin\product\controllers;

use common\models\User;
use Yii;
use yii\rest\Controller;
use yii\web\HttpException;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\product\models\Category;
use api\modules\v1\admin\product\models\CategoryBrand;
use api\modules\v1\admin\product\models\form\CategoryForm;
use api\modules\v1\admin\product\models\form\SortForm;
use api\modules\v1\admin\product\models\search\CategorySearch;

class CategoryController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'view'],
                    'roles' => [User::ROLE_STAFF, User::ROLE_SUPPLIER]
                ],
                [
                    'allow' => true,
                    'roles' => [User::ROLE_MANAGER, User::ROLE_ADMINISTRATOR],
                ]
            ]
        ];
        return $behaviors;
    }

    /**
     * Tạo category. Nhận thêm `brands: [id, ...]` để gán nhiều nhãn hiệu (multiple select).
     *
     * @throws HttpException
     */
    public function actionCreate(): array
    {
        $category = new CategoryForm();
        $category->load(Yii::$app->request->post());
        $transaction = Yii::$app->db->beginTransaction();
        if (!$category->validate() || !$category->save()) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $category->getErrors()], "Can't create Category");
        }
        $category->createOrDeleteBrand();
        $transaction->commit();
        return ResponseBuilder::responseJson(true, compact("category"), "Create Category successfully");
    }

    /**
     * Cập nhật category. Không gửi `brands` => giữ nguyên nhãn hiệu hiện có; gửi `[]` => bỏ hết.
     *
     * @throws HttpException
     */
    public function actionUpdate(int $id): array
    {
        $category = $this->findModel($id);
        $category->load(Yii::$app->request->post());
        $transaction = Yii::$app->db->beginTransaction();
        if (!$category->validate() || !$category->save()) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => $category->getErrors()], "Can't update Category");
        }
        $category->createOrDeleteBrand();
        $transaction->commit();
        return ResponseBuilder::responseJson(true, compact("category"), "Update Category successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionDelete(int $id): array
    {
        $category = $this->findModel($id);
        $transaction = Yii::$app->db->beginTransaction();
        if (!$category->softDelete()) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, null, "Can't delete Category successfully");
        }
        // Bảng nối không có soft delete nên dọn thẳng, tránh để brand còn trỏ tới category đã xoá.
        CategoryBrand::deleteAll(["category_id" => $category->id]);
        $transaction->commit();
        return ResponseBuilder::responseJson(true, null, "Delete Category successfully");
    }

    /**
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $category = $this->findModel($id);
        return ResponseBuilder::responseJson(true, compact("category"));
    }

    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new CategorySearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * Sắp xếp thứ tự hiển thị hàng loạt — FE kéo thả xong gửi 1 request duy nhất.
     *
     * Body: {"items": [{"id": 49, "priority": 1}, {"id": 52, "priority": 2}]}
     * Thiếu `priority` thì lấy vị trí trong mảng. Quy ước: priority nhỏ hiện trước.
     */
    public function actionSort(): array
    {
        $form = new SortForm(["modelClass" => Category::class]);
        $form->load(Yii::$app->request->post());
        if (!$form->validate() || !$form->apply()) {
            return ResponseBuilder::responseJson(false, ["errors" => $form->getErrors()], "Can't sort Category");
        }
        return ResponseBuilder::responseJson(true, ["applied" => $form->getApplied()], "Sort Category successfully");
    }

    /**
     * @return CategoryForm
     * @throws HttpException
     */
    public function findModel(int $id)
    {
        $category = CategoryForm::find()->where(["id" => $id])->unDelete()->one();
        if ($category) {
            return $category;
        }
        throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Category not found", ApiConstant::STATUS_NOT_FOUND);
    }
}
