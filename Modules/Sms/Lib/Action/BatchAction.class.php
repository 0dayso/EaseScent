<?php
class BatchAction extends CommonAction{
    function index(){
        $model = D('SendList');
        $url = "";
        $list = $this->_list($model, $map, 15, $url);
        $this->assign("list",$list);
        $this->display();
    }
    function info(){
        $list = D('Client')->getClientList();
        foreach($list as $k=>$v){
            if($v["text_suffix"]==""){
                $list[$k]["text_suffix"] = "请选择"; 
            }
        }
        $this->assign("list",$list);
        $this->display("info");
    }
    /**
    * 发送信息
    */
    function send(){
        //客户端 用户取得授权解决发信息后缀名问题
        $client_id = $_POST['client'];
        if($client_id==-1){
            $this->error("请选择后缀!");
        }
        $from_user = $_POST["from_user"];
        if(empty($from_user)){
            $this->error("请填写需求人电话!");
        }
        $tel_list = $_POST["tel_list"];
        if(empty($tel_list)){
            $this->error("请填写电话列表!");
        }
        $log = $_POST["log"];
        if(empty($log)){
            $this->error("请填写备注!");
        }
        $sms_text = $_POST["sms_text"];
        $tel_count = json_decode($_POST["tel_count"],true);
        $tel_type = json_decode($_POST["tel_type"],true);
        $send_count = $tel_count["mobile"]*$tel_type["mobile"]+$tel_count["union"]*$tel_type["union"]+$tel_count["unicom"]*$tel_type["unicom"];
        if(empty($sms_text)){
            $this->error("短信内容为空!");
        }
        $send_tel_list = $this->strToUniqueArray($tel_list);
        if(!is_array($send_tel_list)||strlen($send_tel_list[0])!=11){
            $this->error("电话列表格式出问题了!");
        }    
        $c = D('Clients')->where('id='.$client_id)->find();
        //排除空，或者非法电话
        $send_tel_list = array_filter($send_tel_list,"to_filter");
        $re =  $this->sendsms($sms_text,$send_tel_list,$c['skey'],$c['id']);
        $re = json_encode($re);
        if($re['errno']==0){
            //更新发送记录
            $data = array(
                    "client_id"=>$client_id,
                    "from_user"=>$from_user,
                    "tel_list"=>$tel_list,
                    "sms_text"=>$sms_text,
                    "add_time"=>time(),
                    "log"=>$log,
                    "send_count"=>$send_count,
                    "admin_id"=>$_SESSION["admin_uid"]
                );
            $re = D("SendList")->add($data);
            $this->success("发送成功!");
            exit;
        }
        $this->error("很抱歉，发送失败，请重新尝试!");
    }
    function strToUniqueArray($str,$type="\r\n"){
            $uArray = explode($type,$str);
            $uniqueArray = array_unique($uArray);
            return $uniqueArray;  
    } 
    
    function sendsms($msg,$tellist,$skey,$cid) {  
        $arr_sms = array('message'=>$msg,'tellist'=>$tellist);
        $data = $this->sms_encrypt($arr_sms, $skey);
       $token = $this->getToken();
        $posts = array(
            'appid' => 4,
            'accessToken' => $token["result"],
            'ci' => $cid,
            'd'=>$data,
            );
        $url = "http://api.wine.cn/?Sms/Api/send";
        return $this->CurlPost($url, $posts);
    }
    function sms_encrypt($arr, $key) {
         $string = json_encode($arr);
        srand((double) microtime() * 1000000); //for sake of MCRYPT_RAND
        $key = md5($key); //to improve variance
        /* Open module, and create IV */
        $td = mcrypt_module_open('des', '','cfb', '');
        $key = substr($key, 0, mcrypt_enc_get_key_size($td));
        $iv_size = mcrypt_enc_get_iv_size($td);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        /* Initialize encryption handle */
        if (mcrypt_generic_init($td, $key, $iv) != -1) {

            /* Encrypt data */
            $c_t = mcrypt_generic($td, $string);
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
            $c_t = $iv.$c_t;
            return $c_t;
        } //end if
    }
    function getToken() {
        $token_url = "http://api.wine.cn/?Admin/Api/accessToken";
        $post = array('appid' => '4', 'appkey' => '3b28c97bfeef9c07dfb053cb4d7972fe');
        $jGet = CurlPost($token_url, $post);
        return json_decode($jGet,true);                                                                                                                        
    } 
    /**
    * Curl Post
    */
    function CurlPost($url, $data=array(), $timeout = 10, $header = "") {
        $ssl = substr($url, 0, 8) == 'https://' ? true : false;                                                                                                    
        $post_string = http_build_query($data);  
        $ch = curl_init();
        if ($ssl) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查 
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        }   
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
        curl_setopt($ch, CURLOPT_USERPWD, "thisuser:Gst43sB");
        //curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
        }
    }


?>
