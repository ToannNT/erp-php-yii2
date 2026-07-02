<?php

use yii\db\Migration;

/**
 * Class m220706_042138_update_table_order_item
 */
class m220706_042138_update_table_order_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn("order_item", "unit_price", $this->double());
        $this->alterColumn("order_item", "sub_total", $this->double());
        $this->alterColumn("order_item", "total_price", $this->double());
        $this->alterColumn("order_item", "discount_price", $this->double());
        $this->alterColumn("order_item", "discount", $this->double());
        $this->alterColumn("order_item", "tax_price", $this->double());
        $this->alterColumn("order_item", "data_tax", $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220706_042138_update_table_order_item cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220706_042138_update_table_order_item cannot be reverted.\n";

        return false;
    }
    */
}
