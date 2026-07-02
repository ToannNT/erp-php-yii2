<?php

use yii\db\Migration;

/**
 * Class m220616_085814_update_table_order
 */
class m220616_085814_update_table_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("order", "data_payments", $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("order", "data_payments");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220616_085814_update_table_order cannot be reverted.\n";

        return false;
    }
    */
}
