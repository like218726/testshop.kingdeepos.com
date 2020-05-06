<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * Author: lhb
 * Date: 2017-05-15
 */

namespace app\common\logic;

use think\Model;
use app\common\model\Coupon;
/**
 * 活动逻辑类
 */
class ActivityLogic extends Model
{


    /**
     * 获取优惠券查询对象
     * @param int $queryType 0:count 1:select
     * @param type $user_id
     * @param int $type 查询类型 0:未使用，1:已使用，2:已过期
     * @param null $orderBy 排序类型，use_end_time、send_time,默认send_time
     * @param int  $belone 0:具体商家，1:自营, 2:所有商家
     * @param int $store_id
     * @param int $order_money
     * @return mixed
     */
    private function getCouponQuery($queryType, $user_id, $type = 0, $orderBy = null, $belone = 0, $store_id = 0, $order_money = 0)
    {
        if($user_id)
            $where['l.uid'] = $user_id;
        $where['l.deleted'] = 0;
        $where['c.status'] = 1;
        $where['c.type']=array('<>',4);
        $now=time();
        //查询条件
        if (empty($type)) {
            // 未使用
            $where['c.use_end_time'] = array('gt', time());
            $where['c.status'] = 1;
            $where['l.status'] = 0;
            if($user_id){
                $whereOr="l.uid = {$user_id} and c.status = 1 and c.type = 4 and l.deleted = 0 and l.status = 0 and (l.send_time+(86400*c.validity_day)) >= {$now}";
            }else{
                $whereOr="c.status = 1 and c.type = 4 and l.deleted = 0 and l.status = 0 and (l.send_time+(86400*c.validity_day)) >= {$now}";
            }

        } elseif ($type == 1) {
            //已使用
            $where['l.order_id'] = array('gt', 0);
            $where['l.use_time'] = array('gt', 0);
            $where['l.status'] = 1;
            if($user_id){
                $whereOr="l.uid = {$user_id} and l.status =1 and c.status = 1 and c.type = 4 and l.deleted = 0 and l.order_id >0 and l.use_time > 0";
            }else{
                $whereOr="c.status = 1 and l.status = 1 and c.type = 4 and l.deleted = 0 and l.order_id > 0 and l.use_time > 0";
            }
        } elseif ($type == 2) {
            //已过期
            $where['c.use_end_time'] = array('lt', time());
            $where['l.status'] = array('neq', 1);

            if($user_id){
                $whereOr="l.uid = {$user_id} and l.status <> 1 and c.status = 1 and c.type = 4 and l.deleted = 0 and (l.send_time+(86400*c.validity_day)) < {$now}";
            }else{
                $whereOr="c.status = 1 and l.status <> 1 and c.type = 4 and l.deleted = 0 and (l.send_time+(86400*c.validity_day)) < {$now}";
            }
        }
        if ($orderBy == 'use_end_time') {
            //即将过期
            $order['c.use_end_time'] = 'asc';
        } elseif ($orderBy == 'send_time') {
            //最近到账
            $where['l.send_time'] = array('lt',time());
            $order['l.send_time'] = 'desc';
        } elseif (empty($orderBy)) {
            $order = array('l.send_time' => 'DESC', 'l.use_time');
        }

        $condition = floatval($order_money) ? ' AND c.condition <= '.$order_money : '';
        $query = M('coupon_list')->alias('l')
            ->join('__COUPON__ c','l.cid = c.id'.$condition)
            ->join('__GOODS_COUPON__ gc', 'gc.coupon_id=c.id','left')
            ->where($where)
            ->whereOr($whereOr);

        // 加上->group('id')，防止 重复数据
        $query = $query->field('l.*,c.name,c.use_type,c.money,c.use_start_time,c.use_end_time,c.condition,c.validity_day,c.type as pathway')->field('gc.goods_id,gc.goods_category_id')
                ->order($order)->group('id');

        return $query;
    }
    
    /**
     * 获取优惠券数目
     */
    public function getUserCouponNum($user_id, $type = 0, $orderBy = null, $belone = 0, $store_id = 0, $order_money = 0)
    {
        $query = $this->getCouponQuery(0, $user_id, $type, $orderBy, $belone, $store_id, $order_money);
        return $query->count();
    }
    
    /**
     * 获取用户优惠券列表
     */
    public function getUserCouponList($firstRow, $listRows, $user_id, $type = 0, $orderBy = null, $belone = 0, $store_id = 0, $order_money = 0)
    {
        $query = $this->getCouponQuery(1, $user_id, $type, $orderBy, $belone, $store_id, $order_money);
        return $query->limit($firstRow, $listRows)->select();
    }

    /**
     * 领券中心
     * @param type $cat_id 领券类型id
     * @param type $user_id 用户id
     * @param type $p 第几页
     * @param type $goods_id 指定商品id
     * @return type
     */
    public function getCouponCenterList($cat_id, $user_id, $p = 1,$goods_id=0)
    {
        /* 获取优惠券列表 */
        $cur_time = time();
        $coupon_where = ['type'=>2, 'status'=>1, 'send_start_time'=>['elt',time()], 'send_end_time'=>['egt',time()]];
        $query = db('coupon')->alias('c')
            ->field('gc.goods_id,gc.goods_category_id,c.use_type,c.name,c.id,c.money,c.condition,c.createnum,c.use_start_time,c.use_end_time,c.send_num,c.send_end_time-'.$cur_time.' as spacing_time')
            ->where('((createnum-send_num>0 AND createnum>0) OR (createnum=0))')    //领完的也不要显示了
            ->where($coupon_where)->page($p, 15)
            ->order('condition', 'desc');
//        if ($cat_id > 0) {
//            $query = $query->join('__GOODS_COUPON__ gc', 'gc.coupon_id=c.id AND gc.goods_category_id='.$cat_id);
//        }
        $query = $query->join('__GOODS_COUPON__ gc', 'gc.coupon_id=c.id ','left');
        $coupon_list = $query->select();
        if (!(is_array($coupon_list) && count($coupon_list) > 0)) {
            return [];
        }

        $user_coupon = [];
        if ($user_id) {
            $user_coupon = M('coupon_list')->where(['uid' => $user_id, 'type' => 2])->column('cid');
        }

        $types = [];
        if ($cat_id) {
            /* 优惠券类型格式转换 */
            $couponType = $this->getCouponTypes();
            foreach ($couponType as $v) {
                $types[$v['id']] = $v['mobile_name'];
            }
        }

        $store_logo = tpCache('shop_info.store_logo') ?: '';
        $Coupon = new Coupon();
        foreach ($coupon_list as $k => $coupon) {
            /* 是否已获取 */
            $coupon_list[$k]['use_type_title'] = $Coupon->getUseTypeTitleAttr(null, $coupon_list[$k]);
            $coupon_list[$k]['isget'] = 0;
            if (in_array($coupon['id'], $user_coupon)) {
                $coupon_list[$k]['isget'] = 1;
            }

            /* 构造封面和标题 */
            $coupon_list[$k]['image'] = $store_logo;
            $coupon_list[$k]['use_end_time'] = date('Y-m-d',$coupon['use_end_time']);
            $coupon_list[$k]['use_start_time'] = date('Y-m-d',$coupon['use_start_time']);
            switch ($coupon['use_type']){
                case 1;
                    if($goods_id > 0 && $goods_id != $coupon['goods_id']){
                        unset($coupon_list[$k]);
                    }
                    break;
                case 2;
                    if($cat_id > 0 && $cat_id != $coupon['goods_category_id']){
                        unset($coupon_list[$k]);
                    }
                    break;

            }
        }

        return  $coupon_list;
    }

    /**
     * 优惠券类型列表
     * @param type $p 第几页
     * @param type $num 每页多少，null表示全部
     * @return type
     */
    public function getCouponTypes($p = 1, $num = null)
    {
        $list = M('coupon')->alias('c')
            ->join('__GOODS_COUPON__ gc', 'gc.coupon_id=c.id AND gc.goods_category_id!=0')
            ->where(['type' => 2, 'status' => 1])
            ->column('gc.goods_category_id');

        $result = M('goods_category')->field('id, mobile_name')->where("id", "IN", $list)->page($p, $num)->select();
        $result = $result ?: [];
        array_unshift($result, ['id'=>0, 'mobile_name'=>'精选']);

        return $result;
    }

}