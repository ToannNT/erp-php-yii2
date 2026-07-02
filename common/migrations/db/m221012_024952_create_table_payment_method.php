<?php

use yii\db\Migration;

/**
 * Class m221012_024952_create_table_payment_method
 */
class m221012_024952_create_table_payment_method extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("payment_method", [
            "id" => $this->primaryKey(),
            "name" => $this->string(200),
            "code" => $this->string(100),
            "status" => $this->integer(),
            "is_default" => $this->boolean(),
            "created_by" => $this->integer(),
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
        echo "m221012_024952_create_table_payment_method cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221012_024952_create_table_payment_method cannot be reverted.\n";

        return false;
    }
    */
}
