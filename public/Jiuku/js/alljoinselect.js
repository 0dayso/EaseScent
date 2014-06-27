/**
 * 酒库关联属性select所需JS函数
 */
//产区select
function region_change(k){
	var val = $("#region_list_"+k).val();
	$("#join_region_id").val(0);
	var nextk = (parseInt(k)+1);
	$("#region_list_"+k).nextAll('select').remove();
	if(val > 0){
		$("#join_region_id").val($("#region_list_"+k).val());
		$("#join_region_name").val($("#region_list_"+k).find("option:selected").text());
		if($("#region_list_by_"+nextk).children("option[pid][pid='"+val+"']").length > 0){
			$("#region_list_"+k).after('<select id="region_list_'+nextk+'" onchange="region_change('+nextk+');"><option value="0">请选择所属产区</option></select>');
			var html = $("#region_list_by_"+nextk).html();
			$("#region_list_"+nextk).append(html);
			$("#region_list_"+nextk).children("option[pid][pid!='"+val+"']").remove();
			$("#region_list_"+nextk).val(0);
		}
	}else{
		if(k > 1){
			$("#join_region_id").val($("#region_list_"+(parseInt(k)-1)).val());
			$("#join_region_name").val($("#region_list_"+(parseInt(k)-1)).find("option:selected").text());
		}
	}
}
//产区待选button
function region_btn(){
	var region_id = $('#join_region_id').val();
	var region_name = $('#join_region_name').val();
	if(region_id <= 0){
		return;
	}
	if($("#region_list_box").find("input[name='joinregion_region_id[]'][value="+region_id+"]").length > 0 || $("#region_list_box").find("input[name='upd_joinregion_region_id[]'][value="+region_id+"]").length > 0){
		return;
	}
	html ='<div class="selected_box"><input name="joinregion_region_id[]" type="hidden" value="'+region_id+'" /><div>'+region_name+'</div><a class="del_selected" href="javascript:void(0)" onclick="selected_box_del(this);"></a></div>';
	$("#region_list_box").append(html);
}
//庄园待选button
function winery_btn(){
	var winery_id = $('#winery_list').val();
	var winery_name = $('#winery_list').find("option:selected").text();
	if(winery_id <= 0){
		return;
	}
	if($("#winery_list_box").find("input[name='joinwinery_winery_id[]'][value="+winery_id+"]").length > 0 || $("#winery_list_box").find("input[name='upd_joinwinery_winery_id[]'][value="+winery_id+"]").length > 0){
		return;
	}
	html ='<div class="selected_box"><input name="joinwinery_winery_id[]" type="hidden" value="'+winery_id+'" /><div>'+winery_name+'</div><a class="del_selected" href="javascript:void(0)" onclick="selected_box_del(this);"></a></div>';
	$("#winery_list_box").append(html);
}
//品牌待选button
function brand_btn(){
	var brand_id = $('#brand_list').val();
	var brand_name = $('#brand_list').find("option:selected").text();
	if(brand_id <= 0){
		return;
	}
	if($("#brand_list_box").find("input[name='joinbrand_brand_id[]'][value="+brand_id+"]").length > 0 || $("#brand_list_box").find("input[name='upd_joinbrand_brand_id[]'][value="+brand_id+"]").length > 0){
		return;
	}
	html ='<div class="selected_box"><input name="joinbrand_brand_id[]" type="hidden" value="'+brand_id+'" /><div>'+brand_name+'</div><a class="del_selected" href="javascript:void(0)" onclick="selected_box_del(this);"></a></div>';
	$("#brand_list_box").append(html);
}
//生产商待选button
function mfrs_btn(){
	var mfrs_id = $('#mfrs_list').val();
	var mfrs_name = $('#mfrs_list').find("option:selected").text();
	if(mfrs_id <= 0){
		return;
	}
	if($("#mfrs_list_box").find("input[name='joinmfrs_mfrs_id[]'][value="+mfrs_id+"]").length > 0 || $("#mfrs_list_box").find("input[name='upd_joinmfrs_mfrs_id[]'][value="+mfrs_id+"]").length > 0){
		return;
	}
	html ='<div class="selected_box"><input name="joinmfrs_mfrs_id[]" type="hidden" value="'+mfrs_id+'" /><div>'+mfrs_name+'</div><a class="del_selected" href="javascript:void(0)" onclick="selected_box_del(this);"></a></div>';
	$("#mfrs_list_box").append(html);
}
//葡萄品种颜色select
function grapecolor_change(this1){
	var val = $(this1).val();
	$(this1).next().val(0);
	$(this1).next().html($(this1).next().next().html());
	if(val > 0){
		$(this1).next().children("option[color_id][color_id!='"+val+"']").remove();
	}
}
//葡萄品种待选button
function grape_btn(){
	var grape_id = $('#grape_list').val();
	var grape_name = $('#grape_list').find("option:selected").text();
	var grape_percentage = parseFloat($("#grape_percentage").val());
	if(grape_id <= 0){
		return;
	}
	if($("#grape_list_box").find("input[name='joingrape_grape_id[]'][value="+grape_id+"]").length > 0 || $("#grape_list_box").find("input[name='upd_joingrape_grape_id[]'][value="+grape_id+"]").length > 0){
		return;
	}
	if(isNaN(grape_percentage)){
		html ='<div class="selected_box"><input name="joingrape_grape_id[]" type="hidden" value="'+grape_id+'" /><input name="joingrape_grape_percentage[]" type="hidden" /><div>'+grape_name+'</div><a class="del_selected" href="javascript:void(0)" onclick="selected_box_del(this);"></a></div>';
	}else{
		html ='<div class="selected_box"><input name="joingrape_grape_id[]" type="hidden" value="'+grape_id+'" /><input name="joingrape_grape_percentage[]" type="hidden" value="'+grape_percentage+'" /><div>'+grape_percentage+'%&nbsp;&nbsp;'+grape_name+'</div><a class="del_selected" href="javascript:void(0)" onclick="selected_box_del(this);"></a></div>';
	}
	$("#grape_list_box").append(html);
}
//酒款待选button
function wine_btn(){
	var wine_id = $('#wine_list').val();
	var wine_name = $('#wine_list').find("option:selected").text();
	var wine_year = $('#wine_year_list').val();
	if(wine_id <= 0 || wine_year <= 0){
		return;
	}
	if(($("#wine_list_box").find("input[name='joinwine_wine_id[]'][value="+wine_id+"]").length > 0 && $("#wine_list_box").find("input[name='joinwine_wine_year[]'][value="+wine_year+"]").length > 0) || ($("#wine_list_box").find("input[name='upd_joingwine_wine_id[]'][value="+wine_id+"]").length > 0 && $("#wine_list_box").find("input[name='upd_joingwine_wine_year[]'][value="+wine_year+"]").length > 0)){
		return;
	}
	html ='<div class="selected_box"><div><input name="joinwine_wine_id[]" type="hidden" value="'+wine_id+'" /><input name="joinwine_wine_year[]" type="hidden" value="'+wine_year+'" /><p><var>酒款</var>'+wine_name+' '+wine_year+'年</p><p><var>价格</var><input name="joinwine_wine_price[]" type="text" value="" /><var style="width:56px;">购买网址</var><input name="joinwine_wine_buy_url[]" type="text" value="" style="width:250px;" /></p></div><a class="del_selected" href="javascript:void(0)" onclick="selected_box_del(this);"></a></div>';
	$("#wine_list_box").append(html);
}
//荣誉组select
function honorgroup_change(this1){
	var val = $(this1).val();
	$("input[name='honorgroup_id']").val(val);
	$("#pid").val(0);
	$("#pname").val('');
	$("#honorgroup_list").nextAll('select').remove();
	if(val > 0){
		$("#honorgroup_list").after('<select id="honor_list_1" class="honor_list" onchange="honor_change(1);"><option value="0">请选择所属荣誉</option></select>');
		var html = $("#honor_list_by_1").html();
		$("#honor_list_1").append(html);
		$("#honor_list_1").children("option[honorgroup_id][honorgroup_id!='"+val+"']").remove();
		$("#honor_list_1").val(0);
	}
}
//荣誉select
function honor_change(k){
	var val = $("#honor_list_"+k).val();
	$("#join_honor_id").val(0);
	var nextk = (parseInt(k)+1);
	$("#honor_list_"+k).nextAll('select').remove();
	if(val > 0){
		$("#join_honor_id").val($("#honor_list_"+k).val());
		if($("#honor_list_by_"+nextk).children("option[pid][pid='"+val+"']").length > 0){
			$("#honor_list_"+k).after('<select id="honor_list_'+nextk+'" class="honor_list" onchange="honor_change('+nextk+');"><option value="0">请选择所属荣誉</option></select>');
			var html = $("#honor_list_by_"+nextk).html();
			$("#honor_list_"+nextk).append(html);
			$("#honor_list_"+nextk).children("option[pid][pid!='"+val+"']").remove();
			$("#honor_list_"+nextk).val(0);
		}
	}else{
		if(k > 1){
			$("#join_honor_id").val($("#honor_list_"+(parseInt(k)-1)).val());
		}
	}
}
//荣誉待选button
function honor_btn(){
	var honorgroup_name = $('#honorgroup_list').find("option:selected").text();
	var honor_id = $('#join_honor_id').val();
	var honor_name = '';
	if(honor_id <= 0){
		return;
	}
	if($("#honor_list_box").find("input[name='joinhonor_honor_id[]'][value="+honor_id+"]").length > 0 || $("#honor_list_box").find("input[name='upd_joinhonor_honor_id[]'][value="+honor_id+"]").length > 0){
		return;
	}
	for(var i=1;i<=$(".honor_list").length;i++){
		if($('#honor_list_'+i).val() > 0){
			honor_name += $('#honor_list_'+i).find("option:selected").text()+'&nbsp;';
		}
	}
	html ='<div class="selected_box"><input name="joinhonor_honor_id[]" type="hidden" value="'+honor_id+'" /><div>'+honorgroup_name+':&nbsp;&nbsp;'+honor_name+'</div><a class="del_selected" href="javascript:void(0)" onclick="selected_box_del(this);"></a></div>';
	$("#honor_list_box").append(html);
}
//评价待选button
function eval_btn(){
	var evalparty_id = $('#evalparty_list').val();
	var evalparty_name = $('#evalparty_list').find("option:selected").text();
	var eval_score = $("#eval_score").val();
	if(evalparty_id <= 0 || eval_score == ''){
		return;
	}
	if($("#eval_list_box").find("input[name='eval_evalparty_id[]'][value="+evalparty_id+"]").length > 0 || $("#eval_list_box").find("input[name='upd_eval_evalparty_id[]'][value="+evalparty_id+"]").length > 0){
		return;
	}
	html ='<div class="selected_box"><input name="eval_evalparty_id[]" type="hidden" value="'+evalparty_id+'" /><input name="eval_score[]" type="hidden" value="'+eval_score+'" /><div>'+evalparty_name+':&nbsp;&nbsp;'+eval_score+'</div><a class="del_selected" href="javascript:void(0)" onclick="selected_box_del(this);"></a></div>';
	$("#eval_list_box").append(html);
}


function joinregion_box_del(this1,id){
	var idstr = $(":input[name='del_joinregion_idstr']").val();
	if(idstr == ''){
		$(":input[name='del_joinregion_idstr']").val(id);
	}else{
		$(":input[name='del_joinregion_idstr']").val(idstr+','+id);
	}
	selected_box_del(this1);
}
function joinwinery_box_del(this1,id){
	var idstr = $(":input[name='del_joinwinery_idstr']").val();
	if(idstr == ''){
		$(":input[name='del_joinwinery_idstr']").val(id);
	}else{
		$(":input[name='del_joinwinery_idstr']").val(idstr+','+id);
	}
	selected_box_del(this1);
}
function joinbrand_box_del(this1,id){
	var idstr = $(":input[name='del_joinbrand_idstr']").val();
	if(idstr == ''){
		$(":input[name='del_joinbrand_idstr']").val(id);
	}else{
		$(":input[name='del_joinbrand_idstr']").val(idstr+','+id);
	}
	selected_box_del(this1);
}
function joinmfrs_box_del(this1,id){
	var idstr = $(":input[name='del_joinmfrs_idstr']").val();
	if(idstr == ''){
		$(":input[name='del_joinmfrs_idstr']").val(id);
	}else{
		$(":input[name='del_joinmfrs_idstr']").val(idstr+','+id);
	}
	selected_box_del(this1);
}
function joingrape_box_del(this1,id){
	var idstr = $(":input[name='del_joingrape_idstr']").val();
	if(idstr == ''){
		$(":input[name='del_joingrape_idstr']").val(id);
	}else{
		$(":input[name='del_joingrape_idstr']").val(idstr+','+id);
	}
	selected_box_del(this1);
}
function joinhonor_box_del(this1,id){
	var idstr = $(":input[name='del_joinhonor_idstr']").val();
	if(idstr == ''){
		$(":input[name='del_joinhonor_idstr']").val(id);
	}else{
		$(":input[name='del_joinhonor_idstr']").val(idstr+','+id);
	}
	selected_box_del(this1);
}
function joinwine_box_del(this1,id){
	var idstr = $(":input[name='del_joinwine_idstr']").val();
	if(idstr == ''){
		$(":input[name='del_joinwine_idstr']").val(id);
	}else{
		$(":input[name='del_joinwine_idstr']").val(idstr+','+id);
	}
	selected_box_del(this1);
}
function eval_box_del(this1,id){
	var idstr = $(":input[name='del_eval_idstr']").val();
	if(idstr == ''){
		$(":input[name='del_eval_idstr']").val(id);
	}else{
		$(":input[name='del_eval_idstr']").val(idstr+','+id);
	}
	selected_box_del(this1);
}
function img_box_del(this1,id){
	var idstr = $(":input[name='del_img_idstr']").val();
	if(idstr == ''){
		$(":input[name='del_img_idstr']").val(id);
	}else{
		$(":input[name='del_img_idstr']").val(idstr+','+id);
	}
	selected_box_del(this1);
}
