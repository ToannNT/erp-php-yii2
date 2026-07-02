<?php

use yii\db\Migration;

/**
 * Class m200426_041210_create_table_brand
 */
class m200426_041210_create_table_brand extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%brand}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(256),
            'type' => $this->string(32),
            'code' => $this->string(128),
            'description' => $this->text(),
            'icon' => $this->text(),
            'images' => $this->text(),
            'color' => $this->string(10),
            'priority' => $this->integer()->defaultValue(0),
            'parent_id' => $this->integer(),
            'owner_id' => $this->integer(),
            'group_id' => $this->text(),
            'slug' => $this->string(255),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'deleted_at' => $this->dateTime(),
            'status' => $this->integer()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%brand}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200426_041210_create_table_brand cannot be reverted.\n";

        return false;
    }
    */
}
