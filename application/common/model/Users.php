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
use app\common\logic\FlashSaleLogic;
use app\common\logic\GroupBuyLogic;

class Users extends Model
{
    //自定义初始化
    protected static function init()
    {
        //TODO:自定义的初始化
    }

    public function oauthUsers()
    {
        return $this->hasMany('OauthUsers', 'user_id', 'user_id');
    }

    public function userLevel()
    {
        return $this->hasOne('UserLevel', 'level_id', 'level');
    }

    /**
     * 用户团队订单
     * @param $value
     * @param $data
     * @return array
     */
    public function getUserTeamOrderAttr($value, $data){
        $first_leader = DB::name('users')->where(['first_leader'=>$data['user_id']])->getField('user_id',true);
        $second_leader = DB::name('users')->where(['second_leader'=>$data['user_id']])->getField('user_id',true);
        $third_leader = DB::name('users')->where(['third_leader'=>$data['user_id']])->getField('user_id',true);
        $all = array_merge($second_leader,$first_leader,$third_leader)  ; //属于个人的所有下线包含1,2,3级别
        //订单产生的分佣记录是直接上级获得，隔级不算
        if($all){
            $order = DB::name('rebate_log')->alias('a')->join('tp_order b','a.order_id=b.order_id','left')->where(['b.user_id' =>['in',$all] ,'a.user_id'=>$data['user_id'] ,'a.status'=> [ 'in',[1,2,3]] ])->field('b.order_id,b.total_amount')->select();
        }
        $order_count = 0;
        $total_amount = 0;
        if($order){
            foreach ($order as $v){
                $order_count++;
                $total_amount += $v['total_amount'];
            }
        }

        $datas['total_amount'] = $total_amount;      //团队订单总额
        $datas['user_count'] = count($all);
        $datas['order_count'] = $order_count;
        return $datas;
    }

    /**
     * 用户会员订单
     * @param $value
     * @param $data
     * @return array
     */
    public function getUserOrderAttr($value, $data){

        $count = DB::name('order')->where(['user_id'=>$data['user_id'] ,'pay_status'=> 1 ])->count();
        $total_amount = DB::name('order')->where(['user_id'=>$data['user_id'] ,'pay_status'=> 1 ])->sum('total_amount');
        $commission = DB::name('rebate_log')->where(['user_id'=>$GLOBALS['up_user_id'],  'buy_user_id'=>$data['user_id'] ,'status'=> ['in',[1,2,3]] ])->sum('money');
 
        $datas['count'] = $count;
        $datas['total_amount'] = $total_amount;
        $datas['commission'] = $commission;
        return $datas;
    }

    /**
     * 用户会员等级类型展示数据
     * @param $value
     * @param $data
     * @return array
     */
    public function getUserTypeDataAttr($value, $data){
        $first_leader = DB::name('users')->where(['first_leader'=>$data['user_id']])->count();
        $second_leader = DB::name('users')->where(['second_leader'=>$data['user_id']])->count();
        $third_leader = DB::name('users')->where(['third_leader'=>$data['user_id']])->count();
        return  $first_leader + $second_leader + $third_leader;
    }

    /**
     * 我的分销提现记录
     * @param $value
     * @param $data
     * @return array
     */
    public function getRebateLogAttr($value, $data){
        $log = DB::name('withdrawals')->where(['user_id'=>$data['user_id'],'type'=>1])->count();
        return  $log;
    }

    /**
     * 用户下线分销金额
     * @param $value
     * @param $data
     * @return float|int
     */
    public function getRebateMoneyAttr($value, $data){
        $sum_money = DB::name('rebate_log')->where(['status' => 3,'user_id'=>$data['user_id']])->sum('money');
        $rebate_money = empty($sum_money) ? (float)0 : $sum_money;
        return  $rebate_money;
    }
    /**
     * 按月查询用户下线分销金额
     * @param $value
     * @param $data
     * @return float|int
     */
    public function getRebateMoneyMonthAttr($value, $data){
        $sum_money = DB::name('rebate_log')->where(['status' => 3,'user_id'=>$data['user_id']])->whereTime('create_time', 'm')->sum('money');
        $rebate_money = empty($sum_money) ? (float)0 : $sum_money;
        return  $rebate_money;
    }

    /**
     * 按月查询用户消费金额
     * @param $value
     * @param $data
     * @return float|int
     */
    public function getMemberMoneyMonthAttr($value, $data){
        $sum_money = DB::name('order')->where(['order_status' => 4,'pay_status'=>1,'user_id'=>$data['user_id']])->whereTime('confirm_time', 'm')->sum('total_amount');
        $rebate_money = empty($sum_money) ? (float)0 : $sum_money;
        return  $rebate_money;
    }

    /**
     * 获取全部下线
     * @param $value
     * @param $data
     * @return int|string
     */
    public function getUnderlingNumberAttr($value, $data)
    {
        $fisrt_leader = DB::name('users')->where(['first_leader'=>$data['user_id']])->count();
        $second_leader = DB::name('users')->where(['second_leader'=>$data['user_id']])->count();
        $third_leader = DB::name('users')->where(['third_leader'=>$data['user_id']])->count();
        $underling_number = $fisrt_leader+$second_leader+$third_leader;
        DB::name('users')->where(['user_id'=>$data['user_id']])->save(['underling_number'=>$underling_number]);
        return  $underling_number;
//        return  $count;
    }

    /**
     * 获取上级手机号码
     * @param $value
     * @param $data
     * @return int|string
     */
    public function getFisrtLeaderMobileAttr($value, $data){
        $mobile = Users::where(['user_id'=>$data['first_leader']])->value('mobile');
        return  $mobile;
    }

    /**
     * 用户一级下线数
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getFisrtLeaderNumAttr($value, $data){
        $fisrt_leader = Users::where(['first_leader'=>$data['user_id']])->count();
        return  $fisrt_leader;
    }

    /**
     * 用户二级下线数
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getSecondLeaderNumAttr($value, $data){
        $second_leader = Users::where(['second_leader'=>$data['user_id']])->count();
        return  $second_leader;
    }

    /**
     * 用户二级下线数
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getThirdLeaderNumAttr($value, $data){
        $third_leader = Users::where(['third_leader'=>$data['user_id']])->count();
        return  $third_leader;
    }

}
