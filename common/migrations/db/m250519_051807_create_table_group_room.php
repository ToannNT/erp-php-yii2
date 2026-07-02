<?php

use yii\db\Migration;

/**
 * Class m250519_051807_create_table_group_room
 */
class m250519_051807_create_table_group_room extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('group_room', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull()->unique(),
            'code' => $this->string(50)->unique(),
            'created_by' => $this->integer(),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('group_room');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250519_051807_create_table_group_room cannot be reverted.\n";

        return false;
    }
    */
}
