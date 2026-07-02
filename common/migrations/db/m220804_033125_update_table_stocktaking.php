<?php

use yii\db\Migration;

/**
 * Class m220804_033125_update_table_stocktaking
 */
class m220804_033125_update_table_stocktaking extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn("stocktaking", "progess_status", "progress_status");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "changed";
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220804_033125_update_table_stocktaking cannot be reverted.\n";

        return false;
    }
    */
}
