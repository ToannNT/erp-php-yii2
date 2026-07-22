<?php

namespace api\modules\v1\admin\product\controllers;

use api\helper\response\ResponseBuilder;
use api\modules\v1\admin\product\models\form\ProductImportForm;
use common\models\User;
use yii\filters\AccessControl;
use yii\rest\Controller;
use yii\web\UploadedFile;

class ProductImportController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::className(),
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
        $form = new ProductImportForm();
        $form->file = UploadedFile::getInstanceByName('file');
        if (!$form->validate()) {
            return ResponseBuilder::responseJson(false, ['errors' => $form->getErrors()], 'File không hợp lệ.');
        }
        $result = $form->import();
        if (!empty($result['rolled_back'])) {
            $count = count($result['errors']);
            return ResponseBuilder::responseJson(false, $result, "Có {$count} dòng lỗi — import không thành công.");
        }
        $message = "Import hoàn tất: {$result['success']} thêm mới, {$result['skipped']} bỏ qua.";
        return ResponseBuilder::responseJson(true, $result, $message);
    }
}
