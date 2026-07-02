<?php

use yii\db\Migration;

/**
 * Class m220701_072930_update_table_order
 */
class m220701_072930_update_table_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("order", "promotion_id", $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("order", "promotion_id");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220701_072930_update_table_order cannot be reverted.\n";

        return false;
    }
    */
}
