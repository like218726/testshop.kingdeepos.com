<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 商业用途务必到官方购买正版授权, 使用盗版将严厉追究您的法律责任。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * 2016-11-21
 */
namespace app\home\controller;

use app\common\logic\Message;
use app\common\logic\OrderLogic;
use app\common\logic\VirtualLogic;
use think\Page;
use think\Db;
class Virtual extends Base
{

    public $user_id = 0;
    public $user = array();

    public function _initialize()
    {
        parent::_initialize();
        if (session('?user')) {
            $user = session('user');
            $user = M('users')->where("user_id", $user['user_id'])->find();
            session('user', $user);  //覆盖session 中的 user
            $this->user = $user;
            $this->user_id = $user['user_id'];
            $this->assign('user', $user); //存储用户信息
            $this->assign('user_id', $this->user_id);
            //获取用户信息的数量
            $messageLogic = new Message();
            $user_message_count = $messageLogic->getUserMessageNoReadCount();
            $this->assign('user_message_count', $user_message_count);
        } else {
            $nologin = array(
                'login', 'pop_login', 'do_login', 'logout', 'verify', 'set_pwd', 'finished',
                'verifyHandle', 'reg', 'send_sms_reg_code', 'identity', 'check_validate_code',
                'forget_pwd', 'check_captcha', 'check_username', 'send_validate_code',
            );
            if (!in_array(ACTION_NAME, $nologin)) {
                header("location:" . U('Home/User/login'));
                exit;
            }
            if (ACTION_NAME == 'password') $_SERVER['HTTP_REFERER'] = U("Home/User/index");
        }

        //用户中心面包屑导航
        $navigate_user = navigate_user();
        $this->assign('navigate_user', $navigate_user);
    }

    /*
     * 虚拟订单列表
     */
    public function virtual_list()
    {
        $type = I('get.type');
        $search_key = I('search_key');
        $virtualLogic = new \app\common\logic\VirtualLogic;
        $result = $virtualLogic->orderList($this->user_id, $type, $search_key);        
        
        $this->assign('years', buyYear()); // 获取年限
        $this->assign('order_status', C('ORDER_STATUS'));
        $this->assign('shipping_status', C('SHIPPING_STATUS'));
        $this->assign('pay_status', C('PAY_STATUS'));
        $this->assign('page', $result['page']->show());
        $this->assign('lists', $result['order_list']);
        $this->assign('active', 'order_list');
        $this->assign('active_status', I('get.type'));
        $this->assign('now', time());
        return $this->fetch();
    }

    /**
     * 虚拟订单详情
     */
    public function virtual_order(){
        $Order = new \app\common\model\Order();
        $VirtualLogic = new VirtualLogic();
        $order_id = I('get.order_id/d');
        $map['order_id'] = $order_id;
        $map['user_id'] = $this->user_id;
        $orderobj = $Order->where($map)->find();
        if(!$orderobj) $this->error('没有获取到订单信息');
        // 添加属性  包括按钮显示属性 和 订单状态显示属性
        $order_info = $orderobj->append(['order_status_detail','virtual_order_button','order_goods','store','vr_order_code'])->toArray();
        if($order_info['prom_type'] != 5){   //普通订单
            $this->redirect(U('Order/order_detail',['id'=>$order_id]));
        }
        //获取订单操作记录
        $order_action = Db::name('order_action')->where(array('order_id'=>$order_id))->select();

        $result = $VirtualLogic->check_virtual_code($order_info);
        $store = Db::name('store')->field('store_name,store_phone,seller_name')->where(['store_id'=>$order_info['store_id']])->find();
        $this->assign('store',$store);
        $this->assign('order_status',C('ORDER_STATUS'));
        $this->assign('pay_status',C('PAY_STATUS'));
        $this->assign('order_info',$result);
        $this->assign('order_action',$order_action);
        return $this->fetch();
    }


}  