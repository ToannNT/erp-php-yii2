<?php

namespace common\traits;

trait SoftDeleteTrait
{
    public function softDelete()
    {
        $this->status = -99;
        $this->deleted_at = date("Y-m-d H:i:s");
        return $this->save(false);
    }
}
