<?php

use yii\db\Migration;

/**
 * Class m220512_070559_create_table_discount_code
 */
class m220512_070559_create_table_discount_code extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("{{discount_code}}", [
            "id" => $this->primaryKey(),
            "product_ids" => $this->string(255),
            "code" => $this->string(255),
            "price" => $this->integer(),
            "description" => $this->text(),
            "reason" => $this->string(255),
            "limit" => $this->integer(),
            "status" => $this->integer(),
            "expired_at" => $this->dateTime(),
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
        $this->dropTable("{{discount_code}}");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220512_070559_create_table_discount_code cannot be reverted.\n";

        return false;
    }
    */
}
