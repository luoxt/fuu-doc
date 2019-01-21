<?php

return [
    'fallback_locale' => 'cn',
    'locale' => 'cn',

    //安全验证
    'Vilidate' => [
        'required' => ':attribute 不得为空',
        'integer' => ':attribute 必须为数字',
        'alpha_num' => ':attribute 必须为字母、数字类型',
        'alpha_dash' => ':attribute 为字母、数字、破折号（-）或下划线（_）',
        'min' => ':attribute字符长度或数字大小不可小于:min',
        'max' => ':attribute字符长度或数字大小不可大于:max',
        'in' => ':attribute不正确',
        'json' => ':attribute 不是有效的 JSON 字符串',
        'mobile' => ':attribute请填写正确的手机号',
        'email' => ':attribute 请填写正确的邮件地址',
        'url' => ':attribute链接地址不合法',
        'regex' => ':attribute不合法',
    ],

    //大于短信配置
    'sms' => [
        'signName' => env('SMS_SIGN_NAME'),   //签名名称
        'regionId' => env('SMS_REGION_ID'),    //区域id
        'accessKeyId' => env('SMS_ACCESS_KEY_ID'),
        'accessSecret' => env('SMS_ACCESS_SECRET'),
    ],

    //微信第三方开放平台
    'wechat' => [
        //基础配置
        'app_id' => env('APP_ID'),
        'secret' => env('SECRECT'),
        'token' => env('TOKEN'),
        'aes_key' => env('AES_KEY'),
        'redirect' => env('APP_URL').env('REDIRECT'),

        //微信接口地址
        'wxapi' => [
            //授权登陆
            'auth_login' => "https://open.weixin.qq.com/connect/oauth2/authorize?appid=",
            //返回code
            'code' => "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=",

            //获取用户详情
            'userinfo' => "https://api.weixin.qq.com/sns/userinfo",
            //刷新令牌获取access_token
            'mgsAccessToken' => "https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=",
            //获取用户列表openid
            'getUserOpenId' => "https://api.weixin.qq.com/cgi-bin/user/get?access_token=",
            //增加图文素材
            'addMaterial' => "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=",
            //上传图文素材
            'addMpnews' => "https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=",
            //上传图文素材(永久)
            'addMaterialUrl' => "https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=",
            //创建分组
            'makeGroup' => "https://api.weixin.qq.com/cgi-bin/groups/create?access_token=",
            //群发预览
            'prviewMsg' => "https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=",
            //根据标签群发
            'sendAllByTag' => "https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=",
            //查询所有分组获得用户数量
            'searchGroupAll' => "https://api.weixin.qq.com/cgi-bin/groups/get?access_token=",
            //获取用户基本信息接口
            'openUserInfo' => "https://api.weixin.qq.com/cgi-bin/user/info?access_token=",
        ],
    ],

    //API-URL
    'apiUrl' => [
        // 网站host
        'host' => [
            'wechat_api' => env('WECHAT_API_HOST'),
            'platform_api' => env('PLATFORM_API_HOST'),
            'image' => env('IMG_HOST'),
        ],
        // version
        'version' => [
            'platform' => [
                'v' => env('PLATFORM_API_VERSION'),
            ]
        ],
        // header
        'header' => [
            'platform' => [
                'apikey' => env('PLATFORM_API_HEADER'),
            ]
        ],
        // 微信接口
        'wechat_api' => [
            'getJSDK' => 'home/outputapi/jsapiData',
        ],
        // 微信接口
        'platform_api' => [
            'bind_mobile' => 'user/user/bindPrimaryAccount',
        ],
    ],

    //状态码
    'StatusCode' => [
        200 => '成功',
        400 => '未知错误',
        401 => '无此权限',
        403 => '错误的请求方式',
        404 => '非法请求',
        500 => '服务器异常',

        ###用户信息
        1001 => '手机格式错误',
        1002 => '该用户不存在',
        1003 => '验证码格式错误',
        1004 => '验证码无效',
        1005 => '该用户无权限',
        1006 => '请填写手机号跟验证码',
        1007 => '验证码已失效',
        1008 => '验证码发送频繁，请稍后再发送',
        ###活动状态码
        //活动基本状态码
        3000 => '活动不存在',
        3001 => '保存活动异常',
        3002 => '活动未开启或已结束',
        //活动签到
        3100 => '活动配置信息不存在',
        3101 => '签到列表信息为空',
        //活动众筹
        3200 => '众筹活动列表为空',
        3201 => '规格名称不能为空',
        3202 => '规格名称最多输入25字符',
        3203 => '保存众筹失败',
        3204 => '众筹数据列表为空',
        3205 => '众筹不存在',
        3206 => '编辑众筹失败',
        3207 => '活动背景图列表为空',
        3208 => '只能清除一次数据',

        ###报表
        4000 => '生成报表失败',
        4001 => '报表数据为空',
    ],

];
