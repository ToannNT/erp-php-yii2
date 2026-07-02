<?php

use yii\db\Migration;

/**
 * Class m200427_074527_create_table_product_variant
 */
class m200427_074527_create_table_product_variant extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%product_variant}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(256),
            'product_id' => $this->integer(),
            'product_asset_id' => $this->integer(),
            'code' => $this->string(128),
            'sku' => $this->string(128),
            'barcode' => $this->string(128),
            'meta_field' => $this->text(),
            'option_ids' => $this->text(),
            'custom_price' => $this->text(),
            'unit_price' => $this->double()->defaultValue(0),
            'sll_price' => $this->double()->defaultValue(0),
            'import_price' => $this->double()->defaultValue(0),
            'inventory_management' => $this->string(256),
            'inventory_policy' => $this->string(64),
            'inventory_quantity' => $this->integer(),
            'requires_shipping_address' => $this->integer(),
            'unit_type' => $this->string(32),
            'grams' => $this->integer(),
            'weight' => $this->double(),
            'weight_type' => $this->string(32),
            'dimension' => $this->string(64),
            'group_id' => $this->text(),
            'position' => $this->integer()->defaultValue(0),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'deleted_at' => $this->dateTime(),
            'visible' => $this->integer(),
            'color_id' => $this->integer(),
            'status' => $this->integer()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%product_variant}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200427_074527_create_table_product_variant cannot be reverted.\n";

        return false;
    }
    */
}
