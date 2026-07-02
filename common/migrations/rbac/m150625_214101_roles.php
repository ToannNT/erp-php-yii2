<?php

use common\models\User;
use common\rbac\Migration;

class m150625_214101_roles extends Migration
{
    const USER_ID_WEBMASTER = 1;

    private $roles = [
        User::ROLE_ADMINISTRATOR,
        User::ROLE_MANAGER,
        User::ROLE_SUPPLIER,
        User::ROLE_SELLER,
        User::ROLE_USER
    ];

    /**
     * @return bool|void
     * @throws \yii\base\Exception
     * @throws Exception
     */
    public function up()
    {
        $this->auth->removeAll();
        foreach ($this->roles as $role) {
            $roleCreated = $this->auth->createRole($role);
            $this->auth->add($roleCreated);
            if ($role === USER::ROLE_ADMINISTRATOR) {
                $this->auth->assign($roleCreated, self::USER_ID_WEBMASTER);
            }
        }
    }

    /**
     * @return bool|void
     */
    public function down()
    {
        foreach ($this->roles as $role){
            $this->auth->remove($this->auth->getRole($role));
        }
    }
}
