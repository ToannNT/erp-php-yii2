<?php

use yii\db\Migration;

/**
 * Class m221018_100031_update_table_order
 */
class m221018_100031_update_table_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("order", "done_at", $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("order","done_at");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221018_100031_update_table_order cannot be reverted.\n";

        return false;
    }
    */
}
