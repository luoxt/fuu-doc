<?php
namespace App\Exceptions;

/**
 * @brief API异常处理
 * @package App\Exceptions
 * @author luoxt
 * @date 2017-08-23
 *
 * use App\Exceptions\ApiException;
 * throw new ApiException('api erros', 5001);
 */
class ApiException extends \Exception
{
    function __construct($msg='', $code = '4001')
    {
        parent::__construct($msg, $code);
    }


}
