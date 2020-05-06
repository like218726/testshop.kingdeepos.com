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
class StoreAddress extends Model {
    //自定义初始化
    protected static function init()
    {
        //TODO:自定义的初始化
    }
    public function store()
    {
        return $this->hasOne('store','store_id','store_id');
    }

    public function getFullAddressAttr($value, $data)
    {
        $region = Db::name('region')->where('id', 'IN', [$data['province_id'], $data['city_id'], $data['district_id']])->column('name');
        return implode('', $region) . $data['address'];
    }
    public function getTypeDescAttr($value, $data)
    {
        if($data['type'] == 1){
            return '收货';
        }else{
            return '发货';
        }
    }
    public function getIsDefaultDescAttr($value, $data)
    {
        if($data['is_default'] == 1){
            return '是';
        }else{
            return '否';
        }
    }
}
