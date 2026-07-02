<?php

use yii\db\Migration;

/**
 * Class m200428_065637_create_table_inventory_receipt
 */
class m200428_065637_create_table_inventory_receipt extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%inventory_receipt}}', [
            'id'=>$this->primaryKey(),
            'code' => $this->string(128),
            'office_id'=>$this->integer(),
            'inventory_id' => $this->integer(),
            'supplier_id' => $this->integer(),
            'receipt_status' => $this->integer()->defaultValue(0),
            'payment_status' => $this->integer()->defaultValue(0),
            'import_status' => $this->integer()->defaultValue(0),
            'total_price' => $this->double(),
            'other_cost' => $this->text(),
            'total_cost' => $this->double(),
            'total_discount_type' => $this->text(),
            'total_discount_value' => $this->double(),
            'tags' => $this->text(),
            'references'=> $this->text(),
            'note' => $this->text(),
            'import_date' => $this->dateTime(),
            'created_by' => $this->integer(),
            'priority' => $this->integer()->defaultValue(0),
            'owner_id' => $this->integer(),
            'group_id' => $this->text(),
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
        $this->dropTable('{{%inventory_receipt}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200428_065637_create_table_inventory_receipt cannot be reverted.\n";

        return false;
    }
    */
}
