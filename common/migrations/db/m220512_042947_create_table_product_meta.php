<?php

use yii\db\Migration;

/**
 * Class m220512_042947_create_table_product_meta
 */
class m220512_042947_create_table_product_meta extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{product_meta}}',[
            "id" => $this->primaryKey(),
            "product_id" => $this->integer(),
            "warranty" => $this->text(),
            "installment" => $this->text()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{product_meta}}");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220512_042947_create_table_product_meta cannot be reverted.\n";

        return false;
    }
    */
}
