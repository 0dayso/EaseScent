	//banner右侧滚动栏
	$(function(){
		var z_scrollTop=0,
			z_ScrollBox=$('.z_homePage_main_nr_mian1_r_nr_ul'),
			z_Ulh=z_ScrollBox.height(),
			z_scrollTime=setInterval(function(){
				z_scrollTop--;
				if(-z_Ulh==z_scrollTop){
					z_scrollTop=0;
				}
				z_ScrollBox.css({'margin-top':z_scrollTop});
			},30);
		z_ScrollBox.append(z_ScrollBox.children().clone());
		z_ScrollBox.hover(function(e) {
            clearInterval(z_scrollTime);
        },function(){
			z_scrollTime=setInterval(function(){
				z_scrollTop--;
				if(-z_Ulh==z_scrollTop){
					z_scrollTop=0;
				}
				z_ScrollBox.css({'margin-top':z_scrollTop});
			},30);	
		});
	});
	
	//banner轮换
	$(function(){
		var flashPlay=$('.z_homePage_picPlayer'),
			oUl=flashPlay.find('ul'),
			aOl=flashPlay.find('ol li'),
			oUliw=733,
			iNow=0,
			z_moveTime=setInterval(function(){
				iNow++;
				if(iNow==aOl.length){
					iNow=0;	
				}
				moveToFn(iNow);
			},6000);
			
			aOl.hover(function(){
				moveToFn($(this).index())
			});
			flashPlay.hover(function(){
				clearInterval(z_moveTime);
			},function(){
				z_moveTime=setInterval(function(){
					iNow++;
					if(iNow==aOl.length){
						iNow=0;	
					}
					moveToFn(iNow);
				},6000);	
			});
			function moveToFn(i){
				oUl.stop();
				iNow=i;
				aOl.removeClass('active');
				aOl.eq(iNow).addClass('active');
				oUl.animate({'left':-iNow*oUliw},600);
			}
			
	});

