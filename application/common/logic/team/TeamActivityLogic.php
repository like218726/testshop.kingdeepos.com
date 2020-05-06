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
 * Author: lhb
 * Date: 2017-05-15
 */

namespace app\common\logic\team;
use app\common\logic\Prom;
use app\common\model\team\TeamActivity;
use http\Env\Request;
use think\Db;
use app\common\model\SpecGoodsPrice;
use app\common\model\Goods;
use think\Exception;

/**
 * 拼团活动逻辑类
 */
class TeamActivityLogic extends Prom
{
    protected $team;//拼团模型
    protected $goods;//商品模型
    protected $specGoodsPrice;//商品规格模型
    public function __construct($goods,$specGoodsPrice)
    {
        parent::__construct();
        $this->goods = $goods;
        $this->specGoodsPrice = $specGoodsPrice;
        $this->initProm();
    }

    public function initProm(){
        // TODO: Implement initProm() method.
        if($this->specGoodsPrice){
            //活动商品有规格，规格和活动是一对一
            $this->team = TeamActivity::get($this->specGoodsPrice['prom_id'],'',true);
        }else{
            //活动商品没有规格，活动和商品是一对一
            $this->team = TeamActivity::get($this->goods['prom_id'],'',true);
        }
        if ($this->team) {
            //每次初始化都检测活动是否失效，如果失效就恢复商品成普通商品
            if ($this->checkActivityIsEnd()) {
                addLog('teamActivity','每次初始化都检测活动是否失效，如果失效就恢复商品成普通商品', $this->team);
                $this->changePromStatus();
            }
        }
    }

    private function changePromStatus(){
        if($this->specGoodsPrice){
            addLog('teamActivity','每次初始化都检测活动是否失效，如果失效就恢复商品成普通商品--规格', $this->specGoodsPrice);
            Db::name('spec_goods_price')->where('item_id', $this->specGoodsPrice['item_id'])->save(['prom_type' => 0, 'prom_id' => 0]);
            $goodsPromCount = Db::name('spec_goods_price')->where('goods_id', $this->specGoodsPrice['goods_id'])->where('prom_type','>',0)->count('item_id');
            if($goodsPromCount == 0){
                Db::name('goods')->where("goods_id", $this->specGoodsPrice['goods_id'])->save(['prom_type' => 0, 'prom_id' => 0]);
            }
            $item_id = $this->specGoodsPrice['item_id'];
            unset($this->specGoodsPrice);
            $this->specGoodsPrice = SpecGoodsPrice::get($item_id,'',true);
        }else{
            Db::name('goods')->where("goods_id", $this->team['goods_id'])->save(['prom_type' => 0, 'prom_id' => 0]);
        }
        db('team_goods_item')->where('team_id',$this->team->team_id)->update(['deleted'=>1]);
        $goods_id = $this->goods['goods_id'];
        unset($this->goods);
        $this->goods = Goods::get($goods_id);
    }

    public function getPromModel(){
        return $this->team;
    }

    public function getGoodsInfo(){
        return $this->goods;
    }

    public function getActivityGoodsInfo(){
        if($this->specGoodsPrice){
            //活动商品有规格，规格和活动是一对一
            $activityGoods = $this->specGoodsPrice;
            $activityGoods['shop_price']=$activityGoods['price'];
        }else{
            //活动商品没有规格，活动和商品是一对一
            $activityGoods = $this->goods;
        }
        return $activityGoods;
    }

    public function checkActivityIsAble(){
        return $this->IsAble();
    }
    public function checkActivityIsEnd(){
        return !$this->IsAble();
    }
    public function IsAble(){
        if(empty($this->team)){
            return false;
        }
        if($this->team['deleted'] == 1){
            return false;
        }
        if($this->team['status']== 3){
            //审核中不能判断为失效活动
            return false;
        }
        if($this->team['team_type'] == 2 && $this->team['is_lottery'] == 1){
            return false;
        }
        return true;
    }
    /**
     * @param $buyGoods
     * @return array
     */
    public function buyNow($buyGoods){
        $buyGoods['prom_type'] =0;
        $buyGoods['prom_id'] = 0;
        return $buyGoods;
    }
    /**
     * 管理员关闭活动
     */
    public function closeProm()
    {
        if($this->team){
            $this->team['status'] = 3;
            $this->team->save();
            $this->changePromStatus();
        }
    }
    public function getPromId(){
        if($this->team){
            return $this->team['team_id'];
        }else{
            return null;
        }
    }

}