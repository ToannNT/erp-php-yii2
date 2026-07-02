<?php

use yii\db\Migration;

/**
 * Class m220815_073440_create_table_location
 */
class m220815_073440_create_table_location extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(file_get_contents("../database/CreateTables_vn_units.sql"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("ward");
        $this->dropTable("district");
        $this->dropTable("province");
        $this->dropTable("administrative_unit");
        $this->dropTable("administrative_region");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220905_043644_create_table_location cannot be reverted.\n";

        return false;
    }
    */
}
