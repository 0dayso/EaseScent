$(function(){
	create_core.clean("title");
	create_core.clean("verify_qt");
	create_core.clean("introduce");
	$("#map").click(function(){
		art.dialog({                        
            id:'test_debug',    
            iframe:"/index.php/Create/map",
            title:"地图选择",      
            width:'auto',               
            height:'auto'
            });
	})
	$("input[name=ismoney]").click(function(){
		if($(this).val()==2){
			$("#moneyconf").show('slow');
		}else{
			$("#moneyconf").hide('slow');
		}
	});
	$("#party_date").change(function(){
		if($(this).val()==2){
			$("#onedate").hide();
			$("#moredate").slideDown();
		}else{
			$("#moredate").hide();
			$("#onedate").slideDown();
		}
	});
	$("#commonaddress").toggle(function(){
		$("#myaddresslist").slideDown();
	},function(){
		$("#myaddresslist").slideUp();
	})
	$("#myaddresslist").children("div").each(function(){
		$(this).children("input").change(function(){
			var id = $(this).attr("item");
			$.post("/index.php/Create/changeAddress",{id:id},function(data){
				var info = eval("("+ data +")");
				$("#address-l").html(info.selectinfo);
				$("#address_info").val(info.addressinfo);
			});
		})
	});

    $('#cancel').click(function(){
            
        history.back(-1);        
            
    });

    $('#dialog_img_show').click(function(){
        $('#dialog_img').show();        
        
    });
    $('.c-up-quit').click(function(){
        $('#dialog_img').hide();        
    });
    $('#dialog_img_close').click(function(){
        $('#dialog_img').hide();        
    });


    /*               */
    var jcrop_api, boundx, boundy;
    function jcrop(){  
      
        $('#target').Jcrop({
            //minSize : [ 345 , 217 ],
            setSelect : [ 0 , 0 , 345 , 217 ],
            //aspectRatio: 158/100, 
            aspectRatio: 158/100, 
            onChange: saveImg,
            onSelect: saveImg
        },function(){
            // Use the API to get the real image size
            var bounds = this.getBounds();
            boundx = bounds[0];
            boundy = bounds[1];
            // Store the API in the jcrop_api variable
            jcrop_api = this;
        });
    }

    function saveImg(c){
        $('#target').data(c); 
        updatePreview(c);
    }
    function updatePreview(c){
        if (parseInt(c.w) > 0){
            var rx = 345 / c.w;
            var ry = 217 / c.h;
            var sx = 158/ c.w;
            var sy = 100 / c.h;

            $('#preview').css({
                width: Math.round(rx * boundx) + 'px',
                height: Math.round(ry * boundy) + 'px',
                marginLeft: '-' + Math.round(rx * c.x) + 'px',
                marginTop: '-' + Math.round(ry * c.y) + 'px'
            });
	        $('#small').css({
                width: Math.round(sx * boundx) + 'px',
                height: Math.round(sy * boundy) + 'px',
                marginLeft: '-' + Math.round(sx * c.x) + 'px',
                marginTop: '-' + Math.round(sy * c.y) + 'px'
            });

        }
    };

	// $("#uploadify").uploadify({
	//     'uploader'  : '/Common/js/uploadify.swf',
	//     'script'    : 'http://party.wine-cn.com/index.php/Create/partypic/sid/'+$("#sid").val(),
	//     'cancelImg' : 'http://public.wine-cn.com/party/Common/images/cancel.png',
	//     'buttonImg' : 'http://public.wine-cn.com/party/Common/images/w_dia_bg1.jpg',
	//     'folder'    : 'http://upload.wine-cn.com/Upload/',
	//     'fileExt'   : '*.jpg;*.gif;*.png;*.jpeg',
	//     'fileDesc'  : 'Image Files',
	    
 //        'simUploadLimit' : 1,
	//     'auto'      :  true,  //true自动上传
 //        'removeCompleted' : true,
	//     'sizeLimit' :  1*1024*1024,   //上传文件大小
	//     'onError'   :  function(event,ID,fileObj,errorObj){
	//     	alert(errorobj);
	//     }, 
	//     'onComplete' : function(event,queueId,fileObj,response,data){
	//       //document.write('sssss');
	//         //上传后操作的方法;
 //            var stat = eval('('+ response +')');
 //            var response = stat.extension;
 //            var savename = stat.savename;
 //            var showpic = stat.show_pic;
	//         if(response==110){
	//         	alert("请先登录!");
	//         	return;
	//         }else{
	// 	     	if(response){
 //                    var uid = $('#uid').val();
 //                    $('#img').val('s_'+savename);
	// 	     		$("#pictype").val('s_'+savename);
 //                    response = showpic;
 //                    var s=$('.c-up-set-img-l').html('<img src='+response+'  id="target" />');
 //                    alert(s);
 //                    $('#target').attr('src',response);
 //                    $('#preview').attr('src',response);
 //                    $('#small').attr('src',response);
 //                    jcrop();
	// 	     		return;
	// 	     	}else{
	// 	     		alert(response);
	// 	     		return;
	// 	     	}
	//         }
	//      },
	    
	// });
/*$("#uploadify").uploadify({
		height        : 30,
		swf           : 'http://public.wine-cn.com/party/Common/js/uploadify.swf',
		// uploader      : 'http://public.wine-cn.com/party/Common/js/uploadify.php',
		width         : 120
	});*/

    // $('#save_img').live('click', function(){
    //     var filename = $('#img').val(); 
    //     var sid = $('#sid').val();
    //     var w =  $('#target').data();
    //     var uid = $('#uid').val();
    //     $.post("/index.php/Create/save/",{
    //         x : w.x,
    //         y : w.y,
    //         width : w.w,
    //         height : w.h,
    //         pic : filename
    //         },function(data){
    //             if(data && data!=1000011){
    //                 $("#showupload").attr("src","http://public.wine-cn.com/party/upload/"+ uid +"/"+ data);
    //             }
    //             $('#dialog_img').hide();
    //     }); 
    // });

	$("#upc").submit(function(){
		if($.trim($("#verify_qt").val())==""||$.trim($("#verify_qt").val()).length!=4){
            //alert("验证码不正确");
			$(".c_ts2").text("验证码不正确");
			$(".c_ts2").css("color","red");
            return false;
        }
		var ismoney=$('input:radio[name="ismoney"]:checked').val();
            if(ismoney==null){
                $(".c_ts5").text("请选择活动费用!");
				$(".c_ts5").css("color","red");
                return false;
			}else{
				if($('input[name=ismoney]:checked').val() == 1){
					$(".c_ts5").text("");
				}
				if($('input[name=ismoney]:checked').val() == 2){
					var markprice = parseInt($.trim($("#markprice").val()));
					var lowerprice = parseInt($.trim($("#lowerprice").val()));
					if(markprice==""){
						$(".c_ts5").text('请填写市场价');
						$(".c_ts5").css("color","red");
						return false;
					}
					else{
						if(lowerprice > markprice){
							$(".c_ts5").text('市场价应大于优惠价');
							$(".c_ts5").css("color","red");
							return false;
						}
						$(".c_ts5").text("");
					}
				}
			}
		 var introduce = $("#introduce").val();
		 if(introduce=='') {
			alert('活动介绍不能为空');
		 	return false;
		 }else if(introduce.length <=20){
		 	alert('活动介绍内容太短');
		 	return false;
		 }
		 var sum = 5000;
		 var curr = introduce.length;
		 var yu = sum-curr;
		 if(yu<0){
		 	alert("字数已经超出"+Math.abs(yu)+",无法提交!");
		 	return false;
		 }
		 //关键词过滤
		 $.post("/index.php/Create/checkIntroduce",{'introduce' : introduce },
          function(data){
               if(data==1){
                  	alert("内容介绍还有非法词，请检查!");
                  	return false;
               }
          });
		 
		 var contactphone = $.trim($("#contactphone").val());
		  if(!/1\d{10}|((0(\d{3}|\d{2}))-)?\d{7,8}(-\d*)?/.test(contactphone)){
		 	//alert("联系手机格式不正确");
			$(".c_ts3").text("联系手机格式不正确");
		 	$(".c_ts3").css("color","red");
		 	return false;
		 }
	/*	 var contactperson = $.trim($("#contactperson").val());
		 if(contactperson==""){
		 	//alert("请填写联系人");
			$(".c_ts4").text("请补联系人!");
		 	$(".c_ts4").css("color","red");
		 	return false;
		 }
        */
		 var address_info = $.trim($("#address_info").val());
		 var province_id = $.trim($("#province_id").val());
		 //if(province_id<3439){
		 var city_id = $.trim($("#city_id").val());
		 var area_id = $.trim($("#area_id").val());
		 if(!province_id||!city_id||!area_id||address_info==""){
		 	alert("请补全地址!");
		 	return false;
		 }
		/*
		 }else if(province_id=3439){
		 	var city_id = $.trim($("#city_id").val());
		 	if(!province_id||!city_id||address_info==""){
			 	alert("请补全地址!");
			 	return false;
			 }
		 }*/
		 var date_type = $("#party_date").val();
		 if(date_type==1){
			var party_start = $("#party_start1_date").val()+" "+$("#party_start1_time").val();
			var party_end = $("#party_start1_date").val()+" "+$("#party_end1_time").val();
			if($.trim(party_start)==""||$.trim(party_end)==""){
				alert("时间填写不全，请完善时间!");
				return false;
			}
			if(!comptime(party_start,party_end)){
				alert('开始时间不能大于结束时间')
				return false;
		 	}
		 }else if(date_type==2){
			var party_start = $("#party_start2_date").val()+" "+$("#party_start2_time").val();
			var party_end = $("#party_start2_date").val()+" "+$("#party_end2_time").val();
			//alert(party_start+"|||"+party_end);
			if(party_start==""||party_end==""){
				alert("时间填写不全，请完善时间!");
				return false;
			}
			if(!comptime(party_start,party_end)){
				alert('开始时间不能大于结束时间')
				return false;
		 	}
		 }
		 if($.trim($("#title").val())==""){
			//alert("请输入标题");
			$(".c_ts").text("请输入标题");
			$(".c_ts").css("color","red");
			return false;
		 }
	});
	$(".c_ra_list").children().click(function(){
		$(".c_ts5").text('');
	});
	$("#title").blur(function(){
		  if($.trim($("#title").val())==""){
				$(this).next(".c_ts").text("请输入标题");
				$(this).next(".c_ts").css("color","red");
			}
			else{
				$(this).next(".c_ts").text("");
			}
	});
	$("#verify_qt").blur(function(){
		  if($.trim($("#verify_qt").val())==""||$.trim($("#verify_qt").val()).length!=4){
            $(".c_ts2").text("验证码不正确");
			$(".c_ts2").css("color","red");
		  }
		  else{
			 $(".c_ts2").text("");
		  }
	});
	$("#contactphone").blur(function(){
		var contactphone = $.trim($("#contactphone").val());
		 if(!/1\d{10}|((0(\d{3}|\d{2}))-)?\d{7,8}(-\d*)?/.test(contactphone)){
		 	$(".c_ts3").text("联系手机格式不正确");
		 	$(".c_ts3").css("color","red");
		 }
		 else{
			$(".c_ts3").text("");
		 }
	});
	/*$("#contactperson").blur(function(){
		 var contactperson = $.trim($("#contactperson").val());
		 if(contactperson==""){
		 	$(".c_ts4").text("请补联系人!");
		 	$(".c_ts4").css("color","red");
		 }
		 else{
			$(".c_ts4").text("");
		 }
	});
	/* var oEditor = FCKeditorAPI.GetInstance('introduce');
	var introduce = oEditor.EditorDocument.body.innerHTML;
	*/
	$("#introduce").keyup(function(){
		var sum = 5000;
		var curr = $(this).val().length;
		var yu = sum-curr;
		if(yu>0){
			$(this).next("em").html("还可以输入"+yu+"字");
		}else{
			$(this).next("em").html("<font style='color:red;'>已经超出"+Math.abs(yu)+"字</font>");
		}
	})
});
var create_core = {
	clean : function(id){
		var cv = $("#"+id).val();
		$("#"+id).click(function(){
			if($(this).val()==cv){
				if(cv.length<10){
					$(this).val("");
				}
			}
		})
		$("#"+id).blur(function(){
			if($(this).val()==""){
				$(this).val(cv);
			}
		})
	}
}
/**
 *
 *  上传酒会封面弹出层
 *
 *  
 *
 */
/**************common******************/
function getcity(){        
    var province_id = $("#province_id").val();
    var _this = $("#province_id"); 
    if(province_id==0){ _this.nextAll().remove();return;}
    $.post("/index.php/Create/getcitylist",{province_id:province_id},function(data){
            _this.nextAll().remove();
            _this.after("<span class='rad'>"+data+"</span>");
    });   
}
function getarea(){
    var city_id = $("#city_id").val();
    var _this = $("#city_id");
    if(city_id==0){_this.nextAll().remove();return;};
    $.post("/index.php/Create/getarealist",{city_id:city_id},function(data){
        _this.nextAll().remove();
        _this.after("<span class='rad'>"+data+"</span>");
    });
}
//close map
function closemap(){
	window.art.dialog({id:'test_debug'}).close();
}
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
