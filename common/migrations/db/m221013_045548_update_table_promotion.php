<?php

use yii\db\Migration;

/**
 * Class m221013_045548_update_table_promotion
 */
class m221013_045548_update_table_promotion extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn("promotion", "condition_items", $this->json());
        $this->alterColumn("promotion", "offices", $this->json());
        $this->alterColumn("promotion", "group_customers", $this->json());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn("promotion", "condition_items", $this->text());
        $this->alterColumn("promotion", "offices", $this->text());
        $this->alterColumn("promotion", "group_customers", $this->text());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221013_045548_update_table_promotion cannot be reverted.\n";

        return false;
    }
    */
}
