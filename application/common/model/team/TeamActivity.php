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
namespace app\common\model\team;

use think\Model;
use think\Request;

class TeamActivity extends Model
{
    public function teamGoodsItem()
    {
        return $this->hasMany('teamGoodsItem','team_id','team_id');
    }

    public function goods(){
        return $this->hasOne('app\common\model\Goods','goods_id','goods_id')->bind(['shop_price'=>'shop_price']);
    }
    public function store(){
        return $this->hasOne('app\common\model\Store','store_id','store_id');
    }
    public function teamFound(){
        return $this->hasMany('teamFound','team_id','team_id');
    }

    public function getTeamTypeDescAttr($value, $data){
        $status = config('TEAM_TYPE');
        return $status[$data['team_type']];
    }
    public function getTimeLimitHoursAttr($value, $data){
        return $data['time_limit'] / 3600;
    }
    //分享链接
    public function getBdUrlAttr($value, $data){
        return U('Mobile/Team/info',['goods_id'=>$data['goods_id'],'team_id'=>$data['team_id']],'',true);
    }
    public function getBdPicAttr($value, $data){
        $request = Request::instance();
        return $request->domain().$data['share_img'];
    }
    public function getLotteryUrlAttr($value, $data){
        return U('Mobile/Team/lottery',['team_id'=>$data['team_id']],'',true);
    }
    public function getStatusDescAttr($value, $data){
        if($this->isDelete($data['team_id'])){
            return '已删除';
        }
        $status = array('审核中', '进行中', '审核失败', '管理员关闭');
        if ($data['is_lottery'] == 1) {
            return '已开奖';
        }
        return $status[$data['status']];
    }
    // 检测拼团活动有没有删除 team_goods_item表判断
    public function isDelete($team_id){
        $arr = db('team_goods_item')->where('team_id',$team_id)->column('deleted');
        if(!in_array(0,$arr)){
            return true;
        }
        return false;
    }
    public function getVirtualSaleNumAttr($value, $data){
        return $data['virtual_num'] + $data['sales_sum'];
    }

    /**
     * 前台显示拼团详情
     */
    public function getFrontStatusDescAttr($value, $data){
        if($data['status'] != 1){
            return '活动未上架';
        }
        if($data['team_type'] == 2){
            if($data['is_lottery'] == 1){
                return '已开奖';
            }else{
                return '拼团中';
            }
        }else{
            return '拼团中';
        }
    }

    public function getVirtualSalesNumAttr($value, $data){
        return $data['sales_sum']+$data['virtual_num'];
    }
    public function setTimeLimitAttr($value, $data){
        return $value * 3600;
    }
    public function setBonusAttr($value,$data)
    {
        return ($data['team_type'] != 1) ? 0 : $value;
    }
    public function setBuyLimitAttr($value,$data){
        return ($data['team_type'] == 2) ? 1 : $value;
    }
    public function getFollowUsersHeadPicAttr($value,$data){
        $users = db('team_follow')->where('team_id',$data['team_id'])->field('follow_user_head_pic')->limit(3)->select();
        return $users;
    }
}
