<?php

use yii\db\Migration;

/**
 * Class m220816_115209_update_table_promotion
 */
class m220816_115209_update_table_promotion extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn("promotion", "offices", $this->text());
        $this->alterColumn("promotion", "office_ids", $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220816_115209_update_table_promotion cannot be reverted.\n";

        return false;
    }
    */
}
