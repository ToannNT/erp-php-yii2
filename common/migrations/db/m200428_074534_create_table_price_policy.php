<?php

use yii\db\Migration;

/**
 * Class m200428_074534_create_table_price_policy
 */
class m200428_074534_create_table_price_policy extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%price_policy}}', [
            'id' => $this->primaryKey()->comment('ID'),
            'office_ids' => $this->char(255)->comment('Office ID'),
            'name' => $this->string(255)->comment('Name of the office policy'),
            'code' => $this->string(64),
            'type' => $this->integer()->comment("Type of Price"),
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
        $this->dropTable('{{%price_policy}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200428_074534_create_table_price_policy cannot be reverted.\n";

        return false;
    }
    */
}
