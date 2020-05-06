<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 商业用途务必到官方购买正版授权, 使用盗版将严厉追究您的法律责任。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * Author: IT宇宙人
 *
 * Date: 2016-03-09
 */

namespace app\seller\controller;

use think\Db;
use think\Page;

class Finance extends Base
{
    /*
     * 初始化操作
     */
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     *  转账汇款记录
     */
    public function remittance()
    {
        $user_id = I('user_id/d');
        $bank_card = I('bank_card');
        $realname = I('realname');
        $create_time = I('create_time');
        $create_time = str_replace("+", " ", $create_time);
        $create_time = $create_time ? $create_time : date('Y-m-d', strtotime('-1 year')) . ' - ' . date('Y-m-d', strtotime('+1 day'));
        $create_time2 = explode(' - ', $create_time);
        $this->assign('start_time', $create_time2[0]);
        $this->assign('end_time', $create_time2[1]);
        $store_withdrawals_where = array(
            'store_id' => STORE_ID,
            'create_time' => ['between', [strtotime($create_time2[0]), strtotime($create_time2[1])]],
            'status' => ['in',[1,2]]
        );
        if ($user_id) {
            $store_withdrawals_where['user_id'] = $user_id;
        }
        if ($bank_card) {
            $store_withdrawals_where['bank_card'] = ['like', '%' . $bank_card . '%'];
        }
        if ($realname) {
            $store_withdrawals_where['realname'] = ['like', '%' . $realname . '%'];
        }
        $count = Db::name('store_withdrawals')->where($store_withdrawals_where)->count();
        $Page = new Page($count, 20);
        $list = Db::name('store_withdrawals')->where($store_withdrawals_where)->order("`id` desc")->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $this->assign('create_time', $create_time);
        $show = $Page->show();
        $this->assign('show', $show);
        $this->assign('list', $list);
        C('TOKEN_ON', false);
        return $this->fetch();
    }

    /**
     * 提现申请记录
     */
    public function withdrawals()
    {
        $status = I('status');
        $bank_card = I('bank_card');
        $realname = I('realname');
        $begin =  $this->begin;
        $end   =  $this->end;
        if ($begin && $end) {
            $store_withdrawals_where = [
                'store_id' => STORE_ID,
                'create_time' => ['between', [$begin,$end]]
            ];
        }
        if ($status != '') {
            $store_withdrawals_where['status'] = $status;
        }
        if ($bank_card) {
            $store_withdrawals_where['bank_card'] = ['like', '%' . $bank_card . '%'];
        }
        if ($realname) {
            $store_withdrawals_where['realname'] = ['like', '%' . $realname . '%'];
        }
        $count = Db::name("store_withdrawals")->where($store_withdrawals_where)->count();
        $Page = new Page($count, 16);
        $list = Db::name("store_withdrawals")->where($store_withdrawals_where)->order("`id` desc")->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $show = $Page->show();
        $this->assign('show', $show);
        $this->assign('list', $list);
        C('TOKEN_ON', false);
        return $this->fetch();
    }

    /**
     * 添加提现申请
     */
    public function add_edit_withdrawals()
    {
        $id = I('id/d', 0);
        $withdrawals = Db::name('StoreWithdrawals')->where(array('id' => $id, 'store_id' => STORE_ID))->find();

        if (IS_POST) {
            if ($withdrawals['status'] == 1) {
                $this->error('申请成功的不能再修改');
            }
            $data = input('post.');
            if ($data['money'] <= 1) {
                $this->error('提现金额不得小于1');
            }
            //检查是否满足能体现
            $store = M('store')->where("store_id", STORE_ID)->find();
            if ($store['store_money'] < ( $data['money'])) {
                $this->error('您有提现申请待处理，本次提现余额不足');
            }
            
            if ($data['id']) {
                Db::name('store_withdrawals')->update($data);
            } else {
                $data['store_id'] = STORE_ID;
                $data['create_time'] = time();
                Db::name('store_withdrawals')->insert($data);
            }
            //冻结提现金额
            Db::name('store')->where(['store_id'=>STORE_ID])->setInc('frozen_money',$data['money']);
            Db::name('store')->where(['store_id'=>STORE_ID])->setDec('store_money',$data['money']);
            $this->success('保存完成', U('withdrawals'));
            exit();
        }
        $withdrawals_max = M('store')->where(array('store_id' => STORE_ID))->getField('store_money');
        $withdrawals_min = tpCache('basic.min');
        $this->assign('withdrawals_max', $withdrawals_max);
        $this->assign('withdrawals_min', $withdrawals_min);
        $this->assign('withdrawals', $withdrawals);
        return $this->fetch('_withdrawals');
    }

    /**
     * 删除申请记录
     */
    public function delWithdrawals()
    {
        $id = I('id/d');
        $where = array(
            'id' => $id,
            'store_id' => STORE_ID,
            'status' => ['<>', 1]
        );
        Db::name('store_withdrawals')->where($where)->delete();
        $return_arr = array('status' => 1, 'msg' => '操作成功', 'data' => '',);
        $this->ajaxReturn($return_arr);
    }

    /**
     *  商家结算记录
     */
    public function order_statis()
    {
        $order_statis_where = array(
            'store_id' => STORE_ID,
            'create_date' => ['between', [$this->begin, $this->end]]
        );
        $count = Db::name('order_statis')->where($order_statis_where)->count();
        $Page = new Page($count, 16);
        $list = Db::name('order_statis')->where($order_statis_where)->order("`id` desc")->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $show = $Page->show();
        $this->assign('show', $show);
        $this->assign('list', $list);
        C('TOKEN_ON', false);
        return $this->fetch();
    }

    /**
     * 结算订单页
     */
    public function order_list(){
        $id = input('id/d',0);
        $order_sn = input('order_sn',0);
        $where = [
            'store_id'=>STORE_ID,
            'order_statis_id' => $id,
        ];
        $whereOr = [
            'suppliers_id'=>STORE_ID,
            'supplier_order_statis_id' => $id,
        ];
        if ($order_sn) {
            $where['order_sn'] = $whereOr['order_sn'] = $order_sn;
        }
        $count = M('order')->where($where)->whereOr(function ($query)use ($whereOr){
            $query->where($whereOr);
        })->count();
        $page = new Page($count,20);
        $lists = M('order')->where($where)->whereOr(function ($query)use ($whereOr){
            $query->where($whereOr);
        })->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $order_status = C('ORDER_STATUS');
        $shipping_status = C('SHIPPING_STATUS');
        $this->assign('show',$page->show());
        $this->assign('lists',$lists);
        $this->assign('shipping_status', $shipping_status);
        $this->assign('order_status', $order_status);
        $this->assign('order_sn', $order_sn);
        return $this->fetch();
    }
    /**
     * 未结算记录
     * @return mixed
     */
    public function order_no_statis()
    {
        $create_date = I('create_date');
        $create_date = str_replace("+", " ", $create_date);
        $create_date2 = $create_date ? $create_date : date('Y-m-d', strtotime('-1 month')) . ' - ' . date('Y-m-d', strtotime('+1 month'));
        $create_date3 = explode(' - ', $create_date2);
        $where = array(
            'store_id' => STORE_ID,
            'pay_status' => 1,
        	'order_status'=> array('neq',3),	
            'add_time' => array(array('gt', strtotime($create_date3[0])), array('lt', strtotime($create_date3[1]))),
            'order_statis_id' => 0
        );
		$whereOr = array(
            'suppliers_id' => STORE_ID,
            'pay_status' => 1,
        	'order_status'=> array('neq',3),	
            'add_time' => array(array('gt', strtotime($create_date3[0])), array('lt', strtotime($create_date3[1]))),
            'supplier_order_statis_id' => 0
        );
        $this->assign('start_time', $create_date3[0]);
        $this->assign('end_time', $create_date3[1]);
        $model = M('order');
        $count = $model->where($where)->whereOr(function ($query)use ($whereOr){
            $query->where($whereOr);
        })->count();
        $Page = new Page($count, 16);
        $list = $model->where($where)->whereOr(function ($query)use ($whereOr){
            $query->where($whereOr);
        })->order("`add_time` desc")->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('create_date', $create_date2);
        $show = $Page->show();
        $order_status = C('ORDER_STATUS');
        $shipping_status = C('SHIPPING_STATUS');
        $this->assign('shipping_status', $shipping_status);
        $this->assign('order_status', $order_status);
        $this->assign('show', $show);
        $this->assign('list', $list);
        C('TOKEN_ON', false);
        return $this->fetch();
    }
}