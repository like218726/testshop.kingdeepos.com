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

class newalipay extends Model
{
	public $alipay_config = array();// 支付宝支付配置参数
	private $config = null;

	public function  __construct() 
	{            
		$this->config = \Think\Config::get('shop_info');
		$paymentPlugin = M('Plugin')->where("code='newalipay' and  type = 'payment' ")->find(); // 找到支付插件的配置
		$config_value = unserialize($paymentPlugin['config_value']); // 配置反序列化   
		$this->alipay_config['app_id']= $config_value['app_id']; // 支付宝分配给开发者的应用ID
		$this->alipay_config['merchant_private_key']= $config_value['merchant_private_key']; // 商户私钥
		$this->alipay_config['notify_url']= SITE_URL.U('Payment/notifyUrl',array('pay_code'=>'newalipay')); //异步通知地址
		$this->alipay_config['return_url']= SITE_URL.U('Payment/returnUrl',array('pay_code'=>'newalipay')); //同步跳转
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
    	require_once 'pagepay/service/AlipayTradeService.php';
		require_once 'pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';

	    $out_trade_no = trim($order['order_sn']);//商户订单号，商户网站订单系统中唯一订单号，必填
	    $total_amount = trim($order['order_amount']);//付款金额，必填
	    $subject = trim($config_value['body']); //订单名称，必填

	    !$subject && $subject = "{$this->config['copyright']}商品" ;

		//构造参数
		$payRequestBuilder = new AlipayTradePagePayContentBuilder();
		$payRequestBuilder->setSubject($subject);
		$payRequestBuilder->setTotalAmount($total_amount);
		$payRequestBuilder->setOutTradeNo($out_trade_no);

		$aop = new AlipayTradeService($this->alipay_config);

		/**
		 * pagePay 电脑网站支付请求
		 * @param $builder 业务参数，使用buildmodel中的对象生成。
		 * @param $return_url 同步跳转地址，公网可以访问
		 * @param $notify_url 异步通知地址，公网可以访问
		 * @return $response 支付宝返回的信息
	 	*/
		$response = $aop->pagePay($payRequestBuilder,$this->alipay_config['return_url'],$this->alipay_config['notify_url']);
    }

	/**
	* 服务器点对点响应操作给支付接口方调用
	* 
	*/
    function response()
    {
		require_once 'pagepay/service/AlipayTradeService.php';

		$arr=$_POST;
		$alipaySevice = new AlipayTradeService($this->alipay_config); 
		$alipaySevice->writeLog(var_export($_POST,true));
		$result = $alipaySevice->check($arr);

		/* 实际验证过程建议商户添加以下校验。
		1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
		2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
		3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
		4、验证app_id是否为该商户本身。
		*/

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

			if($_POST['trade_status'] == 'TRADE_FINISHED') {
				update_pay_status($order_sn, $trade_no); // 修改订单支付状态
		    }
		    else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
		    	update_pay_status($order_sn, $trade_no); // 修改订单支付状态
		    }
			echo "success";	//请不要修改或删除
		}else {
		    //验证失败
		    echo "fail";

		}
    }

 	function respond2()
    {
    	require_once 'pagepay/service/AlipayTradeService.php';

		$arr=$_GET;
		$alipaySevice = new AlipayTradeService($this->alipay_config);
		$result = $alipaySevice->check($arr);

		/* 实际验证过程建议商户添加以下校验。
		1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
		2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
		3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
		4、验证app_id是否为该商户本身。
		*/
		if($result) {//验证成功

			$order_sn = $out_trade_no = htmlspecialchars($_GET['out_trade_no']); //商户订单号
			$trade_no = htmlspecialchars($_GET['trade_no']);//支付宝交易号

			return array('status'=>1,'order_sn'=>$order_sn);//跳转至成功页面			
		}
		else {
			$out_trade_no = htmlspecialchars($_GET['out_trade_no']); //商户订单号
		    return array('status'=>0,'order_sn'=>$out_trade_no);//跳转至失败页面
		}
    }

    /**
    *支付宝无密退款新接口
    */
    public function payment_refund($data) 
    {
    	require_once 'pagepay/service/AlipayTradeService.php';
		require_once 'pagepay/buildermodel/AlipayTradeRefundContentBuilder.php';

	    // $out_trade_no = trim($data['out_trade_no']);//商户订单号，商户网站订单系统中唯一订单号
	    $trade_no = trim($data['trade_no']); //支付宝交易号  //请二选一设置
	    $refund_amount = trim($data['refund_amount']); //需要退款的金额，该金额不能大于订单金额，必填
	    $refund_reason = trim($data['refund_reason']);//退款的原因说明
	    $out_request_no = trim($data['WIDTRout_request_no']);//标识一次退款请求，同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传

	    //构造参数
		$RequestBuilder=new AlipayTradeRefundContentBuilder();
		// $RequestBuilder->setOutTradeNo($out_trade_no);
		$RequestBuilder->setTradeNo($trade_no);
		$RequestBuilder->setRefundAmount($refund_amount);
		$RequestBuilder->setOutRequestNo($out_request_no);
		$RequestBuilder->setRefundReason($refund_reason);

		$aop = new AlipayTradeService($this->alipay_config);
		
		/**
		 * alipay.trade.refund (统一收单交易退款接口)
		 * @param $builder 业务参数，使用buildmodel中的对象生成。
		 * @return $response 支付宝返回的信息
		 */
		$response = $aop->Refund($RequestBuilder);
	
		if ($response->code == 10000 && $response->msg == "Success") {
			
			$refundLogic = new RefundLogic();

			if ($data['type'] == 1) {
				$order = M('order')->where(array('order_id'=>$data['order_id']))->find();
   				$refundLogic->updateRefundOrder($order);//订单整单申请原路退款
			} else {
				$refundLogic->updateRefundGoods($data['order_id']);//订单商品售后退款原路退回
			}
			return array('status'=>10000);
		} else {
			return array('status'=>0, 'code'=>$response->code , 'msg'=>$response->msg , 'sub_msg'=>$response->sub_msg );
		}

	}

	/**
	 * 单笔转账到支付宝账户接口
	 * @param $data
	 */
	public function transfer($data)
	{
		require_once 'aop/AopClient.php';
		require_once 'aop/request/AlipayFundTransToaccountTransferRequest.php';

		$aop = new \AopClient ();
		$aop->gatewayUrl = $this->alipay_config['gatewayUrl'];
		$aop->appId = $this->alipay_config['app_id'];//your app_id;
		$aop->rsaPrivateKey = $this->alipay_config['merchant_private_key'];//请填写开发者私钥去头去尾去回车，一行字符串;
		$aop->alipayrsaPublicKey=$this->alipay_config['alipay_public_key'];//请填写支付宝公钥，一行字符串;
		$aop->apiVersion = '1.0';
		$aop->signType = 'RSA2';
		$aop->postCharset="UTF-8";
		$aop->format='json';

		$request = new \AlipayFundTransToaccountTransferRequest ();
		$request->setBizContent("{" .
				"\"out_biz_no\":\"".$data['out_biz_no']."\"," .
				"\"payee_type\":\"ALIPAY_LOGONID\"," .
				"\"payee_account\":\"".$data['payee_account']."\"," .
				"\"amount\":\"".$data['amount']."\"," .
				"\"payer_show_name\":\"\"," .
				"\"payee_real_name\":\"".$data['payee_real_name']."\"," .
				"\"remark\":\"".$data['remark']."\"" .
				"  }");

		$result = $aop->execute ( $request);
		//打印转账日志
		$this->writeLog(print_r($result, true));

		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
			return array('status'=>10000, 'out_biz_no'=>$result->$responseNode->out_biz_no);
		} else {
			return array('status'=>0, 'code'=>$resultCode , 'msg'=>$result->$responseNode->msg , 'sub_code'=>$result->$responseNode->sub_code, 'sub_msg'=>$result->$responseNode->sub_msg, 'out_biz_no'=>$result->$responseNode->out_biz_no);
		}
	}

	/**
	 * 请确保项目文件有可写权限，不然打印不了日志。
	 */
	function writeLog($text) {
		file_put_contents (dirname ( __FILE__ ).DIRECTORY_SEPARATOR. "./log.txt", date ( "Y-m-d H:i:s" ) . "  " . $text .PHP_EOL, FILE_APPEND );
	}

}   