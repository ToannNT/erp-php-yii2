<?php
namespace common\components\sharedpref\storage;

use common\components\sharedpref\SharedPreferences;
use Yii;
use yii\base\BaseObject;

class SessionStorage extends BaseObject implements StorageInterface
{
    /**
     * @var string
     */
    public $key = 'sharedPref';
    /**
     * @inheritdoc
     */
    public function load(SharedPreferences $preferences)
    {
        $sharedPrefData = [];
        if (false !== ($session = ($this->session->get($this->key, false)))) {
            $sharedPrefData = unserialize($session);
        }
        return $sharedPrefData;
    }
    /**
     * @inheritdoc
     */
    public function save(SharedPreferences $preferences)
    {
        $sessionData = serialize($preferences->getItems());
        $this->session->set($this->key, $sessionData);
    }
    /**
     * @return object
     */
    public function getSession()
    {
        return Yii::$app->get('session');
    }

    public function setName(string $storageName)
    {
        $this->key = $storageName;
    }
}