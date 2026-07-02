<?php

use yii\db\Migration;

/**
 * Class m221012_030325_create_table_order_payment_method
 */
class m221012_030325_create_table_order_payment_method extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("order_payment_method", [
            "id" => $this->primaryKey(),
            "order_id" => $this->integer(),
            "payment_method_id" => $this->integer(),
            "payment" => $this->double(),
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
        echo "m221012_030325_create_table_order_payment_method cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221012_030325_create_table_order_payment_method cannot be reverted.\n";

        return false;
    }
    */
}
