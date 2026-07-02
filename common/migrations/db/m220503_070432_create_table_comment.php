<?php

use yii\db\Migration;

/**
 * Class m220503_070432_create_table_comment
 */
class m220503_070432_create_table_comment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("{{%comment}}", [
            "id" => $this->primaryKey(),
            "user_id" => $this->integer(),
            "status" => $this->integer(),
            "rating" => $this->integer(),
            "title" => $this->string(255),
            "content" => $this->text(),
            "images" => $this->string(255),
            "parent_id" => $this->integer(),
            "options_id" => $this->integer(),
            "module_id" => $this->integer(),
            "type" => $this->string(100),
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
        $this->dropTable("{{%comment}}");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220503_070432_create_table_comment cannot be reverted.\n";

        return false;
    }
    */
}
