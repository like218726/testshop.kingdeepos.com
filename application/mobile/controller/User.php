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
 * 2015-11-21
 */
namespace app\mobile\controller;

use app\common\logic\ActivityLogic;
use app\common\logic\CartLogic;
use app\common\logic\UsersLogic;
use app\common\logic\OrderLogic;
use app\common\logic\Message;
use app\common\model\StoreCollect;
use app\common\model\MenuMp;
use app\common\model\UserAddress;
use app\common\model\Users;
use app\common\model\UserMessage;
use app\common\util\TpshopException;
use think\Cache;
use think\Loader;
use think\Page;
use think\Validate;
use think\Verify;
use think\Db;
use think\Image;
use app\home\controller\Api;

class User extends MobileBase
{

    public $user_id = 0;
    public $user = array();

    /*
    * 初始化操作
    */
    public function _initialize()
    {
        parent::_initialize();
        if (session('?user')) {
            $session_user = session('user');
            $select_user = M('users')->cache(true,10)->where("user_id", $session_user['user_id'])->find();
            $oauth_users = M('OauthUsers')->where(['user_id'=>$session_user['user_id']])->find();
            empty($oauth_users) && $oauth_users = [];
            empty($select_user) && $select_user = [];
            $user =  array_merge($select_user,$oauth_users);
            $UsersLogic = new \app\common\logic\UsersLogic();
            $UsersLogic->checkUserWithdrawals($user);
            session('user', $user);  //覆盖session 中的 user
            $this->user = $user;
            $this->user_id = $user['user_id'];
            $this->assign('user', $user); //存储用户信息
        }
        $nologin = array(
            'login', 'pop_login', 'do_login', 'logout', 'verify', 'set_pwd', 'finished',
            'verifyHandle', 'reg', 'send_sms_reg_code', 'find_pwd', 'check_validate_code',
            'forget_pwd', 'check_captcha', 'check_username', 'send_validate_code', 'bind_guide', 'bind_account','bind_reg', 'getCoupon'
        );
        $is_bind_account = tpCache('basic.is_bind_account');
        if (!$this->user_id && !in_array(ACTION_NAME, $nologin)) {
            if (strstr($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') && $is_bind_account) {
                header("location:" . U('Mobile/User/bind_guide'));//微信浏览器, 调到绑定账号引导页面
            } else {
                header("location:" . U('Mobile/User/login'));
            }
            exit;
        }

        $order_status_coment = array(
            'WAITPAY' => '待付款 ', //订单查询状态 待支付
            'WAITSEND' => '待发货', //订单查询状态 待发货
            'WAITRECEIVE' => '待收货', //订单查询状态 待收货
            'WAITCCOMMENT' => '待评价', //订单查询状态 待评价
        );
        $this->assign('order_status_coment', $order_status_coment);
    }


    //手机端是通过扫码PC端来绑定微信,需要ajax获取一下openID
    public function get_openid(){
        //halt($this->user_id); 22 以mp为公众号的，open 开放平台的
        $oauthUsers = M("OauthUsers")->where(['user_id'=>$this->user_id, 'oauth'=>'weixin','oauth_child'=>'mp'])->find();
        $openid = $oauthUsers['openid'];
        if(empty($oauthUsers)){
            $openid = Db::name('oauth_users')->where(['user_id'=>$this->user_id, 'oauth'=>'wx'])->value('openid');
        }
        if($openid){
            $this->ajaxReturn(['status'=>1,'result'=>$openid]);
        }else{
            $this->ajaxReturn(['status'=>0,'result'=>'']);
        }
    }

    //添加、编辑提现支付宝账号
    public function add_card(){
        $user_id=$this->user_id;
        $data=I('post.');
        if($data['type']==0){
            $info['cash_alipay']=$data['card'];
            $info['realname']=$data['cash_name'];
            $info['user_id']=$user_id;
            $res=DB::name('user_extend')->where('user_id='.$user_id)->count();
            if($res){
                $res2=Db::name('user_extend')->where('user_id='.$user_id)->save($info);
            }else{
                $res2=Db::name('user_extend')->add($info);
            }
            $this->ajaxReturn(['status'=>1,'msg'=>'操作成功']);
        }else{
            //防止非支付宝类型的表单提交
            $this->ajaxReturn(['status'=>0,'msg'=>'不支持的提现方式']);
        }

    }

    /*
     * 用户中心首页
     */
    public function index()
    {

        $user_id = $this->user_id;
        $logic = new UsersLogic();
        $user = $logic->getMobileUserInfo($user_id); //当前登录用户信息
        $comment_count = M('comment')->where("user_id", $user_id)->count();   // 我的评论数
        $level_name = M('user_level')->where("level_id", $this->user['level'])->getField('level_name'); // 等级名称
        //获取用户信息的数量
        $messageLogic = new Message();
        $user_message_count = $messageLogic->getUserMessageNoReadCount();
        //自定义菜单
        $MenuMp = new MenuMp();
        $menu_list = $MenuMp->where('is_show', 1)->order('menu_id asc')->select();

        $this->assign('menu_list', $menu_list);
        //自定义菜单结束
        $this->assign('user_message_count', $user_message_count);
        $this->assign('level_name', $level_name);
        $this->assign('comment_count', $comment_count);
        $this->assign('user', $user['result']);
        return $this->fetch();
    }


    public function logout()
    {
        session_unset();
        session_destroy();
        setcookie('cn', '', time() - 3600, '/');
        setcookie('user_id', '', time() - 3600, '/');
        setcookie('token', '', time() - 3600, '/');
        //$this->success("退出成功",U('Mobile/Index/index'));
        header("Location:" . U('Mobile/Index/index'));
        exit();
    }

    /*
     * 账户资金
     */
    public function account()
    {
        //账户资金需实时更新
        $user = model('users')->find($this->user_id);
        $UsersLogic = new \app\common\logic\UsersLogic();
        $UsersLogic->checkUserWithdrawals($user);
        $user['cash_in'] =  M('withdrawals')->where(['type'=>0, 'user_id'=>$user['user_id'],'status'=>['in',['0','1']]])->sum('money')?:'0.00'; //统计正在提现的余额

        //获取账户资金记录
        $logic = new UsersLogic();
        $data = $logic->get_account_log($this->user_id, I('get.type'));
        $account_log = $data['result'];

        $this->assign('user', $user);
        $this->assign('account_log', $account_log);
        $this->assign('page', $data['show']);
        $this->assign('info',I('info',0));
        if ($_GET['is_ajax']) {
            return $this->fetch('ajax_account_list');
            exit;
        }
        return $this->fetch();
    }

    public function coupon()
    {
        //
        $logic = new UsersLogic();
        $data = $logic->get_coupon($this->user_id, $_REQUEST['type']);
        foreach ($data['result'] as $k => $v) {
            if ($v['use_type'] == 1) { //指定商品
                $data['result'][$k]['goods_id'] = M('goods_coupon')->field('goods_id')->where(['coupon_id' => $v['cid']])->getField('goods_id');
            }
            if ($v['use_type'] == 2) { //指定分类
                $data['result'][$k]['category_id'] = Db::name('goods_coupon')->where(['coupon_id' => $v['cid']])->getField('goods_category_id');
            }
        }
        $coupon_list = $data['result'];
        $store_id = get_arr_column($coupon_list, 'store_id');
        if (!empty($store_id)) {
            $store = M('store')->where("store_id in (" . implode(',', $store_id) . ")")->getField('store_id,store_name');
        }
        $this->assign('store', $store);
        $this->assign('coupon_list', $coupon_list);
        $this->assign('page', $data['show']);
        if ($_GET['is_ajax']) {
            return $this->fetch('ajax_coupon_list');
        }
        return $this->fetch();
    }

    /**
     * 确定订单的使用优惠券
     */
    public function checkcoupon()
    {
        $type = input('type');
        $now = time();
        $cartLogic = new CartLogic();
        // 找出这个用户的优惠券 没过期的  并且 订单金额达到 condition 优惠券指定标准的
        $cartLogic->setUserId($this->user_id);
        $cartList = $cartLogic->getCartList(1);//获取购物车商品
        $cartTotalPrice = array_sum(array_map(function ($val) {
            return $val['total_fee'];
        }, $cartList));//商品优惠总价
        $where = '';
        if (empty($type)) {
            $where = " c2.uid = {$this->user_id} and {$now} < c1.use_end_time and {$now} > c1.use_start_time and c1.condition <= {$cartTotalPrice} ";
        }
        if ($type == 1) {
            $where = " c2.uid = {$this->user_id} or c1.use_end_time < {$now} and c1.use_start_time > {$now} and c1.condition >= {$cartTotalPrice}";
        }
        $coupon_list = DB::name('coupon')
            ->alias('c1')
            ->field('c1.name,c1.money,c1.condition,c1.use_end_time, c2.*')
            ->join('coupon_list c2', 'c2.cid = c1.id and c1.type in(0,1,2,3) and order_id = 0', 'LEFT')
            ->where($where)
            ->select();
        $this->assign('coupon_list', $coupon_list); // 优惠券列表
        return $this->fetch();
    }

    /**
     *  登录
     */
    public function login()
    {
        $is_bind_account = tpCache('basic.is_bind_account');
        if ($this->user_id > 0) {
            $this->redirect(U('Mobile/User/index'));
        }elseif(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') && $is_bind_account){
            $first_leader = I('first_leader');
            $this->redirect(U('Mobile/User/bind_guide',['first_leader'=>$first_leader]));
        }
        $referurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER']  : U("Home/User/index");
        if(!empty(session('login_goods_id'))){
            $id = session('login_goods_id');
            $referurl = U('Mobile/Goods/goodsInfo',['id'=>$id]);
        }
        $this->assign('referurl', $referurl);
        $this->assign('alipay_url', urlencode(SITE_URL.U("Mobile/LoginApi/login",['oauth'=>'alipay'])));
        return $this->fetch();
    }


    public function do_login()
    {
        $username = I('post.username');
        $password = I('post.password');
        $username = trim($username);
        $password = trim($password);
        //验证码验证
        if (isset($_POST['verify_code'])) {
            $verify_code = I('post.verify_code');
            $verify = new Verify();
            if (!$verify->check($verify_code, 'user_login')) {
                $res = array('status' => 0, 'msg' => '验证码错误');
                $this->ajaxReturn($res);
            }
        }
        $logic = new UsersLogic();
        $res = $logic->login($username, $password);

        if(isset($_POST['code'])) {
            $code = I('post.code');
            $sender = I('post.send');
            $type = I('post.type');
            $session_id = I('unique_id', session_id());
            $scene = I('post.scene');
            $logic = new UsersLogic();
            $res = $logic->check_validate_code($code, $sender, $type, $session_id, $scene);
            if ($res['status'] != 1) $this->ajaxReturn($res);
            $res = $logic->mobile_code_login($username);
        }
        if ($res['status'] == 1) {
            session('user', $res['result']);
            setcookie('user_id', $res['result']['user_id'], null, '/');
            setcookie('token', $res['result']['token'], null, '/');
            setcookie('is_distribut', $res['result']['is_distribut'], null, '/');
            $nickname = empty($res['result']['nickname']) ? $username : $res['result']['nickname'];
            setcookie('uname', urlencode($nickname), null, '/');
            setcookie('cn', 0, time() - 3600, '/');
            $cartLogic = new CartLogic();
            $cartLogic->setUserId($res['result']['user_id']);
            $cartLogic->doUserLoginHandle();  //用户登录后 需要对购物车 一些操作
            $orderLogic = new OrderLogic();//登录后将超时未支付订单给取消掉
            $orderLogic->setUserId($res['result']['user_id']);
            $orderLogic->abolishOrder();
        }
       $this->ajaxReturn($res);
    }

    /**
     *  注册
     */
    public function reg()
    {
        if ($this->user_id > 0) {
            $this->redirect(U('Mobile/User/index'));
        }
        $reg_sms_enable = tpCache('sms.regis_sms_enable');
        $reg_smtp_enable = tpCache('sms.regis_smtp_enable');
        if (IS_POST) {

            $logic = new UsersLogic();
            //验证码检验
            //$this->verifyHandle('user_reg');
            $nickname = I('post.nickname', '');
            $username = I('post.username', '');
            $password = I('post.password', '');
            $password2 = I('post.password2', '');

            //是否开启注册验证码机制
            $code = I('post.mobile_code', '');
            $scene = I('post.scene', 1);
            $is_bind_account = tpCache('basic.is_bind_account');

            $session_id = session_id();
//            if ($this->verifyHandle('user_reg') == false) {
//                $this->ajaxReturn(['status' => 0, 'msg' => '图像验证码错误']);
//            };
            //是否开启注册验证码机制
            if (check_mobile($username)) {
                if ($reg_sms_enable) {
                    //手机功能没关闭
                    $check_code = $logic->check_validate_code($code, $username, 'phone', $session_id, $scene);
                    if ($check_code['status'] != 1) {
                        $this->ajaxReturn($check_code);
                    }
                }
            }
            //是否开启注册邮箱验证码机制
            if (check_email($username)) {
                if ($reg_smtp_enable) {
                    //邮件功能未关闭
                    $check_code = $logic->check_validate_code($code, $username);
                    if ($check_code['status'] != 1) {
                        $this->ajaxReturn($check_code);
                    }
                }
            }

            if ($is_bind_account && session("third_oauth")) { //绑定第三方账号
                $thirdUser = session("third_oauth");
                $head_pic = $thirdUser['head_pic'];
                $data = $logic->reg($username, $password, $password2, 0, $nickname, $head_pic);
                //用户注册成功后, 绑定第三方账号
                $userLogic = new UsersLogic();
                $data = $userLogic->oauth_bind_new($data['result']);
            } else {
                $data = $logic->reg($username, $password, $password2, 0, $nickname);
            }

            if ($data['status'] != 1) {
                $this->ajaxReturn($data);
            }

            //获取公众号openid,并保持到session的user中
            $oauth_users = M('OauthUsers')->where(['user_id' => $data['result']['user_id'], 'oauth' => 'weixin', 'oauth_child' => 'mp'])->find();
            $oauth_users && $data['result']['open_id'] = $oauth_users['open_id'];

            session('user', $data['result']);
            setcookie('user_id', $data['result']['user_id'], null, '/');
            setcookie('is_distribut', $data['result']['is_distribut'], null, '/');
            $cartLogic = new CartLogic();
            $cartLogic->doUserLoginHandle();  //用户登录后 需要对购物车 一些操作
            $this->ajaxReturn($data);
            exit;
        }
        $this->assign('is_bind', $reg_sms_enable); // 注册启用短信：
        $this->assign('regis_sms_enable', $reg_sms_enable); // 注册启用短信：
        $this->assign('regis_smtp_enable', $reg_smtp_enable); // 注册启用邮箱：
        $sms_time_out = tpCache('sms.sms_time_out') > 0 ? tpCache('sms.sms_time_out') : 120;
        $this->assign('sms_time_out', $sms_time_out); // 手机短信超时时间
        return $this->fetch();
    }


    public function bind_guide()
    {
        $data = session('third_oauth');
        //没有第三方登录的话就跳到登录页
        if(empty($data)){
            $this->redirect('User/login');
        }
        $first_leader = Cache::get($data['openid']);
        addLog('bind_guide',' 微商城-bind_guide-', $data);
        addLog('bind_guide',' 微商城 -bind_guide-上级', $first_leader);
        if($first_leader){
            //拿关注传时候过来来的上级id
            @setcookie('first_leader',$first_leader);
        }
        $this->assign("nickname", $data['nickname']);
        $this->assign("oauth", $data['oauth']);
        $this->assign("head_pic", $data['head_pic']);
        return $this->fetch();
    }

    /**
     * 绑定已有账号
     * @return \think\mixed
     */
    public function bind_account()
    {
        $mobile = input('mobile/s');
        $verify_code = input('verify_code/s');
        //发送短信验证码
        $logic = new UsersLogic();
        $check_code = $logic->check_validate_code($verify_code, $mobile, 'phone', session_id(), 1);
        if($check_code['status'] != 1){
            $this->ajaxReturn(['status'=>0,'msg'=>$check_code['msg'],'result'=>'']);
        }
        if(empty($mobile) || !check_mobile($mobile)){
            $this->ajaxReturn(['status' => 0, 'msg' => '手机格式错误']);
        }
        $users = Users::get(['mobile'=>$mobile]);
        if (empty($users)) {
            $this->ajaxReturn(['status' => 0, 'msg' => '账号不存在']);
        }
        $user = new \app\common\logic\User();
        $user->setUser($users);
        $cartLogic = new CartLogic();
        try{
            $user->checkOauthBind();
            $user->oauthBind();
            $user->doLeader();
            $user->refreshCookie();
            $cartLogic->setUserId($users['user_id']);
            $cartLogic->doUserLoginHandle();
            $orderLogic = new OrderLogic();//登录后将超时未支付订单给取消掉
            $orderLogic->setUserId($users['user_id']);
            $orderLogic->abolishOrder();
            $this->ajaxReturn(['status' => 1, 'msg' => '绑定成功']);
        }catch (TpshopException $t){
            $error = $t->getErrorArr();
            $this->ajaxReturn($error);
        }
    }
    /**
     * 先注册再绑定账号
     * @return \think\mixed
     */
    public function bind_reg()
    {
        $mobile = input('mobile/s');
        $verify_code = input('verify_code/s');
        $password = input('password/s');
        $nickname = input('nickname/s', '');
		 setcookie('is_entry',1);
        if(empty($mobile) || !check_mobile($mobile)){
            $this->ajaxReturn(['status' => 0, 'msg' => '手机格式错误']);
        }
        if(empty($password)){
            $this->ajaxReturn(['status' => 0, 'msg' => '请输入密码']);
        }
        $logic = new UsersLogic();
        $check_code = $logic->check_validate_code($verify_code, $mobile, 'phone', session_id(), 1);
        if($check_code['status'] != 1){
            $this->ajaxReturn(['status'=>0,'msg'=>$check_code['msg'],'result'=>'']);
        }
        $thirdUser = session('third_oauth');
        $data = $logic->reg($mobile, $password, $password, 0, $nickname, $thirdUser['head_pic']);
        if ($data['status'] != 1) {
            $this->ajaxReturn(['status'=>0,'msg'=>$data['msg'],'result'=>'']);
        }
        $user = new \app\common\logic\User();
        $user->setUserById($data['result']['user_id']);
        try{
            $user->checkOauthBind();
            $user->oauthBind();
            $user->refreshCookie();
            $this->ajaxReturn(['status' => 1, 'msg' => '绑定成功']);
        }catch (TpshopException $t){
            $error = $t->getErrorArr();
            $this->ajaxReturn($error);
        }
    }

    public function ajaxAddressList()
    {
        $UserAddress = new UserAddress();
        $address_list = $UserAddress->where('user_id', $this->user_id)->order('is_default desc')->select();
        if($address_list){
            $address_list = collection($address_list)->append(['address_area'])->toArray();
        }else{
            $address_list = [];
        }
        $this->ajaxReturn($address_list);
    }

    /*
     * 用户地址列表
     */
    public function address_list()
    {
        $address_lists = Db::name('user_address')->where(array('user_id' => $this->user_id))->select();
        $region_list = Db::name('region')->cache(true)->getField('id,name');
        $this->assign('region_list', $region_list);
        $this->assign('lists', $address_lists);
        return $this->fetch();
    }

    /*
     * 添加地址
     */
    public function add_address()
    {
        if (IS_POST) {
            $logic = new UsersLogic();
            $post_data = input('post.');
            $data = $logic->add_address($this->user_id, 0, $post_data);
            $goods_id = input('goods_id/d');
            $item_id = input('item_id/d');
            $goods_num = input('goods_num/d');
            $action = input('action');
            if ($data['status'] != 1) {
                $this->ajaxReturn($data);
            } elseif ($_POST['source'] == 'cart2') {
                $data['url'] = U('/Mobile/Cart/cart2', array('address_id' => $data['result'], 'goods_id' => $goods_id, 'goods_num' => $goods_num, 'item_id' => $item_id, 'action' => $action));
                $this->ajaxReturn($data);
            } elseif ($_POST['source'] == 'integral') {
                $data['url'] = U('/Mobile/Cart/integral', array('address_id' => $data['result'], 'goods_id' => $goods_id, 'goods_num' => $goods_num, 'item_id' => $item_id));
                $this->ajaxReturn($data);
            } elseif ($_POST['source'] == 'team') {
                $order_id = input('order_id/d');
                $data['url'] = U('/Mobile/Team/order', array('address_id' => $data['result'], 'order_id' => $order_id));
                $this->ajaxReturn($data);
            } elseif ($_POST['source'] == 'pre_sell') {
                $prom_id = input('prom_id/d');
                $data['url'] = U('/Mobile/Cart/pre_sell', array('address_id' => $data['result'],'goods_num' => $goods_num,'prom_id' => $prom_id));
                $this->ajaxReturn($data);
            }
            $data['url'] = U('/Mobile/User/address_list');
            $this->ajaxReturn($data);
        }
        $p = M('region')->where(array('parent_id' => 0, 'level' => 1))->select();
        $this->assign('province', $p);
        return $this->fetch();

    }

    /*
     * 地址编辑
     */
    public function edit_address()
    {
        $id = I('id/d');
        
        $address = M('user_address')->where(array('address_id' => $id, 'user_id' => $this->user_id))->find();
        if (IS_POST) {
            $goods_id = input('goods_id/d');
            $item_id = input('item_id/d');
            $goods_num = input('goods_num/d');
            $order_id = input('order_id/d');
            $action = input('action');
            $logic = new UsersLogic();
            $data = $logic->add_address($this->user_id, $id, I('post.'));
            if ($data['status'] != 1) {
                $this->ajaxReturn($data);
            } elseif ($_POST['source'] == 'cart2') {
                $data['url'] = U('/Mobile/Cart/cart2', array('address_id' => $data['result'], 'goods_id' => $goods_id, 'goods_num' => $goods_num, 'item_id' => $item_id, 'action' => $action));
                $this->ajaxReturn($data);
            } elseif ($_POST['source'] == 'integral') {
                $data['url'] = U('/Mobile/Cart/integral', array('address_id' => $data['result'], 'goods_id' => $goods_id, 'goods_num' => $goods_num, 'item_id' => $item_id));
                $this->ajaxReturn($data);
            } elseif ($_POST['source'] == 'team') {
                $data['url'] = U('/Mobile/Team/order', array('address_id' => $data['result'], 'order_id' => $order_id));
                $this->ajaxReturn($data);
            }elseif ($_POST['source'] == 'pre_sell') {
                $prom_id = input('prom_id/d');
                $data['url'] = U('/Mobile/Cart/pre_sell', array('address_id' => $data['result'],'goods_num' => $goods_num,'prom_id' => $prom_id));
                $this->ajaxReturn($data);
            }
            $data['url'] = U('/Mobile/User/address_list');
            $this->ajaxReturn($data);
        }
        //获取省份
        $p = M('region')->where(array('parent_id' => 0, 'level' => 1))->select();
        $c = M('region')->where(array('parent_id' => $address['province'], 'level' => 2))->select();
        $d = M('region')->where(array('parent_id' => $address['city']))->select();
        if ($address['twon']) {
            $e = M('region')->where(array('parent_id' => $address['district'], 'level' => 4))->select();
            $this->assign('twon', $e);
        }
        $this->assign('province', $p);
        $this->assign('city', $c);
        $this->assign('district', $d);
        $this->assign('address', $address);
        return $this->fetch();
    }


    /**
     * 添加编辑地址
     */
    public function address_save(){
        $address_id = input('address_id',0);
        $data =input('post.');
        $userAddressValidate = Loader::validate('UserAddress');
        if (!$userAddressValidate->batch()->check($data)) {
            $this->ajaxReturn(['status'=>0,'msg'=>'操作失败','result'=>$userAddressValidate->getError()]);
        }
        if ($address_id) {
            $userAddress = UserAddress::get(['address_id'=>$address_id,'user_id'=>$this->user_id]);
            if (!$userAddress) {
                $this->ajaxReturn(['status'=>0,'msg'=>'参数错误']);
            }
            if ($data['is_default'] == 1 && $userAddress->is_default != 1) {
                db('user_address')->where('user_id',$this->user_id)->where('is_default',1)->update(['is_default'=>0]);
            }
        }else{
            $userAddress = new UserAddress();
            $user_address_count = db('user_address')->where('user_id',$this->user_id)->count();
            if($data['is_default'] == 1){
                db('user_address')->where('is_default',1)->update(['is_default'=>0]);
            }
            if ($user_address_count >= 20) {
                $this->ajaxReturn(['status'=>0,'msg'=>'最多只能添加20个收货地址']);
            }
            $data['user_id'] = $this->user_id;
        }

        $userAddress->data($data);
        $row = $userAddress->save();
        if ($row !== false) {
            $this->ajaxReturn(['status' => 1, 'msg' => '操作成功', 'result'=>['address_id'=>$userAddress->address_id]]);
        } else {
            $this->ajaxReturn(['status' => 0, 'msg' => '操作失败']);
        }
    }

    /*
     * 设置默认收货地址
     */
    public function set_default()
    {
        $id = I('get.id/d');
        $source = I('get.source');
        $is_ajax = input('is_ajax',0);
        M('user_address')->where(array('user_id' => $this->user_id))->save(array('is_default' => 0));
        $row = M('user_address')->where(array('user_id' => $this->user_id, 'address_id' => $id))->save(array('is_default' => 1));
        if($is_ajax){
            if($row){
                $this->ajaxReturn(['status'=>1,'msg'=>'操作成功']);
            }else{
                $this->ajaxReturn(['status'=>0,'msg'=>'操作失败']);
            }
        }
        if ($source == 'cart2') {
            header("Location:" . U('Mobile/Cart/cart2'));
        } else {
            header("Location:" . U('Mobile/User/address_list'));
        }
        exit();
    }

    /*
     * 地址删除
     */
    public function del_address()
    {
        $id = I('id/d', '');

        $address = M('user_address')->where("address_id", $id)->find();
        $row = M('user_address')->where(array('user_id' => $this->user_id, 'address_id' => $id))->delete();
        // 如果删除的是默认收货地址 则要把第一个地址设置为默认收货地址
        if ($address['is_default'] == 1) {
            $address2 = M('user_address')->where("user_id", $this->user_id)->find();
            $address2 && M('user_address')->where("address_id", $address2['address_id'])->save(array('is_default' => 1));
        }
        if (!$row)
            $this->ajaxReturn(['status' => 0, 'msg' => '操作失败', 'url' => U('User/address_list')]);
        else
            $this->ajaxReturn(['status' => 1, 'msg' => '操作成功', 'url' => U('User/address_list')]);
    }


    /*
     * 个人信息
     */
    public function userinfo()
    {
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_info($this->user_id); // 获取用户信息
        $user_info = $user_info['result'];
        if (IS_POST) {
            $post = input('post.');
            $scene = input('post.scene', 6);
            $return = $userLogic->upload_headpic(false);
            if ($return['status'] !== 1) {
                $this->error($return['msg']);
            } else {
                if ($return['result'] != '') {
                    $post['head_pic'] = $return['result'];
                }
            }
            if (!empty($post['email'])) {
                $c = M('users')->where(['email' => $post['email'], 'user_id' => ['<>', $this->user_id]])->count();
                $c && $this->error("邮箱已被使用");
            }
            if (!empty($post['mobile'])) {
                $c = M('users')->where(['mobile' => $post['mobile'], 'user_id' => ['<>', $this->user_id]])->count();
                $c && $this->error("手机已被使用");
                if (!$post['mobile_code'])
                    $this->error('请输入验证码');
                $check_code = $userLogic->check_validate_code($post['mobile_code'], $post['mobile'], 'phone', $this->session_id, $scene);
                if ($check_code['status'] != 1)
                    $this->error($check_code['msg']);
            }
            if(!empty($post['manifesto'])){
                if(count($post['manifesto'])>90){
                    $this->error('你输入的内容过长');
                }
            }
            $post['birthday'] ? $post['birthday'] = strtotime($post['birthday']) : false;  // 生日
            if (!$userLogic->update_info($this->user_id, $post))
                $this->error("保存失败");
            $this->success("操作成功",U('User/userinfo'));
            exit;
        }
        //  获取省份
        $province = M('region')->where(array('parent_id' => 0, 'level' => 1))->select();
        //  获取订单城市
        $city = M('region')->where(array('parent_id' => $user_info['province'], 'level' => 2))->select();
        //  获取订单地区
        $area = M('region')->where(array('parent_id' => $user_info['city'], 'level' => 3))->select();
        $this->assign('province', $province);
        $this->assign('city', $city);
        $this->assign('area', $area);
        $this->assign('user', $user_info);
        $this->assign('sex', C('SEX'));

        $action = I('get.action');
        if ($action != '') {
            return $this->fetch("$action");
        }
        return $this->fetch();
    }

    /**
     * 修改绑定手机
     * @return mixed
     */
    public function setMobile()
    {
        $userLogic = new UsersLogic();
        if (IS_POST) {
            $mobile = input('mobile');
            $mobile_code = input('mobile_code');
            $scene = input('post.scene', 6);
            $validate = I('validate', 0);
            $status = I('status', 0);
            $c = Db::name('users')->where(['mobile' => $mobile, 'user_id' => ['<>', $this->user_id]])->count();
            $c && $this->error('手机已被使用');
            if (!$mobile_code)
                $this->error('请输入验证码');
            $check_code = $userLogic->check_validate_code($mobile_code, $mobile, 'phone', $this->session_id, $scene);
            if ($check_code['status'] != 1) {
                $this->error($check_code['msg']);
            }
            if ($validate == 1 & $status == 0) {
                $res = Db::name('users')->where(['user_id' => $this->user_id])->update(['mobile' => $mobile]);
                if ($res) {
                    $source = I('source');
                    !empty($source) && $this->success('绑定成功', U('Mobile/User/'.$source));
                    $this->success('修改成功', U('User/userinfo'));
                }
                $this->error('修改失败');
            }
        }
        $this->assign('status', $status);
        return $this->fetch();
    }

    /*
     * 邮箱验证
     */
    public function email_validate()
    {
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_info($this->user_id); // 获取用户信息
        $user_info = $user_info['result'];
        $step = I('get.step', 1);
        //验证是否未绑定过
        if ($user_info['email_validated'] == 0)
            $step = 2;
        //原邮箱验证是否通过
        if ($user_info['email_validated'] == 1 && session('email_step1') == 1)
            $step = 2;
        if ($user_info['email_validated'] == 1 && session('email_step1') != 1)
            $step = 1;
        if (IS_POST) {
            $email = I('post.email');
            $code = I('post.code');
            $info = session('email_code');
            if (!$info)
                $this->error('非法操作');
            if ($info['email'] == $email || $info['code'] == $code) {
                if ($user_info['email_validated'] == 0 || session('email_step1') == 1) {
                    session('email_code', null);
                    session('email_step1', null);
                    if (!$userLogic->update_email_mobile($email, $this->user_id))
                        $this->error('邮箱已存在');
                    $this->success('绑定成功', U('Home/User/index'));
                } else {
                    session('email_code', null);
                    session('email_step1', 1);
                    redirect(U('Home/User/email_validate', array('step' => 2)));
                }
                exit;
            }
            $this->error('验证码邮箱不匹配');
        }
        $this->assign('step', $step);
        return $this->fetch();
    }

    /**
     * 个人海报推广二维码 （我的名片）
     */
    public function qr_code()
    {
        $user_id = $this->user['user_id'];
        if (!$user_id) {
            return $this->fetch();
        }
        //判断是否是分销商
        $user = M('users')->where('user_id', $user_id)->find();
        //        if (!$user && $user['is_distribut'] != 1) {
        //            return $this->fetch();
        //        }
    
        //判断是否存在海报背景图
        if(!DB::name('poster')->where(['enabled'=>1])->find()){
            $this->error('平台为设置海报封面',U('mobile/user/index'));
        }
    
        //分享数据来源
        $shareLink = urlencode("http://{$_SERVER['HTTP_HOST']}/index.php?m=Mobile&c=Index&a=index&first_leader={$user['user_id']}");
    
        $head_pic = $user['head_pic'] ?: '';
        if ($head_pic && strpos($head_pic, 'http') !== 0) {
            $head_pic = '.'.$head_pic;
            if(!file_exists($head_pic)){
                $head_pic = '';
            }
        }
        // 图片带有&的参数处理法
        if(strpos($head_pic,'&')){
            $head_pic = urlencode($head_pic);
        }
        $this->assign('user',  $user);
        $this->assign('head_pic', $head_pic);
        $this->assign('ShareLink', $shareLink);
        return $this->fetch();
        //$this->poster_qrcode($shareLink,$head_pic);
    }
    
    // 用户海报二维码
    public function poster_qrcode()
    {
        ob_end_clean();
        vendor('topthink.think-image.src.Image');
        vendor('phpqrcode.phpqrcode');
    
        error_reporting(E_ERROR);
        $url = isset($_GET['data']) ? $_GET['data'] : '';
        $url = urldecode($url);
    
        $poster = DB::name('poster')->where(['enabled'=>1])->find();
        define('IMGROOT_PATH', str_replace("\\","/",realpath(dirname(dirname(__FILE__)).'/../../'))); //图片根目录（绝对路径）
        $project_path = '/public/upload/poster/'.I('_saas_app','all');
        $file_path = IMGROOT_PATH.$project_path;
    
        if(!is_dir($file_path)){
            mkdir($file_path,777,true);
        }
    
        $head_pic = input('get.head_pic', '');                   //个人头像
        $head_pic = urldecode($head_pic);
        $head_pic = str_replace('&amp;', '&', $head_pic);        //图片带有&的参数处理法
        $back_img = IMGROOT_PATH.$poster['back_url'];            //海报背景
        $valid_date = input('get.valid_date', 0);                //有效时间
    
        $qr_code_path = UPLOAD_PATH.'qr_code/';
        if (!file_exists($qr_code_path)) {
            mkdir($qr_code_path,777,true);
        }
    
        /* 生成二维码 */
        $qr_code_file = $qr_code_path.time().rand(1, 10000).'.png';
        $size = floor($poster['qrcode_size']/37*100)/100 + 0.01;
        \QRcode::png($url, $qr_code_file, QR_ECLEVEL_M,$size);
		//将二维码大小从px转到函数需要的数值,考虑到生成的二维码还有空白的外边距，这里生成的二维码比实际设置的要大，所以要在下面的imagecopyresampled函数进行缩放
    
        /* 二维码叠加水印 */
        $QR = Image::open($qr_code_file);
        $QR_width = $QR->width();
        $QR_height = $QR->height();
    
        /* 添加头像 */
        if ($head_pic) {
            //如果是网络头像
            if (strpos($head_pic, 'http') === 0) {
                //下载头像
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_URL, $head_pic);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $file_content = curl_exec($ch);
                curl_close($ch);
                //保存头像
                if ($file_content) {
                    $head_pic_path = $qr_code_path.time().rand(1, 10000).'.png';
                    file_put_contents($head_pic_path, $file_content);
                    $head_pic = $head_pic_path;
                }
            }
            //如果是本地头像
            if (file_exists($head_pic)) {
                $logo = Image::open($head_pic);
                $logo_width = $logo->height();
                $logo_height = $logo->width();
                $logo_qr_width = $QR_width / 4;
                $scale = $logo_width / $logo_qr_width;
                $logo_qr_height = $logo_height / $scale;
                $logo_file = $qr_code_path.time().rand(1, 10000);
                $logo->thumb($logo_qr_width, $logo_qr_height)->save($logo_file, null, 100);
                $QR = $QR->thumb($QR_width, $QR_height)->water($logo_file, \think\Image::WATER_CENTER);
                $logo_file && unlink($logo_file);
                if(isset($head_pic_path)) unlink($head_pic_path); // 如果是网络头像的
            }
        }
    
        if ($valid_date && strpos($url, 'weixin.qq.com') !== false) {
            $QR = $QR->text('有效时间 '.$valid_date, "./vendor/topthink/think-captcha/assets/zhttfs/1.ttf", 7, '#00000000', Image::WATER_SOUTH);
        }
        $QR->save($qr_code_file, null, 100);
    
        $canvas_maxWidth = $poster['canvas_width'];
        $canvas_maxHeight = $poster['canvas_height'];
        $info = getimagesize($back_img);                                                           //取得一个图片信息的数组
        $im = checkPosterImagesType($info,$back_img);                                              //根据图片的格式对应的不同的函数
        $rate_poster_width = $canvas_maxWidth/$info[0];                                            //计算绽放比例
        $rate_poster_height = $canvas_maxHeight/$info[1];
        $maxWidth =  floor($info[0]*$rate_poster_width);
        $maxHeight = floor($info[1]*$rate_poster_height);                                          //计算出缩放后的高度
        $des_im = imagecreatetruecolor($maxWidth,$maxHeight);                                      //创建一个缩放的画布
        imagecopyresized($des_im,$im,0,0,0,0,$maxWidth,$maxHeight,$info[0],$info[1]);              //缩放
        $news_poster = $file_path.'/'.createImagesName() . ".png";                                 //获得缩小后新的二维码路径
        inputPosterImages($info,$des_im,$news_poster);                                             //输出到png即为一个缩放后的文件
        $QR = imagecreatefromstring(file_get_contents($qr_code_file));
        $background_img = imagecreatefromstring ( file_get_contents ( $news_poster ) );
    
        imagecopyresampled ( $background_img, $QR,$poster['canvas_x'],$poster['canvas_y'],0,0,$poster['qrcode_size'],$poster['qrcode_size'],$QR_width, $QR_height);      //合成图片
        $result_png = '/'.createImagesName(). ".png";
        $file = $file_path . $result_png;
        imagepng ($background_img, $file);                                                          //输出合成海报图片
        $final_poster = imagecreatefromstring ( file_get_contents (  $file ) );                     //获得该图片资源显示图片
        header("Content-type: image/png");
        imagepng ( $final_poster);
        imagedestroy( $final_poster);
        $news_poster && unlink($news_poster);
        $qr_code_file && unlink($qr_code_file);
        $file && unlink($file);
        exit;
    }
    
    /*
    * 手机验证
    */
    public function mobile_validate()
    {
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_info($this->user_id); // 获取用户信息
        $user_info = $user_info['result'];
        $step = I('get.step', 1);
        //验证是否未绑定过
        if ($user_info['mobile_validated'] == 0)
            $step = 2;
        //原手机验证是否通过
        if ($user_info['mobile_validated'] == 1 && session('mobile_step1') == 1)
            $step = 2;
        if ($user_info['mobile_validated'] == 1 && session('mobile_step1') != 1)
            $step = 1;
        if (IS_POST) {
            $mobile = I('post.mobile');
            $code = I('post.code');
            $info = session('mobile_code');
            if (!$info)
                $this->error('非法操作');
            if ($info['email'] == $mobile || $info['code'] == $code) {
                if ($user_info['email_validated'] == 0 || session('email_step1') == 1) {
                    session('mobile_code', null);
                    session('mobile_step1', null);
                    if (!$userLogic->update_email_mobile($mobile, $this->user_id, 2))
                        $this->error('手机已存在');
                    $this->success('绑定成功', U('User/index'));
                } else {
                    session('mobile_code', null);
                    session('email_step1', 1);
                    redirect(U('User/mobile_validate', array('step' => 2)));
                }
                exit;
            }
            $this->error('验证码手机不匹配');
        }
        $this->assign('step', $step);
        return $this->fetch();
    }

    /*
     *取消收藏
     */
    public function cancel_collect()
    {
        $collect_id = I('collect_id/d');
        $user_id = $this->user_id;
        if (M('goods_collect')->where(["collect_id" => $collect_id, "user_id" => $user_id])->delete()) {
            $this->ajaxReturn(['status' => 1, 'msg' => "已取消收藏", 'url' => U('User/collect_list')]);
        } else {
            $this->ajaxReturn(['status' => 1, 'msg' => "未取消收藏", 'url' => U('User/collect_list')]);
        }
    }

    /**
     *  删除一个收藏店铺
     * @author lxl
     * @time17-3-28
     */
    public function del_store_collect()
    {
        $id = I('get.log_id/d');
        if (!$id)
            $this->error("缺少ID参数");
        $store_id = M('store_collect')->where(array('log_id' => $id, 'user_id' => $this->user_id))->getField('store_id');
        $row = M('store_collect')->where(array('log_id' => $id, 'user_id' => $this->user_id))->delete();
        M('store')->where(array('store_id' => $store_id))->setDec('store_collect');
        if ($row) {
            $this->ajaxReturn(['status' => 1, 'msg' => "已取消收藏", 'url' => U('User/collect_list')]);
        } else {
            $this->ajaxReturn(['status' => 1, 'msg' => "未取消收藏", 'url' => U('User/collect_list')]);
        }
    }

    public function message_list()
    {
        C('TOKEN_ON', true);
        if (IS_POST) {
            $this->verifyHandle('message');

            $data = I('post.');
            $data['user_id'] = $this->user_id;
            $user = session('user');
            $data['user_name'] = $user['nickname'];
            $data['msg_time'] = time();
            if (M('feedback')->add($data)) {
                $this->success("留言成功", U('User/message_list'));
                exit;
            } else {
                $this->error('留言失败', U('User/message_list'));
                exit;
            }
        }
        $msg_type = array(0 => '留言', 1 => '投诉', 2 => '询问', 3 => '售后', 4 => '求购');
        $count = M('feedback')->where("user_id=" . $this->user_id)->count();
        $Page = new Page($count, 100);
        $Page->rollPage = 2;
        $message = M('feedback')->where("user_id=" . $this->user_id)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $showpage = $Page->show();
        header("Content-type:text/html;charset=utf-8");
        $this->assign('page', $showpage);
        $this->assign('message', $message);
        $this->assign('msg_type', $msg_type);
        return $this->fetch();
    }

    /**账户明细*/
    public function points()
    {
        $type = I('type', 'all');    //获取类型
        $this->assign('type', $type);
        if ($type == 'recharge') {
            //充值明细
            $count = M('recharge')->where("user_id", $this->user_id)->count();
            $Page = new Page($count, 16);
            $account_log = M('recharge')->where("user_id", $this->user_id)->order('order_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        } else if ($type == 'points') {
            //积分记录明细
            $count = M('account_log')->where(['user_id' => $this->user_id, 'pay_points' => ['<>', 0]])->count();
            $Page = new Page($count, 16);
            $account_log = M('account_log')->where(['user_id' => $this->user_id, 'pay_points' => ['<>', 0]])->order('log_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        } else {
            //全部
            $count = M('account_log')->where(['user_id' => $this->user_id])->count();
            $Page = new Page($count, 16);
            $account_log = M('account_log')->where(['user_id' => $this->user_id])->order('log_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        }
        $show = $Page->show();
        $this->assign('account_log', $account_log);
        $this->assign('page', $show);
        $this->assign('listRows', $Page->listRows);
        if ($_GET['is_ajax']) {
            return $this->fetch('ajax_points');
            exit;
        }
        return $this->fetch();
    }

    public function points_list()
    {
        $type = I('type', 'all');
        $usersLogic = new UsersLogic;
        $result = $usersLogic->points($this->user_id, $type);

        $this->assign('type', $type);
        $showpage = $result['page']->show();
        $this->assign('account_log', $result['account_log']);
        $this->assign('page', $showpage);
        if ($_GET['is_ajax']) {
            return $this->fetch('ajax_points');
        }
        return $this->fetch();
    }

    public function account_list()
    {
        $type = I('type','all');
        $usersLogic = new UsersLogic;
        $result = $usersLogic->account($this->user_id, $type);
        $this->ajaxReturn(array('status'=>1,'msg'=>'获取成功','result'=>['type'=>$type,'account_log'=>$result['account_log']]));
//    	$this->assign('type', $type);
//    	$this->assign('account_log', $result['account_log']);
//    	if ($_GET['is_ajax']) {
//    		return $this->fetch('ajax_account_list');
//    	}
//    	return $this->fetch();
    }

    /**
     *资金详情
     */
    public function account_detail()
    {
        $log_id = I('log_id/d', 0);
        $detail = Db::name('account_log')->where(['log_id' => $log_id])->find();
        $this->assign('detail', $detail);
        return $this->fetch();
    }

    /*
     * 密码修改
     */
    public function password()
    {
        if (IS_POST) {
            $logic = new UsersLogic();
            $data = $logic->get_info($this->user_id);
            $user = $data['result'];
            if ($user['mobile'] == '' && $user['email'] == '')
                $this->ajaxReturn(['status' => -1, 'msg' => '请先绑定手机或邮箱', 'url' => U('/Mobile/User/index')]);
            if(I('post.code')){
                $code = I('post.code');
                $mobile = I('mobile');
                $send = I('send');
                $sender = empty($mobile) ? $send : $mobile;
                $type = I('type');
                $session_id = I('unique_id', session_id());
                $scene = I('scene', -1);
                $res = $logic->check_validate_code($code, $sender, $type ,$session_id, $scene);
                if($res['status'] != 1 ){
                    $this->ajaxReturn($res);
                }
                $data = $logic->password($this->user_id, I('post.old_password'), I('post.new_password'), I('post.confirm_password'),false);
            }else{
                $data = $logic->password($this->user_id, I('post.old_password'), I('post.new_password'), I('post.confirm_password'));
            }
            if ($data['status'] == -1)
                $this->ajaxReturn(['status' => -1, 'msg' => $data['msg']]);
            $this->ajaxReturn(['status' => 1, 'msg' => $data['msg'],'url' => U('/Mobile/User/index')]);
            exit;
        }
        return $this->fetch();
    }

    function forget_pwd()
    {
        if ($this->user_id > 0) {
            header("Location: " . U('User/Index'));
        }
        if (IS_POST) {
            $username = I('username');
            if (!empty($username)) {
                if (!$this->verifyHandle('forget')) {
                    $this->ajaxReturn(['status' => 0, 'msg' => "验证码错误"]);
                    exit;
                }
                $field = 'mobile';
                if (check_email($username)) {
                    $field = 'email';
                }
                $user = M('users')->where(['email' => $username])->whereOr(['mobile' => $username])->find();
                if ($user) {
                    session('find_password', array('user_id' => $user['user_id'], 'username' => $username,
                        'email' => $user['email'], 'mobile' => $user['mobile'], 'type' => $field));
                    $this->ajaxReturn(['status' => 1, 'msg' => '', 'url' => U('User/find_pwd')]);
                    exit;
                } else {
                    $this->ajaxReturn(['status' => 0, 'msg' => "用户名不存在，请检查"]);
                }
            }
        }
        return $this->fetch();
    }

    function find_pwd()
    {
        if ($this->user_id > 0) {
            header("Location: " . U('User/Index'));
        }
        $user = session('find_password');
        if (empty($user)) {
            $this->error("请先验证用户名", U('User/forget_pwd'));
        }
        $this->assign('user', $user);
        return $this->fetch();
    }


    public function set_pwd()
    {
        if ($this->user_id > 0) {
            header("Location: " . U('User/Index'));
        }
        $check = session('validate_code');
        if (empty($check)) {
            header("Location:" . U('User/forget_pwd'));
        } elseif ($check['is_check'] == 0) {
            $this->error('验证码还未验证通过', U('User/forget_pwd'));
        }
        if (IS_POST) {
            $password = I('post.password');
            $password2 = I('post.password2');
            if ($password2 != $password) {
                $this->error('两次密码不一致', U('User/forget_pwd'));
            }
            if (session('user')['password'] == encrypt($password))
                return array('status' => -1, 'msg' => '新密码不得与旧密码相同！', 'result' => '');
            if ($check['is_check'] == 1) {
                $user = M('users')->where("mobile = '{$check['sender']}' or email = '{$check['sender']}'")->find();
                if ($user) {
                    M('users')->where("user_id=" . $user['user_id'])->save(array('password' => encrypt($password)));
                    session('validate_code', null);
                    $this->success('新密码已设置行牢记新密码', U('User/index'));
                    exit;
                } else {
                    $this->error('操作失败，请稍后再试', U('User/forget_pwd'));
                }
            } else {
                $this->error('验证码还未验证通过', U('User/forget_pwd'));
            }
        }
        $is_set = I('is_set', 0);
        $this->assign('is_set', $is_set);
        return $this->fetch();
    }

    /**
     * 验证码验证
     * $id 验证码标示
     */
    private function verifyHandle($id)
    {
        $verify = new Verify();
        if (!$verify->check(I('post.verify_code'), $id ? $id : 'user_login')) {
            return false;
        }
        return true;
    }

    /**
     * 验证码获取
     */
    public function verify()
    {
        //验证码类型
        $type = I('get.type') ? I('get.type') : 'user_login';
        $config = array(
            'fontSize' => 30,
            'length' => 4,
            'imageH' => 60,
            'imageW' => 300,
            'fontttf' => '5.ttf',
            'useCurve' => false,
            'useNoise' => false,
        );
        $Verify = new Verify($config);
        $Verify->entry($type);
        exit();
    }

    /**
     * 账户管理
     */
    public function accountManage()
    {
        return $this->fetch();
    }

    public function recharge()
    {
        $order_id = I('order_id/d');
        $paymentList = M('Plugin')->where("`type`='payment' and code!='cod' and status = 1 and  scene in(0,1) and code!='weixin'")->select();
        //微信浏览器
        if (strstr($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            $paymentList = M('Plugin')->where("`type`='payment' and status = 1 and code='weixin'")->select();
        }
        $paymentList = convert_arr_key($paymentList, 'code');

        foreach ($paymentList as $key => $val) {
            $val['config_value'] = unserialize($val['config_value']);
            if ($val['config_value']['is_bank'] == 2) {
                $bankCodeList[$val['code']] = unserialize($val['bank_code']);
            }
        }
        $bank_img = include APP_PATH . 'home/bank.php'; // 银行对应图片
        $payment = M('Plugin')->where("`type`='payment' and status = 1")->select();
        $this->assign('paymentList', $paymentList);
        $this->assign('bank_img', $bank_img);
        $this->assign('bankCodeList', $bankCodeList);

        // 查找最近一次充值方式
        $recharge_arr = Db::name('Recharge')->field('pay_code')->where('user_id', $this->user_id)
            ->order('order_id desc')->find();
        $alipay = 'alipayMobile'; //默认支付宝支付
        if($recharge_arr){
            foreach ($paymentList as  $key=>$item) {
                if($key == $recharge_arr['pay_code']){
                    $alipay = $recharge_arr['pay_code'];
                }
            }
        }
        $this->assign('alipay', $alipay);

        if ($order_id > 0) {
            $order = M('recharge')->where("order_id = $order_id")->find();
            $this->assign('order', $order);
        }
        return $this->fetch();
    }

    public function recharge_list(){
        $usersLogic = new UsersLogic;
        $usersLogic->setUserId($this->user_id);
        $result= $usersLogic->get_recharge_log();  //充值记录
        $this->assign('page', $result['show']);
        $this->assign('lists', $result['result']);
        if (I('is_ajax')) {
            return $this->fetch('ajax_recharge_list');
        }
        return $this->fetch();
    }

    /**
     * 申请提现记录
     */
    public function withdrawals()
    {
        C('TOKEN_ON', true);
        if ($this->user['is_lock'] == 1)
            $this->ajaxReturn(['status' => 0, 'msg' => '账号异常已被锁定！']);
		if (IS_POST) {
			$cash_open=tpCache('cash.cash_open');
			if($cash_open!=1){
				$this->ajaxReturn(['status'=>-1, 'msg'=>'提现功能已关闭,请联系商家']);
			}
			if (!$this->verifyHandle('withdrawals')) {
                $this->ajaxReturn(['status' => 0, 'msg' => '验证码错误']);
            }
            if (session('__token__') !== I('post.__token__', '')) {
                $this->ajaxReturn(['status' => 0, 'msg' => '参数错误']);
            };
			$data = I('post.');
			/*if($data['bank_name'] =='微信'){
				$data['realname'] = $data['bank_card'];
			}*/
			$data['user_id'] = $this->user_id;
			$data['create_time'] = time();
			$cash = tpCache('cash');
			if(empty($this->user['paypwd'])){
				$this->ajaxReturn(['status'=>-1, 'msg'=>'请先设置支付密码']);
			}
			//支付密码已加密
			if(isset($_POST['paypwd']) && $data['paypwd'] != $this->user['paypwd']){
				$this->ajaxReturn(['status'=>-1, 'msg'=>'支付密码错误']);
			}
			if ($data['money'] > $this->user['user_money']) {
				$this->ajaxReturn(['status'=>-1, 'msg'=>"本次提现余额不足"]);
			}
			if ($data['money'] <= 0) {
				$this->ajaxReturn(['status'=>-1, 'msg'=>'提现额度必须大于0']);
			}
			
			// 每次限提现额度
			if ($cash['min_cash'] > 0 && $data['money'] < $cash['min_cash']) {
				$this->ajaxReturn(['status'=>-1, 'msg'=>'每次最少提现额度' . $cash['min_cash']]);
			}
			if ($cash['max_cash'] > 0 && $data['money'] > $cash['max_cash']) {
				$this->ajaxReturn(['status'=>-1, 'msg'=>'每次最多提现额度' . $cash['max_cash']]);
			}

			$status = ['in','0,1,2,3'];
			$create_time = ['gt',strtotime(date("Y-m-d"))];
			// 今天限总额度
			if ($cash['count_cash'] > 0) {
				$total_money2 = Db::name('withdrawals')->where(array('user_id' => $this->user_id, 'status' => $status, 'create_time' => $create_time))->sum('money');
				if (($total_money2 + $data['money'] > $cash['count_cash'])) {
					$total_money = $cash['count_cash'] - $total_money2;
					if ($total_money <= 0) {
						$this->ajaxReturn(['status'=>-1, 'msg'=>"你今天累计提现额为{$total_money2},不能再提现了."]);
					} else {
						$this->ajaxReturn(['status'=>-1, 'msg'=>"你今天累计提现额为{$total_money2}，最多可提现{$total_money}账户余额."]);
					}
				}
			}
			// 今天限申请次数
			if ($cash['cash_times'] > 0) {
				$total_times = Db::name('withdrawals')->where(array('user_id' => $this->user_id, 'status' => $status, 'create_time' => $create_time))->count();
				if ($total_times >= $cash['cash_times']) {
					$this->ajaxReturn(['status'=>-1, 'msg'=>"今天申请提现的次数已用完."]);
				}
			}
			
			//手续费
			if ($cash['service_ratio'] > 0) {
				if ($cash['service_ratio'] >= 100) {
					$this->ajaxReturn(['status'=>-1, 'msg'=>'手续费率配置必须小于100%！']);
				}
				$taxfee = round($data['money'] * $cash['service_ratio'] / 100, 2);
				// 限手续费
				if ($cash['max_service_money'] > 0 && $taxfee > $cash['max_service_money']) {
					$taxfee = $cash['max_service_money'];
				}
				if ($cash['min_service_money'] > 0 && $taxfee < $cash['min_service_money']) {
					$taxfee = $cash['min_service_money'];
				}
				if ($taxfee >= $data['money']) {
					$this->ajaxReturn(['status'=>-1, 'msg'=>'手续费超过提现额度了！']);
				}
				$data['taxfee'] = $taxfee;
			}else{
				$data['taxfee'] = 0;
			}

			if (M('withdrawals')->add($data)) {
				$bank['bank_name'] = $data['bank_name'];
				$bank['bank_card'] = $data['bank_card'];
				$bank['realname'] = $data['realname'];
				M('users')->where(array('user_id'=>$this->user_id))->save($bank);
				$this->ajaxReturn(['status'=>1,'msg'=>"已提交申请", 'url'=>U('User/account')]);
			} else {
				$this->ajaxReturn(['status'=>-1,'msg'=>'提交失败,联系客服!']);
			}
        }
		
		$cashConfig = tpCache('cash');
		$this->assign('cash_config', $cashConfig);
        return $this->fetch();
    }

    /**
     * 申请记录列表
     * @param $type 提现类型 ： 0 = 余额提现 ， 1 = 佣金提现
     */
    public function withdrawals_list()
    {
        $type = I('type',0);
        $withdrawals_where['user_id'] = $this->user_id;
        $withdrawals_where['type'] = $type;
        $count = M('withdrawals')->where($withdrawals_where)->count();
        // $pagesize = C('PAGESIZE'); //10条数据，不显示滚动效果
        // $page = new Page($count, $pagesize);
        $page = new Page($count, 15);
        $list = M('withdrawals')->where($withdrawals_where)->order("id desc")->limit("{$page->firstRow},{$page->listRows}")->select();

        $this->assign('page', $page->show());// 赋值分页输出
        $this->assign('list', $list); // 下线
        if (I('is_ajax')) {
            return $this->fetch('ajax_withdrawals_list');
        }
        $this->assign('type',$type);
        return $this->fetch();
    }

    /**
     * 我的关注
     * @author lhb
     * @time   2017/4
     */
    public function myfocus()
    {
        /* 获取收藏的商家数量 */
        $sc_id = I('get.sc_id/d');
        if(!empty($sc_id)){
            $store_collect_where['sc_id'] = $sc_id;
        }
        $store_collect_where['user_id'] = $this->user_id;
        $StoreCollect = new StoreCollect();
        $store_collect_count = $StoreCollect->where($store_collect_where)->count();
        /* 获取收藏的商品数量 */
        $goodsNum = M('goods_collect')->where(array('user_id' => $this->user_id))->count();
        $this->assign('storeNum', $store_collect_count);
        $this->assign('goodsNum', $goodsNum);

        $type = I('get.focus_type/d', 0);
        if ($type == 0) {
            //商品收藏
            $userLogic = new UsersLogic();
            $data = $userLogic->get_goods_collect($this->user_id);
            $this->assign('goodsList', $data['result']);
        } else {
            //店铺收藏
            $store_collect_list = $StoreCollect->where($store_collect_where)->select();
            $this->assign('store_collect_list', $store_collect_list);
        }

        if (I('get.is_ajax')) {
            return $this->fetch('ajax_myfocus');
        }
        return $this->fetch();
    }

    /*
 *取消收藏
 */
    public function del_goods_focus()
    {
        $collect_id = I('collect_id/d');
        $user_id = $this->user_id;
        if (M('goods_collect')->where(["collect_id" => $collect_id, "user_id" => $user_id])->delete()) {
            $this->success("已取消收藏", U('User/myfocus'));
        } else {
            $this->error("未取消收藏", U('User/myfocus'));
        }
    }

    /**
     *  删除一个收藏店铺
     * @author lxl
     * @time17-3-28
     */
    public function del_store_focus()
    {
        $id = I('get.log_id/d');
        if (!$id) {
            $this->error("缺少ID参数");
        }
        $store_id = M('store_collect')->where(array('log_id' => $id, 'user_id' => $this->user_id))->getField('store_id');
        $row = M('store_collect')->where(array('log_id' => $id, 'user_id' => $this->user_id))->delete();
        if ($row) {
            M('store')->where(array('store_id' => $store_id))->setDec('store_collect');
            $this->success("已取消收藏", U('User/myfocus', 'focus_type=1'));
        } else {
            $this->error("未取消收藏", U('User/myfocus', 'focus_type=1'));
        }
    }


    /**
     *  用户消息通知
     */
    public function message_notice()
    {
        $message_logic = new Message();
        $message_logic->checkPublicMessage();
        $where = array(
            'user_id' => $this->user_id,
            'deleted' => 0,
            'category' => 0
        );
        $userMessage = new UserMessage();
        $data['message_notice'] = $userMessage->where($where)->LIMIT(1)->order('rec_id desc')->find();

        $where['category'] = 1;
        $data['message_activity'] = $userMessage->where($where)->LIMIT(1)->order('rec_id desc')->find();

        $where['category'] = 2;
        $data['message_logistics'] = $userMessage->where($where)->LIMIT(1)->order('rec_id desc')->find();

        $data['no_read'] = $message_logic->getUserMessageCount();

        // 最近消息，日期，内容
        $this->assign($data);
        return $this->fetch();
    }
    /**
     * 查看通知消息详情
     */
    public function message_notice_detail()
    {

        $type = I('type', 0);
        // $type==3私信，暂时没有

        $message_logic = new Message();
        $message_logic->checkPublicMessage();

        $where = array(
            'user_id' => $this->user_id,
            'deleted' => 0,
            'category' => $type
        );
        $userMessage = new UserMessage();
        $count = $userMessage->where($where)->count();
        $page = new Page($count, 10);
        //$lists = $userMessage->where($where)->order("rec_id DESC")->limit($page->firstRow . ',' . $page->listRows)->select();

        $rec_id = $userMessage->where( $where)->LIMIT($page->firstRow.','.$page->listRows)->order('rec_id desc')->column('rec_id');
        $lists = $message_logic->sortMessageListBySendTime($rec_id, $type);

        $this->assign('lists', $lists);
        if ($_GET['is_ajax']) {
            return $this->fetch('ajax_message_detail');
        }
        if (empty($lists)) {
            return $this->fetch('user/message_none');
        }
        return $this->fetch();
    }

    /**
     * 通知消息详情
     */
    public function message_notice_info(){
        $message_logic = new Message();
        $message_details = $message_logic->getMessageDetails(I('msg_id'), I('type', 0));
        $this->assign('message_details', $message_details);
        return $this->fetch();
    }


    /**
     * 浏览记录
     */
    public function visit_log()
    {
        $p = I('get.p', 1);

        $user_logic = new UsersLogic;
        $visit_list = $user_logic->visit_log($this->user_id, $p);

        $this->assign('visit_list', $visit_list);
        if (I('get.is_ajax', 0)) {
            return $this->fetch('ajax_visit_log');
        }
        return $this->fetch();
    }

    /**
     * 删除浏览记录
     */
    public function del_visit_log()
    {
        $visit_ids = I('get.visit_ids', 0);
        $row = M('goods_visit')->where('visit_id', 'IN', $visit_ids)->delete();

        if (!$row) {
            $this->error('操作失败', U('User/visit_log'));
        } else {
            $this->success("操作成功", U('User/visit_log'));
        }
    }

    /**
     * 清空浏览记录
     */
    public function clear_visit_log()
    {
        $row = M('goods_visit')->where('user_id', $this->user_id)->delete();

        if (!$row) {
            $this->error('操作失败', U('User/visit_log'));
        } else {
            $this->success("操作成功", U('User/visit_log'));
        }
    }

    /**
     * 支付密码
     * @return mixed
     */
    public function paypwd()
    {
        //检查是否第三方登录用户
        $logic = new UsersLogic();
        $data = $logic->get_info($this->user_id);
        $user = $data['result'];
        if ($user['mobile'] == '')
            $this->error('请先绑定手机号',U('User/setMobile',['source'=>'paypwd']));
        $step = I('step', 1);
        if ($step > 1) {
            $check = session('validate_code');
            if (empty($check)) {
                $this->error('验证码还未验证通过', U('Home/User/paypwd'));
            }
        }
        if (IS_POST && $step == 2) {

            $new_password = trim(I('new_password'));
            $confirm_password = trim(I('confirm_password'));
            $user = $this->user;
            //以前设置过就得验证原来密码
            $userLogic = new UsersLogic();
            $data = $userLogic->paypwd($this->user_id, $new_password, $confirm_password);
            $this->ajaxReturn($data);
            exit;
        }
        $this->assign('step', $step);
        return $this->fetch();
    }


    /**
     * 会员签到积分奖励
     * 2017/9/28
     */
    public function sign()
    {
        $userLogic = new UsersLogic();
        $user_id = $this->user_id;
        $info = $userLogic->idenUserSign($user_id);//标识签到
        $this->assign('info', $info);
        return $this->fetch();
    }

    /**
     * Ajax会员签到
     * 2017/11/19
     */
    public function user_sign()
    {
        $userLogic = new UsersLogic();
        $user_id   = $this->user_id;
        $config    = tpCache('sign');
        $date      = I('date'); //2017-9-29
        //是否正确请求
        (date("Y-n-j", time()) != $date) && $this->ajaxReturn(['status' => false, 'msg' => '签到失败！', 'result' => '']);
        //签到开关
        if ($config['sign_on_off'] > 0) {
            $map['sign_last'] = $date;
            $map['user_id']   = $user_id;
            $userSingInfo     = Db::name('user_sign')->where($map)->find();
            //今天是否已签
            $userSingInfo && $this->ajaxReturn(['status' => false, 'msg' => '您今天已经签过啦！', 'result' => '']);
            //是否有过签到记录
            $checkSign = Db::name('user_sign')->where(['user_id' => $user_id])->find();
            if (!$checkSign) {
                $result = $userLogic->addUserSign($user_id, $date);            //第一次签到
            } else {
                $result = $userLogic->updateUserSign($checkSign, $date);       //累计签到
            }
            $return = ['status' => $result['status'], 'msg' => $result['msg'], 'result' => ''];
        } else {
            $return = ['status' => false, 'msg' => '该功能未开启！', 'result' => ''];
        }
        $this->ajaxReturn($return);
    }

    /**
     * vip充值
     */
    public function rechargevip()
    {
        $paymentList = M('Plugin')->where("`type`='payment' and code!='cod' and status = 1 and  scene in(0,1)")->select();
        //微信浏览器
        if (strstr($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            $paymentList = M('Plugin')->where("`type`='payment' and status = 1 and code='weixin'")->select();
        }
        $paymentList = convert_arr_key($paymentList, 'code');

        foreach ($paymentList as $key => $val) {
            $val['config_value'] = unserialize($val['config_value']);
            if ($val['config_value']['is_bank'] == 2) {
                $bankCodeList[$val['code']] = unserialize($val['bank_code']);
            }
        }
        $bank_img = include APP_PATH . 'home/bank.php'; // 银行对应图片
        $payment = M('Plugin')->where("`type`='payment' and status = 1")->select();
        $this->assign('paymentList', $paymentList);
        $this->assign('bank_img', $bank_img);
        $this->assign('bankCodeList', $bankCodeList);


        return $this->fetch();
    }
    /**
     * 领券
     */
    public function getCoupon()
    {
        $coupon_id = I('coupon_id/s');
        $coupons=array_filter(explode('_',$coupon_id));
        $userInfo = session('user');
        if(empty($userInfo['user_id'])){
            $this->ajaxReturn(['status' => 0, 'msg' => '请先登录']);
        }
        $user = new \app\common\logic\User();
        $user->setUserById($userInfo['user_id']);
        try{
//            $user->getCouponByID($coupon_id);
            foreach($coupons as $coupon){
                $user->getCouponByID($coupon);
            }
        }catch (TpshopException $t){
            $this->ajaxReturn($t->getErrorArr());
        }
        $this->ajaxReturn(['status' => 1, 'msg' => '恭喜您，抢到优惠券!']);
    }

}