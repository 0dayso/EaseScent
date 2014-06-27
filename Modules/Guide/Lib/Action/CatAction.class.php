<?php
class CatAction extends CommonAction{
	//列表页
	function index()
	{
		$model = D('cat');
        $map = ' 1 ';
        $url = '';
        $keyword = Input::getVar($_REQUEST['keyword']);
        
        if($keyword) {
            $map .= ' AND cat_name LIKE "%'.$keyword.'%"';
            $url .= '&keyword=' . $keyword;
        }
        $list = $this->_list($model, $map, 14, $url);

		$lists=array();
		foreach($list as $key=>$val)
		{
			$lists[$val['cat_id']]=$val;
		}
		foreach($lists as $key=>$val)
		{
			if($val['p_id']!=0)
			{
				$lists[$key]['p_name']=$lists[$val['p_id']]['cat_name'];
			}
			else
			{
					$lists[$key]['p_name']='顶级标签';
			}
		}
        $this->assign('list', $lists);
        $this->display();
	}
	//添加
	function add()
	{
		$cat_id = Input::getVar($_REQUEST['cat_id']);
		if(!empty($cat_id))//添加
		{
			$cat_info=M('cat')->where("cat_id='".$cat_id."'")->find();
			$this->assign('cat_info',$cat_info);
			$this->assign('cat_id',$cat_id);
		}
		//读取顶级标签
		$cat_list=$this->getparentcat();
		$this->assign('cat_list',$cat_list);
		$this->display();
	}
	//添加处理
	function add_detil()
	{
		$cat=M('cat');
		$cat_id = Input::getVar($_REQUEST['cat_id']);
		$cat_name=Input::getVar($_REQUEST['cat_name']);
		$p_id=Input::getVar($_REQUEST['p_id']);
		$createuser=Input::getVar($_REQUEST['createuser']);
		$createtime=Input::getVar($_REQUEST['createtime']);
		if(empty($cat_id))//insert
		{
			if($cat_name=='')
			{
            	$this->_jumpGo('标签名称不能为空');
       		}
			else
			{
				$data['cat_name']=$cat_name;
				$data['p_id']=$p_id;
				$data['add_time']=$createtime;
				$data['admin_id']=$createuser;
				$data['edit_time']=$createtime;
				$data['edit_id']=$createuser;
				$re=$cat->add($data);
				if($re)
				{
					$this->_jumpGo('标签添加成功', 'succeed', Url('Cat/index'));
				}
				else
				{
					$this->_jumpGo('标签添加失败');
				}
			}
		}
		else//update
		{
			//查询cat_name是否已经存在
			$count=$cat->where("cat_name='".$cat_name."' and cat_id<>'".$cat_id."'")->count();
			if($count>0)
			{
				$this->_jumpGo('您要添加的标签已经存在');
			}
			else
			{
				if($cat_name=='')
				{
					$this->_jumpGo('标签名称不能为空');
				}
				else
				{
					$data['cat_name']=$cat_name;
					$data['p_id']=$p_id;
					$data['edit_time']=$createtime;
					$data['edit_id']=$createuser;
					$re=$cat->where("cat_id='".$cat_id."'")->save($data);
					if($re)
					{
						$this->_jumpGo('标签修改成功', 'succeed', Url('Cat/index'));
					}
					else
					{
						$this->_jumpGo('标签修改失败');
					}
				}	
			}
		}
	}
	function del()
	{
		$cat=M('cat');
		$cat_id = Input::getVar($_REQUEST['cat_id']);
		if(empty($cat_id))
		{
			$this->_jumpGo('删除失败');
		}
		else
		{
			//查询是否有子标签
			$count=$cat->where("p_id=$cat_id")->count();
			if($count>0)
			{
				$this->_jumpGo('请先删除子标签，再删除父级标签');
			}
			else
			{
				$re=$cat->where("cat_id=$cat_id")->delete();
				if($re)
				{
					$this->_jumpGo('标签删除成功', 'succeed', Url('Cat/index'));
				}
				else
				{
					$this->_jumpGo('标签删除失败');
				}
			}
		}
	}
	//读取顶级标签
	function getparentcat()
	{
		$cat=M('cat');
		$cat_list=$cat->where("p_id=0")->field("cat_id,cat_name")->select();
		return $cat_list;
	}
}
?>