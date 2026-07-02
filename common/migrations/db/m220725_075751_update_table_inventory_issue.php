<?php

use yii\db\Migration;

/**
 * Class m220725_075751_update_table_inventory_issue
 */
class m220725_075751_update_table_inventory_issue extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("inventory_issue", "progess_status", $this->string(255)->after("created_at"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("inventory_issue", "progess_status");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220725_075751_update_table_inventory_issue cannot be reverted.\n";

        return false;
    }
    */
}
