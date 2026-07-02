<?php

use yii\db\Migration;

/**
 * Class m240925_081653_create_table_tag
 */
class m240925_081653_create_table_tag extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tag}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'slug' => $this->string(100)->notNull(),
            'type' => $this->string(100)->notNull(),
            'popularity' => $this->integer()->defaultValue(0),
            'created_at' => $this->dateTime(),
            "updated_at" => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%tag}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240925_081653_create_table_tag cannot be reverted.\n";

        return false;
    }
    */
}
