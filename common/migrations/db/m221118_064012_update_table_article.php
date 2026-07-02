<?php

use yii\db\Migration;

/**
 * Class m221118_064012_update_table_article
 */
class m221118_064012_update_table_article extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn("article_category", "slug", $this->string(1024));
        $this->alterColumn("article", "slug", $this->string(1024));
        $this->alterColumn("article_attachment", "path", $this->string(255));
        $this->addColumn("article_category", "deleted_at", $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn("article_category", "slug", $this->string(1024)->notNull());
        $this->alterColumn("article", "slug", $this->string(1024)->notNull());
        $this->alterColumn("article_attachment", "path", $this->string(255)->notNull());
        $this->dropColumn("article_category", "deleted_at");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221118_064012_update_table_article cannot be reverted.\n";

        return false;
    }
    */
}
