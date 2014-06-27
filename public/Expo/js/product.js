
                function mysubmit(myform,id){
                    if(!check_winename()){
                        $("#require").html('酒款名不能为空');
                    }else if(cur_img_num==0){
                        $("#require").html('请至少上传一张图片');
                    }else if(!check_price()){
                         $("#require").html('代理价格为空，或者格式不正确');
                    }else if(!check_winetype()){
                       var ptt = $("#typeList1").val();
                        if(ptt=="other"){
                            var pttv = $.trim($("#ktypetext").val());
                            if(pttv==""){
                                $("#require").html('请填写葡萄酒类型');
                            }
                        }else{
                            $("#require").html('请选择葡萄酒类型');
                        }
                    }else if(!check_winegrape()){
                        $("#require").html('请至少填写一个葡萄品种');
                    }else if(!check_winecountry()){
                       var country = $.trim($("#countryList").val());
                        if(country=="other"){
                            var cct =  $.trim($("#kcountrytext").val());
                            if(cct==""){
                                $("#require").html('请填写国家');
                                return false;
                            }
                        }else{
                            $("#require").html('请选择国家');
                        }
                    }else if(!check_wineregion()){
                        $("#require").html('请选择产区');
                    }else if(!check_input_region()){
                         $("#require").html('请输入产区');
                    }else if(!check_wineintroduction()){
                        $("#require").html('请填写酒款介绍');
                    }else{
                        $('#parameter').val(id);//关联酒款放入表单
                        javascript:document.getElementById('myform').submit();
                    }
                }
                               



            $(document).ready(function() {
                    wineNameSearch();   //api 加载keyword 相关酒名称
                    init_uploadify();   //加载upload img
                    getWineType();      //获取酒的类型
                    getCountry();       //获取国家     



                 


             

                    

          


                    //一级产区取消关联
                    $('#kregion > i').live('click',function(){
                        var a = $('#kjcountrytext').val();
                        var strindex = a.indexOf("|", 0);
                        var countryid =a.substring(0,strindex);
                        $.post(Jk_Api_getRegion_Url, {country_id:countryid},
                         function(data){
                                $('#kcountrytext').hide();
                                $('#kregiontext').hide();
                                $("#regionList1").empty();
                                $("#regionList1").append("<option value='chose'>请选择</option>");
                                for(var i in data){
                                    if(data[i].cname !=""){
                                         var option = $("<option>").text(data[i].cname).val(data[i].id + '|' + data[i].fname+'|'+data[i].cname);
                                         $("#regionList1").append(option); 
                                    }else{
                                         var option = $("<option>").text(data[i].fname).val(data[i].id + '|' + data[i].fname+'|'+data[i].cname);
                                         $("#regionList1").append(option);
                                    }
                                  
                                }
                                $("#regionList1").append("<option value='other'>其它</option>");
                            },'json');
                        $('#keregiontext').val("");//产区文本提交内容清空
                        $('#kjregiontext').val("");//产区文本提交内容清空
                        $('#regionList1').show();
                        $('#kregion').hide();
                     });


                    //根据一级产区获取二级产区列表信息
                    $("#regionList1").change(function(){
                        $('#kregiontext').hide();
                        if($('#regionList1').val()!='other'&&$('#regionList1').val()!='chose'){
                            //获取选中的文本值赋值到隐藏框中提交
                            var a = $('#regionList1').find('option:selected').val();
                            $('#kjregiontext').val("");
                            $('#regionname1').val(a);
                            var strindex = a.indexOf("|", 0);
                            var pid =a.substring(0,strindex);
                            $.post(Jk_Api_getRegion_Url, {pid:pid},
                            function(data){
                             
                                if(data != null){
                                $('#kcountrytext').hide();
                                $('#kregiontext').hide();
                                $("#regionList2").show();
                                $("#regionList2").empty();
                                $("#regionList2").append("<option value='chose'>请选择</option>");
                                for(var i in data){
                                     if(data[i].cname !=""){ //判断中文名是否为空
                                    var option = $("<option>").text(data[i].cname).val(data[i].id + '|' + data[i].fname+'|'+data[i].cname);
                                    $("#regionList2").append(option);
                                     }else{
                                     var option = $("<option>").text(data[i].fname).val(data[i].id + '|' + data[i].fname+'|'+data[i].cname);
                                    $("#regionList2").append(option);  
                                     }
                                }
                                }
                            },'json');
                        }
                    });


                    //获取二级产区列表信息
                    $("#regionList2").change(function(){
                        //获取选中的文本值赋值到隐藏框中提交
                        var a = $('#regionList2').find('option:selected').val();
                        $('#regionname2').val(a);
                    });

                    //国家框选择其它                    
                    $("#countryList").change(function(){
                        if($("#countryList").val()=='other'){
                            $("#kjcountrytext").val("");
                            $("#countryname").val("");
                            $("#regionList1").empty();//将产区select框至空
                            $("#regionList1").append("<option value='other'>其它</option>");
                            $("#regionList2").hide();
                            $('#kcountrytext').show().val("");
                            $('#kregiontext').show().val("");
                            $("#kjregiontext").val("");//将文本框内容清空
                            $("#regionname1").val("");
                        }
                    });


                    //国家框选择请选择
                    $("#countryList").change(function(){
                        if($("#countryList").val()=='chose'){
                            $("#kjcountrytext").val("");
                            $("#countryname").val("");
                            $("#regionList1").empty();//将产区select框至空
                            $("#regionList1").append("<option value='chose'>请选择</option>");
                            $("#regionList2").hide();
                            $("#kjregiontext").val("");//将文本框内容清空
                            $("#regionname1").val("");
                        }
                    });

                    //产区框选择其它
                    $("#regionList1").change(function(){
                        if($("#regionList1").val()=='other'){
                            $("#kjregiontext").val("");//将文本框内容清空
                            $("#regionname1").val("");
                            $("#regionname2").val("");
                            $("#regionList2").hide();
                            $('#kregiontext').show().val("");
                        }
                    });

                    //类型框选择其它
                    $("#typeList1").change(function(){
                        if($("#typeList1").val()=='other'){
                            $("#ktypet").val("");//将文本框内容清空
                            $("#typename1").val("");
                            $("#typename2").val("");
                            $('#kjtypetext').val("");//将级联类型框空置
                            $("#typeList2").hide();
                            $('#ktypetext').show().val("");
                        }else{
                            $('#ktypetext').hide().val("");
                        }
                    });

             });


              $(function(){
                    $('#z_aWrap_r_showBox_Inp').focusout(function(){
                        var t = setTimeout(function(){
                            $(".z_aWrap_r_showBox").hide();
                        },1000);     
                    });
                });





var cur_img_num = 0;
function first(o){
    var parent = o.parentNode.parentNode;
    parent.parentNode.insertBefore(o.parentNode.parentNode,parent.parentNode.firstChild);
}

function delImg(id, obj){
    cur_img_num--;
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



/*根据酒的名称 请求 API 查询相关的酒*/
function wineNameSearch(){

    //下拉框信息获取
    $("#z_aWrap_r_showBox_Inp").keyup(function(e){

        $.post(Jk_Api_getData_Url, {keyword:$("#z_aWrap_r_showBox_Inp").val()},
        function(data){                         
            $(".z_aWrap_r_showBox").html("");
            for(var i in data){
                var html ='<a id='+data[i].id+' title="'+data[i].fname+'/'+data[i].cname+'" href="javascript:;"><span>'+data[i].fname+'</span>/'+data[i].cname+'</a>';
                $(".z_aWrap_r_showBox").append(html);
            }

            $(".z_aWrap_r_showBox >a").click(function(){
                var a_text=$(this).html();  
                $('#z_aWrap_r_showBox_Inp').hide();     //隐藏search keyword
                $('.z_aWrap_r_showBox').hide();         //关闭列表
               
                $('.z_aWrap_r_showBox_p').show().html(a_text+'<i><img src="'+Public+'/Expo/img/z_aWrapCloseBtn20x20.png"/></i>');  //内容填充到酒款框

                //通过id获取酒款详情
                var id = $(this).attr("id");

                $.post(Jk_Api_getFullData_Url,{id:id},function(data){                    
                    //酒款框关联处理
                    //酒款内容填充到表单提交
                    var winetext = ''+data.id+','+data.fname+','+data.cname+'';
                    $('#kjwinetext').val(winetext);                //可以删除-----
                    //$('#z_aWrap_r_showBox_Inp').val(winetext);     //给keyword search wine_name 填充

                    //国家框关联处理
                    if (data.countryfname || data.countrycname ){
                        $('.country').hide();//原国家框隐藏
                        $('#kcountrytext').hide();//原国家框隐藏
                        $('#countryList').hide();//原国家框隐藏
                        var country = '<span>'+data.countryfname+'</span>'+'('+data.countrycname+')';

                        //内容填充到国家框
                        $('#kcountry').show().html(country+'<i><img src="'+Public+'/Expo/img/z_aWrapCloseBtn20x20.png"/></i>');
                        //内容填充到表单提交
                        var countrtytext = ''+data.countryid+','+data.countryfname+','+data.countrycname+'';
                        $('#kjcountrytext').val(countrtytext);
                    }
                    //类型框关联处理
                    if (data.winetype){
                        $('#type').hide();//原类型框隐藏
                        $('#typeList1').hide();//原类型框隐藏
                        $('#typeList2').hide();//原类型框隐藏
                        var winetype = "";
                        for(var i in data.winetype){
                            winetype += '<span>'+data.winetype[i].fname+'</span>'+'('+data.winetype[i].cname+')'+'/';
                        }
                        //内容填充到类型框
                        $('#ktype').show().html(winetype+'<i><img src="'+Public+'/Expo/img/z_aWrapCloseBtn20x20.png"/></i>');

                        //内容填充到表单提交
                        var typetext = "";
                        for(var i in data.winetype){
                            typetext += ''+data.winetype[i].id+','+data.winetype[i].fname+','+data.winetype[i].cname+'|';
                        }
                        $('#kjtypetext').val(typetext);
                    }
                    //产区框关联处理
                    if (data.region ){
                        //$('#region').hide();//原类型框隐藏
                        $('#kregiontext').hide();
                        $('#regionList1').hide();//原类型框隐藏
                        $('#regionList2').hide();//原类型框隐藏
                        var region = "";
                        for(var i in data.region){
                            region += '<span>'+data.region[i].fname+'</span>'+'('+data.region[i].cname+')'+'/';
                        }
                        //内容填充到关联框
                        $('#kregion').show().html(region+'<i><img src="'+Public+'/Expo/img/z_aWrapCloseBtn20x20.png"/></i>');

                        //内容填充到表单提交
                        var regiontext = "";
                        for(var i in data.region){
                            regiontext += ''+data.region[i].id+','+data.region[i].fname+','+data.region[i].cname+'|';
                        }
                        $('#kjregiontext').val(regiontext);

                    }
                    //品种比例关联处理
                    if (data.grape){
                        $('#percent').hide();//原类型框隐藏
                        var type = "";
                        for(var i in data.grape){
                            type += ''+data.grape[i].cname+':'+data.grape[i].percent+'%'+';';
                        }
                        //内容填充到品种比例框
                        $('#kpercent').show().html(type+'<i><img src="'+Public+'/Expo/img/z_aWrapCloseBtn20x20.png"/></i>');
                  
                        //内容填充到表单提交
                        var grapetext = "";
                        for(var i in data.grape){
                            grapetext += ''+data.grape[i].fname+'/'+data.grape[i].cname+':'+data.grape[i].percent+'%';
                        }
                        $('#kjgrapetext').val(grapetext);
                    }

                },'json');
            });
       
      
        }, "json")

    });

}





//获取酒品的；类型
 function getWineType (){

            //获取一级红酒类型列表信息
            $.post(Jk_Api_getType_Url, {pid:""},function(data){
                //$("#typeList1").append("<option value='chose'>请选sss择</option>");
                for(var i in data){
                    var option = $("<option>").text(data[i].cname).val(data[i].id + '|' + data[i].fname+'|'+data[i].cname);
                    $("#typeList1").append(option);
                }
                $("#typeList1").append("<option value='other'>其它</option>");

            },'json');


            //当被选择后 获取二级红酒类型列表信息
            $("#typeList1").change(function(){
                //$("#typeList2").hide();
                if($('#typeList1').val()!='other'&&$('#typeList1').val()!=''){
                    //获取选中的文本值赋值到隐藏框中提交
                    var a = $('#typeList1').find('option:selected').val();

                   $('#typename1').val(a);   //可以删除
                   $('#kjtypetext').val(""); //将级联类型框空置
                   $('#ktypetext').val("");  //将级联类型框空置
                   
                    $.post(Jk_Api_getType_Url, {pid:$("#typeList1").val()},
                    function(data){
                        $("#typeList2").empty();
                        $("#typeList2").show();
                        $("#typeList2").append("<option value=''>请选择</option>");
                        for(var i in data){
                            var option = $("<option>").text(data[i].cname).val(data[i].id + '|' + data[i].fname+'|'+data[i].cname);
                            $("#typeList2").append(option);
                        }
                    },'json');}
            });


            //获取二级类型列表信息
            $("#typeList2").change(function(){
                //获取选中的文本值赋值到隐藏框中提交
                var a = $('#typeList2').find('option:selected').val();
                $('#typename2').val(a);
            });


 }




//获取国家信息
function getCountry (){    

    //获取国家列表信息
    $.post(Jk_Api_getCountry_Url, {keyword:$("#z_aWrap_r_showBox_Inp").val()},function(data){                        
        //$("#countryList").append("<option value=''>请选择</option>");
        for(var i in data){
            var option = $("<option>").text(data[i].cname).val(data[i].id + '|' + data[i].fname+'|'+data[i].cname);
            $("#countryList").append(option);
        }
        $("#countryList").append("<option value='other'>其它</option>");
    },'json');


    //根据国家id获取产区列表信息
    $("#countryList").change(function(){
        if($('#countryList').val()!=='other'){
            //获取选中的文本值赋值到隐藏框中提交
            var a = $('#countryList').find('option:selected').val();
            $('#countryname').val(a);
            var strindex = a.indexOf("|", 0);
            var countryid =a.substring(0,strindex);
            $.post(Jk_Api_getRegion_Url, {country_id:countryid},
            function(data){
                $('#kcountrytext').hide();
                $('#kregiontext').hide();
                $("#regionList1").empty();
                $("#regionList2").empty();
                $("#regionList2").hide();
                $("#regionList1").append("<option value='chose'>请选择</option>");
                for(var i in data){
                    if(data[i].cname !=""){
                         var option = $("<option>").text(data[i].cname).val(data[i].id + '|' + data[i].fname+'|'+data[i].cname);
                         $("#regionList1").append(option); 
                    }else{
                         var option = $("<option>").text(data[i].fname).val(data[i].id + '|' + data[i].fname+'|'+data[i].cname);
                         $("#regionList1").append(option); 
                    }
                  
                }
                $("#regionList1").append("<option value='other'>其它</option>");
            },'json');
        }
    });
}





// 发布微博图片上传
 init_uploadify = function(){
        $(function(){
            $('#file_uploadify').uploadify({
                'swf'             : Public+'/Expo/uploadify/uploadify.swf',
                'uploader'        : Public+'/Expo/uploadify/uploadify.php?type=wine',
                'buttonImage'     : Public+'/Expo/uploadify/button-upload.gif',
                'height'          : 110,
                'width'           : 110,
                'auto'             : true,
                'buttonCursor'    : 'hand',
                'fileTypeDesc' : 'Image Files',
                'fileTypeExts' : '*.gif; *.jpg; *.png',
                'removeCompleted' : true,
                'fileSizeLimit' : '2048KB',
                'onUploadError'       : function (file, errorCode, errorMsg, errorString){
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
 

                    var html ='<li><input name="img_file[]" type="hidden" value="'+data.filename+'" />'
                            +'<input name="img_queue[]" class="img_queue"type="hidden" value="'+cur_img_num+'" />'
                            +'<img src="'+data.url+'" '+img_size_html+' /> '
                            +'<p class="pic_closeBtn"><img src="'+Public+'/Expo/img/pic_closeBtn.gif" onclick="delImg(-1,this)" /></p>'
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