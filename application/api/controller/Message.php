<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 消息通知
 * @author yhj
 *  @Date: 2018-07-31
 */
namespace app\api\controller;

use app\common\model\MessageActivity;
use app\common\model\MessageLogistics;
use app\common\model\MessageNotice;
use app\common\model\MessagePrivate;
use app\common\model\UserMessage;
use app\common\util\TpshopException;
use think\Page;
use think\Db;

class Message extends Base
{

    /**
     * 析构函数
     */
    function __construct()
    {
        $user_id = I('user_id');
        if ($user_id) {
            $this->user_id = $user_id;
        }else{
            parent::__construct();
        }

        $message = new \app\common\logic\Message();
        $message->checkUserMessage($this->user_id);
    }

    /**
     * 消息中心
     * 返回未读消息数量，和最近一条消息内容与时间
     *
     */
    public function messageNotice()
    {
        $message = $this->getMobileMessageOne();
        $no_read = $this->getUserMessageCount();

        $arr['message_content'] = $message['message_notice']['message_content'];
        $arr['send_time_text'] = $message['message_notice']['send_time_text'];
        $arr['type'] = 0;
        $arr['no_read'] = $no_read['message_notice_no_read'];
        $data[] = $arr;
        $arr['message_content'] = $message['message_activity']['message_content'];
        $arr['send_time_text'] = $message['message_activity']['send_time_text'];
        $arr['type'] = 1;
        $arr['no_read'] = $no_read['message_activity_no_read'];
        $data[] = $arr;
        $arr['message_content'] = $message['message_logistics']['message_content'];
        $arr['send_time_text'] = $message['message_logistics']['send_time_text'];
        $arr['type'] = 2;
        $arr['no_read'] = $no_read['message_logistics_no_read'];
        $data[] = $arr;

        $this->ajaxReturn(['status' => 1, 'msg' => "查询成功", 'result' => $data]);
    }

    /**
     * 查最近一条消息内容与时间
     * return array
     */
    public function getMobileMessageOne()
    {
        $where = array(
            'user_id' => $this->user_id,
            'deleted' => 0,
            'category' => 0
        );
        $message_notice = Db::name('user_message')->alias('u')
            ->join('__MESSAGE_NOTICE__ m','m.message_id=u.message_id')
            ->where($where)
            ->field('m.message_title,m.send_time')
            ->limit(1)
            ->order('m.send_time desc')
            ->find();
        if ($message_notice) {
            $data['message_notice']['message_content'] = htmlspecialchars_decode($message_notice['message_title']);
            $data['message_notice']['send_time_text'] = time_to_str($message_notice['send_time']);
        } else {
            $data['message_notice']['message_content'] = '暂无消息';
            $data['message_notice']['send_time_text'] = '';
        }
        $where['category'] = 1;
        $message_activity = Db::name('user_message')->alias('u')
            ->join('__MESSAGE_ACTIVITY__ m','m.message_id=u.message_id')
            ->where($where)
            ->field('m.message_title,m.send_time')
            ->limit(1)
            ->order('m.send_time desc')
            ->find();
        if ($message_activity) {
            $data['message_activity']['message_content'] = htmlspecialchars_decode($message_activity['message_title']);
            $data['message_activity']['send_time_text'] = time_to_str($message_activity['send_time']);
        } else {
            $data['message_activity']['message_content'] = '暂无消息';
            $data['message_activity']['send_time_text'] = '';
        }

        $where['category'] = 2;
        $message_logistics = Db::name('user_message')->alias('u')
            ->join('__MESSAGE_LOGISTICS__ m','m.message_id=u.message_id')
            ->where($where)
            ->field('m.message_title,m.send_time')
            ->limit(1)
            ->order('m.send_time desc')
            ->find();
        if ($message_logistics) {
            $data['message_logistics']['message_content'] = htmlspecialchars_decode($message_logistics['message_title']);
            $data['message_logistics']['send_time_text'] = time_to_str($message_logistics['send_time']);
        } else {
            $data['message_logistics']['message_content'] = '暂无消息';
            $data['message_logistics']['send_time_text'] = '';
        }
        return $data;
    }

    /**
     * 消息未读数量
     * return array
     */
    public function getUserMessageCount(){

        $where = array(
            'user_id' => $this->user_id,
            'is_see' => 0,
            'deleted' => 0,
            'category' => 0
        );

        $data['message_notice_no_read'] = Db('user_message')->where($where)->count();

        $where['category'] = 1;
        $data['message_activity_no_read'] = Db('user_message')->where($where)->count();

        $where['category'] = 2;
        $data['message_logistics_no_read'] = Db('user_message')->where($where)->count();

        $where['category'] = 3;
        $data['message_private_no_read'] = Db('user_message')->where($where)->count();

        return $data;
    }


    /**
     * 分页消息数量
     * p=1
     * type=0
     *
     */
    public function messageNoticeDetail(){

        $type = I('type', 0);
        $where = array(
            'user_id' => $this->user_id,
            'deleted' => 0,
            'category' => $type
        );

        $userMessage = new UserMessage();
        $count = $userMessage->where($where)->count();
        $Page = new Page($count, 10);
        $list = $userMessage->where($where)->order("rec_id DESC")->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $data = [];
        foreach ($list as $key => $value) {
            if ($type == 0) {
                // 通知消息
                $message = $value->MessageNotice->append(['send_time_text', 'finished'])->toArray();
            } elseif ($type == 1) {
                // 活动消息

                $message = $value->MessageActivity->append(['send_time_text', 'goods_id', 'finished','start_time'])->toArray();
                $message['message_content'] = '';
                $goods = $message['goods_id'];
                $message['goods_id'] = $goods['goods_id'];
                $message['item_id'] = $goods['item_id'];
                $message['start_time'] = $message['start_time'] ? $message['start_time'] : 0;
                $message['is_start'] = $message['start_time'] ? 0 : 1;
            } elseif ($type == 2) {
                // 物流消息
                $message = $value->MessageLogistics->append(['send_time_text', 'finished','order_type','order_text'])->toArray();
            } elseif ($type == 3) {
                // 私信
            }
            $message['is_see'] = $value['is_see'];
            $message['rec_id'] = $value['rec_id'];
            $message['is_finish'] = $message['finished'] ? 1 : 0;
            $message['prom_type'] = intval($message['prom_type']);
            $message['type'] = intval($message['type']);
            $message['prom_id'] = intval($message['prom_id']);
            $data[] = $message;
        }
        $data = array_values(array_sort($data, 'send_time'));
        $this->ajaxReturn(['status' => 1, 'msg' => "查询成功", 'result' => $data, 'type' => $type]);
    }

    /**
     * 所有未读消息数量
     *
     *
     */
    public function getUserMessageNoReadCount(){

        $where = array(
            'user_id' => $this->user_id,
            'is_see' => 0,
            'deleted' => 0
        );
        $message_no_read = Db::name('user_message')->where($where)->count();
        $this->ajaxReturn(['status' => 1, 'msg' => "查询成功", 'result' => $message_no_read]);
    }

    /**
     * 获取用户消息详情
     *
     */
    public function getMessageDetails(){

        $rec_id = I('rec_id', 0);
        $where = array(
            'user_id' => $this->user_id,
            'rec_id' => $rec_id
        );
        $userMessage = new UserMessage();
        $data = $userMessage->where($where)->find();
        if (!$data) {
            $this->ajaxReturn(['status' => -1, 'msg' => "消息不存在", 'result' => '']);
        }
        if ($data['deleted'] == 1) {
            $this->ajaxReturn(['status' => -1, 'msg' => "消息已删除", 'result' => '']);
        }
        // 设置为已读
        if ($data['is_see'] == 0) {
            $userMessage->where($where)->update(['is_see' => 1]);
        }
        $type = $data['category'];
        if ($type == 0) {
            $model = new MessageNotice();
        } elseif ($type == 1) {
            $model = new MessageActivity();
        } elseif ($type == 2) {
            $model = new MessageLogistics();
        } elseif ($type == 3) {
            $model = new MessagePrivate();
        } else {
            $model = new MessageNotice();
        }
        $result = $model->where('message_id', $data['message_id'])->find();
        if ($result) {
            $result['send_time_texts'] = $result->send_time_text;
            $this->ajaxReturn(['status' => 1, 'msg' => "查询成功", 'result' => $result, 'type' => $type]);
        } else {
            $this->ajaxReturn(['status' => -1, 'msg' => "消息不存在", 'result' => '', 'type' => $type]);
        }
    }

    /**
     * 清空消息
     */
    public function deletedMessage(){

        $type = I('type', 0);
        $where = array(
            'user_id' => $this->user_id,
            'deleted' => 0,
            'category' => $type
        );
        $rec_id = I('rec_id', 0);
        if (!empty($rec_id)) {
            unset($where);
            $where['rec_id'] = $rec_id;
        }
        $userMessage = new UserMessage();
        $data = $userMessage->where($where)->update(['deleted' => 1]);

        $this->ajaxReturn(['status' => 1, 'msg' => "操作成功", 'result' => $data]);
    }

    /**
     * 设置用户消息已读
     * @throws \think\Exception
     */

    public function setMessageForRead()
    {
        $rec_id = I('rec_id', 0);
        $set_where['user_id'] = $this->user_id;

        if (strpos($rec_id, ',')) {
            $rec_id = explode(',', $rec_id);
            $set_where['rec_id'] = ['in',$rec_id];
        } elseif (!empty($rec_id)) {
            $set_where['rec_id'] = $rec_id;
        } else {
            $this->ajaxReturn(['status' => -1, 'msg' => "参数错误rec_id", 'result' => '']);
        }

        $res = Db::name('user_message')->where($set_where)->update(['is_see' => 1]);
        if ($res !== false) {
            $this->ajaxReturn(['status' => 1, 'msg' => "操作成功", 'result' => $res]);
        }
        $this->ajaxReturn(['status' => -1, 'msg' => "操作失败", 'result' => $res]);
    }
}
