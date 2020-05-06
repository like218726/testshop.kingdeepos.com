<?php
/**
 * tpshop 银联支付插件
 * ============================================================================
 * 版权所有 2015-2027 齐齐哈尔奇闻科技有限公司，并保留所有权利。
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: yhj
 * Date: 2018-06-14
 */
namespace app\api\controller;
use think\Db;
use think\Model; 
use think\Request;


class Unionpay extends Base
{    
     
    public $unionpay_config = array();// 银联支付配置参数
    
    /**
     * 析构流函数
     */
    public function  __construct() {   
        parent::__construct(); 
    
        $paymentPlugin = M('Plugin')->where("code='unionpay' and  type = 'payment' ")->find(); // 找到支付插件的配置
        $config_value = unserialize($paymentPlugin['config_value']); // 配置反序列化 
    
        $this->unionpay_config['unionpay_mid']= $config_value['unionpay_mid']; // 商户号
        $this->unionpay_config['unionpay_cer_password']       = $config_value['unionpay_cer_password'];// 商户私钥证书密码

       
        include_once  PLUGIN_PATH.'payment/unionpay/sdk/acp_service.php'; // 小茶宝是sdk5 本地是sdk
        include_once  PLUGIN_PATH.'payment/unionpay/sdk/SDKConfig.php';
        \SDKConfig::getSDKConfig();
        \SDKConfig::setSignCertPwd($config_value['unionpay_cer_password']);
    } 

    /**
     * 统一下单
     */
    public function dopay()
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-type: text/plain');

        $is_rechange = false;
        $user = session('user');
        $order_sn = input('order_sn', '');
        $trade_type = input('trade_type', ''); //支付终端方式app 小程序
        if (strpos($order_sn, 'recharge') === 0 || $order_sn === '') {
            $is_rechange = true;
            //充值流程
            $total = input('account/f', 0);
            if ($total <= 0) {
                $this->ajaxReturn(['status'=>-1, 'msg'=>'充值金额不能为'.$total]);
            }
            if ($order_sn) {
                $order = M('recharge')->where("order_sn", $order_sn)->find();
                if (!$order) {
                    $this->ajaxReturn(['status'=>-1, 'msg'=>'该充值订单不存在']);
                }
                M('recharge')->where(['order_sn'=>$order_sn,'user_id'=>$user['user_id']])->save(['account' => $total]);
            } else {
                $order_sn = 'recharge'.get_rand_str(10,0,1);
                $order['user_id'] = $user['user_id'];
                $order['nickname'] = $user['nickname'];
                $order['account'] = $total;
                $order['order_sn'] = $order_sn;
                $order['pay_name'] = '银联APP支付';
                $order['ctime'] = time();
                M('recharge')->add($order);
            }
        } else {
            //支付流程
            $order = M('order')->where(array('order_sn'=>$order_sn))->find();
            if(!$order){
                $this->ajaxReturn([ 'status'=>-1, 'msg'=>'该订单不存在']);
            }
            $total = $order['order_amount'];
        }
        $order['order_amount'] = $total;
        $result = $this->get_code($order);
        if (is_array($result)) {
            unset($result['signPubKeyCert']); //这个太长，删除掉
            $res = array('msg'=>'获取成功','status'=>1,'result'=>$result);
        }else{
            $res = array('msg'=>'获取失败','status'=>-1,'result'=>$result);
        }
        header('Content-type: application/json');
        $this->ajaxReturn($res);
    }

    /**
     * 生成支付代码
     * @param $order
     * @return mixed
     */

    function get_code($order)
    {    
        $txnTime = empty($order['add_time']) ? time() : $order['add_time'];
		$params = array(
			//以下信息非特殊情况不需要改动
			'version' => '5.1.0',                 //版本号
			'encoding' => 'utf-8',				  //编码方式
            'bizType' => '000201',                //业务类型
            'txnTime' => date('YmdHis', $txnTime), //订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，
            // 'backUrl' => SITE_URL.U('Home/Payment/notifyUrl',array('pay_code'=>'unionpay')),   //后台通知地址
            'backUrl' => SITE_URL.'/index.php/Api/Unionpay/notify',
            'currencyCode' => '156',              //交易币种，境内商户固定156
            'txnAmt' =>(int)( $order['order_amount']*100),  //交易金额，单位分，此处默认取demo演示页面传递的参数
			'txnType' => '01',				      //交易类型
			'txnSubType' => '01',				  //交易子类
            'accessType' => '0',                  //接入类型
			'signMethod' => '01',	              //签名方法
			'channelType' => '08',	              //渠道类型，07-PC，08-手机
			
			//TODO 以下信息需要填写
			'merId' => $this->unionpay_config['unionpay_mid'],//商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
			'orderId' => $order['order_sn'],	//商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
		);
		//建立请求
        \AcpService::sign ( $params );

        $uri = \SDKConfig::getSDKConfig()->appTransUrl;
        // $html_form = AcpService::createAutoFormHtml( $params, $uri );
        $result_arr = \AcpService::post ( $params, $uri);
		return $result_arr;
    }
    
    /**
     * 服务器点对点响应操作给支付接口方调用
     * 
     */
    function notify()
    {                

        $verify_result = AcpService::validate( $_POST );
        if($verify_result) //验证成功
        {
            $order_sn = $out_trade_no = $_POST['orderId']; //商户订单号                    
            $queryId = $_POST['queryId']; //银联支付流水号                   
            $respMsg = $_POST['respMsg']; //交易状态
            
            // 解释: 交易成功且结束，即不可再做任何操作。 银联返回的是 成功[0000000] ，后面会返回 success
            if( stripos($respMsg, '成功') !== false)
            {
                //用户在线充值
                if (stripos($order_sn, 'recharge') !== false){
                    $order_amount = M('recharge')->where(['order_sn' => $order_sn, 'pay_status' => 0])->value('account');
                } else {
                    $order_amount = M('order')->where(['order_sn' => $order_sn])->value('order_amount');
                }
                if((string)($order_amount * 100) != (string)$_POST['txnAmt']) {
                    exit('fail'); //验证失败
                }
                update_pay_status($order_sn,array('transaction_id'=>$queryId)); // 修改订单支付状态
            }
            echo "success"; // 处理成功
        }
        else 
        {                
            echo "fail"; //验证失败                                
        }
    }
}