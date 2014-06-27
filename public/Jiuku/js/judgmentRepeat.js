/**
 * 判断重复
 */
//外文名
if($("input[name][name='fname']").length > 0){
	$("input[name][name='fname']").focus(function(){
		$(this).next("em").attr({style:"color:#979797;"}).text('');
	});
	$("input[name][name='fname']").blur(function(){
		var msg_em = $(this).next("em");
		msg_em.attr({style:"color:#979797;"}).text('');
		var id = 0;
		if($("input[name][name='id']").length > 0) id = $("input[name][name='id']").val();
		var dbtable = $("#judgment_repeat_parameter").attr('dbtable');
		var fieldname = 'fname';
		var text = $(this).val();
		newtext = text.replace(/[^\x00-\xff]/g, "**");
		if(text == '' || newtext.length > 80){
			msg_em.attr({style:"color:#B10304;"}).text('*不可为空或超出80个字符！');
			return;
		}
		$.post('?app=Jiuku&m=OutAcCommon&a=judgmentRepeat',{'dbtable':dbtable,'fieldname':fieldname,'text':text,'id':id},function(msg){
			if(msg){
				msg_em.attr({style:"color:#B10304;"}).text('*存在重复数据！ID:'+msg);
				return;
			}
		});
	});
}
//中文名
if($("input[name][name='cname']").length > 0){
	$("input[name][name='cname']").focus(function(){
		$(this).next("em").attr({style:"color:#979797;"}).text('');
	});
	$("input[name][name='cname']").blur(function(){
		var msg_em = $(this).next("em");
		msg_em.attr({style:"color:#979797;"}).text('');
		var text = $(this).val();
		newtext = text.replace(/[^\x00-\xff]/g, "**");
		if(newtext.length > 80){
			msg_em.attr({style:"color:#B10304;"}).text('*不可超出80个字符！');
		}
	});
}
//别名
if($("input[name][name='aname']").length > 0){
	$("input[name][name='aname']").focus(function(){
		$(this).next("em").attr({style:"color:#979797;"}).text('多别名之间用引号“;”隔开');
	});
	$("input[name][name='aname']").blur(function(){
		var msg_em = $(this).next("em");
		msg_em.attr({style:"color:#979797;"}).text('');
		var text = $(this).val();
		newtext = text.replace(/[^\x00-\xff]/g, "**");
		if(newtext.length > 200){
			msg_em.attr({style:"color:#B10304;"}).text('*不可超出200个字符！');
		}
	});
}