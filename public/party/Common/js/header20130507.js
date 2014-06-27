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
//search select
$(function(){
	var oBox=$('.z_head_searchbox_select'),
		oShowBox=oBox.find('.j_pbox'),
		oUl=oBox.find('.j_ulbox'),
		aLi=oUl.find('li');
	oBox.hover(function(){
		oUl.show();	
	},function(){
		oUl.hide();		
	})
	aLi.click(function(){
		oShowBox.text($(this).text());
		$(".z_head_searchbox_inp").find("input[name='type']").val($(this).find("a").attr('val'));
		oUl.hide();
	})
})