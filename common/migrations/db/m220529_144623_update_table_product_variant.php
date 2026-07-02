<?php

use yii\db\Migration;

/**
 * Class m220529_144623_update_table_product_variant
 */
class m220529_144623_update_table_product_variant extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{product_variant}}", "slug", $this->string(255)->after("name"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("{{product_variant}}","slug");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220529_144623_update_table_product_variant cannot be reverted.\n";

        return false;
    }
    */
}
