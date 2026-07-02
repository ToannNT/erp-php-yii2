<?php

use yii\db\Migration;

/**
 * Class m200427_031705_create_table_product
 */
class m200427_031705_create_table_product extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%product}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(256),
            'type' => $this->string(32),
            'key' => $this->string(128),
            'code_id' => $this->integer(),
            'sku' => $this->string(64),
            'bar_code' => $this->string(128),
            'category_id' => $this->integer(),
            'brand_id' => $this->integer(),
            'slug' => $this->text(),
            'note' => $this->text(),
            'icon' => $this->text(),
            'images' => $this->text(),
            'custom_price' => $this->text(),
            'unit_price' => $this->double()->defaultValue(0),
            'sll_price' => $this->double()->defaultValue(0),
            'import_price' => $this->double()->defaultValue(0),
            'product_options' => $this->text(),
            'product_modifier' => $this->text(),
            'color' => $this->string(10),
            'has_tax' => $this->boolean()->defaultValue(false),
            'has_inventory' => $this->boolean()->defaultValue(false),
            'has_properties' => $this->boolean()->defaultValue(false),
            'dimension' => $this->string(64),
            'tags' => $this->string(),
            'allow_sell' => $this->boolean()->defaultValue(false),
            'weight' => $this->string(),
            'weight_type' => $this->string(),
            'priority' => $this->integer()->defaultValue(0),
            'short_description' => $this->text(),
            'description' => $this->text(),
            'warranty_description' => $this->text(),
            'product_period' => $this->text(),
            'prepare_duration' => $this->integer(),
            'parent_id' => $this->integer(),
            'owner_id' => $this->integer(),
            'group_id' => $this->text(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'deleted_at' => $this->dateTime(),
            'visible' => $this->integer(),
            'supplier_ids' => $this->text(),
            'product_status' => $this->string(32),
            'status' => $this->integer()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%product}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200427_031705_create_table_product cannot be reverted.\n";

        return false;
    }
    */
}
