//导航下来菜单效果JS
function down(obj1,obj2,class1,class2){
	var iTime=null;
	obj1.mouseover(function(){
		clearTimeout(iTime);
		obj2.slideDown();
		obj1.addClass(class1);
	});
	obj1.mouseout(function(){
		iTime=setTimeout(function(){
			obj2.slideUp(function(){
				obj1.removeClass(class1);
			});
			
		}, 300);
	});
	obj2.mouseover(function(){
		clearTimeout(iTime);
		obj1.addClass(class1);
	});
	obj2.mouseout(function(){
		iTime=setTimeout(function(){
			obj2.slideUp();
			obj2.slideUp(function(){
				obj1.removeClass(class1);
			});
		}, 300);
	});
}
$(function(){
		   down($("#exp"),$("#sub_more"),"exp_h");
		   })


function addFavorites()
{
	var url = window.location.href;
	var name = document.title;
    try {
        window.external.addFavorite(url,name);
    }
    catch(e) {
        try {
            window.sidebar.addPanel(name,url,"");
        }
        catch(e) {
            alert("加入收藏失败，请使用Ctrl+D进行添加");
        }
    }
}