<?php

use yii\db\Migration;

/**
 * Class m220516_074240_update_table_office
 */
class m220516_074240_update_table_office extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{office}}", "latitude", $this->float());
        $this->addColumn("{{office}}", "longitude", $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("{{office}}", "{{latitude}}");
        $this->dropColumn("{{office}}", "{{longitude}}");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220516_074240_update_table_office cannot be reverted.\n";

        return false;
    }
    */
}
