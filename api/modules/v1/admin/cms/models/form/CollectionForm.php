<?php

namespace api\modules\v1\admin\cms\models\form;

use api\modules\v1\admin\cms\models\Collection;
use yii\helpers\Inflector;

class CollectionForm extends Collection
{
    public function rules(): array
    {
        return $this->rules;
    }
}