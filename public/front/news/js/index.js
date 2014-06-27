
$(function(){
    var n=1;
    var nn=$(".img_num>span").length;
    var t=setInterval(change,4000);
    function change(){
        if(n>=nn) n=0;
        $(".img_num>span").removeClass("img_num_red");
        $(".img_num>span").eq(n).addClass("img_num_red");
        $(".big_img>a").fadeOut(1000);
        $(".big_img>a").eq(n).fadeIn(1000);
        $(".img_shadow").find("a").text($('.big_img>a').eq(n).attr('title'));
        n++;
    };
    $(".img_num>span").each(function(i){
        $(this).hover(
            function(){
                clearInterval(t);
                $(".big_img>a").stop(true,true);
                if(i==n-1) return;
                n=i;
                change();
        },function(){
            clearInterval(t);
          }
        )
    });
    $(".img_tab").hover(
        function(){
            clearInterval(t);
        },
        function(){
            t=setInterval(change,4000);
        }
    );
    /*-- 酒会讲座切换 --*/
    $(".n-con4-nav>li").each(function(i){
        $(this).mouseover(function(){
            $(".n-con4-nav>li").removeClass("n-con4-nav-sp");
            $(this).addClass("n-con4-nav-sp");
            $(".c-con4-box>div").removeClass("c-con4-show");
            $(".c-con4-box>div").eq(i).addClass("c-con4-show");
        })
    });
    /*-- 专题图片切换 --*/
    var num=$(".n-img-big>li").length;
    $(".n-img-big").width(num*248);
    var n3=0;
    var tt=setInterval(function(){n3++;if(n3>=8) n3=0;img_change(n3);},3800);
    $(".n-img-tab").hover(
        function(){clearInterval(tt)},
        function(){tt=setInterval(function(){n3++;if(n3>=8) n3=0;img_change(n3);},3800);}
    );
    $(".n-img-num>span").each(function(i){
        $(this).mouseover(function(){
            n3=i;
            img_change(n3);
        })
    });
    function img_change(n3){
        $(".n-img-num>span").removeClass("n-img-num-red");
        $(".n-img-num>span").eq(n3).addClass("n-img-num-red");
        $(".n-img-big").stop(true,false);
        $(".n-img-big").animate({marginLeft:-248*n3+"px"},800);
    };
})