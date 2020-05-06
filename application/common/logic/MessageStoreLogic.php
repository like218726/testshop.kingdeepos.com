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

class MessageStoreLogic extends MessageBase
{
    protected $storeMsgSetting;     // 店铺配置
    protected $storeMsgTpl;         // 模板配置
    protected $messageSwitch = 1;   // 站内信开关
    protected $mailSwitch = 0;      // 邮箱开关
    protected $shortSwitch = 0;     // 短信开关

    public function sendMessage()
    {
        $this->setMessageSwitchByCode($this->message['smt_code']);
        $storeMsgSetting = $this->storeMsgSetting;
        $storeMsgTpl = $this->storeMsgTpl;
        $data = $this->message;

        // 站内信
        if ($this->messageSwitch == 1 ) {
            if (empty($data['content'])) {
                $this->setContent($storeMsgTpl['smt_message_content']);
            }else{
                $this->setContent($data['content']);
            }
            $this->addStoreMsg();
        }
        // 短信
        if ($this->shortSwitch == 1) {
            $this->setSender($storeMsgSetting['sms_short_number']);
            $this->setShortContent($storeMsgTpl['smt_short_content']);
            $this->sendShort();
        }
        // 邮件
        if ($this->mailSwitch == 1) {
            $this->setMail($storeMsgSetting['sms_mail_number']);
            $this->setMailContent($storeMsgTpl['smt_mail_content']);
            $this->setMailSubject($storeMsgTpl['smt_mail_subject']);
            $this->sendMail();
        }
    }

    /**
     * 添加店消息
     */
    public function addStoreMsg()
    {
        if(empty($this->message['store_id']) or empty($this->message['content'])) return;
        if(!isset($this->message['addtime'])){
            $this->message['addtime'] = time();
        }
        Db::name('store_msg')->add($this->message);
    }

    /**
     * 删除消息
     * @param $sm_id
     * @throws \think\Exception
     */
    public function deletedMessage($sm_id)
    {
        db('store_msg')->where(['sm_id' => $sm_id])->delete();
    }

    /**
     * 发邮件
     * @return array|bool
     */
    public function sendMail()
    {
        $data = $this->message;
        if (empty($data['email']) or empty($data['smt_mail_content'])) {
            return false;
        }
        return send_email($data['email'], $data['smt_mail_subject'], htmlspecialchars_decode($data['smt_mail_content']));
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
        $params['msg'] = $data['smt_short_content']; // 发送短信的内容
        $params['smsParams'] = $data['short_val']; // 变量名和值
        $params['mmt_short_sign'] = $data['smt_short_sign']; // 短信签名
        $params['mmt_short_code'] = $data['smt_short_code']; // 短信模板ID
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


    public function withdrawals($store_id, $money){
        $this->setStoreId($store_id);
        $this->setSmtCode('withdrawals');
        $this->setMessageVal(['money' => $money]);
        return $this;
    }
    /**
     * 设置好order,便可发消息
     * @param $order
     * @param $smt_code
     * @return $this
     */
    public function setOrder($order, $smt_code){
        $this->setStoreId($order['store_id']);
        $this->setSmtCode($smt_code);
        $this->setMessageVal(['order_sn' => $order['order_sn']]);
        return $this;
    }

    /**
     * 根据模板编码 设置发消息的开关
     * @param $smt_code
     */
    public function setMessageSwitchByCode($smt_code)
    {

        $where['smt_code'] = $smt_code;
        $storeMsgTpl = Db::name('store_msg_tpl')->where($where)->find();
        $storeMsgSetting = Db::name('store_msg_setting')->where($where)->find();
        if($storeMsgTpl){
            $this->setMessageSwitch($storeMsgTpl['smt_message_switch']);
            $this->setShortSwitch($storeMsgTpl['smt_short_switch']);
            $this->setMailSwitch($storeMsgTpl['smt_mail_switch']);
        }
        if($storeMsgSetting){
            $this->storeMsgSetting = $storeMsgSetting;
            $this->setMessageSwitch($storeMsgSetting['sms_message_switch']);
            $this->setShortSwitch($storeMsgSetting['sms_short_switch']);
            $this->setMailSwitch($storeMsgSetting['sms_mail_switch']);
        }
        if($storeMsgTpl){
            $this->storeMsgTpl = $storeMsgTpl;
            if($storeMsgTpl['smt_message_forced'] == 1){
                $this->setMessageSwitch(1);
            }
            if($storeMsgTpl['smt_short_forced'] == 1){
                $this->setShortSwitch(1);
            }
            if($storeMsgTpl['smt_mail_forced'] == 1){
                $this->setMailSwitch(1);
            }
        }
    }

    /**
     * 站内信开关
     * @param $value
     */
    public function setMessageSwitch($value){
        $this->messageSwitch = $value;
    }
    /**
     * 短信开关
     * @param $value
     */
    public function setShortSwitch($value){
        $this->shortSwitch = $value;
    }
    /**
     * 邮件开关
     * @param $value
     */
    public function setMailSwitch($value){
        $this->mailSwitch = $value;
    }


    /**
     * 必填
     * @param $value
     */
    public function setContent($value){
        if(!empty($this->message['message_val'])){
            $value = $this->getContent($this->message['message_val'], $value);
        }
        $this->message['content'] = $value;
    }
    /**
     * @param $value
     */
    public function setAddTime($value){
        $this->message['addtime'] = $value;
    }
    /**
     * @param $value
     */
    public function setOpen($value){
        $this->message['open'] = $value;
    }

    public function setMailSubject($value){
        if(!empty($this->message['message_val'])){
            $value = $this->getContent($this->message['message_val'], $value);
        }
        $this->message['smt_mail_subject'] = $value;
    }
    public function setMailContent($value){
        if(!empty($this->message['message_val'])){
            $value = $this->getContent($this->message['message_val'], $value);
        }
        $this->message['smt_mail_content'] = $value;
    }
    public function setShortSign($value){
        $this->message['smt_short_sign'] = $value;
    }
    public function setShortCode($value){
        $this->message['smt_short_code'] = $value;
    }
    public function setShortContent($value){
        if(!empty($this->message['message_val'])){
            $value = $this->getContent($this->message['message_val'], $value);
        }
        $this->message['smt_short_content'] = $value;
    }
    public function setMail($value){
        $this->message['mail'] = $value;
    }
    public function setSender($value){
        $this->message['sender'] = $value;
    }
    public function setSmtCode($value){
        $this->message['smt_code'] = $value;
    }

}