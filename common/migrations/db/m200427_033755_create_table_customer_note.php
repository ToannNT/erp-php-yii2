<?php

use yii\db\Migration;

/**
 * Class m200427_033755_create_table_customer_note
 */
class m200427_033755_create_table_customer_note extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%customer_note}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer(),
            'created_by' => $this->integer(),
            'note' => $this->text(),
            'group' => $this->integer(),
            'priority' => $this->integer(),
            'created_at'=>$this->dateTime(),
            'updated_at'=>$this->dateTime(),
            'deleted_at'=>$this->dateTime(),
            'status' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%customer_note}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200427_033755_create_table_customer_note cannot be reverted.\n";

        return false;
    }
    */
}
