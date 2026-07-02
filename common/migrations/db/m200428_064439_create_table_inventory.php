<?php

use yii\db\Migration;

/**
 * Class m200428_064439_create_table_inventory
 */
class m200428_064439_create_table_inventory extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%inventory}}', [
            'id'=>$this->primaryKey(),
            'name'=>$this->string(256),
            'type' => $this->string(32),
            'code' => $this->string(128),
            'description'=>$this->text(),
            'office_id' => $this->integer(),
            'priority' => $this->integer()->defaultValue(0),
            'parent_id' => $this->integer(),
            'owner_id' => $this->integer(),
            'group_id' => $this->text(),
            'created_at'=>$this->dateTime(),
            'updated_at'=>$this->dateTime(),
            'deleted_at'=>$this->dateTime(),
            'status'=>$this->integer()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%inventory}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200428_064439_create_table_inventory cannot be reverted.\n";

        return false;
    }
    */
}
