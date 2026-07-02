<?php

use yii\db\Migration;

/**
 * Class m200630_073142_update_table_inventory_receipt
 */
class m200630_073142_update_table_inventory_receipt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%inventory_receipt}}', 'discount_price', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('{{%inventory_receipt}}', 'discount_price');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200630_073142_update_table_inventory_receipt cannot be reverted.\n";

        return false;
    }
    */
}
