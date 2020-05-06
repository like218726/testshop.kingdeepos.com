<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: yhj
 * Date: 2018-08-01
 */

namespace app\common\logic;

use think\Model;
use think\db;

/**
 * 消息基础
 * Class CatsLogic
 * @package admin\Logic
 */
class MessageBase extends Model
{
    protected $message;//消息模型
    protected $userMsgTpl;
    public function __construct($message=[])
    {
        parent::__construct();
        $this->message = $message;
    }

    /**
     * 重新设置发送的消息内容
     * @param array $send_data
     */
    public function setSendData($send_data=[])
    {
        if (!empty($send_data)) {
            $this->message = array_merge($this->message, $send_data);
            if(!empty($send_data['users'])){
                $this->message['users'] = $send_data['users'];
            }
        }
    }

    /**
     * 发消息,
     */
    public function sendMessage()
    {
        $this->setMessageSwitchByCode($this->message['mmt_code']);
        $userMsgTpl = $this->userMsgTpl;
        $data = $this->message;
        // 站内信
        if ($data['mmt_message_switch'] == 1) {
            if (empty($data['message_content'])) {
                $this->setMessageContent($userMsgTpl['mmt_message_content']);
            }else{
                $this->setMessageContent($this->message['message_content']);
            }
            if (empty($data['message_title'])) {
                $this->setMessageTitle($userMsgTpl['mmt_name']);
            }else{
                $this->setMessageTitle($this->message['message_title']);
            }

            if (!isset($data['send_time'])) {
                $this->setSendTime(time());
            }

            if (isset($this->message['message_id'])){
                unset($this->message['message_id']);
            }

            $this->addMessage();
            $this->sendUserMessage();
        }
        // 短信
        if ($data['mmt_short_switch'] == 1) {
            $this->setShortContent($userMsgTpl['mmt_short_content']);
            $this->sendShort();
        }
        // 邮件
        if ($data['mmt_mail_switch'] == 1) {
            $this->setMailContent($userMsgTpl['mmt_mail_content']);
            $this->setMailSubject($userMsgTpl['mmt_mail_subject']);
            $this->sendMail();
        }
        // 极光推送
        $this->send_jiguang();
    }

    /**
     * 极光推送
     */
    public function send_jiguang(){
        $message_title = substr($this->message['message_title'],0,64);
        $message_content = substr($this->message['message_content'],0,64); // 极光推送手机显示内容的不多。
        if(strlen($message_content) < 6) return; // 空内容直接返回。
        if(empty($this->message['users'])){
            // 极光广播发送
            $push = new \app\common\logic\PushLogic();
            $push->push($message_content,['title'=>$message_title]); // 向全部发送
        }else{
            // 极光指定用户发送
            $push_ids = Db::name('users')->where('user_id','in',$this->message['users'])->where('push_id','>',0)->column('push_id');
            if($push_ids){
                $push = new \app\common\logic\PushLogic();
                $push->push($message_content,['title'=>$message_title],0,$push_ids);
            }
        }
    }

    /**
     * 添加一条消息,不同类型消息表不同
     */
    public function addMessage(){
        db('message_notice')->insert($this->message);
        $message_id = db('message_notice')->getLastInsID();
        if($message_id) {
            $this->message['message_id'] = $message_id;
        }
    }

    /**
     * 发短信
     * @return bool
     */
    public function sendShort()
    {
        $data = $this->message;
        if (empty($data['sender']) or empty($data['smt_short_sign']) or empty($data['smt_short_code'])) {
            return false;
        }
        $params['msg'] = $data['mmt_short_content']; // 发送短信的内容
        $params['smsParams'] = $data['short_val']; // 变量名和值
        $params['mmt_short_sign'] = $data['mmt_short_sign']; // 短信签名
        $params['mmt_short_code'] = $data['mmt_short_code']; // 短信模板ID
        $params['mmt_code'] = empty($data['scene']) ? $data['smt_code'] : $data['scene']; // 场景值，为兼容以前
        $smsLogic = new SmsLogic();
        if (is_array($data['sender'])) {
            foreach ($data['sender'] as $sender) {
                $params['sender'] = $sender; // 发送者手机号
                $smsLogic->sendMsg($params);
            }
        }else{
            $params['sender'] = $data['sender']; // 发送者手机号
            $smsLogic->sendMsg($params);
        }
        return true;
    }

    /**
     * 发邮件
     * @return bool
     */
    public function sendMail()
    {
        $data = $this->message;
        if (!empty($data['users'])) {
            $where['user_id'] = ['in', $data['users']];
            $where['email_validated'] = 1;
            $data['email'] = Db::name('users')->where($where)->column('email');
        }
        if (empty($data['email'])) {
            return false;
        }
        return send_email($data['email'], $data['mmt_mail_subject'], htmlspecialchars_decode($data['mmt_mail_content']));
    }

    /**
     * 判断用户有没有收藏店铺,可能多次调用
     * @param $user_id
     * @param $store_id
     * @return bool|mixed
     */
    public function hasCollectStore($user_id, $store_id)
    {
        static $arr = [];
        if (empty($user_id) or empty($store_id)) {
            return false;
        }
        if (isset($arr[$user_id . '_' . $store_id])) {
            return $arr[$user_id . '_' . $store_id];
        }
        $log_id = Db::name('store_collect')->where(['user_id' => $user_id, 'store_id' => $store_id])->value('log_id');
        $arr[$user_id . '_' . $store_id] = $log_id;
        return $log_id;
    }

    /**
     * 删除没关注店铺的用户id
     */
    public function deleteUserNoCollectStore()
    {
        if (!empty($this->message['store_id']) && !empty($this->message['users'])){
            foreach ($this->message['users'] as $k => $user_id) {
                if (!$this->hasCollectStore($user_id, $this->message['store_id'])){
                    unset($this->message['users'][$k]);
                }
            }
        }
    }

    /**
     * 把消息发送给用户，站内信通用
     */
    protected function sendUserMessage()
    {
        $data = $this->message;
        if (!empty($data['users']) && !empty($data['message_id'])) {
            foreach ($data['users'] as $user_id) {
                db('user_message')->insert(array('user_id' => $user_id, 'message_id' => $data['message_id'], 'category' => $data['category']));
            }
        }
    }

    /**
     * 模板内容处理
     * @param $data |发送内容变量值
     * @param $str |模板内容支持变量 {$name} or ${name}
     * @return mixed
     */
    protected function getContent($data, $str)
    {
        if (!empty($data) && !empty($str)){
            foreach ($data as $k => $v) {
                $str = str_replace('{$' . $k . '}', $v, $str);
                $str = str_replace('${' . $k . '}', $v, $str);
            }
        }
        return $str;
    }

    /**
     * 根据模板编码 设置发消息的开关
     * @param $mmt_code
     */
    public function setMessageSwitchByCode($mmt_code){
        $data = $this->message;
        $arr = db('member_msg_tpl')->where('mmt_code', $mmt_code)->find();
        if ($arr) {
            if(!isset($data['mmt_message_switch'])){
                $this->setMessageSwitch($arr['mmt_message_switch']);
            }
            if(!isset($data['mmt_short_switch'])){
                $this->setShortSwitch($arr['mmt_short_switch']);
            }
            if(!isset($data['mmt_mail_switch'])){
                $this->setMailSwitch($arr['mmt_mail_switch']);
            }
            $this->userMsgTpl = $arr;
        }
    }

    /**
     * 站内信开关
     * @param $value
     */
    public function setMessageSwitch($value){
        $this->message['mmt_message_switch'] = $value;
    }
    /**
     * 短信开关
     * @param $value
     */
    public function setShortSwitch($value){
        $this->message['mmt_short_switch'] = $value;
    }
    /**
     * 邮件开关
     * @param $value
     */
    public function setMailSwitch($value){
        $this->message['mmt_mail_switch'] = $value;
    }
    /**
     * 如果有变量，则替换
     * @param $value
     */
    public function setMessageTitle($value){
        $value = $this->getContent($this->message['message_val'], $value);
        $this->message['message_title'] = $value;
    }
    /**
     * 如果有变量，则替换
     * @param $value
     */
    public function setMessageContent($value){
        $value = $this->getContent($this->message['message_val'], $value);
        $this->message['message_content'] = $value;
    }
    /**
     * 必填
     * @param $value
     */
    public function setSendTime($value){
        $this->message['send_time'] = $value;
    }
    /**
     * 向用户发消息,可以空
     * @param $array | [1,2,3]
     */
    public function setUsers($array){
        $this->message['users'] = $array;
    }
    /**
     * 模板变量名和值,可以空,也用于短信
     * @param $array |['name'=>'value']
     */
    public function setMessageVal($array){
        $this->message['message_val'] = $array;
    }
    /**
     * 必填
     * @param $value
     */
    public function setStoreId($value){
        $this->message['store_id'] = $value;
    }

    /**
     * 邮件标题 如果有变量，则替换
     * @param $value
     */
    public function setMailSubject($value){
        $value = $this->getContent($this->message['message_val'], $value);
        $this->message['mmt_mail_subject'] = $value;
    }
    /**
     * 邮件内容 如果有变量，则替换
     * @param $value
     */
    public function setMailContent($value){
        $value = $this->getContent($this->message['message_val'], $value);
        $this->message['mmt_mail_content'] = $value;
    }
    public function setShortSign($value){
        $this->message['mmt_short_sign'] = $value;
    }
    public function setShortCode($value){
        $this->message['mmt_short_code'] = $value;
    }
    /**
     * 短信内容 如果有变量，则替换
     * @param $value
     */
    public function setShortContent($value){
        $value = $this->getContent($this->message['message_val'], $value);
        $this->message['mmt_short_content'] = $value;
    }
    /**
     * 接收的邮箱
     * @param $value
     */
    public function setMail($value){
        $this->message['mail'] = $value;
    }
    /**
     * 接收的手机号
     * @param $value
     */
    public function setSender($value){
        $this->message['sender'] = $value;
    }
    /**
     * 发短信场景值，设置前，先设置setMessageVal,默认模板编号
     * @param $value
     */
    public function setScene($value){
        $data = [
            1 => ['code'=>1234],                                        //1. 用户注册 (验证码类型短信只能有一个变量)
            2 => ['code'=>1234],                                        //2. 用户找回密码 (验证码类型短信只能有一个变量)
            3 => ['consignee'=>'$consignee ','phone'=>'$phone'],        //3. 客户下单
            4 => ['orderId'=>'$order_id'],                              //4. 客户支付
            5 => ['userName'=>'$user_name', 'consignee'=>'$consignee'], //5. 商家发货
            6 => ['code'=>1234]
        ];
        $arr = $data[$value];
        if(!empty($arr)){
            $message_val = [];
            foreach ($arr as $key=>$v) {
                $message_val[$key] = $this->message['message_val'][$key];
            }
            $this->setShortVal($message_val);
        }
        $this->message['scene'] = $value;
    }

    /**
     * 设置发送短信的变量和值
     * @param $array
     */
    public function setShortVal($array){
        $this->message['short_val'] = $array;
    }
}