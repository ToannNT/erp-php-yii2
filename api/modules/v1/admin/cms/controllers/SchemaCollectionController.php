<?php

namespace api\modules\v1\admin\cms\controllers;

use yii\rest\Controller;
use yii\web\HttpException;
use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\cms\models\GetSchemaRelation;

class SchemaCollectionController extends Controller
{
    public function verbs()
    {
        return array_merge(parent::verbs(), [
            "table-relation" => ["GET"]
        ]);
    }

    /**
     * @throws HttpException
     */
    public function actionTableRelation()
    {
        $schemaRelation = new GetSchemaRelation();
        return ResponseBuilder::responseJson(true, ["data" => $schemaRelation->getTables()]);
    }
}