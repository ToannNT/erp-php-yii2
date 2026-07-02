<?php

use yii\db\Migration;

/**
 * Class m231022_104739_update_table_product_variant
 */
class m231022_104739_update_table_product_variant extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("product_variant", "extra_fields", $this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("product_variant", "extra_fields");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231022_104739_update_table_product_variant cannot be reverted.\n";

        return false;
    }
    */
}
