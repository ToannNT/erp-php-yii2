<?php
/**
 * Created by PhpStorm.
 * User: xfire
 * Date: 21/09/2018
 * Time: 4:33 PM
 */

namespace common\components\gourl;

use yii\base\Component;
use yii\base\Exception;

class Client extends Component
{
    public $accessKey;
    public $link_api = "https://gocheckin.link/url/make";
    public $url_shortlink = "gocheckin.link/app@";
    const LINK_API = "https://gourl.pro/url/make";
    const URI_SHORTLINK = "https://gourl.pro/";

    public function shortUrl($longUrl, $relation_id = "")
    {
        $apiParams = [
            'url' => $longUrl,
            'relation_id' => $relation_id,
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->link_api . '?key=' . $this->accessKey);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $apiParams);
        $curl_response = curl_exec($curl);
        $json_decode_response = json_decode($curl_response, TRUE);
        curl_close($curl);
        if ($json_decode_response['status'] == 'OK') {
            return $this->url_shortlink . $json_decode_response['result'];
        }
        throw new Exception();

    }
}