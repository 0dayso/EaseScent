$(function(){
	$("#create_party").click(function(){
	 	$.post("/index.php/Uc/checklogin",
	       function(data){
	       		if(data!="null"){
	       	   		var	userinfo = eval("(" + data + ")");
	       	   		if(userinfo.online==0){
	       	   			art.dialog({                        
				            id:'party_action',    
				            content:"<div>您还没有登录，请先登录!</div>",
				            title:"登录验证",      
				            width:'300px',               
				            height:'200px',          
				            ok:function(){
				            
				            },                        
				            cancel:function(){window.art.dialog({id:'party_action'}).close()}
				            });
	       	   		}else{
	       	   		    if(userinfo.email_valid!=1){
	       	   				art.dialog({                        
				            id:'party_action',    
				            content:"<div>您的邮件还没通过验证，请验证您的邮件！</div>",
				            title:"邮件验证",      
				            width:'300px',               
				            height:'200px',          
				            ok:function(){
				            	var curr = window.location.href;
				            	window.location.href="http://user.wine.cn/member/email?continue="+curr;
				            	//window.location.href="/index.php/Create";
				            },                        
				            cancel:function(){window.art.dialog({id:'party_action'}).close()}
				            });
	       	   			}else{
	       	   				window.location.href="/index.php/Create";
	       	   			}
	       	   		}
	       		}
	       }); 
	});
	$("#map").click(function(){
		if(!$(this).attr("checked")){
			return ;
		}
		art.dialog({                        
            id:'test_debug',    
            iframe:"/index.php/Create/map",
            title:"地图选择",      
            width:'550px',               
            height:'365px'
            /*,          
            ok:function(){
            	var myPartyArea = document.getElementById('atrDialogIframe_test_debug').contentWindow.getAreaFun();
            	$("#mapaddress").val(myPartyArea);
            },                        
            cancel:function(){window.art.dialog({id:'test_debug'}).close()}
            */
            });
	})
	$("input[name=ismoney]").click(function(){
		if($(this).val()==2){
			$("#moneyconf").show();
		}else{
			$("#moneyconf").hide();
		}
	});
	$("#partypic").click(function(){
		art.dialog({                        
            id:'party_action',    
            content:"<form target='myhiddenform' id='myparty_pic' method='post' action='/index.php/Create/partypic' enctype='multipart/form-data'><input type='file' name='mypic'></form>",
            title:"酒会图片",      
            width:'300px',               
            height:'200px',          
            ok:function(){
            	$("#myparty_pic").submit();
            },                        
			cancel:function(){window.art.dialog({id:'party_action'}).close()}
			 });
	})
	$("#mypartypic").click(function(){
		var partyid = $("#party_info").val();
		var content = "<form target='mypicform' id='myparty_test' method='post' action='/index.php/Img/partypic' enctype='multipart/form-data'><input type='file' name='mypic'>图片描述：<input type='text' class='partydescription' name='description'><input type='hidden' value="+partyid+" name='party_id'></form>";
		art.dialog({                        
            id:'myparty',    
            content:content,
            title:"上传酒会照片",      
            width:'300px',               
            height:'200px',          
            ok:function(){
            	$("#myparty_test").submit();
            },                        
			cancel:function(){window.art.dialog({id:'myparty'}).close()}
			 });
	})
	$("#changepic").click(function(){
		$("#partypic").click();
	})
	$("#partyform").submit(function(){
        if($.trim($("#verify_qt").val())==""||$.trim($("#verify_qt").val()).length!=4){
            alert("验证码不正确");
            return false;
        }
		if($.trim($("#title").val())==""){
			alert("请输入标题");
			return false;
		}
/*		var person = $.trim($("#person_sum").val());
		if (!/^[1-9]{1}[0-9]{0,}$/.test(person)) {
			alert("人数上限请输入大于0的正整数");
			return false;
		}
        */
		var party_start = $.trim($("#party_start").val());
		var party_end = $.trim($("#party_end").val());
		if(party_start==""||party_end==""){
			alert("开始时间或者结束时间不能为空!");
			return false;
		}
		var party_start = $('#party_start').val();
		 var party_end = $('#party_end').val();
		 if(!comptime(party_start,party_end)){
			alert('开始时间不能大于结束时间')
			return false;
		 }
		 var address_info = $.trim($("#address_info").val());
		 var province_id = $.trim($("#province_id").val());
		 var city_id = $.trim($("#city_id").val());
		 var area_id = $.trim($("#area_id").val());
	
		 if(!province_id||!city_id||!area_id||address_info==""){
		 	alert("请补全地址!");
		 	return false;
		 }
		 var contactperson = $.trim($("#contactperson").val());
		 if(contactperson==""){
		 	alert("请填写联系人");
		 	return false;
		 }
		 
         if($('input[name=ismoney]:checked').val() == 2){
            var markprice = $.trim($("#markprice").val());
            var lowerprice = $.trim($("#lowerprice").val());
            if(lowerprice > markprice){
                alert('市场价应大于优惠价');
                return false;
         
            }
        }
        var cdCard = $.trim($('#identification_card').val());
        var reg = /(^\d{15}$)|(^\d{17}(\d|X)$)/;
        if(reg.test(cdCard)===false){
            alert('请输入正确的身份证号');
            return false;
        }
/*		 var contactphone = $.trim($("#contactphone").val());
		  if(!/^1[0-9]{10}$/.test(contactphone)){
		 	alert("联系手机格式不正确");
		 	return false;
		 }
*/
		 var introduce = $.trim($("#introduce").val());
		 if(introduce==""){
		 	alert("请输入介绍内容!");
		 	return false;
		 }
		 var sum = 1000;
		 var curr = $("#introduce").val().length;
		 var yu = sum-curr;
		 if(yu<0){
		 	alert("字数已经超出"+Math.abs(yu)+",无法提交!");
		 	return false;
		 }
		return true;
	})
	$("#address_info").one("click",function(){
		$(this).val("");
	})
	$("#introduce").keyup(function(){
		var sum = 1000;
		var curr = $("#introduce").val().length;
		var yu = sum-curr;
		if(yu>0){
			$(this).next("em").html("还可以输入"+yu+"字");
		}else{
			$(this).next("em").html("<font style='color:red;'>已经超出"+Math.abs(yu)+"字</font>");
		}
	})
})

function comptime(beginTime,endTime){
    var beginTimes=beginTime.substring(0,10).split('-');
    var endTimes=endTime.substring(0,10).split('-');
    beginTime=beginTimes[1]+'-'+beginTimes[2]+'-'+beginTimes[0]+' '+beginTime.substring(10,19);
    endTime=endTimes[1]+'-'+endTimes[2]+'-'+endTimes[0]+' '+endTime.substring(10,19);
    if(beginTime > endTime){
	return false;
    }else{
    	return true;
    }
}

function getcity(){        
    var province_id = $("#province_id").val();
    var _this = $("#province_id"); 
    if(province_id==0){ _this.nextAll().remove();return;}
    $.post("/index.php/Create/getcitylist",{province_id:province_id},function(data){
            _this.nextAll().remove();
            _this.after("<em>"+data+"</em>");
    });   
}
function getarea(){
    var city_id = $("#city_id").val();
    var _this = $("#city_id");
    if(city_id==0){_this.nextAll().remove();return;};
    $.post("/index.php/Create/getarealist",{city_id:city_id},function(data){
        _this.nextAll().remove();
        _this.after("<em>"+data+"</em>");
    });
}
function closemap(){
	window.art.dialog({id:'test_debug'}).close();
}
