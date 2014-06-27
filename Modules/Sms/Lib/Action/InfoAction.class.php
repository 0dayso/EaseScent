<?php

class InfoAction extends CommonAction
{
    public function index()
    {
        $clients = D('Client')->getClientList();
        $platforms = D('Platform')->getList();
        

        foreach ($clients as $k=>$v) {
            $clients[$k]['sms_total'] = D('Msgs')->countSmsTotal($v['id']);
            $clients[$k]['sms_success'] = D('Msgs')->countSmsSuccess($v['id']);
            $clients[$k]['sms_failed'] = D('Msgs')->countSmsFailed($v['id']);
         
            $clients[$k]['percent_success'] = (int)$clients[$k]['sms_success'] / (int)$clients[$k]['sms_total'] * 100;
            $clients[$k]['percent_failed'] = (int)$clients[$k]['sms_failed'] / (int)$clients[$k]['sms_total'] * 100;
        }
        
        foreach ($platforms as $k=>$v) {
            $platforms[$k]['percent'] = (int)$v['sms_success'] / (int)$v['sms_total'] * 100;
        }

        //return $nlist;
        $this->assign('clients', $clients);
        $this->assign('platforms', $platforms);
        $this->display();
    }
}
