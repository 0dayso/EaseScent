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
function changec(id){
	var fname = $("#data"+id).attr('fname');
	var cname = $("#data"+id).attr('cname');
	var desc = $("#data"+id).attr('desc');
	var img = $("#data"+id).attr('img');
	var regionjson = $("#data"+id).val() ? eval("(" + $("#data"+id).val() + ")") : '';
	var html = '';
	html += '<div class="w_lef"><div class="w_rig"><div class="w_img"><span><b>&nbsp;</b><var><strong>'+cname+'</strong>&nbsp;&nbsp;'+fname+'</var><em>&nbsp;</em></span><img src="'+__upload_domain+'Jiuku/Country/images/'+img+'" alt="'+cname+'('+fname+')" onerror="imgError(this);" /></div></div></div>';
	html += '<div class="w_imgcon"><div class="w_title4">子产区（'+regionjson.length+'个）</div><div class="w_divmod"><ul class="w_ulmod" id="w_ulshow">';
	$.each(regionjson,function(i,n){
		if((i%2) == 0) html += '<li>';
		html += '<div><a href="'+__region_domain+n.id+'.shtml" target="_blank" title="'+n.cname+'('+n.fname+')"><img src="'+__upload_domain+'Jiuku/Region/images/'+n.img+'.80.60" alt="'+n.cname+'('+n.fname+')" onerror="imgError(this);" /><span>'+n.cname+'</span></a></div>';
		if(((i%2) == 1) || ((i+1) == regionjson.length)) html += '</li>';
	});
	html += '</ul></div>';
	if(regionjson.length > 10){
		html += '<div class="w_imgshow" id="w_imgshow">';
		$.each(regionjson,function(i,n){
			if((i%10) == 0){
				html += '<span ';
				if(i == 0) html +='class="w_spspan"';
				html += '>&nbsp;</span>';
			}
		});
		html += '</div>';
	}
	html += '<div class="w_exp"><p>'+desc+'</p><div><a href="'+__region_domain+'c'+id+'.shtml" target="_blank">查看详细</a></div></div></div>';
	$(".w_listm").html(html);
	$("li[ids='tit_li']").removeClass('w_lisp');
	$("#tit_li_"+id).addClass('w_lisp');
	if(obj("w_ulshow")){imgshow(425,obj("w_ulshow"),tag("w_imgshow","span"),"w_spspan");}
	imgError();
}
$(function(){
	//changec(14);
	if(obj("w_ulshow")){imgshow(425,obj("w_ulshow"),tag("w_imgshow","span"),"w_spspan");}
	if(obj("w_li")){imgshow(980,obj("w_li"),tag("w_cli","span"),"w_ssp");}
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
function imgError(this1){
	$(this1).attr({src:__public_domain+'Jiuku/images/region/detail/noimg.jpg',onerror:null});
}