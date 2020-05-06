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
use app\common\logic\team\Team;
use app\common\model\Order;
use app\common\model\PreSell;
use app\common\model\Shop;
use app\common\model\ShopOrder;
use app\common\model\team\TeamActivity;
use app\common\model\Users;
use app\common\util\TpshopException;
use think\Cache;
use think\Hook;
use think\Model;
use think\Db;
/**
 * 提交下单类
 * Class CatsLogic
 * @package Home\Logic
 */
class PlaceOrder
{
    private $invoiceTitle;
    private $userNote;
    private $taxpayer;
    private $pay;
    private $order;
    private $userAddress;
    private $payPsw;
    private $promType;
    private $promId;
    private $mobile;

    private $orderList;
    private $masterOrderSn;//主订单号

    private $consignee;
    private $take_time;
    private $shop;//自提点
    private $orderData;//单上加下单自提用到
    private $preSell;

    public function __construct(Pay $pay)
    {
        $this->pay = $pay;
        $this->order = new Order();
    }

    public function getShop()
    {
        return $this->shop;
    }

    public function setShopById($shop_id)
    {
        if($shop_id){
            $this->shop = Shop::get($shop_id);
        }
        return $this;
    }

    public function setMobile($mobile)
    {
        $payList = $this->pay->getPayList();
        if($payList[0]['is_virtual']){
            if($mobile){
                if(check_mobile($mobile)){
                    $this->mobile = $mobile;
                }else{
                    throw new TpshopException("提交订单",0,['status'=>-1,'msg'=>'手机号码格式错误','result'=>['']]);
                }
            }else{
                throw new TpshopException("提交订单",0,['status'=>-1,'msg'=>'请填写手机号码','result'=>['']]);
            }
        }
        $this->mobile = $mobile;
    }
    /**
     * 支付密码
     * @param string $payPsw|密码为md5加密
     */
    public function setPayPsw($payPsw)
    {
        $this->payPsw = $payPsw;
    }
    
    public function setInvoiceTitle($invoiceTitle)
    {
        $this->invoiceTitle = $invoiceTitle;
    }

    public function setUserNote($userNote)
    {
        if (is_array($userNote)) {
            $this->userNote = $userNote;
        } else {
            foreach ($this->pay->getStoreListPayInfo() as $storePayKey => $storePayVal) {
                $this->userNote = [$storePayKey => $userNote];
                return;
            }
        }
    }
    public function setTaxpayer($taxpayer)
    {
        $this->taxpayer = $taxpayer;
    }

    public function setUserAddress($userAddress)
    {
        $this->userAddress = $userAddress;
    }

    //设置自提时间
    public function setTakeTime($take_time)
    {
        $this->take_time = $take_time;
        return $this;
    }

    //设置收货人
    public function setConsignee($consignee)
    {
        $this->consignee = $consignee;
        return $this;
    }

    private function setPromType($prom_type)
    {
        $this->promType = $prom_type;
    }
    private function setPromId($prom_id)
    {
        $this->promId = $prom_id;
    }

    /**
     * 普通订单下单
     * @throws TpshopException
     */
    public function addNormalOrder()
    {
        $this->check();
        $this->queueInc();
        $this->doOrderGoodsFlashSale();
        $this->addOrder();
        $this->addOrderGoods();
        $this->addShopOrder();
        $reduce = tpCache('shopping.reduce');
        foreach($this->orderList as $orderKey=>$orderVal){
            Hook::listen('user_add_order', $orderVal);//下单行为
            if($reduce== 1 || empty($reduce)){
                minus_stock($orderVal);//下单减库存
            }
            // 如果应付金额为0  可能是余额支付 + 积分 + 优惠券 这里订单支付状态直接变成已支付
            if ($orderVal['order_amount'] == 0) {
                update_pay_status($orderVal['order_sn']);
            }
        }
        $this->deductionCoupon();//扣除优惠券
        $this->changUserPointMoney();//扣除用户积分余额
        $this->queueDec();
    }

    /**
     * @param Team $team
     * 拼团订单下单
     * @throws TpshopException
     */
    public function addTeamOrder(Team $team)
    {
        $this->setPromType(6);
        $teamActivity = $team->getTeamActivity();//设置活动
        $teamFoundId = $team->getFoundId();
        //获取用户ID
        $user = $this->pay->getUser();
        
        if($teamFoundId){
            $is_team = Cache::get('team_found_'.$teamFoundId.'_'.$user['user_id']);
            if(!$is_team)
            {
                $team_found_queue = Cache::get('team_found_queue');
                if($team_found_queue[$teamFoundId] <= 0){
                    $team_found_queue[$teamFoundId] = $team->getTeamFollowNum(); // 再次获取已参团人数
                    if($team_found_queue[$teamFoundId] <= 0){
                        throw new TpshopException('提交订单', 0, ['status' => -1, 'msg' => '此团将要完成，请稍后再试或者再开团!',
                            'result' => '',
                        ]);
                    }
                }
                $team_found_queue[$teamFoundId] = $team_found_queue[$teamFoundId] - 1;
                Cache::set('team_found_queue', $team_found_queue,60);
                Cache::set('team_found_'.$teamFoundId.'_'.$user['user_id'],'1',60);
            }
        }

        $this->setPromId($teamActivity['team_id']);//设置活动id
        $this->check();
        $this->queueInc();//人数排队
        $this->addOrder();
        $this->addOrderGoods();
        $reduce = tpCache('shopping.reduce');
        foreach($this->orderList as $orderKey=>$orderVal){
            $team->log($orderVal);
            Hook::listen('user_add_order', $orderVal);//下单行为
            if($teamActivity['team_type'] != 2){
                if($reduce == 1 || empty($reduce)){
                    minus_stock($orderVal);//下单减库存
                }
            }
            // 如果应付金额为0  可能是余额支付 + 积分 + 优惠券 这里订单支付状态直接变成已支付
            if ($orderVal['order_amount'] == 0) {
                update_pay_status($orderVal['order_sn']);
            }
        }
        $this->deductionCoupon();//扣除优惠券
        $this->changUserPointMoney();//扣除用户积分余额
        $this->queueDec();
    }

    /**
     * 预售订单下单
     * @param PreSell $preSell
     */
    public function addPreSellOrder(PreSell $preSell)
    {
        $this->preSell = $preSell;
        $this->setPromType(4);
        $this->setPromId($preSell['pre_sell_id']);
        $this->check();
        $this->queueInc();
        $this->addOrder();
        $this->addOrderGoods();
        $reduce = tpCache('shopping.reduce');
        foreach($this->orderList as $orderKey=>$orderVal){
            Hook::listen('user_add_order', $orderVal);//下单行为
            if($reduce == 1 || empty($reduce)){
                minus_stock($orderVal);//下单减库存
            }
            //预售暂不至此积分余额优惠券支付
            // 如果应付金额为0  可能是余额支付 + 积分 + 优惠券 这里订单支付状态直接变成已支付
//            if ($orderVal['order_amount'] == 0) {
//                update_pay_status($orderVal['order_sn']);
//            }
        }
//        $this->changUserPointMoney();//扣除用户积分余额
        $this->queueDec();
    }


    /**
     * 获取订单表数据
     * @return Order
     */
    public function getOrderList()
    {
        return $this->orderList;
    }

    private function addShopOrder()
    {
        $shop = $this->pay->getShop();
        if(empty($shop)){
            return;
        }
        $shop_order_data = [
            'order_id' => $this->orderData['order_id'],
            'order_sn' => $this->orderData['order_sn'],
            'shop_id' => $shop['shop_id'],
            'take_time' => date('Y-m-d H:i:s', $this->take_time),
            'add_time' => time(),
            'bar_code' =>rand(16000001,16999999)
        ];
        $shopOrder = new ShopOrder();
        $shopOrder->data($shop_order_data, true)->save();
    }

    /**
     * 提交订单前检查
     * @throws TpshopException
     */
    public function check()
    {
        $shop = $this->pay->getShop();
        if($shop['shop_id'] > 0){
            if($this->take_time <= time()){
                throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => '自提时间不能小于当前时间', 'result' => '']);
            }
            $weekday = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            $day = $weekday[date('w', $this->take_time)];
            if($shop[$day] == 0){
                throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => '自提时间不在营业日范围', 'result' => '']);
            }
            $that_day = date('Y-m-d', $this->take_time);
            $that_day_start_time = strtotime($that_day . ' '.$shop['work_start_time'] . ':00');
            $that_day_end_time = strtotime($that_day . ' '.$shop['work_end_time'] . ':00');
            if($this->take_time < $that_day_start_time || $this->take_time > $that_day_end_time){
                throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => '自提时间不在营业时间范围', 'result' => '']);
            }
            if(empty($this->consignee)){
                throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => '请填写提货人姓名', 'result' => '']);
            }
            if(empty($this->mobile) || !(check_mobile($this->mobile) || check_telephone($this->mobile))){
                throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => '提货人联系方式格式不正确', 'result' => '']);
            }
        }
        $user_money = $this->pay->getUserMoney();
        if ($user_money) {
            $user = $this->pay->getUser();
            if ($user['is_lock'] == 1) {
                throw new TpshopException('提交订单', 0, ['status'=>-5,'msg'=>"账号异常已被锁定，不能使用余额支付！",'result'=>'']);
            }
            if (empty($user['paypwd'])) {
                throw new TpshopException('提交订单', 0, ['status'=>-6,'msg'=>"请先设置支付密码",'result'=>'']);
            }
            if (empty($this->payPsw)) {
                throw new TpshopException('提交订单', 0, ['status'=>-7,'msg'=>"请输入支付密码",'result'=>'']);
            }
            if ($this->payPsw !== $user['paypwd'] && encrypt($this->payPsw) !== $user['paypwd']) {
                throw new TpshopException('提交订单', 0, ['status'=>-8,'msg'=>'支付密码错误','result'=>'']);
            }
        }
        //多商家不允许提交自提订单
        $store_list_pay_info = $this->pay->getStoreListPayInfo();
        $shop = $this->pay->getShop();
        if (count($store_list_pay_info) != 1 && !empty($shop)) {
            throw new TpshopException('提交订单', 0, ['status'=>-9,'msg'=>'该订单含有多个商家的商品，不允许自提','result'=>'']);
        }

    }

    private function queueInc()
    {
        $queue = Cache::get('queue');
        if($queue >= 100){
            throw new TpshopException('提交订单', 0, ['status' => -99, 'msg' => "当前人数过多请耐心排队!" . $queue, 'result' => '']);
        }
        Cache::inc('queue');
    }

    /**
     * 订单提交结束
     */
    private function queueDec()
    {
        Cache::dec('queue');
    }

    /**
     * 插入订单表
     * @throws TpshopException
     */
    private function addOrder()
    {
        $OrderLogic = new OrderLogic();
        $user = $this->pay->getUser();
        $shop = $this->pay->getShop();
        $store_list_pay_info = $this->pay->getStoreListPayInfo();
        $this->masterOrderSn = $OrderLogic->get_order_sn();//先生成主订单号
        $orderAllData = [];
        $payList = $this->pay->getPayList();
        foreach($store_list_pay_info as $payInfoKey => $payInfoVal){
            $orderData = [
                'order_sn'         =>$OrderLogic->get_order_sn(), // 订单编号
                'master_order_sn'  =>$this->masterOrderSn, // 主订单编号
                'user_id'          =>$user['user_id'], // 用户id
                'email'            =>$user['email'], // 用户id
                'goods_price'      =>$payInfoVal['goods_price'],//'商品价格',
                'total_amount'     =>$payInfoVal['total_amount'],// 订单总额
                'order_amount'     =>$payInfoVal['order_amount'],//'应付款金额',
                'add_time'         =>time(), // 下单时间
                'store_id'         =>$payInfoKey,
            ];
	    	//添加供应商id,-1为复合订单,在付款后拆分
			$suppliers_id_arr = $payInfoVal['suppliers_id_arr'];
			$suppliers_id_arr = array_unique($suppliers_id_arr);
			if (count($suppliers_id_arr) > 1) {
				$orderData['suppliers_id'] = -1;
			} else {
				$orderData['suppliers_id'] = $suppliers_id_arr[0];
			}
			
            if($orderData['order_sn'] == $orderData['master_order_sn']){
                $orderData['order_sn'] = $OrderLogic->get_order_sn();
            }
            if($this->promType == 4){
                //预售订单
                if($this->preSell['deposit_price'] > 0){
                    $orderData['goods_price'] = $this->preSell['ing_price'] * $this->pay->getToTalNum();
                    $orderData['total_amount'] = $this->preSell['ing_price'] * $this->pay->getToTalNum();
                    $orderData['order_amount'] = $this->preSell['deposit_price'] * $this->pay->getToTalNum() - $payInfoVal['integral_money'] - $payInfoVal['user_money'];
                }
            }
            //用户地址
            if (!empty($shop)) {
                $orderData['shop_id'] = $shop['shop_id'];
                $orderData['consignee'] = $this->consignee;
                $orderData['mobile'] = $this->mobile;
                $orderData['province'] = $shop['province_id'];
                $orderData['city'] = $shop['city_id'];
                $orderData['district'] = $shop['district_id'];
                $orderData['address'] = $shop['shop_address'];
                $orderData['zipcode'] = $shop['shop_zip'];
            } elseif (!empty($this->userAddress)) {
                $orderData['consignee'] = $this->userAddress['consignee'];// 收货人
                $orderData['province'] = $this->userAddress['province'];//'省份id',
                $orderData['city'] = $this->userAddress['city'];//'城市id',
                $orderData['district'] = $this->userAddress['district'];//'县',
                $orderData['twon'] = $this->userAddress['twon'];// '街道',
                $orderData['address'] = $this->userAddress['address'];//'详细地址'
                $orderData['mobile'] = $this->userAddress['mobile'];//'手机',
                $orderData['zipcode'] = $this->userAddress['zipcode'];//'邮编',
            } else {
                $orderData['consignee'] = $user['nickname'];// 收货人
                $orderData['mobile'] = $user['mobile'];//'手机',
            }
            //运费
            if($this->pay->getShippingPrice() > 0){
                $orderData['shipping_price'] = $payInfoVal['shipping_price'];
            }else{
                $orderData['shipping_price'] = 0;
            }
            if ($orderData['suppliers_id'] > 0) {
                $orderData['supplier_shipping_price'] = $this->pay->getTotalShippingPrice();
            }
            //使用余额
            if($this->pay->getUserMoney() > 0){
                $orderData['user_money'] = $payInfoVal['user_money'];
            }else{
                $orderData['user_money'] = 0;
            }
            //使用积分
            if($this->pay->getPayPoints() > 0){
                $orderData['integral'] = $payInfoVal['integral'];
                $orderData['integral_money'] = $payInfoVal['integral_money'];
            }else{
                $orderData['integral'] = 0;
                $orderData['integral_money'] = 0;
            }
            //使用优惠券
            if($this->pay->getCouponPrice() > 0){
                $orderData['coupon_price'] = $payInfoVal['coupon_price'];
            }else{
                $orderData['coupon_price'] = 0;
            }
            if($this->pay->getOrderPromAmount() > 0){
                $orderData['order_prom_id'] = $payInfoVal['order_prom_id'];
                $orderData['order_prom_amount'] = $payInfoVal['order_prom_amount'];
            }else{
                $orderData['order_prom_id'] = 0;
                $orderData['order_prom_amount'] = 0;
            }
            //用户备注
            if(!empty($this->userNote)){
                $orderData['user_note'] = $this->userNote[$payInfoKey];
            }


            //发票抬头
            if(!empty($this->invoiceTitle)){
                $orderData['invoice_title'] = $this->invoiceTitle;
            }
            //发票纳税人识别号
            if(!empty($this->taxpayer)){
                $orderData['taxpayer'] = $this->taxpayer;
            }
            //支付方式，可能是余额支付或积分兑换，后面其他支付方式会替换
            if($orderData['integral'] > 0 || $orderData['user_money'] > 0){
                $orderData['pay_name'] = $orderData['user_money'] ? '余额支付' : '积分兑换';
            }
            if($payList[0]['is_virtual']){
                $this->promType = 5;
                $orderData['shipping_time'] = $payList[0]['virtual_indate'];
            }
            if($this->promType){
                $orderData['prom_type'] = $this->promType;//订单类型
            }
            if($this->promId > 0){
                $orderData['prom_id'] = $this->promId;//活动id
            }
            if($payList[0]['is_virtual'] !=2 && $shop['shop_id'] > 0){
                $orderData['prom_type'] =  9 ; //自提订单
            }
			if($payList[0]['prom_type'] == 8){
				//砍价订单
				$orderData['prom_type'] = $payList[0]['prom_type'];//订单类型
				$orderData['prom_id'] = $payList[0]['prom_id'];//活动id
			}
            array_push($orderAllData, $orderData);
        }
        $orderSaveList =  $this->order->saveAll($orderAllData);
        //若只购买了一个商家的商品,则可以添加自提数据
        if (count($store_list_pay_info) == 1) {
            $orderData['order_id'] = $orderSaveList[0]->order_id;
            $this->orderData = $orderData;
        }
        if($orderData['prom_type'] == 8){
            //更新砍价信息，绑定订单
            $this->saveBargainFirst($orderData);
        }
        if ($orderSaveList === false) {
            throw new TpshopException("订单入库", 0, ['status' => -8, 'msg' => '添加订单失败', 'result' => '']);
        }
        $this->orderList = $orderSaveList;
    }

    /**
     * 更新砍价信息，绑定订单
     */
    private function saveBargainFirst($order)
    {
        db('bargain_first')->where(['bargain_id'=>$order['prom_id'],'user_id'=>$order['user_id'],'order_id'=>0])->update(['order_id'=>$order['order_id'],'is_end'=>1]);
    }

    /**
     * 插入订单商品表
     */
    private function addOrderGoods()
    {
        $payList = $this->pay->getPayList();
        $goods_ids = get_arr_column($payList,'goods_id');
        $goodsArr = Db::name('goods')->where('goods_id', 'IN', $goods_ids)->getField('goods_id,cost_price,give_integral,distribut,cat_id3');
        $cat_id_arr = get_arr_column($goodsArr,'cat_id3');
        $cat_ids = implode($cat_id_arr,',');
        $commission = Db::name('goods_category')->where('id', 'IN',  $cat_ids)->getField('id,commission');  //分类对应的商家抽成比例
        $orderGoodsAllData = [];
        foreach($payList as $payKey => $payItem)
        {
            $order                                   = $this->findStoreOrder($payItem['store_id']);//找到订单
            $totalPriceToRatio                       = empty($order['goods_price'])?'0':($payItem['member_goods_price']*$payItem['goods_num']) / $order['goods_price'];  //商品价格占总价的比例

            $orderDiscounts                          = $order['order_prom_amount'] + $order['coupon_price']; //订单优惠价钱
            $finalPrice                              = round($payItem['member_goods_price'] - ($totalPriceToRatio * $orderDiscounts), 3);// 每件商品实际支付价格

            $orderGoodsData['order_id']              = $order['order_id']; // 订单id
            $orderGoodsData['goods_id']              = $payItem['goods_id']; // 商品id
            $orderGoodsData['goods_name']            = $payItem['goods_name']; // 商品名称
            $orderGoodsData['goods_sn']              = $payItem['goods_sn']; // 商品货号
            $orderGoodsData['goods_num']             = $payItem['goods_num']; // 购买数量
            $orderGoodsData['final_price']           = $finalPrice; // 每件商品实际支付价格
            $orderGoodsData['goods_price']           = $payItem['goods_price']; // 商品价               为照顾新手开发者们能看懂代码，此处每个字段加于详细注释
            $orderGoodsData['suppliers_id']          = $payItem['suppliers_id'];
            if(!empty($payItem['spec_key'])){
                $orderGoodsData['spec_key']          = $payItem['spec_key']; // 商品规格
                $orderGoodsData['spec_key_name']     = $payItem['spec_key_name']; // 商品规格名称
                $spec_goods_price = Db::name('spec_goods_price')->where(array('goods_id'=>$payItem['goods_id'],'key'=>$payItem['spec_key']))->field('cost,item_id')->find();
                $orderGoodsData['cost_price'] = $spec_goods_price['cost_price']; // 成本价
                $orderGoodsData['item_id'] = $spec_goods_price['item_id']; // 商品规格id
            }else{
                $orderGoodsData['spec_key']          = ''; // 商品规格
                $orderGoodsData['spec_key_name']     = ''; // 商品规格名称
                $orderGoodsData['cost_price'] = $goodsArr[$payItem['goods_id']]['cost_price']; // 成本价
                $orderGoodsData['item_id'] = 0; // 商品规格id
            }
            $orderGoodsData['sku']                   = $payItem['sku']; // sku
            $orderGoodsData['member_goods_price']    = $payItem['member_goods_price']; // 会员折扣价
            $orderGoodsData['give_integral']         = $goodsArr[$payItem['goods_id']]['give_integral']; // 购买商品赠送积分
            $orderGoodsData['prom_type']             = $payItem['prom_type']; // 0 普通订单,1 限时抢购, 2 团购 , 3 促销优惠
            $orderGoodsData['prom_id']               = $payItem['prom_id']; // 活动id
            $orderGoodsData['store_id']              = $payItem['store_id']; // 店铺id
            $orderGoodsData['distribut']             = $goodsArr[$payItem['goods_id']]['distribut']; // 三级分销金额
            $orderGoodsData['commission']            = $commission[$payItem['cat_id3']]; // 商家抽成比例
            array_push($orderGoodsAllData, $orderGoodsData);
        }
        Db::name('order_goods')->insertAll($orderGoodsAllData);
    }

    /**
     * 扣除优惠券
     */
    public function deductionCoupon()
    {
        $userCoupons = $this->pay->getUserCoupon();
        if($userCoupons){
            $user = $this->pay->getUser();
            $couponListData['uid'] = $user['user_id'];
            $couponListData['use_time'] = time();
            $couponListData['status'] = 1;
            $refreshCouponPrice = false;
            foreach($userCoupons as $couponItemKey=>$couponItemVal){
                $order = $this->findStoreOrder($couponItemVal['store_id']);
                //兼容新人优惠券问题，店铺id为0，$order 返回null
                $couponListData['order_id'] = $order['order_id']?$order['order_id']:0;
                Db::name('coupon_list')->where('id',$couponItemVal['id'])->update($couponListData);
                Db::name('coupon')->where('id',$couponItemVal['cid'])->setInc('use_num');// 优惠券的使用数量加一
                $use_type = Db::name('coupon')->where('id',$couponItemVal['cid'])->value('use_type');
                if($use_type > 0){
                    $refreshCouponPrice = true;
                }
            }
            // 需要优惠券金额再分配判断
            if($refreshCouponPrice){
                $this->refreshCouponPrice();
            }
        }
    }

    /**
     * 指定商品，或指定分类要处理的
     * @throws \think\Exception
     */
    function refreshCouponPrice(){
        $payList = $this->pay->getPayList();
        foreach($payList as $payKey => $payItem) {
            $order = $this->findStoreOrder($payItem['store_id']);//找到订单
            // 有优惠券的，
            if (!empty($order['coupon_price'])) {
                // 找优惠券类型是不是指定商品的
                $cid = Db::name('coupon_list')->where('order_id', $order['order_id'])->value('cid');
                $use_type = Db::name('coupon')->where('id', $cid)->value('use_type');
                if($use_type > 0){
                    $order['goods_price'] = $this->get_goods_price_coupon($order['order_id']);
                    $totalPriceToRatio = empty($order['goods_price']) ? '0' : ($payItem['member_goods_price']*$payItem['goods_num']) / $order['goods_price'];  //商品价格占总价的比例

                    $goods_coupon = Db::name('goods_coupon')->where(['coupon_id' => $cid, 'goods_id' => $payItem['goods_id']])->find();
                    //指定的或指定分类的商品，分优惠券
                    if ($goods_coupon) {
                        $finalPrice = $payItem['member_goods_price'] - $order['coupon_price']*$totalPriceToRatio;
                    } else {
                        $finalPrice = $payItem['member_goods_price'];
                    }
                    if($order['order_prom_amount'] > 0){
                        $finalPrice -=  ($order['order_prom_amount'] * $order['order_prom_amount']); // 再扣 订单优惠价的比例
                    }
                    $finalPrice = round($finalPrice,2);
                    Db::name("order_goods")->where(['order_id'=>$order['order_id'],'goods_id' => $payItem['goods_id']])->update(['final_price'=>$finalPrice]);
                }
            }
        }
    }

    /**
     * 指定分类或指定商品的优惠券总商品价格
     * @param $order_id
     * @return int
     */
    function get_goods_price_coupon($order_id){
        $price = Cache::get('goods_price_'.$order_id);
        if($price) return $price;
        $price = 0;
        $cid = Db::name('coupon_list')->where('order_id', $order_id)->value('cid');
        $use_type = Db::name('coupon')->where('id', $cid)->value('use_type');
        if($use_type == 2){
            $order_goods = Db::name('order_goods')->where('order_id',$order_id)->select();
            $goods_category_id = Db::name('goods_coupon')->where(['coupon_id' => $cid])->value('goods_category_id');
            foreach($order_goods as $good){
                $cat_id3 = Db::name('goods')->where('goods_id', $good['goods_id'])->value('cat_id3');
                if ($cat_id3 == $goods_category_id) {
                    $price += $good['goods_price']*$good['goods_num'];
                }
            }
        }elseif($use_type == 1){
            $order_goods = Db::name('order_goods')->where('order_id',$order_id)->select();
            foreach($order_goods as $good){
                $goods_id = Db::name('goods_coupon')->where(['coupon_id' => $cid,'goods_id'=>$good['goods_id']])->value('goods_id');
                if ($goods_id) {
                    $price += $good['goods_price']*$good['goods_num'];
                }
            }
        }
        Cache::set('goods_price_'.$order_id,$price,30);
        return $price;
    }
    /**
     * 扣除用户积分余额
     */
    public function changUserPointMoney()
    {
        if($this->pay->getPayPoints() > 0 || $this->pay->getUserMoney() > 0){
            $user = $this->pay->getUser();
            $user = Users::get($user['user_id']);
            if($this->pay->getPayPoints() > 0){
                $user->pay_points = $user->pay_points - $this->pay->getPayPoints();// 消费积分
            }
            if($this->pay->getUserMoney() > 0){
                $user->user_money = $user->user_money - $this->pay->getUserMoney();// 抵扣余额
            }
            $user->save();
            $storeListPayInfo = $this->pay->getStoreListPayInfo();
            $accountLogAllData = [];
            foreach($storeListPayInfo as $payInfoKey => $payInfoVal){
                $order = $this->findStoreOrder($payInfoKey);
                $accountLogData = [
                    'user_id' => $order['user_id'],
                    'user_money' => -$payInfoVal['user_money'],
                    'pay_points' => -$payInfoVal['integral'],
                    'change_time' => time(),
                    'desc' => '下单消费',
                    'order_sn' => $order['order_sn'],
                    'order_id' => $order['order_id'],
                ];
                array_push($accountLogAllData, $accountLogData);
            }
            Db::name('account_log')->insertAll($accountLogAllData);
        }
    }

    /**
     * 这方法特殊，只限拼团使用。
     * @param $order_list
     */
    public function setOrderList($order_list)
    {
        $this->orderList = $order_list;
    }

    /**
     * 获取主订单号ID
     */
    public function getMasterOrderSn()
    {
        return $this->masterOrderSn;
    }

    /**
     * 获取单个店铺订单
     * @param $store_id
     * @return null
     */
    private function findStoreOrder($store_id){
        foreach($this->orderList as $orderKey => $orderVal){
            if($orderVal['store_id'] == $store_id){
                return $orderVal;
            }
        }
        return null;
    }

    /**
     * 检查订单商品是否有秒杀商品
     */
    private function doOrderGoodsFlashSale()
    {
        $payList = $this->pay->getPayList();
        foreach($payList as $goodsKey => $goodsVal){
            if($goodsVal['prom_type'] == 1){
                $flash_sale_queue = Cache::get('flash_sale_queue');
//                if(array_key_exists($goodsVal['prom_id'],$flash_sale_queue)){
                //判断活动队列是否存在，不用array_key_exists,出现请求超时问题(邮汇派出现这问题)，用isset代替
                if(isset($flash_sale_queue[$goodsVal['prom_id']])){
                    if($flash_sale_queue[$goodsVal['prom_id']] <= 0){
                        throw new TpshopException('提交订单', 0, ['status' => 0, 'msg' => $goodsVal['goods_name'].'--'.$goodsVal['spec_key_name'].'当前抢购人数过多请耐心排队!', 'result' => '']);
                    }
                    $flash_sale_queue[$goodsVal['prom_id']] = $flash_sale_queue[$goodsVal['prom_id']] - 1;
                    Cache::set('flash_sale_queue', $flash_sale_queue);
                }
            }
        }
    }
}