<?php

use yii\db\Migration;

/**
 * Class m230315_090507_update_table_order_ship
 */
class m230315_090507_update_table_order_ship extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn("order_ship", "payment_type_id", "payment_type");
        $this->alterColumn("order_ship", "payment_type", $this->string(50));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn("order_ship", "payment_type", $this->integer());
        $this->renameColumn("order_ship", "payment_type", "payment_type_id");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230315_090507_update_table_order_ship cannot be reverted.\n";

        return false;
    }
    */
}
