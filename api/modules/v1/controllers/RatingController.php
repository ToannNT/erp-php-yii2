<?php

namespace api\modules\v1\controllers;

use api\helper\response\ApiConstant;
use api\helper\response\ResponseHelper;
use common\models\App;
use common\models\AssetSessionRating;
use common\models\behaviors\SendPos;
use common\models\behaviors\SendSmsTask;
use common\models\SessionRating;
use common\models\SubApp;
use Yii;

/**
 * Class RatingController
 *
 * @author Tan Le <tannht@dtsmart.vn>
 * @package api\modules\v1\controllers
 */
class RatingController extends Controller
{
    public function actionPush()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $app = App::find()->where(['id'=>Yii::$app->appDataToken->app_id])->one();
            if ($app->status == App::STATUS_INACTIVE) {
                return ResponseHelper::build(ApiConstant::STATUS_FAIL, null, null, 'Please active app_id');
            }
            $data = $request->post();
            if (isset($data['external_id']) && isset($data['sub_app_id']) && isset($data['staff_list'])) {
                $subApp = SubApp::find()->where(['app_id' => Yii::$app->appDataToken->app_id, 'name' => $data['sub_app_id']])->one();
                if ($subApp) {
                    $sessionRatingOld = SessionRating::find()->where(['sub_app_id' => $subApp->id, 'external_id' => $data['external_id']])->one();
                    if (!$sessionRatingOld) {
                        $sessionRating = $this->createSessionRating($data, $subApp);
                        if ($sessionRating != false) {
                            $checkCreateStaffSessionRating = $this->createStaffAssetSessionRating($data, $sessionRating);
                            if ($checkCreateStaffSessionRating != false) {
                                $this->sendWebHookPos($data, $sessionRating);
                                return ResponseHelper::build(ApiConstant::STATUS_OK, $checkCreateStaffSessionRating, null, null, ApiConstant::STATUS_OK);
                            } else {
                                return ResponseHelper::build(ApiConstant::STATUS_FAIL, null, null, 'Can not save asset session rating');
                            }
                        } else {
                            return ResponseHelper::build(ApiConstant::STATUS_FAIL, null, null, 'Can not save session rating');
                        }
                    }  else {
                        return ResponseHelper::build(ApiConstant::STATUS_FAIL, null, null, 'External Id already');
                    }
                } else {
                    //create new sup app
                    $subAppNew = $this->createSubApp($data);
                    if ($subAppNew != false) {
                        $sessionRatingNew = $this->createSessionRating($data,$subAppNew);
                        if ($sessionRatingNew != false) {
                            $checkCreateStaffSessionRating = $this->createStaffAssetSessionRating($data, $sessionRatingNew);
                            if ($checkCreateStaffSessionRating != false) {
                                $this->sendWebHookPos($data, $sessionRatingNew);
                                return ResponseHelper::build(ApiConstant::STATUS_OK, $checkCreateStaffSessionRating, null, null, ApiConstant::STATUS_OK);
                            } else {
                                return ResponseHelper::build(ApiConstant::STATUS_FAIL, null, null, 'Can not save asset session rating');
                            }
                        }else {
                            return ResponseHelper::build(ApiConstant::STATUS_FAIL, null, null, 'Can not save session rating');
                        }
                    } else {
                        return ResponseHelper::build(ApiConstant::STATUS_FAIL, null, null, 'Can not save sub app');
                    }
                }
            } else {
                return ResponseHelper::build(ApiConstant::STATUS_FAIL, null, null, 'Missing params external_id or sub_app_id or staff_list');
            }
        } else {
            return ResponseHelper::build(ApiConstant::STATUS_FAIL, null, null, 'Only POST allowed!');
        }
    }

    /**
     * @param $data
     * @param $app
     * @return bool|SubApp
     */
    protected function createSubApp($data)
    {
        $subApp = new SubApp([
            'app_id' => Yii::$app->appDataToken->app_id,
            'name' => $data['sub_app_id'],
            'status' => SubApp::STATUS_ACTIVE,
        ]);
        if ($subApp->validate() && $subApp->save()) {
            return $subApp;
        } else {
            return false;
        }
    }

    /**
     * @param $data
     * @param $subApp
     * @return bool|SessionRating
     * @throws \yii\base\Exception
     */
    protected function createSessionRating($data, $subApp)
    {
        $sessionRating = new SessionRating([
            'sub_app_id' => $subApp->id,
            'external_id' => (string)$data['external_id'],
            'customer_data' => json_encode($data['customer']),
            'staffs' => json_encode($data['staff_list']),
            'key' => "S" . $data['external_id'] . Yii::$app->security->generateRandomString(16),
        ]);
        if ($sessionRating->validate() && $sessionRating->save()) {
            return $sessionRating;
        } else {
            return false;
        }
    }

    /**
     * @param $data
     * @param $sessionRating
     * @return bool|AssetSessionRating
     */
    public function createAssetSessionRating($data, $sessionRating)
    {
        $assetSessionRating = new AssetSessionRating([
            'session_rating_id' => $sessionRating->id,
            'staff_id' => $data['user_id'],
            'staff_name' => $data['name'],
            'rating' => $data['rating'],
            'list_service' => json_encode($data['services']),
            'staff_description' => $data['description'],
            'staff_image' => $data['staff_image'],
        ]);
        if ($assetSessionRating->validate() && $assetSessionRating->save()) {
            return $assetSessionRating;
        } else {
            return false;
        }
    }

    /**
     * @param $data
     * @param $sessionRating
     * @return array|bool
     * @throws \yii\base\Exception
     */
    public function createStaffAssetSessionRating($data, $sessionRating)
    {

        $staffList = $data['staff_list'];
        $listAsset = [];
        foreach ($staffList as $item) {
            $assetSessionRating = $this->createAssetSessionRating($item, $sessionRating);
            if ($assetSessionRating != false) {
                $listAsset[] = $assetSessionRating;
            } else {
                return false;
//                return ResponseHelper::build(ApiConstant::STATUS_FAIL, null, null, 'Can not save asset session rating');
            }
        }
        //send sms survey
        $customer = $data['customer'];
        if (isset($customer['phone'])) {
            $key = $sessionRating->key;
            $url = env('FRONTEND_HOST_INFO') . "rating/view?key=$key";
            $shortedUrl = Yii::$app->gourl->shortUrl($url, $sessionRating->external_id);
            /** @var SessionRating $sessionRating */
            $job = new SendSmsTask([
                'type' => SendSmsTask::TYPE_SURVEY,
                'short_link' => $shortedUrl,
                'to_phone' => $customer['phone'],
                'customer_id' => $sessionRating->subApp->name,
                'sessionRating' => $sessionRating,
                'external_id' => $data['external_id'],
            ]);
            //$job->execute([]);
            Yii::$app->smsQueue->push($job);
        }
        return ['session_rating' => $sessionRating, 'asset_session_rating' => $listAsset];
//            return ResponseHelper::build(ApiConstant::STATUS_OK, ['session_rating' => $sessionRating, 'asset_session_rating' => $listAsset], null, null, ApiConstant::STATUS_OK);
    }

    public function actionGet()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $data = $request->post();
            if (isset($data['external_id']) && isset($data['sub_app_id'])) {
                $subApp = SubApp::find()->where(['app_id'=>Yii::$app->appDataToken->app_id, 'name'=>$data['sub_app_id']])->one();
                if ($subApp) {
                    $sessionRating = SessionRating::find()->where(['sub_app_id' => $subApp->id, 'external_id' => $data['external_id']])->one();
                    if ($sessionRating) {
                        return ResponseHelper::build(ApiConstant::STATUS_OK, ['session_rating' => $sessionRating, 'list_staff_rating' => $sessionRating->assetSessionRating], null, null, ApiConstant::STATUS_OK);
                    } else {
                        return ResponseHelper::build(ApiConstant::STATUS_OK, [], null, "Not Found", ApiConstant::STATUS_OK);
                    }
                } else {
                    return ResponseHelper::build(ApiConstant::STATUS_FAIL, null, null, 'Sub app id does not exist');
                }
            } else {
                return ResponseHelper::build(ApiConstant::STATUS_FAIL, null, null, 'Missing params external_id or sub_app_id');
            }
        } else {
            return ResponseHelper::build(ApiConstant::STATUS_FAIL, null, null, 'Only POST allowed!');
        }
    }

    protected function sendWebHookPos($data, $sessionRating = null)
    {
        /** @var SessionRating $sessionRating */
        $job = new SendPos([
            'data' => $data,
            'sessionRating' => $sessionRating,
        ]);
        Yii::$app->posQueue->push($job);
    }
}