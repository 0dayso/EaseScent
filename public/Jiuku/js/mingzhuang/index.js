$(function(){
	$(".v-france>li").hover(
		function(){
			$(this).prev().children(".v-list1").addClass("v-list1-prev");
			$(this).children(".v-list1").addClass("v-list1-hover");
			$(this).children(".v-list2").show();
		},
		function(){
			$(this).prev().children(".v-list1").removeClass("v-list1-prev");
			$(this).children(".v-list1").removeClass("v-list1-hover");
			$(this).children(".v-list2").hide();
		}
	);
	$(".v-del-1").click(function(){
		$(this).parent().hide();
	})
})
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
	$.getJSON(__ajax_domain+"?action=YENfQV8FeE9bX09ZXmNrWkMFTVhLRE5JWF91TU9edURPXXVAQ19aQ0RN&bgw=1&page="+page+"&callback=?",function(msg){
		if(msg.errorCode != 0 || msg.result.errno != 0){
			 $(".z_wineInfoList").html('<div class="msg">获取失败!</div>');return false;
		}
		if(msg.result.rst.length == 0){
			$(".z_wineInfoList").html('<div class="msg">暂无评论记录!</div>');return false;
		}
		generateCommentHTML(msg.result.rst.data,msg.result.rst.pageHtml);
	});
}
function generateCommentHTML(data,pageHTML){
	$(".z_page").html(pageHTML);
	var html='';
	$.each(data,function(){
		html += '<li class="clear">';
		html += 	'<p class="z_wineInfoList_userPic"><a href="'+__i_domain+'User/u/'+this.uid+'" target="__blank"><img src="'+this.avatar_url+'" /></a></p>';
		html += 	'<div class="z_wineInfoList_userSay">';
		html += 		'<p class="z_wineInfo_userInfo"><span class="clear"><a href="'+__i_domain+'User/u/'+this.uid+'" target="__blank" class="z_wineInfo_userInfo_userName">'+this.nickname+'</a><!--<i class="levelIco_16x16 cioV_16x16 mt4"></i> <i class="levelIco_16x16 cioLevel1_16x16 mt4" ></i>--></span>  ：'+this.content+'</p>';
		html += 		'<div class="z_wineInfo_transmit_wineInfo clear">';
		if(this.image){
			html += 		'<p class="pic"><a href="'+this.image_big+'" title="'+this.wine_name+'"><img src="'+this.image_210+'" /></a></p>';
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