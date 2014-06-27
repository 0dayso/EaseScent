<?php
/**
 * 关键词过滤
 * author david
 */
class FiltrateWordsAction extends Action
{	
	function getWords(){
		header("Content-Type:text/html; charset=utf-8");
		$o = D("WordsMg");
		$words = $o->where("status=1")->select();
		foreach ($words as $k=>$v){
			$ws[] = $v["wd"]; 
		}
		return $ws; 
	}
	/**
	 * Enter description here...
	 *
	 * @param string $content
	 * @return unknown
	 */
	function filtrate($content){
		$word = &$this->getWords();
		$st = count($word);
		if($st != 0){
			foreach($word as $k=>$v){
				if(preg_match("/".$v."/i",$content,$matches)){
						return false;
				}
			}
		}
		return true;
		
	}
	/**
	 * Enter description here...
	 *提供测试
	 */
	function test(){
		$this->filtrate("ww",1);
	}
}
?>