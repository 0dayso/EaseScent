/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//取消酒款框关联
$(document).ready(function() {
    $('.z_aWrap_r_showBox_p > i').live('click',function(){
        $('#z_aWrap_r_showBox_Inp').show().val('');
        $('.z_aWrap_r_showBox_p').hide();
        $('.country').show().val('');
        $('#kcountry').hide();
        $('#type').show().val('');
        $('#ktype').hide();
        $('#region').show().val('');
        $('#kregion').hide();
        $('#percent').show().val('');
        $('#kpercent').hide();
        $('#regionList1').show().val('');
        $('#typeList1').show().val('');//原类型框隐藏
        $('#regionList2').show().val('');
        $('#typeList2').show().val('');//原类型框隐藏

        $('#kjwinetext').val("");//酒款文本提交内容清空
        $('#kjcountrytext').val("");//国家文本提交内容清空
        $('#kjregiontext').val("");//产区文本提交内容清空
        $('#ktypet').val("");//类型文本提交内容清空
        $('#kecountrytext').val("");//文本提交内容清空
        $('#ketypetext').val("");
        $('#keregiontext').val("");//产区文本提交内容清空
        $('#kjgrapetext').val("");//品种文本提交内容清空 
    });
    //取消国家框关联
    $('#kcountry > i').live('click',function(){
        $('#kecountrytext').val("");//文本提交内容清空
        $('#kjcountrytext').val("");//文本提交内容清空
         $('#keregiontext').val("");//产区文本提交内容清空
        $('#kjregiontext').val("");//产区文本提交内容清空
        $('.country').show().val('');
        $('#kcountry').hide();
        $('#regionList1').show().val('');
        $('#kregion').hide();
    });
    //取消类型框关联
    $('#ktype > i').live('click',function(){
        $('#ketypetext').val("");
        $('#ktypet').val("");//类型文本提交内容清空
        $('#typeList1').show().val('');
        $('#ktype').hide();

    });
    //取消产区关联
    $('#kregion > i').live('click',function(){
        $('#keregiontext').val("");//产区文本提交内容清空
        $('#kjregiontext').val("");//产区文本提交内容清空
        $('#regionList1').show();        
        $('#kregion').hide();
    });
    //取消品种比例关联
    $('#kpercent > i').live('click',function(){
        $('#kjgrapetext').val("");//品种文本提交内容清空
        $('#percent').show().val('');
        $('#kpercent').hide();
    });

});