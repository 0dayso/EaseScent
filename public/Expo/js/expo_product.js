

	 //搜索相关产品
	function monitor_ActWineName(){

		 $("#z_aWrap_r_showBox_Inp").keyup(function(e){

		 		userErrorAction();
		  		//打开listshow
				 $('.z_aWrap_r_showBox').show();
				 //处理IE6 下winelist 被挡住
				 if($.browser.msie && $.browser.version < "8.0"){hide_ie6(1);}

	            //获取用户酒名输入 并相关查询救命 展示
			  	    $.post(Jk_Api_getData_Url, {keyword:$("#z_aWrap_r_showBox_Inp").val()},function(data){


			  	    	$("#searchList").empty();
				  	   	//替换数据
						for(var i in data){
							var html = '<a id="'+data[i].id+'" title="'+data[i].fname+'/'+data[i].cname+'" href="javascript:;">'+
									   '<span>'+data[i].fname+'</span> / '+data[i].cname+' </a>';
							$(".z_aWrap_r_showBox").append(html);
						}

						/*获取点击动作*/
						$(".z_aWrap_r_showBox >a").click(function(){

							//替换显示位置
							var text=$(this).html(); 				//获取点击的文本内容
							$('#searchList').hide(); 				//关闭searchlist
							$('#z_aWrap_r_showBox_Inp').hide();     //酒名input表单隐藏停用
							$('#NameReplace').show().html(text+'<i><img src="'+Public+'/Expo/img/z_aWrapCloseBtn20x20.png"/></i>');//替换显示的位置

							//获取关联关系 国家 酒类型 产区...
							var id = $(this).attr("id");
							$.post(Jk_Api_getFullData_Url,{id:id},function(data){

								//替换 z_aWrap_r_showBox_Inp input 表单的值
								var winename = ''+data.id+','+data.fname+','+data.cname+'';
								$('#z_aWrap_r_showBox_Inp').val(winename);


								//酒品类型框关联处理       替换
			                    if (data.winetype){
			                    	$("#wineTypeList").hide();
			                    	$("#wineType").hide();

			                        var winetype = "";
			                        for(var i in data.winetype){
			                            winetype += '<span>'+data.winetype[i].fname+'</span>'+'('+data.winetype[i].cname+')'+'/';
			                        }
			                        //内容填充到类型框
			                        $('#r_winetype').show().html(winetype+'<i><img src="'+Public+'/Expo/img/z_aWrapCloseBtn20x20.png"/></i>');

			                        //内容填充到表单提交
			                        var typetext = "";
			                        for(var i in data.winetype){
			                            typetext += ''+data.winetype[i].id+','+data.winetype[i].fname+','+data.winetype[i].cname+'|';
			                        }
			                        $('#replace_winetype_input').val(typetext);
			                    }



								//替换产品区域  国家替换
								//国家框关联处理
			                    if (data.countryfname || data.countrycname ){
			                    	$("#countryList").hide();
			                        var country = '<span>'+data.countryfname+'</span>'+'('+data.countrycname+')';
			                        //内容填充到国家框
			                        $('#r_country').show().html(country+'<i><img src="'+Public+'/Expo/img/z_aWrapCloseBtn20x20.png"/></i>');
			                        //内容填充到表单提交
			                        var countrtytext = ''+data.countryid+','+data.countryfname+','+data.countrycname+'';
			                        $('#replace_country_input').val(countrtytext);
			                    }



								//替还产区
								//产区框关联处理
			                    if (data.region ){
			                    	$("#regionList").hide();
			                        var region = "";
			                        for(var i in data.region){
			                            region += '<span>'+data.region[i].fname+'</span>'+'('+data.region[i].cname+')'+'/';
			                        }
			                        //内容填充到关联框
			                        $('#r_region').show().html(region+'<i><img src="'+Public+'/Expo/img/z_aWrapCloseBtn20x20.png"/></i>');
			                        //内容填充到表单提交
			                        var regiontext = "";
			                        for(var i in data.region){
			                            regiontext += ''+data.region[i].id+','+data.region[i].fname+','+data.region[i].cname+'|';
			                        }
			                        $('#replace_region_input').val(regiontext);
			                    }


							},'json');


						});


		  	   		 },'json');


				$('#z_aWrap_r_showBox_Inp').focusout(function(){
				    var t = setTimeout(function(){
				    if($.browser.msie && $.browser.version < "8.0"){hide_ie6();} //处理IE6 下winelist 被挡住
					$(".z_aWrap_r_showBox").hide("1000");
				    },'slow');
				});



		  });

	}


	 //获取国家信息
	function getCountryList(){
	     $.post(Jk_Api_getCountry_Url, {},function(data){
	        for(var i in data){
	            var option = $("<option>").text(data[i].cname).val(data[i].id + '|' + data[i].fname+'|'+data[i].cname);
	            $("#countryList").append(option);
	        }
	        $("#countryList").append("<option value='other'>其它</option>");
	    },'json');
	}



	//获取红酒的；类型
	function getWineTypeList (){
	    //获取一级红酒类型列表信息
	    $.post(Jk_Api_getType_Url, {pid:""},function(data){
	        for(var i in data){
	            var option = $("<option>").text(data[i].cname).val(data[i].id + '|' + data[i].fname+'|'+data[i].cname);
	            $("#wineTypeList").append(option);
	        }
	        $("#wineTypeList").append("<option value='other'>其它</option>");
	    },'json');
	}


	/*用户行为分析 处理 函数*/
	//监控 国家选项框
	function monitor_ActCountry(){

	    //获取国家选项框的操作动作 做表单处理
	    $("#countryList").change(function(){


	        if($('#countryList').val()!='other'){
	            //获取选中的文本值赋值到隐藏框中提交
	            var changeOptionVal = $('#countryList').find('option:selected').val();

	            //$('#countryname').val(changeOptionVal);
	            var strindex = changeOptionVal.indexOf("|", 0);			//查找“|”的位置
	            var countryid =changeOptionVal.substring(0,strindex); 	//获取国家的 id
	            $.post(Jk_Api_getRegion_Url, {country_id:countryid},    //更具国家id查询 酒库产区信息
	            function(data){
	       	      if(data){
	       	      	   $("#regionList option").remove();
	       	      	   $("#regionList").append('<option value="">请选择</option>');
			                for(var i in data){
			                    if(data[i].cname || data[i].fname){
			                         var option = $("<option>").text(data[i].fname+' '+data[i].cname).val(data[i].id + '|' + data[i].fname+'|'+data[i].cname);
			                         $("#regionList").append(option);
			                    }
			                }
		                $("#regionList").append("<option value='other'>其它</option>");
	            	}else{
	            		$("#regionList option").remove();
	            		$("#regionList").append('<option value="">请选择</option>');
	            		$("#regionList").append("<option value='other'>其它</option>");
	            	}

	            },'json');
	        }else{
	        	    //这里是删除 操作完地区 之后又选国家的 其他other 的变相业务
	        	    if($("#regionListTd_input")){
	        	    	$("#r_region").remove();
	        			$("#regionListTd_input").remove();
						$("#regionListTd  i").remove();
						$("#replace_region_input").val("");
					}

	        	    //当用户点击 other 的时候删除国家 和 产区 加载新的 国家和产区的input 输入框
	        	  	$("#countryList").hide();
	        	  	$("#regionList").hide();
	        	  	$("#countryListTd").append('<input name="countryList_input" id="countryList_input"/><i><img src="'+Public+'/Expo/img/z_aWrapCloseBtn20x20.png"/></i>');
	        	  	$("#regionListTd").append('<input name="regionList_input" id="regionListTd_input"/>');

	        	  	// 国家 input 切换 select
					$('#countryListTd > i').live('click',function(){

						$("#countryList").show();			//显示国家 的select 表单
	        	  		$("#countryList_input").remove();	//删除国家 的input 表单
	        	  	    $("#countryListTd  i").remove();    //删除图片

	        	  	    $("#regionList").show();			//显示 地区的select 表单
	        	  		$("#regionListTd_input").remove();  //删除地区 的input 表单

	        	  		$("#countryList").find("option[value='']").attr("selected",true); // 被选中第一个选项


					});
	        }
	    });
	}


	//监控 地区选项框
	function monitor_ActRegion(){
		//获取地区的操作的动作为other 并做处理
		$("#regionList").change(function(){
			if($('#regionList').val()=='other'){
				$("#regionList").hide();
				$("#regionListTd").append('<input name="regionListTd_input" id="regionListTd_input"/><i><img src="'+Public+'/Expo/img/z_aWrapCloseBtn20x20.png"/></i>');
			}
		});
	}


	//监控 地区input 表单
	function monitor_ActRegionInput(){
		//当用户不启动地区的 input 表单 转会 select 处理
		$('#regionListTd > i').live('click',function(){
			 $("#regionList").show();
			 $("#regionListTd  i").remove();
			 $("#regionListTd_input").remove();
			 $("#regionList").find("option[value='']").attr("selected",true);
		});
	}

	//监控 红酒类型select表单
	function monitor_ActWineType(){

	    //当被选择后 获取二级红酒类型列表信息
	    $("#wineTypeList").change(function(){

	        if($('#wineTypeList').val()!='other'&&$('#wineTypeList').val()!=''){
	            //获取选中的文本值赋值到隐藏框中提交
	            var typeValue = $('#wineTypeList').find('option:selected').val();


	            var strIndex = typeValue.indexOf("|", 0);
	    		var typeId 	 = typeValue.substring(0,strIndex);

	            $.post(Jk_Api_getType_Url, {pid:typeId},function(data){
	                $("#wineType").children().remove();
	                $("#wineType").show();
	                $("#wineType").append("<option>请选择</option>");
	                for(var i in data){
	                    option =$("<option>").text(data[i].cname).val(data[i].id + '|' + data[i].fname+'|'+data[i].cname);
	                    $("#wineType").append(option);
	                }
	            },'json');
	        }
	    });


		//当用户选择其他类型酒（other）对应的操作
		$("#wineTypeList").change(function(){
			if($('#wineTypeList').val()=='other'){
				$("#wineTypeList").hide();
				$("#wineType").hide();
				$("#wineTypeListTd").append('<input name="wineTypeList_input" id="wineTypeListTd_input"/><i><img src="'+Public+'/Expo/img/z_aWrapCloseBtn20x20.png"/></i>');
			}
		});

	}

	//监控 红酒类型input表单
	function monitor_ActWinTpyeInput(){
		//当用户取消 酒类型 文本框输入时
		$('#wineTypeListTd > i').live('click',function(){
					 $("#wineTypeList").show();
					 $("#wineTypeListTd  i").remove();
					 $("#wineTypeListTd_input").remove();
					 $("#wineTypeList").find("option[value='']").attr("selected",true);
		});
	}

   //监控  酒名 酒类型 国家 地区 的 取消按钮
   function monitor_WineNameCancel(){

	    $('.z_aWrap_r_showBox_p > i').live('click',function(){
	        $('#z_aWrap_r_showBox_Inp').show().val('');
	        $('.z_aWrap_r_showBox_p').hide();

	        //删除类型
	    	$('#r_winetype').hide();
	    	$('#replace_winetype_input').val('');
	        $('#wineTypeList').show()

	        //删除国家
	        $('#r_country').hide();
	        $('#countryList').show()
	        $('#replace_country_input').val('');

	        //删除地区
	        $('#r_region').hide();
	        $('#regionList').show()
	        $('#replace_region_input').val('');

	        userErrorAction();


	    });

	    //单个触发删除 wineType 类型
	    $('#r_winetype > i').live('click',function(){
	    	$('#r_winetype').hide();
	    	$('#replace_winetype_input').val('');

	        $('#wineTypeList').show()

	    });

   		//单个触发删除 国家
	  	$('#r_country > i').live('click',function(){
	    	$('#r_country').hide();
	    	$('#replace_country_input').val('');

	        $('#countryList').show()

	    });

		//单个触发删除 地区
	    $('#r_region > i').live('click',function(){
	    	$('#r_region').hide();
	    	$('#replace_region_input').val('');
	        $('#regionList').show()

	    });

   }


	//监控 国家-其他-后图片关闭
	function monitor_imgColse(){
		$('#countryListTd > i').live('click',function(){
			$("#countryList").show();			//显示国家 的select 表单
	  		$("#countryList_input").remove();	//删除国家 的input 表单
	  	    $("#countryListTd  i").remove();    //删除图片
	  	    $("#regionList").show();			//显示 地区的select 表单
	  		$("#regionListTd_input").remove();  //删除地区 的input 表单
	  		$("#regionListTd i").remove()  //删除图片
	  		$("#countryList").find("option[value='']").attr("selected",true); // 被选中第一个选
		});
	}





   //处理用户 多维操作 逆向操作 存在界面的问题
   function userErrorAction(){
   		$("#countryList").show();
   		$("#regionList").show();
		$("#wineTypeListTd_input").hide();
		$("#countryList_input").hide();
		$("#regionListTd_input").hide();
		$("#regionListTd_input").remove();
		$("#countryList_input").remove();
		$("#countryListTd  i").remove();
		$("#wineTypeListTd  i").remove();
		$("#regionListTd i").remove();
   }




	// 发布图片上传
	init_uploadify = function(){
		$(function(){
		    $('#file_uploadify').uploadify({
		        'swf'             : Public+'/Expo/uploadify/uploadify.swf',
		        'uploader'        : Public+'/Expo/uploadify/uploadify.php?type=wine',
		        'buttonImage'     : Public+'/Expo/uploadify/button-upload.gif',
		        'height'          : 110,
		        'width'           : 110,
		        'auto'             : true,
		        'buttonCursor'    : 'hand',
		        'fileTypeDesc' : 'Image Files',
		        'fileTypeExts' : '*.gif; *.jpg; *.png',
		        'removeCompleted' : true,
		        'fileSizeLimit' : '2048KB',
		        'onUploadError'       : function (file, errorCode, errorMsg, errorString){
		             alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
		        },
		        'onUploadSuccess' : function(file, data, response){
			        data = eval("("+ data +")");

			        if (data.error === 0) {
				        /**
				         * @brief  计算图片长宽比例
				         */
			            var img_width = '';
			            var img_height = '';
			            if (data.width > data.height){
			                var img_size_html = 'width="113px;"';
			            } else {
			                var img_size_html = 'height="110px;"';			            }

				        if(cur_img_num < 4){
				        	cur_img_num++;
				            var html ='<li><input name="img_file[]" type="hidden" value="'+data.filename+'" />'
				                    +'<input name="img_queue[]" class="img_queue"type="hidden" value="'+cur_img_num+'" />'
				                    +'<img src="'+data.url+'" '+img_size_html+' /> '
				                    +'<p class="pic_closeBtn"><img src="'+Public+'/Expo/img/pic_closeBtn.gif" onclick="delImg(-1,this)" /></p>'
				                    +'<a href="#" onclick="first(this)"></a></li>';

				            $("#file_uploadify").parent().before(html);
				            if (cur_img_num >3) {
				                $('#file_uploadify').uploadify('stop');
				                $('#file_uploadify').uploadify('destroy');
				                $(".upLoadPicUl li").last().html('<a href="javascript:;" id="file_uploadify"></a>').hide();
				            }
				        }
				        if($("#is_upload_image")){
				        	$("#is_upload_image").remove();
				        	$(".is_upload_imageformError").remove();
				        }

		        	}
		        }

		    });
		});
	}

	//删除图片
	function delImg(id, obj){
	    cur_img_num--;
	    $(obj).parent().parent().remove();
	    $(".upLoadPicUl li").each(function(k, v){
	        if( $(this).find(":input[name='img_queue[]']").val() != undefined)
	            $(this).find(":input[name='img_queue[]']").val(k+1);
	    })
	    if (cur_img_num < 4 ) {
	        init_uploadify();
	        $(".upLoadPicUl li").last().show();
	    }
	}

    //设置图片显示的位置
	function first(o){
	    var parent = o.parentNode.parentNode;
	    parent.parentNode.insertBefore(o.parentNode.parentNode,parent.parentNode.firstChild);
	}


	//添加酒类型 表单
	function monitor_AddInpInput(){
		$('#addRatioBtn').click(function(){
			var i = $("#percent>span").length;
			var p = document.getElementById("percent");
			var addRatioBtn = document.getElementById("addRatioBtnId");
			var newRatio = document.createElement("span");
			newRatio.setAttribute("class", "ratio");
			newRatio.innerHTML = '<input class="inp1" type="text" name="inp[]"/> <input class="validate[custom[integer],min[1]] inp2" type="text" name="inp_percent[]"/> %';
			p.insertBefore(newRatio, addRatioBtn);
			if(i > 8){
			    $('#addRatioBtn').hide();
			}
		});
	}

	//ie6 div 被挡住
    function hide_ie6(status){
	   	if(status){
	   		$(".z_aWrap_r select").hide();
	   		$(".upLoadPicUl").hide();
	   		$("#addRatioBtnId").hide();

	   	}else{
	   		$(".z_aWrap_r select").show();
	   		$(".upLoadPicUl").show();
	   		$("#addRatioBtnId").show();
	   	}

    }


	//处理price_type 问题
	$('input:radio[name="price_type"]').change(function(){
		if($('input:radio[name="price_type"]:checked').val()==1){
			$("#wine_price").removeClass().addClass("w100");
			$(".wine_priceformError").remove();
		}else{
			$("#wine_price").toggleClass("validate[required,custom[number]]");
		}
	});