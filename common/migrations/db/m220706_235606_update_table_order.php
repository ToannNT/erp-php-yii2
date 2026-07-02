<?php

use yii\db\Migration;

/**
 * Class m220706_235606_update_table_order
 */
class m220706_235606_update_table_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("order", "return_note", $this->text()->after("note"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("order", "return_note");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220706_235606_update_table_order cannot be reverted.\n";

        return false;
    }
    */
}
