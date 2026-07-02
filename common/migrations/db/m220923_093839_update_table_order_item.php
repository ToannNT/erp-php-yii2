<?php

use yii\db\Migration;

/**
 * Class m220923_093839_update_table_order_item
 */
class m220923_093839_update_table_order_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("order_item", "quantity_return", $this->integer()->after("quantity")->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("order_item", "quantity_return");
    }

}
