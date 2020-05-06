<?php

/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * 消费者保障服务
 * Author: 545
 * Date: 2017-10-21
 */

namespace app\admin\controller;
use think\Page;
use think\Loader;
use app\common\logic\GuaranteeLogic;

class Guarantee extends Base {

    public function _initialize() {
        parent::_initialize();
        C('TOKEN_ON', false); // 关闭表单令牌验证
    }

    public function index() {
    	$apply_type = I('apply_type',1);
    	$count = db('guarantee_apply')->where('apply_type',$apply_type)->count();
    	$page = new Page($count);
    	$lists  = db('guarantee_apply')->where('apply_type',$apply_type)->limit($page->firstRow.','.$page->listRows)->select();
    	$this->assign('page',$page->show());
    	$this->assign('pager',$page);
    	$this->assign('lists',$lists);
    	$this->assign('apply_type',$apply_type);
    	$guarantee_item = db('guarantee_item')->where('grt_state',1)->order('grt_sort')->getField('grt_id,grt_name');
    	$this->assign('guarantee_item',$guarantee_item);
    	$auditstate = array('等待审核','审核通过,待支付保证金','审核失败','保证金待审核','保证金审核通过','保证金审核失败');
    	$this->assign('auditstate',$auditstate);
        return $this->fetch();
    }

    public function apply_edit(){
    	$id = I('id/d');
    	if(IS_POST){
    		$data['auditstate'] = $arr['auditstate'] = I('auditstate');
    		db('guarantee_apply')->where(array('id'=>$id))->save($data);
    		$apply = db('guarantee_apply')->where('id',$id)->find();
    		$item = db('guarantee_item')->where('grt_id',$apply['grt_id'])->find();
    		$admin = getAdminInfo(session('admin_id'));
    		$guaranteeLogic = new GuaranteeLogic();
    		if($data['auditstate'] == 0 || $data['auditstate'] == 3){
    			$this->error('不能重复更改申请状态',U('Guarantee/index',array('apply_type'=>$apply['apply_type'])));
    		}else if($data['auditstate'] == 2){
    			if($apply['apply_type'] == 0){
    				$log_msg = '管理员拒绝店铺退出保障服务的申请，原因：'.I('apply_reason');
    				$arr['quitstate'] = 2;
    				$arr['auditstate'] = 4;
    			}else{
    				$log_msg = '审核未通过，原因：'.I('apply_reason');
    				$arr['joinstate'] = 0;
    			}
    		}else if($data['auditstate'] == 1){
    			if($apply['apply_type'] == 1){
    				$log_msg = '审核通过，待支付保证金';
    			}else{
    				$log_msg = '管理员审核通过店铺退出保障服务的申请';
    				$arr['joinstate'] = 0;
    				$arr['quitstate'] = 0;
    				$arr['auditstate'] = 0;
    			}
    		}else if($data['auditstate'] == 4){
    			$log_msg = '保证金审核通过';
    			$arr['joinstate'] = 2;
    			$arr['quitstate'] = 0;
    			if($apply['cost']>0){
    				$costArr = array('grt_id'=>$apply['grt_id'],'grt_name'=>$item['grt_name'],'store_id'=>$apply['store_id'],
    						'store_name'=>$apply['store_name'],'admin_id'=>$admin['admin_id'],'admin_name'=>$admin['user_name'],
    						'price'=>$apply['cost'],'add_time'=>time(),'desc'=>'申请加入保障服务，支付保证金'
    				);
    				$guaranteeLogic->costLog($costArr);
    			}
    		}else if($data['auditstate'] == 5){
    			$log_msg = '保证金审核失败，原因：'.I('apply_reason');
    		}
    		
    		db('guarantee')->where(array('grt_id'=>$apply['grt_id'],'store_id'=>$apply['store_id']))->save($arr);
    		$logArr = array('log_storeid'=>$apply['store_id'],'log_storename'=>$apply['store_name'],'log_grtid'=>$apply['grt_id'],
    				'log_grtname'=>$item['grt_name'],'log_msg'=>$log_msg,
    				'log_addtime'=>time(),	'log_role'=>'管理员','log_userid'=>$admin['admin_id'],'log_username'=>$admin['user_name']
    		);
    		$guaranteeLogic->guaranteeHandleLog($logArr);
    		
    		$this->success('操作成功',U('Guarantee/index',array('apply_type'=>I('apply_type'))));
    	}
    	$apply = db('guarantee_apply')->where(array('id'=>$id))->find();
    	$this->assign('apply',$apply);
    	$guarantee = db('guarantee_item')->where(array('grt_id'=>$apply['grt_id']))->find();
    	$this->assign('info',$guarantee);
    	return $this->fetch();
    }
    
    public function apply_info(){
    	$id = I('id/d');
    	$apply = db('guarantee_apply')->where(array('id'=>$id))->find();
    	$this->assign('apply',$apply);
    	$guarantee = db('guarantee_item')->where(array('grt_id'=>$apply['grt_id']))->find();
    	$this->assign('info',$guarantee);
    	$auditstate = array('等待审核','审核通过,待支付保证金','审核失败','保证金待审核','保证金审核通过','保证金审核失败');
    	$this->assign('auditstate',$auditstate);
    	return $this->fetch();
    }
    
    public function item_list(){
    	$count = M('guarantee_item')->count();
    	$page = new Page($count);
    	$lists  = db('guarantee_item')->limit($page->firstRow.','.$page->listRows)->select();
    	$this->assign('page',$page->show());
    	$this->assign('pager',$page);
    	$this->assign('lists',$lists);
    	return $this->fetch();
    }
    
    public function item_info(){
    	$grt_id = I('grt_id/d');
    	$act = I('GET.act','add');
    	if($grt_id>0){
    		$guarantee = db('guarantee_item')->where(array('grt_id'=>$grt_id))->find();
    		$act = 'edit';
    		$this->assign('info',$guarantee);
    	}
    	$this->assign('act',$act);
    	return $this->fetch();
    }
    
    public function item_delete(){
        $grt_id = I('del_id/d');
        if($grt_id>0){
            $res = db('guarantee_item')->where(array('grt_id'=>$grt_id))->delete();
            $res && $this->ajaxReturn(['status' => 1, 'msg' => '操作成功']);
        }
        $this->ajaxReturn(['status' => -1, 'msg' => '操作失败']);
    }
    
    
    public function itemHandle()
    {
    	$data = I('post.');
    	if(empty($data['grt_name']) && $data['act'] != 'del'){
    		$this->ajaxReturn(['status' => -1, 'msg' => '标题不能为空']);
    	}
    	$validate = loader::Validate('Guarantee');
    	if (!$validate->scene($data['act'])->check($data)) {
    		$error = $validate->getError();
    		$this->ajaxReturn(['status' => -1,'msg' => $error]);
    	}
    	if ($data['act'] == 'add') {
    		$r = db('guarantee_item')->add($data);
    	} elseif ($data['act'] == 'edit'){
    		$r = db('guarantee_item')->where('grt_id='.$data['grt_id'])->save($data);
    	}
    	
    	if (!$r) {
    		$this->ajaxReturn(['status' => -1, 'msg' => '操作失败']);
    	}
    	$this->ajaxReturn(['status' => 1, 'msg' => '操作成功']);
    }
    
    public function join_list(){
    	$count = M('guarantee')->count();
    	$page = new Page($count);
    	$lists  = M('guarantee')->limit($page->firstRow.','.$page->listRows)->select();
    	$this->assign('page',$page->show());
    	$this->assign('pager',$page);
    	$this->assign('lists',$lists);
    	$auditstate = array('申请进行中（等待审核）','审核通过','审核失败','已支付保证金,待审核','保证金审核通过','保证金审核失败');
    	$this->assign('auditstate',$auditstate);
    	$guarantee_item = db('guarantee_item')->where('grt_state',1)->order('grt_sort')->getField('grt_id,grt_name');
    	$this->assign('guarantee_item',$guarantee_item);
    	return $this->fetch();
    }

    public function join_edit(){
    	$id = I('id/d');
    	$join = db('guarantee')->where(array('id'=>$id))->find();
    	if(IS_POST){
    		$isopen = I('c_state');
    		if(db('guarantee')->where(array('id'=>$id))->save(array('isopen'=>$isopen))){
    			$guaranteeLogic = new GuaranteeLogic();
    			$admin = getAdminInfo(session('admin_id'));
    			if($isopen == 1){
    				$log_msg = '关闭状态修改为“允许使用”';
    			}else{
    				$log_msg = '关闭状态修改为“永久禁止使用”，原因：'.I('c_reason');
    			}
    			$item = db('guarantee_item')->where('grt_id',$join['grt_id'])->find();
    			$logArr = array('log_storeid'=>$join['store_id'],'log_storename'=>$join['store_name'],'log_grtid'=>$join['grt_id'],
    					'log_grtname'=>$item['grt_name'],'log_msg'=>$log_msg,'log_addtime'=>time(),
    					'log_role'=>'管理员','log_userid'=>$admin['admin_id'],'log_username'=>$admin['user_name']
    			);
    			$guaranteeLogic->guaranteeHandleLog($logArr);
    			$this->success('操作成功',U('Guarantee/join_list'));
    		}else{
    			$this->success('不能重复更改关闭状态',U('Guarantee/join_list'));
    		}
    	}
    	$guarantee = db('guarantee_item')->where(array('grt_id'=>$join['grt_id']))->find();
    	$join['grt_name'] = $guarantee['grt_name'];
    	$this->assign('join',$join);
    	$auditstate = array('申请进行中（等待审核）','审核通过','审核失败','已支付保证金,待审核','保证金审核通过','保证金审核失败');
    	$this->assign('auditstate',$auditstate);
    	return $this->fetch();
    }
    
    public function join_info(){
    	$id = I('id/d');
    	$join = db('guarantee')->where(['id'=>$id])->find();
    	$this->assign('join',$join);
    	$item = db('guarantee_item')->where(['grt_state'=>1,'grt_id'=>$join['grt_id']])->find();
    	$this->assign('item',$item);
    	$auditstate = array('申请进行中（等待审核）','审核通过','审核失败','已支付保证金','保证金审核通过','保证金审核失败');
    	$this->assign('auditstate',$auditstate);
    	
    	$count = db('guarantee_log')->where(['log_storeid'=>$join['store_id'],'log_grtid'=>$join['grt_id']])->order('log_id desc')->count();
    	$page = new Page($count);
    	$lists  = db('guarantee_log')->where(['log_storeid'=>$join['store_id'],'log_grtid'=>$join['grt_id']])->order('log_id desc')->limit($page->firstRow.','.$page->listRows)->select();
    	$this->assign('page',$page->show());
    	$this->assign('pager',$page);
    	$this->assign('handle_log',$lists);
    	return $this->fetch();
    }
    
    public function cost_edit(){
    	return $this->fetch();
    }
    
    public function cost_log(){
    	$id = I('id/d');
    	$join = db('guarantee')->where(['id'=>$id])->find();
    	$this->assign('join',$join);
    	$item = db('guarantee_item')->where(['grt_state'=>1,'grt_id'=>$join['grt_id']])->find();
    	$this->assign('item',$item);
    	$auditstate = array('申请进行中（等待审核）','审核通过','审核失败','已支付保证金','保证金审核通过','保证金审核失败');
    	$this->assign('auditstate',$auditstate);
    	 
    	$count = db('guarantee_costlog')->where(['store_id'=>$join['store_id'],'grt_id'=>$join['grt_id']])->order('id desc')->count();
    	$page = new Page($count);
    	$lists  = db('guarantee_costlog')->where(['store_id'=>$join['store_id'],'grt_id'=>$join['grt_id']])->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
    	$this->assign('page',$page->show());
    	$this->assign('pager',$page);
    	$this->assign('handle_log',$lists);
    	return $this->fetch();
    }
}
