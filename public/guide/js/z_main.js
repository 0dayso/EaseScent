//2013-3-8 zhibao  ף��������Ůͬ���� ���տ��� ^_^��
$(function(){
	
	//�ճ����
	$('.dayEat').hover(function(){
		$('.shoppers-classification-none').show();
	},function(){
		$('.shoppers-classification-none').hide();
	});


	//�õ�Ƭ
	    var oBox=document.getElementById('flashPlayer');
		var oOl=oBox.getElementsByTagName('div')[0];
		var oUl=oBox.getElementsByTagName('ul')[0];
		var aOli=oOl.getElementsByTagName('span');
		var aUli=oUl.getElementsByTagName('li');
		var iNow=0;
		var timer=null;//�˶���ܶ�ʱ��
		var autoTimer=null;
		var i=0;
		for(i=0;i<aOli.length;i++)
		{
			aOli[i].index=i;
			aOli[i].onclick=function()
			{
				iNow=this.index;
				tab();
			}
		}
		
		
		oBox.onmouseover=function()
		{
			clearInterval(autoTimer);
		}
		
		oBox.onmouseout=function()
		{
			autoTimer=setInterval(next,3000);
		}
		
		function next()
		{
			iNow++;
			if(iNow==aOli.length)
			{
				iNow=0;
			}
			tab();
		}
		
		autoTimer=setInterval(next,3000);
		
		function tab()
		{
			for(i=0;i<aOli.length;i++)
			{
				aOli[i].className="";
			}
			aOli[iNow].className="rotation-img-current";
			stratMove(-parseInt(getStyle(aUli[0],'height'))*iNow);
		}
		
		
		function stratMove(iTarget)
		{
			clearInterval(timer);
			timer=setInterval(function(){doMove(iTarget)},30);
		}
		
		function doMove(iTraget)
		{
			if(oUl.offsetTop==iTraget)
			{
				clearInterval(timer);
			}
			else
			{
				var iSpeed=(iTraget-oUl.offsetTop)/5;
				iSpeed=iSpeed>0?Math.ceil(iSpeed):Math.floor(iSpeed);
				oUl.style.top=oUl.offsetTop+iSpeed+"px";
			}
		}

	
	
	function getStyle(obj,attr)
	{
		return obj.currentStyle?obj.currentStyle[attr]:getComputedStyle(obj,false)[attr];
	}
});