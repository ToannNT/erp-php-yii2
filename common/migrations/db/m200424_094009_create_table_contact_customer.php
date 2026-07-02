<?php

use yii\db\Migration;

/**
 * Class m200424_094009_create_table_contact_customer
 */
class m200424_094009_create_table_contact_customer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%contact_customer}}', [
            'id' => $this->primaryKey(),
            'contact_id' => $this->integer(),
            'customer_id' => $this->integer(),
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
        $this->dropTable('{{%contact_customer}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200424_094009_create_table_contact_customer cannot be reverted.\n";

        return false;
    }
    */
}
