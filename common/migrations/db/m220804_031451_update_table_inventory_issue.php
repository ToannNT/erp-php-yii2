<?php

use yii\db\Migration;

/**
 * Class m220804_031451_update_table_inventory_issue
 */
class m220804_031451_update_table_inventory_issue extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn("inventory_issue", "progess_status", "progress_status");
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
        echo "m220804_031451_update_table_inventory_issue cannot be reverted.\n";

        return false;
    }
    */
}
