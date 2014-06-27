function l_oc(this1,id){
	var oc = $(this1).attr('oc');
	if(oc == '1'){
		$(this1).parent("td").parent("tr").after('<tr id="'+id+'_more"><td colspan="'+$(this1).parent("td").parent("tr").children("td").length+'" class="list_more"></td></tr>');
		$(this1).attr({oc:'0',title:'收起',src:__public_domain__+'/Jiuku/images/common/list-close.png'});
		get_wine_desc(id);
	}else{
		$(this1).attr({oc:'1',title:'展开',src:__public_domain__+'/Jiuku/images/common/list-open.png'});
		$("#"+id+"_more").remove();
	}
}
function get_wine_desc(id){
	$("#"+id+"_more").children("td").html('<div class="list_more_msg"><img src="'+__public_domain__+'/Jiuku/images/common/loading.gif" />获取详情...</div>');
	$.getJSON(__ajax_domain__+"?action=Xn1hf2E7REtVZH07Y316cVJ9enA%3D=&id="+id+"&st=1&isf=1&isc=1&isr=1&isw=1&ist=1&isg=1&isrw=1&callback=?",function(msg){
		if(msg.errorCode != 0){
			$("#"+id+"_more").children("td").html('<div class="list_more_msg">获取失败！<span onclick="get_wine_desc('+id+');">重试</span></div>');
			return false;
		}
		msg.result.s_country = '';
		if(msg.result.country){
			msg.result.s_country += msg.result.country.fname+'╱'+msg.result.country.cname;
		}
		msg.result.s_region = '';
		if(msg.result.region){
			$.each(msg.result.region,function(key,val){
				msg.result.s_region += (key==0) ? '' : '<br />';
				$.each(val,function(k,v){
					msg.result.s_region += (k==0) ? '' : ' ＞ ';
					msg.result.s_region += v.fname+'╱'+v.cname;
				})
			})
		}
		msg.result.s_winery = '';
		if(msg.result.winery){
			$.each(msg.result.winery,function(key,val){
				msg.result.s_winery += (key==0) ? '' : '<br />';
				msg.result.s_winery += val.fname+'╱'+val.cname;
			})
		}
		msg.result.s_winetype = '';
		if(msg.result.winetype){
			$.each(msg.result.winetype,function(key,val){
				msg.result.s_winetype += (key==0) ? '' : ' ＞ ';
				msg.result.s_winetype += val.fname+'╱'+val.cname;
			})
		}
		msg.result.s_grape = '';
		if(msg.result.grape){
			$.each(msg.result.grape,function(key,val){
				msg.result.s_grape += (key==0) ? '' : '<br />';
				msg.result.s_grape += val.percent ? val.percent+'% ' : '';
				msg.result.s_grape += val.fname+'╱'+val.cname;
			})
		}
		msg.result.s_refweb = '';
		if(msg.result.refweb){
			$.each(msg.result.refweb,function(key,val){
				msg.result.s_refweb += (key==0) ? '' : '<br />';
				msg.result.s_refweb += val.fname+'╱'+val.cname+': <a href="'+val.refweb_url+'" target="_blank">'+val.refweb_url+'</a>';
			})
		}
		var html = '';
		html +='<div class="list_more_res"><ul>';
		html +='<li><span>序号:</span><var>'+msg.result.id+'</var></li><li><span>外文名:</span><var>'+msg.result.fname+'</var></li><li><span>中文名:</span><var>'+msg.result.cname+'</var></li><li><span>别名:</span><var>'+msg.result.aname+'</var></li><li><span>所属国家:</span><var>'+msg.result.s_country+'</var></li><li><span>所属产区:</span><var>'+msg.result.s_region+'</var></li><li><span>所属庄园:</span><var>'+msg.result.s_winery+'</var></li><li><span>酒款类型:</span><var>'+msg.result.s_winetype+'</var></li><li><span>葡萄品种:</span><var>'+msg.result.s_grape+'</var></li><li><span>内容:</span><var style="color:#F00;">暂不显示</var></li><li><span>图片:</span><var style="color:#F00;">暂不显示</var></li><li><span>参考来源:</span><var>'+msg.result.s_refweb+'</var></li>';
		html +='</ul></div>';
		$("#"+id+"_more").children("td").html(html);
	});
}