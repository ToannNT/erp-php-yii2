<?php

namespace api\modules\v1\frontend\sitemap;

use yii\base\Module;

class SitemapModule extends Module
{
    public function init()
    {
        $this->defaultRoute = "default";
        parent::init();
    }
}
