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

namespace app\common\logic\bargain;

use app\common\logic\Prom;
use app\common\model\BargainFirst;
use app\common\model\Goods;
use app\common\model\PromotionBargainGoodsItem;
use app\common\model\SpecGoodsPrice;
use app\common\util\TpshopException;
use think\Model;
use think\db;

/**
 * 砍价逻辑定义
 * Class CatsLogic
 * @package admin\Logic
 */
class PromotionBargainLogic extends Prom
{
    protected $bargain;//砍价活动模型
    protected $goods;//商品模型
    protected $user_id;//用户id
    protected $specGoodsPrice;//商品规格模型

    public function __construct($goods, $specGoodsPrice)
    {
        parent::__construct();
        $this->goods = $goods;
        $this->specGoodsPrice = $specGoodsPrice;
        $this->initProm();
    }

    public function initProm()
    {
        if($this->specGoodsPrice){
            //活动商品有规格，规格和活动是一对一
            $this->bargain = \app\common\model\PromotionBargain::get($this->specGoodsPrice['prom_id']);
			if ($this->bargain && !PromotionBargainGoodsItem::get(['item_id'=>$this->specGoodsPrice['item_id'],'bargain_id'=>$this->bargain['id'],'goods_num' =>['exp','>buy_num']])) {
				//砍价规格已经卖完了
				$count = Db::name('spec_goods_price')->where('goods_id', $this->specGoodsPrice['goods_id'])->where('prom_type','>',0)->count();
				if ($count > 1) {
					//规格数量大于1时才走这里
					Db::name('spec_goods_price')->where(['prom_type' => 8, 'prom_id' => $this->bargain['id'], 'item_id' => $this->specGoodsPrice['item_id']])->save(['prom_type' => 0, 'prom_id' => 0]);
					$this->bargain = null;
				}
			}
        }else{
            //活动商品没有规格，活动和商品是一对一
            $this->bargain = \app\common\model\PromotionBargain::get($this->goods['prom_id']);
        }

        if ($this->bargain) {
            //每次初始化都检测活动是否结束，如果失效就更新活动和商品恢复成普通商品
//            if ($this->checkActivityIsEnd() && $this->bargain['is_end'] == 0) {
            if ($this->checkActivityIsEnd()) {
                if($this->specGoodsPrice){
                    Db::name('spec_goods_price')->where(['prom_type' => 8, 'prom_id' => $this->bargain['id']])->save(['prom_type' => 0, 'prom_id' => 0]);
                    $goodsPromCount = Db::name('spec_goods_price')->where('goods_id', $this->specGoodsPrice['goods_id'])->where('prom_type','>',0)->count('item_id');
                    if($goodsPromCount == 0){
                        Db::name('goods')->where("goods_id", $this->specGoodsPrice['goods_id'])->save(['prom_type' => 0, 'prom_id' => 0]);
                    }
                    //unset($this->specGoodsPrice);
                    $this->specGoodsPrice = SpecGoodsPrice::get($this->specGoodsPrice['item_id']);
                }else{
                    Db::name('goods')->where(["prom_id"=>$this->bargain['id'],'prom_type'=>8])->save(['prom_type' => 0, 'prom_id' => 0]);
                }
                $this->bargain->is_end = 1;
                $this->bargain->save();
                //unset($this->goods);
                $this->goods = Goods::get($this->goods['goods_id']);
            }
        }
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }
    /**
     * 活动是否正在进行
     * @return bool
     */
    public function checkActivityIsAble(){
        if(empty($this->bargain)){
            return false;
        }
        if(time() > $this->bargain['start_time'] && time() < $this->bargain['end_time'] && $this->bargain['is_end'] == 0){
            return true;
        }
        return false;
    }

    /**
     * 活动是否结束
     * @return bool
     */
    public function checkActivityIsEnd(){
        if(empty($this->bargain)){
            return true;
        }

        $goods_num = array_sum(array_column(json_decode(json_encode($this->bargain['promotionBargainGoodsItem']),true),'goods_num')) ;
        $buy_num = array_sum(array_column(json_decode(json_encode($this->bargain['promotionBargainGoodsItem']),true),'buy_num'));
        if($goods_num <= $buy_num){
            return true;
        }
//        foreach ($this->bargain['promotionBargainGoodsItem'] as $v){
//            if($v['goods_num'] == $v['buy_num']){
//                return true;
//            }
//        }

        if(time() > $this->bargain['end_time']){
            return true;
        }
        if($this->bargain['is_end'] == 1){
            return true;
        }
        return false;
    }

    /**
     * 获取用户已购商品数量
     * @param $user_id
     * @return float|int
     */
    public function getUserBargainGoodsNum($user_id){
        $orderWhere = [
            'user_id'=>$user_id,
            'order_status' => ['<>', 3],
            'add_time' => ['between', [$this->bargain['start_time'], $this->bargain['end_time']]]
        ];
        $order_id_arr = Db::name('order')->where($orderWhere)->getField('order_id', true);
        if ($order_id_arr) {
            $orderGoodsWhere = ['prom_id' => $this->bargain['id'], 'prom_type' => 8, 'order_id' => ['in', implode(',', $order_id_arr)]];
            $goods_num = DB::name('order_goods')->where($orderGoodsWhere)->sum('goods_num');
            if($goods_num){
                return $goods_num;
            }else{
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * 获取用户剩余商品数量
     * @author lxl 2017-5-11
     * @param $user_id|用户ID
     * @return mixed
     */
//    public function getUserFlashResidueGoodsNum($user_id){
//        $purchase_num = $this->getUserBargainGoodsNum($user_id); //用户已购商品数量
//        $residue_num = $this->bargain['goods_num'] - $this->bargain['buy_num']; //剩余库存
//        //限购》已购
//        $residue_buy_limit = $this->bargain['buy_limit'] - $purchase_num;
//        if($residue_buy_limit > $residue_num){
//            return $residue_num;
//        }else{
//            return $residue_buy_limit;
//        }
//    }

    /**
     * 获取单个活动
     * @return static
     */
    public function getPromModel(){
        return $this->bargain;
    }
    /**
     * 获取商品原始数据
     * @return static
     */
    public function getGoodsInfo()
    {
        return $this->goods;
    }


    /**
     * 获取商品转换活动商品的数据
     * @return null|static
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function getActivityGoodsInfo(){
        if($this->specGoodsPrice){
            //活动商品有规格，规格和活动是一对一
            $activityGoods = $this->specGoodsPrice;
            $activityGoods['market_price'] =$this->specGoodsPrice['price'];
        }else{
            //活动商品没有规格，活动和商品是一对一
            $activityGoods = $this->goods;
            $activityGoods['market_price'] =$this->goods['shop_price'];
            $activityGoods['item_id'] = 0;
        }

        $promotionBargainGoodsItem =  PromotionBargainGoodsItem::get(['item_id'=>$activityGoods['item_id'],'bargain_id'=>$this->bargain['id']]);
        $activityGoods['activity_title'] = $this->bargain['title'];
        $activityGoods['shop_price'] = $promotionBargainGoodsItem['start_price'];
        $activityGoods['goods_store_count'] = $activityGoods['store_count'];
        $activityGoods['surplus_store_count'] = $this->bargain['store_count'];
        $activityGoods['store_count'] = $promotionBargainGoodsItem['goods_num'] - $promotionBargainGoodsItem['buy_num'];
        $activityGoods['start_time'] = $this->bargain['start_time'];
        $activityGoods['end_price'] = $promotionBargainGoodsItem['end_price'];
        $activityGoods['end_time'] = $this->bargain['end_time'];
        $activityGoods['buy_limit'] = $this->bargain['buy_limit'];
        $activityGoods['bargain'] = $this->bargain;
        $activityGoods['virtual_num'] =0;
        $bargain_first = 0;
        if(session('user')){
            $bargain_first = db('bargain_first')->where(['user_id'=>session('user.user_id'),'bargain_id'=>$this->bargain['id']])->find();
        }
        $activityGoods['bargain_first'] = $bargain_first?1:0;//是否已参与
//        $activityGoods['bargain_list'] = db('bargain_list')->where(['bargain_first_id'=>$bargain_first['id']])->select();
        $activityGoods['bargain_list'] = db('bargain_first')->alias('b')->field('u.head_pic')->join('__USERS__ u','u.user_id = b.user_id')->where(['bargain_id'=>$this->bargain['id']])->order('id desc')->select();
        return $activityGoods;
    }

    /**
     * 该活动是否已经失效
     */
    public function IsAble(){

        if(empty($this->bargain)){
            return false;
        }
        if($this->bargain['is_end'] == 1){
            return false;
        }
        $goods_num = array_sum(array_column(json_decode(json_encode($this->bargain['promotionBargainGoodsItem']),true),'goods_num'));
        $buy_num = array_sum(array_column(json_decode(json_encode($this->bargain['promotionBargainGoodsItem']),true),'buy_num'));
        if($goods_num <= $buy_num){
            return false;
        }
//        if($this->bargain['buy_num'] >= $this->bargain['goods_num']){
//            return false;
//        }
        if(time() > $this->bargain['end_time']){
            return false;
        }
        return true;
    }

    /**
     * 砍价商品立即购买
     * @param $buyGoods
     * @return mixed
     * @throws TpshopException
     */
    public function buyNow($buyGoods){
        if($this->checkActivityIsEnd()){
            throw new TpshopException('砍价商品立即购买', 0, ['status' => 0, 'msg' => '该活动已结束或者不存在', 'result' => '']);
        }
        if($this->checkActivityIsAble()){
            if($buyGoods['goods_num'] > $this->bargain['buy_limit']){
                throw new TpshopException('砍价商品立即购买', 0, ['status' => 0, 'msg' => '每人限购'.$this->bargain['buy_limit'].'件', 'result' => '']);
            }
        }
        $userFlashOrderGoodsNum = $this->getUserBargainGoodsNum($buyGoods['user_id']); //获取用户抢购已购商品数量
        $userBuyGoodsNum = $buyGoods['goods_num'] + $userFlashOrderGoodsNum;
        if($userBuyGoodsNum > $this->bargain['buy_limit']){
            throw new TpshopException('砍价商品立即购买', 0, ['status' => 0, 'msg' => '每人限购'.$this->bargain['buy_limit'].'件，您已下单'.$userFlashOrderGoodsNum.'件', 'result' => '']);
        }
        $bargain_first  = BargainFirst::get(['bargain_id'=>$this->bargain['id'],'user_id'=>$this->user_id,'order_id'=>0]);
		if(!$bargain_first){
            throw new TpshopException('砍价商品立即购买', 0, ['status' => 0, 'msg' => '无砍价信息或砍价订单已提交', 'result' => '']);
        }
        $promotionBargainGoodsItem =  PromotionBargainGoodsItem::get(['item_id'=>$bargain_first['item_id'],'bargain_id'=>$this->bargain['id']]);
        $flashSalePurchase = $promotionBargainGoodsItem['goods_num'] - $promotionBargainGoodsItem['buy_num'];//剩余库存
        if($buyGoods['goods_num'] > $flashSalePurchase){
            throw new TpshopException('砍价商品立即购买', 0, ['status' => 0, 'msg' => '商品库存不足，剩余'.$flashSalePurchase, 'result' => '']);
        }
        $buyGoods['member_goods_price'] = $bargain_first['end_price'];
        $buyGoods['goods_price'] = $bargain_first['end_price'];
        $buyGoods['prom_type'] = 8;
        $buyGoods['prom_id'] = $this->bargain['id'];
        return $buyGoods;
    }

    public function getPromId(){
        if($this->bargain){
            return $this->bargain['id'];
        }else{
            return null;
        }
    }

    /**
     * 管理员关闭活动
     */
    public function closeProm()
    {
        if($this->bargain){
            $this->bargain['status'] = 3;
            $this->bargain->save();
            $promotion_bargain_goods_item = db('promotion_bargain_goods_item')->where('bargain_id', $this->bargain['id'])->select();
            foreach($promotion_bargain_goods_item as $v) {
                if ($v['item_id']) {
                    Db::name('spec_goods_price')->where('item_id', $v['item_id'])->save(['prom_type' => 0, 'prom_id' => 0]);
                }
            }
        }
    }
}