<?php

use yii\db\Migration;

/**
 * Class m221019_140001_create_table_history_log
 */
class m221019_140001_create_table_history_log extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $this->createTable("history_log", [
            "id" => $this->primaryKey(),
            "level" => $this->integer(),
            "category" => $this->string(255),
            "log_time" => $this->double(),
            "prefix" => $this->string(255),
            "message" => $this->string(),
            "old_data" => $this->json(),
            "new_data" => $this->json(),
            "tag" => $this->string(255)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("history_log");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221019_140001_create_table_history_log cannot be reverted.\n";

        return false;
    }
    */
}
