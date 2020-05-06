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

use app\common\model\CouponList;
use think\Model;
use think\db;

/**
 * 通知消息逻辑定义
 * Class CatsLogic
 * @package admin\Logic
 */
class MessageNoticeLogic extends MessageBase
{
    /**
     * 添加一条通知消息
     */
    public function addMessage(){
        $this->message['category'] = 0;
        db('message_notice')->insert($this->message);
        $message_id = db('message_notice')->getLastInsID();
        if($message_id) {
            $this->message['message_id'] = $message_id;
        }
    }
    /**
     * 需要判断用户有没有关注该店铺
     */
    protected function sendUserMessage()
    {
        // 带有店铺id,则删除没关注店铺的用户
        if (!empty($data['store_id'])) {
            $this->deleteUserNoCollectStore();
        }
        parent::sendUserMessage();
    }

    /**
     * 删除消息
     * @param $prom_id |活动id
     * @param $type |活动类型
     * @throws \think\Exception
     */
    public function deletedMessage($prom_id, $type)
    {
        $message_id = db('message_notice')->where(['prom_id' => $prom_id, 'type' => $type])->value('message_id');
        if ($message_id) {
            db('message_notice')->where(['prom_id' => $prom_id, 'type' => $type])->delete();
            db('user_message')->where(['message_id' => $message_id, 'category' => 0])->delete();
        }
    }

    /**
     * 获取编号
     * @param $type
     * @return string
     */
    public function getCodeByType($type)
    {
        switch ($type) {
            case 0:
                $mmt_code = 'message_notice';
                break;
            case 1:
                $mmt_code = '';
                break;
            case 2:
                $mmt_code = 'coupon_get_notice';
                break;
            case 3:
                $mmt_code = 'coupon_use_notice';
                break;
            case 4:
                $mmt_code = 'coupon_will_expire_notice';
                break;
            case 5:
                $mmt_code = '';
                break;
            case 6:
                $mmt_code = 'withdrawals_notice';
                break;
            default:
                $mmt_code = '';
                break;
        }
        return $mmt_code;
    }

    /**
     * 提现到账消息,多次调用
     * @param $withdrawals_id
     * @param $uid
     * @param $money
     */
    public function withdrawalsNotice($withdrawals_id, $uid, $money)
    {
        $this->setType(6);
        $this->setMessageTitle('提现已到账');
        $this->setMessageContent(''); // 空着，使用模板内容
        $this->setPromId($withdrawals_id);
        $this->setUsers([$uid]);
        $this->setMessageVal(['money' => $money]);
        $this->setStoreId(0);
        $this->sendMessage();
    }
    /**
     * 发放优惠券到账消息
     * @param $cid
     * @param $uid_arr
     */
    public function getCouponNotice($cid, $uid_arr)
    {
        $this->setPromId($cid);
        $this->setType(2);
        $this->setMessageTitle('优惠券已到账');
        $name = db('coupon')->where('id', $cid)->value('name');
        $this->setMessageContent($name);
        $this->setStoreId(0);
        foreach ($uid_arr as $user_id) {
            $this->setUsers([$user_id]);
            $this->sendMessage();
        }
    }

    /**
     * 删除优惠券消息
     * @param $cid
     * @throws \think\Exception
     */
    public function delCouponNotice($cid)
    {
        $uid = db('coupon_list')->where('cid', $cid)->value('uid');
        if ($uid) {
            $where['type'] = 2;
            $where['prom_id'] = $cid;
            $message_id = db('message_notice')->where($where)->column('message_id');
            if ($message_id) {
                db('message_notice')->where($where)->delete();
                db('user_message')->where(['message_id' => ['in', $message_id], 'category' => 0])->delete();
            }
        }
    }

    /**
     * 优惠券将过期
     * @param user_id
     */
    public function couponWillExpire($user_id)
    {
        $where_list['uid'] = $user_id;
        $where_list['status'] = 0;
        $couponList = new CouponList();
        $coupon_list = $couponList->where($where_list)->select();
        foreach ($coupon_list as $coupon) {
            if ($coupon->coupon->is_expiring_notice == 1
                && $coupon->coupon->use_end_time > time()
                && $coupon->coupon->status == 1) {
                $this->couponWillExpireNotice($coupon['cid'], $user_id);
            }
        }

    }

    /**
     * 发送优惠券将过期消息
     * @param $cid
     * @param $user_id
     */
    public function couponWillExpireNotice($cid, $user_id)
    {
        // 谁没有领
        $where = [
            'message_type' => 0,
            'type' => 4,
            'prom_id' => $cid
        ];
        $message_id_arr = db('message_notice')->where($where)->column('message_id');
        $where_message['category'] = 0;
        $where_message['user_id'] = $user_id;
        $where_message['message_id'] = ['in', $message_id_arr];
        $message_id = db('user_message')->where($where_message)->value('message_id');
        if ($message_id) {
            return;
        }

        $this->setPromId($cid);
        $this->setType(4);
        $this->setMessageTitle('优惠券即将过期');
        $message_content = db('coupon')->where('id', $cid)->value('name');
        $this->setMessageContent($message_content);
        $this->setUsers([$user_id]);
        $this->setStoreId(0);
        $this->sendMessage();
    }

    /**
     * 检测必填参数
     * @return bool
     */
    public function checkParam()
    {
        if (empty($this->message['message_title']) || empty($this->message['mmt_code'])
            || empty($this->message['type']) || empty($this->message['send_time'])
        ) {
            return false;
        }
        return true;
    }


    /**
     * 必填
     * type: 0系统公告1降价通知2优惠券到账提醒3优惠券使用提醒4优惠券即将过期提醒5预售订单尾款支付提醒6提现到账提醒
     * @param $value
     */
    public function setType($value){
        $this->message['type'] = $value;
        $this->message['mmt_code'] = $this->getCodeByType($value);
    }
    /**
     * 可空
     * @param $value
     */
    public function setPromId($value){
        $this->message['prom_id'] = $value;
    }
}