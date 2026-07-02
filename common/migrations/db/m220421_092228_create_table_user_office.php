<?php

use yii\db\Migration;

/**
 * Class m220421_092228_create_table_user_office
 */
class m220421_092228_create_table_user_office extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_office}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'office_id' => $this->integer(),
            'status' => $this->integer()->defaultValue(1),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{%user_office}}");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220421_092228_create_table_user_office cannot be reverted.\n";

        return false;
    }
    */
}
