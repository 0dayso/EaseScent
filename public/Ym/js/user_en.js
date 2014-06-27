$(function(){
	$("tr").each(function(){
		$(this).find("input").focus(function(){
			var aname = $(this).attr("name");
			var message = $(this).parent().next().children();
			switch (aname){
				case "username":
					warn(message,"Please enter a valid e-mail address");
					break;
				case "password":
					warn(message,"Please enter a valid password");
					break;
				case "repassword":
					warn(message,"Repeat the password");
					break;
                /*
				case "nickname":
					warn(message,"Please enter a valid Name");
					break;
                */
				case "company_name":
					warn(message,"Please enter the valid company name");
					break;
				case "company_tel":
					warn(message,"Please enter the valid company moblie");
					//message.html("请输入企业电话");
					break;
				case "verify":
					warn(message,"Please enter the captcha");
					break;
			}
		})
	$(this).find("input").focusout(function(){
		var aname = $(this).attr("name");
		var message = $(this).parent().next().children();
		var text = $.trim($(this).val());
		switch (aname){
			case "username":
				if(text==""){
					error(message,"Please enter a valid e-mail address");
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
					error(message,"Please enter a valid password");
				}else if(text.length<5 || text.length>21){
					error(message,"password need 6～20 words");
				}else{
					success(message);
				}
				break;
			case "repassword":
				if(text==""){
					error(message,"Please enter a valid password again");
				}else if(text!=$("#password").val()){
					error(message,"diff with password");
				}else{
					success(message);
				}
				break;
            /*
			case "nickname":
				if(text==""){
					error(message,"Please enter a valid Name");
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
					error(message,"Please enter the valid company name");
				}else{
                    var len = text.length;
                    if(len<3||len>21){
			            error(message,"Full name must 3-21 words");
                    }else{
                        success(message);
                    }
                }
				break;
			case "company_tel":
				if(text==""){
					error(message,"Please enter the valid company moblie");
                    return;
				}else{
					$.post("/index.php/User/check_company_tel",{company_tel:text,is_ajax:1},function(result){
						 var data = eval("("+result+")");
				   		 if(data.status==10001){
				   		 	error(message,data.message);
                            return;
				   		 }
				  	});
				}
                success(message);
				//message.html("请输入企业电话");
				break;
			case "verify":
				if(text==""){
					error(message,"Please enter the captcha");
				}else{
					$.post("/index.php/User/check_verify",{verify:text,is_ajax:1},function(result){
						 var data = eval("("+result+")");
				   		 if(data.status==1){
				   		 	success(message);
				   		 }else{
				   		 	error(message,data.message);
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
		error($("#username").parent().next().children(),"Please enter a valid e-mail address");
		return false;
	}
	if(password==""){
		error($("#password").parent().next().children(),"Please enter a valid password");
		return false;
	}else if(password.length<5||password.length>21){
		error($("#password").parent().next().children(),"password need 6～20 words");
		return false;
	}else{
		success($("#password").parent().next().children());
	}
	if(repassword!=password){
		error($("#repassword").parent().next().children(),"diff with password");
		return false;
	}else{
		success($("#repassword").parent().next().children());
	}
	/*if(nickname==""){
		error($("#nickname").parent().next().children(),"Please enter a valid Name");
		return false;
	}*/
	if(company_name==""){
		error($("#company_name").parent().next().children(),"Please enter the valid company name");
		return false;
	}else{
            var len = company_name.length;
            if(len<3||len>21){
			    error($("#company_name").parent().next().children(),"Full name must 3-21 words");
			    return false;
            }else{
		        success($("#company_name").parent().next().children());
            }
	}
	if(company_tel==""){
		error($("#company_tel").parent().next().children(),"Please enter the valid company moblie");
		return false;
	}
	if(verify==""){
		error($("#verify").parent().next().children(),"Please enter the captcha");
	}
	if(!$("#remb").attr("checked")){
		alert("Please agree to the Registration Agreement!");
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
	   		 		error($("#"+data.dom).parent().next().children(),data.message);
	   		 	}else{
	   		 		error($("#"+data.dom).parent().next().children(),data.message);
	   		 	}
	   		 }
         	}
		  });

});
function l_warn(obj,msg){
    obj.html(msg);
}
function l_error(obj,msg){
    obj.html(msg);
    obj.addClass("tip");
}
function l_success(obj){
    obj.removeClass("tip");
    obj.html("&nbsp;");
}

/***************登录****************/
$("#login>div").each(function(){
		$(this).children("p").children("input").focus(function(){
			var aname = $(this).attr("name");
			var message = $(this).parent().next();
        
			switch (aname){
				case "l_username":
					l_warn(message,"Please enter your username");
					break;
				case "l_password":
					l_warn(message,"Please enter your password");
					break;
				case "l_verify":
					l_warn(message,"Please enter the verify");
				break;
			}
		})
	 $(this).children("p").children("input").focusout(function(){
		var aname = $(this).attr("name");
		var message = $(this).parent().next();
		var text = $(this).val();
		switch (aname){
			case "l_username":
					if(text==""){
						l_error(message,"Please enter your username");
						return false;
					}else{
						l_success(message);
					}
					break;
			case "l_password":
					if(text==""){
						l_error(message,"Please enter your password");
						return false;
					}else{
						l_success(message);
					}
					break;
			case "l_verify":
					if(text == ""){
						l_error(message,"Please enter the verify");
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
						   		 	l_success(message);	
						   		 	//s = true;
						   		 }else{
						   		 	l_error(message,data.message);
						   		 	//s =  false;
						   		 }
					         	}
							  });
					}
				break;
			}
		})
});
$("#s-login").click(function(){
    $("#login").submit();        
})
$("#login").submit(function(){
	var username = $.trim($("#l_username").val());
	var password = $("#l_password").val();
	var verify   = $("#l_verify").val();
	var remb = $("#remb").val();
	if(username == ""){
		l_error($("#l_username").parent().next(),"Please enter your email");
		return false;
	}
	if(password == ""){
		error($("#l_password").parent().next(),"Please enter your password");
		return false;
	}
	/*if(verify == ""){
		error($("#l_verify").parent().next(),"Please enter the verify");
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
			   		 	window.location.href="/index.php";
			   		 }else{
			   		 	alert(data.message);
                        if(data.error_sum>3){
                            $("#login-verify").show();
                        }
			   		 	s = false;
			   		 }
	         		}
			  });
	return false;
});
	function warn(obj,msg){
		obj.html(msg);
		//obj.removeClass();
        obj.attr("class","col959595");
		obj.prev().hide();
		return ;
	}
	function error(obj,msg){
		obj.html(msg);
        obj.attr("class","notice");
        //obj.attr("class","col959595");
		//obj.prev().attr("src",site_pic_url+"/Ym/images/i2.jpg");
		//obj.addClass("reg-prmt-em");
		obj.prev().show();
		return ;
	}
	function success(obj){
		obj.html("");
		obj.removeClass();
		//obj.prev().attr("src",site_pic_url+"/Ym/images/i1.jpg");
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
