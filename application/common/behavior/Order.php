<?php

/**
 * User: dyr
 * Date: 2017/11/24 0024
 * Time: 下午 3:00
 */

namespace app\common\behavior;

use think\Db;
use app\common\logic\wechat\WechatUtil;

class Order
{
    public function userAddOrder(&$order)
    {
        // 记录订单操作日志
        $action_info = array(
            'order_id'        =>$order['order_id'],
            'action_user'     =>$order['user_id'],
            'user_type'       =>2,
            'action_note'     => '您提交了订单，请等待系统确认',
            'status_desc'     =>'提交订单', //''
            'log_time'        =>time(),
        );
        Db::name('order_action')->add($action_info);

        //分销开关全局
        if(file_exists(APP_PATH.'common/logic/DistributLogic.php'))
        {
            $distributLogic = new \app\common\logic\DistributLogic();
            $distributLogic->rebateLog($order); // 生成分成记录
            if($order['order_amount'] == 0){
                update_pay_status($order['order_sn'], 1); // 这里刚刚下的订单必须从主库里面去查
            }
        }

        // 如果有微信公众号 则推送一条消息到微信.微信浏览器才发消息，否则下单超时。by清华
        if(is_weixin()){
            $oauth_users = Db::name('oauth_users')->where(['user_id'=>$order['user_id'] , 'oauth'=>'weixin' , 'oauth_child'=>'mp'])->find();
            if ($oauth_users) {
                $wx_content = "你刚刚下了一笔订单:{$order['order_sn']}！";
                $wechat = new WechatUtil;
                $wechat->sendMsg($oauth_users['openid'], 'text', $wx_content);
            }
        }

        //用户下单, 发送短信给商家
        $res = checkEnableSendSms("3");
        if($res && $res['status'] ==1){
            $store = Db::name('store')->where("store_id", $order['store_id'])->find();
            $sender = (!empty($store) && !empty($store['service_phone'])) ? $store['service_phone'] : false;
            $params = array('consignee'=>$order['consignee'] , 'mobile' => $order['mobile']);
            sendSms("3", $sender, $params);
        }

        $message_store = new \app\common\logic\MessageStoreLogic();
        $message_store->setOrder($order, 'new_order')->sendMessage();
    }

}