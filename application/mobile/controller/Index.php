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
 * $Author: 当燃 2016-01-09
 */

namespace app\mobile\controller;

use app\common\logic\ActivityLogic;
use think\Cookie;
use think\Db;
use think\Page;
use app\common\model\Store;
use app\common\logic\wechat\WechatUtil;
use app\common\model\team\TeamGoodsItem;
class Index extends MobileBase
{

    public function index()
    {
		$edit_ad = input('edit_ad');
        $diy_index = M('mobile_template')->where('is_index=1')->field('template_html,block_info')->find();

        $noob_gift=Db::name('coupon')->where(['type'=>4,'send_start_time'=>array('<=',time()),'send_end_time'=>array('>=',time()),'status'=>'1'])->order('add_time','desc')->limit(2)->select();
        $order=Db::name('order')->where(['user_id'=>Cookie('user_id')])->count();
        $user=Db::name('users')->where(['reg_time'=>array('>',(time()-604800))])->where(['user_id'=>Cookie('user_id')])->count();
        $coupon_list=Db::name('coupon_list')->where(['uid'=>cookie('user_id')])->count();
        if($order==0 and $user and $coupon_list==0){
            $this->assign('noob_gift',$noob_gift);
        }
        if($diy_index){
            // 启用广告时，不能用自定义的
            $edit_ad = input('edit_ad');
            if($edit_ad){
                $this->error("请在自定义页面编缉首页", SITE_URL . '/index.php?m=Admin&c=Block&a=pageList');
            }
            $html = htmlspecialchars_decode($diy_index['template_html']);
            $logo=tpCache('shop_info.wap_home_logo');
            $this->assign('wap_logo',$logo);
            $this->assign('html',$html);
            $this->assign('is_index',"1");
            $this->assign('info',$diy_index['block_info']);
            return $this->fetch('index2');
            exit();
        }



        $TeamGoodsItem = new TeamGoodsItem();//首页拼团
        $whereItem=['a.status' => 1, 'a.is_lottery' => 0, 'a.deleted' => 0];
        $team_goods_items = $TeamGoodsItem->alias('i')->join('__TEAM_ACTIVITY__ a', 'a.team_id = i.team_id')->with([
            'goods' => function ($query) {
                $query->field('goods_id,goods_name,shop_price');
            },
            'specGoodsPrice' => function ($query) {
                $query->field('item_id,price');
            }])->field('* ,i.team_price as team_price, a.team_price as team_activity_price')->group('i.goods_id')->where($whereItem)->order('a.team_id desc')->limit(20)->select();//->cache(true, TPSHOP_CACHE_TIME)->select();


        $this->assign('team_goods_items',$team_goods_items);

        $hot_goods = M('goods')->where("is_hot=1 and is_on_sale=1 and goods_state=1")->order('goods_id DESC')->limit(20)->cache(true, TPSHOP_CACHE_TIME)->select();//首页热卖商品
        $thems = M('goods_category')->where('level=1')->order('sort_order')->limit(9)->cache(true, TPSHOP_CACHE_TIME)->select();
        $this->assign('thems', $thems);
        $this->assign('hot_goods', $hot_goods);
        $favourite_goods = M('goods')->where("is_recommend=1 and is_on_sale=1 and goods_state=1")->order('sort DESC')->limit(20)->cache(true, TPSHOP_CACHE_TIME)->select();//首页推荐商品


        //秒杀商品
        $now_time = time();  //当前时间
        if (is_int($now_time / 7200)) {      //双整点时间，如：10:00, 12:00
            $start_time = $now_time;
        } else {
            $start_time = floor($now_time / 7200) * 7200; //取得前一个双整点时间
        }
        $end_time = $start_time + 7200;   //结束时间
        $flash_sale_list = M('goods')->alias('g')
            ->field('g.goods_id,f.price,s.item_id,f.title')
            ->join('__FLASH_SALE__ f', 'g.goods_id = f.goods_id', 'LEFT')
            ->join('__SPEC_GOODS_PRICE__ s', 's.prom_id = f.id AND g.goods_id = s.goods_id', 'LEFT')
            ->where('f.status', 1)
            ->where("f.start_time >= $start_time and f.end_time <= $end_time and f.recommend=1")
            ->limit(3)->select();

        $this->assign('flash_sale_list', $flash_sale_list);
        $this->assign('start_time', $start_time);
        $this->assign('end_time', $end_time);
        $this->assign('favourite_goods', $favourite_goods);
		$this->assign('edit_ad', $edit_ad);
        return $this->fetch();
    }

    public function index2(){
        $id=I('post.id/d');
        if($id){
            $arr=db('mobile_template')->where(['id'=>$id])->field('template_html,block_info')->find();
        }else{
            $arr=db('mobile_template')->where(['is_index'=>1])->field('template_html,block_info')->find();
        }

        $html=htmlspecialchars_decode($arr['template_html']);
        $this->assign('html',$html);
        $this->assign('info',$arr['block_info']);
        return $this->fetch();
    }

    //商品列表板块参数设置
    public function goods_list_block(){
        $data=I('post.');
        $sql_where = $_POST['sql_where']; // 用input无法接收
        // 13时，轮播传的是sql_where
        if($sql_where){
            // 传goods指定商品id，多个12,23,34
            if(!empty($sql_where['goods'])){
                $data['goods'] = $sql_where['goods'];
            }
            if(!empty($sql_where['label']) && !isset($data['label'])){
                $data['label'] = $sql_where['label'];
            }
            // 商品分类id
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

        $html='';
        if($data['block_type']==13){
            foreach ($goodsList as $k => $v) {
                $html.='<div class="containers-slider-item">';
                 $html.='<a href="/Mobile/Goods/goodsInfo/id/'.$v["goods_id"].'"></a>';
                $html.='<div class="seckill-item-img">';
                $html.='<a href="/Mobile/Goods/goodsInfo/id/'.$v["goods_id"].'"><img src="'.$v["original_img"].'" /></a>';
				if($v['activity']['prom_title']){
                    $html .=' <div class="prom_title1">'.$v['activity']['prom_title'].'</div>';
                }
                $html.='</div>';
                $html.='<div class="seckill-item-name"><p>'.$v["goods_name"].'</p></div>';
                $html.='<div class="seckill-item-price" class="p"><span class="fl">￥<em>'.($v['activity']['prom_price']?$v['activity']['prom_price']:$v['shop_price']).'</em></span>';
                $html.='</div></div>';
            }
        }else{
            foreach ($goodsList as $k => $v) {
                $num = $v['sales_sum']+$v['virtual_sales_sum'];
                $html.='<li>';
                $html.='<a class="tpdm-goods-pic" href="/Mobile/Goods/goodsInfo/id/'.$v["goods_id"].'"><img src="'.$v["original_img"].'" alt="" /></a>';
                if($v['activity']['prom_title']){
                    $html .=' <div class="prom_title">'.$v['activity']['prom_title'].'</div>';
                }
				$html.='<a href="/Mobile/Goods/goodsInfo/id/'.$v["goods_id"].'" class="tpdm-goods-name">'.$v["goods_name"].'</a>';
                $html.= $v['label_name'] ? '<span class="rx-sp">'.$v['label_name'].'</span>' :  '<span class="rx-sp"  style="height: 0.747rem;border:none"></span>';
                $html.='<div class="tpdm-goods-des">';
                $html.='<div class="tpdm-goods-price">￥'.($v['activity']['prom_price']?$v['activity']['prom_price']:$v['shop_price']).'</div>';
                $html.='<a class="tpdm-goods-like">已售出'.$num.'件</a>';
                $html.='</div>';
                $html.='</li>';
            }
        }
        $this->ajaxReturn(['status' => 1, 'msg' => '成功', 'result' =>$html,'data'=>$data,'goods_list'=>$goodsList]);
    }

    /**
     * 智能表单提交
     */
    public function save_form(){
        $block = new \app\common\logic\Block();
        $data = $block->add_form(input('post.'));
        $this->ajaxReturn($data);
    }


    //自定义页面获取秒杀商品数据
    public function get_flash(){
        $now_time = time();  //当前时间
        if(is_int($now_time/7200)){      //双整点时间，如：10:00, 12:00
            $start_time = $now_time;
        }else{
            $start_time = floor($now_time/7200)*7200; //取得前一个双整点时间
        }
        $end_time = $start_time+7200;   //结束时间
        $flash_sale_list = M('goods')->alias('g')
            ->field('g.goods_id,g.shop_price,f.price,s.item_id,f.title')
            ->join('flash_sale f','g.goods_id = f.goods_id','LEFT')
            ->join('__SPEC_GOODS_PRICE__ s','s.prom_id = f.id AND g.goods_id = s.goods_id','LEFT')
            ->where("start_time >= $start_time and end_time <= $end_time and f.recommend=1")
            ->where('f.status', 1)
            ->limit(3)->select();
        $str='';
        if($flash_sale_list){
            foreach ($flash_sale_list as $k => $v) {
                $str.='<a href="'.U('Mobile/Activity/flash_sale_list').'">';
                $str.='<img src="'.goods_thum_images($v['goods_id'],200,200) .'" alt="" />';
                $str.='<p>'.$v['title'].'</p>';
                $str.='<span>￥'.$v['price'].'</span>';
                $str.='<span class="new_span">￥'.$v['shop_price'].'</span>';
            }
        }
        $time=date('H',$start_time);
        $this->ajaxReturn(['status' => 1, 'msg' => '成功','html' => $str, 'start_time'=>$time, 'end_time'=>$end_time]);
    }

    /**
     * 分类列表显示
     */
    public function categoryList()
    {
        return $this->fetch();
    }

    /**
     * 模板列表
     */
    public function mobanlist()
    {
        $arr = glob("D:/wamp/www/svn_tpshop/mobile--html/*.html");
        foreach ($arr as $key => $val) {
            $html = end(explode('/', $val));
            echo "<a href='http://www.php.com/svn_tpshop/mobile--html/{$html}' target='_blank'>{$html}</a> <br/>";
        }
    }

    /**
     * 商品列表页
     */
    public function goodsList()
    {
        $id = I('get.id/d', 0); // 当前分类id
        $lists = getCatGrandson($id);
        $this->assign('lists', $lists);
        return $this->fetch();
    }

    public function ajaxGetMore()
    {
        $p = I('p/d', 1);
        $where = [
            //'is_recommend' => 1,
            'is_on_sale' => 1,
            'goods_state' => 1,
            'virtual_indate' => ['exp', ' = 0 OR virtual_indate > ' . time()],
            'exchange_integral'=>0
        ];
        $favourite_goods = Db::name('goods')->where($where)->order('sort DESC,goods_id DESC')->page($p, 10)->cache(true, TPSHOP_CACHE_TIME)->select();//首页推荐商品
        $this->assign('favourite_goods', $favourite_goods);
        echo $this->fetch();
    }



    /**
     * 店铺街
     * @author dyr
     * @time 2016/08/15
     */
    public function street()
    {
        $store_class = M('store_class')->select();
        $this->assign('store_class', $store_class);//店铺分类
        return $this->fetch();
    }

    /**
     * ajax 获取店铺街
     */
    public function ajaxStreetList()
    {
        $sc_id = I('sc_id/d', 0);
        $province_id = I('province_id');
        $city_id = I('city_id');
        $district_id = I('district_id');
        $order = I('order', 0);
        $storeWhere = ['store_state' => 1,'deleted'=>0,'store_recommend'=>1];
        $orderBy = [];

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
            $orderBy['store_id'] = 'desc'; //store_sales
        }else{
            $orderBy['store_id'] = 'asc';
        }
        $p = input('p/d',1);
        $store_list = Db::name('store')
            ->field("store_id,store_avatar,store_name,store_collect,store_desccredit,store_servicecredit,store_deliverycredit")
            ->where($storeWhere)->order($orderBy)->page($p)
            ->select();
        if($store_list){
            $store_list = collection($store_list)->toArray();
            foreach ($store_list as $key => $value) {
                $store_list[$key]['goods_list'] = Db::name('goods')->field("goods_id,goods_name,shop_price,is_virtual")
                    ->where(['is_on_sale'=>1, 'goods_state'=>1,'store_id'=>$value['store_id']])->limit(3)->order('sort desc')->select();
                if(cookie('user_id') > 0){
                    $store_list[$key]['log_id'] = Db::name('store_collect')
                        ->where(['user_id'=>cookie('user_id'),'store_id'=>$value['store_id']])->value('log_id');
                }

            }
        }
        $this->assign('province_id', $province_id);
        $this->assign('city_id', $city_id);
        $this->assign('district_id', $district_id);
        $this->assign('store_list', $store_list);
        echo $this->fetch();

    }

    /**
     * 品牌街
     * @author dyr
     * @time 2016/08/15
     */
    public function brand()
    {
        $brand_where['status'] = 0;
        $brand_where['is_hot'] = 1;
        $goods = M('goods')->field('goods_id,shop_price,market_price')->where(['is_on_sale' => 1, 'is_recommend' => 1])->limit(12)->order('sort desc')->select();
        $brand_list = M('brand')->field('id,name,logo,url')->order(array('sort' => 'desc'))->cache(true)->where($brand_where)->select();
        for ($i = 0; $i < 3; $i++) {
            $Goods_group[] = array_slice($goods, $i * 3, 3);//每三个一组，取三组
            if (!empty($Goods_group[$i])) { //去掉空的
                $recommendGoods = $Goods_group;
            }
        }
        $this->assign('brand_list', $brand_list);//品牌列表
        $this->assign('recommendGoods', $recommendGoods);//品牌列表
        return $this->fetch();
    }

    /**
     * 门店列表
     * province,查挡前市的所有门店，如果没有查到就查全国的。
     * lng,lat,search_radius，经伟度，查找半径范围内的门店
     *
     */
    public function shopList(){
        $p = input('p',1);
        $data = input('param.');
        if(isset($data['city'])){
            $city_id = Db::name('region')->where('name',$data['city'])->value('id');
            // 按市名查，有些查不到，用like
            if(!$city_id){
                $city_id = Db::name('region')->where('name','like',mb_substr($data['city'], 0, 2,'utf-8').'%')->value('id');
            }
            if($city_id){
                $where['city_id'] = $city_id;
            }
        }
        $Store = new Store();

        $where['deleted'] = 0;
        $where['store_recommend'] = 1;
        $where['store_state'] = 1;
        $where['longitude'] = ['>',0];
        $where['latitude'] = ['>',0];
        $store_list = $Store->where($where)->page($p,20)->select();
        if(!$store_list){
            // 找不到，找全国的
            if(isset($where['city_id'])) unset($where['city_id']);
            if(isset($where['province_id'])) unset($where['province_id']);
            unset($where['longitude']);
            unset($where['latitude']);
            $store_list = $Store->where($where)->page($p,20)->select();
        }
        if($store_list){
            $shop_list = collection($store_list)->visible(['store_id','store_name','store_logo','store_banner','province_id','city_id','district','longitude','latitude','store_address','seo_description','store_free_price','is_own_shop'])
                ->append(['avg_score','count_month_order','min','distance','coupon_max','active_name','active_num','free_price'])->toArray();
            array_multisort(array_column($shop_list,'distance'),SORT_ASC,$shop_list); // 按距离distance从小排
        }else{
            $shop_list = [];
        }
        $this->ajaxReturn(['status' => 1, 'result' => $shop_list]);
    }
    public function newsList(){
        $ids = input('ids');
        if($ids){
            $ids_arr = explode(',',$ids);
            $where['article_id'] = ['in', $ids_arr];
        }
        $num = input('new_num/d', 2);
        $num = $num > 10 ? $num : $num;
        $where['publish_time'] = ['elt',time()];
        $where['is_open'] = 1;
        $list= Db::view('news')
            ->view('newsCat','cat_name','newsCat.cat_id=news.cat_id','left')
            ->where($where)
            ->order('publish_time DESC')
            ->limit($num)
            ->select();
        foreach($list as $k=>$v){
            $list[$k]['content'] = '<p>'.cutstr_html(htmlspecialchars_decode($list[$k]['content']),60).'</p>';
            if(strpos($v['thumb'],'/public') === 0 ){
                if(!file_exists('.'.$v['thumb'])){
                    $list[$k]['thumb'] = '/public/images/icon_goods_thumb_empty_300.png';
                }
            }elseif(empty($v['thumb'])){

                $list[$k]['thumb'] = '/public/images/icon_goods_thumb_empty_300.png';
            }
        }
        $this->ajaxReturn(['status' => 1, 'result' => $list]);
    }
    public function news_list(){
        return $this->fetch();
    }
    public function ajax_news_list(){
        $page = input('page/d', 1);
        $where['publish_time'] = ['elt',time()];
        $where['is_open'] = 1;
        $list= Db::view('news')
            ->view('newsCat','cat_name','newsCat.cat_id=news.cat_id','left')
            ->where($where)
            ->order('publish_time DESC')
            ->page($page, 10)
            ->select();
        foreach($list as $k=>$v){
            $list[$k]['content'] =  '<p>'.cutstr_html(htmlspecialchars_decode($list[$k]['content']),60).'</p>';
        }
        $this->ajaxReturn(['status' => 1, 'result' => $list]);
    }


    //微信Jssdk 操作类 用分享朋友圈 JS
    public function ajaxGetWxConfig()
    {
        $askUrl = input('askUrl');//分享URL
        $askUrl = urldecode($askUrl);

        $wechat = new WechatUtil;
        $signPackage = $wechat->getSignPackage($askUrl);
        if (!$signPackage) {
            exit($wechat->getError());
        }

        $this->ajaxReturn($signPackage);
    }
    
    /**
     * APP下载地址, 如果APP不存在则显示WAP端地址
     * @return \think\mixed
     */
    public function app_down(){
         
        $server_host = 'http://'.$_SERVER['HTTP_HOST'];
        $showTip = false;
        if(tpCache('ios.app_path') && strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
            //苹果:直接指向AppStore下载
            $down_url = tpCache('ios.app_path');
        }else if(tpCache('android.app_path') && strpos($_SERVER['HTTP_USER_AGENT'], 'Android')){
            // 安卓:需要拼接下载地址
            $down_url = $server_host.'/'.tpCache('android.app_path');
            //如果是安卓手机微信打开, 则显示"其他浏览器打开"提示
            (strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') && strpos($_SERVER['HTTP_USER_AGENT'], 'Android')) && $showTip = true;
        } 
        
        $wap_url = $server_host.'/Mobile';
       /*  echo "down_url : ".$down_url;
        echo "wap_url : ".wap_url;
        echo "<br/>showTip : ".$showTip; */
        $this->assign('showTip' , $showTip);
        $this->assign('down_url' , $down_url);
        $this->assign('wap_url' , $wap_url);
        return $this->fetch();
    }

    /**
     * 首页点击地区切换显示地区
     * @return mixed
     */
    public function ajaxLocation(){
        return $this->fetch();
    }
}