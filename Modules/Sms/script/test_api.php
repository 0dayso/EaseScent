<?php
$str = '逸香葡萄酒教育，验证吗AKSDKJF';
sendsms($str);

function sendsms($msg) {                                                                                                                                 
    $skey = 'ae02331ae8d1c986f97aa8654733211d';
    $arr_sms = array('message'=>$msg,'tellist'=>array(
    '15801577119'
    ));
    $data = sms_encrypt($arr_sms, $skey);

    $token = getToken();
    var_dump($token);
    
    $posts = array(
            'appid' => 4,
            'accessToken' => $token["result"],
            'ci' => 1,
            'd'=>$data,
            );

    $url = "http://api.wine.cn.local/?Sms/Api/send";
    echo (CurlPost($url, $posts));

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
    $token_url = "http://api.wine.cn.local/?Admin/Api/accessToken";
    $post = array('appid' => '4', 'appkey' => '3008c9baa3395b47e47c6ea850809a0c');
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

