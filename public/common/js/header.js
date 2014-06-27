function login(type){
	if(type == 1){
		window.location.href = 'http://user.wine.cn/member/login'
	}else{
		window.location.href = 'http://user.wine.cn/member/login'
	}
}
function register(type){
	if(type == 1){
		window.location.href = 'http://user.wine.cn/member/signup?continue='+window.location.href;
	}else{
		window.location.href = 'http://user.wine.cn/member/signup?continue='+window.location.href;
	}
}
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

function setHomepage() {
	var pageURL = window.location.host;
    if (document.all) {
        document.body.style.behavior='url(#default#homepage)';
        document.body.setHomePage(pageURL);
    }
    else {
            try { //IE
                netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
            } catch (e) {
    try{ //Firefox
     var prefs = Components.classes['@mozilla.org/preferences-service;1']
.getService(Components. interfaces.nsIPrefBranch);
     prefs.setCharPref('browser.startup.homepage',pageURL);
    } catch(e) {
                alert( "您的浏览器不支持该操作，请使用浏览器菜单手动设置." );
    }
            }
    }
}
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

