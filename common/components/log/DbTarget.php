<?php

namespace common\components\log;

use yii\helpers\VarDumper;
use yii\log\DbTarget as BaseDbTarget;

class DbTarget extends BaseDbTarget
{
    const TAG_CREATED = "created";
    const TAG_UPDATED = "updated";
    const TAG_DELETED = "deleted";

    public function export()
    {
        if ($this->db->getTransaction()) {
            $this->db = clone $this->db;
        }
        $tableName = $this->db->quoteTableName($this->logTable);
        $sql = "INSERT INTO $tableName ([[level]], [[category]], [[log_time]], [[prefix]], [[message]], [[old_data]],[[new_data]], [[tag]])
VALUES (:level, :category, :log_time, :prefix, :message, :old_data,:new_data, :tag)";
        $command = $this->db->createCommand($sql);
        foreach ($this->messages as $message) {
            list($text, $level, $category, $timestamp, $tag) = $message;
            $bindValues = [
                ':level' => $level,
                ':category' => $category,
                ':log_time' => $timestamp,
                ':prefix' => $this->getMessagePrefix($message),
                ':message' => $text["message"],
                ':old_data' => is_array($text["old_data"]) ? json_encode($text["old_data"]) : $text["old_data"],
                ':new_data' => is_array($text["new_data"]) ? json_encode($text["new_data"]) : $text["new_data"],
                ':tag' => $text["tag"]
            ];
            if ($command->bindValues($bindValues)->execute() > 0) {
                continue;
            }
        }
    }

}