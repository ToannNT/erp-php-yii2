<?php

namespace api\modules\v1\admin\inventory\models\form;

use yii\base\Model;

class ImportInventoryReceiptForm extends Model
{
    const DIR_IMPORT = "file/temps/";
    const MAP_CELL_EXCEL = [
        2 => "sku",
        4 => "quantity",
        5 => "unit_price"
    ];

    public $file;

    public function rules()
    {
        return [
            ["file", "required"],
            ["file", "file", "extensions" => "xlsx,xlsm,xlsm,xltx", 'maxSize' => 1024 * 1024 * 5]
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
