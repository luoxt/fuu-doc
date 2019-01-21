<?php

    /**
     * @brief 调试方法
     * @author luoxt
     * @date 2017-9-13 15:56:17
     */
    function debug(...$args)
    {
        echo '<pre>';print_r($args);exit;
    }

    /**
     * @brief 获取ID
     * @author luoxt
     * @date 2018-10-31
     */
    function getid($args='')
    {
       $id_ojb = App\Base\Plugins\Guid\IdWork::getInstance();
       return $id_ojb->nextId();

    }

    /**
     * @brief   将二维数组按指定字段排序
     * @param  [type] $data   [数组]
     * @param  string $column [排序字段]
     * @param  string $sort   ['ASC' OR 'DESC']
     * @author zicai
     * @date 2017-8-15 11:22:35
     */
    function arrayOrderBy(array &$data = [], $column = '', $sort = 'DESC')
    {
        if (empty($column) && isset($sorts[$sort])) {
            return $data;
        }
        $sorts = [
            'DESC' => SORT_DESC,
            'ASC' => SORT_ASC,
        ];
        array_multisort(array_column($data, $column), $sorts[$sort], $data);
    }

    /**
     * 拆分area信息，只显示地区文字信息
     * @param  [string] $data [地区信息 云南省/红河哈尼族彝族自治州/泸西县:530000/532500/532527]
     * @return [string]       [重新组装地区信息 云南省红河哈尼族彝族自治州泸西县]
     * @author zicai
     * @date 2017-8-10 10:20:10
     */
    function getArea($data)
    {
        if (empty($data)) {
            return '';
        }

        $area_arr = explode(':',$data);
        $area = str_replace('/','',$area_arr[0]);
        return $area;
    }


    /**
     * @brief 将一个字符串拼接至一个不包含自身的字符串中
     * @author luoxt
     * @date 2017-08-04
     */
    function str_append($string, $appendStr)
    {
        if($string === '*'){
            return $string;
        }
        $str_arr = explode(',',$string);
        $append_arr = explode(',',$appendStr);

        //合并去重
        $arr = array_merge($str_arr, $append_arr);
        $arr = array_unique(array_filter($arr));

        return $arr;
    }

    /**
     * @brief 根据传入的数组和数组中值的键值，将对数组的键进行替换
     * @param array $array
     * @param string $key
     */
    function array_bind_key($array, $key )
    {
        foreach( (array)$array as $value )
        {
            if( !empty($value[$key]) )
            {
                $k = $value[$key];
                $result[$k] = $value;
            }
        }
        return $result;
    }


    /**
     * 简单对称加密算法之加密
     * @param String $string 需要加密的字串
     * @param String $skey 加密EKY
     * @date 2013-08-13 19:30
     * @update 2014-10-10 10:10
     * @return String
     */
    function encryption($string = '', $skey = 'wxtty')
    {
        $strArr = str_split(base64_encode($string));
        $strCount = count($strArr);
        foreach (str_split($skey) as $key => $value)
            $key < $strCount && $strArr[$key] .= $value;
        return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
    }


    /**
     * 简单对称加密算法之解密
     * @param String $string 需要解密的字串
     * @param String $skey 解密KEY
     * @date 2013-08-13 19:30
     * @update 2014-10-10 10:10
     * @return String
     */
    function decryption($string = '', $skey = 'wxtty')
    {
        $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
        $strCount = count($strArr);
        foreach (str_split($skey) as $key => $value)
            $key <= $strCount && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
        return base64_decode(join('', $strArr));
    }


    /**
     * @brief 多返回值
     */
    function remulti(...$param)
    {
        return $param;
    }


    /**
     * 返回Json格式数据方法
     * @param  boolean $status [成功/失败]
     * @param  string  $code   [code状态码]
     * @param  string  $msg    [消息]
     * @param  array   $data   [数据]
     * @return [json]          [description]
     */
    function reJson($status = true, $code = '', $msg = '', $data = [], $headerCode = 200)
    {
        $code_msg = '';
        if (!empty($code)) {
            $StatusCode = config('config.StatusCode');
            $code_msg = isset($StatusCode[$code]) ? $StatusCode[$code] : '';
        }
        if (is_array($msg) && !array_filter($msg)) {
            $msg = '';
        }else if (is_string($msg) && empty(trim($msg))) {
            $msg = $code_msg;
        }

        //整数型值超16位转字符串
        longintToStr($data);

        $res = [
            'status' => $status,
            'code'   => (string)$code,
            'msg'    => $msg,
            'data'    => $data,
        ];
        return response()->json($res, $headerCode);
    }


    /**
     * 数字超过16位长度转换成字符串
     * @author zoue
     * @date 2018-12-01
     */
    function longintToStr(&$data)
    {
        if(is_array($data)){
            foreach($data as &$row){
                if(is_array($row)){
                    longintToStr($row);
                    continue;
                }
                if(is_object($row)){
                    $row = (array)$row;
                    longintToStr($row);
                    continue;
                }
                if(strlen($row)>16){
                    $row = (string)$row;
                }
            }
            return true;
        }
        if(strlen($data)>16){
            $data = (string)$data;
        }
        return true;
    }

    /**
     * 判断是否json格式（此方法必须在PHP7以上版本使用）
     * @param  [string] $string []
     * @return [bool]       []
     * @author zicai
     * @date 2017-8-14 16:12:09
     */
    function isJson($string)
    {
        if(is_numeric($string)){
            return false;
        }
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }


    function requestGet($url = '', array $header = [], $timeout = 10)
    {
        $res = [];
        $curl = new \Curl\Curl();
        $curl->setHeaders($header);
        $curl->setConnectTimeout($timeout);
        $curl->get($url);

        $post_options['url'] = $url;
        $post_options['header'] = $header;

        if ($curl->error) {
            $msg = 'CURL Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            $msg .= json_encode($post_options) . "\n";
        } else {
            $msg = 'CURL Success: ' . "\n" . json_encode($post_options) . "\n";
        }

        //strClass to array, don`t del this code. --zicai
        $res = json_decode(json_encode($curl->response),true);

        $msg .= 'result:'.json_encode($res);
        app()['Log']::debug($msg);
        return $res;
    }

    /**
     * POST请求
     * 文档说明：https://github.com/php-curl-class/php-curl-class
     * @author zicai
     * @date 2017-7-20 14:29:20
     */
    function requestPOST($url = '', array $body = [], array $header = [], $timeout = 10)
    {
        $res = [];
        $curl = new Curl();
        $curl->setHeaders($header);
        $curl->setConnectTimeout($timeout);
        $curl->post($url,$body);

        $post_options['url'] = $url;
        $post_options['header'] = $header;
        $post_options['body'] = $body;

        if ($curl->error) {
            $msg = 'CURL Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            $msg .= json_encode($post_options) . "\n";
        } else {
            $msg = 'CURL Success: ' . "\n" . json_encode($post_options) . "\n";
        }

        //strClass to array, don`t del this code. --zicai
        $res = json_decode(json_encode($curl->response),true);

        $msg .= 'result:'.json_encode($res);
        app()['Log']::debug($msg);
        return $res;
    }

    /**
     * 生成签名 ( 生成平台中心或者Java服务签名 )
     * @var [type]
     */
    function get_signature($data)
    {
        if ( ! isset($data['apiKey']) || ! isset($data['timestamp']) ) return '';
        $key = $data['apiKey'];
        $time  = $data['timestamp'];

        $str = generateStr($data);

        $signature = "";
        if (function_exists('hash_hmac')) {
            $signature = base64_encode(hash_hmac("sha1", $str, $key, true));
        } else {
            $blocksize = 64;
            $hashfunc = 'sha1';
            if (strlen($key) > $blocksize) {
                $key = pack('H*', $hashfunc($key));
            }
            $key = str_pad($key, $blocksize, chr(0x00));
            $ipad = str_repeat(chr(0x36), $blocksize);
            $opad = str_repeat(chr(0x5c), $blocksize);
            $hmac = pack(
                'H*', $hashfunc(
                    ($key ^ $opad) . pack(
                        'H*', $hashfunc(
                            ($key ^ $ipad) . $str
                        )
                    )
                )
            );
            $signature = base64_encode($hmac);
        }
        $signature = str_replace('/', $time, $signature);
        $signature = str_replace('+', $time, $signature);
        $signature = str_replace('=', '', $signature);
        return $signature;
    }


    function generateStr($data)
    {
        if (!is_array($data)) return '';
        ksort($data);

        $str = '';
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                $str .= generateStr($val);
            } else {
                $str .= $val;
            }
        }
        return $str;
    }

    /**
     * @brief 生成密码
     */
    function mkpass($password)
    {
        return md5('TTYUN'.md5($password));
    }

    /**
     * @brief 检查密码
     */
    function chpass($password, $oldpassword)
    {
        $newpassword = md5('TTYUN'.md5($password));
        if($oldpassword === $newpassword) {
            return true;
        }
        return false;
    }


    /**
     * 字符判断
     *
     */
    function array_default($arr=array(), $fild='', $default=0)
    {
        if(!is_array($arr)){
            return $default;
        }
        if (isset($arr[$fild])) {
            if(!is_array($arr[$fild])){
                return trim($arr[$fild]);
            }
            return $arr[$fild];
        } else {
            return $default;
        }
    }

    /**
     * 数组判断
     */
    function def($arr=array(), $fild='', $default='')
    {
        if(!is_array($arr)){
            return $default;
        }
        if (isset($arr[$fild])) {
            if(!is_array($arr[$fild])){
                return trim($arr[$fild]);
            }
            return $arr[$fild];
        } else {
            return $default;
        }
    }

    /**
     * 数组 转 对象
     * @param array $arr 数组
     * @return object
     */
    function arr2obj($arr)
    {
        if (gettype($arr) != 'array') {
            return;
        }
        foreach ($arr as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object') {
                $arr[$k] = (object)array_to_object($v);
            }
        }

        return (object)$arr;
    }

    /**
     * 对象 转 数组
     * @param object $obj 对象
     * @return array
     */
    function obj2arr($obj)
    {
        $obj = (array)$obj;

        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)obj2arr($v);
            }
        }

        return $obj;
    }

    /**
     * 字符单个统计(中文也算一个字符)
     * @param string
     * @return int
     */
    function strlen_by_single_count($string = null)
    {
        if(is_null($string))
            return 0;
        preg_match_all("/./us", $string, $match); // 将字符串分解为单元
        return count($match[0]);
    }

    /**
     * 验证数字型字符串
     */
    function is_number_str($str)
    {
        if(is_null($str))
            return false;
        if(preg_match("/^[0-9]*$/", $str)){
            return true;
        }
        return false;
    }

    function erpPost($url = '', array $body = [], array $header = [], $timeout = 10)
    {
        $curl = new \Curl\Curl();

        //url
        $base_url = env('ERP_API_HOST');
        $url = $base_url.$url;

        //获取毫秒
        list($msec, $sec) = explode(' ', microtime());
        $msectime =  (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);

        $appkey = "89b8bf6b-c39f-4543-9975-3db9535b881f";
        $apiKey = env('ERP_API_KEY');

        $str_par = $appkey . $msectime . $apiKey;

        //升序转成二进制
        $str_arr = str_split($str_par);
        sort($str_arr);
        $str_sort = implode('', $str_arr);
        $signature = md5($str_sort);

        //body
        $base_body = [
            "user_id" => 0,
            "org_id" => 0,
            "appkey" =>$appkey,
            "timestamp" =>$msectime,
            "signature" =>$signature,
        ];
        $body = array_merge($base_body,$body);
        $body_json = json_encode($body);

        $header = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];
        $curl->setHeaders($header);
        $curl->setConnectTimeout($timeout);
        $curl->post($url, $body_json);

        $post_options['url'] = $url;
        $post_options['header'] = $header;
        $post_options['body'] = $body;
        app()['Log']::debug(json_encode($post_options));

        if ($curl->error) {
            $msg = 'CURL Platform Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            $msg .= json_encode($post_options) . "\n";
        } else {
            $msg = 'CURL Platform Success: ' . "\n" . json_encode($post_options) . "\n";
        }

        //strClass to array, don`t del this code. --zicai
        $res = json_decode(json_encode($curl->response), true);

        $msg .= 'result:'.json_encode($res);
        app()['Log']::debug($msg);

        return $res;
    }

    function authPost($url = '', array $body = [], array $header = [], $timeout = 10)
    {
        $curl = new \Curl\Curl();

        //url
        $base_url = env('AUTH_API_HOST');
        $url = $base_url.$url;

        //body
        $base_body = [];
        $base_body['apiKey'] = env('AUTH_API_KEY');
        $base_body['timestamp'] = time();
        $body = array_merge($base_body,$body);
        $body['sign'] = get_signature($body);

        $curl->setHeaders($header);
        $curl->setConnectTimeout($timeout);
        $curl->post($url, $body);

        $post_options['url'] = $url;
        $post_options['header'] = $header;
        $post_options['body'] = $body;
        app()['Log']::debug(json_encode($post_options));

        if ($curl->error) {
            $msg = 'CURL Platform Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            $msg .= json_encode($post_options) . "\n";
        } else {
            $msg = 'CURL Platform Success: ' . "\n" . json_encode($post_options) . "\n";
        }

        //strClass to array, don`t del this code. --zicai
        $res = json_decode(json_encode($curl->response), true);

        $msg .= 'result:'.json_encode($res);
        app()['Log']::debug($msg);

        return $res;
    }

    function ssoPost($url = '', array $body = [], array $header = [], $timeout = 10)
    {
        $curl = new \Curl\Curl();

        //url
        $base_url = env('SSO_API_HOST');
        $url = $base_url.$url;

        //body
        $base_body = [];
        $base_body['apiKey'] = env('SSO_API_KEY');
        $base_body['timestamp'] = time();
        $body = array_merge($base_body,$body);
        $body['sign'] = get_signature($body);

        $curl->setHeaders($header);
        $curl->setConnectTimeout($timeout);
        $curl->post($url, $body);

        $post_options['url'] = $url;
        $post_options['header'] = $header;
        $post_options['body'] = $body;
        app()['Log']::debug(json_encode($post_options));

        if ($curl->error) {
            $msg = 'CURL Platform Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            $msg .= json_encode($post_options) . "\n";
        } else {
            $msg = 'CURL Platform Success: ' . "\n" . json_encode($post_options) . "\n";
        }

        //strClass to array, don`t del this code. --zicai
        $res = json_decode(json_encode($curl->response), true);

        $msg .= 'result:'.json_encode($res);
        app()['Log']::debug($msg);

        return $res;
    }

    /**
     * POST请求
     * 文档说明：https://github.com/php-curl-class/php-curl-class
     * @author zicai
     * @date 2017-7-20 14:29:20
     */
    function pfPost($url = '', array $body = [], array $header = [], $timeout = 10)
    {
        $curl = new \Curl\Curl();

        //url
        $base_url = env('PLATFORM_API_HOST');
        $url = $base_url.$url;

        //body
        $base_body = [];
        $base_body['apiKey'] = env('PLATFORM_API_KEY');
        $base_body['timestamp'] = time();

        $body = array_merge($base_body, $body);
        $body['sign'] = get_signature($body);
        $curl->setHeaders($header);
        $curl->setConnectTimeout($timeout);
        $curl->post($url, $body);

        $post_options['url'] = $url;
        $post_options['header'] = $header;
        $post_options['body'] = $body;
        app()['Log']::debug(json_encode($post_options));

        if ($curl->error) {
            $msg = 'CURL Platform Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            $msg .= json_encode($post_options) . "\n";
        } else {
            $msg = 'CURL Platform Success: ' . "\n" . json_encode($post_options) . "\n";
        }
        //strClass to array
        $res = json_decode(json_encode($curl->response), true);

        $msg .= 'result:'.json_encode($res);
        app()['Log']::debug($msg);

        return $res;
    }

    /**
     * @brief 微信接口请求
     */
    function wxPost($url = '', array $body = [], array $header = [], $timeout = 10)
    {
        $curl = new \Curl\Curl();

        //url
        $base_url = env('WECHAT_API_HOST');
        $url = $base_url . $url;

        //body
        $base_body = [];
        $base_body['apiKey'] = env('WECH_API_KEY');
        $base_body['timestamp'] = time();
        $body = array_merge($base_body, $body);
        $body['sig'] = get_signature($body);

        $curl->setHeaders($header);
        $curl->setConnectTimeout($timeout);
        $curl->post($url, $body);

        $post_options['url'] = $url;
        $post_options['header'] = $header;
        $post_options['body'] = $body;

        if ($curl->error) {
            $msg = 'CURL Platform Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            $msg .= json_encode($post_options) . "\n";
        } else {
            $msg = 'CURL Platform Success: ' . "\n" . json_encode($post_options) . "\n";
        }

        //strClass to array
        $res = json_decode(json_encode($curl->response), true);

        $msg .= 'result:' . json_encode($res);
        app()['Log']::debug($msg);

        return $res;
    }

    /**
     * 转码utf8转gbk
     */
    function getSafeStr($str)
    {
        $s1 = iconv('utf-8', 'gbk//IGNORE', $str);
        $s0 = iconv('gbk', 'utf-8//IGNORE', $s1);
        if ($s0 == $str) {
            return $s1;
        } else {
            return mb_convert_encoding($str, 'GBK', 'utf-8');
        }
    }

    /**
     * 创建目录
     */
    function mkdirs(string $dir, string $mode = '0777')
    {
        if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
        if (!mkdirs(dirname($dir), $mode)) return FALSE;
        return @mkdir($dir, $mode);
    }


    /**
     * 获取时间戳到毫秒
     * @return bool|string
     */
    function getMillisecond()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectimes = substr($msectime, 0, 13);
    }

    /**
     * @brief 发送短信验证码
     */
    function send_code($mobile, $code)
    {
        //短信内容 手机号登录验证码：${code}
        $sendTo = $mobile;
        $tempId = 'SMS_79040011';
        //$code = rand(100000, 999999);
        $tempData = ['code' => "{$code}"];

        //将号码放入缓存
        $redis_key = 'send_code:'. $sendTo;

        Illuminate\Support\Facades\Redis::set($redis_key, $code);
        Illuminate\Support\Facades\Redis::expire($redis_key, 3000);

        //发送短信
        $aliyunSms = new App\Base\Plugins\Sms\Sms();
        try {
            $aliyunSms->sendSms($sendTo, $tempId, $tempData);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * 生成短息验证码
     */
    function getCode(){
        $code = rand(1000,9999);
        return $code;
    }

    /**
     * @brief：微信接口 抓获网页数据
     * @param $url,$type,$res,$arr
     * @return 抓获网页数据的函数
     */
    function https_request($url, $type, $res, $arr)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if($type == 'post'){    //type可以为“get”或“post”
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
        }

        $output = curl_exec($ch);
        curl_close($ch);

        if($res == 'json'){    //res可以是“json”或"xml"
            return json_decode($output,true);
        }
    }

    /**
     * @brief：微信接口 https GET请求
     * @param： $url
     * @return：$data
     */
    function https_get($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return $tmpInfo;
    }

    /***
     * @brief 微信接口 curl post请求
     * @param $url
     * @param $dataObj
     * @return mixed
     */
    function curl_post($url, $dataObj)
    {
        //初使化init方法
        $ch = curl_init();
        //指定URL
        curl_setopt($ch, CURLOPT_URL, $url);
        //设定请求后返回结果
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //声明使用POST方式来进行发送
        curl_setopt($ch, CURLOPT_POST, 1);
        //发送什么数据呢
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataObj, JSON_UNESCAPED_UNICODE));
        //忽略证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //忽略header头信息
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //设置超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        //发送请求
        $output = curl_exec($ch);
        //关闭curl
        curl_close($ch);
        //返回数据
        return $output;
    }
