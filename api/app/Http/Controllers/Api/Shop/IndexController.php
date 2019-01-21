<?php
namespace App\Http\Controllers\Api\Shop;


/**
 *
 * @package App\Http\Controllers\Api\Organize
 */
class IndexController extends ShopController
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

    public function index(){

        $username = I("username");
        $password = I("password");
        $v_code = I("v_code");

        //检查用户输错密码的次数。如果超过一定次数，则需要验证 验证码
        $key= 'login_fail_times_'.$username;
        if(!D("VerifyCode")->_check_times($key)){
            if (!$v_code || $v_code != session('v_code')) {
                $this->sendError(10206,L('verification_code_are_incorrect'));
                return;
            }
        }

        $ret = D("User")->checkLogin($username, $password);
        if ($ret) {
            unset($ret['password']);
            session("login_user" , $ret );
            D("User")->setLastTime($ret['uid']);

            $token = D("UserToken")->createToken($ret['uid']);
            //cookie('cookie_token',$token, 60*60*24*90);//此处由服务端控制token是否过期，所以cookies过期时间设置多久都无所谓
            $_SESSION["cookie_token"] = $token;
            $this->sendResult(['token'=>$token]);

        }else{
            D("VerifyCode")->_ins_times($key);//输错密码则设置输错次数

            if(D("VerifyCode")->_check_times($key)){
                $error_code = 10204 ;
            }else{
                $error_code = 10210 ;
            }
            $this->sendError($error_code,L('username_or_password_incorrect'));
            return;
        }

    }



}
