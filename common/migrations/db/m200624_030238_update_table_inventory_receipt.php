<?php

use yii\db\Migration;

/**
 * Class m200624_030238_update_table_inventory_receipt
 */
class m200624_030238_update_table_inventory_receipt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%inventory_receipt}}', 'quantity', $this->integer());
        $this->addColumn('{{%inventory_receipt}}', 'sub_total_price', $this->double());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('{{%inventory_receipt}}', 'quantity');
        $this->dropColumn('{{%inventory_receipt}}', 'sub_total_price');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200624_030238_update_table_inventory_receipt cannot be reverted.\n";

        return false;
    }
    */
}
