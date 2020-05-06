<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 商业用途务必到官方购买正版授权, 使用盗版将严厉追究您的法律责任。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * Author: 当燃
 * Date: 2016-05-28
 */

namespace app\mobile\controller;
use think\Page;

class Store extends MobileBase {
	public $store = array();
	public $user_id = '';

	public function _initialize() {
	    parent::_initialize();
		$store_id = I('store_id/d');
		if(empty($store_id)){
			$this->error('参数错误,店铺系列号不能为空',U('Index/index'));
		}
		$store = M('store')->where(array('store_id'=>$store_id))->find();
		if($store){
			if($store['store_state'] == 0){
				$this->error('该店铺不存在或者已关闭', U('Index/index'));
			}
			$mb_slide = explode(',', $store['mb_slide']);
			$mb_slide_url = explode(',', $store['mb_slide_url']);
			$mb_slide_length = count($mb_slide);
			for ($i = 0; $i < $mb_slide_length; $i++) {
				$store['slide'][] = array('img' => $mb_slide[$i], 'url' => $mb_slide_url[$i]);
			}
            $store_province = M('region')->where(array('id' => $store['province_id']))->getField('name');
            $store_city = M('region')->where(array('id' => $store['city_id']))->getField('name');
            $store_district = M('region')->where(array('id' => $store['district']))->getField('name');
            $store['region'] = $store_province.'，'.$store_city.'，'.$store_district;
			$this->store = $store;
			$this->assign('store',$store);
		}else{
			$this->error('该店铺不存在或者已关闭',U('Index/index'));
		}
		
		if (session('?user')) {
			$user = session('user');
			$this->user_id = $user['user_id'];
			$this->assign('user', $user); //存储用户信息
		}
	}
	
	public function index(){
 
        $sort = I('sort','sort'); // 排序
        $sort_asc = I('sort_asc','desc');
        $q = urldecode(trim(I('q',''))); // 关键字搜索
        $q  && ($_GET['q'] = $filter_param['q'] = $q); //加入帅选条件中
        $where = array(
            'goods_name' => array('like', '%' . $q . '%'),
            'goods_state' => 1,
            'is_on_sale' => 1,
            'exchange_integral'=>0, //不检索积分商品
        );
        if($sort!='sort'){
            $where['store_id']=$this->store['store_id'];
            $whter['is_on_sale']=1;

            $count = M('goods')->where($where)->order([$sort=>$sort_asc])->count();
            $page_count = 20;
            $page = new Page($count, $page_count);
            $goods_list = M('goods')->where($where)->order([$sort=>$sort_asc])->limit($page->firstRow.','.$page->listRows)->select();
        }else{
            $where['store_id']=$this->store['store_id'];
            $whter['is_on_sale']=1;

            $count = M('goods')->where($where)->count();
            $page_count = 20;
            $page = new Page($count, $page_count);
            $goods_list = M('goods')->field('goods_content',true)->where($where)->limit($page->firstRow.','.$page->listRows)->select();
        }
        //热门商品排行
        $hot_goods = M('goods')->field('goods_content',true)->where($where)->order('sales_sum desc')->select();
        //新品
        $new_goods = M('goods')->field('goods_content',true)->where(array('store_id'=>$this->store['store_id'],'is_new'=>1,'is_on_sale'=>1))->order('goods_id desc')->select();
        //推荐商品
        $recomend_goods = M('goods')->field('goods_content',true)
            ->where(array('store_id'=>$this->store['store_id'],'is_recommend'=>1))
            ->order('goods_id desc')->select();
        //所有商品
        $total_goods = M('goods')->where(array('store_id'=>$this->store['store_id'],'is_on_sale'=>1))->count();
        //
        $this->assign('sort_asc', $sort_asc == 'asc' ? 'desc' : 'asc');

        $user_collect = M('store_collect')->where(['user_id' => $this->user_id, 'store_id' => $this->store['store_id']])->count();

        if (IS_AJAX){
            foreach($goods_list as $k=>$good){
                $good['thum_images']=goods_thum_images($good['goods_id'],400,400);
                $goods_list[$k]=$good;
            }
            $this->ajaxReturn($goods_list);
        }

        $this->assign('page',$page);// 赋值分页输出
        $this->assign('page_count', $page_count);
        $this->assign('user_collect',$user_collect);
        $this->assign('goods_list',$goods_list);
		$this->assign('hot_goods',$hot_goods);
		$this->assign('new_goods',$new_goods);
		$this->assign('recomend_goods',$recomend_goods);
		$this->assign('total_goods',$total_goods);
		return $this->fetch();
	}

	public function index2(){
		return $this->fetch();
	}
	
	public function goods_list(){
		$cat_id = I('cat_id/d', 0);
        $status = I('sta');  //商品状态
		$p = I('p', '1');
        $sort = I('sort','goods_id'); // 排序条件
        $sort_asc = I('sort_asc','asc'); // 排序
		$keywords = I('keywords');
		$map = ['store_id' => $this->store['store_id'], 'is_on_sale' => 1,'goods_state'=>1]; //店铺上架的商品
		$cat_name = "全部商品";
		if ($cat_id > 0) {  //分类
			$cat_name = M('store_goods_class')->where(array('cat_id' => $cat_id))->getField('cat_name');
		}
		if($keywords){  //搜索商品
			$map['goods_name'] = array('like',"%$keywords%");
		}
        if($status){
            $map["$status"]=1;
        }
		$filter_goods_id = M('goods')->where($map)->where(function($query) use($cat_id){
		    if ($cat_id > 0) {
		        $query->where("store_cat_id1",$cat_id)->whereOr("store_cat_id2" , $cat_id);;
		    }else{
		        $query->where("1=1");
		    }
		})->getField("goods_id", true);
		$count = count($filter_goods_id);
		$page_count = 10;//每页多少个商品
		if ($count > 0 && $filter_goods_id>0) {
			$goods_list = M('goods')->where("goods_id in (" . implode(',', $filter_goods_id) . ")")
                ->order([$sort=>$sort_asc])
                ->page($p,$page_count)->select();
		}

		$this->assign('sort', $sort);
		$this->assign('cat_id', $cat_id);
		$this->assign('sta', $status);
		$this->assign('sort', $sort);
        $sort_asc = ($sort_asc=='asc') ? 'desc' :'asc';
		$this->assign('sort_asc', $sort_asc);
		$this->assign('keywords', $keywords);
		$this->assign('goods_list', $goods_list);
		$this->assign('cat_name', $cat_name);
		$this->assign('goods_list_total_count',$count);
		$this->assign('page_count',$page_count);
		if(IS_AJAX){
			echo $this->fetch('ajaxGoodsList');
		}else{
			echo $this->fetch();
		}
	}
	
	public function about(){
        //热门商品排行
        $hot_goods = M('goods')->field('goods_content',true)->where(array('store_id'=>$this->store['store_id'],'is_hot'=>1,'is_on_sale'=>1))->order('sales_sum desc')->select();
        //新品
        $new_goods = M('goods')->field('goods_content',true)->where(array('store_id'=>$this->store['store_id'],'is_new'=>1,'is_on_sale'=>1))->order('goods_id desc')->select();
		$total_goods = M('goods')->where(array('store_id'=>$this->store['store_id'],'is_on_sale'=>1))->count();
		$this->assign('total_goods',$total_goods);
        $this->assign('hot_goods',$hot_goods);
        $this->assign('new_goods',$new_goods);
		return $this->fetch();
	}
	
	public function store_goods_class(){

		$store_goods_class_list = M('store_goods_class')->where(array('store_id'=>$this->store['store_id']))->select();
		
		if($store_goods_class_list){
			$sub_cat = $main_cat = array();
			foreach ($store_goods_class_list as $val){
			    if ($val['parent_id'] == 0) {
                    $main_cat[] = $val;
                } else {
                    $sub_cat[$val['parent_id']][] = $val;
                }
			}
			$this->assign('main_cat',$main_cat);
			$this->assign('sub_cat',$sub_cat);
			
		}
		$this->assign('store_id',$this->store['store_id']);
		return $this->fetch();
	}

    /**
     * 商品搜索列表页
     */
    public function ajaxSearch()
    {
        return $this->fetch();
    }

}