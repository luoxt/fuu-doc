<?php
/**
 * @brief 权限列表
 * @param label 权限名称
 * @param sign 权限标示
 * @param url 权限地址
 * @param display 是否启用
 * @param children 子权限
 *
 */
return [

    //组织客户
    [
        'label' => '组织客户',
        'sign' => 'back.organize.index',
        'children' => [
            [
                'label'=>'组织客户管理',
                'sign' => 'back.organize',
                'children' => [
                    [
                        'label'=>'组织客户列表',
                        'sign' => 'back.organize.index',
                        'children' => [
                            [
                                'label'=>'多组织列表',
                                'sign' => 'back.organize.list',
                                'children' => []
                            ],
                            [
                                'label'=>'组织批量导入',
                                'sign' => 'back.organize.batch',
                                'url'=>'/back/organize/batch',
                                'children' => []
                            ],
                            [
                                'label'=>'添加组织',
                                'sign' => 'back.organize.add',
                                'children' => []
                            ],

                            [
                                'label'=>'组织修改',
                                'sign' => 'back.organize.update',
                                'children' => []
                            ],
                            [
                                'label'=>'组织启用',
                                'sign' => 'back.organize.enable',
                                'children' => []
                            ],
                            [
                                'label'=>'组织停用',
                                'sign' => 'back.organize.disable',
                                'children' => []
                            ],
                            [
                                'label'=>'组织删除',
                                'sign' => 'back.organize.delete',
                                'children' => []
                            ],
                            [
                                'label'=>'部门列表',
                                'sign' => 'back.organize.dept_list',
                                'children' => []
                            ],
                            [
                                'label'=>'组织用户',
                                'sign' => 'back.organize.user_info',
                                'children' => []
                            ],
                            [
                                'label'=>'组织用户添加',
                                'sign' => 'back.organize.user_add',
                                'children' => []
                            ],
                            [
                                'label'=>'组织用户修改',
                                'sign' => 'back.organize.user_update',
                                'children' => []
                            ],
                            [
                                'label'=>'组织银行',
                                'sign' => 'back.organize.bank_info',
                                'children' => []
                            ],
                            [
                                'label'=>'组织银行添加',
                                'sign' => 'back.organize.bank_add',
                                'children' => []
                            ],
                            [
                                'label'=>'组织银行修改',
                                'sign' => 'back.organize.bank_update',
                                'children' => []
                            ],
                            [
                                'label'=>'组织银行删除',
                                'sign' => 'back.organize.bank_delete',
                                'children' => []
                            ],
                            [
                                'label'=>'推荐人信息',
                                'sign' => 'back.organize.referee_info',
                                'children' => []
                            ],
                            [
                                'label'=>'保存推荐人信息',
                                'sign' => 'back.organize.referee_save',
                                'children' => []
                            ],
                        ]
                    ],

                    [
                        'label'=>'组织权限包',
                        'sign' => 'back.organize.package_list',
                        'url'=>'/back/organize/package_list',
                        'children' => []
                    ],
                    [
                        'label'=>'组织权限包保存',
                        'sign' => 'back.organize.package_save',
                        'url'=>'/back/organize/package_save',
                        'children' => []
                    ],
                    [
                        'label'=>'组织审核详情',
                        'sign' => 'back.organize.check_info',
                        'url'=>'/back/organize/check_info',
                        'children' => []
                    ],
                    [
                        'label'=>'组织审核保存',
                        'sign' => 'back.organize.check_save',
                        'url'=>'/back/organize/check_save',
                        'children' => []
                    ]

                ]
            ],

            [
                'label'=>'组织权限包',
                'sign' => 'back.package.info',
                'url'=>'/back/package/info',
                'children' => []
            ],
            [
                'label'=>'组织权限',
                'sign' => 'back.permission.index',
                'url'=>'/back/permission/index',
                'children' => [

                ]
            ]
        ]
    ],

    //系统管理
    [
        'label' => '系统管理',
        'sign' => 'back.area.index',
        'url'=>'/back/area/index',
        'children' => [
            [
                'label'=>'角色管理',
                'sign' => 'back.role.index',
                'url'=>'/back/role/index',
                'children' => []
            ],
            [
                'label'=>'系统用户',
                'sign' => 'back.user.index',
                'url'=>'/back/user/index',
                'children' => []
            ],
            [
                'label'=>'操作日志',
                'sign' => 'back.area.index',
                'url'=>'/back/area/index',
                'children' => []
            ]
        ]
    ]
];
