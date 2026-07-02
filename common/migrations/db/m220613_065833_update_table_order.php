<?php

use yii\db\Migration;

/**
 * Class m220613_065833_update_table_order
 */
class m220613_065833_update_table_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{order}}", "channel", $this->string()->after("code"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("{{order}}", "channel");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220613_065833_update_table_order cannot be reverted.\n";

        return false;
    }
    */
}
