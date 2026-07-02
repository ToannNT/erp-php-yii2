<?php

namespace api\modules\v1\admin\order_return\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\HttpException;
use common\models\User;
use common\models\Order as OrderAlias;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\order_return\models\Order;
use api\modules\v1\admin\order_return\models\search\OrderSearch;

class OrderController extends Controller
{
    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        $dataProvider = (new OrderSearch())->search(Yii::$app->request->queryParams);
        return ResponseBuilder::responseJson(true, $dataProvider);
    }

    /**
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $query = Order::find()
            ->where(["id" => $id])
            ->andWhere(["status" => OrderAlias::STATUS_DONE]);
        if (!Yii::$app->user->can(User::ROLE_ADMINISTRATOR)) {
            $offices = Yii::$app->user->identity->offices;
            $query->andWhere(['in', 'order.office_id', array_column($offices, 'id')]);
            if (Yii::$app->user->can(User::ROLE_SELLER)) {
                $query->channelPos();
            };
        }
        $order = $query->one();
        if ($order) {
            return ResponseBuilder::responseJson(true, compact("order"));
        }
        return ResponseBuilder::responseJson(false, null, "Order not found", ApiConstant::STATUS_NOT_FOUND);
    }

}