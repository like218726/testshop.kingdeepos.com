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
 * Author: 当燃
 * 专题管理
 * Date: 2016-06-09
 *  拼团控制器
 */

namespace app\admin\controller;
use app\common\model\Order;
use app\common\model\PreSell as PreSellModel;
use think\Loader;
use think\Db;
use think\Page;

class PreSell extends Base
{
	public function _initialize() {
		parent::_initialize();
		$this->assign('time_begin',date('Y-m-d', strtotime("-3 month")+86400));
		$this->assign('time_end',date('Y-m-d', strtotime('+1 days')));
	}
	public function index()
	{	
	    
		$title = input('title');
		$status = input('status');
		$pre_sell_where = [];
		if ($title) {
			$pre_sell_where['title'] = ['like', '%' . $title . '%'];
		}
		if ($status != '') {
			$pre_sell_where['status'] = $status;
		}
		$PreSell = new PreSellModel();
		$count = $PreSell->where($pre_sell_where)->count();
		$Page = new Page($count, 10);
		$list = $PreSell->append(['status_desc'])->where($pre_sell_where)->order('pre_sell_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('page', $Page);
		$this->assign('list', $list);
		return $this->fetch();
		
	}


	/**
	 * 审核
	 */
	public function examine(){
		
		$pre_sell_id = input('pre_sell_id');
		$status = input('status');
		if (empty($pre_sell_id) || empty($status)) {
			$this->ajaxReturn(['status' =>0,'msg' => '参数有误','result' => '']);
		}
		$PreSell = new PreSellModel();
		$preSell = $PreSell::get($pre_sell_id);
		if($preSell){
			$preSell->status = $status;
			$row = $preSell->save();
			if($row !== false){
				if ($status == 1) {
					$message_logic = new \app\common\logic\MessageActivityLogic();
					$message_logic->sendPreSell($preSell);
				}
				$this->ajaxReturn(['status' =>1,'msg' => '操作成功','result' => '']);
			}else{
				$this->ajaxReturn(['status' =>0,'msg' => '操作失败','result' => '']);
			}
		}else{
			$this->ajaxReturn(['status' =>0,'msg' => '没有找到数据','result' => '']);
		}
		
	}

	public function order_list(){
		
		$pre_sell_id = input('pre_sell_id');
		$add_time_begin = input('add_time_begin',date('Y-m-d', strtotime("-3 month")+86400));
		$add_time_end = input('add_time_end',date('Y-m-d', strtotime('+1 days')));
		$order_status = input('order_status');
		$pay_status = input('pay_status');
		$shipping_status = input('shipping_status');
		$pay_code = input('pay_code');
		$key_type = input('key_type');
		$user_id = input('user_id');
		$order_by = input('order_by');
		$sort = input('sort','DESC');
		$begin = strtotime($add_time_begin);
		$end = strtotime($add_time_end);
		// 搜索条件
		$condition = ['prom_type'=>4];
		$keywords = I('keywords','','trim');
		if($key_type != '' && $keywords != ''){
			if($key_type == 'store_name'){
				$store_id_arr = Db::name('store')->where("store_name", "like", '%' . $keywords . '%')->getField('store_id', true);
				if ($store_id_arr) {
					$condition['store_id'] = array('in', $store_id_arr);
				}
			}elseif($key_type == 'order_sn'){
				$condition['order_sn'] = $keywords;
			}elseif($key_type == 'consignee'){
				$condition['consignee'] = $keywords;
			}
		}
		if($begin && $end){
			$condition['add_time'] = array('between',"$begin,$end");
		}
		if($order_status != ''){
			$condition['order_status'] = $order_status;
		}
		if($pay_status != ''){
			$condition['pay_status'] = $pay_status;
		}
		if($pay_code != ''){
			$condition['pay_code'] = $pay_code;
		}
		if($shipping_status != ''){
			$condition['shipping_status'] = $shipping_status;
		}
		if($user_id != ''){
			$condition['user_id'] = $user_id;
		}
		if($pre_sell_id){
			$condition['prom_id'] = $pre_sell_id;
		}
		if ($order_by != '') {
			$orderBy = [$order_by => $sort];
		} else {
			$orderBy = ['order_id' => $sort];
		}
		$order = new Order();
		$count = $order->where($condition)->count();
		$page  = new Page($count);
		//获取订单列表
		$orderList = $order->where($condition)->limit($page->firstRow,$page->listRows)->order($orderBy)->select();
		$this->assign('orderList',$orderList);
		$this->assign('page',$page);
		$this->assign('add_time_begin',date('Y-m-d H:i',$begin));
		$this->assign('add_time_end',date('Y-m-d H:i',$end));
		return $this->fetch();
		
	}

}