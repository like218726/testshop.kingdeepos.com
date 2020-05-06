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
 * Date: 2016-05-09
 */

namespace app\seller\controller;

use think\Db;
use think\Validate;
use think\Controller;
use app\seller\logic\OrderLogic;

class Api extends Controller
{
    public $seller = null;
    public $store = null;

    public function __construct()
    {
        parent::__construct();

        if (ACTION_NAME == 'token' || ACTION_NAME == 'test') {
            return;
        }

        if (!$token = input('token')) {
            ajaxReturn(['status' => -100, 'msg' => 'token不存在']);
        }

        $seller = Db::name('seller')->where('token', $token)->find();
        if (!$seller) {
            ajaxReturn(['status' => -100, 'msg' => 'token已过期或商家不存在']);
        }
        $this->seller = $seller;
        session('seller_id', $seller['seller_id']);

        $store = Db::name('store')->where('store_id', $seller['store_id'])->find();
        if (!$store) {
            ajaxReturn(['status' => -1, 'msg' => '店铺不存在']);
        }
        if ($store['store_state'] == 0) {
            ajaxReturn(['status' => -1, 'msg' => '店铺已关闭']);
        }
        $this->store = $store;
        define(STORE_ID, $store['store_id']);
    }

    /**
     * 获取token
     */
    public function token()
    {
        $seller_name = input('username');
        $password = input('password');

        $seller = Db::name('seller')->where('seller_name', $seller_name)->find();
        if (!$seller) {
            ajaxReturn(['status' => -1, 'msg' => '商家不存在']);
        }
        $user = Db::name('users')->where(['user_id' => $seller['user_id'], 'password' => encrypt($password)])->find();
        if (!$user) {
            ajaxReturn(['status' => -1, 'msg' => '用户不存在']);
        }

        $token = md5(time() . mt_rand(1, 999999999));
        Db::name('seller')->where('seller_name', $seller_name)->save(['token' => $token]);

        ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => ['token' => $token]]);
    }

    /**
     * 发货
     */
    public function deliveryHandle()
    {
        $post = I('post.');
        $validate = new Validate([
            ['order_sn', 'require', 'order_sn为空'],
            ['goods_sn', 'require', 'goods_sn为空'],
            ['shipping_name', 'require', 'shipping_name为空'],
            ['invoice_no', 'require', 'invoice_no为空'],
        ]);
        if (!$validate->check($post)) {
            ajaxReturn(['status' => -1, 'msg' => $validate->getError()]);
        }

        $order = Db::name('order')->where('order_sn', $post['order_sn'])->find();
        if (!$order) {
            ajaxReturn(['status' => -1, 'msg' => '订单不存在']);
        }
        $goods_sns = I('post.goods_sn/a', []);
        $order_goods = Db::name('order_goods')->where(['order_id' => $order['order_id'], 'goods_sn' => ['in', $goods_sns]])->select();
        if (!$order_goods) {
            ajaxReturn(['status' => -1, 'msg' => '订单中商品不存在']);
        }
        $shipping = Db::name('plugin')->where(['name' => $post['shipping_name'], 'type' => 'shipping'])->find();
        if (!$shipping) {
            ajaxReturn(['status' => -1, 'msg' => '物流不存在']);
        }

        $rec_ids = get_arr_column($order_goods, 'rec_id');
        $data = [
            'order_id' => $order['order_id'],
            'goods' => $rec_ids,
            'shipping_code' => $shipping['code'],
            'shipping_name' => $post['shipping_name'],
            'invoice_no' => $post['invoice_no'],
            'shipping' => 0,
            'note' => '',
        ];
        $orderLogic = new OrderLogic();
        $res = $orderLogic->deliveryHandle($data, STORE_ID);
        if (!$res) {
            ajaxReturn(['status' => -1, 'msg' => '操作失败']);
        }

        ajaxReturn(['status' => 1, 'msg' => '操作成功']);
    }
}