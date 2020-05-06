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
 * Date: 2015-09-09
 */
namespace app\admin\controller;
use app\admin\logic\OrderLogic;
use app\admin\logic\RefundLogic;
use app\admin\model\OrderAction;
use app\common\model\team\TeamFound;
use think\AjaxPage;
use think\Db;
use think\Page;

class Order extends Base {
    public  $order_status;
    public  $shipping_status;
    public  $pay_status;
    /*
     * 初始化操作
     */
    public function _initialize() {
        parent::_initialize();
        C('TOKEN_ON',false); // 关闭表单令牌验证
        // 订单 支付 发货状态
        $this->order_status = C('ORDER_STATUS');
        $this->pay_status = C('PAY_STATUS');
        $this->shipping_status = C('SHIPPING_STATUS');
        $this->assign('order_status',$this->order_status);
        $this->assign('pay_status',$this->pay_status);
        $this->assign('shipping_status',$this->shipping_status);
    }

    /*
     *订单首页
     */
    public function index(){
        return $this->fetch();
    }

    /*
     *Ajax首页
     */
    public function ajaxindex(){

		// 取消订单
		$orderlogic = new \app\common\logic\OrderLogic();
		$orderlogic->order_cancel_all();

        $select_year = getTabByTime(I('add_time_begin')); // 表后缀
        $timegap = I('timegap');
        if($timegap){
        	$gap = explode('-', $timegap);
        	$begin = strtotime($gap[0]);
        	$end = strtotime($gap[1]);
        }else{
            //@new 新后台UI参数
            $begin = $this->begin;
            $end = $this->end;
        }
        // 搜索条件
        $condition = array();
        $condition['shop_id'] = 0;
        $keyType = I("keytype");
        $keywords = I('keywords','','trim');
    
        $consignee =  ($keyType && $keyType == 'consignee') ? $keywords : I('consignee','','trim');
        $consignee ? $condition['consignee'] = ['like','%' . trim($consignee) . '%'] : false;
        
        if($begin && $end){
        	$condition['add_time'] = array('between',"$begin,$end");
        }
        
        $store_name = ($keyType && $keyType == 'store_name') ? $keywords :  I('store_name','','trim');
        if($store_name)
        {
            $store_id_arr = M('store')->where("store_name like '%$store_name%'")->getField('store_id',true);
            if($store_id_arr)
            {
                $condition['store_id'] = array('in',$store_id_arr);
            }
        }    
        $condition['prom_type'] = array('in','0,1,2,3,4,8');
        $order_sn = ($keyType && $keyType == 'order_sn') ? $keywords : I('order_sn') ;
        $order_sn ? $condition['order_sn'] = trim($order_sn) : false;
         
        I('order_status') != '' ? $condition['order_status'] = I('order_status') : false;
        I('pay_status') != '' ? $condition['pay_status'] = I('pay_status') : false;
        I('pay_code') != '' ? $condition['pay_code'] = I('pay_code') : false;
        I('shipping_status') != '' ? $condition['shipping_status'] = I('shipping_status') : false;
        I('user_id') ? $condition['user_id'] = trim(I('user_id')) : false;
        I('order_statis_id') != '' ? $condition['order_statis_id'] = I('order_statis_id') : false; // 结算统计的订单
        if($condition['order_statis_id'] > 0) unset($condition['add_time']);
        $sort_order = I('order_by','DESC').' '.I('sort');
        $count = M('order'.$select_year)->where($condition)->count();
        $Page  = new AjaxPage($count,20);
        $show = $Page->show();
        //获取订单列表
        //$orderList = $orderLogic->getOrderList($condition,$sort_order,$Page->firstRow,$Page->listRows);
        $orderList = M('order'.$select_year)->where($condition)->limit("{$Page->firstRow},{$Page->listRows}")->order($sort_order)->select();
        $store_list = M('store')->getField('store_id,store_name');        
        $this->assign('store_list',$store_list);       
        $this->assign('orderList',$orderList);
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('pager',$Page);// 赋值分页输出
        
        return $this->fetch();
    }

    /*
     * ajax 发货订单列表
    */
    public function ajaxdelivery(){
    	$condition = array();
    	I('consignee') ? $condition['consignee'] = trim(I('consignee')) : false;
    	I('order_sn') != '' ? $condition['order_sn'] = trim(I('order_sn')) : false;
    	$condition['order_status'] = array('in','1,2,4');
    	$shipping_status = I('shipping_status');
    	$condition['shipping_status'] = empty($shipping_status) ? array('neq',1) : $shipping_status;    	
    	$count = M('order')->where($condition)->count();
    	$Page  = new AjaxPage($count,10);
    	//搜索条件下 分页赋值
    	foreach($condition as $key=>$val) {
    		$Page->parameter[$key]   =   urlencode($val);
    	}
    	$show = $Page->show();
    	$orderList = M('order')->where($condition)->limit($Page->firstRow.','.$Page->listRows)->order('add_time DESC')->select();
    	$this->assign('orderList',$orderList);
    	$this->assign('page',$show);// 赋值分页输出
    	return $this->fetch();
    }

    /**
     * 订单详情
     * @param $order_id
     * @return mixed
     */
    public function detail($order_id){
        $orderLogic = new OrderLogic();
        $order = $orderLogic->getOrderInfo($order_id);
        $orderGoods = $orderLogic->getOrderGoods($order_id);
        $button = $orderLogic->getOrderButton($order);
        // 获取操作记录
        $select_year = getTabByOrderId($order_id);        
        // 获取操作记录
        $action_log = M('order_action'.$select_year)->where(array('order_id'=>$order_id))->order('log_time desc')->select();
        $express = Db::name('delivery_doc'.$select_year)->where("order_id" , $order_id)->select();  //发货信息（可能多个）
        //查找用户昵称
		if($action_log){
			$userIds = [];
			$sellerIds = [];
			foreach ($action_log as $actionKey => $actionVal){
				if($actionVal['user_type'] == 2){
					$userIds[$actionKey] = $actionVal['action_user'];
				}
				if($actionVal['user_type'] == 1){
					$sellerIds[$actionKey] = $actionVal['action_user'];
				}
			}
			if(count($userIds) > 0){
				$users = Db::name("users")->where(['user_id'=>['in',array_unique($userIds)]])->getField("user_id,nickname");
				$this->assign('users',$users);
			}
			if(count($sellerIds) > 0){
				$users = Db::name("seller")->where(['seller_id'=>['in',array_unique($sellerIds)]])->getField("seller_id,seller_name");
				$this->assign('sellers',$users);
			}
		}
        $this->assign('order',$order);
        $this->assign('action_log',$action_log);
        $this->assign('orderGoods',$orderGoods);
        $this->assign('express',$express);
        $split = count($orderGoods) >1 ? 1 : 0;
        foreach ($orderGoods as $val){
        	if($val['goods_num']>1){
        		$split = 1;
        	}
        }
        $this->assign('split',$split);
        $this->assign('button',$button);
        return $this->fetch();
    }
    
    public function refund_order_list(){
    	$condition = array();
    	I('consignee') ? $condition['consignee'] = trim(I('consignee')) : false;
    	I('order_sn') != '' ? $condition['order_sn'] = trim(I('order_sn')) : false;
    	I('mobile') != '' ? $condition['mobile'] = trim(I('mobile')) : false;
		$prom_type = input('prom_type');
		if($prom_type){
			$condition['prom_type'] = $prom_type;
		}
    	$condition['shipping_status'] = 0;
    	$condition['order_status'] = 3;
    	$condition['pay_status'] = array('gt',0);
    	$count = Db::name('order')->where($condition)->count();
    	$Page  = new Page($count,10);
    	//搜索条件下 分页赋值
//    	foreach($condition as $key=>$val) {
//    		if(!is_array($val)){
//    			$Page->parameter[$key]   =   urlencode($val);
//    		}
//    	}
        //注释的原因：urlencode导致总后台第二次搜索商品失效
    	$show = $Page->show();
    	$orderList = M('order')->where($condition)->limit($Page->firstRow.','.$Page->listRows)->order('add_time DESC')->select();
    	$this->assign('orderList',$orderList);
    	$this->assign('page',$show);// 赋值分页输出
    	$this->assign('pager',$Page);
    	return $this->fetch();
    }
    
    /**
     * 退回用户金额(原路/余额退还)
     * @param unknown $order_id
     * @return \think\mixed
     */
    public function refund_order_info($order_id){
    	$orderLogic = new OrderLogic();
    	$order = $orderLogic->getOrderInfo($order_id);
    	$orderGoods = $orderLogic->getOrderGoods($order_id);
    	$this->assign('order',$order);
    	$this->assign('orderGoods',$orderGoods);
    	return $this->fetch();
    }

    //处理取消订单  订单原路退款
    public function refund_order(){
    	$data = I('post.');
    	$orderLogic = new OrderLogic();
    	$order = $orderLogic->getOrderInfo($data['order_id']);
    	if(!$order){
    		$this->error('订单不存在或参数错误');
    	}
        if($data['pay_status'] == 3) {
            $refundLogic = new RefundLogic();
			// 预售的退款单不许退定金,走退库存的方法，退款额设置为0,加这个防止误操作
			if($order['prom_type'] == 4){
				$order['order_amount'] = $order['user_money'] = 0;
				$data['refund_type'] == 1;
			}
            if ($data['refund_type'] == 1) {
                //退到用户余额  8-25
                if($refundLogic->updateRefundOrder($order,1)){
                    $this->success('成功退款到账户余额');
                }else{
                    $this->error('退款失败');
                }
            }
            if ($data['refund_type'] == 0) {   
	                 
                $pay_code_arr = ['weixinH5','weixin'/*PC+公众号微信支付*/ , 'alipay'/*APP,PC支付宝支付*/ , 'newalipay'/*新支付宝支付*/ , 'alipayMobile'/*手机支付宝支付*/ , 'newalipayMobile'/*新手机支付宝支付*/ , 'miniAppPay'/*小程序微信支付*/  , 'appWeixinPay'/*APP微信支付*/];
                if(in_array($order['pay_code'] , $pay_code_arr)){
                     if($order['pay_code'] == 'weixinH5' || $order['pay_code'] == 'weixin' || $order['pay_code'] == 'miniAppPay'  || $order['pay_code'] == 'appWeixinPay'){
						 if($order['pay_code'] == 'weixinH5'){
							 $order['pay_code'] == 'weixin';
						 }
						 // 微信退款总额再查一次
						 $total_fee = Db::name('order')->where('transaction_id',$order['transaction_id'])->sum('order_amount');
						 include_once PLUGIN_PATH . "payment/weixin/weixin.class.php";
            			$payment_obj = new \weixin($order['pay_code']);
            			$refund_data = array('transaction_id' => $order['transaction_id'], 'total_fee' => $total_fee, 'refund_fee' => $order['order_amount']);
            			$result = $payment_obj->payment_refund($refund_data);
            			if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
                            if($refundLogic->updateRefundOrder($order)){
                                $this->success('支付原路退款成功');
                            }else{
                                $this->error('支付原路退款成功,余额支付部分退款失败');
                            }
            			}else{
            				$this->error('支付原路退款失败'.$result['return_msg']." =>".$result['err_code_des'].' , 商户号: '.$result['mch_id']);
            			}
            		} else if ($order['pay_code'] == 'newalipay' || $order['pay_code'] == 'newalipayMobile') {

                        include_once PLUGIN_PATH . "payment/newalipay/newalipay.class.php";
                        $payment_obj = new \newalipay();
                        $refund_data = array('order_id' => $order['order_id'], 'trade_no' => $order['transaction_id'], 'refund_amount' => $order['order_amount'], 'refund_reason' => $data['admin_note'], 'type' => 1);

                        $result = $payment_obj->payment_refund($refund_data);
                        if ($result['status'] == 10000) {
                            $this->success('支付原路退款成功'); 
                        } else {
                            $this->error('支付原路退款失败');
                        } 
                    } else {
            			include_once PLUGIN_PATH . "payment/alipay/alipay.class.php";
            			$payment_obj = new \alipay();
            			$detail_data = $order['transaction_id'] . '^' . $order['order_amount'] . '^' . '用户申请订单退款';
                        $refund_data = array('batch_no' => date('YmdHi') .'o'.$order['order_id'], 'batch_num' => 1, 'detail_data' => $detail_data);
            			$payment_obj->payment_refund($refund_data);
            		}
            	} else {
            		$this->error('该订单支付方式不支持在线退回');
            	}
		
            }
        }else{
    		M('order')->where(array('order_id'=>$order['order_id']))->save($data);
    		$this->success('拒绝退款操作成功');
    	}
    }
// 虚拟订单列表
    public function virtual_list(){
    
    	$condition['prom_type'] = 5;
    	$sort_order = 'order_id desc';         
    	$begin = $this->begin;
    	$end = $this->end;
        $select_year = getTabByTime(I('add_time_begin')); // 表后缀 
    	if($begin && $end){
    		$condition['add_time'] = array('between',"$begin,$end");
    	}
    	I('pay_status') != '' ? $condition['pay_status'] = I('pay_status') : false;
    	I('pay_code') != '' ? $condition['pay_code'] = I('pay_code') : false;
    	$keyType = I("keytype");
    	$keywords = I('keywords','','trim');
    	$mobile =  ($keyType && $keyType == 'mobile') ? $keywords : I('mobile','','trim');
    	$mobile ? $condition['mobile'] = trim($mobile) : false;
    	$order_sn = ($keyType && $keyType == 'order_sn') ? $keywords : I('order_sn') ;
    	$order_sn ? $condition['order_sn'] = trim($order_sn) : false;
    	$store_name = ($keyType && $keyType == 'store_name') ? $keywords :  I('store_name','','trim');
    	if($store_name){
    		$store_id_arr = M('store')->where("store_name like '%$store_name%'")->getField('store_id',true);
    		if($store_id_arr) $condition['store_id'] = array('in',$store_id_arr);
    	}
    	$orderLogic = new OrderLogic();
    	$export = I('export');
    	if($export == 1){
			$order_ids = I('order_ids');
			if($order_ids){
				$condition['order_id'] = ['in',$order_ids];
			}
    		$orderList = M('order'.$select_year)->where($condition)->order($sort_order)->select();
    		$strTable ='<table width="500" border="1">';
    		$strTable .= '<tr>';
    		$strTable .= '<td style="text-align:center;font-size:12px;width:120px;">订单编号</td>';
    		$strTable .= '<td style="text-align:center;font-size:12px;" width="100">日期</td>';
    		$strTable .= '<td style="text-align:center;font-size:12px;" width="*">接收人</td>';
    		$strTable .= '<td style="text-align:center;font-size:12px;" width="*">购买人</td>';
    		$strTable .= '<td style="text-align:center;font-size:12px;" width="*">订单金额</td>';
    		$strTable .= '<td style="text-align:center;font-size:12px;" width="*">实际支付</td>';
    		$strTable .= '<td style="text-align:center;font-size:12px;" width="*">支付方式</td>';
    		$strTable .= '<td style="text-align:center;font-size:12px;" width="*">支付状态</td>';
    		$strTable .= '<td style="text-align:center;font-size:12px;" width="*">使用期限</td>';
			$strTable .= '<td style="text-align:center;font-size:12px;" width="*">商品数量</td>';
    		$strTable .= '<td style="text-align:center;font-size:12px;" width="*">商品信息</td>';
    		$strTable .= '</tr>';
    		 
    		foreach($orderList as $k=>$val){
    			$strTable .= '<tr>';
    			$strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['order_sn'].'</td>';
    			$strTable .= '<td style="text-align:left;font-size:12px;">'.date('Ymd',$val['add_time']).' </td>';
    			$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['mobile'].'</td>';
    			$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['consignee'].' </td>';
    			$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['goods_price'].'</td>';
    			$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['order_amount'].'</td>';
    			$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['pay_name'].'</td>';
    			$strTable .= '<td style="text-align:left;font-size:12px;">'.$this->pay_status[$val['pay_status']].'</td>';
    			$strTable .= '<td style="text-align:left;font-size:12px;">'.date('Ymd',$val['shipping_time']).'</td>';
    			$orderGoods = M('order_goods'.$select_year)->where('order_id='.$val['order_id'])->select();
    			$strGoods="";
				$goods_num = 0;
    			foreach($orderGoods as $goods){
					$goods_num = $goods_num + $goods['goods_num'];
    				$strGoods .= "商品编号：".$goods['goods_sn']." 商品名称：".$goods['goods_name'];
    				if ($goods['spec_key_name'] != '') $strGoods .= " 规格：".$goods['spec_key_name'];
    				$strGoods .= "<br />";
    			}
    			unset($orderGoods);
				$strTable .= '<td style="text-align:left;font-size:12px;">'.$goods_num.' </td>';
    			$strTable .= '<td style="text-align:left;font-size:12px;">'.$strGoods.' </td>';
    			$strTable .= '</tr>';
    		}
    		$strTable .='</table>';
    		unset($orderList);
    		downloadExcel($strTable,'order');
    		exit();
    	}
    	$count = M('order'.$select_year)->where($condition)->count();
    	$Page  = new Page($count,20);
    	$show = $Page->show();
    	$orderList = $orderLogic->getOrderList($condition,$sort_order,$Page->firstRow,$Page->listRows);
    	//获取每个订单的商品列表
    	$order_id_arr = get_arr_column($orderList, 'order_id');
    	$user_id_arr = get_arr_column($orderList, 'user_id');
    	$store_id_arr = get_arr_column($orderList, 'store_id');
    	if(!empty($order_id_arr));
    	if($order_id_arr){
    		$goods_list = M('order_goods'.$select_year)->where("order_id in (".  implode(',', $order_id_arr).")")->select();
    		$goods_arr = array();
    		foreach ($goods_list as $v){
    			$goods_arr[$v['order_id']][] =$v;
    		}
    		$this->assign('goodsArr',$goods_arr);
    		$user_arr = M('users')->where("user_id in (".  implode(',', $user_id_arr).")")->getField('user_id,nickname');
    		$this->assign('userArr',$user_arr);
    		$store_arr = M('store')->where("store_id in (".  implode(',', $store_id_arr).")")->getField('store_id,store_name');
    		$this->assign('store_arr',$store_arr);
    	}
    	$this->assign('orderList',$orderList);
    	$this->assign('page',$show);
    	$this->assign('total_count',$count);
    	return $this->fetch();
	
    }
    
    public function virtual_info(){
    	$order_id = I('order_id');
        // 获取操作表
        $select_year = getTabByOrderId($order_id);           
    	$order = M('order'.$select_year)->where(array('order_id'=>$order_id))->find();
    	if($order['pay_status'] == 1){
    		$vrorder = M('vr_order_code')->where(array('order_id'=>$order_id))->select();
    		$this->assign('vrorder',$vrorder);
    	}
    	$order_goods = M('order_goods'.$select_year)->where(array('order_id'=>$order_id))->find();
    	$order_goods['commission_money'] = $order_goods['commission']*$order_goods['goods_price']*$order_goods['goods_num']/100;
    	$order_goods['virtual_indate'] = M('goods')->where(array('goods_id'=>$order_goods['goods_id']))->getField('virtual_indate');
        $order['order_status_detail'] = C('ORDER_STATUS')[$order['order_status']];
    	$this->assign('order',$order);
    	$this->assign('order_goods',$order_goods);
    	$store = M('store')->where(array('store_id'=>$order['store_id']))->find();
    	$this->assign('store',$store);
    	return $this->fetch();
    }

    
    /*
     * 价钱修改
     */
    public function editprice($order_id){
        $orderLogic = new OrderLogic();
        $order = $orderLogic->getOrderInfo($order_id);
        $this->editable($order);
        if(IS_POST){
        	$admin_id = session('admin_id');
            if(empty($admin_id)){
                $this->error('非法操作');
                exit;
            }
            $update['discount'] = I('post.discount');
            $update['shipping_price'] = I('post.shipping_price');
			$update['order_amount'] = $order['goods_price'] + $update['shipping_price'] - $update['discount'] - $order['user_money'] - $order['integral_money'] - $order['coupon_price'];
            $row = M('order')->where(array('order_id'=>$order_id))->save($update);
            if(!$row){
                $this->success('没有更新数据',U('Admin/Order/editprice',array('order_id'=>$order_id)));
            }else{
                $this->success('操作成功',U('Admin/Order/detail',array('order_id'=>$order_id)));
            }
            exit;
        }
        $this->assign('order',$order);
        return $this->fetch();
    }

    
    /**
     * 订单取消付款
     */
    public function pay_cancel($order_id){
    	if(I('remark')){
    		$data = I('post.');
    		$note = array('退款到用户余额','已通过其他方式退款','不处理，误操作项');
    		if($data['refundType'] == 0 && $data['amount']>0){
    			accountLog($data['user_id'], $data['amount'], 0,  '退款到用户余额');
    		}
    		$orderLogic = new OrderLogic();
			$admin_id = session('admin_id'); // 当前操作的管理员
    		$d = $orderLogic->orderActionLog($data['order_id'],'取消付款',$data['remark'].':'.$note[$data['refundType']],$admin_id);
    		if($d){
    			exit("<script>window.parent.pay_callback(1);</script>");
    		}else{
    			exit("<script>window.parent.pay_callback(0);</script>");
    		}
    	}else{
    		$order = M('order')->where("order_id=$order_id")->find();
    		$this->assign('order',$order);
    		return $this->fetch();
    	}
    }

    /**
     * 订单打印
     * @param $order_id
     * @return mixed
     */
    public function order_print($order_id){
        $orderLogic = new OrderLogic();
        $order = $orderLogic->getOrderInfo($order_id);
        $order['province'] = getRegionName($order['province']);
        $order['city'] = getRegionName($order['city']);
        $order['district'] = getRegionName($order['district']);
        $order['full_address'] = $order['province'].' '.$order['city'].' '.$order['district'].' '. $order['address'];
        $orderGoods = $orderLogic->getOrderGoods($order_id);
        $shop = tpCache('shop_info');
        $this->assign('order',$order);
        $this->assign('shop',$shop);
        $this->assign('orderGoods',$orderGoods);
        return $this->fetch('print');
    }

    /**
     * 生成发货单
     */
    public function deliveryHandle(){
        $orderLogic = new OrderLogic();
		$data = I('post.');
		$res = $orderLogic->deliveryHandle($data);
		if($res){
			$this->success('操作成功',U('Admin/Order/delivery_info',array('order_id'=>$data['order_id'])));
		}else{
			$this->success('操作失败',U('Admin/Order/delivery_info',array('order_id'=>$data['order_id'])));
		}
    }

    
    public function delivery_info(){
    	$order_id = I('order_id');
    	$orderLogic = new OrderLogic();
    	$order = $orderLogic->getOrderInfo($order_id);
    	$orderGoods = $orderLogic->getOrderGoods($order_id);
    	$this->assign('order',$order);
    	$this->assign('orderGoods',$orderGoods);
		$delivery_record = Db::name('delivery_doc')->alias('d')->join('__SELLER__ s','s.seller_id = d.admin_id', 'LEFT')->where('d.order_id', $order_id)->select();
		$this->assign('delivery_record',$delivery_record);//发货记录
    	return $this->fetch();
    }
    
    /**
     * 发货单列表
     */
    public function delivery_list(){
        return $this->fetch();
    }
	
    /*
     * ajax 退货订单列表
     */
    public function ajax_return_list(){
        // 搜索条件        
        $order_sn =  trim(I('order_sn'));
        $order_by = I('order_by') ? I('order_by') : 'id';
        $sort_order = I('sort_order') ? I('sort_order') : 'desc';
        $status =  I('status','','trim');       
        
        $where = " 1 = 1 ";       
        $order_sn && $where.= " and order_sn like '%$order_sn%' ";
        ($status === '') ? 'do nothing' : ($where.= " and status = '$status' ");
          
        $count = M('return_goods')->where($where)->count();
        $Page  = new AjaxPage($count,13);
        $show = $Page->show();
        $list = M('return_goods')->where($where)->order("$order_by $sort_order")->limit("{$Page->firstRow},{$Page->listRows}")->select();        
        $goods_id_arr = get_arr_column($list, 'goods_id');
        if(!empty($goods_id_arr)){
            $goods_list = M('goods')->where("goods_id in (".implode(',', $goods_id_arr).")")->getField('goods_id,goods_name');
        }
        $store_list = M('store')->getField('store_id,store_name');        
        $this->assign('store_list',$store_list);
        $this->assign('goods_list',$goods_list);
        $this->assign('list',$list);
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('pager',$Page);// 赋值分页输出
        return $this->fetch();
    }
    
    /**
     * 删除某个退换货申请
     */
    public function return_del(){
        $id = I('get.id');
        M('return_goods')->where("id = $id")->delete(); 
        $this->success('成功删除!');
    }
    
    /**
     * 退换货操作
     */
    public function return_info()
    {
        $id = I('id');
        $return_goods = M('return_goods')->where("id= $id")->find();
        if($return_goods['imgs'])            
             $return_goods['imgs'] = explode(',', $return_goods['imgs']);
        $user = M('users')->where("user_id = {$return_goods[user_id]}")->find();
        $goods = M('goods')->where("goods_id = {$return_goods[goods_id]}")->find();
        $type_msg = array('退换','换货');
        $status_msg = array('未处理','处理中','已完成');
        if(IS_POST)
        {
            $data['type'] = I('type');
            $data['status'] = I('status');
            $data['refund_mark'] = I('refund_mark');                                    
            $note ="退换货:{$type_msg[$data['type']]}, 状态:{$status_msg[$data['status']]},处理备注：{$data['remark']}";
            $result = M('return_goods')->where("id= $id")->save($data);    
            if($result)
            {        
            	$type = empty($data['type']) ? 2 : 3;
            	$where = " order_id = ".$return_goods['order_id']." and goods_id=".$return_goods['goods_id'];
            	M('order_goods')->where($where)->save(array('is_send'=>$type));//更改商品状态        
                $orderLogic = new OrderLogic();
				$admin_id = session('admin_id'); // 当前操作的管理员
                $log = $orderLogic->orderActionLog($return_goods[order_id],'退换货',$note,$admin_id);
                $this->success('修改成功!');            
                exit;
            }  
        }        
        
        $this->assign('id',$id); // 用户
        $this->assign('user',$user); // 用户
        $this->assign('goods',$goods);// 商品
        $this->assign('return_goods',$return_goods);// 退换货               
        return $this->fetch();
    }
    
    /**
     * 管理员生成申请退货单
     */
    public function add_return_goods()
   {                
            $order_id = I('order_id'); 
            $goods_id = I('goods_id');
                
            $return_goods = M('return_goods')->where("order_id = $order_id and goods_id = $goods_id")->find();            
            if(!empty($return_goods))
            {
                $this->error('已经提交过退货申请!',U('Admin/Order/return_list'));
                exit;
            }
            $order = M('order')->where("order_id = $order_id")->find();
            
            $data['order_id'] = $order_id; 
            $data['order_sn'] = $order['order_sn']; 
            $data['goods_id'] = $goods_id; 
            $data['addtime'] = time(); 
            $data['user_id'] = $order[user_id];            
            $data['remark'] = '管理员申请退换货'; // 问题描述            
            M('return_goods')->add($data);            
            $this->success('申请成功,现在去处理退货',U('Admin/Order/return_list'));
            exit;
    }

    public function order_log(){
        $OrderActionModel = new OrderAction();
    	$begin = $this->begin;
    	$end = $this->end;
        
    	$condition = array();
    	if($begin && $end){
    		$condition['oa.log_time'] = array('between',"$begin,$end");
    	}
    	$admin_id = I('admin_id');
		if($admin_id >0 ){
			$condition['oa.action_user'] = $admin_id;
		}
    	$count = $OrderActionModel->alias('oa')->where($condition)->count();
    	$Page = new Page($count,20);    	 
    	$show = $Page->show();
    	$list = $OrderActionModel->where($condition)->alias('oa')
            ->field('oa.*,u.user_id,u.nickname')
            ->join('users u','oa.action_user = u.user_id','left')
            ->order('oa.action_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
    	$this->assign('list',$list);
    	$this->assign('page',$show);
		$this->assign('pager',$Page);
    	$admin = M('admin')->getField('admin_id,user_name');
    	$this->assign('admin',$admin);    	
    	return $this->fetch();
    }

    /**
     * 检测订单是否可以编辑
     * @param $order
     */
    private function editable($order){
        if($order['shipping_status'] != 0){
            $this->error('已发货订单不允许编辑');
            exit;
        }
        return;
    }

    public function export_order()
    {
    	//搜索条件
		$consignee = I('consignee');
		$order_sn =  I('order_sn');
		$order_status = I('order_status');
		$order_ids = I('order_ids');
        $prom_type = I('prom_type'); //订单类型
        $shipping_status = I('shipping_status');
        $pay_code = I('pay_code');
        $pay_status = I('pay_status');
        $found_id = input('found_id/d',0);
		$where = array();//搜索条件
        if ($found_id) {
            $Found = new TeamFound();
            $found = $Found->where('found_id',$found_id)->with('teamFollow')->find();
            $order_list = [];
            $order_list[] = $found['order_id'];
            if ($found['team_follow']) {
                foreach ($found['team_follow'] as $v){
                    $order_list[] = $v->order_id;
                }
            }
            $where['order_id'] = ['in',implode(',',$order_list)];
        }
		if($consignee){
			$where['consignee'] = ['like','%'.$consignee.'%'];
		}
		if($order_sn){
			$where['order_sn'] = $order_sn;
		}
		if($pay_status !=''){
            $where['pay_status'] = $pay_status;
        }
        if($pay_code !=''){
            $where['pay_code'] = $pay_code;
        }
        if($shipping_status !=''){
            $where['shipping_status'] = $shipping_status;
        }
		if($order_status && $order_status != -1){
			$where['order_status'] = $order_status;
		}
        $prom_type != '' ? $where['prom_type'] = $prom_type : $where['prom_type'] = ['lt',5];
		if($this->begin && $this->end){
			$where['add_time']=['Between',"$this->begin,$this->end"];
		}
		if($order_ids){
			$where['order_id'] = ['in', $order_ids];
		}

		$region	= Db::name('region')->cache(true)->getField('id,name');
		$orderList = Db::name('order')->field("*,FROM_UNIXTIME(add_time,'%Y-%m-%d') as create_time")->where($where)->order('order_id')->select();
    	$strTable ='<table width="500" border="1">';
    	$strTable .= '<tr>';
    	$strTable .= '<td style="text-align:center;font-size:12px;width:120px;">订单编号</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="100">日期</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">收货人</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">收货地址</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">电话</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">订单金额</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">应付金额</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">支付方式</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">支付状态</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">发货状态</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">商品数量</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">商品信息</td>';
    	$strTable .= '</tr>';
    	
    	foreach($orderList as $k=>$val){
    		$strTable .= '<tr>';
    		$strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['order_sn'].'</td>';
    		$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['create_time'].' </td>';
			$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['consignee'].'</td>';
			$strTable .= '<td style="text-align:left;font-size:12px;">'."{$region[$val['province']]},{$region[$val['city']]},{$region[$val['district']]},{$region[$val['twon']]}{$val['address']}".' </td>';
    		$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['mobile'].'</td>';
    		$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['goods_price'].'</td>';
    		$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['order_amount'].'</td>';
    		$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['pay_name'].'</td>';
    		$strTable .= '<td style="text-align:left;font-size:12px;">'.$this->pay_status[$val['pay_status']].'</td>';
    		$strTable .= '<td style="text-align:left;font-size:12px;">'.$this->shipping_status[$val['shipping_status']].'</td>';    
    		$orderGoods = D('order_goods')->where('order_id='.$val['order_id'])->select();
    		$strGoods="";
			$goods_num = 0;
    		foreach($orderGoods as $goods){
				$goods_num = $goods_num + $goods['goods_num'];
    			$strGoods .= "商品编号：".$goods['goods_sn']." 商品名称：".$goods['goods_name'];
    			if ($goods['spec_key_name'] != '') $strGoods .= " 规格：".$goods['spec_key_name'];
    			$strGoods .= "<br />";
    		}
    		unset($orderGoods);
    		$strTable .= '<td style="text-align:left;font-size:12px;">'.$goods_num.' </td>';
    		$strTable .= '<td style="text-align:left;font-size:12px;">'.$strGoods.' </td>';
    		$strTable .= '</tr>';
    	}
    	$strTable .='</table>';
    	unset($orderList);
    	downloadExcel($strTable,'order');
    	exit();
    }
    
    /**
     * 退货单列表
     */
    public function return_list(){
        return $this->fetch();
    }

    
    /**
     * 选择搜索商品
     */
    public function search_goods()
    {
    	$brandList =  M("brand")->select();
    	$categoryList =  M("goods_category")->select();
    	$this->assign('categoryList',$categoryList);
    	$this->assign('brandList',$brandList);   	
    	$where = ' is_on_sale = 1 ';//搜索条件
    	I('intro')  && $where = "$where and ".I('intro')." = 1";
    	if(I('cat_id')){
    		$this->assign('cat_id',I('cat_id'));    		
            $grandson_ids = getCatGrandson(I('cat_id')); 
            $where = " $where  and cat_id in(".  implode(',', $grandson_ids).") "; // 初始化搜索条件
                
    	}
        if(I('brand_id')){
            $this->assign('brand_id',I('brand_id'));
            $where = "$where and brand_id = ".I('brand_id');
        }
    	if(!empty($_REQUEST['keywords']))
    	{
    		$this->assign('keywords',I('keywords'));
    		$where = "$where and (goods_name like '%".I('keywords')."%' or keywords like '%".I('keywords')."%')" ;
    	}  	
    	$goodsList = M('goods')->where($where)->order('goods_id DESC')->limit(10)->select();
                
        foreach($goodsList as $key => $val)
        {
            $spec_goods = M('spec_goods_price')->where("goods_id = {$val['goods_id']}")->select();
            $goodsList[$key]['spec_goods'] = $spec_goods;            
        }
    	$this->assign('goodsList',$goodsList);
    	return $this->fetch();
    }
    
    public function ajaxOrderNotice(){
        $order_amount = M('order')->where(array('order_status'=>0))->count();
        echo $order_amount;
    }

    /**
     * 删除订单日志
     */
    public function delOrderLogo(){
        $ids = I('ids');
        empty($ids) &&  $this->ajaxReturn(['status' => -1,'msg' =>"非法操作！",'url'  =>'']);
        $order_ids = rtrim($ids,",");
        $res = Db::name('order_action')->whereIn('order_id',$order_ids)->delete();
        if($res !== false){
            $this->ajaxReturn(['status' => 1,'msg' =>"删除成功！",'url'  =>'']);
        }else{
            $this->ajaxReturn(['status' => -1,'msg' =>"删除失败",'url'  =>'']);
        }
    }
}
