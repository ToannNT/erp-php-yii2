<?php

use yii\db\Migration;

/**
 * Class m220722_032619_update_table_inventory_receipt
 */
class m220722_032619_update_table_inventory_receipt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("inventory_receipt", "progess_status", $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("inventory_receipt", "progess_status");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220722_032619_update_table_inventory_receipt cannot be reverted.\n";

        return false;
    }
    */
}
