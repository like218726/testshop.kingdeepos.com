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
namespace app\api\controller;

use app\common\logic\GoodsLogic;
use app\common\logic\DistributLogic;
use think\Db;
use think\Page;
use app\common\model\Users;

class Distribut extends Base 
{
    /**
     * 分销用户中心首页（分销中心）
     */
    public function index(){
        $time=strtotime(date("Y-m-d"));
        $money['today_money'] = Db::name('rebate_log')->where("user_id=$this->user_id and status in(2,3) and create_time>$time")->sum('money');    //今日收入

        $UserModel  = new Users();
        $where['user_id'] = $this->user_id;
        $user =  $UserModel->where($where)
            ->with(['userLevel'])
            ->find();
        if($user) {
            $user = $user->append(['user_team_order','rebate_log','rebate_money'])->toArray();
        }
        $withdraw = $user['distribut_money'] - $user['distribut_withdrawals_money']; //可提现
        $user['withdrawing'] = DB::name('withdrawals')->where(['user_id'=>$this->user_id,'type'=>1,'status'=>['in',[0,1]] ])->sum('money');    //正在提现
        $user['withdraw'] = $withdraw - $user['withdrawing'];  //最终可提现
        $distribut_level = '未成为分销商';
        if(1 == $user['is_distribut']){
            $distribut_level = '默认分销商';
            if($user['distribut_level'] > 0){
                $distribut_level = Db::name('distribut_level')->where('level_id', $user['distribut_level'])->value('level_name');
                if(!$distribut_level) $distribut_level = '默认分销商'; // 脏数据造成的
            }
        }

        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => [
            'money'         => $money,
            'user'          => $user,
            'distribut_level'=>$distribut_level
        ]]);
    }
    
    /**
     * 分销用户中心首页（分销中心）
     */
    public function index_bck()
    {
        // 销售额 和 我的奖励
        $result = DB::query("select sum(goods_price) as goods_price, sum(money) as money from __PREFIX__rebate_log where user_id = {$this->user_id}");
        $result = $result[0];
        $result['goods_price'] = $result['goods_price'] ?: 0;
        $result['money'] = $result['money'] ?: 0;

        $lower_count[] = Db::name('users')->where("first_leader", $this->user_id)->count();
        $lower_count[] = Db::name('users')->where("second_leader", $this->user_id)->count();
        $lower_count[] = Db::name('users')->where("third_leader", $this->user_id)->count();


        $result2 = DB::query("select status,count(1) as c , sum(goods_price) as goods_price from `__PREFIX__rebate_log` where user_id = :user_id group by status",['user_id'=>$this->user_id]);
        $level_order = convert_arr_key($result2, 'status');
        for ($i = 0; $i <= 5; $i++) {
            $level_order[$i]['c'] = $level_order[$i]['c'] ? $level_order[$i]['c'] : 0;
            $level_order[$i]['goods_price'] = $level_order[$i]['goods_price'] ? $level_order[$i]['goods_price'] : 0;
        }

        $money['withdrawals_money'] = Db::name('withdrawals')->where(['user_id'=>$this->user_id, 'status'=>1])->sum('money') ?: 0; // 已提现财富
        $money['achieve_money'] = Db::name('rebate_log')->where(['user_id'=>$this->user_id,'status'=>3])->sum('money') ?: 0;  //累计获得佣金
        $time=strtotime(date("Y-m-d"));
        $money['today_money'] = Db::name('rebate_log')->where("user_id=$this->user_id and status in(2,3) and create_time>$time")->sum('money') ?: 0;    //今日收入

        $store = Db::name('user_store')->field('store_time,store_name')->where("user_id", $this->user_id)->find();
         
        $user = [
            'nickname'      => $this->user['nickname'],
            'head_pic'      => empty($this->user['head_pic']) ? "" : $this->user['head_pic'],
            'user_money'    => $this->user['user_money'],
        ];
                
        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => [
            'level_order'   => $level_order,            // 下线订单
            'lower_count'   => $lower_count,            // 下线人数
            'sales_volume'  => $result['goods_price'],  // 销售额
            'reward'        => $result['money'],        // 奖励
            'money'         => $money,
            'store_time'    => $store['store_time'] ?: 0,
            'store_name'    => $store['store_name'] ?: '',
            'user'          => $user
        ]]);
    }

    /**
     * 下线列表(我的团队)
     */
    public function lower_list() {
        if ($this->user['is_distribut'] != 1) {
            $this->ajaxReturn(['status' => -1, 'msg' => '您还不是分销商']);
        }

        $level = I('get.level', 1);
        $q = I('post.q', '', 'trim');

        $logic = new DistributLogic;
        $result = $logic->lowerList($this->user_id, $level, $q);

        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $result]);
    }
    
    /**
     * 下线列表(我的团队)
     */
    public function lower_list_bck() {
        if ($this->user['is_distribut'] != 1) {
            $this->ajaxReturn(['status' => -1, 'msg' => '您还不是分销商']);
        }

        $level = I('get.level', 1);
        $q = I('post.q', '', 'trim');
        
        $logic = new DistributLogic;
        $result = $logic->lowerList($this->user_id, $level, $q);
        
        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $result['lists']]);
    }
    /**
     * 下线店铺列表(我的团队)
     */
    public function lower_store_list(){
        $user =$this->user;
        if($user['is_distribut'] != 1) {
            $this->ajaxReturn(['status' => -1, 'msg' => '您还不是分销商']);
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
        if ($list){
            $list = collection($list)->append(['store_member_count','StoreOrderSum'])->toArray();
        }
        if (I('is_ajax')) {
            return $this->fetch('ajax_lower_list');
        }

        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' =>$list]);

    }
    /**
     * 下线订单列表（分销订单）
     */
    public function order_list()
    {
        if ($this->user['is_distribut'] != 1) {
            $this->ajaxReturn(['status' => -1, 'msg' => '您还不是分销商']);
        }
        
        $status = I('get.status', 0);
        
        $logic = new DistributLogic;
        $result = $logic->orderList($this->user_id, $status);

        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $result['list']]);
    }

    /**
     * 个人推广二维码 （我的名片）
     */
    public function qr_code(){
        $ShareLink = urlencode(SITE_URL ."/index.php?m=Mobile&c=Index&a=index&first_leader={$this->user_id}"); //默认分享链接
        //是否小程序端
        if(I('oauth') == 'miniapp'){
            if($this->user['xcx_qrcode']){
                $ShareLink = SITE_URL . $this->user['xcx_qrcode'];
            }else{
                $qrcode = new \app\common\logic\UsersLogic();
                $ShareLink = SITE_URL.$qrcode->checkUserQrcode($this->user_id);
            }
            $this->assign('oauth',I('oauth'));
        }
        if($this->user['is_distribut'] == 1) {
            $this->assign('ShareLink',$ShareLink);
        }
        $this->assign('user',$this->user);
        return $this->fetch();
    }

    public function open_store_code()
    {

        $qr_mode = input('qr_mode', 0); //0：商家二维码，1：微信二维码
        $user_id = $this->user_id;
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


    /**
     * 平台分销商品列表
     */
    public function goods_list()
    {
        if ($this->user['is_distribut'] != 1) {
            $this->ajaxReturn(['status' => -1, 'msg' => '您还不是分销商']);
        }
        $store = db('user_store')->where(['user_id'=>$this->user['user_id']])->count();
        if (!$store) {
            $setS = new DistributLogic();
            $setS->setStore($this->user);
        }

        $sort = I('sort', 'goods_id'); // 排序
        $order = I('order', 0); // 排序
        $cat_id = I('cat_id/d', 0);
        $brand_id = I('brand_id/d', 0);//品牌
        $key_word = trim(I('key_word/s', ''));

        $logic = new DistributLogic;
        $result = $logic->goodsList($this->user_id, $sort, $order, $cat_id, $brand_id, $key_word);

        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $result['goodsList']]);
    }

    public function goods_types()
    {
        $GoodsLogic = new GoodsLogic();
        $brandList = $GoodsLogic->getSortBrands();
//        $categoryList = $GoodsLogic->getSortCategory();
        $categoryList = Db::name("goods_category")->field('id,name,parent_id,level')->where(['level' => 1])->order('sort_order desc')->select();
        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => [
            'categoryList' => $categoryList,//分类
            'brandList' => $brandList,//品牌
        ]]);        
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
        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => [
            'distribut' => tpCache('distribut'),
            'shopping' => tpCache('shopping'),
            'user' => $user
        ] ]);
    }

    /**
     * 申请提现记录
     */
    public function withdrawals()
    {
        C('TOKEN_ON', true);
        $cash_open=tpCache('cash.cash_open');
        if($cash_open!=1){
            $this->ajaxReturn(['status'=>0, 'msg'=>'提现功能已关闭,请联系商家']);
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

            if($data['paypwd'] != $this->user['paypwd']){
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

        $this->ajaxReturn(['status'=>1,'msg'=>'获取成功','result'=>[
            'distribut_config'=>tpCache('distribut'),
            'distribut_money' => $withdraw
        ]]);

    }
    
    /**
     * 添加分销商品
     */
    public function add_goods()
    {
        if (!$this->user_id) {
            $this->redirect('Mobile/User/index');
        }
        $goods_ids = I('post.goods_ids/a', []);
        $terminal = I('terminal/s','');
        
        //M: 小程序传递过来的参数变成了二维数组, 需要重新处理,否则添加多个分销商品时只能添加一个
        if($terminal == 'miniapp'){
            $goods_ids = $goods_ids[0];
        }
        
        $distributLogic = new DistributLogic;
        $result = $distributLogic->addGoods($this->user, $goods_ids);
        if (!$result) {
            $this->ajaxReturn(['status' => -1, 'msg' => '添加失败']);
        }
        
        $this->ajaxReturn(['status' => 1, 'msg' => '添加成功']);
    }

    public function get_store(){
        $store_name = Db::name('user_store')->where(['user_id'=>$this->user_id])->find();
        $store_name['user_distribution'] = Db::name('user_distribution')->where('user_id', $this->user_id)->count();
        $store_name['user_distribution_no'] = (new DistributLogic())->getUserNotAddGoodsNum($this->user_id);
        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $store_name ]);
    }

    /**
     * 店铺设置
     */
    public function store()
    {
        if ($this->user['is_distribut'] != 1) {
            $this->ajaxReturn(['status' => -1, 'msg' => '您还不是分销商']);
        }
        
        if (request()->isGet()) {
            $logic = new DistributLogic;
            $return = $logic->getStoreInfo($this->user_id);
            $this->ajaxReturn($return);
        }
        
        if (request()->isPost()) {
            $storeName = I('store_name', '');
            $trueName = I('true_name', '');
            $mobile = I('mobile', '');
            $qq = I('qq', '');
            $logic = new DistributLogic;
            $result = $logic->setStoreInfo($this->user_id, $storeName, $trueName, $mobile, $qq);

            $this->ajaxReturn($result);
        }
       
        $this->ajaxReturn(['status' => -1, 'msg' => '请求方式不对']);
    }

    /**
     * 用户分销商品
     */
    public function my_store()
    {
        if ($this->user['is_distribut'] != 1) {
            $this->ajaxReturn(['status' => -1, 'msg' => '您还不是分销商']);
        }
        
        $logic = new DistributLogic;
        $goods = $logic->getStoreGoods($this->user_id);
        
        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $goods['list']]);
    }

    /**
     * 我的分销商品列表
     * @throws \think\Exception
     */
    public function distribution_list(){
        $logic = new DistributLogic;
        $goodsList = $logic->getStoreGoods($this->user_id);
        return $this->ajaxReturn(['status'=>1,'msg'=>'获取成功','result'=>$goodsList['list']]);
    }

    /**
     * 删除分销商品
     * @throws \think\Exception
     */
    public function delete(){
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
     * 获取商店的概况信息
     */
    public function store_summery()
    {
        if ($this->user['is_distribut'] != 1) {
            $this->ajaxReturn(['status' => -1, 'msg' => '您还不是分销商']);
        }
        
        $logic = new DistributLogic;
        $wait_add_num = $logic->getUserNotAddGoodsNum($this->user_id);
        $had_add_num = M('user_distribution')->where(['user_id'=>$this->user_id])->count();
        
        $store = M('user_store')->field('store_img,store_name')->where('user_id', $this->user_id)->find();
        $head_pic = M('users')->where('user_id', $this->user_id)->limit(1)->getField('head_pic');
        
        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => [
            'wait_add_num' => $wait_add_num, //未上架商品数
            'had_add_num'  => $had_add_num,  //已上架商品数
            'store'     => $store,
            'head_pic'  => $head_pic
        ]]);        
    }


    /**
     * 新手必看
     */
    public function must_see(){
        $article = M('article')->where(['cat_id'=>13,'is_open'=>1])->select();
        $this->assign('article', $article);
        return $this->fetch();
    }

    /**
     *分销排行
     */
    public function rankings()
    {
        $sort = I('get.sort', 'distribut_money');
        $p= I('get.p/d', 1);

        $logic = new DistributLogic;
        $result = $logic->rankings($this->user, $sort, $p);
        $new_user = (new Users())->where(['user_id'=>$this->user_id])->find();
        $this->user['underling_number'] = $new_user['underling_number'];
        $this->assign('lists', $result['lists']);
        $this->assign('sort', $sort);
        //        if('miniapp'==input('oauth')){
        //针对小程序分享排行跳转
        $this->assign('oauth', input('oauth'));
        $this->assign('token', input('token'));
        $this->assign('unique_id', input('unique_id'));
        $this->assign('is_json', input('is_json'));
//        }
        $this->assign('user', $this->user);
        $this->assign('firstRow', $result['firstRow']);  //当前分页开始数
        $this->assign('place', $result['place']);  
        
        if(I('is_ajax')){
            return $this->fetch('ajax_rankings');
        }
        return $this->fetch();
    }

    /**
     * 分成记录页面
     */
    public function rebate_log()
    {
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

    /**
     * 设置店铺上传图片
     */
    public function upload_store_img()
    {
        $logic = new DistributLogic;
        $return = $logic->uploadStoreImg();
        $this->ajaxReturn($return);
    }
}
