<?php
namespace App\Http\Controllers\Api\Base;

use App\Http\Controllers\Api\Controller;

/**
 * 地区管理
 * @package App\Http\Controllers\Api\
 */
class MessageController extends Controller
{
    /**
     * Create a new controller instance.
     * IndexController constructor.
     * @param
     */
    public function __construct( )
    {
        parent::__construct();
    }


    /**
     * @brief 发送短息验证码
     * @return \Illuminate\Http\JsonResponse
     */
    public function send_code()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'mobile' => 'required|regex:/^1[34578][0-9]{9}$/',  //手机号
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);
        $code = getCode();
        $rt = send_code(def($post_data, 'mobile'), $code);
        if($rt){
            return reJson(true, '200', '请求成功');
        }else{
            return reJson(true, '300', '短息验证码发送失败');
        }

    }

}
