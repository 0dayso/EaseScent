// 
    // 弹出层
    var dialog = function(){
        this.tpl;
    };
    dialog.prototype = {
    
        alertt : function(text){
            this.tpl = tpl.dialog;
            this.tpl = this.tpl.replace('{text}', text);
            $('body').append(this.tpl);
            setTimeout(function(){
                $('#dialog').remove();
            
            },1500);
        },
        
        login : function(){
            this.tpl = tpl.login;
            $('body').append(this.tpl);
            input  = $('.l_login_clef > li > input');
            input.each(function(){
                if(this.value == '用户名' || this.value == '密码'){
                    $(this).one('click' , function(){
                        $(this).val('');
                    });
                }
            });
            //  dialog close
            //
            $('.l_login_out').bind('click', function(){
                $('#loginDialog').remove();        
            });
            
            // zhuce 
            //
            $('#dialog_regist').bind('click', function(){
                window.location = 'http://user.wine.cn/member/signup?continue='+window.location.href;      
            });
            
            $('#dialog_user').bind('click', function(){
                if($(this).val() == '帐号/邮箱'){
                    $(this).val('');
                }        
            });
            $('#dialog_user').bind('blur', function(){
                if($(this).val() == ''){
                    $(this).val('帐号/邮箱');
                }     
                    
            });

            $('#dialog_pass').bind({
                'click': function(){
                    if($(this).val() == '******'){
                        $(this).val('');
                    }        
                },
                'focus': function(){
                    if($(this).val() == '******'){
                        $(this).val('');
                    }
                },
                'blur':  function(){
                    if($(this).val() == ''){
                        $(this).val('******');
                    }       
                },
                'keypress': function(e){
                    if(e.keyCode == 13){
                        dialog_init();
                    }    
                } 
            });
            $('.l_login_l').bind('click', function(){
                    dialog_init();
            });
            function dialog_init(){
                var username = $('#dialog_user').val();
                var password = $('#dialog_pass').val();
                if(username == '' || password == ''){
                    alert('请输入完整的用户名或密码');
                    return;
                }
                $.post('/index.php/Uc/login',
                    {username : username, password : password},
                    function(data){
                        if(data){
                            var userinfo = eval("(" + data + ")");
                            if(userinfo.online==1){
                               // var userinfo = eval("(" + data + ")");
                               // var loginOut = $("#loginOutUrl").val();
                               // var usermenu = $("<span>你好，欢迎来到逸香网</span><span>&nbsp;"+userinfo.nickname+"</span><span>|</span><a href='"+loginOut+"?continue="+window.location.href+"'>退出</a><span>|</span><a href='__ACCOUNT__/member/home'>我的逸香网</a>");
                               // var url = $("#uc_url").val()+"/api/auth?sid="+userinfo.sid;
                               // $("#accessLogin").html("<iframe style='display:none' src="+url+"></iframe>");
                               // $("#loginDiv").html(usermenu);
                                window.location.reload();
                                return ;
                            }
                        }else{
                            alert("用户名密码错误");
                            return false;
                        }
                    });
				}
            }
       
    };
    var wine = new dialog();
    
