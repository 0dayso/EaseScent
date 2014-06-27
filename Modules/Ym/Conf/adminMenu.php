<?php

/**
 * 后台管理左侧导航,只支持二级导航
 */
return array(
    array(
		'name' => '企业通行证',
		'link' => 'Ym/Index',
		'children' => array(
			array(
				'name' => '用户列表',
				'link' => 'Ym/AdminUser/index',
			),
			array(
				'name' => '企业认证',
				'link' => 'Ym/CompanyConfirm/index',
			),
		),
    )
);
