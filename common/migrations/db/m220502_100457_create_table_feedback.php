<?php

use yii\db\Migration;

/**
 * Class m220502_100457_create_table_feedback
 */
class m220502_100457_create_table_feedback extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("{{%feedback}}", [
            "id" => $this->primaryKey(),
            "user_id" => $this->integer(),
            "subject" => $this->string(),
            "title" => $this->string(),
            "fullname" => $this->string(255),
            "phone" => $this->string(50),
            "email" => $this->string(255),
            "content" => $this->text(),
            "status" => $this->integer(),
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
        $this->dropTable("{{%feedback}}");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220502_100457_create_table_feedback cannot be reverted.\n";

        return false;
    }
    */
}
