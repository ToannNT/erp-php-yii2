<?php

use yii\db\Migration;

/**
 * Class m220804_030004_update_table_inventory_receipt
 */
class m220804_030004_update_table_inventory_receipt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn("inventory_receipt", "progess_status", "progress_status");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "changed";
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220804_030004_update_table_inventory_receipt cannot be reverted.\n";

        return false;
    }
    */
}
