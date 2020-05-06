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
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * Author: wangqh
 * Date: 2015-09-09
 */

namespace app\common\logic;

 
use think\Model;
use think\Db;
/**
 * 虚拟订单logic
 * Class CatsLogic
 * @package common\Logic
 */
class VirtualLogic extends Model
{
    protected $goods;//商品模型
     

    public function __construct()
    {
        parent::__construct();
        $this->session_id = session_id();
    }

    public function check_virtual_goods($goods_id,$item_id=0,$goods_num=0){ 
     
        if(empty($goods_id)){
            return array('status'=>-1 , 'msg'=>'请求参数错误');
        }
        $goods = M('goods')->where(array('goods_id'=>$goods_id))->find();
        if(!$goods){
            return array('status'=>-1 , 'msg'=>'该商品不允许购买，原因有：商品下架、不存在、过期等');
        }
        if($goods['is_virtual'] == 1 && $goods['virtual_indate']>time() && $goods['store_count']>0){
            $goods_num = $goods['goods_num'] = $goods_num;
            if($goods_num < 1){
                return array('status'=>-1 , 'msg'=>'最少购买1件');
            }
            if ($goods['virtual_limit'] > $goods['store_count'] || $goods['virtual_limit'] == 0) {
                $goods['virtual_limit'] = $goods['store_count'];
            }
            
            if($item_id>0){
                $specGoodsPrice = M('SpecGoodsPrice')->where(array('item_id'=>$item_id))->cache(true,TPSHOP_CACHE_TIME)->find(); // 获取商品对应的规格价钱 库存 条码
            
                if($specGoodsPrice) // 有选择商品规格
                { 
                    if($specGoodsPrice['store_count'] < $goods_num){
                        return array('status'=>-1 , 'msg'=>'该商品规格库存不足'); 
                    }
                    $goods['goods_spec_key'] = $specGoodsPrice['key'];
                    $goods['item_id'] = $specGoodsPrice['item_id'];
                    $goods['spec_key_name'] = $specGoodsPrice['key_name'];
                    $spec_price = $specGoodsPrice['price']; // 获取规格价格
                    $goods['shop_price'] = empty($spec_price) ? $goods['shop_price'] : $spec_price;
                }
            }  
            $goods['goods_fee'] = $goods['shop_price']*$goods['goods_num'];
            return array('status'=>1 , 'goods'=>$goods); 
        }else{
            return array('status'=>-1 , 'msg'=>'该商品不允许购买，原因可能：商品下架、不存在、过期等');
        }
    }

    /**
     * 虚拟订单列表
     * @param $user_id
     * @param $type
     * @param $search_key
     * @return array
     */
    public function orderList($user_id, $type, $search_key)
    {
        $order = new \app\common\model\Order();
        $select_year = select_year(); // 查询 三个月,今年内,2016年等....订单
        //删除的订单不列出来， 作废订单不列出来，不是虚拟订单不列出来
        $where = 'user_id=:user_id and deleted = 0  and prom_type = 5 ';
        $bind['user_id'] = $user_id;
        //条件搜索
        if (C('buy_version') != 1) {
            $search_year = I('select_year');
            if ($search_year == '') { // 近三个月
                $add_start_time = time() - (86400 * 90);
                $add_end_time = time();
            } elseif ($search_year == '_this_year') { // 今年内
                $add_start_time = strtotime(date('Y-01-01'));
                $add_end_time = time();
            } else {
                $search_year = substr($search_year,1);
                $add_start_time = strtotime(date("$search_year-01-01"));
                $search_year +=1;
                $add_end_time = strtotime(date("$search_year-01-01"));
            }
            $where .= " and add_time > :add_start_time and add_time < :add_end_time";
            $bind['add_start_time'] = $add_start_time;
            $bind['add_end_time'] = $add_end_time;
        }

        if ($type) {
            $where .= C(strtoupper($type));
        }
        // 搜索订单 根据商品名称 或者 订单编号
        $search_key = trim($search_key);
        if ($search_key) {
            $where .= " and (order_sn like :search_key1 or order_id in (select order_id from `" . C('database.prefix') . "order_goods{$select_year}` where goods_name like :search_key2) ) ";
            $bind['search_key1'] = '%' . $search_key . '%';
            $bind['search_key2'] = '%' . $search_key . '%';
        }
        $count = M('order'.$select_year)->where($where)->bind($bind)->count();
        $page = new \think\Page($count, 10);
        $order_str = "order_id DESC";
        //获取订单
        $order_list = [];
        $order_list_obj = M('order')->order($order_str)->where($where)->bind($bind)->limit($page->firstRow, $page->listRows)->select(); 
        if ($order_list_obj) {
            foreach ($order_list_obj as $k => $v) {
                $v['order_status_detail'] = $order->getOrderStatusDetailAttr(null,$v);
                $v['order_button'] = $order->getVirtualOrderButtonAttr(null,$v);
                $v['order_goods'] = M('order_goods'.$select_year)->cache(true,3)->where('order_id = '.$v['order_id'])->select();
                $v['store'] = M('store')->cache(true)->where('store_id = '.$v['store_id'])->field('store_id,store_name,store_qq')->find();
                $order_list[] = $v;
            }
        }
        return [
            'order_list' => $order_list,
            'page' => $page
        ];
    }

    /**
     * 检查虚拟订单兑换码状态
     * @param $order
     * @return mixed
     */
    public function check_virtual_code($order){
        $vrOrders = $order['vr_order_code'];
        if(count($vrOrders) > 0){
            $overdue_num=0; //计算订单过期兑换码数量
            foreach($vrOrders as $codeKey => $codeVal){
                if($codeVal['vr_indate'] < time() && $codeVal['vr_state'] == 0){ //没使用，超过有效期
                    $overdue_num++;
                    Db::name('vr_order_code')->where(['vr_indate'=>$codeVal['vr_indate']])->update(['vr_state'=>2]);//已过期
                    $vrOrders[$codeKey]['vr_state']=2;
                }
            }
            if($overdue_num == count($vrOrders)){  //全部过期就作废
                Db::name('order')->where(['order_id'=>$order['order_id']])->update(['order_status'=>5]);
                $order['order_status']=5;
                $order['virtual_order_button']['receive_btn'] = 0;
            }
        }
        return $order;
    }
}