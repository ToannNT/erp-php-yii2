<?php

use yii\db\Migration;

/**
 * Class m221129_022510_create_table_order_discount
 */
class m221129_022510_create_table_order_discount extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("order_discount", [
            "id" => $this->primaryKey(),
            "order_id" => $this->integer(),
            "type_id" => $this->integer(),
            "type" => $this->string(255),
            "code" => $this->string(255),
            "title" => $this->string(255),
            "discount_type" => $this->integer(),
            "discount_value" => $this->double(),
            "discount_price" => $this->double(),
            "offices" => $this->json(),
            "condition_items" => $this->json(),
            "condition_type" => $this->string(),
            "apply_for_all" => $this->integer(),
            "order_total_required" => $this->double(),
            "limit" => $this->integer(),
            "used" => $this->integer(),
            "extras_fields" => $this->json(),
            "created_at" => $this->dateTime(),
            "updated_at" => $this->dateTime()
        ]);
        $this->createIndex("idx-order-discount-order_id", "order_discount", "order_id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex("idx-order-discount-order_id", "order_discount");
        $this->dropTable("order_discount");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221129_022510_create_table_order_discount cannot be reverted.\n";

        return false;
    }
    */
}
