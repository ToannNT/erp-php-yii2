<?php

namespace api\modules\v1\admin\setting\models;

use common\validators\EmailValidator;
use Yii;
use common\models\User as BaseUser;
use common\validators\IsArrayValidator;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

class User extends BaseUser
{
    public $role;
    public $suppliers;
    public $offices;
    public $password;

    public function fields()
    {
        return [
            "id",
            "username",
            "email",
            "status",
            "offices" => function () {
                return $this->getOffices()->addSelect(["id", "name"])->all();
            },
            "suppliers" => function () {
                return $this->getSuppliers()->addSelect(["id", "name"])->all();
            },
            "role" => function () {
                return empty($this->roleFirst) ? null : $this->roleFirst->item_name;
            },
            "logged_at",
            "created_at"
        ];
    }

    public function extraFields()
    {
        return [
            "role" => function ($model) {
                foreach (Yii::$app->authManager->getRolesByUser($model->id) as $role) {
                    return $role->name;
                }
            }
        ];
    }

    public function formName()
    {
        return "";
    }

    public function afterSave($insert, $changedAttributes)
    {
        $auth = Yii::$app->authManager;
        $auth->revokeAll($this->getId());
        $auth->assign($auth->getRole($this->role), $this->getId());
    }

    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        if ($this->password) {
            $this->setPassword($this->password);
        }
        return true;
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'status' => User::STATUS_DELETE,
                    'deleted_at' => date("Y-m-d H:i:s")
                ],
            ],
        ]);
    }

    public function rules()
    {
        return [
            ["password", "required", "on" => self::SCENARIO_CREATE],
            [["username", "email"], "required"],
            [["email"], EmailValidator::class],
            [["email"], "string", "min" => 5],
            [["username"], "string", "min" => 5],
            [["password"], "string", "min" => 5],
            [["username", "email"], "unique", "filter" => [
                "!=", "status", self::STATUS_DELETE
            ]],
            [["status"], "integer"],
            [["status"], "default", "value" => BaseUser::STATUS_INACTIVE],
            ["status", 'in', 'range' => [BaseUser::STATUS_ACTIVE, BaseUser::STATUS_INACTIVE]],
            [["suppliers"], IsArrayValidator::class],
            [["offices"], IsArrayValidator::class],
            [["offices"], "inOfficesValidator"],
            [["suppliers"], "inSupplierValidator"],
            [["role"], "IsRoleValidator"],
            [["suppliers", "offices"], "default", "value" => []]
        ];
    }

    public function inOfficesValidator($attribute)
    {
        $offices = Office::find()->where(["id" => $this->offices])->active()->count();
        if (!$offices) {
            $this->addError($attribute, "Invalid");
        }
        if ($this->role == User::ROLE_SELLER && $offices > 1) {
            $this->addError($attribute, "Seller only 1 office");
        }
    }

    public function inSupplierValidator($attribute)
    {
        $suppliers = Supplier::find()->where(["id" => $this->suppliers])->active()->count();
        if (!$suppliers) {
            $this->addError($attribute, "Invalid");
        }
    }

    public function IsRoleValidator($attribute)
    {
        if (!array_key_exists($this->$attribute, Yii::$app->authManager->getRoles())) {
            return $this->addError($attribute, "{$this->$attribute} Invaild");
        }
    }
}
