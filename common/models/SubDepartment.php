<?php

namespace common\models;

use Yii;
use \common\models\base\SubDepartment as BaseSubDepartment;
use yii\helpers\ArrayHelper;

/**
 * Class SubDepartment
 * @property Department $department
 * @property User $subDepartmentHead
 * @author Tan Le <tannht@dtsmart.vn>
 * @package common\models
 */
class SubDepartment extends BaseSubDepartment
{
    const STATUS_DELETE = -99;

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
    public function getDepartment()
    {
        return $this->hasOne(Department::class, ['id' => 'department_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubDepartmentHead()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function softDelete()
    {
        $this->status = self::STATUS_DELETE;
        return $this->save(false);
    }
}
