<?php

use yii\db\Migration;

/**
 * Class m220928_064810_update_table_order
 */
class m220928_064810_update_table_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("order", "data_other_fee", $this->json()->after("data_payments")->defaultValue("[]"));
        $this->addColumn("order", "other_fee", $this->double()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("order", "data_other_fee");
        $this->dropColumn("order", "other_fee");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220928_064810_update_table_order cannot be reverted.\n";

        return false;
    }
    */
}
