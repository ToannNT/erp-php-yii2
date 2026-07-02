<?php

namespace api\modules\v1\frontend;

use api\modules\v1\frontend\product_variant\ProductVariantModule;
use yii\base\Module;

class FrontendModule extends Module
{
    public function init()
    {
        parent::init();
        $this->components = [
            "cart" => cart\components\CartComponent::class
        ];
        $this->modules = [
            "user" => user\UserModule::class,
            "product" => product\ProductModule::class,
            "feedback" => feedback\FeedbackModule::class,
            "comment" => comment\CommentModule::class,
            "article" => article\ArticleModule::class,
            "page" => page\PageModule::class,
            "cart" => cart\CartModule::class,
            "banner" => banner\BannerModule::class,
            "order" => order\CheckoutModule::class,
            "pos" => pos\Module::class,
            "service" => service\ServiceModule::class,
            "location" => location\Module::class,
            "product-variant" => product_variant\ProductVariantModule::class,
            "cms" => cms\CmsModule::class
        ];
    }
}
