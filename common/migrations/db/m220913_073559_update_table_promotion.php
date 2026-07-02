<?php

use yii\db\Migration;

/**
 * Class m220913_073559_update_table_promotion
 */
class m220913_073559_update_table_promotion extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("promotion", "apply_for_all", $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("promotion", "apply_for_all");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220913_073559_update_table_promotion cannot be reverted.\n";

        return false;
    }
    */
}
