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
 * Author: IT宇宙人
 * Date: 2015-09-09
 */
namespace app\common\model;
use think\Db;
use think\Model;
class Store extends Model {
    public function InviteUser(){
        return $this->hasOne('users','user_id','invite_user_id');
    }
    public function shippingAreas(){
        return $this->hasMany('ShippingArea','store_id','store_id');
    }
    public function carts()
    {
        return $this->hasMany('cart','store_id','store_id');
    }
    public function getAddressRegionAttr($value, $data){
        $regions = Db::name('region')->where('id', 'IN', [$data['province_id'], $data['city_id'], $data['district']])->order('level desc')->select();
        $address = '';
        if($regions){
            foreach($regions as $regionKey=>$regionVal){
                $address = $regionVal['name'] . $address;
            }
        }
        return $address;
    }

    /**
     * 获取店铺分类的评分
     * @param $value
     * @param $data
     * @return array|false|\PDOStatement|string|Model
     */
    public function getStoreClassStatisticsAttr($value, $data)
    {
        $comparison_where = array('sc_id' => $data['sc_id'], 'deleted' => 0);
        $field = "avg(store_desccredit) as store_desccredit_avg,avg(store_servicecredit) as store_servicecredit_avg,avg(store_deliverycredit) as store_deliverycredit_avg";
        $statistics = Db::name('store')->field($field)->where($comparison_where)->cache('true')->find();
        if($statistics && $statistics['store_desccredit_avg']>0 && $statistics['store_servicecredit_avg']>0 && $statistics['store_deliverycredit_avg']>0){
            $statistics['store_desccredit_match'] = ($data['store_desccredit'] - $statistics['store_desccredit_avg']) / $statistics['store_desccredit_avg'] * 100;
            $statistics['store_servicecredit_match'] = ($data['store_servicecredit'] - $statistics['store_servicecredit_avg']) / $statistics['store_servicecredit_avg'] * 100;
            $statistics['store_deliverycredit_match'] = ($data['store_deliverycredit'] - $statistics['store_deliverycredit_avg']) / $statistics['store_deliverycredit_avg'] * 100;
        }else{
            $statistics['store_desccredit_match'] = 100;
            $statistics['store_servicecredit_match'] = 100;
            $statistics['store_deliverycredit_match'] = 100;
        }
        return $statistics;
    }

    /**
     * 获取平均评分
     * @param $value
     * @param $data
     * @return float|string
     */
    public function getAvgScoreAttr($value, $data){
        if(empty($data['store_desccredit']) && empty($data['store_servicecredit']) && empty($data['store_deliverycredit'])){
            return '4.8';
        }
        $score = ($data['store_desccredit'] + $data['store_servicecredit'] + $data['store_deliverycredit']) / 3;
        return round($score,1);
    }

    /**
     * 上个月售多少单
     * @param $value
     * @param $data
     * @return int|string
     */
    public function getCountMonthOrderAttr($value, $data){
        $where['store_id'] = $data['store_id'];
        $where['order_status'] = ['in','1,2,4']; // 订单状态.0待确认，1已确认，2已收货，3已取消，4已完成，5已作废
        /* 看需求，是查上个月，还是查最近30天
         * $timestamp = time();
        $firstday = strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-01');
        $lastday = strtotime(date('Y-m-01',$timestamp));
        $where['add_time'] = [
            ['gt',$firstday],
            ['lt',$lastday]
        ];
        // 查最近30天之内的订单
        $where['add_time'] = [
            ['gt',$timestamp-30*24*60*60],
            ['lt',$timestamp]
        ];*/
        // 改为查所有，当订单大于99999时，显示 99999+
        $count = Db::name('order')->where($where)->count();
        return $count;
    }

    /**
     * 计算距离店家的距离,请求时要带lat,lng
     * @param $value
     * @param $data
     * @return float|int
     */
    public function getDistanceAttr($value, $data){
        $lng = input('lng');
        $lat = input('lat');
        if($lng && $lat && ($data['longitude']!=0) && ($data['latitude']!=0)){
            $dis = $this->getDistance($data['latitude'],$data['longitude'],$lat,$lng);
            return round($dis,2);
        }
        return 0;
    }

    /**
     * 最少15分钟，最长90分
     * 每增加5公里增加30分钟配送时间
     * @param $value
     * @param $data
     * @return float|int
     */
    public function getMinAttr($value, $data){
        $min = $this->getDistanceAttr($value, $data);
        $min *= 6;
        if($min < 15){
            $min = 15;
        }
        if($min > 90){
            $min = 90;
        }
        return $min;
    }
    /**
     * 获取2点之间的距离
     * @param $lat1
     * @param $lng1
     * @param $lat2
     * @param $lng2
     * @return float|int
     */
    public function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $p = 3.1415926535898;
        $r = 6378.137;

        $radLat1 = $lat1 * ($p / 180);
        $radLat2 = $lat2 * ($p / 180);
        $a = $radLat1 - $radLat2;
        $b = ($lng1 * ($p / 180)) - ($lng2 * ($p / 180));
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
        $s = $s * $r;
        $s = round($s * 10000) / 10000;
        return $s;
    }

    /**
     * 代金券最大金额
     * @param $value
     * @param $data
     * @return string
     */
    public function getCouponMaxAttr($value, $data){
        return '88代100元';
    }

    /**
     * 活动名称
     * @param $value
     * @param $data
     * @return string
     */
    public function getActiveNameAttr($value, $data){
        return '36元单人套餐，16.8元招牌缤纷水果茶';
    }
    /**
     * 活动数量
     * @param $value
     * @param $data
     * @return string
     */
    public function getActiveNumAttr($value, $data){
        return '3';
    }
    /**
     * 多少起送
     * @param $value
     * @param $data
     * @return string
     */
    public function getFreePriceAttr($value, $data){

        return rand(20,35);
    }
    public function storeGoodsClass()
    {
        return $this->hasMany('StoreGoodsClass', 'store_id', 'store_id')->cache(true);
    }

    public function storeGoodsClassTopParent()
    {
        return $this->hasMany('StoreGoodsClass', 'store_id', 'store_id')->where(['parent_id' => 0])->cache(true);
    }

    public function province()
    {
        return $this->hasOne('region', 'id', 'province_id')->cache(true);
    }
    public function city()
    {
        return $this->hasOne('region', 'id', 'city_id')->cache(true);
    }
    public function district()
    {
        return $this->hasOne('region', 'id', 'district')->cache(true);
    }

    public function goods()
    {
        return $this->hasMany('goods','store_id','store_id');
    }

    /**
     * 获取店铺商品总数
     * @param $value
     * @param $data
     * @return int|string
     */
    public function getGoodsCountAttr($value, $data){
        return Db::name('goods')->where(['store_id'=>$data['store_id']])->count();
    }

    public function getTotalCreditAttr($value, $data)
    {
        return ($data['store_desccredit'] + $data['store_servicecredit'] + $data['store_servicecredit']) / 3;
    }

    /**
     * 获取店铺活动
     * @param $value
     * @param $data
     * @return array
     */
    public function getActivityAttr($value,$data){
        $time = time();
        //$g = Db::name('prom_goods')->where(['store_id'=>$data['store_id'],'status'=>1,'is_end'=>0,'start_time'=>['lt',$time],'end_time'=>['gt',$time]])->field("type,title,if(id,'1','0') as type")->select();
        
        $a = Db::name('prom_goods')->where(['store_id'=>$data['store_id'],'status'=>1,'is_end'=>0,'start_time'=>['lt',$time],'end_time'=>['gt',$time]])->field("title,if(id,'1','0') as type")->select();
        $b = Db::name('prom_order')->where(['store_id'=>$data['store_id'],'status'=>1,'start_time'=>['lt',$time],'end_time'=>['gt',$time]])->field("title,if(id,'2','0') as type")->select();
        $c = Db::name('flash_sale')->where(['store_id'=>$data['store_id'],'status'=>1,'start_time'=>['lt',$time],'end_time'=>['gt',$time]])->field("title,if(id,'3','0') as type")->select();
        $d = Db::name('group_buy')->where(['store_id'=>$data['store_id'],'status'=>1,'start_time'=>['lt',$time],'end_time'=>['gt',$time]])->field("title,if(id,'4','0') as type")->select();
        $e = Db::name('team_activity')->where(['store_id'=>$data['store_id'],'status'=>1])->field("act_name title,if(team_id,'5','0') as type")->select();
        $f = Db::name('pre_sell')->where(['store_id'=>$data['store_id'],'status'=>1,'sell_start_time'=>['lt',$time],'sell_end_time'=>['gt',$time]])->field("goods_name title,if(pre_sell_id,'6','0') as type")->select();
        //$g = Db::name('coupon')->where(['store_id'=>$data['store_id'],'type' => 2,'status'=>1,'send_start_time'=>['elt', $time],'send_end_time'=>['egt', $time]])->field("name title,if(id,'7','0') as type")->select();
        return array_merge($a,$b,$c,$d,$e,$f);
    }

    public function getStoreAddressAttr($value,$data){
        $regions = Db::name('region')->where('id', 'IN', [$data['province_id'], $data['city_id'], $data['district']])->order('level desc')->select();
        $address = '';
        if($regions){
            foreach($regions as $regionKey=>$regionVal){
                $address = $regionVal['name'] . $address;
            }
        }
        return $address.$value;
    }

    /**
     * 店铺的营业额（属于该店铺会员的所有消费，不限制于该店铺）
     * @param $value
     * @param $data
     * @return float|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getStoreOrderSumAttr($value, $data){
        $store_order =  Db::query("SELECT SUM(o.total_amount) AS total_amount FROM `__PREFIX__order` AS o INNER JOIN __PREFIX__users AS u
ON o.user_id = u.user_id WHERE  o.order_status IN (2,4)  AND o.pay_status = 1  AND u.is_store_member =".$data['store_id']);
        return $store_order[0]['total_amount']?$store_order[0]['total_amount']:0.00;
    }

    public function getStoreMemberCountAttr($value, $data){
        return db('users')->where(['is_store_member'=>$data['store_id']])->count();
    }
}
