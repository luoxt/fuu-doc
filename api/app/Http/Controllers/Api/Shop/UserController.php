<?php
namespace App\Http\Controllers\Api\Shop;

use App\Models\User;
use App\Models\UserToken;

/**
 *
 * @package App\Http\Controllers\Api\Organize
 */
class UserController extends ShopController
{

    protected $user_model = null;
    protected $usertoken_model = null;
    /**
     * Create a new controller instance.
     * IndexController constructor.
     * @param
     */
    public function __construct(User $user, UserToken $userToken)
    {
        parent::__construct();
        $this->user_model = $user;
        $this->usertoken_model = $userToken;
    }

    //注册
    public function register()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'username' => 'required|alpha_num',  
            'password' => 'required|alpha_num',  
            'confirm_password' => 'required|alpha_num',  
            'v_code' => 'alpha_num',  
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);

        $username = trim($post_data['username']);
        $password = $post_data['password'];
        $confirm_password = $post_data['confirm_password'];
        $v_code = $post_data['v_code'];

        if (C('CloseVerify') || $v_code && $v_code == session('v_code') ) {
            if ( $password != '' && $password == $confirm_password) {

                if ( ! D("User")->isExist($username) ) {
                    $new_uid = D("User")->register($username,$password);
                    if ($new_uid) {
                        //设置自动登录
                        $ret = D("User")->where("uid = '$new_uid' ")->find() ;
                        unset($ret['password']);
                        session("login_user" , $ret );
                        $token = D("UserToken")->createToken($ret['uid']);
                        cookie('cookie_token',$token,60*60*24*90);//此处由服务端控制token是否过期，所以cookies过期时间设置多久都无所谓
                        session('v_code',null) ;
                        $this->sendResult(array());

                    }else{
                        $this->sendError(10101,'register fail');
                    }
                }else{
                    $this->sendError(10101,L('username_exists'));
                }

            }else{
                $this->sendError(10101,L('code_much_the_same'));
            }
        }else{
            $this->sendError(10206,L('verification_code_are_incorrect'));
        }
    }

    //登录
    public function login()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'username' => 'required',  
            'password' => 'required',  
            'v_code' => 'alpha_num',  
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);

        $username = $post_data['username'];
        $password = $post_data['password'];

        $ret = $this->user_model->checkLogin($username, $password);
        if (isset($ret['uid']) && $ret['uid']) {
            unset($ret['password']);
            $this->user_model->setLastTime($ret['uid']);

            $token_expire = time() + 60*60*24*90;
            $token = md5(md5($ret['uid'].$token_expire.time().rand(100000, 999999)));

            $token = $this->usertoken_model->createToken($token, $token_expire, $ret);
            $ret['token'] = $token;

            return reJson('true', '200', '登录成功', $ret);
        }
        return reJson('false', '400', '登录失败');

    }

    //获取用户信息
    public function info()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'token' => 'required|alpha_num',
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);
        $token = $post_data['token'];

        $login_user = $this->usertoken_model->getToken($token);
        return reJson('true', '200', '获取成功', $login_user);
    }

    //通过旧密码验证来更新用户密码
    public function resetPassword()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'token' => 'required|alpha_num',
            'password' => 'required|alpha_num',
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);
        $token = $post_data['token'];
        $password = $post_data['password'];

        $login_user = $this->usertoken_model->getToken($token);
        if ($login_user) {
            $ret = $this->user_model->updatePwd($login_user['uid'], $password);
            if ($ret) {
                return reJson('true', '200', '设置成功');
            }
        }
        return reJson('false', '400', '设置失败');
    }

    //退出登录
    public function logout()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'token' => 'required|alpha_num',
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);
        $token = $post_data['token'];

        $res = $this->usertoken_model->getToken($token);
        if(isset($res['uid']) && $res['uid']){
            $ret = $this->usertoken_model->del(['uid'=>$res['uid']]);
            if ($ret) {
                return reJson('true', '200', '退出成功');
            }
            return reJson('false', '400', '退出失败');
        }
        return reJson('true', '200', '退出成功');
    }



}
