<?php

use common\components\shipping\shipper\GHNShipper;
use common\components\shipping\shipper\GHTKShipper;
use yii\db\Migration;
use common\models\Shipper;

/**
 * Class m220915_093305_create_seed_data_shipper
 */
class m220915_093305_create_seed_data_shipper extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $GhnShipper = new Shipper([
            "id" => 1,
            "short_name" => GHNShipper::TYPE,
            "name" => GHNShipper::SHIPPER,
            "type" => "[1]",
            "thumbnail" => "https://theme.hstatic.net/200000472237/1000829412/14/favicon.png?v=440",
            "service_extras" => '{"shop_id":3166668}',
            "status" => Shipper::STATUS_ACTIVE
        ]);
        $GhtkShipper = new Shipper([
            "id" => 2,
            "short_name" => GHTKShipper::TYPE,
            "name" => GHTKShipper::SHIPPER,
            "type" => "[1,2]",
            "thumbnail" => "https://giaohangtietkiem.vn/wp-content/themes/giaohangtk/images/ico/apple-touch-icon-144-precomposed.png",
            "service_extras" => '[]',
            "status" => Shipper::STATUS_ACTIVE,
        ]);
        if (!$GhnShipper->save() || !$GhtkShipper->save()) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Shipper::deleteAll(["id" => [1, 2]]);
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220915_093305_create_seed_data_shipper cannot be reverted.\n";

        return false;
    }
    */
}
