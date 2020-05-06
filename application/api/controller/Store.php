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

use app\common\logic\StoreGoodsClass;
use app\common\logic\StoreLogic;
use app\common\logic\User;
use app\common\util\TpshopException;


class Store extends Base {
    private $store;
    
    public function _initialize(){
        $store_id = I('store_id/d', 0);
        $this->store = M('store')->where(array('store_id'=>$store_id))->find();
    }


    /**
     * 首页获取店铺列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function storeList()
    {
        $result = (new StoreLogic)->storeList(input(''));
        $this->ajaxReturn($result);
    }



    public function storeClassList()
    {
        //需要传类型id,没有处理
        if(!IS_POST){ $this->ajaxReturn(['status'=>0,'msg'=>'请求方式错误']);}
        $StoreLogic = new StoreLogic;
        try{
            $StoreLogic->setShopClassId(input('shop_class_id/d'));
            $result = $StoreLogic->shopClassList(input(''));
            $this->ajaxReturn($result);
        }catch (TpshopException $t){
            $error = $t->getErrorArr();
            $this->ajaxReturn($error);
        }
    }


    /**
     * 获取店铺详情
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function storeInfo()
    {
        //需要传类型id,没有处理
        $StoreLogic = new StoreLogic;
        try{
            $StoreLogic->setStoreId(input('store_id/d',0));
            $StoreLogic->setUserId($this->user_id);
            $result = $StoreLogic->shopInfo(input(''));
            $this->ajaxReturn($result);
        }catch (TpshopException $t){
            $error = $t->getErrorArr();
            $this->ajaxReturn($error);
        }
    }

    /**
     * 获取店铺o2o的商品
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function storeGoodsList()
    {
        //需要传类型id,没有处理
//        if(!IS_POST){$this->ajaxReturn(['status'=>0,'msg'=>'请求方式错误']);}
        $StoreLogic = new StoreLogic;
        try{
            $StoreLogic->setStoreId(input('store_id/d'));
            $StoreLogic->setUserId($this->user_id);
            $result = $StoreLogic->getStoreGoodsList();
            $this->ajaxReturn($result);
        }catch (TpshopException $t){
            $error = $t->getErrorArr();
            $this->ajaxReturn($error);
        }
    }

    public function adminSite()
    {
        $StoreLogic = new StoreLogic;
        try{
            $result = $StoreLogic->getAdminSite();
            $this->ajaxReturn($result);
        }catch (TpshopException $t){
            $error = $t->getErrorArr();
            $this->ajaxReturn($error);
        }
    }

    /**
     * 获取店铺o2o的商品分类
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function storeAllGoodsClass()
    {
        //需要传类型id,没有处理
        if(!IS_POST){$this->ajaxReturn(['status'=>0,'msg'=>'请求方式错误']);}
        $StoreLogic = new StoreLogic;
        try{
            $StoreLogic->setShopId(input('shop_id/d'));
            $result = $StoreLogic->shopGoodsClass(input(''));
            $this->ajaxReturn($result);
        }catch (TpshopException $t){
            $error = $t->getErrorArr();
            $this->ajaxReturn($error);
        }
    }

    public function storeComment()
    {
        //需要传类型id,没有处理
        $StoreLogic = new StoreLogic;
        try{
            $StoreLogic->setStoreId(input('store_id/d'));
            $result = $StoreLogic->shopComment();
            $this->ajaxReturn($result);
        }catch (TpshopException $t){
            $error = $t->getErrorArr();
            $this->ajaxReturn($error);
        }
    }


    //以上是o2o

















    /**
     * 关于店铺(店铺基本信息)
     */
    public function about(){
        $store_id = I('store_id/d',0); // 当前分类id //  "store_id , store_name , grade_id , province_id , city_id , store_address , store_time"
        $store = M('store')->where("store_id",$store_id)->find();
        if (!$store) {
            $this->ajaxReturn(['status' => -1, 'msg' => '店铺不存在']);
        }
        
        //所在地
        $regions = M("region")->where(" id in( ".$store['province_id'] ." , ".$store['city_id']." , ".$store['district']." )")->select();
        $region= "";
        foreach($regions as $k => $v){
            $region .= $v['name'];
        }
        $store['location'] = $region;
         
        $gradgeId = $store['grade_id'];
        
        //查询店铺等级
        $gradgeName = M('store_grade')->where("sg_id",$gradgeId)->getField("sg_name");
        $store['grade_name'] = $gradgeName;
        
        $total_goods = M('goods')->where(array('store_id'=>$store_id,'is_on_sale'=>1))->count();
        $store['total_goods'] = $total_goods;
        
        //新品
        $new_goods = M('goods')->where(array('store_id'=>$store_id,'is_new'=>1,'is_on_sale'=>1))->count();
        $store['new_goods'] = $new_goods;
        
        //热卖商品
        $hot_goods = M('goods')->where(array('store_id'=>$store_id,'is_hot'=>1,'is_on_sale'=>1))->count();
        $store['hot_goods'] = $hot_goods;
        
        $collect = M('store_collect')->where(['store_id'=> $store_id, 'user_id' => $this->user_id])->find();
        $store['is_collect'] = $collect ? 1 : 0;
        
        $store['store_code_url'] = urldecode("http://{$_SERVER['HTTP_HOST']}/mobile/Store/index/store_id/".$store_id);

        $res = array('status'=>1,'msg'=>'获取成功','result'=>$store );
        $this->ajaxReturn($res);
    }
      

    /***
     * 店铺
     */
    public function index()
    {
        $store_id = I('store_id/d',0);
        $store = M('store')->where("store_id=$store_id")->find();
        if (!$store) {
            $this->ajaxReturn(['status' => -1, 'msg' => '店铺不存在']);
        }
        
        //新品
        $new_goods_list = M('goods')->field('goods_content',true)->where(array('store_id'=>$store_id,'is_new'=>1,'is_on_sale'=>1))->order('goods_id desc')->limit(10)->select();
        //推荐商品
        $recomend_goods = M('goods')->field('goods_content',true)->where(array('store_id'=>$store_id,'is_recommend'=>1,'is_on_sale'=>1))->order('goods_id desc')->limit(10)->select();  
        //热卖商品
        $hot_goods_list = M('goods')->field('goods_content',true)->where(array('store_id'=>$store_id,'is_hot'=>1,'is_on_sale'=>1))->order('sales_sum desc')->limit(10)->select();
        
        //店铺商品总数
        $total_goods = M('goods')->where(array('store_id'=>$store_id,'is_on_sale'=>1))->count();
        
        //店铺收藏总数
        $store_collect = M('store_collect')->where('store_id', $store_id)->count();
        
        $collect = M('store_collect')->where(['store_id'=> $store_id, 'user_id' => $this->user_id])->find();
        
        //新品
        $new_goods = M('goods')->where(array('store_id'=>$store_id,'is_new'=>1,'is_on_sale'=>1))->count();
        
        //热卖商品
        $hot_goods = M('goods')->where(array('store_id'=>$store_id,'is_hot'=>1,'is_on_sale'=>1))->count();
        
        $store['is_collect'] = $collect ? 1 : 0;
        $store['recomend_goods'] = $recomend_goods;
        $store['new_goods_list'] = $new_goods_list;
        $store['hot_goods_list'] = $hot_goods_list;
        $store['store_collect'] = $store_collect;
        $store['total_goods'] = $total_goods;
        $store['new_goods'] = $new_goods;
        $store['hot_goods'] = $hot_goods;
        $store['mb_slide'] = trim($store['mb_slide'],',');

        $json_arr = array('status'=>1,'msg'=>'获取成功','result'=>$store );
        
        $this->ajaxReturn($json_arr);
    }
    
    
    /**
     * 搜索店铺内的商品
     */
    public function searchStoreGoodsClass(){
    
        $store_id = I('store_id/d',1);
      
        $search_key = I("search_key");  // 关键词搜索
        
        $where = " where 1 = 1 ";
    
        $search_key && $where .= " and (goods_name like '%$search_key%' or keywords like '%$search_key%')";
    
        if ($store_id > 0) {
            $where .= " and store_id = ".  $store_id;     //店铺ID
        }
        
        $cat_id  = I("cat_id/d",0); // 所选择的商品分类id
        if ($cat_id > 0) {
            $where .= " and store_cat_id2 = ".  $cat_id ; // 初始化搜索条件
        }
        
        $list = M("goods")->where("store_id = 1")->field("goods_remark,goods_content,is_virtual" , true)->limit(0 , 10)->select();
        $this->ajaxReturn(['status'=>1, 'msg'=>'获取成功', 'result'=>$list]);
    }
    
    /**
     * 获取店铺商品分类
     */
    public function storeGoodsClass(){
        $store_id = $this->store['store_id'];
        $goods_logic = new StoreGoodsClass;
        $store_goods_class =  $goods_logic->getStoreGoodsClass($store_id);
        $json_arr = array('status'=>1,'msg'=>'获取成功','result'=>$store_goods_class);
        $this->ajaxReturn($json_arr);
    }

    /**
     * @author dyr
     * 修改于2016/08/26
     * 获取店铺商品列表
     */
    public function storeGoods()
    {
        $store_id = $this->store['store_id'];
        $page = I('p', 1);
        $sort = I('sort', 'comprehensive');
        $sore_mode = I('mode', 'desc');
        $cat_id = I('cat_id/d');
        $sta = I('sta/s');  //状态:is_new 是否最新, is_hot是否热销
        $q = I('q', ''); //搜索词
        
        $store_goods_where['store_id'] = $store_id;
        
        if ($q !== '') {
            $store_goods_where['goods_name|keywords'] = ['like', "%$q%"];
        }
        
        if (!empty($cat_id) && ($cat_id != -1)) {
            $store_goods_class_info = M('store_goods_class')->where(array('cat_id' => $cat_id))->find();
            if ($store_goods_class_info['parent_id'] == 0) {
                //一级分类
                $store_goods_where['store_cat_id1'] = $cat_id;
            } else {
                //二级分类
                $store_goods_where['store_cat_id2'] = $cat_id;
            }
        }

        if ($sort == 'sales') { //销量排序
            $orderBy = array(
                'sales_sum' => $sore_mode,
                'sort' => 'desc',
            );
        } else if ($sort == 'price') { //价格排序
            $orderBy = array(
                'shop_price' => $sore_mode,
                'sort' => 'desc',
            );
        } else { //综合排序
            $orderBy = array(
                'sort' => 'desc',
            );
        }
        
        if($sta && $sta == 'is_new'){//最新
            $store_goods_where['is_new'] = 1;
        }
        if($sta && $sta == 'is_hot'){//热销
            $store_goods_where['is_hot'] = 1;
        }
        
        $store_goods_where['is_on_sale'] = 1;
        $store_goods_list['goods_list'] = M('goods')
            ->field('goods_id,cat_id3,goods_sn,goods_name,shop_price,comment_count,sales_sum,is_virtual,virtual_sales_sum')
            ->where($store_goods_where)
            ->order($orderBy)
            ->page($page, 10)
            ->select();
        $store_goods_list['sort'] = $sort;
        $store_goods_list['sort_asc'] = $sore_mode;
        $store_goods_list['orderby_default'] = U('storeGoods', array('store_id' => $store_id));
        $store_goods_list['orderby_sales_sum'] = ($sort == 'sales' && $sore_mode == 'desc') ? U('storeGoods', array('store_id' => $store_id, 'sort' => 'sales', 'mode' => 'asc')) : U('storeGoods', array('store_id' => $store_id, 'sort' => 'sales', 'mode' => 'desc'));
        $store_goods_list['orderby_price'] = ($sort == 'price' && $sore_mode == 'desc') ? U('storeGoods', array('store_id' => $store_id, 'sort' => 'price', 'mode' => 'asc')) : U('storeGoods', array('store_id' => $store_id, 'sort' => 'price', 'mode' => 'desc'));
        $store_goods_list['orderby_comprehensive'] = ($sort == 'comprehensive' && $sore_mode == 'desc') ? U('storeGoods', array('store_id' => $store_id, 'mode' => 'asc')) : U('storeGoods', array('store_id' => $store_id, 'mode' => 'desc'));
        $json_arr = array('status' => 1, 'msg' => '获取成功', 'result' => $store_goods_list);
        $this->ajaxReturn($json_arr);
    }

    /**
     * @author dyr
     * 店铺收藏or取消操作
     */
    public function collectStoreOrNo()
    {
        $user = new User();
        $user->setUserById($this->user_id);
        $collect = $user->collectStoreOrNo($this->store);
        if($collect){
            $this->ajaxReturn(['status' => 1, 'msg' => '关注成功']);
        }else{
            $this->ajaxReturn(['status' => 1, 'msg' => '取消成功']);
        }
    }
}