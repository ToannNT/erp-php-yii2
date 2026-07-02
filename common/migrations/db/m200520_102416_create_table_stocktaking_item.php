<?php

use yii\db\Migration;

/**
 * Class m200520_102416_create_table_stocktaking_item
 */
class m200520_102416_create_table_stocktaking_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%stocktaking_item}}', [
            'id'=>$this->primaryKey(),
            'stocktaking_id'=>$this->integer(),
            'product_id'=>$this->integer(),
            'product_variant_id'=>$this->integer(),
            'number_inventory'=>$this->integer(),
            'number_difference'=>$this->integer(),
            'number_adjustment'=>$this->integer(),
            'reason'=>$this->text(),
            'created_at'=>$this->dateTime(),
            'updated_at'=>$this->dateTime(),
            'deleted_at'=>$this->dateTime(),
            'status'=>$this->integer()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%stocktaking_item}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200520_102416_create_table_stocktaking_item cannot be reverted.\n";

        return false;
    }
    */
}
