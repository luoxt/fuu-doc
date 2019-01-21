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
class Template extends BaseModel
{
    protected $table = 'template';

    public $primaryKey = 'id';//主键

    public $incrementing = false;//是否自增主键

    public $timestamps = false;//不自动维护created_at 和 updated_at

    public $guarded = ['']; //字段批量赋值黑名单 为空表示不限制
}