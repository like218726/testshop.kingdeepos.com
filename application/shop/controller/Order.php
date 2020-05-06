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
namespace app\shop\controller;

use app\common\model\OrderAction;
use app\shop\logic\OrderLogic;
use think\AjaxPage;
use think\Db;
use think\Page;

class Order extends Base
{
    public $order_status;
    public $shipping_status;
    public $pay_status;

    /*
     * 初始化操作
     */
    public function _initialize()
    {
        parent::_initialize();
        C('TOKEN_ON', false); // 关闭表单令牌验证
        // 订单 支付 发货状态
        $this->order_status = C('ORDER_STATUS');
        $this->pay_status = C('PAY_STATUS');
        $this->shipping_status = C('SHIPPING_STATUS');
        $this->assign('order_status', $this->order_status);
        $this->assign('pay_status', $this->pay_status);
        $this->assign('shipping_status', $this->shipping_status);
    }

    /**
     * 订单首页
     */
    public function index()
    {
        $begin = date('Y-m-d', strtotime("-3 month"));//30天前
        $end = date('Y-m-d', strtotime('+1 days'));
        $shop_order_wait_off_num = Db::name('shop_order')
            ->alias('s')
            ->join('__ORDER__ o','o.order_id = s.order_id')
            ->where(['s.shop_id'=>session('shop_id'),'s.is_write_off' => 0,'o.order_status'=>'1'])
            ->count('s.shop_order_id');
        $this->assign('shop_order_wait_off_num', $shop_order_wait_off_num);
        $this->assign('timegap', $begin . '-' . $end);
        $this->assign('begin', date('Y-m-d', strtotime("-3 month")+86400));
        $this->assign('end', date('Y-m-d', strtotime('+1 days')));
        return $this->fetch();
    }

    /**
     * Ajax首页
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

        // 搜索条件 STORE_ID
        $where = [];
        $where['s.shop_id'] = session('shop_id');
        if($is_write_off == '0' || $is_write_off){
            $where['s.is_write_off'] = $is_write_off;
            $where['o.order_status'] = 1;
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
        if ($statistic) {
            $where['o.pay_status'] = 1;
            $where['o.order_status'] = ['in', [1, 2, 4]];
        }
        if ($order_status == 2) {
            $where['o.order_status'] = array('between','0,1');
            $where['o.pay_status'] = 1;
        }

        $ShopOrder = new \app\common\model\ShopOrder();
        $count = $ShopOrder->alias('s')->join('__ORDER__ o', 's.order_id = o.order_id')->where($where)->count('s.shop_order_id');
        $Page = new AjaxPage($count, 10);
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
        return $this->fetch();
    }

    /*
     * ajax 发货订单列表
    */
    public function ajaxdelivery()
    {
        $begin = strtotime(I('add_time_begin'));
        $end = strtotime(I('add_time_end'));
        $select_year = getTabByTime(I('add_time_begin')); // 表后缀
        $condition = array('store_id' => STORE_ID ,'deleted' =>0);
        if ($begin && $end) {
            $condition['add_time'] = array('between', "$begin,$end");
        }
        I('consignee') ? $condition['consignee'] = trim(I('consignee')) : false;
        I('order_sn') != '' ? $condition['order_sn'] = trim(I('order_sn')) : false;
        $shipping_status = I('shipping_status');
        $condition['shipping_status'] = empty($shipping_status) ? 0 : $shipping_status;
        $condition['order_status'] = array('in', '1,2,4');
        $condition['prom_type'] = array('in','1,2,3,4,6');
        $count = M('order'.$select_year)->where($condition)->count();
        $Page = new AjaxPage($count, 10);
        $show = $Page->show();
        $orderList = M('order'.$select_year)->where($condition)->limit($Page->firstRow . ',' . $Page->listRows)->order('add_time DESC')->select();
        //@new 新UI 需要 {
        //获取每个订单的商品列表
        $order_id_arr = get_arr_column($orderList, 'order_id');
        //查询所有订单的所有商品
        if (count($order_id_arr) > 0) {
            $goods_list = M('order_goods'.$select_year)->where("is_send<2 and order_id", "in", implode(',', $order_id_arr))->select();
            $goods_arr = array();
            foreach ($goods_list as $v) {
                $goods_arr[$v['order_id']][] = $v;
            }
            $this->assign('goodsArr', $goods_arr);
        }
        //查询所有订单的用户昵称
        $user_id_arr = get_arr_column($orderList, 'user_id');
        if (count($user_id_arr) > 0) {
            $users = M('users')->where("user_id", "in", implode(',', $user_id_arr))->getField("user_id,nickname");
            $this->assign('users', $users);
        }
        //}
        $this->assign('orderList', $orderList);
        $this->assign('page', $show);// 赋值分页输出
        return $this->fetch();
    }

    /**
     * 订单详情
     * @param $order_id
     * @return mixed
     */
    public function detail($order_id)
    {
        $orderLogic = new OrderLogic();
        $order = $orderLogic->getOrderInfo($order_id);
        if(!$order){
        	$this->error('该订单不存在或没有权利查看', U('Seller/Order/index'));
        }
        $orderGoods = $orderLogic->getOrderGoods($order_id);
        $button = $orderLogic->getOrderButton($order);
        // 获取操作记录
        $select_year = getTabByOrderId($order_id);
        $OrderAction = new OrderAction();
        $action_log = $OrderAction->name('order_action'.$select_year)->where(['order_id'=>$order_id])->order('log_time desc')->select();
        $shop = db('shop')->where('shop_id',$order['shop_id'])->find();
        $this->assign('shop',$shop);
        $this->assign('order', $order);
        $this->assign('action_log', $action_log);
        $this->assign('orderGoods', $orderGoods);

        /*
         定义一个变量, 用于前端UI显示订单5个状态进度. 1: 提交订单;2:订单支付; 3: 商家发货; 4: 确认收货; 5: 订单完成
         此判断依据根据来源于 Common的config.phpz中的"订单用户端显示状态" @{

       '1'=>' AND pay_status = 0 AND order_status = 0 AND pay_code !="cod" ', //订单查询状态 待支付
        '2'=>' AND (pay_status=1 OR pay_code="cod") AND shipping_status !=1 AND order_status in(0,1) ', //订单查询状态 待发货
        '3'=>' AND shipping_status=1 AND order_status = 1 ', //订单查询状态 待收货
        '4'=> ' AND order_status=2 ', // 待评价 已收货     //'FINISHED'=>'  AND order_status=1 ', //订单查询状态 已完成
        '5'=> ' AND order_status = 4 ', // 已完成 */

        $show_status = $orderLogic->getShowStatus($order);
        if($order['is_comment'] == 1){
            $comment_time = M('comment')->where('order_id' , $order['order_id'])->order('comment_id desc')->value('add_time');
            $this->assign('comment_time', $comment_time); //查询评论时间
        }
        $this->assign('show_status', $show_status);
        $this->assign('button', $button);
        return $this->fetch();
    }


    /**
     * 订单删除
     */
    public function delete_order()
    {
        $orderLogic = new OrderLogic();
        $order_id = input('order_id/d');
        if(empty($order_id)){
            $this->error('参数错误');
        }
        $order = Db::name('order')->where('order_id',$order_id)->find();
        if(empty($order)){
            $this->error('订单记录不存在');
        }
        if($order['deleted'] == 1){
            $this->error('订单记录已经删除');
        }
        if($order['order_status'] != 5){
            $this->error('只有作废的订单才能删除');
        }
        $del = $orderLogic->delOrder($order_id,STORE_ID);
        if ($del !== false) {
            $this->success('删除订单成功');
        } else {
            $this->error('订单删除失败');
        }
    }

    /**
     * 订单打印
     * @param int $id 订单id
     */
    public function order_print()
    {
        $order_id = I('order_id/d');
        $orderLogic = new OrderLogic();
        $order = $orderLogic->getOrderInfo($order_id);
        if ($order['shop_id']!=session('shop_id')) {
            $this->error('该订单不存在', U('Shop/Order/index'));
        }
        $order['province'] = getRegionName($order['province']);
        $order['city'] = getRegionName($order['city']);
        $order['district'] = getRegionName($order['district']);
        $order['full_address'] = $order['province'] . ' ' . $order['city'] . ' ' . $order['district'] . ' ' . $order['address'];
        $orderGoods = $orderLogic->getOrderGoods($order_id);
        //@new 兼容新UI 计算商品总是 { 
        $order_num_arr = get_arr_column($orderGoods, 'goods_num');
        if ($order_num_arr) {
            $goodsCount = array_sum($order_num_arr);
            $this->assign('goods_count', $goodsCount);
        }
        // }
        $shop = tpCache('shop_info');
        $this->assign('order', $order);
        $this->assign('shop', $shop);
        $this->assign('orderGoods', $orderGoods);
        $template = I('template', 'print');
        if (strstr($template,'.')||strstr($template,'/') || strstr($template,'\\')) {
            $this->error('非法模板名称');
        }
        return $this->fetch($template);
    }

    /**
     * 快递单打印
     */
    public function shipping_print()
    {
        $order_id = I('get.order_id/d');
        $orderLogic = new OrderLogic();
        $order = $orderLogic->getOrderInfo($order_id);
        if ($order['store_id'] != STORE_ID) {
            $this->error('该订单不存在', U('shop/Order/index'));
        }
        //查询是否存在订单及物流
        $shipping = M('plugin')->where(array('code' => $order['shipping_code'], 'type' => 'shipping'))->find();
        if (!$shipping) {
            $this->error('物流插件不存在');
        }
        if (empty($shipping['config_value'])) {
            $this->error('请联系平台管理员设置' . $shipping['name'] . '打印模板');
        }
        $shop = M('store')->where(array('store_id' => STORE_ID))->find();
        $shop['province'] = empty($shop['province_id']) ? '' : getRegionName($shop['province_id']);
        $shop['city'] = empty($shop['city_id']) ? '' : getRegionName($shop['city_id']);
        $shop['district'] = empty($shop['district']) ? '' : getRegionName($shop['district']);

        $order['province'] = getRegionName($order['province']);
        $order['city'] = getRegionName($order['city']);
        $order['district'] = getRegionName($order['district']);
        if (empty($shipping['config'])) {
            $config = array('width' => 840, 'height' => 480, 'offset_x' => 0, 'offset_y' => 0);
            $this->assign('config', $config);
        } else {
            $this->assign('config', unserialize($shipping['config']));
        }
        $template_var = array("发货点-名称", "发货点-联系人", "发货点-电话", "发货点-省份", "发货点-城市",
            "发货点-区县", "发货点-手机", "发货点-详细地址", "收件人-姓名", "收件人-手机", "收件人-电话",
            "收件人-省份", "收件人-城市", "收件人-区县", "收件人-邮编", "收件人-详细地址", "时间-年", "时间-月",
            "时间-日", "时间-当前日期", "订单-订单号", "订单-备注", "订单-配送费用");
        $content_var = array($shop['store_name'], $shop['seller_name'], $shop['store_phone'], $shop['province'], $shop['city'],
            $shop['district'], $shop['store_phone'], $shop['store_address'], $order['consignee'], $order['mobile'], $order['phone'],
            $order['province'], $order['city'], $order['district'], $order['zipcode'], $order['address'], date('Y'), date('M'),
            date('d'), date('Y-m-d'), $order['order_sn'], $order['admin_note'], $order['shipping_price'],
        );
        $shipping['config_value'] = str_replace($template_var, $content_var, $shipping['config_value']);
        $this->assign('shipping', $shipping);
        return $this->fetch("Plugin/print_express");
    }

    public function delivery_info()
    {
        //商家发货
        $order_id = I('order_id/d');
        $orderLogic = new OrderLogic();
        $order = $orderLogic->getOrderInfo($order_id);
        if ($order['shop_id'] != session('shop_id')) {
            $this->error('该订单不存在', U('shop/Order/index'));
        }
        $return_goods = M('return_goods')->where(array('order_id' => $order_id, 'status' => array('<>', -2)))->getField('rec_id,goods_num');
        if ($order['shipping_status'] == 1) {
            $this->error('该订单已发货', U('shop/Order/index'));
        }
        $orderGoods = $orderLogic->getOrderGoods($order_id,2);
        $delivery_record = Db::name('delivery_doc')->alias('d')->join('__SELLER__ s', 's.seller_id = d.admin_id')->where(['d.order_id' => $order_id])->select();
        if ($delivery_record) {
            $order['invoice_no'] = $delivery_record[count($delivery_record) - 1]['invoice_no'];
        }
        foreach ($orderGoods as $k=>$v){
        	if(!empty($return_goods[$v['rec_id']])){
        		$orderGoods[$k]['unsend'] = 1;
        	}else{
        		$orderGoods[$k]['unsend'] = 0;
        	}
        }
        $this->assign('order', $order);
        $this->assign('orderGoods', $orderGoods);
        $this->assign('delivery_record', $delivery_record);//发货记录
        return $this->fetch();
    }

    /**
     * 发货单列表
     */
    public function delivery_list()
    {
        $this->assign('begin', date('Y-m-d', strtotime("-3 month")+86400));
        $this->assign('end', date('Y-m-d', strtotime('+1 days')));        
        return $this->fetch();
    }
    
    /**
     * 订单操作
     * @param $id
     */
    public function order_action()
    {
        $orderLogic = new OrderLogic();
        $type = I('get.type');
        $order_id = I('get.order_id/d');
        if ($type && $order_id) {
        	$order = $orderLogic->getOrderInfo($order_id);
            $button = $orderLogic->getOrderButton($order);
        	if($order){
        		$a = $orderLogic->orderProcessHandle($order_id, $type, session('shop_id'));
        		$seller_id = session('seller_id');
                $action = '';
                if(in_array($type,array_keys($button))){
                    $action = $button[$type];
                }
        		$res = $orderLogic->orderActionLog($order, $action, I('note'), $seller_id, 1);
        		if ($res && $a) {
        			exit(json_encode(array('status' => 1, 'msg' => '操作成功')));
        		} else {
        			exit(json_encode(array('status' => 0, 'msg' => '操作失败')));
        		}
        	}else{
        		exit(json_encode(array('status' => 0, 'msg' => '非法操作')));
        	}
        } else {
            $this->error('参数错误', U('shop/Order/detail', array('order_id' => $order_id)));
        }
    }

    public function order_log()
    {
        $condition = array();
        $log = M('order_action');
        $admin_id = I('admin_id/d');
        if ($admin_id > 0) {
            $condition['action_user'] = $admin_id;
        }
        $condition['store_id'] = STORE_ID;
        $count = $log->where($condition)->count();
        $Page = new Page($count, 20);
        foreach ($condition as $key => $val) {
            $Page->parameter[$key] = urlencode($val);
        }
        $show = $Page->show();
        $list = $log->where($condition)->order('action_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $seller = M('seller')->getField('seller_id,seller_name');
        $this->assign('admin', $seller);
        return $this->fetch();
    }

    public function export_order()
    {
        //搜索条件
        $where['store_id'] = STORE_ID;
        $consignee = I('consignee');
        if ($consignee) {
            $where['consignee'] = ['like', '%'.$consignee.'%'];
        }
        $order_sn = I('order_sn');
        if ($order_sn) {
            $where['order_sn'] = $order_sn;
        }
        $order_status = I('order_status');
        if ($order_status) {
            $where['order_status'] = $order_status;
        }

        $timegap = I('timegap');
        if ($timegap) {
            $gap = explode('-', $timegap);
            $begin = strtotime($gap[0]);
            $end = strtotime($gap[1]);
            $where['add_time'] = ['between',[$begin,$end]];
        }
        $region = Db::name('region')->cache(true)->getField('id,name');
        $orderList = Db::name('order')->field("*,FROM_UNIXTIME(add_time,'%Y-%m-%d') as create_time")->where($where)->order('order_id')->select();
        $strTable = '<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">订单编号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">日期</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">收货人</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">收货地址</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">电话</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">订单金额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">实际支付</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">支付方式</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">支付状态</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">发货状态</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">商品信息</td>';
        $strTable .= '</tr>';

        foreach ($orderList as $k => $val) {
            $strTable .= '<tr>';
            $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;' . $val['order_sn'] . '</td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['create_time'] . ' </td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . "{$region[$val['province']]},{$region[$val['city']]},{$region[$val['district']]},{$region[$val['twon']]}{$val['consignee']}" . ' </td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['address'] . '</td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['mobile'] . '</td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['goods_price'] . '</td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['order_amount'] . '</td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['pay_name'] . '</td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $this->pay_status[$val['pay_status']] . '</td>';
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $this->shipping_status[$val['shipping_status']] . '</td>';
            $orderGoods = D('order_goods')->where('order_id', $val['order_id'])->select();
            $strGoods = "";
            foreach ($orderGoods as $goods) {
                $strGoods .= "商品编号：" . $goods['goods_sn'] . " 商品名称：" . $goods['goods_name'];
                if ($goods['spec_key_name'] != '') $strGoods .= " 规格：" . $goods['spec_key_name'];
                $strGoods .= "<br />";
            }
            unset($orderGoods);
            $strTable .= '<td style="text-align:left;font-size:12px;">' . $strGoods . ' </td>';
            $strTable .= '</tr>';
        }
        $strTable .= '</table>';
        unset($orderList);
        downloadExcel($strTable, 'order');
        exit();
    }



}
