<?php

namespace console\controllers;

use yii\console\Controller;

class SmartDigiteachController extends Controller
{
    public function actionInit()
    {
        echo "Running Migration...";
        $this->run("migrate/up", ["interactive" => false]);
        echo "Running Rbac...";
        $this->run("rbac-migrate/up", ["interactive" => false]);
        echo "Running Synchronize location shipper...";
        $this->run("synchronize-location-shipper/run");
        echo "Done! Begin Hacking";
    }
}