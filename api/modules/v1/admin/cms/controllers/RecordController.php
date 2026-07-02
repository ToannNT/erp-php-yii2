<?php

namespace api\modules\v1\admin\cms\controllers;

use api\modules\v1\admin\cms\models\Collection;
use api\modules\v1\admin\cms\models\Collection2;
use api\modules\v1\admin\cms\models\RecordModel;
use api\modules\v1\admin\cms\models\form\CollectionForm;
use Throwable;
use Yii;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\cms\models\search\CollectionSearch;
use api\modules\v1\admin\cms\models\SystemCmsCollection;
use yii\db\Query;
use yii\db\StaleObjectException;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class RecordController extends Controller
{
    /**
     * @throws HttpException
     */
    public function actionIndex($collection_name)
    {
        $collection = $this->findModel($collection_name);
        CollectionSearch::setFields($collection->schemas);
        CollectionSearch::setTableName($collection_name);
        $model = new CollectionSearch();
        return ResponseBuilder::responseJson(true, $model->search(Yii::$app->request->queryParams));
    }

    /**
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionCreate($collection_name)
    {
        $request = Yii::$app->request->post();
        $collection = $this->findModel($collection_name);
        CollectionForm::setFields($collection->schemas);
        CollectionForm::setTableName($collection_name);
        $collection = new CollectionForm();
        $collection->load($request);
        if (!$collection->validate() || !$collection->save(false)) {
            return ResponseBuilder::responseJson(false, ["errors" => $collection->getErrors()], "Can't create {$collection_name}");
        }
        return ResponseBuilder::responseJson(true, [$collection_name => $collection], "Create {$collection_name} successfully");
    }

    /**
     * @throws NotFoundHttpException
     * @throws HttpException
     */
    public function actionUpdate($collection_name, $id)
    {
        $collection = SystemCmsCollection::find()->where(["name" => $collection_name])->one();
        if (!$collection) {
            throw new NotFoundHttpException("Collection not found");
        }
        CollectionForm::setFields($collection->schemas);
        CollectionForm::setTableName($collection_name);
        $record = CollectionForm::find()->where(["id" => $id])->one();
        if (!$record) {
            throw new NotFoundHttpException("Record not found");
        }
        $record->load(Yii::$app->request->post());
        if (!$record->validate() || !$record->save(false)) {
            return ResponseBuilder::responseJson(false, ["errors" => $record->getErrors()], "Can't update {$collection_name}");
        }
        return ResponseBuilder::responseJson(true, [$collection_name => $record], "Update {$collection_name} successfully");
    }


    /**
     * @param $collection_name
     * @param $id
     * @return array
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($collection_name, $id)
    {
        $collection = $this->findModel($collection_name);
        Collection::setFields($collection->schemas);
        Collection::setTableName($collection_name);
        $record = Collection::find()->where(["id" => $id])->one();
        if (!$record) {
            throw new NotFoundHttpException("Record not found");
        }
        $record->delete();
        return ResponseBuilder::responseJson(true, [], "Delete Record successfully");
    }

    /**
     * @throws NotFoundHttpException
     * @throws HttpException
     */
    public function actionView($collection_name, $id)
    {
        $collection = $this->findModel($collection_name);
        CollectionForm::setFields($collection->schemas);
        CollectionForm::setTableName($collection_name);
        $collection = Collection::find()->where(["id" => $id])->one();
        if (!$collection) {
            throw new NotFoundHttpException("Collection not found");
        }
        return ResponseBuilder::responseJson(true, [$collection_name => $collection]);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel($collection_name)
    {
        $collection = SystemCmsCollection::find()->where(["name" => $collection_name])->one();
        if (!$collection) {
            throw new NotFoundHttpException("Collection Not found");
        }
        return $collection;
    }
}