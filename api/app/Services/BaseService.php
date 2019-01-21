<?php

namespace App\Services;

use App\Base\Plugins\Validators\Validate;
use App\Exceptions\ApiException;

/**
 * 基础服务
 * @author zicai
 * @date 2017-7-20 14:26:06
 */
class BaseService
{
    use Validate;

    public $log = null;
    public $redis = null;
    public $request = null;

    function __construct()
    {
        $this->log = app('Log');
        $this->redis = app('redis');
        $this->request = app('request');
    }

    /**
     * @brief 获取某个INPUT请求参数
     * @param $params
     * @return mixed
     */
    public function input($params = null)
    {
        return $this->request->input($params);
    }

    /**
     * @brief：xml数据转array
     * @param $xml
     * @return：array
     */
//    static function xmlToArray($xml)
//    {
//        //禁止引用外部xml实体
//        libxml_disable_entity_loader(true);
//        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
//        $val = json_decode(json_encode($xmlstring), true);
//        return $val;
//    }

    /**
     * @param str $url post传递的url地址
     * @return string
     */
//
//    static public function get_url($url){
//        //2初始化
//        $ch = curl_init();
//        //3.设置参数
//        curl_setopt($ch , CURLOPT_URL, $url);
//        curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
//        //4.调用接口
//        $res = curl_exec($ch);
//        if( curl_errno($ch) ){
//            exit( curl_error() );
//            exit();
//        }else{
//            return $res;
//            //5.关闭curl
//            curl_close( $ch );
//        }
//    }

}
