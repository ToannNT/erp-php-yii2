<?php

use yii\db\Migration;

/**
 * Class m200427_072238_create_table_product_asset
 */
class m200427_072238_create_table_product_asset extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%product_asset}}', [
            'id'=>$this->primaryKey(),
            'type' => $this->integer(),
            'src' => $this->text(),
            'product_id' => $this->integer(),
            'product_variant_ids' => $this->text(),
            'group_id' => $this->text(),
            'priority' => $this->integer()->defaultValue(0),
            'created_at'=>$this->dateTime(),
            'updated_at'=>$this->dateTime(),
            'deleted_at'=>$this->dateTime(),
            'status'=>$this->integer()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%product_asset}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200427_072238_create_table_product_image cannot be reverted.\n";

        return false;
    }
    */
}
