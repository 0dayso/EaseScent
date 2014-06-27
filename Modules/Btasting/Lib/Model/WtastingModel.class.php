<?php

class WtastingModel {

    /**
     * 计算分数
     */
    public function dealScore($id) {
        $wine = M('wines')->where(array('id' => $id))->find();
        //求平均分
        $score = M('score')->where(array('wineid' => $id))->select();
        if(is_array($score)) {
            $max = 0;
            $min = 100;
            $totscore = 0;
            foreach($score as $key => $val) {
                if($val['score'] > $max) $max = $val['score'];
                if($val['score'] < $min) $min = $val['score'];
                $totscore += $val['score'];
            }
            $count = count($score) - 2;
            if($count) {
                $avgscore = ceil(($totscore - $max - $min)/$count);
                M('wines')->where(array('id' => $id))->save(array('score' => $avgscore));
            }
        }
        //分组排名
        $gwine = M('wines')->where(array('gid' => $wine['gid']))->order('`score` DESC')->select();
        if(is_array($gwine)) {
            foreach($gwine as $key => $gval) {
                M('wines')->where(array('id' => $gval['id']))->save(array('gorder' => $key + 1));
            }
        }
        //更新每款酒的数据统计
        $wp = M('wineparty')->where(array('id' => $wine['wid']))->find();
        $exps = $this->experts($wp['experts']);
        $score = M('score')->where(array('wineid' => $id))->select();
        $expNum = count($exps);
        $scoNum = count($score);
        if($expNum) {
            $statement = ceil(($scoNum/$expNum)*100);
            $statement = $statement > 100 ? 100 : $statement;
            M('wines')->where(array('id' => $id))->save(array('statement' => $statement));
        }
        //全部排名
        $wpwine = M('wines')->where(array('wid' => $wine['wid']))->order('`score` DESC')->select();
        if(is_array($wpwine) && !empty($wpwine)) {
            $statements = 0;
            foreach($wpwine as $key => $wpval) {
                M('wines')->where(array('id' => $wpval['id']))->save(array('order' => $key + 1));
                $statements += $wpval['statement'];
            }
            //增加酒会数据统计
            $count = count($wpwine);
            $finish = ceil($statements/$count);
            $finish = $finish > 100 ? 100 : $finish;
            M('wineparty')->where(array('id' => $wine['wid']))->save(array('finish' => $finish));
        }
    }

    public function experts($ids) {
        if(!empty($ids)) {
            $ids = trim($ids, '|');
            $experts = M("expert")->where(" `id` IN (".str_replace("||",",", $ids).")")->select();
            return $experts;
        }
        return array();
    }
}
