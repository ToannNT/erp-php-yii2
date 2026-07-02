<?php

namespace api\modules\v1\admin\inventory\controllers;

use Yii;
use yii\web\HttpException;
use yii\rest\Controller;
use api\helper\response\ApiConstant;
use api\modules\v1\admin\inventory\models\InventoryHistory;
use api\modules\v1\admin\inventory\models\Stocktaking;
use api\modules\v1\admin\order\models\Order;
use common\models\InventoryHistory as InventoryHistoryAlias;
use api\modules\v1\admin\inventory\models\InventoryIssue;
use api\modules\v1\admin\inventory\models\InventoryReceipt;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\inventory\models\search\InventoryHistorySearch;

class  HistoryController extends Controller
{
    /**
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new InventoryHistorySearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @throws HttpException
     */
    public function actionViewModule(int $id): array
    {
        $history = $this->findModel($id);
        $result = [];
        switch ($history->type) {
            case InventoryHistoryAlias::TYPE_ORDER:
                $result = Order::find()->where(["code" => $history->voucher_code])->one();
                break;
            case InventoryHistoryAlias::TYPE_INVENTORY_RECEIPT:
                $result = InventoryReceipt::find()->where(["code" => $history->voucher_code])->one();
                break;
            case InventoryHistoryAlias::TYPE_INVENTORY_STOCKTAKING:
                $result = Stocktaking::find()->where(["code" => $history->voucher_code])->one();
                break;
            case InventoryHistoryAlias::TYPE_INVENTORY_ISSUE:
                $result = InventoryIssue::find()->where(["code" => $history->voucher_code])->one();
                break;
            default :
                return ResponseBuilder::responseJson(false, null, "Type invalid", ApiConstant::STATUS_INTERNAL_SERVER_ERROR);
        }
        return ResponseBuilder::responseJson(true, [
            "result" => $result,
            "type" => $history->type
        ]);
    }

    /**
     * @throws HttpException
     */
    public function findModel(int $id)
    {
        $history = InventoryHistory::find()->where(["id" => $id])->one();
        if ($history) {
            return $history;
        }
        throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Inventory History not found", ApiConstant::STATUS_NOT_FOUND);
    }
}