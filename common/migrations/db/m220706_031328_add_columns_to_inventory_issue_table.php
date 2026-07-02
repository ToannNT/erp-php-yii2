<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%inventory_issue}}`.
 */
class m220706_031328_add_columns_to_inventory_issue_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('inventory_issue', 'type', $this->integer());
        $this->addColumn('inventory_issue','order_id',$this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('inventory_issue','type');
        $this->dropColumn('inventory','order_id');
    }
}
