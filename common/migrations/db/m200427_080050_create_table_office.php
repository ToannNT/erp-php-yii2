<?php

use yii\db\Migration;

/**
 * Class m200427_080050_create_table_office
 */
class m200427_080050_create_table_office extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%office}}', [
            'id' => $this->primaryKey(),
            'type' => $this->char(255),
            'name' => $this->string(255),
            'description' => $this->string(512),
            'custom_fields' => $this->json(),
            'domains' => $this->string(255),
            'note' => $this->json(),
            'postal_code' => $this->string(255),
            'health_score' => $this->string(255),
            'account_tier' => $this->string(255),
            'renewal_date' => $this->string(255),
            'industry' => $this->string(255),
            'work_phone' => $this->string(512),
            'address1' => $this->string(512),
            'address2' => $this->string(512),
            'state' => $this->string(512),
            'city' => $this->string(512),
            'country' => $this->string(512),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'deleted_at' => $this->dateTime(),
            'status' => $this->integer(),
            'email' => $this->string(255),
            'street' => $this->string(255),
            'contact_person_id' => $this->integer(),
            'biz_phone' => $this->string(512),
            'security_code' => $this->string(512),
            'additional_information' => $this->json(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%office}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200427_080050_create_table_office cannot be reverted.\n";

        return false;
    }
    */
}
