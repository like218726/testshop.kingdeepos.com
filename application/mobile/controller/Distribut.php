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
use app\common\logic\GoodsLogic;
use app\common\logic\DistributLogic;
use app\common\model\Users;
use think\Verify;
use think\Db;
use think\Page;
use app\common\logic\UsersLogic;

class Distribut extends MobileBase {
    /*
    * 初始化操作
    */
    public $user_id = 0;
    public $user = [];
    public function _initialize() {
        parent::_initialize();
        if($this->tpshop_config['distribut_switch'] != 1){
            $this->error('分销已关闭',U('Mobile/Index/index'));
        }
        if(session('?user'))
        {
            $user = session('user');
            $this->user = $user;
            $this->user_id = $user['user_id'];
            $new_user = (new Users())->where(['user_id'=>$this->user_id])->find();
            $user['underling_number'] = $new_user['underling_number'];
            $this->assign('user',$user); //存储用户信息
        }
        $nologin = array(
            'login','pop_login','do_login','logout','verify','set_pwd','finished',
            'verifyHandle','reg','send_sms_reg_code','find_pwd','check_validate_code',
            'forget_pwd','check_captcha','check_username','send_validate_code',
        );
        if(!$this->user_id && !in_array(ACTION_NAME,$nologin)){
            header("location:".U('Mobile/User/login'));
            exit;
        }
        $first_leader = I('first_leader/d');
        if($this->user['is_distribut'] == 1){ //是分销商才查找用户店铺信息
            $store_user_id = ($first_leader>0) ? $first_leader :  $this->user_id;
            $user_store = Db::name('user_store')->where("user_id", $store_user_id)->find();
            $this->userStore=$user_store;
            $this->assign('store',$user_store);
        }

        $order_count = Db::name('order')->where("user_id", $this->user_id)->count(); // 我的订单数
        $goods_collect_count = Db::name('goods_collect')->where("user_id", $this->user_id)->count(); // 我的商品收藏
        $comment_count = Db::name('comment')->where("user_id", $this->user_id)->count();//  我的评论数
        $coupon_count = Db::name('coupon_list')->where("uid", $this->user_id)->count(); // 我的优惠券数量
        $first_nickname = Db::name('users')->where("user_id", $this->user['first_leader'])->getField('nickname');
        $level_name = Db::name('user_level')->where("level_id", $this->user['level'])->getField('level_name'); // 等级名称
        $this->assign('level_name',$level_name);
        $this->assign('first_nickname',$first_nickname);
        $this->assign('order_count',$order_count);
        $this->assign('goods_collect_count',$goods_collect_count);
        $this->assign('comment_count',$comment_count);
        $this->assign('coupon_count',$coupon_count);

    }

    /**
     * 分销用户中心首页（分销中心）
     */
    /**
     * 新版分销用户中心首页（分销中心）
     */
    public function index(){

        $time=strtotime(date("Y-m-d"));
        $money['today_money'] = Db::name('rebate_log')->where("user_id=$this->user_id and status in(2,3) and create_time>$time")->sum('money');    //今日收入

        $logic =  new DistributLogic;
        $user = $logic->lower($this->user_id);
        $withdraw = $user['distribut_money'] - $user['distribut_withdrawals_money']; //可提现
        $user['withdrawing'] = DB::name('withdrawals')->where(['user_id'=>$this->user_id,'type'=>1,'status'=>['in',[0,1]] ])->sum('money');    //正在提现
        $user['withdraw'] = $withdraw - $user['withdrawing'];  //最终可提现

        $distribut_level = '未成为分销商';
        if(1 == $user['is_distribut']){
            $distribut_level = '默认分销商';
            if($user['distribut_level'] > 0){
                $distribut_level = Db::name('distribut_level')->where('level_id',$this->user['distribut_level'])->value('level_name');
            }
        }

        $this->assign('user',$user);
        $this->assign('distribut_level',$distribut_level);
        $this->assign('user_id',$this->user_id);
        $this->assign('money',$money);
        return $this->fetch();
    }


    /**
     * 下线列表(我的团队)
     */
    public function lower_list(){
        $user =$this->user;
        if($user['is_distribut'] != 1) {
            $this->error('您还不是分销商');
        }
        $level = I('get.level',1);
        $q = I('post.q','','trim');

        $logic = new DistributLogic;
        $result = $logic->lowerList($this->user_id, $level, $q);

        $this->assign('count', $result['count']);// 总人数
        $this->assign('fcount', $result['fcount']);// 一级总人数
        $this->assign('scount', $result['scount']);// 二级总人数
        $this->assign('tcount', $result['tcount']);// 三级总人数
        $this->assign('page', $result['page']);// 赋值分页输出
        $this->assign('lists',$result['lists']); // 下线
        if (I('is_ajax')) {
            return $this->fetch('ajax_lower_list');
        }
        return $this->fetch();
    }

    /**
     * 下线店铺列表(我的团队)
     */
    public function lower_store_list(){
        $user =$this->user;
        if($user['is_distribut'] != 1) {
            $this->error('您还不是分销商');
        }
//        if(!$user['is_store_member']){
//            $user['is_store_member'] = db('store')->where(['user_id'=>$user['user_id']])->value('store_id');
//        }
//        $where['u.is_store_member'] = $user['is_store_member'];
        $where['s.invite_user_id'] = $user['user_id'];
        $count = (new \app\common\model\Store())->alias('s')
            ->where($where)
            ->join('__USERS__ u','u.user_id = s.user_id','left')
            ->count();
        $Page = new Page($count, 20);
        $show = $Page->show();
        $list = (new \app\common\model\Store())->alias('s')
            ->join('__USERS__ u','u.user_id = s.user_id','left')
            ->where($where)->order('s.user_id desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();
        if (I('is_ajax')) {
            return $this->fetch('ajax_lower_list');
        }
//halt($list[0]['store_logo']);
        $this->assign('lists', $list);
        $this->assign('show', $show);
        return $this->fetch();

//        $level = I('get.level',1);
//        $q = I('post.q','','trim');
//
//        $logic = new DistributLogic;
//        $result = $logic->lowerList($this->user_id, $level, $q);
//        $this->assign('count', $result['count']);// 总人数
//        $this->assign('page', $result['page']);// 赋值分页输出
//        $this->assign('lists',$result['lists']); // 下线
//        if (I('is_ajax')) {
//            return $this->fetch('ajax_lower_list');
//        }
//        return $this->fetch();
    }


    /**
     * 下线订单列表（分销订单）
     */
    public function order_list(){
        $user =$this->user;
        if($user['is_distribut'] != 1)
            $this->error('您还不是分销商');
        $store = db('user_store')->where(['user_id'=>$this->user['user_id']])->find();
        if (!$store) {
            $setS = new DistributLogic();
            $setS->setStore($this->user);
        }
        $status = I('get.status','');
        $where = array('user_id'=>$this->user_id);
        if($status != '') $where['status'] = ['in',$status];
        $count = M('rebate_log')->where($where)->count();
        $Page  = new Page($count,C('PAGESIZE'));
        $list = M('rebate_log')->where($where)->order("id desc")->limit($Page->firstRow.','.$Page->listRows)->select(); //分成订单记录
        $user_id_list = get_arr_column($list, 'buy_user_id');
        if(!empty($user_id_list))
            $userList = M('users')->where("user_id", "in", implode(',', $user_id_list))->getField('user_id,nickname,mobile,head_pic');  //购买者信息
        /*获取订单商品*/
        $model = new UsersLogic();
        foreach ($list as $k => $v) {
            $data = $model->get_order_goods($v['order_id']);
            $num = 0;
            foreach ($data['result'] as $vv){
                $num += $vv['goods_num'];
            }
            $list[$k]['num'] = $num;
            $list[$k]['goods_list'] = $data['result'];
        }
        $this->assign('count', $count);// 总人数
        $this->assign('page', $Page->show());// 赋值分页输出
        $this->assign('userList',$userList); //
        $this->assign('list',$list); // 下线
        if(I('is_ajax')){
            return $this->fetch('ajax_order_list');
        }
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
            $this->error("验证码错误");
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
            'useCurve' => true,
            'useNoise' => false,
        );
        $Verify = new Verify($config);
        $Verify->entry($type);
    }

    /**
     * 个人推广二维码 （我的名片）
     */
    public function qr_code()
    {
        $qr_mode = input('qr_mode', 0); //0：商家二维码，1：微信二维码
        $user_id = input('user_id', 0);
        if (!$user_id) {
            return $this->fetch();
        }
        
        $is_owner = false;//是否是本网页的用户
        if ($user_id == $this->user_id) {
            $user = $this->user;
            $is_owner = true;
        } else {
            $user = M('users')->where('user_id', $user_id)->find();
            if (!$user && $user['is_distribut'] != 1) {
                return $this->fetch();
            }
        }
        
        if ($qr_mode == 1 && $user['is_distribut'] != 1) {
            $this->error('楼主已不是分销商');
        }

        $wx_user = M('wx_user')->find();
        if ($qr_mode && $wx_user) {
            $wechatObj = new \app\common\logic\wechat\WechatUtil($wx_user);
            $wxdata = $wechatObj->createTempQrcode(2592000, $user['user_id']); //30天过期,推荐人
            if (empty($wxdata['url'])) {
                $this->error('微信未成功接入');
            }
        } 
        if ($qr_mode && $wx_user && !empty($wxdata['url'])) {
            $shareLink = urlencode($wxdata['url']);
        } else {
            $shareLink = urlencode(SITE_URL . "/index.php?m=Mobile&c=Index&a=index&first_leader={$user['user_id']}"); //默认分享链接
        }
        
        $head_pic = $user['head_pic'] ?: '';
        if ($head_pic && strpos($head_pic, 'http') !== 0) {
            $head_pic = '.'.$head_pic;
        }
        
        $config = tpCache('distribut');
        $back_img = $config['qr_back'] ? '.'.$config['qr_back'] : './template/mobile/new2/static/images/zz6.png'; 

        $this->assign('user',  $user);
        $this->assign('is_owner', $is_owner);
        $this->assign('qr_mode',  $qr_mode);
        $this->assign('head_pic', $head_pic);
        $this->assign('back_img', $back_img);
        $this->assign('ShareLink', $shareLink);
        return $this->fetch();
    }
    public function open_store_code()
    {

        $qr_mode = input('qr_mode', 0); //0：商家二维码，1：微信二维码
        $user_id = input('user_id', 0);
        if (!$user_id) {
            return $this->fetch();
        }

        $is_owner = false;//是否是本网页的用户
        if ($user_id == $this->user_id) {
            $user = $this->user;
            $is_owner = true;
        } else {
            $user = M('users')->where('user_id', $user_id)->find();
            if (!$user && $user['is_distribut'] != 1) {
                return $this->fetch();
            }
        }

        if ($qr_mode == 1 && $user['is_distribut'] != 1) {
            $this->error('楼主已不是分销商');
        }

        $wx_user = M('wx_user')->find();
        if ($qr_mode && $wx_user) {
            $wechatObj = new \app\common\logic\wechat\WechatUtil($wx_user);
            $wxdata = $wechatObj->createTempQrcode(2592000, $user['user_id']); //30天过期,推荐人
            if (empty($wxdata['url'])) {
                $this->error('微信未成功接入');
            }
        }
        if ($qr_mode && $wx_user && !empty($wxdata['url'])) {
            $shareLink = urlencode($wxdata['url']);
        } else {
            $shareLink = urlencode(SITE_URL . "/index.php?m=Mobile&c=Newjoin&a=guidance&first_leader={$user['user_id']}"); //默认分享链接
        }

        $head_pic = $user['head_pic'] ?: '';
        if ($head_pic && strpos($head_pic, 'http') !== 0) {
            $head_pic = '.'.$head_pic;
        }

        $config = tpCache('distribut');
        $back_img = $config['qr_back'] ? '.'.$config['qr_back'] : './template/mobile/new2/static/images/zz6.png';
        $this->assign('user',  $user);
        $this->assign('is_owner', $is_owner);
        $this->assign('qr_mode',  $qr_mode);
        $this->assign('head_pic', $head_pic);
        $this->assign('back_img', $back_img);
        $this->assign('ShareLink', $shareLink);
        return $this->fetch();
    }

    public function distribution_list(){
        if(request()->isAjax()){
            $logic = new DistributLogic;
            $goodsList = $logic->getStoreGoods($this->user_id);
            $this->assign('goodsList', $goodsList['list']);
            return $this->fetch('ajax_goods_list');
        }
        return $this->fetch();
    }

    public function delete(){
        if (!$this->user_id) {
            $this->ajaxReturn(['status' => 0, 'msg' => '请先登录']);
        }
        $goods_ids = I('post.goods_ids/a', []);
        if(count($goods_ids) > 0){
            $deleted = Db::name('user_distribution')->where(['user_id'=>$this->user_id,'goods_id'=>['in',$goods_ids]])->delete();
            if($deleted !== false){
                $this->ajaxReturn(['status' => 1, 'msg' => '删除成功']);
            }else{
                $this->ajaxReturn(['status' => 0, 'msg' => '删除失败']);
            }
        }else{
            $this->ajaxReturn(['status' => 0, 'msg' => '请选择要删除的商品']);
        }
    }

    /**
     * 平台分销商品列表
     */
    public function goods_list() 
    {
        if ($this->user['is_distribut'] != 1) {
            $this->error('您还不是分销商');
        }
        $store = db('user_store')->where(['user_id'=>$this->user['user_id']])->find();
        if (!$store) {
            $this->error('请先创建店铺');
        }
        $goodsLogic = new GoodsLogic();
        $brandList = $goodsLogic->getSortBrands();
        $categoryList =  Db::name("GoodsCategory")->where(['level'=>1])->getField('id,name,parent_id,level');
        $this->assign('categoryList', $categoryList);    //品牌
        $this->assign('brandList', $brandList);  //分类
        return $this->fetch();
    }
    
    /**
     * 平台分销商品列表
     */
    public function ajax_goods_list()
    {
        $sort = I('sort', 'goods_id'); // 排序
        $order = I('sort_asc', 'asc'); // 排序
        $cat_id = I('cat_id/d', 0);
        $brand_id = I('brand_id/d', 0);//品牌
        $key_word = trim(I('key_word/s', ''));
        $where = ['is_on_sale'=>1,'distribut'=>['gt',0]];
        if ($cat_id > 0) {
            $grandson_ids = getCatGrandson($cat_id);
            $where['cat_id1'] = ['in',$grandson_ids];
        }
        if ($key_word) {  //搜索
            $where['goods_name'] = ['like', '%'.$key_word.'%'];
        }
        if ($brand_id > 0) {
            $where['brand_id'] = $brand_id;
        }
        if (!in_array($sort, ['goods_id', 'is_new', 'sales_sum', 'distribut'])) {
            $sort = 'goods_id';
        }
        //查找用户已添加的商品ID
        $distribution_ids = Db::name('user_distribution')->where('user_id', $this->user_id)->column('goods_id');
        if ($distribution_ids) {
            $where['goods_id'] = ['not in', $distribution_ids];
        }
        $count = Db::name('goods')->where($where)->count();
        $page = new Page($count, 10);
        $goodsList = Db::name('goods')->field('goods_name,goods_id,distribut,shop_price,brand_id,store_id,cat_id3')
            ->where($where)->order($sort, $order)
            ->limit($page->firstRow, $page->listRows)
            ->select();
        $this->assign('goodsList', $goodsList);
        return $this->fetch();
    }

    /**
     * 添加分销商品
     * @author  lxl
     * @time2017-4-6
     */
    public function add_goods()
    {
        if (!$this->user_id) {
            $this->redirect('Mobile/User/index');
        }
        $goods_ids = I('post.goods_ids/a', []);
        
        $distributLogic = new DistributLogic;
        $result = $distributLogic->addGoods($this->user, $goods_ids);
        if($result){
            $this->success('成功',U('Mobile/Distribut/goods_list'));
        }else{
            $this->error('失败');
        }
    }

    /**
     * 店铺设置
     * @author  lxl
     * @time2017-4-6
     */
    public function set_store()
    {
        if (IS_POST) {
            $storeName = I('store_name', '');
            $trueName = I('true_name', '');
            $mobile = I('mobile', '');
            $qq = I('qq/d');
            $logic = new DistributLogic;
            $result = $logic->setStoreInfo($this->user_id, $storeName, $trueName, $mobile, $qq);
            if ($result['status'] != 1) {
                $this->ajaxReturn(['status'=>-1,'msg'=>$result['msg']]);
            }
            $this->ajaxReturn(['status'=>1,'msg'=>$result['msg']]);
        }
        if ($this->user['is_distribut'] != 1) {
            $this->error('您还不是分销商');
        }
        return $this->fetch();
    }


    /**
     * 新版查看网店
     */
    public function my_store(){
        $user =$this->user;
        if($user['is_distribut'] != 1){
            $this->error('您还不是分销商');
        }
        $store = db('user_store')->where(['user_id'=>$this->user['user_id']])->find();
        if (!$store) {
            $setS = new DistributLogic();
            $setS->setStore($user);
        }
        $first_leader = I('first_leader/d');
        if($first_leader > 0){ //如果是上级店铺的链接则显示上级的微店
            $firstLeader = M("Users")->where('user_id' , $first_leader)->field('nickname , mobile , head_pic')->find();
            $user_id = $first_leader;
            $first_leader_nickname = empty($firstLeader['nickname']) ? $firstLeader['mobile'] : $firstLeader['nickname'];
            $head_pic = $firstLeader['head_pic'];
            $store_name = $first_leader_nickname.'的微店';
        }else{
            $user_id = $this->user_id;
            $head_pic = $this->user['head_pic'];
            $store_name = $store['store_name']?:"我的店铺";
        }
        $distributLogic = new DistributLogic;
        $lists = $distributLogic->getStoreGoods($user_id);
        $sales = $distributLogic->getStoreSales();
        $this->assign('lists', $lists['list']);
        if(I('is_ajax')){
            return $this->fetch('ajax_my_store');
        }

        $this->assign('user_id', $user_id);
        $this->assign('head_pic', $head_pic);
        $this->assign('store_name', $store_name);
        $this->assign('promotion', $sales['prom_num']);
        $this->assign('new', $sales['new_num']);
        $this->assign('totalRows', $lists['totalRows']);
        return $this->fetch();
    }


    /**
     * 分销佣金数据处理，分销佣金明细实时读取
     *
     */
    public function distribut_detail(){
        $user = DB::name('users')->where(['user_id'=>$this->user['user_id']])->find();
        $user['unsettlement'] = DB::name('rebate_log')->where(['user_id'=>$this->user['user_id'],'status'=>['in',[0]]])->sum('money'); //未结算
        $user['pending_receipt'] = DB::name('rebate_log')->where(['user_id'=>$this->user['user_id'],'status'=>['in',[1,2]]])->sum('money'); //待收货佣金 = 用户已收货，但未达到售后时间过期的佣金

        $user['invalid'] = DB::name('rebate_log')->where(['user_id'=>$this->user['user_id'],'status'=>4])->sum('money'); //无效佣金
        $user['apply'] = DB::name('withdrawals')->where(['user_id'=>$this->user['user_id'],'status'=>0,'type'=>1])->sum('money'); //提现申请中
        $user['wait'] = DB::name('withdrawals')->where(['user_id'=>$this->user['user_id'],'status'=>1,'type'=>1])->sum('money'); //提现审核通过待打款
        $withdraw = $user['distribut_money'] - $user['distribut_withdrawals_money']; //可提现
        $withdrawing = DB::name('withdrawals')->where(['user_id'=>$this->user_id,'type'=>1,'status'=>['in',[0,1]] ])->sum('money');    //正在提现
        $user['withdraw'] = $withdraw - $withdrawing;  //最终可提现

        $this->assign('distribut', tpCache('distribut'));//提现配置项
        $this->assign('shopping', tpCache('shopping'));//提现配置项
        $this->assign('user',$user);
        return $this->fetch();
    }

    /**
     * 申请记录列表
     * @param $type 提现类型 ： 0 = 余额提现 ， 1 = 佣金提现
     */
    public function withdrawals_log(){
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
     * 申请提现记录
     */
    public function withdrawals()
    {
        C('TOKEN_ON', true);
        $cash_open=tpCache('cash.cash_open');
        if($cash_open!=1){
            $this->error('提现功能已关闭,请联系商家');
        }
        $user = DB::name('users')->where(['user_id'=>$this->user_id ])->find();
        $withdraw = $user['distribut_money'] - $user['distribut_withdrawals_money']; //可提现
        $withdrawing = DB::name('withdrawals')->where(['user_id'=>$this->user_id,'type'=>1,'status'=>['in',[0,1]] ])->sum('money');    //正在提现
        $withdraw = $withdraw - $withdrawing;  //最终可提现
        if (IS_POST) {
            $cash_open=tpCache('cash.cash_open');
            if($cash_open!=1){
                $this->ajaxReturn(['status'=>0, 'msg'=>'提现功能已关闭,请联系商家']);
            }

            $data = I('post.');
            $data['user_id'] = $this->user_id;
            $data['create_time'] = time();
            $data['type'] = 1; //提现类型为佣金提现
            $cash = tpCache('cash');
            $distribut = tpCache('distribut');

            if(encrypt($data['paypwd']) != $this->user['paypwd']){
                $this->ajaxReturn(['status'=>0, 'msg'=>'支付密码错误']);
            }
            if ($data['money'] > $withdraw ) {
                $this->ajaxReturn(['status'=>0, 'msg'=>"本次提现余额不足"]);
            }
            if ($data['money'] <= 0) {
                $this->ajaxReturn(['status'=>0, 'msg'=>'提现额度必须大于0']);
            }

            // if ($data['money'] > $this->user['user_money']) {
            //     $this->ajaxReturn(['status'=>0, 'msg'=>"您有提现申请待处理，本次提现余额不足"]);
            // }

            if ($cash['cash_open'] == 1) {
                $taxfee =  round($data['money'] * $cash['service_ratio'] / 100, 2);
                // 限手续费
                if ($cash['max_service_money'] > 0 && $taxfee > $cash['max_service_money']) {
                    $taxfee = $cash['max_service_money'];
                }
                if ($cash['min_service_money'] > 0 && $taxfee < $cash['min_service_money']) {
                    $taxfee = $cash['min_service_money'];
                }
                if ($taxfee >= $data['money']) {
                    $this->ajaxReturn(['status'=>0, 'msg'=>'提现额度必须大于手续费！']);
                }
                $data['taxfee'] = $taxfee;

                // 每次限最多提现额度
                if ($distribut['distribut_withdrawals_money'] > 0 && $data['money'] < $distribut['distribut_withdrawals_money']) {
                    $this->ajaxReturn(['status'=>0, 'msg'=>'每次最少提现额度' . $distribut['distribut_withdrawals_money']]);
                }
                if ($cash['max_cash'] > 0 && $data['money'] > $cash['max_cash']) {
                    $this->ajaxReturn(['status'=>0, 'msg'=>'每次最多提现额度' . $cash['max_cash']]);
                }

                $status = ['in','0,1,2,3'];
                $create_time = ['gt',strtotime(date("Y-m-d"))];
                // 今天限总额度
                if ($cash['count_cash'] > 0) {
                    //获取佣金提现类型
                    $total_money2 = Db::name('withdrawals')->where(array('user_id' => $this->user_id, 'type'=>1, 'status' => $status, 'create_time' => $create_time))->sum('money');
                    if (($total_money2 + $data['money'] > $cash['count_cash'])) {
                        $total_money = $cash['count_cash'] - $total_money2;
                        if ($total_money <= 0) {
                            $this->ajaxReturn(['status'=>0, 'msg'=>"你今天累计提现额为{$total_money2},金额已超过可提现金额."]);
                        } else {
                            $this->ajaxReturn(['status'=>0, 'msg'=>"你今天累计提现额为{$total_money2}，最多可提现{$total_money}账户余额."]);
                        }
                    }
                }
                // 今天限申请次数
                if ($cash['cash_times'] > 0) {
                    $total_times = Db::name('withdrawals')->where(array('user_id' => $this->user_id, 'type'=>1, 'status' => $status, 'create_time' => $create_time))->count();
                    if ($total_times >= $cash['cash_times']) {
                        $this->ajaxReturn(['status'=>0, 'msg'=>"今天申请提现的次数已用完."]);
                    }
                }
            }else{
                $data['taxfee'] = 0;
            }

            if (M('withdrawals')->add($data)) {
                $this->ajaxReturn(['status'=>1,'msg'=>"已提交申请",'url'=>U('distribut/distribut_detail',['type'=>2])]);
            } else {
                $this->ajaxReturn(['status'=>0,'msg'=>'提交失败,联系客服!']);
            }
        }
        $user_extend=Db::name('user_extend')->where('user_id='.$this->user_id)->find();

        //获取用户绑定openId 以mp为公众号的，open 开放平台的
        $oauthUsers = M("OauthUsers")->where(['user_id'=>$this->user_id, 'oauth'=>'weixin','oauth_child'=>'mp'])->find();
        $openid = $oauthUsers['openid'];

        $this->assign('cash_config', tpCache('cash'));//提现配置项
        $this->assign('user_extend',$user_extend);
        $this->assign('distribut', tpCache('distribut'));//提现配置项
        $this->assign('user_money', $withdraw );    //用户分销佣金余额
        $this->assign('openid',$openid);    //用户绑定的微信openid
        return $this->fetch();
    }


    /**
     * 新手必看
     * @author  lxl
     * @time2017-4-6
     */
    public function must_see(){
        $article = M('article')->where(['cat_id'=>13,'is_open'=>1])->select();
        $this->assign('article', $article);
        return $this->fetch();
    }

    /**
     *分销排行
     * @author  lxl
     * @time2017-4-6
     */
    public function rankings()
    {
        $sort = I('get.sort', 'distribut_money');
        $p= I('get.p/d', 1);

        $logic = new DistributLogic;
        $result = $logic->rankings($this->user, $sort, $p);
        
        $this->assign('lists', $result['lists']);
        $this->assign('sort', $sort);
        $this->assign('firstRow', $result['firstRow']);  //当前分页开始数
        $this->assign('place', $result['place']);  
        
        if(I('is_ajax')){
            return $this->fetch('ajax_rankings');
        }
        return $this->fetch();
    }
    
    /**
     * 分成记录
     * @author  lxl
     * @time2017-4-6
     */
    public function rebate_log()
    {
        if ($this->user['is_distribut'] != 1) {
            $this->error('您还不是分销商');
        }
        
        $status = I('status',''); //日志状态
        $order = I('sort_asc','desc');  //排序
        $sort  = I('sort','create_time'); //排序条件
        
        $logic = new DistributLogic;
        $result = $logic->getRebateLog($this->user_id, $status, $sort, $order);        
        
        $this->assign('lists',$result['list']);
        if(I('is_ajax')){
            return $this->fetch('ajax_rebate_log');
        }
        return $this->fetch();
    }
}