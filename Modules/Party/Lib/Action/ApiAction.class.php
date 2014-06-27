<?php
class ApiAction extends BaseAction
{
    /**
     *  外部调用,获取酒会信息
     *  @access public
     *  @return void
     */
    public function get(){
        $id = $_GET['id']; 
        $model = D('EswWineParty');
        $callback = $_GET['callback'];
        $data = $model->field('id, title, pictype')->where('id in('.$id.')')->select();
        if($data){
            foreach($data as $k=>$v){
                $data[$k]['pic'] = 'http://jiuhui.wine.cn/Common/images/partycreate/'.$v['id'].'/d_.'.$v['pictype'];
                unset($v['pictype']);
            }
            $returnData = array('stat'=>1, 'data'=>$data);
        }else{
            $returnData = array('stat'=>1000001,'data'=>'');
        }
        ob_clean();
        echo "{$callback}(".json_encode($returnData).")";
    }

    /**
     *
     *
     *
     */
    public function getInfo(){
        $callback = $_GET['callback'];
        $id = $_GET['id'];
        $model = D('EswWineParty');
        $mark = $_GET['mark'];
        $data = $model->field('id, title, pictype,markprice,address_info,party_start,party_end,date_type')->where('id = '.$id.' and is_show = 1')->find();
        if($data){
            $d['id'] = $data['id'];
            $d['title'] = $data['title'];
            $d['pic']   = 'http://jiuhui.wine.cn/Common/images/partycreate/'.$data['id'].'/d_.'.$data['pictype'];
            $d['price'] = $data['markprice'];
            $d['time']  = date('m月d日 H:i',$data['party_start']).'-'.($data['date_type']==1 ? date('H:i',$v['party_end']):date('m月d日 H:i',$data['party_end'])); 
            $d['address'] = $data['address_info'];
            $returnData = array('stat'=>1, 'data'=>$d,'mark'=>$mark); 
        }else{
            $returnData = array('stat'=>1000001, 'data'=>'','mark'=>$mark);
        }
        ob_clean();
        echo "{$callback}(".json_encode($returnData).")";
    }
    /**
     * api for phone...
     * author@david
     */
   public function joinPaty(){
    	$param = base64_decode($_POST["params"]);
    	parse_str($param);
    	$sid = $_GET["sid"];
    	file_get_contents("http://wwww.wine.cn/Uc/reregister/sid/".$sid);
    	//echo $param;
    	if(!$uid){
    		echo  100001;
    		exit;
    	}
    	if(!$type){
    		echo  100002;
    		exit;
    	}
    	if(!$pid){
    		echo  100003;
    		exit;
    	}
    	$model = D('EswWinePartyUser');
    	if($type==1){
    		if(!$this->getUid($uid)){
    			echo 100005;
    			exit;
    		}
    		$data["uid"] = $this->getUid($uid);
    		$data["party_id"] = $pid;
    		$info = $model->where($data)->find();
    		if($info){
    			echo 100006;exit;
    		}
    		$last_id = $model->add($data);
    		if($last_id){
    			echo  1;
    		}else{
    			echo  100004;
    			exit;
    		}
    	}elseif ($type==2){
    		$where["uid"] = $this->getUid($uid);
    		$where["party_id"] = $pid;
    		$model->where($where)->delete();
    		echo  1;
    	}
    }
    
    /**
     *
     * 逸香首页图
     * 
     */
    public function get_img(){
        $model = M();
        $sql = 'SELECT ep.id, title, party_start, puser_num, markprice, ismoney, isbar, p_type, pictype, date_type, region_name, region_id
                FROM es_esw_wine_party ep
                LEFT JOIN es_region_new en ON ep.city_id = en.region_id
                WHERE ep.is_show =1
                AND ep.p_type = 0
                AND isbar = 2
                ORDER BY party_start DESC , add_time DESC 
                LIMIT 0 , 1';
        $data = $model->query($sql);
        $data = $data[0];
        if($data){
            $d['id'] = $data['id'];
            $d['title'] = $data['title'];
            $d['pic']   = 'http://jiuhui.wine.cn/Common/images/partycreate/'.$data['id'].'/d_.'.$data['pictype'];
            $d['price'] = $data['markprice'];
            $d['start'] = $data['party_start'];
            $d['region_name'] = $data['region_name'];
            $returnData = array('stat'=>1, 'data'=>$d); 
        }else{
            $returnData = array('stat'=>1000001, 'data'=>'','mark'=>$mark);
        }
        ob_clean();
        die(json_encode($returnData));
    } 
    /**
     *
     * 逸香首页列表
     *
     */
    public function get_list(){
        $model = M();
        $sql = 'SELECT ep.id, title, party_start, puser_num, markprice, ismoney, isbar, p_type, pictype, date_type, region_name, region_id
                FROM es_esw_wine_party ep
                LEFT JOIN es_region_new en ON ep.city_id = en.region_id
                WHERE ep.is_show =1
                AND ep.p_type = 0
                AND isbar = 2
                ORDER BY party_start DESC , add_time DESC 
                LIMIT 0 , 1';
        $data = $model->query($sql);
        $data = $data[0];
        $d['id'] = $data['id'];
        $d['title'] = $data['title'];
        $d['pic']   = 'http://jiuhui.wine.cn/Common/images/partycreate/'.$data['id'].'/d_.'.$data['pictype'];
        $d['price'] = $data['markprice'];
        $d['start'] = $data['party_start'];
        $d['region_name'] = $data['region_name'];


        $bigArr['img'] = $d; 
        $sql = 'SELECT ep.id, title, party_start, puser_num, markprice, ismoney, isbar, p_type, pictype, date_type, region_name, region_id
                FROM es_esw_wine_party ep
                LEFT JOIN es_region_new en ON ep.city_id = en.region_id
                WHERE ep.is_show =1
                AND ep.p_type = 0
                AND isbar = 3
                ORDER BY party_start DESC , add_time DESC 
                LIMIT 0 , 1';
        $data = $model->query($sql);
        $data = $data[0];
        $d = array();
        $d['id'] = $data['id'];
        $d['title'] = $data['title'];
        $d['pic']   = 'http://jiuhui.wine.cn/Common/images/partycreate/'.$data['id'].'/d_.'.$data['pictype'];
        $d['price'] = $data['markprice'];
        $d['start'] = $data['party_start'];
        $d['region_name'] = $data['region_name'];

        $bigArr['top'] = $d;


        $sql = 'SELECT ep.id, title, party_start, puser_num, markprice, ismoney, isbar, p_type, pictype, date_type, region_name, region_id
                FROM es_esw_wine_party ep
                LEFT JOIN es_region_new en ON ep.city_id = en.region_id
                WHERE ep.is_show =1
                AND ep.p_type = 0
                AND isbar = 4
                ORDER BY party_start DESC , add_time DESC 
                LIMIT 0 , 9';
        $data = $model->query($sql);

        $d = array();
        foreach($data as $k=>$v){
            $d[$k]['id'] = $v['id'];
            $d[$k]['title'] = $v['title'];
            $d[$k]['pic']   = 'http://jiuhui.wine.cn/Common/images/partycreate/'.$v['id'].'/d_.'.$v['pictype'];
            $d[$k]['price'] = $v['markprice'];
            $d[$k]['start'] = $v['party_start'];
            $d[$k]['region_name'] = $v['region_name'];
        }
        $bigArr['list'] = $d;

        $returnData = array('stat'=>1, 'data'=>$bigArr); 
        ob_clean();
        die(json_encode($returnData));
    }
}
