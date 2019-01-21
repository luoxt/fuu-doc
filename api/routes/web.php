<?php

//Application Routes
$router->group(['prefix' => 'api', 'namespace' => 'Api'], function () use ($router) {

    //返回版本号
    $router->get('/', function () use ($router) {
        return reJson(true, '200', $router->app->version(), ['time' => date('Y-m-d H:i:s')]);
    });

    //基础接口
    $router->group(['prefix' => 'base', 'namespace' => 'Base'], function () use ($router) {
        //上传图片
        $router->options('upload/images', ['uses' => 'UploadController@images']);
        $router->post('upload/images', ['uses' => 'UploadController@images']);

    });

    /**
     * @brief 客户后端接口 shop
     *
     */
    $router->group(['prefix' => '', 'namespace' => 'Shop'], function () use ($router) {

        //用户登录
        $router->post('user/login', ['uses' => 'UserController@login']);
        //获取用户信息
        $router->post('user/info', ['uses' => 'UserController@info']);
        //退出登录
        $router->post('user/logout', ['uses' => 'UserController@logout']);

        //获取文档列表
        $router->post('page/list', ['uses' => 'PageController@list']);
        //获取文档详情
        $router->post('page/info', ['uses' => 'PageController@info']);

        //获取项目详情
        $router->post('item/info', ['uses' => 'ItemController@info']);
        //
        $router->post('item/detail', ['uses' => 'ItemController@detail']);


        //登录访问 中间件授权
        $router->group(['middleware' => 'shop'], function () use ($router) {

            //项目管理
            $router->group(['prefix' => ''], function () use ($router) {
                //获取我的项目
                $router->post('item/mylist', ['uses' => 'ItemController@myList']);
                //删除项目
                $router->post('item/delete', ['uses' => 'ItemController@delete']);
                //保存项目
                $router->post('item/update', ['uses' => 'ItemController@update']);
                //添加项目
                $router->post('item/add', ['uses' => 'ItemController@add']);
                //
                $router->post('item/getKey', ['uses' => 'ItemController@getKey']);

            });

            //文档管理
            $router->group(['prefix' => ''], function () use ($router) {
                //保存文档详情
                $router->post('page/save', ['uses' => 'PageController@save']);
                //删除文档
                $router->post('page/delete', ['uses' => 'PageController@delete']);
                //文档历史
                $router->post('page/history', ['uses' => 'PageController@history']);
            });

            //成员管理
            $router->group(['prefix' => ''], function () use ($router) {
                //成员列表
                $router->post('member/getList', ['uses' => 'MemberController@getList']);
                //删除成员
                $router->post('member/delete', ['uses' => 'MemberController@delete']);
                //保存成员
                $router->post('member/save', ['uses' => 'MemberController@save']);
            });

            //目录管理
            $router->group(['prefix' => ''], function () use ($router) {
                $router->post('catalog/catListGroup', ['uses' => 'CatalogController@catListGroup']);
                //获取目录
                $router->post('catalog/secondCatList', ['uses' => 'CatalogController@secondCatList']);
                //默认目录
                $router->post('catalog/getDefaultCat', ['uses' => 'CatalogController@getDefaultCat']);
                //子目录
                $router->post('catalog/childCatList', ['uses' => 'CatalogController@childCatList']);
                //保存目录
                $router->post('catalog/save', ['uses' => 'CatalogController@save']);
                //删除目录
                $router->post('catalog/delete', ['uses' => 'CatalogController@delete']);
            });


        });
    });

});
