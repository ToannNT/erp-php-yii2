<?php

use yii\db\Migration;

/**
 * Class m200423_034228_create_table_customer
 */
class m200423_034228_create_table_customer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%customer}}', [
            'id'=>$this->primaryKey(),
            'unique_external_id'=>$this->integer(),
            'owner_id'=>$this->integer(),
            'name'=>$this->string(255),
            'description'=>$this->text(),
            'note'=>$this->text(),
            'phone'=>$this->string(255),
            'biz_phone'=>$this->string(255),
            'email'=>$this->string(255),
            'website'=>$this->string(255),
            'type'=>$this->string(255),
            'postal_code'=>$this->string(255),
            'time_zone'=>$this->string(512),
            'address_1'=>$this->string(512),
            'address_2'=>$this->string(512),
            'country'=>$this->string(512),
            'state'=>$this->string(512),
            'city'=>$this->string(512),
            'avatar'=>$this->text(),
            'custom_fields'=>$this->text(),
            'additional_information'=>$this->text(),
            'created_at'=>$this->dateTime(),
            'updated_at'=>$this->dateTime(),
            'deleted_at'=>$this->dateTime(),
            'renewal_date'=>$this->dateTime(),
            'status'=>$this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%customer}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200423_034228_create_table_customer cannot be reverted.\n";

        return false;
    }
    */
}
