<?php

use yii\db\Migration;

/**
 * Class m220804_045625_update_table_inventory_receipt_item
 */
class m220804_045625_update_table_inventory_receipt_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("inventory_receipt_item", "sub_total_price", $this->double()->after("unit_price"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("inventory_receipt_item", "sub_total_price");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220804_045625_update_table_inventory_receipt_item cannot be reverted.\n";

        return false;
    }
    */
}
