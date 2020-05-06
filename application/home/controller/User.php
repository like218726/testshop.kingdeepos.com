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
namespace app\home\controller;

use app\common\logic\MessageLogic;
use app\common\logic\OrderLogic;
use app\common\logic\UsersLogic;
use app\common\logic\CartLogic;
use app\common\logic\Message;
use app\common\model\UserMessage;
use app\common\model\GoodsCollect;
use app\common\model\GoodsVisit;
use app\common\model\StoreCollect;
use app\common\model\Users;
use app\common\util\TpshopException;
use think\Db;
use think\Page;
use think\Session;
use think\Verify;


class User extends Base
{

    public $user_id = 0;
    public $user = array();

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
            session('user', $user);  //覆盖session 中的 user
            $this->user = $user;
            $this->user_id = $user['user_id'];
            $this->assign('user', $user); //存储用户信息
            $this->assign('user_id', $this->user_id);
            //获取用户信息的数量
            $messageLogic = new Message();
            $user_message_count = $messageLogic->getUserMessageNoReadCount();
            $this->assign('user_message_count', $user_message_count);
        }
            $nologin = array(
                'login', 'pop_login', 'do_login', 'logout', 'verify', 'set_pwd', 'finished',
                'verifyHandle', 'reg', 'send_sms_reg_code', 'identity', 'check_validate_code',
                'forget_pwd', 'check_captcha', 'check_username', 'send_validate_code','bind_account','bind_guide','bind_reg',
            );
            if (!$this->user_id && !in_array(ACTION_NAME, $nologin)) {
                header("location:" . U('Home/User/login'));
                exit;
            }
            if (ACTION_NAME == 'password') $_SERVER['HTTP_REFERER'] = U("Home/User/index");


        //用户中心面包屑导航
        $navigate_user = navigate_user();
        $this->assign('navigate_user', $navigate_user);
    }

    /*
     * 用户中心首页
     */
    public function index()
    {
        $select_year = select_year(); // 查询 三个月,今年内,2016年等....订单
        $logic = new UsersLogic();
        $order = new \app\common\model\Order();
        $user = $logic->getHomeUserInfo($this->user_id);
        $user = $user['result'];
        $order_obj = M('order'.$select_year)->where(['user_id'=>$user['user_id'],'deleted'=>0,'prom_type'=>['lt',5]])->order('order_id DESC')->find();
        //先判断是否存在, 否则新注册用户进入用户中心报错
        if($order_obj && $order_obj['order_id'] && $order_obj['store_id']){
            $order_obj['order_status_detail'] = $order->getOrderStatusDetailAttr(null,$order_obj);
            $order_obj['order_button'] = $order->getOrderButtonAttr(null,$order_obj);
            $order_obj['order_goods'] = M('order_goods'.$select_year)->cache(true,3)->where('order_id = '.$order_obj['order_id'])->select();
            $order_obj['store'] = M('store')->cache(true)->where('store_id = '.$order_obj['store_id'])->field('store_id,store_name,store_qq')->find();
            $collect_result =Db::name('goods_collect')->alias('c')->field('c.*,g.shop_price')
                ->join('goods g','c.goods_id = g.goods_id','INNER')->where("c.user_id = ".$user['user_id'])
                ->order('collect_id')->select(); //收藏商品
        }
        $time = time();
        $coupon_list = Db::name('coupon_list')->alias('l')
            ->join('coupon c','c.id = l.cid','INNER')
            ->where("l.uid = $this->user_id and l.use_time = 0 and l.status<1 and c.status=1 and c.use_end_time > $time")
            ->order('l.id desc')
            ->limit(2)
            ->select();
        $level = M('user_level')->cache(true)->select();
        $level = convert_arr_key($level, 'level_id');
        $this->assign('level', $level);
        $this->assign('collect_result', $collect_result);
        $this->assign('coupon_list', $coupon_list);
        $this->assign('user', $user);
        $this->assign('order', $order_obj);
        return $this->fetch();
    }


    public function logout()
    {
        setcookie('uname', '', time() - 3600, '/');
        setcookie('cn', '', time() - 3600, '/');
        setcookie('user_id', '', time() - 3600, '/');
        setcookie('PHPSESSID','',time()-3600,'/');
        session_unset();
        session_destroy();
        $this->redirect(U('User/login'));
    }

    /*
     * 账户资金
     */
    public function account()
    {
        $user = session('user');
        //获取账户资金记录
        $logic = new UsersLogic();
        $logic->setUserId($this->user_id);
        $get_data = I('get.');
        $data = $logic->getPointsLog($get_data);
        $account_log = $data['result'];
        $this->assign('user', $user);
        $this->assign('account_log', $account_log);
        $this->assign('page', $data['show']);
        $this->assign('active', 'account');
        return $this->fetch();
    }

    /*
     * 优惠券列表
     */
    public function coupon()
    {
        $belong_type = input('belong_type/d',0);//0:全部,1:自营店, 2:商家
        $logic = new UsersLogic();
        $data = $logic->get_coupon($this->user_id, I('type'), I('order') , $belong_type);
        foreach($data['result'] as $k =>$v){
            if($v['use_type']==1){ //指定商品
                $data['result'][$k]['goods_id'] = M('goods_coupon')->field('goods_id')->where(['coupon_id'=>$v['cid']])->getField('goods_id');
            }
            if($v['use_type']==2){ //指定分类
                $data['result'][$k]['category_id'] = Db::name('goods_coupon')->where(['coupon_id'=>$v['cid']])->getField('goods_category_id');
            }
        }
        $coupon_list = $data['result'];
        $store_id = get_arr_column($coupon_list,'store_id');
        if(!empty($store_id)){
            $store = M('store')->where("store_id in (".implode(',', $store_id).")")->getField('store_id,store_name');
        }
        $this->assign('store',$store);
        $this->assign('coupon_list', $coupon_list);
        $this->assign('page', $data['show']);
        $this->assign('active', 'coupon');
        return $this->fetch();
    }
    
    /*
     * 删除优惠券
     */
    public function del_coupon()
    {
        $list_id = I('list_id/d',0);
        $row = M('coupon_list')->where('id' , $list_id)->update(['deleted' => 1]);
        if($row){
            $res = array('status'=>1 , 'msg'=>'删除成功');
        }else{
            $res = array('status'=>-1 , 'msg'=>'删除失败');
        }
        $this->ajaxReturn($res);
    }

    /**
     *  登录
     */
    public function login()
    {
        if ($this->user_id > 0) {
            $this->redirect(U('User/index'));
        }
        $redirect_url = Session::get('redirect_url');
        $referurl = $redirect_url ? $redirect_url : U("Home/User/index");
        $this->assign('referurl', $referurl);
        return $this->fetch();
    }

    public function pop_login()
    {
        $referurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U("Home/User/index");
        $this->assign('referurl', $referurl);
        return $this->fetch();
    }

    public function do_login()
    {
        $username = I('post.username');
        $password = I('post.password');
        $username = trim($username);
        $password = trim($password);
        $verify_code = I('post.verify_code');

        $verify = new Verify();
        if (!$verify->check($verify_code, 'user_login')) {
            $res = array('status' => 0, 'msg' => '验证码错误');
            $this->ajaxReturn($res);
        }

        $logic = new UsersLogic();
        $res = $logic->login($username, $password);

        if ($res['status'] == 1) {
            $res['url'] = htmlspecialchars_decode(I('post.referurl'));
            $res['result']['nickname'] = empty($res['result']['nickname']) ? $username : $res['result']['nickname'];
            setcookie('user_id', $res['result']['user_id'], null, '/');
            setcookie('is_distribut', $res['result']['is_distribut'], null, '/');
            setcookie('uname', urlencode($res['result']['nickname']), null, '/');
            setcookie('head_pic', urlencode($res['result']['head_pic']), null, '/');
            setcookie('cn', 0, time() - 3600, '/');
            session('user', $res['result']);
            $cartLogic = new CartLogic();
            $cartLogic->setUserId($res['result']['user_id']);
            $cartLogic->doUserLoginHandle();  //用户登录后 需要对购物车 一些操作
            $orderLogic= new OrderLogic();//登录后将超时未支付订单给取消掉
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
        if ($this->user_id > 0) $this->redirect(U('User/index'));
        $reg_sms_enable = tpCache('sms.regis_sms_enable');
        $reg_smtp_enable = tpCache('smtp.regis_smtp_enable');
        if (IS_POST) {
            $logic = new UsersLogic();
            //验证码检验
            $username = I('post.username', '');
            $password = I('post.password', '');
            $password2 = I('post.password2', '');
            $code = I('post.code', '');
            $scene = I('post.scene', 1);
            $session_id = session_id();
            if(check_mobile($username)){
                if($reg_sms_enable){   //是否开启注册验证码机制
                    //手机功能没关闭
                    $check_code = $logic->check_validate_code($code, $username, 'phone', $session_id, $scene);
                    if($check_code['status'] != 1){
                        $this->ajaxReturn(['status'=>0,'msg'=>$check_code['msg'],'result'=>'']);
                    }
                }else{
                    $verify = $this->verifyHandle('user_reg');
                    if(!$verify){
                        $this->ajaxReturn(['status'=>0,'msg'=>'图像验证码有误','result'=>'']);
                    }
                }
            }
            if(check_email($username)){
                if($reg_smtp_enable){        //是否开启注册邮箱验证码机制
                    //邮件功能未关闭
                    $check_code = $logic->check_validate_code($code, $username);
                    if($check_code['status'] != 1){
                        $this->ajaxReturn(['status'=>0,'msg'=>$check_code['msg'],'result'=>'']);
                        $this->error($check_code['msg']);
                    }
                }else{
                    $verify = $this->verifyHandle('user_reg');
                    if(!$verify){
                        $this->ajaxReturn(['status'=>0,'msg'=>'图像验证码有误','result'=>'']);
                    }
                }
            }
            $data = $logic->reg($username, $password, $password2);
            if ($data['status'] != 1) {
                $this->ajaxReturn($data);
            }
            session('user', $data['result']);
            setcookie('user_id', $data['result']['user_id'], null, '/');
            setcookie('is_distribut', $data['result']['is_distribut'], null, '/');
            $nickname = empty($data['result']['nickname']) ? $username : $data['result']['nickname'];
            setcookie('uname', $nickname, null, '/');
            setcookie('head_pic', urlencode($data['result']['head_pic']), null, '/');
            $cartLogic = new CartLogic();
            $cartLogic->setUserId($data['result']['user_id']);
            $cartLogic->doUserLoginHandle();  //用户登录后 需要对购物车 一些操作
            $this->ajaxReturn($data);
        }
        $this->assign('regis_sms_enable', tpCache('sms.regis_sms_enable')); // 注册启用短信：
        $this->assign('regis_smtp_enable', tpCache('smtp.regis_smtp_enable')); // 注册启用邮箱：
        $sms_time_out = tpCache('sms.sms_time_out') > 0 ? tpCache('sms.sms_time_out') : 120;
        $this->assign('sms_time_out', $sms_time_out); // 手机短信超时时间
        return $this->fetch();
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
        $this->assign('active', 'address_list');

        return $this->fetch();
    }

    /*
     * 添加地址
     */
    public function add_address()
    {
        header("Content-type:text/html;charset=utf-8");
        if (IS_POST) {
            $logic = new UsersLogic();
            $data = $logic->add_address($this->user_id, 0, I('post.'));
            if ($data['status'] != 1)
                exit('<script>alert("' . $data['msg'] . '");history.go(-1);</script>');
            $call_back = $_REQUEST['call_back'];
            echo "<script>parent.{$call_back}('success');</script>";
            exit(); // 成功 回调closeWindow方法 并返回新增的id
        }
        $p = M('region')->where(array('parent_id' => 0, 'level' => 1))->select();
        $this->assign('province', $p);
        return $this->fetch('edit_address');

    }

    /*
     * 地址编辑
     */
    public function edit_address()
    {
        header("Content-type:text/html;charset=utf-8");
        $id = I('get.id/d');
        $address = M('user_address')->where(array('address_id' => $id, 'user_id' => $this->user_id))->find();
        if (IS_POST) {
            $logic = new UsersLogic();
            $data = $logic->add_address($this->user_id, $id, I('post.'));
            if ($data['status'] != 1)
                exit('<script>alert("' . $data['msg'] . '");history.go(-1);</script>');

            $call_back = $_REQUEST['call_back'];
            echo "<script>parent.{$call_back}('success');</script>";
            exit(); // 成功 回调closeWindow方法 并返回新增的id
        }
        //获取省份
        $p = M('region')->where(array('parent_id' => 0, 'level' => 1))->select();
        $c = M('region')->where(array('parent_id' => $address['province'], 'level' => 2))->select();
        $d = M('region')->where(array('parent_id' => $address['city'], 'level' => 3))->select();
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
     * 设置默认收货地址
     */
    public function setAddressDefault()
    {
        $id = input('id/d');
        Db::name('user_address')->where(['user_id'=>$this->user_id])->update(['is_default' => 0]);
        $row = Db::name('user_address')->where(array('user_id' => $this->user_id, 'address_id' => $id))->update(array('is_default' => 1));
        if ($row !== false){
            $this->ajaxReturn(['status'=>1,'msg'=>'设置成功','result'=>'']);
        }else{
            $this->ajaxReturn(['status'=>0,'msg'=>'设置失败','result'=>$row]);
        }
    }

    /*
     * 地址删除
     */
    public function del_address()
    {
        $id = I('get.id/d');

        $address = M('user_address')->where("address_id", $id)->find();
        $row = M('user_address')->where(array('user_id' => $this->user_id, 'address_id' => $id))->delete();
        // 如果删除的是默认收货地址 则要把第一个地址设置为默认收货地址
        if ($address['is_default'] == 1) {
            $address2 = M('user_address')->where("user_id", $this->user_id)->find();
            $address2 && M('user_address')->where("address_id", $address2['address_id'])->save(array('is_default' => 1));
        }
        if (!$row)
            $this->error('操作失败', U('User/address_list'));
        else
            $this->success("操作成功", U('User/address_list'));
    }

    /**
     * 个人信息
     */
    public function info()
    {
        $userLogic = new UsersLogic();
        $user_info = M('users')->where('user_id', $this->user_id)->find();
        if (IS_POST) {
            I('post.nickname') ? $post['nickname'] = I('post.nickname') : false; //昵称
            I('post.qq') ? $post['qq'] = I('post.qq') : false;  //QQ号码
            I('post.head_pic') ? $post['head_pic'] = I('post.head_pic') : false; //头像地址
            I('post.sex') ? $post['sex'] = I('post.sex') : $post['sex'] = 0;  // 性别
            I('post.birthday') ? $post['birthday'] = strtotime(I('post.birthday')) : false;  // 生日
            I('post.province') ? $post['province'] = I('post.province') : false;  //省份
            I('post.city') ? $post['city'] = I('post.city') : false;  // 城市
            I('post.district') ? $post['district'] = I('post.district') : false;  //地区
            if (!$userLogic->update_info($this->user_id, $post))
                $this->error("保存失败");
            $this->success("操作成功");
            exit;
        }
        if($user_info['province'])
        {
            //  获取省份
            $province = M('region')->cache(true)->where(array('parent_id' => 0, 'level' => 1))->select();
            //  获取订单城市
            $city = M('region')->cache(true)->where(array('parent_id' => $user_info['province'], 'level' => 2))->select();
            //获取订单地区
            $area = M('region')->cache(true)->where(array('parent_id' => $user_info['city'], 'level' => 3))->select();
        }
        $this->assign('province', $province);
        $this->assign('city', $city);
        $this->assign('area', $area);
        $this->assign('user', $user_info);
        $this->assign('sex', C('SEX'));
        $this->assign('active', 'info');
        return $this->fetch();
    }

    /*
     * 邮箱验证
     */
    public function email_validate()
    {
        $userLogic = new UsersLogic();
        $user_info = M('users')->where('user_id', $this->user_id)->find();
        $step = I('get.step', 1);
        if (IS_POST) {
            $email = I('post.email');
            $old_email = I('post.old_email'); //旧邮箱
            $code = I('post.code');
            $info = session('validate_code');
            if (!$info)
                $this->error('非法操作');
            if ($info['time'] < time()) {
                session('validate_code', null);
                $this->error('验证超时，请重新验证');
            }
            //检查原邮箱是否正确
            if ($user_info['email_validated'] == 1 && $old_email != $user_info['email'])
                $this->error('原邮箱匹配错误');
            //验证邮箱和验证码
            if ($info['sender'] == $email && $info['code'] == $code) {
                session('validate_code', null);
                if (!$userLogic->update_email_mobile($email, $this->user_id))
                    $this->error('邮箱已存在');
                $this->success('绑定成功', U('Home/User/index'));
                exit;
            }
            $this->error('邮箱验证码不匹配');
        }
        $this->assign('step', $step);
        $this->assign('user_info', $user_info);
        return $this->fetch();
    }


    /*
    * 手机验证
    */
    public function mobile_validate()
    {
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_info($this->user_id); //获取用户信息
        $user_info = $user_info['result'];
        $config = tpCache('sms');
        $sms_time_out = $config['sms_time_out'];
        $step = I('get.step', 1);
        if (IS_POST) {
            $mobile = I('post.mobile');
            $old_mobile = I('post.old_mobile');
            $code = I('post.code');
            $scene = I('post.scene', 6);
            $session_id = I('unique_id', session_id());

            $logic = new UsersLogic();
            $res = $logic->check_validate_code($code, $old_mobile, 'phone', $session_id, $scene);

            if (!$res && $res['status'] != 1) $this->error($res['msg']);

            //检查原手机是否正确
            if ($user_info['mobile_validated'] == 1 && $old_mobile != $user_info['mobile'])
                $this->error('原手机号码错误');
            //验证手机和验证码

            //验证手机和验证码
            if ($res['status'] == 1) {
                return $this->fetch('set_mobile');
            } else {
                $this->error($res['msg']);
            }
             
        }
        $this->assign('time', $sms_time_out);
        $this->assign('step', $step);
        $this->assign('user_info', $user_info);
        return $this->fetch();
    }

    /**
     *我的收藏
     */
    public function goods_collect()
    {
        $show_type = I('get.show_type/d', -1);   //-1: 全部商品, 2:活动商品
        //商品收藏
        $userLogic = new UsersLogic();
        $data = $userLogic->get_goods_collect($this->user_id , -1);//全部商品
        $prom_data = $userLogic->get_goods_collect($this->user_id , 2);//活动商品
        if($show_type==-1){
            //全部
            $this->assign('lists', $data['result']);
            $this->assign('promPager', $prom_data['page']);
            $this->assign('pager', $data['page']);
        }else{//活动
            $this->assign('lists', $prom_data['result']);
            $this->assign('promPager', $prom_data['page']);
            $this->assign('pager', $data['page']);
        }
        $this->assign('page', $data['show']);// 赋值分页输出
        $this->assign('active', 'goods_collect');
        return $this->fetch();
    }

    /**
     * 店铺收藏
     */
    public function store_collect()
    {
        $StoreCollect = new StoreCollect();
        $sc_id = input('get.sc_id/d');
        if(!empty($sc_id)){
            $storeWhere['sc_id'] = $sc_id;
        }
        $store_class = Db::name('store_class')->field('sc_id,sc_name')->select(); //店铺分类
        $store_id_arr = Db::name('store')->where($storeWhere)->getField('store_id',true);  //分类找店铺
        $store_ids = implode(',',$store_id_arr);
        $storeCollectWhere['user_id'] = $this->user_id;
        $storeCollectWhere['store_id'] = ['in',$store_ids];
        $store_collect_count = $StoreCollect->where($storeCollectWhere)->count();
        $page = new Page($store_collect_count, 10);
        $store_collect_list = $StoreCollect->where($storeCollectWhere)->select();  //符合条件的店铺
        $this->assign('page', $page);
        $this->assign('store_collect_list', $store_collect_list);
        $this->assign('store_class', $store_class);//店铺分类
        return $this->fetch();
    }


    public function myCollect()
    {
        $item = input('item', 12);
        $goodsCollectModel = new GoodsCollect();
        $user_id = $this->user_id;
        $goodsList = $goodsCollectModel->with('goods')->where('user_id', $user_id)->limit($item)->order('collect_id', 'desc')->select();
        foreach($goodsList as $key=>$goods){
            $goodsList[$key]['url'] = $goods->url;
            $goodsList[$key]['imgUrl'] = goods_thum_images($goods['goods_id'], 160, 160);
        }
        if ($goodsList) {
            $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $goodsList]);
        } else {
            $this->ajaxReturn(['status' => 0, 'msg' => '没有记录', 'result' => '']);
        }
    }

    /*
     * 删除一个收藏商品
     */
    public function del_goods_collect()
    {
        $id = I('get.id');//这里为字符串
        if (!$id)
            $this->error("缺少ID参数");
        $row = M('goods_collect')->where(array('collect_id' => array('in', $id), 'user_id' => $this->user_id))->delete();
        if (!$row)
            $this->error("删除失败");
        $this->success('删除成功');
    }

    /**
     *  删除一个收藏店铺
     */
    public function del_store_collect()
    {
        $id = I('get.log_id/d');
        if (!$id)
            $this->ajaxReturn(['status'=>0,'msg'=>"参数错误", 'url'=>U('User/goods_collect',['collect_type'=>2])]);
        $store_id = M('store_collect')->where(array('log_id' => $id, 'user_id' => $this->user_id))->getField('store_id');
        $row = M('store_collect')->where(array('log_id' => $id, 'user_id' => $this->user_id))->delete();
        M('store')->where(array('store_id' => $store_id))->setDec('store_collect');
        if ($row){
            $this->ajaxReturn(['status'=>1,'msg'=>"取消成功", 'url'=>U('User/goods_collect',['collect_type'=>2])]);
        } else {
            $this->ajaxReturn(['status'=>0,'msg'=>"取消失败", 'url'=>U('User/goods_collect',['collect_type'=>2])]);
        }
    }

    /*
     * 密码修改
     */
    public function password()
    {
        //检查是否第三方登录用户
        $logic = new UsersLogic();
        $data = $logic->get_info($this->user_id);
        $user = $data['result'];
        if ($user['mobile'] == '' && $user['email'] == '')
            $this->error('请先绑定手机或邮箱', U('Home/User/info'));
        $step = I('step', 1);
        if ($step > 1) {
            $check = session('validate_code');
            if (empty($check)) {
                $this->error('验证码还未验证通过', U('Home/User/password'));
            }
        }
        if (IS_POST && $step == 3) {
            $old_password =  trim(I('old_password'));
            $new_password =  trim(I('new_password'));
            $confirm_password =  trim(I('confirm_password'));
            $data = $logic->password($this->user_id,$old_password,$new_password,$confirm_password);
            if ($data['status'] == -1) $this->error($data['msg']);
            $this->redirect(U('Home/User/password', array('step' => 3)));
            exit;
        }
        $this->assign('step', $step);
        return $this->fetch();
    }

    public function paypwd()
    {
        //检查是否第三方登录用户
        $logic = new UsersLogic();
        $data = $logic->get_info($this->user_id);
        $user = $data['result'];
        if ($user['mobile'] == '')
            $this->error('请先绑定手机',U('User/mobile_validate',['source'=>'paypwd']));
        $step = I('step', 1);
        if ($step > 1) {
            $check = session('validate_code');
            if (empty($check)) {
                $this->error('验证码还未验证通过', U('Home/User/paypwd'));
            }
        }
        if (IS_POST && $step == 3) {
            $userLogic = new UsersLogic();
            $oldpaypwd = trim(I('old_paypwd')); 
            /* if(!empty($user['paypwd']) && ($user['paypwd'] != encrypt($oldpaypwd))){
                $this->error('原密码验证错误！');
            } */
            $data = $userLogic->paypwd($this->user_id, I('new_password'), I('confirm_password'));
            if ($data['status'] == -1)
                $this->error($data['msg']);
            $this->redirect(U('Home/User/paypwd', array('step' => 3)));
            exit;
        }
        $this->assign('step', $step);
        return $this->fetch();
    }

    public function forget_pwd()
    {
        if ($this->user_id > 0) {
            header("Location: " . U('Home/User/index'));exit;
        }
        if (IS_POST) {
            $username = I('username');
            if (!empty($username)) {
                $field = 'mobile';
                if (check_email($username)) {
                    $field = 'email';
                }
                $user = M('users')->where("email", $username)->whereOr('mobile', $username)->find();
                if ($user) {
                    session('find_password', array('user_id' => $user['user_id'], 'username' => $username,
                        'email' => $user['email'], 'mobile' => $user['mobile'], 'type' => $field));
                    header("Location: " . U('User/identity'));
                    exit;
                } else {
                    $this->error("用户名不存在，请检查");
                }
            }
        }
        return $this->fetch();
    }

    public function set_pwd()
    {
        if ($this->user_id > 0) {
            header("Location: " . U('Home/User/index'));exit;
        }
        $check = session('validate_code');
        $logic = new UsersLogic();
        if (empty($check)) {
            header("Location:" . U('Home/User/forget_pwd'));exit;
        } elseif ($check['is_check'] == 0) {
            $this->error('验证码还未验证通过', U('Home/User/forget_pwd'));
        }
        if (IS_POST) {
            $password = I('post.password');
            $password2 = I('post.password2');
            if ($password2 != $password) {
                $this->error('两次密码不一致', U('Home/User/forget_pwd'));
            }
            if ($check['is_check'] == 1) {
                $user = M('users')->where("mobile", $check['sender'])->whereOr('email', $check['sender'])->find();
                if ($user) {
                    if (M('users')->where("user_id", $user['user_id'])->save(array('password' => encrypt($password)))) {
			session('validate_code',null);
                        header("Location:" . U('Home/User/finished'));exit;
                    } else {
                        $this->error('操作失败，请稍后再试', U('Home/User/forget_pwd'));
                    }
                }
            } else {
                $this->error('验证码还未验证通过', U('Home/User/forget_pwd'));
            }
        }
        return $this->fetch();
    }
    
    
    /**
     * 修改绑定手机
     * @return mixed
     */
    public function set_mobile()
    {
        $userLogic = new UsersLogic();
        if (IS_POST) {
            $mobile = input('mobile');
            $mobile_code = input('mobile_code');
            $scene = input('post.scene', 6);
            $validate = I('validate', 0);
            $status = I('status', 0);
            $session_id = I('unique_id', session_id());// 唯一id  类似于 pc 端的session id
            
            $c = Db::name('users')->where(['mobile' => $mobile, 'user_id' => ['<>', $this->user_id]])->count();
            $c && $this->ajaxReturn(['status'=>-1,'msg'=>'手机已被使用']);
            if (!$mobile_code)$this->ajaxReturn(['status'=>-1,'msg'=>'请输入验证码']);
 
            $check_code = $userLogic->check_validate_code($mobile_code, $mobile, 'phone', $session_id, $scene);
                                   
            if ($check_code['status'] != 1) {
                $this->ajaxReturn(['status'=>-1,'msg'=>$check_code['msg']]);
            }
           
            if (!$userLogic->update_email_mobile($mobile, $this->user_id, 2)){
                $source = I('source');
                !empty($source) && $this->ajaxReturn(['status'=>1,'msg'=>'绑定成功', 'url'=>U('Home/User/'.$source)]);  
                $this->ajaxReturn(['status'=>1,'msg'=>'绑定成功', 'url'=>U('User/info')]);
            }else{
                $this->ajaxReturn(['status'=>1,'msg'=>'修改成功']);
            }
        }
        $sms_time_out = tpCache('sms.sms_time_out') > 0 ? tpCache('sms.sms_time_out') : 120;
        $this->assign('sms_time_out', $sms_time_out); // 手机短信超时时间
        $this->assign('status', $status);
        return $this->fetch();
    }

    public function finished()
    {
        if ($this->user_id > 0) {
            header("Location: " . U('Home/User/index'));exit;
        }
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
    
    public function bind_guide(){
        
        $data = session('third_oauth');
        $this->assign("nickname", $data['nickname']);
        $this->assign("oauth", $data['oauth']);
        $this->assign("head_pic", $data['head_pic']);
        
        return $this->fetch();
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
    // 绑定第三方账号
    public function bind_auth()
    {
        $list = M('plugin')->cache(true)->where(array('type' => 'login', 'status' => 1))->select();
        if ($list) {
            foreach ($list as $val) {
                $val['is_bind'] = 0;
                if($val['code'] == 'weixin'){
                    $thridUser = Db::name('OauthUsers')->where('user_id', $this->user['user_id'])->where(function ($query) {
                        $query->where('oauth', 'weixin')->whereor('oauth', 'wx')->whereor('oauth', 'miniapp');
                    })->find();
                }else{
                    $thridUser = Db::name('OauthUsers')->where(array('user_id'=>$this->user['user_id'] , 'oauth'=>$val['code']))->find();
                }

                if ($thridUser) {
                    $val['is_bind'] = 1;
                }
                $val['bind_url'] = U('LoginApi/login', array('oauth' => $val['code']));
                $val['bind_remove'] = U('User/bind_remove', array('oauth' => $val['code']));;
                $val['config_value'] = unserialize($val['config_value']);
                $lists[] = $val;
            }
        }
        $this->assign('lists', $lists);
        return $this->fetch();
    }

    public function bind_remove()
    {
        $oauth = I('oauth');
        if($oauth == 'weixin'){
            $row = Db::name('OauthUsers')->where('user_id', $this->user_id)->where(function ($query) {
                $query->where('oauth', 'weixin')->whereor('oauth', 'wx')->whereor('oauth', 'miniapp');
            })->delete();
        }else{
            $row = Db::name('OauthUsers')->where(array('user_id' => $this->user_id , 'oauth'=>$oauth))->delete();
        }
        if ($row) {
            $this->success('解除绑定成功', U('Home/User/bind_auth'));
        } else {
            $this->error('解除绑定失败', U('Home/User/bind_auth'));
        }
        
    }

    public function check_captcha()
    {
        $verify = new Verify();
        $type = I('post.type', 'user_login');
        if (!$verify->check(I('post.verify_code'), $type)) {
            exit(json_encode(0));
        } else {
            exit(json_encode(1));
        }
    }

    public function check_username()
    {
        $username = I('post.username');
        if (!empty($username)) {
            $count = M('users')->where("email", $username)->whereOr('mobile', $username)->count();
           if($count)$this->ajaxReturn(['status'=>1,'msg'=>'验证成功']);
            $this->ajaxReturn(['status'=>0,'msg'=>'用户名验证有误']);
        } else {
            $this->ajaxReturn(['status'=>0,'msg'=>'请输入用户名']);
        }
    }

    public function identity()
    {
        if ($this->user_id > 0) {
            header("Location: " . U('Home/User/index'));exit;
        }
        $user = session('find_password');
        if (empty($user)) {
            $this->error("请先验证用户名", U('User/forget_pwd'));
        }
        $this->assign('userinfo', $user);
        $sms_time_out = tpCache('sms.sms_time_out') > 0 ? tpCache('sms.sms_time_out') : 120;
        $this->assign('sms_time_out', $sms_time_out); // 手机短信超时时间
        return $this->fetch();
    }

    /**
     * 验证码验证
     * $id 验证码标示
     */
    private function verifyHandle($id)
    {
        $verify = new Verify();
        $result = $verify->check(I('post.verify_code'), $id ? $id : 'user_login');
        if (!$result) {
            return false;
        }else{
            return true;
        }
    }

    /**
     * 验证码获取
     */
    public function verify()
    {
        //验证码类型
        $type = I('get.type') ? I('get.type') : 'user_login';
        $config = array(
            'fontSize' => 40,
            'length' => 4,
            'useCurve' => false,
            'useNoise' => false,
        );
        $Verify = new Verify($config);
        $Verify->entry($type);
		exit();
    }

    /**
     * 安全设置
     */
    public function safety_settings()
    {
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_info($this->user_id); // 获取用户信息
        $user_info = $user_info['result'];
        $this->assign('user', $user_info);
        return $this->fetch();
    }

    /**
     * 申请提现
     */
    public function withdrawals()
    {
        if($this->user['is_lock'] == 1)$this->error('账号异常已被锁定！');
        if (IS_POST) {
			$cash_open=tpCache('cash.cash_open');
			if($cash_open!=1){
				$this->error('提现功能已关闭,请联系商家');
			}
			$data = I('post.');
			if($data['bank_name'] =='微信'){
				$data['realname'] = $data['bank_card'];
			}
			$data['user_id'] = $this->user_id;
			$data['create_time'] = time();
			$cash = tpCache('cash');
			if(empty($this->user['paypwd'])){
				$this->error('请先设置支付密码');
			}
			//支付密码已加密
			if(isset($_POST['paypwd']) && $data['paypwd'] != $this->user['paypwd']){
				$this->error('支付密码错误');
			}
			if ($data['money'] > $this->user['user_money']) {
				$this->error("本次提现余额不足");
			}
			if ($data['money'] <= 0) {
				$this->error('提现额度必须大于0');
			}
			
			// 每次限提现额度
			if ($cash['min_cash'] > 0 && $data['money'] < $cash['min_cash']) {
				$this->error('每次最少提现额度' . $cash['min_cash']);
			}
			if ($cash['max_cash'] > 0 && $data['money'] > $cash['max_cash']) {
				$this->error('每次最多提现额度' . $cash['max_cash']);
			}

			$status = ['in','0,1,2,3'];
			$create_time = ['gt',strtotime(date("Y-m-d"))];
			// 今天限总额度
			if ($cash['count_cash'] > 0) {
				$total_money2 = Db::name('withdrawals')->where(array('user_id' => $this->user_id, 'status' => $status, 'create_time' => $create_time))->sum('money');
				if (($total_money2 + $data['money'] > $cash['count_cash'])) {
					$total_money = $cash['count_cash'] - $total_money2;
					if ($total_money <= 0) {
						$this->error("你今天累计提现额为{$total_money2},不能再提现了.");
					} else {
						$this->error("你今天累计提现额为{$total_money2}，最多可提现{$total_money}账户余额.");
					}
				}
			}
			// 今天限申请次数
			if ($cash['cash_times'] > 0) {
				$total_times = Db::name('withdrawals')->where(array('user_id' => $this->user_id, 'status' => $status, 'create_time' => $create_time))->count();
				if ($total_times >= $cash['cash_times']) {
					$this->error("今天申请提现的次数已用完.");
				}
			}
			
			if ($cash['service_ratio'] > 0) {
				if ($cash['service_ratio'] >= 100) {
					$this->error('手续费率配置必须小于100%！');
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
					$this->error('手续费超过提现额度了！');
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
				$this->success("已提交申请");
			} else {
				$this->error('提交失败,联系客服!');
			}
        }
        $userLogic = new UsersLogic();
        $result = $userLogic->get_withdrawals_log($this->user_id);  //提现记录
        $this->assign('show',$result['show']);//赋值分页输出
        $this->assign('list',$result['result']); //下线
        return $this->fetch();
    }

	/**
     * 申请提现记录
     */
    public function recharge()
    {
        if (IS_POST) {
            $user = session('user');
            $data['user_id'] = $this->user_id;
            $data['nickname'] = $user['nickname'];
            $data['account'] = I('account');
            $data['order_sn'] = 'recharge' . get_rand_str(10, 0, 1);
            $data['ctime'] = time();
            $order_id = M('recharge')->add($data);
            if ($order_id) {
                $url = U('Home/Payment/getPay', array('pay_radio' => $_REQUEST['pay_radio'], 'order_id' => $order_id));
                $this->redirect($url);
            } else {
                $this->error('提交失败,参数有误!');
            }
        }
        $paymentList = M('Plugin')->cache(true)->where("`type`='payment' and code!='cod' and status = 1 and scene in(0,2)")->select();
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
        $this->assign('bankCodeList', $bankCodeList);

        // 查找最近一次充值方式
        $recharge_arr = Db::name('Recharge')->field('pay_code')->where('user_id', $this->user_id)
           ->order('order_id desc')->find();
        $alipay = 'alipay'; //默认支付宝支付
        if($recharge_arr){
            foreach ($paymentList as  $key=>$item) {
                if($key == $recharge_arr['pay_code']){
                    $alipay = $recharge_arr['pay_code'];
                }
             }
        }
        $this->assign('alipay', $alipay);

        $type = I('type');
        $userLogic = new UsersLogic();
        $userLogic->setUserId($this->user_id);
        if($type == 1){
            $result=$userLogic->getMoneylog();  //用户资金变动记录
        }else if($type == 2){
            $result=$userLogic->get_withdrawals_log();  //提现记录
        }else{
            $result=$userLogic->get_recharge_log();  //充值记录
        }
        $this->assign('page', $result['show']);
        $this->assign('lists', $result['result']);
        return $this->fetch();
    }

    /**
     *  用户消息通知
     * @author yhj
     * @time 2018-7-16
     */
    public function message_notice()
    {
        $message_logic = new Message();
        $message_logic->checkPublicMessage();

        $type = I('type', 2);
        $user_info = session('user');
        $where = array(
            'user_id' => $user_info['user_id'],
            'deleted' => 0,
            'category' => $type
        );
        $size = $type == 0 ? 4 : 3;
        $userMessage = new UserMessage();

        $count = $userMessage->where($where)->count();
        $page = new Page($count, $size);
        $show = $page->show();
        $rec_id = $userMessage->where( $where)->LIMIT($page->firstRow.','.$page->listRows)->order('rec_id desc')->column('rec_id');
        if(empty($rec_id) && empty($count)){
            $list = [];
        } else {
            if(empty($rec_id) && $count > 0){
                $rec_id = $userMessage->where( $where)->limit($size)->order('rec_id desc')->column('rec_id');
            }
            $list = $message_logic->sortMessageListBySendTime($rec_id, $type);
        }


        $no_read = $message_logic->getUserMessageCount();
        $this->assign('no_read', $no_read);
        $this->assign('page', $show);
        $this->assign('list', $list);
        return $this->fetch();
    }
    /**
     *  用户消息详情
     * @author yhj
     * @time 2018-7-16
     */
    public function message_details()
    {

        $message_logic = new Message();
        $data['message_details'] = $message_logic->getMessageDetails(I('msg_id'), I('type', 0));
        $data['no_read'] = $message_logic->getUserMessageCount();
        $this->assign($data);
        return $this->fetch();
    }
    /**
     * ajax用户消息删除请求
     * @author yhj
     * @time 2018-7-16
     */
    public function deletedMessage()
    {
        $message_logic = new Message();
        $res = $message_logic->deletedMessage(I('msg_id'),I('type'));
        $this->ajaxReturn($res);
    }
    /**
     * ajax设置用户消息已读
     * @author yhj
     * @time 2018-7-17
     */
    public function setMessageForRead()
    {
        $message_logic = new Message();
        $res = $message_logic->setMessageForRead(I('msg_id'));
        $this->ajaxReturn($res);
    }
    /**
     * ajax用户消息通知请求
     * @author dyr
     * @time 2016/09/01
     */
    public function ajax_message_notice()
    {
        $type = I('type');
        $message_model = new MessageLogic();
        if ($type === '0') {
            //系统消息
            $user_sys_message = $message_model->getUserMessageNotice();
        } else if ($type == 1) {
            //活动消息
            $user_sys_message = $message_model->getUserSellerMessage();
        } else {
            //全部消息
            $user_sys_message = $message_model->getUserAllMessage();
        }
        $this->assign('messages', $user_sys_message);
        echo $this->fetch();
    }

    /**
     * ajax用户消息通知请求
     * @time 2016/09/01
     */
    public function set_message_notice()
    {
        $type = I('type');
        $msg_id = I('msg_id');
        $user_logic = new UsersLogic();
        $res = $user_logic->setMessageForRead($type,$msg_id);
        $this->ajaxReturn($res);
    }

    /**
     * 删除足迹
     */
    public function del_visit_log(){
        
        $visit_id = I('visit_id/d' , 0);
        $row = M('goods_visit')->where(array('visit_id'=>$visit_id))->delete();
        if($row>0){
            return $this->ajaxReturn(['status'=>1 , 'msg'=> '删除成功']);
        }else{
            return $this->ajaxReturn(['status'=>-1 , 'msg'=> '删除失败']);
        }
    }

    public function visit_log()
    {
        $cat_id3 = I('cat_id3', 0);
        $map['user_id'] = $this->user_id;
        $visit_total = M('goods_visit a')->where($map)->count();
        if ($cat_id3 > 0) $map['a.cat_id3'] = $cat_id3;
        $count = M('goods_visit a')->where($map)->count();
        $Page = new Page($count, 50);
        $visit_list = M('goods_visit a')->field("a.*,g.goods_name,g.shop_price")
            ->join('__GOODS__ g', 'a.goods_id = g.goods_id', 'LEFT')->where($map)
            ->limit($Page->firstRow . ',' . $Page->listRows)->order('a.visit_time desc')->select();
        $visit_log = $cates = array();
        if ($visit_list) {
            $now = time();
            $endLastweek = mktime(23, 59, 59, date('m'), date('d') - date('w') + 7 - 7, date('Y'));
            $weekarray = array("日", "一", "二", "三", "四", "五", "六");
            foreach ($visit_list as $k => $val) {
                if ($now - $val['visit_time'] < 3600 * 24 * 7) {
                    if (date('Y-m-d') == date('Y-m-d', $val['visit_time'])) {
                        $val['date'] = '今天';
                    } else {
                        if ($val['visit_time'] < $endLastweek) {
                            $val['date'] = "上周" . $weekarray[date("w", $val['visit_time'])];
                        } else {
                            $val['date'] = "周" . $weekarray[date("w", $val['visit_time'])];
                        }
                    }
                } else {
                    $val['date'] = '更早以前';
                }
                $visit_log[$val['date']][] = $val;
            }
            $cates = M('goods_visit a')->field('cat_id3,COUNT(cat_id3) as csum')->where($map)->group('cat_id3')->select();
            $cat_ids = get_arr_column($cates,'cat_id3');
            $cateArr = Db::name('goods_category')->whereIN('id', array_unique($cat_ids))->getField('id,name'); //收藏商品对应分类名称
            foreach ($cates as $k => $v) {
                if (isset($cateArr[$v['cat_id3']])) $cates[$k]['name'] = $cateArr[$v['cat_id3']];
            }
        }
        $this->assign('visit_total', $visit_total);
        $this->assign('catids', $cates);
        $this->assign('page', $Page->show());
        $this->assign('visit_log', $visit_log);//浏览记录
        return $this->fetch();
    }

    /**
     * 历史记录
     */
    public function historyLog(){
        $item = input('item', 12);
        $goodsCollectModel = new GoodsVisit();
        $user_id = $this->user_id;
        $goodsList = $goodsCollectModel->with('goods')->where('user_id', $user_id)->limit($item)->order('visit_id', 'desc')->select();
        foreach($goodsList as $key=>$goods){
            $goodsList[$key]['url'] = $goods->url;
            $goodsList[$key]['imgUrl'] = goods_thum_images($goods['goods_id'], 160, 160);
        }
        if ($goodsList) {
            $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $goodsList]);
        } else {
            $this->ajaxReturn(['status' => 0, 'msg' => '没有记录', 'result' => '']);
        }
    }

    /**
     * 用户领取优惠券
     */
    public function getCoupon(){
        $coupon_id = input('coupon_id');
        if(empty($coupon_id)){
            $this->ajaxReturn(['status' => 0, 'msg' => '请选择要领取的优惠券', 'result' => '']);
        }
        $user = new \app\common\logic\User();
        $user->setUserById($this->user_id);
        try{
            $user->getCouponByID($coupon_id);
        }catch (TpshopException $t){
            $this->ajaxReturn($t->getErrorArr());
        }
        $this->ajaxReturn(['status' => 1, 'msg' => '恭喜您，抢到优惠券!']);
    }
}