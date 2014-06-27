<?php

/**
 * 后台管理左侧导航,只支持二级导航
 */
return array(
    array(
		'name' => '个人面板',
		'link' => 'Admin/Index',
		'children' => array(
			array(
				'name' => '修改密码',
				'link' => 'Admin/Index/changePassword',
			),
			array(
				'name' => '个人信息',
				'link' => 'Admin/Index/myInfo',
			),
		),
    ),
    array(
        'name' => '系统管理',
        'link' => 'Admin/System',
        'children' => array(
            array(
                'name' => '用户管理',
                'link' => 'Admin/User/index',    
            ),
            array(
                'name' => '用户组管理',
                'link' => 'Admin/UserGroup/index',    
            ),
            array(
                'name' => '权限设定',
                'link' => 'Admin/Ac/index',    
            ),
			array(
				'name' => '清空缓存',
				'link' => 'Admin/System/clearRuntime',
			),
			array(
				'name' => '应用管理',
				'link' => 'Admin/System/app',
			),
			array(
				'name' => '整站区块管理',
				'link' => 'Admin/Block/index',
			),
        ),    
    ),
);
