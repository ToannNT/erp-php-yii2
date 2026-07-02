<?php

use yii\db\Migration;

/**
 * Class m220527_025819_create_table_product_supplier
 */
class m220527_025819_create_table_product_supplier extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("{{product_supplier}}", [
            "id" => $this->primaryKey(),
            "product_id" => $this->integer(),
            "supplier_id" => $this->integer(),
            "status" => $this->integer(),
            "created_at" => $this->dateTime(),
            "updated_at" => $this->dateTime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{product_supplier}}");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220527_025819_create_table_product_supplier cannot be reverted.\n";

        return false;
    }
    */
}
