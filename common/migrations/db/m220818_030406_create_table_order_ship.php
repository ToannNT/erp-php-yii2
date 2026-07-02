<?php

use yii\db\Migration;

/**
 * Class m220818_030406_create_table_order_ship
 */
class m220818_030406_create_table_order_ship extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("{{order_ship}}", [
            "id" => $this->primaryKey(),
            "order_id" => $this->integer(),
            "sender_name" => $this->string(255),
            "sender_province_id" => $this->string(255),
            "sender_district_id" => $this->string(255),
            "sender_ward_id" => $this->string(255),
            "sender_address" => $this->string(500),
            "sender_phone" => $this->string(255),
            "sender_email" => $this->string(255),
            "order_code" => $this->string(255),
            "receiver_name" => $this->string(255),
            "receiver_province_id" => $this->string(255),
            "receiver_district_id" => $this->string(255),
            "receiver_ward_id" => $this->string(255),
            "receiver_address" => $this->string(500),
            "receiver_phone" => $this->string(255),
            "receiver_email" => $this->string(255),
            "status" => $this->integer(),
            "cod" => $this->double(),
            "payment_type_id" => $this->integer(),
            "note" => $this->text(),
            "value" => $this->double(),
            "shipper_note" => $this->text(),
            "weight" => $this->double(),
            "length" => $this->double(),
            "width" => $this->double(),
            "height" => $this->double(),
            "coupon" => $this->string(),
            "transport" => $this->string(),
            "pick_shift" => $this->string(),
            "extra_fields" => $this->text(),
            "extra_shipper" => $this->text(),
            "weight_option" => $this->string(),
            "shipper_type" => $this->string(),
            "shipper_id" => $this->integer(),
            "expected_delivery_time" => $this->timestamp(),
            "partner_code" => $this->string(),
            "fee" => $this->double(),
            "total_fee" => $this->double(),
            "created_at" => $this->dateTime(),
            "updated_at" => $this->dateTime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("order_ship");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220818_030406_create_table_order_ship cannot be reverted.\n";

        return false;
    }
    */
}
