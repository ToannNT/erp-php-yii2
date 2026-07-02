<?php

use yii\db\Migration;

/**
 * Class m220726_092601_update_table_order
 */
class m220726_092601_update_table_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("order", "progess_status", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("order", "progess_status");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220726_092601_update_table_order cannot be reverted.\n";

        return false;
    }
    */
}
