<?php

use yii\db\Migration;

/**
 * Class m200706_090951_update_table_customer
 */
class m200706_090951_update_table_customer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%customer}}', 'code', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('{{%customer}}', 'code');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200706_090951_update_table_customer cannot be reverted.\n";

        return false;
    }
    */
}
