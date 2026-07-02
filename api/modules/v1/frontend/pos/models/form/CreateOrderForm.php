<?php

namespace api\modules\v1\frontend\pos\models\form;

use common\components\log\BuildLogDbTarget;
use common\components\log\DbTarget;
use Throwable;
use Yii;
use common\models\Customer;

class CreateOrderForm extends CheckoutOrderForm
{

    public function fields()
    {
        return array_merge([
            "code"
        ], parent::fields());
    }


    public function rules(): array
    {
        return array_merge([
            [["client_id", "inventory_id"], "integer"],
            [["client_id"], "exist", "targetClass" => Customer::class, "targetAttribute" => ["client_id" => "id"]],
            ["price_policy", "in", "range" => [self::UNIT_PRICE, self::SLL_PRICE], 'allowArray' => true],
            ["price_policy", "default", "value" => self::UNIT_PRICE],
            [["note"], "string"],
            ["type", "default", "value" => self::TYPE_ORDER_NORMAL],
            ["external_id", "string"],
        ]);
    }

    /**
     * @throws Throwable
     */
    public function beforeSave($insert): bool
    {
        parent::beforeSave($insert);
        if ($insert) {
            $office = Yii::$app->user->identity->office;
            $inventory = Yii::$app->user->identity->inventoryFirst;
            $this->office_id = $office ? $office->id : null;
            $this->inventory_id = $inventory->id ?? null;
            $this->status = self::STATUS_ORDER;
            $this->channel = self::CHANNEL_POS;
            $this->quantity = 0;
            $this->discount = 0;
            $this->tax_price = 0;
            $this->total_price = 0;
            $this->payments = 0;
            $this->created_by = Yii::$app->user->identity->getId();
        }
        return true;
    }

    /**
     * @throws Throwable
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $this->setFormatCode();
            $this->save(false);
            (new BuildLogDbTarget())->push("Create Order POS", __METHOD__, DbTarget::TAG_CREATED, $this->getAttributes());
        }
    }
}
