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
class UserToken extends BaseModel
{
    protected $table = 'user_token';

    public $primaryKey = 'id';//主键

    public $incrementing = false;//是否自增主键

    public $timestamps = false;//不自动维护created_at 和 updated_at

    public $guarded = ['']; //字段批量赋值黑名单 为空表示不限制


    public function createToken($token, $token_expire = 0, $data_info=[] )
    {
        $data['uid'] = $data_info['uid'] ;
        $data['token'] = $token ;
        $data['token_expire'] = $token_expire ;
        $data['ip'] = '' ;
        $data['addtime'] = time() ;
        $data['data_info'] = json_encode($data_info);

        $ret = $this->insert($data);
        if ($ret) {
            //删除过期的token
            $this->del( [["token_expire", '<', time()]]);
            return $token ;
        }
        return false ;
    }

    public function getToken($token)
    {
        return $this->getRow('*', ['token'=>$token]);
    }

    public function setLastTime($token)
    {
        return $this->up(array("token_expire"=>time()+2*24*3600), ['token'=>$token]);
    }
}