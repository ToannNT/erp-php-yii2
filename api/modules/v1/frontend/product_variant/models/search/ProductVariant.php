<?php

namespace api\modules\v1\frontend\product_variant\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\modules\v1\frontend\product_variant\models\ProductVariant as ProductVariantModel;

/**
 * ProductVariant represents the model behind the search form of `api\modules\v1\frontend\product_variant\models\ProductVariant`.
 */
class ProductVariant extends ProductVariantModel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'product_id', 'product_asset_id', 'inventory_quantity', 'requires_shipping_address', 'grams', 'position', 'visible', 'color_id', 'status'], 'integer'],
            [['name', 'slug', 'code', 'sku', 'barcode', 'meta_field', 'option_ids', 'custom_price', 'inventory_management', 'inventory_policy', 'unit_type', 'weight_type', 'dimension', 'group_id', 'created_at', 'updated_at', 'deleted_at', 'images', 'extra_fields'], 'safe'],
            [['unit_price', 'sll_price', 'import_price', 'weight'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ProductVariantModel::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_asset_id' => $this->product_asset_id,
            'unit_price' => $this->unit_price,
            'sll_price' => $this->sll_price,
            'import_price' => $this->import_price,
            'inventory_quantity' => $this->inventory_quantity,
            'requires_shipping_address' => $this->requires_shipping_address,
            'grams' => $this->grams,
            'weight' => $this->weight,
            'position' => $this->position,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'visible' => $this->visible,
            'color_id' => $this->color_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'sku', $this->sku])
            ->andFilterWhere(['like', 'barcode', $this->barcode])
            ->andFilterWhere(['like', 'meta_field', $this->meta_field])
            ->andFilterWhere(['like', 'option_ids', $this->option_ids])
            ->andFilterWhere(['like', 'custom_price', $this->custom_price])
            ->andFilterWhere(['like', 'inventory_management', $this->inventory_management])
            ->andFilterWhere(['like', 'inventory_policy', $this->inventory_policy])
            ->andFilterWhere(['like', 'unit_type', $this->unit_type])
            ->andFilterWhere(['like', 'weight_type', $this->weight_type])
            ->andFilterWhere(['like', 'dimension', $this->dimension])
            ->andFilterWhere(['like', 'group_id', $this->group_id])
            ->andFilterWhere(['like', 'images', $this->images])
            ->andFilterWhere(['like', 'extra_fields', $this->extra_fields]);

        return $dataProvider;
    }
}
