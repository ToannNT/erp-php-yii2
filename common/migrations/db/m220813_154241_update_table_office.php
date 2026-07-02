<?php

use yii\db\Migration;

/**
 * Class m220813_154241_update_table_office
 */
class m220813_154241_update_table_office extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn("office","state","province_code");
        $this->renameColumn("office", "city", "district_code");
        $this->addColumn("office", "ward_code", $this->string()->after("district_code"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn("office", "province_code", "state");
        $this->renameColumn("office", "district_code", "city");
        $this->dropColumn("office","ward_code");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220813_154241_update_table_office cannot be reverted.\n";

        return false;
    }
    */
}
