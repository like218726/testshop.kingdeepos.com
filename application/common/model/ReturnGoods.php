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

class ReturnGoods extends Model{
    /**
     * 售后类型
     * @param $value
     * @param $data
     */
    public function getServiceTypeAttr($value, $data)
    {
        $return_type =  C('RETURN_TYPE');
        return $return_type[$data['type']];
    }

    /**
     * 售后状态
     * @param $value
     * @param $data
     * @return string
     */
    public function getServiceStatusAttr($value, $data)
    {
        $return_status =  C('RETURN_STATUS');
        if($data['type']== 0 && $data['status']==3){
            return '待退款';
        }
        return $return_status[$data['status']];
    }
}