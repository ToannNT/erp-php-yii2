<?php

use yii\db\Migration;

/**
 * Class m220512_035745_create_table_color
 */
class m220512_035745_create_table_color extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{color}}',[
            "id" => $this->primaryKey(),
            "name" => $this->text(),
            "value" =>$this->text(),
            "status" =>$this->integer(),
            "created_at" => $this->dateTime(),
            "updated_at" => $this->dateTime(),
            "delete_at" => $this->dateTime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{color}}");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220512_035745_create_table_color cannot be reverted.\n";

        return false;
    }
    */
}
