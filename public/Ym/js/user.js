$(function(){
	$("#register>div").each(function(){
		$(this).children("input").focus(function(){
			var aname = $(this).attr("name");
			var message = $(this).next().next();
			switch (aname){
				case "username":
					warn(message,"请输入正确的邮箱或手机号码");
					break;
				case "password":
					warn(message,"请输入密码");
					break;
				case "repassword":
					warn(message,"请输入确认密码");
					break;
				/*
                case "nickname":
					warn(message,"建议使用企业品牌名称，如”逸香教育”");
					break;
                */
				case "company_name":
					warn(message,"请输入与公司营业执照一致的公司全称，长度不超过64个字");
					break;
				case "company_tel":
					warn(message,"如：010-58174036");
					//message.html("请输入企业电话");
					break;
				case "verify":
					warn(message.next(),"请输入验证码");
					break;
			}
		})
	$(this).children("input").focusout(function(){
		var aname = $(this).attr("name");
		var message = $(this).next().next();
		var text = $.trim($(this).val());
		switch (aname){
			case "username":
				if(text==""){
					error(message,"手机或邮箱不能为空");
					return ;
				}else{
					$.post("/index.php/User/check_username",{username:text,is_ajax:1},function(data){
						 var data = eval("(" + data + ")");
				   		 if(data.status==10001){
				   		 	error(message,data.message);
				   		 	return false;
				   		 }else if(data.status==10002){
				   		 	error(message,data.message);
				   		 	return false;
				   		 }else if(data.status==1){
				   		 	success(message);
				   		 }
				  	});
				}
				//success(message);
				break;
			case "password":
				if(text==""){
					error(message,"请输入密码");
				}else if(text.length<5 || text.length>21){
					error(message,"密码长度在6～20之间");
				}else{
					success(message);
				}
				break;
			case "repassword":
				if(text==""){
					error(message,"请重新输入密码");
				}else if(text!=$("#password").val()){
					error(message,"两次输入的密码不一致请重新填写");
				}else{
					success(message);
				}
				break;
			/*
             case "nickname":
				if(text==""){
					error(message,"请输入企业简称");
				}else{
					$.post("/index.php/User/check_nickname",{nickname:text,is_ajax:1},function(data){
						 var data = eval("(" + data + ")");
				   		 if(data.status==1){
				   		 	success(message);
				   		 }else{
				   		 	error(message,data.message);
				   		 }
				  	});
				}
				break;
            */
			case "company_name":
				if(text==""){
					error(message,"请输入企业名称");
				}else{
                    if(text.length<3||text.length>20){
                        error(message,"企业名称字数在3-20之间");
                    }else{
                        success(message);
                    }
                }
				break;
			case "company_tel":
				if(text==""){
					error(message,"请输入企业电话");
				}else{
					$.post("/index.php/User/check_company_tel",{company_tel:text,is_ajax:1},function(result){
						 var data = eval("("+result+")");
				   		 if(data.status==10001){
				   		 	error(message,data.message);
				   		 }else{
                            success(message);
                         }
				  	});
				}
				//message.html("请输入企业电话");
				break;
			case "verify":
				if(text==""){
					error(message.next(),"请输入验证码");
				}else{
					$.post("/index.php/User/check_verify",{verify:text,is_ajax:1},function(result){
						 var data = eval("("+result+")");
				   		 if(data.status==1){
				   		 	success(message.next());
				   		 }else{
				   		 	error(message.next(),data.message);
				   		 }
				  	});
				}
				break;
		}		
	})
})
/************注册******************/
$("#reg").click(function(){
	var username = $.trim($("#username").val());
	var password = $("#password").val();
	var repassword = $("#repassword").val();
	//var nickname = $.trim($("#nickname").val());
	var company_name = $.trim($("#company_name").val());
	var company_tel = $.trim($("#company_tel").val());
	var verify = $("#verify").val();
	if(username==""){
		error($("#username").next().next(),"请输入用户名");
		return false;
	}
	if(password==""){
		error($("#password").next().next(),"请输入密码");
		return false;
	}else if(password.length<5||password.length>21){
		error($("#password").next().next(),"密码长度在6～20之间");
		return false;
	}else{
		success($("#password").next().next());
	}
	if(repassword!=password){
		error($("#repassword").next().next(),"两次输入的密码不一致请重新填写");
		return false;
	}else{
		success($("#repassword").next().next());
	}
	/*if(nickname==""){
		error($("#nickname").next().next(),"企业简称不能为空");
		return false;
	}*/
	if(company_name==""){
		error($("#company_name").next().next(),"企业名称不能为空");
		return false;
	}else{
        if(company_name.length<3||company_name.length>20){
            error(message,"企业名称字数在3-20之间");
            return false;
        }else{
		    success($("#company_name").next().next());
        }
	}
	if(company_tel==""){
		error($("#company_tel").next().next(),"企业电话不能为空");
		return false;
	}
	if(verify==""){
		error($("#verify").next().next().next(),"验证码不能为空");
	}
	if(!$("#remb").attr("checked")){
		alert("请接受注册协议!");
		return false;
	}
	
	//$("#register").submit();
	 $.ajax({
         type: "POST",
         async:true, //同步；
         url: "/index.php/User/add",
         data: "username="+username+"&password="+password+"&company_name="+company_name+"&company_tel="+company_tel+"&verify="+verify,
         success: function(msg){
         	var data = eval("("+msg+")");
	   		 if(data.status==1){
	   		 	if(data.url!=""){
	   		 		reg_cleanup();
	   		 		window.location.href = data.url;
	   		 	}
	   		 }else{
	   		 	if(data.dom=="verify"){
	   		 		error($("#"+data.dom).next().next().next(),data.message);
	   		 	}else{
	   		 		error($("#"+data.dom).next().next(),data.message);
	   		 	}
	   		 }
         	}
		  });

});


/***************登录****************/
$("#login>div").each(function(){
		$(this).children("input").focus(function(){
			var aname = $(this).attr("name");
			var message = $(this).next().next();
			switch (aname){
				case "l_username":
					warn(message,"请填写手机或者邮箱");
					break;
				case "l_password":
					warn(message,"请输入密码");
					break;
				case "l_verify":
					warn(message.next(),"请输入验证码");
				break;
			}
		})
	 $(this).children("input").focusout(function(){
		var aname = $(this).attr("name");
		var message = $(this).next().next();
		var text = $.trim($(this).val());
		switch (aname){
			case "l_username":
					if(text==""){
						error(message,"手机或邮箱不能为空");
						return false;
					}else{
						success(message);
					}
					break;
			case "l_password":
					if(text==""){
						error(message,"密码不能为空");
						return false;
					}else{
						success(message);
					}
					break;
			case "l_verify":
					if(text == ""){
						error(message.next(),"验证码不能为空");
						return false;
					}else{
						/*$.post("/index.php/User/check_verify",{verify:text,is_ajax:1},function(result){
							 var data = eval("("+result+")");
					   		 if(data.status==1){
					   		 	success($("#l_verify").next().next().next());
					   		 }else{
					   		 	error($("#l_verify").next().next().next(),data.message);
					   		 	return false;
					   		 }
					  	});*/
						//同步验证
						 $.ajax({
					         type: "POST",
					         async:true, //同步；
					         url: "/index.php/User/check_verify",
					         data: "verify="+text+"&is_ajax=1",
					         success: function(msg){
					         	var data = eval("("+msg+")");
						   		 if(data.status==1){
						   		 	success($("#l_verify").next().next().next());	
						   		 	//s = true;
						   		 }else{
						   		 	error($("#l_verify").next().next().next(),data.message);
						   		 	//s =  false;
						   		 }
					         	}
							  });
					}
				break;
			}
		})
})
$("#login").submit(function(){
	var username = $.trim($("#l_username").val());
	var password = $("#l_password").val();
	var verify   = $("#l_verify").val();
	var remb = $("#remb").val();
	if(username == ""){
		error($("#l_username").next().next(),"手机或邮箱不能为空");
		return false;
	}
	if(password == ""){
		error($("#l_password").next().next(),"密码不能为空");
		return false;
	}
	/*if(verify == ""){
		error($("#l_verify").next().next().next(),"验证码不能为空");
		return false;
	}*/
        var st = $("#remb").attr("checked");
        if(!st)remb=0;
		$.ajax({
	         type: "POST",
	         async:true, //同步；
	         url: "/index.php/User/doLogin",
	         data: "l_verify="+verify+"&l_username="+username+"&remb="+remb+"&l_password="+password+"&is_ajax=1",
	         success: function(msg){
		         	var data = eval("("+msg+")");
			   		 if(data.status==1){	
			   		 	window.location.href=data.jumpUrl;
			   		 }else{
			   		 	$("#l_message").html(data.message);
                        if(data.error_sum>3){
                            $("#login-verify").show();
                        }
                        /*****垃圾测试需求****/
                        $("#l_password").next().hide();
                        $("#l_username").next().hide();
                        /*******end**********/
			   		 	s = false;
			   		 }
	         		}
			  });
	
	return false;
});
	function warn(obj,msg){
		obj.html("<span style='padding-left:7px;'>"+msg+"</span>");
		obj.removeClass();
		obj.prev().hide();
		return ;
	}
	function error(obj,msg){
		obj.html(msg);
		obj.prev().attr("src",site_pic_url+"/Ym/images/i2.jpg");
		obj.addClass("reg-prmt-em");
		obj.prev().show();
		return ;
	}
	function success(obj){
		obj.html("");
		obj.removeClass();
		obj.prev().attr("src",site_pic_url+"/Ym/images/i1.jpg");
		obj.prev().show();
		return ;
	}
	function cleanup(obj){
		obj.val("");
	}
	function reg_cleanup(){
		cleanup($("#username"));
		cleanup($("#password"));
		cleanup($("#repassword"));
		cleanup($("#company_name"));
		cleanup($("#company_tel"));
		cleanup($("#verify"));
	}
})
