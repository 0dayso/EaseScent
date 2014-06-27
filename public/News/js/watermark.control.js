$("#btn-save").click(function(){
//Get type.
var type;
var obj=document.getElementsByName("radio-watermark-type");
if(obj!=null){
	var i;
	for(i=0;i<obj.length;i++){
		if(obj[i].checked){
			type = obj[i].value;            
		}
	}
}

//Get markstring
var markstring = $("#watermark-string").val();

//Get font size. 
var fontsize;
var obj=document.getElementsByName("radio-watermark-font");
if(obj!=null){
	var i;
	for(i=0;i<obj.length;i++){
		if(obj[i].checked){
			fontsize = obj[i].value;            
		}
	}
}

//Get watermark position.
var position;
var obj=document.getElementsByName("radio-watermark-position");
if(obj!=null){
	var i;
	for(i=0;i<obj.length;i++){
		if(obj[i].checked){
			position = obj[i].value;            
		}
	}
}

//Get watermark fontcolor.
var fontcolor = $("#watermark-font-color").val();

//Get watermark fontstyle.
var fontstyle = $("#watermark-font-style").val();

//Get watermark image
var markimage = $("#watermark-image").val();
//alert(type+markstring+fontsize+position+markimage+fontcolor+fontstyle);
	$.post('index.php?app=News&m=Water&a=saveWatermarkConfig',
			{ 
				type:type, 
				markstring:markstring, 
				fontsize:fontsize, 
				position:position, 
				markimage:markimage,
				fontcolor:fontcolor,
				fontstyle:fontstyle,
			},function(msg){
				if(msg == '1'){
					art.dialog({
		                time: 1000,
		                content: '<p class="dialog-info"><span class="emotion">:-)</span> 水印设置修改成功！</p>'
		            });
			    }else{
			    	alert("ajax-error!");
	    }
	});
});
