<?php

/**
 * 品酒会管理Action
 *
 * @author mengfk@eswine.com
 * @since  20130327
 */

class WtastingAction extends BaseAdmin {
    
    public function index(){
        if($this->isPost()) {
            $op = isset($_GET['op']) ? $_GET['op']: '';
            if($op == 'showdetail') {
                $wpid = intval($_POST['wpid']);
                $wp = M("wineparty")->where(array('id' => $wpid))->find();
                $wp['experts'] = D('Wtasting')->experts($wp['experts']);
                $wp['groups'] = M("wgroups")->where(array('wid' => $wpid))->select();
                die(json_encode($wp));
            } elseif($op == 'getwinedetail') {
                $gid = intval($_POST['gid']);
                $wines = M("wines")->where(array('gid' => $gid))->select();
                die(json_encode($wines));
            } elseif($op == 'getwinescores') {
                $wpid = intval($_POST['wpid']);       
                $wineid = intval($_POST['wineid']);
                $wp = M('wineparty')->where(array('id' => $wpid))->find();
                $experts = D('Wtasting')->experts($wp['experts']);
                $eids = $ename = $ex = $sc = array();
                foreach($experts as $eval) {
                    $eids[] = $eval['mid'];
                    $ename[$eval['mid']] = $eval['name'];
                }
                if($eids) {
                    $scores = M('score')->where( " `wineid` = ".$wineid." AND `expert` IN (" .implode(',', $eids).")")->select();
                    foreach($scores as $score) {
                        $sc[$score['expert']] = $score['score'];
                    }
                    foreach($eids as $eid) {
                        $ex[] = array(
                            'name' => $ename[$eid],
                            'statement' => isset($sc[$eid]) ? 100 : 0,
                        );
                    }
                }
                $html = '';
                $count = count($ex);
                for($loop = 0;$loop < $count; $loop+=6) {
                    $html .= "<tr>";
                    for($td = 0; $td < 6; $td++) {
                        $html .= "<td width='16%' style='border-right:1px solid #eeeeee;'>".(isset($ex[$td + $loop]) ? $ex[$td + $loop]['name']: '')."</td>";
                    }
                    $html .= "</tr>";
                    $html .= "<tr>";
                    for($td = 0; $td < 6; $td++) {
                        $html .= "<td width='16%' style='border-right:1px solid #eeeeee;'>".(isset($ex[$td + $loop]) ? $ex[$td + $loop]['statement']: '')."%</td>";
                    }
                    $html .= "</tr>";
                }
                die(json_encode($html));
            } elseif($op == 'delete') {
                $ids = $_POST['ids'];
                if(!$ids) {
                    $result = array(
                        'errno' => 1,
                        'message' => '参数有误，删除失败！',    
                    );
                    die(json_encode($result));
                }
                M('wineparty')->where(' `id` IN ('.$ids.')')->delete();
                M('wines')->where(' `wid` IN ('.$ids.')')->delete();
                M('wgroups')->where(' `wid` IN ('.$ids.')')->delete();
                M('advimage')->where(' `wid` IN ('.$ids.')')->delete();
                M('score')->where(' `wid` IN ('.$ids.')')->delete();
                $result = array(
                    'errno' => 0,
                    'message' => '删除成功',    
                );
                die(json_encode($result));
            }
        }
		import('@.ORG.Util.Page');
        $map = array('status' => 1);
        $count = M('wineparty')->where($map)->count('*');
		$p = new Page($count, 20);
        $voList = M('wineparty')->where($map)->order(' `id` DESC')->limit($p->firstRow . ',' . $p->listRows)->select();
        foreach($voList as $key => $val) {
            $voList[$key]['experts'] = count(D('Wtasting')->experts($val['experts']));
            $date = date('Ymd', $val['dateline']);
            $today = date('Ymd', time());
            $voList[$key]['statement'] = $date > $today ? '未开始' : ($date == $today ? '正在进行' : '已经结束') ;
        }
        $page = $p->show();
        $this->assign('volist', $voList);
        $this->assign('page', $page);
        $this->display();
    }

    public function add() {
        if($this->isPost()) {
            $op = isset($_GET['op']) ? $_GET['op']: '';

            if($op == 'addwp') {
                $rst = M('wineparty')->add(array('status' => 2));
                $result = array('errno' => 0, 'result' => $rst);
                die(json_encode($result));
            } elseif($op == 'upload') {
                $this->_dealAdvImagesUpload();
            } elseif($op == 'addgroup') {
                $data = array(
                    'name' => $_POST['name'],
                    'item' => $_POST['id'],
                    'wid' => $_POST['wpid'],
                );
                $id = M('wgroups')->add($data);
                if($id) {
                    $result = array('result' => $id, 'errno' => 0);
                } else {
                    $result = array('result' => '新建分组失败', 'errno' => 1);
                }
			    die(json_encode($result));
            } elseif($op == 'addwine') {
                $data = array(
                    'gid' => intval($_POST['gid']),
                    'name' => $_POST['name'],
                    'jkid' => intval($_POST['jkid']),
                    'yid' => intval($_POST['yid']),
                    'wid' => intval($_POST['wpid']),
                    'agent' => $_POST['agent'],
                    'price' => $_POST['price'],
                );
                $checks = M('wines')->where(array('jkid' => $data['jkid'], 'gid' => $data['gid'], 'yid' => $data['yid']))->find();
                if(!$checks) {
                    $rst = M('wines')->add($data);
			        die(json_encode(array('errno' => 0, 'result' => '添加成功')));
                } else {
                    die(json_encode(array('errno' => 1, 'result' => '该款酒在该分组下已经存在')));
                }
            } elseif($op =='winelist') {
                $gid = intval($_POST['gid']);
                $wpid = intval($_POST['wpid']);
                Import("@.ORG.Util.Page");
                $map = array('gid' => $gid, 'wid' => $wpid);
                $count = M('wines')->where($map)->count('*');
                if($count > 0) {
			        $p = new Page($count, 10);
			        $voList = M('wines')->where($map)->order("id DESC")->limit($p->firstRow . ',' . $p->listRows)->select();
                    $page = $p->show();
                    $result = array(
                        'list' => $voList ? $voList : array(),
                        'page' => $page ? $page: '',
                    );
                    die(json_encode($result));
                } else {
                    $result = array(
                        'list' => array(),
                        'page' => '',
                    );
                    die(json_encode($result));
                }
                exit();
            } elseif($op == 'updateWineItem') {
                $wid = intval($_POST['wid']);
                $item = $_POST['item'];    
                $rst = M('wines')->where(array('id' => $wid))->save(array('item' => $item));
                $result = array('result' => $item);
                die(json_encode($result));
            } elseif($op == 'delwines') {
                $ids = $_POST['ids'];
                if($ids) {
                    $rst = M("wines")->where(' `id` IN('.$ids.') ')->delete();
                    M('score')->where(' `wineid` IN ('.$ids.')')->delete();
                }
                if(isset($rst) && $rst) {
                    $result = array(
                        'errno' => 0,
                        'message' => '删除成功,共删除'.$rst.'条数据',    
                    );
                } else {
                    $result = array(
                        'errno' => 1,
                        'message' => '删除数据时遇到错误',    
                    );
                }
                die(json_encode($result));
            } elseif($op == 'grouplist') {
                $wid = intval($_POST['wid']);
                if(!$wid) {
                    $result = array(
                        'errno' => 1,
                        'message' => '酒会ID错误',    
                    );
                    die(json_encode($result));
                }
                $rst = M('wgroups')->where(array('wid' => $wid))->select();
                if(!$rst) {
                    $result = array(
                        'errno' => 1,
                        'message' => '酒会分组数据获取异常',    
                    );
                    die(json_encode($result));
                }
                $result = array(
                    'errno' => 0,
                    'list' => $rst,    
                );
                die(json_encode($result));
            } elseif($op == 'changewinegroup') {
                $gid = intval($_POST['gid']);
                $id = intval($_POST['wineid']);
                $rst = M('wines')->where(array('id' => $id))->save(array('gid' => $gid));
                if($rst) {
                    $result = array(
                        'errno' => 0,
                        'message' => '分组移动成功',    
                    );
                } elseif($rst === false) {
                    $result = array(
                        'errno' => 1,
                        'message' => '分组移动失败',    
                    );
                }
                die(json_encode($result));
            } elseif($op == 'submit') {
                $wpid = intval($_POST['wpid']);
                $name = $_POST['name'];
                $type = $_POST['type'];
                $experts = $_POST['experts'];
                $password = $_POST['password'];
                $time = strtotime($_POST['time']);
                if(!$wpid) {
                    $result = array(
                        'errno' => 1,
                        'message' => '网络异常，酒会数据添加失败',    
                    );
                    die(json_encode($result));
                }
                $rst = M('wineparty')->where(array('id' => $wpid))->save(array('status' => 1, 'name' => $name, 'type' => $type, 'experts' => $experts, 'password' => $password, 'dateline' => $time));
                if($rst === false) {
                    $result = array(
                        'errno' => 1,
                        'message' => '数据异常，酒会数据添加失败',    
                    );
                } else {
                    $result = array(
                        'errno' => 0,
                        'message' => '酒会添加成功',    
                    );
                }
                die(json_encode($result));
            } elseif($op == 'editgroup') {
                $gid = intval($_POST['gid']);
                $name = $_POST['name'];
                $item = $_POST['item'];
                if(!$gid) {
                    $result = array(
                        'errno' => 1,
                        'message' => '分组ID丢失',    
                    );
                    die(json_encode($result));
                } elseif(!$name) {
                    $result = array(
                        'errno' => 1,
                        'message' => '分组名称丢失',    
                    );
                    die(json_encode($result));
                }
                $data = array('name' => $name, 'item' => $item);
                $rst = M("wgroups")->where(array('gid' => $gid))->save($data);
                if($rst === false) {
                    $result = array(
                        'errno' => 1,
                        'message' => '分组重命名失败',    
                    );
                    die(json_encode($result));
                } else {
                    $result = array(
                        'errno' => 0,
                        'message' => '分组修改成功',    
                    );
                    die(json_encode($result));
                }
            } elseif($op == 'dropwine') {
                $wpid = intval($_POST['wpid']);
                M('wineparty')->where(array('id' => $wpid))->delete();
                M('wines')->where(array('wid' => $wpid))->delete();
                M('wgroups')->where(array('wid' => $wpid))->delete();
                die(json_encode(array('result' => 1)));
            } elseif($op == 'delimg') {
                $id = intval($_POST['id']);
                $rst = M('advimage')->where(array('id' => $id))->delete();
                if($rst) {
                    $result = array(
                        'errno' => 0,
                        'message' => '删除成功',    
                    );
                } else {
                    $result = array(
                        'errno' => 1,
                        'message' => '数据不存在或者已经被删除',    
                    );
                }
                die(json_encode($result));
            } elseif($op == 'winefinished') {
                $wineid = intval($_POST['id']);
                $finish = intval($_POST['finished']);
                $rst = M('wines')->where(array('id' => $wineid))->save(array('finish' => $finish));
                die(json_encode($rst));
            } elseif($op == 'locked') {
                $wpid = intval($_POST['wpid']);
                $rst = M('wineparty')->where(array('id' => $wpid))->save(array('locked' => 1));
                die(json_encode($rst));
            } elseif($op == 'delgroup') {
                $gid = intval($_POST['gid']);
                $rst = M('wgroups')->where(array('gid' => $gid))->delete();
                M('wines')->where(array('gid' => $gid))->save(array('gid' => 0));
                die(json_encode($rst));
            } elseif($op == 'pub') {
                $wpid = intval($_POST['wpid']);
                //发布酒评
                $rst = M('wineparty')->where(array('id' => $wpid))->save(array('pub' => 1));
                die(json_encode($rst));
            } elseif($op == 'agents') {
                $wineid = intval($_POST['wineid']);
                $year = intval($_POST['year']);
                $sql = "
                    SELECT 
                        ag.cname as name, w.price as price
                    FROM 
                        `jiuku_agents_internet_sales_wine` w
                        LEFT JOIN `jiuku_agents` ag ON w.`agents_id` = ag.`id`
                    WHERE 
                        `wine_id` = {$wineid} AND `year` = {$year} 
                ";
                $rst = M()->query($sql);
                die(json_encode($rst));
            }
        }
        //读出专家列表
        $expert = M('expert')->select();

        $this->assign('expert', $expert);
        $this->display();
    }

    public function edit() {
        $id = intval($_GET['wpid']);
        $wineparty = M('wineparty')->where(array('id' => $id))->find();
        if($wineparty['locked']) {
            $this->_jumpGo("酒单已上传，禁止编辑！");
        }
        $wineparty['expertslist'] = $wineparty['expertsname'] = $dots = '';
        if($wineparty['experts']) {
            $wineparty['expertslist'] = '|' . trim($wineparty['experts'], '|'). '|';
            $experts = D('Wtasting')->experts($wineparty['experts']);
            foreach($experts as $exp) {
                $wineparty['expertsname'] .= $dots . $exp['name'];
                $dots = '、';
            }
        }
        $expert = M('expert')->select();
        $groups = M('wgroups')->where(array('wid' => $id))->select();
        $adv = M('advimage')->where(array('wid' => $id))->select();
        $this->assign('wineparty', $wineparty);
        $this->assign('groups', $groups);
        $this->assign('expert', $expert);
        $this->assign('adv', $adv);
        $this->display();
    }

    public function score() {
        if($this->isPost()) {
            $op = $_GET['op'];
            if($op == 'chgscore') {
                $wineid = intval($_POST['wineid']);
                $wpid = intval($_POST['wpid']);
                $score = intval($_POST['score']);
                $expert = intval($_POST['expert']);
                $score = $score <50 ? 50 : ($score > 100 ? 100 : $score);
                $where = array(
                    'wineid' => $wineid,
                    'wid' => $wpid,
                    'expert' => $expert    
                );
                if(M('score')->where($where)->find()) {
                    $rst = M('score')->where($where)->save(array(
                        'score' => $score    
                    ));
                } else {
                    $rst = M('score')->add(array(
                        'wineid' => $wineid,
                        'wid' => $wpid,
                        'expert' => $expert,
                        'score' => $score    
                    ));
                }
                D('Wtasting')->dealScore($wineid);
                die(json_encode(1));
            }
        }
        $id = intval($_GET['id']);
        $wp = M('wineparty')->where(array('id' => $id))->find();
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
                foreach($experts as $exp) {
                    $wines[$key]['sc'][$exp['mid']] = isset($score[$exp['mid']][$wine['id']]) ? $score[$exp['mid']][$wine['id']] : 0;
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
        $this->display();
    }


    protected function _dealAdvImagesUpload() {
        $wpid = $_GET['wpid'];
		import('@.ORG.Util.Upload');
        $upload = new Upload;
        $upload->config(array(
							  'ext' =>  C('ADVIMG_EXT'),
                              'size' => C('ADVIMG_MSIZE'),
							  'path' => C('ADVIMG_PATH'),
        ));
		$rst = $upload->uploadFile('uploadfile');
		if($rst['errno']) {
			$this->show("<script>window.parent.uploadImageCallBack(false, '" . $upload->error($rst['errno']) ."')</script>");
			exit();
		}
		$data = array('wid' => $wpid, 'path' => $rst['path'], 'ext' => $rst['ext'], 'size' => $rst['size']);
		$advid = M('advimage')->add($data);
		$this->show("<script>window.parent.uploadImageCallBack(true, ". $advid .")</script>");
		exit();
    }

	/**
	 * 后台通用跳转函数
	 */
	protected function _jumpGo($message, $mode = 'info', $url = 'javascript:history.go(-1)', $time=1) {
		$html = '<!DOCTYPE html PUBLIC"-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Refresh" contect="'.$time.';url='.$url.'"><meta http-equiv="Content-Type"content="text/html; charset=utf-8"/><title>提示信息</title><style type="text/css">body{background-color:#fff;margin:40px;font:13px/20px Arial,sans-serif;color:#4F5155}a{color:#00F;background-color:transparent;font-weight:normal;text-decoration:none}h1{color:#444;background-color:transparent;border-bottom:1px solid#D0D0D0;font-size:19px;font-weight:normal;margin:0 0 14px 0;padding:14px 15px 10px 15px}#body{margin:0 15px 0 15px}p.footer{text-align:right;font-size:11px;border-top:1px solid#D0D0D0;line-height:32px;padding:0 10px 0 10px;margin:20px 0 0 0}#container{margin:10px;border:1px solid#D0D0D0;-webkit-box-shadow:0 0 8px#D0D0D0}h1 span{height:27px;line-height:27px;vertical-align:middle;padding-left:32px;display:block}.error{background:url(http://public.wine.cn/admin/images/error.gif)no-repeat}.succeed{background:url(http://public.wine.cn/admin/images/succeed.gif)no-repeat}.info{background:url(http://public.wine.cn/admin/images/info.gif)no-repeat}</style></head><body><div id="container"><h1><span class="'.$mode.'">'.$message.'……</span></h1><div id="body"><p><a href="'.$url.'">如果您的浏览器没有自动跳转，请点击此链接...</a></p></div><p class="footer">&copy 2012 wine.cn</p></div><script>var code = \'location.href="'.$url.'";\'; setTimeout(code, 1000);</script></body></html>';
        $this->display('','','',$html);
        exit();
	}
}
