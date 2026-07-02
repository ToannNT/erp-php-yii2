<?php

use yii\db\Migration;

/**
 * Class m200520_095949_create_table_stocktaking
 */
class m200520_095949_create_table_stocktaking extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%stocktaking}}', [
            'id'=>$this->primaryKey(),
            'code'=>$this->string(),
            'office_id'=>$this->integer(),
            'inventory_id'=>$this->integer(),
            'note'=>$this->text(),
            'tags'=>$this->text(),
            'total_difference'=>$this->integer(),
            'total_adjustment'=>$this->integer(),
            'created_by'=>$this->integer(),
            'stocktaking_date'=>$this->dateTime(),
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
        $this->dropTable('{{%stocktaking}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200520_095949_create_table_stocktaking cannot be reverted.\n";

        return false;
    }
    */
}
