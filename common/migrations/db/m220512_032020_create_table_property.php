<?php

use yii\db\Migration;

/**
 * Class m220512_032020_create_table_property
 */
class m220512_032020_create_table_property extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("{{property}}",[
            "id" => $this->primaryKey(),
            "property_title" => $this->text(),
            "created_at" => $this->dateTime(),
            "updated_at" => $this->dateTime()
    ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{property}}");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220512_032020_create_table_property cannot be reverted.\n";

        return false;
    }
    */
}
