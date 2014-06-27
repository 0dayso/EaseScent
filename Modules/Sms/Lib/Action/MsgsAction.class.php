<?php

class MsgsAction extends CommonAction
{

   public function index()
   {
        $model = D('Msgs');
        $map = ' 1 ';
        $url = '';
        $list = $this->_list($model, $map, 14, $url);
        foreach ($list as $k =>$v ) {
            $list[$k]['content'] = mb_substr($v['content'], 0, 50, 'utf-8'); 
            $list[$k]['tel_list'] =  mb_substr($v['tel_list'], 0, 50, 'utf-8'); 
        }
        $this->assign('list', $list);
        $clients = D('Client')->getClientList();
        $nlist = array();
        foreach ($clients as $key => $val) {
            $nlist[$val['id']] = $val;
        }
        
        //return $nlist;
        $this->assign('list', $list);
        $this->assign('clients', $nlist);
        $this->display();
 
   }
}
