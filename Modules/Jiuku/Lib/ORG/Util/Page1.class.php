<?php
class Page1
{

    // 起始行数
    public $firstRow	;
    // 列表每页显示行数
    public $listRows	;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 分页总页面数
    public $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    public $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页栏每页显示的页数
    protected $rollPage   ;
	// 分页显示定制
    protected $config = array(
    'theme1'=>'<span><em id="list-count-num">%totalRow%</em> %header% %nowPage%/%totalPage% 页</span> %upPage%%downPage%%first%%prePage%%linkPage%%nextPage%%end%',
    'theme'=>' %first% %prev% %linkPage% %next% %last% '
    );

    /**
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows,$listRows,$parameter='') {
        $this->totalRows = $totalRows;
        $this->parameter = $parameter;

        // Show page nums count.
        $this->rollPage = 5;

        // The count of the row each page.
        $this->listRows = !empty($listRows) ? $listRows : 20;
        $this->totalPages = ceil($this->totalRows/$this->listRows);     //总页数
        $this->totalPages = $this->totalPages ? $this->totalPages : 1;
        $this->coolPages  = ceil($this->totalPages/$this->rollPage);
        $this->nowPage  = !empty($_GET['page'])?$_GET['page']:1;

        if($this->totalPages < $this->nowPage)
            die(json_encode(RST('', ERROR_PAGE, "error page")));
        if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage = $this->totalPages;
        }
        $this->firstRow = $this->listRows*($this->nowPage-1);
    }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    /**
     * 分页显示输出
     */
    public function pageHtml() {
        if(0 == $this->totalRows) return '';
        $p = 'p';
        $nowCoolPage      = ceil($this->nowPage/$this->rollPage);
        $url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
        $parse = parse_url($url);
        if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
            unset($params[$p]);
            $url   =  $parse['path'].'?'.http_build_query($params);
        }
        //上下翻页字符串
        $upRow   = $this->nowPage-1;
        $downRow = $this->nowPage+1;
        $theEndRow = $this->totalPages;
        if ($upRow > 0){
			$theFirst = '<li class="sBtn first"><span style="cursor:pointer;" onclick="javascript:gotopage(\'1\')">&nbsp;&nbsp;</span></li>';
			$prePage = '<li class="sBtn previous"><span style="cursor:pointer;" onclick="javascript:gotopage(\''.$upRow.'\');">&nbsp;&nbsp;</span></li>';
		}else{
			$theFirst = '<li class="sBtn nofirst"><span>&nbsp;&nbsp;</span></li>';
            $prePage='<li class="sBtn noprevious"><span href="javascript:;">&nbsp;&nbsp;</span></li>';
        }

        if ($downRow <= $this->totalPages){
			$theEnd = '<li class="sBtn last"><span style="cursor:pointer;" onclick="javascript:gotopage(\''.$theEndRow.'\')">&nbsp;&nbsp;</span></li>';
			$downPage = '<li class="sBtn next"><span style="cursor:pointer;" onclick="javascript:gotopage(\''.$downRow.'\');">&nbsp;&nbsp;</span></li>';
		}else{
			$theEnd = '<li class="sBtn nolast"><span">&nbsp;&nbsp;</span></li>';
            $downPage= '<li class="sBtn nonext"><span href="javascript:;">&nbsp;&nbsp;</span></li>';
        }
        // << < > >>
        /*
        if($nowCoolPage == 1){
			$theFirst = '<li class="sBtn first"><span style="cursor:pointer;" onclick="javascript:gotopage(\'1\')">&nbsp;</span></li>';
            $prePage = $this->config['prev'];
        }else{
            $preRow =  $this->nowPage-1;
			$pPage = '<li class="sBtn previous"><span style="cursor:pointer;" onclick="javascript:gotopage(\''.$preRow.'\')">&nbsp;</span></li>';
			$theFirst = '<li class="sBtn first"><span style="cursor:pointer;" onclick="javascript:gotopage(\'1\')">&nbsp;</span></li>';

		}
        if($nowCoolPage == $this->coolPages){
            $nextPage = $this->config['next'];
            $theEnd= $this->config['last'];
        }else{
            $nextRow = $this->nowPage + 1;
            $theEndRow = $this->totalPages;
			$nextPage = '<li class="sBtn next"><span style="cursor:pointer;" onclick="javascript:gotopage(\''.$nextRow.'\')">&nbsp;</span></li>';
			$theEnd = '<li class="sBtn last"><span style="cursor:pointer;" onclick="javascript:gotopage(\''.$theEndRow.'\')">&nbsp;</span></li>';
		}
        */
        // 1 2 3 4 5
        // 1 ... 7 8 9 10 11 ... 20
        // 1 ... 16 17 18 19 20
        $linkPage = '';
        
        if($this->totalPages <= $this->rollPage){
            for($i = 1; $i <= $this->totalPages; $i++){
                if($i == $this->nowPage){
                    $linkPage .= '<li class="active"><span>'.$i.'</span></li>';
                }else{
                    $linkPage .= '<li><span style="cursor:pointer;" onclick="javascript:gotopage(\''.$i.'\')">'.$i.'</span></li>';
                }
            }
        
        }else{
            if($this->nowPage < $this->rollPage){
                for($i = 1; $i <= $this->rollPage + 1; $i++){
                    if($i == $this->nowPage){
                        $linkPage .= '<li class="active"><span>'.$i.'</span></li>';
                    }else{
                        $linkPage .= '<li><span style="cursor:pointer;" onclick="javascript:gotopage(\''.$i.'\')">'.$i.'</span></li>';
                    }  
                }    
                if($this->rollPage + 1 != $this->totalPages){
                    $linkPage .= '<li><span>..</span></li>'; 
                    $linkPage .= '<li><span style="cursor:pointer;" onclick="javascript:gotopage(\''.$this->totalPages.'\')">'.$this->totalPages.'</span></li>'; 
                }            
            }elseif($this->nowPage >= ($this->totalPages - ($this->rollPage + 1) / 2 )){
                
                if($this->totalPages != $this->rollPage + 1){
                    $linkPage .= '<li><span style="cursor:pointer;" onclick="javascript:gotopage(\'1\')">1</span></li>';
                    $linkPage .= '<li><span>..</span></li>'; 
                }else{
                    $linkPage .= '<li><span style="cursor:pointer;" onclick="javascript:gotopage(\'1\')">1</span></li>';
                }
                if($this->nowPage > ($this->totalPages - 2))
                    $sPage = $this->rollPage - 1;
                else
                    $sPage = ($this->rollPage - 1)/2;
                for($i = $this->nowPage - $sPage ; $i <= $this->totalPages ; $i++){
                    if($i == $this->nowPage){
                        $linkPage .= '<li class="active"><span>'.$i.'</span></li>';
                    }else{
                        $linkPage .= '<li><span style="cursor:pointer;" onclick="javascript:gotopage(\''.$i.'\')">'.$i.'</span></li>';
                    }  
                }    
            }else{

                $linkPage .= '<li><span style="cursor:pointer;" onclick="javascript:gotopage(\'1\')">1</span></li>';
                $linkPage .= '<li><span>..</span></li>'; 
                for($i = $this->nowPage - 2 ; $i <= $this->nowPage + 2 ; $i++){
                    if($i == $this->nowPage){
                        $linkPage .= '<li class="active"><span>'.$i.'</span></li>';
                    }else{
                        $linkPage .= '<li><span style="cursor:pointer;" onclick="javascript:gotopage(\''.$i.'\')">'.$i.'</span></li>';
                    }
                }
                $linkPage .= '<li><span>..</span></li>';
                $linkPage .= '<li><span style="cursor:pointer;" onclick="javascript:gotopage(\''.$this->totalPages.'\')">'.$this->totalPages.'</span></li>';
            }

        }
        
       // var_dump($this->totalPages);
       // var_dump($linkPage);exit;
        /*
        if($this->nowPage == 1)
            $linkPage = '<li class="active"><span)">1</span></li>';
        else
            $linkPage = '<li><span style="cursor:pointer;" onclick="javascript:gotopage(\'1\')">1</span></li>';

        for($i=1;$i<=$this->rollPage;$i++){
            $page=($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if($page<=$this->totalPages){
					$linkPage .= '<li><span style="cursor:pointer;" onclick="javascript:gotopage(\''.$page.'\')">'.$page.'</span></li>';
				}else{
                    break;
                }
            }else{
                if($this->totalPages != 1){
                    //$linkPage .= "<span class='current'>".$page."</span>";
					$linkPage .= '<li class="active"><span>'.$page.'</span></li>';

				}
            }
        }
        if($this->nowPage == $theEndRow)
            $linkPage .= '<li class="active"><span>'.$page.'</span></li>';
        else
            $linkPage .= '<li><span style="cursor:pointer;" onclick="javascript:gotopage(\''.$theEndRow.'\')">'.$theEndRow.'</span></li>';
        */
        $pageStr = str_replace(
            array('%prev%','%first%','%linkPage%','%next%','%last%'),
            array($prePage,$theFirst,$linkPage,$downPage,$theEnd),$this->config['theme']);
        return $pageStr;
    }

}
?>
