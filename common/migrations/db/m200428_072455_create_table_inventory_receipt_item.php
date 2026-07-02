<?php

use yii\db\Migration;

/**
 * Class m200428_072455_create_table_inventory_receipt_item
 */
class m200428_072455_create_table_inventory_receipt_item extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%inventory_receipt_item}}', [
            'id'=>$this->primaryKey(),
            'receipt_id' => $this->string(128),
            'product_id'=>$this->integer(),
            'product_variant_id' => $this->integer(),
            'quantity' => $this->integer(),
            'product_serials' => $this->text(),
            'unit_price' => $this->double()->defaultValue(0),
            'total_price' => $this->double(),
            'other_cost' => $this->text(),
            'total_cost' => $this->double(),
            'tax' => $this->double(),
            'discount_type' => $this->text(),
            'discount_value' => $this->double(),
            'references'=> $this->text(),
            'note' => $this->text(),
            'owner_id' => $this->integer(),
            'created_at'=>$this->dateTime(),
            'updated_at'=>$this->dateTime(),
            'deleted_at'=>$this->dateTime(),
            'status'=>$this->integer()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%inventory_receipt_item}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200428_072455_create_table_inventory_receipt_item cannot be reverted.\n";

        return false;
    }
    */
}
