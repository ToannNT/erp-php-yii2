<?php

use yii\db\Migration;

/**
 * Class m250521_074517_update_table_order_item
 */
class m250521_074517_update_table_order_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('order_item', 'name', $this->string()->after('order_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('order_item', 'name');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250519_052704_create_table_room cannot be reverted.\n";

        return false;
    }
    */
}
