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
 * Author: zj
 * Date: 2018/9/26
 */

namespace app\seller\controller;


use app\common\util\TpshopException;
use think\AjaxPage;

class ShopOrder extends Base
{
    public function index()
    {
        //获取默认平台店铺id
        $default_store_id = db('store')->where(['default_store'=>1,'store_type'=>0,'is_own_shop'=>1])->value('store_id');

        $shop_order_wait_off_num = db('shop_order')->alias('s')
            ->join('__ORDER__ o','o.order_id = s.order_id')->where('o.order_status','in','0,1')->where(['s.is_write_off' => 0,'o.store_id'=>[['eq',STORE_ID],['eq',$default_store_id],'or'],'o.order_store_id'=>STORE_ID,'o.pay_status'=>1])->count('s.shop_order_id');
        $this->assign('shop_order_wait_off_num', $shop_order_wait_off_num);
        $shop_list = db('shop')->field('shop_id,shop_name')->where(['deleted'=>0,'store_id'=>STORE_ID])->cache(true)->select();
        $this->assign('shop_list', $shop_list);
        $this->assign('default_store_id', $default_store_id);
        return $this->fetch();
    }

    /*
     *Ajax首页
     */
    public function ajaxindex()
    {
        $is_write_off = input('is_write_off');
        $select_year = $this->select_year;
        $add_time_start = input('start_time/s', date('Y-m-d H:i:s', strtotime("-3 month")));
        $add_time_end = input('end_time/s', date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' 23:59:59')));
        $take_time_start = input('take_time_start/s');
        $take_time_end = input('take_time_end/s');
        $statistic = input('statistic/d',0);//是否是从数据统计页进来的
        $order_sn = input('order_sn/s');
        $order_status = input('order_status/d');
        $shop_id = input('shop_id/d');

        // 搜索条件 STORE_ID
        $where = [];
        $where['o.store_id'] = STORE_ID;
        if($is_write_off == '0' || $is_write_off){
            $where['s.is_write_off'] = $is_write_off;
            $where['o.order_status'] = 1;
        }

        if ($shop_id) {
            $where['s.shop_id'] = $shop_id;
        }

        if($add_time_start || $add_time_end){
            $add_time_start = str_replace('+',' ',$add_time_start);
            $add_time_end = str_replace('+',' ',$add_time_end);
            $where['o.add_time'] = ['between',[strtotime($add_time_start), strtotime($add_time_end)]];
        }
        if($order_sn){
            $where['s.order_sn'] = $order_sn;
        }
        if ($take_time_start || $take_time_end) {
            $where['s.take_time'] = ['between', [$take_time_start, $take_time_end]];
            unset($where['o.add_time']);
        }
        if ($order_status == 2) {
            $where['o.order_status'] = array('between','0,1');
            $where['o.pay_status'] = 1;
        }
        if ($statistic) {
            $where['o.pay_status'] = 1;
            $where['o.order_status'] = ['in', [1, 2, 4]];
        }
        $conditionOr = $where;
        $conditionOr['o.store_id'] = ['neq',STORE_ID];
        //获取默认平台店铺id
        $default_store_id = db('store')->where(['default_store'=>1,'store_type'=>0,'is_own_shop'=>1])->value('store_id');
        if($default_store_id == STORE_ID){
            //用户购买平台商品自提，在商家显示自提订单，在平台显示快递订单，快递到商家店给用户自提
            $conditionOr['o.store_id'] = STORE_ID;
        }
        $ShopOrder = new \app\common\model\ShopOrder();
        $count = $ShopOrder->alias('s')->join('__ORDER__ o', 's.order_id = o.order_id')->where($where)->count('s.shop_order_id');
        $Page = new AjaxPage($count, 20);
        $show = $Page->show();
        //获取订单列表
        $orderList = $ShopOrder->alias('s')->join('__ORDER__ o', 's.order_id = o.order_id')->where($where)->limit($Page->firstRow.','.$Page->listRows)->order(['o.add_time'=>'desc'])->select();

        //获取每个订单的商品列表
        $order_id_arr = get_arr_column($orderList, 'order_id');
        if (!empty($order_id_arr)) ;
        if ($order_id_arr) {
            $goods_list = db('order_goods' . $select_year)->where("order_id", "in", implode(',', $order_id_arr))->select();
            $goods_arr = array();
            foreach ($goods_list as $v) {
                $goods_arr[$v['order_id']][] = $v;
            }
            $this->assign('goodsArr', $goods_arr);
        }
        $pay_status = [0=>'待支付',1=>'已支付',2=>'部分支付',3=>'已退款',4=>'拒绝退款'];
        $this->assign('pay_status',$pay_status);
        $this->assign('orderList', $orderList);
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('pager', $Page);
        $this->assign('default_store_id', $default_store_id);
        return $this->fetch();
    }

    /**
     * ajax 获取自提订单信息
     * order_id
     */
    public function getOrderGoodsInfo()
    {
        $order_id = input("order_id/d",0);
        $Order = new \app\common\model\Order();
        $order = $Order->with("shop,shop_order")->where(['order_id'=>$order_id])->find();
        $order_info = $order->append(['delivery_method','shipping_status_desc'])->toArray();
        $this->ajaxReturn($order_info);
    }

    /**
     * 核销
     */
    public function writeOff()
    {
        $bar_code = input('bar_code/d', 0);
        if (!$bar_code) {
            $this->ajaxReturn(['status' => 0, 'msg'=>'请输入核验码']);
        }
        $ShopOrderLogic = new \app\common\logic\ShopOrder();
        $ShopOrderLogic->setShopOrderByBarCode($bar_code);
        try {
            $ShopOrderLogic->writeOff();
            $this->ajaxReturn(['status' => 1, 'msg'=>'核销成功']);
        } catch (TpshopException $t) {
            $error = $t->getErrorArr();
            $this->ajaxReturn($error);
        }
    }

    /**
     * 数据统计
     */
    public function statistic()
    {

        if (input('take_time_start')) {
            $take_time_start = strtotime(input('take_time_start'));
            $take_time_end =  strtotime(input('take_time_end'));
        } else {
            $take_time_start = strtotime("-3 month");
            $take_time_end = strtotime('+1 days');
        }
        $where = ['o.pay_status' => 1, 'o.order_status' => ['in', [1, 2, 4]]];
        $where['o.store_id'] = STORE_ID;
        if($take_time_start || $take_time_end){
            $where['s.take_time'] = ['between', [date('Y-m-d H:i:s',$take_time_start), date('Y-m-d H:i:s',$take_time_end)]];
        }
        $store_id = STORE_ID;
        $Shop = new \app\common\model\Shop();
        $ShopOrder = new \app\common\model\ShopOrder();
        $now_date = date('Y-m-d');
        $shop_list = $Shop->where(['deleted'=>0])->cache(true)->select();
        $shop_order_today_count = $ShopOrder->alias('s')->join('__ORDER__ o','s.order_id = o.order_id')
            ->where(["DATE_FORMAT(s.take_time, '%Y-%m-%d')"=>['eq', $now_date], 'o.pay_status' => 1, 'o.order_status' => ['in', [1, 2, 4]],'o.store_id'=>$store_id])->count('s.shop_order_id');//今日销售总额
        $shop_order_sum_list = $ShopOrder->alias('s')->join('__ORDER__ o','s.order_id = o.order_id')
            ->field("DATE_FORMAT(s.take_time, '%Y-%m-%d' ) as date,COUNT(s.shop_order_id) as order_count")->where($where)->group("date")->select();

        $this->assign('shop_list', $shop_list);
        $this->assign('take_time_start', $take_time_start);
        $this->assign('take_time_end', $take_time_end);
        $this->assign('shop_order_today_count', $shop_order_today_count);
        $this->assign('shop_order_sum_list', $shop_order_sum_list);
        //开始拼装数组
        $start_date = date("Y-m-d", $take_time_start);
        $end_date = date("Y-m-d", $take_time_end);
        $start_time = strtotime($start_date);
        $end_time = strtotime($end_date);
        $date_arr = [];
        $order_count_arr = [];

        $label = [];
        while ($start_time <= $end_time) {
            $date_current = date('Y-m-d', $start_time);
            $label[$date_current]['count'] = false;//只做标识用
            foreach ($shop_order_sum_list as $shop_order_sum) {
                if ($date_current == $shop_order_sum['date']) {
                    $order_count_arr[] = $shop_order_sum['order_count'];
                    $label[$date_current]['count'] = true;
                    break;
                }
            }
            $date_arr[] = $date_current;//得到dataarr的日期数组。
            if (!$label[$date_current]['count']) {
                $order_count_arr[] = 0;
            }
            $start_time = $start_time + 24*3600;
        }
        $table['order_count_list'] = $order_count_arr;
        $table['date_list'] = $date_arr;
        $this->assign('table', json_encode($table));
        return $this->fetch();
    }

    //二维数组去掉重复值
    public function a_array_unique($array){
        $out = array();

        foreach ($array as $key=>$value) {
            if (!in_array($value, $out)){
                $out[$key] = $value;
            }
        }

        $out = array_values($out);
        return $out;
    }
}