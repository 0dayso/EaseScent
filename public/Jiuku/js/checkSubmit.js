function checkSubmit(table_name,jump_url){
	if(!table_name){
		$("form[name='myform']").submit();
		return;
	}
	if(table_name == 'Ywine'){
		if($("input[name='year']").val() == ''){
			art.dialog({content: '您没有选择任何年份！',}).time(3000);
			return;
		}		
		$.post('?app=Jiuku&m=OutAcCommon&a=checkYwineSubmit',{'wine_id':$("input[name='wine_id']").val(),'year':$("input[name='year']").val()},function(msg){
			var msg = eval("("+msg+")");
			if(msg.errorCode == 0){
				$("form[name='myform']").submit();
				return;
			}else{
				art.dialog({content: msg.errorStr,}).time(5000);
				return;
			}
		});
	}
	if($("input[name='jump_url']").length > 0 && jump_url != undefined){
		$("input[name='jump_url']").val(jump_url);
	}
	if($("input[name='fname']").length > 0){
		var fname = $("input[name='fname']").val();
		newfname = fname.replace(/[^\x00-\xff]/g, "**");
		if(newfname == '' || newfname.length > 80){
			$("input[name='fname']").focus();
			art.dialog({content: '外文名为空或超出80个字符！',}).time(3000);
			return false;
		}
	}
	if($("input[name='cname']").length > 0){
		var cname = $("input[name='cname']").val();
		newcname = cname.replace(/[^\x00-\xff]/g, "**");
		if(newcname.length > 80){
			$("input[name='cname']").focus();
			art.dialog({content: '中文名超出80个字符！',}).time(3000);
			return false;
		}
	}
	if($("input[name='aname']").length > 0){
		var aname = $("input[name='aname']").val();
		newaname = aname.replace(/[^\x00-\xff]/g, "**");
		if(newaname.length > 200){
			$("input[name='aname']").focus();
			art.dialog({content: '别名超出200个字符！',}).time(3000);
			return false;
		}
	}
	if($("input[name='year']").length > 0){
		var year = $("input[name='year']").val();
		if(year == ''){
			art.dialog({content: '您没有选择任何年份！',}).time(3000);
			return false;
		}
	}
	if(table_name == 'Ywine'){
		$.post('?app=Jiuku&m=OutAcCommon&a=checkYwineSubmit',{'table_name':table_name,'wine_id':$("select[name='wine_id']").val(),'year':$("input[name='year']").val()},function(msg){
			var msg = eval("("+msg+")");
			if(msg.response == 1){
				$("form[name='myform']").submit();
			}else{
				art.dialog({content: '年份'+msg.msg.yearstr+'数据已存在！',}).time(5000);
				return false;
			}
		});
	}else if(table_name == 'Agents'){
		$.post('?app=Jiuku&m=OutAcCommon&a=checkSubmit',{'table_name':table_name,'id':$("input[name='id']").val(),'fname':$("input[name='fname']").val(),'cname':$("input[name='cname']").val()},function(msg){
			var msg = eval("("+msg+")");
			if(msg.response == 1){
				var success = true;
				var dittostr =''
				$("input:enabled[name='joinwine_wine_id[]'],input:enabled[name='upd_joinwine_wine_id[]']").each(function(i){
					var wine_id = $("input:enabled[name='joinwine_wine_id[]'],input:enabled[name='upd_joinwine_wine_id[]']").eq(i).val();
					var wine_year = $("input:enabled[name='joinwine_wine_year[]'],input:enabled[name='upd_joinwine_wine_year[]']").eq(i).val();
					if(!/^\d{4}$/.test(wine_year)){
						art.dialog({content: '年份“'+wine_year+'”格式错误！需键入大于1900（包括1900）的4位正整数。',}).time(5000);
						success = false;
						return false;
					}
					var thisdittostr = '|'+wine_id+'-'+wine_year+'|';
					if(dittostr.indexOf(thisdittostr) >= 0){
						art.dialog({content: '年份“'+wine_year+'”重复！',}).time(5000);
						success = false;
						return false;
					}
					dittostr = dittostr + thisdittostr;
				});
				if(success == false){
					return false;
				}
				$("form[name='myform']").submit();
			}else{
				art.dialog({content: msg.msg,}).time(5000);
				return false;
			}
		});
	}else{
		$.post('?app=Jiuku&m=OutAcCommon&a=checkSubmit',{'table_name':table_name,'id':$("input[name='id']").val(),'fname':$("input[name='fname']").val(),'cname':$("input[name='cname']").val()},function(msg){
			var msg = eval("("+msg+")");
			if(msg.response == 1){
				$("form[name='myform']").submit();
			}else{
				art.dialog({content: msg.msg,}).time(5000);
				return false;
			}
		});
	}
}