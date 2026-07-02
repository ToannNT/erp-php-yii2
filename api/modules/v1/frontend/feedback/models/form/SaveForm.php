<?php

namespace api\modules\v1\frontend\feedback\models\form;

use common\validators\EmailValidator;

class SaveForm extends \common\models\Feedback
{
    public function rules(): array
    {
        return [
            [["title", "email", "phone", "fullname", "content"], "required"],
            [["email"], "email"],
            [["subject", "content"], "string"],
            [['title', 'fullname', 'email'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 50],
            [['status'], "default", "value" => self::STATUS_INACTIVE]
        ];
    }
}