<?php

use yii\db\Migration;

/**
 * Class m220815_090636_update_table_district
 */
class m220815_090636_update_table_district extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("district", "code_ghn", $this->string()->after("code"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("district", "code_ghn");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220815_090636_update_table_district cannot be reverted.\n";

        return false;
    }
    */
}
