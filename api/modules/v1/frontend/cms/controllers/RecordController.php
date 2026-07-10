<?php

namespace api\modules\v1\frontend\cms\controllers;

use api\helper\response\ApiConstant;
use api\helper\response\ResponseBuilder;
use api\modules\v1\frontend\cms\models\Collection;
use api\modules\v1\frontend\cms\models\search\CollectionSearch;
use common\models\SystemCmsCollectionQuery;
use Yii;
use yii\db\Query;
use yii\rest\Controller;
use api\modules\v1\frontend\cms\models\SystemCmsCollection;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class RecordController extends Controller
{
    /**
     * @throws NotFoundHttpException
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
     * @throws NotFoundHttpException
     */
    public function actionView($collection_name, $id)
    {
        $collection = $this->findModel($collection_name);
        Collection::setFields($collection->schemas);
        Collection::setTableName($collection_name);
        $record = Collection::find()->where(["id" => $id])->one();
        if (!$record) {
            throw new NotFoundHttpException("Record not found");
        }
        return ResponseBuilder::responseJson(true, $record);
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
