<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;

/**
 * Class TruncateController
 *
 * @author Tan Le <tannht@dtsmart.vn>
 * @package console\controllers
 */
class TruncateController extends Controller
{
    public function actionTable()
    {
        Yii::$app->db->createCommand()->truncateTable('product')->execute();
        Yii::$app->db->createCommand()->truncateTable('product_inventory')->execute();
        Yii::$app->db->createCommand()->truncateTable('product_variant')->execute();
//        Yii::$app->db->createCommand()->truncateTable('inventory')->execute();
        Yii::$app->db->createCommand()->truncateTable('inventory_history')->execute();
        Yii::$app->db->createCommand()->truncateTable('inventory_issue')->execute();
        Yii::$app->db->createCommand()->truncateTable('inventory_issue_item')->execute();
        Yii::$app->db->createCommand()->truncateTable('inventory_receipt')->execute();
        Yii::$app->db->createCommand()->truncateTable('inventory_receipt_item')->execute();
        Yii::$app->db->createCommand()->truncateTable('product_inventory')->execute();
        Yii::$app->db->createCommand()->truncateTable('stocktaking')->execute();
        Yii::$app->db->createCommand()->truncateTable('stocktaking_item')->execute();
        Yii::$app->db->createCommand()->truncateTable('order')->execute();
        Yii::$app->db->createCommand()->truncateTable('order_item')->execute();
        echo "DONE";
    }
}