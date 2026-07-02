<?php

namespace api\modules\v1;
use yii\base\Module;

class VersionModule extends Module
{
    
    public function init()
    {
        parent::init();
        $this->modules = [
            "frontend" => frontend\FrontendModule::class,
            "admin"    => admin\AdminModule::class
        ];
    }
}
