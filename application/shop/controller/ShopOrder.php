<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * Author: 当燃
 * Date: 2015-09-09
 */
namespace app\shop\controller;
use app\common\model\Order;
use app\common\model\Shop;
use app\common\util\TpshopException;
use think\Page;
use think\Db;

class ShopOrder extends Base {

    public function _initialize() {
        parent::_initialize();
    }

    /**
     * 订单列表
     */
    public function index(){
        /*code_16门店管理模块代码*/
        $shop_id = input('shop_id/d');
        $is_write_off = input('is_write_off');
        $add_time_start = input('add_time_start/s', date('Y-m-d H:i:s', strtotime("-3 month")));
        $add_time_end = input('add_time_end/s', date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' 23:59:59')));
        $take_time_start = input('take_time_start/s');
        $take_time_end = input('take_time_end/s');
        $statistic = input('statistic/d');//是否是从数据统计页进来的
        $keywords = input('keywords/s');
        $where = [];
        if($shop_id){
            $where['s.shop_id'] = $shop_id;
        }
        if($is_write_off == '0' || $is_write_off){
            $where['s.is_write_off'] = $is_write_off;
            $where['o.order_status'] = 1;
        }
        if($add_time_start || $add_time_end){
            $add_time_start = str_replace('+',' ',$add_time_start);
            $add_time_end = str_replace('+',' ',$add_time_end);
            $where['o.add_time'] = ['between',[strtotime($add_time_start), strtotime($add_time_end)]];
        }
        if($keywords){
            $where['s.order_sn'] = $keywords;
        }
        if ($take_time_start || $take_time_end) {
            $where['s.take_time'] = ['between', [$take_time_start, $take_time_end]];
            unset($where['o.add_time']);
        }
        if ($statistic) {
            $where['o.pay_status'] = 1;
            $where['o.order_status'] = ['in', [1, 2, 4]];
        }
        $ShopOrder = new \app\common\model\ShopOrder();
        $count = $ShopOrder->alias('s')->join('__ORDER__ o', 's.order_id = o.order_id')->where($where)->count('s.shop_order_id');
        $Page = new Page($count, 10);
        $list = $ShopOrder->alias('s')->join('__ORDER__ o', 's.order_id = o.order_id')->where($where)->limit($Page->firstRow.','.$Page->listRows)->order(['o.add_time'=>'desc'])->select();
        $Shop = new Shop();
        $shop_list = $Shop->where(['deleted'=>0])->cache(true)->select();
        $this->assign('shop_list', $shop_list);
        $this->assign('page', $Page);
        $this->assign('list', $list);
        $this->assign('add_time_start', $add_time_start);
        $this->assign('add_time_end', $add_time_end);
        return $this->fetch();
        /*code_16门店管理模块代码*/
    }

    /**
     * 核销页
     */
    public function off()
    {
        $shop_order_wait_off_num = db('shop_order')->alias('s')
            ->join('__ORDER__ o','o.order_id = s.order_id')->where('order_status','in','0,1')->where(['s.is_write_off' => 0,'o.shop_id'=>session('shop_id'),'pay_status'=>1])->count('s.shop_order_id');
        $this->assign('shop_order_wait_off_num', $shop_order_wait_off_num);
        return $this->fetch();
    }
    /**
     * ajax 获取自提订单信息
     * order_id
     */
    public function getOrderGoodsInfo()
    {
        $order_id = input("order_id/d",0);
        $Order = new Order();
        $order = $Order->with("shop,shop_order")->where(['order_id'=>$order_id])->find();
        $order_info = $order->append(['delivery_method','shipping_status_desc'])->toArray();
        $this->ajaxReturn($order_info);
    }
    /**
     * 核销
     */
    public function writeOff()
    {
        $bar_code = input('bar_code', 0);
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
        $shop_id = session('shop_id');
        $where = ['o.pay_status' => 1, 'o.order_status' => ['in', [1, 2, 4]]];
        $where['o.shop_id'] = $shop_id;
        if($take_time_start || $take_time_end){
            $where['s.take_time'] = ['between', [date('Y-m-d H:i:s',$take_time_start), date('Y-m-d H:i:s',$take_time_end)]];
        }
        $Shop = new Shop();
        $ShopOrder = new \app\common\model\ShopOrder();
        $now_date = date('Y-m-d');
        $shop_list = $Shop->where(['deleted'=>0])->cache(true)->select();
        $shop_order_today_count = $ShopOrder->alias('s')->join('__ORDER__ o','s.order_id = o.order_id')
            ->where(["DATE_FORMAT(s.take_time, '%Y-%m-%d')"=>['eq', $now_date], 'o.pay_status' => 1, 'o.order_status' => ['in', [1, 2, 4]],'o.shop_id'=>$shop_id])->count('s.shop_order_id');//今日销售总额
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
        while ($start_time <= $end_time) {
            $date_current = date('Y-m-d', $start_time);
            foreach ($shop_order_sum_list as $shop_order_sum) {
                if ($date_current == $shop_order_sum['date']) {
                    $order_count_arr[] = $shop_order_sum['order_count'];
                    break;
                }
            }
            $order_count_arr[] = 0;
            $date_arr[] = $date_current;//得到dataarr的日期数组。
            $start_time = $start_time + 86400;
        }
        $table['order_count_list'] = $order_count_arr;
        $table['date_list'] = $date_arr;
        $this->assign('table', json_encode($table));
        return $this->fetch('order/statistic');
    }

}
