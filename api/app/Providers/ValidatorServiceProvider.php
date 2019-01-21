<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * 验证方法扩展
 * @author zicai
 * @date 2017-7-25 15:28:42
 */
class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 验证是否是整数或纯整数的一维数组
        app()['Validator']::extend('integerOrIntegerArray', function ($attribute, $value, $parameters, $validator) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    if (!ctype_digit((string)$v)) {
                        return false;
                    }
                }
                return true;
            }else if(ctype_digit((string)$value)){
                return true;
            }
            return false;
        });

        // 验证是否是手机号码
        app()['Validator']::extend('mobile', function ($attribute, $value, $parameters, $validator) {
            if (preg_match("/^1\d{10}$/", $value)) {
                return true;
            }
            return false;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
