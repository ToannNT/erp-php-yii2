<?php

use yii\db\Migration;

/**
 * Class m220815_073441_update_table_province
 */
class m220815_073441_update_table_province extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("province", "code_ghn", $this->string()->after("code"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("province", "code_ghn");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220815_073441_update_table_province cannot be reverted.\n";

        return false;
    }
    */
}
