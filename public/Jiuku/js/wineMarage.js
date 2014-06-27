//提交判断
function checkSubmit(jump_url){
	if($("input[name='jump_url']").length > 0 && jump_url){
		$("input[name='jump_url']").val(jump_url);
	}
	if($("input[name='fname']").length > 0){
		var fname = $("input[name='fname']").val();
		newfname = fname.replace(/[^\x00-\xff]/g, "**");
		if(newfname == '' || newfname.length > 120){
			$("input[name='fname']").focus();
			art.dialog({content: '外文名为空或超出120个字符！',}).time(3000);
			return;
		}
	}
	if($("input[name='cname']").length > 0){
		var cname = $("input[name='cname']").val();
		newcname = cname.replace(/[^\x00-\xff]/g, "**");
		if(newcname.length > 120){
			$("input[name='cname']").focus();
			art.dialog({content: '中文名超出120个字符！',}).time(3000);
			return;
		}
	}
	if($("input[name='aname']").length > 0){
		var aname = $("input[name='aname']").val();
		newaname = aname.replace(/[^\x00-\xff]/g, "**");
		if(newaname.length > 200){
			$("input[name='aname']").focus();
			art.dialog({content: '别名超出200个字符！',}).time(3000);
			return;
		}
	}
	$.post('?app=Jiuku&m=OutAcWine&a=ajaxCheckSubmit',{'id':$("input[name='id']").val(),'fname':$("input[name='fname']").val(),'cname':$("input[name='cname']").val()},function(msg){
		var msg = eval("("+msg+")");
		if(msg.response == 1){
			$("form[name='myform']").submit();
		}else{
			art.dialog({content: msg.msg,}).time(5000);
			return;
		}
	});
}


$(document).ready(function(){
/**
 * 判断外文名是否符合规则
 */
	$("input[name][name='fname']").focus(function(){
		$(this).next("em").attr({style:"color:#979797;"}).text('');
	});
	$("input[name][name='fname']").blur(function(){
		var msg_lable = $(this).next("em");
		msg_lable.attr({style:"color:#979797;"}).text('');
		var text = $(this).val();
		var id = 0;
		if($("input[name][name='id']").length > 0) id = $("input[name][name='id']").val();
		newtext = text.replace(/[^\x00-\xff]/g, "**");
		if(text == '' || newtext.length > 120){
			msg_lable.attr({style:"color:#B10304;"}).text('*不可为空或超出120个字符！');
			return;
		}
		$.post('?app=Jiuku&m=OutAcWine&a=ajaxJudgmentRepeat',{'field':'fname','text':text,'id':id},function(msg){
																											console.log(1);
			if(msg){
				msg_lable.attr({style:"color:#B10304;"}).text('*存在重复数据！ID:'+msg);
				return;
			}
		});
	});
	$("input[name][name='fname']").blur();
/**
 * 判断中文名是否符合规则
 */
	$("input[name][name='cname']").focus(function(){
		$(this).next("em").attr({style:"color:#979797;"}).text('');
	});
	$("input[name][name='cname']").blur(function(){
		var msg_lable = $(this).next("em");
		msg_lable.attr({style:"color:#979797;"}).text('');
		var text = $(this).val();
		if(text == '') return;
		var id = 0;
		if($("input[name][name='id']").length > 0) id = $("input[name][name='id']").val();
		newtext = text.replace(/[^\x00-\xff]/g, "**");
		if(newtext.length > 120){
			msg_lable.attr({style:"color:#B10304;"}).text('*不可超出120个字符！');
			return;
		}
		$.post('?app=Jiuku&m=OutAcWine&a=ajaxJudgmentRepeat',{'field':'cname','text':text,'id':id},function(msg){
			if(msg){
				msg_lable.attr({style:"color:#B10304;"}).text('*存在重复数据！ID:'+msg);
				return;
			}
		});
	});
	$("input[name][name='cname']").blur();
/**
 * 判断别名是否符合规则
 */
	$("input[name][name='aname']").focus(function(){
		$(this).next("em").attr({style:"color:#979797;"}).text('多别名之间用引号“;”隔开');
	});
	$("input[name][name='aname']").blur(function(){
		var msg_lable = $(this).next("em");
		msg_lable.attr({style:"color:#979797;"}).text('');
		var text = $(this).val();
		newtext = text.replace(/[^\x00-\xff]/g, "**");
		if(newtext.length > 200){
			msg_lable.attr({style:"color:#B10304;"}).text('*不可超出200个字符！');
		}
	});
	$("input[name][name='aname']").blur();
});