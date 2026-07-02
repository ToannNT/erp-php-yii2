<?php

use yii\db\Migration;

/**
 * Class m250515_033115_update_table_order
 */
class m250515_033115_update_table_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("order", "external_id", $this->string()->after("client_id"));
        $this->createIndex("ix_order_external_id", "order", "external_id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("order", "external_id");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250515_033115_update_table_order cannot be reverted.\n";

        return false;
    }
    */
}
