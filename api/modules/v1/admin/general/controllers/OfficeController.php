<?php

namespace api\modules\v1\admin\general\controllers;

use api\helper\response\ResponseBuilder;
use Yii;
use api\modules\v1\admin\general\models\Office;
use api\modules\v1\admin\general\models\search\OfficeSearch;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * GeneralController implements the CRUD actions for Office model.
 */
class OfficeController extends Controller
{

    /**
     * Lists all Office models.
     * @return array
     * @throws HttpException
     */
    public function actionIndex(): array
    {
        return ResponseBuilder::responseJson(true, (new OfficeSearch())->search(Yii::$app->request->queryParams));
    }


    /**
     * Displays a single Office model.
     * @param integer $id
     * @return array
     * @throws HttpException
     */
    public function actionView(int $id): array
    {
        $office = $this->findModel($id);
        return ResponseBuilder::responseJson(true, compact("office"));
    }

    /**
     * Creates a new Office model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return "Chưa làm";
    }

    /**
     * Updates an existing Office model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        return "Chưa làm";
    }

    /**
     * Delete an existing Office model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        return "Chưa làm";
    }

    /**
     * Delete multiple existing Office model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkDelete()
    {
        return "Chưa làm";
    }

    /**
     * Finds the Office model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Office the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Office
    {
        if (($model = Office::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
