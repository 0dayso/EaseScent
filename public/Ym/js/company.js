$(function(){
	$(".login-div").each(function(){
		$(this).children("input").focus(function(){
			var name = $(this).attr("name");
			switch(name){
				case "nick_name":
					warn($(this),"请输入企业简称");
				break;
				case "company_name":
					warn($(this),"请输入公司名称");
				break;
				case "web_address":
					warn($(this),"请输入企业网址");
				break;
				case "address_info":
					warn($(this),"请输入详细地址");
				break;
				case "company_tel":
					warn($(this),"请输入企业电话");
				break;
				case "contacts":
					warn($(this),"请输入联系人名字");
				break;
			}
		})
		/*****************过滤*******************/
			$(this).children("input").focusout(function(){
			var name = $(this).attr("name");
			var _this = $(this);
			switch(name){
				case "nick_name":   
					$.post("/index.php/Company/check_nickname_out_self",{nickname:$.trim($(this).val()),is_ajax:1},function(data){
				   		 if(data.status==1){
				   		 	success(_this);
				   		 }else{
				   		 	error(_this,data.message);
				   		 }
				  	},"json");
				break;
				case "company_name":
				if($.trim($(this).val())==""){
					error($(this),"公司名称不能为空");
				}else{
                    var len = $.trim($(this).val()).length;
                    if(len<3||len>21){
                        error($(this),"企业名称字数在3-20之间");
                    }else{
					    success($(this));
                    }
                }
				break;
				case "web_address":
				if($.trim($(this).val())==""){
					error($(this),"企业网址不能为空");
				}else{
					if(!IsURL($.trim($(this).val()))){
						error($(this),"企业网址格式不正确");
					}
				}
				success($(this));
				break;
				case "address_info":
				if($.trim($(this).val())==""){
					error($(this),"详细地址不能为空");
				}
				success($(this));
				break;
				case "company_tel":
				if($.trim($(this).val())==""){
					error($(this),"企业电话不能为空");
				}
					success($(this));
				break;
				case "contacts":
				if($.trim($(this).val())==""){
					error($(this),"联系人名字不能为空");
				}
					success($(this));
				break;
			}
		})
	})
	
	$("#company_btn").click(function(){
		var company_name = $.trim($("input[name=company_name]").val());
		var hangye = $("select[name=hangye]").val();
		var xingzhi = $("select[name=xingzhi]").val();
		var guimo =  $("select[name=guimo]").val();
		var web_address = $.trim($("input[name=web_address]").val());
		var introduction = $.trim($("textarea[name=introduction]").val());
		var province = $("#province").val();
		var city = $("#city").val();
		var address_info = $.trim($("input[name=address_info]").val());
		var company_tel = $("input[name=company_tel]").val();
		var contacts = $("input[name=contacts]").val();
		var reg_province = $.trim($("#reg_province").val());
		var reg_city = $.trim($("#reg_city").val());
		var reg_address_info = $.trim($("input[name=reg_address_info]").val());
		var qy_qq = $.trim($("input[name=qq]").val());
		var qy_skype = $.trim($("input[name=skype]").val());
		var nick_name = $.trim($("input[name=nick_name]").val());
		var type = $(this).val();
		if(company_name==""){
			error($("input[name=company_name]"),"企业名称不能为空");
			return false;
		}else{
            var len = company_name.length;
            if(len<3||len>21){
			    error($("input[name=company_name]"),"企业名称字数在3-20之间");
			    return false;
            }
        }

		if(hangye==-1){
			alert("请选择从事行业");
			return false;
		}
		if(xingzhi==-1){
			alert("请选择企业性质");
			return false;
		}
		if(guimo == -1){
			alert("请选择企业规模");
			return false;
		}
		if(web_address==""){
			error($("input[name=web_address]"),"企业网址不能为空");
			return false;
		}else{
			if(!IsURL(web_address)){
				error($("input[name=web_address]"),"企业网址格式不正确");
				return false;
			}
		}
		if(introduction==""){
			alert("企业简介不能为空");
			return false;
		}
		if(province==""||city==""||address_info==""){
			error($("input[name=address_info]"),"企业地址不完整，请完善!");
			return false;
		}
		if(reg_province==""||reg_city==""||reg_address_info==""){
			error($("input[name=reg_address_info]"),"注册地址不完整，请完善!");
			return false;
        }
        if(qy_qq!=""){
            var strP = /^\d+$/;
            if(!strP.test(qy_qq)){
                alert("QQ号码必须为数字");
                return false;
            }
        }
		if(company_tel==""){
			error($("input[name=company_tel]"),"企业电话不能为空");
			return false;
		}
		if(contacts==""){
			error($("input[name=contacts]"),"联系人不能为空");
			return false;
		}
		if(nick_name==""){
			error($("input[name=nick_name]"),"企业简称不能为空");
			return false;
		}
		$.post("/index.php/Company/update",{
			qy_name:company_name,qy_hangye:hangye,qy_xingzhi:xingzhi,qy_guimo:guimo,
			qy_web_address:web_address,qy_introduction:introduction,qy_province:province,
			qy_city:city,qy_address:address_info,qy_contacts:contacts,qy_moblie:company_tel
			,nick_name:nick_name,type:type,reg_province:reg_province,reg_city:reg_city,qy_reg_address:reg_address_info,
            qq:qy_qq,skype:qy_skype
			},function(result){
				if(result.status==1){
					alert(result.message);
					window.location.href=result.jumpUrl;
				}else{
					alert(result.message);
				}
		},"json")
	})
	
	
	
	function warn(obj,msg){
		obj.next().html(msg);
		obj.next().css({color:"#555555"});
	}
	function error(obj,msg){
		obj.next().html(msg);
		obj.next().css({color:"red"});
	}
	function success(obj){
		obj.next().html("");
	}
	function IsURL(str_url){
        var strRegex = "^((https|http|ftp|rtsp|mms)?://)"
        + "?(([0-9a-z_!~*'().&amp;=+$%-]+: )?[0-9a-z_!~*'().&amp;=+$%-]+@)?" //ftp的user@
        + "(([0-9]{1,3}\.){3}[0-9]{1,3}" // IP形式的URL- 199.194.52.184
        + "|" // 允许IP和DOMAIN（域名）
        + "([0-9a-z_!~*'()-]+\.)*" // 域名- www.
        + "([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\." // 二级域名
        + "[a-z]{2,6})" // first level domain- .com or .museum
        + "(:[0-9]{1,4})?" // 端口- :80
        + "((/?)|" // a slash isn't required if there is no file name
        + "(/[0-9a-z_!~*'().;?:@&amp;=+$,%#-]+)+/?)$";
        var re=new RegExp(strRegex);
        //re.test()
        if (re.test(str_url)){
                return true;
        }else{
                return false;
        }
	}


})
