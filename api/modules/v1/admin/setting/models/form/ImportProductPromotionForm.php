<?php

namespace api\modules\v1\admin\setting\models\form;

use yii\base\Model;

class ImportProductPromotionForm extends Model
{
    public $file;

    const DIR_IMPORT = "file/temps/";
    const INDEX_SKU = 2;

    public function rules()
    {
        return [
            ["file", "required"],
            ["file", "file", "extensions" => "xlsx,xlsm,xlsb,xls,xlsm,xltx", 'maxSize' => 1024 * 1024 * 5]
        ];
    }

    public function arrayErrors()
    {
        return [join(", ", array_map(function ($error) {
            return join(",", $error);
        }, $this->getErrors()))];
    }

    public function getFilename()
    {
        return self::DIR_IMPORT . date("Y-m-d_") . $this->file->name;
    }

    public function formName()
    {
        return "";
    }
}