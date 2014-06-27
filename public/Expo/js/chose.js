/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//国家框选择其它
$(document).ready(function() {
    $("#countryList").change(function(){
        if($("#countryList").val()=='other'){
            $("#kjcountrytext").val("");
            $("#countryname").val("");
            $("#regionList1").empty();//将产区select框至空
            $("#regionList1").append("<option value='other'>其它</option>");
            $("#regionList2").hide();
            $('#kcountrytext').show().val("");
            $('#kregiontext').show().val("");
            $("#kjregiontext").val("");//将文本框内容清空
            $("#regionname1").val("");
        }
    });
    //国家框选择请选择
    $("#countryList").change(function(){
        if($("#countryList").val()=='chose'){
            $("#kjcountrytext").val("");
            $("#countryname").val("");
            $("#regionList1").empty();//将产区select框至空
            $("#regionList1").append("<option value='chose'>请选择</option>");
            $("#regionList2").hide();
            $("#kjregiontext").val("");//将文本框内容清空
            $("#regionname1").val("");
        }
    });

    //产区框选择其它
    $("#regionList1").change(function(){
        if($("#regionList1").val()=='other'){
            $("#kjregiontext").val("");//将文本框内容清空
            $("#regionname1").val("");
            $("#regionname2").val("");
            $("#regionList2").hide();
            $('#kregiontext').show().val("");
        }
    });

    //类型框选择其它
    $("#typeList1").change(function(){
        if($("#typeList1").val()=='other'){
            $("#ktypet").val("");//将文本框内容清空
            $("#typename1").val("");
            $("#typename2").val("");
            $('#kjtypetext').val("");//将级联类型框空置
            $("#typeList2").hide();
            $('#ktypetext').show().val("");
        }
    });

   

    $('.inp2').blur(function(){ //验证品种 比例是否为数字
        if(!isNaN($(this).val())){
            $(".Grape").hide();
        }else{
            $(".Grape").show();
        }
    });
});

//检验最低购买量
function check(obj){
    var   r   =   /^[0-9]*[1-9][0-9]*$/;　
    if(r.test(obj.value)){
        $(".MinimumBuying").hide();
    }else{
        $(".MinimumBuying").show();
    }
}
//校验酒精度
function check_wine(obj){
    if((obj.value+'').match(/^\d+\.{0,1}\d+$/)|| !isNaN(obj.value)){
        $(".Alcohol").hide();
    }else{
        $(".Alcohol").show();
    }

}

//检验酒款名是否输入
function check_winename(){
    var kjwine = $('#z_aWrap_r_showBox_Inp').val();
    var kwine = $('#kjwinetext').val();
    
    if(kwine || kjwine  ){
        return true;
    }else{
        return false;
    }
}


//检验酒款类型是否输入
function check_winetype(){
    var ketype = $('#ketypetext').val();
    var kjtype = $('#kjtypetext').val();
    var ktype =  $('#ktypetext').val();
    var typename1 = $('#typename1').val();
    if(kjtype || ktype || typename1 || ketype ){
        return true;
    }else{
        return false;
    }
}

//检验酒款产区是否选择
function check_wineregion(){
    var keregion = $('#keregiontext').val();
    var kjregion = $('#kjregiontext').val();
    var kregion =  $('#kregiontext').val();
    var regionname1 = $('#regionname1').val();
    
    if(kjregion || keregion || kregion || regionname1){
        return true;  
    }else{
        return false;
    }
}

function check_editregion(){
    
    var keregion = $('#keregiontext').val();
    var kjregion = $('#kjregiontext').val();
    var kregion =  $('#kregiontext').val();
    var regionname1 = $('#regionname1').val();
    
    if(kjregion || keregion || kregion || regionname1){
        return true;  
    }else{
        return false;
    }
       
}

//检验酒款产区是否输入
function check_input_region(){
    var kregion =  $('#kregiontext').val();
    if($('#regionList1').val()=='other'){
        if(kregion){
            return true;
        }else{
             return false;
        }
    }else{
        return true;
    }   
}


//检验酒款产区是否输入
function check_winecountry(){
    var kecountry = $('#kecountrytext').val();
    var kjcountry = $('#kjcountrytext').val();
    var kcountry=  $('#kcountry').val();
    var countryname = $('#countryname').val();
	var ccname = $.trim($('#kcountrytext').val());
    if(kjcountry || kcountry || countryname || kecountry || ccname){
        return true;
    }else{
        return false;
    }
}

//检验酒款葡萄品种是否输入
function check_winegrape(){
    var kevariety = $('#kegrapetext').val();
    var kjvariety = $('#kjgrapetext').val();
    var kvariety=  $('#variety').val();
    if(kjvariety || kvariety || kevariety){
        return true;
    }else{
        return false;
    }
}

//检验酒款葡萄品种是否输入
function check_wineintroduction(){
    var description = $.trim($('#description').val());
    if(description == ""){
        return false;
    }else{
        return true;
    }
}
//检测价格输入框是否为空
function check_price(){
    value = $("input[name='price']:checked").val();
    if(value == 0){
		var price = $.trim($("#pricetext").val());
		var strP = /^\d+$/;
        if($('#pricetext').val()!=""&&strP.test(price)){
            return true;
        }else{
            return false;
        }
    }else{
        return true;
    }
}
//检测qq
function check_qq(){
    value = $("#qy_qq").val();
    var strP = /^\d+$/;
    if(value!=""&&!strP.test(value)){
        $('#qq_ts').show();
        return false;
    }else{
        $('#qq_ts').hide();
        return true;
    }
    
}
//检测企业名称
function check_qy(){
    var len = $.trim($("#qy_name").val()).length;
    if(len<3||len>21){
        $('#qy_ts').show();
        return false;
    }else{
        $('#qy_ts').hide();
        return true;
    }
    
}




    

