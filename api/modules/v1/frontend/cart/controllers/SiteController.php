<?php

namespace api\modules\v1\frontend\cart\controllers;

use Yii;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\components\ProductInventory;
use common\models\DiscountCode;

class SiteController extends Controller
{
    /**
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionIndex(): array
    {
        /**@var $productInventoryComponent ProductInventory */
        $productInventoryComponent = $this->module->productInventory;
        $cartRqs = Yii::$app->request->post("carts");
        if (!is_array($cartRqs)) {
            return ResponseBuilder::responseJson(
                false,
                null,
                Yii::t("api", "{module} not found"),
                ApiConstant::STATUS_NOT_FOUND
            );
        }
        $carts = array_map(function ($cart) use ($productInventoryComponent) {
            if (isset($cart["product_variant_id"], $cart["quantity"], $cart["office_id"])) {
                $productInventoryComponent->setParam($cart["product_variant_id"], $cart["office_id"]);
                $cart["available"] = $productInventoryComponent->getAvailable();
                $cart["is_enough"] = $cart["quantity"] <= $cart["available"];
                return $cart;
            }
        }, $cartRqs);
        return ResponseBuilder::responseJson(
            true,
            compact("carts"),
            Yii::t("api", "Some products are not enough")
        );
    }

    /**
     * @throws yii\web\HttpException
     * @author khuongdev2001
     */
    public function actionDiscount($code): array
    {
        $discount = DiscountCode::find()->where(["code" => $code])->active()->one();
        if ($discount) {
            return ResponseBuilder::responseJson(true, compact("discount"));
        }
        return ResponseBuilder::responseJson(
            false,
            null,
            Yii::t("api", "Code not found"),
            ApiConstant::STATUS_NOT_FOUND
        );
    }
}
