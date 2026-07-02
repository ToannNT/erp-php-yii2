<?php

namespace common\components\helperDb;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\Exception;

class BaseDatabase extends Component
{
    const PREFIX = "ecommerce_";
    public $hostname;
    public $port;
    public $tenantId;
    public $username;
    public $password;

    /**
     * @throws InvalidConfigException
     */
    public function initDatabase($prefix = BaseDatabase::PREFIX): BaseDatabase
    {
        $hostname = $this->hostname;
        $port = $this->port;
        Yii::$app->set("db", [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=' . $hostname . ';port=' . $port . ';dbname=' . $prefix . $this->tenantId,
            'username' => $this->username,
            'password' => $this->password,
            'charset' => 'utf8'
        ]);
        return $this;
    }

    /**
     * @throws Exception
     */
    public function createDatabase($prefix = BaseDatabase::PREFIX): BaseDatabase
    {
        Yii::$app->db->createCommand("CREATE DATABASE `" . $prefix . "{$this->tenantId}` CHARACTER SET utf8mb4 -- UTF-8 Unicode COLLATE utf8mb4_general_ci;")->execute();
        return $this;
    }

    public function checkTenant($tenantId, $prefix = BaseDatabase::PREFIX)
    {
        return Yii::$app->db->createCommand("SHOW DATABASES WHERE `Database` LIKE '%{$prefix}{$tenantId}%' AND `Database` NOT LIKE '%queue%'")->queryColumn();
    }

    /**
     * @throws Exception
     */
    public function getDatabases($prefix = BaseDatabase::PREFIX)
    {
        return Yii::$app->db->createCommand("SHOW DATABASES WHERE `Database` LIKE '%{$prefix}%' AND `Database` NOT LIKE '%queue%'")->queryColumn();
    }

    public function setTenant($tenantId): BaseDatabase
    {
        $this->tenantId = $tenantId;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function open()
    {
        Yii::$app->db->close();
        Yii::$app->db->open();
    }
}
