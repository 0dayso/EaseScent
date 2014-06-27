/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
            var editor;
            var imgbtn;
            var imgnum = 0;
            KindEditor.ready(function(K) {
                editor = K.create("#qy_introduction", {
                    uploadJson : "{:Url('upload')}&type=Agent/uploads",
                    allowFileManager : false
                });

                imgbtn = K.uploadbutton({
                    button : K('#imgbtn')[0],
                    fieldName : 'imgFile',
                    url : "{:U('upload')}&type=Agent/images",
                    afterUpload : function(data) {

                        if (data.error === 0) {
                            if(imgnum < 5){
                                imgnum++;
                                if(imgnum ===1){
                                    var html ='<li><input name="img_file[]" type="hidden" value="'+data.url+'" /><input name="img_queue[]" type="hidden" value="'+imgnum+'" /><img src="http://www.wine.com'+data.url+'" width="100%" height="100%" /> <p class="pic_closeBtn"><img src="__PUBLIC__/Expo/img/pic_closeBtn.gif" /></p><a href="#" onclick="first(this)"></a></li>';
                                }else{
                                    var html ='<li id="imgnum"><input name="img_file[]" type="hidden" value="'+data.url+'" /><input name="img_queue[]" type="hidden" value="'+imgnum+'" /><img src="http://www.wine.com'+data.url+'" width="100%" height="100%" /> <p class="pic_closeBtn"><img src="__PUBLIC__/Expo/img/pic_closeBtn.gif" onclick="delImg(imgnum)"/></p><p class="txt"><a href="#" onclick="first(this)">首图</a></p></li>';
                                }
                                $("#imglist").before(html);
                                if(imgnum === 4){
                                    $("#imglist").hide();
                                }
                            }

                        }
                    }
                });
                $(":input[name='imgFile']").css('width','72px');
                imgbtn.fileBox.change(function(e) {
                    imgbtn.submit();
                });

            });

            function first(o){
                var parent = o.parentNode.parentNode;
                parent.parentNode.insertBefore(o.parentNode.parentNode,parent.parentNode.firstChild);
            }
            function delImg(id){
                $("#id").hide();
            }

            function checkEmail()
            {
                var div = document.getElementById("div3");
                div.innerHTML = "";
                submit_disabled = false;
                var email = document.myform.email.value;
                if (email == '')
                {

                    div.innerHTML = "请填写邮箱";
                    submit_disabled = true;
                    email.focus();
                }
                else if (!test())
                {
                    div.innerHTML =" 邮箱格式不正确";
                    submit_disabled = true;
                    email.focus();
                }
                else
                    return true;
            }
            function test()
            {
                var temp = document.getElementById("email");
                //对电子邮件的验证
                var myreg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
                if(!myreg.test(temp.value))
                {

                    return false;
                }
                return true;
            }
            function checkInfo()
            {
                if(checkEmail())
                {
                    $("form[name='myform']").submit();
                }
            }

            function delPicture(this1){
                $(this1).parent().remove();
            }

