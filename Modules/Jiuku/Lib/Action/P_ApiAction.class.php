<?php
/**
* @file P_ApiAction.class.php     #文件名
* 
* @brief  公共API，无权限，无需token        #程序文件描述 
* 
* Copyright(C) 2010-2015 Easescent.com, Inc. or its affiliates. All Rights Reserved.     #版权信息
* 
* @version $Id$         #版本号，由svn功能自动生成，不用修改
* @author Goni, goni@sina.com         #程序作者
* @date 2013-01-01                 #日期
*/

/**
* @class P_ApiAction       # 类名           
* @brief 公共API，无权限，无需token。如用ajax入口访问，则使用各方法的加密action    #类功能描述  
*         
*/
class P_ApiAction extends Action{
    
    /**
    * @brief _initialize    #方法名+描述
    *
    * @param $xxxxx {类型}    #方法参数描述，大括号内注明类型
    *
    * @return $xxxxxx{类型}    #返回值
    */
    public function _initialize() {
        import('@.ORG.Util.Json');
    }
    
    /**
    * @brief linkRedis    判断redis服务是否开启    #方法名+描述
    *
    * @param $xxxxx {类型}    #方法参数描述，大括号内注明类型
    *
    * @return $Redis{resource}|false{bool}    #返回值
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
    * @brief countryFind    获取服务器时间    加密action=    WXpmeGY8Q0xSY3o8dHZnR3p%2Bdg%3D%3D    #方法名+描述
    *
    *
    * @return echo{json}    #返回值
    */
    public function getTime(){
        $this->echo_exit(time());
    }
    
    /**
    * @brief countryFind    通过国家ID获取国家信息    加密action=    XX5ifGI4R0hWZ344dHhieWNlblF%2BeXM%3D    #方法名+描述
    *
    * @param $_GET['id'] {int}    $_GET['st'] {int}    #方法参数描述，大括号内注明类型
    *
    * @return echo{json}    #返回值
    */
    public function countryFind(){
        if(empty($_GET['id']) || intval($_GET['id']) <= 0){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
        $map['id'] = intval($_GET['id']);
        $map['is_del'] = '-1';
        $res = D('Country')->where($map)->find();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        $return = array('id' => $res['id'],'fname' => $res['fname'],'cname' => $res['cname'],);
        //是否获取完整数据is_full
        if(isset($_GET['isf']) && intval($_GET['isf']) === 1){
            $return['aname'] = $res['aname'];
            $return['description'] = $res['description'];
            $return['content'] = $res['content'];
            $return['seo_t'] = $res['seo_t'];
            $return['seo_k'] = $res['seo_k'];
            $return['seo_d'] = $res['seo_d'];
        }
        //是否获取图片is_img
        if(isset($_GET['isi']) && intval($_GET['isi']) === 1){
            $return['img'] = NULL;
            $img_map = array('country_id'=>$res['id'],'is_del'=>'-1');
            if($map['status']) $img_map['status'] = '1';
            $img_res = D('CountryImg')->field('id,filename,description,alt')->where($img_map)->order('queue ASC')->select();
            foreach($img_res as $key=>$val){
                $img_size = getimagesize(C('DOMAIN.UPLOAD').'Jiuku/Country/images/'.$val['filename']);
                $return['img'][] = array(
                                         'id' => $val['id'],
                                         'filename' => C('DOMAIN.UPLOAD').'Jiuku/Country/images/'.$val['filename'],
                                         'description' => $val['description'],
                                         'alt' => $val['alt'],
                                         'width' => $img_size[0],
                                         'height' => $img_size[1],
                                         );
            }
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
    }
    
    /**
    * @brief countrySelect    获取国家列表    加密action=    U3Bscmw2SUZYaXA2enZsd21rYEp8dXx6bQ%3D%3D    #方法名+描述
    *
    * @param $_GET['st'] {int}    #方法参数描述，大括号内注明类型
    *
    * @return echo{json}    #返回值
    */
    public function countrySelect(){
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
        $map['is_del'] = '-1';
        $res = D('Country')->where($map)->order('CONVERT( `cname` USING gbk )')->select();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        foreach($res as $key=>$val){
            $return[$key] = array(
                                  'id' => $val['id'],
                                  'fname' => $val['fname'],
                                  'cname' => $val['cname'],
                                  );
        }
        if(isset($_GET['hr']) && intval($_GET['hr']) === 1){
            $haveregionid = $return1 = array();
            $haveregion_map['is_del'] = '-1';
            if($map['status']) $haveregion_map['status'] = '1';
            $haveregionid = D('Region')->where($haveregion_map)->group('country_id')->getfield('country_id',true);
            foreach($return as $key=>$val){
                if(in_array($val['id'],$haveregionid)){
                    $return1[] = $val;
                }
            }
            $return = $return1;
        }
        if(isset($_GET['hwy']) && intval($_GET['hwy']) === 1){
            $havewineryid = $return1 = array();
            $havewinery_map['is_del'] = '-1';
            if($map['status']) $havewinery_map['status'] = '1';
            $havewineryid = D('Winery')->where($havewinery_map)->group('country_id')->getfield('country_id',true);
            foreach($return as $key=>$val){
                if(in_array($val['id'],$havewineryid)){
                    $return1[] = $val;
                }
            }
            $return = $return1;
        }
        //是否获取图片is_img
        if(isset($_GET['isi']) && intval($_GET['isi']) === 1){
            foreach($res as $key=>$val){
                $return[$key]['img'] = NULL;
                $img_map = array('country_id'=>$val['id'],'is_del'=>'-1');
                if($map['status']) $img_map['status'] = '1';
                $img_res = D('CountryImg')->field('id,filename,description,alt')->where($img_map)->order('queue ASC')->select();
                foreach($img_res as $k=>$v){
                    //$img_size = getimagesize(C('DOMAIN.UPLOAD').'Jiuku/Country/images/'.$val['filename']);
                    $return[$key]['img'][$k] = array(
                                                     'id' => $v['id'],
                                                     'filename' => C('DOMAIN.UPLOAD').'Jiuku/Country/images/'.$v['filename'],
                                                     'description' => $v['description'],
                                                     'alt' => $v['alt'],
                                                     //'width' => $img_size[0],
                                                     //'height' => $img_size[1],
                                                     );
                }
            }
        }
        $count = $this->getParamCount();
        if($count){
            $return_group = array_chunk($return,$count);
            $return = $return_group[0];
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
    }
    
    /**
    * @brief RegionlevelidFindCountry    通过产区级别ID获取国家信息    加密action=    bk1RT1ELdHtlVE0LdkFDTUtKSEFSQUhNQGJNSkBnS1FKUFZd    #方法名+描述
    *
    * @param $_GET['id'] {int}    $_GET['st'] {int}    #方法参数描述，大括号内注明类型
    *
    * @return echo{json}    #返回值
    */
    public function RegionlevelidFindCountry(){
        if(empty($_GET['id']) || intval($_GET['id']) <= 0){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
        $country_id = D('Regionlevel')->where(array('id'=>intval($_GET['id']),'is_del'=>'-1'))->getfield('country_id');
        if(!$country_id){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        $map['id'] = $country_id;
        $map['is_del'] = '-1';
        $res = D('Country')->field('`id`,`fname`,`cname`')->where($map)->find();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$res));
    }
    
    /**
    * @brief RegionidFindCountry    通过产区ID获取国家信息    加密action=    VXZqdGowT0Beb3YwTXp4dnBxdntZdnF7XHBqcWttZg%3D%3D    #方法名+描述
    *
    * @param $_GET['id'] {int}    $_GET['st'] {int}      #方法参数描述，大括号内注明类型
    *
    * @return echo{json}    #返回值
    */
    public function RegionidFindCountry(){
        if(empty($_GET['id']) || intval($_GET['id']) <= 0){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
        $country_id = D('Region')->where(array('id'=>intval($_GET['id']),'is_del'=>'-1'))->getfield('country_id');
        if(!$country_id){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        $map['id'] = $country_id;
        $map['is_del'] = '-1';
        $res = D('Country')->field('`id`,`fname`,`cname`')->where($map)->find();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$res));
    }
    
    /**
    * @brief CountryidSelectRegionlevel    通过国家ID获取产区分级列表    加密action=    bE9TTVMJdnlnVk8JZUlTSFJUX09CdUNKQ0VSdENBT0lISkNQQ0o%3D    #方法名+描述
    *
    * @param $_GET['id'] {int}    $_GET['st'] {int}      #方法参数描述，大括号内注明类型
    *
    * @return echo{json}    #返回值
    */
    public function CountryidSelectRegionlevel(){
        if(empty($_GET['id']) || intval($_GET['id']) <= 0){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        $map['country_id'] = intval($_GET['id']);
        $map['is_del'] = '-1';
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
        $res = D('Regionlevel')->where($map)->select();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        foreach($res as $key=>$val){
            $return[] = array(
                              'id'=>$val['id'],
                              'sname'=>$val['sname'],
                              'cname'=>$val['cname'],
                              );
        }
        $count = $this->getParamCount();
        if($count){
            $return_group = array_chunk($return,$count);
            $return = $return_group[0];
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
    }
    
    /**
    * @brief regionlevelFind    通过产区分级ID获取产区分级信息    加密action=    UXJucG40S0Raa3I0aX58cnR1d35tfnddcnV%2F    #方法名+描述
    *
    * @param $_GET['id'] {int}    $_GET['st'] {int}    #方法参数描述，大括号内注明类型
    *
    * @return echo{json}    #返回值
    */
    public function regionlevelFind(){
        if(empty($_GET['id']) || intval($_GET['id']) <= 0){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
        $map['id'] = intval($_GET['id']);
        $map['is_del'] = '-1';
        $res = D('Regionlevel')->where($map)->find();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        $return = array(
                        'id' => $res['id'],
                        'sname' => $res['sname'],
                        'cname' => $res['cname'],
                        );
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
    }
    
    /**
    * @brief CountryidSelectRegion    通过国家ID获取产区列表    加密action=    a0hUSlQOcX5gUUgOYk5UT1VTWEhFckRNREJVc0RGSE5P    #方法名+描述
    *
    * @param $_GET['id'] {int}    $_GET['st'] {int}    #方法参数描述，大括号内注明类型
    *
    * @return echo{json}    #返回值
    */
    public function CountryidSelectRegion(){
        if(empty($_GET['id']) || intval($_GET['id']) <= 0){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
        //是否获取所有所属该国家的数据allregion 1:是 其他:仅获取顶级产区
        if(empty($_GET['ar']) || intval($_GET['ar']) !== 1){
            $map['pid'] = 0;
        }
        $map['country_id'] = intval($_GET['id']);
        $res = D('Region')->where($map)->select();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        foreach($res as $key=>$val){
            $return[$key] = array(
                                  'id' => $val['id'],
                                  'fname' => $val['fname'],
                                  'cname' => $val['cname'],
                                  );
        }
        //是否获取完整数据is_full
        if(isset($_GET['isf']) && intval($_GET['isf']) === 1){
            foreach($res as $key=>$val){
                $return[$key] = array(
                                      'aname' => $val['aname'],
                                      'soil' => $val['soil'],
                                      'climate' => $val['climate'],
                                      'term' => $val['term'],
                                      'latitude' => $val['latitude'],
                                      'longitude' => $val['longitude'],
                                      'description' => $val['description'],
                                      'content' => $val['content'],
                                      'seo_t' => $val['seo_t'],
                                      'seo_k' => $val['seo_k'],
                                      'seo_d' => $val['seo_d'],
                                      );
            }
        }
        $return = $this->getParamCountPage($return);
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
    }
    
    /**
    * @brief RegionidSelectRegion    通过产区ID获取子产区列表    加密action=    aklVS1UPcH9hUEkPckVHSU9OSURzRUxFQ1RyRUdJT04%3D    #方法名+描述
    *
    * @param $_GET['id'] {int}    $_GET['st'] {int}    #方法参数描述，大括号内注明类型
    *
    * @return echo{json}    #返回值
    */
    public function RegionidSelectRegion(){
        if(empty($_GET['id']) || intval($_GET['id']) <= 0){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
        $map['pid'] = intval($_GET['id']);
        $res = D('Region')->where($map)->select();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        foreach($res as $key=>$val){
            $return[$key] = array(
                                  'id' => $val['id'],
                                  'fname' => $val['fname'],
                                  'cname' => $val['cname'],
                                  );
        }
        //是否获取完整数据is_full
        if(isset($_GET['isf']) && intval($_GET['isf']) === 1){
            foreach($res as $key=>$val){
                $return[$key] = array(
                                      'aname' => $val['aname'],
                                      'soil' => $val['soil'],
                                      'climate' => $val['climate'],
                                      'term' => $val['term'],
                                      'latitude' => $val['latitude'],
                                      'longitude' => $val['longitude'],
                                      'description' => $val['description'],
                                      'content' => $val['content'],
                                      'seo_t' => $val['seo_t'],
                                      'seo_k' => $val['seo_k'],
                                      'seo_d' => $val['seo_d'],
                                      );
            }
        }
        $return = $this->getParamCountPage($return);
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
    }
    
    /**
    * @brief CountryidRegionnameSelectRegion    通过国家ID及产区外文名/中文名获取产区列表    加密action=    YUJeQF4Ee3RqW0IEaEReRV9ZUkJPeU5MQkRFRUpGTnhOR05IX3lOTEJERQ%3D%3D    #方法名+描述
    *
    * @param $_GET['id'] {int}    $_GET['kw'] {string}    $_GET['st'] {int}    #方法参数描述，大括号内注明类型
    *
    * @return echo{json}    #返回值
    */
    public function CountryidRegionnameSelectRegion(){
        if(empty($_GET['id']) || intval($_GET['id']) <= 0){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        if(empty($_GET['kw'])){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
        $map['country_id'] = intval($_GET['id']);
        $map['is_del'] = '-1';
        if(true==false){
            //Redis备用
            /*$idarr = array_unique(array_merge($Redis->sMembers('jiuku_winefname_wine:'.strtolower($_GET['kw'])),$Redis->sMembers('jiuku_winecname_wine:'.strtolower($_GET['kw']))));
            if(!$idarr){
                $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
            }
            $map['id'] = array('in',$idarr);*/
        }else{
            $map['_complex'] = array('fname'=>array('like','%'.$_GET['kw'].'%'),'cname'=>array('like','%'.$_GET['kw'].'%'),'_logic'=>'or');
        }
        $res = D('Region')->where($map)->select();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        foreach($res as $key=>$val){
            $return[] = array(
                              'id'=>$val['id'],
                              'fname'=>$val['fname'],
                              'cname'=>$val['cname'],
                              );
        }
        $count = $this->getParamCount();
        if($count){
            $return_group = array_chunk($return,$count);
            $return = $return_group[0];
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
    }
    
    /**
    * @brief wineryFind    通过酒庄ID获取酒庄信息    加密action=    XH9jfWM5RklXZn85YX94c2RvUH94cg%3D%3D    #方法名+描述
    *
    * @param $_GET['id'] {int}    $_GET['st'] {int}    $_GET['isf'] {int}    $_GET['isi'] {int}    $_GET['isc'] {int}    $_GET['ish'] {int}    $_GET['isg'] {int}    #方法参数描述，大括号内注明类型
    *
    * @return echo{json}    #返回值
    */
    public function wineryFind(){
        if(empty($_GET['id']) || intval($_GET['id']) <= 0){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
        $map['id'] = intval($_GET['id']);
        $map['is_del'] = '-1';
        $res = D('Winery')->where($map)->find();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        $return = array('id' => $res['id'],'fname' => $res['fname'],'cname' => $res['cname']);
        //是否获取完整数据is_full
        if(isset($_GET['isf']) && intval($_GET['isf']) === 1){
            $return['aname'] = $res['aname'];
            $return['tel'] = $res['tel'];
            $return['url'] = $res['url'];
            $return['blog_url'] = $res['blog_url'];
            $return['address'] = $res['address'];
            $return['acreage'] = $res['acreage'];
            $return['plant_age'] = $res['plant_age'];
            $return['yield'] = $res['yield'];
            $return['oak_storage_duration'] = $res['oak_storage_duration'];
            $return['g_map'] = C('DOMAIN.UPLOAD').'Jiuku/Winery/map/'.$res['g_map'];
            $return['description'] = $res['description'];
            $return['content'] = $res['content'];
            $return['seo_t'] = $res['seo_t'];
            $return['seo_k'] = $res['seo_k'];
            $return['seo_d'] = $res['seo_d'];
        }
        //是否获取图片is_img
        if(isset($_GET['isi']) && intval($_GET['isi']) === 1){
            $return['img'] = NULL;
            $img_map = array('winery_id'=>$res['id'],'is_del'=>'-1');
            if($map['status']) $img_map['status'] = '1';
            $img_res = D('WineryImg')->field('id,filename,description,alt')->where($img_map)->order('queue ASC')->select();
            foreach($img_res as $key=>$val){
                $img_size = getimagesize(C('DOMAIN.UPLOAD').'Jiuku/Winery/images/'.$val['filename']);
                $return['img'][] = array(
                                         'id' => $val['id'],
                                         'filename' => C('DOMAIN.UPLOAD').'Jiuku/Winery/images/'.$val['filename'],
                                         'description' => $val['description'],
                                         'alt' => $val['alt'],
                                         'width' => $img_size[0],
                                         'height' => $img_size[1],
                                         );
            }
        }
        //是否获取所属国家is_country
        if(isset($_GET['isc']) && intval($_GET['isc']) === 1){
            $return['country'] = NULL;
            $country_map = array('id'=>$res['country_id'],'is_del'=>'-1');
            if($map['status']) $country_map['status'] = '1';
            $return['country'] = D('Country')->field('id,fname,cname')->where($country_map)->find();
        }
        //是否获取荣誉is_honor
        if(isset($_GET['ish']) && intval($_GET['ish']) === 1){
            $return['honor'] = NULL;
            $honor_idarr = D('JoinWineryHonor')->where(array('winery_id'=>$res['id'],'is_del'=>'-1'))->getfield('honor_id',true);
            foreach($honor_idarr as $key=>$val){
                $honor_id = $val;
                while($honor_id){
                    $honor_map = array('id'=>$honor_id,'is_del'=>'-1');
                    if($map['status']) $honor_map['status'] = '1';
                    $honor_res = D('Honor')->where($honor_map)->find();
                    if($honor_res){
                        $return['honor'][$key][] = array(
                                                          'id' => $honor_res['id'],
                                                          'fname' => $honor_res['fname'],
                                                          'cname' => $honor_res['cname'],
                                                          );
                    }
                    $honor_id = $honor_res['pid'];
                }
                if($return['honor'][$key]) $return['honor'][$key] = array_reverse($return['honor'][$key]);
            }
        }
        //是否获取酒款is_wine
        if(isset($_GET['isw']) && intval($_GET['isw']) === 1){
            $return['wine'] = NULL;
            //$wine_idarr = D('JoinWineWinery')->where(array('winery_id'=>$res['id'],'is_del'=>'-1'))->getfield('wine_id',true);
            $wine_map = 'A.winery_id='.$res['id'].' AND A.wine_id = B.id AND A.is_del=\'-1\' AND B.is_del=\'-1\'';
            if($map['status']) $wine_map .= ' AND B.status=\'1\''; 
            $return['wine'] = D()->table('jiuku_join_wine_winery A,jiuku_wine B')->field('B.id,B.fname,B.cname')->where($wine_map)->select();
        }
        //是否获取葡萄品种is_grape
        if(isset($_GET['isg']) && intval($_GET['isg']) === 1){
            $return['grape'] = NULL;
            $grape_map = 'A.winery_id='.$res['id'].' AND A.grape_id = B.id AND A.is_del=\'-1\' AND B.is_del=\'-1\'';
            if($map['status']) $grape_map .= ' AND B.status=\'1\''; 
            $return['grape'] = D()->table('jiuku_join_winery_grape A,jiuku_grape B')->field('B.id,A.grape_percentage AS percent,B.fname,B.cname')->where($grape_map)->order('A.grape_percentage +0 DESC')->select();
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
    }
    
    /**
    * @brief WinerynameSelectBordeauxWinery    通过中文名/外文名获取波尔多名庄列表    加密action=    YENfQV8FenVrWkMFfUNET1hTREtHT3lPRk9JXmhFWE5PS19SfUNET1hT    #方法名+描述
    *
    * @param $_GET['kw'] {string}    $_GET['st'] {int}    #方法参数描述，大括号内注明类型
    *
    * @return echo{json}    #返回值
    */
    public function WinerynameSelectBordeauxWinery(){
        if(empty($_GET['kw'])){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
        $map['_complex'] = array('fname'=>array('like','%'.$_GET['kw'].'%'),'cname'=>array('like','%'.$_GET['kw'].'%'),'_logic'=>'or');
        if($map['status']) $map_honor_idarr_map['status'] = '1';
        $map_honor_idarr_map['honorgroup_id'] = 1;
        $map_honor_idarr_map['is_del'] = '-1';
        $map_honor_idarr = D('Honor')->where($map_honor_idarr_map)->getfield('id',true);
        $map_winery_idarr = D('JoinWineryHonor')->where(array('honor_id'=>array('in',$map_honor_idarr),'is_del'=>'-1'))->getfield('winery_id',true);
        $map['id'] = array('in',$map_winery_idarr);
        $res = D('Winery')->where($map)->select();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        foreach($res as $key=>$val){
            $return[$key] = array('id'=>$val['id'],'fname'=>$val['fname'],'cname'=>$val['cname'],);
        }
        if(isset($_GET['isf']) && intval($_GET['isf']) === 1){
            foreach($res as $key=>$val){
                $return[$key]['aname'] = $val['aname'];
                $return[$key]['tel'] = $val['tel'];
                $return[$key]['url'] = $val['url'];
                $return[$key]['blog_url'] = $val['blog_url'];
                $return[$key]['address'] = $val['address'];
                $return[$key]['acreage'] = $val['acreage'];
                $return[$key]['plant_age'] = $val['plant_age'];
                $return[$key]['yield'] = $val['yield'];
                $return[$key]['oak_storage_duration'] = $val['oak_storage_duration'];
                $return[$key]['g_map_url'] = $val['g_map_url'];
                $return[$key]['g_map'] = $val['g_map'];
                $return[$key]['description'] = $val['description'];
                $return[$key]['content'] = $val['content'];
                $return[$key]['seo_t'] = $val['seo_t'];
                $return[$key]['seo_k'] = $val['seo_k'];
                $return[$key]['seo_d'] = $val['seo_d'];
            }
        }
        $return = $this->getParamCountPage($return);
        //是否获取图片is_img
        if(isset($_GET['isi']) && intval($_GET['isi']) === 1){
            foreach($return['data'] as $key=>$val){
                $return['data'][$key]['img'] = NULL;
                $img_map = array('winery_id'=>$val['id'],'is_del'=>'-1');
                if($map['status']) $img_map['status'] = '1';
                $img_res = D('WineryImg')->field('id,filename,description,alt')->where($img_map)->select();
                foreach($img_res as $k=>$v){
                    $img_size = getimagesize(C('DOMAIN.UPLOAD').'Jiuku/Winery/images/'.$val['filename']);
                    $return['data'][$key]['img'][$k] = array(
                                                             'id' => $val['id'],
                                                             'filename' => C('DOMAIN.UPLOAD').'Jiuku/Winery/images/'.$val['filename'],
                                                             'description' => $val['description'],
                                                             'alt' => $val['alt'],
                                                             'width' => $img_size[0],
                                                             'height' => $img_size[1],
                                                             );
                }
                
            }
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
    }
    
    /**
    * @brief wineFind    通过酒款ID获取酒款信息    加密action=    Xn1hf2E7REtVZH07Y316cVJ9enA%3D=    #方法名+描述
    *
    * @param $_GET['id'] {int}    $_GET['st'] {int}    $_GET['isf'] {int}    $_GET['ist'] {int}    $_GET['isi'] {int}    $_GET['isc'] {int}    $_GET['isr'] {int}    $_GET['ish'] {int}    $_GET['isg'] {int}    $_GET['isrw'] {int}    $_GET['ia'] {int}    #方法参数描述，大括号内注明类型
    *
    * @return echo{json}    #返回值
    */
    public function wineFind(){
        if(empty($_GET['id']) || intval($_GET['id']) <= 0){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
        $map['id'] = intval($_GET['id']);
        $map['is_del'] = '-1';
        $res = D('Wine')->where($map)->find();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        $return = array('id' => $res['id'],'fname' => $res['fname'],'cname' => $res['cname']);
        //是否获取完整数据is_full
        if(isset($_GET['isf']) && intval($_GET['isf']) === 1){
            $return['aname'] = $res['aname'];
            $return['content'] = $res['content'];
            $return['seo_t'] = $res['seo_t'];
            $return['seo_k'] = $res['seo_k'];
            $return['seo_d'] = $res['seo_d'];
        }
        //是否获取酒款类型is_type
        if(isset($_GET['ist']) && intval($_GET['ist']) === 1){
            $return['winetype'] = NULL;
            if($winetype_id = $res['winetype_id']){
                while($winetype_id){
                    $winetype_map = array('id'=>$winetype_id,'is_del'=>'-1');
                    if($map['status']) $winetype_map['status'] = '1';
                    $winetype_res = D('Winetype')->where($winetype_map)->find();
                    if($winetype_res){
                        $return['winetype'][] = array(
                                                      'id' => $winetype_res['id'],
                                                      'fname' => $winetype_res['fname'],
                                                      'cname' => $winetype_res['cname'],
                                                      );
                    }
                    $winetype_id = $winetype_res['pid'];
                }
                if($return['winetype']) $return['winetype'] = array_reverse($return['winetype']);
            }
        }
        //是否获取图片is_img
        if(isset($_GET['isi']) && intval($_GET['isi']) === 1){
            $return['img'] = NULL;
            $img_map = array('wine_id'=>$res['id'],'is_del'=>'-1');
            if($map['status']) $img_map['status'] = '1';
            $img_res = D('WineImg')->field('id,filename,description,alt')->where($img_map)->select();
            foreach($img_res as $key=>$val){
                $img_size = getimagesize(C('DOMAIN.UPLOAD').'Jiuku/Wine/images/'.$val['filename']);
                $return['img'][] = array(
                                         'id' => $val['id'],
                                         'filename' => C('DOMAIN.UPLOAD').'Jiuku/Wine/images/'.$val['filename'],
                                         'description' => $val['description'],
                                         'alt' => $val['alt'],
                                         'width' => $img_size[0],
                                         'height' => $img_size[1],
                                         );
            }
        }
        //是否获取所属国家is_country
        if(isset($_GET['isc']) && intval($_GET['isc']) === 1){
            $return['country'] = NULL;
            $country_map = array('id'=>$res['country_id'],'is_del'=>'-1');
            if($map['status']) $country_map['status'] = '1';
            $return['country'] = D('Country')->field('id,fname,cname')->where($country_map)->find();
        }
        //是否获取所属产区is_region
        if(isset($_GET['isr']) && intval($_GET['isr']) === 1){
            $return['region'] = NULL;
            $region_idarr = D('JoinWineRegion')->where(array('wine_id'=>$res['id'],'is_del'=>'-1'))->getfield('region_id',true);
            foreach($region_idarr as $key=>$val){
                $region_id = $val;
                while($region_id){
                    $region_map = array('id'=>$region_id,'is_del'=>'-1');
                    if($map['status']) $region_map['status'] = '1';
                    $region_res = D('Region')->where($region_map)->find();
                    if($region_res){
                        $return['region'][$key][] = array(
                                                          'id' => $region_res['id'],
                                                          'fname' => $region_res['fname'],
                                                          'cname' => $region_res['cname'],
                                                          );
                    }
                    $region_id = $region_res['pid'];
                }
                if($return['region'][$key]) $return['region'][$key] = array_reverse($return['region'][$key]);
            }
        }
        //是否获取所属庄园is_winery
        if(isset($_GET['isw']) && intval($_GET['isw']) === 1){
            $return['winery'] = NULL;
            $winery_map = 'A.wine_id='.$res['id'].' AND A.winery_id = B.id AND A.is_del=\'-1\' AND B.is_del=\'-1\'';
            if($map['status']) $winery_map .= ' AND B.status=\'1\''; 
            $return['winery'] = D()->table('`jiuku_join_wine_winery` A,`jiuku_winery` B')->field('B.id,B.fname,B.cname')->where($winery_map)->select();
        }
        //是否获取荣誉is_honor
        if(isset($_GET['ish']) && intval($_GET['ish']) === 1){
            $return['honor'] = NULL;
            $honor_idarr = D('JoinWineHonor')->where(array('wine_id'=>$res['id'],'is_del'=>'-1'))->getfield('honor_id',true);
            foreach($honor_idarr as $key=>$val){
                $honor_id = $val;
                while($honor_id){
                    $honor_map = array('id'=>$honor_id,'is_del'=>'-1');
                    if($map['status']) $honor_map['status'] = '1';
                    $honor_res = D('Honor')->where($honor_map)->find();
                    if($honor_res){
                        $return['honor'][$key][] = array(
                                                          'id' => $honor_res['id'],
                                                          'fname' => $honor_res['fname'],
                                                          'cname' => $honor_res['cname'],
                                                          );
                    }
                    $honor_id = $honor_res['pid'];
                }
                if($return['honor'][$key]) $return['honor'][$key] = array_reverse($return['honor'][$key]);
            }
        }
        //是否获取葡萄品种is_grape
        if(isset($_GET['isg']) && intval($_GET['isg']) === 1){
            $return['grape'] = NULL;
            $grape_map = 'A.wine_id='.$res['id'].' AND A.grape_id = B.id AND A.is_del=\'-1\' AND B.is_del=\'-1\'';
            if($map['status']) $grape_map .= ' AND B.status=\'1\''; 
            $return['grape'] = D()->table('jiuku_join_wine_grape A,jiuku_grape B')->field('B.id,A.grape_percentage AS percent,B.fname,B.cname')->where($grape_map)->order('A.grape_percentage DESC')->select();
        }
        //是否获取年份数据
        if(isset($_GET['isy']) && intval($_GET['isy']) === 1){
            $return['ywine'] = NULL;
            $ywine_map = array('wine_id'=>$res['id'],'is_del'=>'-1');
            if($map['status']) $ywine_map['status'] = '1';
            $return['ywine'] = D('Ywine')->field('id,year,price,alcohol,bottle_size')->where($ywine_map)->order('year DESC')->select();
        }
        //是否获取参考网站is_refweb
        if(isset($_GET['isrw']) && intval($_GET['isrw']) === 1){
            $return['refweb'] = NULL;
            $refweb_map = 'A.wine_id='.$res['id'].' AND A.refweb_id = B.id AND A.is_del=\'-1\' AND B.is_del=\'-1\'';
            if($map['status']) $refweb_map .= ' AND B.status=\'1\''; 
            $return['refweb'] = D()->table('`jiuku_join_wine_refweb` A,`jiuku_refweb` B')->field('B.id,B.sname,B.fname,B.cname,B.url,A.refweb_url')->where($refweb_map)->select();
        }
        //判断是否为波尔多名庄酒isBordeauxGrandCruWine
        if(isset($_GET['isbgw']) && intval($_GET['isbgw']) === 1){
            $isbgw_winery_id = D('JoinWineWinery')->where(array('wine_id'=>$res['id'],'is_del'=>'-1'))->getfield('winery_id',true);
            if(!$isbgw_winery_id){
                $return['isbgw'] = 'n';
            }else{
                $isbgw_honor_id = D('JoinWineryHonor')->where(array('winery_id'=>array('in',$isbgw_winery_id),'is_del'=>'-1'))->getfield('honor_id',true);
                if(!$isbgw_honor_id){
                    $return['isbgw'] = 'n';
                }else{
                    if($map['status']) $isbgw_honor_map['status'] = '1';
                    $isbgw_honor_map['honorgroup_id'] = '1';
                    $isbgw_honor_map['id'] = array('in',$isbgw_honor_id);
                    $isbgw_honor_map['is_del'] = '-1';
                    $isbgw = D('Honor')->where($honor_map)->getfield('id',true);
                    if(!$isbgw){
                        $return['isbgw'] = 'n';
                    }else{
                        $return['isbgw'] = 'y';
                    }
                }
            }
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
    }
    
    /**
    * @brief wineFind    通过年份酒ID获取年份酒信息    加密action=    X3xgfmA6RUpUZXw6bGJ8e3BTfHtx    #方法名+描述
    *
    * @param $_GET['id'] {int}    $_GET['st'] {int}    $_GET['isf'] {int}    $_GET['ise'] {int}    #方法参数描述，大括号内注明类型
    *
    * @return echo{json}    #返回值
    */
    public function ywineFind(){
        if(isset($_GET['id']) && intval($_GET['id']) > 0){
            $map['id'] = intval($_GET['id']);
        }elseif(isset($_GET['pidy']) && trim($_GET['pidy']) != ''){
            $pidy = explode('-',trim($_GET['pidy']));
            $map['wine_id'] = $pidy[0];
            $map['year'] = $pidy[1];
        }else{
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
        $map['is_del'] = '-1';
        $res = D('Ywine')->where($map)->find();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        $return = array('id' => $res['id'],'year' => $res['year']);
        //是否获取完整数据is_full
        if(isset($_GET['isf']) && intval($_GET['isf']) === 1){
            $return['price'] = $res['price'];
            $return['alcohol'] = $res['alcohol'];
            $return['production'] = $res['production'];
            $return['bottle_size'] = $res['bottle_size'];
        }
        //是否获取搜索酒款信息is_wine
        if(isset($_GET['isw']) && intval($_GET['isw']) === 1){
            $return['wine'] = NULL;
            $wine_map = array('id'=>$res['wine_id'],'is_del'=>'-1');
            if($map['status']) $wine_map['status'] = '1';
            $return['wine'] = D('Wine')->field('id,fname,cname')->where($wine_map)->find();
        }
        //是否获取评价信息is_eval
        if(isset($_GET['ise']) && intval($_GET['ise']) === 1){
            $return['eval'] = NULL;
            $evalparty_map = array('is_del'=>'-1');
            if($map['status']) $evalparty_map['status'] = '1';
            $return['eval'] = D('Evalparty')->field('id,sname,fname,cname,aname')->where($evalparty_map)->order('id ASC')->select();
            foreach($return['eval'] as $key=>$val){
                $eval_map = array('evalparty_id'=>$val['id'],'ywine_id'=>$res['id'],'is_del'=>'-1');
                if($map['status']) $eval_map['status'] = '1';
                $eval_res = D('YwineEval')->where($eval_map)->find();
                $return['eval'][$key]['score'] = $eval_res['score'] ? $eval_res['score'] : '';
                $return['eval'][$key]['seval'] = $eval_res['seval'] ? $eval_res['seval'] : '';
                $return['eval'][$key]['leval'] = $eval_res['leval'] ? $eval_res['leval'] : '';
            }
        }
        //是否获取代理商
        if(isset($_GET['isa']) && intval($_GET['isa']) === 1){
            $return['agents'] = NULL;
            $agents_map = 'A.ywine_id='.$res['id'].' AND A.agents_id = B.id AND A.is_del=\'-1\' AND B.is_del=\'-1\'';
            if($map['status']) $agents_map .= ' AND B.status=\'1\''; 
            $return['agents'] = D()->table('jiuku_join_agents_wine A,jiuku_agents B')->field('B.id,B.fname,B.cname,A.wine_price AS price,A.wine_buy_url AS buy_url')->where($agents_map)->select();
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
    }
    
    /**
    * @brief wineFind    通过年份酒IDS获取年份酒信息    加密action=    XX5ifGI4R0hWZ344bmB%2BeXJEcntydGM%3D    #方法名+描述
    *
    * @param $_GET['id'] {int}    $_GET['st'] {int}    $_GET['isf'] {int}    $_GET['ise'] {int}    #方法参数描述，大括号内注明类型
    *
    * @return echo{json}    #返回值
    */
    public function ywineSelect(){
        if(empty($_GET['ids']) || trim($_GET['ids']) == ''){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
        $map['id'] = array('in',explode(',',trim($_GET['ids'])));
        $map['is_del'] = '-1';
        $res = D('Ywine')->where($map)->order('find_in_set(id,"'.trim($_GET['ids']).'")')->select();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        foreach($res as $key=>$val){
            $return[$key] = array('id' => $val['id'],'year' => $val['year'],);
        }
        //是否获取完整数据is_full
        if(isset($_GET['isf']) && intval($_GET['isf']) === 1){
            foreach($res as $key=>$val){
                $return[$key]['price'] = $val['price'];
                $return[$key]['alcohol'] = $val['alcohol'];
                $return[$key]['production'] = $val['production'];
                $return[$key]['bottle_size'] = $val['bottle_size'];
            }
        }
        //是否获取搜索酒款信息is_wine
        if(isset($_GET['isw']) && intval($_GET['isw']) === 1){
            foreach($res as $key=>$val){
                $return[$key]['wine'] = NULL;
                $wine_map = array('id'=>$val['wine_id'],'is_del'=>'-1');
                if($map['status']) $wine_map['status'] = '1';
                $return[$key]['wine'] = D('Wine')->field('id,fname,cname')->where($wine_map)->find();
            }
        }
        //是否获取评价信息is_eval
        if(isset($_GET['ise']) && intval($_GET['ise']) === 1){
            foreach($res as $key=>$val){
                $return[$key]['eval'] = NULL;
                $evalparty_map = array('is_del'=>'-1');
                if($map['status']) $evalparty_map['status'] = '1';
                $return[$key]['eval'] = D('Evalparty')->field('id,sname,fname,cname,aname')->where($evalparty_map)->order('id ASC')->select();
                foreach($return[$key]['eval'] as $k=>$v){
                    $eval_map = array('evalparty_id'=>$v['id'],'ywine_id'=>$val['id'],'is_del'=>'-1');
                    if($map['status']) $eval_map['status'] = '1';
                    $eval_res = D('YwineEval')->where($eval_map)->find();
                    $return[$key]['eval'][$k]['score'] = $eval_res['score'] ? $eval_res['score'] : '';
                    $return[$key]['eval'][$k]['seval'] = $eval_res['seval'] ? $eval_res['seval'] : '';
                    $return[$key]['eval'][$k]['leval'] = $eval_res['leval'] ? $eval_res['leval'] : '';
                }
            }
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
    }
    
    /**
    * @brief isHonorWine    判断是否为波尔多列级名庄酒    加密action=    aEtXSVcNcn1jUksNS1FgTVBGR0NXWmVQQ0xGYVBXdUtMRw%3D%3D    #方法名+描述
    *
    * @param $_GET['kw'] {string}    $_GET['st'] {int}    #方法参数描述，大括号内注明类型
    *
    * @return echo{json}    #返回值
    */
    public function isBordeauxGrandCruWine(){
        if(empty($_GET['id']) || intval($_GET['id']) <= 0){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
        $map['id'] = intval($_GET['id']);
        $map['is_del'] = '-1';
        $id = D('Wine')->where($map)->getfield('id');
        if(!$id){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>'n'));
        }
        $winery_id = D('JoinWineWinery')->where(array('wine_id'=>$id,'is_del'=>'-1'))->getfield('winery_id',true);
        if(!$winery_id){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>'n'));
        }
        $honor_id = D('JoinWineryHonor')->where(array('winery_id'=>array('in',$winery_id),'is_del'=>'-1'))->getfield('honor_id',true);
        if(!$honor_id){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>'n'));
        }
        if($map['status']) $honor_map['status'] = '1';
        $honor_map['honorgroup_id'] = '1';
        $honor_map['id'] = array('in',$honor_id);
        $honor_map['is_del'] = '-1';
        $is = D('Honor')->where($honor_map)->getfield('id',true);
        if(!$is){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>'n'));
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>'y'));
    }
    /**
    * @brief WinenameSelectWine    通过中文名/外文名获取酒款列表    加密action=    VHdrdWsxTkFfbncxSXdwe3B%2Fc3tNe3J7fWpJd3B7    #方法名+描述
    *
    * @param $_GET['kw'] {string}    $_GET['st'] {int}    #方法参数描述，大括号内注明类型
    *
    * @return echo{json}    #返回值
    */
    public function WinenameSelectWine(){
        if(empty($_GET['kw'])){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
        if($Redis = $this->linkRedis()){
            $idarr = array_unique(array_merge($Redis->sMembers('jiuku_winefname_wine:'.strtolower($_GET['kw'])),$Redis->sMembers('jiuku_winecname_wine:'.strtolower($_GET['kw']))));
            if(!$idarr){
                $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
            }
            $map['id'] = array('in',$idarr);
        }else{
            $map['_complex'] = array('fname'=>array('like','%'.$_GET['kw'].'%'),'cname'=>array('like','%'.$_GET['kw'].'%'),'_logic'=>'or');
        }
        $map['merge_id'] = 0;
        $map['is_del'] = '-1';
        $res = D('Wine')->where($map)->select();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        foreach($res as $key=>$val){
            $return[] = array(
                              'id'=>$val['id'],
                              'fname'=>$val['fname'],
                              'cname'=>$val['cname'],
                              );
        }
        $count = $this->getParamCount();
        if($count){
            $return_group = array_chunk($return,$count);
            $return = $return_group[0];
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
    }
    
    /**
    * @brief WinefnameSelectWine    通过外文名获取酒款列表    加密action=    VXZqdGowT0Beb3YwSHZxenlxfnJ6THpzenxrSHZxeg%3D%3D    #方法名+描述
    *
    * @param $_GET['kw'] {string}    $_GET['st'] {int}    #方法参数描述，大括号内注明类型
    *
    * @return echo{json}    #返回值
    */
    public function WinefnameSelectWine(){
        if(empty($_GET['kw'])){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
        if($Redis = $this->linkRedis()){
            $idarr = $Redis->sMembers('jiuku_winefname_wine:'.strtolower($_GET['kw']));
            if(!$idarr){
                $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
            }
            $map['id'] = array('in',$idarr);
        }else{
            $map['fname'] = array('like','%'.$_GET['kw'].'%');
        }
        $map['merge_id'] = 0;
        $map['is_del'] = '-1';
        $res = D('Wine')->where($map)->select();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        foreach($res as $key=>$val){
            $return[] = array(
                              'id'=>$val['id'],
                              'fname'=>$val['fname'],
                              'cname'=>$val['cname'],
                              );
        }
        $count = $this->getParamCount();
        if($count){
            $return_group = array_chunk($return,$count);
            $return = $return_group[0];
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
    }
    
    /**
    * @brief WinecnameSelectWine    通过中文名获取酒款列表    加密action=    VXZqdGowT0Beb3YwSHZxenxxfnJ6THpzenxrSHZxeg%3D%3D    #方法名+描述
    *
    * @param $_GET['kw'] {string}    $_GET['st'] {int}    #方法参数描述，大括号内注明类型
    *
    * @return echo{json}    #返回值
    */
    public function WinecnameSelectWine(){
        if(empty($_GET['kw'])){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
        if($Redis = $this->linkRedis()){
            $idarr = $Redis->sMembers('jiuku_winecname_wine:'.strtolower($_GET['kw']));
            if(!$idarr){
                $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
            }
            $map['id'] = array('in',$idarr);
        }else{
            $map['cname'] = array('like','%'.$_GET['kw'].'%');
        }
        $map['merge_id'] = 0;
        $map['is_del'] = '-1';
        $res = D('Wine')->where($map)->select();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        foreach($res as $key=>$val){
            $return[] = array(
                              'id'=>$val['id'],
                              'fname'=>$val['fname'],
                              'cname'=>$val['cname'],
                              );
        }
        $count = $this->getParamCount();
        if($count){
            $return_group = array_chunk($return,$count);
            $return = $return_group[0];
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
    }
    
    /**
    * @brief WineryidSelectWine    通过酒庄ID获取酒款列表    加密action=    VHdrdWsxTkFfbncxSXdwe2xnd3pNe3J7fWpJd3B7    #方法名+描述
    *
    * @param $_GET['id'] {int}    $_GET['st'] {int}    #方法参数描述，大括号内注明类型
    *
    * @return echo{json}    #返回值
    */
    public function WineryidSelectWine(){
        if(empty($_GET['id']) || intval($_GET['id']) <= 0){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
        $idarr = D('JoinWineWinery')->where(array('winery_id'=>intval($_GET['id']),'is_del'=>'-1'))->getfield('wine_id',true);
        if(!$idarr){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        $map['id'] = array('in',$idarr);
        $res = D('Wine')->where($map)->select();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        foreach($res as $key=>$val){
            $return[] = array(
                              'id'=>$val['id'],
                              'fname'=>$val['fname'],
                              'cname'=>$val['cname'],
                              );
        }
        $count = $this->getParamCount();
        if($count){
            $return_group = array_chunk($return,$count);
            $return = $return_group[0];
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
    }
    
    /**
    * @brief WineidSelectRegionTreeCountry    通过酒款ID获取所属产区树及国家    加密action=    Y0BcQlwGeXZoWUAGfkBHTEBNekxFTEpde0xOQEZHfVtMTGpGXEddW1A%3D    #方法名+描述
    *
    * @param $_GET['id'] {int}    $_GET['st'] {int}    #方法参数描述，大括号内注明类型
    *
    * @return echo{json}    #返回值
    */
    public function WineidSelectRegionTreeCountry(){
        if(empty($_GET['id']) || intval($_GET['id']) <= 0){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
        $map['is_del'] = '-1';
        $regionidarr = D('JoinWineRegion')->where(array('wine_id'=>intval($_GET['id']),'is_del'=>'-1'))->getfield('region_id',true);
        if(!$regionidarr){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        foreach($regionidarr as $key=>$val){
            $regionid = $val;
            while($regionid){
                $regionres = D('Region')->where(array_merge(array('id'=>$regionid),$map))->find();
                $return[$key]['region'][] = array(
                                                  'id' => $regionres['id'],
                                                  'fname' => $regionres['fname'],
                                                  'cname' => $regionres['cname'],
                                                  );
                $regionid = $regionres['pid'];
            }
            $return[$key]['region'] = array_reverse($return[$key]['region']);
            if(!$regionres['country_id']){
                $return[$key]['country'] = NULL;
            }else{
                $countryres = D('Country')->where(array_merge(array('id'=>$regionres['country_id']),$map))->find();
                if(!$countryres){
                    $return[$key]['country'] = NULL;
                }else{
                    $return[$key]['country'] = array(
                                                     'id' => $countryres['id'],
                                                     'fname' => $countryres['fname'],
                                                     'cname' => $countryres['cname'],
                                                     );
                }
            }
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
    }
    
    /**
    * @brief getParamCount    解析各方法中获取的$_GET['co']的值    #方法名+描述
    *
    * @param $_GET['co'] {int|string}    #方法参数描述，大括号内注明类型
    *
    * @return $_GET['co']{int}|false{bool}    #返回值
    */
    function getParamCount(){
        if(empty($_GET['co'])){
            return 20;
        }else{
            if($_GET['co'] === 'all'){
                return false;
            }else{
                if(intval($_GET['co']) <= 0){
                    return 20;
                }else{
                    return intval($_GET['co']);
                }
            }
        }
    }
    
    /**
    * @brief getParamCount    解析各方法中获取的$_GET['co'] 和 $_GET['pa']的值    #方法名+描述
    *
    * @param $_GET['co'] {int|string} $_GET['pa'] {int}    #方法参数描述，大括号内注明类型
    *
    * @return     #返回值
    */
    public function getParamCountPage($data){
        if(empty($_GET['co'])){
            $count = 20;
        }else{
            if($_GET['co'] === 'all'){
                $count = false;
            }else{
                if(intval($_GET['co']) <= 0){
                    $count = 20;
                }else{
                    $count = intval($_GET['co']);
                }
            }
        }
        if(empty($_GET['pa'])){
            $page = 1;
        }else{
            if(intval($_GET['pa']) <= 0){
                $page = 1;
            }else{
                $page = intval($_GET['pa']);
            }
        }
        if($count){
            $return['data_count'] = count($data);
            $return['page_count'] = ceil(count($data)/$count);
            $data_group = array_chunk($data,$count);
            $return['data'] = $data_group[($page-1)];
        }else{
            $return['data'] = $data;
        }
        return $return;
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
