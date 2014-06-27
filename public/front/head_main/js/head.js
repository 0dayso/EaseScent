//判断是否登录
function isLogin(){
	$.getJSON(__ajax_domain+"?action=XXhxdXIzTnltaXlvaF1sdUlveW4zdW9Qc3t1cg%3D%3D&callback=?",function(msg){
		if(msg.errorCode == 0 && msg.result.online == 1){
			$("input[name='_islogin']").val(1);
			var html = '<a href="'+__user_domain+'/member/home">'+msg.result.nickname+'</a>	<a href="'+__ajax_domain+'?action=ZkNKTkkIdUJWUkJUU2ZXTnJUQlUITVJKV2tIQE5JaFJTZUJBSFVC&page='+window.location.href+'">退出</a>	<a href="'+__user_domain+'member/home">我的逸香通行证</a>	<a href="javascript:setHomepage()">设为首页</a>';
		}else{
			var html = '<a href="javascript:jumplogin();">登录</a> <a href="javascript:jumpregistration();">注册</a> <a href="javascript:setHomepage();">设为首页</a>';
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
//弹出框登录
function popuplogin(){
	$(".z_userLiongeBoxBg").height($(document).height()).show();
	$(".z_userLiongeBoxBg").after('<div id="pop_box" class="jump-box"><div class="lump-title"><var>登录逸香通行证</var><em id="pop_close">关闭</em></div><div class="login-index"><div class="login-index-left"><ul class="login-index-ul"><li><span>邮箱/手机：</span><input type="text" id="pop_user" name="user" value=""><var></var></li><li><span>密码：</span><input type="password" id="pop_pass" name="pass" value=""><var></var></li></ul><a href="'+__user_domain+'member/remember" class="login-index-a" hidefocus="">忘记密码？</a><p><label class="login-label"><input type="checkbox"><var>两周内自动登录</var></label></p><input type="button" class="login-index-but" id="pop_login" name="login" value="立即登录" hidefocus=""></div><div class="login-index-right"><h2>您还没有逸香通行证？</h2><input class="login-index-but" type="button" value="马上注册" onclick="jumpregistration();" hidefocus=""><a href='+__user_domain+'oauth/qq?r=1&continue='+window.location.href+'" class="other-log" hidefocus=""><img src="'+__public_domain+'front/head_main/images/login-j7.jpg"><em>用QQ账号登录</em></a><a href="'+__user_domain+'oauth/sina?r=1&continue='+window.location.href+'" class="other-log" hidefocus=""><img src="'+__public_domain+'front/head_main/images/login-j8.jpg"><em>用新浪微博账号登录</em></a><a href="'+__user_domain+'oauth/douban?r=1&continue='+window.location.href+'" class="other-log" hidefocus=""><img src="'+__public_domain+'front/head_main/images/login-j10.jpg"><em>用豆瓣帐号登录</em></a><a href="'+__user_domain+'oauth/tianya?r=1&continue='+window.location.href+'" class="other-log" hidefocus=""><img src="'+__public_domain+'front/head_main/images/login-j11.jpg"><em>用天涯帐号登录</em></a><a href="'+__user_domain+'oauth/renren?r=1&continue='+window.location.href+'" class="other-log" hidefocus=""><img src="'+__public_domain+'front/head_main/images/login-j9.jpg"><em>用人人帐号登录</em></a></div></div></div>');
	$("#pop_close").click(function(){
		$("#pop_box").remove();
		$(".z_userLiongeBoxBg").hide();
	});
	$("#pop_user").focus(function(){
		$(this).next().html('请输入您的邮箱或手机号');
	}).blur(function(){
		if(!(/^([a-z0-9+_]|-|.)+@(([a-z0-9_]|-)+.)+[a-z]{2,6}$/.test(this.value)) && !(/^(13|15|18)d{9}$/i.test(this.value))){
			$(this).next().html('请输入正确的手机或邮箱地址').css({"color":"#B10304"});
		}else{
			$(this).next().html('')
		}
	});
	$("#pop_pass").focus(function(){
		$(this).next().html('请输入您的密码');
	}).blur(function(){
		$(this).next().html('');
	});
	$("#pop_login").click(function(){
		if($("#pop_user").val() == ''){
			return false;
		}
		if($("#pop_pass").val() == ''){
			return false;
		}
		this.value = "登录中...";
		$.getJSON(__ajax_domain+"?action=XntydnEwTXpuanpsa15vdkpsem0wb3Bvam9TcHh2cQ%3D%3D&username="+$("#pop_user").val()+"&password="+$("#pop_pass").val()+"&callback=?",function(msg){
			if(msg.errorCode == 0){
				if(msg.result.online == 1){
					this.value = '登录成功';
					$("#pop_box").append('<iframe style="display:none" src="'+__user_domain+'api/auth?sid='+msg.result.sid+'" onload="window.location.reload();"></iframe>');
				}else{
					$("#pop_user").next().html('用户名或密码错误').css({"color":"#B10304"});
					this.value = '立即登录';
					return false;
				}
			}else{
				$("#pop_user").next().html('登录异常').css({"color":"#B10304"});
				this.value = '立即登录';
				return false;
			}
		});
	});
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
}//search select
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

