<?php


// +----------------------------------------------------------------------
// | 商机网 [ 58.wine.cn]
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2013 http://58.wine.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Angf <272761906@qq.com>
// +----------------------------------------------------------------------



/**
 * Expo 新版本数据转换
 * @category   web 前端
 * @subpackage  Action
 * @author    Angf <272761906@qq.com>
 */


class SqlReplaceAction extends Action {

	public function _initialize() {
		echo "未开启";exit;
		header("Content-type:text/html;charset=utf-8");
	}
    /*
     * 默认入口 酒庄和代理商 导入操作
     */
    public function index() {

		if($_GET['ac']==1){
			$Model = new Model();
			$Model->execute("INSERT INTO eswine.expo_agent(a_id,company_type,email,qy_id,qy_name,pic_url,description) SELECT id,0,email,qy_id,qy_name,pic_url,description FROM expo.expo_agent");
			echo "代理商-数据导入成功...<br>";
			$Model->execute("INSERT INTO eswine.expo_agent(w_id,company_type,email,qy_id,qy_name,pic_url,description) SELECT id as w_id,1,email,qy_id,qy_name,pic_url,description FROM expo.expo_winery");
			echo "酒庄 - 数据导入成功...<br>";
		}
		echo ':-> &nbsp;&nbsp;&nbsp;&nbsp; <a target="_blank" href="index.php?m=SqlReplace&a=index&ac=1">导入酒庄和代理商的用户信息</a><br/><br/>';
		echo ':-> &nbsp;&nbsp;&nbsp;&nbsp; <a target="_blank" href="index.php?m=SqlReplace&a=goods_replace">导入商品数据</a><br/><br/>';
		echo ':-> &nbsp;&nbsp;&nbsp;&nbsp; <a target="_blank" href="index.php?m=SqlReplace&a=update_goodsImgs">导入商品图片</a><br/><br/>';
		echo ':-> &nbsp;&nbsp;&nbsp;&nbsp; <a target="_blank" href="index.php?m=SqlReplace&a=goods_field_update">处理字段差异问题</a><br/><br/>';
    }


    //导入产品数据
	public function goods_replace(){
		$Model = new Model();
		//导入代理商的用户的产品
		$Model->execute("INSERT INTO  eswine.expo_goods(goods_id,uid,agent_id,wineid,fname,cname,typeid,typename,second_typeid,second_typename,variety,country_id,country_name,region_id,region_name,awards,strength,volume,price_type,goods_price,currency,minimum,pic_url,create_time,sale_status,is_delete,company_type) SELECT id,0,agent_id,jk_id,fname,cname,type_id,null,typesub_id,null,variety,country_id,null,region_id,null,honor,degree,if(volume=-1,null,volume),1,0,0,lowest_buy_amount,pic_url,null,1,0,0 FROM expo.expo_wine  as ex WHERE ex.agent_id > 0");

		$Model->execute("update  eswine.expo_goods as g  inner join (select id,qy_id,a_id from eswine.expo_agent) as a on a.a_id  = g.agent_id  set g.agent_id = a.id , g.uid=a.qy_id where g.company_type=0");
		echo "代理商产品导入成功...<br/>";


		//导入代理商的用户的产品
		$Model->execute("INSERT INTO  eswine.expo_goods(goods_id,uid,agent_id,wineid,fname,cname,typeid,typename,second_typeid,second_typename,variety,country_id,country_name,region_id,region_name,awards,strength,volume,price_type,goods_price,currency,minimum,pic_url,create_time,sale_status,is_delete,company_type) SELECT id,0,winery_id,jk_id,fname,cname,type_id,null,typesub_id,null,variety,country_id,null,region_id,null,honor,degree,if(volume=-1,null,volume),1,0,0,lowest_buy_amount,pic_url,null,1,0,1 FROM expo.expo_wine  as ex WHERE ex.winery_id > 0");

		$Model->execute("update  eswine.expo_goods as g  inner join (select id,qy_id,w_id from eswine.expo_agent) as a on a.w_id = g.agent_id   set g.agent_id = a.id , g.uid=a.qy_id where g.company_type=1");
		echo "酒庄产品导入成功...<br/>";
	}

	//更新产品图片
	public function update_goodsImgs(){
		$Model = new Model();
		$Model->execute('INSERT INTO eswine.expo_goods_img(goods_id,img_width,img_height,img_url) SELECT wine_id,img_width,img_height,url FROM expo.expo_wine_img');
		echo "图片处理完成";
	}



	//更新商品部分字段 升级后的问题
	public function goods_field_update(){
		$Model = new Model();
		$Model->execute(" UPDATE eswine.expo_goods SET variety = replace(variety,'%','+') ");
		$Model->execute(" UPDATE eswine.expo_goods SET variety = replace(variety,'+',',') ");
		$Model->execute(" UPDATE eswine.expo_goods SET variety = replace(variety,',,',',') ");
		$Model->execute(" UPDATE eswine.expo_goods SET variety = replace(variety,':,','') where variety REGEXP '[/\:,$/]' ");
		ECHO "更新成功";
	}










}
