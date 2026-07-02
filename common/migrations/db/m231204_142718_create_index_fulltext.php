<?php

use yii\db\Migration;

/**
 * Class m231204_142718_create_index_fulltext
 */
class m231204_142718_create_index_fulltext extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // product
        $this->execute("ALTER TABLE product ADD FULLTEXT INDEX `idx_product_fulltext` (`name`,`sku`,`slug`);");
        // product variant
        $this->execute("ALTER TABLE product_variant ADD FULLTEXT idx_product_variant_fulltext(`name`,`sku`,`slug`);");
        // category
        $this->execute("ALTER TABLE category ADD FULLTEXT idx_category_fulltext(`name`,`slug`,`code`);");
        // brand
        $this->execute("ALTER TABLE `brand` ADD FULLTEXT `idx_brand_fulltext` (`name`,`slug`,`code`);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // product
        $this->execute("ALTER TABLE product DROP INDEX idx_product_fulltext;");
        // product variant
        $this->execute("ALTER TABLE product_variant DROP INDEX idx_product_variant_fulltext;");
        // category
        $this->execute("ALTER TABLE category DROP INDEX idx_category_fulltext;");
        // brand
        $this->execute("ALTER TABLE brand DROP INDEX idx_brand_fulltext;");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231204_142718_create_index_fulltext cannot be reverted.\n";

        return false;
    }
    */
}
