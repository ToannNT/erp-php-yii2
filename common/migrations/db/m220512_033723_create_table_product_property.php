<?php

use yii\db\Migration;

/**
 * Class m220512_033723_create_table_product_property
 */
class m220512_033723_create_table_product_property extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("{{product_property}}",[
            "id" => $this->primaryKey(),
            "product_id" => $this->integer(),
            "product_variant_id" => $this->integer(),
            "property_content" => $this->text(),
            "type" => $this->integer(),
            "parent_id" => $this->integer(),
            "group_id" => $this->integer(),
            "created_at" => $this->dateTime(),
            "updated_at" => $this->dateTime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{product_property}}");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220512_033723_create_table_product_property cannot be reverted.\n";

        return false;
    }
    */
}
