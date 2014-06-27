//验证标签表单
function check_cat()
{
	var cat_name=$("#cat_name").val();
	if(cat_name=='')
	{
		alert('标签不能为空');
		$("#cat_name").focus();
		return false;
	}
	return true;
}
//验证商品表单
function check_goods()
{
	var t_goods_url=$("#t_goods_url").val();
	if(t_goods_url=='')
	{
		alert('请输入商品地址,并点击获取商品详情按钮');
		$("#t_goods_url").focus();
		return false;
	}
	var t_goods_id=$("#t_goods_id").val();
	if(t_goods_id=='')
	{
		alert('未获取到淘宝/天猫商品编号,请重新点击获取商品详情按钮获取');
		$("#t_goods_id").focus();
		return false;
	}
	var click_url=$("#click_url").val();
	if(click_url=='')
	{
		alert('未获取到淘宝/天猫商品返利链接,请重新点击获取商品详情按钮获取');
		$("#click_url").focus();
		return false;
	}
	var goods_name=$("#goods_name").val();
	if(goods_name=='')
	{
		alert('未获取到淘宝/天猫商品返利链接,请重新点击获取商品详情按钮获取');
		$("#goods_name").focus();
		return false;
	}
	var goods_price=$("#goods_price").val();
	if(goods_price=='')
	{
		alert('未获取到淘宝/天猫商品价格,请重新点击获取商品详情按钮获取');
		$("#goods_price").focus();
		return false;
	}
	var goods_recommend=$("#goods_recommend").val();
	if(goods_recommend=='')
	{
		alert('请输入商品推荐理由');
		$("#goods_recommend").focus();
		return false;
	}
	var pic_url=$("#pic_url").val();
	if(pic_url=='')
	{
		alert('未获取到淘宝/天猫商品图片,请重新点击获取商品详情按钮获取或者点击上传图片按钮上传');
		$("#pic_url").focus();
		return false;
	}

	var is_check=0;
	$("input[type=checkbox]").each(function(){ //由于复选框一般选中的是多个,所以可以循环输出
			if($(this).attr('checked')){
				is_check=1;
			}
  	});
/*$("input[name='cat_ids':checked]").each(function(){
		var val = $(this).val();
});
	return false;*/
	if(is_check==0)
	{
		 alert("请选择商品所属标签");
		 return false;
	}
	return true;
}
//获取淘宝商品信息
regTaobaoUrl = /(.*\.?taobao.com(\/|$))|(.*\.?tmall.com(\/|$))/i;
function getTaoItem(url)
{
	 if(url==''){
		alert('网址不能为空！');
		return false;
	}
	if (!url.match(regTaobaoUrl)){
		alert('这不是一个淘宝网址！');
		return false;
	}
	$.ajax({
	    url: "index.php?app=Guide&m=Goods&a=ajax_getTaoItem",
		type: "POST",
		data:{'url':url},
		dataType: "json",
		success: function(data){
			if(data.s==0)
			{
			    alert(data.msg);
			}
			else if(data.s==1)
			{
	            $('#goods_name').val(data.re.title);
				$('#t_goods_id').val(data.id);
	            $('#goods_price').val(data.re.price);
				$('#price').html(data.re.price);
				if($('#is_regetimg').attr('checked'))
				{

				}
				else
				{
					$('#pic_url').val(data.re.pic_url);
					$('#img').html('<img width="150" height="150" src="'+data.re.pic_url+'_310x310.jpg" />');
					$('#img_from').val(0);
				}
				$('#click_url').val(data.re.click_url);
				if(data.re.promotion.item_promo_price)
				{
					$("#promotion").html(data.re.promotion.item_promo_price);
					$("#goods_promotion").val(data.re.promotion.item_promo_price);

					$("#promotion_start").html(data.re.promotion.start_time);
					$("#promotion_starttime").val(data.re.promotion.start_time);

					$("#promotion_end").html(data.re.promotion.end_time);
					$("#promotion_endtime").val(data.re.promotion.end_time);

				}
				//获取佣金比例
				get_commission('commission_info',data.id,'');
			}
		 }
	});
	//获取淘宝佣金，obj_id显示佣金框的编号，id淘宝商品编号，field要查询的信息
	//num_iid,title,nick,pic_url,price,click_url,commission,commission_rate,commission_num,commission_volume,shop_click_url,seller_credit_score,item_location,volume,promotion_price
	function get_commission(obj_id,id,field)
	{
		if(field=='')
		{
			field='num_iid,click_url,commission_rate,commission,volume,promotion_price,end_time,starttime'
		}
			 TOP.api('rest', 'get',{
    								method:'taobao.taobaoke.widget.items.convert',
   									num_iids:id,
    								fields:field
  									},function(resp){
									    if(resp.error_response){
									      alert('taobao.taobaoke.widget.items.convert获取佣金失败,错误：'+resp.error_response.msg);
									      return false;
								     }
						     		var respItem=resp.taobaoke_items.taobaoke_item;
								    for(var i=0;i<respItem.length;i++)
									{
										//alert(respItem[i].commission);
										$("#"+obj_id).html(respItem[i].commission);
										//设置隐藏域的佣金和佣金比例
										$("#commission").val(respItem[i].commission);
										$("#commission_rate").val(respItem[i].commission_rate/100);
								    }
  			})
	}
}