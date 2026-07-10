<?php

use yii\db\Migration;

/**
 * Handles adding is_confirm_term column to table `{{%feedback}}`.
 */
class m260710_194300_update_feedback_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%feedback}}', 'is_confirm_term', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%feedback}}', 'is_confirm_term');
    }
}
