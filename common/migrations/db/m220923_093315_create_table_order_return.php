<?php

use yii\db\Migration;

/**
 * Class m220923_093315_create_table_order_return
 */
class m220923_093315_create_table_order_return extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $this->createTable("order_return", [
            "id" => $this->primaryKey(),
            "client_id" => $this->integer(),
            "code" => $this->string(),
            "order_id" => $this->integer(),
            "discount_value" => $this->double(),
            "discount_type" => $this->integer(),
            "discount" => $this->double()->defaultValue(0),
            "delivery_fee" => $this->double()->defaultValue(0),
            "data_delivery_fee" => $this->json(),
            "other_fee" => $this->json(),
            "status" => $this->integer(),
            "progress_status" => $this->json(),
            "quantity" => $this->integer()->defaultValue(0),
            "unit_price" => $this->double()->defaultValue(0),
            "total_price" => $this->double()->defaultValue(0),
            "payments" => $this->double()->defaultValue(0),
            "sub_total_price" => $this->double()->defaultValue(0),
            "created_by" => $this->integer(),
            "note" => $this->text(),
            "office_id" => $this->integer(),
            "inventory_id" => $this->integer(),
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
        $this->dropTable("order_return");
    }
}
