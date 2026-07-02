<?php

use yii\db\Migration;

/**
 * Class m200708_031920_create_table_inventory_history
 */
class m200708_031920_create_table_inventory_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%inventory_history}}', [
            'id' => $this->primaryKey(),
            'created_by' => $this->integer(),
            'action' => $this->string(256),
            'change_quantity' => $this->string(),
            'inventory' => $this->integer(),
            'voucher_code' => $this->string(),
            'office_id' => $this->integer(),
            'inventory_id' => $this->integer(),
            'product_id' => $this->integer(),
            'product_variant_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'status' => $this->integer()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%inventory_history}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200708_031920_create_table_inventory_history cannot be reverted.\n";

        return false;
    }
    */
}
