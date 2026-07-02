<?php

use yii\db\Migration;

/**
 * Class m200708_031842_update_table_product_inventory
 */
class m200708_031842_update_table_product_inventory extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%product_inventory}}', 'incoming', $this->integer()->defaultValue(0));
        $this->addColumn('{{%product_inventory}}', 'on_way', $this->integer()->defaultValue(0));
        $this->addColumn('{{%product_inventory}}', 'committed', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('{{%product_inventory}}', 'incoming');
        $this->dropColumn('{{%product_inventory}}', 'on_way');
        $this->dropColumn('{{%product_inventory}}', 'committed');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200708_031842_update_table_product_inventory cannot be reverted.\n";

        return false;
    }
    */
}
