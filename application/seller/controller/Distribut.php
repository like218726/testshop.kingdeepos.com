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

use app\admin\model\Users;
use think\Db;
use think\Page;
use app\admin\logic\GoodsLogic;

class Distribut extends Base
{

    /*
     * 初始化操作
     */
    public function _initialize()
    {
        parent::_initialize();
    }


    /**
     * 三级分销设置
     */
    public function distribut()
    {
        // 每个店铺有一个分销 记录
        $store_distribut = M('store_distribut')->where("store_id", STORE_ID)->find();
        if (IS_POST) {
            $Model = M('store_distribut');
            $data = input('post.');
            $data['store_id'] = STORE_ID;
            if ($store_distribut)
                $Model->save($data);
            else
                $Model->add($data);
            $this->success("操作成功", U('Store/distribut'));
            exit;
        }
        $distribut_set_by = M('config')->where("name", "distribut_set_by")->getField('value');
        $auto_service_date = tpCache('shopping.auto_transfer_date');  //最终退货时间
        $this->assign('auto_service_date', $auto_service_date);
        $this->assign('distribut_set_by', $distribut_set_by);
        $this->assign('config', $store_distribut);
        $default_config = tpCache('distribut');
        $this->assign('default_config',$default_config);
        return $this->fetch();
    }

    /**
     * 分成记录
     */
    public function rebate_log()
    {
        $log_model = Db::name("rebate_log");
        $status = I('status');
        $user_id = I('user_id/d');
        $order_sn = I('order_sn');
        $start_time =I('start_time', date('Y-m-d', strtotime('-1 year')));
        $end_time   =I('end_time',date('Y-m-d', strtotime('+1 day')+86399));

        $this->assign('start_time', $start_time);
        $this->assign('end_time',$end_time);
        $log_where = array(
            'store_id' => STORE_ID,
            'create_time' => ['between', [strtotime($start_time), strtotime( $end_time )]]
        );
        if ($status === '0' || $status > 0) {
            $log_where['status'] = $status;
        }
        $user_id && $log_where['user_id'] = $user_id;
        $order_sn && $log_where['order_sn'] = ['like', '%' . $order_sn . '%'];
        $count = $log_model->where($log_where)->count();
        $Page = new Page($count, 16);
        $list = $log_model->where($log_where)->order("`id` desc")->limit($Page->firstRow . ',' . $Page->listRows)->select();

        //nickname
        $get_user_id = get_arr_column($list, 'user_id'); // 获佣用户
        $buy_user_id = get_arr_column($list, 'user_id'); // 购买用户
        $user_id_arr = array_merge($get_user_id, $buy_user_id);
        if (!empty($user_id_arr))
            $user_arr = M('users')->where("user_id", "in", implode(',', $user_id_arr))->getField("user_id,nickname,email,mobile",true);
       
        $this->assign('user_arr', $user_arr);
        $show = $Page->show();
        $this->assign('show', $show);
        $this->assign('list', $list);
        C('TOKEN_ON', false);
        return $this->fetch();
    }

    public function goods_list()
    {
        $cat_id = I('cat_id/d');
        $key_word = I('key_word') ? trim(I('key_word')) : '';
        $brand_id = I('brand_id/d');
        $GoodsLogic = new GoodsLogic();
        $brandList = $GoodsLogic->getSortBrands();
        $categoryList = $GoodsLogic->getSortCategory();
        $this->assign('categoryList', $categoryList);
        $this->assign('brandList', $brandList);
        $goods_where = array(
            'distribut' => ['gt', 0],
            'store_id' => STORE_ID
        );
        if ($cat_id > 0) {
            $goods_where['cat_id1|cat_id2|cat_id3'] = $cat_id;
        }
        if ($key_word) {
            $goods_where['goods_name|goods_sn'] = ['like', '%' . $key_word . '%'];
        }
        if ($brand_id) {
            $goods_where['brand_id'] = $brand_id;
        }
        $goods_model = Db::name('goods');
        $count = $goods_model->where($goods_where)->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $goodsList = $goods_model->where($goods_where)->order('sales_sum desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('goodsList', $goodsList);
        $this->assign('page', $show);
        return $this->fetch();
    }

    /**
     * 修改编辑 分成
     */
    public function editRebate()
    {
        $id = I('id/d', 0);
        $log_model = Db::name('rebate_log');
        $rebate_log = $log_model->where(array('id' => $id, 'store_id' => STORE_ID))->find();
        if (empty($rebate_log)) {
            $this->error("参数错误!!!");
        }
        if (IS_POST) {
            $data = input('post.');
            // 如果是确定分成 将金额打入分佣用户余额
            if ($data['status'] == 3 && $rebate_log['status'] != 3) {
                accountLog($data['user_id'], $rebate_log['money'], 0, "订单:{$rebate_log['order_sn']}分佣", $rebate_log['money']);
            }
            Db::name('rebate_log')->update($data);
            $this->success("操作成功!!!", U('Distribut/rebate_log'));
            exit;
        }

        $user = M('users')->where("user_id", $rebate_log['user_id'])->find();
        if ($user['nickname']) {
            $rebate_log['user_name'] = $user['nickname'];
        } elseif ($user['email']) {
            $rebate_log['user_name'] = $user['email'];
        } elseif ($user['mobile']) {
            $rebate_log['user_name'] = $user['mobile'];
        }
        $this->assign('user', $user);
        $this->assign('rebate_log', $rebate_log);
        return $this->fetch();
    }

    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function store_member()
    {
        $search = input('search');
        $where['is_store_member'] = STORE_ID;
        if($search){
            $where['user_id|mobile|nickname'] = ['like','%'.$search.'%'];
        }
        $count = (new Users())->where($where)->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $list = (new Users())->where($where)->order('user_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('show', $show);
        return $this->fetch();
    }


    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function member_store()
    {
        $search = input('search');
//        $where['u.is_store_member'] = STORE_ID;
        $where['s.invite_user_id'] = $this->store['user_id'];
        if($search){
            $where['u.user_id|u.mobile|u.nickname|s.store_name'] = ['like','%'.$search.'%'];;
        }
        $count = (new \app\common\model\Store())->alias('s')
            ->where($where)
            ->join('__USERS__ u','u.user_id = s.user_id','left')
            ->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $list = (new \app\common\model\Store())->alias('s')
            ->join('__USERS__ u','u.user_id = s.user_id','left')
            ->where($where)->order('s.user_id desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();
        $this->assign('list', $list);
        $this->assign('show', $show);
        return $this->fetch();
    }

}