<?php

use yii\db\Migration;

/**
 * Class m220808_095039_update_table_inventory_receipt_item
 */
class m220808_095039_update_table_inventory_receipt_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("inventory_receipt_item", "total_discount_value", $this->double()->after("discount_value"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("inventory_receipt_item", "total_discount_value");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220808_095039_update_table_inventory_receipt_item cannot be reverted.\n";

        return false;
    }
    */
}
