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
class PromotionBargain extends Model {
    //自定义初始化
    protected static function init()
    {
        //TODO:自定义的初始化
    }


    public function goods()
    {
        return $this->hasOne('goods','goods_id','goods_id');
    }
    public function promotionBargainGoodsItem()
    {
        return $this->hasMany('promotionBargainGoodsItem','bargain_id','id');
    }

    //剩余库存
    public function getStoreCountAttr($value, $data)
    {
        $data['promotionBargainGoodsItem'] = db('promotion_bargain_goods_item')->field('sum(goods_num) as goods_num , sum(buy_num) as buy_num')->where(['bargain_id'=>$data['id']])->find();
        return $data['promotionBargainGoodsItem']['goods_num'] - $data['promotionBargainGoodsItem']['buy_num'];
    }


    /**
     * 状态描述
     * @param $value
     * @param $data
     * @return string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function getStatusDescAttr($value, $data)
    {
        if($data['is_end'] == 1){
            return '已结束';
        }else{
            $promotion_bargain_goods_item =  db('promotion_bargain_goods_item')->field('*,sum(buy_num) as buy_num ,sum(goods_num) as goods_num ')->where(['bargain_id'=>$data['id']])->find();
            if($promotion_bargain_goods_item['buy_num'] >= $promotion_bargain_goods_item['goods_num']){
                return '已售罄';
            }else{
                if($data['start_time'] > time()){
                    return '未开始';
                }else if ($data['start_time'] < time() && $data['end_time'] > time()) {
                    return '进行中';
                }else{
                    Db::name('promotion_bargain')->where(['id'=>$data['id']])->update(['is_end'=>1]);
                    if($promotion_bargain_goods_item['item_id']){
                        Db::name('spec_goods_price')->where(['prom_type' => 8, 'prom_id' => $data['id']])->save(['prom_type' => 0, 'prom_id' => 0]);
                        $goodsPromCount = Db::name('spec_goods_price')->where('goods_id', $data['goods_id'])->where('prom_type','>',0)->count('item_id');
                        if($goodsPromCount == 0){
                            Db::name('goods')->where("goods_id", $data['goods_id'])->save(['prom_type' => 0, 'prom_id' => 0]);
                        }
                    }else{
                        Db::name('goods')->where(["prom_id"=>$data['id'],'prom_type'=>8])->save(['prom_type' => 0, 'prom_id' => 0]);
                    }
                    return '已过期';

//
                }
            }
        }
    }

    /**
     * 是否编辑
     * @param $value
     * @param $data
     * @return int
     */
    public function getIsEditAttr($value, $data)
    {
        if ($data['is_end'] == 1 || $data['start_time'] < time()){
            return 0;
        }
        return 1;
    }

    /**
     * 获取商品的原始价格
     */
//    public function getShopPriceAttr($value, $data){
//        if($data['item_id']>0){
//            //获取规格价格
//            $price = $this->specGoodsPrice->price;
//            unset($this->specGoodsPrice);
//            return $price;
//        }else{
//            return $value;
//        }
//
//    }



}
