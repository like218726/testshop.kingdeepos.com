<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * Author: IT宇宙人
 * Date: 2015-09-09
 */

namespace app\common\logic;


use app\common\model\Cart;
use app\common\model\Coupon;
use app\common\model\Goods;
use app\common\model\Store;
use app\common\util\TpshopException;
use think\Cache;
use think\Loader;
use think\Db;
use think\Page;

/**
 * 店铺o2o 逻辑定义
 * Class CatsLogic
 * @package common\Logic
 */
class StoreLogic
{

    protected $store_id;
    protected $user_id;
    protected $shop_class_id;

//    public function __construct()
//    {
//        parent::__construct();
////        $this->session_id = session_id();
//    }

    /**
     * @param $value
     * @throws TpshopException
     */
    public function setStoreId($value)
    {
        $this->store_id = $value;
    }

    /**
     * @param $value
     * @throws TpshopException
     */
    public function setUserId($value)
    {
        $this->user_id = $value;
    }

    public function setShopClassId($value)
    {
        $this->shop_class_id = $value;
    }

    /**
     * 获取商家店铺列表
     * @param $data
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function storeList($data)
    {
        $store = Loader::validate('Store');
        if (!$store->batch()->check($data)) {
            return ['status' => 0, 'msg' => '认证失败', 'result' => $store->getError()];
        }
        $longitude = $data['longitude'];
        $latitude = $data['latitude'];
        $ak = tpCache('basic.lbsyun'); //Sgg73Hgc2HizzMiL74TUj42o0j3vM5AL
        if($data['region_id']){
            $city_id = $data['region_id'];
            $city_name= db('region')->where(['id'=>$city_id ])->value('name');
        }else{
            $url = 'http://api.map.baidu.com/geocoder/v2/?callback=renderReverse&location='.$latitude.','.$longitude.'&output=json&latest_admin=1&ak='.$ak;
            $result = httpRequest($url, "get");
            $result = str_replace("renderReverse&&renderReverse(","",$result);
            $result = json_decode(trim($result,')'),true);
            if(0 != $result['status']){ return ['status'=>0,'msg'=>'获取不到用户的定位城市','result'=>[]];}
            $city_name = $result['result']['addressComponent']['city'];
            $city_id = db('region')->where(['name'=>$result['result']['addressComponent']['city']])->value('id');
            
        }
        $where = [
            'is_on_sale' => 1,
            'goods_state' => 1,
            'virtual_indate' => ['exp', ' = 0 OR virtual_indate > ' . time()],
            'exchange_integral'=>0
        ];
        $where = ['deleted' => 0, 'store_state' => 1];
        if($city_id){
            $where['city_id'] = $city_id;
        }
        //计算门店距离
        $field = 'round(SQRT((POW(((' . $longitude . ' - longitude)* 111),2))+  (POW(((' . $latitude . ' - latitude)* 111),2))),2) AS distance';
        $order = 'distance ASC';
        $where['longitude'] = ['>',0];
        $where['latitude'] = ['>',0];
        $shop = M('shop');
        $count = $shop->field($field)->where($where)->count();
        $Page = new Page($count, 10);
        $shop_list = $store->field($field)->where($where)->limit($Page->firstRow,$Page->listRows)->order($order)->select();
        if ($shop_list) {
            $shop_list = collection($shop_list)->append(['activity'])->toArray();
            foreach ($shop_list as $k=>$v)
            {
                $origin = $latitude . ',' . $longitude;
                $destination = $v['latitude'] . ',' . $v['longitude'];
                $url = 'http://api.map.baidu.com/routematrix/v2/driving?output=json&origins=' . $origin . '&destinations=' . $destination. '&tactics=11&ak='.$ak;
                $result = httpRequest($url, "get");
                $data = json_decode($result, true);
                if (!empty($data['result'])) {
                   $shop_list[$k]['distance_text'] = $data['result'][0]['distance']['text'];
                   $shop_list[$k]['distance_number'] = $data['result'][0]['distance']['value'];
                   $shop_list[$k]['duration_text'] = $data['result'][0]['duration']['text'];
                   $shop_list[$k]['duration_number'] = $data['result'][0]['duration']['value'];
                }else{
                   $shop_list[$k]['distance_number'] = 99999;
                   $shop_list[$k]['distance_text'] = $data['message'];
                    $shop_list[$k]['duration_text'] = '未知时间';
                    $shop_list[$k]['duration_number'] = 0;
                }
                $this->store_id = $v['store_id'];
                $shop_list[$k]['coupon_list'] = $this->store_coupon_list();
            }

        }
        if(1 == $data['colligate']){
            array_multisort(array_column($shop_list,'colligate'),SORT_DESC,$shop_list);
        }
        //距离最近
        if(1 == input('distance')){
            array_multisort(array_column($shop_list,'distance_number'),SORT_ASC,$shop_list);
        }
        return ['status'=>1,'msg'=>'获取成功','result'=>$shop_list,'city_name'=>$city_name];
    }

    /**
     * 获取店铺优惠券
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function store_coupon_list()
    {
        $time = time();
        $where = array('type' => 2,'status'=>1,'send_start_time'=>['elt', $time],'send_end_time'=>['egt', $time]);
        $order = array('id' => 'desc');

        if($this->store_id){
            $where['store_id'] = $this->store_id;
        }
        $Coupon = new Coupon();
        $coupon_list = $Coupon->field("*,send_end_time-'$time' as spacing_time")->where($where)->order($order)->select();
        if ($coupon_list) {
            $coupon_list = collection($coupon_list)->append(['use_type_title'])->toArray();
            if ($this->user_id) {
                $user_coupon = Db::name('coupon_list')->where(['uid' => $this->user_id, 'type' => 2])->getField('cid',true);
            }
            if (!empty($user_coupon)) {
                foreach ($coupon_list as $k => $val) {
                    $coupon_list[$k]['isget'] = 0;
                    if (in_array($val['id'],$user_coupon)) {
                        $coupon_list[$k]['isget'] = 1;
                    }
                    $coupon_list[$k]['use_scope'] = $coupon_list[$k]['use_type_title'];
                }
            }
        }
        return $coupon_list;
    }


    public function shopClassList()
    {
        //搜索分类
        $parent_id = db('goods_category')->where(['id'=>$this->shop_class_id])->value('parent_id');
        if(0 == $parent_id){
            //查全部分类
            $shop_class_id = db('goods_category')->where(['parent_id'=>$this->shop_class_id])->column('id');
        }else{
            $shop_class_id = [$this->shop_class_id];
        }
        $where['shop_class_id'] = ['in',$shop_class_id];//全部则传1级分类查2级，否则传2级

        $shop = (new Shop())->where($where)->column();

    }


    /**
     * 店铺详情
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function shopInfo()
    {
        //if(!$this->checkShop()){return ['status'=>0,'msg'=>'没有数据','result'=>[]];}
        if(!$this->store_id){
            return ['status'=>0,'msg'=>'获取失败','result'=>[]];
        }
        $where = ['store_id'=>$this->store_id];
        $store = (new Store())->where($where)->field('store_id,store_name,store_sales,store_logo,store_phone,longitude,latitude,province_id,city_id,district,store_address')->find()->append(['activity','store_address']);
        $store['is_collect'] = Db::name('store_collect')->where(['user_id'=>$this->user_id,'store_id'=>$this->store_id])->count();
        return ['status'=>1,'msg'=>'获取成功','result'=>$store?$store:[]];
    }

//    public function getStoreGoodsList(){
//        $where = [
//            'store_id'=>$this->store_id,
//            'is_on_sale'=>1,
//            'is_virtual'=>0,
//            'exchange_integral'=>0
//        ];
//        $model = model('goods');
//        $store = $model->where($where)->field('goods_id,cat_id1,store_sales,store_logo')->find()->append(['activity']);
//
//    }

    /**
     * 店铺商品分类和商品
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getStoreGoodsList()
    {
        $cacheKey = 'storeGoods'.$this->store_id;
        if(Cache::has($cacheKey)){
            return ['status'=>1,'msg'=>'获取成功','result'=>Cache::get($cacheKey)];
        }
        $where = [
            'store_id'=>$this->store_id,
            'parent_id'=>0,
        ];
        $storeGoodsClassModel = model('StoreGoodsClass');
        $storeGoodsClassGoods =  $storeGoodsClassModel->with(['goods','goods.spec_goods_price'])
            ->where($where)->order('cat_sort desc')->field('cat_id,cat_name,0 as  total_num')->select();
        $storeGoods = collection($storeGoodsClassGoods)->toArray();
        $storeGoods = convert_arr_key($storeGoods,'cat_id');
        $storeBindPlatformGoodsModel = model('StoreBindPlatformGoods');
        $storeBindPlatformGoods = $storeBindPlatformGoodsModel->with(['goods','goods.spec_goods_price'])
            ->where(['store_id'=>$this->store_id,'id_delete'=>0])->field('store_cat_id,goods_id')->select();
        $storeGoodsPlatform = collection($storeBindPlatformGoods)->toArray();
//halt($storeGoodsPlatform);
        foreach ($storeGoodsPlatform as $val){
            if($val['goods']){
                $val['goods']['own_name'] = '自营';
                $val['goods']['store_cat_id1'] = $val['store_cat_id'];
            }
            array_push($storeGoods[$val['store_cat_id']]['goods'],$val['goods']);
        }
        //显示全部商品分类,先默认给个大键值
        $storeGoods[10000]['cat_id'] = 0 ;
        $storeGoods[10000]['cat_name'] = '全部商品' ;
        $storeGoods[10000]['total_num'] = 0 ;
        $storeGoods[10000]['goods'] = (new Goods())->with('spec_goods_price')->field('goods_id,goods_name,original_img,sales_sum,shop_price,store_cat_id1,store_count')->where(['store_id'=>$this->store_id,'prom_type'=>['in',[0,3]],'exchange_integral'=>0,'is_on_sale'=>1,'is_virtual'=>0])->select();
        foreach ($storeGoods as $key=> $val){
            if(0 == count($storeGoods[$key]['goods'])){
                unset($storeGoods[$key]);
            }else{
                $total_num = 0;
                foreach ($val['goods'] as $k=>$v){
                    if(!isset($storeGoods[$key]['goods'][$k])){
                        unset($storeGoods[$key]['goods'][$k]);
                    }else{
                        $storeGoods[$key]['goods'][$k]['original_img']= goods_thum_images($v['goods_id'],100,100);
                        $storeGoods[$key]['goods'][$k]['spec_list'] = $this->getSpecList($v['spec_goods_price']);
                        if($this->user_id){
                            //返回已勾选的购物车商品
                            $goods_num = ((new Cart())->field('sum(goods_num) as goods_num')->where(['goods_id'=>$v['goods_id'],'cart_store_id'=>$this->store_id,'user_id'=>$this->user_id])->group('goods_id')->find());
                            $storeGoods[$key]['goods'][$k]['select_num'] = $goods_num['goods_num'];
                            $total_num = $total_num+$storeGoods[$key]['goods'][$k]['select_num'];
                            $storeGoods[$key]['total_num'] = $total_num;
                        }
                    }
                }
                $storeGoods[$key]['goods'] = array_values($storeGoods[$key]['goods']);
            }
        }
        if($this->user_id){
            $all_goods_num = Cart::field('sum(goods_num) as goods_num')->where(['cart_store_id'=>$this->store_id,'user_id'=>$this->user_id])->find();
            $storeGoods[10000]['total_num'] = $all_goods_num['goods_num'];
        }

//        $storeGoods &&  Cache::set($cacheKey,$storeGoods,7200);
        return ['status'=>1,'msg'=>'获取成功','result'=>$storeGoods];
    }

    /**
     * 获取商品规格
     * @param $data
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    private function getSpecList($data){
        $spec_list = [];
        if($data) {
            $spec_goods_price_key_str = '';
            foreach ($data as $val) {
                $spec_goods_price_key_str .= '_' . $val['key'];
            }
            $spec_goods_price_key_arr = explode('_', $spec_goods_price_key_str);
            $spec_goods_price_key_arr = array_unique(array_filter($spec_goods_price_key_arr));
            $spec_item_list = Db::name('spec_item')->where('id', 'IN', $spec_goods_price_key_arr)->field('id,spec_id,item')->select();
            $spec_ids = array_unique(get_arr_column($spec_item_list, 'spec_id'));
            $spec_list = Db::name('spec')->where('id', 'IN', $spec_ids)->field('id,name')->order('`order` desc, id asc')->select();
            foreach ($spec_list as $spec_key => $spec_val) {
                foreach ($spec_item_list as $spec_item_key => $spec_item_val) {
                    if ($spec_val['id'] == $spec_item_val['spec_id']) {
                        $spec_list[$spec_key]['spec_item'][] = $spec_item_val;
                    }
                }
            }
            return $spec_list;
        }
        return $spec_list;
    }


    public function getAdminSite(){
        $admin_site = Db::name('admin_site')->where('is_status',1)->field('site_name,region_id')->select();
        $list = [];
        if($admin_site){
            foreach ($admin_site as $k => $v){
                $first = GetFirst(mb_substr($v['site_name'],0,1,'UTF-8'));
                $list[$first ?: '其他'][] = $v;
            }
            ksort($list);
        }
        return ['status'=>1,'msg'=>'获取成功','result'=>$list];
    }


    /**
     * 店铺商品评论ajax分页
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public function shopComment()
    {
        $commentType = I('commentType/d',1)?:1; // 1 全部 2好评 3 中评 4差评 5晒图
        $commentLogic = new CommentLogic();
        $goodsCommentList = $commentLogic->getShopGoodsComment($this->store_id,0, $commentType);
        return ['status'=>1,'msg'=>'获取成功','result'=>$goodsCommentList];
    }


    /**
     * 检查门店是否有效
     * @return bool
     */
    public function checkShop()
    {
        $where = ['shop_id'=>$this->shop_id,'s.deleted'=>0,'p.deleted'=>0,'store_state'=>1];
        $week = date("w");//星期 1 2 3 4 5 6 0， 0代表星期天
        switch ($week){
            case 1: $where['monday'] = 1;break;
            case 2: $where['tuesday'] = 1;break;
            case 3: $where['wednesday'] = 1;break;
            case 4: $where['thursday'] = 1;break;
            case 5: $where['friday'] = 1;break;
            case 6: $where['saturday'] = 1;break;
            case 0: $where['sunday'] = 1;break;
        }
        $shop = (new Shop())->alias('p')->where($where)->join("__STORE__ s","p.store_id = s.store_id",'left')->count();
        if($shop){return true;}else{return false;}
    }

    /**
     * 获取城市经纬信息
     * @return array
     */
    public function getCityLocation(){
        $url = 'http://api.map.baidu.com/geocoder/v2/?address='.I('city/s').'&output=json&ak='.tpCache('basic.lbsyun');
        $r = json_decode(httpRequest($url, "get"));
        if(0 == $r->status && $r->result){
            return ['status'=>1,'msg'=>'获取成功','result'=>$r->result];
        }
        return ['status'=>0,'msg'=>'无找到相关城市','result'=>[]];
    }

}