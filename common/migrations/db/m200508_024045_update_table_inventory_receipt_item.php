<?php

use yii\db\Migration;

/**
 * Class m200508_024045_update_table_inventory_receipt_item
 */
class m200508_024045_update_table_inventory_receipt_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%inventory_receipt_item}}', 'tax_price', $this->double()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('{{%inventory_receipt_item}}', 'tax_price');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200508_024045_update_table_inventory_receipt_item cannot be reverted.\n";

        return false;
    }
    */
}
