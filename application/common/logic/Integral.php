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
 * Author: dyr
 * Date: 2016-08-09
 */

namespace app\common\logic;

use app\common\model\Goods;
use app\common\model\UserAddress;
use app\common\model\SpecGoodsPrice;
use app\common\util\TpshopException;
use think\Cache;
use think\Model;
use think\Db;


/**
 * 积分商品
 * Class IntegralLogic
 * @package app\common\logic
 */
class Integral extends Model
{
    private $goods;//商品
    private $specGoodsPrice;//商品规格
    private $buyNum;//购买数量
    private $user;//用户
    private $userAddress;//用户地址
    private $userMoney;//使用余额
    private $invoiceTitle;//发票抬头
    private $taxpayer;//纳税识别号
    private $userNote;//用户备注

    public function setInvoiceTitle($invoiceTitle)
    {
        $this->invoiceTitle = $invoiceTitle;
    }
    public function setTaxpayer($taxpayer)
    {
        $this->taxpayer = $taxpayer;
    }
    public function setUserNote($userNote)
    {
        $this->userNote = $userNote;
    }

    public function setGoodsById($goods_id)
    {
        if($goods_id){
            $this->goods = Goods::get($goods_id);
            if(empty($this->goods)){
                throw new TpshopException('积分兑换', 0, ['status' => 0, 'msg' => '该商品不存在']);
            }
        }
    }

    public function setGoods($goods){
        if (empty($goods)) {
            throw new TpshopException('积分兑换', 0, ['status' => 0, 'msg' => '该商品不存在']);
        }else{
            $this->goods = $goods;
        }
    }

    public function setSpecGoodsPrice($specGoodsPrice){
        if (empty($specGoodsPrice)) {
            throw new TpshopException('积分兑换', 0, ['status' => 0, 'msg' => '商品规格不存在']);
        }else{
            $this->specGoodsPrice = $specGoodsPrice;
        }
    }

    public function setSpecGoodsPriceById($item_id){
        if($item_id){
            $this->specGoodsPrice = SpecGoodsPrice::get($item_id);
            if(empty($this->specGoodsPrice)){
                throw new TpshopException('积分兑换', 0, ['status' => 0, 'msg' => '商品规格不存在']);
            }
        }
    }

    public function setBuyNum($buyNum)
    {
        if (empty($buyNum)) {
            throw new TpshopException('积分兑换', 0, ['status' => 0, 'msg' => '购买数不能为零']);
        } else {
            $this->buyNum = $buyNum;
        }
    }

    public function setUser($user)
    {
        if (empty($user)) {
            throw new TpshopException('积分兑换', 0, ['status' => 0, 'msg' => '请登录']);
        } else {
            $this->user = $user;
        }
    }

    public function setUserAddressBydId($address_id)
    {
      if($address_id){
          $this->userAddress = UserAddress::get($address_id);
      }
    }

    public function getUserAddress()
    {
        return $this->userAddress;
    }
    public function setUserMoney($userMoney){
        $this->userMoney = $userMoney;
    }

    /**
     * 购买前检查
     * @throws TpshopException
     */
    public function checkBuy()
    {
        if ($this->goods['is_on_sale'] != 1) {
            throw new TpshopException('积分兑换', 0, ['status' => 0, 'msg' => '商品已下架']);
        }
        if ($this->goods['exchange_integral'] <= 0) {
            throw new TpshopException('积分兑换', 0, ['status' => 0, 'msg' => '该商品不属于积分兑换商品']);
        }
        if ($this->goods['store_count'] == 0) {
            throw new TpshopException('积分兑换', 0, ['status' => 0, 'msg' => '商品库存为零']);
        }
        if ($this->buyNum > $this->goods['store_count']) {
            throw new TpshopException('积分兑换', 0, ['status' => 0, 'msg' => '商品库存不足，剩余' . $this->goods['store_count'] . '份']);
        }
        $total_integral = $this->goods['exchange_integral'] * $this->buyNum;
        if (empty($this->specGoodsPrice)) {
            $goods_spec_list = SpecGoodsPrice::all(['goods_id' => $this->goods['goods_id']]);
            if (count($goods_spec_list) > 0) {
                throw new TpshopException('积分兑换', 0, ['status' => 0, 'msg' => '请传递规格参数', 'result' => '']);
            }
            //没有规格
        } else {
            //有规格
            if ($this->buyNum > $this->specGoodsPrice['store_count']) {
                throw new TpshopException('积分兑换', 0, ['status' => 0, 'msg' => '该商品规格库存不足，剩余' . $this->specGoodsPrice['store_count'] . '份']);
            }
        }
        $integral_use_enable = tpCache('shopping.integral_use_enable');
        //购买设置必须使用积分购买，而用户的积分不足以支付
        if ($total_integral > $this->user['pay_points'] && $integral_use_enable == 1) {
            throw new TpshopException('积分兑换', 0, ['status' => 0, 'msg' => "你的账户可用积分为:" . $this->user['pay_points']]);
        }
    }

    /**
     * 计算价格
     */
    public function pay()
    {
        $integralGoods = $this->goods;
        $total_integral = $this->goods['exchange_integral'] * $this->buyNum;//需要兑换的总积分
        if (empty($this->specGoodsPrice)) {
            //没有规格
            $integralGoods['goods_price'] = $this->goods['shop_price'];
            $integralGoods['sku'] = $this->goods['sku'];
        } else {
            //有规格
            $integralGoods['goods_price'] = $this->specGoodsPrice['price'];
            $integralGoods['spec_key'] = $this->specGoodsPrice['key'];// 商品规格
            $integralGoods['spec_key_name'] = $this->specGoodsPrice['key_name'];// 商品规格名称
            $integralGoods['sku'] = $this->specGoodsPrice['sku'];
        }
        $integralGoods['goods_num'] = $this->buyNum;
        $goodsList[0] = $integralGoods;
        $pay = new Pay();
        $pay->setUserId($this->user['user_id']);
        $pay->payGoodsList($goodsList);
        $pay->delivery($this->userAddress['district']);
        $pay->usePayPoints($total_integral, true);
        $pay->useUserMoney($this->userMoney);
        return $pay;
    }
}