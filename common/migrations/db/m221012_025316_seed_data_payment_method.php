<?php

use common\models\PaymentMethod;
use yii\db\Migration;

/**
 * Class m221012_025316_seed_data_payment_method
 */
class m221012_025316_seed_data_payment_method extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert("payment_method", [
            "id" => PaymentMethod::CASH_PAYMENT,
            "name" => "Tiền Mặt",
            "code" => "TM_01",
            "status" => 1,
            "is_default" => true,
            "created_by" => 1,
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->insert("payment_method", [
            "id" => PaymentMethod::CARD_PAYMENT,
            "name" => "Quẹt Thẻ",
            "code" => "QT_02",
            "status" => 1,
            "is_default" => false,
            "created_by" => 1,
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
        $this->insert("payment_method", [
            "id" => PaymentMethod::TRANSFER_PAYMENT,
            "name" => "Chuyển Khoản",
            "code" => "CK_03",
            "status" => 1,
            "is_default" => false,
            "created_by" => 1,
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221012_025316_seed_data_payment_method cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221012_025316_seed_data_payment_method cannot be reverted.\n";

        return false;
    }
    */
}
