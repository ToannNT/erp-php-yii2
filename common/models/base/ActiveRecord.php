<?php
/**
 * Smart Digitech LLC (c) 2016, All right reverse
 * Fast Boy Marketing CRM Project
 */

namespace common\models\base;

use Yii;

use Hashids\Hashids;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord as BaseActiveRecord;
use Carbon\Carbon;
use yii\db\Expression;

/**
 *Active Record class it support base soft delete, timestamp attribute...
 * Who implements it must be have common columns for support features.
 *
 * @property int $created_at
 * @property int $updated_at
 * @property int $status
 * @property string $updatedDate
 * @property string $createdDate
 * @property string $statusHtml
 *
 * @property string $encodedPrimaryKey
 *
 * @method void touch($attribute)
 *
 * @author Vuong Minh <minhvx@dtsmart.vn>
 * @since 2.0.0
 */
class ActiveRecord extends BaseActiveRecord
{

    /**
     * Create scenario
     */
    const SCENARIO_CREATE = 'create';

    /**
     * Update scenario
     */
    const SCENARIO_UPDATE = 'update';

    /**
     * Delete scenario
     */
    const SCENARIO_DELETE = 'delete';

    const STATUS_ACTIVE = 1;

    const STATUS_INACTIVE = 0;

    const STATUS_DELETE = -99;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => date('Y-m-d H:i:s')
            ],

        ];
    }

    /**
     * Create encode primary key useful when you need send it to browser.
     *
     * @param $primaryKey
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public static function encodePrimaryKey($primaryKey): string
    {
        return static::createHashIds()->encode($primaryKey);
    }

    /**
     * Primary key decode useful when you need extract it to primary key.
     *
     * @param string $encodedPrimaryKey
     * @param bool $asArray
     * @return array|string
     * @throws \yii\base\InvalidConfigException
     */
    public static function decodePrimaryKey(string $encodedPrimaryKey, $asArray = false)
    {
        $primaryKey = static::createHashIds()->decode($encodedPrimaryKey);

        if (!$asArray && count($primaryKey) === 1) {
            return $primaryKey[0];
        } else {
            return $primaryKey;
        }
    }

    /**
     * @return Hashids
     * @throws \yii\base\InvalidConfigException
     */
    protected static function createHashIds(): Hashids
    {
        return Yii::createObject(Hashids::class, Yii::$app->params['arHashKeyConfig']);
    }

    /**
     * @param string $encodedKey
     * @return static|null ActiveRecord instance matching the condition, or `null` if nothing matches.
     * @throws \yii\base\InvalidConfigException
     */
    public static function findOneByEncodedKey(string $encodedKey)
    {
        $primaryKey = static::primaryKey();
        $decodedKey = static::decodePrimaryKey($encodedKey, true);
        $numKey = count($primaryKey);

        if ($numKey === count($decodedKey) && $numKey > 0) {
            return static::findOne(array_combine($primaryKey, $decodedKey));
        } else {
            return null;
        }
    }

    /**
     * Get current primary key encoded.
     *
     * @return string encoded primary key.
     * @throws \yii\base\InvalidConfigException
     */
    public function getEncodedPrimaryKey()
    {
        return static::encodePrimaryKey($this->getPrimaryKey());
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        return array_merge([
            self::SCENARIO_CREATE => $scenarios[self::SCENARIO_DEFAULT],
            self::SCENARIO_UPDATE => $scenarios[self::SCENARIO_DEFAULT],
            self::SCENARIO_DELETE => $scenarios[self::SCENARIO_DEFAULT]
        ], $scenarios);
    }


    /**
     * @return string
     */
    public function getStatusHtml()
    {
        if (isset($this->status)) {
            if ($this->status == self::STATUS_ACTIVE) {
                return '<span class="glyphicon glyphicon-ok text-success"></span>';
            }
        }
        return '<span class="glyphicon glyphicon-remove text-danger"></span>';
    }
}