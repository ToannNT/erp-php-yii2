<?php

use yii\db\Migration;

/**
 * Class m220726_075645_update_table_promotion
 */
class m220726_075645_update_table_promotion extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("promotion", "deleted_at", $this->string());
        $this->addColumn("promotion", "offices", $this->string()->after("deleted_at"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("promotion", "deleted_at");
        $this->dropColumn("promotion", "offices");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220726_075645_update_table_promotion cannot be reverted.\n";

        return false;
    }
    */
}
