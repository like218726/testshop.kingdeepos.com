<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 商业用途务必到官方购买正版授权, 使用盗版将严厉追究您的法律责任。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * Author: IT宇宙人
 *
 * Date: 2016-03-09
 */

namespace app\admin\controller;
use think\Page;
use think\Db;
use app\admin\logic\RefundLogic;

class Finance extends Base {

	/*
     * 初始化操作
     */
	public function _initialize() {
		parent::_initialize();
	}

	/**
	 *  店家转账汇款记录
	 */
	public function store_remittance(){
		$status = I('status',1);
		$this->assign('status',$status);
		$this->get_store_withdrawals($status);
		return $this->fetch();
	}
	/**
	 *  转账汇款记录
	 */
	public function remittance(){
		$status = I('status',1);
		$this->assign('status',$status);
		$this->get_withdrawals_list($status);
		return $this->fetch();
	}

	/**
	 * 提现申请记录
	 */
	public function withdrawals()
	{
		$this->get_withdrawals_list();
		$this->assign('withdraw_status',C('WITHDRAW_STATUS'));
		return $this->fetch();
	}

	public function get_withdrawals_list($status=''){
		$id = I('selected/a');
		$user_id = I('user_id/d');
		$realname = I('realname');
		$bank_card = I('bank_card');
		$create_time = I('create_time');
		$create_time = str_replace("+"," ",$create_time);
		$create_time2 = $create_time  ? $create_time  : date('Y-m-d',strtotime('-1 year')).' - '.date('Y-m-d',strtotime('+1 day'));
		$create_time3 = explode(' - ',$create_time2);
		$this->assign('start_time',$create_time3[0]);
		$this->assign('end_time',$create_time3[1]);
		$where['w.create_time'] =  array(array('gt', strtotime($create_time3[0]), array('lt', strtotime($create_time3[1]))));
		$status = $status == '' ? I('status') : $status;
		if($status !== ''){
			$where['w.status'] = $status;
		}else{
			$where['w.status'] = ['lt',2];
		}
		if($id){
			$where['w.id'] = ['in',$id];
		}
		$user_id && $where['u.user_id'] = $user_id;
		$realname && $where['w.realname'] = array('like','%'.$realname.'%');
		$bank_card && $where['w.bank_card'] = array('like','%'.$bank_card.'%');
		$export = I('export');
		if($export == 1){
			$strTable ='<table width="500" border="1">';
			$strTable .= '<tr>';
			$strTable .= '<td style="text-align:center;font-size:12px;width:120px;">申请人</td>';
			$strTable .= '<td style="text-align:center;font-size:12px;" width="100">提现金额</td>';
			$strTable .= '<td style="text-align:center;font-size:12px;" width="*">银行名称</td>';
			$strTable .= '<td style="text-align:center;font-size:12px;" width="*">银行账号</td>';
			$strTable .= '<td style="text-align:center;font-size:12px;" width="*">开户人姓名</td>';
			$strTable .= '<td style="text-align:center;font-size:12px;" width="*">申请时间</td>';
			$strTable .= '<td style="text-align:center;font-size:12px;" width="*">提现备注</td>';
			$strTable .= '</tr>';
			$remittanceList = Db::name('withdrawals')->alias('w')->field('w.*,u.nickname')->join('__USERS__ u', 'u.user_id = w.user_id', 'INNER')->where($where)->order("w.id desc")->select();
			if(is_array($remittanceList)){
				foreach($remittanceList as $k=>$val){
					$strTable .= '<tr>';
					$strTable .= '<td style="text-align:center;font-size:12px;">'.$val['nickname'].'</td>';
					$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['money'].' </td>';
					$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['bank_name'].'</td>';
					$strTable .= '<td style="vnd.ms-excel.numberformat:@">'.$val['bank_card'].'</td>';
					$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['realname'].'</td>';
					$strTable .= '<td style="text-align:left;font-size:12px;">'.date('Y-m-d H:i:s',$val['create_time']).'</td>';
					$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['remark'].'</td>';
					$strTable .= '</tr>';
				}
			}
			$strTable .='</table>';
			unset($remittanceList);
			downloadExcel($strTable,'remittance');
			exit();
		}
		$count = Db::name('withdrawals')->alias('w')->join('__USERS__ u', 'u.user_id = w.user_id', 'INNER')->where($where)->count();
		$Page  = new Page($count,20);
		$list = Db::name('withdrawals')->alias('w')->field('w.*,u.nickname')->join('__USERS__ u', 'u.user_id = w.user_id', 'INNER')->where($where)->order("w.id desc")->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('create_time',$create_time2);
		$show  = $Page->show();
		$this->assign('show',$show);
		$this->assign('list',$list);
		$this->assign('pager',$Page);
		C('TOKEN_ON',false);
	}
	/**
	 * 商家提现申请记录
	 */
	public function store_withdrawals()
	{
		$this->get_store_withdrawals(null);
		return $this->fetch();
	}

	public function get_store_withdrawals($status){
		$store_id = I('store_id');
		$realname = I('realname');
		$bank_card = I('bank_card');
		$create_time = I('create_time');

		$create_time = str_replace("+"," ",$create_time);
		$create_time2 = $create_time  ? $create_time  : date('Y-m-d',strtotime('-1 year')).' - '.date('Y-m-d',strtotime('+1 day'));
		$create_time3 = explode(' - ',$create_time2);
		$this->assign('start_time', $create_time3[0]);
		$this->assign('end_time', $create_time3[1]);
		$where['sw.create_time'] =  array(array('gt', strtotime($create_time3[0])), array('lt', strtotime($create_time3[1])));
		$store_id && $where['sw.store_id'] = $store_id;
		$status = empty($status) ? I('status') : $status;
//      if($status === '0' || $status > 0) {
		$where['sw.status'] = $status;
//      }
		$bank_card && $where['sw.bank_card'] = array('like','%'.$bank_card.'%');
		$realname && $where['sw.realname'] = array('like','%'.$realname.'%');
		$export = I('export');
		if($export == 1){
			$this->exportStoreWithdrawals($where);  //打印
		}
		$count = Db::name('store_withdrawals')->alias('sw')->field('sw.id')->join('__STORE__ s','s.store_id = sw.store_id', 'INNER')->where($where)->count();
		$Page  = new Page($count,20);
		$list = Db::name('store_withdrawals')->alias('sw')->field('sw.*,s.store_name')->join('__STORE__ s','s.store_id = sw.store_id', 'INNER')->where($where)->order("`id` desc")->limit($Page->firstRow.','.$Page->listRows)->select();

		$this->assign('create_time',$create_time2);
		$show  = $Page->show();
		$this->assign('show',$show);
		$this->assign('list',$list);
		$this->assign('pager',$Page);
		C('TOKEN_ON',false);
	}

	function  exportStoreWithdrawals($where){
		$strTable ='<table width="500" border="1">';
		$strTable .= '<tr>';
		$strTable .= '<td style="text-align:center;font-size:12px;width:120px;">申请人</td>';
		$strTable .= '<td style="text-align:center;font-size:12px;" width="100">提现金额</td>';
		$strTable .= '<td style="text-align:center;font-size:12px;" width="*">银行名称</td>';
		$strTable .= '<td style="text-align:center;font-size:12px;" width="*">银行账号</td>';
		$strTable .= '<td style="text-align:center;font-size:12px;" width="*">开户人姓名</td>';
		$strTable .= '<td style="text-align:center;font-size:12px;" width="*">申请时间</td>';
		$strTable .= '<td style="text-align:center;font-size:12px;" width="*">提现备注</td>';
		$strTable .= '</tr>';
		$remittanceList = Db::name('store_withdrawals')->alias('sw')->field('sw.*,s.store_name')->join('__STORE__ s','s.store_id = sw.store_id', 'INNER')->where($where)->order("sw.id desc")->select();
		if(is_array($remittanceList)){
			foreach($remittanceList as $k=>$val){
				$strTable .= '<tr>';
				$strTable .= '<td style="text-align:center;font-size:12px;">'.$val['store_name'].'</td>';
				$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['money'].' </td>';
				$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['bank_name'].'</td>';
				$strTable .= '<td style="vnd.ms-excel.numberformat:@">'.$val['bank_card'].'</td>';
				$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['realname'].'</td>';
				$strTable .= '<td style="text-align:left;font-size:12px;">'.date('Y-m-d H:i:s',$val['create_time']).'</td>';
				$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['remark'].'</td>';
				$strTable .= '</tr>';
			}
		}
		$strTable .='</table>';
		unset($remittanceList);
		downloadExcel($strTable,'remittance');
		exit();
	}
	/**
	 * 删除申请记录
	 */
	public function delStoreWithdrawals()
	{
		$del_id = I('del_id');
		$del_where =  "id = $del_id and status < 0";
		$res = M("store_withdrawals")->where($del_where)->delete();
		if($res){
			$this->ajaxReturn(['status'=>1,'msg'=>'删除成功']);
		}else{
			$this->ajaxReturn(['status'=>0,'msg'=>'删除失败']);
		}
	}

	/**
	 * 修改编辑商家 申请提现
	 */
	public function editStoreWithdrawals()
	{
		$id = I('id');
		$withdrawals = Db::name('store_withdrawals')->where('id', $id)->find();
		$store = M('store')->where("store_id", $withdrawals['store_id'])->find();
		$this->assign('store', $store);
		$this->assign('data', $withdrawals);
		return $this->fetch();
	}

	/**
	 * 删除申请记录
	 */
	public function delWithdrawals()
	{
		$del_id = I('del_id/d');
		$Info = DB::name("withdrawals")->where(['id '=>$del_id])->where('status < 0')->find();
		if(empty($Info)){
		    $this->ajaxReturn(['status'=>1,'msg'=>'数据不存在']);
		}
    	Db::startTrans();
        try{
           $res_one = DB::name("withdrawals")->where(['id '=>$Info['id']])->delete();
    		//冻结提现金额处理
    	   $res_tow = Db::name('store')->where(['store_id'=>STORE_ID])->setInc('store_money',$data['money']);
    	   $res_th = Db::name('store')->where(['store_id'=>STORE_ID])->setDec('frozen_money',$data['money']);
    	   if(!$res_one || !$res_tow || !$res_th){
    	       Db::rollback();
    	       $this->ajaxReturn(['status'=>0,'msg'=>'删除成失败']);
    	   }
           Db::commit();    
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            if($res!==false){
                $this->ajaxReturn(['status'=>1,'msg'=>'删除成功']);
            }else{
                $this->ajaxReturn(['status'=>0,'msg'=>'删除失败']);
            }
        }
		
		

	}

	/**
	 * 修改编辑 申请提现
	 */
	public  function editWithdrawals(){
		$id = I('id');
		$model = M("withdrawals");
		$withdrawals = $model->find($id);
		$user = M('users')->where("user_id = {$withdrawals[user_id]}")->find();
		if($user['nickname'])
			$withdrawals['user_name'] = $user['nickname'];
		elseif($user['email'])
			$withdrawals['user_name'] = $user['email'];
		elseif($user['mobile'])
			$withdrawals['user_name'] = $user['mobile'];
		$this->assign('withdraw_status',C('WITHDRAW_STATUS'));
		$this->assign('user',$user);
		$this->assign('data',$withdrawals);
		return $this->fetch();
	}

	/**
	 *  商家结算记录
	 */
	public function order_statis(){
		$store_id = I('store_id/d',0);
		$create_date = I('create_date');
		$create_date = str_replace("+"," ",$create_date);
		$create_date2 = $create_date  ? $create_date  : date('Y-m-d',strtotime('-1 month')).' - '.date('Y-m-d',strtotime('+1 month'));
		$create_date3 = explode(' - ',$create_date2);
		$where = " os.create_date >= '".strtotime($create_date3[0])."' and os.create_date <= '".strtotime($create_date3[1])."' ";
		$this->assign('start_time',$create_date3[0]);
		$this->assign('end_time',$create_date3[1]);
		$store_id && $where .= " and os.store_id = $store_id ";

		$count = Db::name('order_statis')->alias('os')->where($where)->count();
		$Page  = new Page($count,20);
		$list = Db::name('order_statis')
				->alias('os')->join('__STORE__ s','s.store_id = os.store_id')
				->where($where)->order("`id` desc")->limit($Page->firstRow.','.$Page->listRows)->select();

		$this->assign('create_date',$create_date2);
		$this->assign('pager',$Page);
		$this->assign('list',$list);
		C('TOKEN_ON',false);
		return $this->fetch();
	}

	/**
	 *  处理会员提现申请
	 */
	public function withdrawals_update(){
		$id = I('id/a');
		$data['status']= $status = I('status');
		$data['remark'] = I('remark');
		
		if (M('withdrawals')->where('id in ('.implode(',', $id).')')->where('status', 'in', '-2,-1,3')->find()) {
			$this->ajaxReturn(array('status'=>0,'msg'=>"审核失败或作废的申请无法再次操作"),'JSON');
		}
		
		if($status == 1) {
			$data['check_time'] = time();
		}
		if($status != 1) {
			$data['refuse_time'] = time();
			$oldStatus = M('withdrawals')->where('id in ('.implode(',', $id).')')->getField('id, id, status, money, user_id');
		}
		
		$Model = M('withdrawals');
		Db::startTrans();
		$r = $Model->where('id in ('.implode(',', $id).')')->update($data);
		if($r){
			Db::commit();
			$this->ajaxReturn(array('status'=>1,'msg'=>"操作成功"),'JSON');
		}else{
			$this->ajaxReturn(array('status'=>0,'msg'=>"操作失败"),'JSON');
		}
	}

	/**
	 *  处理店铺提现申请  商家提现
	 */
	public function store_withdrawals_update(){
		$id = I('id/a');
		$data['status']= $status = I('status');
		if($status == 1) $data['check_time'] = time();
		if($status != 1) $data['refuse_time'] = time();
		$data['remark'] = I('remark');
		
		Db::startTrans();
        try{
           $r = M('store_withdrawals')->where(' id in ('.implode(',', $id).')')->save($data);
           if(!$r){
               $this->ajaxReturn(array('status'=>0,'msg'=>"操作失败"));
           }
    	   //冻结提现金额处理
    	   if($status != 1){
    	       foreach ($id as $k =>$v)
    	       {
    	           $withdrawalsInfo = Db::name('store_withdrawals')->where(['id'=>$v])->find();
    	           $res_tow    = Db::name('store')->where(['store_id'=>$withdrawalsInfo['store_id']])->setInc('store_money',$withdrawalsInfo['money']);
    	           $res_one    = Db::name('store')->where(['store_id'=>$withdrawalsInfo['store_id']])->setDec('frozen_money',$withdrawalsInfo['money']);
    	           if(!$res_one || !$res_tow ){
    	               Db::rollback();
    	               $this->ajaxReturn(['status'=>0,'msg'=>'操作失败']);
    	           }
    	       }
    	   }
           Db::commit();    
           $this->ajaxReturn(['status'=>1,'msg'=>'操作成功']);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->ajaxReturn(['status'=>0,'msg'=>$e->getmessage()]);
        }
	}
	// 用户申请提现
	public function transfer(){
		$id = I('selected/a');
		if(empty($id))$this->error('请至少选择一条记录');
		$atype = I('atype');
		if(is_array($id)){
			$withdrawals = M('withdrawals')->where('id in ('.implode(',', $id).')')->select();
		}else{
			$withdrawals = M('withdrawals')->where(array('id'=>$id))->select();
		}
		$alipay['batch_num'] = 0;
		$alipay['batch_fee'] = 0;
		$message_logic = new \app\common\logic\MessageNoticeLogic();
		$refundLogic = new RefundLogic();

		foreach($withdrawals as $val){
			$user = M('users')->where(array('user_id'=>$val['user_id']))->find();
			//查找用户和微信的绑定关系，优先选择微信客户端的绑定关系
            $oauthUsers = M("OauthUsers")->where(['user_id' => $user['user_id'], 'oauth' => 'weixin'])->find();
			if (!$oauthUsers) {
				$oauthUsers = M("OauthUsers")->where(['user_id' => $user['user_id']])->find();
			}
			
			//与微信的不同绑定方式选择不同的支付类型
			switch ($oauthUsers['oauth']) {
				case 'weixin': $code = 'weixin'; break;
				case 'wx': $code = 'appWeixinPay'; break;
				case 'miniapp': $code = 'miniAppPay';break;
				default: $code = '';
			}
            $money = $user['distribut_money'] - $user['distribut_withdrawals_money']; //可提现佣金

			if ($val['type'] == 1 && $money < $val['money']) {
                $data = array('status' => -2, 'remark' => '账户佣金可用余额不足');
                M('withdrawals')->where(array('id' => $val['id']))->save($data);
                $this->error('账户佣金可用余额不足');
            }
            else if ($val['status'] == 1) {
				$realMoney = $val['money'] - $val['taxfee'];
				$rdata = array('type'=>1,'money'=>$realMoney,'log_type_id'=>$val['id'],'user_id'=>$val['user_id']);
				if($atype == 'online'){
					
					if($val['bank_name'] == '支付宝'){
						$alipay = array(
							'out_biz_no' =>  date('YmdHis').mt_rand(100, 999).'u',//商户转账唯一订单号。用于将转账回执通知给来源方。
							'payee_account' => $val['bank_card'],//收款方账户。
							'amount'=>$realMoney,//转账金额
							'payee_real_name' => $val['realname'],//收款方真实姓名
							'remark' => '恭喜您提现申请成功!'
						);

						//单笔转账到支付宝账户接口
						include_once  PLUGIN_PATH."payment/newalipay/newalipay.class.php";
						$alipay_obj = new \newalipay();
						$res  = $alipay_obj->transfer($alipay);
						if ($res['status'] == 10000) {//支付宝转账成功
                            if($val['type'] == 1){
                                accountDistributLog($val['user_id'],0,"分销佣金提现申请", $val['money'] , $val['id']);
                            }else{
                                accountLog($val['user_id'], ($val['money'] * -1), 0, "平台处理用户提现申请",0,0,'',false,$val['money']);
                            }
							//更新提现记录
							M('withdrawals')->where(array('id'=>$val['id']))->save(array('status'=>2,'pay_time'=>time(),'pay_code'=>$res['out_biz_no']));
							//平台支出日志记录
							$refundLogic->expenseLog($rdata);
						} else {
							//更新提现记录
							$update = array('error_code'=>$res['sub_code'],'pay_time'=>time(),'pay_code'=>$res['out_biz_no'], 'remark'=>$res['sub_msg']);
							M('withdrawals')->where(array('id'=>$val['id']))->save($update);
							$this->error($res['msg'].'=>'.$res['sub_msg'], 'Admin/Finance/remittance');
						}

					}else if($val['bank_name'] == '微信'){
						$wxpay = array(
								'userid' => $val['user_id'],//用户ID做更新状态使用
								'openid' => $oauthUsers['openid'],//收款人微信号对应的 OPENID
								'pay_code'=>$val['user_id'].$val['id'].date('YmdHis'),//商户订单号，需要唯一
								'money' => $realMoney,//金额
								'desc' => '恭喜您提现申请成功!'
						);
						include_once  PLUGIN_PATH."payment/weixin/weixin.class.php";
						$wxpay_obj = new \weixin($code);
						$res = $wxpay_obj->transfer($wxpay);//微信在线付款转账
						if($res['partner_trade_no']){
                            if($val['type'] == 1){
                                accountDistributLog($val['user_id'],0,"分销佣金提现申请", $val['money'] , $val['id']);
                            }else{
                                accountLog($val['user_id'], ($val['money'] * -1), 0, "平台处理用户提现申请",0,0,'',false,$val['money']);
                            }
							M('withdrawals')->where(array('id'=>$val['id']))->save(array('status'=>2,'pay_time'=>time(),'pay_code'=>$res['partner_trade_no']));
							$refundLogic->expenseLog($rdata);
							$message_logic->withdrawalsNotice($val['id'], $val['user_id'], $realMoney . '(-' . $val['taxfee'] . '元手续费)');
						}else{
							$this->error($res['msg'].'=>'.$res['0']);
						}
					}else{
						$this->error('由于银联不提供在线付款接口，所以银行卡提现不支持在线转账');
					}
					
				}else{
                    if($val['type'] == 1){
                        accountDistributLog($val['user_id'],0,"分销佣金提现申请", $val['money'] , $val['id']);
                    }else{
                        accountLog($val['user_id'], ($val['money'] * -1), 0, "平台处理用户提现申请",0,0,'',false,$val['money']);
                    }
					$r = M('withdrawals')->where(array('id'=>$val['id']))->save(array('status'=>2,'pay_time'=>time()));
					$refundLogic->expenseLog($rdata);//支出记录日志
					$message_logic->withdrawalsNotice($val['id'], $val['user_id'], $realMoney . '(-' . $val['taxfee'] . '元手续费)');
				}
			} else {
				$this->error('参数错误，操作失败');
			}
		}
		
		$this->success("操作成功!",U('remittance'),3);
	}

	// 商家提现
	public function store_transfer(){
		$id = I('selected/a');
		if(empty($id)) $this->error('请至少选择一条记录');

		$atype = I('atype');
		if(is_array($id)){
			$withdrawals = M('store_withdrawals')->where('id in ('.implode(',', $id).')')->select();
		}else{
			$withdrawals = M('store_withdrawals')->where(array('id'=>$id))->select();
		}

		$message_store = new \app\common\logic\MessageStoreLogic();
		$refundLogic = new RefundLogic();

		$alipay['batch_num'] = 0;
		$alipay['batch_fee'] = 0;
		foreach($withdrawals as $val){
		    //冻结资金处理
			$store = M('store')->where(array('store_id'=>$val['store_id']))->find();
			if($store['frozen_money'] < $val['money'])
			{
				$data['status'] = -2;
				$data['remark'] = '冻结金额不足';
				M('store_withdrawals')->where(array('id'=>$val['id']))->save($data);
				$this->error('冻结金额不足');
			}else{
				$rdata = array('type'=>0,'money'=>$val['money'],'log_type_id'=>$val['id'],'store_id'=>$val['store_id']);
				if($atype == 'online'){
					
					if($val['bank_name'] == '支付宝'){

						//单笔转账到支付宝账户接口
						$alipay = array(
							'out_biz_no' =>  time().mt_rand(100, 999).'s',//商户转账唯一订单号。用于将转账回执通知给来源方。
							'payee_account' => $val['bank_card'],//收款方账户。
							'amount'=>$val['money'],//转账金额
							'payee_real_name' => $val['realname'],//收款方真实姓名
							'remark' => '恭喜您提现申请成功!'
						);

						include_once  PLUGIN_PATH."payment/newalipay/newalipay.class.php";

						$alipay_obj = new \newalipay();
						$res  = $alipay_obj->transfer($alipay);

						if ($res['status'] == 10000) {//支付宝转账成功
							//记录帐户变动
							storeAccountLog($val['store_id'], 0,0,($val['money'] * -1),"平台理用户提现处申请");
							//更新提现记录
							M('store_withdrawals')->where(array('id'=>$val['id']))->save(array('status'=>2,'pay_time'=>time(),'pay_code'=>$res['out_biz_no']));
							//平台支出日志记录
							$refundLogic->expenseLog($rdata);
						} else {
							//更新提现记录
							$update = array('error_code'=>$res['sub_code'],'pay_time'=>time(),'pay_code'=>$res['out_biz_no'], 'remark'=>$res['sub_msg']);
							M('store_withdrawals')->where(array('id'=>$val['id']))->save($update);
							$this->error($res['msg'].'=>'.$res['sub_msg'], 'Admin/Finance/store_remittance');
						}

					}else if($val['bank_name'] == '微信'){
						//查找用户和微信的绑定关系，优先选择微信客户端的绑定关系
						$oauthUsers = M("OauthUsers")->where(['user_id' => $user['user_id'], 'oauth' => 'weixin'])->find();
						if (!$oauthUsers) {
							$oauthUsers = M("OauthUsers")->where(['user_id' => $user['user_id']])->find();
						}
						//与微信的不同绑定方式选择不同的支付类型
						switch ($oauthUsers['oauth']) {
							case 'weixin': $code = 'weixin'; break;
							case 'wx': $code = 'appWeixinPay'; break;
							case 'miniapp': $code = 'miniAppPay';break;
							default: $code = '';
						}
						
						$wxpay = array(
							'userid' => $val['user_id'],//用户ID做更新状态使用
							'openid' => $OauthUsers['openid'],//收款人微信号对应的 OPENID
							'pay_code'=>$val['store_id'].date('YmdHis'),//商户订单号，需要唯一
							'money' => $val['money'],//金额
							'desc' => '恭喜您提现申请成功!'
						);
						include_once  PLUGIN_PATH."payment/weixin/weixin.class.php";
						$wxpay_obj = new \weixin($code);
						$res = $wxpay_obj->transfer($wxpay);//微信在线付款转账
						if($res['partner_trade_no']){
							storeAccountLog($val['store_id'],0, 0,($val['money'] * -1),"平台处理商家提现申请");
							M('store_withdrawals')->where(array('id'=>$val['id']))->save(array('status'=>2,'pay_time'=>time(),'pay_code'=>$res['partner_trade_no']));
							$refundLogic->expenseLog($rdata);
							$message_store->withdrawals($val['store_id'], $val['money'])->sendMessage();
						}else{
							$this->error($res['msg']);
						}
					}else{
						$this->error('由于银联不提供在线付款接口，所以银行卡提现不支持在线转账');
					}
					
				}else{
					storeAccountLog($val['store_id'], 0,0,($val['money'] * -1),"管理员处理商家提现申请");//手动转账，默认视为已通过线下转方式处理了该笔提现申请
					$r = M('store_withdrawals')->where(array('id'=>$val['id']))->save(array('status'=>2,'pay_time'=>time()));
					$refundLogic->expenseLog($rdata);//支出记录日志
					$message_store->withdrawals($val['store_id'], $val['money'])->sendMessage();
				}
			}
			//去掉冻结金额
			
		}

		$this->success("操作成功!",U('store_remittance'),3);
	}

	public function expense_log(){
		$map = array();
		$begin = strtotime(I('add_time_begin'));
		$end = strtotime(I('add_time_end'));
		if($begin && $end){
			$map['addtime'] = array('between',"$begin,$end");
		}
		$count = Db::name('expense_log')->where($map)->count();
		$page = new Page($count);
		$lists  = Db::name('expense_log')->where($map)->limit($page->firstRow.','.$page->listRows)->order('addtime desc')->select();
		$this->assign('page',$page->show());
		$this->assign('total_count',$count);
		$this->assign('list',$lists);
		$admin = Db::name('admin')->getField('admin_id,user_name');
		$this->assign('admin',$admin);
		$typeArr = array('商家提现','会员提现','订单取消退款','订单售后退款','其他');
		$this->assign('typeArr',$typeArr);
		return $this->fetch();
	}
	/**
	 * 平台订单收入记录
	 */
	public function order_revenue_log()
	{
		$pay_status = C('PAY_STATUS');
		$store_list = M('store')->getField('store_id,store_name');

		$condition['order_status'] = ['in', [1, 2, 4]];
		$condition['pay_status'] = 1;
		$begin = strtotime(I('start_time'));
		$end = strtotime(I('end_time'));
		if($begin && $end){
			$condition['add_time'] = array('between',"$begin,$end");
		}

		$count = db('order')->where($condition)->count();
		$money_count = db('order')->where($condition)->sum('total_amount');
		$page = new Page($count);

		$orderList = db('order')
				->where($condition)
				->field('order_sn, total_amount, pay_status, pay_name, add_time, store_id')
				->order('order_id desc')
				->limit($page->firstRow.','.$page->listRows)
				->select();

		$this->assign('page',$page->show());
		$this->assign('total_count',$count);
		$this->assign('money_count',$money_count);
		$this->assign('pay_status',$pay_status);
		$this->assign('store_list',$store_list);
		$this->assign('orderList', $orderList);
		return $this->fetch();
	}
	/**
	 * 平台充值收入记录
	 */
	public function recharge_revenue_log()
	{
		$timegap = I('timegap');
		$map['pay_status'] = 1;
		if($timegap){
			$gap = explode(',', $timegap);
			$begin = $gap[0];
			$end = $gap[1];
			$this->assign('start_time',$begin);
			$this->assign('end_time',$end);
			$map['ctime'] = array('between',array(strtotime($begin),strtotime($end)));
		}

		$count = M('recharge')->where($map)->count();
		$money_count = M('recharge')->where($map)->sum('account');
		$page = new Page($count,20);
		$lists  = M('recharge')->where($map)->order('ctime desc')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('pager',$page);
		$this->assign('page',$page->show());
		$this->assign('money_count',$money_count);
		$this->assign('lists',$lists);
		return $this->fetch();
	}
}