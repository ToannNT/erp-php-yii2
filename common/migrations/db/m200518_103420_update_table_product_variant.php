<?php

use yii\db\Migration;

/**
 * Class m200518_103420_update_table_product_variant
 */
class m200518_103420_update_table_product_variant extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%product_variant}}', 'images', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('{{%product_variant}}', 'images');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200518_103420_update_table_product_variant cannot be reverted.\n";

        return false;
    }
    */
}
