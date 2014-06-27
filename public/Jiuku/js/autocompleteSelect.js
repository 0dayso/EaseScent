$(function() {
	$( ".autocomplete_selector" ).each(function(i){
		$(".autocomplete_selector").eq(i).focus(function(){
			$(this).val('');
			$(this).prev("input[type='hidden']").val(0);
			return false;
		});
		$(".autocomplete_selector").eq(i).autocomplete({
			minLength: 1,
			source: "?app=Jiuku&m=OutAcCommon&a=getAutocompleteDataJs&dbtable=" +$( ".autocomplete_selector" ).eq(i).attr('dbtable'),
			focus: function( event, ui) {
				$(this).val( ui.item.value );
				$(this).prev("input[type='hidden']").val( ui.item.id );
				return false;
			},
			select: function( event, ui ) {
				$(this).val( ui.item.value );
				$(this).prev("input[type='hidden']").val( ui.item.id );
				return false;
			}
		});
	});
});
$(function(){
//国家单选搜索
	$(".country_radio_input").autocomplete({
		minLength: 1,
		source: function(request,response){
			$.post('?app=Jiuku&m=OAC_getAjaxData&a=searchCountry',{'kw':request.term,'type':1},function(msg){
				var msg = eval("("+msg+")");
				if(msg.errorCode != 0){msg.result ={};}
				response($.map(msg.result,function(item){
					return{id:item.id,value:item.fname+' ╱ '+item.cname}
				}));
			});
		},
		select: function(event,ui){
			$(".country_radio_input").hide().val('');
			$("#ini_country_id").attr("disabled",true);
			$(".country_radio_input").parent('td').append('<div class="selected_box"><input name="country_id" type="hidden" value="'+ui.item.id+'" /><div>'+ui.item.value+'</div><a class="del_selected" href="javascript:void(0)" onclick="country_radio_input_del(this);"></a></div>');
			country_radio_input_selected_next(ui.item.id);
			return false;
		}
	});
//产区多选搜索
	$(".region_choose_input").autocomplete({
		minLength: 1,
		source: function(request,response){
			$.post('?app=Jiuku&m=OAC_getAjaxData&a=searchRegion',{'kw':request.term,'type':1},function(msg){
				var msg = eval("("+msg+")");
				if(msg.errorCode != 0){msg.result ={};}
				response($.map(msg.result,function(item){
					return{id:item.id,value:item.fname+' ╱ '+item.cname}
				}));
			});
		},
		select: function(event,ui){
			$(".region_choose_input").val('');
			var selector_list_box = $(".region_choose_input").parent("td").children(".selector_list_box");
			if(selector_list_box.find("input[name='joinregion_region_id[]'][value="+ui.item.id+"]").length > 0 || selector_list_box.find("input[name='upd_joinregion_region_id[]'][value="+ui.item.id+"]").length > 0){
				return false;
			}
			var html = '<div class="selected_box"><input name="joinregion_region_id[]" type="hidden" value="'+ui.item.id+'" /><div>'+ui.item.value+'</div><a class="del_selected" href="javascript:void(0)" onclick="region_choose_input_del(this);"></a></div>';
			selector_list_box.append(html);
			return false;
		}
	});
//产区单选搜索
	$(".region_radio_input").autocomplete({
		minLength: 1,
		source: function(request,response){
			$.post('?app=Jiuku&m=OAC_getAjaxData&a=searchRegion',{'kw':request.term,'type':1},function(msg){
				var msg = eval("("+msg+")");
				if(msg.errorCode != 0){msg.result ={};}
				response($.map(msg.result,function(item){
					return{id:item.id,value:item.fname+' ╱ '+item.cname}
				}));
			});
		},
		select: function(event,ui){
			$(".region_radio_input").hide().val('');
			$("#ini_pid").attr("disabled",true);
			$(".region_radio_input").parent().append('<div class="selected_box"><input name="pid" type="hidden" value="'+ui.item.id+'" /><div>'+ui.item.value+'</div><a class="del_selected" href="javascript:void(0)" onclick="region_radio_input_del(this);"></a></div>');
			return false;
		}
	});
//产区跨区单选搜索
	$(".region2_radio_input").autocomplete({
		minLength: 1,
		source: function(request,response){
			$.post('?app=Jiuku&m=OAC_getAjaxData&a=searchRegion',{'kw':request.term,'type':1},function(msg){
				var msg = eval("("+msg+")");
				if(msg.errorCode != 0){msg.result ={};}
				response($.map(msg.result,function(item){
					return{id:item.id,value:item.fname+' ╱ '+item.cname}
				}));
			});
		},
		select: function(event,ui){
			$(".region2_radio_input").hide().val('');
			$("#ini_pid2").attr("disabled",true);
			$(".region2_radio_input").parent().append('<div class="selected_box"><input name="pid2" type="hidden" value="'+ui.item.id+'" /><div>'+ui.item.value+'</div><a class="del_selected" href="javascript:void(0)" onclick="region2_radio_input_del(this);"></a></div>');
			return false;
		}
	});
//庄园筛选搜索
	$("#winery_filter_input").autocomplete({
		source: function(request,response){
			$.getJSON(__ajax_domain__+"?action=aklVS1UPcH9hUEkPd0lORVJZTkFNRXNFTEVDVHdJTkU%3D=&kw="+request.term+"&st=1&callback=?",function(msg){
				if(msg.errorCode != 0 || !msg.result){msg.result ={};}
				response($.map(msg.result,function(item){
					return{id:item.id,value:item.fname+' ╱ '+item.cname}
				}));
			});
		},
		select: function(event,ui){
			$(this).attr('readonly',true).val( ui.item.value );
			$(this).prev("input[type='hidden']").val( ui.item.id );
			$("#winery_filter_input_del").show();
			return false;
		}
	});
//庄园筛选搜索重置
	$("#winery_filter_input_del").click(function(){
		$("#winery_filter_input").attr('readonly',false).val('');
		$("#winery_filter_input").prev("input[type='hidden']").val('');
		$(this).hide();
		return false;
	});
//庄园多选搜索
	$(".winery_choose_input").autocomplete({
		minLength: 1,
		source: function(request,response){
			$.post('?app=Jiuku&m=OAC_getAjaxData&a=searchWinery',{'kw':request.term,'type':1},function(msg){
				var msg = eval("("+msg+")");
				if(msg.errorCode != 0){msg.result ={};}
				response($.map(msg.result,function(item){
					return{id:item.id,value:item.fname+' ╱ '+item.cname}
				}));
			});
		},
		select: function(event,ui){
			$(".winery_choose_input").val('');
			var selector_list_box = $(".winery_choose_input").parent("td").children(".selector_list_box");
			if(selector_list_box.find("input[name='joinwinery_winery_id[]'][value="+ui.item.id+"]").length > 0 || selector_list_box.find("input[name='upd_joinwinery_winery_id[]'][value="+ui.item.id+"]").length > 0){
				return false;
			}
			var html = '<div class="selected_box"><input name="joinwinery_winery_id[]" type="hidden" value="'+ui.item.id+'" /><div>'+ui.item.value+'</div><a class="del_selected" href="javascript:void(0)" onclick="winery_choose_input_del(this);"></a></div>';
			selector_list_box.append(html);
			return false;
		}
	});
//葡萄品种多选搜索
	$(".grape_choose_input").autocomplete({
		minLength: 1,
		source: function(request,response){
			$.post('?app=Jiuku&m=OAC_getAjaxData&a=searchGrape',{'kw':request.term,'type':1},function(msg){
				var msg = eval("("+msg+")");
				if(msg.errorCode != 0){msg.result ={};}
				response($.map(msg.result,function(item){
					return{id:item.id,value:item.fname+' ╱ '+item.cname}
				}));
			});
		},
		select: function(event,ui){
			$(".grape_choose_input").val('');
			var selector_list_box = $(".grape_choose_input").parent("td").children(".selector_list_box");
			var percentage_html = '';
			if($(".grape_choose_input").attr('percentage') == 'true'){
				var percentage_html = '<input name="joingrape_grape_percentage[]" style="width:30px;" />% ';
			}
			if(selector_list_box.find("input[name='joingrape_grape_id[]'][value="+ui.item.id+"]").length > 0 || selector_list_box.find("input[name='upd_joingrape_grape_id[]'][value="+ui.item.id+"]").length > 0){
				return false;
			}
			var html = '<div class="selected_box"><input name="joingrape_grape_id[]" type="hidden" value="'+ui.item.id+'" /><div>'+percentage_html+ui.item.value+'</div><a class="del_selected" href="javascript:void(0)" onclick="grape_choose_input_del(this);"></a></div>';
			selector_list_box.append(html);
			return false;
		}
	});
//酒款多选搜索（代理商）
	$(".wine_choose_input").autocomplete({
		minLength: 1,
		source: function(request,response){
			$.post('?app=Jiuku&m=OAC_getAjaxData&a=searchWine',{'kw':request.term,'type':1},function(msg){
				var msg = eval("("+msg+")");
				if(msg.errorCode != 0){msg.result ={};}
				response($.map(msg.result,function(item){
					return{id:item.id,value:item.fname+' ╱ '+item.cname}
				}));
			});
		},
		select: function(event,ui){
			$(".wine_choose_input").val('');
			var selector_list_box = $(".wine_choose_input").parent("td").children(".selector_list_box");
			if(selector_list_box.find("input[name='wine_id[]'][value="+ui.item.id+"]").length > 0 || selector_list_box.find("input[name='upd_wine_id[]'][value="+ui.item.id+"]").length > 0){
				return false;
			}
			var html = '<div class="selected_box"><input name="wine_id[]" type="hidden" value="'+ui.item.id+'" /><div>'+ui.item.value+'<span style="cursor:pointer;" onclick="wine_choose_input_addyear(this,'+ui.item.id+');" title="增加年份">[+]</span></div><a class="del_selected" href="javascript:void(0)" onclick="wine_choose_input_del(this);"></a></div>';
			selector_list_box.append(html);
			return false;
		}
	});
//荣誉多选搜索
	$(".honor_choose_input").autocomplete({
		minLength: 1,
		source: function(request,response){
			$.post('?app=Jiuku&m=OAC_getAjaxData&a=searchHonor',{'kw':request.term,'type':1},function(msg){
				var msg = eval("("+msg+")");
				if(msg.errorCode != 0){msg.result ={};}
				response($.map(msg.result,function(item){
					return{id:item.id,value:item.fname+' ╱ '+item.cname}
				}));
			});
		},
		select: function(event,ui){
			$(".honor_choose_input").val('');
			var selector_list_box = $(".honor_choose_input").parent("td").children(".selector_list_box");
			if(selector_list_box.find("input[name='joinhonor_honor_id[]'][value="+ui.item.id+"]").length > 0 || selector_list_box.find("input[name='upd_joinhonor_honor_id[]'][value="+ui.item.id+"]").length > 0){
				return false;
			}
			var html = '<div class="selected_box"><input name="joinhonor_honor_id[]" type="hidden" value="'+ui.item.id+'" /><div>'+ui.item.value+'</div><a class="del_selected" href="javascript:void(0)" onclick="honor_choose_input_del(this);"></a></div>';
			selector_list_box.append(html);
			return false;
		}
	});
});
function country_radio_input_del(this1){
	$(this1).parent('div').remove();
	$(".country_radio_input").show();
	$("#ini_country_id").attr("disabled",false);
	country_radio_input_del_next();
}
function country_radio_input_selected_next(country_id){
	if($("input[name='regionlevel_id']").length > 0){
		show_regionlevel(country_id);
	}
}
function country_radio_input_del_next(){
	if($("input[name='regionlevel_id']").length > 0){
		del_regionlevel();
	}
}
function region_choose_input_del(this1,id){
	if(id && $("input[name='del_joinregion_idstr']").length > 0){
		if($("input[name='del_joinregion_idstr']").val() == ''){
			$("input[name='del_joinregion_idstr']").val(id);
		}else{
			$("input[name='del_joinregion_idstr']").val($("input[name='del_joinregion_idstr']").val()+','+id);
		}
	}
	$(this1).parent('div').remove();
}
function region_radio_input_del(this1){
	$(this1).parent('div').remove();
	$(".region_radio_input").show();
	$("#ini_pid").attr("disabled",false);
}
function region2_radio_input_del(this1){
	$(this1).parent('div').remove();
	$(".region2_radio_input").show();
	$("#ini_pid2").attr("disabled",false);
}
function winery_choose_input_del(this1,id){
	if(id && $("input[name='del_joinwinery_idstr']").length > 0){
		if($("input[name='del_joinwinery_idstr']").val() == ''){
			$("input[name='del_joinwinery_idstr']").val(id);
		}else{
			$("input[name='del_joinwinery_idstr']").val($("input[name='del_joinwinery_idstr']").val()+','+id);
		}
	}
	$(this1).parent('div').remove();
}
function grape_choose_input_del(this1,id){
	if(id && $("input[name='del_joingrape_idstr']").length > 0){
		if($("input[name='del_joingrape_idstr']").val() == ''){
			$("input[name='del_joingrape_idstr']").val(id);
		}else{
			$("input[name='del_joingrape_idstr']").val($("input[name='del_joingrape_idstr']").val()+','+id);
		}
	}
	$(this1).parent('div').remove();
}
function wine_choose_input_del(this1,id){
	if(id && $("input[name='del_joinwine_idstr']").length > 0){
		var idstr = '';
		$(this1).parent('.selected_box').find('p').each(function(i){
			if(idstr == ''){
				idstr = $(this1).parent(".selected_box").find('p').eq(i).find("input[name='upd_joinwine_id[]']").val();
			}else{
				idstr = idstr+','+$(this1).parent(".selected_box").find('p').eq(i).find("input[name='upd_joinwine_id[]']").val();
			}
		});
		if($("input[name='del_joinwine_idstr']").val() == ''){
			$("input[name='del_joinwine_idstr']").val(idstr);
		}else{
			$("input[name='del_joinwine_idstr']").val($("input[name='del_joinwine_idstr']").val()+','+idstr);
		}
	}
	$(this1).parent('div').remove();
}
function wine_choose_input_addyear(this1,id){
	var html = '<p><input name="joinwine_wine_id[]" type="hidden" value="'+id+'"> 年份<input name="joinwine_wine_year[]" class="measure-input" type="text" value="" style="width:30px;"> 价格<input name="joinwine_wine_price[]" class="measure-input" type="text" value=""> 购买网址<input name="joinwine_wine_buy_url[]" class="measure-input" type="text" value="" style="width:250px;"> <span style="cursor:pointer;" onclick="wine_choose_input_delyear(this);" title="删除">[-]</span></p>';
	$(this1).parent('div').append(html);
}
function wine_choose_input_delyear(this1,id){
	if(id && $("input[name='del_joinwine_idstr']").length > 0){
		if($("input[name='del_joinwine_idstr']").val() == ''){
			$("input[name='del_joinwine_idstr']").val(id);
		}else{
			$("input[name='del_joinwine_idstr']").val($("input[name='del_joinwine_idstr']").val()+','+id);
		}
	}
	$(this1).parent("p").remove();
}
function honor_choose_input_del(this1,id){
	if(id && $("input[name='del_joinhonor_idstr']").length > 0){
		if($("input[name='del_joinhonor_idstr']").val() == ''){
			$("input[name='del_joinhonor_idstr']").val(id);
		}else{
			$("input[name='del_joinhonor_idstr']").val($("input[name='del_joinhonor_idstr']").val()+','+id);
		}
	}
	$(this1).parent('div').remove();
}

function show_regionlevel(country_id){
	$("#regionlevelList").html('');
	var allchildren = $("#regionlevelList_by").children("input[country_id='"+country_id+"']");
	if(allchildren.length > 0){
		allchildren.each(function(i){
			var regionlevel_id = allchildren.eq(i).val();
			var regionlevel_sname = allchildren.eq(i).attr('sname');
			$("#regionlevelList").append('<div><span id="regionlevel_'+regionlevel_id+'" onclick="select_regionlevel('+regionlevel_id+');">'+regionlevel_sname+'</span></div>');
		});
	}
}
function select_regionlevel(id){
	$(".boxes").find("div").attr({style:""});
	$(".boxes").find("span").attr({style:""});
	if($("input[name='regionlevel_id']").val() == id){
		$("input[name='regionlevel_id']").val(0);
	}else{
		$("input[name='regionlevel_id']").val(id);
		$("#regionlevel_"+id).attr({style:"color:red;"});
		$("#regionlevel_"+id).parentsUntil(".boxes","div").attr({style:"border:1px solid #3CF;"});
	}
}
function del_regionlevel(country_id){
	$("#regionlevelList").html('');
	$("input[name='regionlevel_id']").val(0);
}