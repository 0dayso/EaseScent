<?php

/**
 * Jiuku项目的公共控制器2
 */
class Common2Action extends BaseAdmin {

    /**
     * 初始化
     */
    public function _initialize() {
        parent::_initialize();
    }

    /**
     * 链接Redis数据库
     */
    function linkRedis(){
        if(!extension_loaded('redis')){
            return false;exit();
        }
        $Redis = new Redis();
        $Rediscfg = C('REDIS_CONFIG');
        try{
            $Redis->connect($Rediscfg['host'], $Rediscfg['port']);
        }catch(Exception $e){
            return false;exit();
        }
        return $Redis;
    }

    /**
     * 后台通用跳转函数
     */
    protected function _jumpGo($message, $mode = 'info', $url = 'javascript:history.go(-1)', $time=1) {
        $html = '<!DOCTYPE html PUBLIC"-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Refresh" contect="'.$time.';url='.$url.'"><meta http-equiv="Content-Type"content="text/html; charset=utf-8"/><title>提示信息</title><style type="text/css">body{background-color:#fff;margin:40px;font:13px/20px Arial,sans-serif;color:#4F5155}a{color:#00F;background-color:transparent;font-weight:normal;text-decoration:none}h1{color:#444;background-color:transparent;border-bottom:1px solid#D0D0D0;font-size:19px;font-weight:normal;margin:0 0 14px 0;padding:14px 15px 10px 15px}#body{margin:0 15px 0 15px}p.footer{text-align:right;font-size:11px;border-top:1px solid#D0D0D0;line-height:32px;padding:0 10px 0 10px;margin:20px 0 0 0}#container{margin:10px;border:1px solid#D0D0D0;-webkit-box-shadow:0 0 8px#D0D0D0}h1 span{height:27px;line-height:27px;vertical-align:middle;padding-left:32px;display:block}.error{background:url('.C('TMPL_PARSE_STRING.__PUBLIC__').'admin/images/error.gif)no-repeat}.succeed{background:url('.C('TMPL_PARSE_STRING.__PUBLIC__').'admin/images/succeed.gif)no-repeat}.info{background:url(/public/admin/images/info.gif)no-repeat}</style></head><body><div id="container"><h1><span class="'.$mode.'">'.$message.'……</span></h1><div id="body"><p><a href="'.$url.'">如果您的浏览器没有自动跳转，请点击此链接...</a></p></div><p class="footer">&copy 2012 wine.cn</p></div><script>var code = \'location.href="'.$url.'";\'; setTimeout(code, 1000);</script></body></html>';
        $this->display('','','',$html);
        exit();
    }

    /**
     * 列表页将POST参数改为GET
     */
    function post_to_get(){
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        foreach($_POST as $key=>$val){
            $url .= '&' . $key . '=' . $val;
        }
        header('Location: ' . $url);
        exit;
    }

    /**
     * 字符窜过滤
     */
    function sanitize($input) {
        if (is_array($input)) {
            foreach($input as $var=>$val) {
                $output[$var] = $this->sanitize($val);
            }
        } else {
            if (get_magic_quotes_gpc()) {
                $input = stripslashes($input);
            }
            $search = array(
                '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
                '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
                '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
                '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
            );
            $input = preg_replace($search, '', $input);
            //$input = mysql_real_escape_string($input);
            $output = trim($input);
        }
        return $output;
    }

    /**
     * 标记百度地图
     */
    public function baiduMap(){
        $this->assign('mark',$_GET['mark']);
        $this->display();
    }

    /**
     * 标记谷歌地图
     */
    public function googleMap(){
        $this->assign('mark',$_GET['mark']);
        $this->display();
    }
}
