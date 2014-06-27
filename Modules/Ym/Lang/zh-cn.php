<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/*
从事行业
1.教育/培训
2.酒店/餐饮
3.零售/批发
4.旅游/度假
5.交通/运输/物流
6.政府/公共事业/非盈利机构
7.综合性工商企业
8.快速消费品
9.娱乐/体育/休闲
10.IT/互联网/电子商务
11.贸易/进出口
12.其他
*/
$Lang = array();

define("HANGYE_1",1);
define("HANGYE_2",2);
define("HANGYE_3",3);
define("HANGYE_4",4);
define("HANGYE_5",5);
define("HANGYE_6",6);
define("HANGYE_7",7);
define("HANGYE_8",8);
define("HANGYE_9",9);
define("HANGYE_10",10);
define("HANGYE_11",11);
define("HANGYE_12",12);

$Lang["HANGYE"][HANGYE_1] = "教育/培训";
$Lang["HANGYE"][HANGYE_2] = "酒店/餐饮";
$Lang["HANGYE"][HANGYE_3] = "零售/批发";
$Lang["HANGYE"][HANGYE_4] = "教育/培训";
$Lang["HANGYE"][HANGYE_5] = "旅游/度假";
$Lang["HANGYE"][HANGYE_6] = "政府/公共事业/非盈利机构";
$Lang["HANGYE"][HANGYE_7] = "综合性工商企业";
$Lang["HANGYE"][HANGYE_8] = "快速消费品";
$Lang["HANGYE"][HANGYE_9] = "娱乐/体育/休闲";
$Lang["HANGYE"][HANGYE_10] = "IT/互联网/电子商务";
$Lang["HANGYE"][HANGYE_11] = "贸易/进出口";
$Lang["HANGYE"][HANGYE_12] = "其他";

/**
 * 
企业性质：
1.外资企业
2.合资企业
3.私营企业
4.民营企业
5.股份制企业
6.国有企业
7.上市公司
**/

define("XINGZHI_1",1);
define("XINGZHI_2",2);
define("XINGZHI_3",3);
define("XINGZHI_4",4);
define("XINGZHI_5",5);
define("XINGZHI_6",6);
define("XINGZHI_7",7);
$Lang["XINGZHI"][XINGZHI_1] = "外资企业";
$Lang["XINGZHI"][XINGZHI_2] = "合资企业";
$Lang["XINGZHI"][XINGZHI_3] = "私营企业";
$Lang["XINGZHI"][XINGZHI_4] = "民营企业";
$Lang["XINGZHI"][XINGZHI_5] = "股份制企业";
$Lang["XINGZHI"][XINGZHI_6] = "国有企业";
$Lang["XINGZHI"][XINGZHI_7] = "上市公司";


/**
企业规模:
10人以下
10-50人
50-200人
200-500人
500-1000人
1000-5000人
5000人以上
**/
define("GUIMO_1",1);
define("GUIMO_2",2);
define("GUIMO_3",3);
define("GUIMO_4",4);
define("GUIMO_5",5);
define("GUIMO_6",6);
define("GUIMO_7",7);
$Lang["GUIMO"][GUIMO_1] = "10人以下";
$Lang["GUIMO"][GUIMO_2] = "10-50人";
$Lang["GUIMO"][GUIMO_3] = "50-200人";
$Lang["GUIMO"][GUIMO_4] = "200-500人";
$Lang["GUIMO"][GUIMO_5] = "500-1000人";
$Lang["GUIMO"][GUIMO_6] = "1000-5000人";
$Lang["GUIMO"][GUIMO_7] = "5000人以上";
