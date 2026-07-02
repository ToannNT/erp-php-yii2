<?php

use yii\db\Migration;

class m150414_195800_timeline_event extends Migration
{
    /**
     * @return bool|void
     */
    public function safeUp()
    {
        $this->createTable('{{%timeline_event}}', [
            'id' => $this->primaryKey(),
            'application' => $this->string(64)->notNull(),
            'category' => $this->string(64)->notNull(),
            'event' => $this->string(64)->notNull(),
            'data' => $this->text(),
            'created_at'=>$this->dateTime(),
            'updated_at'=>$this->dateTime(),
            'deleted_at'=>$this->dateTime(),
        ]);

        $this->createIndex('idx_created_at', '{{%timeline_event}}', 'created_at');


    }

    /**
     * @return bool|void
     */
    public function down()
    {
        $this->dropTable('{{%timeline_event}}');
    }
}
