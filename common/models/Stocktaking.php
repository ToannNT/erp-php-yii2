<?php

namespace common\models;

use Yii;
use \common\models\base\Stocktaking as BaseStocktaking;
use yii\helpers\ArrayHelper;

/**
 * Class Stocktaking
 * @property StocktakingItem[] $stocktakingItem
 * @property Office $office
 * @property Inventory $inventory
 * @property User $createdBy
 * @property string $tagsHtml
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class Stocktaking extends BaseStocktaking
{
    const STATUS_PENDING = 0;
    const STATUS_DONE = 1;
    const STATUS_DELETE = -99;
    const STATUS_CANCEL = -1;

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStocktakingItem()
    {
        return $this->hasMany(StocktakingItem::class, ['stocktaking_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStocktakingItems()
    {
        return $this->hasMany(StocktakingItem::class, ['stocktaking_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffice()
    {
        return $this->hasOne(Office::class, ['id' => 'office_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInventory()
    {
        return $this->hasOne(Inventory::class, ['id' => 'inventory_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getTagsHtml()
    {
        $tags = json_decode($this->tags);
        $str = '';
        if (is_array($tags) && count($tags) > 0) {
            foreach ($tags as $tag) {
                $str .= '<span class="btn btn-sm btn-light ml-1">' . $tag . '</span>';
            }
        }

        return $str;
    }

    public function getStatus()
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                return Yii::t("api", "Pending");
            case self::STATUS_DONE:
                return Yii::t("api", "Done");
        }
    }

    public function setFormatCode()
    {
        $tmp = sprintf("%'.07d", $this->id);
        $this->code = 'STON' . $tmp;
    }
}
