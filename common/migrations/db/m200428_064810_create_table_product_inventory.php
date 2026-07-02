<?php

use yii\db\Migration;

/**
 * Class m200428_064810_create_table_product_inventory
 */
class m200428_064810_create_table_product_inventory extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%product_inventory}}', [
            'id'=>$this->primaryKey(),
            'product_id'=>$this->integer(),
            'product_variant_id' => $this->integer(),
            'inventory_id' => $this->integer(),
            'quantity' => $this->integer(),
            'prime_cost' => $this->double(),
            'unit_price'=>$this->double(),
            'policy' => $this->text(),
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
        $this->dropTable('{{%product_inventory}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200428_064810_create_table_product_inventory cannot be reverted.\n";

        return false;
    }
    */
}
