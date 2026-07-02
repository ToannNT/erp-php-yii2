<?php

use yii\db\Migration;

/**
 * Class m220614_105407_update_table_order_item
 */
class m220614_105407_update_table_order_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("order_item", "note", $this->text()->after("created_at"));
        $this->addColumn("order_item", "discount", $this->integer()->after("note"));
        $this->addColumn("order_item", "data_discount", $this->text()->after("discount"));
        $this->addColumn("order_item", "tax_price", $this->integer()->after("data_discount"));
        $this->addColumn("order_item", "data_tax", $this->integer()->after("tax_price"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("order_item", "note");
        $this->dropColumn("order_item", "discount");
        $this->dropColumn("order_item", "data_discount");
        $this->dropColumn("order_item", "tax_price");
        $this->dropColumn("order_item", "data_tax");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220614_105407_update_table_order_item cannot be reverted.\n";

        return false;
    }
    */
}
