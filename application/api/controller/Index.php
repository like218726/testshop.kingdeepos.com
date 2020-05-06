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
use app\common\logic\GoodsLogic;
use app\common\model\Store;
use app\common\model\team\TeamActivity;
use app\common\model\team\TeamFound;
use app\common\model\team\TeamGoodsItem;
use think\Db;
use think\Page;
use app\common\logic\StoreLogic;
use app\common\logic\MomentsLogic;
use app\common\validate\Moments;
use app\common\logic\UsersLogic;
use app\common\model\Moments as MobileMoment;

class Index extends Base {

    public function index(){
        return $this->fetch();
    }

    public function get_noob(){
        $noob_gift=Db::name('coupon')->where(['type'=>4,'send_start_time'=>array('<=',time()),'send_end_time'=>array('>=',time()),'status'=>'1'])->order('add_time','desc')->limit(2)->select();
        $order=Db::name('order')->where(['user_id'=>$this->user_id])->count();
        $user=Db::name('users')->where(['reg_time'=>array('>',(time()-604800))])->where(['user_id'=>$this->user_id])->count();
        $coupon_list=Db::name('coupon_list')->where(['uid'=>$this->user_id])->count();
		
        if($order==0 and $user and $coupon_list==0 and $noob_gift){
            $this->ajaxReturn(array('status'=>1,'noob_gift'=>$noob_gift));
        }
        $this->ajaxReturn(array('status'=>0,'noob_gift'=>array()));
    }

    public function get_noob_coupon(){
        $coupon_id = I('coupon_id/s');
        $coupons=array_filter(explode('_',$coupon_id));
        $userInfo = session('user');
        if($this->user_id==0){
            $this->ajaxReturn(['status' => 0, 'msg' => '请先登录']);
        }
        $user = new \app\common\logic\User();
        $user->setUserById($this->user_id);
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

    //自定义首页内容
    public function block_index(){
        $id = input('id/d');
        if($id){
            $where['id'] = $id;
        }else{
            $where['is_index'] = 1;
        }
        $arr = M('mobile_template')->where($where)->field('block_info')->find();
        if(!$arr){
            $this->ajaxReturn(array(
                'status'=>0,
                'msg'=>'获取失败',
                'result'=>'未设置自定义首页,请启用系统默认首页'
            ));
            exit();
        }

        $arr=$arr['block_info'];
        $info=json_decode(htmlspecialchars_decode(htmlspecialchars_decode($arr)),true);//这里不知道为毛url要反序列化两遍才正常
        $info_array=$this->object_array($info);//转为数组

        //橱窗类型数组处理
        foreach ($info_array as $k => $v) {
            if(is_array($v)){
                if($v['block_type']==4){
                    if($v['window_style']==0){
                        unset($info_array[$k]['nav'][1]);
                        unset($info_array[$k]['nav'][2]);
                        unset($info_array[$k]['nav'][3]);
                    }
                    if($v['window_style']==1){
                        unset($info_array[$k]['nav'][2]);
                        unset($info_array[$k]['nav'][3]);
                    }
                    if(($v['window_style']>1) && ($v['window_style']<5)){
                        unset($info_array[$k]['nav'][3]);
                    }
                }
            }
        }

        foreach ($info_array as $k => $v) {
            if(is_array($v)){
                switch ($v['block_type']) {
                    case '0'://海报
                        $tmp=$this->get_url($v['url']);
                        $info_array[$k]['app_url']=$tmp['info'];
                        if(!isset($v['pic'])){
                            $info_array[$k]['pic'] = '';
                        }
                        // height 23.4375
                        if(strpos($v['height'],'rem')){
                            $num = str_replace('rem','',$v['height']);
                            $info_array[$k]['height'] = $num * 23.4375;
                        }
                        break;

                    case '1'://轮播广告
                        foreach ($v['nav'] as $k2 => $v2) {
                            if(empty($v2['pic'])){
                                $info_array[$k]['nav'][$k2]['pic'] = '/public/static/images/banner1.jpg';
                            }
                            $tmp=$this->get_url($v2['url']);
                            $info_array[$k]['nav'][$k2]['url_type']=$tmp['url_type'];
                            $info_array[$k]['nav'][$k2]['app_url']=$tmp['info'];
                        }
                        break;

                    case '2'://快捷入口
                        foreach ($v['nav'] as $k2 => $v2) {
                            $tmp=$this->get_url(htmlspecialchars_decode($v2['url']));
                            $info_array[$k]['nav'][$k2]['url_type']=$tmp['url_type'];
                            $info_array[$k]['nav'][$k2]['app_url']=$tmp['info'];
                            $info_array[$k]['nav'][$k2]['url']=$tmp['info'];
                        }
                        break;

                    case '3'://商品列表
                        foreach ($v['nav'] as $k2 => $v2) {
                            $v2['sql_where']['order'] = $v['order']; // 保持与h5显示一样
                            $tmp=$this->goods_list_block($v2['sql_where'],$v['num']);

                            $info_array[$k]['nav'][$k2]['goods_list']=$tmp;
                        }
                        break;

                    case '4'://橱窗    
                        foreach ($v['nav'] as $k2 => $v2) {
                            $tmp=$this->get_url(htmlspecialchars_decode($v2['url']));
                            $info_array[$k]['nav'][$k2]['url_type']=$tmp['url_type'];
                            $info_array[$k]['nav'][$k2]['app_url']=$tmp['info']; 
                        }
                        break;
                    case '10'://公告
                        foreach ($v['nav'] as $k2 => $v2) {
                            $tmp=$this->get_url(htmlspecialchars_decode($v2['url']));
                            $info_array[$k]['nav'][$k2]['url_type']=$tmp['url_type'];
                            $info_array[$k]['nav'][$k2]['app_url']=$tmp['info']; 
                        }
                        break;
                    case '6'://营销活动
                        if($v['activity_type']==0){//拼团
                            $info_array[$k]['team_list']=$this->team_list();
                            if(empty($info_array[$k]['team_list'])){
                                unset($info_array[$k]); //藏起组件
                            }
                        }
                        if($v['activity_type']==1){//秒杀
                            $tmp=$this->get_flash_sale_goods();
                            $info_array[$k]['flash_sale_list']=$tmp['list'];
                            $info_array[$k]['server_time']=time();
                            $info_array[$k]['start_time']=$tmp['time']['start_time'];
                            $info_array[$k]['start_time_format']=date('H',$tmp['time']['start_time']);
                            $info_array[$k]['end_time']=$tmp['time']['end_time'];
                            if(empty($tmp['list'])){
                                unset($info_array[$k]); //藏起组件
                            }
                        }

                        break;
                    case '5':
                        $tmp=$this->get_url(htmlspecialchars_decode($v['url']));
                        $info_array[$k]['url_type']=$tmp['url_type'];
                        $info_array[$k]['app_url']=$tmp['info'];
                        break;
                    case '12':
                        $tmp=$this->get_new_list($v['new_num'],trim($v['ids'],','));
                        $info_array[$k]['new_list']=$tmp;
                        break;
                    case '13'://多图滑动
                        $data=array('block_type'=>$v['block_type'],'num'=>$v['num'],'order'=>$v['order'],'goods'=>$v['goods'],'ids'=>$v['ids']);
                        if($v['sql_where']){
                            $data['sql_where'] = $v['sql_where'];
                        }
                        $tmp=$this->goods_list_block($data,$v['num']);
                        $info_array[$k]['goods_list']=$tmp;
                        break;
                    case '15':
                        $tmp=$this->get_url($v['url']);
                        $info_array[$k]['url_type']=$tmp['url_type'];
                        $info_array[$k]['app_url']=$tmp['info'];
                        break;
                    case '16':
                        $tmp=$this->get_url($v['url']);
                        $info_array[$k]['url_type']=$tmp['url_type'];
                        $info_array[$k]['app_url']=$tmp['info'];
                        break;
                    case '17': //单文本
                        $tmp=$this->get_url(htmlspecialchars_decode($v['url']));
                        $info_array[$k]['url_type']=$tmp['url_type'];
                        $info_array[$k]['app_url']=$tmp['info'];
                        break;
                    case '11': //底部
                        foreach ($v['nav'] as $k2 => $v2) {
                            $tmp=$this->get_url(htmlspecialchars_decode($v2['url']));
                            $info_array[$k]['nav'][$k2]['url_type']=$tmp['url_type'];
                            $info_array[$k]['nav'][$k2]['app_url']=$tmp['info'];
                        }
                        break;
                    default:

                        break;
                }
            }else{
                //不是数组时的处理
            }
        }

        foreach ($info_array as $k => $v) {
            if(is_array($v)){
                $info_array['blocks'][]=$v;
                unset($info_array[$k]);
            }
        }

        foreach ($info_array['blocks'] as $k => $v) {
            //优惠券部分字段优化,便于小程序使用
            if($v['block_type']==7){
                foreach ($v['nav'] as $k2 => $v2) {
                    $info_array['blocks'][$k]['nav'][$k2]['money']=intval($v2['money']);
                    $info_array['blocks'][$k]['nav'][$k2]['condition']=intval($v2['condition']);
                }
            }
        }

        $this->ajaxReturn(array(
            'status'=>1,
            'msg'=>'获取成功',
            'result'=>$info_array
        ));
    }
 
    //object类型转化为array类型方便处理
    public function object_array($array){
        if(is_object($array)){
            $array = (array)$array;
        }
        if(is_array($array)){
            foreach($array as $key=>$value){
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }

    //获取url(无url_type参数时)
    public function get_url($url=''){
        $url=htmlspecialchars_decode($url);
        $arr=array('url_type'=>'','info'=>'');
        $header=substr($url,0,10);

        //index2/id/

        if($header=='/index.php'){
            if((strpos($url,'goodsList/id'))!==false){//属于分类链接
                $a=explode('/id',$url);
                $a=explode('.', $a[1]);
                $arr['url_type']=2;
                $arr['info']=substr($a[0],1);
            }elseif((strpos($url,'a=goodsInfo'))!==false){//属于商品详情链接
                $a=explode('id=', $url);
                $arr['url_type']=3;
                $arr['info']=$a[1];
                ///index.php?m=api&c=news&a=news_detail&news_id=14
            }elseif((strpos($url,'news_id='))!==false){//新闻链接
                //$a=explode('news_id=', $url);
                $arr['url_type']=5;
                //$arr['info']=$a[1];
                $arr['info']=$url;
            }elseif((strpos($url,'index2/id'))!==false){//自定义页面链接
                $a=explode('id/', $url);
                $arr['url_type']=4;
                $arr['info']=$a[1];
            }else{


                if(strpos($url,'Goods/categoryList')){//快捷入口的分类链接
                    $arr = $this->get_url_by(5);
                }elseif(strpos($url,'Index/index') ){ //   个人中心
                    $arr = $this->get_url_by(0);
                }elseif(strpos($url,'User/collect_list')){ //收藏
                    $arr = $this->get_url_by(10);
                }elseif(strpos($url,'Cart/index')){ //购物车
                    $arr = $this->get_url_by(1);
                }elseif(strpos($url,'User/index')){ //首页
                    $arr = $this->get_url_by(6);
                }else{
                    $arr['url_type']=1;
                    $arr['info']=$url;
                }
            }
        }else{


            if(strpos($url,'Goods/categoryList')){//快捷入口的分类链接
                $arr = $this->get_url_by(5);
            }elseif(strpos($url,'Index/index') ){ //   个人中心
                $arr = $this->get_url_by(0);
            }elseif(strpos($url,'User/collect_list')){ //收藏
                $arr = $this->get_url_by(10);
            }elseif(strpos($url,'Cart/index')){ //购物车
                $arr = $this->get_url_by(1);
            }elseif(strpos($url,'User/index')){ //首页
                $arr = $this->get_url_by(6);
            }else{
                $arr['url_type']=0;
                $arr['info']=$url;
            }

        }
        return $arr;
    }

    function get_url_by($index){
        $arr = [
            ['info'=>'/index.php/Mobile/Index/index','url_type'=>1],//商城首页              0
            ['info'=>'/index.php/Mobile/Cart/index','url_type'=>1],//购物车                1
            ['info'=>'/index.php/Mobile/activity/coupon_list','url_type'=>1],//优惠券中心    2
            ['info'=>'/index.php/Mobile/Team/index','url_type'=>1],//拼团中心               3
            ['info'=>'/index.php/Mobile/Activity/flash_sale_list','url_type'=>1],//限时秒杀 4
            ['info'=>'/index.php/Mobile/Goods/categoryList','url_type'=>1],//分类             5
            ['info'=>'/index.php/Mobile/User/index','url_type'=>1], //会员中心              6
            ['info'=>'/index.php/Mobile/Distribut/index','url_type'=>1],//我的分销          7
            ['info'=>'/index.php/Mobile/Goods/integralMall','url_type'=>1],//积分商城       8
            ['info'=>'/index.php/Mobile/Goods/ajaxSearch','url_type'=>1],//搜索               9
            ['info'=>'/index.php/Mobile/User/collect_list','url_type'=>1],//收藏               10
        ];
        return $arr[$index];
    }
    //获取开屏广告配置
    public function get_screen(){
        $is_screen=tpCache('shop_info.is_screen');
        $img=tpCache('shop_info.screen_ad');
        if($is_screen!=1){
            $this->ajaxReturn(['status' => 0, 'msg' => '功能已关闭', 'result' => '']);
        }
        if($is_screen==1 && (!$img)){
            $this->ajaxReturn(['status' => 0, 'msg' => '没有设置开屏广告图片', 'result' => '']);
        }
        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $img]);
    }


    //获取商品列表
    public function goods_list_block($data=array(),$count){
        // 13时轮播，传的是sql_where
        if($data['sql_where']){
            $sql_where = $data['sql_where'];
            if(!empty($sql_where['label']) && !isset($data['label'])){
                $data['label'] = $sql_where['label'];
            }
            if(!empty($sql_where['ids']) && !isset($data['ids'])){
                $data['ids'] = $sql_where['ids'];
            }
            if(!empty($sql_where['min_price']) && !empty($sql_where['max_price']) && $sql_where['min_price'] < $sql_where['max_price']){
                $data['min_price'] = $sql_where['min_price'];
                $data['max_price'] = $sql_where['max_price'];
            }
        }

        $block = new \app\common\logic\Block();
        $goodsList = $block->goods_list_block($data);
        return $goodsList;
    }

    //获取新闻列表
    public function get_new_list($num,$ids){
        $sql='is_open=1';
        if($ids){
            $sql.=' and article_id in('.$ids.')';
        }

        $list=Db::name('news')->where('is_open',1)->where('publish_time','<',time())->where($sql)->order('publish_time DESC')
            ->limit($num)->field('article_id,title,description,content,add_time,cat_id,link,thumb,publish_time')->select();

        foreach ($list as $k => $v) {
            $list[$k]['add_time']=date("Y-m-d",$v['publish_time']);
            $list[$k]['cat_name']=Db::name('news_cat')->where('cat_id='.$v['cat_id'])->getField('cat_name');
        }
        return $list;
    }

    // 来自手机端 获取拼团数据 /index.php/Mobile/Team/AjaxTeamList
    public function team_list()
    {
        $p = Input('p', 1);
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
        $team_where = ['a.status' => 1, 'a.is_lottery' => 0, 'a.deleted' => 0];
        if (count($goods_where) > 0) {
            $goods_ids = Db::name('goods')->where(['is_on_sale' => 1])->where($goods_where)->getField('goods_id', true);
            if (!empty($goods_ids)) {
                $team_where['i.goods_id'] = ['IN', $goods_ids];
            } else {
                return [];
                //$this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => '']);
            }
        }
        $TeamGoodsItem = new TeamGoodsItem();
        $team_goods_items = $TeamGoodsItem->alias('i')->join('__TEAM_ACTIVITY__ a', 'a.team_id = i.team_id')->with([
            'goods' => function ($query) {
                $query->field('goods_id,goods_name,shop_price');
            },
            'specGoodsPrice' => function ($query) {
                $query->field('item_id,price');
            }])->where($team_where)->field('* ,i.team_price as team_price, a.team_price as team_activity_price')->group('i.goods_id')->order('a.team_id desc')->page($p, 10)->select();
        return(collection($team_goods_items)
            ->append(['act_name','virtual_sales_num','follow_users_head_pic','add_time','bonus','buy_limit','deleted','goods','goods_id','goods_name','is_lottery','is_recommend'
                ,'item_id','needer','sales_sum','share_desc','share_img','share_title','sort','spec_goods_price','status','stock_limit','store_id','team_activity_price',
                'team_id','team_price','team_type','time_limit','virtual_num'])
            ->visible(['virtual_sales_num','follow_users_head_pic'])
            ->toArray());
    }

    //获取抢购数据
    public function get_flash_sale_goods(){
        $time_space = flash_sale_time_space();
        //dump($time_space);exit();

        $time_arr = $time_space[1];//获取当前时间节点的请购信息

        
        $goodsLogic = new GoodsLogic();
        $flash_sale_goods = $goodsLogic->getFlashSaleGoods(3 ,1 , $time_arr['start_time'], $time_arr['end_time']);
        $arr=array();
        $arr['time']=$time_arr;
        $arr['list']=$flash_sale_goods;

        return $arr;
    }

    /**
     * 门店列表
     * province,如果有省名，传省名字
     * lng,lat,search_radius，经伟度，查找半径范围内的门店
     */
    public function shopList(){
        $p = input('p',1);
        $data = input('param.');
        if(!empty($data['city_id'])){
            // 按选择的市id查
            $where['city_id'] = $data['city_id'];
        }elseif(!empty($data['city'])){
            // 一打开首页按定位的市名查
            $city_id = Db::name('region')->where('name',$data['city'])->value('id');
            if($city_id){
                $where['city_id'] = $city_id;
            }
        }

        $Store = new \app\common\model\Store();

        $where['deleted'] = 0;
        $where['store_recommend'] = 1;
        $where['store_state'] = 1;
        $where['longitude'] = ['>',0];
        $where['latitude'] = ['>',0];
        $store_list = $Store->where($where)->page($p,20)->select();
        if($store_list){
            $shop_list = collection($store_list)->visible(['store_id','store_name','store_logo','store_banner','province_id','city_id','district','longitude','latitude','store_address','seo_description','store_free_price','is_own_shop'])
                ->append(['avg_score','count_month_order','min','distance','coupon_max','active_name','active_num','free_price'])->toArray();
            array_multisort(array_column($shop_list,'distance'),SORT_ASC,$shop_list); // 按距离distance从小排
        }else{
            $shop_list = [];
        }
        $this->ajaxReturn(['status' => 1, 'result' => $shop_list]);
    }
    /**
     * 获取首页数据
     */
    public function homePage()
    {
        $new_ad = I('new_ad',0); 
        $goodsLogic = new GoodsLogic(); 
        if($new_ad == 1){
            //新版新增广告模式
            $banners =  $goodsLogic->getAppHomeAdv(true);
            foreach ($banners as $k => $v){
                if($v['media_type'] == 4){//如果是分类, 截取最后一个分类
                    $cats = explode('_',$v['ad_link']);
                    $count = count($cats);
                    if($count == 0)continue;
                    $v['ad_link'] = $cats[$count-1];
                    $banners[$k] = $v;
                }
            }
            $advs =  $goodsLogic->getAppHomeAdv(false);
            foreach ($advs as $k => $v){
                if($v['media_type'] == 4){//如果是分类, 截取最后一个分类
                    $cats = explode('_',$v['ad_link']);
                    $count = count($cats); 
                    if($count == 0)continue;
                    $v['ad_link'] = $cats[$count-1];
                    $advs[$k] = $v;
                }
            }
           
            $time_space = flash_sale_time_space();
            $time_arr = $time_space[1];//获取当前时间节点的请购信息
             
            $flash_sale_goods = $goodsLogic->getFlashSaleGoods(3 ,1 , $time_arr['start_time'], $time_arr['end_time']);
            $hot_goods = $goodsLogic-> getHotGood(1,10);
            $this->ajaxReturn(array(
                'status'=>1,
                'msg'=>'获取成功',
                'result'=>array(
                    'banner'=>$banners,
                    'ad'=>empty($advs) ? array() : $advs,
                    'flash_sale_goods' => $flash_sale_goods,
                    'hot_goods' => $hot_goods,
                    'server_time'=>time(),
                ),
            ));
        } 
        
        $promotion_goods = $goodsLogic->getPromotionGoods();
        $high_quality_goods = $goodsLogic->getRecommendGoods(1);
        $flash_sale_goods = $goodsLogic->getFlashSaleGoods(3);
        $new_goods = $goodsLogic->getNewGoods();
        $advs =  $goodsLogic->getHomeAdv();
        foreach ($advs as &$adv) {
            $adv['ad_code'] = SITE_URL.$adv['ad_code'];
        }
        $this->ajaxReturn(array(
            'status'=>1,
            'msg'=>'获取成功',
            'result'=>array(
               'promotion_goods'=>$promotion_goods,
               'high_quality_goods'=>$high_quality_goods,
               'flash_sale_goods' => $flash_sale_goods,
               'new_goods'=>$new_goods,
                'server_time'=>time(),
               'ad'=>$advs
            ),
        ));
    }
    
  
    /**
     * 推荐的商品列表
     */
    public function recommend()
    {
        $p = I('p/d',1);
        $goodsLogic = new GoodsLogic();
        $json = [
            'status'=>1,
            'msg'=>'获取成功',
            'result' => $goodsLogic->getRecommendGoods($p),
        ];
       $this->ajaxReturn($json);
    }

    /**
     * 猜你喜欢: 根据经纬度, 返回距离由近到远的商品
     */
    public function favourite()
    {
       $p = I('p',1);
        
        $lng =trim(I('lng/s',114.067345));  //经度
        $lat =trim(I('lat/s',22.632611));    //纬度   
  
        $count= Db::query("SELECT COUNT(store_id) as num  FROM __PREFIX__store WHERE store_state = 1");//正常店铺
        $Page=new Page($count[0]['num'],10);
        $firstRow = ($p-1)*10;
        $goods_list = Db::query("SELECT g.goods_id, goods_name,is_virtual,shop_price,cat_id3, s.store_id , ROUND(SQRT((POW((($lng - longitude)* 111),2))+ (POW((($lat - latitude)* 111),2))),2) AS distance FROM __PREFIX__goods AS g INNER JOIN __PREFIX__store AS s
                                            ON g.`store_id` = s.store_id  AND store_state=1 AND is_recommend=1 AND g.goods_state=1 AND  g.is_on_sale=1 ORDER BY distance ASC  LIMIT {$firstRow},{$Page->listRows} ");
        
        $json = array(
            'status'=>1,
            'msg'=>'获取成功',
            'result' => array(
                'favourite_goods'=>$goods_list,
            ),
        );    
        
       $this->ajaxReturn($json);
    }

    /**
     * 获取服务器配置
     */
    public function getConfig()
    {
        $data = M('plugin')->where("type='login' and code in ('weixin','qq')")->select();
        $arr = array();
        foreach($data as $k=>$v){
            unset( $data[$k]['config']);
        
			if(!$v['config_value']){
				$data[$k]['config_value'] = "";
			}else{
				$data[$k]['config_value'] = unserialize($v['config_value']);
			}
		 
            if($data[$k]['type'] == 'login'){
                $arr['login'][] =  $data[$k];
            }
        } 
        $is_block_index = M('mobile_template')->where('is_index=1')->count();
        $is_block_index=array('id'=>'','name'=>'is_block_index','value'=>$is_block_index,'inc_type'=>'','desc'=>'');
        
        $config_name = ['im_choose','qq', 'qq2', 'qq3', 'store_name', 'point_rate', 'phone',
            'address','hot_keywords', 'app_test', 'sms_time_out', 'regis_sms_enable', 
            'forget_pwd_sms_enable', 'bind_mobile_sms_enable','integral_use_enable' , 'wap_home_logo' ,  'auto_service_date'];
        $inc_type = ['ios','app'];
        $config = M('config')->where('name', 'IN', $config_name)->whereOr('inc_type' , 'IN' , $inc_type)->select();
	if(!$is_block_index){
            $is_block_index=0;
            $is_block_index=(string)$is_block_index;
        }
        $config[]=$is_block_index;
        $result = ['config' => $config] + $arr;
        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $result]);
    }

    /**
     * 店铺街
     * @author dyr
     * @time 2016/08/15
     * 根据百度坐标，获取周边商家
     *  $lng 经度
     *  $lat 纬度
     *  $scope 范围  千米
     *  $fourpoint
     * */
    public function store_street()
    {
        $sc_id = I('get.sc_id/d', '');
        $p = I('get.p',1);
        $lng =trim(I('lng/s',114.067345));  //经度
        $lat =trim(I('lat/s',22.632611));    //纬度
        $order = I('sale_order', 0);
        $search_key = I('search_key', 0);//搜索关键词
        $city = I('city', '');
          
        if($sc_id > 0){
            $storeWhere['sc_id'] = $sc_id;
        }
        $storeWhere['sc_id'] = $sc_id;
        if($order){
            $orderBy['store_sales'] = 'asc';
        }else{
            $orderBy['store_sales'] = 'desc';
        }
        
        $storeWhere = ['store_state' => 1,'deleted'=>0,'store_recommend'=>1];
         
        //查找城市对应的地区id
        if(!empty($city)){
            if(strpos($city,"市") > 0){
                $cityOr = str_replace('市','',$city);
            }else{
                $cityOr = $city.'市';
            }
            $cityRegionId = M('Region')->where(['name'=>$city])->whereOr(['name'=>$cityOr])->getField('id');
            //地区ID,目前搜索时只精确到市
            $storeWhere['city_id'] = $cityRegionId;
        }
        if(!empty($search_key)){
            $storeWhere['store_name'] = ['like' , "%$search_key%"];
        }
        $Store = new Store();
        $store_list = $Store->field('store_id,store_logo,store_avatar,store_name,store_collect,store_desccredit,province_id,city_id,district,store_servicecredit,longitude,latitude,store_deliverycredit,round(SQRT((POW((('.$lng.' - longitude)* 111),2))+  (POW((('.$lat.' - latitude)* 111),2))),2) AS distance')
            ->where($storeWhere)->page($p,10)->order($orderBy)->select();
        if($store_list){
            $store_list = collection($store_list)->toArray();
//            $distance = convert_arr_key($store_list,"store_id");
            //遍历获取店铺的四个商品数据
            foreach ($store_list as $key => $value) {
                $region = Db::name('region')->where('id','in',[$value['province_id'],$value['city_id'],$value['district']])->order('level asc')->select();
                $store_list[$key]['province_name'] = $region[0]['name'];
                $store_list[$key]['city_name'] = $region[0]['name'];
                $store_list[$key]['district_name'] = $region[0]['name'];
                $store_list[$key]['cartList'] = Db::name('goods')->field("goods_id,goods_name,shop_price,is_virtual")
                    ->where([ 'is_on_sale'=>1, 'goods_state'=>1,'store_id'=>$value['store_id']])->limit(4)->order('sort desc')->select();
                $store_list[$key]['store_count'] = Db::name('goods')->where(['store_id'=>$value['store_id']])->count();
                $log_id = Db::name('store_collect')
                    ->where(['user_id'=>$this->user_id,'store_id'=>$value['store_id']])->value('log_id');
                $store_list[$key]['is_collect'] = $log_id ? 1 : 0;
                if ($value['longitude']<=0 && $value['latitude']<=0){
                    $store_list[$key]['distance'] = 0;
                }
            }
        }

        $result['store_list'] = $store_list;

        if ($p <= 1) {
            $result['store_class'] = M('store_class')->field('sc_id,sc_name')->select();
            array_unshift($result['store_class'], ['sc_id' => 0, 'sc_name' => '全部分类']);

            //查找广告
            $start_time = strtotime(date('Y-m-d H:00:00'));
            $end_time = strtotime(date('Y-m-d H:00:00'));
            $adv = M("ad")->field(array('ad_link','ad_name','ad_code','media_type,pid'))->where("pid=535 AND enabled=1 and start_time< $start_time and end_time > $end_time")->find();
            if($adv && $adv['media_type'] == 4){//如果是分类, 截取最后一个分类
                $cats = explode('_',$adv['ad_link']);
                $count = count($cats);
                if($count != 0){
                    $adv['ad_link'] = $cats[$count-1];
                }
            }

            $result['ad'] = empty($adv) ? "" : $adv ;
        }

        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $result]);
    }

    /**
     * 小程序的店铺街
     */
    public function store_street_list()
    {
        $p = I('p',1);
        $sc_id = I('get.sc_id/d',0);
        $province_id = I('province_id', 0);
        $city_id = I('city_id', 0);
        $order = I('sale_order', 0);

        //地区ID,目前搜索时只精确到市
        $storeWhere = [];
        if($province_id){
            $storeWhere['province_id'] = $province_id;
        }
        if($city_id){
            $storeWhere['city_id'] = $city_id;
        }
        if($sc_id > 0){
            $storeWhere['sc_id'] = $sc_id;
        }
        if($order){
            $orderBy['store_sales'] = 'asc';
        }else{
            $orderBy['store_sales'] = 'desc';
        }
        $Store = new Store();
        $store_list = $Store->where($storeWhere)->order($orderBy)->select();
        foreach($store_list as $storeKey=>$storeVal){
            $store_list[$storeKey]['cartList'] = Db::name('goods')->field("goods_id,goods_name,shop_price,is_virtual")
                ->where([ 'is_on_sale'=>1, 'goods_state'=>1,'store_id'=>$storeVal['store_id']])->limit(4)->order('sort desc')->select();
            $store_list[$storeKey]['store_count'] = Db::name('goods')->where(['store_id'=>$storeVal['store_id']])->count();
            $region = Db::name('region')->where('id','in',[$storeVal['province_id'],$storeVal['city_id'],$storeVal['district']])->order('level asc')->select();
            $store_list[$storeKey]['province_name'] = $region[0]['name'];
            $store_list[$storeKey]['city_name'] = $region[0]['name'];
            $store_list[$storeKey]['district_name'] = $region[0]['name'];
            $log_id = Db::name('store_collect')
                ->where(['user_id'=>$this->user_id,'store_id'=>$storeVal['store_id']])->value('log_id');
            $store_list[$storeKey]['is_collect'] = $log_id ? 1 : 0;
            $store['distance'] = 0;
        }

        $result['store_list'] = $store_list;
        
        if ($p <= 1) {
            $result['store_class'] = M('store_class')->field('sc_id,sc_name')->select();
            array_unshift($result['store_class'], ['sc_id' => 0, 'sc_name' => '全部分类']);
            $result['ad'] = M('ad')->field(['ad_link','ad_name','ad_code'])->where('pid', 2)->cache(true, TPSHOP_CACHE_TIME)->find();
        }
        
        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $result]);
    }

    /**
     * 店铺分类
     */
    public function store_class()
    {
        $store_class = M('store_class')->field('sc_id,sc_name')->select();
        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $store_class]);
    }

    /**
     * 品牌街
     * @author dyr
     * @time 2016/08/15
     */
    public function brand_street()
    {
        $p = I('get.p', 1);
        
        $brand_list = M('brand')->field('id,name,logo,url')
                ->where(['is_hot' => 1])
                ->order(['sort' => 'desc', 'id' => 'asc'])
                ->where('status', 0)
                ->page($p, 30)
                ->select();
        $result['brand_list'] = $brand_list;
        
        if ($p <= 1) {
            $goodsLogic = new GoodsLogic();
            //查找广告
            $start_time = strtotime(date('Y-m-d H:00:00'));
            $end_time = strtotime(date('Y-m-d H:00:00'));
            $adv = M("ad")->field(array('ad_link','ad_name','ad_code','media_type,pid'))->where("pid=533 AND enabled=1 and start_time< $start_time and end_time > $end_time")->find();
            if($adv && $adv['media_type'] == 4){//如果是分类, 截取最后一个分类
                    $cats = explode('_',$adv['ad_link']);
                    $count = count($cats);
                    if($count != 0){
                        $adv['ad_link'] = $cats[$count-1];
                    }
             }
        
            $result['ad'] = empty($adv) ? "" : $adv ;
            $result['hot_list'] = $goodsLogic->getBrandGoods(12);
        }

        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $result]);
    }

    /**
     * 获取区域地址列表，region_id=0是获取所有省份
     */
    public function get_region()
    {
        $parent_id = I('get.parent_id/d', 0);
        $data = M('region')->field('id,name')->where("parent_id", $parent_id)->select();
        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $data]);
    }
    /**
     * 智能表单提交
     */
    public function save_form(){
        $block = new \app\common\logic\Block();
        $data = $block->add_form(input('param.'));
        $this->ajaxReturn($data);
    }

    public function momentsList()
    {
        if($this->user_id){
            $data = [
                'user_id' => $this->user_id,
            ];
        }


        // 数据验证
        $validate = \think\Loader::validate('Moments');

        if (!$validate->batch()->scene('momentsList')->check($data)) {
            $this->ajaxReturn(['status' => -1, 'msg' => implode(',',$validate->getError())]);
        }

        $return = MomentsLogic::select_moments($data);
        $this->ajaxReturn($return);

    }

    /**
     * 关注
     */
    public function ajaxAttention(){

        if(I('id') == $this->user_id){
            $this->ajaxReturn(['status' => 0, 'msg' => '不能关注自己哦']);
        }
        $where['user_id']=$this->user_id;
        $where['att_user_id']=I('id/d');
        $row=Db::name('user_attention')->where($where)->count();
        if($row){
            Db::name('user_attention')->where($where)->delete();
            $this->ajaxReturn(['status' => 0, 'msg' => '成功','state'=>'关注']);
        }
        else{
            Db::name('user_attention')
                ->save(['user_id'=>$this->user_id,
                    'att_user_id'=>I('id/d'),
                    'add_time'=>time()]);
            $this->ajaxReturn(['status' => 1, 'msg' => '成功','state'=>'已关注']);
        }
    }

    /**
     * pc 只能单个上传图片
     */
    function upImg()
    {
        $moments_imgs = MomentsLogic::uploadMomentsImg();
        $this->ajaxReturn($moments_imgs);
    }

    /**
     * 发表朋友圈
     */
    public function addMoments()
    {
        if (!request()->isPost()) {
            $this->ajaxReturn(['status' => -101, 'msg' => '请用post请求！！']);
        }
        $city=I('city');
        $city_id=$city_id = Db::name('region')->where(['name'=>$city])->getField('id');
        //$moments_imgs = MomentsLogic::uploadMomentsImg();
        $data = [
            'user_id' => $this->user_id,
            'title'=>I('post.title',''),
            'classify_id'=>I('post.classify_id/d',0),
            'moments_imgs' => I('momrnyd_imgs'),
            'moments_content' => I('post.moments_content', ''), //内容
            'add_time' => time(),
            'city_id'=>$city_id
        ];
        // 数据验证
        $validate = new Moments();//\think\Loader::validate('Moments');
        if (!$validate->batch()->scene('addMoments')->check($data)) {
            $this->ajaxReturn(['status' => -1, 'msg' => implode(',',$validate->getError())]);
        }

        if($data['moments_imgs']){
            $data['moments_imgs'] = trim($data['moments_imgs'],',') ;
        }else{
            $moments_imgs = MomentsLogic::uploadMomentsImg();
            if ($moments_imgs['status'] == -1) {
                $this->ajaxReturn($moments_imgs);
            }
            $data['moments_imgs'] = $moments_imgs['result'];
        }

        if($data['moments_imgs']=='' && $data['moments_content']==''){
            $this->ajaxReturn(['status' => -1, 'msg' =>'图片和内容必须存在一个']);
        }

        $return = MomentsLogic::add_moments($data);
        $this->ajaxReturn($return);
    }
    /*
     * 点赞
     * */
    public function addLike()
    {
        if (!request()->isPost()) {
            $this->ajaxReturn(['status' => -101, 'msg' => '请用post请求！！']);
        }
        $data = [
            'user_id' => $this->user_id,
            'moments_id' => I('post.moments_id/d'),
            'add_time' => time(),
        ];

        // 数据验证
        $validate = new Moments();//\think\Loader::validate('Moments');

        if (!$validate->batch()->scene('addLike')->check($data)) {
            $this->ajaxReturn(['status' => -1, 'msg' => implode(',',$validate->getError())]);
        }
        $return = MomentsLogic::add_like($data);
        $this->ajaxReturn($return);

    }
    /*
     * 获得一条动态
     * */
    public function seeFindMoments()
    {
        if (!request()->isPost()) {
            $this->ajaxReturn(['status' => -1, 'msg' => '请用post请求！！']);
        }

        // 数据验证
        $validate = new Moments();//\think\Loader::validate('Moments');

        if (!$validate->batch()->scene('delMoments')->check(I('post.'))) {
            $this->ajaxReturn(['status' => -1, 'msg' => implode(',',$validate->getError())]);
        }
        $user_id = I('post.uid/d',0);

        if($user_id == $this->user_id || $user_id==0){
            //查看自己状态
            $data = [
                'user_id' => $this->user_id,//用来查询数据
                'moments_id' => I('post.moments_id/d'),
                'order_user_id' => $this->user_id,//拿来对比
                'type' => 1,
            ];
        }else{
            //查看别人状态
            $data = [
                'user_id' => $user_id,//用来查询数据
                'moments_id' => I('post.moments_id/d'),
                'order_user_id' => $this->user_id,//拿来对比
                'type' => 0,
            ];
        }

        $return = MomentsLogic::see_find_moments($data);
        $this->ajaxReturn($return);

    }

    public function getClassifySort(){
        $result=Db::name('moments_classify')->order('sort_order','ASC')->select();
        if($result){
            $result[0]['is_first']=1;
            $this->ajaxReturn(array('status'=>1,'result'=>$result));
        }else{
            $this->ajaxReturn(array('status'=>0,'result'=>[]));
        }
    }

    public function getProtocol(){
        $result=Db::name('system_article')->where(['doc_title'=>['like','%用户服务协议%']])->find();
        if($result){
            $system_activity['doc_title']=$result['doc_title'];
            $system_activity['doc_content']=htmlspecialchars_decode($result['doc_content']);
            $this->ajaxReturn(array('status'=>1,'result'=>$system_activity));
        }else{
            $this->ajaxReturn(array('status'=>0,'result'=>[]));
        }
    }

    /**
     * 獲取某人或者自己所有动态列表
     */
    public function seeAllMoments()
    {
        if (!request()->isPost()) {
            $this->ajaxReturn(['status' => -101, 'msg' => '请用post请求！！']);
        }

        // 数据验证
        $validate = new Moments();//\think\Loader::validate('Moments');
        if (!$validate->batch()->scene('momentsList')->check(I('post.'))) {
            $this->ajaxReturn(['status' => -1, 'msg' => implode(',',$validate->getError())]);
        }

        $user_id = I('post.uid/d',0);

        if($user_id == $this->user_id || $user_id==0){
            //查看自己状态
            $data = [
                'user_id' => $this->user_id,
                'type' => 1,
            ];
        }else{
            //查看别人状态
            $data = [
                'user_id' => $user_id,
                'type' => 0,
            ];
        }

        $return = MomentsLogic::see_all_moments($data);
        $this->ajaxReturn($return);

    }

    /**
     * 个人中心
     */
    public function personage()
    {
        $user_id = I('id')?I('id'):$this->user_id;
        $userObj = new UsersLogic();

        $user = Db::name('users')->where(['user_id'=>  $user_id ])->field(['nickname','manifesto','head_pic'])->find();
        $result['attention_count'] = $userObj->getAttentionCount($user_id);
        $result['fans_count'] = $userObj->getFansCount($user_id);
        //登录用户是否已关注该用户
        $check = Db::name('user_attention')->where(['user_id'=>$this->user_id,'att_user_id'=>$user_id])->count();
        //获取该参与开店地区位置
        $city_name = Db::name('store')->alias('a')->where(['a.user_id'=>$user_id])->join('tp_region b','a.city_id = b.id','LEFT')->find();
        $result['city_name'] = $city_name['name'];
        $result['attention'] = $check ? 1 : 0;
        $this->ajaxReturn(['status'=>1,'result'=>$result,'user'=>$user]);
    }

    public function getComment (){
            if($this->user_id){
                $where = "(c.user_id=:id and c.status=:userStatus and c.moments_id=:userMoments_id and is_delete=:userDelete)or is_delete=:delete and c.moments_id=:moments_id and c.status=:status";
                $bind = ['id' => $this->user_id, 'userStatus' => MobileMoment::$STATUS_WAIT, 'userMoments_id' => I('moments_id'),
                    'userDelete' => MobileMoment::$DETELE_NO, 'delete' => MobileMoment::$DETELE_NO, 'moments_id' => I('moments_id'), 'status' => MobileMoment::$STATUS_SUCCESS];
            }else{
                $where = "is_delete=:delete and c.moments_id=:moments_id and c.status=:status ";
                $bind = [ 'delete' => MobileMoment::$DETELE_NO, 'moments_id' => I('moments_id'), 'status' => MobileMoment::$STATUS_SUCCESS];
            }
            $getComment = M('moments_comment')
                ->alias('c')
                ->where($where)
                ->bind($bind)
                ->join('__USERS__ u', 'u.user_id = c.user_id')
                ->field('comment_id,comment_content,p_name,pid,c.user_id,nickname,head_pic,add_time')
                ->order('add_time asc')
                ->select();
            if($getComment){
                $this->ajaxReturn(['status'=>1,'comment'=>$getComment]);
            }
            $this->ajaxReturn(['status'=>0,'comment'=>[]]);
    }

    public function getMenu(){
        $menu=Db::name('menu_mp')->where(['is_show'=>1])->select();
        if($menu){
            $this->ajaxReturn(['status'=>1,'menu'=>$menu,'msg'=>'加载成功']);
        }else{
            $this->ajaxReturn(['status'=>0,'menu'=>[],'msg'=>'加载失败']);
        }
    }


}