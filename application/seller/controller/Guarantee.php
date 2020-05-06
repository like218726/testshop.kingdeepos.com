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
 * 消费者保障服务控制器
 * Author: 545
 * Date: 2017-10-23
 */

namespace app\seller\controller;

use think\AjaxPage;
use think\Db;
use think\Page;
use app\common\logic\GuaranteeLogic;

class Guarantee extends Base {
    public $store_id;
    
    public function _initialize() {
        parent::_initialize();
        C('TOKEN_ON', false); // 关闭表单令牌验证       
    }

    public function index() {
    	$item_list = db('guarantee_item')->where('grt_state',1)->order('grt_sort')->select();	
    	$guarantee = db('guarantee')->where('store_id',STORE_ID)->order('id')->getField('grt_id,joinstate,auditstate,quitstate');
    	foreach ($item_list as $k=>$val){
    		if(!empty($guarantee[$val['grt_id']])){
    			$item_list[$k]['joinstate'] = $guarantee[$val['grt_id']]['joinstate'];
    			$item_list[$k]['auditstate'] = $guarantee[$val['grt_id']]['auditstate'];
    			$item_list[$k]['quitstate'] = $guarantee[$val['grt_id']]['quitstate'];
    		}else{
    			$item_list[$k]['joinstate'] = 0;
    			$item_list[$k]['auditstate'] = 0;
    			$item_list[$k]['quitstate'] = 0;
    		}
    	}
    	$this->assign('item_list',$item_list);
        return $this->fetch();
    }
    //保障服务详情
    public function detail() {
    	$grt_id = I('grt_id');
    	$item_info = db('guarantee_item')->where(['grt_state'=>1,'grt_id'=>$grt_id])->find();
    	$this->assign('item_info',$item_info);
    	$guarantee = db('guarantee')->where(['store_id'=>STORE_ID,'grt_id'=>$grt_id])->find();
    	$this->assign('guarantee',$guarantee);
    	$auditstate = array('申请进行中（等待审核）','审核通过','审核失败','已支付保证金','保证金审核通过','保证金审核失败');
    	$auditstate = empty($guarantee) ? '未申请' : $auditstate[$guarantee['auditstate']];
    	$this->assign('auditstate',$auditstate);
    	$handle_log = db('guarantee_log')->where(['log_storeid'=>STORE_ID,'log_grtid'=>$grt_id])->order('log_id desc')->select();
    	$this->assign('handle_log',$handle_log);
    	return $this->fetch();
    }
    
    public function apply() {
    	$grt_id = I('post.grt_id');
    	$apply_type = I('post.apply_type',0);
    	$item = db('guarantee_item')->where(['grt_state'=>1,'grt_id'=>$grt_id])->find();
		$apply = db('guarantee_apply')->where(['auditstate'=>0,'store_id'=>STORE_ID,'grt_id'=>$grt_id,'apply_type'=>$apply_type])->find();
		if ($apply) {
			$this->ajaxReturn(['status' => -1, 'msg' => '请耐心等待审核']);
		}else{
			$data = array('grt_id'=>$grt_id,'store_id'=>STORE_ID,'add_time'=>time(),
					'store_name'=>$this->storeInfo['store_name'],'cost'=>$item['grt_cost'],'apply_type'=>$apply_type
			);

			if(db('guarantee_apply')->insert($data)){
				if($apply_type == 1){
					//加入申请
					$data['joinstate'] = 1;
					$log_msg = '店铺申请加入保障服务';
					$data['auditstate'] = 0;
				}else{
					//退出申请
					$data['quitstate'] = 1;
					$log_msg = '店铺申请退出保障服务';
				}
				if(db('guarantee')->where(['store_id'=>STORE_ID,'grt_id'=>$grt_id])->find()){
					db('guarantee')->where(['store_id'=>STORE_ID,'grt_id'=>$grt_id])->save($data);
				}else{
					db('guarantee')->insert($data);
				}
				$seller = session('seller');
				$logArr = array('log_storeid'=>STORE_ID,'log_storename'=>$this->storeInfo['store_name'],'log_grtid'=>$grt_id,
					'log_grtname'=>$item['grt_name'],'log_msg'=>$log_msg,'log_addtime'=>time(),	'log_role'=>'商家',
					'log_userid'=>$seller['user_id'],'log_username'=>$seller['seller_name']
				);
				$guaranteeLogic = new GuaranteeLogic();
				$guaranteeLogic->guaranteeHandleLog($logArr);
				$this->ajaxReturn(['status' => 1, 'msg' => '操作成功']);
			}else{
				$this->ajaxReturn(['status' => -1, 'msg' => '操作失败']);
			}
		}	
    }
    
    public function apply_pay(){
    	$grt_id = I('grt_id');
    	if(IS_POST){
    		$data['costimg'] = I('costimg');
    		$data['auditstate'] = 3;
    		db('guarantee_apply')->where(['store_id'=>STORE_ID,'grt_id'=>$grt_id])->order('id desc')->limit(1)->save($data);
    		db('guarantee')->where(['store_id'=>STORE_ID,'grt_id'=>$grt_id])->save($data);
    		$seller = session('seller');
    		$item = db('guarantee_item')->where(['grt_state'=>1,'grt_id'=>$grt_id])->find();
    		$logArr = array('log_storeid'=>STORE_ID,'log_storename'=>$this->storeInfo['store_name'],'log_grtid'=>$grt_id,
    				'log_grtname'=>$item['grt_name'],'log_msg'=>'店铺支付保证金','log_addtime'=>time(),	'log_role'=>'商家',
    				'log_userid'=>$seller['user_id'],'log_username'=>$seller['seller_name']
    		);
    		$guaranteeLogic = new GuaranteeLogic();
    		$guaranteeLogic->guaranteeHandleLog($logArr);
    		$this->success('操作成功',U('Guarantee/index'));
    	}
    	$apply = db('guarantee_apply')->where(['store_id'=>STORE_ID,'grt_id'=>$grt_id])->order('id desc')->limit(1)->find();
    	$item = db('guarantee_item')->where(['grt_state'=>1,'grt_id'=>$grt_id])->find();
    	$this->assign('apply',$apply);
    	$this->assign('info',$item);
    	return $this->fetch();
    } 
}
