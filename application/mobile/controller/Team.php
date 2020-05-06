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
namespace app\mobile\controller;

use app\common\logic\CouponLogic;
use app\common\logic\GoodsLogic;
use app\common\logic\OrderLogic;
use app\common\logic\Pay;
use app\common\logic\PlaceOrder;
use app\common\logic\team\TeamActivityLogic;
use app\common\logic\team\TeamOrder;
use app\common\model\Goods;
use app\common\model\Order;
use app\common\model\OrderGoods;
use app\common\model\team\SpecGoodsPrice;
use app\common\model\team\TeamActivity;
use app\common\model\team\TeamFound;
use app\common\model\team\TeamGoodsItem;
use app\common\util\TpshopException;
use think\Cache;
use think\Db;
use think\Page;


class Team extends MobileBase
{
    public $user_id = 0;
    public $user = array();
    /**
     * 构造函数
     */
    public function  __construct()
    {
        parent::__construct();
        if (session('?user')) {
            $user = session('user');
            $user = M('users')->where("user_id", $user['user_id'])->find();
            session('user', $user);  //覆盖session 中的 user
            $this->user = $user;
            $this->user_id = $user['user_id'];
            $this->assign('user', $user); //存储用户信息
        }
    }

    /**
     * 拼团首页
     * @return mixed
     */
    public function index()
    {
        $goods_category = Db::name('goods_category')->where(['level' => 1, 'is_show' => 1])->select();
        $this->assign('goods_category', $goods_category);
        return $this->fetch();
    }

    public function category()
    {
        $id = input('id/d');//一级分类ID
        $tid = input('tid/d');//二级分类ID
        $goods_category_level_one = Db::name('goods_category')->where(['id' => $id])->find();
        $goods_category_level_two = Db::name('goods_category')->where(['parent_id' => $goods_category_level_one['id']])->select();//二级分类
        $goods_where = ['cat_id1' => $id];
        if($tid){
            $goods_where['cat_id2'] = $tid;
        }
        $this->assign('goods_category_level_one', $goods_category_level_one);
        $this->assign('goods_category_level_two', $goods_category_level_two);
        return $this->fetch();
    }


    /**
     * 拼团首页列表
     */
    public function AjaxTeamList()
    {
        $p = input('p', 1);
        $id = input('id/d');//一级分类ID
        $tid = input('tid/d');//二级分类ID
        $two_all_ids = input('two_all_ids/s');//二级分类全部id
        $goods_where = [];
        if ($id && $two_all_ids) {
            $category_three_ids = Db::name('goods_category')->where(['parent_id' => ['in', $two_all_ids]])->getField('id', true);//三级分类id
            $goods_where['cat_id'] = ['in', $category_three_ids];
        }
        if ($tid) {
            $category_three_ids = Db::name('goods_category')->where(['parent_id' => $tid])->getField('id', true);//三级分类id
            $goods_where['cat_id'] = ['in', $category_three_ids];
        }
        $team_where = ['a.status' => 1, 'a.is_lottery' => 0, 'a.deleted' => 0, 'i.deleted' => 0];
        if (count($goods_where) > 0) {
            $goods_ids = Db::name('goods')->where(['is_on_sale' => 1])->where($goods_where)->getField('goods_id', true);
            if (!empty($goods_ids)) {
                $team_where['i.goods_id'] = ['IN', $goods_ids];
            } else {
                $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => '']);
            }
        }
        $TeamGoodsItem = new TeamGoodsItem();
        $team_goods_items = $TeamGoodsItem->alias('i')->join('__TEAM_ACTIVITY__ a', 'a.team_id = i.team_id')->with([
            'goods' => function ($query) {
                $query->field('goods_id,goods_name,shop_price,original_img');
            },
            'specGoodsPrice' => function ($query) {
                $query->field('item_id,price');
            }])->where($team_where)->field('* ,i.team_price as team_price, a.team_price as team_activity_price')->group('i.goods_id')->order('a.team_id desc')->page($p, 10)->select();
        // 设置拼团头像
        foreach($team_goods_items as $k=>$v){
            $follow_users_head_pic = Db::name('team_follow')->distinct('follow_user_head_pic')->where('team_id',$v['team_id'])->limit(3)->column('follow_user_head_pic');
            if(!empty($follow_users_head_pic)){
                foreach($follow_users_head_pic as $f=>$fv){
                    if(empty($fv)) unset($follow_users_head_pic[$f]);
                }
                $team_goods_items[$k]['follow_users_head_pic'] = array_values($follow_users_head_pic);
            }else{
                $team_goods_items[$k]['follow_users_head_pic'] = [];
            }
        }

        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $team_goods_items]);
    }

    public function info(){
        $team_id = input('team_id/d');
        $goods_id = input('goods_id/d');
        if (empty($team_id)) {
            $this->error('参数错误', U('Mobile/Team/index'));
        }
        $TeamActivity = new TeamActivity();
        $team_activity = $TeamActivity->where('team_id', $team_id)->find();
        $Goods = new Goods();
        $goods = $Goods->where(['is_on_sale'=>1,'goods_id'=>$goods_id])->find();
        if (empty($team_activity)) {
            $this->error('该商品拼团活动不存在或者已被删除', U('Mobile/Team/index'));
        }
        if (empty($team_activity['goods']) || $team_activity['goods']['is_on_sale'] == 0) {
            $this->error('此商品不存在或者已下架', U('Mobile/Team/index'));
        }
        $team_goods_item = Db::name('team_goods_item')->where('team_id',$team_id)->where('goods_id',$goods_id)->where('deleted',0)->find();
        if(empty($team_goods_item)){
            $this->error('此商品拼团活动已结束', U('Mobile/Team/index'));
        }
        $user_id = cookie('user_id');
        if ($user_id) {
            $collect = Db::name('goods_collect')->where(array("goods_id" => $team_activity['goods_id'], "user_id" => $user_id))->count();
            $this->assign('collect', $collect);
        }

        $team_goods_item = db('team_goods_item')->where('team_id',$team_id)->where('goods_id',$team_activity['goods_id'])->where('deleted',0)->field('item_id')->select();
        $team_goods_item = array_column($team_goods_item,'item_id');
        foreach ($team_activity['goods']['spec_goods_price'] as $k => $v){
            if (in_array($v['item_id'],$team_goods_item)) {
                $team_activity['goods']['spec_goods_price'][$k]['team_id'] = $team_id;
            }else{
                $team_activity['goods']['spec_goods_price'][$k]['team_id'] = 0;
            }
        }
        $store = Db::name('store')->where('store_id',$team_activity['store_id'])->find();
        $goodsLogic = new GoodsLogic();
        $filter_spec = $goodsLogic->get_spec($goods_id);
        $this->assign('filter_spec', $filter_spec);//规格参数
        $this->assign('goods',$goods);
        $this->assign('store', $store);
        $this->assign('team_activity', $team_activity);
        $this->assign('team', $team_activity); // 微信分享
        return $this->fetch();
    }

    public function ajaxCheckTeam(){
        $item_id = input('item_id/d', 0);
        $goods_id = input('goods_id/d');
        if (empty($goods_id)) {
            $this->ajaxReturn(['status' => 0, 'msg' => '参数错误']);
        }
        $TeamGoodsItem = new TeamGoodsItem();
        $team_goods_item = $TeamGoodsItem->with('team_activity,specGoodsPrice,goods')->where(['goods_id' => $goods_id, 'item_id' => $item_id, 'deleted' => 0])->find();
        if (empty($team_goods_item) || empty($team_goods_item['team_activity'])) {
            $this->ajaxReturn(['status' => 0, 'msg' => '该商品拼团活动不存在或者已被删除']);
        }
        if (empty($team_goods_item['goods'])) {
            $this->ajaxReturn(['status' => 0, 'msg' => '此商品不存在或者已下架']);
        }
        $team_goods_item = $team_goods_item->append(['team_activity' => ['bd_url', 'front_status_desc', 'bd_pic']])->toArray();
        $this->ajaxReturn(['status' => 1, 'msg' => '此商品拼团活动可以购买', 'result' => ['team_goods_item' => $team_goods_item]]);

    }

    public function ajaxTeamFound()
    {
        $goods_id = input('goods_id');
        $TeamActivity = new TeamActivity();
        $TeamFound = new TeamFound();
        $team_ids = $TeamActivity->where(['goods_id'=>$goods_id,'status'=>1,'is_lottery'=>0])->getField('team_id',true);
        //活动正常，抽奖团未开奖才获取商品拼团活动拼单
        if (count($team_ids) > 0) {
            $teamFounds = $TeamFound->with('order,teamActivity')->where(['team_id' => ['IN',$team_ids], 'status' => 1])->order('found_id asc')->select();
            if($teamFounds) {
                $teamFounds = collection($teamFounds)->append(['surplus'])->toArray();
            }
            $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => ['teamFounds' => $teamFounds]]);
        } else {
            $this->ajaxReturn(['status' => 0, 'msg' => '没有相关记录', 'result' => []]);
        }
    }

    /**
     * 下单
     */
    public function addOrder()
    {
        C('TOKEN_ON', false);
        $goods_id = input('goods_id/d');
        $item_id = input('item_id/d',0);
        $goods_num = input('goods_num/d');
        $found_id = input('found_id/d');//拼团id，有此ID表示是团员参团,没有表示团长开团
        if ($this->user_id == 0) {
            $this->ajaxReturn(['status' => -101, 'msg' => '购买拼团商品必须先登录', 'result'=>['url'=>U('Mobile/User/login')]]);
        }

        if(empty($goods_num)){
            $this->ajaxReturn(['status' => 0, 'msg' => '至少购买一份', 'result' => '']);
        }
        $team = new \app\common\logic\team\Team();
        $team->setUserById($this->user_id);
        $team->setTeamGoodsItemById($goods_id, $item_id);//设置拼团活动，拼团id  //通过goods_id,item_id查询出活动id，活动
        $team->setTeamFoundById($found_id);
        $team->setBuyNum($goods_num);
        try{
            $team->buy();
            $goods = $team->getTeamBuyGoods();
            $goodsList[0] = $goods;
            $pay = new Pay();
            $pay->setUserId($this->user_id);
            $pay->payGoodsList($goodsList);
            $placeOrder = new PlaceOrder($pay);
            $placeOrder->addTeamOrder($team);
            $order_list = $placeOrder->getOrderList();
            $this->ajaxReturn(['status' => 1, 'msg' => '提交拼团订单成功', 'result' => ['order_id' => $order_list[0]['order_id']]]);
        }catch (TpshopException $t){
            $error = $t->getErrorArr();
            $this->ajaxReturn($error);
        }
    }

    /**
     * 结算页
     * @return mixed
     */
    public function order()
    {
        $order_id = input('order_id/d',0);
        $address_id = input('address_id/d');
        if(empty($this->user_id)){
            $this->redirect("User/login");
            exit;
        }
        $Order = new Order();
        $OrderGoods = new OrderGoods();
        $order = $Order->with('store')->where(['order_id'=>$order_id,'user_id'=>$this->user_id])->find();
        if(empty($order)){
            $this->error('订单不存在或者已取消', U("Mobile/Order/order_list"));
        }
        if ($address_id) {
            $address_where = ['address_id' => $address_id];
        } else {
            $address_where = ["user_id" => $this->user_id];
        }
        $address = Db::name('user_address')->where($address_where)->order(['is_default'=>'desc'])->find();
        if(empty($address)){
            header("Location: ".U('Mobile/User/add_address',array('source'=>'team','order_id'=>$order_id)));
            exit;
        }else{
            $this->assign('address',$address);
        }
        $order_goods = $OrderGoods->with('goods')->where(['order_id' => $order_id])->find();
        // 如果已经支付过的订单直接到订单详情页面. 不再进入支付页面
        if($order['pay_status'] == 1){
            $order_detail_url = U("Mobile/Order/order_detail",array('id'=>$order_id));
            header("Location: $order_detail_url");
        }
        if($order['order_status'] == 3 ){   //订单已经取消
            $this->error('订单已取消',U("Mobile/Order/order_list"));
        }
        //微信浏览器
        if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            $plugin_where = ['type'=>'payment','status'=>1,'code'=>'weixin'];
        }else{
            $plugin_where = ['type'=>'payment','status'=>1,'scene'=>1];
        }
        $pluginList = Db::name('plugin')->where($plugin_where)->select();
        $paymentList = convert_arr_key($pluginList, 'code');
        //不支持货到付款
        foreach ($paymentList as $key => $val) {
            $val['config_value'] = unserialize($val['config_value']);
            //判断当前浏览器显示支付方式
            if (($key == 'weixin' && !is_weixin()) || ($key == 'alipayMobile' && is_weixin())) {
                unset($paymentList[$key]);
            }
        }
        //订单没有使用过优惠券
        if($order['coupon_price'] <= 0){
            $couponLogic = new CouponLogic();
            $team = new \app\common\logic\team\Team();
            $userCouponList = $couponLogic->getUserAbleCouponList($this->user_id, [$order_goods['goods_id']], [$order_goods['goods']['cat_id3']]);//用户可用的优惠券列表
            $team->setOrder($order);
            $userCartCouponList = $team->getCouponOrderList($userCouponList);
            $this->assign('userCartCouponList', $userCartCouponList);
        }
        $this->assign('paymentList', $paymentList);
        $this->assign('order', $order);
        $this->assign('order_goods', $order_goods);
        return $this->fetch();
    }

    /**
     * 获取订单详情
     */
    public function getOrderInfo()
    {
        $order_id       = input('order_id/d');
        $goods_num      = input('goods_num/d');
        $coupon_id      = input('coupon_id/d');
        $address_id     = input('address_id/d');
        $user_money     = input('user_money/f');
        $pay_points     = input('pay_points/d');
        $pay_pwd        = trim(input("paypwd")); //  支付密码
        $user_note      = trim(input("user_note")); //  用户备注
        $act            = input('post.act','');
        if(empty($this->user_id)){
            $this->ajaxReturn(['status'=>0,'msg'=>'登录超时','result'=>['url'=>U("User/login")]]);
        }
        if(empty($order_id)){
            $this->ajaxReturn(['status'=>0,'msg'=>'参数错误','result'=>[]]);
        }
        try{
            $teamOrder = new TeamOrder($this->user_id, $order_id);
            $teamOrder->changNum($goods_num);//更改数量
            $teamOrder->pay();//获取订单结账信息
            $teamOrder->useUserAddressById($address_id);//设置配送地址
            $teamOrder->useCouponById($coupon_id);//使用优惠券
            $teamOrder->usePayPoints($pay_points);//使用积分
            $teamOrder->useUserMoney($user_money);//使用余额
            $order = $teamOrder->getOrder();//获取订单信息
            $orderGoods = $teamOrder->getOrderGoods();//获取订单商品信息
            if ($act == 'submit_order') {
                $teamOrder->setUserNote($user_note);//设置用户备注
                $teamOrder->setPayPsw($pay_pwd);//设置支付密码
                $teamOrder->submit();//确认订单
                $this->ajaxReturn(['status' => 1, 'msg' => '提交成功', 'result' => ['order_amount'=>$order['order_amount']]]);
            }else{
                $couponLogic = new CouponLogic();
                $team = new \app\common\logic\team\Team();
                $userCouponList = $couponLogic->getUserAbleCouponList($this->user_id, [$orderGoods['goods_id']], [$orderGoods['goods']['cat_id3']]);//用户可用的优惠券列表
                $team->setOrder($order);
                $userCartCouponList = $team->getCouponOrderList($userCouponList);
                $result = [
                    'order'=>$order,
                    'order_goods'=>$orderGoods,
                    'couponList'=>$userCartCouponList
                ];
                $this->ajaxReturn(['status' => 1, 'msg' => '计算成功', 'result' => $result]);
            }
        }catch (TpshopException $t){
            $error = $t->getErrorArr();
            $this->ajaxReturn($error);
        }

    }

    /**
     * 拼团分享页
     * @return mixed
     */
    public function found()
    {
        $found_id = input('id/d');
        if (empty($found_id)) {
            $this->error('参数错误', U('Mobile/Team/index'));
        }
        $team = new \app\common\logic\team\Team();
        $team->setTeamFoundById($found_id);
        $teamFound = $team->getTeamFound();
        $teamFollow = $teamFound->teamFollow()->where('status','IN', [1,2])->select();
        $TeamActivity = new TeamActivity();
        $team_activity = $TeamActivity->where('team_id', $teamFound->team_id)->find();
        $team = $teamFound->teamActivity;
        //给拼单信息增加当前用户是否是团长ID
        $status = 0;//拼团分享等状态  1 团长查看详情   2 好友或者其他进入
        if(session('user')['user_id']){
            if($teamFound['user_id'] == session('user')['user_id']){//显示分享拼单
                $status = 1;
            }else{//好友
                foreach ($teamFollow as $key => $value) {
                    if($value['follow_user_id'] == session('user')['user_id']){//好友已参团
                        $status = 2;
                    }
                }
            }
        }
        //dump($status);exit;
        $this->assign('status', $status);//新增返回状态
        $this->assign('teamFollow', $teamFollow);//团员
        $this->assign('team', $team);//活动
        $this->assign('team_activity', $team_activity);//活动
        $this->assign('teamFound', $teamFound);//团长
//        halt($team['goods']['spec_goods_price']);
        return $this->fetch();
    }

    public function ajaxGetMore(){
//        $p = input('p/d',0);
//        $TeamGoodsItem = new TeamGoodsItem();
//        $team_goods_items = $TeamGoodsItem->with('goods')->alias('i')->join('__TEAM_ACTIVITY__ a', 'a.team_id = i.team_id')
//            ->where(['a.status' => 1, 'a.deleted' => 0])->where('a.is_lottery','<>',1)->page($p, 4)->group('i.goods_id')->order(['a.is_recommend' => 'desc', 'a.sort' => 'desc'])->select();
//        if(empty($team_goods_items)){
//            $this->ajaxReturn(['status'=>0,'msg'=>'已显示完所有记录']);
//        }else{
//            $result = collection($team_goods_items)->append(['team_activity' => ['virtual_sale_num']])->toArray();
//            $this->ajaxReturn(['status'=>1,'msg'=>'','result'=>$result]);
//        }
        $p = input('p/d', 0);

        $team_where = ['a.status' => 1, 'a.is_lottery' => 0, 'a.deleted' => 0];

        $TeamGoodsItem = new TeamGoodsItem();
        $team_goods_items = $TeamGoodsItem->alias('i')->join('__TEAM_ACTIVITY__ a', 'a.team_id = i.team_id')->with([
            'goods' => function ($query) {
                $query->field('goods_id,goods_name,shop_price');
            },
            'specGoodsPrice' => function ($query) {
                $query->field('item_id,price');
            }])->where($team_where)->field('* ,i.team_price as team_price')->group('i.goods_id')->order('a.team_id desc')->page($p, 10)->select();
        // 苹果app需要shop_price
        foreach ($team_goods_items as $key => $value) {
            $team_goods_items[$key]['shop_price'] = $team_goods_items[$key]['goods']['shop_price'];
            $team_goods_items[$key]['price'] = $team_goods_items[$key]['shop_price'];
            $team_goods_items[$key]['virtual_sale_num'] = $team_goods_items[$key]['virtual_num'] + $team_goods_items[$key]['sales_sum'];
        }

        if (empty($team_goods_items)) {
            $this->ajaxReturn(['status' => 0, 'msg' => '已显示完所有记录','result' =>[]]);
        } else {

            $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $team_goods_items]);
        }
    }

    public function lottery(){
        $team_id = input('team_id/d',0);
        $team_lottery = Db::name('team_lottery')->where('team_id',$team_id)->select();
        $TeamActivity = new TeamActivity();
        $team = $TeamActivity->where('team_id',$team_id)->find();
        //查出该订单价格充当拼团价
        $goods_price = db('order')->where('order_id', $team_lottery[0]['order_id'])->field('goods_price')->find()['goods_price'];
        $team['team_price'] = $goods_price;
        $this->assign('team',$team);
        $this->assign('team_lottery',$team_lottery);
        return $this->fetch();
    }

}