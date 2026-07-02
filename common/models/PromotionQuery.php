<?php

namespace common\models;

use yii\db\Expression;

/**
 * This is the ActiveQuery class for [[Promotion]].
 *
 * @see Promotion
 */
class PromotionQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    public function unDelete()
    {
        $this->andWhere(["<>", "promotion.status", Promotion::STATUS_DELETE]);
        return $this;
    }

    public function active()
    {
        $this->andWhere(["[[promotion.status]]" => Promotion::STATUS_ACTIVE]);
        return $this;
    }

    public function available()
    {
        $this->andWhere([
            "or",
            [">", new Expression("`promotion`.`limit`"), new Expression("`promotion`.`used`")],
            ['is', '[[promotion.limit]]', null]
        ])
            ->andWhere([
                "or",
                [">=", "[[promotion.end_date]]", date("Y-m-d H:i:s")],
                ["is", "[[promotion.end_date]]", null]
            ]);
        return $this;
    }

    /**
     * @inheritdoc
     * @return Promotion[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Promotion|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
