$(function(){
        
    /*导航条切换效果*/
	$("#nav_list>li").click(function(){
		if($(this).attr("id")=="nav_exp"){
			$("#exp").addClass("exp_h");
			if($("#sub_more").css("display")=="none"){
					$("#sub_more").slideDown();
				}else{
					$("#sub_more").slideUp();
				}
				return;
		}
		else{
			$("#exp").removeClass("exp_h");
			$("#sub_more").css("display","none");
		}
		$("#nav_list>li").removeClass("nav_list_hover");
		$(this).addClass("nav_list_hover");
	});
    /*  进入我的酒会 */

    $('#goto_my_wine').click(function(){
        var userInfo = replaceUserInfo();
        if(userInfo.uid == ''){
           window.location.href = 'party.wine-cn.com/index.php/User/my';
          // window.location.href = 'http://user.wine.cn/member/auth?continue='+window.location.href;
        }else{
            window.location.href = '/index.php/User/my';
            //window.location.href = '/User/myInsert';
            return false;
        }
        //window.location.href = 'index.php/User/myInsert';
    });
    /*  创建酒会    */
    $('#goto_create').click(function(){
        var userInfo = replaceUserInfo();
        if(userInfo.uid == ''){
            window.location.href = 'http://user.wine.cn/member/auth?continue='+window.location.href;
        }else{
            window.location.href = '/index.php/Create';
            return false;
        }        
    });
    /* 关闭 酒会*/
    close_wine = function(id){
        var close = confirm('确定要删除此酒会');
        if(close){
            $.post('index.php?m=Detail&a=setShow',
                {'id' : id},
                function(t){
                var data = eval("(" + t + ")");
                    if(data.status > 0 && data.status == '1'){
                        wine.alertt('关闭酒会成功'); 
                        $('#party_'+id).remove();
                    }else{
                        wine.alertt('关闭酒会失败,可能你已经关闭了酒会，请尝试刷新');
                    }
            });
        }
    };
    /* 发布评论 */
    send_comment = function(id,_this){
        //获取验证码
        if(id==0){
        	var verify = $("#verify").val();
        }
        var pid = id,
            party_id = $('#party_info').val(),
            contentObj,
            content;

        if(pid == 0){
            contentObj  = $('#comment');
        }else{
            contentObj  = $('#comm_'+id);
        }
        content = contentObj.val();
        var imgComment = $('#img').val();
        if(content.length == 0){
            wine.alertt('您还没有输入内容');
            return false;
        }
        var photo_id = $('#photo_id').val();
        var huifu = $('#huifu').val();
        $.post("/Uc/checklogin",
            function(data){
                if(data!="null"){
                    var userinfo = eval("(" + data + ")");
                    if(userinfo.online==0){
                        wine.login();
                    }else{
                        if(id != 0){
                            var sum = $('#comm_'+id).val().length;
                            if(sum > 140){
                                wine.alertt('亲，您输入的字太多了！');
                                return false;
                            }
                            if($('#comm_'+id).val() == '140字以内'){
                                wine.alertt('您还没有输入内容');
                                return false;
                            }
			            }
                        $.post('/Detail/insert_comment',
                            {
                                'pid' : pid,
                                'party_id' : party_id,
                                'content'  : content,
                                'photo_id' : photo_id,
                                'img'      : imgComment,
                                'huifu'    : huifu,
                                'verify'   : verify

                            },
                            function(t){
                                if(t==200011){
                            		alert("验证码不正确,请刷新验证码!");
                            		return;
                            	}
                                var data;
                                data = eval('('+ t +')');
                                if(data.status > 0 && data.status == 1){
                                    if(id == 0){
                                        if(imgComment != ''){
                                            $.post('/Detail/imgCommentCountPlus',
                                                {
                                                    'id' : imgComment  
                                                });
                                        }

                                            wine.alertt('评论成功');
        
                                        setTimeout(function(){
                                            window.location.reload();  
                                        },3000);
                                    }else{
                                    	//获取评论总数
                                    	var currsum = $("#l_s_up_"+id).prev("var").children("em").html();
                                    	//回复开始
                                        var html = tpl.comment_singer;
                                        $('#comment_'+pid).prepend(html);
                                        var newComment = $('#create_comment_singer');
                                        newComment.children('div.wine_other_face').children('img').attr('src',"http://i.wine.cn/User/Api/User.php?c=User&m=avatar&access_token=1000000003&token_secret=92A5hymz0AXTykJfhUr1&uid="+userinfo.mid);
                                        var commentUser =  newComment.children('div.wine_other_n').children('div.other_header').children('span');
                                        commentUser.children('a').text(''+userinfo.nickname+'');
                                        commentUser.children('em').text(content);
                                        newComment.children('div.wine_other_n').children('div.other_back_con').text('刚刚');
                                        newComment.attr('id','comm_'+data.data);
                                        $('#comment_'+pid).prev('comment_num').children('var').children('em').text(eval(parseInt($('#comment_'+pid).prev().children('var').children('em').text()) + 1));
                                        $('#comm_'+data.data).children('div.wine_other_n').children('div.other_header').children('a').bind('click',function(){
                                        	//获取评论总数 
                                        	var currsum = $("#l_s_up_"+id).prev("var").children("em").html();
                                        	$("#l_s_up_"+id).prev("var").children("em").html(parseInt(currsum)-1);   
                                        	delComment(data.data);        
                                        })
                                   		$("#l_s_up_"+id).prev("var").children("em").html(parseInt(currsum)+1);
                                    }
                                    wine.alertt('评论成功');
                                    
                                }else{
                                    wine.alertt('发布评论失败');
                                }
                                $('#photo_id').val('');
                                contentObj.val('');
                            });
                    }
                }
            });
    }
    
    delComment = function(id,cid){
    	if(!confirm("确定删除?")){
    		return;
    	}
        $.post('/Detail/delComment',
                {
                    'id'  : id,
                    'img' : $('#img').val() 
                },function(t){
                    var data = eval('('+ t +')');
                    if(data.status > 0 && data.status == 1){
                        if(cid != 0){
                            $('#comm_'+id).parent().prev().children('var').children('em').text(eval(parseInt($('#comm_'+id).parent().prev().children('var').children('em').text()) - 1));

                            $('#comm_'+id).remove();
                            var csum = $("#l_s_up_"+cid).prev("var").children("em").html();
                            $("#l_s_up_"+cid).prev("var").children("em").html(parseInt(csum)-1); 
                        }else{
                            $('#c_'+id).remove(); 
                        }
                        wine.alertt('删除成功'); 
                    }else{
                        wine.alertt('删除失败'); 
                    }
                
                }); 
    }
    comment_up = function(id){
        if($('#comment_'+id).css('display') == 'block'){
            $('#comment_'+id).slideUp();
            $('#l_s_up_'+id).text('展开回复');
        }else{
            $('#comment_'+id).slideDown();
            $('#l_s_up_'+id).text('收起回复');
        }
    
    }

    getCityInfo = function(){
        var province_id = $("#province_id").val();
        $.post('/Common/getCityName',
                {
                    'id' : province_id
                },function(data){
                    $('#city').html(data);
        }); 
    
    }


    $('#show').toggle(
        function(){
            $('.wine_list_p').css({height : 'auto'});
            $(this).removeClass();
            $(this).addClass('wine_list_p_less');
            $('.wine_list_p_show').css({'margin-top' : '0px'});
            $(this).text('收起');
        },
        function(){
            $('.wine_list_p').css({height : '1000px'});
            $(this).removeClass();
            $(this).addClass('wine_list_p_more');
            $(this).text('展开');
        }
    );

    gotoPage = function(){
        var city =  $('#city').val();
        window.location.href="/PartyList/index/city/"+city;
    }
    $('textarea[name=mycomment]').click(function(e){
        if($(this).val() == '140字以内'){
            $(this).val('');
        }
        $(this).bind('keyup',function(){
            var sum = 140,
                html,
                sun;
            sun = $(this).val().length;
            if(sum -sun > 0){
                html = '您已输入'+ sun +'字，还可以输入'+ eval(sum - sun) +'字。';
                $(this).parent('div').siblings('div').children('span').text(html); 
            }else{
                html = '您已输入'+ sun +'字，超过了'+ eval(sun -sum) +'字。';
                $(this).parent('div').siblings('div').children('span').text(html); 
            }
        });
            
    });
    $('textarea[name=mycomment]').blur(function(){
        if($.trim($(this).val()) == ''){
            $(this).val('140字以内');
        }     
    });
    $('#verify').blur(function(){
        if($(this).val() == ''){
            $(this).val('验证码');
        }        
    });
    $('#verify').click(function(){
        if($(this).val() == '验证码'){
            $(this).val('');
        }        
    })
    // map big show
    $('#big_map').click(function(){
        var img_src = $('.wine_map>img').attr('src');
        var content = "<div><img src='"+img_src+"'/></div>";
        art.dialog({                        
            id:'map',    
            content:content,
            title:"上传酒会照片",      
            width:'700px',               
            height:'500px',          
	        cancel:function(){window.art.dialog({id:'map'}).close()}
	    });     
            
    });
    $('.back_face').click(function(){
        $('#new-face-frame').html(' ');
        var id = $(this).attr('data');
        var nTop = $(this).offset().top;
        $('#new-face-frame').css({'top':nTop});
        var html =  getFace(id);
        $("#new-face-frame").html(html);
    });
 
    $('.comment_con_back').live('click',function(){
        var html = $(this).siblings('span').children('a').text();
        if(html == ''){
            html = $(this).siblings('a').text();
            html = '回复' +  html +':';
            $(this).parents('.comment_con_header').siblings('.comment_con_others').children('.wine_comment_mod2').children('.comment_put_in2').children('textarea').val(html);
        }else{
            html = '回复 '+ html +':';
            $(this).parents('.comment_con_others').children('.wine_comment_mod2').children('.comment_put_in2').children('textarea').val(html);
        }
    });
    $("#show_city").click(function(){
		$("#city_list").show('slow');
    });
    $("#city_list_del").click(function(){
		$("#city_list").hide("fast");
    });
	$(".my_back").children("textarea").click(function(){
		$("span[name=re_verify]").remove();
		var dom =$("<span name='re_verify' class='l_yzm'><input  type='text' value='验证码'/><img src='/Detail/verify'  onclick=\"this.src='/Detail/verify/'+Math.random()\" title='刷新验证码'/></span>");
		$(this).next().children("img").after(dom);
		dom.children("img").click();
		/*dom.children("img").bind("click",function(){
			$(this).attr("src",$(this).attr("src")+Math.random())
		});//刷新验证码*/
	})
    $('#show_price').click(function(){
        $('.price_list').toggle();    
    });

    $('#show_lower').click(function(){
        $('.off_list').toggle();    
    });
   
    $('.back_img').click(function(){
        var id = $(this) .attr('data');
        var content = "<form target='myhiddenform' id='myparty_test' method='post' action='/Detail/commentPic' enctype='multipart/form-data'><input type='file' name='mypic'><input type='hidden' value="+id+" name='id'></form>";
		
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
    $('.l_back_of').live('click', function(){
        var name = $(this).siblings('a.l_user').text();
        var id   = $(this).attr('data');
        $('#comm_'+id).val('回复 '+ name +':');           
    });
    //取消参加酒会
    $('#unregist').live('click',function(){
        
        var firm = confirm('确定要取消关注酒会么?');
        if(firm){
            var userinfo = replaceUserInfo();
            var partyId = $('#partyId').val();
            $.post('/Detail/uninsert',
                {
                    partyId : partyId 
                },
                function(t){
                    var data = eval('('+ t +')');
                    if(data.status == 1){
                        wine.alertt('取消成功');
                        $('#unregist').parent().html('<a href="###" class="wine_join" id="regist">关注</a>');
                        $('#insert_user_num > em').text(eval($('#insert_user_num > em').text() - 1));
                        $('#new_insert_user').remove();
                        $('#insert_user_'+userinfo.uid).remove();
                        if(!$('.wine_join_p').children().get(0)){
                            $('.wine_list_con5 > .wine_list_h3').after('<div class="no_person">还木有人关注哦，快来抢个沙发吧！</div>'); 
                        } 
                    }else{
                    
                        wine.alertt('取消失败');
                    }
                
                }
            );
        }
    });
    function replaceUserInfo(){
        var info = $('#userinfo').val();
        info = "{"+ info + "}";
        info = eval('('+info+')');
        return info;
    } 
    $('#regist').live('click',function(){
        $.post("/Uc/checklogin",function(t){
            if(t!="null"){
                var userinfo = eval("("+ t + ")");
                if(userinfo.online == 0){
                   wine.login(); 
                
                }else{
                    var partyId = $('#partyId').val(),
                    userinfo = replaceUserInfo();
                    $.post('/Detail/insert',
                        {
                            partyId  : partyId
                        },
                        function(data){
                            data = eval('('+data+')');
                            if(data.status == 1){
                                wine.alertt('申请成功');
                                $('#regist').parent().html('<a href="###" class="wine_join" id="unregist">取消关注</a>');
                                $('#insert_user_num > em').text(eval(parseInt($('#insert_user_num > em').text()) + 1));
                                $('.no_person').remove();
                                var html = tpl.join_user;
                                $('.wine_join_p').append(html);
                                $('#new_insert_user').children('a').attr('href','/User/other/uid/'+userinfo.uid);
                                $('#new_insert_user').children('a').children('img').attr('src',"http://i.wine.cn/User/Api/User.php?c=User&m=avatar&access_token=1000000003&token_secret=92A5hymz0AXTykJfhUr1&uid="+userinfo.mid);
                                $('#new_insert_user').children('span').children('a').text(userinfo.nick);
                                $('#new_insert_user').children('span').children('a').attr('href','/User/other/uid/'+userinfo.uid);
                                //$('#new_insert_user').attr('id','insert_user_'+userinfo.uid);
                               //window.location.reload();
                            }else{
                                wine.alertt('你已经关注过了 ,请不要重复关注');
                            } 
                        });
                }
            }
        }); 
    });
    if($('.wine_list_p').height() <= 1000){
        $('.wine_list_p_more').remove();
        $('.wine_list_p').css({'height' : 'auto'});
    }else{
        $('.wine_list_p').css({'height' : 1000});
    }
});

function down(obj1,obj2,class1,class2){
	var iTime=null;
	obj1.mouseover(function(){
		clearTimeout(iTime);
		obj2.slideDown();
		obj1.addClass(class1);
	});
	obj1.mouseout(function(){
		iTime=setTimeout(function(){
			obj2.slideUp(function(){
				obj1.removeClass(class1);
			});
			
		}, 300);
	});
	obj2.mouseover(function(){
		clearTimeout(iTime);
		obj1.addClass(class1);
	});
	obj2.mouseout(function(){
		iTime=setTimeout(function(){
			obj2.slideUp();
			obj2.slideUp(function(){
				obj1.removeClass(class1);
			});
		}, 300);
	});
}
$(function(){
	down($('#exp'),$('#sub_more'),"exp_h");
	down($('#exp2'),$('#sub_more2'),"exp_h");
	var T=null;
	$(".i_per_model_t").mouseover(function(){
		clearTimeout(T);
		var y = $(this).offset().top;
		var x = $(this).offset().left;
		$(".i_per_model_b").css({top:y+"px",left:x+120+"px",display:"block"});
	});
	$(".i_per_model_t").mouseout(function(){
		T=setTimeout(function(){
			$(".i_per_model_b").css({display:"none"});
		}, 300);
	});
	$(".i_per_model_b").mouseover(function(){
		clearTimeout(T);
		$(".i_per_model_b").css({display:"block"});
	});
	$(".i_per_model_b").mouseout(function(){
		T=setTimeout(function(){
			$(".i_per_model_b").css({display:"none"});
		}, 300);
	});
})
$(function(){
        /*
	var i=$(".i_big_list_img>img").length;
	$("#i_img_length").text(i);
	$(".i_big_list_img").width(i*613);
	var n=1;
	$(".i_big_title").text($(".i_big_list_img").children().eq(0).attr("alt"));
	$("#i_big_rig").click(function(){
		$(".i_big_title").text($(".i_big_list_img").children().eq(n-1).attr("alt"));
		var marginL = $(".i_big_list_img").css("margin-left")=="auto"?0:parseInt($(".i_big_list_img").css("margin-left"));
		if(i>1){
			if(!$(".i_big_list_img").is(":animated")){
				n++;
				if(n>$(".i_big_list_img>img").length){
					n=$(".i_big_list_img>img").length;
					return;
				}
				$("#i_img_num").text(n);
				$(".i_big_list_img").animate({
					marginLeft:marginL-613+"px"
				},"slow",function(){
					i=i-1;
					$(".i_big_lef").find("span:first").addClass("i_big_lef_s");
					if(i<=1){
						$(".i_big_rig").find("span:first").addClass("i_big_rig_s");
					}
				})
			}
		}
	});
	$("#i_big_lef").click(function(){
		$(".i_big_title").text($(".i_big_list_img").children().eq(n-1).attr("alt"));
		var marginL = $(".i_big_list_img").css("margin-left")=="auto"?0:parseInt($(".i_big_list_img").css("margin-left"));
		if(i<$(".i_big_list_img>img").length){
			if(!$(".i_big_list_img").is(":animated")){
				n--;
				if(n<1){
					n=1;
					return;
				}
				$("#i_img_num").text(n);
				$(".i_big_list_img").animate({
					marginLeft:marginL+613+"px"
				},"slow",function(){
					i=i+1;
					$(".i_big_rig").find("span:first").removeClass("i_big_rig_s");
					if(i>=$(".i_big_list_img>img").length){
						$(".i_big_lef").find("span:first").removeClass("i_big_lef_s");	
					}
				})
			}
		}
	});
    */
	$("#city_change").click(function(){
		$("#city_list").show('slow');
	});
	$("#city_list_del").click(function(){
		$("#city_list").hide("fast");
	})
})
 //用户删除图片处理
    function deleteimg(id,_this){
    	if(!confirm("确定删除?")){
    		return;
    	}
    	$.post('/index.php/Img/deleteimg',
                    {id : id},
                    function(data){
                        if(data){
                        	if(data==1000){
                        		alert("请先登录!");
                        		return;
                        	}else if(data==1001){
                        		alert("不存在此图片!")
                        		return;
                        	}else if(data==1003){
                        		alert("出现异常联系管理员!");
                        		return;
                        	}else if(data==1002){
                        		var i = $("body").find("#photo_sum").html();
                        		if(i>0){
                        			i = i-1;
                        			$("body").find("#photo_sum").html(i);
                        		}
                        		$(_this).parent().remove();
                        		alert("删除成功!");
                        	}
                  }
         })
    }



