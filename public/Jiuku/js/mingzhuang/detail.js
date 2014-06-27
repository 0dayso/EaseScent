$(function(){
	//hit
	$.getJSON(__ajax_domain+"?action=X3xgfmA6WmBhVHZCfHtwZ2w6fXxh&id="+__id+"&callback=?",function(msg){
		if(msg.errorCode == 0)	$(".z_mz_plBox").find("span:eq(0)").html(msg.result);
	});
});
function isLoginAfter(){
	$(document).ready(function(){
		isAttention();		   
	});
}
/*关注操作*/
function isAttention(){
	$.getJSON(__ajax_domain+"?action=bU5STFIIdUJWUkJUU25mV04IQFVGSUNEVVJ4TlR4RlNTQklTTkhJ&id="+__id+"&callback=?",function(msg){
		if(msg.errorCode == 600003){
			$("#attention_li").html('<span class="already-a"><a href="javascript:popuplogin()">登录</a>后可关注该名庄!</span>');
		}else if(msg.result.data == 1){
			$("#attention_li").html('<span class="already-a">已关注</span><a href="javascript:unattention()" class="already-a"">取消关注</a>');
		}else if(msg.result.data == 0){
			$("#attention_li").html('<a class="attention-a" href="javascript:attention()">关注</a>');
		}
	});
}
function attention(){
	$("#attention_li").html('<span class="already-a">请稍后...</span>');
	$.getJSON(__ajax_domain+"?action=bk1RT1ELdkFVUUFXUG1lVE0LQ1ZFSkBHVlF7RVBQQUpQTUtK&id="+__id+"&callback=?",function(msg){
		if(msg.errorCode == 0 && msg.result.status == 1){
			$("#attention_li").html('<span class="already-a">已关注</span><a href="javascript:unattention()" class="already-a"">取消关注</a>');
		}else{
			$("#attention_li").html('<span class="already-a">内部错误</span>');
		}
	});
}
function unattention(){
	$("#attention_li").html('<span class="already-a">请稍后...</span>');
	$.getJSON(__ajax_domain+"?action=bE9TTVMJdENXU0NVUm9nVk8JQVRHSEJFVFN5U0hHUlJDSFJPSUg%3D&id="+__id+"&callback=?",function(msg){
		if(msg.errorCode == 0 && msg.result.status == 1){
			$("#attention_li").html('<a class="attention-a" href="javascript:attention()">关注</a>');
		}else{
			$("#attention_li").html('<span class="already-a">内部错误</span>');
		}
	});
}
/*关注操作END*/
//内容显示隐藏
$(function(){
	$("#article-a").click(function(){
		$(".article-detail").toggle();
		if($(".article-detail").css("display")=="block"){
			$("#article-a").attr("class","article-a1");
			$(document).scrollTop($(".article-box").offset().top);//移动滚动条
			$("#article-a").text("收起全部");
		}else{
			$("#article-a").attr("class","article-a");
			$(document).scrollTop($(".article-box").offset().top);//移动滚动条
			$("#article-a").text("查看全部");
		}
	});
});
//内容显示隐藏END
//图片滚动
function img_scroll(con,conlist,c1,c2){
	var i=$(conlist).length;
	$(con).width(i*122);
	$(c1).click(function(){
		var marginL = $(con).css("margin-left")=="auto"?0:parseInt($(con).css("margin-left"));
		if(i>5){
			if(!$(con).is(":animated")){
				$(con).animate({
					marginLeft:marginL-122+"px"
				},"slow",function(){
					i=i-1;
					$(c2).css("cursor","pointer");
					if(i<=5){
						$(c1).css("cursor","auto");
					}
				})
			}
		}
	});
	$(c2).click(function(){
		var marginL = $(con).css("margin-left")=="auto"?0:parseInt($(con).css("margin-left"));
		if(i<$(conlist).length){
			if(!$(con).is(":animated")){
				$(con).animate({
					marginLeft:marginL+122+"px"
				},"slow",function(){
					i=i+1;
					$(c1).css("cursor","pointer");
					if(i>=$(conlist).length){
						$(c2).css("cursor","auto");	
					}
				})
			}
		}
	})
}
$(function(){
	img_scroll(".c-box",".c-box>a","#c1","#c2");
});
//图片滚动END
//酒款分数随select改变
$(function(){
	$(".score_years").each(function(i){
		$(this).change(function(){
			var html = {};
			$("#"+$(this).val()+"_score>em").each(function(i){
				html[i] = $(this).html();
			});
			$(this).parent().children('em').each(function(i){
				$(this).html(html[i]);
			});
		});
	})
});
//酒款分数随select改变END
//酒款分数列表显示隐藏
$(function(){
	$(".manor-right").each(function(i){
		$(".manor-right").eq(i).css("z-index",100-i);
	})
	$(".history").each(function(){
		$(this).click(function(){
			$(this).next(".history-list-box").toggle();
			if($(this).next(".history-list-box").css("display")=="block")
				$(this).html("<a href='javascript:;;'>关闭历年所有评分</a>");
			else
				$(this).html("<a href='javascript:;;'>查看历年所有评分</a>");
		})
	});
});
//酒款分数列表显示隐藏END
//获取酒评
$(function(){
	get_comment();
});
function gotopage(page){
	$(document).scrollTop($(".z_wineInfo_l").offset().top);//移动滚动条
	get_comment(page);
}
function get_comment(page)
{
	var page = page ? page : 1;
	$(".z_wineInfoList").html('<div class="msg">评论加载中...</div>');
	if(!__wine_idstr){
		$(".z_wineInfoList").html('<div class="msg">暂无评论记录!</div>');return false;
	}
	$.getJSON(__ajax_domain+"?action=Y0BcQlwGe0xYXExaXWBoWUAGTltIR01KW1x2TkxddkNAXFl2S1B2QE0%3D&wine_idstr="+__wine_idstr+"&page="+page+"&page_limit="+__page_limit+"&callback=?",function(msg){
		if(msg.errorCode != 0 || msg.result.errno != 0){
			 $(".z_wineInfoList").html('<div class="msg">获取失败!</div>');return false;
		}
		$(".z_mz_plBox").find("span:eq(1)").html(msg.result.rst.total_count);
		if(msg.result.rst.total_count == 0){
			$(".z_wineInfoList").html('<div class="msg">暂无评论记录!</div>');return false;
			//$(".v-comment-page").html('');
		}
		generateCommentHTML(msg.result.rst.data,msg.result.rst.pageHtml,msg.result.rst.pageHtml2);
	});
}
function generateCommentHTML(data,pageHTML,pageHTML2){
	$(".z_page").html(pageHTML);
	$(".z_mz_page_ul").html(pageHTML2);
	var html='';
	$.each(data,function(){
		html += '<li class="clear">';
		html += 	'<p class="z_wineInfoList_userPic"><a href="'+__i_domain+'User/u/'+this.uid+'" target="__blank"><img src="'+this.avatar_url+'" /></a></p>';
		html += 	'<div class="z_wineInfoList_userSay">';
		html += 		'<p class="z_wineInfo_userInfo"><span class="clear"><a href="'+__i_domain+'User/u/'+this.uid+'" target="__blank" class="z_wineInfo_userInfo_userName">'+this.nickname+'</a><!--<i class="levelIco_16x16 cioV_16x16 mt4"></i> <i class="levelIco_16x16 cioLevel1_16x16 mt4" ></i>--></span>  ：'+this.content+'</p>';
		html += 		'<div class="z_wineInfo_transmit_wineInfo clear w488">';
		if(this.image){
			html += 		'<p class="pic"><a href="'+this.image_big+'" title="'+this.wine_name+'"><img src="'+this.image_200+'" /></a></p>';
		}
		html += 			'<div class="nr">';
		html += 				'<p>中文名：<i>'+this.wine_name+'</i></p>';
		html += 				'<p>英文名：<i>'+this.wine_e_name+'</i></p>';
		html += 				'<p class="col_959595">年份：<i>'+this.wine_year+'</i></p>';
		if(this.wine_score == '1' || this.wine_score == '2' || this.wine_score == '3' || this.wine_score == '4' || this.wine_score == '5'){
			html += 			'<p class="col_959595"><span>'+this.nickname+'</span>的评价：<i><img src="'+__i_domain+'User/Template/Default/images/x'+this.wine_score+'.jpg" /></i></p>';
		}
		html += 			'</div>';
		html += 		'</div>';
		html += 		'<div class="z_wineInfo_sendwine clear">';
		html += 			'<p class="z_wineInfo_sendwine_time"><a href="'+__i_domain+'index.php?m=detail&tid='+this.tid+'" target="__blank">'+this.format_time+'</a> 来自 <a href="'+this.from_url+'" target"__blank">'+this.from+'</a></p>';
		html += 			'<div class="z_wineInfo__sendwineBtn">';
		html += 				'<span><a href="'+__i_domain+'index.php?m=detail&tid='+this.tid+'" target="__blank">转发（<i>'+this.forwards+'</i>）</a> | <a href="'+__i_domain+'index.php?m=detail&tid='+this.tid+'" target="__blank">收藏（<i>0</i>）</a> | <a href="'+__i_domain+'index.php?m=detail&tid='+this.tid+'" target="__blank">评论（<i>'+this.replys+'</i>）</a></span>';
		html +=				'</div>';
		html += 		'</div>';
		html += 	'</div>';
		html += '</li>';
	})
	$(".z_wineInfoList").html(html);
	zoomIni();
}
//获取关注列表
$(function(){
	$.getJSON(__ajax_domain+"?action=Y0BcQlwGe0xYXExaXWBoWUAGTltIR01KW1x2SF1dTEddQEZHdlxaTFs%3D&id="+__id+"&callback=?",function(msg){
		if(msg.errorCode == 0 && msg.result.status == 1 && msg.result.data.count > 0){
			var html = '';
			html += '<h2 class="attention-right-h2"><span>最新关注</span><!--<a href="##">查看更多成员</a>--></h2>';
			html += '<div class="img-box">';
			$.each(msg.result.data.data,function(i,n){
				html += '<a href="'+__i_domain+'User/u/'+this.uid+'" target="_blank" title="'+this.nickname+'" hidefocus><img src="'+this.avater+'"><var>'+this.nickname+'</var></a>';
				if(i > 4) return false;
			});
			html += '</div>';
			$(".attention-right").append(html);
		}
	});
});
//获取关注列表END