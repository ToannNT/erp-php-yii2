<?php

use yii\db\Migration;

/**
 * Class m220510_032532_create_table_page
 */
class m220510_032532_create_table_page extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("{{page}}", [
            "id" => $this->primaryKey(),
            "slug" => $this->string(255),
            "title" => $this->string(255),
            "status" => $this->integer(),
            "content" => $this->text(),
            "create_by" => $this->integer(),
            "created_at" => $this->dateTime(),
            "updated_at" => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("page");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220510_032532_create_table_page cannot be reverted.\n";

        return false;
    }
    */
}
