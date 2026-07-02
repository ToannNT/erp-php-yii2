<?php

use yii\db\Migration;

/**
 * Class m200521_024006_create_table_inventory_issue
 */
class m200521_024006_create_table_inventory_issue extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%inventory_issue}}', [
            'id'=>$this->primaryKey(),
            'code'=>$this->string(),
            'office_id'=>$this->integer(),
            'inventory_id'=>$this->integer(),
            'office_receive_id'=>$this->integer(),
            'inventory_receive_id'=>$this->integer(),
            'note'=>$this->text(),
            'total_number'=>$this->integer(),
            'created_by'=>$this->integer(),
            'delivery_date'=>$this->dateTime(),
            'received_date'=>$this->dateTime(),
            'created_at'=>$this->dateTime(),
            'updated_at'=>$this->dateTime(),
            'deleted_at'=>$this->dateTime(),
            'status'=>$this->integer()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%inventory_issue}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200521_024006_create_table_inventory_issue cannot be reverted.\n";

        return false;
    }
    */
}
