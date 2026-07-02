<?php

use yii\db\Migration;

/**
 * Class m220511_023523_create_table_category_branch
 */
class m220511_023523_create_table_category_brand extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("{{category_brand}}", [
            "id" => $this->primaryKey(),
            "brand_id" => $this->integer(),
            "category_id" => $this->integer(),
            "status" => $this->integer(),
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
        $this->dropTable("{{category_brand}}");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220511_023523_create_table_category_branch cannot be reverted.\n";

        return false;
    }
    */
}
