//2012-11-28 zhibao
$(document).ready(function(e) {
    //酒款名联想
  
    $('.z_aWrap_r_showBox > a').click(function(){
        var a_text=$(this).html();
        $('#z_aWrap_r_showBox_Inp').hide();
        $('.z_aWrap_r_showBox').hide();
        $('.z_aWrap_r_showBox_p').show().html(a_text+'<i><img src="img/z_aWrapCloseBtn20x20.png" /></i>');
    });

    $('.z_aWrap_r_showBox_p > i').live('click',function(){
        $('#z_aWrap_r_showBox_Inp').show().val('');
        $('.z_aWrap_r_showBox_p').hide();
    });



  
    
    //酒款名联想 End


    //联动select
    $('.country').change(function(){
        var the_val=$(this).val();
        var two_select=$('.country2').attr('opar');
               
        if(the_val==two_select)
        {
            $('.country2').show();
            $('.country2').change(function(){
                var the_val=$(this).val();
                var two_select=$('.country3').attr('opar');
                if(the_val==two_select)
                {
                    $('.country3').show();
                }else{
                    $('.country3').hide();
                }
            });
        }
        else{
            $('.country2').hide();
        }
    });
    
    $('#addRatioBtn').click(function(){
        var i = $("#percent>span").length;

        var p = document.getElementById("percent");
        var addRatioBtn = document.getElementById("addRatioBtnId");
        var newRatio = document.createElement("span");
        newRatio.setAttribute("class", "ratio");
        newRatio.innerHTML = '<input class="inp1" type="text" name="variety[]"/> <input class="inp2" type="text" name="percent[]"/> %';
        p.insertBefore(newRatio, addRatioBtn);
        if(i >= 10){
            $('#addRatioBtn').hide();
        }
    });
});

