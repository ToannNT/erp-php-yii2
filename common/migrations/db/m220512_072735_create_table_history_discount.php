<?php

use yii\db\Migration;

/**
 * Class m220512_072735_create_table_history_discount
 */
class m220512_072735_create_table_history_discount extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("{{history_discount}}", [
            "id" => $this->primaryKey(),
            "order_id" => $this->integer(),
            "code_id" => $this->integer(),
            "status" => $this->integer(),
            "created_at" => $this->dateTime(),
            "updated_at" => $this->dateTime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{history_discount}}");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220512_072735_create_table_history_discount cannot be reverted.\n";

        return false;
    }
    */
}
