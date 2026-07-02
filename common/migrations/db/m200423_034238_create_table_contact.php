<?php

use yii\db\Migration;

/**
 * Class m200423_034238_create_table_contact
 */
class m200423_034238_create_table_contact extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%contact}}', [
            'id'=>$this->primaryKey(),
            'unique_external_id'=>$this->integer(),
            'first_name'=>$this->string(255),
            'last_name'=>$this->string(255),
            'name'=>$this->string(255),
            'description'=>$this->text(),
            'note'=>$this->text(),
            'phone'=>$this->string(255),
            'email'=>$this->string(255),
            'type'=>$this->string(255),
            'postal_code'=>$this->string(255),
            'time_zone'=>$this->string(512),
            'address_1'=>$this->string(512),
            'address_2'=>$this->string(512),
            'country'=>$this->string(512),
            'state'=>$this->string(512),
            'city'=>$this->string(512),
            'avatar'=>$this->text(),
            'created_at'=>$this->dateTime(),
            'updated_at'=>$this->dateTime(),
            'deleted_at'=>$this->dateTime(),
            'status'=>$this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%contact}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200423_034238_create_table_contact cannot be reverted.\n";

        return false;
    }
    */
}
