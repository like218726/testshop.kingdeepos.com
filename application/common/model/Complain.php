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

class Complain extends Model
{
    public function goods()
    {
        return $this->hasOne('goods','goods_id','order_goods_id');
    }

    public function getComplainStateAttr($value,$data){
        $complain_state = array(1=>'待处理',2=>'对话中',3=>'待仲裁',4=>'已完成');
        return $complain_state[$data['complain_state']];
    }

    public function getComplainPicAttr($value,$data){
       return  unserialize($data['complain_pic']);
    }
}
