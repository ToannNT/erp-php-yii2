<?php

namespace common\components\sharedpref\storage;

use common\components\sharedpref\SharedPreferences;

/**
 * Interface StorageInterface
 *
 * @package yii2mod\cart\storage
 */
interface StorageInterface
{

    public function setName(string $storageName);

    public function load(SharedPreferences $preferences);


    public function save(SharedPreferences $preferences);
}