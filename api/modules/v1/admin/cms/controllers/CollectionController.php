<?php

namespace api\modules\v1\admin\cms\controllers;

use api\modules\v1\admin\cms\models\search\SystemCmsCollectionSearch;
use api\modules\v1\admin\cms\models\SystemCmsCollection;
use Throwable;
use Yii;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\rest\Controller;
use yii\web\HttpException;
use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\cms\models\form\SystemCmsCollectionForm;
use api\modules\v1\admin\cms\command\MigrateColumnCollection;

class CollectionController extends Controller
{
    /**
     * @throws HttpException
     */
    public function actionCreate()
    {
        $collection = new SystemCmsCollectionForm();
        $collection->load(Yii::$app->request->post());
        if (!$collection->validate()) {
            return ResponseBuilder::responseJson(false, ["errors" => $collection->getErrors()]);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $migrateCollection = new MigrateColumnCollection(["collection" => $collection]);
            $migrateCollection->up();
            if (!$collection->save()) {
                $transaction->rollBack();
                return ResponseBuilder::responseJson(false, ["errors" => $collection->getErrors()]);
            }
            $transaction->commit();
            return ResponseBuilder::responseJson(true, compact("collection"), "Create collection successfully");
        } catch (\Exception $exception) {
            $transaction->rollBack();
            return ResponseBuilder::responseJson(false, ["errors" => "System Error","message"=>$exception->getMessage()]);
        }
    }

    /**
     * @throws Throwable
     * @throws Exception
     * @throws \yii\base\Exception
     * @throws StaleObjectException
     * @throws HttpException
     */
    public function actionUpdate($id): array
    {
        $collection = SystemCmsCollectionForm::find()->where(["id" => $id])->one();
        if (!$collection) {
            return ResponseBuilder::responseJson(false, null, "Collection not found", ApiConstant::STATUS_NOT_FOUND);
        }
        $collection->load(Yii::$app->request->post());
        if (!$collection->validate()) {
            return ResponseBuilder::responseJson(false, ["errors" => $collection->getErrors()]);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $migrationCollection = new MigrateColumnCollection(["collection" => $collection]);
            $migrationCollection->addOrDropColumns($collection->getOldAttributes());
            if (!$collection->save()) {
                $transaction->rollBack();
                return ResponseBuilder::responseJson(false, ["errors" => $collection->getErrors()]);
            }
            $transaction->commit();
            return ResponseBuilder::responseJson(true, compact("collection"), "Create collection successfully");
        } catch (\Exception $exception) {
            $transaction->rollBack();
//            $collection->delete();
//            $migrationCollection->dropBatchCollection();
            throw $exception;
        }
    }

    /**
     * @return array
     * @throws HttpException
     */
    public function actionIndex()
    {
        return ResponseBuilder::responseJson(true, (new SystemCmsCollectionSearch())->search(Yii::$app->request->queryParams));
    }

    /**
     * @param $id
     * @return array
     * @throws HttpException
     */
    public function actionView($id)
    {
        $collection = SystemCmsCollection::find()->where(["id" => $id])->one();
        if (!$collection) {
            throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Collection not found", ApiConstant::STATUS_NOT_FOUND);
        }
        return ResponseBuilder::responseJson(true, compact("collection"));
    }

    /**
     * @param $id
     * @return array
     * @throws HttpException
     * @throws Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $collection = SystemCmsCollection::find()->where(["id" => $id])->one();
        if (!$collection) {
            throw new HttpException(ApiConstant::STATUS_NOT_FOUND, "Collection not found", ApiConstant::STATUS_NOT_FOUND);
        }
        $collection->delete();
        $migrateCollection = new MigrateColumnCollection(["collection" => $collection]);
        $migrateCollection->down();
        return ResponseBuilder::responseJson(true, [], "Delete collection successfully");
    }
}