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
 * $Author: IT宇宙人 2015-08-10 $
 */ 
namespace app\api\controller;

use think\Db;
use app\common\model\Users;

class Payment extends Base 
{
    /**
     * app端发起支付宝,支付宝返回服务器端,  返回到这里
     * http://www.tp-shop.cn/index.php/Api/Payment/alipayNotify
     */
    public function alipayNotify()
    {
        $paymentPlugin = M('Plugin')->where("code='alipay' and  type = 'payment' ")->find(); // 找到支付插件的配置
        $config_value = unserialize($paymentPlugin['config_value']); // 配置反序列化        

        require_once("plugins/payment/alipay/app_notify/alipay.config.php");
        require_once("plugins/payment/alipay/app_notify/lib/alipay_notify.class.php");

        $alipay_config['partner'] = $config_value['alipay_partner'];//合作身份者id，以2088开头的16位纯数字        

        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();

        //验证成功
        if ($verify_result) {                           
            $order_sn = $out_trade_no = trim($_POST['out_trade_no']); //商户订单号
            $trade_no = $_POST['trade_no'];//支付宝交易号
            $trade_status = $_POST['trade_status'];//交易状态
            
			
		//用户在线充值
		if (stripos($order_sn, 'recharge') !== false)
			$order_amount = M('recharge')->where(['order_sn' => $order_sn, 'pay_status' => 0])->value('account');
		else			
            $order_amount = M('order')->where(['master_order_sn'=>$order_sn])->whereOr(['order_sn'=>$order_sn])->sum('order_amount');
            if($order_amount!=$_POST['price'])
                exit("fail"); //验证失败

            if ($_POST['trade_status'] == 'TRADE_FINISHED') {
                update_pay_status($order_sn,$trade_no); // 修改订单支付状态                
            } elseif ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                update_pay_status($order_sn,$trade_no); // 修改订单支付状态                
            }               
            M('order')->where('order_sn', $order_sn)->whereOr('master_order_sn',$order_sn)->save(array('pay_code'=>'alipay','pay_name'=>'app支付宝'));
            echo "success"; //  告诉支付宝支付成功 请不要修改或删除               
        } else {
            echo "fail"; //验证失败         
        }
    }
 
    public function alipay_sign()
    {
        $orderSn = input('post.order_sn', '');
        $user = session('user');

        if (strpos($orderSn, 'recharge') === 0 || $orderSn === '') {
            //充值流程
            $orderAmount = input('account/f', 0);
            if ($orderAmount <= 0) {
                $this->ajaxReturn(['status'=>-1, 'msg'=>'充值金额不能为'.$orderAmount]);
            }
            if ($orderSn) {
                $order = M('recharge')->where("order_sn", $orderSn)->find();
                if (!$order) {
                    $this->ajaxReturn(['status'=>-1, 'msg'=>'该充值订单不存在']);
                }
                M('recharge')->where(['order_sn' => $orderSn, 'user_id' => $user['user_id']])->save(['account' => $orderAmount]);
            } else {
                $orderSn = 'recharge'.get_rand_str(10,0,1);
                $order['user_id'] = $user['user_id'];
                $order['nickname'] = $user['nickname'];
                $order['account'] = $orderAmount;
                $order['order_sn'] = $orderSn;
                $order['pay_name'] = 'app支付宝';
                $order['ctime'] = time();
                M('recharge')->add($order);
            }
            $payBody = '在线充值';
        } else {
            //支付流程
            $order = M('order')->alias('o')->field('o.order_amount,o.order_id')
                    ->where('o.order_sn|o.master_order_sn', $orderSn)->select();
            if (!$order) {
                $this->ajaxReturn(['status' => -1, 'msg' => '订单不存在']);
            }
            // 所有商品单价相加
            $orderAmount = array_reduce($order, function ($sum, $val) {
                return $sum + $val['order_amount'];
            }, 0);
            
            $payBody = getPayBody($order[0]['order_id']);
        }
        
        if (!function_exists('openssl_sign')) {
            $this->ajaxReturn(['status' => -1, 'msg' => '请先启用php的openssl扩展']);
        }
        
        $paymentPlugin = M('plugin')->where(['code' => 'alipay', 'type' => 'payment'])->find();
        $cfgVal = unserialize($paymentPlugin['config_value']); // 配置反序列化
        if (!$cfgVal || empty($cfgVal['alipay_partner']) || empty($cfgVal['alipay_private_key']) || empty($cfgVal['alipay_account'])) {
            $this->ajaxReturn(['status' => -1, 'msg' => '支付宝重要配置不能为空！']);
        }
        
        $storeName = M('config')->where('name', 'store_name')->getField('value');
        
        include_once(PLUGIN_PATH.'payment/alipay/app_notify/lib/alipay_sign.class.php');

        
        $sign = new \AlipaySign;
        $sign->partner = $cfgVal['alipay_partner'];
        $sign->rsaPrivateKey = $cfgVal['alipay_private_key'];
        $sign->seller_id = $cfgVal['alipay_account'];
        $sign->notifyUrl = SITE_URL.'/index.php/Api/Payment/alipayNotify';
        $result = $sign->execute($storeName, $payBody, $orderAmount, $orderSn);
        
		$this->ajaxReturn(['status' => 1, 'msg' => '签名成功', 'result' => $result]);
    }

/********************************************新的app支付宝支付--start********************************************/

    /**
     * 2017-07-21 by 我是个导演
     * 欢迎访问支付宝论坛：https://openclub.alipay.com/index.php
     * 
     * APP支付 RSA2签名方法
     */
    public function newalipay_sign()
    {
        $orderSn = input('post.order_sn', '');
        $user = session('user');
        if(empty($user['user_id'])){
            $user_id = input('user_id');
            $user = M('users')->find($user_id);
            if(empty($user)){
                $this->ajaxReturn(['status'=>-1, 'msg'=>'用户不存在']);
            }
        }
        if (strpos($orderSn, 'recharge') === 0 || $orderSn === '') {
            //充值流程
            $orderAmount = input('account/f', 0);
            if ($orderAmount <= 0) {
                $this->ajaxReturn(['status'=>-1, 'msg'=>'充值金额不能为'.$orderAmount]);
            }
            if ($orderSn) {
                $order = M('recharge')->where("order_sn", $orderSn)->find();
                if (!$order) {
                    $this->ajaxReturn(['status'=>-1, 'msg'=>'该充值订单不存在']);
                }
                M('recharge')->where(['order_sn' => $orderSn, 'user_id' => $user['user_id']])->save(['account' => $orderAmount]);
            } else {
                $orderSn = 'recharge'.get_rand_str(10,0,1);
                $order['user_id'] = $user['user_id'];
                $order['nickname'] = $user['nickname'];
                $order['account'] = $orderAmount;
                $order['order_sn'] = $orderSn;
                $order['pay_name'] = 'APP支付宝';
                $order['ctime'] = time();
                M('recharge')->add($order);
            }
            $payBody = '在线充值';
        } else {
            //支付流程
            $order = Db::name('order')->field('order_amount,order_id')->where('master_order_sn', $orderSn)->whereOr('order_sn', $orderSn)->select();
            if (!$order) {
                $this->ajaxReturn(['status' => -1, 'msg' => '订单不存在']);
            }
            // 所有商品单价相加
            $orderAmount = array_reduce($order, function ($sum, $val) {
                return $sum + $val['order_amount'];
            }, 0);
           $payBody = getPayBody($order[0]['order_id']);
        }
        
        $paymentPlugin = M('plugin')->where(['code' => 'newalipay', 'type' => 'payment'])->find();
        $cfgVal = unserialize($paymentPlugin['config_value']); // 配置反序列化
        if (!$cfgVal || empty($cfgVal['app_id']) || empty($cfgVal['merchant_private_key']) || empty($cfgVal['alipay_public_key'])) {
            $this->ajaxReturn(['status' => -1, 'msg' => '支付宝重要配置不能为空！']);
        }

        $storeName = M('config')->where('name', 'store_name')->getField('value');
        require_once PLUGIN_PATH.'payment/newalipay/app_pay/AopSdk.php';

        $aop = new \AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';//正式环境网关
        $aop->appId = $cfgVal['app_id'];
        $aop->rsaPrivateKey = $cfgVal['merchant_private_key'];
        $aop->alipayrsaPublicKey = $cfgVal['alipay_public_key'];
        $aop->apiVersion = '1.0';
        $aop->postCharset='utf-8';
        $aop->format='json';
        $aop->signType = 'RSA2';

        $request = new \AlipayTradeAppPayRequest();
        //异步地址传值方式
        $request->setNotifyUrl(SITE_URL.'/index.php/Api/Payment/newalipayNotify');
        $request->setBizContent("{\"out_trade_no\":\"".$orderSn."\",\"total_amount\":".$orderAmount.",\"product_code\":\"QUICK_MSECURITY_PAY\",\"subject\":\"".$storeName."\"}");

        $result = $aop->sdkExecute($request);

        $this->ajaxReturn(['status' => 1, 'msg' => '签名成功', 'result' => htmlspecialchars($result)]);
    }

    /**
     * 新app端发起支付宝,支付宝返回服务器端,  返回到这里
     * http://www.tp-shop.cn/index.php/Api/Payment/newalipayNotify
     */
    public function newalipayNotify()
    {    
        $paymentPlugin = M('Plugin')->where("code='newalipay' and  type = 'payment' ")->find(); // 找到支付插件的配置
        $config_value = unserialize($paymentPlugin['config_value']); // 配置反序列化        
        
        require_once PLUGIN_PATH.'payment/newalipay/app_pay/AopSdk.php';
        $aop = new \AopClient;
        $aop->alipayrsaPublicKey = $config_value['alipay_public_key'];
        $flag = $aop->rsaCheckV1($_POST, NULL, "RSA2");

        //认证成功
        if ($flag) {
            $order_sn = $out_trade_no = trim($_POST['out_trade_no']); //商户订单号
            $trade_no = $_POST['trade_no'];//支付宝交易号
            $trade_status = $_POST['trade_status'];//交易状态
            
            //用户在线充值
            if (stripos($order_sn, 'recharge') !== false){
                $order_amount = M('recharge')->where(['order_sn' => $order_sn, 'pay_status' => 0])->value('account');
            }else{                  
                $order_amount = M('order')->where(['master_order_sn'=>"$order_sn"])->whereOr(['order_sn'=>"$order_sn"])->sum('order_amount');
            }
            if($order_amount!=$_POST['total_amount'])
                exit("fail"); //验证失败
            
            if ($_POST['trade_status'] == 'TRADE_FINISHED') {

                update_pay_status($order_sn , $trade_no); // 修改订单支付状态    

            } elseif ($_POST['trade_status'] == 'TRADE_SUCCESS') {   

                update_pay_status($order_sn , $trade_no); // 修改订单支付状态 
                
            }               
            M('order')->where("order_sn", $order_sn)->whereOr('master_order_sn',$order_sn)->save(array('pay_code'=>'newalipay','pay_name'=>'app支付宝'));
            echo "success"; //  告诉支付宝支付成功 请不要修改或删除     
        } else {
            echo "fail"; //验证失败       
        }
    }


/********************************************新的app支付宝支付--end********************************************/


    /**
     * 普通订单余额支付
     */
    public function balancePay(){
        $master_order_sn = I('master_order_sn',''); //主订单号
        $order_sn = I('order_sn',''); //拼团订单号专用
        $order_id = I('order_id/d',0); //单一订单id
        $password = I('password',''); // 支付密码
        $user = session('user');
        $sum_order_amount = 0; //订单需支付的总金额
        if (!$user) {
            $this->ajaxReturn(['status' => -1, 'msg' => '请先登录' ]);
        }
        $order_where['user_id'] = $user['user_id'];
        /*如果是主订单号过来的, 说明可能是合并付款的,
         *一般刚提交订单时候才会传这个参数
         * */
        if($master_order_sn)
        {
            $order_where['master_order_sn'] = $master_order_sn;
            $order_list = Db::name('order')->where($order_where)->select();

            if (count($order_list) > 0) {
                foreach ($order_list as $orderKey => $orderVal) {
                    $sum_order_amount += $orderVal['order_amount'];
                }
            }
        }else{
            if($order_id){
                $order_where['order_id'] = $order_id;
            }else{
                $order_where['order_sn'] = $order_sn;
            }
            $orderModel = new \app\common\model\Order();
            $order = $orderModel->where($order_where)->find();
            if(!$order){
                $this->ajaxReturn(['status' => 0, 'msg' => '订单不存在！' ]);
            }
            // 如果已经支付过的订单直接到订单详情页面. 不再进入支付页面
            if($order['pay_status'] == 1){
                $this->ajaxReturn(['status' => 3, 'msg' => '订单已支付，不可重复支付' , 'result'=>$order['order_id'] ]);
            }
            $sum_order_amount = $order['order_amount'] ;
            $order_list[] =  $order->toArray();
        }

        //获取用户实时余额
        $user = Users::get($user['user_id']);
        if($user['user_money'] - $sum_order_amount < 0){
            $this->ajaxReturn(['status' => 0, 'msg' => '余额不足，请充值' ]);
        }

        if (empty($user['paypwd'])) {
            $this->ajaxReturn(['status' => 2, 'msg' => '请先设置支付密码' ]);
        }
        //下单前检查
        if ($user['is_lock'] == 1) {
            $this->ajaxReturn(['status' => 0, 'msg' => '账号异常已被锁定，不能使用余额支付！' ]);
        }
        if (empty($password )) {
            $this->ajaxReturn(['status' => 0, 'msg' => '请输入支付密码' ]);
        }
        if ($password  !== $user['paypwd'] && encrypt($password) !== $user['paypwd']) {
            $this->ajaxReturn(['status' => 0, 'msg' => '支付密码错误']);
        }
        //扣除用户余额
        $user->user_money = $user->user_money - $sum_order_amount;// 抵扣余额
        $user->save();
        //并产生消费记录
        $accountLogAllData = [];
        foreach($order_list as $payInfoKey => $payInfoVal){
            $accountLogData = [
                'user_id' => $payInfoVal['user_id'],
                'user_money' => -$payInfoVal['order_amount'],
                'change_time' => time(),
                'desc' => '下单消费',
                'order_sn' => $payInfoVal['order_sn'],
                'order_id' => $payInfoVal['order_id'],
            ];

            //设订单余额支付的金额,使用了余额支付，那线上支付设置为0，线上支付和余额支付不能共存
            Db::name('order')->where('order_id',$payInfoVal['order_id'])->save(['user_money'=>$payInfoVal['order_amount'],'order_amount'=>0,'pay_name'=>'余额支付']);

            //使用余额全额付款，修改状态订单
            update_pay_status($payInfoVal['order_sn']);
            array_push($accountLogAllData, $accountLogData);
        }

        Db::name('account_log')->insertAll($accountLogAllData);

        //支付成功跳转页面
        $this->ajaxReturn(['status' => 1, 'msg' => '支付成功' ,'result'=>$order['order_id'] ]);
    }
}
