//判断是否登录
function isLogin(){
	$.getJSON(__ajax_domain+"?action=XXhxdXIzTnltaXlvaF1sdUlveW4zdW9Qc3t1cg%3D%3D&callback=?",function(msg){
		if(msg.errorCode == 0 && msg.result.online == 1){
			$("input[name='_islogin']").val(1);
			var html = '<a href="'+__user_domain+'/member/home">'+msg.result.nickname+'</a> <var>|</var> <a href="'+__ajax_domain+'?action=ZkNKTkkIdUJWUkJUU2ZXTnJUQlUITVJKV2tIQE5JaFJTZUJBSFVC&page='+window.location.href+'">退出</a> <var>|</var> <a href="'+__user_domain+'member/home">我的逸香通行证</a>';
		}else{
			var html = '<a href="javascript:jumplogin();">登录</a> <var>|</var> <a href="javascript:jumpregistration();">注册</a> <var>|</var> <a href="'+__user_domain+'member/remember" target="_blank">忘记密码</a>';
		}
		$("#loginDiv").html(html);
		if(typeof isLoginAfter == 'function'){
			isLoginAfter();
		}
	});
}
//跳转登录
function jumplogin(){
    var url = __ajax_domain+'?action=ZUBJTUoLdkFVUUFXUGVUTXFXQVYLTlFJVGhLQ01KZkFCS1ZB&page='+window.location.href;
    window.location.href = url;
}
//跳转注册
function jumpregistration(){
    var url = __ajax_domain+'?action=b0xQTlAKd0BUUEBWUXBWQFdkVUwKT1BIVXdAQkxWUWdAQ0pXQA%3D%3D&page='+window.location.href;
    window.location.href = url;
}
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