<?php

use yii\db\Migration;

/**
 * Class m220927_032355_update_table_order_ship
 */
class m220927_032355_update_table_order_ship extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn("order_ship", "fee", "ship_fee");
        $this->addColumn("order_ship", "insurance_fee", $this->double()->after("total_fee")->defaultValue(0));
        $this->addColumn("order_ship", "total_other_fee", $this->double()->after("insurance_fee")->defaultValue(0));
        $this->addColumn("order_ship", "data_other_fee", $this->json()->after("total_other_fee")->defaultValue("[]"));
        $this->addColumn("order_ship", "payments", $this->double()->after("total_other_fee")->defaultValue(0));
        $this->alterColumn("order_ship", "progress_status", $this->json()->after("data_other_fee")->defaultValue("[]"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("order_ship", "insurance_fee");
        $this->dropColumn("order_ship", "total_other_fee");
        $this->dropColumn("order_ship", "data_other_fee");
        $this->dropColumn("order_ship", "payments");
        $this->renameColumn("order_ship", "ship_fee", "fee");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220927_032355_update_table_order_ship cannot be reverted.\n";

        return false;
    }
    */
}
