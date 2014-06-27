/*****定义全局图片路径******/
/*
var site_pic_url = "http://public.wine.cn.quwine.com";
var site_upload_url ="http://wine.cn.quwine.com/Upload";
*/

var site_pic_url = "http://public.wine.cn";
var site_upload_url ="http://upload.wine.cn";
/*
var site_pic_url = "http://static.account.com";
var site_upload_url ="http://site.account.com/Upload";
*/
$(function(){
$(".nav-dl>dd").each(function(){
    var oldsrc = $(this).find('img').attr('src');
    if($(this).find("a").attr("class")=="nav-spa") return;
    $(this).hover(
        function(){
            var oldsrc1ist = oldsrc.split(".jpg");
            $(this).find('img').attr('src',oldsrc1ist[0]+"_2.jpg");
            $(this).find("a").attr("class","nav-spa");
        },
        function(){
            $(this).find('img').attr('src',oldsrc);
            $(this).find("a").attr("class","");
        }
    )
})
})
