<?php

use yii\db\Migration;

/**
 * Class m200702_025231_update_table_product_inventory
 */
class m200702_025231_update_table_product_inventory extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%product_inventory}}', 'sll_price', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('{{%product_inventory}}', 'sll_price');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200702_025231_update_table_product_inventory cannot be reverted.\n";

        return false;
    }
    */
}
