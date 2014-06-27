<?php

class HtmlAction extends Action {

    public function show() {
        $wpid = intval($_GET['id']);
        $wp = M('wineparty')->where(array('id' => $wpid))->find();
        if(!$wp) {
            $this->_jump();
        }
        $adv = M('advimage')->where(array('wid' => $wpid))->select();
        if($wp['experts']) {
            $exp = array_filter($exp = explode('||', $wp['experts']));
            $exps = M('expert')->where(' id IN ('.  implode(',', $exp) .') ')->select();
        }
        $this->assign('wp', $wp);
        $this->assign('adv', $adv);
        $this->assign('exps', $exps);
        $this->display('index');
    }

    public function table() {
        $id = intval($_GET['id']);
        $wp = M('wineparty')->where(array('id' => $id))->find();
        if(!$wp) {
            $this->_jump();
        }
        $exps = explode('||', $wp['experts']);
        $exps = array_filter($exps);
        $experts = array();
        if($exps) {
            $experts = M('expert')->where("id IN(". implode(',', $exps) .")")->order('`id` ASC')->select();
        }
        $gps = M('wgroups')->where(array('wid' => $id))->select();
        $scores = M('score')->where(array('wid' => $id))->select();
        $score = array();
        foreach($scores as $sc) {
            $score[$sc['expert']][$sc['wineid']] = $sc['score'];
        }
        $groups = array();
        foreach($gps as $gval) {
            $wines = M('wines')->where(array('gid' => $gval['gid']))->select();
            foreach($wines as $key => $wine) {
                $max = 0;
                $min = 100;
                foreach($experts as $exp) {
                    $wines[$key]['sc'][$exp['mid']] = isset($score[$exp['mid']][$wine['id']]) ? $score[$exp['mid']][$wine['id']] : 0;
                    if($wines[$key]['sc'][$exp['mid']] < $min ) {
                        $min = $wines[$key]['sc'][$exp['mid']];
                        $wines[$key]['min_mid'] = $exp['mid'];
                    }
                    if($wines[$key]['sc'][$exp['mid']] > $max ) {
                        $max = $wines[$key]['sc'][$exp['mid']];
                        $wines[$key]['max_mid'] = $exp['mid'];
                    }
                }
            }
            $count = count($wines);
            if($count) {
                $groups[] = array(
                    'name' => $gval['name'],
                    'item' => $gval['item'],
                    'wines' => $wines,
                    'count' => count($wines),
                );
            }
        }
        $this->assign('experts', $experts);
        $this->assign('groups', $groups);
        $this->assign('wp', $wp);
        $this->display('table');
    }

    public function wines() {
        $id = intval($_POST['id']);
        $wine = M('wines')->where(array('id' => $id))->find();
        define('NO_API', true);
        $rst = A('Api')->getWineDetail($wine['jkid'], $wine['yid']);
        $return = array(
            'cname' => isset($rst['cname']) ? $rst['cname']: '未知酒名',
            'fname' => isset($rst['fname']) ? $rst['fname']: '',
            'year' => isset($rst['year']) ? $rst['year']: '',
            'img' => isset($rst['img']) ? $rst['img']: '',
            'type' => isset($rst['type']) ? $rst['type']: '',
            'price' => isset($wine['price']) ? $wine['price']: '',
            'agent' => isset($wine['agent']) ? $wine['agent']: '',
            'winery' => isset($rst['winery']['cname']) ? $rst['winery']['cname']: '',
            'content' => isset($rst['content']) ? strip_tags($rst['content']): '',
        );
        $return['grape'] = '';
        if(is_array($rst['grape'])) {
            foreach($rst['grape'] as $val) {
                $return['grape'] .= ' ' . $val['cname'] . $val['percentage'].'%'; 
            }
        }
        die(json_encode($return));
    }

    protected function _jump() {
        $this->display('jump');
        exit();
    }
}
