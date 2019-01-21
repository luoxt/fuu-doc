<?php
namespace App\Http\Controllers\Api\Base;

use App\Http\Controllers\Api\Controller;

/**
 * 地区管理
 * @package App\Http\Controllers\Api\
 */
class AreaController extends Controller
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


    /**
     * @brief 区域树形数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $ext_api = 'admin/area/map';
        $body_data = [];

        return $this->pfPost($ext_api, $body_data);
    }

    /**
     * @brief 下级区域
     * @return \Illuminate\Http\JsonResponse
     */
    public function map()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'area_id' => 'required|integer',
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        $ext_api = 'area/subArea';
        $body_data = $this->postData($params, $rule);

        return $this->pfPost($ext_api, $body_data);
    }

    /**
     * @brief 区域信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function info()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'area_id' => 'required|integer|min:1',
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        $ext_api = 'area/info';
        $body_data = $this->postData($params, $rule);

        return $this->pfPost($ext_api, $body_data);
    }

}
