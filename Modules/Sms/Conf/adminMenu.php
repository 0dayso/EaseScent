<?php

return array(
	array(
		'name' => '短信平台管理',
		'link' => 'Sms/Index',
		'children' => array(
			array(
				'name' => '信息汇总',
				'link' => 'Sms/Info/index'
			),
			array(
				'name' => '第三方平台管理',
				'link' => 'Sms/Platform/manage'
			),
			array(
				'name' => '短信发送客户端管理',
				'link' => 'Sms/Client/index'
			),

			array(
				'name' => '短信发送列表',
				'link' => 'Sms/Msgs/index'
			),
            array(
				'name' => '批量发送短信',
				'link' => 'Sms/Batch/index'
			),
		)
	)
);
