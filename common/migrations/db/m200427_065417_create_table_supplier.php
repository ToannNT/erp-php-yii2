<?php

use yii\db\Migration;

/**
 * Class m200427_065417_create_table_supplier
 */
class m200427_065417_create_table_supplier extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%supplier}}', [
            'id'=>$this->primaryKey(),
            'name'=>$this->string(256),
            'type' => $this->string(32),
            'code' => $this->string(128),
            'description'=>$this->text(),
            'icon' => $this->text(),
            'images' => $this->text(),
            'color' => $this->string(10),
            'email' => $this->string(128),
            'phone' => $this->string(64),
            'website' => $this->string(128),
            'fax'=> $this->string(64),
            'tax_code' => $this->string(64),
            'address_1' => $this->text(),
            'address_2' => $this->text(),
            'contact_id' => $this->integer(),
            'note' => $this->text(),
            'supplier_status' => $this->string(64),
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
        $this->dropTable('{{%supplier}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200427_065417_create_table_supplier cannot be reverted.\n";

        return false;
    }
    */
}
