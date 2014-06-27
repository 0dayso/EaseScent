<?php

/**
 * file:Winery
 * brief:酒庄相关类
 * author:zhuyl
 * date:2013-1.5
 */
class WineryAction extends YmApiAction {
    public $ym_host = '';
    function _initialize() { //初始化
    //验证是否登录
        import('@.ORG.Util.Input');
        import('@.ORG.Util.Page');
        import('@.ORG.Util.String');
        $this->ym_host = C('YM_DOMAIN');
        if(!$_SESSION["Ym_SIGN"]) {
            parent::checkLogin();
        }else {

            $info = array(
                'qy_id' => $_SESSION["ym_users"]["uid"],
            );
            $wineryInfo =  M('Winery')->where($info)->find();    //查询相应的企业id，是否已入代理商表
            $agentInfo =  M('Agent')->where($info)->find();    //查询相应的企业id，是否已入代理商表
            if(!$wineryInfo){
                 $this->redirect("Select/index"); //跳转到通行证登录页
            }
            if($agentInfo) {
                redirect($this->ym_host."/index.php/User/login");//跳转到通行证登录页
            }
        }
    }

    /**
     * info
     * 代理商信息页面
     * @access public
     * @return void
     */
    public function info() {
        $qy_list = parent::getQyInfo();//获取企业的相关信息
        $qy_id = $qy_list['id'];
        $map = array('qy_id' =>$qy_id);
        $winery =  M('Winery')->where($map)->find(); //通过企业ID查询代理商ID
        $winery_id = $winery['id'];//获取酒庄ID

        $img_condition = array('winery_id' =>$winery_id);//获取企业图片
        $img_list = D('WineryImg')->where($img_condition)->select();
        $img_count = D('WineryImg')->where($img_condition)->count();

        if($this->isPost()) {
        //获取企业通行证信息
            if($_REQUEST['country']) {
                $country = $_REQUEST['country'];//获取企业国家
            }else {
                $country = $_REQUEST['countrytext'];
            }

            $qy_info = array(
                'qy_name' => $_POST['qy_name'],//企业名称
                'qy_introduction' => $_POST['qy_introduction'],//企业简介
                'qy_address' => $_POST['qy_address'],
                'qy_skype' => $_POST['qy_skype'],
                'qy_qq' => $_POST['qy_qq'],
                'qy_moblie' => $_POST['qy_moblie'],
                'qy_web_address' => $_POST['qy_web_address'],
                'qy_country' => $country,
            );
            $map = array('id' =>$qy_list['id']);
            $data =M()->Table('ym_users_qy')->where($map)->save($qy_info);//修改企业通行证信息

            //获取相应酒庄信息
            $winery_info = array(
                'email' => $_POST['winery_email'],
                'honor' =>  $_POST['honor'], //酒庄荣誉
                'qy_id' => $qy_id,
                'qy_name' => $_POST['qy_name'],//企业的id，name存入Winery表
                'pic_url' => $_REQUEST['img_file'][0],   //首图存入Winery表
            );
            //查询相应酒庄信息是否已入库
            $winery_map = array(
                'qy_id' => $qy_id,
            );

            M('Winery')->where($winery_map)->save($winery_info);//修改Winery信息

            /**
             * @brief  保存企业照片
             */
            $img_file = isset($_REQUEST['img_file']) ? $_REQUEST['img_file'] : array();
            $img_queue =isset($_REQUEST['img_queue']) ? $_REQUEST['img_queue'] :array();

            M("WineryImg")->where("winery_id=$winery_id")->delete();

            foreach ( $img_file as $key =>$file ) {
                $desDir = C('UPLOAD_PATH').'Winery'.DS.'images'.$file;
                if(!is_dir(dirname($desDir))) {
                    mkdir(dirname($desDir), 0755, true);
                }
                rename(C('UPLOAD_PATH').'tmp'.$file, $desDir);
                $img_data = array(
                    'url' => $file,
                    'queue' => $img_queue[$key],
                    'winery_id' => $winery_id
                );
                $id=$this->_insert(D('WineryImg'),$img_data);
            }

            $this->redirect("Winery/introduce?winery_id=$winery_id"); //添加信息成功
        }
        $this->assign('qy_list', $qy_list);
        $this->assign('winery_id', $winery_id);
        $this->assign('winery', $winery);
        $img_list = $this->parse_img_url($img_list, 'Winery');
        $this->assign('img_list', $img_list);
        $this->assign('img_count', $img_count);
        $this->display();
    }
    /**
     * @brief  格式化展会相关图片url
     *
     * @params $type {string} wine/agent
     *
     * @return
     */
    public function parse_img_url($data, $type) {

        if (!is_array($data) ) return  C('UPLOAD_WWWPATH').$type.'/'.'images'.'/'.$data;

        foreach ( $data as $key => $dt ) {
            $data[$key]['path'] = $dt['url'];
            $data[$key]['url'] = C('UPLOAD_WWWPATH').$type.'/'.'images'.'/'.$dt['url'];
        }

        return $data;
    }
    /**
     * introduce
     * 酒庄信息介绍
     * @access public
     * @return void
     */
    public function introduce() {

        $qy_list = parent::getQyInfo();//获取企业的相关信息
        $map = array('qy_id' =>$qy_list['id']);
        $winery =  M('Winery')->where($map)->find(); //通过企业ID查询代理商ID
        $winery_id = $winery['id'];

        $map = array(
            'id' => $winery_id,//通过请求获取代理商id
        );
        $info = M('Winery')->where($map) ->field('description')->find();

        if($this->isPost()) {
            $info = array(
                'description' => stripslashes($_POST['content']),
            );
            M('Winery')->where($map)->save($info);
            $this->redirect("Winery/release?winery_id=$winery_id");
        }
        $this->assign('description', $info['description']);
        $this->assign('winery_id', $winery_id);
        $this->assign('qy_list', $qy_list);
        $this->display();
    }

    /**
     * introduce
     * 发布酒庄荣誉
     * @access public
     * @return void
     */
    public function status() {
        $qy_list = parent::getQyInfo();//获取企业的相关信息
        $winery_id = Input::getVar($_REQUEST['winery_id']);
        $map = array(
            'winery_id' => $winery_id,//通过请求获取代理商id
        );
        $statusInfo = M('WineryStatus')->where($map)->order('id asc')->select();
        $img_count = D('WineryStatus')->where($map)->count();

        if($this->isPost()) {
           //获取相应酒庄信息
            foreach($_REQUEST['status'] as $key=>$val) {
                 $pic = str_replace(C(UPLOAD_WWWPATH),'',$_REQUEST['img_file'][$key]);
                if(!empty($_REQUEST['oldid'][$key])){//修改
                      $condition = array('id' => $_REQUEST['oldid'][$key]);
                   
                      if(!empty($_REQUEST['img_file'][$key])){
                       
                        $winery_status = array(
                            'pic_url' => $pic,
                            'winery_id' => $winery_id,
                            'brief' => $_REQUEST['status'][$key],  //首图存入Winery表
                            'description' => $_REQUEST['description'][$key],
                            'queue' => $_REQUEST['img_queue'][$key],
                            );
                      }else{
                           $winery_status = array(
                            'winery_id' => $winery_id,
                            'brief' => $_REQUEST['status'][$key],  //首图存入Winery表
                            'description' => $_REQUEST['description'][$key],
                            'queue' => $_REQUEST['img_queue'][$key],
                            );
                      }

                      M('WineryStatus')->where($condition)->save($winery_status);

                }else{//添加

                            $winery_status = array(
                            'pic_url' => $pic,
                            'winery_id' => $winery_id,
                            'brief' => $_REQUEST['status'][$key],  //首图存入Winery表
                            'description' => $_REQUEST['description'][$key],
                            'queue' => $_REQUEST['img_queue'][$key],
                            );

                        $this->_insert(D('WineryStatus'),$winery_status);//插入酒庄荣誉图片             
             }
          }
          
            $this->redirect("Winery/introduce?winery_id=$winery_id");
        }
        $this->assign('statusInfo', $statusInfo);
        $this->assign('winery_id', $winery_id);
        $this->assign('img_count', $img_count);
        $this->assign('qy_list', $qy_list);
        $this->display();
    }
    /**
     * release
     * 发布酒款
     * @access public
     * @return void
     */
    public function release() {
        $qy_list = parent::getQyInfo();//获取企业的相关信息

        $map = array('qy_id' =>$qy_list['id']);
        $winery =  M('Winery')->where($map)->find(); //通过企业ID查询代理商ID
        $winery_id = $winery['id'];

        if($this->isPost()) {
        //获取相关信息
            if($_POST['kjcountrytext']) {//判断是否与酒款关联
                $country = Input::getVar($_POST['kjcountrytext']);
                $country = explode(",",$country);
                $country_jk_id = $country[0];
                $country_fname = $country[1];
                $country_cname = $country[2];
            }else if($_POST['countryname']) {  //判断select框国家是否关联，获取国家信息
                    $country = Input::getVar($_POST['countryname']);
                    $country = explode("|",$country);//将字符串拆分
                    $country_jk_id = $country[0];
                    $country_fname = $country[1];
                    $country_cname = $country[2];
                }else { //选择其它时，获取信息
                    $country_cname = Input::getVar($_POST['kcountrytext']);
                    $country_fname = Input::getVar($_POST['kcountrytext']);
                    $other = 1;
                }
            $country_id['id'] = parent::insertCountry($country_cname, $country_fname, $country_jk_id, $other);

            if($_POST['kjregiontext']) { //获取产区信息
                $region_id = parent::insertAllReleat($_POST['kjregiontext'], $country_id['id']);
            }else if($_POST['regionname1']) {  //判断select框产区是否关联，获取产区信息
                    $region1 = Input::getVar($_POST['regionname1']);//从post获取数据
                    $region1 = explode("|",$region1);//将字符串拆分
                    $region2 = Input::getVar($_POST['regionname2']);
                    $region2 = explode("|",$region2);//将字符串拆分
                    $region_id = parent::insertPartRegionReleat($region1, $region2,$country_id['id']);//插入局部关联产区
                }else { //选择其它时，获取信息
                    $region1_name = Input::getVar($_POST['kregiontext']); //通过获取文本框内容提交
                    $region_id = parent::insertNoReleat($region1_name,$country_id['id']);
                }
            //把酒款类型信息插入
            if($_POST['kjtypetext']) {//判断是否与酒款关联
                $type_id = parent::insertAllTypeReleat($_POST['kjtypetext']);
            } else if($_POST['typename1']) {  //判断select框类型是否关联，获取类型信息
                    $type1 = Input::getVar($_POST['typename1']);
                    $type1 = explode("|",$type1);//将字符串拆分
                    $type2 = Input::getVar($_POST['typename2']);
                    $type2 = explode("|",$type2);//将字符串拆分
                    $type_id = parent::insertPartTypeReleat($type1, $type2);
                } else { //选择其他时，类型信息存入
                    $type1_name = Input::getVar($_POST['ktypetext']); //通过获取文本框内容提交
                    $type_id = parent::insertNoTypeReleat($type1_name);
                }
            //获取酒款葡萄品种
            if($_POST['kjgrapetext']) {
                $variety = Input::getVar($_REQUEST['kjgrapetext']);
            }else {
                $var= $_REQUEST['variety'];
                $percent = $_REQUEST['percent'];
                $variety = $dot = '';
                foreach($var as $key =>$value) {
                    if($value) {
                        $variety .= $dot . $value . ":" .$percent[$key]."%";
                        $dot = ',';
                    }
                }

            }

            //把酒款相应信息插入酒款表
            //获取酒款相应信息
            if($_POST['kjwinetext']) {//判断酒款是否关联
                $wine = explode(",",$_POST['kjwinetext']);
                $jk_id = $wine[0];
                $fname = $wine[1];
                $cname = $wine[2];
            }else { //插入非关联酒款名
                $cname = Input::getVar($_POST['kwinetext']);
                $fname = Input::getVar($_POST['kwinetext']);
            }

            $price_type = Input::getVar($_POST['price']);
            if($price_type == -1) {//获取发布价格类型和信息
                $price = -1;
            }else {
                if($_POST['moneytype'] == '$') {//如果是美元，将其转化成人民币在存入

                    $inital_price = Input::getVar($_POST['pricetext']);//原价格
                    if($inital_price!="") {
                        $price =  $inital_price * parent::getMoneyrate();   //转换成人民币价格
                    }else {
                        $price = -1;
                        $inital_price = -1;
                    }
                }else {
                    $price = Input::getVar($_POST['pricetext']);
                }
            }
            $wine_data = array(
                'winery_id' => $winery_id,
                'fname' => $fname,
                'cname' => $cname,
                'variety' => $variety,
                'lowest_buy_amount' =>$_REQUEST['lowest_buy_amount'],
                'country_id' => $country_id['id'],
                'price' => empty($price)?'-1':$price,
                'degree' => $_REQUEST['degree'],
                'volume' => $_REQUEST['volume'],
                'description' => $_REQUEST['description'],
                'region_id' => $region_id['0'],
                'regionsub_id' => $region_id['1'],
                'type_id' => $type_id['0'],
                'typesub_id' => $type_id['1'],
                'jk_id' => $jk_id,
                'down_shelf' => -1,//发布酒款未上架
                'money_type' => Input::getVar($_POST['moneytype']),
                'pic_url' => $_REQUEST['img_file'][0],
                'inital_price' => empty($inital_price)?'-1':$inital_price,//存入未转化前的美元价格
                'price' => $price,//存入未转化前的美元价格
                'honor' =>$_REQUEST['honor'],//酒款荣誉
            );
            if($fname || $cname) {  //如果酒款名有值，则存入数据库
                $wine_id = $this->_insert(D('Wine'),$wine_data);
            }

            if($wine_id) {
                $img_file = isset($_REQUEST['img_file']) ? $_REQUEST['img_file'] : array();
                $img_queue =isset($_REQUEST['img_queue']) ? $_REQUEST['img_queue'] :array();

                M("WineImg")->where("wine_id=$wine_id")->delete();

                foreach ( $img_file as $key =>$file ) {
                    $desDir = C('UPLOAD_PATH').'Wine'.DS.'images'.$file;
                    if(!is_dir(dirname($desDir))) {
                        mkdir(dirname($desDir), 0755, true);
                    }
                    rename(C('UPLOAD_PATH').'tmp'.$file, $desDir);
                    $img_data = array(
                        'url' => $file,
                        'queue' => $img_queue[$key],
                        'wine_id' => $wine_id
                    );
                    $id=$this->_insert(D('WineImg'),$img_data);
                }
                if($_REQUEST['parameter'] == 0) {//判断是否继续发布酒款，选择跳转页面
                    $this->redirect("Winery/manage?winery_id=$winery_id");//图片添加成功后，跳转到管理页面
                }else {
                    $this->redirect("Winery/release");
                }
            }
        }

        $this->assign('winery_id', $winery_id);
        $this->assign('qy_list', $qy_list);
        $this->display();
    }


    /**
     * manage
     * 酒款管理
     * @access public
     * @return void
     */
    public function manage() {

        $qy_list = parent::getQyInfo();//获取企业的相关信息
        $map = array('qy_id' =>$qy_list['id']);
        $winery =  M('Winery')->where($map)->find(); //通过企业ID查询代理商ID
        $winery_id = $winery['id'];
        $keyword = Input::getVar($_REQUEST['keyword']);//获取搜索关键字
        $map = array();
        $url = '';
        if($keyword) {
            $map_k['fname'] = array('like', '%'.$keyword.'%');
            $map_k['cname'] = array('like', '%'.$keyword.'%');
            $map_k['_logic'] = 'or';
            $map['_complex'] = $map_k;
            $url .= '&keyword=' . $keyword;
        }
        $map = array('winery_id' => $winery_id);
        $list = $this->_list(D('Wine'), $map, 10, $url); //分页显示
        foreach($list as $key=>$val) {
            $type = D('WineType')->where(array('id'=> $val['type_id']))->find();//查询相应酒款类型
            $list[$key]['typeName'] = $type['fname'];//获取酒款类型名

            $img = D('WineImg')->where(array('wine_id'=> $val['id']))->order('queue ASC')->limit(1)->select();
            if(!empty($img)) {
                $imglist = $this->parse_img_url($img, 'Wine');
                $list[$key]['img'] = $imglist[0]['url'];
            }else {
                $list[$key]['img'] = '';
            }
        }

        $this->assign('typeName', $typeName);
        $this->assign('list', $list);

        $this->assign('winery_id', $winery_id);
        $this->assign('qy_list', $qy_list);
        $this->display();

    }

    /**
     * del
     * 酒款删除
     * @access public
     * @return void
     */
    public function del() {
    //获取id
        $winery_id = Input::getVar($_REQUEST['winery_id']);
        $id = Input::getVar($_REQUEST['id']);
        $ids = $_REQUEST['ids'];//获取批量删除
        if($id) { //单个删除
            $map = array('id' => $id);
        } elseif($ids) { //批量删除
            $map = array('id' => array('in', $ids));
        }
        if($map) {
            D('Wine')->where($map)->delete();
            $this->redirect("Winery/manage?winery_id =$winery_id");
        }
    }

    /**
     * down
     * 酒款下架
     * @access public
     * @return void
     */
    public function down() {
    //获取id
        $winery_id = Input::getVar($_REQUEST['winery_id']);
        $id = Input::getVar($_REQUEST['id']);
        $ids = $_REQUEST['ids'];  //获取批量删除
        if($id) { //单个删除
            $map = array('id' => $id);
        } else if($ids) { //批量删除
                $map = array('id' => array('in', $ids));
            }else {
                $this->redirect("Winery/manage?winery_id =$winery_id");
            }
        if($map) {
            $data = array(
                'down_shelf' => '-1',
            );
            D('Wine')->where($map)->save($data);
            $this->redirect("Winery/manage?winery_id =$winery_id");
        }
    }

    /**
     * up
     * 酒款上架
     * @access public
     * @return void
     */
    public function up() {

        $winery_id = Input::getVar($_REQUEST['winery_id']);//获取酒庄id
        $id = Input::getVar($_REQUEST['id']);
        $ids = $_REQUEST['ids'];//获取批量上架
        if($id) {//单个上架
            $map = array('id' => $id);
        } else if($ids) {//批量上架
                $map = array('id' => array('in', $ids));
            }else {
                $this->redirect("Winery/manage?winery_id =$winery_id");
            }

        $winery_map = array('id' => $winery_id);
        $winery_info = M('Winery')->where($winery_map)->find();//查询酒庄信息
        $qy_map = array('id' => $winery_info['qy_id']);
        $qy_info = M()->Table('ym_users_qy')->where($qy_map)->find();//根据企业id查询企业信息

        if( $qy_info['comany_valid'] == 2) {//判断查询条件和酒庄认证是否成功
            $data = array(
                'down_shelf' => '0',
            );
            D('Wine')->where($map)->save($data);
            $this->redirect("Winery/manage?winery_id =$winery_id");
        }else {
            $this->redirect("Index/ywprompt");//跳转到通行证首页进行认证
        }

    }

    /**
     * edit
     * 酒款编辑
     * @access public
     * @return void
     */
    public function edit() {
    //获取id
        $winery_id = Input::getVar($_REQUEST['winery_id']);
        $wine_id = Input::getVar($_REQUEST['id']);//获取酒款id
        $wineInfo = D('Wine')->where(array('id' => $wine_id))->find();

        $type = D('WineType')->where(array('id' => $wineInfo['type_id']))->find();//查询酒款父类型
        $typeSub = D('WineType')->where(array('id' => $wineInfo['typesub_id']))->find();//查询酒款父类型
        $country = D('Country')->where(array('id' => $wineInfo['country_id']))->find();//查询相应酒款国家
        $region = D('Region')->where(array('id' => $wineInfo['region_id']))->find();//查询相应酒款父产区
        $regionSub = D('Region')->where(array('id' => $wineInfo['regionsub_id']))->find();//查询相应酒款父产区
        $variety = explode(",",$wineInfo['variety']);//将葡萄品种拆分数组
        $grape = array();
        foreach($variety as $key =>$value) {
            if($value) {
                $grape[] = explode(":",$value);//将葡萄品种拆分数组
            }
        }

        $img_condition = array('wine_id' =>$wine_id);//获取企业图片
        $img_list = D('WineImg')->where($img_condition)->select();
        $img_count = D('WineImg')->where($img_condition)->count();

        if($this->isPost()) {
        //获取相关信息
            if($_POST['kecountrytext']) {//判断是否保留发布酒款中country信息
                $country_id['id'] = Input::getVar($_POST['kecountrytext']);
            }else {
                if($_POST['kjcountrytext']) {//判断是否与酒款关联
                    $country = Input::getVar($_POST['kjcountrytext']);
                    $country = explode(",",$country);
                    $country_jk_id = $country[0];
                    $country_fname = $country[1];
                    $country_cname = $country[2];
                    $other = -1;
                }else if($_POST['countryname']) {  //判断select框国家是否关联，获取国家信息
                        $country = Input::getVar($_POST['countryname']);
                        $country = explode("|",$country);//将字符串拆分
                        $country_jk_id = $country[0];
                        $country_fname = $country[1];
                        $country_cname = $country[2];
                        $other = -1;
                    }else { //选择其它时，获取信息
                        $country_cname = Input::getVar($_POST['kcountrytext']);
                        $country_fname = Input::getVar($_POST['kcountrytext']);
                        $other = 1;
                    }
                //插入国家信息
                $country_id['id'] = parent::insertCountry($country_cname, $country_fname, $country_jk_id, $other);
            }
            if($_POST['keregiontext']) {//判断是否保存发布时的类型
                $region = explode('|',$_POST['keregiontext']);
                $region_id[0] =  $region[0];
                $region_id[1] =  $region[1];
            }else {
                if($_POST['kjregiontext']) {
                    $region_id = parent::insertAllReleat($_POST['kjregiontext'], $country_id['id']);
                }else if($_POST['regionname1']) {  //判断select框产区是否关联，获取产区信息
                        $region1 = Input::getVar($_POST['regionname1']);//从post获取数据
                        $region1 = explode("|",$region1);//将字符串拆分
                        $region2 = Input::getVar($_POST['regionname2']);
                        $region2 = explode("|",$region2);//将字符串拆分
                        $region_id = parent::insertPartRegionReleat($region1, $region2,$country_id['id']);
                    }else { //选择其它时，获取信息
                        $region1_name = Input::getVar($_POST['kregiontext']); //通过获取文本框内容提交
                        $region_id = parent::insertNoReleat($region1_name,$country_id['id']);
                    }
            }
            if($_POST['ketypetext']) {//判断是否保存发布时的类型
                $type = explode('|',$_POST['ketypetext']);
                $type_id[0] = $type[0];
                $type_id[1] = $type[1];
            }else {
                if($_POST['kjtypetext']) {//判断是否与酒款关联
                    $type_id = parent::insertAllTypeReleat($_POST['kjtypetext']);
                } else if($_POST['typename1']) {  //判断select框类型是否关联，获取类型信息
                        $type1 = Input::getVar($_POST['typename1']);
                        $type1 = explode("|",$type1);//将字符串拆分
                        $type2 = Input::getVar($_POST['typename2']);
                        $type2 = explode("|",$type2);//将字符串拆分
                        $type_id = parent::insertPartTypeReleat($type1, $type2);
                    } else { //选择其他时，类型信息存入
                        $type1_name = Input::getVar($_POST['ktypetext']); //通过获取文本框内容提交
                        $type_id = parent::insertNoTypeReleat($type1_name);
                    }
            }
            //获取酒款葡萄品种
            if($_POST['kjgrapetext']) {
                $variety = Input::getVar($_POST['kjgrapetext']);
            }else {
                $var= $_POST['variety'];
                $percent = $_POST['percent'];
                $variety = $dot = '';
                foreach($var as $key =>$value) {
                    if($value) {
                        $variety .= $dot . $value . ":" .$percent[$key]."%";
                        $dot = ',';
                    }
                }
            }

            //把酒款相应信息插入酒款表
            //获取酒款相应信息
            if($_POST['kjwinetext']) {//判断酒款是否关联
                $wine = explode(",",$_POST['kjwinetext']);
                $jk_id = $wine[0];
                $fname = $wine[1];
                $cname = $wine[2];
            }else { //非关联酒款名
                $cname = Input::getVar($_POST['kwinetext']);
                $fname = Input::getVar($_POST['kwinetext']);
            }

            $price_type = Input::getVar($_POST['price']);
            if($price_type == -1) {//获取发布价格类型和信息
                $price = -1;
            }else {
                if($_POST['moneytype'] == '$') {//如果是美元，将其转化成人民币在存入
                    $inital_price = Input::getVar($_POST['pricetext']);//原价格
                    if($inital_price!="") {
                        $price =  $inital_price * parent::getMoneyrate();   //转换成人民币价格
                    }else {
                        $price = -1;
                        $inital_price = -1;
                    }
                }else {
                    $price = Input::getVar($_POST['pricetext']);
                }
            }

            $wine_data = array(
                'winery_id' => $winery_id,
                'fname' => $fname,
                'cname' => $cname,
                'variety' => $variety,
                'lowest_buy_amount' => $_REQUEST['lowest_buy_amount'],
                'country_id' => $country_id['id'],
                'price' => empty($price)? '-1':$price,
                'degree' => $_REQUEST['degree'],
                'volume' => $_REQUEST['volume'],
                'description' => $_REQUEST['description'],
                'region_id' => $region_id['0'],
                'regionsub_id' => $region_id['1'],
                'type_id' => $type_id['0'],
                'typesub_id' => $type_id['1'],
                'jk_id' => empty($jk_id)? '-1':$jk_id,
                'down_shelf' => 0,
                'money_type' => Input::getVar($_POST['moneytype']),
                'pic_url' =>  $_REQUEST['img_file'][0],            //酒款首图
                'honor' =>$_REQUEST['honor'],
                'inital_price' => empty($inital_price)? '-1':$inital_price,//存入未转化前的美元价格
                'price' => $price,//存入未转化前的美元价格
            );
            $condition = array('id' => $wine_id);
            D('Wine')->where($condition)->save($wine_data);
            //修改酒款图片
            if($wine_id) {
                $img_file = isset($_REQUEST['img_file']) ? $_REQUEST['img_file'] : array();
                $img_queue =isset($_REQUEST['img_queue']) ? $_REQUEST['img_queue'] :array();

                M("WineImg")->where("wine_id=$wine_id")->delete();

                foreach ( $img_file as $key =>$file ) {
                    $desDir = C('UPLOAD_PATH').'Wine'.DS.'images'.$file;
                    if(!is_dir(dirname($desDir))) {
                        mkdir(dirname($desDir), 0755, true);
                    }
                    rename(C('UPLOAD_PATH').'tmp'.$file, $desDir);
                    $img_data = array(
                        'url' => $file,
                        'queue' => $img_queue[$key],
                        'wine_id' => $wine_id
                    );
                    $id=$this->_insert(D('WineImg'),$img_data);
                }
                $this->redirect("Winery/manage?winery_id=$winery_id");
            }

        }
        $this->assign('winery_id', $winery_id);
        $this->assign('wine_id', $wine_id);//获取酒款id
        $this->assign('wineInfo', $wineInfo);
        $img_list = $this->parse_img_url($img_list, 'Wine');
        $this->assign('img_list', $img_list);
        $this->assign('img_count', $img_count);
        $this->assign('type', $type);
        $this->assign('typeSub', $typeSub);
        $this->assign('country', $country);
        $this->assign('region', $region);
        $this->assign('regionSub', $regionSub);
        $this->assign('typeList', $typeList);
        $this->assign('grape', $grape);
        $this->display();
    }

    public function upload() {
        $this->_uploads();
    }
    /**
     * uploadWinery
     * 上传酒庄图片
     * @access public
     * @return void
     */
    public function uploadWinery() {
        $qy_list = parent::getQyInfo();//获取企业的相关信息
        $qy_id = $qy_list['id'];
        $map = array('qy_id' =>$qy_id);
        $winery =  M('Winery')->where($map)->find(); //通过企业ID查询代理商ID
        $winery_id = $winery['id'];

        $result = $this->_newuploads();
        if($result['error']==0) {
            $img_data = array(
                'winery_id' => $winery_id,
                'url' => $result['url'],
                'queue' => -1,
            );
            $condition = array(
                'winery_id' => $winery_id,
            );
            $id=$this->_insert(D('WineryImg'),$img_data);
            $result['id'] = $id;
        }
        echo json_encode($result);

    }

    /**
     * delImg
     * 删除酒庄上传图片
     * @access public
     * @return void
     */
    public function delImg() {
        $id = $_REQUEST['id'];
        $map = array(
            'id' => $id,
        );
        $img_data =  M('WineryImg')->where($map)->find();
        if($img_data['queue'] ==1) {
            M('WineryImg')->where($map)->delete(); // 删除id为5的用户数据

            $allImg = $_REQUEST['allImg'];
            if($allImg) {
                $allImg = explode(",",$allImg);
                sort($allImg);
                $value = array(
                    'queue' => 1,
                );
                M('WineryImg')->where("id=$allImg[0]")->save($value); // 删除id为5的用户数据
            }
        }
        M('WineryImg')->where($map)->delete(); // 删除id为5的用户数据
    }


}
