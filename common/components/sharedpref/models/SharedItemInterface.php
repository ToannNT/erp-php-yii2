<?php
namespace common\components\sharedpref\models;

interface SharedItemInterface
{
    public function getUniqueID(): string;
}