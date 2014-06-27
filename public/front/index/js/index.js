// JavaScript Document
$(function(){
    //flashplay
	var n=1;
	var t=setInterval(change,8000);
	var num=$(".img_num>li").length;
	function change(){
		if(n>=num)
			n=0;
		$(".img_num>li").removeClass("img_num_red");
		$(".img_num>li").eq(n).addClass("img_num_red");
		$(".big_img>a").fadeOut(1000);
		$(".big_img>a").eq(n).fadeIn(1000);
		//$(".img_intro").css("left","-345px");
		//$(".img_intro").animate({left:"0px"},1000);
		$(".img_intro").fadeOut('fast');
		$(".img_intro").fadeIn('fast');
		$(".img_intro>p").removeClass("img_intro_show");
		$(".img_intro>p").eq(n).addClass("img_intro_show");
		n++;
	};
	$(".img_num>li").each(function(i){
		$(this).hover(
			function(){
				clearInterval(t);
				$(".big_img>a").stop(true,true);
				$(".img_intro").stop(true,true);
				if(i==n-1) return;
				n=i;
				change();
		},function(){
			clearInterval(t);
		  }
		)
	});
	$(".flashPlay").hover(
		function(){
			clearInterval(t);
		},
		function(){
			t=setInterval(change,8000);
		}
	)
	
	
	//newListTab
	tabFn($('.main_tab_tit'),'main_tab_nr');
	tabFn($('.z_r_tab_tit'),'z_r_tab_nr');
	
	//scrollprice_table
	var scrollprice_tableNum=0,
	    scrollprice_table=$('.scrollprice_table');
	scrollprice_table.append(scrollprice_table.find('tbody>tr').clone());
	setInterval(function(){
		scrollprice_tableNum--;
		if(scrollprice_tableNum<-scrollprice_table.height()/2){
			scrollprice_tableNum=0;
		}
		scrollprice_table.css('top',scrollprice_tableNum);
	},60)
})

$(function(){
	//userweibotab
	$('.winecon_nr').hover(function(){
		clearInterval(userweibotabTime);								
	},function(){
		userweibotabTime=setInterval(function(){
			iNow++;
			if(iNow>aLi.length-1){iNow=0;}
			scrollFnJiuP(iNow);			 
		},7000);
	});
	
	$('.winecon_nrlistbtn').find('li').click(function(){
		clearInterval(userweibotabTime);
		iNow=$(this).index();
		scrollFnJiuP(iNow);
	});
	var iNow=0,
		aLi=$('.winecon_nrlistbtn').find('li');
	var userweibotabTime=setInterval(function(){
		iNow++;
		if(iNow>aLi.length-1){iNow=0;}
		scrollFnJiuP(iNow);			 
	},7000);
	
	function scrollFnJiuP(iNow){
		aLi.removeClass('active');
		aLi.eq(iNow).addClass('active');
		var oW=-300*iNow;
		$('.winecon_nrlist').animate({'left':oW},'fast');
	}		   
})

//tabFn
function tabFn(btnBox,showBoxClass){
	btnBox.find('li').click(function(){
		btnBox.find('li').removeClass('active');
		$(this).addClass('active');
		var a_showBox=$(this).parent().parent().parent().children('.'+showBoxClass+'');
		a_showBox.hide();
		a_showBox.eq($(this).index()).show();
		btnBox.find('.j_tabmore').hide();
		btnBox.find('.j_tabmore').eq($(this).index()).show();
	});
}

/*产区图片滚动*/
$(function(){
	var i=ii=$(".j_winecon_nrbox_tabUl>li").length;
	$(".j_winecon_nrbox_tabUl").width(i*134);
	$(".winecon_nrbox_rbtn").click(function(){
		var marginL = $(".j_winecon_nrbox_tabUl").css("margin-left")=="auto"?0:parseInt($(".j_winecon_nrbox_tabUl").css("margin-left"));
		if(i>5){
			if(i-5>=4){
				if(!$(".j_winecon_nrbox_tabUl").is(":animated")){
					$(".j_winecon_nrbox_tabUl").animate({
						marginLeft:marginL-134*4+"px"
					},"slow",function(){
						i=i-4;
						$(".winecon_nrbox_lbtn").css({cursor:"pointer",background:"url(news/Common/images/index20130312/scrollPicBtn_l.gif) 0 0 no-repeat"});
						if(i<=5){
							$(".winecon_nrbox_rbtn").css({cursor:"auto",background:"url(news/Common/images/index20130312/scrollPicBtn_r_gray.gif) 0 0 no-repeat"});
						}
					})
				}
			}
			else{
				if(!$(".j_winecon_nrbox_tabUl").is(":animated")){
					$(".j_winecon_nrbox_tabUl").animate({
						marginLeft:marginL-134*(i-5)+"px"
					},"slow",function(){
						i=i-(i-5);
						$(".winecon_nrbox_lbtn").css({cursor:"pointer",background:"url(news/Common/images/index20130312/scrollPicBtn_l.gif) 0 0 no-repeat"});
						if(i<=5){
							$(".winecon_nrbox_rbtn").css({cursor:"auto",background:"url(news/Common/images/index20130312/scrollPicBtn_r_gray.gif) 0 0 no-repeat"});;
						}
					})
				}
			}
		}
	});
	$(".winecon_nrbox_lbtn").click(function(){
		var marginL = $(".j_winecon_nrbox_tabUl").css("margin-left")=="auto"?0:parseInt($(".j_winecon_nrbox_tabUl").css("margin-left"));
		if(i<ii){
			if(i<ii-4){
				if(!$(".j_winecon_nrbox_tabUl").is(":animated")){
					$(".j_winecon_nrbox_tabUl").animate({
						marginLeft:marginL+134*4+"px"
					},"slow",function(){
						i=i+4;
						$(".winecon_nrbox_rbtn").css({cursor:"pointer",background:"url(news/Common/images/index20130312/scrollPicBtn_r.gif) 0 0 no-repeat"});
						if(i>=$(".j_winecon_nrbox_tabUl>li").length){
							$(".winecon_nrbox_lbtn").css({cursor:"auto",background:"url(news/Common/images/index20130312/scrollPicBtn_l_gray.gif) 0 0 no-repeat"});	
						}
					})
				}
			}
			else{
				if(!$(".j_winecon_nrbox_tabUl").is(":animated")){
					$(".j_winecon_nrbox_tabUl").animate({
						marginLeft:marginL+134*(ii-i)+"px"
					},"slow",function(){
						i=ii;
						$(".winecon_nrbox_rbtn").css({cursor:"pointer",background:"url(news/Common/images/index20130312/scrollPicBtn_r.gif) 0 0 no-repeat"});
						if(i>=$(".j_winecon_nrbox_tabUl>li").length){
							$(".winecon_nrbox_lbtn").css({cursor:"auto",background:"url(news/Common/images/index20130312/scrollPicBtn_l_gray.gif) 0 0 no-repeat"});;	
						}
					})
				}
			}
		}
	})
})