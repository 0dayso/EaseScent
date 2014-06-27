$(function(){
    var n=0;
    var t=setInterval(function(){n++;if(n>=4) n=0;img_change(n);},3400);
    $(".e-img-tab").hover(
        function(){clearInterval(t)},
        function(){t=setInterval(function(){n++;if(n>=4) n=0;img_change(n);},3400);}
    );
    $(".img-tab-small>li").each(function(i){
        $(this).mouseover(function(){
            n=i;
            img_change(n);
        })
    });
    function img_change(n){
        $(".img-tab-small>li").removeClass("e-red");
        $(".img-tab-small>li").eq(n).addClass("e-red");
        $(".img-tab-big").stop(true,false);
        $(".img-tab-big").animate({
            marginTop:-254*n+"px"},400,
            function(){
                $(".img-tab-title>a").eq(0).text($(".img-tab-small>li").eq(n).find("a:first").attr("title"));
                $(".img-tab-title>a").eq(0).attr("href",$(".img-tab-small>li").eq(n).find("a:first").attr("href"));
            }
        )
    };
})