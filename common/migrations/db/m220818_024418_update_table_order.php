<?php

use yii\db\Migration;

/**
 * Class m220818_024418_update_table_order
 */
class m220818_024418_update_table_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("order", "type", $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("order", "type");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220818_024418_update_table_order cannot be reverted.\n";

        return false;
    }
    */
}
