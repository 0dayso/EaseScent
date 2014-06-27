var iSeamlessHandover2=document.getElementById('SeamlessHandover2');
var iSeamlessHandover2_ul=iSeamlessHandover2.getElementsByTagName('ul')[0];
var aSeamlessHandover_li2=iSeamlessHandover2_ul.getElementsByTagName('li');
var iLiW2=aSeamlessHandover_li2[0].offsetWidth;
var bBtn2=true;
getWidth2()
//设置ul宽度
function getWidth2()
{
	iSeamlessHandover2_ul.style.width=aSeamlessHandover_li2.length*iLiW2+'px';	
}

var iNum2=1;//当前一次循环的个数
	
function toMove2(direction)
{
	if(bBtn2)
	{
		bBtn2=false;
		if(direction=='l')
		{
			for(var i=0; i<iNum2 ;i++){
				var aNewLi=aSeamlessHandover_li2[i].cloneNode(true);
				iSeamlessHandover2_ul.appendChild(aNewLi);
				SetRemainTimeIni();
				getWidth2();
			}
			
			stratMove(iSeamlessHandover2_ul,{left:-iNum2*iLiW2},function(){
				for(var i=0;i<iNum2;i++){
					iSeamlessHandover2_ul.removeChild(aSeamlessHandover_li2[0]);
				}
				iSeamlessHandover2_ul.style.left=0;
				
				bBtn2=true;
			});		
		}else{
			for(var i=0; i<iNum2 ;i++){
				var aAllNum=aSeamlessHandover_li2.length;
				var aNewLi=aSeamlessHandover_li2[aAllNum-1-i].cloneNode(true);
				iSeamlessHandover2_ul.insertBefore(aNewLi,aSeamlessHandover_li2[0]);
				iSeamlessHandover2_ul.style.left=-iLiW2*iNum2+'px';
				SetRemainTimeIni();
				getWidth2();
			}
			
			stratMove(iSeamlessHandover2_ul,{left:0},function(){
				for(var i=0;i<iNum2;i++){
					iSeamlessHandover2_ul.removeChild(aSeamlessHandover_li2[aAllNum-i]);
				}
				iSeamlessHandover2_ul.style.left=0;
				bBtn2=true;
			});	
		}
	}	
}

function stratMove(obj,json,fn)
{
	clearInterval(obj.timer);	
	obj.timer=setInterval(function(){doMove(obj,json,fn);},30);
}
function doMove(obj,json,fn)
{
	var iCur=0;
	var attr=null;
	var dope=true;
	for(attr in json)
	{
		if(attr=="opacity")
		{
			iCur=parseInt(getStyle(obj,attr)*100);
		}
		else
		{
			iCur=parseInt(getStyle(obj,attr));
		}
		
		if(isNaN(iCur)){iCur=0;}
		
		var iSpeed=parseInt(json[attr]-iCur)/6;
		iSpeed=iSpeed>0?Math.ceil(iSpeed):Math.floor(iSpeed);
		if(iCur != json[attr])
		{
			dope=false;
		}
		if(attr=="opacity")
		{
			obj.style.opacity=(iCur+iSpeed)/100;
			obj.style.filter="alpha(opacity:"+(iCur+iSpeed)+")";
		}
		else
		{
			obj.style[attr]=iCur+iSpeed+"px";
		}
	}
	if(dope)
	{
		clearInterval(obj.timer);
		if(fn)
		{
			fn.call(obj);
		}
	}
	
}

function getClass(oParent,iClass)
{
	var arr=[];
	var aObj=oParent.getElementsByTagName('*');
	for(var i=0;i<aObj.length;i++)
	{
		if(aObj[i].className==iClass)
		{
			arr.push(aObj[i]);
		}
	}
	return arr;
}


function getStyle(obj,attr)
{
	return obj.currentStyle?obj.currentStyle[attr]:getComputedStyle(obj,false)[attr];;
}


//通用倒计时
var mark = 0;
var Obj,timer; 
var InterValObj; 
SetRemainTimeIni();
function SetRemainTimeIni(){
	Obj = $(".lasttime input[type='hidden']"); //这里获取倒计时的起始时间html的对象
	if(mark == 0){
		$.getJSON(__ajax_domain+"?action=WXpmeGY8Q0xSY3o8dHZnR3p%2Bdg%3D%3D&callback=?",function(msg){
			$.each(Obj,function(k,v){
				$(v).val(parseInt($(v).val())-parseInt(msg));
				mark = 1;
			});
		});
	}
	SetRemainTime(); //间隔函数，1秒执行 
}
//将时间减去1秒，计算天、时、分、秒 
function SetRemainTime() { 
clearInterval(timer);
timer=setInterval(function(){
	$.each(Obj,function(k,v){
		// console.log($(v).val());
		var lasttime = $(v).val();
		if (lasttime > 0) {
			lasttime = lasttime - 1; 
			var second = Math.floor(lasttime % 60);             // 计算秒     
			var minite = Math.floor((lasttime / 60) % 60);      //计算分 
			var hour = Math.floor((lasttime / 3600) % 24);      //计算小时 
			var day = Math.floor((lasttime / 3600) / 24);        //计算天 
			$(v).prev('span').html("<i>" + day + "</i>天<i>" + hour + "</i>时<i>" + minite + "</i>分<i>" + second + "</i>秒");
			$(v).val(lasttime);
		} else {
			// 剩余时间小于或等于0的时候，就停止间隔函数 
			// window.clearInterval(InterValObj); 
			//这里可以添加倒计时时间为0后需要执行的事件
			$(v).prev('span').html("活动已结束");
		}
	});
},1000)
};

/*$.getJSON(__ajax_domain+"?action=WXpmeGY8Q0xSY3o8dHZnR3p%2Bdg%3D%3D&callback=?",function(msg){
	Obj = $(".lasttime input[type='hidden']"); //这里获取倒计时的起始时间html的对象
	$.each(Obj,function(k,v){
		Obj.val(parseInt($(v).val())-parseInt(msg))
	});
});*/