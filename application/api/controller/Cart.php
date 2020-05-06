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
namespace app\api\controller; 

use app\common\logic\CartLogic;
use app\common\logic\GoodsLogic;
use app\common\logic\Integral;
use app\common\logic\CouponLogic;
use app\common\logic\Pay;
use app\common\logic\PlaceOrder;
use app\common\model\Goods;
use app\common\model\PreSell;
use app\common\logic\PreSellLogic;
use app\common\model\SpecGoodsPrice;
use app\common\util\TpshopException;
use think\Db;
use think\Loader;

class Cart extends Base {
    /**
     * 析构流函数
     */
    public function  __construct() {   
        parent::__construct();
        $unique_id = I("unique_id"); // 唯一id  类似于 pc 端的session id
        // 给用户计算会员价 登录前后不一样
        if ($this->user) {
            Db::name('cart')->where(function ($query) use ($unique_id) {
                $query->where('user_id', $this->user['user_id'])->whereOr('session_id', $unique_id);
            })->where('prom_type', 0)->save(['member_goods_price' => ['exp', 'goods_price * '. $this->user['discount']]]);
        }
    }

    /**
     * 将商品加入购物车
     */
    function addCart()
    {
        $goods_id = I("goods_id/d"); // 商品id
        $goods_num = I("goods_num/d");// 商品数量
        $item_id = I("item_id/d"); // 商品规格id
        $unique_id = I("unique_id"); // 唯一id  类似于 pc 端的session id
        $form = I("form/d"); // 标识是否商品详情页表单提交
        if (empty($goods_id)) {
            $this->ajaxReturn(['status' => 0, 'msg' => '请选择要购买的商品', 'result' => '']);
        }
        if (empty($goods_num)) {
            $this->ajaxReturn(['status' => 0, 'msg' => '购买商品数量不能为0', 'result' => '']);
        }

        $cartLogic = new CartLogic();
        $cartLogic->setGoodsModel($goods_id);
        $cartLogic->setUniqueId($unique_id);
        $cartLogic->setUserId($this->user_id);
        $cartLogic->setSpecGoodsPriceModel($item_id);
        $cartLogic->setGoodsBuyNum($goods_num);
        $cartLogic->setFrom($form);
        try {
            $cartLogic->addGoodsToCart();
            $this->ajaxReturn(['status' => 1, 'msg' => '加入购物车成功', 'result' => ['cart_num' => $cartLogic->getUserCartGoodsNum()]]);
        } catch (TpshopException $t) {
            $error = $t->getErrorArr();
            $this->ajaxReturn($error);
        }
    }
    
    /**
     * 删除购物车的商品
     */
    public function delCart()
    {       
        $ids = I("ids"); // 商品 ids        
        $result = M("Cart")->where("id","in", $ids)->delete(); // 删除id为5的用户数据
        
        // 查找购物车数量
        $unique_id = I("unique_id"); // 唯一id  类似于 pc 端的session id
        $cartLogic = new CartLogic();
        $cartLogic->setUniqueId($unique_id);
        $cart_count =  $cartLogic->getUserCartGoodsNum();
        $return_arr = array('status'=>1,'msg'=>'删除成功','result'=>$cart_count); // 返回结果状态       
        $this->ajaxReturn($return_arr);
    }
    
    /*
     * 请求获取购物车列表
     */
    public function cartList()
    {                    
        $cart_form_data = $_POST["cart_form_data"]; // goods_num 购物车商品数量
        $cart_form_data = json_decode($cart_form_data,true); //app 端 json 形式传输过来                
        $unique_id = I("unique_id/s"); // 唯一id  类似于 pc 端的session id
        $unique_id = empty($unique_id) ? -1 : $unique_id;
        $where['session_id'] = $unique_id; // 默认按照 $unique_id 查询
        $store_where = "session_id = '{$unique_id}'";
        // 如果这个用户已经登录则按照用户id查询
        if ($this->user_id) {
            unset($where);
            $where['user_id'] = $this->user_id;
            $store_where  = "user_id = ".$this->user_id;
        } else {
            $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' =>[]]);
        }
        $cartList = M('Cart')->where($where)->getField("id,goods_num,selected"); 
        
        $cartLogic = new CartLogic();
        $cartLogic->setUserId($this->user_id);
        $cartLogic->setUniqueId($unique_id);
        
        if ($cart_form_data) {
            $updateData = [];
            // 修改购物车数量 和勾选状态
            foreach ($cart_form_data as $key => $val) {
                if (!isset($cartList[$val['cartID']])) {
                    continue;
                }
                $updateData[$key]['goods_num'] = $val['goodsNum'];
                $updateData[$key]['selected'] = $val['selected'];
                $updateData[$key]['id'] = $val['cartID'];
                if ($cartList[$val['cartID']]['goods_num'] != $val['goodsNum']) {
                    $changeResult = $cartLogic->changeNum($val['cartID'], $val['goodsNum']);
                    if ($changeResult['status'] != 1) {
                        $this->ajaxReturn($changeResult);
                    }
                    break;
                }
            }
            if ($updateData) {
                $cartLogic->AsyncUpdateCart($updateData);
            }
        } 
        $cartList = $cartLogic->getCartList(1);// 选中的商品
 
        $result['total_price'] = $cartLogic->getCartPriceInfo($cartList);
 
        if($result['total_price']){
            $result['total_price']['cut_fee'] = $result['total_price']['goods_fee'];
            
            unset($result['total_price']['goods_fee']);
            unset($result['total_price']['goods_num']);
        }
        $cartList = $cartLogic->getCartList(0);// 所有的商品
        $cart_count = 0;
        foreach($cartList as $cartKey=>$cart){
            $cart['store_count'] = $cart['goods']['store_count'];
            $cart_count += $cart['goods_num'];//重新计算购物车商品数量
             unset($cart['goods']); 
        }
        
        $storeList = M('store')->where("store_id in(select store_id from ".C('database.prefix')."cart where ( {$store_where})  )")->getField("store_id,store_name,store_logo,is_own_shop"); // 找出商家
        foreach($storeList as $k => $v)
        {
            $store = array("store_id"=>$k,'store_name'=>$v['store_name'],'store_logo'=>$v['store_logo'],'is_own_shop'=>$v['is_own_shop']);
            foreach($cartList as $k2 => $v2)
            {
                if($v2['store_id'] == $k){
                    $store['cartList'][] = $v2;
                }
            }
            $result['storeList'][] = $store;
        }
         
        $result['total_price']['num'] = $cart_count;
        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $result]);
    }

    /**
     * 购物车第二步确定页面
     */
    public function cart2(){
        $goods_id = input("goods_id/d"); // 商品id
        $goods_num = input("goods_num/d");// 商品数量
        $item_id = input("item_id/d"); // 商品规格id
        $prom_type = input('prom_type/d');//立即购买时才会用到.
        $action = input("action"); // 行为
        $address_id = input('address_id/d');
        if ($this->user_id == 0){
            $this->ajaxReturn(array('status'=>-1,'msg'=>'用户user_id不能为空','result'=>''));
        }
        //获取地址
        if($address_id){
            $userAddress = M('user_address')->where("address_id" , $address_id)->find();
        }else{
            $userAddress = Db::name('user_address')->where(['user_id' => $this->user_id])->order(['is_default' => 'desc'])->find();
        }
        if(empty($userAddress)){
            $this->ajaxReturn(['status' => -11, 'msg' => '请先添加收货地址', 'result' => null]);// 返回结果状态
        }else{
            $userAddress['total_address'] = getTotalAddress($userAddress['province'], $userAddress['city'], $userAddress['district'], $userAddress['twon'], $userAddress['address']);
        }

        $cartLogic = new CartLogic();
        $couponLogic = new CouponLogic();
        $cartLogic->setUserId($this->user_id);
        //立即购买
        if($action == 'buy_now'){
            $cartLogic->setGoodsModel($goods_id);
            $cartLogic->setSpecGoodsPriceModel($item_id);
            $cartLogic->setGoodsBuyNum($goods_num);
            $cartLogic->setPromType($prom_type);
            $buyGoods = [];
            try{
                $cartLogic->validateItemId($item_id);
                $buyGoods = $cartLogic->buyNow();
            }catch (TpshopException $t){
                $error = $t->getErrorArr();
                $this->ajaxReturn(['status' => 0, 'msg' =>$error['msg']]);
                $this->error($error['msg']);
            }
            $cartList[0] = $buyGoods;
            $cartGoodsTotalNum = $goods_num;
        }else{
            if ($cartLogic->getUserCartOrderCount() == 0){
                $this->ajaxReturn(['status' => 0, 'msg' => '你的购物车没有选中商品', 'result' => null]);// 返回结果状态
            }
            $cartList = $cartLogic->getCartList(1); // 获取用户选中的购物车商品
            $cartGoodsTotalNum = array_sum(array_map(function($val){return $val['goods_num'];}, $cartList));//购物车购买的商品总数
        }
        $usersInfo = get_user_info($this->user_id);  // 用户
        $cartGoodsList = get_arr_column($cartList,'goods');
        $cartGoodsId = get_arr_column($cartGoodsList,'goods_id');
        $cartGoodsCatId = get_arr_column($cartGoodsList,'cat_id3');
        $storeCartList = $cartLogic->getStoreCartList($cartList);//转换成带店铺数据的购物车商品
        $storeCartTotalPrice= array_sum(array_map(function($val){return $val['store_goods_price'];}, $storeCartList));//商品优惠总价
        $userCouponList = $couponLogic->getUserAbleCouponList($this->user_id, $cartGoodsId, $cartGoodsCatId);//用户可用的优惠券列表
        $cartLogic->getCouponCartList($storeCartList, $userCouponList);//计算优惠券数量
        $UserStoreCouponNum = $cartLogic->getUserStoreCouponNumArr();
        $couponNum = !empty($UserStoreCouponNum) ? $UserStoreCouponNum : [];
        $json_arr = array(
            'status'=>1,
            'msg'=>'获取成功',
            'result'=>array(
                'addressList' =>$userAddress, // 收货地址
                'couponNum'=>$couponNum,  //用户可用的优惠券列表
                'cartGoodsTotalNum'=>$cartGoodsTotalNum,   //购物车购买的商品总数
                'storeShippingCartList'=>$storeCartList,//购物车列表
                'storeCartTotalPrice'=>$storeCartTotalPrice,//商品总价
                'userInfo'    =>$usersInfo, // 用户详情
            ));
        $this->ajaxReturn($json_arr) ;
    }

    /**
     * ajax 获取订单商品价格 或者提交 订单
     */
    public function cart3()
    {
        if ($this->user_id == 0){
            $this->ajaxReturn(['status' => -100, 'msg' => "登录超时请重新登录!", 'result' => null]);// 返回结果状态
        }
        $address_id         = input("address_id/d"); //  收货地址id
        $goods_id           = input("goods_id/d"); // 商品id
        $goods_num          = input("goods_num/d");// 商品数量
        $item_id            = input("item_id/d"); // 商品规格id
        $action             = input("action"); // 立即购买
        $invoice_title      = input('invoice_title'); // 发票
        $taxpayer           = input('taxpayer');  // 纳税人识别号
        $invoice_desc       = input('invoice_desc'); // 内容详情
        $pay_points         = input("pay_points/d",0); //  使用积分
        $user_money         = input("user_money/f",0); //  使用余额
        $pay_pwd            = input('pay_pwd','');
        $cart_form_data     = input("cart_form_data"); // goods_num 购物车商品数量
        $cart_form_data     = urldecode($cart_form_data);
        $cart_form_data     = json_decode($cart_form_data,true); //app 端 json 形式传输过来
        $user_note          = $cart_form_data['user_note'] ?: ''; // $user_note = I('user_note'); // 给卖家留言      数组形式
        $coupon_id          = $cart_form_data['coupon_id'] ?: 0; // $coupon_id =  I("coupon_id/d",0); //  优惠券id  数组形式
        $data               = input('request.');
        $shop_id            = input('shop_id/d', 0);//自提点id
        $take_time          = input('take_time/d');//自提时间
        $consignee          = input('consignee/s');//自提点收货人
        $mobile             = input('mobile/s');//自提点联系方式

        $cart_validate = Loader::validate('Cart');
        if (!$cart_validate->check($data)) {
            $error = $cart_validate->getError();
            $this->ajaxReturn(['status' => 0, 'msg' => $error[0], 'result' => '']);
        }
        $address = Db::name('UserAddress')->where("address_id", $address_id)->find();
        $cartLogic = new CartLogic();
        $pay = new Pay();
        // 启动事务
        Db::startTrans();
        try {
    		$cartLogic->setUserId($this->user_id);
    		$pay->setUserId($this->user_id);
            if ($action == 'buy_now') {
                $cartLogic->setGoodsModel($goods_id);
                $cartLogic->setSpecGoodsPriceModel($item_id);
                $cartLogic->setGoodsBuyNum($goods_num);
                $cart_list[0] = $cartLogic->buyNow();
                $pay->payGoodsList($cart_list);
            } else {
                $cart_list = $cartLogic->getCartList(1);
                $cartLogic->checkStockCartList($cart_list);
                $pay->payCart($cart_list);
            }
            $pay->setShopById($shop_id);
            $pay->delivery($address['district']);
            $pay->useCoupons($coupon_id);
            $pay->orderPromotion();
            $pay->usePayPoints($pay_points);
            $pay->useUserMoney($user_money);
            if ($_REQUEST['act'] == 'submit_order') {
                //保存发票信息
                $invoice_result = $cartLogic->save_invoice($invoice_title, $taxpayer, $invoice_desc);
                if($invoice_result['status'] ==-2){
                    $this->ajaxReturn($invoice_result);
                }
                $placeOrder = new PlaceOrder($pay);
                $placeOrder->setMobile($mobile);
                $placeOrder->setUserAddress($address);
                $placeOrder->setConsignee($consignee);
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
        // 如果是主订单号过来的, 说明可能是合并付款的
        $master_order_sn = I('master_order_sn','');
        $order_sn = I('order_sn','');
        $order_id = I('order_id','');
        $select_order_where = empty($master_order_sn) ? $order_sn : $master_order_sn;
        $select_order_where = empty($select_order_where) ? $order_id : $select_order_where;
        if (!$select_order_where) {
            $this->ajaxReturn(['status'=>-1, 'msg'=>'参数错误']);
        }
        $sum_order_amount = M('order')->where("order_sn|master_order_sn|order_id", $select_order_where)->sum('order_amount');
        if (!is_numeric($sum_order_amount)) {
            $this->ajaxReturn(['status'=>-1, 'msg'=>'订单不存在']);
        }
        $this->ajaxReturn(['status'=>1,'msg'=>'获取成功','result' => $sum_order_amount]);
    }

    /**
     *  获取发票信息
     * @date2017/10/19 14:45
     */
    public function invoice()
    {
        $map['user_id']=  input('user_id');
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
        
            //A.1获取发票信息
            $invoice_title = trim(I("invoice_title"));
            $taxpayer      = trim(I("taxpayer"));
            $invoice_desc  = trim(I("invoice_desc"));
    
            //B.1校验用户是否有历史发票记录
            $map            = [];
            $map['user_id'] =  $this->user_id;
            $info           = M('user_extend')->where($map)->find();
    
            //B.2发票信息
            $data=[];
            $data['invoice_title'] = $invoice_title;
            $data['taxpayer']      = $taxpayer;
            $data['invoice_desc']  = $invoice_desc;
    
            //B.3发票抬头
            if($invoice_title=="个人"){
                $data['invoice_title'] ="个人";
                $data['taxpayer']      = "";
            }
    
            //是否存贮过发票信息
            if(empty($info)){
                $data['user_id'] = $this->user_id;
                (M('user_extend')->add($data))?
                $status=1:$status=-1;
            }else{
                (M('user_extend')->where($map)->save($data))?
                $status=1:$status=-1;
            }
            $result = ['status' => $status, 'msg' => '报错成功', 'result' =>''];
            $this->ajaxReturn($result);
    }
    
    /**
     * 优惠券兑换
     */
    public function coupon_exchange()
    {
        $coupon_code = input('coupon_code');
        $couponLogic = new \app\common\logic\CouponLogic;
        $return = $couponLogic->exchangeCoupon($this->user_id, $coupon_code);
        if ($return['status'] != 1) {
            $this->ajaxReturn($return);
        }
        $limit_store = '平台';
        $store_id = $return['result']['coupon']['store_id'];
        if ($store_id) {
            $store = \app\common\model\Store::get($store_id);
            $limit_store = $store['store_name'];
        }
        $return['result']['coupon']['limit_store'] = $limit_store;
        $this->ajaxReturn($return);
    }


    /**
     * ajax 获取用户收货地址 用于购物车确认订单页面
     */
    public function ajaxAddress()
    {
        $address_id = I('address_id/d');
        //获取地址
        if ($address_id) {
            $userAddress = M('UserAddress')->where(['user_id' => $this->user_id, 'address_id' => $address_id])->find();
        }
        if (!$address_id || !$userAddress) {
            $addresslist = M('UserAddress')->where(['user_id' => $this->user_id])->select();
            $userAddress = $addresslist[0];
            foreach ($addresslist as $address) {
                if ($address['is_default'] == 1) {
                    $userAddress = $address;
                    break;
                }
            }
        }
        if ($userAddress) {
            $userAddress['total_address'] = getTotalAddress($userAddress['province'], $userAddress['city'], $userAddress['district'], $userAddress['twon'], $userAddress['address']);
        }
        if(empty($userAddress)){
            $this->ajaxReturn(['status' => -1, 'msg' => '请先添加收货地址', 'result' => null]);// 返回结果状态
        }
        $this->ajaxReturn(['status'=>1,'msg'=>'获取成功','result'=>$userAddress]);
    }


    /**
     *  积分商品结算页 1
     * @return mixed
     */
    public function integral(){
       // if(IS_POST) {
            $goods_id = input('goods_id/d');
            $item_id = input('item_id/d');
            $goods_num = input('goods_num/d');
            $address_id = input('address_id/d');
            if (empty($goods_id)) {
                $this->ajaxReturn(['status' => 0, 'msg' => '非法操作']);
            }
            if (empty($goods_num)) {
                $this->ajaxReturn(['status' => 0, 'msg' => '购买数不能为零']);
            }
            $Goods = new Goods();
            $goods = $Goods->with('store')->where(['goods_id' => $goods_id])->find();
            if (empty($goods)) {
                $this->ajaxReturn(['status' => 0, 'msg' => '该商品不存在']);
            }
            //获取地址
            if ($address_id) {
                $userAddress = M('UserAddress')->where(['user_id' => $this->user_id, 'address_id' => $address_id])->find();
            }
            if (!$address_id || !$userAddress) {
                $addresslist = M('UserAddress')->where(['user_id' => $this->user_id])->select();
                $userAddress = $addresslist[0];
                foreach ($addresslist as $address) {
                    if ($address['is_default'] == 1) {
                        $userAddress = $address;
                        break;
                    }
                }
            }
            if(empty($userAddress)){
                $this->ajaxReturn(['status' => -1, 'msg' => '请先添加收货地址', 'result' => null]);// 返回结果状态
            }else{
                $userAddress['total_address'] = getTotalAddress($userAddress['province'], $userAddress['city'], $userAddress['district'], $userAddress['twon'], $userAddress['address']);
            }

            if (empty($item_id)) {
                $goods_spec_list = SpecGoodsPrice::all(['goods_id' => $goods_id]);
                if (count($goods_spec_list) > 0) {
                    $this->ajaxReturn(['status' => 0, 'msg' => '请传递规格参数']);
                }
                $goods_price = $goods['shop_price'];
                //没有规格
            } else {
                //有规格
                $specGoodsPrice = SpecGoodsPrice::get(['item_id' => $item_id, 'goods_id' => $goods_id]);
                if ($goods_num > $specGoodsPrice['store_count']) {
                    $this->ajaxReturn(['status' => 0, 'msg' => '该商品规格库存不足，剩余' . $specGoodsPrice['store_count'] . '份']);
                }
                $goods_price = $specGoodsPrice['price'];
                $goods['item_id'] = $specGoodsPrice['item_id'];
                $goods['spec_key_name'] = $specGoodsPrice['key_name'];
            }
            $usersInfo = get_user_info($this->user_id);  // 用户
            $point_rate = tpCache('shopping.point_rate');
            $data = [
                'userAddress'=>$userAddress,  //用户地址
                'point_rate' => $point_rate,  //积分比例
                'goods' => $goods,  //商品信息
                'goods_price' => $goods_price,  //商品价格
                'goods_num' => $goods_num,     //商品购买数量
                'userInfo'    =>$usersInfo, // 用户详情
            ];
            $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $data]);
       /*} else{
            $this->ajaxReturn(['status' => -100, 'msg' => '请求方式错误！', 'result' => null]);
        } */
    }

    /**
     *  积分商品价格提交(计算价格)
     * @return mixed
     */
    public function integral2(){
        if(IS_POST) {
            $goods_id       = input('goods_id/d');
            $item_id        = input('item_id/d');
            $goods_num      = input('goods_num/d');
            $address_id     = input("address_id/d"); //  收货地址id
            $user_note      = input('user_note',''); // 给卖家留言
            $invoice_title  = input('invoice_title'); // 发票
            $taxpayer       = input('taxpayer'); // 纳税人识别号
            $user_money     = input("user_money/f", 0); //  使用余额
            $pwd            = input('pay_pwd','');
            strlen($user_note) > 50 && exit(json_encode(['status'=>-1,'msg'=>"备注超出限制可输入字符长度！",'result'=>null]));
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
        }else{
            $this->ajaxReturn(['status' => -100, 'msg' => '请求方式错误！', 'result' => null]);
        }
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
	
	public function pre_sell()
    {
        $prom_id = input('prom_id/d');
        $goods_num = input('goods_num/d');
        if ($this->user_id == 0) {
            $this->ajaxReturn(['status' => 0, 'msg' => '请先登录', 'result' => []]);
        }
        if (empty($prom_id)) {
            $this->ajaxReturn(['status' => 0, 'msg' => '参数错误', 'result' => []]);
        }
        $PreSell = new PreSell();
        $preSell = $PreSell::get($prom_id);
        if (empty($preSell)) {
            $this->ajaxReturn(['status' => 0, 'msg' => '活动不存在', 'result' => []]);
        }
        $PreSellLogic = new PreSellLogic($preSell->goods, $preSell->specGoodsPrice);
        if ($PreSellLogic->checkActivityIsEnd()) {
            $this->ajaxReturn(['status' => 0, 'msg' => '活动已结束', 'result' => []]);
        }
        if (!$PreSellLogic->checkActivityIsAble()) {
            $this->ajaxReturn(['status' => 0, 'msg' => '活动未开始', 'result' => []]);
        }
        $cartList = [];
        try {
            $cartList[0] = $PreSellLogic->buyNow($goods_num);
        } catch (TpshopException $t) {
            $error = $t->getErrorArr();
            $this->ajaxReturn(['status' => 0, 'msg' => $error['msg'], 'result' => []]);
        }
        $cartTotalPrice = array_sum(array_map(function ($val) {
            return $val['goods_fee'];
        }, $cartList));//商品优惠总价
        $result['cart_list'] = $cartList;
        $result['pre_sell'] = $preSell;
        $result['cart_total_price'] = $cartTotalPrice;
        $this->ajaxReturn(['status' => 1, 'msg' => 'success', 'result' => $result]);
    }

    public function pre_sell_place(){
        if ($this->user_id == 0){
            $this->ajaxReturn(['status' => -100, 'msg' => "登录超时请重新登录!", 'result' => null]);// 返回结果状态
        }
        $address_id = input("address_id/d"); //  收货地址id
        $user_note = input('user_note/s'); // 给卖家留言
        $invoice_title = input('invoice_title'); // 发票
        $taxpayer = input('taxpayer'); // 纳税人识别号
        $goods_num = input("goods_num/d");// 商品数量
        $pre_sell_id = input("pre_sell_id/d");// 预售活动id
        $data = input('request.');
        $cart_validate = Loader::validate('Cart');
        strlen($user_note) > 50 && $this->ajaxReturn(['status' => 0, 'msg' => "留言长度最多为50个字符", 'result' => null]);
		if (empty($address_id)) {
			$this->ajaxReturn(['status' => 0, 'msg' => '请先填写收货人信息', 'result' => null]);
		}
        $address = Db::name('UserAddress')->where("address_id", $address_id)->find();
        $pay = new Pay();
        $PreSell = new PreSell();
        $preSell = $PreSell::get($pre_sell_id);
        $PreSellLogic = new PreSellLogic($preSell->goods, $preSell->specGoodsPrice);
        try{
            //预售商品暂不支持优惠券，积分，余额支付。当订金支付时，订单退款涉及积分余额退款和原设计冲突
            $cart_list[0] = $PreSellLogic->buyNow($goods_num);
            $pay->payGoodsList($cart_list);
			$pay->setUserId($this->user_id);
			$pay->delivery($address['district']);
            if ($data['act'] == 'submit_order') {
                $placeOrder = new PlaceOrder($pay);
                $placeOrder->setUserAddress($address);
                $placeOrder->setInvoiceTitle($invoice_title);
                $placeOrder->setUserNote($user_note);
                $placeOrder->setTaxpayer($taxpayer);
                $placeOrder->addPreSellOrder($preSell);
                //$order = $placeOrder->getOrderList();
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
