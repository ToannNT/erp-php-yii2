<?php

use yii\db\Migration;

/**
 * Class m220923_093330_create_table_order_return_item
 */
class m220923_093330_create_table_order_return_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("order_return_item", [
            "id" => $this->primaryKey(),
            "office_id" => $this->integer(),
            "inventory_id" => $this->integer(),
            "order_return_id" => $this->integer(),
            "quantity" => $this->integer()->defaultValue(0),
            "unit_price" => $this->double()->defaultValue(0),
            "sub_total_price" => $this->double()->defaultValue(0),
            "total_price" => $this->double()->defaultValue(0),
            "discount_value" => $this->double(),
            "discount_type" => $this->integer(),
            "discount" => $this->double()->defaultValue(0),
            "status" => $this->integer(),
            "product_id" => $this->integer(),
            "product_variant_id" => $this->integer(),
            "note" => $this->text(),
            "created_at" => $this->dateTime(),
            "updated_at" => $this->dateTime(),
            "deleted_at" => $this->dateTime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("order_return_item");
    }
}
