<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * Author: IT宇宙人
 * Date: 2015-09-09
 */

namespace app\common\logic;

use app\common\model\GroupBuy;
use app\common\model\SpecGoodsPrice;
use app\common\util\TpshopException;
use think\db;
use app\common\model\Goods;

/**
 * 团购逻辑定义
 * Class CatsLogic
 * @package common\Logic
 */
class GroupBuyLogic extends Prom
{
    protected $GroupBuy;//团购模型
    protected $goods;//商品模型
    protected $specGoodsPrice;//商品规格模型

    public function __construct($goods,$specGoodsPrice)
    {
        parent::__construct();
        $this->goods = $goods;
        $this->specGoodsPrice = $specGoodsPrice;
        $this->initProm();
    }

    public function initProm()
    {
        // TODO: Implement initProm() method.
        if($this->specGoodsPrice){
            //活动商品有规格，规格和活动是一对一
            $this->GroupBuy = GroupBuy::get($this->specGoodsPrice['prom_id'],'',true);
        }else{
            //活动商品没有规格，活动和商品是一对一
            $this->GroupBuy = GroupBuy::get($this->goods['prom_id'],'',true);
        }
        if ($this->GroupBuy) {
            //每次初始化都检测活动是否失效，如果失效就更新活动和商品恢复成普通商品
            if ($this->checkActivityIsEnd() && $this->GroupBuy['is_end'] == 0) {
                if($this->specGoodsPrice){
                    Db::name('spec_goods_price')->where('item_id', $this->specGoodsPrice['item_id'])->save(['prom_type' => 0, 'prom_id' => 0]);
                    $goodsPromCount = Db::name('spec_goods_price')->where('goods_id', $this->specGoodsPrice['goods_id'])->where('prom_type','>',0)->count('item_id');
                    if($goodsPromCount == 0){
                        Db::name('goods')->where("goods_id", $this->specGoodsPrice['goods_id'])->save(['prom_type' => 0, 'prom_id' => 0]);
                    }
                    $item_id = $this->specGoodsPrice['item_id'];
                    unset($this->specGoodsPrice);
                    $this->specGoodsPrice = SpecGoodsPrice::get($item_id,'',true);
                }else{
                    Db::name('goods')->where("goods_id", $this->GroupBuy['goods_id'])->save(['prom_type' => 0, 'prom_id' => 0]);
                }
                $this->GroupBuy->is_end = 1;
                $this->GroupBuy->save();
                $goods_id = $this->goods['goods_id'];
                unset($this->goods);
                $this->goods = Goods::get($goods_id);
            }
        }
    }

    /**
     * 获取团购剩余库存
     */
    public function getPromotionSurplus(){
        $item_id = $this->specGoodsPrice ? $this->specGoodsPrice['item_id'] : 0;
        $GroupBuyGoodsItem = db('group_buy_goods_item')->where(['group_buy_id'=>$this->GroupBuy['id'], 'item_id'=>$item_id])->find();

        return $GroupBuyGoodsItem['goods_num'] - $GroupBuyGoodsItem['buy_num'];//团购剩余库存
    }
    public function getPromModel(){
        return $this->GroupBuy;
    }
    
    /**
     * 获取虚拟参与人数
     * @return number
     */
    public function getVirtualNum(){
        $item_id = $this->specGoodsPrice ? $this->specGoodsPrice['item_id'] : 0;
        $GroupBuyGoodsItem = db('group_buy_goods_item')->where(['group_buy_id'=>$this->GroupBuy['id'], 'item_id'=>$item_id])->find();
        $GroupBuyGoodsItem['goods_num'] - $GroupBuyGoodsItem['buy_num'];//团购剩余库存

        return $GroupBuyGoodsItem['virtual_num'] - $GroupBuyGoodsItem['buy_num'];
    }
    
    /**
     * 活动是否正在进行
     * @return bool
     */
    public function checkActivityIsAble(){
        if (empty($this->GroupBuy)) {
            return false;
        }
        if(time() > $this->GroupBuy['start_time'] && time() < $this->GroupBuy['end_time' ]&& $this->GroupBuy['status'] == 1 && $this->GroupBuy['is_end'] == 0){
            return true;
        }
        return false;
    }
    /**
     * 活动是否结束
     * @return bool
     */
    public function checkActivityIsEnd(){
        if(empty($this->GroupBuy)){
            return true;
        }
        $goods_num = array_sum(array_column(json_decode(json_encode($this->GroupBuy['groupBuyGoodsItem']),true),'goods_num')) ;
        $buy_num = array_sum(array_column(json_decode(json_encode($this->GroupBuy['groupBuyGoodsItem']),true),'buy_num'));
        if($buy_num >= $goods_num){
            return true;
        }
        if(time() > $this->GroupBuy['end_time']){
            return true;
        }
        if($this->GroupBuy['status'] == 3){
            return true;
        }
        return false;
    }
    /**
     * 获取商品原始数据
     * @return Goods
     */
    public function getGoodsInfo()
    {
       return $this->goods;
    }

    /**
     * 获取商品转换活动商品的数据
     * @return static
     */
    public function getActivityGoodsInfo(){
        if($this->specGoodsPrice){
            //活动商品有规格，规格和活动是一对一
            $activityGoods = $this->specGoodsPrice;
            $item_id = $this->specGoodsPrice['item_id'];
        }else{
            //活动商品没有规格，活动和商品是一对一
            $activityGoods = $this->goods;
            $item_id = 0;
        }
        $GroupBuyGoodsItem = db('group_buy_goods_item')->where(['group_buy_id'=>$this->GroupBuy['id'], 'item_id'=>$item_id])->find();
        $activityGoods['activity_title'] = $this->GroupBuy['title'];
        $activityGoods['market_price'] = $GroupBuyGoodsItem['goods_price'];//搞活动把原来的本店价或规格价变成市场价
        $activityGoods['shop_price'] = $GroupBuyGoodsItem['price'];//活动价格
        $activityGoods['store_count'] = $GroupBuyGoodsItem['goods_num'] - $GroupBuyGoodsItem['buy_num'];//剩下参与的活动库存
        $activityGoods['start_time'] = $this->GroupBuy['start_time'];
        $activityGoods['end_time'] = $this->GroupBuy['end_time'];
        $activityGoods['virtual_num'] = $GroupBuyGoodsItem['virtual_num'] + $GroupBuyGoodsItem['buy_num'];

        return $activityGoods;
    }

    /**
     * 该活动是否已经失效
     */
    public function IsAble(){
        if(empty($this->GroupBuy)){
            return false;
        }
        $item_id = $this->specGoodsPrice ? $this->specGoodsPrice['item_id'] : 0;
        $GroupBuyGoodsItem = db('group_buy_goods_item')->where(['group_buy_id'=>$this->GroupBuy['id'], 'item_id'=>$item_id])->find();
        if($GroupBuyGoodsItem['buy_num'] >= $GroupBuyGoodsItem['goods_num']){
            return false;
        }
        if(time() > $this->GroupBuy['end_time']){
            return false;
        }
        if($this->GroupBuy['status'] != 1){
            return false;
        }
        if($this->GroupBuy['is_end'] == 1){
            return false;
        }
        return true;
    }

    /**
     * 立即购买
     * @param $buyGoods
     * @return mixed
     * @throws TpshopException
     */
    public function buyNow($buyGoods){
        //活动是否已经结束
        if($this->GroupBuy['is_end'] == 1 || empty($this->GroupBuy)){
            throw new TpshopException('立即购买',0,['status' => 0, 'msg' => '团购活动已结束', 'result' => '']);
        }
        if($this->checkActivityIsAble()){
            $item_id = $this->specGoodsPrice ? $this->specGoodsPrice['item_id'] : 0;
            $GroupBuyGoodsItem = db('group_buy_goods_item')->where(['group_buy_id'=>$this->GroupBuy['id'], 'item_id'=>$item_id])->find();
            $groupBuyPurchase = $GroupBuyGoodsItem['goods_num'] - $GroupBuyGoodsItem['buy_num'];//团购剩余库存
            if($buyGoods['goods_num'] > $groupBuyPurchase){
                throw new TpshopException('立即购买',0,['status' => 0, 'msg' => '商品库存不足，剩余'.$groupBuyPurchase, 'result' => '']);
            }
            $buyGoods['member_goods_price'] = $GroupBuyGoodsItem['price'];
            $buyGoods['prom_type'] = 2;
            $buyGoods['prom_id'] = $this->GroupBuy['id'];
        }
        return $buyGoods;
    }

    /**
     * 管理员关闭活动
     */
    public function closeProm()
    {
        if($this->GroupBuy){
            $this->GroupBuy['status'] = 3;
            $this->GroupBuy->save();
            $group_buy_goods_item = db('group_buy_goods_item')->where('group_buy_id', $this->GroupBuy['id'])->select();
            foreach($group_buy_goods_item as $v) {
                if ($v['item_id']) {
                    Db::name('spec_goods_price')->where('item_id', $v['item_id'])->save(['prom_type' => 0, 'prom_id' => 0]);
                }
            }
        }
    }
    public function getPromId(){
        if($this->GroupBuy){
            return $this->GroupBuy['id'];
        }else{
            return null;
        }
    }

}