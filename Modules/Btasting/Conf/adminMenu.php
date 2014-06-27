<?php

/**
 * 后台管理左侧导航,只支持二级导航
 */
return array(
    array(
		'name' => '品酒会管理',
		'link' => 'Btasting/Index',
		'children' => array(
			array(
				'name' => '品酒会管理',
				'link' => 'Btasting/Wtasting/index',
			),
			array(
				'name' => '专家团管理',
				'link' => 'Btasting/Expert/index',
			),
		),
    )
);
