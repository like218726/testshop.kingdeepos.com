<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采有最新thinkphp5助手函数特性实现函数简写方式M D U等,也可db::name方式,可双向兼容
 * ============================================================================
 * Author: dyr
 * Date: 2017-12-04
 */

namespace app\common\logic;

use app\common\model\CouponList;
use app\common\model\Shop;
use app\common\util\TpshopException;
use think\Model;
use think\Db;

/**
 * 计算价格类
 * Class CatsLogic
 * @package Home\Logic
 */
class Pay
{
    protected $payList;
    protected $userId;
    protected $user;

    private $totalAmount = 0;//订单总价
    private $orderAmount = 0;//应付金额
    private $shippingPrice = 0;//物流费
    private $goodsPrice = 0;//商品总价
    private $cutFee = 0;//共节约多少钱
    private $totalNum = 0;// 商品总共数量
    private $integralMoney = 0;//积分抵消金额
    private $userMoney = 0;//使用余额
    private $payPoints = 0;//使用积分
    private $couponPrice = 0;//优惠券抵消金额
    private $orderPromAmount = 0;

    private $storeListPayInfo;//各个店铺的价格信息，key为店铺ID
    private $storeList;//店铺信息
    private $userCoupons;
    private $shop;//自提点
    private $totalShippingPrice = 0;//抛开满免的物流费总额

    /**
     * 计算订单表的普通订单商品
     * @param $order_goods
     * @throws TpshopException
     */
    public function payOrder($order_goods)
    {
        $this->payList = $order_goods;
        $order = Db::name('order')->where('order_id', $this->payList[0]['order_id'])->find();
        if (empty($order)) {
            throw new TpshopException('计算订单价格', 0, ['status' => -9, 'msg' => '找不到订单数据', 'result' => '']);
        }
        $reduce = tpCache('shopping.reduce');
        if ($order['pay_status'] == 0 && $reduce == 2) {
            $goodsListCount = count($this->payList);
            for ($payCursor = 0; $payCursor < $goodsListCount; $payCursor++) {
                $goods_stock = getGoodNum($this->payList[$payCursor]['goods_id'], $this->payList[$payCursor]['spec_key']); // 最多可购买的库存数量
                if ($goods_stock <= 0 && $this->payList[$payCursor]['goods_num'] > $goods_stock) {
                    throw new TpshopException('计算订单价格', 0, ['status' => -9, 'msg' => $this->payList[$payCursor]['goods_name'] . ',' . $this->payList[$payCursor]['spec_key_name'] . "库存不足,请重新下单", 'result' => '']);
                }
            }
        }
        $this->Calculation();
    }

    /**
     * 计算购买购物车的商品
     * @param $cart_list
     * @throws TpshopException
     */
    public function payCart($cart_list)
    {
        $this->payList = $cart_list;
        $goodsListCount = count($this->payList);
        if ($goodsListCount == 0) {
            throw new TpshopException('计算订单价格', 0, ['status' => -9, 'msg' => '你的购物车没有选中商品', 'result' => '']);
        }
        $this->Calculation();
    }

    /**
     * 计算购买商品表的商品
     * @param $goods_list
     * @throws TpshopException
     */
    public function payGoodsList($goods_list)
    {
        $goods_list_count = count($goods_list);
        if ($goods_list_count == 0) {
            throw new TpshopException('计算订单价格', 0, ['status' => -9, 'msg' => '你的购物车没有选中商品', 'result' => '']);
        }
        $discount = $this->getDiscount();
        for ($goods_cursor = 0; $goods_cursor < $goods_list_count; $goods_cursor++) {
            //优先使用member_goods_price，没有member_goods_price使用goods_price
            if(empty($goods_list[$goods_cursor]['member_goods_price'])){
                //积分商品不打折。因为是全积分商品打会员折扣，结算会出现负数
                if($goods_list[$goods_cursor]['exchange_integral'] > 0){
                    $goods_list[$goods_cursor]['member_goods_price'] = $goods_list[$goods_cursor]['goods_price'];
                }else{
                    if ( $goods_list[$goods_cursor]['prom_type'] !='3' ) {
                        $goods_list[$goods_cursor]['member_goods_price'] = $discount * $goods_list[$goods_cursor]['goods_price'];//优先使用member_goods_price，没有member_goods_price使用goods_price
                    }
                }
            }

        }
        $this->payList = $goods_list;
        $this->Calculation();
    }

    /**
     * 获取折扣
     * @return int
     */
    private function getDiscount()
    {
        if(empty($this->user['discount'])){
            return 1;
        }else{
            return $this->user['discount'];
        }
    }


    /**
     * 初始化计算
     */
    private function Calculation()
    {
        $goodsListCount = count($this->payList);
        for ($payCursor = 0; $payCursor < $goodsListCount; $payCursor++) {
            $this->payList[$payCursor]['goods_fee'] = $this->payList[$payCursor]['goods_num'] * $this->payList[$payCursor]['member_goods_price'];    // 小计
            $this->goodsPrice += $this->payList[$payCursor]['goods_fee']; // 商品总价
            if (array_key_exists('market_price', $this->payList[$payCursor])) {
                $this->cutFee += $this->payList[$payCursor]['goods_num'] * ($this->payList[$payCursor]['market_price'] - $this->payList[$payCursor]['member_goods_price']);// 共节约
            }
            $this->totalNum += $this->payList[$payCursor]['goods_num'];
            $this->storeListPayInfo[$this->payList[$payCursor]['store_id']]['goods_price'] += $this->payList[$payCursor]['goods_fee']; //每个商家的商品总价
            $this->storeListPayInfo[$this->payList[$payCursor]['store_id']]['order_amount'] = $this->storeListPayInfo[$this->payList[$payCursor]['store_id']]['goods_price']; //每个商家的应付金额
            $this->storeListPayInfo[$this->payList[$payCursor]['store_id']]['total_amount'] = $this->storeListPayInfo[$this->payList[$payCursor]['store_id']]['goods_price']; //每个商家的订单总价
            $this->storeListPayInfo[$this->payList[$payCursor]['store_id']]['suppliers_id_arr'][] = $this->payList[$payCursor]['suppliers_id']; //每个商家的商品的供应商的数组
        }
        $store_ids = array_keys($this->storeListPayInfo);//获取store_id集合
        $this->storeList = Db::name('store')->where('store_id', 'in', $store_ids)->cache(true, 100)->getField('store_id,store_free_price,store_state');
        $this->orderAmount = $this->goodsPrice;
        $this->totalAmount = $this->goodsPrice;
    }

    /**
     * 设置用户ID
     * @throws TpshopException
     * @param $user_id
     */
    public function setUserId($user_id)
    {
        $this->userId = $user_id;
        $this->user = Db::name('users')->where(['user_id' => $this->userId])->find();
        if (empty($this->user)) {
            throw new TpshopException("计算订单价格", 101, ['status' => 0, 'msg' => '未找到用户', 'result' => '']);
        }
    }

    /**
     * 设置自提点id
     * @param $shop_id
     * @return $this
     */
    public function setShopById($shop_id)
    {
        if ($shop_id) {
            $this->shop = Shop::get($shop_id);
        }
        return $this;
    }

    /**
     * 使用积分
     * @throws TpshopException
     * @param $pay_points
     * @param $is_exchange |是否有使用积分兑换商品流程
     */
    public function usePayPoints($pay_points, $is_exchange = false)
    {
        if ($pay_points > 0 && $this->orderAmount > 0) {
            $point_rate = tpCache('shopping.point_rate'); //兑换比例
            if ($is_exchange == false) {
                $use_percent_point = tpCache('shopping.point_use_percent') / 100;     //最大使用限制: 最大使用积分比例, 例如: 为50时, 未50% , 那么积分支付抵扣金额不能超过应付金额的50%
                $min_use_limit_point = tpCache('shopping.point_min_limit'); //最低使用额度: 如果拥有的积分小于该值, 不可使用
                if ($use_percent_point == 0) {
                    throw new TpshopException("计算订单价格", 0, ['status' => -1, 'msg' => '该笔订单不能使用积分', 'result' => '']);
                }
                if ($min_use_limit_point > $this->user['pay_points']) {
                    throw new TpshopException("计算订单价格", 0, ['status' => -1, 'msg' => '积分需大于'.$min_use_limit_point,'result' => '']);
                }
                if ($pay_points > $this->user['pay_points']) {
                    throw new TpshopException("计算订单价格", 0, ['status' => -1, 'msg' => '你的账户可用积分为：'.$this->user['pay_points'],'result' => '']);
                }
                if ($use_percent_point > 0 && $use_percent_point <= 1) {
                    //计算订单最多使用多少积分,默认只用最大值
                    $pay_points2 = intval($this->totalAmount * $point_rate * $use_percent_point);
                    if($pay_points2 < $pay_points){
                        throw new TpshopException("计算订单价格", 0, ['status' => -1, 'msg' => "该笔订单, 您使用的积分不能超过" . $pay_points2,'result' => '']);
                    }
                }
                //计算订单最多使用多少积分,默认只用最大值
                $point_limit = $this->orderAmount * $point_rate*$use_percent_point;
                if($pay_points > $point_limit){
                    // 直接使用订单的最大积分抵扣
                    $this->payPoints = $point_limit;
                }else{
                    // 改为填多少积分使用多少积分
                    $this->payPoints = $pay_points;
                }

            } else {
                //积分兑换流程
                if ($pay_points <= $this->user['pay_points']) {
                    $this->payPoints = $pay_points;
                } else {
                    $this->payPoints = 0;//需要兑换的总积分
                }
            }
            $surplus_pay_points = $this->payPoints;
            foreach ($this->storeListPayInfo as $infoKey => $infoVal) {
                $proportion = $infoVal['order_amount'] / $this->orderAmount;//每个商家订单应付金额占总应付金额比例
                $store_integral = round($proportion * $this->payPoints, 1); //每个商家平摊用了多少积分;
                $surplus_pay_points = $surplus_pay_points - $store_integral;//剩余用户积分
                $this->storeListPayInfo[$infoKey]['integral'] = $store_integral;//每个商家平摊用了多少积分
                $this->storeListPayInfo[$infoKey]['integral_money'] = $this->storeListPayInfo[$infoKey]['integral'] / $point_rate; //每个商家平摊用了多少积分抵扣金额
                $this->storeListPayInfo[$infoKey]['order_amount'] = round(round($this->storeListPayInfo[$infoKey]['order_amount'], 4) - round($this->storeListPayInfo[$infoKey]['integral_money'], 4), 2);// 每个商家减去积分支付抵消的
            }
            if ($surplus_pay_points > 0) {
                //把剩余的用户积分平摊给第一个商家订单
                foreach ($this->storeListPayInfo as $infoKey => $infoVal) {
                    $this->storeListPayInfo[$infoKey]['integral'] += $surplus_pay_points;//第一个商家平摊用了多少积分抵扣金额
                    $this->storeListPayInfo[$infoKey]['integral_money'] = $this->storeListPayInfo[$infoKey]['integral'] / $point_rate; //第一个商家平摊用了多少积分抵扣金额
                    $this->storeListPayInfo[$infoKey]['order_amount'] -= $this->storeListPayInfo[$infoKey]['integral_money'];// 第一个商家减去积分支付抵消的
                    break;
                }
            }
            $this->integralMoney = $this->payPoints / $point_rate;//总积分兑换成的金额
            $this->orderAmount = $this->orderAmount - $this->integralMoney;

        }
    }

    /**
     * 使用余额
     * @throws TpshopException
     * @param $user_money
     */
    public function useUserMoney($user_money)
    {
        if ($user_money > 0 && $this->orderAmount > 0) {
            if ($user_money > $this->user['user_money']) {
                throw new TpshopException("计算订单价格", 0, ['status' => -6, 'msg' => "你的账户可用余额为:" . $this->user['user_money'], 'result' => '']);
            }
            if ($user_money > $this->orderAmount) {
                $this->userMoney = $this->orderAmount;
            } else {
                $this->userMoney = $user_money;
            }
            $surplus_user_money = $this->userMoney;
            foreach ($this->storeListPayInfo as $infoKey => $infoVal) {
                $proportion = $infoVal['order_amount'] / $this->orderAmount;//每个商家订单应付金额占总应付金额比例
                $store_user_money = round($proportion * $this->userMoney, 2);//每个商家平摊用了多少余额,保留两位小数;
                $surplus_user_money = $surplus_user_money - $store_user_money;//剩余用户金额
                $this->storeListPayInfo[$infoKey]['user_money'] = $store_user_money;
                $this->storeListPayInfo[$infoKey]['order_amount'] -= $this->storeListPayInfo[$infoKey]['user_money'];// 每个商家减去余额支付抵消的
            }
            //把剩余的用户余额平摊给第一个商家订单
            foreach ($this->storeListPayInfo as $infoKey => $infoVal) {
                $this->storeListPayInfo[$infoKey]['user_money'] += $surplus_user_money; //第一个商家平摊用了多少余额
                $this->storeListPayInfo[$infoKey]['order_amount'] -= $surplus_user_money;// 第一个商家减去余额支付抵消的
                break;
            }
            $this->orderAmount = $this->orderAmount - $this->userMoney;
        }
    }

    /**
     * 目前仅限拼团使用
     * 减去应付金额
     * @param $cut_money
     */
    public function cutOrderAmount($cut_money)
    {
        $this->orderAmount = $this->orderAmount - $cut_money;
    }

    /**
     *
     * 使用优惠券
     * @param $coupons => [store_id=>coupon_id]
     */
    public function useCoupons($coupons)
    {
        // 循环优惠券
        $coupon_count = count($coupons);
        if ($coupon_count > 0 && $coupons) {
            $coupon_list_ids = [];
            foreach ($coupons as $couponStoreId => $couponId) {
                array_push($coupon_list_ids, $couponId);
            }
            $couponList = new CouponList();
            $coupon_list = $couponList->where(['uid' => $this->user['user_id'], 'deleted' => 0, 'status' => 0, 'id' => ['in', $coupon_list_ids]])->select();
            if ($coupon_list) {
                $coupon_ids = get_arr_column($coupon_list, 'cid');
                $coupon_arr = Db::name('coupon')->where(['id' => ['in', $coupon_ids], 'status' => 1])->select(); // 获取有效优惠券类型表
                if ($coupon_arr) {
                    foreach ($coupon_arr as $couponKey => $couponVal) {
                        if($couponVal['store_id']!=0){
                            $this->couponPrice += $couponVal['money'];
                            $this->storeListPayInfo[$couponVal['store_id']]['coupon_price'] = $couponVal['money'];
                            $this->storeListPayInfo[$couponVal['store_id']]['order_amount'] = $this->storeListPayInfo[$couponVal['store_id']]['order_amount'] - $couponVal['money'];
                        }else{
                            $this->couponPrice += $couponVal['money'];//如果不是商铺发放的优惠券，则遍历所有商品依次抵消
                            $coupon_money=$couponVal['money'];
                            foreach($this->storeListPayInfo as $id=>$store){
                                if($store['goods_price']>= $coupon_money){
                                    $this->storeListPayInfo[$id]['coupon_price'] = $coupon_money;
                                    $this->storeListPayInfo[$id]['order_amount'] = $this->storeListPayInfo[$id]['order_amount'] - $coupon_money;
                                    break;
                                }else{
                                    $this->storeListPayInfo[$id]['coupon_price'] = $store['goods_price'];
                                    $this->storeListPayInfo[$id]['order_amount'] = $this->storeListPayInfo[$id]['order_amount'] - $store['goods_price'];
                                    $coupon_money-=$store['goods_price'];
                                }
                            }
                        }
                    }
                    $this->orderAmount = $this->orderAmount - $this->couponPrice;
                    $this->userCoupons = $coupon_list;
                }
            }
        }
    }

    /**
     * 获取用户使用是优惠券
     */
    public function getUserCoupon()
    {
        return $this->userCoupons;
    }

    /**
     * 配送
     * @param $district_id
     * @throws TpshopException
     */
    public function delivery($district_id)
    {
        if (array_key_exists('is_virtual', $this->payList[0]) && $this->payList[0]['is_virtual'] == 0) {
            if (empty($this->shop) && empty($district_id)) {
                throw new TpshopException("计算订单价格", 0, ['status' => -1, 'msg' => '请填写收货信息', 'result' => ['']]);
            }
        }

        $GoodsLogic = new GoodsLogic();
        $checkGoodsShipping = $GoodsLogic->checkGoodsListShipping($this->payList, $district_id);
        foreach ($checkGoodsShipping as $shippingKey => $shippingVal) {
            if ($shippingVal['shipping_able'] != true) {
                throw new TpshopException("计算订单价格", 0, ['status' => -1, 'msg' => '订单中部分商品不支持对当前地址的配送请返回购物车修改', 'result' => ['goods_shipping' => $checkGoodsShipping]]);
            }
        }
        //使用自提点不计算运费
        if (!empty($this->shop)) {
            return;
        }
        //预售活动暂不计算运费
        if ($this->payList[0]['prom_type'] == 4) {
            return;
        }
        $store_goods_shipping = $GoodsLogic->getStoreFreight($this->payList, $district_id);
        foreach ($this->storeListPayInfo as $infoKey => $infoVal) {
            // 如果店铺设置满额包邮并且商品总价大于或等于满额包邮则运费为零
            if ($this->storeList[$infoKey]['store_free_price'] != 0 && $infoVal['goods_price'] >= $this->storeList[$infoKey]['store_free_price']) {
                $this->storeListPayInfo[$infoKey]['shipping_price'] = 0;
            } else {
                $this->storeListPayInfo[$infoKey]['shipping_price'] = $store_goods_shipping[$infoKey];//各个商家的物流费
                $this->storeListPayInfo[$infoKey]['order_amount'] = $this->storeListPayInfo[$infoKey]['order_amount'] + $store_goods_shipping[$infoKey];
                $this->storeListPayInfo[$infoKey]['total_amount'] = $this->storeListPayInfo[$infoKey]['total_amount'] + $store_goods_shipping[$infoKey];
                $this->shippingPrice += $store_goods_shipping[$infoKey];
            }
            $this->totalShippingPrice += $store_goods_shipping[$infoKey];
        }
        $this->orderAmount = $this->orderAmount + $this->shippingPrice;
        $this->totalAmount = $this->totalAmount + $this->shippingPrice;
    }

    /**
     * 使用订单优惠
     */
    public function orderPromotion()
    {
        $time = time();
        $order_prom_where = ['type' => ['lt', 2], 'end_time' => ['gt', $time], 'start_time' => ['lt', $time], 'status' => 1];
        foreach ($this->storeListPayInfo as $infoKey => $infoVal) {
            $order_prom_where['store_id'] = $infoKey;
            $order_prom_where['money'] = ['elt', $infoVal['order_amount']];
            $order_prom = Db::name('prom_order')->where($order_prom_where)->order('money desc')->find();
            if ($order_prom) {
                if ($order_prom['type'] == 0) {
                    $expression_amount = round($infoVal['order_amount'] * $order_prom['expression'] / 100, 2);//满额打折
                    $this->storeListPayInfo[$infoKey]['order_prom_amount'] = round($infoVal['goods_price'] - $infoVal['coupon_price']- $expression_amount, 2);
                    $this->storeListPayInfo[$infoKey]['order_amount'] -= $this->storeListPayInfo[$infoKey]['order_prom_amount'];
                    $this->storeListPayInfo[$infoKey]['order_prom_id'] = $order_prom['id'];
                    $this->storeListPayInfo[$infoKey]['order_prom_title'] = $order_prom['title'];
                } elseif ($order_prom['type'] == 1) {
                    $this->storeListPayInfo[$infoKey]['order_prom_amount'] = $order_prom['expression'];
                    $this->storeListPayInfo[$infoKey]['order_amount'] -= $this->storeListPayInfo[$infoKey]['order_prom_amount'];
                    $this->storeListPayInfo[$infoKey]['order_prom_id'] = $order_prom['id'];
                    $this->storeListPayInfo[$infoKey]['order_prom_title'] = $order_prom['title'];
                } else {
                    $this->storeListPayInfo[$infoKey]['order_prom_amount'] = 0;
                    $this->storeListPayInfo[$infoKey]['order_prom_id'] = 0;
                    $this->storeListPayInfo[$infoKey]['order_prom_title'] = '';
                }
            } else {
                $this->storeListPayInfo[$infoKey]['order_prom_amount'] = 0;
                $this->storeListPayInfo[$infoKey]['order_prom_id'] = 0;
                $this->storeListPayInfo[$infoKey]['order_prom_title'] = '';
            }
            $this->orderPromAmount += $this->storeListPayInfo[$infoKey]['order_prom_amount'];
        }
        $this->orderAmount = $this->orderAmount - $this->orderPromAmount;
    }

    /**
     * 获取实际上使用的余额
     * @return int
     */
    public function getUserMoney()
    {
        return $this->userMoney;
    }

    /**
     * 获取订单总价
     * @return int
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * 获取订单应付金额
     * @return int
     */
    public function getOrderAmount()
    {
        return $this->orderAmount;
    }

    /**
     * 获取实际上使用的积分抵扣金额
     * @return float
     */
    public function getIntegralMoney()
    {
        return $this->integralMoney;
    }

    /**
     * 获取实际上使用的积分
     * @return float|int
     */
    public function getPayPoints()
    {
        return $this->payPoints;
    }

    /**
     * 获取优惠券金额
     * @return int
     */
    public function getCouponPrice()
    {
        return $this->couponPrice;
    }

    /**
     * 商品总价
     * @return int
     */
    public function getGoodsPrice()
    {
        return $this->goodsPrice;
    }

    /**
     * 获取用户
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * 获取计算价格的商品列表
     * @return mixed
     */
    public function getPayList()
    {
        return $this->payList;
    }

    /**
     * 获取各个商家的价格信息
     * @return mixed
     */
    public function getStoreListPayInfo()
    {
        return $this->storeListPayInfo;
    }

    /**
     * 获取运费
     * @return int
     */
    public function getShippingPrice()
    {
        return $this->shippingPrice;
    }

    /**
     * 获取抛开满免的运费
     * @return int
     */
    public function getTotalShippingPrice()
    {
        return $this->totalShippingPrice;
    }

    /**
     * 获取订单优惠总额
     * @return int
     */
    public function getOrderPromAmount()
    {
        return $this->orderPromAmount;
    }

    public function getToTalNum()
    {
        return $this->totalNum;
    }

    public function getShop()
    {
        return $this->shop;
    }

    public function toArray()
    {
        return [
            'shipping_price' => $this->shippingPrice,
            'coupon_price' => $this->couponPrice,
            'user_money' => $this->userMoney,
            'integral_money' => $this->integralMoney,
            'pay_points' => $this->payPoints,
            'order_amount' => round($this->orderAmount, 2),
            'total_amount' => round($this->totalAmount, 2),
            'goods_price' => round($this->goodsPrice, 2),
            'order_prom_amount' => $this->orderPromAmount,
            'total_num' => $this->totalNum,
            'store_list_pay_info' => $this->storeListPayInfo,
            'discount' => $this->user['discount']*10,
        ];
    }
}