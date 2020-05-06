<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 商业用途务必到官方购买正版授权, 使用盗版将严厉追究您的法律责任。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * $Author: IT宇宙人 2015-08-10 $
 */
namespace app\home\controller;

use app\common\logic\CartLogic;
use app\common\logic\CouponLogic;
use app\common\logic\Integral;
use app\common\logic\Pay;
use app\common\logic\PlaceOrder;
use app\common\logic\PreSellLogic;
use app\common\model\Goods;
use app\common\model\PreSell;
use app\common\model\SpecGoodsPrice;
use app\common\util\TpshopException;
use think\Db;
use think\Loader;

class Cart extends Base
{
    public $user_id = 0;
    public $user = array();
    /**
     * 初始化函数
     */
    public function __construct()
    {
        parent::__construct();
        if (session('?user')) {
            $user = session('user');
            $user = M('users')->cache(true,10)->where("user_id", $user['user_id'])->find();
            session('user', $user);  //覆盖session 中的 user
            $this->user = $user;
            $this->user_id = $user['user_id'];
            $this->assign('user', $user); //存储用户信息
            // 给用户计算会员价 登录前后不一样
            if ($user) {
                $discount = (empty((float)$user['discount'])) ? 1 : $user['discount'];
                if ($discount != 1) {
                    $c = Db::name('cart')->where(['user_id' => $user['user_id'], 'prom_type' => 0])->where('member_goods_price = goods_price')->count();
                    $c && Db::name('cart')->where(['user_id' => $user['user_id'], 'prom_type' => 0])->update(['member_goods_price' => ['exp', 'goods_price*' . $discount]]);

                }
            }
        }
    }

    /**
     * 购物车第一步
     * @return mixed
     */
    public function index(){
        $cartLogic = new CartLogic();
        $cartLogic->setUserId($this->user_id);
        $cartList = $cartLogic->getCartList();//用户购物车
        $storeCartList = $cartLogic->getStoreCartList($cartList);//
        $userCartGoodsTypeNum = $cartLogic->getUserCartGoodsTypeNum();//获取用户购物车商品总数
        $this->assign('userCartGoodsTypeNum', $userCartGoodsTypeNum);
        $this->assign('storeCartList', $storeCartList);//购物车列表
        return $this->fetch();
    }

    /**
     * 更新购物车，并返回计算结果
     */
    public function AsyncUpdateCart()
    {
        $cart = input('cart/a', []);
        $cartLogic = new CartLogic();
        $cartLogic->setUserId($this->user_id);
        $result = $cartLogic->AsyncUpdateCart($cart);
        $this->ajaxReturn($result);
    }

    /**
     *  购物车加减
     */
    public function changeNum(){
        $cart = input('cart/a',[]);
        if (empty($cart)) {
            $this->ajaxReturn(['status' => 0, 'msg' => '请选择要更改的商品', 'result' => '']);
        }
        $cartLogic = new CartLogic();
        $result = $cartLogic->changeNum($cart['id'],$cart['goods_num']);
        $this->ajaxReturn($result);
    }

    /**
     * 删除购物车商品
     */
    public function delete(){
        $cart_ids = input('cart_ids/a',[]);
        $cartLogic = new CartLogic();
        $cartLogic->setUserId($this->user_id);
        $result = $cartLogic->delete($cart_ids);
        if($result !== false){
            $this->ajaxReturn(['status'=>1,'msg'=>'删除成功','result'=>$result]);
        }else{
            $this->ajaxReturn(['status'=>0,'msg'=>'删除失败','result'=>$result]);
        }
    }

    /**
     * 购物车优惠券领取列表
     */
    public function getStoreCoupon()
    {
        $store_ids = input('store_ids/a', []);
        $goods_ids = input('goods_ids/a', []);
        $goods_category_ids = input('goods_category_ids/a', []);
        if (empty($store_ids) && empty($goods_ids) && empty($goods_category_ids)) {
            $this->ajaxReturn(['status' => 0, 'msg' => '获取失败', 'result' => '']);
        }
        $CouponLogic = new CouponLogic();
        $newStoreCoupon = $CouponLogic->getStoreGoodsCoupon($store_ids, $goods_ids, $goods_category_ids);
        if ($newStoreCoupon) {
            $user_coupon = Db::name('coupon_list')->where('uid', $this->user_id)->getField('cid', true);
            foreach ($newStoreCoupon as $key => $val) {
                if (in_array($newStoreCoupon[$key]['id'], $user_coupon)) {
                    $newStoreCoupon[$key]['is_get'] = 1;//已领取
                } else {
                    $newStoreCoupon[$key]['is_get'] = 0;//未领取
                }
            }
        }
        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $newStoreCoupon]);
    }

    /**
     * ajax 将商品加入购物车
     */
    function ajaxAddCart()
    {
        $goods_id = I("goods_id/d"); // 商品id
        $goods_num = I("goods_num/d");// 商品数量
        $item_id = I("item_id/d"); // 商品规格id
        $form = I("form/d"); // 标识是否商品详情页表单提交
        if (empty($goods_id)) {
            $this->ajaxReturn(['status' => 0, 'msg' => '请选择要购买的商品', 'result' => '']);
        }
        if (empty($goods_num)) {
            $this->ajaxReturn(['status' => 0, 'msg' => '购买商品数量不能为0', 'result' => '']);
        }
        if ($goods_num > 200) {
            $this->ajaxReturn(['status' => 0, 'msg' => '购买商品数量大于200', 'result' => '']);
        }
        $cartLogic = new CartLogic();
        $cartLogic->setUserId($this->user_id);
        $cartLogic->setGoodsModel($goods_id);
        $cartLogic->setSpecGoodsPriceModel($item_id);
        $cartLogic->setGoodsBuyNum($goods_num);
        $cartLogic->setFrom($form);
        try {
            $cartLogic->addGoodsToCart();
            $this->ajaxReturn(['status' => 1, 'msg' => '加入购物车成功']);
        } catch (TpshopException $t) {
            $error = $t->getErrorArr();
            $this->ajaxReturn($error);
        }
    }

    /**
     * 购物车第二步确定页面
     */
    public function cart2(){
        $goods_id = input("goods_id/d"); // 商品id
        $goods_num = input("goods_num/d");// 商品数量
        $item_id = input("item_id/d"); // 商品规格id
        $action = input("action"); // 行为
        if ($this->user_id == 0){
            session('redirect_url',U('Home/Goods/goodsInfo',['id'=>$goods_id]));
            $this->error('请先登录', U('Home/User/login'));
        }
        $cartLogic = new CartLogic();
        $couponLogic = new CouponLogic();
        $cartLogic->setUserId($this->user_id);
        //立即购买
        if($action == 'buy_now'){
            $cartLogic->setGoodsModel($goods_id);
            $cartLogic->setSpecGoodsPriceModel($item_id);
            $cartLogic->setGoodsBuyNum($goods_num);
            $buyGoods = [];
            try{
                $cartLogic->validateItemId($item_id);
                $buyGoods = $cartLogic->buyNow();
            }catch (TpshopException $t){
                $error = $t->getErrorArr();
                $this->error($error['msg']);
            }
            $cartList[0] = $buyGoods;
            $cartGoodsTotalNum = $goods_num;
        }else{
            if ($cartLogic->getUserCartOrderCount() == 0){
                $this->error('你的购物车没有选中商品', 'Cart/index');
            }
            $cartList = $cartLogic->getCartList(1); // 获取用户选中的购物车商品
            $cartGoodsTotalNum = array_sum(array_map(function($val){return $val['goods_num'];}, $cartList));//购物车购买的商品总数
        }
        $cartGoodsList = get_arr_column($cartList,'goods');
        $cartGoodsId = get_arr_column($cartGoodsList,'goods_id');
        $cartGoodsCatId = get_arr_column($cartGoodsList,'cat_id3');
        $storeCartList = $cartLogic->getStoreCartList($cartList);//转换成带店铺数据的购物车商品
        $storeCartTotalPrice= array_sum(array_map(function($val){return $val['store_goods_price'];}, $storeCartList));//商品优惠总价
        $userCouponList = $couponLogic->getUserAbleCouponList($this->user_id, $cartGoodsId, $cartGoodsCatId);//用户可用的优惠券列表
        $userCartCouponList = $cartLogic->getCouponCartList($storeCartList, $userCouponList);
        $this->assign('userCartCouponList', $userCartCouponList);
        $this->assign('cartGoodsTotalNum', $cartGoodsTotalNum);
        $this->assign('storeCartList', $storeCartList);//购物车列表
        $this->assign('storeCartTotalPrice', $storeCartTotalPrice);//商品总价
        return $this->fetch();
    }

    /**
     * ajax 获取用户收货地址 用于购物车确认订单页面
     */
    public function ajaxAddress()
    {
        $address_list = Db::name('UserAddress')->where("user_id", $this->user_id)->order('is_default desc')->select();
        if ($address_list) {
            $area_id = array();
            foreach ($address_list as $val) {
                $area_id[] = $val['province'];
                $area_id[] = $val['city'];
                $area_id[] = $val['district'];
                $area_id[] = $val['twon'];
            }
            $area_id = array_filter($area_id);
            $area_id = implode(',', $area_id);
            $regionList = Db::name('region')->where("id", "in", $area_id)->getField('id,name');
            $this->assign('regionList', $regionList);
        }
        $c = Db::name('UserAddress')->where(['user_id' => $this->user_id, 'is_default' => 1])->count(); // 看看有没默认收货地址
        if ((count($address_list) > 0) && ($c == 0)) // 如果没有设置默认收货地址, 则第一条设置为默认收货地址
            $address_list[0]['is_default'] = 1;

        $this->assign('address_list', $address_list);
        return $this->fetch('ajax_address');
    }

    /**
     * 优惠券兑换
     */
    public function cartCouponExchange()
    {
        $coupon_code = input('coupon_code');
        $couponLogic = new CouponLogic;
        $return = $couponLogic->exchangeCoupon($this->user_id, $coupon_code);
        $this->ajaxReturn($return);
    }

    public function cart3()
    {
        if ($this->user_id == 0){
            $this->ajaxReturn(['status' => -100, 'msg' => "登录超时请重新登录!", 'result' => null]);// 返回结果状态
        }
        $address_id = input("address_id/d"); //  收货地址id
        $user_note = input('user_note/a'); // 给卖家留言
        $coupon_id = input("coupon_id/a"); //  优惠券id
        $invoice_title = input('invoice_title'); // 发票
        $taxpayer = input('taxpayer'); // 纳税人识别号
        $pay_points = input("pay_points/d", 0); //  使用积分
        $user_money = input("user_money/f", 0); //  使用余额
        $goods_id = input("goods_id/d"); // 商品id
        $goods_num = input("goods_num/d");// 商品数量
        $item_id = input("item_id/d"); // 商品规格id
        $mobile = input('mobile/s');
        $prom_type = input('prom_type/d');//立即购买时才会用到.
        $pay_pwd = input('pwd');
        $action = input("action/s", ''); // 立即购买
        $data = input('request.');
        $shop_id = input('shop_id/d',0);//自提点id
        $take_time = input('take_time/d');//自提点时间
        $consignee = input('consignee/s');//自提点收货人
        $cart_validate = Loader::validate('Cart');
        if (!$cart_validate->check($data)) {
            $error = $cart_validate->getError();
            $this->ajaxReturn(['status' => 0, 'msg' => $error, 'result' => '']);
        }
        $address = Db::name('UserAddress')->where("address_id", $address_id)->find();
        $cartLogic = new CartLogic();
        $pay = new Pay();
        // 启动事务
        Db::startTrans();
        try{
            $cartLogic->setUserId($this->user_id);
            if($action == 'buy_now'){
                $cartLogic->setGoodsModel($goods_id);
                $cartLogic->setSpecGoodsPriceModel($item_id);
                $cartLogic->setGoodsBuyNum($goods_num);
                $cartLogic->setPromType($prom_type);
                $cart_list[0] = $cartLogic->buyNow();
                $pay->payGoodsList($cart_list);
            }else{
                $cart_list = $cartLogic->getCartList(1);
                $cartLogic->checkStockCartList($cart_list);
                $pay->payCart($cart_list);
            }
            $pay->setUserId($this->user_id);
            $pay->setShopById($shop_id);
            $pay->delivery($address['district']);
            $pay->useCoupons($coupon_id);
            $pay->orderPromotion();
            $pay->usePayPoints($pay_points);
            $pay->useUserMoney($user_money);
            if ($_REQUEST['act'] == 'submit_order') {
                $placeOrder = new PlaceOrder($pay);
                $placeOrder->setUserAddress($address);
                $placeOrder->setConsignee($consignee);
                $placeOrder->setMobile($mobile);
                $placeOrder->setInvoiceTitle($invoice_title);
                $placeOrder->setUserNote($user_note);
                $placeOrder->setTaxpayer($taxpayer);
                $placeOrder->setPayPsw($pay_pwd);
                $placeOrder->setTakeTime($take_time);
                $placeOrder->addNormalOrder();
                $cartLogic->clear();
                $master_order_sn = $placeOrder->getMasterOrderSn();
                // 提交事务
                Db::commit();
                $this->ajaxReturn(['status'=>1,'msg'=>'提交订单成功','result'=>$master_order_sn]);
            }
            $result = $pay->toArray();
            $return_arr = array('status' => 1, 'msg' => '计算成功', 'result' => $result); // 返回结果状态
            $this->ajaxReturn($return_arr);
        }catch (TpshopException $t){
            // 回滚事务
            Db::rollback();
            $error = $t->getErrorArr();
            $this->ajaxReturn($error);
        }

    }
    /**
     * 订单支付页面
     */
    public function cart4()
    {
        $order_id = I('order_id/d', 0);
        $master_order_sn = I('master_order_sn', '');
        if(empty($this->user_id)){
            $this->redirect('User/login');
        }
        if(empty($master_order_sn) && empty($order_id)){
            $this->error('非法操作！',U("Home/Order/order_list"));
        }
        // 如果是主订单号过来的, 说明可能是合并付款的，只有在提交订单时候才会传主单号来付款
        $order_where['user_id'] = $this->user_id;
        if($master_order_sn){
            $order_where['master_order_sn'] = $master_order_sn;
        }else{
            $order_where['order_id'] = $order_id;
        }
        $order = M('Order')->where($order_where)->find();
        if(empty($order)){
            $this->error('订单不存在！',U("Home/Order/order_list"));
        }
        if($order['order_status'] == 3){
            $this->error('订单已取消',U("Home/Order/order_list"));
        }
        if ($master_order_sn) {
            $order_list = Db::name('order')->where($order_where)->select();
            if (count($order_list) > 0) {
                $sum_order_amount = 0;
                $order_pay_status_arr = get_arr_column($order_list, 'pay_status');
                if (!in_array(0, $order_pay_status_arr)) {
                    if($order_list[0]['prom_type'] == 5){
                        $this->redirect('Virtual/virtual_list');
                    }else{
                        $this->redirect('Order/order_list');
                    }
                }
                foreach ($order_list as $orderKey => $orderVal) {
                    $sum_order_amount += $orderVal['order_amount'];
                }
            }
        } else {
            // 如果已经支付过的订单直接到订单详情页面. 不再进入支付页面
            if ($order['pay_status'] == 1) {
                $this->redirect('Order/order_detail', ['id' => $order_id]);
            }
        }
        $paymentList = M('Plugin')->where("`type`='payment' and status = 1 and  scene in(0,2)")->select();
        $paymentList = convert_arr_key($paymentList, 'code');

        foreach ($paymentList as $key => $val) {
            $val['config_value'] = unserialize($val['config_value']);
            if ($val['config_value']['is_bank'] == 2) {
                $bankCodeList[$val['code']] = unserialize($val['bank_code']);
            }
        }

        $bank_img = include APP_PATH . 'home/bank.php'; // 银行对应图片
        $this->assign('paymentList', $paymentList);
        $this->assign('bank_img', $bank_img);
        $this->assign('master_order_sn', $master_order_sn); // 主订单号
        $this->assign('sum_order_amount', $sum_order_amount); // 所有订单应付金额        
        $this->assign('order', $order);
        $this->assign('bankCodeList', $bankCodeList);
        $pay_date = $order['add_time']+C('finally_pay_time');
        $this->assign('pay_date', $pay_date);
        return $this->fetch();
    }

    /**
     * ajax 请求购物车列表
     */
    public function header_cart_list()
    {
        $cartLogic = new CartLogic();
        $cartLogic->setUserId($this->user_id);
        $cartList = $cartLogic->getCartList();
        $cartPriceInfo = $cartLogic->getCartPriceInfo($cartList);
        $this->assign('cartList', $cartList); // 购物车的商品
        $this->assign('cartPriceInfo', $cartPriceInfo); // 总计
        $template = input('template/s', 'header_cart_list');
        if (strstr($template,'.')||strstr($template,'/') || strstr($template,'\\')) {
            $this->error('非法模板名称');
        }
        return $this->fetch($template);
    }

    /**
     * 兑换积分商品
     */
    public function buyIntegralGoods(){
        $goods_id = input('goods_id/d');
        $item_id = input('item_id/d');
        $goods_num = input('goods_num');
        if(empty($goods_id)){
            $this->ajaxReturn(['status'=>0,'msg'=>'非法操作']);
        }
        $integral = new Integral();
        try{
            $integral->setGoodsById($goods_id);
            $integral->setSpecGoodsPriceById($item_id);
            $integral->setUser($this->user);
            $integral->setBuyNum($goods_num);
            $integral->checkBuy();
            $url = U('Cart/integral', ['goods_id' => $goods_id, 'item_id' => $item_id, 'goods_num' => $goods_num]);
            $result = ['status' => 1, 'msg' => '购买成功', 'result' => ['url' => $url]];
            $this->ajaxReturn($result);
        }catch (TpshopException $t){
            $result = $t->getErrorArr();
            $this->ajaxReturn($result);
        }
    }

    /**
     *  积分商品结算页
     * @return mixed
     */
    public function integral(){
        $goods_id = input('goods_id/d');
        $item_id = input('item_id/d');
        $goods_num = input('goods_num/d');
        if(empty($this->user)){
            $this->error('请登录');
        }
        if(empty($goods_id)){
            $this->error('非法操作');
        }
        if(empty($goods_num)){
            $this->error('购买数不能为零');
        }
        $Goods = new Goods();
        $goods = $Goods->with('store')->where(['goods_id'=>$goods_id])->find();
        if(empty($goods)){
            $this->error('该商品不存在');
        }
        if (empty($item_id)) {
            $goods_spec_list = SpecGoodsPrice::all(['goods_id' => $goods_id]);
            if (count($goods_spec_list) > 0) {
                $this->error('请传递规格参数');
            }
            $goods_price = $goods['shop_price'];
            //没有规格
        } else {
            //有规格
            $specGoodsPrice = SpecGoodsPrice::get(['item_id'=>$item_id,'goods_id'=>$goods_id]);
            if ($goods_num > $specGoodsPrice['store_count']) {
                $this->error('该商品规格库存不足，剩余' . $specGoodsPrice['store_count'] . '份');
            }
            $goods_price = $specGoodsPrice['price'];
            $this->assign('specGoodsPrice', $specGoodsPrice);
        }
        $point_rate = tpCache('shopping.point_rate');
        $this->assign('point_rate', $point_rate);
        $this->assign('goods', $goods);
        $this->assign('goods_price', $goods_price);
        $this->assign('goods_num',$goods_num);
        return $this->fetch();
    }

    /**
     *  积分商品价格提交
     * @return mixed
     */
    public function integral2()
    {
        if ($this->user_id == 0) {
            $this->ajaxReturn(['status' => -100, 'msg' => "登录超时请重新登录!", 'result' => null]);
        }
        $goods_id       = input('goods_id/d');
        $item_id        = input('item_id/d');
        $goods_num      = input('goods_num/d');
        $address_id     = input("address_id/d"); //  收货地址id
        $user_note      = input('user_note'); // 给卖家留言
        $invoice_title  = input('invoice_title'); // 发票
        $taxpayer       = input('taxpayer'); // 纳税人识别号
        $user_money     = input("user_money/f", 0); //  使用余额
        $pwd            = input('pwd');
        if (empty($address_id)) {
            $this->ajaxReturn(['status' => -3, 'msg' => '请先填写收货人信息', 'result' => null]);
        }
        $integral = new Integral();
        try {
            $integral->setUser($this->user);
            $integral->setGoodsById($goods_id);
            $integral->setSpecGoodsPriceById($item_id);
            $integral->setUserAddressBydId($address_id);
            $integral->setBuyNum($goods_num);
            $integral->setUserMoney($user_money);
            $integral->checkBuy();
            $pay = $integral->pay();
            if ($_REQUEST['act'] == 'submit_order') {
                $placeOrder = new PlaceOrder($pay);
                $user_address = $integral->getUserAddress();
                $placeOrder->setUserAddress($user_address);
                $placeOrder->setInvoiceTitle($invoice_title);
                $placeOrder->setUserNote($user_note);
                $placeOrder->setTaxpayer($taxpayer);
                $placeOrder->setPayPsw($pwd);
                $placeOrder->addNormalOrder();
                $master_order_sn = $placeOrder->getMasterOrderSn();
                $this->ajaxReturn(['status'=>1,'msg'=>'提交订单成功','result'=>$master_order_sn]);
            }
            $result = ['status' => 1, 'msg' => '计算成功', 'result' => $pay->toArray()];
            $this->ajaxReturn($result);
        } catch (TpshopException $t) {
            $result = $t->getErrorArr();
            $this->ajaxReturn($result);
        }
    }

    /**
     *  获取发票信息
     * @date2017/10/19 14:45
     */
    public function invoice(){

        $map = [];
        $map['user_id']=  $this->user_id;
        
        $field=[          
            'invoice_title',
            'taxpayer',
            'invoice_desc',	
        ];
        
        $info = M('user_extend')->field($field)->where($map)->find();
        if(empty($info)){
            $result=['status' => -1, 'msg' => 'N', 'result' =>''];
        }else{
            $result=['status' => 1, 'msg' => 'Y', 'result' => $info];
        }
        $this->ajaxReturn($result);            
    }
     /**
     *  保存发票信息
     * @date2017/10/19 14:45
     */
    public function save_invoice(){
        if(IS_AJAX){
            $cartLogic = new CartLogic();
            //A.1获取发票信息
            $invoice_title = trim(I("invoice_title"));
            $taxpayer      = trim(I("taxpayer"));
            $invoice_desc  = trim(I("invoice_desc"));
            $cartLogic->setUserId($this->user['user_id']);
            $result = $cartLogic->save_invoice($invoice_title, $taxpayer, $invoice_desc);
            $this->ajaxReturn($result);
        }
    }

    /**
     * 预售
     */
    public function pre_sell()
    {
        $prom_id = input('prom_id/d');
        $goods_num = input('goods_num/d');
        if ($this->user_id == 0){
            $this->error('请先登录');
        }
        if(empty($prom_id)){
            $this->error('参数错误');
        }
        $PreSell = new PreSell();
        $preSell = $PreSell::get($prom_id);
        if(empty($preSell)){
            $this->error('活动不存在');
        }
        $PreSellLogic = new PreSellLogic($preSell->goods, $preSell->specGoodsPrice);
        if($PreSellLogic->checkActivityIsEnd()){
            $this->error('活动已结束');
        }
        if(!$PreSellLogic->checkActivityIsAble()){
            $this->error('活动未开始');
        }
        $cartLogic = new CartLogic();
        $couponLogic = new CouponLogic();
        $cartList = [];
        try{
            $cartList[0] = $PreSellLogic->buyNow($goods_num);
        }catch (TpshopException $t){
            $error = $t->getErrorArr();
            $this->error($error['msg']);
        }
        $storeCartList = $cartLogic->getStoreCartList($cartList);//转换成带店铺数据的购物车商品
        $storeCartTotalPrice = array_sum(array_map(function($val){return $val['store_goods_price'];}, $storeCartList));//商品优惠总价
        $userCouponList = $couponLogic->getUserAbleCouponList($this->user_id, [$preSell['goods_id']], [$preSell->goods->cat_id3]);//用户可用的优惠券列表
        $userCartCouponList = $cartLogic->getCouponCartList($storeCartList, $userCouponList);
        $this->assign('userCartCouponList', $userCartCouponList);
        $this->assign('storeCartList', $storeCartList);//购物车列表
        $this->assign('storeCartTotalPrice', $storeCartTotalPrice);//商品总价
        $this->assign('preSell', $preSell);
        return $this->fetch();
    }

    public function pre_sell_place()
    {
        if ($this->user_id == 0){
            $this->ajaxReturn(['status' => -100, 'msg' => "登录超时请重新登录!", 'result' => null]);// 返回结果状态
        }
        $address_id = input("address_id/d"); //  收货地址id
        $user_note = input('user_note/a'); // 给卖家留言
//        $coupon_id = input("coupon_id/a"); //  优惠券id
        $invoice_title = input('invoice_title'); // 发票
        $taxpayer = input('taxpayer'); // 纳税人识别号
//        $pay_points = input("pay_points/d", 0); //  使用积分
//        $user_money = input("user_money/f", 0); //  使用余额
        $goods_num = input("goods_num/d");// 商品数量
        $pre_sell_id = input("pre_sell_id/d");// 预售活动id
        $pay_pwd = input('pwd');
        $data = input('request.');
        $cart_validate = Loader::validate('Cart');
        if (!$cart_validate->check($data)) {
            $error = $cart_validate->getError();
            $this->ajaxReturn(['status' => 0, 'msg' => $error[0], 'result' => '']);
        }
        $address = Db::name('UserAddress')->where("address_id", $address_id)->find();
        $pay = new Pay();
        $PreSell = new PreSell();
        $preSell = $PreSell::get($pre_sell_id);
        $PreSellLogic = new PreSellLogic($preSell->goods, $preSell->specGoodsPrice);
        try{
            $cart_list[0] = $PreSellLogic->buyNow($goods_num);
            $pay->payGoodsList($cart_list);
            $pay->setUserId($this->user_id);
            $pay->delivery($address['district']);
//            $pay->useCoupons($coupon_id);//预售商品暂不支持优惠券，积分，余额支付。当订金支付时，订单退款涉及积分余额退款和原设计冲突
//            $pay->usePayPoints($pay_points);
//            $pay->useUserMoney($user_money);
            if ($_REQUEST['act'] == 'submit_order') {
                $placeOrder = new PlaceOrder($pay);
                $placeOrder->setUserAddress($address);
                $placeOrder->setInvoiceTitle($invoice_title);
                $placeOrder->setUserNote($user_note);
                $placeOrder->setTaxpayer($taxpayer);
                $placeOrder->setPayPsw($pay_pwd);
                $placeOrder->addPreSellOrder($preSell);
                $master_order_sn = $placeOrder->getMasterOrderSn();
                $this->ajaxReturn(['status'=>1,'msg'=>'提交订单成功','result'=>$master_order_sn]);
            }
            $result = $pay->toArray();
            $result['deposit_price'] = $preSell['deposit_price'];//订金
            $result['balance_price'] = ($preSell['ing_price'] - $preSell['deposit_price']) * $goods_num;//尾款
            $return_arr = array('status' => 1, 'msg' => '计算成功', 'result' => $result); // 返回结果状态
            $this->ajaxReturn($return_arr);
        }catch (TpshopException $t){
            $error = $t->getErrorArr();
            $this->ajaxReturn($error);
        }
    }

}
