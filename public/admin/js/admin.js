/**
 * 后台管理所需JS函数
 */

var _DIALOG_BG_COLOR = '#600';
var _DIALOG_BG_OPACITY = 0.87;

$(document).ready(function(){
	//监听a标签删除命令
	$("a[target=delete]").each(function(){
		$(this).click(function(event){
			var $this = $(this);
			var title = $this.attr("title");
			var href = $this.attr("href");
			if(!title) {
				title = '请确认是否删除该内容？';
			}
			art.dialog({
				lock: true,
				background: _DIALOG_BG_COLOR,
				opacity: _DIALOG_BG_OPACITY,
				content: title,
				ok:function(){
					location.href = href;
				},
				cancel:function(){
					return true;
				},
				okValue: '确定',
				cancelValue: '取消'
			});
			return false;
		});	
	});	
});

/**
 * tab 切换控制
 */
function SwapTab(id1, id2) {
	$(".tab-nav").each(function(){
			$(this).css('display','none');
	});    
	$(".on").each(function(){
		$(this).removeClass('on');
	});    
	$("#" + id1).addClass('on');
	$("#" + id2).css('display', 'block');
}

/**
 * checked all
 */
function selectAll(name) {
    var aa = $("#check_box").attr("checked");
    if($("#check_box").attr("checked")=='checked') {
        $("input[name='"+name+"']").each(function() {
            this.checked=true;
        });
    } else {
        $("input[name='"+name+"']").each(function() {
            this.checked=false;
        });
    }
}
