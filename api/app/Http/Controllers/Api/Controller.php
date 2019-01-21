<?php
/**
 *  @brief API 基类
 *
 */
namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use App\Base\Plugins\Validators\Validate;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use Validate;

    protected $log = null;
    protected $redis = null;
    protected $request = null;

    public function __construct()
    {
        $this->log = app('log');
        $this->redis = app('redis');
        $this->request = app('request');
    }

    /**
     * @brief 获取某个INPUT请求参数
     * @param $params
     * @return mixed
     */
    public function input($params=null)
    {
        return $this->request->input($params);
    }

    /**
     * @brief 过滤有效的POST请求
     * @param $params post数据
     * @param $rule post规则
     * @param int $trim 是否过滤空值字段 0不过滤，1过滤
     * @return array
     */
    public function postData($params, $rule, $trim=1)
    {
        $body_data = [];
        $post_params = array_intersect_key($params, $rule);
        foreach ($post_params as $pkey => $pval) {
            if($trim){
                if(trim($pval)){
                    $body_data[$pkey] = trim($pval);
                }
            } else {
                $body_data[$pkey] = is_string($pval) ? trim($pval) : $pval;
            }

        }
        return $body_data;
    }

    /**
     * @brief 平台接口
     * @param $ext_api
     * @param $body_data
     * @return \Illuminate\Http\JsonResponse
     */
    public function pfPost($ext_api, $body_data)
    {
        try{
            $api_data = pfPost($ext_api, $body_data);

            //数据判断
            if(!isset($api_data['ret'])){
                return reJson('false', 404, '平台接口数据错误', $api_data);
            }

            //请求失败
            if ($api_data['ret'] != '200' || !isset($api_data['data']['code'])){
                return reJson('false', $api_data['ret'], $api_data['msg'], $api_data);
            }

            //操作失败
            if($api_data['data']['code']!='0'){
                return reJson('false', $api_data['data']['code'], $api_data['data']['msg'],$api_data);
            }

            //成功
            return reJson('true', '200', $api_data['data']['msg'], $api_data['data']['info']);

        } catch (\Exception $exception) {

            $code = $exception->getCode();
            $msg = $exception->getMessage();
            return reJson(false, $code, $msg);
        }
        $this->log->info('调用平台接口');

        return reJson('false', '4000', '平台接口返回出错');
    }

    /**
     * @brief 权限服务接口
     * @param $ext_api
     * @param $body_data
     * @return \Illuminate\Http\JsonResponse
     */
    public function authPost($ext_api, $body_data)
    {
        try{
            $api_data = ssoPost($ext_api, $body_data);
            $resut_data = ['host'=>$ext_api, 'param'=>$body_data, 'response'=>$api_data];

            if(!isset($api_data['code'])){
                return reJson(false, '4000', '服务接口数据错误', $resut_data);
            }

            if(!isset($api_data['result'])){
                return reJson(false, '4000', '服务接口返回格式出错！', $resut_data);
            }

            if($api_data['code']!=200){
                return reJson(false, $api_data['code'], $api_data['msg'], $resut_data);
            }

            //成功
            return reJson('true', '200', '操作成功', $api_data['result']);

        } catch (\Exception $exception) {

            $code = $exception->getCode();
            $msg = $exception->getMessage();
            return reJson(false, $code, $msg);
        }
        $this->log->info('调用SSO接口');

        return reJson('false', '4000', '服务接口返回出错');
    }

    /**
     * @brief 登录服务接口
     * @param $ext_api
     * @param $body_data
     * @return \Illuminate\Http\JsonResponse
     */
    public function ssoPost($ext_api, $body_data)
    {
        try{
            $api_data = ssoPost($ext_api, $body_data);
            $resut_data = ['host'=>$ext_api, 'param'=>$body_data, 'response'=>$api_data];

            if(!isset($api_data['code'])){
                return reJson(false, '4000', '服务接口数据错误', $resut_data);
            }

            if(!isset($api_data['data'])){
                return reJson(false, '4000', '服务接口返回格式出错！', $resut_data);
            }

            if($api_data['code']!=200){
                return reJson(false, $api_data['code'], $api_data['msg'], $resut_data);
            }

            //成功
            return reJson('true', '200', '操作成功', $api_data['data']);

        } catch (\Exception $exception) {

            $code = $exception->getCode();
            $msg = $exception->getMessage();
            return reJson(false, $code, $msg);
        }
        $this->log->info('调用SSO接口');

        return reJson('false', '4000', '服务接口返回出错');
    }
}
