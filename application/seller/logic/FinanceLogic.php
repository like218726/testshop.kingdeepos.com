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
 * Date: 2015-09-14
 */

namespace app\seller\logic;

use think\Model;
use think\Db;

class FinanceLogic extends Model
{
    /**
     * 获取商品退换货信息
     * @param $goods
     * @return array
     */
    public function getGoodsReturnInfo($goods)
    {
        foreach($goods as $key => $v){
            $order_goods = Db::name('order_goods')->field("SUM(final_price) as sales_price, SUM(goods_num) as sales_amount")
                ->where(['goods_id'=>$v['goods_id'],'deleted'=>0,'is_send'=>['lt',3]])->find();
            $refund = Db::name('return_goods')->field("SUM('refund_deposit+refund_money') as refund_price,SUM('goods_num')as refund_num")
                ->where(['goods_id'=>$v['goods_id'],'type'=>['lt',2],'status'=>['notIn','-2,-1,4']])->find();
            $maintenance = Db::name('return_goods')->where(['goods_id'=>$v['goods_id'],'type'=>['gt',1],'status'=>['eq','-2,-1']])->sum('goods_num');
            $v['sales_amount']  =$order_goods['sales_amount'];
            $v['sales_price']=$order_goods['sales_price'];
            $v['refund_num']  =$refund['refund_num'];
            $v['refund_price']=$refund['refund_price'];
            $v['refund_goods_ratio']= $order_goods['sales_amount'] > 0 ? $refund['refund_num']/$order_goods['sales_amount']*100 : 0;
            $v['maintenance_ratio']= $order_goods['sales_amount'] > 0 ? $maintenance['goods_num']/$order_goods['sales_amount']*100 : 0;
            $data[]=$v;
        }
        return $data;
    }
}