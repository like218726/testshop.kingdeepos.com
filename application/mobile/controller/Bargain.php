<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * $Author: IT宇宙人 2015-08-10 $
 */
namespace app\mobile\controller;



use app\common\logic\bargain\BargainLogic;
use app\common\model\BargainFirst;
use app\common\model\PromotionBargain;

class Bargain extends MobileBase
{
    public $user_id = 0;
    public $user = array();

    /**
     * 构造函数
     */
    public function  __construct()
    {
        parent::__construct();
		if(session('?user'))
        {
        	$user = session('user');
            $user = M('users')->where("user_id", $user['user_id'])->find();
            session('user',$user);  //覆盖session 中的 user               
        	$this->user = $user;
        	$this->user_id = $user['user_id'];
        	$this->assign('user',$user); //存储用户信息
        	$this->assign('user_id',$this->user_id);
        }else{
        	header("location:".U('User/login'));
        	exit;
        }
    }

    /**
     *  砍价页面
     * @return mixed
     */
    public function index()
    {
        $id = input('id');
        $BargainLogic = new BargainLogic();
        $BargainLogic->setBargainFirstId($id);
        $BargainLogic->setUserId($this->user_id);
        $data = $BargainLogic->showBargain();
        if($data['status'] == 0){
            $this->error('活动已结束');
        }
        $this->assign('data', $data['result']); //信息
        return $this->fetch();
    }


    /**
     * 活动列表
     * @throws \think\exception\DbException
     */
    function bargain_list()
    {
        if (IS_POST) {
            $data = input('');
            $data['type'] = input('type/d',0);
            $BargainLogic = new BargainLogic();
            $BargainLogic->setUserId($this->user_id);
            $startBargain = $BargainLogic->bargainList($data);
            $this->ajaxReturn($startBargain);
        }
        return $this->fetch();
    }

    /**
     * 我的砍价列表
     * @throws \think\exception\DbException
     */
    function order_list()
    {
        if (IS_POST) {
            $data = input('');
            $data['type'] = input('type/d',0);
            $BargainLogic = new BargainLogic();
            $BargainLogic->setUserId($this->user_id);
            $startBargain = $BargainLogic->orderList($data);
            $this->ajaxReturn($startBargain);
        }
        return $this->fetch();
    }


    /**
     * 发起创建砍价
     * @throws \think\exception\DbException
     */
    public function start_bargain()
    {
        $bargain_id = input('bargain_id/d',0);
        $goods_num = input('goods_num/d',1);
        $item_id = input('item_id/d',0);
        if(!$bargain_id ){
            $this->ajaxReturn(['status'=>0,'msg'=>'缺少参数','result'=>[]]);
        }
        $BargainLogic = new BargainLogic();
        $BargainLogic->setBargainId($bargain_id);
        $BargainLogic->setItemId($item_id);
        $BargainLogic->setUserId($this->user_id);
        $startBargain = $BargainLogic->startBargain($goods_num);
        $this->ajaxReturn($startBargain);
    }

    /**
     * 帮助砍价页面
     * @throws \think\exception\DbException
     */
    public function show_bargain()
    {
        $bargain_first_id = input('bargain_first_id/d',0);
        if(!$bargain_first_id){
            $this->ajaxReturn(['status'=>0,'msg'=>'缺少参数','result'=>[]]);
        }
        $BargainLogic = new BargainLogic();
        $BargainLogic->setBargainFirstId($bargain_first_id);
        $BargainLogic->setUserId($this->user_id);
        $getCutActor = $BargainLogic->showBargain();
        $this->ajaxReturn($getCutActor);

    }

    /**
     * 砍价接口
     * @throws \think\exception\DbException
     */
    public function bargain_cut()
    {
        if (!$this->user_id) {
            $this->ajaxReturn(['status' => -1, 'msg' => '请先登录']);
        }
        $bargain_first_id = input('bargain_first_id');
        if(!$bargain_first_id){
            $this->ajaxReturn(['status'=>0,'msg'=>'缺少参数','result'=>[]]);
        }
        $BargainLogic = new BargainLogic();
        $BargainLogic->setBargainFirstId($bargain_first_id);
        $getCutActor = $BargainLogic->cut($this->user);
        $this->ajaxReturn($getCutActor);
    }

}