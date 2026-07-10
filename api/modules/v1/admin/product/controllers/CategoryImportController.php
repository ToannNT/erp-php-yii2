<?php

namespace api\modules\v1\admin\product\controllers;

use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\product\models\form\CategoryImportForm;
use common\models\User;
use Yii;
use yii\rest\Controller;
use yii\web\UploadedFile;

class CategoryImportController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'roles' => [User::ROLE_MANAGER, User::ROLE_ADMINISTRATOR],
                ]
            ]
        ];
        return $behaviors;
    }

    public function verbs(): array
    {
        return [
            'import' => ['POST'],
        ];
    }

    public function actionImport(): array
    {
        $form = new CategoryImportForm();
        $form->file = UploadedFile::getInstanceByName('file');

        if (!$form->validate()) {
            return ResponseBuilder::responseJson(false, ['errors' => $form->getErrors()], 'File không hợp lệ.');
        }

        $result = $form->import();

        $success = $result['success'] > 0 || empty($result['errors']);
        $message = "Import hoàn tất: {$result['success']} thêm mới, {$result['skipped']} bỏ qua.";

        return ResponseBuilder::responseJson($success, $result, $message);
    }
}
