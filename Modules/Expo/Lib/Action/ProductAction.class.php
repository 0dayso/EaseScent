<?php

/**
 * file:Admin
 * brief:后台管理
 * author:Angf
 * date:2013-5-6
 */
class ProductAction extends AdminCommonAction {


    /**
     * index 默认模块 产品列表页
     */
    function index(){

        $Goods = M('goods');
        import('ORG.Util.Page');

        //搜索提交
        $keyword = $this->isPost() ? $this->_post('keyword') : $this->_get('keyword') ;

        if($keyword){
            $sreach['fname']     = array('like',$keyword.'%');
            $sreach['cname']     = array('like',$keyword.'%');
            $sreach['_logic']    = 'or';
            $conditions['_complex'] = $sreach;
        }

        if($this->_post('sale_status')!="") $conditions['sale_status'] = $this->_post('sale_status');
        if($this->_post('is_verify')!="")   $conditions['is_verify']   = $this->_post('is_verify');
        $conditions['is_delete']   =0;
        $count      = $Goods->where($conditions)->count();
        $Page       = new Page($count,C('GOODS_PAGE_NUM'));
        if($keyword) $Page->parameter.=   "&keyword=".$keyword;

        $Page->setConfig('theme',"<li><a>%totalRow% %header% %nowPage%/%totalPage% 页</a></li> <li>%upPage%</li> <li>%downPage%</li> <li>%first%</li> <li>%prePage%</li> <li>%linkPage%</li> <li>%nextPage%</li> <li>%end%</li>");

        $show       = $Page->show();// 分页显示输出
        $fieldStr='goods_id,agent_id,pic_url,typename,cname,fname,price_type,goods_price,create_time,currency,sale_status,is_verify';
        $list = $Goods->field($fieldStr)->where($conditions)->order('goods_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        //echo $Goods->getLastSql();    //获取上一个sql
        $this->assign('list',$list);    // 赋值数据集
        $this->assign('page',$show);    // 赋值分页输出
        $this->assign('keyword',$keyword);
        $this->display('Admin:Productslist');
    }




    /**
     * 产品管理
     */
    function manage(){
        $goods_ids= $_REQUEST['goods_id'];
        $action = $this->_post('action');
        if(!is_array($goods_ids)) $this->error('请选择你要操作的产品');

        switch ($action) {
            case 'delete':
                $this->goods_delete($goods_ids);
                break;

            case 'unlocked':
                $this->goods_verify($goods_ids,1);
                break;

            case 'locked':
                $this->goods_verify($goods_ids);
                break;

            case 'up':
                $this->goods_sale_status($goods_ids,1);
                break;

            case 'down':
                $this->goods_sale_status($goods_ids);
                break;
        }
    }




    /**
      * 产品管理 删除产品
      */
    function goods_delete($goods_ids){
      $goods_ids = trim(implode(',',$goods_ids));
      $condition['goods_id']  = array('in',$goods_ids);

      $goodsImgs = M("goods_img")->field('id,goods_id,img_url')->where($condition)->select();
      if(!M('goods')->where($condition)->delete()) $this->error("删除失败");
      if(!M("goods_img")->where($condition)->delete()) $this->error("删除失败");

      //删除图片文件
      foreach ($goodsImgs as $key => $value) {
              $fileSepeator = explode('.', $value['img_url']);
              unlink(C('UPLOAD_PATH') . 'Wine' . DS . 'images'.$value['img_url']);
              unlink(C('UPLOAD_PATH') . 'Wine' . DS . 'images'.$fileSepeator['0'].'_100_thumb.'.$fileSepeator['1']);
              unlink(C('UPLOAD_PATH') . 'Wine' . DS . 'images'.$fileSepeator['0'].'_400_thumb.'.$fileSepeator['1']);
      }
       $this->success("删除成功");
    }



    /**
      * 产品管理 设置产品审核状态
      */
    function goods_verify($goods_ids,$status=0){
        $goods_ids = trim(implode(',',$goods_ids));
        $condition['goods_id']  = array('in',$goods_ids);
        M('goods')->where($condition)->save(array('is_verify'=>$status));
        $this->success('操作成功');

     }



   /**
     * 产品管理 产品审核
     */
    function goods_sale_status($goods_ids,$status=0){
        $goods_ids = trim(implode(',',$goods_ids));
        $condition['goods_id']  = array('in',$goods_ids);
        M('goods')->where($condition)->save(array('sale_status'=>$status));
        $this->success('操作成功');
    }




}
?>