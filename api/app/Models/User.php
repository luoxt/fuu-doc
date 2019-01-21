<?php
/**
 * Created by PhpStorm.
 * User: ttyun
 * Date: 2018/11/3
 * Time: 14:27
 */

namespace App\Models;

/**
 * c端用户信息(主)表
 */
class User extends BaseModel
{
    protected $table = 'user';

    public $primaryKey = 'uid';//主键

    public $incrementing = false;//是否自增主键

    public $timestamps = false;//不自动维护created_at 和 updated_at

    public $guarded = ['']; //字段批量赋值黑名单 为空表示不限制


    /**
     * 用户名是否已经存在
     *
     */
    public function isExist($username)
    {
        return $this->getRow('*', ['username' => $username]);
    }

    /**
     * 注册新用户
     *
     */
    public function register($username, $password)
    {
        $password = md5(base64_encode(md5($password)) . '576hbgh6');
        return $this->insert(array('username' => $username, 'password' => $password, 'reg_time' => time()));
    }

    //修改用户密码
    public function updatePwd($uid, $password)
    {
        $password = md5(base64_encode(md5($password)) . '576hbgh6');
        return $this->up(array('password' => $password), ['uid' =>$uid]);
    }

    /**
     * 返回用户信息
     * @return
     */
    public function userInfo($uid)
    {
        return $this->getRow('*', ['uid'=>$uid]);
    }

    /**
     * @param username :登录名
     * @param password 登录密码
     */
    public function checkLogin($username, $password)
    {
        $password = md5(base64_encode(md5($password)) . '576hbgh6');
        return $this->getRow('*', ['username'=>$username, 'password'=>$password]);
    }

    //设置最后登录时间
    public function setLastTime($uid)
    {
        return $this->up(array("last_login_time" => time()), ['uid'=>$uid]);
    }

}