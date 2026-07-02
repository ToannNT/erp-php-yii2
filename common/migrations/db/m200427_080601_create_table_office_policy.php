<?php

use yii\db\Migration;

/**
 * Class m200427_080601_create_table_office_policy
 */
class m200427_080601_create_table_office_policy extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%office_policy}}', [
            'id' => $this->primaryKey()->comment('ID'),
            'office_id' => $this->char(255)->comment('Office ID'),
            'name' => $this->string(255)->comment('Name of the office policy'),
            'description' => $this->text()->comment('Description of the policy'),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'deleted_at' => $this->dateTime(),
            'status' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%office_policy}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200427_080601_create_table_office_policy cannot be reverted.\n";

        return false;
    }
    */
}
