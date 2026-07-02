<?php

use yii\db\Migration;

/**
 * Class m200426_040044_create_table_category
 */
class m200426_040044_create_table_category extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(256),
            'type' => $this->string(32),
            'code' => $this->string(64),
            'icon' => $this->text(),
            'images' => $this->text(),
            'color' => $this->string(10),
            'priority' => $this->integer()->defaultValue(0),
            'description' => $this->text(),
            'parent_id' => $this->integer(),
            'owner_id' => $this->integer(),
            'group_id' => $this->text(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'deleted_at' => $this->dateTime(),
            'status' => $this->integer()->defaultValue(0),
            'slug' => $this->string(255)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%category}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200426_040044_create_table_category cannot be reverted.\n";

        return false;
    }
    */
}
