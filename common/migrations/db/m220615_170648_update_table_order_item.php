<?php

use yii\db\Migration;

/**
 * Class m220615_170648_update_table_order_item
 */
class m220615_170648_update_table_order_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("order_item", "sub_total", $this->integer()->after("unit_price"));
        $this->addColumn("order_item", "discount_price", $this->integer()->after("note"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("order_item", "sub_total");
        $this->dropColumn("order_item", "discount_price");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220615_170648_update_table_order_item cannot be reverted.\n";

        return false;
    }
    */
}
