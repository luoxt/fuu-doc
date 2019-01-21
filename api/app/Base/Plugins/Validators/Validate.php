<?php

namespace App\Base\Plugins\Validators;

/**
 * 使用例子：
 * use App\Base\Plugins\Validators\Validate;
 * class IndexController extends Controller
 * {
 *    use Validate;
 *
 *    if ($this->validation(['email' => '6666'], ['email' => 'required|email']) === false) {
 *       $error = $this->error();
 *       return reJson(false, $error['code'], $error['message']);
 *    }
 *   ...
 */
use Validator;

trait Validate
{
    protected $error_message  = '';

    protected $error_code  = 4000;

    /**
     * @brief 验证方法入口
     * @param array $request
     * @param array $rule
     * @return bool
     */
    public function validation($request = [], $rule = [])
    {
        if (!array_filter($request)) {
            return false;
        }
        if (!array_filter($rule)) {
            return false;
        }

        //获取错误信息
        $messages = config('config.Vilidate');
        $validator = Validator::make($request, $rule, $messages);

        if ($validator->fails()) {
            $message = current($validator->errors()->all());
            if($message){
                $this->error_code = 4000;
                $this->error_message = $message;
            }
            return false;
        }
        return true;

        //获取错误信息
//        $messages = config('config.Vilidate');
//        $validator = app()['Validator']::make($request, $rule, $messages);
//
//        if ($validator->fails()) {
//            debug($validator->errors());
//            $this->error_code = 4001;
//
//            //前端传参错误
//            foreach ($validator->errors()->all() as $message) {
//                $this->error[] = $message;
//            }
//            return false;
//        }
//        return true;
    }

    /**
     * @brief 输出错误信息
     * @return array
     */
    public function error()
    {
        $res = [
            'code' => $this->error_code,
            'message' => $this->error_message
        ];
        return $res;
    }

}
