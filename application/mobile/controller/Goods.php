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

use app\common\logic\ActivityLogic;
use app\common\logic\Block;
use app\common\logic\GoodsLogic;
use app\common\logic\ReplyLogic;
use app\common\logic\GoodsPromFactory;
use app\common\logic\User;
use app\common\model\SpecGoodsPrice;
use app\common\model\PromOrder;
use think\AjaxPage;
use think\Page;
use think\Db;

class Goods extends MobileBase {
    public function index(){       
        return $this->fetch();
    }

    /**
     * 分类列表显示
     */
    public function categoryList(){
        return $this->fetch();
    }

    /**
     * 商品列表页
     */
    public function goodsList(){
        
        $filter_param = array(); // 帅选数组
        $id = I('get.id/d',0); // 当前分类id
        $brand_id = I('brand_id',0);
        $attr = I('attr',''); // 属性
        $sort = I('sort','sort'); // 排序
        $sort_asc = I('sort_asc','desc'); // 排序
        $price = I('price',''); // 价钱
        $start_price = trim(I('start_price','0')); // 输入框价钱
        $end_price = trim(I('end_price','0')); // 输入框价钱
        $sel = trim(I('sel')); //筛选货到付款,仅看有货,促销商品
        if($start_price && $end_price) $price = $start_price.'-'.$end_price; // 如果输入框有价钱 则使用输入框的价钱       
        $filter_param['id'] = $id; //加入帅选条件中
        $brand_id  && ($filter_param['brand_id'] = $brand_id); //加入帅选条件中
        $attr  && ($filter_param['attr'] = $attr); //加入帅选条件中
        $price  && ($filter_param['price'] = $price); //加入帅选条件中
        $sel  && ($filter_param['sel'] = $sel); //加入帅选条件中
         
        $goodsLogic = new GoodsLogic(); // 前台商品操作逻辑类
        // 分类菜单显示
        $goodsCate = M('GoodsCategory')->where("id" , $id)->find();// 当前分类
        $cateArr = $goodsLogic->get_goods_cate($goodsCate);
         
        // 帅选 品牌 规格 属性 价格
        $cat_id_arr = getCatGrandson ($id);
        $goods_where = ['goods_state' => 1, 'is_on_sale' => 1];
        if ($goodsCate) {
            $goods_where['cat_id' . $goodsCate['level']] = $id;
        }
        $filter_goods_id = Db::name('goods')->where($goods_where)->cache(true)->getField("goods_id",true);

        // 过滤帅选的结果集里面找商品
        if($brand_id || $price)// 品牌或者价格
        {
            $goods_id_1 = $goodsLogic->getGoodsIdByBrandPrice($brand_id,$price); // 根据 品牌 或者 价格范围 查找所有商品id
            $filter_goods_id = array_intersect($filter_goods_id,$goods_id_1); // 获取多个帅选条件的结果 的交集
        }
        if($sel)
        {
            $goods_id_4 = $goodsLogic->getFilterSelected($sel,$cat_id_arr);
            $filter_goods_id = array_intersect($filter_goods_id,$goods_id_4);
        }
        if($attr)// 属性
        {
            $goods_id_3 = $goodsLogic->getGoodsIdByAttr($attr); // 根据 规格 查找当所有商品id
            $filter_goods_id = array_intersect($filter_goods_id,$goods_id_3); // 获取多个帅选条件的结果 的交集
        }
         
        $filter_menu  = $goodsLogic->get_filter_menu($filter_param,'goodsList'); // 获取显示的帅选菜单
        $filter_price = $goodsLogic->get_filter_price($filter_goods_id,$filter_param,'goodsList'); // 帅选的价格期间
        $filter_brand = $goodsLogic->get_filter_brand($filter_goods_id,$filter_param,'goodsList'); // 获取指定分类下的帅选品牌
        $filter_attr  = $goodsLogic->get_filter_attr($filter_goods_id,$filter_param,'goodsList',1); // 获取指定分类下的帅选属性
        
        $count = count($filter_goods_id);
        $page_count = 20;
        $page = new Page($count, $page_count);
    	if($count > 0)
    	{
    		$goods_list = M('goods')->where("goods_id in (".  implode(',', $filter_goods_id).")")->order([$sort=>$sort_asc])->limit($page->firstRow.','.$page->listRows)->select();
            foreach ($goods_list as $k=>$v){
                $goods_list[$k]['activity'] = (new Block())->check_activity($v);
            }
    		$filter_goods_id2 = get_arr_column($goods_list, 'goods_id');
    		if($filter_goods_id2)
    			$goods_images = M('goods_images')->where("goods_id in (".  implode(',', $filter_goods_id2).")")->cache(true)->select();
    	}
    	$goods_category = M('goods_category')->where('is_show=1')->cache(true)->getField('id,name,parent_id,level'); // 键值分类数组
    	$this->assign('goods_list',$goods_list);
    	$this->assign('goods_category',$goods_category);
    	$this->assign('goods_images',$goods_images);  // 相册图片
    	$this->assign('filter_menu',$filter_menu);  // 帅选菜单
    	$this->assign('filter_attr',$filter_attr);  // 帅选属性
    	$this->assign('filter_brand',$filter_brand);// 列表页帅选属性 - 商品品牌
    	$this->assign('filter_price',$filter_price);// 帅选的价格期间
    	$this->assign('goodsCate',$goodsCate);
    	$this->assign('cateArr',$cateArr);
    	$this->assign('filter_param',$filter_param); // 帅选条件
    	$this->assign('cat_id',$id);
    	$this->assign('page',$page);// 赋值分页输出
        $this->assign('page_count', $page_count);//一页显示多少条
        $this->assign('sort_asc', $sort_asc == 'asc' ? 'desc' : 'asc');
        C('TOKEN_ON',false);
        
        if(request()->isAjax())
            return $this->fetch('ajaxGoodsList');
        else
            return $this->fetch();
    }

    /**
     * 商品列表页 ajax 翻页请求 搜索
     */
    public function ajaxGoodsList() {
        $where ='';

        $cat_id  = I("id/d",0); // 所选择的商品分类id
        if($cat_id > 0)
        {
            $grandson_ids = getCatGrandson($cat_id);
            $where .= " WHERE cat_id in(".  implode(',', $grandson_ids).") "; // 初始化搜索条件
        }

        $result = Db::query("select count(1) as count from __PREFIX__goods $where ");
        $count = $result[0]['count'];
        $page = new AjaxPage($count,10);

        $order = " order by goods_id desc"; // 排序
        $limit = " limit ".$page->firstRow.','.$page->listRows;
        $list = Db::query("select *  from __PREFIX__goods $where $order $limit");

        $this->assign('lists',$list);
        $html = $this->fetch('ajaxGoodsList'); //return $this->fetch('ajax_goods_list');
       exit($html);
    }
    function goodsInfojs(){

        $arr = Db::name('goods')->field('goods_id,goods_name,shop_price,comment_count,sales_sum')->find(input('id'));
        if($arr) $arr['status'] = 1;
        else $arr['status'] = 0;
        $this->ajaxReturn($arr);
    }
    /**
     * 领取优惠券
     */
    public function couponList(){
        $p = input('p', 1);
        $cat_id = input('cat_id', 0);
        $goods_id = input('goods_id', 0);
        $user = session('user');

        $activityLogic = new ActivityLogic();
        $result = $activityLogic->getCouponCenterList($cat_id, $user['user_id'], $p,$goods_id);
        $return = array(
            'status' => 1,
            'msg' => '获取成功',
            'result' => $result ,
        );
        $this->ajaxReturn($return);
    }
      /**
         * 分类商品列表，搜索出的商品列表，点击购物车按钮弹出
         */
     public function goodsinfolist()
    {
        C('TOKEN_ON',true);
        $goodsLogic = new GoodsLogic();
        $goods_id = I("post.id/d");

        $goodsModel = new \app\common\model\Goods();
        $goods = $goodsModel::get($goods_id);

        if(empty($goods) || ($goods['is_on_sale'] == 0) || ($goods['is_virtual']==1 && $goods['virtual_indate'] <= time())){
            $this->error('此商品不存在或者已下架');
        }

        if($goods['brand_id']){
            $brnad = M('brand')->where("id", $goods['brand_id'])->find();
            $goods['brand_name'] = $brnad['name'];
        }

        $goods_images_list = M('GoodsImages')->where("goods_id", $goods_id)->select(); // 商品 图册
        $goods_attribute = M('GoodsAttribute')->getField('attr_id,attr_name'); // 查询属性
        $goods_attr_list = M('GoodsAttr')->where("goods_id", $goods_id)->select(); // 查询商品属性表
        $filter_spec = $goodsLogic->get_spec($goods_id);
        $spec_goods_price  = M('spec_goods_price')->where("goods_id", $goods_id)->getField("key,price,store_count,item_id"); // 规格 对应 价格 库存表
        $this->assign('spec_goods_price', json_encode($spec_goods_price,true)); // 规格 对应 价格 库存表
        $goods['sale_num'] = M('order_goods')->where(['goods_id'=>$goods_id,'is_send'=>1])->count();

        //当前用户收藏
        $user_id = cookie('user_id');
        $collect = M('goods_collect')->where(array("goods_id"=>$goods_id ,"user_id"=>$user_id))->count();
        $goods_collect_count = M('goods_collect')->where(array("goods_id"=>$goods_id))->count(); //商品收藏数

        $this->assign('collect',$collect);
        $this->assign('goods_attribute',$goods_attribute);//属性值
        $this->assign('goods_attr_list',$goods_attr_list);//属性列表
        $this->assign('filter_spec',$filter_spec);//规格参数
        $this->assign('goods_images_list',$goods_images_list);//商品缩略图
        $this->assign('goods',$goods->toArray());
        //积分规则修改后的逻辑

        $kf_config['im_choose'] = tpCache('basic.im_choose');
        $this->assign('kf_config',$kf_config);

        $point_rate = tpCache('integral.point_rate');
        //$point_rate = tpCache('shopping.point_rate');
        $this->assign('goods_collect_count',$goods_collect_count); //商品收藏人数
        $this->assign('point_rate', $point_rate);
        return $this->fetch('goodsinfotwo');
    }
    /**
     * 商品详情页
     */
    public function goodsInfo(){

        C('TOKEN_ON',true);
        $goodsLogic = new GoodsLogic();
        $goods_id = I("get.id/d" , 0);
        $store_id = input("get.store_id/d", 0);
        $Goods = new \app\common\model\Goods();
        $goods = $Goods::get($goods_id);
        if ($goods['is_virtual']==1 && $goods['virtual_indate'] <= time()) {
            //虚拟商品过期，就下架
            $goods->save(['is_on_sale'=>0]);
        }
        if(empty($goods) || $goods['is_on_sale'] !=1){
            $this->error('此商品不存在或者已下架');
        }
        if (cookie('user_id')) {
            $user = new User();
            $user->setUserById(cookie('user_id'));
            $user->visitGoodsLog($goods);
        }
        if (session('?user')) {
            session('login_goods_id',null);
        }else{
            session('login_goods_id',$goods_id);
        }
        $kf_config = tpCache('basic');
        $kf_config['im_choose'] = tpCache('basic.im_choose');
        $this->assign('kf_config',$kf_config);

        $goods->save(['click_count' => $goods['click_count'] + 1]); //点击数+1
        $goods_attribute = Db::name('goods_attribute')->getField('attr_id,attr_name'); // 查询属性
		if ($goods['prom_type'] == 8) {
			//砍价活动(有规格)查询每个活动规格的是否已经卖完,卖完改状态
			$prom_id = Db::name('spec_goods_price')->where(["goods_id" => $goods_id, 'prom_id' => ['gt',0]])->value("prom_id");
			$bargain_item_id_arr = Db::name('promotion_bargain_goods_item')->where(['bargain_id' => $prom_id, 'goods_id' => $goods_id, 'goods_num' => ['exp', '<=buy_num'], 'item_id ' => ['gt', 0]])->getField('item_id',true);
			if (count($bargain_item_id_arr) > 0) {
				$bargain_item_id_str = implode(',', $bargain_item_id_arr);
				Db::name('spec_goods_price')->where(['prom_type' => 8, 'prom_id' => $prom_id, 'item_id' => ['in', $bargain_item_id_str]])->save(['prom_type' => 0, 'prom_id' => 0]);
			}
		}
		$spec_goods_price = Db::name('spec_goods_price')->where("goods_id",$goods_id)->getField("key,price,store_count,item_id,prom_id"); // 规格 对应 价格 库存表
        $filter_spec = $goodsLogic->get_spec($goods_id);
        $user_id = cookie('user_id');
        $collect = Db::name('goods_collect')->where(array("goods_id"=>$goods_id ,"user_id"=>$user_id))->count();
        $this->assign('spec_goods_price', json_encode($spec_goods_price,true)); // 规格 对应 价格 库存表
        $this->assign('goods_attribute',$goods_attribute);//属性值
        $this->assign('filter_spec',$filter_spec);//规格参数
        //从店铺查看商品带有店铺id，检查改商品属于店铺还是平台
        if($store_id){
            if($goods['store_id'] != $store_id){
                //如果该商品不是店铺的，检查有没有绑定平台
                $store_bind_platform_goods = db('store_bind_platform_goods')->where(['store_id'=>$store_id,'goods_id'=>$goods['goods_id']])->count();
                if($store_bind_platform_goods){
                    $goods['store'] = db('store')->where(['store_id'=>$store_id])->find();
                }
            }
        }
		
		$time = time();
        $couponWhere = ['type' => 2,'status'=>1,'send_start_time'=>['elt', $time],'send_end_time'=>['egt', $time],'store_id'=>$goods['store_id']];
        $couponList = Db::name('coupon')
			->alias('c')
			->join('__GOODS_COUPON__ gc', 'gc.coupon_id=c.id ','left')
			->where($couponWhere)
			->where('createnum>send_num or createnum=0')
			->getField('c.id, c.*,gc.goods_id,gc.goods_category_id');
        if ($couponList) {
            // 只显示与该商品有关的优惠券
            $couponWhere['use_type'] = 0;
            $ids_arr1 = Db::name('coupon')
                ->alias('c')
                ->join('__GOODS_COUPON__ gc', 'gc.coupon_id=c.id ','left')
                ->where($couponWhere)
                ->where('createnum>send_num or createnum=0')
                ->column('id');
            $ids_arr = [];
            $ids_arr1 && $ids_arr = $ids_arr1;
            $ids_arr2 = Db::name('goods_coupon')->where('goods_id',$goods_id)->whereOr('goods_category_id',$goods['cat_id3'])->column('coupon_id');
            if($ids_arr2){
                $ids_arr = array_merge($ids_arr,$ids_arr2);
            }
            if($ids_arr){
                foreach($couponList as $key=>$v){
                    if(!in_array($key,$ids_arr)){
                        unset($couponList[$key]);
                    }
                }
            }else{
                $couponList = [];
            }
            // 以上是添加的，减去不合格的
            if (cookie('user_id')) {
                $userCouponCid = Db::name('coupon_list')->where(['uid' => cookie('user_id'), 'store_id' => $goods['store_id']])->column('cid');
				foreach ($userCouponCid as $val) {
					isset($couponList[$val]) && $couponList[$val]['is_own'] = 1;
				}
            }
        }
        if ($couponList) {
            $couponId = array_keys($couponList);
            $goodsCoupon = Db::name('goods_coupon')
                ->alias('gc')
                ->join('goods g', 'gc.goods_id=g.goods_id', 'left')
                ->join('goods_category gcat', 'gc.goods_category_id=gcat.id', 'left')
                ->where(['coupon_id' => ['in', $couponId]])
                ->field('gc.*, g.goods_name, gcat.mobile_name')
                ->select();
            $goodsCoupon1 = [];
            foreach ($goodsCoupon as $val) {
                if (isset($goodsCoupon1[$val['coupon_id']])) {
                    if (!empty($val['goods_name'])) {
                        $goodsCoupon1[$val['coupon_id']]['goods_name'] .= '；' . $val['goods_name'];
                    } else if (!empty($val['mobile_name'])) {
                        $goodsCoupon1[$val['coupon_id']]['mobile_name'] .= '；' . $val['mobile_name'];
                    }
                } else {
                    $goodsCoupon1[$val['coupon_id']] = $val;
                }
            }
        }else{
            $goodsCoupon1 = [];
        }

		$this->assign('coupon_list',$couponList);
		$this->assign('goods_coupon',$goodsCoupon1);
		
		$promGoods = Db::name('prom_goods')
			->where(['status'=>1,'start_time'=>['elt', $time],'end_time'=>['egt', $time],'store_id'=>$goods['store_id'],'id'=>$goods['prom_id']])
			->find();
		if ($promGoods && $spec_goods_price) {
			foreach ($spec_goods_price as $val) {
				if ($val['prom_id'] == $goods['prom_id']) {
					$promGoods['item_id'] = $val['item_id'];
					break;
				}
			}
		} else {
			$promGoods['item_id'] = 0;
		}
		$promOrder = Db::name('prom_order')
			->where(['status'=>1,'start_time'=>['elt', $time],'end_time'=>['egt', $time],'store_id'=>$goods['store_id']])
			->order('orderby')
			->select();
        $this->assign('prom_goods',$promGoods);
        $this->assign('prom_order',$promOrder);

        $PromOrder = new PromOrder();
        $prom_order_list = $PromOrder->where(['start_time' => ['<=', time()], 'end_time' => ['>', time()], 'status' => 1,'store_id'=>$goods['store_id']])->limit(3)->order('id desc')->select();
        $prom_order = [];
        if($prom_order_list){
            $prom_order = collection($prom_order_list)->append(['prom_detail'])->toArray();
        }

        $this->assign('prom_order',$prom_order);

        $good_num=Db::name('goods')->where(['store_id'=>$store_id,'is_on_sale'=>1])->count();
        $this->assign('goods',$goods);
        $prefix = config('database.prefix');
        $goods_attr = $Goods->goodsAttr()->join("{$prefix}goods_attribute","{$prefix}goods_attribute.attr_id={$prefix}goods_attr.attr_id",'left')
        ->where("{$prefix}goods_attribute.attr_index",1)->where("{$prefix}goods_attr.goods_id",$goods_id)->order("{$prefix}goods_attribute.order DESC")->select();
        $this->assign('goods_attr',$goods_attr);
        $this->assign('point_rate', tpCache('shopping.point_rate'));
        $this->assign('collect',$collect);
        $this->assign('good_num',$good_num);
        return $this->fetch();
    }

    public function activity(){
        $goods_id = input('goods_id/d');//商品id
        $item_id = input('item_id/d');//规格id
        $Goods = new \app\common\model\Goods();
        $goods = $Goods::get($goods_id);//不缓存，活动实时更新
        $goodsPromFactory = new GoodsPromFactory();
        if ($goodsPromFactory->checkPromType($goods['prom_type'])) {
            //这里会自动更新商品活动状态，所以商品需要重新查询
            if($item_id){
                $specGoodsPrice = SpecGoodsPrice::get($item_id);
                $goodsPromLogic = $goodsPromFactory->makeModule($goods,$specGoodsPrice);
            }else{
                $goodsPromLogic = $goodsPromFactory->makeModule($goods,null);
            }
            $goods['collect']=Db::name('goods_collect')->where(['goods_id'=>$goods_id,'user_id'=>cookie('user_id')])->count();
            //检查活动是否有效
            if($goodsPromLogic->checkActivityIsAble()){
                $goods = $goodsPromLogic->getActivityGoodsInfo();
                $goods['activity_is_on'] = 1;
                $this->ajaxReturn(['status'=>1,'msg'=>'该商品参与活动','result'=>['goods'=>$goods]]);
            }else{
                $goods['activity_is_on'] = 0;
                $this->ajaxReturn(['status'=>1,'msg'=>'该商品没有参与活动.','result'=>['goods'=>$goods]]);
            }
        }
        $this->ajaxReturn(['status'=>1,'msg'=>'该商品没有参与活动','result'=>['goods'=>$goods]]);
    }

    /*
     * 商品评论
     */
    public function comment(){
        $goods_id = I("goods_id/d",'0');
        $this->assign('goods_id',$goods_id);
        return $this->fetch();
    }

    /**
     * 商品评论ajax分页
     */
    public function ajaxComment()
    {
        $num=I("num/d");
        $goods_id = I("goods_id/d", '0');
        $commentType = I('commentType', '1'); // 1 全部 2好评 3 中评 4差评 5晒图
        if ($commentType == 5) {
            $where = "c.is_show = 1 and c.goods_id = :goods_id and c.parent_id = 0 and c.img !='' and c.img NOT LIKE 'N;%' and c.deleted = 0";
         
        } else {
            $typeArr = array('1' => '0,1,2,3,4,5', '2' => '4,5', '3' => '3', '4' => '0,1,2');
            $where = "c.is_show = 1 and c.goods_id = :goods_id and c.parent_id = 0 and ceil(c.goods_rank) in($typeArr[$commentType]) and c.deleted = 0";
        }
        $count = Db::name('comment')->alias('c')->where($where)->bind(['goods_id'=>$goods_id])->count();

        $page_count = 20;
        $page = new AjaxPage($count, empty($num)?$page_count:$num);
        $list = Db::name('comment')->alias('c')
            ->field("u.head_pic,u.nickname,c.add_time,c.spec_key_name,c.content,c.is_anonymous,
                    c.impression,c.comment_id,c.zan_num,c.zan_userid,c.reply_num,c.goods_rank,
                    c.img,c.parent_id,o.pay_time")
            ->join('__USERS__ u','u.user_id = c.user_id', 'LEFT')
            ->join('__ORDER__ o','o.order_id = c.order_id','LEFT')
            ->where($where)
            ->bind(['goods_id'=>$goods_id])
            ->order("c.add_time desc")
            ->limit($page->firstRow . ',' . $page->listRows)->select();
        $replyList = M('Comment')->where(['goods_id'=>$goods_id,'parent_id'=>['>',0]])->order("add_time desc")->select();
        $reply_logic = new ReplyLogic();
        foreach ($list as $k => $v) {
            $list[$k]['img'] = unserialize($v['img']); // 晒单图片
            $list[$k]['parent_id'] = $reply_logic->getReplyListToArray($v['comment_id'], 5);
            $list[$k]['reply_num'] = Db::name('reply')->where(['comment_id'=>$v['comment_id'],'parent_id'=>0])->count();
            if($v['is_anonymous'] == 1){
                $list[$k]['nickname'] =  mb_substr($v['nickname'], 0, 3,'utf-8') . '***';
            }
        }
        $this->assign('commentlist', $list);// 商品评论
        $this->assign('replyList', $replyList); // 管理员回复
        $this->assign('commentType',$commentType);// 1 全部 2好评 3 中评 4差评 5晒图
        $this->assign('count', $count);//总条数
        $this->assign('user_id', cookie('user_id'));//页数
        echo $this->fetch();
    }
    
    /*
     * 获取商品规格
     */
    public function goodsAttr(){
        $goods_id = I("get.goods_id/d",'0');
        $goods_attribute = M('GoodsAttribute')->getField('attr_id,attr_name'); // 查询属性
        $goods_attr_list = M('GoodsAttr')->where("goods_id" , $goods_id)->select(); // 查询商品属性表
        $this->assign('goods_attr_list',$goods_attr_list);
        $this->assign('goods_attribute',$goods_attribute);
        return $this->fetch();
    }

    /**
     * 积分商城
     */
    public function integralMall()
    {
        $rank= I('get.rank', '');
        $user_id = cookie('user_id');
        $p=I('p/d',0);
        $goodsLogic = new GoodsLogic();
        $result = $goodsLogic->integralMall($rank, $user_id,$p);
        
        $this->assign('goods_list', $result['goods_list']);
        $this->assign('point_rate', $result['point_rate']);//兑换率
        
        if (IS_AJAX) {
            return $this->fetch('ajaxIntegralMall'); //获取更多
        }
        return $this->fetch();
    }
     /**
     * 商品搜索列表页
     */
    public function search(){
        
        $filter_param = array(); // 帅选数组
        $id = I('get.id/d',0); // 当前分类id
        $brand_id = I('brand_id',0);
        $sort = I('sort','sort'); // 排序
        $sort_asc = I('sort_asc','desc'); // 排序
        $price = I('price',''); // 价钱
        $start_price = trim(I('start_price','0')); // 输入框价钱
        $end_price = trim(I('end_price','0')); // 输入框价钱
        if($start_price && $end_price) $price = $start_price.'-'.$end_price; // 如果输入框有价钱 则使用输入框的价钱       
        $filter_param['id'] = $id; //加入帅选条件中
        $brand_id  && ($filter_param['brand_id'] = $brand_id); //加入帅选条件中                
        $price  && ($filter_param['price'] = $price); //加入帅选条件中
        $q = urldecode(trim(I('q',''))); // 关键字搜索
        $q  && ($_GET['q'] = $filter_param['q'] = $q); //加入帅选条件中
        $where = array(
            'goods_name|keywords' => array('like', '%' . $q . '%'),
            'goods_state' => 1,
            'is_on_sale' => 1,
            'exchange_integral'=>0, //不检索积分商品
        );
        $goodsLogic = new GoodsLogic(); // 前台商品操作逻辑类
        $filter_goods_id = M('goods')->where($where)->cache(true)->getField("goods_id",true);

        // 过滤帅选的结果集里面找商品
        if($brand_id || $price)// 品牌或者价格
        {
            $goods_id_1 = $goodsLogic->getGoodsIdByBrandPrice($brand_id,$price); // 根据 品牌 或者 价格范围 查找所有商品id
            $filter_goods_id = array_intersect($filter_goods_id,$goods_id_1); // 获取多个帅选条件的结果 的交集
        }

        $filter_menu  = $goodsLogic->get_filter_menu($filter_param,'search'); // 获取显示的帅选菜单
        $filter_price = $goodsLogic->get_filter_price($filter_goods_id,$filter_param,'search'); // 帅选的价格期间
        $filter_brand = $goodsLogic->get_filter_brand($filter_goods_id,$filter_param,'search'); // 获取指定分类下的帅选品牌

    	$count = count($filter_goods_id);
    	$page = new Page($count,20);
    	if($count > 0 && $filter_goods_id > 0)
    	{
    		$goods_list = M('goods')->where("goods_id in (".  implode(',', $filter_goods_id).")")->order([$sort=>$sort_asc])->limit($page->firstRow.','.$page->listRows)->select();
            foreach ($goods_list as $k=>$v){
                $goods_list[$k]['activity'] = (new Block())->check_activity($v);
            }
    		$filter_goods_id2 = get_arr_column($goods_list, 'goods_id');
    		if($filter_goods_id2)
    			$goods_images = M('goods_images')->where("goods_id in (".  implode(',', $filter_goods_id2).")")->cache(true)->select();
    	}
    	$goods_category = M('goods_category')->where('is_show=1')->cache(true)->getField('id,name,parent_id,level'); // 键值分类数组
    	$this->assign('goods_list',$goods_list);
    	$this->assign('goods_category',$goods_category);
    	$this->assign('goods_images',$goods_images);  // 相册图片
    	$this->assign('filter_menu',$filter_menu);  // 帅选菜单
    	$this->assign('filter_brand',$filter_brand);// 列表页帅选属性 - 商品品牌
    	$this->assign('filter_price',$filter_price);// 帅选的价格期间
    	$this->assign('filter_param',$filter_param); // 帅选条件    	
    	$this->assign('page',$page);// 赋值分页输出
    	$this->assign('sort_asc', $sort_asc == 'asc' ? 'desc' : 'asc');
    	C('TOKEN_ON',false);
        
        if($_GET['is_ajax'])
            return $this->fetch('ajaxGoodsList');
        else
            return $this->fetch();
    }

    /**
     * 回复显示页
     * @author dyr
     */
    public function reply()
    {
        $comment_id = I('get.comment_id/d', 1);
        $page = (I('get.page', 1) <= 0) ? 1 : I('get.page', 1);//页数
        $list_num = 30;//每页条数
        $reply_logic = new ReplyLogic();
        $reply_list = $reply_logic->getReplyPage($comment_id, $page - 1, $list_num);
        $page_sum = ceil($reply_list['count'] / $list_num);
        $comment_info = M('comment')->where(array('comment_id' => $comment_id))->find();
        $comment_info['img'] = unserialize($comment_info['img']);
        if (empty($comment_info)) {
            $this->error('找不到该商品');
        }
        $goods_info = M('goods')->where(array('goods_id' => $comment_info['goods_id']))->find();
        $order_info = M('order')->where(array('order_id' => $comment_info['order_id']))->find();
        $goods_rank = M('comment')->where(array('goods_id' => $comment_info['goods_id'], 'store_id' => $comment_info['store_id']))->avg('goods_rank');
        $order_goods_info = M('order_goods')->where(array('goods_id' => $comment_info['goods_id'], 'order_id' => $comment_info['order_id']))->find();
        $this->assign('goods_rank', number_format($goods_rank, 1));
        $this->assign('goods_info', $goods_info);//商品内容
        $this->assign('order_info', $order_info);//订单内容
        $this->assign('order_goods_info', $order_goods_info);//订单商品内容
        $this->assign('comment_info', $comment_info);//评价内容
        $this->assign('page_sum', intval($page_sum));//总页数
        $this->assign('page_current', intval($page));//当前页
        $this->assign('reply_count', $reply_list['count']);//总回复数
        $this->assign('reply_list', $reply_list['list']);//回复列表
        $this->assign('floor', $reply_list['count'] - (intval($page) - 1) * $list_num);//楼层
        return $this->fetch();
    }
    
    /**
     * 商品搜索列表页
     */
    public function ajaxSearch()
    {
        return $this->fetch();
    }    
    
    /**
     * 用户收藏某一件商品
     * @param type $goods_id
     */
    public function collect_goods()
    {
        $goods_id = I('goods_id/d');
        $goodsLogic = new GoodsLogic();
        $result = $goodsLogic->collect_goods(cookie('user_id'),$goods_id);
        $this->ajaxReturn($result);
    }      

	/**
     * 获取商品分享海报
     */
    public function goodsSharePoster(){
        $goods_id = I("get.id/d",0);
        $item_id = I("get.item_id/d",0);
        $prom_id = I("get.prom_id/d",0);
        $prom_type = I("get.prom_type/d",0);
        $leader_id = I("get.leader_id/d",0);
        if($leader_id == 0 && cookie('user_id')){
            $leader_id = cookie('user_id');
        }

        $data = ['goods_id'=>$goods_id,'item_id'=>$item_id,'prom_id'=>$prom_id,'prom_type'=>$prom_type,'first_leader'=>$leader_id];
        // 用户登录了，获取头像，昵称
        $user = session('user');
        if(!empty($user)){
            $data['head_pic'] = $user['head_pic'];
            $data['nickname'] = $user['nickname'];
        }
        if(empty($data['nickname'])){
            $data['nickname'] = '神秘人物';
        }
        $goodsLogic = new GoodsLogic();
        $goodsLogic->getGoodsSharePoster($data,2); // 加个2表示是手机端
    }
}