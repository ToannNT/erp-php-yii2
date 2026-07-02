<?php

use yii\db\Migration;

/**
 * Class m220907_085510_update_table_order_ship
 */
class m220907_085510_update_table_order_ship extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("order_ship", "progress_status", $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("order_ship", "progress_status");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220907_085510_update_table_order_ship cannot be reverted.\n";

        return false;
    }
    */
}
