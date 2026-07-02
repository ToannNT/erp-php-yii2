<?php

use yii\db\Migration;

/**
 * Class m220706_041315_update_table_order
 */
class m220706_041315_update_table_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn("order", "total_price", $this->double());
        $this->alterColumn("order", "tax_price", $this->double());
        $this->alterColumn("order", "discount", $this->double());
        $this->alterColumn("order", "delivery_fee", $this->double());
        $this->alterColumn("order", "payments", $this->double());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220706_041315_update_table_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220706_041315_update_table_order cannot be reverted.\n";

        return false;
    }
    */
}
