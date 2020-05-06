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
 * ============================================================================
 * Author: yhj
 * Date: 2019-2-26
 */
namespace app\admin\controller;
use app\admin\logic\GoodsLogic;
use app\common\logic\ActivityLogic;

use think\Db;
use think\AjaxPage;
class Block extends Base{

	/**
	 * 自定义手机模板 修改 页面
	 * @return mixed
	 */
	public function index(){
        $id = I('get.id');
        if($id){
	        $arr=M('mobile_template')->where('id='.$id)->field('template_html,block_info,template_name')->find();
		    $html=htmlspecialchars_decode($arr['template_html']);

	       	$this->assign('html',$html);
	       	$this->assign('info',$arr['block_info']);
	       	$this->assign('template_name',$arr['template_name']);
	       	$this->assign('id',$id);
        }

        $page_list=M('mobile_template')->field('id,template_name,add_time,is_index')->select();
		
       	$cat_list = Db::name('goods_category')->where("parent_id = 0 and is_show=1")->select(); // 联动菜单第一级

       	$cat_tree=$cat_list;
       	foreach ($cat_tree as $k => $v) {
       		$cat_tree[$k]['son'] = Db::name('goods_category')->where("parent_id =".$v['id']."  and is_show=1")->select(); // 菜单第二级
       	}

       	//商品列表数据
       	$goodsList = M('Goods')->where('is_on_sale=1')->order('goods_id desc')->page(1,10)->select();
       	$count=M('Goods')->where('is_on_sale=1')->count();
        $count=ceil($count/10);

		$ArticleCat = new \app\admin\logic\NewsLogic();
		$newsCat = $ArticleCat->article_cat_list(0, 0, false);
		$this->assign('newsCat',$newsCat);
		//新闻列表数据 默认前10个
		$where_news['publish_time'] = ['elt',time()];
		$where_news['is_open'] = 1;
		$count_new=Db::name('news')->where($where_news)->count();
		$count_new=ceil($count_new/10);
		$newsList = Db::view('news')
				->view('newsCat','cat_name','newsCat.cat_id=news.cat_id','left')
				->where($where_news)
				->order('publish_time DESC')
				->page(1,10)
				->select();
		$this->assign('newsList',$newsList);
		$this->assign('count_new',$count_new>1?$count_new:1);

		$this->coupon_list();
		// 所有店铺，和商品分类
		$goods_category = Db::name('goods_category')->where('is_show',1)->where('level',3)->order('id desc')->column('id,name');
		$store = Db::name('store')->where('store_state',1)->order('store_id asc')->column('store_id,store_name');
		$this->assign('store_list',$store);
		$this->assign('goods_category',$goods_category);
       	$this->assign('page_list',$page_list);
       	$this->assign('cat_list',$cat_list);
       	$this->assign('cat_tree',$cat_tree);
       	$this->assign('goodsList',$goodsList);
       	$this->assign('count',$count);
		return $this->fetch();
	}

	function coupon_list(){
		$p = I('p', '');
		$cat_id = I('cat_id', '');
		$activityLogic = new ActivityLogic();
		$coupon_list = $activityLogic->getCouponCenterList($cat_id, '', $p);
		$this->assign('coupon_list',$coupon_list);
	}
	/**
	 * 自定义页面列表
	 * @return mixed
	 */
	public function pageList(){
		$list=M('mobile_template')->where('store_id=0')->field('id,template_name,add_time,is_index')->select();
		$this->assign('list',$list);
		return $this->fetch();
	}
	/**
	 * 自定义手机模板数据的修改或添加 保存
	 */
	public function add_data(){
		$param=I('post.');
		$html=$param['html'];
		$html=str_replace("\n"," ",$html);
		$data['add_time']=time();
		$data['template_html']=$html;
		$data['block_info']=$param['info'];
		$data['template_name']=$param['template_name'];
		if(!empty($param['footmenu'])){
			$data['footmenu'] = json_encode($param['footmenu']);
			$data['footmenu_html'] = $param['footmenu_html'];
		}
		$id=I('post.edit_id');
		if($id){
			$res=M('mobile_template')->where('id='.$id)->save($data);
		}else{
			$res=M('mobile_template')->add($data);
		}

		//传递id回去防止重复添加
		if($res){
			if(empty($id)){
				$id = $res;
			}
			echo json_encode($id);
			$this->save_form($param['info'],$id);
		}else{
			echo json_encode(0);
		}
	}
	/**
	 * 设置首页
	 * @throws \think\Exception
	 */
	public function set_index(){
		$data=I('post.');
		$s = false;
		if($data['status']==0){
			$update_data = [
					'is_index'=>['exp',"if(id=".$data['id'].", 1, 0)"]
			];
			$s=Db::name('mobile_template')->where('1=1')->update($update_data);
		}elseif($data['status']==1){
			$s=Db::name('mobile_template')->where('id='.$data['id'])->save(array('is_index'=>0));
		}
		if($s){
			$this->ajaxReturn(['status' => 1, 'msg' => '成功','result' => 1]);
		}else{
			$this->ajaxReturn(['status' => 0, 'msg' => '失败','result' => 0]);
		}
	}
	/**
	 * 删除页面
	 */
	public function delete(){
		$id=I('post.id');
		if($id){
			M('mobile_template')->where('id', $id)->delete();
			exit(json_encode(1));
		}
		exit(json_encode(0));
	}
	/**
	 * 查门店列表,默认3个后台编缉显示
	 */
	public function shopList(){
		$where['deleted'] = 0;
		$where['shop_status'] = 1;
//        $shop = new \app\common\model\Shop();
//        $shop_list = $shop->with('shop_images')->where($where)->limit(3)->select();
		$shop_list = Db::name('shop')->field('shop_id,shop_name,province_id,city_id,district_id,shop_address,longitude,latitude,deleted,shop_desc')->where($where)->limit(3)->select();
		$this->ajaxReturn(['status' => 1, 'msg' => '成功', 'result' =>$shop_list]);
	}

	public function ajaxGoodsList(){
		$page_num  = input('page_num/d',10);
        $page=I('page');
		$where['is_on_sale'] = 1;
        // 关键词搜索               
        $key_word = I('keywords') ? trim(I('keywords')) : '';
        if($key_word){
			if(is_numeric($key_word)){
				$where['goods_id'] = $key_word;
			}
			if(strpos($key_word,',')){
				$goods_id_arr = explode(',',$key_word);
				$where['goods_id'] = ['in',$goods_id_arr];
			}else{
				//
				$where['goods_name|keywords'] = ['like',"%$key_word%"];

			}
        }
		// goods_id 查询支持 1,2,33 只查选中的商品
		$goods_id = input('goods_id');
		if($goods_id){
			if(strpos($goods_id,',')){
				$goods_id_arr = explode(',',$goods_id);
				$where['goods_id'] = ['in',$goods_id_arr];
			}else{
				$where['goods_id'] = $goods_id;
			}
		}
		// 查店铺
		$store_id = input('store_id/d',0);
		if($store_id){
			$where['store_id'] = $store_id;
		}
		// 分类只查第三级
		$cat_id3 = input('cat_id3/d',0);
		if($cat_id3){
			$where['cat_id3'] = $cat_id3;
		}
		$order_str = 'goods_id desc';
        $count = M('Goods')->where($where)->count();
        $goodsList = M('Goods')->where($where)->order($order_str)->page($page,$page_num)->select();
		
        $html='';
        foreach ($goodsList as $k => $v) {
        	$html.='<ul class="p-goods-item">';
        	$html.='<li class="pi-li0"><input type="checkbox" value="'.$v['goods_id'].'" /></li>';
        	$html.='<li class="pi-li1">'.$v['goods_id'].'</li>';
        	$html.='<li class="pi-li2">'.$v['goods_name'].'</li>';
        	$html.='<li class="pi-li3"><img src="'.$v['original_img'].'" alt="" /></li>';
        	$html.='<li class="pi-li4">'.$v['cat_id'].'</li>';
        	$html.='<li class="pi-li4">'.$v['shop_price'].'</li>';
        	$html.='<li class="pi-li4">'.$v['store_count'].'</li>';
        	$html.='</ul>';
        }
        $result['html']=$html;
        $result['count']=ceil($count/10);
		$is_admin = input('is_admin');
		if($count > 0 && $is_admin){
			//
			$list = [];
			foreach($goodsList as $k=>$v){
				$v['img'] = goods_thum_images($v['goods_id'],200,200);
				$goods_data[] = $v;
				if( ($k+1)%3 == 0){
					$list[] = $goods_data;
					unset($goods_data);
				}
			}
			if(isset($goods_data)){
				$list[] = $goods_data;
			}
			$goodsList = $list;
		}
        $this->ajaxReturn(['status' => 1, 'msg' => '成功', 'result' =>$result,'where'=>$goods_id,'goods_list'=>$goodsList,'lay_count'=>$count]);
    }
	/**
	 * 商品列表
	 */
	public function goods_list_block(){
		$data = input('post.');
		// // 13时轮播，传的是sql_where
		if(isset($data['sql_where'])){
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
			if(!empty($sql_where['goods'])){
				$data['goods'] = $sql_where['goods'];
			}
		}

		$block = new \app\common\logic\Block();
		$goodsList = $block->goods_list_block($data);

		$html='';
		if($data['block_type']==13){
			foreach ($goodsList as $k => $v) {
				$html.='<div class="containers-slider-item">';
				$html.='<div class="seckill-item-img">';
				if($v['activity']['prom_title']){
                    $html .=' <div class="prom_title">'.$v['activity']['prom_title'].'</div>';
                }
				$html.='<a href="/Mobile/Goods/goodsInfo/id/'.$v["goods_id"].'"><img src="'.$v["original_img"].'" /></a>';
				$html.='</div>';
				$html.='<div class="seckill-item-name"><p>'.$v["goods_name"].'</p></div>';
				$html.='<div class="seckill-item-price" class="p"><span class="fl">￥<em>'.$v['shop_price'].'</em></span>';
				$html.='</div></div>';
			}
		}else{
			foreach ($goodsList as $k => $v) {
				$num = $v['sales_sum']+$v['virtual_sales_sum'];
				$html.='<li>';
				if($v['activity']['prom_title']){
                    $html .=' <div class="prom_title">'.$v['activity']['prom_title'].'</div>';
                }
				$html.='<a class="tpdm-goods-pic" href="/Mobile/Goods/goodsInfo/id/'.$v["goods_id"].'"><img src="'.$v["original_img"].'" alt="" /></a>';
				$html.='<a href="/Mobile/Goods/goodsInfo/id/'.$v["goods_id"].'" class="tpdm-goods-name">'.$v["goods_name"].'</a>';
				$html.= $v['label_name'] ? '<span class="rx-sp">'.$v['label_name'].'</span>' :  '<span class="rx-sp"  style="border:none"></span>';
				$html.='<div class="tpdm-goods-des">';
				$html.='<div class="tpdm-goods-price">￥<em>'.explode_price($v['shop_price'],0).'.</em><em>'.explode_price($v['shop_price'],1).'</em>'.'</div>';
				$html.='<a class="tpdm-goods-like">已售出'.$num.'件</a>';
				$html.='</div>';
				$html.='</li>';
			}
		}

		$this->ajaxReturn(['status' => 1, 'msg' => '成功', 'result' =>$html,'goodsList'=>$goodsList]);
		
	}

	/**
	 * 新闻列表 浏览
	 */
	public function get_news_list(){
		$data=I('post.');
		$num=I('post.num',2);
		$ids=$data['news'];

		if($ids){
			$ids = substr($ids,0,strlen($ids)-1);
			$ids_arr = explode(',', $ids);
			$where_news['article_id'] = ['in', $ids_arr];
		}
		$where_news['publish_time'] = ['elt',time()];
		$where_news['is_open'] = 1;

		$block = new \app\common\logic\Block();
		$list = $block->news_list($where_news,$num);

		$html='';
		foreach ($list as $k => $v) {

			$html.='<li><a href="'.'/api/news/news_detail.html?news_id='.$v['article_id'].'"><div class="carlist-img fl">';
			$html.='<img src="'.$v['thumb'].'"></div>';
			$html.='<div class="carlist-txt fr"><b>'.$v['title'].'</b>';
			$html.='<p>'.$v['description'].'</p>';
			$html.='<span><em>'.$v['cat_name'].'</em><img src="/public/static/images/icon-fire.png">';
			$html.='<i>'.date("Y-m-d",$v['publish_time']).'</i></span></div></a></li>';
		}
		$this->ajaxReturn(['status' => 1, 'msg' => '成功', 'result' =>$html]);
	}

	//ajax获取新闻 修改
	public function ajaxNewsList(){

		$page = input('page/d',1);
		$cat_id = input('cat');
		if($cat_id){
			$where['cat_id'] = $cat_id;
		}
		$where['publish_time'] = ['elt',time()];
		$where['is_open'] = 1;
		$count_new=Db::name('news')->where($where)->count();
		if($cat_id){
			unset($where['cat_id']);
			$where['news.cat_id'] = $cat_id;
		}

		$list= Db::view('news')
				->view('newsCat','cat_name','newsCat.cat_id=news.cat_id','left')
				->where($where)
				->order('publish_time DESC')
				->page($page,10)
				->select();

		$html='';
		foreach ($list as $k => $v) {
			if(strpos($v['thumb'],'/public') === 0 ){
				if(!file_exists('.'.$v['thumb'])){
					$v['thumb'] = '/public/images/icon_goods_thumb_empty_300.png';
					$list[$k]['thumb'] = $v['thumb'];
				}
			}elseif(empty($v['thumb'])){
				$v['thumb'] = '/public/images/icon_goods_thumb_empty_300.png';
				$list[$k]['thumb'] = $v['thumb'];
			}
			$html.='<ul class="p-goods-item">';
			$html.='<li class="pi-li0"><input type="checkbox" value="'.$v['article_id'].'" /></li>';
			$html.='<li class="pi-li1">'.$v['article_id'].'</li>';
			$html.='<li class="pi-li2">'.$v['title'].'</li>';
			if($v['thumb']){
				$html.='<li class="pi-li3"><img src="'.$v['thumb'].'" alt="" /></li>';
			}else{
				$html.='<li class="pi-li3"></li>';
			}
			$html.='<li class="pi-li4">'.$v['cat_name'].'</li>';
			$html.='<li class="pi-li4">'.date("Y-m-d",$v['publish_time']).'</li>';
			$html.='</ul>';
		}

		$count_new=ceil($count_new/10);

		$result['html']=$html;
		$result['count_new']=$count_new;
		$this->ajaxReturn(['status' => 1, 'msg' => '成功', 'result' =>$result,'list'=>$list]);
	}

	/**
	 * 添加智能表单的配置
	 * @param $json
	 * @param int $tpl_id
	 * @param int $industry_id
	 * @throws \think\Exception
	 */
	private function save_form($json, $tpl_id=0,$industry_id=0){
		if(!empty($tpl_id)){
			Db::name('form_config')->where('tpl_id',$tpl_id)->delete();
		}
		$data = json_decode(htmlspecialchars_decode($json),true);
		foreach($data as $k=>$v){
			if(isset($v['block_type']) && $v['block_type'] == '19'){
				$arr['tpl_timeid'] = $v['timeid'];
				$arr['tpl_id']=$tpl_id;
				$arr['industry_id'] = $industry_id;
				$arr['form_name'] = $v['form_name'];
				$arr['config_value'] = json_encode($v,JSON_UNESCAPED_UNICODE);
				$this->save_form_config($arr);
			}
		}
	}

	/**
	 * for save_form
	 * @param $data
	 */
	private function save_form_config($data){
		$tpl_timeid = Db::name('form_config')->where('tpl_timeid',$data['tpl_timeid'])->value('tpl_timeid');
		if($tpl_timeid){
			Db::name('form_config')->where('tpl_timeid',$data['tpl_timeid'])->save($data);
		}else{
			$data['create_time'] = time();
			Db::name('form_config')->add($data);
		}
	}

	/**
	 * 智能表单 列表
	 * @return mixed
	 */
	public function form_list(){
		// 所有表单名称
		$form_config = Db::name('form_config')->select();
		// 当前表单
		$tpl_timeid = input('tpl_timeid',0);
		if(empty($tpl_timeid) && !empty($form_config)){
			$tpl_timeid = $form_config[0]['tpl_timeid'];
		}

		$where['tpl_timeid'] = $tpl_timeid;
		$arr = Db::name('form_config')->where($where)->find();
		$form_config_list = json_decode($arr['config_value'],true);
		$name_list = $form_config_list['nav'];

		// 要查看的项
		$this->assign('name_list', $name_list);
		$this->assign('tpl_timeid', $tpl_timeid);
		$this->assign('form_config', $form_config);
		return $this->fetch();
	}

	/**
	 * 智能表单 列表
	 * @return mixed
	 */
	public function ajax_form_list(){
		// 搜索条件
		$condition = [];
		$condition['tpl_timeid'] = input('tpl_timeid',0);
		$sort = input('sort','desc');
		$order_by = input('order_by','form_id');
		if(!in_array($order_by,['form_id','submit_time'])){
			$order_by = 'form_id';
		}
		$sort_order = $order_by . ' ' . ($sort == 'asc' ? 'asc':'desc');//exit($sort_order);
		$mobile = input('mobile');
		if($mobile){
			$condition['mobile'] = ['like','%'.$mobile.'%'];
		}
		$start_time = input('start_time');
		$end_time = input('end_time');
		if($start_time && $end_time){
			$start_time = strtotime($start_time);
			$end_time = strtotime($end_time);
			if($start_time <= $end_time){
				$condition['submit_time'] = [
						['egt', $start_time],
						['elt', $end_time]
				];
			}
		}

		$usersModel = model('form');
		$count = $usersModel->where($condition)->count();
		$Page = new AjaxPage($count, 10);
		$userList = $usersModel->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$name_list=[];
		$name_data = $userList[0]['submit_value'];
		for ($i=0; $i < count($name_data); $i++) {
			if(isset($name_data['name'.$i])){
				$arr['name'] = 'name'.$i;
				$arr['title'] = $name_data['title'.$i];
				$name_list[] = $arr;
			}
		}
		$show = $Page->show();
		$this->assign('userList', $userList);
		$this->assign('page', $show);// 赋值分页输出
		$this->assign('pager', $Page);
		$this->assign('name_list', $name_list);
		return $this->fetch();
	}

	/**
	 * 智能表单 删除
	 * @throws \think\Exception
	 */
	public function delete_form(){
		$form_id = input('post.form_id/d',0);
		$s = Db::name('form')->delete($form_id);
		if($s){
			$this->ajaxReturn(['status' => 1, 'msg' => '删除成功']);
		}else{
			$this->ajaxReturn(['status' => 0, 'msg' => '删除失败']);
		}
	}

}
?>