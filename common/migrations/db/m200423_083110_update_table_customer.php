<?php

use yii\db\Migration;

/**
 * Class m200423_083110_update_table_customer
 */
class m200423_083110_update_table_customer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%customer}}', 'groups', $this->text());
        $this->addColumn('{{%customer}}', 'currency', $this->string());
        $this->addColumn('{{%customer}}', 'language', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('{{%customer}}', 'groups');
        $this->dropColumn('{{%customer}}', 'currency');
        $this->dropColumn('{{%customer}}', 'language');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200423_083110_update_table_customer cannot be reverted.\n";

        return false;
    }
    */
}
