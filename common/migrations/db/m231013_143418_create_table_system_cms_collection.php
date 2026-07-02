<?php

use yii\db\Migration;

/**
 * Class m231013_143418_create_table_system_cms_collection
 */
class m231013_143418_create_table_system_cms_collection extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("system_cms_collection", [
            "id" => $this->primaryKey(),
            "name" => $this->string("200"),
            "user_id" => $this->integer(),
            "schemas" => $this->json(),
            "indexs" => $this->json(),
            "type" => $this->integer(),
            "status" => $this->integer(),
            "list_rule" => $this->json(),
            "view_rule" => $this->json(),
            "create_rule" => $this->json(),
            "update_rule" => $this->json(),
            "delete_rule" => $this->json(),
            "options" => $this->json(),
            "external_data" => $this->json(),
            "created_at" => $this->dateTime(),
            "updated_at" => $this->dateTime()
        ]);
        $this->createIndex("idx-system_cms_collection-user_id", "system_cms_collection", "user_id");
        $this->createIndex("idx-system_cms_collection-type", "system_cms_collection", "type");
        $this->createIndex("idx-system_cms_collection-status", "system_cms_collection", "status");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("system_cms_collection");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231013_143418_create_table_system_cms_collection cannot be reverted.\n";

        return false;
    }
    */
}
