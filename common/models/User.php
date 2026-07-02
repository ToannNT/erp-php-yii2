<?php

namespace common\models;

use common\commands\AddToTimelineCommand;
use common\models\query\UserQuery;
use Yii;
use common\models\base\User as BaseUser;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 */
class User extends BaseUser implements IdentityInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DELETE = -99;
    const USER_ID_ADMIN = 1;

    const ROLE_USER = 'user';
    const ROLE_MANAGER = 'manager';
    const ROLE_ADMINISTRATOR = 'administrator';
    const ROLE_SUPPLIER = "supplier";
    const ROLE_SELLER = "seller";
    const ROLE_STAFF = "staff";

    const EVENT_AFTER_SIGNUP = 'afterSignup';
    const EVENT_AFTER_LOGIN = 'afterLogin';
    /**
     * @var mixed|null
     */

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::find()
            ->active()
            ->andWhere(['id' => $id])
            ->one();
    }

    public function getRole()
    {
        return Yii::$app->authManager->getRolesByUser($this->id);
    }

    /**
     * @return UserQuery
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $accessToken = UserToken::find()->where(['token' => $token])->andWhere(['>', 'expire_at', strtotime('now')])->one();
        if (!$accessToken) return $accessToken;
        return User::findOne(['id' => $accessToken->user_id, 'status' => User::STATUS_ACTIVE]);
    }

    /**
     * Finds user by email
     *
     * @param string $info
     * @return User|array|null
     */
    public static function findByEmailOrUserName(string $info)
    {
        return static::find()
            ->active()
            ->andWhere(['or', ['username' => $info], ['email' => $info]])
            ->one();
    }

    /**
     * Finds user by username or email
     *
     * @param string $login
     * @return User|array|null
     */
    public static function findByLogin($login)
    {
        return static::find()
            ->active()
            ->andWhere(['or', ['username' => $login], ['email' => $login]])
            ->one();
    }

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                'oauth_create' => [
                    'oauth_client', 'oauth_client_user_id', 'email', 'username', '!status'
                ]
            ]
        );
    }

    /**
     * @throws yii\base\Exception
     */
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }


    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE => Yii::t('common', 'Not Active'),
            self::STATUS_ACTIVE => Yii::t('common', 'Active'),
            self::STATUS_DELETE => Yii::t('common', 'Deleted')
        ];
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
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::class, ['user_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    /**
     * Creates user profile and application event
     * @param array $profileData
     */
    public function afterSignup(array $profileData = [])
    {
        $this->refresh();
        Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'category' => 'user',
            'event' => 'signup',
            'data' => [
                'public_identity' => $this->getPublicIdentity(),
                'user_id' => $this->getId(),
                'created_at' => $this->created_at
            ]
        ]));
        $profile = new UserProfile();
        $profile->locale = Yii::$app->language;
        $profile->load($profileData, '');
        $this->link('userProfile', $profile);
        $this->trigger(self::EVENT_AFTER_SIGNUP);
        // Default role
        $auth = Yii::$app->authManager;
        $auth->assign($auth->getRole(User::ROLE_USER), $this->getId());
    }

    public function getRoleFirst()
    {
        return $this->hasOne(RbacAuthAssignment::className(), ["user_id" => "id"]);
    }

    /**
     * @return string
     */
    public function getPublicIdentity()
    {
        if ($this->userProfile && $this->userProfile->getFullname()) {
            return $this->userProfile->getFullname();
        }
        if ($this->username) {
            return $this->username;
        }
        return $this->email;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getOffices(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Office::class, ["id" => "office_id"])
            ->viaTable("user_office", ["user_id" => "id"]);
    }

    public function getOffice()
    {
        return $this->hasOne(Office::class, ["id" => "office_id"])
            ->viaTable("user_office", ["user_id" => "id"]);
    }

    public function getOfficeByRoles()
    {
        if (Yii::$app->user->canIn([User::ROLE_SUPPLIER, User::ROLE_MANAGER])) {
            return $this->offices;
        }
        return Office::find()->all();
    }

    public function getSuppliers()
    {
        return $this->hasMany(Supplier::class, ["id" => "supplier_id"])
            ->viaTable("user_supplier", ["user_id" => "id"]);
    }

    public function getProductAssignSupplier()
    {
        return $this->hasMany(Product::class, ["id" => "product_id"])
            ->viaTable("user_supplier", ["user_id" => "id"]);
    }

    public function getGroupOfficeHtml(): string
    {
        if (array_key_exists("administrator", \Yii::$app->authManager->getRolesByUser($this->id))) {
            return '<span class="btn btn-sm btn-success mr-1 my-1 d-block">Quản Trị Tất Cả</span>';
        }
        $offices = $this->offices;
        $str = '';
        foreach ($offices as $office) {
            $str .= '<span class="btn btn-sm btn-outline-light mr-1 my-1 d-block">' . $office->name . '</span>';
        }
        return $str;
    }

    public function getInventorys()
    {
        return $this->hasMany(Inventory::class, ["office_id" => "id"])
            ->where(["status" => Office::STATUS_ACTIVE])
            ->via("offices");
    }

    public function getInventoryFirst()
    {
        return $this->hasOne(Inventory::class, ["office_id" => "id"])
            ->where(["status" => Office::STATUS_ACTIVE])
            ->via("offices");
    }

    public function getInventoryByRoles()
    {
        if (Yii::$app->user->canIn([self::ROLE_ADMINISTRATOR, self::ROLE_SUPPLIER])) {
            return Inventory::find()->active()->all();
        }
        return $this->inventorys;
    }

    public function getSupplierByRoles()
    {
        if (Yii::$app->user->canIn([self::ROLE_ADMINISTRATOR, self::ROLE_MANAGER])) {
            return Supplier::find()->active()->all();
        }
        return $this->suppliers;
    }

    public static function findByAccessToken($token)
    {
        return self::findOne(["access_token" => $token]);
    }
}
