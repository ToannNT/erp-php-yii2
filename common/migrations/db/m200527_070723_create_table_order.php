<?php

use yii\db\Migration;

/**
 * Class m200527_070723_create_table_order
 */
class m200527_070723_create_table_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%order}}', [
            'id'=>$this->primaryKey(),
            'office_id'=>$this->integer(),
            'inventory_id'=>$this->integer(),
            'client_id'=>$this->integer(),
            'price_policy'=>$this->string(),
            'tax'=>$this->string(),
            'shipping_address'=>$this->text(),
            'order_address'=>$this->text(),
            'note'=>$this->text(),
            'tags'=>$this->text(),
            'total_price'=>$this->float(),
            'tax_price'=>$this->float(),
            'discount'=>$this->float(),
            'delivery_fee'=>$this->float(),
            'payments'=>$this->text(),
            'delivery'=>$this->text(),
            'created_by'=>$this->integer(),
            'created_at'=>$this->dateTime(),
            'updated_at'=>$this->dateTime(),
            'deleted_at'=>$this->dateTime(),
            'status'=>$this->integer(),
            'data_tax'=>$this->text(),
            'data_discount'=>$this->text(),
            'data_delivery_fee'=>$this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%order}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200527_070723_create_table_order cannot be reverted.\n";

        return false;
    }
    */
}
