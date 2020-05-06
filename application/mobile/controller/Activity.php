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
 * $Author: 当燃   2016-05-10
 */ 
namespace app\mobile\controller;

use app\common\model\Coupon;
use app\common\model\FlashSale;
use app\common\model\GroupBuy;
use app\common\model\PreSell;
use think\AjaxPage;
use think\Db;
use think\Page;
use app\admin\logic\NewsLogic;
use app\common\logic\NewsLogic as CommonNewsLogic;

class Activity extends MobileBase {
    public function index(){      
        return $this->fetch();
    }
    
    /**
     * 团购活动列表
     */
    public function group_list()
    {
        $type = input('type', '');
        $is_ajax = input('is_ajax',0);
        if ($type == 'new') {
            $order = 'gb.start_time';
        } elseif ($type == 'comment') {
            $order = 'g.comment_count';
        } else {
            $order = 'gb.id';
        }
        $group_by_where = array(
            'gb.start_time'=>array('lt',time()),
            'gb.end_time'=>array('gt',time()),
            'gb.recommend' =>1,
            'gb.status' =>1,
            'g.is_on_sale'=>1
        );
        $GroupBuy = new GroupBuy();
        $count =  $GroupBuy->alias('gb')->join('__GOODS__ g', 'g.goods_id = gb.goods_id')->where($group_by_where)->count('gb.goods_id');// 查询满足要求的总记录数
        $page = new Page($count, 20);
        $list = $GroupBuy
            ->alias('gb')
            ->join('__GOODS__ g', 'gb.goods_id=g.goods_id AND g.prom_type=2')
            ->where($group_by_where)
            ->limit($page->firstRow, $page->listRows)
            ->order($order, 'desc')
            ->select();
        $this->assign('list', $list);
        if($is_ajax){
            return $this->fetch('ajax_group_list');
        }
        return $this->fetch();
    }
    
    public function ajaxGroupListGetMore(){
        $p = I('p',1);
        $list = M('GroupBuy')->where(time()." >= start_time and ".time()." <= end_time ")->page($p,10)->select(); // 找出这个商品
        $this->assign('list', $list);
        return $this->fetch();
    }
    
    
    public function discount_list(){
        $prom_id = I('id/d');    //活动ID
        $where = array(     //条件
            'is_on_sale'=>1,
            'prom_type'=>3,
            'prom_id'=>$prom_id,
        );
        $count =  M('goods')->where($where)->count(); // 查询满足要求的总记录数
        $Page = new Page($count, 20); //分页类
        $prom_list = Db::name('goods')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select(); //活动对应的商品
        $spec_goods_price = Db::name('specGoodsPrice')->where(['prom_type'=>3,'prom_id'=>$prom_id])->select(); //规格
        foreach($prom_list as $gk =>$goods){  //将商品，规格组合
            foreach($spec_goods_price as $spk =>$sgp){
                if($goods['goods_id']==$sgp['goods_id']){
                    $prom_list[$gk]['spec_goods_price']=$sgp;
                }
            }
        }
        foreach($prom_list as $gk =>$goods){  //计算优惠价格
            $PromGoodsLogicuse = new \app\common\logic\PromGoodsLogic($goods,$goods['spec_goods_price']);
            if(!empty($goods['spec_goods_price'])){
                $prom_list[$gk]['prom_price']=$PromGoodsLogicuse->getPromotionPrice($goods['spec_goods_price']['price']);
            }else{
                $prom_list[$gk]['prom_price']=$PromGoodsLogicuse->getPromotionPrice($goods['shop_price']);
            }

        }
        $this->assign('prom_list', $prom_list);
        if(I('is_ajax')){
            return $this->fetch('ajax_discount_list');
        }
        return $this->fetch();
    }
    
    public function discount_goods_list(){
    	$prom_list = M('prom_goods')->where("end_time>".time())->select();
    	$this->assign('prom_list', $prom_list);
    	return $this->fetch();
    }

    /**
     * 商品活动页面
     * $author lxl
     * $time 2017-1
     */
    public function promote_goods(){
        $now_time = time();
        $where = " start_time <= $now_time and end_time >= $now_time and status=1 and recommend=1 and is_end = 0";
        $count = M('prom_goods')->where($where)->count();  // 查询满足要求的总记录数
        $pagesize = 20;  //每页显示数
        $Page  = new Page($count,$pagesize); //分页类
        $promote = M('prom_goods')->field('id,title,start_time,end_time,prom_img')
            ->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();    //查询活动列表
        $this->assign('promote',$promote);
        if(I('is_ajax')){
            return $this->fetch('ajax_promote_goods');
        }
        return $this->fetch();
    }
    /**
     * 抢购活动列表页
     */
    public function flash_sale_list()
    {
        $time_space = flash_sale_time_space();
        $this->assign('time_space', $time_space);
        return $this->fetch();
    }

    /**
     * 抢购活动列表ajax
     */
    public function ajax_flash_sale()
    {
        $p = input('p',1);
        $start_time = input('start_time');
        $end_time = input('end_time');
        $where = array(
            'fl.status' => 1,
            'fl.start_time'=>array('egt',$start_time),
            'fl.end_time'=>array('elt',$end_time),
            'g.is_on_sale'=>1
        );
        $FlashSale = new FlashSale();
        $flash_sale_goods = $FlashSale->alias('fl')->join('__GOODS__ g', 'g.goods_id = fl.goods_id')->with(['specGoodsPrice', 'goods'])->field('*,100*(FORMAT(buy_num/goods_num,2)) as percent')->where($where)->page($p, 10)->select();
        $this->assign('flash_sale_goods',$flash_sale_goods);
        return $this->fetch();
    }


    public function coupon_list()
    {
        $type = input('type', 1);
        $p = input('p', '');
        $user = session('user');
        $time = time();
        $couponWhere = ['type' => 2,'status'=>1,'send_start_time'=>['elt', $time],'send_end_time'=>['egt', $time]];
        $orderBy = array('id' => 'desc');
        if ($type == 2) {
            //即将过期
            $orderBy = ['spacing_time' => 'asc'];
            $where["send_end_time-'$time'"] = ['egt', 0];
        } elseif ($type == 3) {
            //面值最大
            $orderBy = ['money' => 'desc'];
        }
        $Coupon = new Coupon();
        $coupon_list = $Coupon->alias('c')->field("*,send_end_time-'$time' as spacing_time")->field('gc.goods_id,gc.goods_category_id')->join('__GOODS_COUPON__ gc', 'gc.coupon_id=c.id ','left')->where($couponWhere)->page($p, 15)->group('id')->order($orderBy)->select();
        if ($coupon_list) {
            $coupon_list = collection($coupon_list)->append(['coupon_img','use_type_title'])->toArray();
            $store_id_arr = get_arr_column($coupon_list, 'store_id');
            $store_list = Db::name('store')->where("store_id", "in", $store_id_arr)->getField('store_id,store_name,store_logo');
            $this->assign('store_list', $store_list);
            if ($user['user_id']) {
                $user_coupon_cid = Db::name('coupon_list')->where(['uid' => $user['user_id'], 'type' => 2])->column('cid');
                $this->assign('user_coupon_cid', $user_coupon_cid);
            }
        }
        $this->assign('coupon_list', $coupon_list);
        if (request()->isAjax()) {
            return $this->fetch('ajax_coupon_list');
        }
        return $this->fetch();
    }
    /**
     * 领券
     */
    public function getCoupon()
    {
        $id = I('coupon_id/d');
        $user = session('user');
        $user['user_id'] = $user['user_id'] ?: 0;
        $activityLogic = new \app\common\logic\CouponLogic();
        $return = $activityLogic->get_coupon($id, $user['user_id']);
        $this->ajaxReturn($return);
    }


    public function pre_sell_list()
    {
        $p = input('p', 1);
        $PreSell = new PreSell();
        //$pre_sell_list = $PreSell->where(['sell_end_time'=>['gt',time()],'is_finished' => 0, 'status' => 1])->order(['pre_sell_id' => 'desc'])->page($p, 10)->select();

        $type = input('type', 0);
        if($type == 1){
            $order['is_new'] = 'desc';
        }elseif($type == 2){
            $order['comment_count'] = 'desc';
        }else{
            $order = ['pre_sell_id' => 'desc'];
        }
        $pre_sell_list = Db::view('PreSell','pre_sell_id,goods_id,item_id,goods_name,deposit_goods_num,sell_end_time')
            ->view('Goods','is_new,sort,comment_count,collect_sum','Goods.goods_id=PreSell.goods_id')
            ->where(['sell_end_time'=>['gt',time()],'is_finished' => 0])
            ->page($p, 10)
            ->order($order)
            ->select();
        foreach($pre_sell_list as $k => $v){
            $pre_sell = $PreSell::get($v['pre_sell_id']);
            $pre_sell_list[$k]['ing_price'] = $pre_sell->ing_price;
        }
        $this->assign('pre_sell_list', $pre_sell_list);
        if (request()->isAjax()) {
            return $this->fetch('ajax_pre_sell_list');
        }
        return $this->fetch();
    }

    public function add_news()
    {
        $ArticleCat = new NewsLogic();
        $act = I('GET.act','add');
        $info = array();
        $info['publish_time'] = time()+3600*24;
        $tag=config('NEWS_TAG');
        $cats = $ArticleCat->article_cat_list(0,$info['cat_id']);
        $this->assign('cat_select',$cats);
        $this->assign('act',$act);
        $this->assign('info',$info);
        $this->assign('tags',$tag);
        return $this->fetch();
    }

    public function add_news_handle() 
    {
        
        $data = I('post.');

        $CommonNewsLogic = new CommonNewsLogic();
        $return = $CommonNewsLogic->upload_img();
        if ($return['status'] !== 1) {
            $this->error($return['msg']);
        }

        $data['thumb'] = $return['result'][0];
        $data['thumb2'] = $return['result'][1];
        $data['thumb3'] = $return['result'][2];

        $re= $CommonNewsLogic->addNews($data);
        if ($re['status'] == -1) {
            $this->error($re['msg']);
        } else {
             $this->success($re['msg'], U('Activity/news_list'));
        }
       
    }

    public function news_list()
    {
        $user_id = session('user.user_id') ;
        $CommonNewsLogic = new CommonNewsLogic();
        $news_list = $CommonNewsLogic->userNews();
        $this->assign('news_list',$news_list);

        return $this->fetch();
    }

    /**
    *用户删除新闻
    *@param $news_ids 新闻id数组
    *@return array
    */
    public function del_news() 
    {

        $news_ids = I('news_ids');
        $news_ids_arr = explode(",", $news_ids);
        $user_id = session('user.user_id') ;
        $result = M('news')->where('user_id', $user_id)->delete($news_ids_arr);

        if (!$result) {
            $this->error("删除失败");
        } else {
            $this->success("删除成功");
        }
    }
}