$(function(){
	//时间
	$("#t").hide();
	//价格
	$("#p").hide();
	//城市
	$("#c").hide();
	//默认隐藏按城市分类列表
	//$("div[tp='cydiv']").hide();
	$("div[tp='pediv']").hide();
	//$("div[tp='cyabc']").hide();
	$("div[tp='peabc']").hide();
    
	//显示隐藏日期
	$("#tpt").click(function(event){
		event.stopPropagation();
		if($("#t").is(":hidden")){
			$("#tpt").removeClass();
			$("#tpt").addClass("wine_nav_time date_bg");
			$("#t").show();	
		}else{
			$("#tpt").removeClass();
			$("#tpt").addClass("wine_nav_time");
			$("#t").hide();	
		} 		
	});
	//显示隐藏价格
	$("#ppt").click(function(event){
		event.stopPropagation();
		if($("#p").is(":hidden")){
			$("#ppt").removeClass();
			$("#ppt").addClass("wine_nav_price date_bg");
			$("#p").show();	
		}else{
			$("#ppt").removeClass();
			$("#ppt").addClass("wine_nav_price");
			$("#p").hide();	
		} 		
	});
	//显示城市列表
	$("#cshow").click(function(event){
		event.stopPropagation();
		$("#c").show();
	});
	//点击页面隐藏层
	$("body").click(function(){
		$("#c").hide();
		$("#t").hide();
		$("#p").hide();
		$("#ppt").removeClass();
		$("#tpt").removeClass();
		$("#tpt").addClass("wine_nav_time");
		$("#ppt").addClass("wine_nav_price");
	});
	$("#c").click(function(event){
		event.stopPropagation();
	});
	//隐藏城市列表
	$("#chide").click(function(){
		$("#c").hide();
	});
	//点击显示隐藏按省,按城市列表
	$("#allpe").click(function(){
		if($("#allpe").attr("class")=='city_select_a'){
			return;
		}else{
			$("#allpe").attr("class","city_select_a");
			$("#allcy").attr("class","");
			$("div[tp='pediv']").show();
			$("div[tp='peabc']").show();
			$("div[tp='cydiv']").hide();
			$("div[tp='cyabc']").hide();
		}
	});
	$("#allcy").click(function(){
		if($("#allcy").attr("class")=='city_select_a'){
			return;
		}else{
			$("#allcy").attr("class","city_select_a");
			$("#allpe").attr("class","");
			$("div[tp='cydiv']").show();
			$("div[tp='cyabc']").show();
			$("div[tp='pediv']").hide();
			$("div[tp='peabc']").hide();
		}
	});

	//搜索城市名
	$("#incyname").blur(function(){
		if($(this).val() == '') {
			$(this).val('输入城市名');
		}
	}).focus(function(){
		if($(this).val() == '输入城市名') {
			$(this).val('');
		}
	});
	$("#inbt").click(function(){
		var temp = '';
		var cname = '';
		if($("#incyname").val() != '输入城市名') {
			$("div[class='city_rank']").find("div:visible").find("a").each(function(){
				if($(this).text()==$("#incyname").val()) {
					//alert($(this).parent().parent().attr("id"));
					//alert($("#incyname").val());
					temp = $(this).parent().parent().attr("id");
					cname = $(this).text();
				}
			});
			if(temp !='') {
				var offsetHeight = $("div[class='city_rank']")[0].offsetHeight;
				var offsetTop = $("#"+temp)[0].offsetTop;
				//alert(offsetTop);
				var scrollTop = offsetTop - offsetHeight;
				$("div[class='city_rank']").scrollTop(scrollTop);
				$("div[class='city_rank']").find("a").each(function(){
					$(this).css("color","");
				})
				$("#"+temp).find("a").each(function(){
					if($(this).text()==cname) {
						$(this).css("color","red");
					}
				})	
				
			}else {
				alert('没有这个城市');
			}
		}else {
			alert('请输入城市名');
		}
	})
});
//计算城市列表滚动距离
function s_top(id) {
	var offsetHeight = $("div[class='city_rank']")[0].offsetHeight;
	var offsetTop = $("#"+id)[0].offsetTop;
	//alert(offsetTop);
	var scrollTop = offsetTop - offsetHeight;
	$("div[class='city_rank']").scrollTop(scrollTop);
}
