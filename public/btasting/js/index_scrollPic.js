var iSeamlessHandover=document.getElementById('SeamlessHandover');
var iSeamlessHandover_ul=SeamlessHandover.getElementsByTagName('ul')[0];
var aSeamlessHandover_li=iSeamlessHandover_ul.getElementsByTagName('li');
var iLiW=aSeamlessHandover_li[0].offsetWidth+5;
var bBtn=true;
getWidth()
//设置ul宽度
function getWidth()
{
	iSeamlessHandover_ul.style.width=aSeamlessHandover_li.length*iLiW+'px';	
}

var iNum=2;//当前一次循环的个数
	
function toMove(direction)
{
	if(bBtn)
	{
		bBtn=false;
		if(direction=='l')
		{
			for(var i=0; i<iNum ;i++){
				var aNewLi=aSeamlessHandover_li[i].cloneNode(true);
				iSeamlessHandover_ul.appendChild(aNewLi);
				getWidth();
			}
			
			stratMove(iSeamlessHandover_ul,{left:-iNum*iLiW},function(){
				for(var i=0;i<iNum;i++){
					iSeamlessHandover_ul.removeChild(aSeamlessHandover_li[0]);
				}
				iSeamlessHandover_ul.style.left=0;
				bBtn=true;
			});		
		}else{
			for(var i=0; i<iNum ;i++){
				var aAllNum=aSeamlessHandover_li.length;
				var aNewLi=aSeamlessHandover_li[aAllNum-1-i].cloneNode(true);
				iSeamlessHandover_ul.insertBefore(aNewLi,aSeamlessHandover_li[0]);
				iSeamlessHandover_ul.style.left=-iLiW*iNum+'px';
				getWidth();
			}
			
			stratMove(iSeamlessHandover_ul,{left:0},function(){
				for(var i=0;i<iNum;i++){
					iSeamlessHandover_ul.removeChild(aSeamlessHandover_li[aAllNum-i]);
				}
				iSeamlessHandover_ul.style.left=0;
				bBtn=true;
			});	
		}
	}	
}
