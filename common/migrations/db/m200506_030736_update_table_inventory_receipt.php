<?php

use yii\db\Migration;

/**
 * Class m200506_030736_update_table_inventory_receipt
 */
class m200506_030736_update_table_inventory_receipt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%inventory_receipt}}', 'billing_address', $this->text());
        $this->addColumn('{{%inventory_receipt}}', 'shipping_address', $this->text());
        $this->addColumn('{{%inventory_receipt}}', 'tax', $this->string());
        $this->addColumn('{{%inventory_receipt}}', 'delivery_date', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('{{%inventory_receipt}}', 'billing_address');
        $this->dropColumn('{{%inventory_receipt}}', 'shipping_address');
        $this->dropColumn('{{%inventory_receipt}}', 'tax');
        $this->dropColumn('{{%inventory_receipt}}', 'delivery_date');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200506_030736_update_table_inventory_receipt cannot be reverted.\n";

        return false;
    }
    */
}
