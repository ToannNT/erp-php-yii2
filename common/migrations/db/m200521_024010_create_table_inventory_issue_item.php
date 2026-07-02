<?php

use yii\db\Migration;

/**
 * Class m200521_024010_create_table_inventory_issue_item
 */
class m200521_024010_create_table_inventory_issue_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%inventory_issue_item}}', [
            'id'=>$this->primaryKey(),
            'inventory_issue_id'=>$this->integer(),
            'product_id'=>$this->integer(),
            'product_variant_id'=>$this->integer(),
            'number_inventory'=>$this->integer(),
            'quantity'=>$this->integer(),
            'created_at'=>$this->dateTime(),
            'updated_at'=>$this->dateTime(),
            'deleted_at'=>$this->dateTime(),
            'status'=>$this->integer()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%inventory_issue_item}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200521_024010_create_table_inventory_issue_item cannot be reverted.\n";

        return false;
    }
    */
}
