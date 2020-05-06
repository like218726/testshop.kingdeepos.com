<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采有最新thinkphp5助手函数特性实现函数简写方式M D U等,也可db::name方式,可双向兼容
 * ============================================================================
 * Author: dyr
 * Date: 2017-12-04
 */

namespace app\seller\logic;

use app\common\util\TpshopException;
use app\common\model\Order;
use app\common\model\OrderGoods;
use think\Model;
use think\Db;
/**
 * 订单类
 * Class CatsLogic
 * @package Home\Logic
 */
class SellerOrder
{
    private $order = [];

    public function __construct($order_id)
    {
        $order = Order::get($order_id);
        if (empty($order)) {
            throw new TpshopException('修改订单价格', 0, ['status'=>-5,'msg'=>"非法操作！！",'result'=>'']);
        }
        $this->order = $order;
    }

    /**
     * 获取订单详情
     */
    public function getOrderInfo(){
        return $this->order;
    }

    /**
     * 修改订单价格
     * @param $data
     * @throws TpshopException
     */
    public function updataOrderPrice($data){
        $order = $this->order;
        if ($order['shipping_status'] != 0 && $order['pay_status'] > 0 && $order['order_status'] < 2) {
            throw new TpshopException('修改订单价格', 0, ['status'=>-5,'msg'=>"订单状态不允许修改！！",'result'=>'']);
        }
        
        //检测修改的价格是否低于需要分销和平台提成的金额
        $orderGoods = M('order_goods')->where(['order_id'=>$order['order_id']])->select();
        $alldistribut = $allcommission =$allsettlement= 0;
        foreach ($orderGoods as $k =>$val)
        {
            //此商品金额初始值
            $settlement =  $val['member_goods_price'] * $val['goods_num']; 
            //减去购买该商品赠送的积分金额
            if ($val['give_integral'] > 0 && $val['is_send']<3) {
            
                $settlement = round(($settlement - $val['goods_num'] * $val['give_integral'] ),2);
            }
            if ($val['distribut'] > 0) {
                //减去分销分成金额 若价格调整分销的金额不变 订单分销分成
                $distribut = round(($val['distribut'] * $val['goods_num']),2);
            }
            //去掉商品优惠的价格  减去优惠券抵扣金额和优惠折扣
            if( $order['order_prom_amount'] > 0 || $order['coupon_price'] > 0){
                $prom_and_coupon = round(($order['order_prom_amount'] + $order['coupon_price']),2);
                $settlement = round(($settlement - $prom_and_coupon),2);
            }
            
            $alldistribut += round(($distribut +$settlement * $val['commission']/100),2);
        }
        $alldistribut +=$data['shipping_price'];
        $shipping_price = $data['shipping_price']-$order['shipping_price'];  //调整后物流价格跟现在可能存在差价
        $update_price = [
            'shipping_price'    => $data['shipping_price'],
            'order_amount'      => $order['order_amount'] + $shipping_price + $data['discount']-$order['discount'],
            'total_amount'      => $order['total_amount'] + $shipping_price + $data['discount']-$order['discount'],
            'discount'          => $data['discount'],
        ];
        $lastMoney = round(($update_price['order_amount']-$alldistribut+$shipping_price),2);//需要支付的金额

        if(  $update_price['order_amount'] < 0 || $lastMoney < 0){
            throw new TpshopException('修改订单价格', -1, ['status'=>-4,'msg'=>"价格调整后必须大于{$lastMoney}元！！",'result'=>[]]);
        }
        $row = Db::name('order')->where(['order_id' => $order['order_id'], 'store_id' => STORE_ID])->update($update_price);
        if (!$row) {
            throw new TpshopException('修改订单价格', 0, ['status'=>-5,'msg'=>"没有更新数据！！",'result'=>'']);
        }
        if ($data['discount']){  //修改订单价才用改订单商品表
            $this->updataOrderGoodsPrice();
        }
    }

    /**
     * 修改订单商品价格
     * @throws TpshopException
     */
    public function updataOrderGoodsPrice(){
        $old_order = $this->order;  //修改参数前订单信息
        $order_id =$old_order['order_id'];
        $orderGoodsObj = OrderGoods::all(['order_id'=>$order_id]);
        $nowOrderObj = Order::get(['order_id'=>$order_id]);
        $old_order_goods_price =$old_order['total_amount']-$old_order['shipping_price'];
        $now_order_goods_price =$nowOrderObj['total_amount']-$nowOrderObj['shipping_price'];
        foreach ($orderGoodsObj as $key => $og){  //根据比例来计算价格
            $ratio = round(($og['final_price']*$og['goods_num'])/$old_order_goods_price,8); //算出原来商品价占订单总价比例，（实际购买价*数量）/订单商品总价
            $final_price = ($now_order_goods_price*$ratio)/$og['goods_num']; //现在商品实际购买价格
            Db::name('order_goods')->where(['rec_id'=>$og['rec_id']])->update(['final_price'=>$final_price]);
        }
        
    }
}