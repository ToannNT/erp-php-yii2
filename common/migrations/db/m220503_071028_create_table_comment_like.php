<?php

use yii\db\Migration;

/**
 * Class m220503_071028_create_table_comment_like
 */
class m220503_071028_create_table_comment_like extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("{{%comment_like}}", [
            "id" => $this->primaryKey(),
            "status" => $this->integer(),
            // foreign key to comment table
            "comment_id" => $this->integer(),
            "user_id" => $this->integer(),
            "options" => $this->integer(),
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
        $this->dropTable("{{%comment_like}}");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220503_071028_create_table_comment_like cannot be reverted.\n";

        return false;
    }
    */
}
