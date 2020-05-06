<?php
/**
 * tpshop 支付宝插件
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: IT宇宙人
 * Date: 2019-04-08
 */
 
use think\Model; 
use think\Request;
use app\admin\logic\RefundLogic;

/**
 * 支付 逻辑定义
 * Class AlipayPayment
 * @package Home\Payment
 */

class newalipayMobile extends Model
{
	public $alipay_config = array();// 支付宝支付配置参数
	private $config = null;

	public function  __construct() 
	{            
		$this->config = \Think\Config::get('shop_info');
		$paymentPlugin = M('Plugin')->where("code='newalipayMobile' and  type = 'payment' ")->find(); // 找到支付插件的配置
		$config_value = unserialize($paymentPlugin['config_value']); // 配置反序列化
		$this->alipay_config['app_id']= $config_value['app_id']; // 支付宝分配给开发者的应用ID
		$this->alipay_config['merchant_private_key']= $config_value['merchant_private_key']; // 商户私钥
		$this->alipay_config['notify_url']= SITE_URL.U('Payment/notifyUrl',array('pay_code'=>'newalipayMobile')); //异步通知地址
		$this->alipay_config['return_url']= SITE_URL.U('Payment/returnUrl',array('pay_code'=>'newalipayMobile')); //同步跳转
		$this->alipay_config['charset']= "UTF-8"; //编码格式
		$this->alipay_config['sign_type']= "RSA2"; //签名方式
		$this->alipay_config['gatewayUrl']= "https://openapi.alipay.com/gateway.do"; //支付宝网关
		$this->alipay_config['alipay_public_key']= $config_value['alipay_public_key']; //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
	}

	/**
	* 生成支付代码
	* @param   array   $order      订单信息
	* @param   array   $config_value    支付方式信息
	*/
    function get_code($order, $config_value)
    {
    	require_once 'wappay/service/AlipayTradeService.php';
		require_once 'wappay/buildermodel/AlipayTradeWapPayContentBuilder.php';
	
		if (!empty($order['order_sn'])&& trim($order['order_sn'])!=""){

		    $out_trade_no = $order['order_sn'];//商户订单号，商户网站订单系统中唯一订单号，必填
		    $total_amount = $order['order_amount'];//付款金额，必填
		    $subject = $config_value['body'];//订单名称，必填
		    $timeout_express="1m";//超时时间
		    
		    !$subject && $subject = "{$this->config['copyright']}商品" ;

		    $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
		    $payRequestBuilder->setSubject($subject);
		    $payRequestBuilder->setOutTradeNo($out_trade_no);
		    $payRequestBuilder->setTotalAmount($total_amount);
		    $payRequestBuilder->setTimeExpress($timeout_express);

		    $payResponse = new AlipayTradeService($this->alipay_config);
		    $result=$payResponse->wapPay($payRequestBuilder,$this->alipay_config['return_url'],$this->alipay_config['notify_url']);
		    
		    return ;
		}
    }

	/**
	* 服务器点对点响应操作给支付接口方调用
	* 
	*/
    function response()
    {

    	require_once 'wappay/service/AlipayTradeService.php';

    	$arr=$_POST;
		$alipaySevice = new AlipayTradeService($this->alipay_config); 
		$alipaySevice->writeLog(var_export($_POST,true));
		$result = $alipaySevice->check($arr);

		if($result) {//验证成功
			
			$order_sn = $out_trade_no = $_POST['out_trade_no'];//商户订单号
			$trade_no = $_POST['trade_no'];//支付宝交易号
			$trade_status = $_POST['trade_status'];//交易状态

			//用户在线充值
			if (stripos($order_sn, 'recharge') !== false)
				$order_amount = M('recharge')->where(['order_sn' => $order_sn, 'pay_status' => 0])->value('account');
			else
                $order_amount = M('order')->where(['master_order_sn'=>"$order_sn"])->whereOr(['order_sn'=>"$order_sn"])->value('order_amount');									
            if($order_amount!=$_POST['total_amount']) 
            	exit("fail"); //验证失败  

		    if($_POST['trade_status'] == 'TRADE_FINISHED') 
		    {
				update_pay_status($order_sn, $trade_no); // 修改订单支付状态
		    }
		    else if ($_POST['trade_status'] == 'TRADE_SUCCESS') 
		    {
				update_pay_status($order_sn, $trade_no); // 修改订单支付状态
		    }
		        
			echo "success";//请不要修改或删除
				
		}else{
		    //验证失败
		    echo "fail";//请不要修改或删除

		}
    }

 	function respond2()
    {
    	require_once 'wappay/service/AlipayTradeService.php';

    	$arr=$_GET;
		$alipaySevice = new AlipayTradeService($this->alipay_config); 
		$result = $alipaySevice->check($arr);

		if($result) {//验证成功

			$order_sn = $out_trade_no = htmlspecialchars($_GET['out_trade_no']);//商户订单号
			$trade_no = htmlspecialchars($_GET['trade_no']);//支付宝交易号

			return array('status'=>1,'order_sn'=>$order_sn);//跳转至成功页面
		}else {
		    //验证失败
		    $out_trade_no = htmlspecialchars($_GET['out_trade_no']);//商户订单号
		    return array('status'=>0,'order_sn'=>$out_trade_no);//跳转至失败页面
		}
    }

}   