<?php
if(!empty($_GET["sid"]))
{
	if(!empty($_GET['goto']))
	{
		header("Location:/index.php/Uc/register/sid/".$_GET["sid"]."/mode/2/goto/".$_GET['goto']);
	}
	else
	{
		header("Location:/index.php/Uc/register/sid/".$_GET["sid"]."/mode/1");
	}
}else{
	if(!empty($_GET['goto'])){
		$goto = $_GET["goto"];
		$goto = "http://".str_ireplace("-","/",$goto);
		header("Location:".$goto);
	}
}
?>