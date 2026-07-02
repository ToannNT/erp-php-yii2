<?php

use yii\db\Migration;

/**
 * Class m250519_052704_create_table_room
 */
class m250519_052704_create_table_room extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('room', [
            'id' => $this->primaryKey(),
            'group_id' => $this->integer(),
            'name' => $this->string(50)->notNull(),
            'code' => $this->string(50),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(1),
            'created_by' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->createIndex("idx-room-group_id", "room", "group_id");
        $this->createIndex("idx-room-created_by", "room", "created_by");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('room');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250519_052704_create_table_room cannot be reverted.\n";

        return false;
    }
    */
}
