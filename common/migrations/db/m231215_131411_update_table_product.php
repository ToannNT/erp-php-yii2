<?php

use yii\db\Migration;

/**
 * Class m231215_131411_update_table_product
 */
class m231215_131411_update_table_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("product", "specifications", $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("product", "specifications");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231215_131411_update_table_product cannot be reverted.\n";

        return false;
    }
    */
}
