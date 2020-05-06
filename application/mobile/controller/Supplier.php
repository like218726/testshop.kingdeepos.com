<?php
namespace app\mobile\controller;
use app\common\model\Order as OrderModel;
/**
 * 客服IM控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/13
 * Time: 17:29
 */
class Supplier extends MobileBase{

    //客服im界面
    public function index()
    {
        $store_id = input('get.store_id');
        $goods_id = input('get.goods_id');
        $user = array();
        if(session("?user")){
            $user = session('user');
            if(!empty($user['head_pic'])){
                if(strpos($user['head_pic'], 'http')===0){
                }else{
                    $user['head_pic'] = SITE_URL . $user['head_pic'];
                }
            }else{
                $user['head_pic'] = '';
            }
        }
        $order_id = input('order_id');
        if($order_id){
            $Order = new OrderModel();
            $order = $Order::get(['order_id' => $order_id]);
            $this->assign('order', $order);
            $user['store_id'] = $order->store_id;
        }
        //店铺总会有
        $user['store_id'] = $store_id;
        $user['goods_id'] = $goods_id;
        $this->assign('user',$user);
        return $this->fetch();
    }

    //app 客服交互页面
    public function appServiceContact()
    {
        $store_id = input('get.store_id');
        if(empty($store_id)) exit('参数错误');
        $user = [
            'store_id' => $store_id,
            'goods_id' => input('get.goods_id') ? : '',
            'user_id' => input('get.user_id') ? : '',
            'nickname' => input('get.nickname') ? : '',
            'head_pic' => input('get.head_pic') ? : '',
        ];
        //store_id order_id
        $order_id = input('order_id');
        if($order_id){
            $Order = new OrderModel();
            $order = $Order::get(['order_id' => $order_id]);
            $this->assign('order', $order);
            $user['order_id'] = $order_id;
            $user['store_id'] = $order->store_id;
        }
        $this->assign('user',$user);
        return $this->fetch('app');
    }
}