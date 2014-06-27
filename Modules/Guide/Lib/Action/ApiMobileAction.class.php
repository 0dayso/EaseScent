<?php


// +----------------------------------------------------------------------
// | 商机网 [ 58.wine.cn]
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2013 http://58.wine.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Angf <272761906@qq.com>
// +----------------------------------------------------------------------



/**
 * Expo Web前端默认首页模块
 * @category   web 前端
 * @subpackage  Action
 * @author    Angf <272761906@qq.com>
 */


class ApiMobileAction extends Api {

    /*获取产品列表*/

    public function Goodslist() {
        $Goods = M('goods');
        import('ORG.Util.Page');

        //搜索提交
        $keyword = $this->_REQUEST('keyword');
        if($keyword){
            $sreach['fname']     = array('like',$keyword.'%');
            $sreach['cname']     = array('like',$keyword.'%');
            $sreach['_logic']    = 'or';
            $conditions['_complex'] = $sreach;
        }

        $count = $Goods->where($conditions)->count();
        $Page  = new Page($count,20);
        if($keyword) $Page->parameter.=   "&keyword=".$keyword;

        //$show  = $Page->show();// 分页显示输出
        $p = intval($_REQUEST['p']);

        $fieldStr='goods_id,fname,goods_name,goods_promotion,goods_img,t_goods_url,click_url,goods_recommend';
        //$list  = $Goods->field($fieldStr)->where($conditions)->cache('guide'.$p,'3600')->page($p.',15')->order('edit_time desc,add_time desc')->select();
        $list  = $Goods->field($fieldStr)->where($conditions)->cache('guide'.$p,'3600')->page($p.',15')->order('goods_id desc')->select();

        foreach($list as $key => $value){
            $list[$key]['goods_recommend_rows'] =explode("\r\n",$value['goods_recommend']);
            $list[$key]['goods_img'] = C('img_url').'/'.$value['goods_img'];
            if($list[$key]['t_goods_url']==1)
                $list[$key]['t_goods_url'] = $list[$key]['click_url'];

        }
        //echo $Goods->getLastSql();
        $data['list'] = array();
        $data['list'] = $list;
        $data['totalPages'] = $Page->totalPages;
        Api::rst($data, 0, 'request is success');
    }

}
?>
