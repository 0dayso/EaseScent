// This file is related with setting-avatar.php
$(document).ready(function(){
	
    //图片上传
    $(function() {
    		var usersign = $("#usersign").val();
    		var faceid = $("#faceid").val();
            $('#file_avatar_upload').uploadify({
                'swf'        : site_pic_url+'/Ym/js/uploadify.swf',
				'uploader'		  : site_pic_url+'/Ym/js/uploadify.php',
				'buttonImage'		  : site_pic_url+'/Ym/images/upload_bg.jpg',
				'buttonText':'浏览',
				'formData' : {'usersign':usersign},
				/*
				'cancelImg'		  : '/images/cancel.png',
				'folder'		  : '/User/Data/Uploads/Avatar/real',*/
				'fileTypeExts'		  : '*.jpg;*.gif;*.png',
				'fileTypeDesc'		  : 'Image Files',
				'auto'			  : true,
				'fileObjName'	  :'Filedata',
				'progressData'    : 'percentage',
                'removeCompleted' : true,
				'debug'           : false, 
				'fileSizeLimit'	  :1024, 
				'width'           : 100, 
				//'uploadLimit'     : 1,
				'buttonCursor'    : 'hand', 
				'onUploadError'		  : function (event,ID,fileObj,errorObj){
					errorMsg('100016');
				},
				'onUploadSuccess' : function(file,data,response){
                   var fileinfo = file.name.split(".");
                   var extend = fileinfo[fileinfo.length-1].toLocaleLowerCase(); 
                   var filesrc = site_upload_url+"/Ym/face/u_"+faceid+"/myface_upload."+extend+"?id="+Math.random();
                   $("#big-img").attr("src",filesrc);
                   $('#preview').attr('src',filesrc);
                   $('#small').attr('src',filesrc);
                   autoload();
                   $("#big-img").next().find("img").attr("src",filesrc);
                },
                'onCancel' : function(){
                    $("#upload-file").val('');
                }
        });             
    });
    //autoload();
    var jcrop_api, boundx, boundy;
    function autoload(){
	    $("#big-img").Jcrop({
	    	setSelect : [ 0 , 0 , 170 , 160 ],
	    	aspectRatio: 85/80,
	    	onChange:showCoords,
	    	onSelect:showCoords
	    },function(){                                                                                            
	            // Use the API to get the real image size                                                            
	            var bounds = this.getBounds();                                                                       
	            boundx = bounds[0];                                                                                  
	            boundy = bounds[1];                                                                                  
	            // Store the API in the jcrop_api variable                                                           
	            jcrop_api = this;                                                                                    
	     	}
	    );
    }
    function showCoords(c){
    	$('#big-img').data(c);
    	if (parseInt(c.w) > 0){
            var rx = 207 / c.w;
            var ry = 200 / c.h;
            var sx = 85/ c.w;
            var sy = 80 / c.h;
       
            $('#preview').css({
               // width: Math.round(rx * boundx) + 'px',
                width: Math.round(rx * boundx) + 'px',
               // height: Math.round(ry * boundy) + 'px',
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
	 }
});



// 提交头像
$("#post-avatar").live("click",function(){
        
 	var w = $('#big-img').data();
 	if(w.x==undefined){
 		alert("请上传头像!");
 		return false;
 	}
 	var file = $("#big-img").attr("src");
 	var fileinfo = file.split("?");
 	var fileinfo = fileinfo[0].split(".");
    var extend = fileinfo[fileinfo.length-1]; 
 	
    $.post('/index.php/Setface/save',
            {
                x : w.x,
                y : w.y,
                width : w.w,
                height : w.h,
                extend:extend
            },function(resp){
            	//var resp = eval("("+resp+")");
				if(resp.status == '1'){
					alert('头像保存成功！');
				}else{
					alert('头像保存失败！');
					return false;
				}
				//window.location.reload();
            }, "json");
});



