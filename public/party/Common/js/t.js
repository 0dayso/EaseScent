var tpl = {
	comment_singer : [
		'<div class="wine_other_back" id="create_comment_singer">',           
        '<div class="wine_other_face"><img src=""/></div>',
		'<div class="wine_other_n">',       
		'<div class="other_header">',       
		'<span><a href="###" ></a><em></em></span>',
        '<a href="javascript:;" class="comment_del">删除</a>',
		'</div>',                          
		'<div class="other_back_con"></div>',                 
		'</div>',                           
		'</div>',                             
		'</div>'
	].join(''),
	
	comment_big : [
		'<div class="l_back_con">',
		'<div class="l_back_con_f">',
		'<div class="l_user_face">',
        '<a href="###"><img src=""/></a>',
		'</div>',
		'<div class="l_back_main">',
		'<div class="l_user_name">',
		'<a href="###" class="l_user"></a>',
		'<a href="###" class="l_back_of">回复</a>',
		'</div>',
		'<div class="l_back_time">',
		'<span><em>刚刚</em>  |  <var>0条回复</var></span>',
		'<a href="javascript:;" class="l_s_up"  onclick="">收起回复</a>',
		'</div>',
		'<div class="l_others_back" id="comment_{$comment.id}">',
        '</div>',
		'<div class="my_back">',
		'<textarea id="comm_{$comment.id}"></textarea>',
		'<div class="l_back_face">',
		'<em>您已输入<var>0</var>字，还可以输入<var>46</var>字。</em>',
		'<img src="/Common/images/back_face.jpg" class="back_face"/>',
		'<img src="/Common/images/back_img.jpg" class="back_img"/>',
		'<span>',
		'<input type="button" class="l_back_sent" onclick="javascript:send_comment({$comment.id});" hidefocus/>',
		'</span>',
		'</div>',
		'</div>',
		'</div>',
		'</div>',
		'</div>'
	].join(''),
	comment_image : [
		'<div class="l_pro">',
		'<div class="l_back_img">',
        '<a href="__ROOT__/images/" class="l_back_img_a" hidefocus>',
		'<img src="__ROOT__/images/" width="200px"/>',
        '</a>',
		'</div>',
		'</div>'	
	].join(''),
    join_user : [
        '<li id="new_insert_user">',
		'<a href="###"><img src=""/></a>',
		'<span><a href="###"></a></span>',
		'</li>'    
    ].join(''),
	dialog : [
        //'<div style="height:100%;width:100%;position:absolute;z-i"></div>',
		'<div style="height:80px;width:150px;font-size:12px;color:#2b2b2b;top:50%;left:50%;margin:-40px 0 0 -75px;border:1px #4B4C4E solid;position:fixed;background-color:#fff;_position:absolute;box-shadow:1px 2px 5px 1px #333;border-radius:6px;z-index:100" id="dialog">',
		'<div style="height:80px;line-height:80px;text-align:center;">{text}</div>',
		'</div>'
	].join(''),
    dialog2 : [
        '<div style="height:100%;width:100%;position:absolute;top:0;left:0;z-index:99;opacity:0.7;background-color:#eee;" id="aaa"></div>',
		'<div style="height:400px;width:900px;font-size:12px;color:#2b2b2b;top:10%;left:10%;border:1px #4B4C4E solid;position:fixed;background-color:#fff;box-shadow:1px 2px 5px 1px #333;border-radius:6px;z-index:100" id="dialog">',
		'<div style="width:500px;height:500px;overflow:hidden;float:left;">',
        '<img src="#" id="target" alt="Flowers"  />',
        '</div>',      
        '<div style="width:318px;height:211px;overflow:hidden;float:left;">',
        '<img src="#" id="preview" alt="Preview" />',
        '</div>',
        '<div style="width:158px;height:100px;overflow:hidden;float:left;">',
        '<img src="#" id="small" alt="Preview" />',
        '</div>',
        '<div style="width:900px;height:50px;margin-top:30px;line-height:50px:float:left;text-algin:center;"><a href="javascript:;" id="save_img">保存图像</a></div>',
		'</div>'
	].join(''),
	login : [
		'<div class="l_login_box" id="loginDialog">',
		'<div class="l_login_shadow"></div>',
		'<div class="l_login">',
		'<div class="l_login_t">',
		'<span>请登录逸香通行证</span>',
		'<a href="###" class="l_login_out"></a>',
		'</div>',
		'<div class="l_login_c">',
		'<ul class="l_login_clef">',
		'<li><input type="text" value="帐号/邮箱" class="l_login_clef_input1" id="dialog_user"/></li>',
		'<li><input type="password" value="******" class="l_login_clef_input1" id="dialog_pass"/></li>',
		'<li>',
		'<a href="###" class="l_login_l">登录</a>',
		'<a href="###" class="l_login_l" id="dialog_regist">注册</a>',
		'<a href="http://user.wine.cn/member/remember" class="l_login_f">忘记密码?</a>',
		'</li>',
		'</ul>',		
		'</div>',
		'</div>',
		'</div>'
	].join('')
	
};
