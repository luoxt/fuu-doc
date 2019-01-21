<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Support\Facades\Redis;
use App\Models\UserToken;

class ShopAuthenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;
    protected $usertoken_model = null;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth, UserToken $userToken)
    {
        $this->auth = $auth;
        $this->usertoken_model = $userToken;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        //获取参数
        $token = $request->input('token');
        $url = $request->input('_url');

        if(!$token){
            return reJson(false, '400', '请求参数[token]错误！');
        }

        $login_user = $this->usertoken_model->getToken($token);
        if(!isset($login_user["uid"]) || !$login_user["uid"]){
            return reJson(false, '400', '登录信息已失效！');
        }

        //有效时间
        if($login_user['token_expire'] < time()){
            return reJson(false, '400', '登录信息已过期！');
        } elseif ($login_user['token_expire'] < (time()+24*3600)) {
            //延迟两天
            $this->usertoken_model->setLastTime($token);
        }

        if(!isset($login_user["data_info"]) || empty($login_user["data_info"])){
            return reJson(false, '400', '数据结构出错！');
        }
        $data_info = json_decode($login_user["data_info"], true);

        //用户数据：app('data_info')
        $app = app();
        $app['data_info'] = $data_info;

        return $next($request);
    }
}
