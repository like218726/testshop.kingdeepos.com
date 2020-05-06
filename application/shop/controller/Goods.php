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
 * Author: IT宇宙人
 * Date: 2015-09-09
 */
namespace app\shop\controller;

use app\admin\model\SpecGoodsPrice;
use app\common\model\ShopGoodsStock;
use app\shop\logic\GoodsCategoryLogic;
use app\shop\model\ShippingArea;
use think\Db;
use think\Page;
use think\AjaxPage;
use app\shop\logic\GoodsLogic;
use think\Loader;

class Goods extends Base
{

    /**
     *  商品列表
     */
    public function goodsList()
    {
        checkIsBack();
        $shopper = session('shopper');
        $store_goods_class_list = Db::name('store_goods_class')->where(['parent_id' => 0, 'store_id' => $shopper['store_id']])->select();//本店分类
        $brand_list = Db::name('brand')->where(['store_id' => $shopper['store_id'], 'status' => 0])->select();//品牌
        $this->assign('store_goods_class_list', $store_goods_class_list);
        $this->assign('brand_list', $brand_list);
        return $this->fetch('goodsList');
    }

    /**
     *  商品列表
     */
    public function ajaxGoodsList()
    {
        $shopper = session('shopper');
        $intro = I('intro', 0);
        $store_cat_id1 = I('store_cat_id1', '');
        $key_word = trim(I('key_word', ''));
        $orderby1 = I('post.orderby1', '');
        $orderby2 = I('post.orderby2', '');
        $suppliers_id = input('suppliers_id','');
        $brand_id = input('brand_id','');
        $goods_where = [
            'goods_state' => 1,
            'is_virtual' => 0,
            'store_id'=>$shopper['store_id']
        ];
        if($suppliers_id !== ''){
            $goods_where['suppliers_id'] = $suppliers_id;
        }
        if($brand_id !== ''){
            $goods_where['brand_id'] = $brand_id;
        }
        if (!empty($intro)) {
            $goods_where[$intro] = 1;
        }
        if ($store_cat_id1 !== '') {
            $goods_where['store_cat_id1'] = $store_cat_id1;
        }
        if ($key_word !== '') {
            $goods_where['goods_name|goods_sn'] = array('like', '%' . $key_word . '%');
        }
        $order_str = array();
        if ($orderby1 !== '') {
            $order_str[$orderby1] = $orderby2;
        }
        $count = Db::name('goods')->where($goods_where)->count();
        $Page = new AjaxPage($count, 10);

        //是否从缓存中获取Page
        if (session('is_back') == 1) {
            $Page = getPageFromCache();
            //重置获取条件
            delIsBack();
        }
        $goodsList =  Db::name('goods')->where($goods_where)->order($order_str)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        cachePage($Page);
        $show = $Page->show();

        $catList =  M('goods_category')->cache(true)->select();
        $catList = convert_arr_key($catList, 'id');
        $store_warning_storage = M('store')->where('store_id', STORE_ID)->getField('store_warning_storage');
        $this->assign('store_warning_storage', $store_warning_storage);
        $this->assign('catList', $catList);
        $this->assign('goodsList', $goodsList);
        $this->assign('page', $show);// 赋值分页输出
        return $this->fetch();
    }

    public function selectGoods(){
        $shopper = session('shopper');
        $intro = I('intro', 0);
        $store_cat_id1 = I('store_cat_id1', '');
        $key_word = trim(I('key_word', ''));
        $orderby1 = I('post.orderby1', '');
        $orderby2 = I('post.orderby2', '');
        $suppliers_id = input('suppliers_id','');
        $brand_id = input('brand_id','');
        $goods_where = [
            'goods_state' => 1,
            'is_virtual' => 0,
            'store_id'=>$shopper['store_id']
        ];
        if($suppliers_id !== ''){
            $goods_where['suppliers_id'] = $suppliers_id;
        }
        if($brand_id !== ''){
            $goods_where['brand_id'] = $brand_id;
        }
        if (!empty($intro)) {
            $goods_where[$intro] = 1;
        }
        if ($store_cat_id1 !== '') {
            $goods_where['store_cat_id1'] = $store_cat_id1;
        }
        if ($key_word !== '') {
            $goods_where['goods_name|goods_sn'] = array('like', '%' . $key_word . '%');
        }
        $order_str = array();
        if ($orderby1 !== '') {
            $order_str[$orderby1] = $orderby2;
        }
        $count = Db::name('goods')->where($goods_where)->count();
        $Page = new AjaxPage($count, 10);

        //是否从缓存中获取Page
        if (session('is_back') == 1) {
            $Page = getPageFromCache();
            //重置获取条件
            delIsBack();
        }
        $goodsList =  Db::name('goods')->where($goods_where)->order($order_str)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        cachePage($Page);
        $show = $Page->show();

        $catList =  M('goods_category')->cache(true)->select();
        $catList = convert_arr_key($catList, 'id');
        $store_warning_storage = M('store')->where('store_id', STORE_ID)->getField('store_warning_storage');
        $this->assign('store_warning_storage', $store_warning_storage);
        $this->assign('catList', $catList);
        $this->assign('goodsList', $goodsList);
        $this->assign('page', $show);// 赋值分页输出
        return $this->fetch();
    }
    
    public function goods_offline(){
    	$where['store_id'] = STORE_ID;
    	$model = M('Goods');
        $suppliers_id = input('suppliers_id');
        if($suppliers_id){
            $where['suppliers_id'] = $suppliers_id;
        }
    	if(I('is_on_sale') == 2){
    		$where['is_on_sale'] = 2;
    	}else{
  			$where['is_on_sale'] = 0;
    	}
    	$goods_state = I('goods_state', '', 'string'); // 商品状态  0待审核 1审核通过 2审核失败
    	if($goods_state != ''){
    		$where['goods_state'] = intval($goods_state);
    	}
    	$store_cat_id1 = I('store_cat_id1', '');
    	if ($store_cat_id1 !== '') {
    		$where['store_cat_id1'] = $store_cat_id1;
    	}
    	$key_word = trim(I('key_word', ''));
    	if ($key_word !== '') {
    		$where['goods_name|goods_sn'] = array('like', '%' . $key_word . '%');
    	}
    	$count = $model->where($where)->count();
    	$Page = new Page($count, 10);
    	$goodsList = $model->where($where)->order('goods_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
    	$show = $Page->show();
    	$store_goods_class_list = M('store_goods_class')->where(['parent_id' => 0, 'store_id' => STORE_ID])->select();
    	$this->assign('store_goods_class_list', $store_goods_class_list);
    	$suppliers_list = M('suppliers')->where(array('store_id'=>STORE_ID))->select();
    	$this->assign('suppliers_list', $suppliers_list);
		$this->assign('state',C('goods_state'));
    	$this->assign('goodsList', $goodsList);
    	$this->assign('page', $show);// 赋值分页输出
    	return $this->fetch();
    } 

    public function stock_list()
    {
        $model = M('stock_log');
        $map['store_id'] = STORE_ID;
        $mtype = I('mtype');
        if ($mtype == 1) {
            $map['stock'] = array('gt', 0);
        }
        if ($mtype == -1) {
            $map['stock'] = array('lt', 0);
        }
        $goods_name = I('goods_name');
        if ($goods_name) {
            $map['goods_name'] = array('like', "%$goods_name%");
        }
        $ctime = I('ctime');
        if ($ctime) {
            $gap = explode(' - ', $ctime);
            $this->assign('ctime', $gap[0] . ' - ' . $gap[1]);
            $this->assign('start_time', $gap[0]);
            $this->assign('end_time', $gap[1]);
            $map['ctime'] = array(array('gt', strtotime($gap[0])), array('lt', strtotime($gap[1])));
        }
        $count = $model->where($map)->count();
        $Page = new Page($count, 20);
        $show = $Page->show();
        $this->assign('page', $show);// 赋值分页输出
        $stock_list = $model->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('stock_list', $stock_list);
        return $this->fetch();
    }

    /**
     * 编辑商品库存
     */
    public function stock(){
        $goods_id = input('goods_id');
        if(empty($goods_id)){
            $this->error('参数有误');
        }
        $shopper = session('shopper');
        $Goods = new \app\common\model\Goods();
        $goods = $Goods->where(['goods_id'=>$goods_id,'store_id'=>$shopper['store_id']])->find();
        if(empty($goods)){
            $this->error('非法操作');
        }
        $SpecGoodsPrice = new SpecGoodsPrice();
        $specGoodsPrice = $SpecGoodsPrice->where(['goods_id'=>$goods_id,'store_id'=>$shopper['store_id']])->select();
        if(!empty($specGoodsPrice)){
            $spec_key_list = [];
            foreach($specGoodsPrice as $specKey => $specVal){
                $temp_key = explode('_', $specVal['key']);
                $spec_key_list = array_merge_recursive($spec_key_list,$temp_key);
            }

            $spec_item_list = Db::name('spec_item')->where("id", "in", array_unique($spec_key_list))->select(); // 找出这个类型的所有规格
            $spec_id_arr = get_arr_column($spec_item_list,'spec_id');
            $specArray = Db::name('spec')->where("id", "in", array_unique($spec_id_arr))->order('`order` desc')->select(); // 找出这个类型的所有规格
            if ($specArray) {
                foreach ($specArray as $k => $v) {
                    foreach($spec_item_list as $spec_item_key => $spec_item_val){
                        if($spec_item_val['spec_id'] == $v['id']){
                            $specArray[$k]['spec_item'][] = $spec_item_val;
                        }
                    }
                }
            }
            $this->assign('specArray',$specArray);
        }
        $ShopGoodsStock = new ShopGoodsStock();
        $shop_stock_list = $ShopGoodsStock->where(['shop_id' => $shopper['shop_id'], 'goods_id' => $goods_id])->select();
        if ($shop_stock_list) {
            $shop_stock_list = collection($shop_stock_list)->append(['key_arr'])->toArray();
        }
        $shop_stock_default = $ShopGoodsStock->where(['shop_id' => $shopper['shop_id'], 'goods_id' => $goods_id,'key_name'=>''])->find();
        $this->assign('shop_stock_list',$shop_stock_list);
        $this->assign('shop_stock_default',$shop_stock_default);
        return $this->fetch();
    }

    public function stockSave(){
        $goods_id = input('goods_id');
        $spec = input('spec/a');
        $sku = input('sku/a');
        $stock_count = input('stock_count/a');
        $stock_default = input('stock_default');
        $spec_arr = [];
        if ($spec) {
            $spec_arr = array_values($spec);
        }

        $spec_count = count($spec_arr[0]['id']);
        $spec_list = [];
        $spec_data = [];
        for ($spec_index = 0; $spec_index < $spec_count; $spec_index++) {
            $temp = array();
            foreach($spec_arr as $specKey=>$specVal){
                $temp[] = $specVal['id'][$spec_index];
            }
            if(!empty($temp) && $sku[$spec_index] != '' && $stock_count[$spec_index] != ''){
                $spec_key = implode('_',$temp);
                $spec_list[] = $spec_key;
                $spec_data[] = ['key'=>$spec_key,'sku'=>$sku[$spec_index],'stock_count'=>$stock_count[$spec_index]];
            }
        }
        $shopper = session('shopper');
        if(count($spec_data) == 0){
            if($stock_default == ''){
                $this->ajaxReturn(['status' => 0, 'msg' => '分配库存失败', 'result' => '']);
            }
            $data = [
                'goods_id'=>$goods_id,
                'stock_count'=>$stock_default,
                'store_id'=>$shopper['store_id'],
                'shop_id'=>$shopper['shop_id'],
            ];
            $insert = Db::name('shop_goods_stock')->insert($data);
            if ($insert !== false) {
                $this->ajaxReturn(['status' => 1, 'msg' => '分配库存成功', 'result' => '']);
            } else {
                $this->ajaxReturn(['status' => 0, 'msg' => '分配库存失败', 'result' => '']);
            }
        }
        $spec_key_str = implode("','",$spec_list);
        $orderBy = "field(`key`,'".$spec_key_str."')";
        $spec_goods_price_list = Db::name('spec_goods_price')->where(['store_id'=>$shopper['store_id']])->where('key','IN',$spec_list)->order($orderBy)->select();
        Db::name('shop_goods_stock')->where(['shop_id'=>$shopper['shop_id'],'goods_id'=>$goods_id])->where('key','IN',$spec_list)->delete();
        $spec_goods_price_count = count($spec_goods_price_list);
        for ($data_index = 0; $data_index < $spec_goods_price_count; $data_index++) {
            $data[] = [
                'goods_id'=>$goods_id,
                'key'=>$spec_goods_price_list[$data_index]['key'],
                'key_name'=>$spec_goods_price_list[$data_index]['key_name'],
                'stock_count'=>$spec_data[$data_index]['stock_count'],
                'bar_code'=>$spec_goods_price_list[$data_index]['bar_code'],
                'sku'=>$spec_data[$data_index]['sku'],
                'store_id'=>$shopper['store_id'],
                'shop_id'=>$shopper['shop_id'],
            ];
        }
        if (!empty($data)) {
            $insert = Db::name('shop_goods_stock')->insertAll($data);
            if ($insert !== false) {
                $this->ajaxReturn(['status' => 1, 'msg' => '分配库存成功', 'result' => '']);
            } else {
                $this->ajaxReturn(['status' => 0, 'msg' => '分配库存失败', 'result' => '']);
            }
        }else{
            $this->ajaxReturn(['status' => 0, 'msg' => '分配库存失败', 'result' => '']);
        }
    }

    /**
     * 更改指定表的指定字段
     */
    public function updateField()
    {
        $primary = array(
            'goods' => 'goods_id',
            'goods_attribute' => 'attr_id',
            'ad' => 'ad_id',
        );
        $id = I('id/d', 0);
        $field = I('field');
        $value = I('value');
        Db::name($_POST['table'])->where($primary[$_POST['table']], $id)->where('store_id', STORE_ID)->save(array($field => $value));
        $return_arr = array(
            'status' => 1,
            'msg' => '操作成功',
            'data' => array('url' => U('Goods/goodsAttributeList')),
        );
        $this->ajaxReturn($return_arr);
    }

    /**
     * 动态获取商品属性输入框 根据不同的数据返回不同的输入框类型
     */
    public function ajaxGetAttrInput()
    {
        $cat_id3 = I('cat_id3/d', 0);
        $goods_id = I('goods_id/d', 0);
        empty($cat_id3) && exit('');
        $type_id = M('goods_category')->where("id", $cat_id3)->getField('type_id'); // 找到这个分类对应的type_id
        empty($type_id) && exit('');
        $GoodsLogic = new GoodsLogic();
        $str = $GoodsLogic->getAttrInput($goods_id, $type_id);
        exit($str);
    }

    /**
     * 删除商品
     */
    public function delGoods()
    {
        $goods_id = I('id/d');
        $error = '';
        
        // 判断此商品是否有订单
        $c1 = M('OrderGoods')->where("goods_id", $goods_id)->count('1');
        $c1 && $error .= '此商品有订单,不得删除! <br/>';

        // 商品团购
        $c1 = M('group_buy')->where("goods_id", goods_id)->count('1');
        $c1 && $error .= '此商品有团购,不得删除! <br/>';

        // 商品退货记录
        $c1 = M('return_goods')->where("goods_id", $goods_id)->count('1');
        $c1 && $error .= '此商品有退货记录,不得删除! <br/>';

        if ($error) {
            $return_arr = array('status' => -1, 'msg' => $error, 'data' => '',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);
            $this->ajaxReturn($return_arr);
        }

        // 删除此商品        
        $result = M("Goods")->where(['goods_id' => $goods_id, 'store_id' => STORE_ID])->delete();  //商品表
        if ($result) {
            M("cart")->where('goods_id', $goods_id)->delete();  // 购物车
            M("comment")->where('goods_id', $goods_id)->delete();  //商品评论
            M("goods_consult")->where('goods_id', $goods_id)->delete();  //商品咨询
            M("goods_images")->where('goods_id', $goods_id)->delete();  //商品相册
            M("spec_goods_price")->where('goods_id', $goods_id)->delete();  //商品规格
            M("spec_image")->where('goods_id', $goods_id)->delete();  //商品规格图片
            M("goods_attr")->where('goods_id', $goods_id)->delete();  //商品属性
            M("goods_collect")->where('goods_id', $goods_id)->delete();  //商品收藏
        }
        $return_arr = array('status' => 1, 'msg' => '操作成功', 'data' => '',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);
        $this->ajaxReturn($return_arr);
    }

    /**
     * ajax 获取 品牌列表
     */
    public function getBrandByCat()
    {
        $db_prefix = C('database.prefix');
        $type_id = I('type_id/d');
        if ($type_id) {
//            $list = M('brand')->join("left join {$db_prefix}brand_type on {$db_prefix}brand.id = {$db_prefix}brand_type.brand_id and  type_id = $type_id")->order('id')->select();
            $list = Db::name('brand')->alias('b')->join('__BRAND_TYPE__ t', 't.brand_id = b.id', 'LEFT')->where(['t.type_id' => $type_id])->order('b.id')->select();
        } else {
            $list = M('brand')->order('id')->select();
        }
//        $goods_category_list = M('goods_category')->where("id in(select cat_id1 from {$db_prefix}brand) ")->getField("id,name,parent_id");
        $goods_category_list = Db::name('goods_category')
            ->where('id', 'IN', function ($query) {
                $query->name('brand')->where('')->field('cat_id1');
            })
            ->getField("id,name,parent_id");
        $goods_category_list[0] = array('id' => 0, 'name' => '默认');
        asort($goods_category_list);
        $this->assign('goods_category_list', $goods_category_list);
        $this->assign('type_id', $type_id);
        $this->assign('list', $list);
        return $this->fetch();
    }


    /**
     * ajax 获取 规格列表
     */
    public function getSpecByCat()
    {

        $db_prefix = C('database.prefix');
        $type_id = I('type_id/d');
        if ($type_id) {
//            $list = M('spec')->join("left join {$db_prefix}spec_type on {$db_prefix}spec.id = {$db_prefix}spec_type.spec_id  and  type_id = $type_id")->order('id')->select();
            $list = Db::name('spec')->alias('s')->join('__SPEC_TYPE__ t', 't.spec_id = s.id', 'LEFT')->where(['t.type_id' => $type_id])->order('s.id')->select();
        } else {
            $list = M('spec')->order('id')->select();
        }
//        $goods_category_list = M('goods_category')->where("id in(select cat_id1 from {$db_prefix}spec) ")->getField("id,name,parent_id");
        $goods_category_list = Db::name('goods_category')
            ->where('id', 'IN', function ($query) {
                $query->name('spec')->where('')->field('cat_id1');
            })
            ->getField("id,name,parent_id");
        $goods_category_list[0] = array('id' => 0, 'name' => '默认');
        asort($goods_category_list);
        $this->assign('goods_category_list', $goods_category_list);
        $this->assign('type_id', $type_id);
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**
     * 动态获取商品规格选择框 根据不同的数据返回不同的选择框
     */
    public function ajaxGetSpecSelect()
    {
        $goods_id = I('goods_id/d', 0);
        $cat_id3 = I('cat_id3/d', 0);
        empty($cat_id3) && exit('');
        $goods_id = $goods_id ? $goods_id : 0;

        $type_id = M('goods_category')->where("id", $cat_id3)->getField('type_id'); // 找到这个分类对应的type_id
        empty($type_id) && exit('');
        $spec_id_arr = M('spec_type')->where("type_id", $type_id)->getField('spec_id', true); // 找出这个类型的 所有 规格id
        empty($spec_id_arr) && exit('');

        $specList = D('Spec')->where("id", "in", implode(',', $spec_id_arr))->order('`order` desc')->select(); // 找出这个类型的所有规格
        if ($specList) {
            foreach ($specList as $k => $v) {
                $specList[$k]['spec_item'] = D('SpecItem')->where(['store_id' => STORE_ID, 'spec_id' => $v['id']])->getField('id,item'); // 获取规格项
            }
        }

        $items_id = M('SpecGoodsPrice')->where("goods_id", $goods_id)->getField("GROUP_CONCAT(`key` SEPARATOR '_') AS items_id");
        $items_ids = explode('_', $items_id);

        // 获取商品规格图片                
        if ($goods_id) {
            $specImageList = M('SpecImage')->where("goods_id", $goods_id)->getField('spec_image_id,src');
        }
        $this->assign('specImageList', $specImageList);

        $this->assign('items_ids', $items_ids);
        $this->assign('specList', $specList);
        return $this->fetch('ajax_spec_select');
    }

    /**
     * 动态获取商品规格输入框 根据不同的数据返回不同的输入框
     */
    public function ajaxGetSpecInput()
    {
        $GoodsLogic = new GoodsLogic();
        $goods_id = I('get.goods_id/d', 0);
        $spec_arr = I('spec_arr/a', []);
        $str = $GoodsLogic->getSpecInput($goods_id, $spec_arr, STORE_ID);
        $this->ajaxReturn(['status'=>1,'msg'=>'','result'=>$str]);
    }

    /**
     * 商家发布商品时添加的规格
     */
    public function addSpecItem()
    {
        $spec_id = I('spec_id/d', 0); // 规格id
        $spec_item = I('spec_item', '', 'trim');// 规格项

        $c = M('spec_item')->where(['store_id' => STORE_ID, 'item' => $spec_item, 'spec_id' => $spec_id])->count();
        if ($c > 0) {
            $return_arr = array(
                'status' => -1,
                'msg' => '规格已经存在',
                'data' => '',
            );
            exit(json_encode($return_arr));
        }
        $data = array(
            'spec_id' => $spec_id,
            'item' => $spec_item,
            'store_id' => STORE_ID,
        );
        M('spec_item')->add($data);

        $return_arr = array(
            'status' => 1,
            'msg' => '添加成功!',
            'data' => '',
        );
        exit(json_encode($return_arr));
    }

    /**
     * 商家发布商品时删除的规格
     */
    public function delSpecItem()
    {
        $spec_id = I('spec_id/d', 0); // 规格id
        $spec_item = I('spec_item', '', 'trim');// 规格项
        $spec_item_id = I('spec_item_id/d', 0); //规格项 id

        if (!empty($spec_item_id)) {
            $id = $spec_item_id;
        } else {
            $id = M('spec_item')->where(['store_id' => STORE_ID, 'item' => $spec_item, 'spec_id' => $spec_id])->getField('id');
        }

        if (empty($id)) {
            $return_arr = array('status' => -1, 'msg' => '规格不存在');
            exit(json_encode($return_arr));
        }
        $c = M("SpecGoodsPrice")->where("store_id", STORE_ID)->where(" `key` REGEXP :id1 OR `key` REGEXP :id2 OR `key` REGEXP :id3 or `key` = :id4")->bind(['id1' => '^' . $id . '_', 'id2' => '_' . $id . '_', 'id3' => '_' . $id . '$', 'id4' => $id])->count(); // 其他商品用到这个规格不得删除
        if ($c) {
            $return_arr = array('status' => -1, 'msg' => '此规格其他商品使用中,不得删除');
            exit(json_encode($return_arr));
        }
        M('spec_item')->where(['id' => $id, 'store_id' => STORE_ID])->delete(); // 删除规格项
        M('spec_image')->where(['spec_image_id' => $id, 'store_id' => STORE_ID])->delete(); // 删除规格图片选项
        $return_arr = array('status' => 1, 'msg' => '删除成功!');
        exit(json_encode($return_arr));
    }

    /**
     * 商品规格列表
     */
    public function specList()
    {
        $cat_list = M('goods_category')->where("parent_id = 0")->getField('id,name,parent_id'); // 已经改成联动菜单                
        $this->assign('cat_list', $cat_list);
        return $this->fetch();
    }

    /**
     *  商品规格列表
     */
    public function ajaxSpecList()
    {
        //ob_start('ob_gzhandler'); // 页面压缩输出
        $cat_id3 = I('cat_id3/d', 0);
        $spec_id = I('spec_id/d', 0);
        $type_id = M('goods_category')->where("id", $cat_id3)->getField('type_id'); // 获取这个分类对应的类型
        if (empty($cat_id3) || empty($type_id)) exit('');

        $spec_id_arr = M('spec_type')->where("type_id", $type_id)->getField('spec_id', true); // 获取这个类型所拥有的规格
        if (empty($spec_id_arr)) exit('');

        $spec_id = $spec_id ? $spec_id : $spec_id_arr[0]; //没有传值则使用第一个

        $specList = M('spec')->where("id", "in", implode(',', $spec_id_arr))->getField('id,name,cat_id1,cat_id2,cat_id3');
        $specItemList = M('spec_item')->where(['store_id' => STORE_ID, 'spec_id' => $spec_id])->order('id')->select(); // 获取这个类型所拥有的规格
        //I('cat_id1')   && $where = "$where and cat_id1 = ".I('cat_id1') ;                       
        $this->assign('spec_id', $spec_id);
        $this->assign('specList', $specList);
        $this->assign('specItemList', $specItemList);
        return $this->fetch();
    }

    /**
     * 删除商品相册图
     */
    public function del_goods_images()
    {
        $path = I('filename', '');
        $goods_images = M('goods_images')->where(array('image_url' => $path))->select();
        foreach ($goods_images as $key => $val) {
            $goods = M('goods')->where(array('goods_id' => $goods_images[$key]['goods_id']))->find();
            if ($goods['store_id'] == STORE_ID) {
                M('goods_images')->where(array('img_id' => $goods_images[$key]['img_id']))->delete();
            }
        }
    }
}