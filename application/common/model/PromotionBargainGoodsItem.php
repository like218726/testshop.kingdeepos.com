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
class PromotionBargainGoodsItem extends Model {
    //自定义初始化
    protected static function init()
    {
        //TODO:自定义的初始化
    }

    public function specGoodsPrice()
    {
        return $this->hasOne('specGoodsPrice', 'item_id', 'item_id');
    }
    //获取该活动的某个商品
    public function getOneBargainGoodsItemAttr($value, $data)
    {
//        return db('promotionBargainGoodsItem')->where(['item_id'=>$data['item_id'],'bargain_id'=>$data['bargain_id']])->find();
        return $data;
    }
}
