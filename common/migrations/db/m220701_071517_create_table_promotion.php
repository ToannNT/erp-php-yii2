<?php

use yii\db\Migration;

/**
 * Class m220701_071517_create_table_promotion
 */
class m220701_071517_create_table_promotion extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("promotion", [
            "id" => $this->primaryKey(),
            "title" => $this->string(255),
            "code"  => $this->string(255),
            "description" => $this->text(),
            "discount_type" => $this->integer(),
            "discount_value" => $this->float(),
            "start_date" => $this->dateTime(),
            "end_date" => $this->dateTime(),
            "limit" => $this->integer(),
            "used" => $this->integer(),
            "order_total_required" => $this->float(),
            "group_customers" => $this->text(),
            "status" => $this->integer(),
            "office_ids" => $this->string(255),
            "condition_items" => $this->text(),
            "condition_type"  => $this->text(),
            "promotion_type" => $this->integer(),
            "created_at" => $this->dateTime(),
            "updated_at" => $this->dateTime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("promotion");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220701_071517_create_table_promotion cannot be reverted.\n";

        return false;
    }
    */
}
