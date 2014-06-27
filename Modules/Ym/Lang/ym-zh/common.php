<?php
/**********企业通行证语言包***********/
define("HANGYE_1",1);
define("HANGYE_2",2);
define("HANGYE_3",3);
define("HANGYE_4",4);
define("HANGYE_5",5);
define("HANGYE_6",6);
define("HANGYE_7",7);
define("HANGYE_8",8);
define("HANGYE_9",9);
define("HANGYE_10",10);
define("HANGYE_11",11);
define("HANGYE_12",12);

define("XINGZHI_1",1);
define("XINGZHI_2",2);
define("XINGZHI_3",3);
define("XINGZHI_4",4);
define("XINGZHI_5",5);
define("XINGZHI_6",6);
define("XINGZHI_7",7);

define("GUIMO_1",1);
define("GUIMO_2",2);
define("GUIMO_3",3);
define("GUIMO_4",4);
define("GUIMO_5",5);
define("GUIMO_6",6);
define("GUIMO_7",7);

return array(
    "HANGYE"=>array(
    			HANGYE_1=>"教育/培训",
    			HANGYE_2=>"酒店/餐饮",
    			HANGYE_3=>"零售/批发",
    			HANGYE_4=>"旅游/度假",
    			HANGYE_5=>"交通/运输/物流",
    			HANGYE_6=>"政府/公共事业/非盈利机构",
    			HANGYE_7=>"综合性工商企业",
    			HANGYE_8=>"快速消费品",
    			HANGYE_9=>"娱乐/体育/休闲",
    			HANGYE_10=>"IT/互联网/电子商务",
    			HANGYE_11=>"贸易/进出口",
    			HANGYE_12=>"其他"
    			),
    "XINGZHI"=>array(
    				XINGZHI_1=>"外资企业",
    				XINGZHI_2=>"合资企业",
    				XINGZHI_3=>"私营企业",
    				XINGZHI_4=>"民营企业",
    				XINGZHI_5=>"股份制企业",
    				XINGZHI_6=>"国有企业",
    				XINGZHI_7=>"上市企业"
    			),
    "GUIMO"=>array(
    				GUIMO_1=>"10人以下",
    				GUIMO_2=>"10-50人",
    				GUIMO_3=>"50-200人",
    				GUIMO_4=>"200-500人",
    				GUIMO_5=>"500-1000人",
    				GUIMO_6=>"1000-5000人",
    				GUIMO_7=>"5000人以上"
    			),
    /************title*************/
    "title_my_edit"=>"编辑个人信息-逸香企业",
    "title_company"=>"身份认证-逸香企业",
    "title_email_valid"=>"验证邮箱-逸香企业",
    "title_moblie_valid"=>"验证手机-逸香企业",
    "title_add_email"=>"添加邮箱-逸香企业",
    "title_add_moblie"=>"添加手机-逸香企业",
    "title_b_upload"=>"上传营业执照-逸香企业",
    "title_email_confirmation"=>"邮箱认证-逸香企业",
    "title_moblie_confirmation"=>"手机认证-逸香企业",
    "title_edit_email"=>"修改邮箱-逸香企业",
    "title_edit_moblie"=>"修改手机-逸香企业",
    "title_my_info"=>"个人信息页-逸香企业",
    "title_edit_pass"=>"修改密码-逸香企业",
    "title_login"=>"登录-逸香企业",
    "title_register"=>"注册-逸香企业",
    "title_set_face"=>"设置头像-逸香企业",
    /**********message**************/
    "nick_length_limit"=>"企业简称只能在2-20字符",
    "nick_all_number"=>"企业简称不能为全数字",
    "nick_check"=>"企业简称只能为字母数字下划线和中文",
    "nick_exit"=>"此企业简称已经存在",
    "nick_can_use"=>"此企业简称可以使用",
    "edit_success"=>"修改成功",
    "pass_edit_success"=>"密码修改成功,请重新登录!",
    "old_pass_error"=>"原密码验证错误!",
    "user_name_check"=>"邮箱或手机格式不正确",
    "user_name_exit"=>"此用户名已经存在",
    "user_name_can_use"=>"邮箱或手机格式可以使用",
    "moblie_check_error"=>"手机格式不正确",
    "moblie_can_use"=>"此手机号可以使用",
    "email_check_error"=>"邮箱格式不正确",
    "email_can_use"=>"此邮箱可以使用",
    "qy_moblie_error"=>"企业电话格式不正确",
    "qy_moblie_can_use"=>"企业电话可以使用",
    "verify_code_success"=>"验证码正确",
    "verify_code_error"=>"验证码错误",
    "user_name_empty"=>"用户名为空",
    "pass_length_error"=>"密码只能在6-20字符",
    "company_name_empty"=>"企业名称不能为空",
    "company_moblie_empty"=>"企业电话不能为空",
    "company_moblie_check"=>"企业电话格式不正确",
    "register_success"=>"注册成功",
    "login_error"=>"用户名或者密码不正确",
    "login_empty"=>"用户名或者密码为空",
    "login_success"=>"登录成功",
    "loginout_success"=>"退出成功",
    "face_save_success"=>"头像保存成功",
    /*********12.25**********/
    "upload_success" =>"上传成功",
    "upload_wait"    =>"恭喜您！您提交的认证申请已经成功提交，审核会在1-3个工作日结束，请您耐心等待。",
    "upload_qy_name" =>"您申请认证的企业为：",
    "renzheng_error" =>"认证失败",
    "renzheng_success" =>"认证成功",
    "email_verify"   =>"你的邮箱已经验证！",
    "email_verify_success"   =>"恭喜您！邮件验证成功！",
    "send_email_hello" =>"您好:",
    "send_email_msg1" =>"您已申请认证邮箱，点击以下链接，即可完成安全认证:",
    "send_email_msg2"=>"如果链接无法点击，请完整拷贝到浏览器地址栏里直接访问.",
    "send_email_msg3"=>"这只是一封系统自动发出的邮件，请不要直接回复。",
    "send_email_from" =>"【逸香企业会员中心】",
    "send_email_title"=>"逸香企业会员中心认证提醒",
);


?>
