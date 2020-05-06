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
namespace app\mobile\controller;


use think\Db;
use app\common\util\TpshopException;
use app\common\model\Users;
use app\common\model\Order;
use app\admin\logic\RefundLogic;

class Payment extends MobileBase {
    
    public $payment; //  具体的支付类
    public $pay_code; //  具体的支付code
    public $is_session = false; // 解决苹果手机h5支付有痕模式，报pay_code 不能为空的问题,无痕浏览，无法解决
 
    /**
     * 析构流函数
     */
    public function __construct()
    {
        parent::__construct();

        // 获取支付类型
        $pay_radio = input('pay_radio');
        if (!empty($pay_radio)) {
            $pay_radio = parse_url_param($pay_radio);
            $this->pay_code = $pay_radio['pay_code']; // 支付 code
        } else {
            $this->pay_code = I('get.pay_code');
            unset($_GET['pay_code']); // 用完之后删除, 以免进入签名判断里面去 导致错误
        }

        if (empty($this->pay_code)) {
            $this->is_session = true;
            $this->pay_code = session('pay_pay_code');
        }

        // 获取通知的数据
        if (empty($this->pay_code)) {
            exit('pay_code 不能为空');
        }

        $order_id = I('order_id/d');
        if($order_id){
            session('pay_order_id', $order_id);
        }
        $master_order_sn = I('master_order_sn', '');// 多单付款的 主单号
        if($master_order_sn){
            session('pay_master_order_sn', $master_order_sn);
        }
        session('pay_pay_code', $this->pay_code);
        //不是余额支付则导入插件
        if($this->pay_code != 'balance'){
        // 导入具体的支付类文件
        include_once "plugins/payment/{$this->pay_code}/{$this->pay_code}.class.php"; // D:\wamp\www\svn_tpshop\www\plugins\payment\alipay\alipayPayment.class.php
        $code = '\\' . $this->pay_code; // \alipay
        $this->payment = new $code();
        }
    }
   
    /**
     * tpshop 提交支付方式
     */
    public function getCode()
    {

        if (!session('user')) {
            $this->error('请先登录', U('User/login'));
        }

        // 修改订单的支付方式
        $payment_arr = M('Plugin')->where("`type` = 'payment'")->getField("code,name");
        $order_id = I('order_id/d', 0); // 订单id
        $master_order_sn = I('master_order_sn', '');// 多单付款的 主单号

        if($this->is_session){
            $deeplink_flag = 0;
            if(empty($order_id) && empty($master_order_sn)){
                $order_id = session('pay_order_id');
                $master_order_sn = session('pay_master_order_sn');
            }
        }

        // 如果是主订单号过来的, 说明可能是合并付款的
        if ($master_order_sn) {
            M('order')->where("master_order_sn", $master_order_sn)->save(array('pay_code' => $this->pay_code, 'pay_name' => $payment_arr[$this->pay_code]));
            $order = M('order')->where("master_order_sn", $master_order_sn)->find();
            $order['order_sn'] = $master_order_sn; // 临时修改 给支付宝那边去支付
            $order['order_amount'] = M('order')->where("master_order_sn", $master_order_sn)->sum('order_amount'); // 临时修改 给支付宝那边去支付
        } else {
            M('order')->where("order_id", $order_id)->save(array('pay_code' => $this->pay_code, 'pay_name' => $payment_arr[$this->pay_code]));
            $order = M('order')->where("order_id", $order_id)->find();
        }
        if ($order['pay_status'] == 1) {
            //$this->error('此订单，已完成支付!');
            $this->assign('order', $order);
            return $this->fetch('success');
        }
		
		if ($order['prom_type'] == 8) {
			$promRes = $this->checkProm($order);
			if (!$promRes) {
				$this->error('活动已结束', U('User/index'));
			}
		}
			
        // 订单支付提交
        $config = parse_url_param($this->pay_code); // 类似于 pay_code=alipay&bank_code=CCB-DEBIT 参数
        $config['body'] = getPayBody($order['order_id']);

        if ($this->pay_code == 'weixin' && $_SESSION['openid'] && strstr($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            //微信JS支付
            $code_str = $this->payment->getJSAPI($order, $config);
            exit($code_str);
        } elseif ($this->pay_code == 'weixinH5') {
            //微信H5支付,直接返回
            $return = $this->payment->get_code($order, $config);
            ajaxReturn($return);
        } else {
            //其他支付（支付宝、银联...）
            $code_str = $this->payment->get_code($order, $config);
        }

        $this->assign('code_str', $code_str);
        $this->assign('order_id', $order['order_id']);
        $this->assign('master_order_sn', $master_order_sn); // 主订单号
        header("Content-type:text/html;charset=utf-8");
        return $this->fetch('payment');  // 分跳转 和不 跳转
    }
    
    public function getPay(){
       	//手机端在线充值
        C('TOKEN_ON',false); // 关闭 TOKEN_ON 
        $order_id = I('order_id/d'); //订单id
        $user = session('user');
        $data['account'] = I('account');
        if($order_id>0){
        	M('recharge')->where(array('order_id'=>$order_id,'user_id'=>$user['user_id']))->save($data);
        }else{
        	$data['user_id'] = $user['user_id'];
        	$data['nickname'] = $user['nickname'];
        	$data['order_sn'] = 'recharge'.get_rand_str(10,0,1);
        	$data['ctime'] = time();
        	$order_id = M('recharge')->add($data);
        }
        if($order_id){
        	$order = M('recharge')->where("order_id" , $order_id)->find();
        	if(is_array($order) && $order['pay_status']==0){
        		$order['order_amount'] = $order['account'];
        		$pay_radio = $_REQUEST['pay_radio'];
        		$config_value = parse_url_param($pay_radio); // 类似于 pay_code=alipay&bank_code=CCB-DEBIT 参数
                $config_value['body'] = '会员充值';
        		$payment_arr = M('Plugin')->where("`type` = 'payment'")->getField("code,name");
        		M('recharge')->where("order_id" , $order_id)->save(array('pay_code'=>$this->pay_code,'pay_name'=>$payment_arr[$this->pay_code]));       		
        		//微信JS支付
        		if($this->pay_code == 'weixin' && $_SESSION['openid'] && strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
        			$code_str = $this->payment->getJSAPI($order);
        			exit($code_str);
        		} elseif ($this->pay_code == 'weixinH5') {
                    //微信H5支付,直接返回
                    $return = $this->payment->get_code($order, $config_value);
                    ajaxReturn($return);
                }else{
        			$code_str = $this->payment->get_code($order,$config_value);
        		}
        	}else{
        		$this->error('此充值订单，已完成支付!');
        	}
        }else{
        	$this->error('提交失败,参数有误!');
        }
        $this->assign('code_str', $code_str); 
        $this->assign('order_id', $order_id); 
    	return $this->fetch('recharge'); //分跳转 和不 跳转
    }

        // 服务器点对点 // http://www.tp-shop.cn/index.php/Home/Payment/notifyUrl        
        public function notifyUrl(){            
            $this->payment->response();            
            exit();
        }

        // 页面跳转 // http://www.tp-shop.cn/index.php/Home/Payment/returnUrl        
        public function returnUrl(){
             $result = $this->payment->respond2(); // $result['order_sn'] = '201512241425288593';            
             if(stripos($result['order_sn'],'recharge') !== false)
             {
             	$order = M('recharge')->where("order_sn" , $result['order_sn'])->find();
             	$this->assign('order', $order);
             	if($result['status'] == 1)
             		return $this->fetch('recharge_success');
             	else
             		return $this->fetch('recharge_error');
             	exit();
             }             
            // 先查看一下 是不是 合并支付的主订单号
             $sum_order_amount = M('order')->where("master_order_sn" , $result['order_sn'])->sum('order_amount');
             if($sum_order_amount)
             {
                $this->assign('master_order_sn', $result['order_sn']); // 主订单号
                $this->assign('sum_order_amount', $sum_order_amount); // 所有订单应付金额                        
             }
             else
             {
                $order = M('order')->where("order_sn" ,$result['order_sn'])->find();
                $this->assign('order', $order);
             }            
             
            if($result['status'] == 1)
                return $this->fetch('success');   
            else
                return $this->fetch('error');   
        }


    /**
     * 普通订单余额支付
     */
    public function balancePay(){
        $master_order_sn = I('master_order_sn',''); //主订单号
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
            $order_where['order_id'] = $order_id;
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
		
		if (count($order_list) == 1) {
			$prom_type = $order_list[0]['prom_type'];
			if ($prom_type == 8) {
				$promRes = $this->checkProm($order_list[0]);
				if (!$promRes) {
					$this->ajaxReturn(['status' => 4, 'msg' => '活动已结束']);
				}
			}
		}

        //获取用户实时余额
        $user = Users::get($user['user_id']);
        if($user['user_money'] - $sum_order_amount < 0){
            $this->ajaxReturn(['status' => 0, 'msg' => '余额不足，请充值' ]);
        }

        if (empty($user['paypwd'])) {
            $url = url('Cart/cart4').'?order_id='.$order['order_id'];
            if($master_order_sn) $url = url('Cart/cart4').'?order_sn='.$master_order_sn;
            session('payPriorUrl',$url);
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
				//将应付金额保存为0
				Db::name('order')->where('order_id',$payInfoVal['order_id'])->setField('order_amount', 0);
                //使用余额全额付款，修改状态订单
                update_pay_status($payInfoVal['order_sn']);
                array_push($accountLogAllData, $accountLogData);
            }

            Db::name('account_log')->insertAll($accountLogAllData);

            //支付成功跳转页面
            $this->ajaxReturn(['status' => 1, 'msg' => '支付成功' ,'result'=>$order['order_id'],'prom_type'=>$prom_type]);
    }
	
	/**
	 *检查砍价活动是否结束
	 */
	public function checkProm($order) {
		//砍价活动判断活动是否结束
		$bargainLogic = new \app\common\logic\bargain\BargainLogic();
		$bargainLogic->setBargainId($order['prom_id']);
		$isEnd = $bargainLogic->checkActivityIsEnd();
		if ($isEnd) {
			//过滤掉未支付的订单,订单超时,更改订单状态取消
		   Order::update(['order_status'=>3],['order_id'=>$order['order_id']]);
		   if($order['user_money'] > 0){
			   //自动退款
			   $refundLogic = new RefundLogic();
			   $refundLogic->updateRefundOrder(Order::get(['order_id'=>$order['order_id']]),0);
			   $messageFactory = new \app\common\logic\MessageFactory();
			   $messageLogic = $messageFactory->makeModule(['category' => 2]);
			   $messageLogic->sendRefundNotice($order['order_id'],$order['order_amount']);
		   }
		   return false;
		}
		return true;
	}
              
}
