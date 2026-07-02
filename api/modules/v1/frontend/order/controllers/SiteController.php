<?php

namespace api\modules\v1\frontend\order\controllers;

use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\order\models\form\OrderForm;

//use api\modules\v1\components\Order;
use api\modules\v1\frontend\order\models\form\OrderItemForm;
use api\modules\v1\frontend\pos\models\OrderItem;
use common\models\Order;
use Exception;
use Sentry\Response;
use Yii;

class SiteController extends \yii\rest\Controller
{

    public function actionCreate(): array
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $order = new OrderForm();
            if (!$order->load(Yii::$app->request->post(), '') || !$order->validate()) {
                return ResponseBuilder::responseJson(false, ['errors' => $order->getErrors()],"Order validation failed", ApiConstant::STATUS_BAD_REQUEST);
            }
            if (!$order->save()) {
                $transaction->rollBack();
                return ResponseBuilder::responseJson(false, ['errors' => $order->getErrors(),"Can't create order"], ApiConstant::STATUS_BAD_REQUEST);
            }
            if (!$order->saveOrderItem()) {
                $transaction->rollBack();
                return ResponseBuilder::responseJson(false, ['errors' => $order->getErrors(),"Can't create order item"], ApiConstant::STATUS_BAD_REQUEST);
            }
            $transaction->commit();
            return ResponseBuilder::responseJson(true, ['order' => $order], 'Create order successfully', ApiConstant::STATUS_OK);
        } catch (Exception $e) {
            Yii::error($e);
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, [], 'internal server error',ApiConstant::STATUS_BAD_REQUEST);
        }
    }
}
