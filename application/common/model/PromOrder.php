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
use think\Model;
class PromOrder extends Model {
    public function getPromDescAttr($value,$data)
    {
        $parse_type = array('0' => '满额打折', '1' => '满额优惠金额', '2' => '满额送积分', '3' => '满额送优惠券');
        return $parse_type[$data['type']];
    }

    public function getPromDetailAttr($value,$data)
    {
        switch ($data['type']){
            case 1:
                $title = '订单满'.$data['money'].'元减'.$data['expression'].'元';
                break;
            case 2:
                $title = '订单满'.$data['money'].'元送'.$data['expression'].'积分';
                break;
            case 3:
                $coupon = db('coupon')->where('id', $data['expression'])->find();
                $title = '订单满' . $data['money'] . '元送' . $coupon['name'] . '优惠券￥' . $coupon['money'];
                break;
            default:
                $title = '订单满'.$data['money'].'元打'. $data['expression']/10 . '折';
        }
        return $title;
    }
    //状态描述
    public function getStatusDescAttr($value, $data)
    {
        $status = array(0=>'管理员关闭', 1=>'正常');
        if($data['status'] != 1){
            return $status[$data['status']];
        }else{
            if(time() < $data['start_time']){
                return '未开始';
            }else if(time() > $data['start_time'] && time() < $data['end_time'] ){
                return '进行中';
            }else{
                return '已结束';
            }
        }
    }
}
