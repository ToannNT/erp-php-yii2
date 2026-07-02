<?php

use yii\db\Migration;

/**
 * Class m200427_080509_create_table_department
 */
class m200427_080509_create_table_department extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%department}}', [
            'id' => $this->primaryKey()->comment('ID'),
            'office_id' => $this->integer()->comment('Office ID'),
            'user_id' => $this->integer()->comment('Deparment Head ID'),
            'name' => $this->string(255)->comment('Name of the Department'),
            'description' => $this->string(512)->comment('Description of the Department'),
            'custom_fields' => $this->json()->comment('Custom field'),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'deleted_at' => $this->dateTime(),
            'status' => $this->integer(),
            'additional_information' => $this->json(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%department}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200427_080509_create_table_department cannot be reverted.\n";

        return false;
    }
    */
}
