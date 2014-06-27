<?php
/**
 * 权限外公共控制器
 */
class PublicAction extends Action {
    /**
     * 初始化
     */
    public function _initialize() {
        //引入输入过滤类
        import('@.ORG.Util.Input');
    }
    //通过代理商ID获取网络渠道列表
    public function getInternetSalesListForAgentsId(){
        $id = intval($_POST['id']);
        if(!$id)
            $this->echo_exit(array('errorCode'=>1,'errorStr'=>'Parameter Error'));
        $map = array(
            'agents_id' => $id,
            'is_del' => '-1',
        );
        if(empty($_POST['type']) || $_POST['type'] != 1){
            $map['status'] = '1';
        }
        $res = D('InternetSales')->field('id,name')->where(array('agents_id'=>$id,'is_del'=>'-1'))->select();
        if(!$res)
            $this->echo_exit(array('errorCode'=>1,'errorStr'=>'No Data'));
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$res));
    }
    //通过代理商ID获取实体渠道列表
    public function getStoreSalesListForAgentsId(){
        $id = intval($_POST['id']);
        if(!$id)
            $this->echo_exit(array('errorCode'=>1,'errorStr'=>'Parameter Error'));
        $map = array(
            'agents_id' => $id,
            'is_del' => '-1',
        );
        if(empty($_POST['type']) || $_POST['type'] != 1){
            $map['status'] = '1';
        }
        $res = D('StoreSales')->field('id,name')->where(array('agents_id'=>$id,'is_del'=>'-1'))->select();
        if(!$res)
            $this->echo_exit(array('errorCode'=>1,'errorStr'=>'No Data'));
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$res));
    }
    //通过关键字获取标准酒款列表
    public function getJiukuWineListForKw(){
        $kw = Input::getVar($_POST["kw"]);
        if($kw == ''){
            $this->echo_exit(array('errorCode'=>1,'errorStr'=>'Parameter Error'));
        }
        if(empty($_POST['type']) || $_POST['type'] != 1){
            $map['status'] = '1';
        }
        $map['merge_id'] = 0;
        $map['is_del'] = '-1';
        $count = intval($_POST['count']) ? intval($_POST['count']) : 10;
        $result = array();
        if(preg_match("/^(-|\+)?\d+$/",$kw)){
            $res = M('Wine','jiuku_')->field('id,fname,cname')->where(array_merge($map,array('id'=>$kw)))->find();
            array_push($result,$res);
        }else{
            $eq_res = M('Wine','jiuku_')->field('id,fname,cname')->where(array_merge($map,array('_complex'=>array('fname'=>$kw,'cname'=>$kw,'_logic'=>'or'))))->limit($count)->select();
            foreach($eq_res as $val){
                array_push($result, array('id'=>$val['id'],'fname'=>$val['fname'],'cname'=>$val['cname']));
                $exist_idarr[] = $val['id'];
            }
            $like_res = M('Wine','jiuku_')->field('id,fname,cname')->where(array_merge($map,array('_complex'=>array('fname'=>array('like','%'.$kw.'%'),'cname'=>array('like','%'.$kw.'%'),'_logic'=>'or'))))->limit($count-count($eq_res))->select();
            foreach($like_res as $val){
                if(!in_array($val['id'], $exist_idarr))
                    array_push($result, array('id'=>$val['id'],'fname'=>$val['fname'],'cname'=>$val['cname']));
            }
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$result));
    }
    //通过标准酒款id获取中文名列表
    public function getWineCanameListForWineId(){
        $id = intval($_POST['id']);
        if($id == 0){
            $this->echo_exit(array('errorCode'=>1,'errorStr'=>'Parameter Error'));
        }
        if(empty($_POST['type']) || $_POST['type'] != 1){
            $map['status'] = '1';
        }
        $map['cname'] = array('neq','');
        $map['wine_id'] = $id;
        $map['is_del'] = '-1';
        $res = M('WineCaname','jiuku_')->field('id,cname')->where($map)->select();
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$res));
    }
    //通过id删除抓取数据
    public function dropCapturDataForId(){
        $id = intval($_POST['id']);
        $res = M('CrawlWineData','jiuku_')->save(array('id'=>$id,'is_del'=>'1'));
        if($res){
            echo 1;
        }else{
            echo 0;
        }
    }
    function echo_exit($arr){
        if(empty($_GET['callback'])){
            echo json_encode($arr);
        }else{
            echo $_GET['callback'].'('.json_encode($arr).')';
        }
        exit();
    }
}