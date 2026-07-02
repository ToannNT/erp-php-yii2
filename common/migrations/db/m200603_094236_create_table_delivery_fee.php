<?php

use yii\db\Migration;

/**
 * Class m200603_094236_create_table_delivery_fee
 */
class m200603_094236_create_table_delivery_fee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%delivery_fee}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'price' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'status' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%delivery_fee}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200603_094236_create_table_delivery_fee cannot be reverted.\n";

        return false;
    }
    */
}
