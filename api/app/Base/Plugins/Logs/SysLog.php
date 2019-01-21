<?php
namespace App\Base\Plugins\Logs;

use App\Base\Plugins\Guid\IdWork;
use Illuminate\Support\Facades\DB;

class SysLog
{

    public function __construct()
    {
    }

    public function info($log_params)
    {
        try {
            $log_id = new IdWork();
            $params = [
                'log_id' => $log_id->nextId(),    //bigint(20) NOT NULL,
                'account_id' => $log_params['account_id'],    // bigint(20) NOT NULL COMMENT '管理员ID',
                'account_name' => $log_params['account_name'],    // varchar(20) NOT NULL COMMENT '管理员姓名',
                'op_type' => $log_params['op_type'],    // char(20) DEFAULT NULL COMMENT '操作类型',
                'op_url' => $log_params['op_url'],    // varchar(100) DEFAULT NULL COMMENT '操作地址',
                'op_describe' => $log_params['op_describe'],    // varchar(200) DEFAULT NULL COMMENT '操作描叙',
                'op_params' => $log_params['op_params'],    // varchar(200) DEFAULT NULL COMMENT '请求参数',
                'op_time' => time(),    // timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '操作时间',
            ];
            DB::table('desktop_log')->insert($params);

        } catch (\Exception $exception){

        }


    }

}