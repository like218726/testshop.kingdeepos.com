<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/29
 * Time: 10:02
 */

namespace app\common\model\team;

use think\Db;
use think\Model;

class TeamGoodsItem extends Model
{
    public function specGoodsPrice()
    {
        return $this->hasOne('app\common\model\SpecGoodsPrice', 'item_id', 'item_id');
    }
    public function goods(){
        return $this->hasOne('app\common\model\Goods', 'goods_id', 'goods_id');
    }
    public function teamActivity(){
        return $this->hasOne('teamActivity', 'team_id', 'team_id');
    }

    public function getVirtualNumAttr($value,$data){
        return Db::name('team_activity')->where(['goods_id'=>$data['goods_id']])->getField('virtual_num');
    }
    public function getVirtualSalesNumAttr($value, $data){
        $activity=Db::name('team_activity')->where(['goods_id'=>$data['goods_id']])->find();
        return $activity['sales_sum']+$activity['virtual_num'];
    }
    public function getFollowUsersHeadPicAttr($value,$data){
        $users = db('team_follow')
            ->alias('f')
            ->join('team_activity a','f.team_id=a.team_id')
            ->where(['goods_id'=>$data['goods_id']])
            ->limit(3)->getField('follow_user_head_pic',true);
        return $users;
    }
}