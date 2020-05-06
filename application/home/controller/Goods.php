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


use app\common\logic\CommentLogic;
use app\common\logic\FreightLogic;
use app\common\logic\GoodsPromFactory;
use app\common\logic\SearchWordLogic;
use app\common\logic\GoodsLogic;
use app\common\logic\ReplyLogic;
use app\common\logic\User;
use app\common\model\SpecGoodsPrice;
use app\common\model\UserAddress;
use think\AjaxPage;
use think\Db;
use think\Page;
use think\Verify;


class Goods extends Base
{
    public function index()
    {
        return $this->fetch();
    }

    public function goodsInfo()
    {
        $goods_id = input("get.id/d", 0);
        $store_id = input("get.store_id/d", 0);
        $Goods = new \app\common\model\Goods();
        $goodsLogic = new GoodsLogic();
        $goods = $Goods::get($goods_id);
        if ($goods['is_virtual']==1 && $goods['virtual_indate'] <= time()) {
            $goods->save(['is_on_sale'=>0]);//虚拟商品过期，就下架
        }
        if (empty($goods) || ($goods['is_on_sale'] != 1) && I('identity')!='admin') {
            $this->error('该商品已经下架', U('Index/index'));
        }
        if (cookie('user_id')) {
            $user = new User();
            $user->setUserById(cookie('user_id'));
            $user->visitGoodsLog($goods);
        }
        $goods->save(['click_count' => $goods['click_count'] + 1]); //点击数+1
        $goods_attribute = db('goods_attribute')->alias('b')->join('__GOODS_ATTR__ a','a.attr_id=b.attr_id','RIGHT')
            ->where('a.goods_id',$goods_id)->where('b.attr_index',1)
            ->order('b.order DESC')->getField('b.attr_id,b.attr_name,a.attr_value'); // 查询属性
        $spec_goods_price = db('spec_goods_price')->where('goods_id',$goods_id)->getField("key,item_id,price,store_count"); // 规格 对应 价格 库存表
        $filter_spec = $goodsLogic->get_spec($goods_id);
        $look_goods_list = db('goods')->field('goods_name,goods_id,shop_price')->where(['cat_id1'=>['neq',$goods['cat_id1']],'is_on_sale'=>1])->limit(12)->select();
        $ShareLink= urlencode(SITE_URL . "/index.php?m=Mobile&c=Goods&a=goodsInfo&id={$goods_id}"); //二维码连接
        if($goods['plate_top']){
            $plate_top_info = db('store_plate')->where('plate_id='.$goods['plate_top'])->getField('plate_content');
            $this->assign('plate_top_info',$plate_top_info);  
        }
        if($goods['plate_bottom']){
            $plate_bottom_info = db('store_plate')->where('plate_id='.$goods['plate_bottom'])->getField('plate_content'); 
            $this->assign('plate_bottom_info',$plate_bottom_info);
        }
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
		$grtids = db('guarantee')->where(['store_id'=>$goods['store_id'],'isopen'=>1,'auditstate'=>4])->cache(true)->getField('grt_id',true);
		if(is_array($grtids)){
			$guarantee = db('guarantee_item')->where(array('grt_id'=>array('in',$grtids)))->select();
			$this->assign('guarantee',$guarantee);//特色服务保障
		}

        $kf_config['im_choose'] = tpCache('basic.im_choose');
        $this->assign('kf_config',$kf_config);

        $point_rate = tpCache('shopping.point_rate');
        $this->assign('spec_goods_price', json_encode($spec_goods_price, true)); // 规格 对应 价格 库存表
        $this->assign('goods_attribute', $goods_attribute);//属性值
        $this->assign('filter_spec', $filter_spec);//规格参数
        $this->assign('look_goods_list', $look_goods_list);//看了又看
        $this->assign('goods', $goods);
        $this->assign('ShareLink', $ShareLink);
        $this->assign('point_rate', $point_rate);
        return $this->fetch();
    }

    public function activity(){
        $goods_id = input('goods_id/d');//商品id
        $item_id = input('item_id/d');//规格id
        $Goods = new \app\common\model\Goods();
        $goods = $Goods::get($goods_id,'',true);
        $goodsPromFactory = new GoodsPromFactory();
        if ($goodsPromFactory->checkPromType($goods['prom_type'])) {
            //这里会自动更新商品活动状态，所以商品需要重新查询
            if($item_id){
                $specGoodsPrice = SpecGoodsPrice::get($item_id,'',true);
                $goodsPromLogic = $goodsPromFactory->makeModule($goods,$specGoodsPrice);
            }else{
                $goodsPromLogic = $goodsPromFactory->makeModule($goods,null);
            }
            //检查活动是否有效
            if($goodsPromLogic->checkActivityIsAble()){
                $goods = $goodsPromLogic->getActivityGoodsInfo();
                $goods['activity_is_on'] = 1;
                $this->ajaxReturn(['status'=>1,'msg'=>'该商品参与活动','result'=>['goods'=>$goods]]);
            }else{
                $goods['activity_is_on'] = 0;
                $this->ajaxReturn(['status'=>1,'msg'=>'该商品没有参与活动','result'=>['goods'=>$goods]]);
            }
        }
        $this->ajaxReturn(['status'=>1,'msg'=>'该商品没有参与活动','result'=>['goods'=>$goods]]);
    }

    /**
     *  查询配送地址，并执行回调函数
     */
    public function region()
    {
        $fid = I('fid/d');
        $callback = I('callback');

        $parent_region_code = $parent_region = Db::name('region')->field('id,name')->where(array('parent_id' => $fid))->cache(true)->select();
        foreach($parent_region_code as $key=>$val){
            $parent_region_code[$key]['name'] = urlencode($val['name']);
        }
//        Cookie::set('parent_region', $parent_region_code);
        echo $callback . '(' . json_encode($parent_region) . ')';
        exit;
    }

    /**
     * 商品物流配送和运费
     */
    public function dispatching()
    {
        $goods_id = I('goods_id/d');//143
        $region_id = I('region_id/d');//28242
        if(empty($region_id)){
            $province = input('province');
            $city = input('city');
            $district = input('district');
            $province_id = Db::name('region')->where('name',$province)->where('level',1)->value('id');
            if(empty($province_id)){
                $province_id = Db::name('region')->where('name','like',mb_substr($province, 0, 2,'utf-8').'%')->where('level',1)->value('id');
            }
            $city_id = Db::name('region')->where('name',$city)->where('parent_id',$province_id)->where('level',2)->value('id');
            if(!$city_id){
                $city_id = Db::name('region')->where('name','like',mb_substr($city, 0, 2,'utf-8').'%')->where('parent_id',$province_id)->where('level',2)->value('id');
            }
            if(!$city_id){
                $region_id = $province_id;
            }else{
                $district_id = Db::name('region')->where('name',$district)->where('parent_id',$city_id)->where('level',3)->value('id');
                if(!$district_id){
                    $district_id = Db::name('region')->where('name','like',mb_substr($district, 0, 2,'utf-8').'%')->where('parent_id',$city_id)->where('level',3)->value('id');
                    if(!$district_id){
                        $region_id = $city_id;
                    }else{
                        $region_id = $district_id;
                    }
                }else{
                    $region_id = $district_id;
                }
            }

            $addr_id['province_id'] = $province_id;
            $addr_id['city_id'] = $city_id;
            $addr_id['district_id'] = $district_id;
            $addr_id['$province'] = $province;
            $addr_id['$city'] = $city;
            $addr_id['$district'] = $district;
        }else{
            $addr_id = [];
        }
        $dispatching_data = S("goods_dispatching_{$goods_id}_$region_id");
        if($dispatching_data){
            $this->ajaxReturn($dispatching_data);
        }
        $Goods = new \app\common\model\Goods();
        $goods = $Goods->cache(true)->where('goods_id',$goods_id)->find();
        $freightLogic = new FreightLogic();
        $freightLogic->setGoodsModel($goods);
        $freightLogic->setRegionId($region_id);
        $freightLogic->setGoodsNum(1);
        $isShipping = $freightLogic->checkShipping();
        if($isShipping){
            $freightLogic->doCalculation();
            $freight = $freightLogic->getFreight();
            $dispatching_data = ['status'=>1,'msg'=>'可配送','result'=>['freight'=>$freight],'addr_id'=>$addr_id];
        }else{
            $dispatching_data = ['status'=>0,'msg'=>'该地区不支持配送','result'=>'','addr_id'=>$addr_id,'is_not'=>1];
            //如果用户还没有填地址，则默认配送
            $user_id = session('user.user_id');
            if($user_id){
                $count =  UserAddress::where(['user_id'=>$user_id])->cache(60)->count();
                if(0 == $count){
                    $dispatching_data = ['status' => 1, 'msg' => '暂无收货地址', 'result' =>  ['freight' => 0],'addr_id'=>$addr_id];
                }
            }
        }
        S("goods_dispatching_{$goods_id}_$region_id", $dispatching_data ,60);
        $this->ajaxReturn($dispatching_data);
    }

    /**
     * 商品列表页
     */
    public function goodsList()
    {
        $key = md5($_SERVER['REQUEST_URI'] . $_POST['start_price'] . '_' . $_POST['end_price']);
        $html = S($key);
        if (!empty($html)) {
            exit($html);
        }

        $filter_param = array(); // 筛选数组
        $id = I('get.id/d', 0); // 当前分类id
        $brand_id = I('get.brand_id',0);

        $own_shop = I('get.own_shop/d', 0);     //自营商品
        $recommend = I('get.recommend/d', 0);    //推荐商品
        $stock = I('get.stock/d', 0);    //显示有货
        $promotion = I('get.promotion/d', 0);    //促销商品

        //$spec = I('get.spec',0); // 规格
        $attr = I('get.attr', ''); // 属性
        $sort = I('get.sort', 'sort'); // 排序
        $sort_asc = I('get.sort_asc', 'desc'); // 排序
        $price = I('get.price', ''); // 价钱
        $start_price = trim(I('post.start_price', '0')); // 输入框价钱
        $end_price = trim(I('post.end_price', '0')); // 输入框价钱
        if ($start_price && $end_price) $price = $start_price . '-' . $end_price; // 如果输入框有价钱 则使用输入框的价钱

        $filter_param['id'] = $id; //加入筛选条件中
        $brand_id && ($filter_param['brand_id'] = $brand_id); //加入筛选条件中
        //$spec  && ($filter_param['spec'] = $spec); //加入筛选条件中
        $attr && ($filter_param['attr'] = $attr); //加入筛选条件中
        $price && ($filter_param['price'] = $price); //加入筛选条件中

        $goodsLogic = new GoodsLogic(); // 前台商品操作逻辑类

        // 分类菜单显示
        $goodsCate = M('GoodsCategory')->where("id", $id)->find();// 当前分类
        //($goodsCate['level'] == 1) && header('Location:'.U('Home/Channel/index',array('cat_id'=>$id))); //一级分类跳转至大分类馆
        $cateArr = $goodsLogic->get_goods_cate($goodsCate);

        // 筛选 品牌 规格 属性 价格
        //$cat_id_arr = getCatGrandson ($id);
        $goods_where = ['goods_state' => 1, 'is_on_sale' => 1];
        if ($goodsCate) {
            $goods_where['cat_id' . $goodsCate['level']] = $id;
        }
        $filter_goods_id = Db::name('goods')->where($goods_where)->cache(true)->getField("goods_id",true);
        $this->assign('filter_goods_id_str', implode(',', $filter_goods_id) ? implode(',', $filter_goods_id) : 0);
        // 过滤筛选的结果集里面找商品
        if ($brand_id || $price)// 品牌或者价格
        {
            $goods_id_1 = $goodsLogic->getGoodsIdByBrandPrice($brand_id, $price); // 根据 品牌 或者 价格范围 查找所有商品id
            $filter_goods_id = array_intersect($filter_goods_id, $goods_id_1); // 获取多个筛选条件的结果 的交集
        }

        if ($own_shop || $recommend || $stock || $promotion)// 自营商品 , 是否推荐 , 促销商品 , 显示有货
        {
            $goods_id_1 = $goodsLogic->getGoodsIdByCheckbox($own_shop, $recommend, $promotion, $stock);//根据自营商品 , 是否推荐 , 促销商品 , 显示有货 条件筛选出 商品id
            $filter_goods_id = array_intersect($filter_goods_id, $goods_id_1); // 获取多个筛选条件的结果 的交集
        }

        //if($spec)// 规格
        //{
        //    $goods_id_2 = $goodsLogic->getGoodsIdBySpec($spec); // 根据 规格 查找当所有商品id
        //    $filter_goods_id = array_intersect($filter_goods_id,$goods_id_2); // 获取多个筛选条件的结果 的交集
        //}
        if ($attr)// 属性
        {
            $goods_id_3 = $goodsLogic->getGoodsIdByAttr($attr); // 根据 规格 查找当所有商品id
            $filter_goods_id = array_intersect($filter_goods_id, $goods_id_3); // 获取多个筛选条件的结果 的交集
        }
        $count = count($filter_goods_id);
        // 如果商品id 过多 分多次查询
        $s = 1000;
        $p = $count/$s;
        $p = ceil($p);
        $filter_price = $filter_brand = $filter_attr = [];
        for($i=0; $i<$p; $i++)
        {
            $start =  $i * $s;
            $filter_goods_id2 = array_slice($filter_goods_id,$start,$s);

            $filter_price2 = $goodsLogic->get_filter_price($filter_goods_id2, $filter_param, 'goodsList'); // 筛选的价格期间
            $filter_price = array_merge($filter_price,$filter_price2);
            $filter_price = array_unique($filter_price,SORT_REGULAR);

            $filter_brand2 = $goodsLogic->get_filter_brand($filter_goods_id2, $filter_param, 'goodsList'); // 获取指定分类下的筛选品牌
            $filter_brand = array_merge($filter_brand,$filter_brand2);
            $filter_brand = array_unique($filter_brand,SORT_REGULAR);

            $filter_attr2 = $goodsLogic->get_filter_attr($filter_goods_id2, $filter_param, 'goodsList', 1); // 获取指定分类下的筛选属性
            $filter_attr = array_merge($filter_attr,$filter_attr2);
            $filter_attr = array_unique($filter_attr,SORT_REGULAR);

        }
        array_multisort(array_column($filter_attr,'order'),SORT_ASC,$filter_attr);
        $filter_menu = $goodsLogic->get_filter_menu($filter_param, 'goodsList'); // 获取显示的筛选菜单

        $page = new Page($count, 40);
        if ($count > 0) {
            $goods_list = M('goods')->alias('g')
                ->join('__STORE__ s', 's.store_id = g.store_id')
                ->where("goods_id", "in", implode(',', $filter_goods_id))
                ->order([$sort=>$sort_asc])->limit($page->firstRow . ',' . $page->listRows)->select();
            $filter_goods_id2 = get_arr_column($goods_list, 'goods_id');
            if ($filter_goods_id2)
                $goods_images = M('goods_images')->where("goods_id", "in", implode(',', $filter_goods_id2))->cache(true)->select();
        }
        $goods_category = M('goods_category')->where('is_show=1')->cache(true)->getField('id,name,parent_id,level'); // 键值分类数组
        $navigate_cat = navigate_goods($id); // 面包屑导航
        $this->assign('goods_list', $goods_list);
        $this->assign('navigate_cat', $navigate_cat);
        $this->assign('goods_category', $goods_category);
        $this->assign('goods_images', $goods_images);  // 相册图片
        $this->assign('filter_menu', $filter_menu);  // 筛选菜单
        //$this->assign('filter_spec', $filter_spec);  // 筛选规格
        $this->assign('filter_attr', $filter_attr);  // 筛选属性
        $this->assign('filter_brand', $filter_brand);  // 列表页筛选属性 - 商品品牌
        $this->assign('filter_price', $filter_price);// 筛选的价格期间
        $this->assign('goodsCate', $goodsCate);
        $this->assign('cateArr', $cateArr);
        $this->assign('filter_param', $filter_param); // 筛选条件
        $this->assign('cat_id', $id);
        $this->assign('page', $page);// 赋值分页输出
        C('TOKEN_ON', false);
        $html = $this->fetch();
        S($key, $html);
        echo $html;
    }

    /**
     * @author dyr
     * 回复显示页
     */
    public function reply()
    {
        $comment_id = I('get.comment_id/d', 1);
        $page = (I('get.page', 1) <= 0) ? 1 : I('get.page', 1);//页数
        $list_num = 10;//每页条数
        $reply_logic = new ReplyLogic();
        $reply_list = $reply_logic->getReplyPage($comment_id, $page - 1, $list_num);
        $page_sum = ceil($reply_list['count'] / $list_num);
        $comment_info = M('comment')->where(array('comment_id' => $comment_id))->find();
        if (empty($comment_info)) {
            $this->error('找不到该商品');
        }
        $comment_info['img'] = unserialize($comment_info['img']);
        $goods_info = M('goods')->where(array('goods_id' => $comment_info['goods_id']))->find();
        $order_info = M('order')->where(array('order_id' => $comment_info['order_id']))->find();
        $goods_rank = M('comment')->where(array('goods_id' => $comment_info['goods_id'], 'store_id' => $comment_info['store_id']))->avg('goods_rank');
        $order_goods_info = M('order_goods')->where(array('goods_id' => $comment_info['goods_id'], 'order_id' => $comment_info['order_id']))->find();
        $this->assign('goods_rank', number_format($goods_rank, 1));
        $this->assign('goods_info', $goods_info);
        $this->assign('order_info', $order_info);
        $this->assign('order_goods_info', $order_goods_info);
        $this->assign('comment_info', $comment_info);
        $this->assign('page_sum', intval($page_sum));//总页数
        $this->assign('page_current', intval($page));//当前页
        $this->assign('reply_count', $reply_list['count']);//总回复数
        $this->assign('reply_list', $reply_list['list']);//回复列表
        return $this->fetch();
    }

    /**
     * @author dyr
     * 获取回复
     */
    public function ajaxReply()
    {
        $comment_id = I('post.comment_id/d', 1);
        $reply_logic = new ReplyLogic();
        $reply_list = $reply_logic->getReplyListToArray($comment_id, 4);
        $this->ajaxReturn($reply_list);
    }


    /**
     * 商品搜索列表页
     */
    public function search()
    {
        //C('URL_MODEL',0);
        $filter_param = array(); // 筛选数组
        $id = I('get.id/d', 0); // 当前分类id
        $brand_id = I('brand_id', 0);

        $own_shop = I('get.own_shop/d', 0);     //自营商品
        $recommend = I('get.recommend/d', 0);    //推荐商品
        $stock = I('get.stock/d', 0);    //显示有货
        $promotion = I('get.promotion/d', 0);    //促销商品

        $sort = I('sort', 'sort'); // 排序
        $sort_asc = I('sort_asc', 'desc'); // 排序
        $price = I('price', ''); // 价钱
        $start_price = trim(I('start_price', '0')); // 输入框价钱
        $end_price = trim(I('end_price', '0')); // 输入框价钱
        $store_id = I('store_id/d');
        if ($start_price && $end_price) $price = $start_price . '-' . $end_price; // 如果输入框有价钱 则使用输入框的价钱
        $q = urldecode(trim(I('q', ''))); // 关键字搜索
        $id && ($filter_param['id'] = $id); //加入筛选条件中
        $brand_id && ($filter_param['brand_id'] = $brand_id); //加入筛选条件中
        $price && ($filter_param['price'] = $price); //加入筛选条件中
        $q && ($_GET['q'] = $filter_param['q'] = $q); //加入筛选条件中

        $goodsLogic = new GoodsLogic(); // 前台商品操作逻辑类
        $SearchWordLogic = new SearchWordLogic();
        $where = $SearchWordLogic->getSearchWordWhere($q);
        $where['is_on_sale'] = 1;
        $where['goods_state'] = 1;
        if ($store_id) {
            $where['store_id'] = $store_id;
        }
        Db::name('search_word')->where('keywords', $q)->setInc('search_num');
        $goodsHaveSearchWord = Db::name('goods')->where($where)->count();
        if ($goodsHaveSearchWord) {
            $SearchWordIsHave = Db::name('search_word')->where('keywords',$q)->find();
            if($SearchWordIsHave){
                Db::name('search_word')->where('id',$SearchWordIsHave['id'])->update(['goods_num'=>$goodsHaveSearchWord]);
            }else{
                $SearchWordData = [
                    'keywords' => $q,
                    'pinyin_full' => $SearchWordLogic->getPinyinFull($q),
                    'pinyin_simple' => $SearchWordLogic->getPinyinSimple($q),
                    'search_num' => 1,
                    'goods_num' => $goodsHaveSearchWord
                ];
                Db::name('search_word')->insert($SearchWordData);
            }
        }
        if ($id) {
            //分类菜单显示
            $goodsCate = M('GoodsCategory')->where("id", $id)->find();// 当前分类
            $where['cat_id' . $goodsCate['level']] = $id;
        }

        $search_goods = M('goods')->where($where)->getField('goods_id,cat_id3');
        $filter_goods_id = array_keys($search_goods);
        $filter_cat_id = array_unique($search_goods); // 分类需要去重
        if ($filter_cat_id) {
            $cateArr = M('goods_category')->where("id", "in", implode(',', $filter_cat_id))->select();
            $tmp = $filter_param;
            foreach ($cateArr as $k => $v) {
                $tmp['id'] = $v['id'];
                $cateArr[$k]['href'] = U("/Home/Goods/search", $tmp);
            }
        }
        // 过滤筛选的结果集里面找商品
        if ($brand_id || $price)// 品牌或者价格
        {
            $goods_id_1 = $goodsLogic->getGoodsIdByBrandPrice($brand_id, $price); // 根据 品牌 或者 价格范围 查找所有商品id
            $filter_goods_id = array_intersect($filter_goods_id, $goods_id_1); // 获取多个筛选条件的结果 的交集
        }
        $this->assign('filter_goods_id_str', implode(',', $filter_goods_id) ? implode(',', $filter_goods_id) :1);
        if ($own_shop || $recommend || $stock || $promotion)// 自营商品 , 是否推荐 , 促销商品 , 显示有货
        {
            $goods_id_1 = $goodsLogic->getGoodsIdByCheckbox($own_shop, $recommend, $promotion, $stock);//根据自营商品 , 是否推荐 , 促销商品 , 显示有货 条件筛选出 商品id
            $filter_goods_id = array_intersect($filter_goods_id, $goods_id_1); // 获取多个筛选条件的结果 的交集
        }
        $filter_menu = $goodsLogic->get_filter_menu($filter_param, 'search'); // 获取显示的筛选菜单
        $filter_price = $goodsLogic->get_filter_price($filter_goods_id, $filter_param, 'search'); // 筛选的价格期间
        $filter_brand = $goodsLogic->get_filter_brand($filter_goods_id, $filter_param, 'search'); // 获取指定分类下的筛选品牌

        $count = count($filter_goods_id);
        $page = new Page($count, 20);
        if ($count > 0) {
            $goods_list = M('goods')->where(['is_on_sale' => 1, 'goods_id' => ['in', implode(',', $filter_goods_id)]])->order([$sort=>$sort_asc])->limit($page->firstRow . ',' . $page->listRows)->select();
            $filter_goods_id2 = get_arr_column($goods_list, 'goods_id');
            if ($filter_goods_id2)
                $goods_images = M('goods_images')->where("goods_id", "in", implode(',', $filter_goods_id2))->select();
        }

        $this->assign('goods_list', $goods_list);
        $this->assign('goods_images', $goods_images);  // 相册图片
        $this->assign('filter_menu', $filter_menu);  // 筛选菜单
        $this->assign('filter_brand', $filter_brand);  // 列表页筛选属性 - 商品品牌
        $this->assign('filter_price', $filter_price);// 筛选的价格期间
        $this->assign('cateArr', $cateArr);
        $this->assign('filter_param', $filter_param); // 筛选条件
        $this->assign('cat_id', $id);
        $this->assign('q', I('q'));
        $this->assign('page', $page);// 赋值分页输出
        C('TOKEN_ON', false);
        return $this->fetch();
    }


    /**
     * @author dyr
     * 商品评论ajax分页
     */
    public function ajaxComment()
    {
        $goods_id = I("goods_id/d", '0');
        $commentType = I('commentType', '1'); // 1 全部 2好评 3 中评 4差评 5晒图
        $commentLogic = new CommentLogic();
        $goodsCommentList = $commentLogic->getGoodsComment($goods_id, $commentType);
        $this->assign('commentlist', $goodsCommentList['list']);// 商品评论
//        $this->assign('replyList', $replyList); // 管理员回复
        $this->assign('page', $goodsCommentList['page']);// 赋值分页输出
        return $this->fetch();
    }


    /**
     *  商品咨询
     */
    public function goodsConsult()
    {
        //  form表单提交
        C('TOKEN_ON', true);
        $goods_id = I("goods_id/d", '0'); // 商品id
        $store_id = I("store_id/d", '0'); // 商品id
        $consult_type = I("consult_type", '1'); // 商品咨询类型
        $username = I("username", 'TPshop用户'); // 网友咨询
        $content = trim(I("content",'')); // 咨询内容
        if(session('?user')){
            $user = session('user');
            $user_id = $user['user_id'];
        }else{
            $user_id = 0;
        }
        if(strlen($content) >500)
            $this->error("咨询内容不得超过500字符！！");
        $verify = new Verify();
        if (!$verify->check(I('post.verify_code'), 'consult')) {
            $this->error("验证码错误");
        }
        $data = array(
            'goods_id' => $goods_id,
            'consult_type' => $consult_type,
            'username' => $username,
            'content' => $content,
            'store_id' => $store_id,
            'is_show' => 1,
            'add_time' => time(),
            'user_id' => $user_id,
        );
        Db::name('goodsConsult')->add($data);
        $this->success('咨询已提交!', U('/Home/Goods/goodsInfo', array('id' => $goods_id)));
    }

    /**
     * 商品咨询ajax分页
     */
    public function ajax_consult()
    {
        $goods_id = I("goods_id/d", '0');
        $consult_type = I('consult_type', '0'); // 0全部咨询  1 商品咨询 2 支付咨询 3 配送 4 售后
        $where = ['parent_id' => 0, 'goods_id' => $goods_id,'is_show'=>1];
        $consult_type > 0 ? $where['consult_type'] = $consult_type :false;
        $goodsConsultModel= new  \app\common\model\GoodsConsult();
        $count = $goodsConsultModel->where($where)->count();
        $page = new AjaxPage($count, 5);
        $consultList = $goodsConsultModel->where($where)->order("id desc")->limit($page->firstRow,$page->listRows)->order('add_time desc')->select();
        $this->assign('consultList', $consultList );// 商品咨询
        $this->assign('page', $page);// 赋值分页输出
        return $this->fetch();
    }

    /**
     * 用户收藏某一件商品
     */
    public function collect_goods()
    {
        $goods_id = I('goods_id/d');
        $collect=Db::name('goods_collect')->where(['goods_id'=>$goods_id,'user_id'=>cookie('user_id')])->find();
        if($collect){
            if (M('goods_collect')->where(["collect_id" => $collect['collect_id'], "user_id" => cookie('user_id')])->delete()) {
                Db::name('goods')->where('goods_id', $goods_id)->setDec('collect_sum');
                $num=Db::name('goods')->where('goods_id', $goods_id)->getField('collect_sum');
                $this->ajaxReturn(['status' => 0, 'msg' => "已取消收藏",'result'=>["num"=>$num]]);
            } else {
                $num=Db::name('goods')->where('goods_id', $goods_id)->getField('collect_sum');
                $this->ajaxReturn(['status' => 0, 'msg' => "未取消收藏",'result'=>["num"=>$num]]);
            }
//                $this->ajaxReturn(['status' => 1, 'msg' => "已取消收藏", 'status'=>0]);
//            } else {
//                $this->ajaxReturn(['status' => 1, 'msg' => "未取消收藏", 'status'=>0]);
//            }
        }else{
            $goodsLogic = new GoodsLogic();
            $result = $goodsLogic->collect_goods(cookie('user_id'), $goods_id);
            exit(json_encode($result));
        }
    }

    public function collects(){
        $goods_ids = I('goods_ids/a',[]);
        if(empty($goods_ids)){
            $this->ajaxReturn(['status'=>0,'msg'=>'请至少选择一个商品','result'=>'']);
        }
        $goodsLogic = new GoodsLogic();
        $result = [];
        foreach($goods_ids as $key=>$val){
            $result[] = $goodsLogic->collect_goods(cookie('user_id'), $val);
        }
        $this->ajaxReturn(['status'=>1,'msg'=>'已添加至我的收藏','result'=>$result]);
    }

    /**
     * 加入购物车弹出
     */
    public function open_add_cart()
    {
        return $this->fetch();
    }

    public function integralMall()
    {
        $cat_id = I('get.id/d');
        $brandType = I('get.brandType');
        $point_rate = tpCache('shopping.point_rate'); //多少积分抵1元
        $goods_where = ['is_virtual'=>0,'is_on_sale'=>1,'exchange_integral'=>['gt',0]];
        // 分类id
        if (!empty($cat_id)) {
            $goods_where['cat_id1'] = ['IN',$cat_id];
        }
        //积分+金额
        if ($brandType == 1) {
            $goods_where['exchange_integral'] = ["exp","> 0 and exchange_integral < shop_price * $point_rate"];
        }
        //全部积分
        if ($brandType == 2) {
            $goods_where['exchange_integral'] = ["exp","> 0 and exchange_integral = shop_price * $point_rate"];
        }
        $goods_list_count = Db::name('goods')->cache(true)->where($goods_where)->count();
        $page = new Page($goods_list_count, 30);
        $goods_list = Db::name('goods')
            ->cache(true)
            ->where($goods_where)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        $category_list = Db::name('goods_category')->cache(true)->where('level',1)->select();
        $this->assign('goods_list', $goods_list);
        $this->assign('page', $page);
        $this->assign('goods_list_count', $goods_list_count);
        $this->assign('category_list', $category_list);//商品1级分类
        $this->assign('point_rate', $point_rate);//兑换率
        return $this->fetch();
    }

    /**
     * 全部商品分类
     * @time17-4-18
     */
    public function all_category(){
        return $this->fetch();
    }

    /**
     * 全部品牌列表
     * @time17-4-18
     */
    public function all_brand(){
        return $this->fetch();
    }

}