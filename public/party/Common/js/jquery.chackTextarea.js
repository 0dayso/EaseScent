/*
	Check the input numbers at runtime.	
*/
(function($){
    $.fn.chackTextarea = function(options) {
        var opts = $.extend({},$.fn.chackTextarea.deflunt,options);
        //将中文视为一个字符，将英文视为半个字符，也就是两个英文字符按一个字符计算。
        var chackNums = opts.chackNum*2;
        //设置初始文本
        $(this).find(opts.chackNumObj).html("还能输入<em>" +opts.chackNum +"</em>个字!");
        var chackText=function($this){
			var timer;
            //清除timer
            $(opts.chackObj).blur(function(){
                clearInterval(timer);
            });
            $(opts.chackObj).focus(function(){
					timer=setInterval(function(){
					// 当前文本框中的字符串。
					var curText = $this.find(opts.chackObj).val();

					// 所有链接长度为10个字符
					var reg = /((f|ht)(tp|tps):\/\/)[-a-zA-^Z0-9@:\%_\+.~#?&//=]+/g;
					var newValue = curText.replace(reg, "********************");
			        newValue = newValue.replace(/[^\x00-\xff]/g, "**");
                    if(newValue.length>0){
                        if (newValue.length > chackNums) {
                            $this.find(opts.chackNumObj).html("已超出<em>" +Math.ceil((newValue.length - chackNums)/2) +"</em>个字!");
                            $this.find(opts.chackNumObj).css({"color":"#FF3300"});
                            // jquery 1.7.2 true or false
                            $this.find(opts.chackBtn).attr("disabled",true);
                            $this.find(opts.chackBtn).addClass(opts.disabledClass);
                            $this.find(opts.chackBtn).removeClass(opts.chackBtnHover);
                        }else{

                            $this.find(opts.chackNumObj).html("还能输入<em>" +Math.ceil((chackNums-newValue.length)/2) +"</em>个字!");
                            $this.find(opts.chackNumObj).css({"color":"#939393"});
                            $this.find(opts.chackBtn).attr("disabled",false);
                            $this.find(opts.chackBtn).addClass(opts.chackBtnHover);
                            $this.find(opts.chackBtn).removeClass(opts.disabledClass);
                        }
                    }else{
                        $this.find(opts.chackBtn).removeClass(opts.disabledClass);
                        $this.find(opts.chackNumObj).removeClass(opts.errorClass);
                        $this.find(opts.chackBtn).removeClass(opts.chackBtnHover);
                    }
                },500);
                return this;
            });

        };
        return $(this).each(function() {
            chackText($(this));
        });
    };
    $.fn.chackTextarea.deflunt={
        chackNum : 140,
        chackObj:".chackTextarea-area",
        chackNumObj :".chackTextarea-num",
        chackBtn:".chackTextarea-btn",
        disabledClass:"chackTextarea-disabled",
        errorClass:"chackTextarea-errortxt",
        chackBtnHover:"chackTextarea-btn-hover"
    };
})(jQuery);
