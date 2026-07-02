<?php

use yii\db\Migration;

/**
 * Class m220804_031523_update_table_order
 */
class m220804_031523_update_table_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn("order", "progess_status", "progress_status");
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
        echo "m220804_031523_update_table_order cannot be reverted.\n";

        return false;
    }
    */
}
