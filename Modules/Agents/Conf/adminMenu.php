<?php

return array(
	array(
		'name' => '代理商管理',
		'link' => 'Agents/Index',
		'children' => array(
			array(
				'name' => '代理商管理',
				'link' => 'Agents/Agents/index'
			),
			array(
				'name' => '网络渠道管理',
				'link' => 'Agents/InternetSales/index'
			),
			array(
				'name' => '实体渠道管理',
				'link' => 'Agents/StoreSales/index'
			),
			array(
				'name' => '网络酒款管理',
				'link' => 'Agents/InternetWine/index'
			),
			array(
				'name' => '实体酒款管理',
				'link' => 'Agents/StoreWine/index'
			),
			array(
                'name' => '总代品牌审核',
                'link' => 'Agents/Method/index'
           ),
			array(
                'name' => '新建酒款审核',
                'link' => 'Agents/Jiumethod/index'
           ),
			array(
                'name' => '新建酒庄审核',
                'link' => 'Agents/Zhuangmethod/index'
           )
		)
	)
);
