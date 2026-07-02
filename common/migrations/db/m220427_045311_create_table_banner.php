<?php

use yii\db\Migration;

/**
 * Class m220427_045311_create_table_banner
 */
class m220427_045311_create_table_banner extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%banner}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(256),
            'url' => $this->text(),
            'description' => $this->text(),
            'status' => $this->integer(),
            'type' => $this->string(),
            'priority' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'video_url' => $this->text(),
            'link' => $this->text()
        ]);
    }
    public function down()
    {
        $this->dropTable('{{%banner}}');
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220427_045311_create_table_banner cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220427_045311_create_table_banner cannot be reverted.\n";

        return false;
    }
    */
}
