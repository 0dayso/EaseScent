<?php

return array(
	array(
		'name' => '终端报表',
		'link' => 'AppReport/Index',
		'children' => array(
			array(
				'name' => '概况',
				'link' => 'AppReport/Overview/index'
			),
			array(
				'name' => '应用-新增用户',
				'link' => 'AppReport/AppAddUser/index'
			),
			array(
				'name' => '应用-活跃用户',
				'link' => 'AppReport/AppActiveUser/index'
			),
			array(
				'name' => '应用-存留用户',
				'link' => 'AppReport/AppRetentionUser/index'
			),
			array(
				'name' => '应用-启动次数',
				'link' => 'AppReport/AppStarts/index'
			),
			array(
				'name' => '应用-版本分布',
				'link' => 'AppReport/AppVersionDistribu/index'
			),
			array(
				'name' => '用户-用户行为',
				'link' => 'AppReport/UserBehavior/index'
			),
			array(
				'name' => '用户-使用时段',
				'link' => 'AppReport/UserUseTime/index'
			),
			array(
				'name' => '用户-使用轨迹',
				'link' => 'AppReport/UserUseTrack/index'
			),
			array(
				'name' => '用户-地域分布',
				'link' => 'AppReport/UserAreaDistribu/index'
			),
			array(
				'name' => '渠道分析',
				'link' => 'AppReport/Channel/index'
			),
			array(
				'name' => '终端分布',
				'link' => 'AppReport/TerminalDistribu/index'
			),
		)
	)
);
