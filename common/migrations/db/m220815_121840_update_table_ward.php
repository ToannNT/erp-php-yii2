<?php

use yii\db\Migration;

/**
 * Class m220815_121840_update_table_ward
 */
class m220815_121840_update_table_ward extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("ward", "code_ghn", $this->string()->after("code"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("ward", "code_ghn");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220815_121840_update_table_ward cannot be reverted.\n";

        return false;
    }
    */
}
