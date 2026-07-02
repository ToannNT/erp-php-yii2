<?php

namespace common\models;

use Yii;
use \common\models\base\UserToken as BaseUserToken;
use yii\base\InvalidCallException;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user_token".
 */
class UserToken extends BaseUserToken
{

    public $tokenExpiration = 60 * 24 * 365; // in seconds
    public $defaultAccessGiven = '{"access":["all"]}';
    public const TYPE_ACTIVATION = 'activation';
    public const TYPE_PASSWORD_RESET = 'password_reset';
    public const TYPE_LOGIN_PASS = 'login_pass';
    protected const TOKEN_LENGTH = 40;
    public $defaultConsumer = 'web';

    /**
     * @param mixed $user_id
     * @param string $type
     * @param int|null $duration
     * @return bool|UserToken
     * @throws \yii\base\Exception
     */
    public static function create($user_id, $type, $duration = null)
    {
        $model = new self;
        $model->setAttributes([
            'user_id' => $user_id,
            'type' => $type,
            'token' => Yii::$app->security->generateRandomString(self::TOKEN_LENGTH),
            'expire_at' => $duration ? time() + $duration : null
        ]);

        if (!$model->save()) {
            throw new InvalidCallException;
        };

        return $model;
    }

    /**
     * @param $token
     * @param $type
     * @return bool|User
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public static function use($token, $type)
    {
        $model = self::find()
            ->where(['token' => $token])
            ->andWhere(['type' => $type])
            ->andWhere(['>', 'expire_at', time()])
            ->one();

        if ($model === null) {
            return null;
        }

        $user = $model->user;
        $model->delete();

        return $user;
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
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @param int|null $duration
     */
    public function renew($duration)
    {
        $this->updateAttributes([
            'expire_at' => $duration ? time() + $duration : null
        ]);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->token;
    }

    /**
     * Generate new access_token that will be used at Authorization
     *
     * @param object $user the User Object (User::findOne($id))
     * @throws yii\base\Exception
     */
    public static function generateAuthKey($user): void
    {
        $token = Yii::$app->security->generateRandomString();
        $accessToken = new UserToken();
        $accessToken->user_id = $user->id;
        $accessToken->type = $user->type ?? $accessToken->defaultConsumer;
        $accessToken->token = $token;
        $accessToken->expire_at = $accessToken->tokenExpiration + time();
        $accessToken->save();
        $user->token = $token;
    }

    /**
     * Make all user token based on any user_id expired
     *
     * @param int @userId
     * @return nothing
     */
    public static function makeAllUserTokenExpiredByUserId($userId)
    {
        UserToken::updateAll(['expire_at' => strtotime("now")], ['user_id' => $userId]);
    }

    /**
     * Expire any access_token
     *
     * @return bool
     */
    public function expireThisToken()
    {
        $this->expire_at = strtotime("now");
        return $this->save();
    }
}
