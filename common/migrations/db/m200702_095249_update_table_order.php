<?php

use yii\db\Migration;

/**
 * Class m200702_095249_update_table_order
 */
class m200702_095249_update_table_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%order}}', 'code', $this->string());
        $this->addColumn('{{%order}}', 'quantity', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('{{%order}}', 'code');
        $this->dropColumn('{{%order}}', 'quantity');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200702_095249_update_table_order cannot be reverted.\n";

        return false;
    }
    */
}
