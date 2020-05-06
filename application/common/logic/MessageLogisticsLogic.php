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
 * 物流消息逻辑定义
 * Class CatsLogic
 * @package admin\Logic
 */
class MessageLogisticsLogic extends MessageBase
{
    /**
     * 添加一条物流消息
     */
    public function addMessage(){
        $this->message['category'] = 2;
        db('message_logistics')->insert($this->message);
        $message_id = db('message_logistics')->getLastInsID();
        if($message_id) {
            $this->message['message_id'] = $message_id;
        }
    }

    /**
     * 是否已发过消息
     * @param $order
     * @param $type
     * @return array|false|\PDOStatement|string|Model
     */
    public function isExitsMessage($order, $type)
    {
        if(empty($order['user_id'])) return false;
        $message_logistics_where = [
            'type' => $type,
            'order_id' => $order['order_id'],
            'store_id' => $order['store_id'],
        ];
        $message_id = Db::name('message_logistics')->where($message_logistics_where)->value('message_id');
        return $message_id;
    }
    public function setOrderOnly($order){
        $goods_id = Db('order_goods')->where('order_id', $order['order_id'])->value('goods_id');
        $goods = Db('goods')->where('goods_id', $goods_id)->find();
        $this->setOrderId($order['order_id']);
        $this->setMessageContent($goods['goods_name']);
        $this->setOrderSn($order['order_sn']);
        $this->setStoreId($order['store_id']);
        $this->setImgUri($goods['original_img']);
        $this->setUsers([$order['user_id']]);
    }
    /**
     * 发货消息
     * @param $order
     */
    public function sendDeliverGoods($order)
    {
        if($this->isExitsMessage($order, 2)) return;
        $this->setOrderOnly($order);
        $this->setMessageTitle('商品已发货');
        $this->setType(2);
        $this->sendMessage();
    }
    /**
     * 待评价消息
     * @param $order
     */
    public function sendEvaluate($order)
    {
        if($this->isExitsMessage($order, 4)) return;
        $this->setOrderOnly($order);
        $this->setMessageTitle('商品待评价');
        $this->setType(4);
        $this->sendMessage();
    }

    /**
     * 发送退款消息
     * @param $order
     * @param $money
     */
    public function sendRefundNotice($order, $money)
    {
//        if($money <= 0) return;
        $this->setOrderOnly($order);
        $this->setType(6);
        $this->setMessageTitle('订单已退款');
        $this->setMessageContent('');
        $this->setMessageVal(['money' => $money]);
        $this->sendMessage();
    }

    /**
     * 发送虚拟订单消息
     * @param $order
     * @param $goods
     */
    public function sendVirtualOrder($order, $goods=[])
    {
        if($this->isExitsMessage($order, 7)) return;
        if(empty($goods)){
            $goods_id = Db('order_goods')->where('order_id', $order['order_id'])->value('goods_id');
            $goods = Db('goods')->where('goods_id', $goods_id)->find();
        }
        $this->setOrderId($order['order_id']);
        $this->setOrderSn($order['order_sn']);
        $this->setImgUri($goods['original_img']);
        $this->setType(7);
        $this->setMessageTitle('商品已发货');
        $this->setMessageContent($goods['goods_name']);
        $this->setUsers([$order['user_id']]);
        $this->setStoreId($order['store_id']);
        $this->sendMessage();
    }    

    /**
     * 获取编号
     * @param $type
     * @return string
     */
    public function getCodeByType($type)
    {
        // 1到货通知2发货提醒3签收提醒4评价提醒5退货提醒6退款提醒7虚拟商品
        switch ($type) {
            case 1:
                $mmt_code = '';
                break;
            case 2:
                $mmt_code = 'deliver_goods_logistics';
                break;
            case 3:
                $mmt_code = '';
                break;
            case 4:
                $mmt_code = 'evaluate_logistics';
                break;
            case 5:
                $mmt_code = '';
                break;
            case 6:
                $mmt_code = 'refund_logistics';
                break;
            case 7:
                $mmt_code = 'virtual_order_logistics';
                break;    
            default:
                $mmt_code = '';
                break;
        }
        return $mmt_code;
    }

    /**
     * 删除消息
     * @param $prom_id |活动id
     * @param $type |消息类型
     * @throws \think\Exception
     */
    public function deletedMessage($prom_id, $type)
    {
        $message_id = db('message_logistics')->where(['order_id' => $prom_id, 'type' => $type])->column('message_id');
        if ($message_id) {
            db('message_logistics')->where(['order_id' => $prom_id, 'prom_type' => $type])->delete();
            db('user_message')->where(['message_id' => ['in',$message_id], 'category' => 2])->delete();
        }
    }



    /**
     * 检测必填参数
     * @return bool
     */
    public function checkParam()
    {
        if (empty($this->message['message_title']) || empty($this->message['order_sn'])
            || empty($this->message['send_time']) || empty($this->message['img_uri'])
            || empty($this->message['type']) || empty($this->message['order_id'])
            || empty($this->message['mmt_code'])
        ) {
            return false;
        }
        return true;
    }


    /**
     * 必填
     * @param $value
     */
    public function setImgUri($value){
        $this->message['img_uri'] = $value;
    }

    /**
     * 必填
     * type:1到货通知2发货提醒3签收提醒4评价提醒5退货提醒6退款提醒7虚拟商品
     * @param $value
     */
    public function setType($value){
        $this->message['type'] = $value;
        $this->message['mmt_code'] = $this->getCodeByType($value);
    }
    /**
     * 必填
     * @param $value
     */
    public function setOrderId($value){
        $this->message['order_id'] = $value;
    }
    /**
     * 必填
     * @param $value
     */
    public function setOrderSn($value){
        $this->message['order_sn'] = $value;
    }

}