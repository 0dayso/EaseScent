//2013-4-10 zhibao 

setInterval(function(){toMove('l')},7000);

//首页渐变幻灯片
$(function(){
	var aShowLi=$('.j_flash_box > li'),
		iNow=0,
		time=setInterval(function(){
		goIt()
	},4000);
	
	aShowLi.hover(function(){
		clearInterval(time);	
	},function(){
		time=setInterval(function(){
			goIt()
		},4000);	
	});
	
	function goIt(){
		iNow++;
		if(iNow==aShowLi.length){
			iNow=0;
		}
		aShowLi.fadeOut('slow');
		aShowLi.eq(iNow).fadeIn('slow');	
	}
})

//table  事件
$(function(){
	var aBtn=$('.j_hover').find('tbody > tr'),
		aTr=$('.j_tab').find('tbody > tr'),
		aTr2=$('.j_tab2').find('tbody > tr')
		aTr3=$('.j_tab3').find('tbody > tr');
	
	
	
	aBtn.each(function(index, element) {
        if($(this).hasClass('trbor')){
			$(this).find('td').css('border-top','2px solid #FEE58D');
		}
    });
	
	
	$('.j_tab2').css('width',$('.j_tab2').find('th').length*41);
	
	aBtn.hover(function(){
		var iNow=$(this).index();
		aBtn.css({'background':'#a75c3f','color':'#FFF'});
        aTr.eq(iNow).css({'background':'#b8b8b8','color':'#000'});
		aTr2.eq(iNow).css({'background':'#b8b8b8','color':'#000'});
		aTr3.eq(iNow).css({'background':'#b8b8b8','color':'#000'});
	},function(){
		aBtn.css({'background':'#a75c3f','color':'#FFF'});
	});
	
	$('.j_winename').click(function(){
        var that = $(this);
        var url = that.attr('url'),
            id = that.attr('wine-id');
        $("#alert-show").remove();
        $.post(url, {id: id}, function(data){
            var html  = '<div class="alert_showBox" id="alert-show"><p class="alerty_closeBtn"><img src="http://public.wine.cn/btasting/img/alert_hideBtn.gif" /></p>';
                html += '<div class="alert_main clear">';
                html += '<p class="pic"><img width="198" height="198" src="' + data.img + '" /></p>';
                html += '<div class="nr" style="width:330px;">';
                html += '<h3>' + data.cname + '</h3>';
                html += '<h4>' + data.fname + '</h4>';
                html += '<ul>';
                html += '<li>年份：<span>' + data.year + '</span></li>';
                html += '<li>类型：<span>' + data.type + '</span></li>';
                html += '<li>品种：<span>' + data.grape + '</span></li>';
                html += '<li>酒庄：<span>' + data.winery + '</span></li>';
                html += '<li>供应商：<span>' + data.agent + '</span></li>';
                html += '<li>参考价：<span>￥' + data.price + '</span></li>';
                html += '</ul>';
                html += '</div>';
                html += '</div>';
                html += '<p class="alert_botP">' + data.content + '</p></div>';
            var o = $(html);
                o.appendTo($("body")[0]);
                o.css({'top':that.offset().top+22 ,'left':that.offset().left-372}).show();
                aBtn.unbind('hover');
                var iNow=that.parent().parent().index();
                aBtn.css({'background':'#a75c3f','color':'#FFF'});
                aTr.eq(iNow).css({'background':'#b8b8b8','color':'#000'});
                aTr2.eq(iNow).css({'background':'#b8b8b8','color':'#000'});
                aTr3.eq(iNow).css({'background':'#b8b8b8','color':'#000'});

            $('.alerty_closeBtn').bind('click', function(){
                $('#alert-show').remove();
                aBtn.hover(function(){
                    var iNow=$(this).index();
                    aBtn.css({'background':'#a75c3f','color':'#FFF'});
                    aTr.eq(iNow).css({'background':'#b8b8b8','color':'#000'});
                    aTr2.eq(iNow).css({'background':'#b8b8b8','color':'#000'});
                    aTr3.eq(iNow).css({'background':'#b8b8b8','color':'#000'});
                },function(){
                    aBtn.css({'background':'#a75c3f','color':'#FFF'});
                });
            })
            
        }, 'json');
	})
})
