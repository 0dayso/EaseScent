$(function(){
	//hit
	var getUrlAction = (type == 'country') ? 'XH9jfWM5WWNiV3VVeWN4YmRvOX5%2FYg%3D%3D' : 'X3xgfmA6WmBhVHZHcHJ8ens6fXxh';
	$.getJSON(__ajax_domain+"?action="+getUrlAction+"&id="+id+"&callback=?",function(msg){
	});
});
function isLoginAfter(){
	$(document).ready(function(){
		show_comment();		
		get_comment();   
	});
}
function show_comment(){
	$.getJSON(__ajax_domain+"?action=VnVpd2kzTnltaXlvaElveW5dbHUzdW9Qc3t1cg%3D%3D&callback=?",function(msg){
		if(msg.errorCode == 0){
			if(msg.result.online == 1){
				$(".n-login").html('');
				$(".is-login-c-l").find("img").attr("src",__i_domain+"User/Api/User.php?c=User&m=avatar&uid="+msg.result.mid);
				$(".is-login-c-l-t").html(msg.result.nickname);
				$(".is-login").show();
				$(".is-login-c-r").chackTextarea({
					chackNum : 140,
					chackObj:"textarea", //文本域的hook
					chackNumObj :"#prompt",//提示文字的hook
					chackBtn:"#comment_button", //按钮的hook
					disabledClass:"chackTextarea-disabled",//按钮disabled状态下的样式
					errorClass:"chackTextarea-errortxt" //超过限定字符提示文字的样式
				});
				$("#comment_button").click(function(){
					var c_content = $(".is-login-c-r").find("textarea").val();
					if(c_content == ''){
						$(".is-login-c-r").find("#prompt").html('请输入回复内容').css({"color":"#FF3300"});
						return false;
					}
					var getUrlAction = (type == 'country') ? 'a0hUSlQOc0RQVERSVWhgUUgOQk5UT1VTWH5CTkxMRE9V' : 'aklVS1UPckVRVUVTVGlhUEkPUkVHSU9Of0NPTU1FTlQ%3D';
					$.getJSON(__ajax_domain+"?action="+getUrlAction+"&id="+id+"&content="+encodeURI(encodeURI(c_content))+"&callback=?",function(msg1){
						if(msg1.errorCode != 0){
							$("#c_msg>img").attr("src",__public_domain+"Jiuku/images/region/detail/c-msg-f.gif");
							$('#c_msg').show();
							setTimeout(function(){$("#c_msg").hide();},2000);	
						}else if(msg1.result.status != 1){
							$("#c_msg>img").attr("src",__public_domain+"Jiuku/images/region/detail/c-msg-f.gif");
							$('#c_msg').show();
							setTimeout(function(){$("#c_msg").hide();},2000);	
						}else{
							$(".is-login-c-r").find("textarea").val('');
							$("#c_msg>img").attr("src",__public_domain+"Jiuku/images/region/detail/c-msg-s.gif");
							$('#c_msg').show();
							setTimeout(function(){$("#c_msg").hide();},2000);
							get_comment();
						}
					});
				});
			}else{
				$(".is-login").html('');
				$(".n-login").show();
			}
		}
	});
}
//获取评论列表
function get_comment(){
	$("#comment_list").html('<li class="comment-msg">获取评论......</li>');
	var getUrlAction = (type == 'country') ? 'b0xQTlAKd0BUUEBWUWxkVUwKQkBRekZKUEtRV1x6RkpISEBLUQ%3D%3D' : 'bk1RT1ELdkFVUUFXUG1lVE0LQ0FQe1ZBQ01LSntHS0lJQUpQ';
	$.getJSON(__ajax_domain+"?action="+getUrlAction+"&id="+id+"&callback=?",function(msg){
		if(msg.errorCode != 0){
			$("#comment_list").html('<li class="comment-msg">获取失败！<a href="javascript:get_comment();">重试</a></li>');
		}else if(msg.result.status != 1){
			$("#comment_list").html('<li class="comment-msg">获取失败！<a href="javascript:get_comment();">重试</a></li>');
		}else{
			$(".comment-t>span").html('共有'+msg.result.data.count+'条评论');
			if(msg.result.data.count == 0){
				$("#comment_list").html('<li class="comment-msg">还没有评论，赶快抢占沙发~！</li>');
			}else{
				gcommentbox(msg.result.data.data,$("#comment_list"));
			}
		}
	});
}

//构建评论列表HTML
function gcommentbox(data,commentbox){
	var html = '';
	$.each(data,function(){
		html +='<li id="comment_'+this.tid+'">';
			html +='<div class="com-person-face"><a href="'+__i_domain+'User/u/'+this.uid+'" target="_blank"><img src="'+this.avater+'" alt="'+this.nickname+'" /></a></div>';
			html +='<div class="comment-con-r">';
				html +='<p	class="comment-text"><var class="com-name"><a href="'+__i_domain+'User/u/'+this.uid+'" target="_blank">'+this.nickname+'</a>：</var>'+this.content+'</p>';
				html +='<div class="com-contr">';
					html +='<span>';
					html +='<a href="'+__i_domain+'index.php?m=detail&tid='+this.tid+'" target="_blank" class="com-show">回复('+this.replys+')</a></span>';
					html +='<a href="'+__i_domain+'index.php?m=detail&tid='+this.tid+'" target="_blank">'+changeTimeFormat(this.create_time*1000)+'</a><var>来自<a href="'+this.from_url+'" target="_blank">'+this.from+'</a></var>';
				html +='</div>';
			html +='</div>';
			html +='<div class="comment-others"></div>';
		html +='</li>';
	});	
	commentbox.html(html);
}
function jumpr(){
	var jumppageid = 0;
	var id_prefix = '';
	$.each($(".w_select1").find("select[disabled!='disabled']"),function(i,n){
		if($(n).val() != 0){
			jumppageid = $(n).val();
			if($(n).attr('type') == 'c')	id_prefix = 'c';
			return false;
		}
	});
	if(jumppageid == 0){
		window.location.href = __region_domain+'index.shtml';
	}else{
		window.location.href = __region_domain+id_prefix+jumppageid+'.shtml';
	}
}
function getr(id){
	$("#region_1").html('').attr("disabled",true);
	if(id != 0){
		$.getJSON(__ajax_domain+"?action=a0hUSlQOcX5gUUgOYk5UT1VTWEhFckRNREJVc0RGSE5P&id="+id+"&co=all&callback=?",function(msg){
			if(msg.errorCode != 0){
				 $("#region_1").html('<option>获取失败</option>');return false;
			}
			if(msg.result == null){
				$("#region_1").html('<option>无子分类</option>');return false;
			}
			var html = '<option value="0">请选择</option>';
			$.each(msg.result.data,function(i,n){
				html += '<option value="'+n.id+'">'+n.cname+'('+n.fname+')</option>';
			});
			$("#region_1").html(html).attr("disabled",false);return false;
		});
	}
}
function getnr(id,nid){
	$("#region_"+nid).html('').attr("disabled",true);
	if(id != 0){
		$.getJSON(__ajax_domain+"?action=aklVS1UPcH9hUEkPckVHSU9OSURzRUxFQ1RyRUdJT04%3D&id="+id+"&co=all&callback=?",function(msg){
			if(msg.errorCode != 0){
				 $("#region_"+nid).html('<option>获取失败</option>');return false;
			}
			if(msg.result == null){
				$("#region_"+nid).html('<option>无子分类</option>');return false;
			}
			var html = '<option value="0">请选择</option>';
			$.each(msg.result.data,function(i,n){
				html += '<option value="'+n.id+'">'+n.cname+'('+n.fname+')</option>';
			});
			$("#region_"+nid).html(html).attr("disabled",false);return false;
		});
	}
}
$(function(){
	if(obj("oid") && obj("oid2")){imgshow(1000,obj("oid"),tag("oid2","span"),"w_botton1");}
	if(obj("oidul")) {rrr(150,obj("oidul"),tag("oidul","li"),obj("w_clilef"),obj("w_clirig"),1,50,4);}
});
function obj(oid){
	return document.getElementById(oid);
}
function tag(oid,tagName){
	return obj(oid).getElementsByTagName(tagName);
}
function imgshow(length,oid,oid2,classn){
	var f=0;
	var p=0;
	var tt;
	var l=oid2;
	var Tween={
			easeOut: function(t,b,c,d){
            return -c *(t/=d)*(t-2) + b;
        }
	}
	function run(){
		var w=length;
		var t=0;
		var b=p;
		var c=w*f-b;
		var d=20;
		var div=oid;
		function run1(){
			var n= Math.ceil(Tween.easeOut(t,b,c,d));
			div.style.left =-n+"px";
			if(t<d){
				t++;
				tt=setTimeout(run1,10);
			}
			p=n;
		}
		clearTimeout(tt);
		t=0;
		run1();
	}
	function change(){
		for(i=0;i<l.length;i++){
			l[i].className="";
			if(l[i]==this){
			f=i;
			run();
			this.className=classn;
			}
		}
	}
	for(i=0;i<l.length;i++){
		l[i].onmouseover=change;
	}
}
function rrr(imglength,runoid,runlength,o1,o2,i,time,num){
	var ff=0;
	var pp=0;
	var tt;
	var tr=true;
	var Tween={
			Linear: function(t,b,c,d){ return c*t/d + b; }
		}
	function rr(){	
			var w=imglength;
			var t=0;
			var b=pp;
			var c=w*ff-b;
			var d=time;
			var div=runoid;
			function rr1(){
				var n= Math.ceil(Tween.Linear(t,b,c,d));
				div.style.left =-n+"px";
				if(t<d){
					t++;
					tt=setTimeout(rr1,10);
				}
				pp=n;
			}
			clearTimeout(tt);
			t=0;
			rr1();
	}
	function gg(){
		if(tr)
			ff--;
		else
			ff++;
		rr();
	}
	function tru(){
		tr=true;
		if(ff==0) return;
		gg();
	}
	function fal(){
		if(ff*i>=runlength.length-num) return;
		tr=false;
		gg();
	}
	o1.onclick=tru;
	o2.onclick=fal;
}
function imgError(this1){
	$(this1).attr({src:__public_domain+'Jiuku/images/region/detail/noimg.jpg',onerror:null});
}
//时间戳转日期
function changeTimeFormat(time) {
	var date = new Date(time);
	var month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
	var currentDate = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
	var hh = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
	var mm = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
	return date.getFullYear() + "-" + month + "-" + currentDate+" "+hh + ":" + mm;
	//返回格式：yyyy-MM-dd hh:mm
}