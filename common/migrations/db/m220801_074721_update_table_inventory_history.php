<?php

use yii\db\Migration;

/**
 * Class m220801_074721_update_table_inventory_history
 */
class m220801_074721_update_table_inventory_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("inventory_history", "type", $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("inventory_history","type");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220801_074721_update_table_inventory_history cannot be reverted.\n";

        return false;
    }
    */
}
