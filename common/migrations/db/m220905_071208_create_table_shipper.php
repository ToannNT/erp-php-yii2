<?php

use yii\db\Migration;

/**
 * Class m220905_071208_create_table_shipper
 */
class m220905_071208_create_table_shipper extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("shipper", [
            "id" => $this->primaryKey(),
            "name" => $this->string(255),
            "short_name" => $this->string(255),
            "slug" => $this->string(255),
            "type" => $this->text(),
            "thumbnail" => $this->string(255),
            "token" => $this->string(500),
            "service_extras" => $this->text(),
            "status" => $this->integer(),
            "created_by" => $this->integer(),
            "created_at" => $this->dateTime(),
            "updated_at" => $this->dateTime(),
            "deleted_at" => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("shipper");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220905_071208_create_table_shipper cannot be reverted.\n";

        return false;
    }
    */
}
