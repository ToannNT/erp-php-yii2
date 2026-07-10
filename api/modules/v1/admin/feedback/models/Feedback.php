<?php

namespace api\modules\v1\admin\feedback\models;

use common\models\Feedback as BaseFeedback;

class Feedback extends BaseFeedback
{
    public function fields(): array
    {
        return [
            'id',
            'user_id',
            'subject',
            'title',
            'fullname',
            'phone',
            'email',
            'content',
            'is_confirm_term',
            'status',
            'created_at',
            'updated_at',
        ];
    }
}
