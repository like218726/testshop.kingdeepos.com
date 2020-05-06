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
 * Date: 2015-09-09
 */

namespace app\admin\logic;
use think\Model;
use think\Db;
use app\common\logic\OrderLogic as adminOrderLogic;
/**
 * 分类逻辑定义
 * Class CatsLogic
 * @package Home\Logic
 */
class RefundLogic extends Model
{

    /**
     * 订单商品售后退款
     * @param $rec_id
     * @param int $refund_type  //退款类型，0原路返回，1退到用户余额
     * @param int $refund_type
     */
    function updateRefundGoods($rec_id,$refund_type=0){
        $order_goods = M('order_goods')->where(array('rec_id'=>$rec_id))->find();
        $return_goods = M('return_goods')->where(array('rec_id'=>$rec_id))->find();
        $updata = array('refund_type'=>$refund_type,'refund_time'=>time(),'status'=>5);
        //使用积分或者余额抵扣部分原路退还
        if($return_goods['refund_deposit']>0 || $return_goods['refund_integral']>0){
            accountLog($return_goods['user_id'],$return_goods['refund_deposit'],$return_goods['refund_integral'],'用户申请商品退款',0,$return_goods['order_id'],$return_goods['order_sn']);
        }
        //在线支付金额退到余额去
        if($refund_type==1 && $return_goods['refund_money']>0){
            accountLog($return_goods['user_id'],$return_goods['refund_money'],0,'用户申请商品退款',0,$return_goods['order_id'],$return_goods['order_sn']);
        }
     
        M('return_goods')->where(array('rec_id'=>$return_goods['rec_id']))->save($updata);//更新退款申请状态
        M('order_goods')->where(array('rec_id'=>$return_goods['rec_id']))->save(array('is_send'=>3));//修改订单商品状态
        
        //查找该笔订单下是否有其他商品需要申请售后并且已经处理完成, 如果都已经申请售后则修改订单状态为关闭
        $buyGoodsCount = M('order_goods')->where(array('order_id'=>$order_goods['order_id']))->count('goods_id');
        $returnGoodsCount = M('return_goods')->where(array('order_id'=>$return_goods['order_id'] , 'status'=>5))->count('goods_id');
        $count = $order_goods['order_id'].', '.$return_goods['rec_id'].' , '.$buyGoodsCount.' , '.$returnGoodsCount;
        if($buyGoodsCount == $returnGoodsCount){//该笔订单所下所有商品已经处理退款完成, 修改订单状态为已关闭
            M('Order')->where(['order_id'=>$return_goods['order_id']])->save(['order_status'=>5]);
        }
        if($return_goods['is_receive'] == 1){
            //赠送积分追回
            if($order_goods['give_integral']>0){
                $user = get_user_info($return_goods['user_id']);
                if($order_goods['give_integral']<=$user['pay_points']){ //如果赠送的积分已经被用户用完了也没去追回了???
                    accountLog($return_goods['user_id'],0,-$order_goods['give_integral']*$order_goods['goods_num'],'退货积分追回',0,$return_goods['order_id'],$return_goods['order_sn']);
                }else{//积分不够扣, 从退款金额里面扣
                    $point_to_money = $order_goods['give_integral']/tpCache('shopping.point_rate');  //按比例将积分转换成金额
                    $return_goods['refund_money'] = $return_goods['refund_money'] - $point_to_money; //这时候的值可能是负数
                }
            }
            //追回订单商品赠送的优惠券
            $coupon_info = M('coupon_list')->where(array('uid'=>$return_goods['user_id'],'get_order_id'=>$return_goods['order_id']))->find();
            if(!empty($coupon_info)){
                if($coupon_info['status'] == 1) { //如果优惠券被使用,那么从退款里扣
                    $coupon = M('coupon')->where(array('id' => $coupon_info['cid']))->find();
                    if ($return_goods['refund_money'] > $coupon['money']) {
                        //退款金额大于优惠券金额直接扣
                        $return_goods['refund_money'] = $return_goods['refund_money'] - $coupon['money'];//更新实际退款金额
                        M('return_goods')->where(['id' => $return_goods['id']])->save(['refund_money' => $return_goods['refund_money']]);
                    }else{
                        //否则从退还余额里扣
                        $return_goods['refund_deposit'] = $return_goods['refund_deposit'] - $coupon['money'];//更新实际退还余额
                        M('return_goods')->where(['id' => $return_goods['id']])->save(['refund_deposit' => $return_goods['refund_deposit']]);
                    }
                }else {
                    M('coupon_list')->where(array('id' => $coupon_info['id']))->delete();
                    M('coupon')->where(array('id' => $coupon_info['cid']))->setDec('send_num');//优惠券追回
                }
            }
        }
    
        //退还使用的优惠券
        $order_goods_count =  M('order_goods')->where(array('order_id'=>$return_goods['order_id']))->sum('goods_num');
        $return_goods_count = M('return_goods')->where(array('order_id'=>$return_goods['order_id']))->sum('goods_num');
        if($order_goods_count == $return_goods_count){
            $coupon_list = M('coupon_list')->where(['uid'=>$return_goods['user_id'],'order_id'=>$return_goods['order_id']])->find();
            if(!empty($coupon_list)){
                $update_coupon_data = ['status'=>0,'use_time'=>0,'order_id'=>0];
                M('coupon_list')->where(['id'=>$coupon_list['id'],'status'=>1])->save($update_coupon_data);//符合条件的，优惠券就退给他
            }
        }
        $expense_data = [
            'money'=>$return_goods['refund_money']+$return_goods['refund_deposit'],
            'integral'=>$return_goods['refund_integral'],
            'log_type_id'=>$return_goods['rec_id'],
            'type'=>3,
            'user_id'=>$return_goods['user_id'],
            'store_id'=>$return_goods['store_id']
        ];
        $this->expenseLog($expense_data);//退款记录日志
    }
    
    
    /**
     * 取消订单退还余额，优惠券等
     * @param $order
     * @return bool
     */
    function updateRefundOrder($order,$type=0){

        //要么退到余额，要么下面退
        if($order['order_amount']>0 && $type == 1){
            //改方法已经是退回余额, 不需要判断原路退回还是退还到余额 @add by wangqh
            accountLog($order['user_id'],$order['order_amount'],$order['integral'],'用户取消订单退款',0,$order['order_id'],$order['order_sn']);
        }else{
            //使用积分或者余额抵扣部分一一退还
            if ($order['user_money'] > 0 || $order['integral'] > 0) {
                $update_money_res=accountLog($order['user_id'], $order['user_money'], $order['integral'], '用户申请订单退款', 0, $order['order_id'], $order['order_sn']);
                if(!$update_money_res){
                    return false;
                }
            }
        }
    
        //符合条件的，该笔订单使用的优惠券就退还
        $coupon_list = M('coupon_list')->where(['uid'=>$order['user_id'],'order_id'=>$order['order_id']])->find();
        if(!empty($coupon_list)){
            $update_coupon_data = ['status'=>0,'use_time'=>0,'order_id'=>0];
            M('coupon_list')->where(['id'=>$coupon_list['id'],'status'=>1])->save($update_coupon_data);
        }
        M('order')->where(array('order_id'=>$order['order_id']))->save(array('pay_status'=>3)); //更改订单状态
        $orderLogic = new adminOrderLogic();
        $orderLogic->alterReturnGoodsInventory($order);//取消订单后改变库存
        $expense_data = [
            'money'         => $order['user_money'],
            'integral'      => $order['integral'],
            'log_type_id'   => $order['order_id'],
            'type'          => 2,
            'user_id'       => $order['user_id'],
            'store_id'      => $order['store_id'],
        ];
        $this->expenseLog($expense_data);//平台支出记录
        $message_logic = new \app\common\logic\MessageLogisticsLogic();
        $message_logic->sendRefundNotice($order, $order['order_amount']);
        return true;
    }
    
    	private $allSplitOrderId;
	/**
	 * 判断拆分订单全部退货了没(包括没拆单的)
	 */
    function isAllSplitGoodsRefund($order){
		$this->allSplitOrderId = [];
		//先找此订单根父订单
		$firstOrder = $this->getRootOrder($order);
		array_push($this->allSplitOrderId, $firstOrder['order_id']);
		//寻找此根订单的子孙订单的order_id
		$this->getChildrenOrderId($firstOrder);
		
		$orderGoodsCount = Db::name('order_goods')->where('order_id', 'in', $this->allSplitOrderId)->sum('goods_num');
		$returnGoodsCount = Db::name('return_goods')->where('order_id', 'in', $this->allSplitOrderId)->sum('goods_num');
		if ($orderGoodsCount == $returnGoodsCount) {
			return $firstOrder['order_id'];
		} else {
			return false;
		}
    }
	
	/**
	 * 获取子订单id
	 */
    function getChildrenOrderId($order){
		$childrenOrder = Db::name('order')->where(['parent_sn' => $order['order_sn'], 'store_id' => $order['store_id']])->select();
		if ($childrenOrder) {
			foreach ($childrenOrder as $val) {
				array_push($this->allSplitOrderId, $val['order_id']);
				$this->getChildrenOrderId($val);
			}
		} else {
			return;
		}
    }
	
	/**
	 * 判断拆分订单是否全部都退款了(包括没拆单的)
	 */
    function isAllSplitOrderRefund($order){
		//先找此订单根父订单
		$firstOrder = $this->getRootOrder($order);
		$this->allSplitOrderId = [];
		array_push($this->allSplitOrderId, $firstOrder['order_id']);
		//寻找此根订单的子孙订单的order_id
		$this->getChildrenOrderId($firstOrder);
		
		$orderList = Db::name('order')->where('order_id', 'in', $this->allSplitOrderId)->select();
		foreach ($orderList as $val) {
			if ($val['pay_status'] != 3) return false;
		}
		return $firstOrder['order_id'];
    }
	
	/**
	 * 先找此订单根父订单(针对拆单)
	 */
    function getRootOrder($order){
		$firstOrder = $order;
		while (1) {
			if ($firstOrder['parent_sn']) {
				$firstOrder = Db::name('order')->where(['order_sn' => $firstOrder['parent_sn'], 'store_id' => $firstOrder['store_id']])->find();
			} else {
				break;
			}
		}
		return $firstOrder;
    }
    
    /**
     * 平台支出日志记录
     * @param $data
     */
    function expenseLog($data){
        $data['addtime'] = time();
        $data['admin_id'] = session('admin_id');
        M('expense_log')->add($data);
    }
    
}