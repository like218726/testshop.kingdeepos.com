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
 * Date: 2018-01-31
 * 预售控制器
 */

namespace app\seller\controller;

use app\common\model\Order;
use think\Loader;
use think\Db;
use app\common\model\PreSell as PreSellModel;
use think\Page;

class PreSell extends Base
{
	public function index()
	{
		
		$PreSell = new PreSellModel();
		$count = $PreSell->where('store_id', STORE_ID)->count();
		$page = new Page($count, 10);
		$list = $PreSell->append(['status_desc'])->where('store_id', STORE_ID)->order('pre_sell_id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
		$this->assign('page', $page);
		$this->assign('list', $list);
		return $this->fetch();
		
	}

	/**
	 * 预售详情
	 * @return mixed
	 */
	public function info()
	{
		
		$pre_sell_id = input('id');
		if ($pre_sell_id) {
			$PreSellModel = new PreSellModel();
			$preSell = $PreSellModel->where(['pre_sell_id'=>$pre_sell_id,'store_id'=>STORE_ID])->find();
			if(empty($preSell)){
				$this->error('非法操作');
			}
			$this->assign('preSell', $preSell);
		}
		return $this->fetch();
		
	}

	/**
	 * 保存
	 */
	public function save()
	{
		
		$data = input('post.');
		$preSellValidate = Loader::validate('PreSell');
		if (!$preSellValidate->batch()->check($data)) {
			$this->ajaxReturn(['status' => 0, 'msg' => '操作失败', 'result' => $preSellValidate->getError()]);
		}
		if ($data['pre_sell_id']) {
			$preSell = PreSellModel::get(['pre_sell_id' => $data['pre_sell_id'], 'store_id' => STORE_ID]);
			if (empty($preSell)) {
				$this->ajaxReturn(array('status' => 0, 'msg' => '非法操作', 'result' => ''));
			}
			if($preSell['is_finished'] > 0){
				$this->ajaxReturn(array('status' => 0, 'msg' => '该活动已结束不能修改资料', 'result' => ''));
			}
		} else {
			$preSell = new PreSellModel();
		}
		$preSell->data($data, true);
		if($data['item_id']){
			$preSell['item_name'] = Db::name('spec_goods_price')->where('item_id', $data['item_id'])->value('key_name');
		}
		$price_ladder = array();
		foreach ($data['ladder_amount'] as $key => $value) {
			$price_ladder[$key]['amount'] = intval($data['ladder_amount'][$key]);
			$price_ladder[$key]['price'] = floatval($data['ladder_price'][$key]);
		}
		$price_ladder = array_values(array_sort($price_ladder, 'amount', 'asc'));
		$preSell['price_ladder'] = json_encode($price_ladder);
		$preSell['store_id'] = STORE_ID;
		$row = $preSell->allowField(true)->save();
		if ($data['item_id'] > 0) {
			Db::name('spec_goods_price')->where(['item_id' => $data['item_id']])->update(['prom_id' => $preSell->pre_sell_id, 'prom_type' => 4]);
			Db::name('goods')->where(['goods_id' => $data['goods_id']])->update(['prom_type' => 4, 'prom_id' => 0]);
		} else {
			Db::name('goods')->where(['goods_id' => $data['goods_id']])->update(['prom_id' => $preSell->pre_sell_id, 'prom_type' => 4]);
		}
		if ($row !== false) {
			$this->ajaxReturn(['status' => 1, 'msg' => '操作成功', 'result' => '']);
		} else {
			$this->ajaxReturn(['status' => 0, 'msg' => '操作失败', 'result' => '']);
		}
		
	}

	/**
	 * 删除
	 */
	public function delete(){
		
		$pre_sell_id = input('pre_sell_id');
		if($pre_sell_id){
			$order_goods = Db::name('order_goods')->where(['prom_type' => 4, 'prom_id' => $pre_sell_id])->find();
			if($order_goods){
				$this->ajaxReturn(['status' => 0, 'msg' => '该活动有订单参与不能删除!', 'result' => '']);
			}
			$preSell= PreSellModel::get(['store_id'=>STORE_ID,'pre_sell_id'=>$pre_sell_id]);
			if($preSell){
				if($preSell['item_id']){
					Db::name('spec_goods_price')->where('item_id', $preSell['item_id'])->save(['prom_type' => 0, 'prom_id' => 0]);
					$goodsPromCount = Db::name('spec_goods_price')->where('goods_id', $preSell['goods_id'])->where('prom_type','>',0)->count('item_id');
					if($goodsPromCount == 0){
						Db::name('goods')->where("goods_id", $preSell['goods_id'])->save(['prom_type' => 0, 'prom_id' => 0]);
					}
				}else{
					Db::name('goods')->where("goods_id", $preSell['goods_id'])->save(['prom_type' => 0, 'prom_id' => 0]);
				}
				$row = $preSell->delete();
				if($row !== false){
					$message_logic = new \app\common\logic\MessageActivityLogic([]);
					$message_logic->deletedMessage($pre_sell_id, 4);
					$this->ajaxReturn(['status' => 1, 'msg' => '删除成功', 'result' => '']);
				}else{
					$this->ajaxReturn(['status' => 0, 'msg' => '删除失败', 'result' => '']);
				}
			}else{
				$this->ajaxReturn(['status' => 0, 'msg' => '参数错误', 'result' => '']);
			}
		}else{
			$this->ajaxReturn(['status' => 0, 'msg' => '参数错误', 'result' => '']);
		}
		
	}

	public function succeed()
	{
		
		$pre_sell_id = input('pre_sell_id');
		$PreSell = new PreSellModel();
		$preSell = $PreSell::get(['store_id'=>STORE_ID,'pre_sell_id'=>$pre_sell_id]);
		if(empty($preSell)){
			$this->ajaxReturn(['status' => 0, 'msg' => '操作失败', 'result' => '']);
		}
		if($preSell['is_finished'] == 0){
			$this->ajaxReturn(['status' => 0, 'msg' => '该预售商品正在预售中，请先结束活动', 'result' => '']);
		}
		if($preSell['is_finished'] != 1){
			$this->ajaxReturn(['status' => 0, 'msg' => '该预售商品已经结束', 'result' => '']);
		}
		//获取购买预售商品的订单id数组
		$pre_sell_order_id_where = array(
				'prom_type' => 4,
				'prom_id' => $pre_sell_id,
				'order_status' => 0
		);
		$pre_sell_order_id_list = Db::name('order')->where($pre_sell_order_id_where)->getField('order_id', true);
		if (count($pre_sell_order_id_list) > 0) {
			//更新所有预售商品的订单的订单商品的金额
			Db::name('order_goods')->where('order_id', 'IN', $pre_sell_order_id_list)->save(['member_goods_price' => $preSell['ing_price'], 'final_price' => $preSell['ing_price']]);
			//获取所有更新后的订单商品的商品总价
			$pre_sell_order_goods = Db::name('order_goods')
					->field('order_id,SUM(goods_num*member_goods_price) as goods_amount')
					->where('order_id', 'IN', $pre_sell_order_id_list)
					->group('order_id')
					->select();
			//更新订单的价格
			$Order = new Order();
			foreach ($pre_sell_order_goods as $key => $val) {
				$able_message = false;//是否需要通知用户
				$message = '';
				$preSellOrder = $Order->where(['order_id' => $pre_sell_order_goods[$key]['order_id']])->find();
				//如果订单未支付的将其作废
				if ($preSellOrder['pay_status'] == 0) {
					$preSellOrder['order_status'] = 5;
					$preSellOrder->save();
				}
				//如果是支付定金的
				if ($preSellOrder['paid_money'] > 0 && $preSellOrder['pay_status'] == 2) {
					$preSellOrder['goods_price'] = $pre_sell_order_goods[$key]['goods_amount'];
					$preSellOrder['total_amount'] = $pre_sell_order_goods[$key]['goods_amount'];
					$preSellOrder['order_amount'] = $pre_sell_order_goods[$key]['goods_amount'] - $preSellOrder['paid_money'];//需要支付的尾款
					$preSellOrder->save();
//					$able_message = true;
					$message = '您的预售订单需要支付尾款，订单号为' . $preSellOrder['order_sn'];
				}
				//如果是支付全款的
				if ($preSellOrder['paid_money'] == 0 && $preSellOrder['pay_status'] == 1) {
					//如果需要退还差价的
					if ($preSellOrder['order_amount'] > $pre_sell_order_goods[$key]['goods_amount']) {
						$cha_amount = $preSellOrder['order_amount'] - $pre_sell_order_goods[$key]['goods_amount'];
						$preSellOrder['goods_price'] = $pre_sell_order_goods[$key]['goods_amount'];
						$preSellOrder['total_amount'] = $pre_sell_order_goods[$key]['goods_amount'];
						$preSellOrder['order_amount'] = $pre_sell_order_goods[$key]['goods_amount'];
						$preSellOrder->save();
						accountLog($preSellOrder['user_id'], $cha_amount, 0, '退还预售商品' . $preSell['goods_name'] . '的差价，订单ID为' . $preSellOrder['order_id'], 0,$preSellOrder['order_id'],$preSellOrder['order_sn']);
					}
				}
				//通知用户订单处理
				if ($able_message == true) {
					$user_info = Db::name('users')->where('user_id', $preSellOrder['user_id'])->find();
					if (!empty($user_info)) {
						if (!empty($user_info['email'])) {
							send_email($user_info['email'], '预售订单处理', $message);
						}
					}
				}
				//更新发票价格
				Db::name('invoice')->where(['order_id'=>$pre_sell_order_goods[$key]['order_id']])->update(['invoice_money'=>$pre_sell_order_goods[$key]['goods_amount']]);
			}
		}
		$preSell['is_finished'] = 2;
		$preSell->save();
		$this->ajaxReturn(['status' => 1, 'msg' => '操作成功', 'result' => '']);
		
	}

	public function fail()
	{
		
		$pre_sell_id = input('pre_sell_id');
		$PreSell = new PreSellModel();
		$preSell = $PreSell::get(['store_id'=>STORE_ID,'pre_sell_id'=>$pre_sell_id]);
		if(empty($preSell)){
			$this->ajaxReturn(['status' => 0, 'msg' => '操作失败', 'result' => '']);
		}
		if($preSell['is_finished'] == 0){
			$this->ajaxReturn(['status' => 0, 'msg' => '该预售商品正在预售中，请先结束活动', 'result' => '']);
		}
		if($preSell['is_finished'] != 1){
			$this->ajaxReturn(['status' => 0, 'msg' => '该预售商品已经结束', 'result' => '']);
		}
		//获取购买预售商品的并且已经支付的订单id
		$pre_sell_order_where = array(
				'prom_type' => 4,
				'prom_id' => $pre_sell_id,
				'order_status' => 0,
				'pay_status' => ['in',[1,2]],
		);
		$pre_sell_order_list = Db::name('order')->field('user_id,order_id,order_sn,pay_status,goods_price,total_amount,order_amount,paid_money')->where($pre_sell_order_where)->select();
		foreach ($pre_sell_order_list as $key => $val) {
			//如果是支付定金的
			if ($pre_sell_order_list[$key]['paid_money'] > 0 && $pre_sell_order_list[$key]['pay_status'] == 2) {
				//退还订金
				accountLog($pre_sell_order_list[$key]['user_id'], $pre_sell_order_list[$key]['paid_money'], 0, '退还预售商品' . $preSell['goods_name'] . '的定金，订单ID为：' . $pre_sell_order_list[$key]['order_id'], 0, $pre_sell_order_list[$key]['order_id'], $pre_sell_order_list[$key]['order_sn']);
			}
			//如果是支付全款的
			if ($pre_sell_order_list[$key]['paid_money'] == 0 && $pre_sell_order_list[$key]['pay_status'] == 1) {
				//退还全款
				accountLog($pre_sell_order_list[$key]['user_id'], $pre_sell_order_list[$key]['order_amount'], 0, '退还预售商品' . $preSell['goods_name'] . '的全款，订单ID为：' . $pre_sell_order_list[$key]['order_id'], 0, $pre_sell_order_list[$key]['order_id'], $pre_sell_order_list[$key]['order_sn']);
			}
		}
		Db::name('order')->where(['prom_type' => 4,'prom_id' => $pre_sell_id,'order_status' => 0,])->save(['order_status' => 5]);//最后把该预售商品的订单标记已关闭
		$preSell['is_finished'] = 3;
		$preSell->save();
		$this->ajaxReturn(['status' => 1, 'msg' => '操作成功', 'result' => '']);
		
	}

	public function finish()
	{
		
		$pre_sell_id = input('pre_sell_id');
		$PreSell = new PreSellModel();
		$preSell = $PreSell::get(['store_id'=>STORE_ID,'pre_sell_id'=>$pre_sell_id]);
		if(empty($preSell)){
			$this->ajaxReturn(['status' => 0, 'msg' => '操作失败', 'result' => '']);
		}
		if($preSell['item_id']){
			$preSell->specGoodsPrice->save(['prom_type' => 0, 'prom_id' => 0]);
			$goodsPromCount = Db::name('spec_goods_price')->where('goods_id', $preSell['goods_id'])->where('prom_type','>',0)->count('item_id');
			if($goodsPromCount == 0){
				$preSell->goods->save(['prom_type' => 0, 'prom_id' => 0]);
			}
		}else{
			$preSell->goods->save(['prom_type' => 0, 'prom_id' => 0]);
		}
		if(time() < $preSell['sell_end_time']){
			$preSell->sell_end_time = date('Y-m-d H:i:s');
		}
		$preSell['is_finished'] = 1;
		$preSell->save();
		$this->ajaxReturn(['status' => 1, 'msg' => '操作成功', 'result' => '']);
		
	}
}
