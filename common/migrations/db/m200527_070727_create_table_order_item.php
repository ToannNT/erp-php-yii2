<?php

use yii\db\Migration;

/**
 * Class m200527_070727_create_table_order_item
 */
class m200527_070727_create_table_order_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%order_item}}', [
            'id'=>$this->primaryKey(),
            'order_id'=>$this->integer(),
            'product_id'=>$this->integer(),
            'product_variant_id'=>$this->integer(),
            'number_inventory'=>$this->integer(),
            'unit_price'=>$this->integer(),
            'quantity'=>$this->integer(),
            'total_price'=>$this->integer(),
            'created_at'=>$this->dateTime(),
            'updated_at'=>$this->dateTime(),
            'deleted_at'=>$this->dateTime(),
            'status'=>$this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%order_item}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200527_070727_create_table_order_item cannot be reverted.\n";

        return false;
    }
    */
}
