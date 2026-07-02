<?php

use yii\db\Migration;

/**
 * Class m200709_102653_update_table_inventory_history
 */
class m200709_102653_update_table_inventory_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%inventory_history}}', 'link_detail', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('{{%inventory_history}}', 'link_detail');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200709_102653_update_table_inventory_history cannot be reverted.\n";

        return false;
    }
    */
}
