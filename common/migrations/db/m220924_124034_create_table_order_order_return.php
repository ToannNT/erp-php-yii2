<?php

use yii\db\Migration;

/**
 * Class m220924_124034_create_table_order_order_return
 */
class m220924_124034_create_table_order_order_return extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("order_order_return", [
            "id" => $this->primaryKey(),
            "status" => $this->integer(),
            "order_id" => $this->integer(),
            "order_return_id" => $this->integer(),
            "created_at" => $this->dateTime(),
            "updated_at" => $this->dateTime(),
            "deleted_at" => $this->dateTime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("order_order_return");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220924_124034_create_table_order_order_return cannot be reverted.\n";

        return false;
    }
    */
}
