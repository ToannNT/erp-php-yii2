<?php

namespace common\components\log;

use Throwable;
use Yii;

class BuildLogDbTarget
{
    /**
     * @param string $task
     * @param string $category
     * @param string $tag
     * @param array|null $new_data
     * @param array|null $old_data
     * @return void
     * @throws Throwable
     */
    public function push(
        string $task,
        string $category,
        string $tag = "",
        array  $new_data = null,
        array  $old_data = null
    )
    {
        Yii::info([
            "message" => $this->setMessage($task),
            "new_data" => $new_data,
            "old_data" => $old_data,
            "tag" => $tag
        ], $category);
    }

    /**
     * @throws Throwable
     */
    protected function setMessage($message): string
    {
        $userLogged = Yii::$app->user->getIdentity();
        return "USER_ID:$userLogged->id Task:$message";
    }
}