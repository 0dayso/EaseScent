;$(function(){
	$('#upBtn').click(function(){
		var subDir = $('#subDirName').val();
		if(subDir == ''){
			alert('目录名不能为空！');
			return;
		}
		$('#upjcimg').ajaxSubmit({
			data : {
				subdir : subDir
			},
			dataType:'json',
			success : function(r) {
				if(r.st ) {
					var e = '';
					var cat = r.cat;
					switch(cat){
						case 'js':
						case 'css':
							e  = '<li>';
							e += '<a href="http://topic.wine.cn/'+$('input[name="dirnm"]').val()+'/'+cat+'/'+r.file+'" title="'+r.file+'" target="_blank">'+r.file+'</a>';
		                    e += '<input type="hidden" name="delLink" value="http://admin.wine.cn/index.php?app=Topics&m=Index&a=delStatics&type='+cat+'&fn='+r.file+'"/>';   				
		                    e += '<span class="delBtn">删&nbsp;除</span>';
		                    e += '</li>';
		                    var newEle = $(e);
		                    newEle.appendTo($('ul[cat='+cat+']'));
	                    break;
	                    default:
							e  = '<li>';
							e += '<a class="zoom" href="http://topic.wine.cn/'+$('input[name="dirnm"]').val()+'/images/'+r.file+'" title="'+r.file+'">'+r.file+'</a>';
		                    e += '<input type="hidden" name="delLink" value="http://admin.wine.cn/index.php?app=Topics&m=Index&a=delStatics&type=images&fn='+r.file+'"/>';   				
		                    e += '<span class="delBtn">删&nbsp;除</span>';
		                    e += '</li>';
		                    var newEle = $(e);
		                    newEle.prependTo($('ul[cat="images"]'));
	                    break;
					}
					art.dialog({
						lock:true,
						content:'<b style="font-size:16px;">:) </b><br/> Success. ',
						time:2000
					});
				}else{
					art.dialog({
						lock:true,
						content:'<b style="font-size:16px;">:( </b><br/> Oops! Something unexpected happend!',
						time:13000
					}); 
				}
			}
		});
	});
/*----------------*/
	$('.left').find('input,textarea').css({border:'1px dashed #BBB',padding:'3px'});
	$('.hints').hide();
	$('.part1').find('input').each(function(){
		$(this).blur(function(){
			if($(this).val() === ''){
				$('.hints').show();
			} else {
				$('.hints').hide();
			}
		});
		
	});
/*----------------*/
	$('#changeView').click(function(){
		var el = '<input type="file" id="fileToUpload" name="topicsView" />';
		$(this).css('display','none');
		$(this).parent().append(el);
	});
		
/*----------------*/
	$('#showBtn').find('input').click(function(){
		$('#editorBlock').css('display','block');       
		$('#showBtn').css('display','none');
	});

	$('#editDone').click(function(){
		$('#editorBlock').css('display','none');   
		$('#showBtn').css('display','block');
	});
	
	var topicHeader = $('#headerFile').val();
	$('#headerFile').change(function(){
		topicHeader = $('#headerFile').val();
	});
/*----------------*/	//内容提交
	$('#actionBar').find('input').click(function(){
		$('#topicsData').ajaxSubmit({
			data : {
				th: topicHeader
			},
			type : 'post',
			dataType : 'json',
			success : function(r) {
				 (r.st) ? 
						art.dialog({
								lock:true,
								content:':) <br/> Success. ',
								time:2000
							}) 
						: 
						art.dialog({
								lock:true,
								content:':( <br/> Oops! Something unexpected happend!',
								time:3000
							}); 
			}
		});
		return false;
	});
//-----编辑时：
	var tid = $('#typeId').attr('tid');	
	$('#actionBarForEdit').find('input').click(function(){
		$('#topicsData').ajaxSubmit({
			data : {
                oldView:$('.oldView').html(),
				th: topicHeader,
				update:true //编辑更新时使用
			},
			type : 'post',
			dataType : 'json',
			success : function(r) {
				 (r.st) ? 
						art.dialog({
								lock:true,
								content:':) <br/> Success. ',
								time:2000
							}) 
						: 
						art.dialog({
								lock:true,
								content:':( <br/> Oops! Something unexpected happend!',
								time:3000
							}); 
			}
		});
		return false;
	});
/*----------------*/
    // 批量操作
    $('#doSel').click(function(){
       var item = [],id = '',idstr = '';
       var __MAKEURL__ = $('input[type="hidden"]').eq(0).val();
       var es = $('#ifrm').contents().find('input:checked').not('#selAllBtn');
       es.each(function(i){
            id = $(this).parent().next().html();
            item.push(id);
       }); 
       idstr = item.join(',');
       $.ajax({
            url : __MAKEURL__,
            type : 'post',
            data : {item:idstr},
            dataType : 'json',
            success : function(e) {
                            var eli='',html='';
                            for(i in e){
                                if(i == 'st') continue;
                                var url = 'http://wine.cn/html/'+e[i]["htmldir"]+'/'+e[i]["article_id"]+'.html';
                                var img = e[i]['pic'];
                                img ? img = 'src="http://wine.cn/images/'+img+'"' : img = '暂无';
                                eli = $('<tr><td>'+e[i]["article_id"]+'</td><td>'+url+'</td><td>'+img+'</td></tr>');
                                $('#urlContainer').append(eli);
                            }
                        }
       });
       $('#urlList').css('display','block');
       return;
    }); 
/*----------------*/
    $('#so').click(function(){
        $('#ifrm').attr('src',$('input[type="hidden"]').eq(1).val()+'&sort='+$('#sorts').val());
    });
/*----------------*/   
    $('.expand').click(function(){
    	$('#topicImg').slideDown('fast',function(){
    		$('.expand').html('');
    	});
    });
    $('.fold').click(function(){
    	$('#topicImg').slideUp('fast',function(){
    		$('.expand').html('展开...');
    	});
    });
/*---------------*/
    $('.delBtn').live('click', function(){
    	var that = $(this);
    	var fileName = that.siblings('a').text();
    	var dirnm = $('input[name="dirnm"]').val();
    	var pgnm = $('input[name="pgnm"]').val();
    	var url = that.siblings('input[type="hidden"]').val();
    	var c = confirm('确定删除？');
    	if(c){
    		$.ajax({
    			url : url,
    			data :{dirnm:dirnm,pgnm:pgnm},
    			type : 'post',
    			dataType : 'text',
    			success : function(i){
    				if(i){
    					that.parent().remove();
    				}else{
    					alert('删除错误！');
    				}
    			}
    		});
    	}
    	return;
    });
    $('#fieldList').click(function(){
    	$('#fieldShow').toggle();
    });
});
