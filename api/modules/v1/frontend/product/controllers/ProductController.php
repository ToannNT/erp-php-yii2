<?php

namespace api\modules\v1\frontend\product\controllers;

use api\helper\response\ApiConstant;
use api\helper\response\ResponseHelper;
use api\modules\v1\frontend\product\models\Product;
use api\modules\v1\frontend\product\models\search\ProductTagSearch;
use common\models\Tag;
use Yii;
use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\product\models\search\ProductSearch;

class ProductController extends Controller
{
    public function actionIndex()
    {
        $request = Yii::$app->request->queryParams;
        return ResponseBuilder::responseJson(true, (new ProductSearch())->search($request));
    }

    /**
     * @author khuongdev2001
     */
    public function actionView($slug): array
    {
        // `slug` KHÔNG unique: mỗi lần tạo lại sản phẩm cùng tên là thêm một dòng trùng slug, bản cũ
        // chỉ bị xoá mềm. Thiếu active() thì trả về đúng bản đã xoá (id nhỏ nhất), kèm dữ liệu cũ
        // hoặc rỗng — triệu chứng hay gặp là `additional_data: null`.
        $product = Product::find()
            ->where(["slug" => $slug])
            ->active()
            ->orderBy(["id" => SORT_DESC])
            ->one();
        if (!$product) {
            return ResponseBuilder::responseJson(false, null, "Product not found", ApiConstant::STATUS_NOT_FOUND);
        }
        return ResponseBuilder::responseJson(true, compact("product"));
    }

    public function actionListTags()
    {
        $request = Yii::$app->request->queryParams;
        return ResponseBuilder::responseJson(true, (new ProductTagSearch())->search($request));
    }
}
