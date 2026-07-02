<?php

use yii\db\Migration;

/**
 * Class m200423_085940_create_table_group
 */
class m200423_085940_create_table_group extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%group}}', [
            'id'=>$this->primaryKey(),
            'name'=>$this->string(512),
            'description'=>$this->text(),
            'type'=>$this->text(),
            'created_at'=>$this->dateTime(),
            'updated_at'=>$this->dateTime(),
            'deleted_at'=>$this->dateTime(),
            'status'=>$this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%group}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200423_085940_create_table_group cannot be reverted.\n";

        return false;
    }
    */
}
