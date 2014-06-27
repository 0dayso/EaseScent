var upblic = 'http://www.wine.com/public';
function first(o){
    var parent = o.parentNode.parentNode;
    parent.parentNode.insertBefore(o.parentNode.parentNode,parent.parentNode.firstChild);
}

function delImg(id, obj){
    cur_img_num--;
    console.log(cur_img_num);
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


 // 发布微博图片上传
 init_uploadify = function(){
        $(function(){
            $('#file_uploadify').uploadify({
                'swf'             : +upblic+'/Expo/uploadify/uploadify.swf',
                'uploader'        : +upblic+'/Expo/uploadify/uploadify.php',
                'buttonImage'     : +upblic+'/Expo/uploadify/button-upload.gif',
                'height'          : 110,
                'width'           : 110,
                'auto'			   : true,
                'buttonCursor'    : 'hand',
                'fileTypeDesc' : 'Image Files',
                'fileTypeExts' : '*.gif; *.jpg; *.png',
                'removeCompleted' : true,
                'fileSizeLimit' : '2048KB',
                'onUploadError'		  : function (file, errorCode, errorMsg, errorString){
                alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
                },
                'onUploadSuccess' : function(file, data, response){
                data = eval("("+ data +")");
                if (data.error === 0) {
                /**
                 * @brief  计算图片长宽比例
                 */
                    var img_width = '', img_height = '';
                    if (data.width > data.height){
                        var img_size_html = 'width="100%"';
                    } else {
                        var img_size_html = 'height="100%"';
                    }

                if(cur_img_num < 4){
                    cur_img_num++;
                    //console.log(cur_img_num);

                    var html ='<li><input name="img_file[]" type="hidden" value="'+data.filename+'" />'
                            +'<input name="img_queue[]" class="img_queue"type="hidden" value="'+cur_img_num+'" />'
                            +'<img src="'+data.url+'" '+img_size_html+' /> '
                            +'<p class="pic_closeBtn"><img src="upblic/Expo/img/pic_closeBtn.gif" onclick="delImg(-1,this)" /></p>'
                            +'<a href="#" onclick="first(this)"></a></li>';

                    $("#file_uploadify").parent().before(html);

                    if (cur_img_num === 4) {
                        $('#file_uploadify').uploadify('stop');
                        $('#file_uploadify').uploadify('destroy');
                        $(".upLoadPicUl li").last()
                        .html('<a href="javascript:;" id="file_uploadify"></a>')
                        .hide();
                    }
                }

                }
                }

            });
        });
}