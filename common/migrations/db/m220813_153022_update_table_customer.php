<?php

use yii\db\Migration;

/**
 * Class m220813_153022_update_table_customer
 */
class m220813_153022_update_table_customer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn("customer", "state", "province_code");
        $this->renameColumn("customer", "city", "district_code");
        $this->addColumn("customer", "ward_code", $this->string()->after("district_code"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn("customer", "province_code", "state");
        $this->renameColumn("customer", "district_code", "city");
        $this->dropColumn("customer","ward_code");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220813_153022_update_table_customer cannot be reverted.\n";

        return false;
    }
    */
}
