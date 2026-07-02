<?php

namespace api\modules\v1\frontend\product;

use yii\base\Module;

class ProductModule extends Module
{
    public function init()
    {
        $this->defaultRoute = "product";
        parent::init();
    }
}
