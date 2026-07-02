<?php

use yii\db\Migration;

/**
 * Class m220724_041618_update_table_stocktaking
 */
class m220724_041618_update_table_stocktaking extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("stocktaking", "progess_status", $this->string(255)->after("stocktaking_date"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("stocktaking", "progess_status");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220724_041618_update_table_stocktaking cannot be reverted.\n";

        return false;
    }
    */
}
