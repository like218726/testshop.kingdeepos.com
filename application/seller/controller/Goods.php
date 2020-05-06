<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 商业用途务必到官方购买正版授权, 使用盗版将严厉追究您的法律责任。
 * ============================================================================
 * Author: IT宇宙人
 * Date: 2015-09-09
 */
namespace app\seller\controller;

use app\common\model\SpecGoodsPrice;
use app\common\model\Goods as GoodsModel;
use app\common\model\StoreBindPlatformGoods;
use app\seller\logic\GoodsCategoryLogic;
use app\seller\logic\GoodsLogic;
use app\seller\logic\AdminLogic;
use think\Db;
use think\Page;
use think\AjaxPage;
use think\Loader;

class Goods extends Base
{

    /**
     * 删除分类
     */
    public function delGoodsCategory()
    {
        // 判断子分类
        $GoodsCategory = M("GoodsCategory");
        $count = $GoodsCategory->where("parent_id", $_GET['id'])->count("id");
        $count > 0 && $this->error('该分类下还有分类不得删除!', U('Admin/Goods/categoryList'));
        // 判断是否存在商品
        $goods_count = M('Goods')->where("cat_id", $_GET['id'])->count('1');
        $goods_count > 0 && $this->error('该分类下有商品不得删除!', U('Admin/Goods/categoryList'));
        // 删除分类
        $GoodsCategory->where("id", $_GET['id'])->delete();
        $this->success("操作成功!!!", U('Admin/Goods/categoryList'));
    }

    /**
     *  商品列表
     */
    public function goodsList()
    {
        checkIsBack();
        $nowPage = 1;
        $store_goods_class_list = M('store_goods_class')->where(['parent_id' => 0, 'store_id' => STORE_ID])->select();
        $this->assign('store_goods_class_list', $store_goods_class_list);
        $suppliers_list = M('suppliers')->where(array('store_id'=>STORE_ID))->select();
        $this->assign('suppliers_list', $suppliers_list);
        $this->assign("now_page" , $nowPage);
        return $this->fetch('goodsList');
    }

    /**
     *  商品列表
     */
    public function ajaxGoodsList()
    {
        $where['store_id'] = STORE_ID;
		$where['purpose'] = 1;
        $intro = I('intro', 0);
        $store_cat_id1 = I('store_cat_id1', '');
        $key_word = trim(I('key_word', ''));
        $orderby1 = I('post.orderby1', '');
        $orderby2 = I('post.orderby2', '');
        $suppliers_id = input('suppliers_id','');
        if($suppliers_id !== ''){
            $where['suppliers_id'] = $suppliers_id;
        }
        if (!empty($intro)) {
            $where[$intro] = 1;
        }
        if ($store_cat_id1 !== '') {
            $where['store_cat_id1'] = $store_cat_id1;
        }
        $where['is_on_sale'] = 1;
        $where['goods_state'] = 1;
        if ($key_word !== '') {
            $where['goods_name|goods_sn'] = array('like', '%' . $key_word . '%');
        }
        $order_str = array();
        if ($orderby1 !== '') {
            $order_str[$orderby1] = $orderby2;
        }
        $model = M('Goods');
        $count = $model->where($where)->count();
        $Page = new AjaxPage($count, 10);

        //是否从缓存中获取Page
        if (session('is_back') == 1) {
            $Page = getPageFromCache();
            //重置获取条件
            delIsBack();
        }
        $goodsList = $model->where($where)->order($order_str)->limit($Page->firstRow . ',' . $Page->listRows)->select();
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
		$where['purpose'] = 1; //本店售卖的商品
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

    public function stock_log()
    {
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
        $ctime = urldecode(I('post.ctime'));
        if ($ctime) {
            $gap = explode(' - ', $ctime);
            $this->assign('ctime', $gap[0] . ' - ' . $gap[1]);
            $this->assign('start_time', $gap[0]);
            $this->assign('end_time', $gap[1]);
            $map['ctime'] = array(array('gt', strtotime($gap[0])), array('lt', strtotime($gap[1])));
        }
        $count = Db::name('stock_log')->where($map)->count();
        $Page = new Page($count, 20);
        $show = $Page->show();
        $this->assign('page', $show);// 赋值分页输出
        $stock_list = Db::name('stock_log')->where($map)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('stock_list', $stock_list);
        return $this->fetch();
    }
    
    public function stock_list(){
    	$map['store_id'] = STORE_ID;
    	$goods_name = I('goods_name');
    	$spec_name = I('spec_name');
    	if ($goods_name) {
    		$map['goods_name'] = array('like', "%$goods_name%");
    	}
    	if($spec_name){
    		$map['key_name'] = array('like', "%$spec_name%");
    	}
        $count = Db::view('goods','goods_id,goods_name,goods_sn,shop_price,store_count,store_id')
            ->view('spec_goods_price','item_id,price,store_count as spec_store_count,key_name','spec_goods_price.goods_id=goods.goods_id','LEFT')
            ->where($map)
            ->limit(10)
            ->count();
        if($count>20){
            $Page = new Page($count, 20);
            $show = $Page->show();
            $this->assign('page', $show);// 赋值分页输出
        }
        $stock_list = Db::view('goods','goods_id,root_goods_id,goods_name,goods_sn,shop_price,store_count,store_id,prom_type,store_id')
            ->view('spec_goods_price','item_id,price,store_count as spec_store_count,key_name,prom_type as spec_prom_type','spec_goods_price.goods_id=goods.goods_id','LEFT')
            ->where($map)
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->order('goods_id desc')
            ->select();
        foreach($stock_list as $key=>$val){
            if(!empty($val['item_id'])){
                $stock_list[$key]['store_count'] = $val['spec_store_count'];
                $stock_list[$key]['shop_price'] = $val['price'];
                $stock_list[$key]['prom_type'] = $val['spec_prom_type'];
            }
        }
    	$this->assign('stock_list', $stock_list);
    	return $this->fetch();
    }
    
    public function updateGoodsStock(){
        $goods_id = input('goods_id');

        $item_id = I('item_id/d');
        $store_count = I('store_count/d');
        $old_stock = I('old_stock');
        $spec_goods = Db::name('goods')->alias('g')->field('s.*,g.goods_name,g.root_goods_id')->join('spec_goods_price s', 'g.goods_id = s.goods_id', 'LEFT')->where(['g.goods_id'=>$goods_id])->find();
        if ($spec_goods['root_goods_id'] > 0) {
            exit(json_encode(array('status'=>0,'msg'=>'供应商品，本店铺无法修改，修改失败')));
        }
        if(empty($item_id)){
            $r = Db::name('goods')->where('goods_id',$goods_id)->update(['store_count'=>$store_count]);
            //$spec_goods = Db::name('goods')->field('goods_id,goods_name')->where('goods_id',$goods_id)->find();
            $spec_goods['key_name'] = '无';
        }else{
            /*$spec_goods = Db::name('spec_goods_price')->alias('s')->field('s.*,g.goods_name')->join('__GOODS__ g', 'g.goods_id = s.goods_id', 'LEFT')->where(['s.item_id'=>$item_id])->find();*/
            $r = M('spec_goods_price')->where(array('item_id'=>$item_id))->save(array('store_count'=>$store_count));
        }

    	if($r){
    		$stock = $store_count - $old_stock;
    		$goods = array('goods_id'=>$spec_goods['goods_id'],'goods_name'=>$spec_goods['goods_name'],'key_name'=>$spec_goods['key_name'],'store_id'=>STORE_ID);
    		update_stock_log(STORE_ID, $stock,$goods);
    		exit(json_encode(array('status'=>1,'msg'=>'修改成功')));
    	}else{
    		exit(json_encode(array('status'=>0,'msg'=>'修改失败')));
    	}
    }

    /**
     *
     */
    public function addStepOne(){
        //限制发布商品数量，0为不限制
        $alreadyPushNum = Db::name('goods')->where(['store_id' => STORE_ID])->count();
        $sgGoodsLimit = Db::name('store_grade')->where(['sg_id' => $this->storeInfo['grade_id']])->getField('sg_goods_limit');
        if($alreadyPushNum >= $sgGoodsLimit && $sgGoodsLimit > 0 && $this->storeInfo['is_own_shop'] !=1){
            $this->error("可发布商品数量已达到上限", U('Goods/goodsList'));
        }
        $goods_id = input('goods_id');
        if($goods_id){
            $goods = Db::name('goods')->where('goods_id',$goods_id)->find();
            $this->assign('goods',$goods);
        }
        $GoodsCategoryLogic = new GoodsCategoryLogic();
        $GoodsCategoryLogic->setStore($this->storeInfo);
        $goodsCategoryLevelOne = $GoodsCategoryLogic->getStoreGoodsCategory();
        $this->assign('goodsCategoryLevelOne',$goodsCategoryLevelOne);
        return $this->fetch();
    }

    /**
     * 相册图片
     */
    public function album_image(){
        
        $album_id = I('album_id/d' , 0);
        /* 列出图片 */
        $allowFiles = 'png|jpg|jpeg|gif|bmp';
        $path = UPLOAD_PATH.'store/'.session('store_id').'/goods_other_album/album_'.$album_id;
        $key = empty($_GET['key']) ? '' : $_GET['key'];
        $listSize = 20;

        /* 获取参数 */
        $size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
    	$page = isset($_GET['p']) ? htmlspecialchars($_GET['p']) : 1;
    	$start = ($page-1)*$size;
    	$end = $start + $size;

        /* 获取文件列表 */
        $adminLogc = new AdminLogic();
        $file = $adminLogc->getfiles($path, $allowFiles, $key ,true);
        $urls = array();
        if (!count($file)) {
            $this->assign('imageList',array());
        }else{
            /* 获取指定范围的列表 */
            $len = count($file);
            for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
                $list[] = $file[$i];
                array_push($urls , $file[$i]['url']);
            }
            $where = implode($urls, ",");
            $extends = M('Image_extend')->where('img_url' , 'in' , $where)->getField("img_url , cn_name , en_name" , true);
            /* 返回数据 */
            $Page = new Page($len, $size);
            $show = $Page->show();
            $this->assign('show_page', $show);
            $this->assign('imageList',$list);
            $this->assign('extends',$extends);
        }
          
        $this->assign("album_id" , $album_id);
         
        return $this->fetch();
        
    }
    
    
    /**
     * 添加修改商品
     */
    public function set_mobile_url(){
        
        $album_id = I('album_id/d' , 0);
        $album_img = I('filename/s' , '');
        
        if(empty($album_id) || empty($album_img))$this->ajaxReturn(['status'=>-1 , 'msg'=>'相册ID或图片路径错误']);
        
        $row = M('StoreAlbum')->where(['id'=>$album_id])->save(['album_img'=>$album_img]);
        
        if($row>0)$this->ajaxReturn(['status'=>1 , 'msg'=>'设置成功']);
    }
    
    
    
    /**
     * 添加修改商品
     */
    public function addEditGoods()
    {
        $goods_id = I('goods_id/d', 0);
        $goods_cat_id3 = I('cat_id3/d', 0);
        $purpose = I('purpose/d', 0);
		if ($purpose == 1 && !$this->store['is_dealer']) {
			$this->error("你不是销售商，无法发布销售商品");
		}
		if ($purpose == 2 && !$this->store['is_supplier']) {
			$this->error("你不是供应商，无法发布供应商品");
		}
        if(empty($goods_id)){
            if(empty($goods_cat_id3)){
                $this->error("您选择的分类不存在，或没有选择到最后一级，请重新选择分类。", U('Goods/addStepOne'));
            }
            $goods_cat[0] = Db::name('goods_category')->where('id', $goods_cat_id3)->find();
            $goods_cat[1] = Db::name('goods_category')->where('id', $goods_cat[0]['parent_id'])->find();
            $goods_cat[2] = Db::name('goods_category')->where('id', $goods_cat[1]['parent_id'])->find();
            $cat_id = $goods_cat[2]['id'];
        }else{
            $Goods = new GoodsModel();
            $goods_info = $Goods->where(['goods_id' => $goods_id, 'store_id' => STORE_ID])->find();
            if(empty($goods_info)){
                $this->error("非法操作", U('Goods/goodsList'));
            }else{
                $this->assign('goodsInfo', $goods_info);  // 商品详情
				$purpose = $goods_info['purpose'];
            }
            $cat_id = $goods_info['cat_id1'];
            $goods_cat = Db::name('goods_category')->where('id','IN',[$goods_info['cat_id1'],$goods_info['cat_id2'],$goods_info['cat_id3']])->order('level desc')->select();

            //判断该商品的经营类目店铺有没有
            $bind_class = Db::name('store_bind_class')->where(['class_1' => $goods_info['cat_id1'], 'class_2' => $goods_info['cat_id2'], 'class_3' => $goods_info['cat_id3'], 'store_id' => STORE_ID])->find();
            if ($bind_class) {
                $goods_info['bind_class_state'] = $bind_class['state'];
            } else {
                $goods_info['bind_class_state'] = -1; //表示无此类目
            }
        }
        //$cat_arr = get_arr_column($goods_cat, 'id');

        //版式
        $plate_1=M('store_plate')->where('plate_position=1 and store_id='.STORE_ID)->field('plate_id,plate_name')->select();
        $plate_0=M('store_plate')->where('plate_position=0 and store_id='.STORE_ID)->field('plate_id,plate_name')->select();
        $this->assign('plate_1',$plate_1);
        $this->assign('plate_0',$plate_0);
        

        $store_goods_class_list = Db::name('store_goods_class')->where(['parent_id' => 0, 'store_id' => STORE_ID])->select(); //店铺内部分类

        // 仅查顶级分类,平台的分类也可以用
        $brandList = db('brand')->where('cat_id1',$cat_id)->where('store_id','in',[0,STORE_ID])->select();
        $goodsType = Db::name("GoodsType")->select();
        $suppliersList = Db::name("suppliers")->where(['is_check'=>1,'store_id'=>STORE_ID])->select();
        $goodsImages = Db::name("GoodsImages")->where('goods_id', $goods_id)->order('img_sort asc')->select();
        $freight_template = Db::name('freight_template')->where(['store_id' => STORE_ID])->select();
        $this->assign('freight_template',$freight_template);
        $this->assign('goods_cat', $goods_cat);
        $this->assign('purpose', $purpose);
        $this->assign('store_id', STORE_ID);
        $this->assign('store_goods_class_list', $store_goods_class_list);
        $this->assign('brandList', $brandList);
        $this->assign('goodsType', $goodsType);
        $this->assign('suppliersList', $suppliersList);
        $this->assign('goodsImages', $goodsImages);  // 商品相册
		if ($purpose == 1) {
			return $this->fetch('_goods'); //普通店铺商品
		} else {
			return $this->fetch('_supply_goods');  //供应商品
		}
    }

    /**
     * 普通商品保存
     */
    public function save(){
		if (!$this->store['is_dealer']) {
			$this->error("你不是销售商，无法发布编辑销售商品");
		}
        // 数据验证
        $data =input('post.');
        $goods_id = input('post.goods_id');
        $goods_cat_id3 = input('post.cat_id3');
        $spec_goods_item = input('post.item/a',[]);
        $store_count = input('post.store_count');
        $is_virtual = input('post.is_virtual');
        $virtual_indate = I('post.virtual_indate');//虚拟商品有效期
        $exchange_integral = I('post.exchange_integral');//虚拟商品有效期
        $validate = Loader::validate('Goods');
        $data['store_id'] = STORE_ID;
		if (!$validate->batch()->scene('save')->check($data)) {
            $error = $validate->getError();
            $error_msg = array_values($error);
            $return_arr = array('status' => -1,'msg' => $error_msg[0],'data' => $error);
            $this->ajaxReturn($return_arr);
        }
        $data['on_time'] = time(); // 上架时间
        $type_id = M('goods_category')->where("id", $goods_cat_id3)->getField('type_id'); // 找到这个分类对应的type_id
        $stores = M('store')->where(array('store_id' => STORE_ID))->getField('store_id , goods_examine,is_own_shop' , 1);
        $store_goods_examine = $stores[STORE_ID]['goods_examine'];
        if ($store_goods_examine) {
            $data['goods_state'] = 0; // 待审核
            $data['is_on_sale'] = 0; // 下架
        } else {
            $data['goods_state'] = 1; // 出售中
        }
        //总平台自营标识为2 , 第三方自营店标识为1
        $is_own_shop = (STORE_ID == 1) ? 2 : ($stores[STORE_ID]['is_own_shop']);
        $data['is_own_shop'] = $is_own_shop;
        $data['goods_type'] = $type_id ? $type_id : 0;
        $data['virtual_indate'] = strtotime($virtual_indate)>0 ? strtotime($virtual_indate) : 0;
        $data['exchange_integral'] = ($is_virtual == 1) ? 0 : $exchange_integral;
        //序列化保存手机端商品描述数据
        if ($_POST['m_body'] != '') {
        	$_POST['m_body'] = str_replace('&quot;', '"', $_POST['m_body']);
        	$_POST['m_body'] = json_decode($_POST['m_body'], true);
        	if (!empty($_POST['m_body'])) {
        		$_POST['m_body'] = serialize($_POST['m_body']);
        	} else {
        		$_POST['m_body'] = '';
        	}
        }
        $data['mobile_content'] = $_POST['m_body'];
        
        if ($goods_id > 0) {
            $Goods = GoodsModel::get(['goods_id' => $goods_id, 'store_id' => STORE_ID]);
            if(empty($Goods)){
                $this->ajaxReturn(array('status' => 0, 'msg' => '非法操作','result'=>''));
			}
            if (in_array($Goods['supplier_goods_status'], [1,2])) {
                //当源供应商品有必要供货数据修改，或停止供货时
                $data['goods_state'] = 0; // 待审核
                $data['is_on_sale'] = 0; // 下架
            }
            if (empty($spec_goods_item) && $store_count != $Goods['store_count']) {
                $real_store_count = $store_count - $Goods['store_count'];
                update_stock_log(session('admin_id'), $real_store_count, array('goods_id' => $goods_id, 'goods_name' => $Goods['goods_name'], 'store_id' => STORE_ID));
            } else {
                unset($data['store_count']);
            }
			//当时供应商品时，过滤掉运费、成本价、库存
			if ($Goods['root_goods_id'] > 0) {
				if (isset($data['store_count'])) {
					unset($data['store_count']);
				}
				unset($data['is_free_shipping']);
				unset($data['template_id']);
				unset($data['cost_price']);
			}
            $Goods->data($data, true); // 收集数据
            $update = $Goods->save(); // 写入数据到数据库
            // 更新成功后删除缩略图
            if($update !== false){
                // 修改商品后购物车的商品价格也修改一下
                Db::name('cart')->where("goods_id", $goods_id)->where("spec_key = ''")->save(array(
                    'market_price' => $Goods['market_price'], //市场价
                    'goods_price' => $Goods['shop_price'], // 本店价
                    'member_goods_price' => $Goods['shop_price'], // 会员折扣价
                ));
                delFile("./public/upload/goods/thumb/$goods_id", true);
            }
        } else {
            $Goods = new GoodsModel();
            $Goods->data($data, true); // 收集数据
            $Goods->save(); // 新增数据到数据库
            $goods_id = $Goods->getLastInsID();
            //商品进出库记录日志
            if (empty($spec_goods_item)) {
                update_stock_log(session('admin_id'), $store_count, array('goods_id' => $goods_id, 'goods_name' => $Goods['goods_name'], 'store_id' => STORE_ID));
            }
        }
        $GoodsLogic = new GoodsLogic();
        $GoodsLogic->afterSave($goods_id, STORE_ID);
        $GoodsLogic->saveGoodsAttr($goods_id, $type_id); // 处理商品 属性
        if(input('goods_id')){
            delFile("./public/upload/goods/thumb/$goods_id"); // 删除缩略图
            clearCache();
        }
        $this->ajaxReturn([ 'status' => 1, 'msg' => '操作成功', 'result' => ['goods_id'=>$Goods->goods_id]]);
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
        $ids= I('ids');
        $GoodsLogic = new GoodsLogic();
        $res = $GoodsLogic->delStoreGoods($ids);
        $this->ajaxReturn($res);
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

        //$items_id = M('SpecGoodsPrice')->where("goods_id", $goods_id)->getField("GROUP_CONCAT(`key` SEPARATOR '_') AS items_id");
		$items_id = M('SpecGoodsPrice')->where("goods_id", $goods_id)->getField("key", true);
        $items_id = implode('_', $items_id);
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
        $purpose = I('purpose/i', 1);
        $root_goods_id = I('root_goods_id/i', 0); //根源商品id，用来判断是不是供应商品
        $str = $GoodsLogic->getSpecInput($goods_id, $spec_arr, STORE_ID, $purpose, $root_goods_id);
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
        $count = db('spec_item')->where(['spec_id'=>$spec_id,'store_id'=>STORE_ID])->count();
        if ($count >= 15) {
            $return_arr = array(
                'status' => -1,
                'msg' => '规格值最多添加15个',
                'data' => '',
            );
            //exit(json_encode($return_arr));
        }
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
        $c = M("SpecGoodsPrice")->where("store_id", STORE_ID)->where(" `key` REGEXP :id1 OR `key` REGEXP :id2 OR `key` REGEXP :id3 or `key` = :id4")->bind(['id1' => '^' . $id . '_', 'id2' => '_' . $id . '_', 'id3' => '_' . $id . '$', 'id4' => $id])->select(); // 其他商品用到这个规格不得删除
        if ($c) {
            $return_arr = array('status' => -1, 'msg' => '当前有商品在使用该规格值，不能删除');
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
        $GoodsCategoryLogic = new GoodsCategoryLogic();
        $GoodsCategoryLogic->setStore($this->storeInfo);
        $goodsCategoryLevelOne = $GoodsCategoryLogic->getStoreGoodsCategory();
        $this->assign('cat_list', $goodsCategoryLevelOne);
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
    
    public function ajaxAlbumList(){
        $list = Db::name('StoreAlbum')->where(array('store_id' => STORE_ID))->order('sort asc')->select();
        $html = '';
        foreach ($list as $k => $v)
            $html .= "<option value='{$v['id']}'>{$v['album_name']}</option>";
        exit($html);
    }

    /**
     *  批量添加修改规格
     */
    public function batchAddSpecItem()
    {
        $spec_id = I('spec_id/d', 0);
        $item = I('item/a');
        if (count($item)>15) {
            $this->ajaxReturn(['status'=>0,'msg'=>'规格值不能超过15','result'=>'']);
        }
        if (in_array('',$item)) {
            $this->ajaxReturn(['status'=>0,'msg'=>'规格值不能为空','result'=>'']);
        }
        $spec_item = M('spec_item')->where(['store_id' => STORE_ID, 'spec_id' => $spec_id])->getField('id,item');
        $spec_item_id_array = [];
        foreach ($item as $k => $v) {
            $spec_item_id = 0;
            $v = trim($v);
            if (empty($v)) continue; // 值不存在 则跳过不处理
            // 如果spec_id 存在 并且 值不相等 说明值被改动过
            if (array_key_exists($k, $spec_item) && $v != $spec_item[$k]) {
                M('spec_item')->where(['id' => $k, 'store_id' => STORE_ID])->save(array('item' => $v));
                // 如果这个key不存在 并且规格项也不存在 说明 需要插入
            } elseif (!array_key_exists($k, $spec_item) && !in_array($v, $spec_item)) {
                $spec_item_id = M('spec_item')->add(array('spec_id' => $spec_id, 'item' => $v, 'store_id' => STORE_ID));
            }
            if ($spec_item_id) {
                $spec_item_id_array[] = $spec_item_id;
            }
        }
        $this->ajaxReturn(['status'=>1,'msg'=>'保存成功','result'=>$spec_item_id_array]);
    }

   
    
    /**
     * 品牌列表
     */
    public function brandList()
    {
        $keyword = I('keyword');
        $brand_where['store_id'] = STORE_ID;
        if ($keyword) {
            $brand_where['name'] = ['like', '%' . $keyword . '%'];
        }
        $count = Db::name('brand')->where($brand_where)->count();
        $Page = new Page($count, 16);
        $brandList = Db::name('brand')->where($brand_where)->order("`sort` asc")->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $show = $Page->show();
        $cat_list = M('goods_category')->where("parent_id = 0")->getField('id,name'); // 已经改成联动菜单
        $this->assign('cat_list', $cat_list);
        $this->assign('show', $show);
        $this->assign('brandList', $brandList);
        return $this->fetch('brandList');
    }

    /**
     * 添加修改编辑  商品品牌
     */
    public function addEditBrand()
    {
        $id = I('id/d', 0);
        if (IS_POST) {
            $data = input('post.');
            $brandVilidate = Loader::validate('Brand');
            if (!$brandVilidate->batch()->check($data)){
                $error_msg = '';
                foreach ($brandVilidate->getError() as $key =>$value){
                    $error_msg .= $value.'，';
                }
                $this->ajaxReturn(['status'=>-1,'msg'=>$error_msg]);
            }
            if ($id) {
                Db::name('brand')->update($data);
            } else {
                $data['store_id'] = STORE_ID;
                M("Brand")->insert($data);
            }
            $this->ajaxReturn(['status'=>1,'msg'=>'操作成功！！','url'=>U('Goods/brandList')]);
        }
        $GoodsCategoryLogic = new GoodsCategoryLogic();
        $GoodsCategoryLogic->setStore($this->storeInfo);
        $goodsCategoryLevelOne = $GoodsCategoryLogic->getStoreGoodsCategory();
        $this->assign('cat_list', $goodsCategoryLevelOne);
        $brand = Db::name('brand')->where(array('id' => $id, 'store_id' => STORE_ID))->find();
        $this->assign('brand', $brand);
        return $this->fetch('_brand');
    }

    /**
     * 添加/编辑相册
     */
    public function addEditAlbum()
    {
        $id = I('id/d', 0);
        if (IS_POST) {
            $data = input('post.');
            $store_id = session('store_id');
            $savePath = 'store/'.$store_id.'/goods/';
            
            if(empty($id)){
                $storeAlbum = M('StoreAlbum')->where(['album_name'=>$data['album_name']])->find();
                $storeAlbum && $this->ajaxReturn(['status'=>-1,'msg'=>'已经存在相同相册','url'=>U('Goods/albumList')]);
            }
            
            // 获取表单上传文件 例如上传了001.jpg
            $album_file = request()->file('album_img');
            // 移动到框架应用根目录/public/uploads/ 目录下
            if($album_file){
                $info = $album_file->move('public/upload/'.$savePath);
                if($info){
                    // 成功上传后 获取上传信息
                    $return_url = '/public/upload/'.$savePath.$info->getSaveName();
                    $data['album_img'] = $return_url;
                }else{
                    // 上传失败获取错误信息
                    $upload_error = $album_file->getError();
                    $this->ajaxReturn(['status'=>-1,'msg'=>'相册图上传失败:'.$upload_error,'url'=>U('Goods/albumList')]);
                }
            } 
            
            if($data['is_default'] == 1){
                M('StoreAlbum')->where(['sort'=>0])->update(['sort'=>1]);
                $data['sort'] = 0; 
            }else{
                $data['sort'] = 1;
            }
                 
            if ($id) {
                Db::name('StoreAlbum')->update($data);
            } else {
                $data['store_id'] = STORE_ID;
                M("StoreAlbum")->insert($data);
            }
            $this->ajaxReturn(['status'=>1,'msg'=>'操作成功！！','url'=>U('Goods/albumList')]);
        }
    
        $album = Db::name('StoreAlbum')->where(array('id' => $id, 'store_id' => STORE_ID))->find();
        $this->assign('album', $album);
        return $this->fetch('_album');
    }
     
    /**
     * 相册列表列表
     */
    public function albumList()
    {
        $keyword = I('keyword');
        $album_model = Db::name('StoreAlbum');
        $album_where['store_id'] = STORE_ID;
        if ($keyword) {
            $album_where['album_name'] = ['like', '%' . $keyword . '%'];
        }
        
        $allowFiles = 'png|jpg|jpeg|gif|bmp';
        $listSize = 100000;
         
         
        $count = $album_model->where($album_where)->count();
        $Page = new Page($count, 16);
        $albumList = $album_model->where($album_where)->order("id desc")->limit($Page->firstRow . ',' . $Page->listRows)->select();
       
        $show = $Page->show();
        
        /* 获取文件列表 */
        $adminLogc = new AdminLogic();
        foreach ($albumList as $k => $v){
            $path = UPLOAD_PATH.'store/'.session('store_id').'/goods_other_album/album_'.$v['id'];
            $imageList = $adminLogc->getfiles($path, $allowFiles, '' ,true);
            $albumList[$k]['count'] = count($imageList);
        }
       
        $this->assign('show', $show);
        $this->assign('albumList', $albumList);
    
        return $this->fetch('albumList');
    }
    
    /**
     * 删除品牌
     */
    public function delBrand()
    {
        $model = M("Brand");
        $id = I('id/d');
        $model->where(['id' => $id, 'store_id' => STORE_ID])->delete();
        $return_arr = array('status' => 1, 'msg' => '操作成功', 'data' => '',);
        $this->ajaxReturn($return_arr);
    }
    
    
    /**
     * 删除品牌
     */
    public function delAlbum()
    {
        $model = M("Brand");
        $id = I('id/d');
        //删除此相册下的所有图片
        $albumPath = UPLOAD_PATH.'store/'.session('store_id').'/goods/album_'.$id;
        $isSuccessful = delFile($albumPath , true);
        //删除此相册数据库记录
        $row = M("StoreAlbum")->where(['id'=>$id])->delete();
        if($row > 0){
            $this->ajaxReturn(['status'=>1 , 'msg'=>'删除成功']);
        }else{
            $this->ajaxReturn(['status'=>-1 , 'msg'=>'删除失败']);
        }
        
    }

    public function brand_save()
    {
        $data = I('post.');
        if ($data['act'] == 'del') {
            $goods_count = M('Goods')->where("brand_id", $data['id'])->count('1');
            if ($goods_count) respose(array('status' => -1, 'msg' => '此品牌有商品在用不得删除!'));
            $r = M('brand')->where('id', $data['id'])->delete();
            if ($r) {
                respose(array('status' => 1));
            } else {
                respose(array('status' => -1, 'msg' => '操作失败'));
            }
        } else {
            if (empty($data['id'])) {
                $data['store_id'] = STORE_ID;
                $r = M('brand')->add($data);
            } else {
                $r = M('brand')->where('id', $data['id'])->save($data);
            }
        }
        if ($r) {
            $this->success("操作成功", U('Store/brand_list'));
        } else {
            $this->error("操作失败", U('Store/brand_list'));
        }
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

    /**
     * 重新申请商品审核
     */
    public function goodsUpLine()
    {
        $goods_ids = input('goods_ids');
        $res = Db::name('goods')->where('goods_id', 'in', $goods_ids)->where('store_id', STORE_ID)->update(['is_on_sale' => 0, 'is_supply' => 0, 'goods_state' => 0]);
        if($res !== false){
            $this->success('操作成功');
        }else{
            $this->success('操作失败');
        }

    }
    
    public function pic_list(){
        
        $albumList = M('StoreAlbum')->where(['store_id' => STORE_ID])->order("sort asc")->select();
        
    	$path = UPLOAD_PATH.'store/'.session('store_id').'/goods_other_album';
    	$listSize = 14;
    	
    	$album_id = I('album_id/d' , 0);
    	
    	if($album_id < 1){//如果没指定相册, 默认显示第一个
    	    $album_id = M('StoreAlbum')->order('sort','asc')->getField('id');
    	}
    	
    	if($album_id > 0){
    	    $path .= "/album_$album_id";
    	}
    	
    	$key = empty($_GET['key']) ? '' : $_GET['key'];
    	/* 获取参数 */
    	$size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
    	$page = isset($_GET['p']) ? htmlspecialchars($_GET['p']) : 1;
    	$start = ($page-1)*$size;
    	$end = $start + $size;
     
    	/* 获取文件列表 */
    	$allowFiles = 'png|jpg|jpeg|gif|bmp';
    	$adminLogc = new AdminLogic();
    	$files = $adminLogc->getfiles($path, $allowFiles, $key);
    	if (!count($files)) {
    		$this->assign('result',array());
    	}else{
    		/* 获取指定范围的列表 */
    		$len = count($files);
    		$urls = array();
    		for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
    			$list[] = $files[$i];
    			array_push($urls, $files[$i]['url']);
    		}
    		
    		$where = implode($urls, ",");
    		$extends = M('Image_extend')->where('img_url' , 'in' , $where)->getField("img_url , cn_name , en_name" , true);
    		$returnList = array();
    		foreach ($list as $k =>$v){
    		    $extends[$v['url']]['cn_name'] && $list[$k]['name'] = $extends[$v['url']]['cn_name'];
    		}
    	 
    		/* 返回数据 */
    		$Page = new Page($len, $size);
    		$show = $Page->show();
    		$this->assign('show_page', $show);
    		$this->assign('result',$list);
    	}
    	
    	if(IS_POST){
    	    $this->ajaxReturn($albumList);
    	}
    	
    	$this->assign('album_id',$album_id);
    	$this->assign('albumList',$albumList);
    	return $this->fetch();
    }
    
     
    /**
     * 获取商品分类
     */
    public function getSellerBindCategory()
    {
        $parent_id = I('parent_id/d', '0'); // 商品分类 父id
        $rank = I('rank'); // 商品分类 父id
        $next_class = I('next_class'); // 商品分类 父id
        empty($parent_id) && $this->ajaxReturn(['status'=>1,'msg'=>'','data'=>[]]);
        if($this->storeInfo['bind_all_gc']==1){
            $list = Db::name('goods_category')->where(['parent_id'=>$parent_id])->select();
        }else{
            $where=['store_id' => STORE_ID, "$rank" => $parent_id, 'state' => 1];
            $bind_class_arr = Db::name('store_bind_class')->where($where)->group("$next_class")->getField("$next_class",true);
            $bind_class_ids = implode(',',$bind_class_arr);
            $list = Db::name('goods_category')->whereIn('id',$bind_class_ids)->select();
        }
        if ($list){
            $this->ajaxReturn(['status'=>1,'msg'=>'获取成功','data'=>$list]);
        }
        $this->ajaxReturn(['status'=>-1,'msg'=>'请先添加绑定分类','data'=>'']);
    }

    /**
     * 平台商品列表
     * @return mixed
     */
    public function platformGoods()
    {
        checkIsBack();
        $nowPage = 1;
        $this->assign("now_page" , $nowPage);
        //店铺分类
//        $store_goods_class_list = db('store_goods_class')->where(['parent_id' => 0, 'store_id' => STORE_ID])->select();
//        $this->assign('store_goods_class_list', $store_goods_class_list);
//        $suppliers_list = M('suppliers')->where(array('store_id'=>STORE_ID))->select();
//        $this->assign('suppliers_list', $suppliers_list);
        return $this->fetch();
    }


    /**
     * 平台商品列表分页
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function ajaxPlatformGoods()
    {
        //先获取自营店默认平台商品
        $store_id = db('store')->where(['default_store'=>1,'store_type'=>0,'is_own_shop'=>1])->value('store_id');
        $where['store_id'] = $store_id;
        $intro = I('intro', 0);
        $store_cat_id1 = I('store_cat_id1', '');
        $key_word = trim(I('key_word', ''));
        $orderby1 = I('post.orderby1', '');
        $orderby2 = I('post.orderby2', '');
        $suppliers_id = input('suppliers_id','');
        if($suppliers_id !== ''){
            $where['suppliers_id'] = $suppliers_id;
        }
        if (!empty($intro)) {
            $where[$intro] = 1;
        }
        if ($store_cat_id1 !== '') {
            $where['store_cat_id1'] = $store_cat_id1;
        }
        $where['is_on_sale'] = 1;
        $where['store_count'] = ['>',0];
        $where['goods_state'] = 1;
        if ($key_word !== '') {
            $where['goods_name|goods_sn'] = array('like', '%' . $key_word . '%');
        }
        $order_str = array();
        if ($orderby1 !== '') {
            $order_str[$orderby1] = $orderby2;
        }
        $model = new \app\common\model\Goods();
        $count = $model->where($where)->count();
        $Page = new AjaxPage($count, 10);

        //是否从缓存中获取Page
        if (session('is_back') == 1) {
            $Page = getPageFromCache();
            //重置获取条件
            delIsBack();
        }
        $goodsList = $model->where($where)->order($order_str)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        cachePage($Page);
        $show = $Page->show();
        //店铺分类
        $store_goods_class_list = db('store_goods_class')->where(['parent_id' => 0, 'store_id' => STORE_ID])->select();
        $this->assign('store_goods_class_list', $store_goods_class_list);
//        $store = db('store')->where(['store_id' => STORE_ID])->find();
//        $this->assign('store', $store);
        $this->assign('store_status', 0);
        if(STORE_ID == $store_id){
            $this->assign('store_status', 1);
        }
        $catList =  M('goods_category')->cache(true)->select();
        $catList = convert_arr_key($catList, 'id');
        $this->assign('catList', $catList);
        $store_warning_storage = M('store')->where('store_id', $store_id)->getField('store_warning_storage');
        $this->assign('store_warning_storage', $store_warning_storage);
        $this->assign('goodsList', $goodsList);
        $this->assign('page', $show);// 赋值分页输出
        return $this->fetch();
    }

    /**
     * 添加平台商品绑定到店铺
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addPlatformGoods()
    {
//        STORE_ID
            $goods_id = input('goods_id');
            $store_cat_id = input('cat_id');
            $type = input('type/d',0);//0添加，1删除
            $where['store_id'] = STORE_ID;
            $where['goods_id'] = $goods_id;
//            $where['store_cat_id'] = $store_cat_id;
            //先查存在已绑定的商品没
            $store = (new StoreBindPlatformGoods())->where($where)->find();
            if(1 == $type){
                //删除
                db('StoreBindPlatformGoods')->where($where)->save(['id_delete'=>1]);
            }else{
                if($store){
                    $store->save(['id_delete'=>0,'store_cat_id'=>$store_cat_id]);
                }else{
                    (new StoreBindPlatformGoods())->save(['store_id'=>STORE_ID,'goods_id'=>$goods_id,'id_delete'=>0,'store_cat_id'=>$store_cat_id]);
                }
            }

        $this->ajaxReturn(['status'=>1,'msg'=>'操作成功','data'=>'']);
    }
	
	/**
     *  供应商品列表
     */
    public function supplierGoodsList()
    {
		$isSupply = I('is_supply', -1);
		if ($isSupply > -1) {
			$where['is_supply'] = $isSupply;
		} else {
			$where['is_supply'] = ['in', '0,1'];
		}
        $where['store_id'] = STORE_ID;
    	$model = M('Goods');
    	$goods_state = I('goods_state', '', 'string'); // 商品状态  0待审核 1审核通过 2审核失败
    	if($goods_state != ''){
    		$where['goods_state'] = intval($goods_state);
    	}
    	$key_word = trim(I('key_word', ''));
    	if ($key_word !== '') {
    		$where['goods_name|goods_sn'] = array('like', '%' . $key_word . '%');
    	}
		$where['purpose'] = 2;
    	$count = $model->where($where)->count();
    	$Page = new Page($count, 10);
    	$goodsList = $model->where($where)->order('goods_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$goodsIds = array_column($goodsList, 'goods_id');
		$goodsCount = Db::name('goods')->where(['root_goods_id' => ['in', $goodsIds]])->group('root_goods_id')->getField('root_goods_id, count(*) as count');
    	$show = $Page->show();
		$this->assign('state',C('goods_state'));
    	$this->assign('goodsList', $goodsList);
    	$this->assign('goods_count', $goodsCount);
    	$this->assign('page', $show);// 赋值分页输出
    	return $this->fetch();
    }
	
	/**
     * 供应商品保存
     */
    public function supplierSave(){
		if (!$this->store['is_supplier']) {
			$this->error("你不是供应商，无法发布编辑供应商品");
		}
        // 数据验证
        $data = input('post.');
		$goods_id = input('post.goods_id');
		$goods_cat_id3 = input('post.cat_id3');
		$spec_goods_item = input('post.item/a',[]);
		$validate = Loader::validate('Goods');
		$data['store_id'] = STORE_ID;
		if (!$validate->batch()->scene('supplier_save')->check($data)) {
			$error = $validate->getError();
			$error_msg = array_values($error);
			$return_arr = array('status' => -1,'msg' => $error_msg[0],'data' => $error);
			$this->ajaxReturn($return_arr);
		}
		$data['on_time'] = time(); // 上架时间
		$type_id = M('goods_category')->where("id", $goods_cat_id3)->getField('type_id'); // 找到这个分类对应的type_id
		$stores = M('store')->where(array('store_id' => STORE_ID))->getField('store_id , goods_examine,is_own_shop' , 1);
		$store_goods_examine = $stores[STORE_ID]['goods_examine'];
		if ($store_goods_examine) {
			$data['goods_state'] = 0; // 待审核
			$data['is_supply'] = 0;
		} else {
			$data['goods_state'] = 1; // 审核通过
			$data['is_supply'] = 1;
		}
		$data['is_on_sale'] = 0; // 下架，供应商品始终保持下架
		$data['suppliers_id'] = STORE_ID;
		$data['purpose'] = 2;
		//总平台自营标识为2 , 第三方自营店标识为1
		$is_own_shop = (STORE_ID == 1) ? 2 : ($stores[STORE_ID]['is_own_shop']);
		$data['is_own_shop'] = $is_own_shop;
		$data['goods_type'] = $type_id ? $type_id : 0;
		//序列化保存手机端商品描述数据
		if ($_POST['m_body'] != '') {
			$_POST['m_body'] = str_replace('&quot;', '"', $_POST['m_body']);
			$_POST['m_body'] = json_decode($_POST['m_body'], true);
			if (!empty($_POST['m_body'])) {
				$_POST['m_body'] = serialize($_POST['m_body']);
			} else {
				$_POST['m_body'] = '';
			}
		}
		$data['mobile_content'] = $_POST['m_body'];
		
		if ($goods_id > 0) {
			$Goods = GoodsModel::get(['goods_id' => $goods_id, 'store_id' => STORE_ID]);
            if (!$store_goods_examine) {
                Db::name('goods')->where(['root_goods_id' => $goods_id, 'supplier_goods_status' => 3])->update(['supplier_goods_status' => 1]);
                Db::name('goods')->where(['root_goods_id' => $goods_id, 'supplier_goods_status' => 2])->update(['supplier_goods_status' => 0]);
            }
			//检查供应商品的成本价、运费模块、规格有没更改，有则下架销售商对应的商品，待销售商同意更改后再上架，以下的修改记录都指上述的几个数据
			$is_modify = false;
			if ($data['cost_price'] != $Goods['cost_price']) {
				$is_modify = true;
			}
			if ($data['template_id'] != $Goods['template_id']) {
				$is_modify = true;
			}
			$goodsItem = I('item/a');
			if ($goodsItem) {
				$specGoodsPrice = Db::name('spec_goods_price')->where(['goods_id' => $goods_id])->select();
				if (count($specGoodsPrice) != count($goodsItem)) {
					$is_modify = true;
				} else {
                    foreach ($specGoodsPrice as $sgpKey => $sgpVal) {
                        if (!$goodsItem[$sgpVal['key']]) { //如果不存在这个规格
                            $is_modify = true;
                        }
                        if ($goodsItem[$sgpVal['key']]['cost'] != $sgpVal['cost']) { //如果对应的规格的价格变了
                            $is_modify = true;
                        }
				    }
					/*$goodsItemKey = array_keys($goodsItem);
					$specGoodsPriceKey = array_column($specGoodsPrice, 'key');
					$intersect = array_intersect($goodsItemKey, $specGoodsPriceKey);
					if (count($intersect) != count($goodsItem)) {
						$is_modify = true;
					}*/
				}
			}
            if ($is_modify) {
				$dealerGoodsList = Db::name('goods')->where(['root_goods_id' => $goods_id])->select();
				$dealerCount = count($dealerGoodsList);
				if ($dealerCount > 0) { //有供应的店铺才需要
					foreach ($dealerGoodsList as $dealerGoods) {
						Db::name('goods')->where('goods_id', $dealerGoods['goods_id'])->update(['supplier_goods_status' => 1, 'is_on_sale' => 0]);
					}
					
					//记录原始供应商品的修改状态，用于追踪对应销售商商品的相应数据是否修改
					$modify['status'] = $data['goods_state'];
					$modify['modify_time'] = time();
					$modify['goods_id'] = $goods_id;
					$modify['store_id'] = STORE_ID;
					$dealerIds = array_column($dealerGoodsList, 'store_id');
					$status = array_fill(0, $dealerCount, 0);
					$dealerStatus = array_combine($dealerIds, $status);
					$modify['dealer_status'] =json_encode($dealerStatus);
					$res = Db::name('supplier_goods_modify')->where('goods_id',$goods_id)->find();
					if ($res) {//如果有该供应商品的修改记录，就直接覆盖，不管销售商有没全部同意
						Db::name('supplier_goods_modify')->where('modify_id', $res['modify_id'])->update($modify);
					} else {
						Db::name('supplier_goods_modify')->add($modify);
					}
				}
			}
			
			if(empty($Goods)){
				$this->ajaxReturn(array('status' => 0, 'msg' => '非法操作','result'=>''));
			}
			$Goods->data($data, true); // 收集数据
			$update = $Goods->save(); // 写入数据到数据库
		} else {
			$Goods = new GoodsModel();
			$Goods->data($data, true); // 收集数据
			$Goods->save(); // 新增数据到数据库
			$goods_id = $Goods->getLastInsID();
		}
		$GoodsLogic = new GoodsLogic();
		$GoodsLogic->afterSave($goods_id, STORE_ID);
		$GoodsLogic->saveGoodsAttr($goods_id, $type_id); // 处理商品 属性
		$this->ajaxReturn([ 'status' => 1, 'msg' => '操作成功', 'result' => ['goods_id'=>$Goods->goods_id]]);
    }
	
	/**
     *  供应商品派送给销售商
     */
    public function sendToDealer()
    {
		$goodsId = I('goods_id');
		$goods = Db::name('goods')->where(['goods_id' => $goodsId])->find();
		$supplierGoodsStoreIds = Db::name('goods')->where(['root_goods_id' => $goodsId, 'suppliers_id' => STORE_ID])->getField('store_id', true); //已经有此供应商品的店铺id
		
		$supplierGoodsApplyIds = Db::name('supplier_goods_apply')->where(['goods_id' => $goodsId, 'supplier_id' => STORE_ID, 'status' => 0])->getField('dealer_id', true); //已经在申请中的销售商店铺id
		
		if (IS_POST) {
			if ($goods['store_id'] != STORE_ID) {
				$this->ajaxReturn(['status' => 0, 'msg' => $goods['store_id']]);
			}
			if ($goods['purpose'] != 2) {
				$this->ajaxReturn(['status' => 0, 'msg' => '此商品不是供应商品']);
			}
			if ($goods['goods_state'] != 1 || $goods['is_supply'] != 1) {
				$this->ajaxReturn(['status' => 0, 'msg' => '此商品状态不符合可供应条件']);
			}
			$storeIds = I('store_ids/a');
			//$storeCount = I('store_count/a');
			if (count(array_intersect($supplierGoodsStoreIds, $storeIds)) > 0) {
				$this->ajaxReturn(['status' => 0, 'msg' => '部分销售商已有该供应商品']);
			}
			$data = [
				'supplier_id' => STORE_ID,
				'initiator' => 0,
				'goods_id' => $goodsId,
				'status' => 0,
				'initiate_time' => time()
			];
			Db::startTrans();
			foreach ($storeIds as $val) {
				$data['dealer_id'] = $val;
				//$data['store_count'] = $storeCount[$val] ?: 0;
				$map = [
					'supplier_id' => STORE_ID,
					'goods_id' => $goodsId,
					'dealer_id' => $val,
				];
				$res = Db::name('supplier_goods_apply')->where($map)->find();
				if ($res) {//有记录就覆盖，不论申请成功与否
					if ($res['status'] == 0) {
						Db::rollback();
						$this->ajaxReturn(['status' => 0, 'msg' => '部分销售商已经在申请中，不可重复操作']);
					} else {
						Db::name('supplier_goods_apply')->where('apply_id', $res['apply_id'])->save($data);
					}
				} else {
					Db::name('supplier_goods_apply')->insert($data);
				}
			}
			Db::commit(); 
			$this->ajaxReturn(['status' => 1, 'msg' => '申请成功，请耐心等待']);
		} else {
			if ($goods['store_id'] != STORE_ID) {
				$this->error('此商品不是本店铺的商品');
			}
			if ($goods['purpose'] != 2) {
				$this->error('此商品不是供应商品');
			}
			if ($goods['goods_state'] != 1 || $goods['is_supply'] != 1) {
				$this->error('此商品状态不符合可供应条件');
			}
			$filterStoreIds = array_merge($supplierGoodsApplyIds, $supplierGoodsStoreIds);
			$map = [
				'ss.supplier_store_id' => STORE_ID,
				'ss.seller_status' => 1,
				'ss.admin_status' => 1,
				'ss.dealer_store_id' => ['not in', $filterStoreIds]
			];
			$list = Db::name('store_supplier')
				->alias('ss')
				->join('store s', 'ss.dealer_store_id=s.store_id', 'left')
				->where($map)
				->select();
			$this->assign('list', $list);
			return $this->fetch();
		}
    }
	
	/**
     * 查看供应商品的销售商
     */
    public function lookGoodsDealer()
    {
		$goodsId = I('goods_id');
		$count = Db::name('goods')->where('root_goods_id', $goodsId)->count();
		$Page = new Page($count, 10);
    	$list = Db::name('goods')
			->alias('g')
			->join('store s', 's.store_id=g.store_id', 'left')
			->field('g.*,s.store_name')
			->where('g.root_goods_id', $goodsId)
			->limit($Page->firstRow . ',' . $Page->listRows)
			->select();
    	$show = $Page->show();

        $modify = Db::name('supplier_goods_modify')->where('goods_id', $goodsId)->find();
        $dealerStatus = json_decode($modify['dealer_status'], true);
        $this->assign('dealer_status', $dealerStatus);

    	$this->assign('goods_list', $list);
    	$this->assign('page', $show);// 赋值分页输出
		
		return $this->fetch();
    }
	
	/**
     *  供应商品列表(销售商)
     */
    public function dealerGoodsList()
    {
        $where['g.store_id'] = STORE_ID;
    	$model = M('Goods');
    	$goods_state = I('goods_state', '', 'string'); // 商品状态  0待审核 1审核通过 2审核失败
    	if($goods_state != ''){
    		$where['g.goods_state'] = intval($goods_state);
    	}
    	$key_word = trim(I('key_word', ''));
    	if ($key_word !== '') {
    		$where['g.goods_name|goods_sn'] = array('like', '%' . $key_word . '%');
    	}
		$supplier_goods_status = I('supplier_goods_status', -1); // 商品状态  0待审核 1审核通过 2审核失败
    	if($supplier_goods_status >= 0){
    		$where['g.supplier_goods_status'] = $supplier_goods_status;
    	}
		$where['g.purpose'] = 1;
		$where['g.root_goods_id'] = ['neq', '0'];
    	$count = $model->alias('g')->where($where)->count();
    	$Page = new Page($count, 10);
    	$goodsList = $model
			->alias('g')
			->join('store s', 'g.suppliers_id = s.store_id', 'left')
			->field('g.*,s.store_name')
			->where($where)
			->order('goods_id desc')
			->limit($Page->firstRow . ',' . $Page->listRows)
			->select();
		$rootGoodsIds = array_column($goodsList, 'root_goods_id');
		$modifyList = Db::name('supplier_goods_modify')->where(['goods_id' => ['in', $rootGoodsIds]])->select();
		$modifyStatusList = [];
		foreach ($modifyList as $val) {
			$modifyStatusList[$val['goods_id']]['modify_status'] = $val['status'];
			$dealerStatus = json_decode($val['dealer_status'], true);
			$modifyStatusList[$val['goods_id']]['dealer_status'] = $dealerStatus[STORE_ID];
		}
    	$show = $Page->show();
		$this->assign('state',C('goods_state'));
    	$this->assign('goodsList', $goodsList);
    	$this->assign('modify_status_list', $modifyStatusList);
    	$this->assign('page', $show);// 赋值分页输出
    	return $this->fetch();
    }
	
	/**
     *  查看供应商品修改(供应商)
     */
    public function supplierGoodsModify()
    {
        $goodsId = I('goods_id');
		$goods = Db::name('goods')->where('goods_id', $goodsId)->find();
		$modify = Db::name('supplier_goods_modify')->where('goods_id', $goods['root_goods_id'])->find();
		if (!$modify || $goods['supplier_goods_status'] != 1) {
			$this->error('非法操作');
		}
		if ($modify['status'] != 1) {
			$this->error('源供应商品正在审核中，无法查看商品数据修改变化');
		}
		$this->assign('modify', $modify);
		$supplierGoods = Db::name('goods')->where('goods_id', $goods['root_goods_id'])->find();
		$this->assign('goods', $goods);
		$this->assign('supplier_goods', $supplierGoods);
		
		//商品规格
		$specGoodsPrice = Db::name('spec_goods_price')->where('goods_id', $goodsId)->select();
		$this->assign('spec_goods_price', $specGoodsPrice);
		$supplierSpecGoodsPrice = Db::name('spec_goods_price')->where('goods_id', $goods['root_goods_id'])->select();
		$this->assign('supplier_spec_goods_price', $supplierSpecGoodsPrice);
		
		//运费模板
		$freightTemplate = new \app\common\model\FreightTemplate();
        $template = $freightTemplate->append(['type_desc'])->with('freightConfig')->where('template_id', $goods['template_id'])->find();
        $this->assign('template', $template);
        $supplierTemplate = $freightTemplate->append(['type_desc'])->with('freightConfig')->where('template_id', $supplierGoods['template_id'])->find();
        $this->assign('supplier_template', $supplierTemplate);
		
    	return $this->fetch();
    }
	
	/**
     *  销售商同意供应商品修改数据（成本价等）
     */
    public function agreeSupplierGoodsModify()
    {
        $goodsId = I('goods_id');
        $modifyId = I('modify_id');
		
		$goods = Db::name('goods')->where('goods_id', $goodsId)->find();
		$modify = Db::name('supplier_goods_modify')->where('modify_id', $modifyId)->find();
		$supplierGoods = Db::name('goods')->where('goods_id', $modify['goods_id'])->find();
		
		$data =[];
		//复制成本价
		if ($goods['cost_price'] != $supplierGoods['cost_price']) {
			$data['cost_price'] = $supplierGoods['cost_price'];
		}
		//复制运费模板
		if ($supplierGoods['is_free_shipping']) {
			$data['is_free_shipping'] = 1;
			$data['template_id'] = 0;
			//删除运费模板
			if ($goods['template_id'] > 0) {
				Db::name('freight_region')->where(['template_id' => $goods['template_id'], 'store_id' => STORE_ID])->delete();
				Db::name('freight_config')->where(['template_id' => $goods['template_id'], 'store_id' => STORE_ID])->delete();
				Db::name('freight_template')->where(['template_id' => $goods['template_id'], 'store_id' => STORE_ID])->delete();
			}
		} else {
			$data['is_free_shipping'] = 0;
			//更新运费模板
			$freightLogic = new \app\seller\logic\FreightLogic();
			$data['template_id'] = $freightLogic->updataDealerFreightTemplate($supplierGoods['template_id'], $goods);
		}
		
		//复制商品规格
		$specGoodsPrice = Db::name('spec_goods_price')->where(['goods_id' => $goodsId])->select();
		$supplierSpecGoodsPrice = Db::name('spec_goods_price')->where(['goods_id' => $modify['goods_id']])->select();
		if (count($supplierSpecGoodsPrice) > 0) {
			$supplierSgpItemIdArr = array_column($supplierSpecGoodsPrice, 'item_id');
			if ($specGoodsPrice) {
				$sgpRootItemIdArr = array_column($specGoodsPrice, 'root_item_id');
			} else {
				$sgpRootItemIdArr = [];
			}
			$updateItemIdArr = [];
			$newSPGData = [
				'goods_id' => $goodsId,
				'store_id' => $goods['store_id'],
			];
			$newSPGDataArr = [];
			$specImage = [];
			$goodsLogin = new GoodsLogic();
			foreach ($supplierSpecGoodsPrice as $k => $v) {
				$updateItemIdArr[] = $v['item_id'];
				if (!in_array($v['item_id'], $sgpRootItemIdArr)) {
					$newSPGData['root_item_id'] = $v['item_id'];
					$newSPGData['sku'] = $v['sku'];
					$newSPGData['store_count'] = 0;
					$newSPGData['cost'] = $v['cost'];
					//spec_goods_price表里的规格图标
					if ($v['spec_img']) {
						$newImgUrl = $goodsLogin->supplierCopyImg($v['spec_img'], STORE_ID);
						$newSPGData['spec_img'] = $newImgUrl;
					} else {
						$newImgUrl = '';
					}
					
					//spec_item表 规格
					$specitemIds = explode('_', $v['key']);
					$specGoodsPriceKey = [];
                    $specGoodsPriceKeyName = [];
					foreach ($specitemIds as $specItemIdVal) {
						$specItem = Db::name('spec_item')->where('id', $specItemIdVal)->find();
						$storeSpecItem = Db::name('spec_item')->where(['item' => $specItem['item'], 'spec_id' => $specItem['spec_id'], 'store_id' => STORE_ID])->find();
						//判断有没该店铺下有没同名的规格项，有则沿用此规格项，没则创建
						if (!$storeSpecItem) {
							unset($specItem['id']);
							$specItem['store_id'] = $goods['store_id'];
							$specItemId = Db::name('spec_item')->insertGetId($specItem);
							
							//spec_image表
							$specImageItem =[
								'goods_id' => $goodsId,
								'spec_image_id' => $specItemId,
								'src' => $newImgUrl,
								'store_id' => $goods['store_id']
							];
							array_push($specImage, $specImageItem);
						} else {
							$specItemId = $storeSpecItem['id'];
						}
                        $spec = Db::name('spec')->where('id', $specItem['spec_id'])->find();
                        $specGoodsPriceKeyName[$specItemId] = $spec['name'] . ':' . $specItem['item'];
						array_push($specGoodsPriceKey, $specItemId);
					}
					sort($specGoodsPriceKey);
					$keyName = '';
					foreach ($specGoodsPriceKey as $kkk => $vvv) {
					    if ($keyName) {
                            $keyName .= ' ';
                        }
                        $keyName .= $specGoodsPriceKeyName[$vvv];
                    }
                    $newSPGData['key'] = implode('_', $specGoodsPriceKey);
					$newSPGData['key_name'] = $keyName;
					array_push($newSPGDataArr, $newSPGData);
				} else {
                    Db::name('spec_goods_price')
                        ->where(['goods_id' => $goodsId, 'root_item_id' => $v['item_id']])
                        ->update(['cost' => $v['cost']]);
                }
			}
			Db::name('spec_goods_price')->insertAll($newSPGDataArr);
			Db::name('spec_image')->insertAll($specImage);
			if($updateItemIdArr){
				Db::name('spec_goods_price')->where(['goods_id' => $goodsId, 'root_item_id' => ['not in', $updateItemIdArr]])->delete();
			}
		} else {
			Db::name('spec_goods_price')->where(['goods_id' => $goodsId])->delete();
		}
		
		$dealerStatus = json_decode($modify['dealer_status'], true);
		$dealerStatus[STORE_ID] = 1;
		$dealerStatus = json_encode($dealerStatus);
		Db::name('supplier_goods_modify')->where('modify_id', $modify['modify_id'])->save(['dealer_status' => $dealerStatus]);
		$data['supplier_goods_status'] = 0;
		$data['goods_state'] = 0;
		Db::name('goods')->where('goods_id', $goodsId)->save($data);
		
    	$this->ajaxReturn(['status' => 1, 'msg' => '操作成功']);
    }

    /**
     *  销售商拒绝供应商品修改数据（成本价等）
     */
    public function refuseSupplierGoodsModify()
    {
        $modifyId = I('modify_id');
        $modify = Db::name('supplier_goods_modify')->where('modify_id', $modifyId)->find();
        $dealerStatus = json_decode($modify['dealer_status'], true);
        $dealerStatus[STORE_ID] = 2;
        $dealerStatus = json_encode($dealerStatus);
        Db::name('supplier_goods_modify')->where('modify_id', $modify['modify_id'])->save(['dealer_status' => $dealerStatus]);
        $this->ajaxReturn(['status' => 1, 'msg' => '操作成功']);
    }
	
	/**
     *  供应商品审理列表（销售商）
     */
    public function supplierGoodsHandleList()
    {
		$map = [
			'sga.dealer_id' => STORE_ID,
			'sga.status' => 0,
			'sga.initiator' => 0,
		];
		$field = 'sga.*, s.store_name, g.goods_name,g.cost_price';
		$count = Db::name('supplier_goods_apply')->alias('sga')->where($map)->count();
		$Page = new Page($count, 10);
		$list = Db::name('supplier_goods_apply')
			->alias('sga')
			->join('store s', 's.store_id=sga.supplier_id', 'left')
			->join('goods g', 'g.goods_id=sga.goods_id', 'left')
			->field($field)
			->where($map)
			->limit($Page->firstRow . ',' . $Page->listRows)
			->select();
		$show = $Page->show();
    	$this->assign('list', $list);
    	$this->assign('page', $show);// 赋值分页输出*/
    	return $this->fetch();
    }
	
	/**
     *  查看供应商品（销售商）
     */
    public function checkGoods()
    {
		$applyId = I('apply_id');
		$apply = Db::name('supplier_goods_apply')->where(['apply_id' => $applyId])->find();
		if (!$apply || $apply['dealer_id'] != STORE_ID || $apply['initiator'] != 0 || $apply['status'] != 0) {
			$this->error('店铺下无此申请，无法查看商品');
		}
		
		$goodsId = $apply['goods_id'];
		$Goods = new GoodsModel();
		$goods_info = $Goods->where(['goods_id' => $goodsId])->find();
		if(empty($goods_info)){
			$this->error("不存在此商品");
		}
		if ($goods_info['goods_state'] != 1) {
			$this->error('此商品不符合供应条件');
		}
		if ($goods_info['purpose'] != 2) {
			$this->error('此商品不是供应商品，无法查看');
		}
		
		$goods_cat = Db::name('goods_category')->where('id','IN',[$goods_info['cat_id1'],$goods_info['cat_id2'],$goods_info['cat_id3']])->order('level desc')->select();
		if ($this->store['bind_all_gc']) {
            $goods_info['bind_class_state'] = 1;
        }else {
            $bind_class = Db::name('store_bind_class')
                ->where([
                    'class_1' => $goods_info['cat_id1'],
                    'class_2' => $goods_info['cat_id2'],
                    'class_3' => $goods_info['cat_id3'],
                    'store_id' => STORE_ID]
                )
                ->find();
            if ($bind_class) {
                $goods_info['bind_class_state'] = $bind_class['state'];
            } else {
                $goods_info['bind_class_state'] = -1; //表示无此类目
            }
		}
		$this->assign('goods_cat', $goods_cat);
        
        $brand = db('brand')->where('id',$goods_info['brand_id'])->find();
		$goods_info['brand_name'] = $brand['name'];
        $goodsType = Db::name("GoodsType")->select();
        $goodsImages = Db::name("GoodsImages")->where('goods_id', $goodsId)->order('img_sort asc')->select();
		
		$this->assign('goodsInfo', $goods_info);  // 商品详情
        $this->assign('goodsType', $goodsType);
        $this->assign('goodsImages', $goodsImages);  // 商品相册
		
		//商品规格
		$specGoodsPrice = Db::name('spec_goods_price')->where('goods_id', $goodsId)->select();
		$this->assign('spec_goods_price', $specGoodsPrice);
		//商品属性
		$goodsAttr = Db::name('goods_attr')
			->alias('ga')
			->join('goods_attribute gat', 'ga.attr_id=gat.attr_id', 'left')
			->where('goods_id', $goodsId)
			->select();
		$this->assign('goods_attr', $goodsAttr);
		
		//运费模板
		$freightTemplate = new \app\common\model\FreightTemplate();
        $template = $freightTemplate->append(['type_desc'])->with('freightConfig')->where('template_id', $goods_info['template_id'])->find();
        $this->assign('template', $template);
		
    	return $this->fetch();
    }
	
	/**
     *  同意使用供应商品（销售商）
     */
    public function agreeSupplierGoods()
    {
		$goodsId = I('goods_id', 0);
		$goods = Db::name('goods')->where('goods_id', $goodsId)->find();
		if (!$goods) {
			$this->ajaxReturn(['status' => 0, 'msg' => '无此商品']);
		}
		$applyId = I('apply_id', 0);
		$apply = Db::name('supplier_goods_apply')->where(['apply_id' => $applyId, 'status' => 0])->find();
		if (!$apply) {
			$this->ajaxReturn(['status' => 0, 'msg' => '无此申请信息或此申请已审理']);
		}
		if ($apply['dealer_id'] != STORE_ID) {
			$this->ajaxReturn(['status' => 0, 'msg' => '本店铺无此申请信息']);
		}
		$supplierStore = Db::name('store')->where('store_id', $goods['store_id'])->find();
        if (!$this->store['bind_all_gc']) {
            $bind_class = Db::name('store_bind_class')
                ->where([
                    'class_1' => $goods['cat_id1'],
                    'class_2' => $goods['cat_id2'],
                    'class_3' => $goods['cat_id3'],
                    'store_id' => STORE_ID]
                )->find();
            if ($bind_class) {
                if ($bind_class['state'] == 0) {
                    $this->ajaxReturn(['status' => 0, 'msg' => '此商品分类在申请中，请在申请成功后进行此操作']);
                }
            } else {
                $this->ajaxReturn(['status' => 0, 'msg' => '无此商品分类，请先申请该商品分类']);
            }
        }
        //将供应商品复制成销售商品
        $goodsLogic = new GoodsLogic();
        $goodsLogic->addDealerGoods($goods, $this->store);
        //修改申请状态
        $newApply = [
            'status' => 1,
            'check_time' => time(),
        ];
        Db::name('supplier_goods_apply')->where(['apply_id' => $applyId])->update($newApply);
		$this->ajaxReturn(['status' => 1, 'msg' => '操作成功', 'url' => U('Seller/Goods/supplierGoodsHandleList')]);
    }
	
	/**
     *  供应商品库存列表（供应商）
     */
    public function supplierStockList()
    {
		$goods_name = I('goods_name');
    	$spec_name = I('spec_name');
		$goodsId = I('goods_id', 0);
		if ($goodsId) {
			$gids = M('goods')->where(['root_goods_id' => $goodsId])->getField('goods_id',true);
		} else {
			$sgids = M('goods')->where(['purpose' => 2, 'store_id' => STORE_ID])->getField('goods_id',true);
			$gids = M('goods')->where(['root_goods_id' => ['in', $sgids], 'suppliers_id' => STORE_ID])->getField('goods_id',true);
		}
		if ($goods_name) {
    		$map['goods_name'] = array('like', "%$goods_name%");
    		$gids2 = M('goods')->where($map)->getField('goods_id',true);
            unset($map['goods_name']);
    		if($gids2){
    			$gids = array_intersect($gids, $gids2);
    		}
    	}
		$map['goods_id'] = array('in',$gids);
    	if($spec_name){
    		$map['key_name'] = array('like', "%$spec_name%");
    	}
    	$count = Db::view('goods','goods_id,goods_name,goods_sn,shop_price,store_count,store_id')
            ->view('spec_goods_price','item_id,price,store_count as spec_store_count,key_name','spec_goods_price.goods_id=goods.goods_id','LEFT')
            ->where($map)
            ->limit(10)
            ->count();
        if($count>20){
            $Page = new Page($count, 20);
            $show = $Page->show();
            $this->assign('page', $show);// 赋值分页输出
        }
        $stock_list = Db::view('goods','goods_id,root_goods_id,goods_name,goods_sn,shop_price,store_count,store_id,prom_type,store_id')
            ->view('spec_goods_price','item_id,price,store_count as spec_store_count,key_name,prom_type as spec_prom_type','spec_goods_price.goods_id=goods.goods_id','LEFT')
            ->where($map)
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->order('goods_id desc')
            ->select();
    	if($stock_list){
            foreach($stock_list as $key=>$val){
                if(!empty($val['item_id'])){
                    $stock_list[$key]['store_count'] = $val['spec_store_count'];
                    $stock_list[$key]['shop_price'] = $val['price'];
                    $stock_list[$key]['prom_type'] = $val['spec_prom_type'];
                }
            }
            $stock_list = collection($stock_list)->toArray();
    		$storeIds = get_arr_column($stock_list, 'store_id');
    		$storeArr = M('store')->where(array('store_id'=>array('in',$storeIds)))->getField('store_id,store_name as dealer_name');
    		$this->assign('store_arr',$storeArr);
    	}
        $this->assign('stock_list', $stock_list);
    	return $this->fetch();
    }
	
	/**
	 * 供应商品库存更新（供应商）
	 */
	public function updateSupplierGoodsStock(){
    	$item_id = I('item_id/d');
		$store_count = I('store_count/d');
		$old_stock = I('old_stock');
		if ($item_id) {
			$spec_goods = Db::name('spec_goods_price')->alias('s')->field('s.*,g.goods_name')->join('__GOODS__ g', 'g.goods_id = s.goods_id', 'LEFT')->where(['s.item_id'=>$item_id])->find();
			$goods = Db::name('goods')->where(['goods_id' => $spec_goods['goods_id']])->find();
			$r = M('spec_goods_price')->where(array('item_id'=>$item_id))->save(array('store_count'=>$store_count));
			if($r){
				$stock = $store_count - $old_stock;
				$goods = array('goods_id'=>$spec_goods['goods_id'],'goods_name'=>$spec_goods['goods_name'],'key_name'=>$spec_goods['key_name'],'store_id'=>$goods['store_id']);
				update_stock_log(STORE_ID, $stock, $goods , '', 1);
				$storeCount = $goods['store_count'] + $stock;
				$storeCount < 0 && $storeCount = 0;
				M('goods')->where(array('goods_id'=>$spec_goods['goods_id']))->save(array('store_count' => $storeCount));
				exit(json_encode(array('status'=>1,'msg'=>'修改成功')));
			}else{
				exit(json_encode(array('status'=>0,'msg'=>'修改失败')));
			}
		} else {
			$goods_id = I('goods_id/d');
			$goods = Db::name('goods')->where(['goods_id' => $goods_id])->find();
			$stock = $store_count - $old_stock;
			$goods = array('goods_id'=>$goods['goods_id'],'goods_name'=>$goods['goods_name'],'key_name'=>'','store_id'=>$goods['store_id']);
			update_stock_log(STORE_ID, $stock, $goods , '', 1);
			$store_count < 0 && $store_count = 0;
			M('goods')->where(array('goods_id'=>$goods_id))->save(array('store_count' => $store_count));
			exit(json_encode(array('status'=>1,'msg'=>'修改成功')));
		}
    }
	
	/**
	 *库存列表（供应商）
	 */
	public function supplierStockLog()
    {
		$sgids = M('goods')->where(['purpose' => 2, 'store_id' => STORE_ID])->getField('goods_id',true);
		$gids = M('goods')->where(['root_goods_id' => ['in', $sgids]])->getField('goods_id',true);
		$map['sl.goods_id'] = array('in',$gids);
        $mtype = I('mtype');
        if ($mtype == 1) {
            $map['sl.stock'] = array('gt', 0);
        }
        if ($mtype == -1) {
            $map['sl.stock'] = array('lt', 0);
        }
        $goods_name = I('goods_name');
        if ($goods_name) {
            $map['sl.goods_name'] = array('like', "%$goods_name%");
        }
        $ctime = urldecode(I('post.ctime'));
        if ($ctime) {
            $gap = explode(' - ', $ctime);
            $this->assign('ctime', $gap[0] . ' - ' . $gap[1]);
            $this->assign('start_time', $gap[0]);
            $this->assign('end_time', $gap[1]);
            $map['sl.ctime'] = array(array('gt', strtotime($gap[0])), array('lt', strtotime($gap[1])));
        }
        $count = Db::name('stock_log')->alias('sl')->where($map)->count();
        $Page = new Page($count, 20);
        $show = $Page->show();
        $this->assign('page', $show);// 赋值分页输出
        $stock_list = Db::name('stock_log')
			->alias('sl')
			->join('store s', 'sl.store_id=s.store_id', 'left')
			->where($map)
			->order('id desc')
			->limit($Page->firstRow . ',' . $Page->listRows)
			->select();
        $this->assign('stock_list', $stock_list);
        return $this->fetch();
    }
	
	/**
	 * 改变供应商品的供应状态（供应商）
	 */
	public function changeGoodsSupply()
    {
		$isSupply = I('is_supply', -1);
		$goodsId = I('goods_id');
		if ($isSupply == -1 || !$goodsId) {
			$this->ajaxReturn(['status' => 0, 'msg' => '参数错误']);
		}
		$supplierGoods = Db::name('goods')->where('goods_id', $goodsId)->find();
		if ($supplierGoods['purpose'] != 2) {
			$this->ajaxReturn(['status' => 0, 'msg' => '此商品不是供应商品']);
		}
		Db::name('goods')->where('goods_id', $goodsId)->update(['is_supply' => $isSupply]);
		$dealerGoodsIds = Db::name('goods')->where(['root_goods_id' => $goodsId])->getField('goods_id', true);
		if ($isSupply == 1) {
			$data = ['supplier_goods_status' => 0];
		} else if ($isSupply == 0) {
			$data = [
				'supplier_goods_status' => 2,
				'is_on_sale' => 0
			];
		}
		Db::name('goods')->where(['goods_id' => ['in', $dealerGoodsIds]])->update($data);
		$this->ajaxReturn(['status' => 1, 'msg' => '操作成功']);
    }
	
	/**
	 * 拒绝供应商品的申请（销售商）
	 */
	public function refuseSupplierGoods()
    {
		$applyId = I('apply_id', 0);
		if (!$applyId) {
			$this->ajaxReturn(['status' => 0, 'msg' => '参数错误']);
		}
		$apply = Db::name('supplier_goods_apply')->where(['apply_id' => $applyId])->find();
		if ($apply['dealer_id'] != STORE_ID || $apply['initiator'] != 0 || $apply['status'] != 0) {
			$this->ajaxReturn(['status' => 0, 'msg' => '无此申请信息或此申请无申请条件']);
		}
		Db::name('supplier_goods_apply')->where(['apply_id' => $applyId])->update(['status' => 2, 'check_time' => time()]);
		$this->ajaxReturn(['status' => 1, 'msg' => '操作成功']);
    }
	
	/**
     * 删除供应商品（供应商）
     */
    public function delSupplierGoods()
    {
        $id= I('id');
        $goodsLogic = new GoodsLogic();
        $res = $goodsLogic->delStoreSupplierGoods($id);
        $this->ajaxReturn($res);
    }

    /**
     * 一键铺货
     */
    public function addDealerGoods()
    {
        $goodsIds = I('goods_ids', '');
        if (!$goodsIds) {
            $this->ajaxReturn(['status' => 0, 'msg' => '参数错误']);
        }
        $goodsArr = Db::name('goods')->where(['goods_id' => ['in', $goodsIds]])->select();
        if (!$goodsArr) {
            $this->ajaxReturn(['status' => 0, 'msg' => '无商品数据']);
        }
        $goodsLogic = new GoodsLogic();
        foreach ($goodsArr as $key => $value) {
            $goodsLogic->addDealerGoods($value, $this->store);
        }
        $this->ajaxReturn(['status' => 1, 'msg' => '铺货成功']);
    }
}