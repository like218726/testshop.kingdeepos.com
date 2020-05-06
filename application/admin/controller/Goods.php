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
namespace app\admin\controller;
use app\admin\model\Goods as GoodsModel;
use app\admin\logic\GoodsLogic;
use app\admin\logic\SearchWordLogic;
use think\AjaxPage;
use think\Page;
use think\Db;

class Goods extends Base {
    
    /**
     *  商品分类列表
     */
    public function categoryList(){                
        $GoodsLogic = new GoodsLogic();
        $cat_list = $GoodsLogic->goods_cat_list();
        $goods_type = M('goods_type')->getField('id,name');
        $this->assign('goods_type',$goods_type);
        $this->assign('cat_list',$cat_list);
        return $this->fetch();
    }
    
    /**
     * 添加修改商品分类
     * 手动拷贝分类正则 ([\u4e00-\u9fa5/\w]+)  ('393','$1'), 
     * select * from tp_goods_category where id = 393
        select * from tp_goods_category where parent_id = 393
        update tp_goods_category  set parent_id_path = concat_ws('_','0_76_393',id),`level` = 3 where parent_id = 393
        insert into `tp_goods_category` (`parent_id`,`name`) values 
        ('393','时尚饰品'),
     */
    public function addEditCategory()
    {
        $GoodsLogic = new GoodsLogic();
        $db_prefix = C('database.prefix');
        if (IS_GET) {
            $goods_category_info = D('GoodsCategory')->where('id=' . I('get.id', 0))->find();
            $this->assign('goods_category_info', $goods_category_info);
            
            $cat_list = Db::name('goods_category')->where("parent_id = 0")->select(); // 已经改成联动菜单
            $this->assign('cat_list', $cat_list);
            
            $all_type = Db::name('goods_category')->where("level<3")->getField('id,name,parent_id');//上级分类数据集，限制3级分类，那么只拿前两级作为上级选择
            if(!empty($all_type)){
            	$parent_id = empty($goods_category_info) ? I('parent_id',0) : $goods_category_info['parent_id'];
            	$all_type = $GoodsLogic->getCatTree($all_type);
            	$cat_select = $GoodsLogic->exportTree($all_type,0,$parent_id);

            	$this->assign('cat_select',$cat_select);
            }

            $goods_category_list = Db::name('goods_category')->where('level',1)->getField("id,name,parent_id,type_id");
            $goods_category_list_level3 = Db::name('goods_category')->where('level',3)->where('type_id','<>',0)->field("id,type_id,parent_id_path")->select();
            $goods_category_list[0] = array('id' => 0, 'name' => '默认');
            asort($goods_category_list);
            $this->assign('goods_category_list', $goods_category_list);

            $goods_type_list = db('goods_type')->field('id,name')->select(); // 所有类型id
            //将模型下面的所有分类打包放在该模型下面前台做判断

//            $goods_type_list[-1] = array('id' => 0, 'name' => '默认');
//            sort($goods_type_list);

            foreach ($goods_type_list as $k => $v){
                $goods_type_list[$k]['category_list'] = [];
                foreach ($goods_category_list_level3 as $kk => $vv){
                    if ($v['id'] == $vv['type_id']) {
                        explode('_',$vv['id'])[1];
                        $goods_type_list[$k]['category_list'][] = explode('_',$vv['parent_id_path'])[1];
                    }
                }
                if (!$goods_type_list[$k]['category_list']) {
                    $goods_type_list[$k]['category_list'][] = 0;
                }
                $goods_type_list[$k]['category_list'] = array_unique( $goods_type_list[$k]['category_list']);
            }
            $this->assign('goods_type_list', $goods_type_list);
            return $this->fetch('_category');
            exit;
        }

        $GoodsCategory = D('GoodsCategory'); //

        $type = $_POST['id'] > 0 ? 2 : 1; // 标识自动验证时的 场景 1 表示插入 2 表示更新
        $id = input('id');
        //ajax提交验证
        if ($_GET['is_ajax'] == 1) {

            $data = input('post.');

            if (input('type_id')) {
                //必须三级分类才能绑定模型
                $category = M('goods_category')->where("id = {$_POST['parent_id']}")->find();
                if ($category['level'] != 2) {
                    $return_arr = array('status' => 0,'msg' => '必须三级分类才能绑定模型','data' => '');
                    $this->ajaxReturn($return_arr);
                }
            }

            // 数据验证
            $validate = \think\Loader::validate('GoodsCategory');
            if (!$validate->batch()->check($data)) {
                $error = $validate->getError();
                $error_msg = array_values($error);
                //  编辑
                $return_arr = array('status' => 0,'msg' => $error_msg[0],'data' => $error);
                $this->ajaxReturn($return_arr);
            } else {
                $GoodsCategory->data(input('post.'), true); // 收集数据
                $GoodsCategory->parent_id = $_POST['parent_id'];
                //查找同级分类是否有重复分类
                $par_id = ($GoodsCategory->parent_id > 0) ? $GoodsCategory->parent_id : 0;
                $sameCateWhere = ['parent_id'=>$par_id , 'name'=>$GoodsCategory['name']];
                if($id > 0){
                    $sameCateWhere['id'] = array('<>' ,$id);
                }
                $same_cate = M('GoodsCategory')->where($sameCateWhere)->find();
               
                if($same_cate){
                    $return_arr = array('status' => 0,'msg' => '同级已有相同分类存在','data' => '',);
                    $this->ajaxReturn($return_arr);
                }
                
                if ($GoodsCategory->id > 0 && $GoodsCategory->parent_id == $GoodsCategory->id) {
                	//  编辑
                	$return_arr = array('status' => 0,'msg' => '上级分类不能为自己','data' => '',);
                	$this->ajaxReturn($return_arr);
                }

                //判断不能为自己的子类
                if ($GoodsCategory->id > 0) {
                    $category_id_list = db('goods_category')->where('parent_id',$GoodsCategory->id)->field('id')->select();
                    $category_id_list = array_column($category_id_list,'id');
                    if (in_array($GoodsCategory->parent_id,$category_id_list)) {
                        $return_arr = array('status' => 0,'msg' => '上级分类不能为自己的子类','data' => '');
                        $this->ajaxReturn($return_arr);
                    }
                }
                // 平台抽成比例
                if ($data['commission'] > 100) {
                    //编辑
                    $return_arr = array('status' => 0,'msg' => '抽成比例不得超过100%','data' => '');
                    $this->ajaxReturn($return_arr);
                }

                if ($type == 2)
                    $GoodsCategory->isUpdate(true)->save(); // 写入数据到数据库
                else {
                    $GoodsCategory->save(); // 写入数据到数据库
                    $_POST['id'] = $GoodsCategory->getLastInsID();
                }

                $GoodsLogic->refresh_cat($_POST['id']);
                // 修改它下面的所有分类的 type_id 等于它的type_id

//                M('goods_category')->where("parent_id_path like '{$category['parent_id_path']}\_%'")->save(array('type_id' => $_POST['type_id'], 'commission' => $_POST['commission']));
                model('goods_category')->save(['type_id'=>$id,'commission' => $_POST['commission']],['id'=>$_POST['type_id']]);
                $return_arr = array(
                    'status' => 1,
                    'msg' => '操作成功',
                    'data' => array('url' => U('Admin/Goods/categoryList')),
                );
                $this->ajaxReturn($return_arr);

            }
        }
    }

    /**
     * 删除分类
     */
    public function delGoodsCategory()
    {
        // 判断子分类
        $id = I('id/d');
        if (empty($id)) {
            $this->error('非法操作');
        }
        $count = Db::name('goods_category')->where("parent_id", $id)->count("id");
        if ($count > 0) {
            $this->error('该分类下还有分类不得删除!');
        }
        // 判断是否存在商品
        $goods_count = Db::name('goods')->where(['cat_id1|cat_id2|cat_id3' => $id])->count('goods_id');
        if ($goods_count > 0) {
            $this->error('该分类下有商品不得删除!');
        }
        // 删除分类
        $del = Db::name('goods_category')->where("id", $id)->delete();
        if ($del !== false) {
            $this->success("删除成功!!!", U('Admin/Goods/categoryList'));
        } else {
            $this->error("删除失败!!!", U('Admin/Goods/categoryList'));
        }
    }

    /**
     *  商品列表
     */
    public function goodsList(){      
        $GoodsLogic = new GoodsLogic();        
        $brandList = $GoodsLogic->getSortBrands();
        $categoryList = $GoodsLogic->getSortCategory();
        $this->assign('categoryList',$categoryList);
        $this->assign('brandList',$brandList);
        return $this->fetch();                                           
    }

    /**
     *  商品列表
     */
    public function ajaxGoodsList(){ 
        $where = ' 1 = 1 '; // 搜索条件
        $is_on_sale = I('is_on_sale');
        $goods_state = I('goods_state');
        $brand_id = I('brand_id');
        $intro = I('intro');
        $purpose = I('purpose');
        $intro && $where = "$where and $intro= 1" ;
        $purpose && $where = "$where and purpose= $purpose" ;
        $brand_id && $where = "$where and brand_id = $brand_id";
        ($is_on_sale !== '') && $where = "$where and (is_on_sale = $is_on_sale or is_supply = $is_on_sale)"; //普通店铺商品和供应商品
        if ($goods_state !== '') {
            $where = "$where and goods_state = $goods_state and is_on_sale<2";
            if ($goods_state == 0) {
                $where = "$where and supplier_goods_status = 0";
            }
        }
        $cat_id = I('cat_id');
        // 关键词搜索               
        $key_word = trim(I('key_word'));
        if($key_word)
        {
            $where = "$where and (goods_name like '%$key_word%' or goods_sn like '%$key_word%')" ;
        }
        
        if($cat_id > 0)
        {            
            $where .= " and (cat_id1 = $cat_id or cat_id2 = $cat_id or cat_id3 = $cat_id ) "; // 初始化搜索条件
        }
        $goodsModel = new GoodsModel();
        $count = $goodsModel->where($where)->count();
        $Page  = new AjaxPage($count,10);
        $show = $Page->show();
        $order_str = "`{$_POST['orderby1']}` {$_POST['orderby2']}";
        $goodsList = $goodsModel->where($where)->order($order_str)->limit($Page->firstRow.','.$Page->listRows)->select();
		$store_id_list = get_arr_column($goodsList, 'store_id');
		if (!empty($store_id_list)) {
			$store_list = M('store')->where("store_id", "in", implode(',', $store_id_list))->getField('store_id,store_name');
		}
		$this->assign('store_list',$store_list);
        $catList = M('goods_category')->cache(true)->select();
        $catList = convert_arr_key($catList, 'id');
        $store_type = array('加盟店','平台联营','平台自营');
        $this->assign('store_type',$store_type);
        $goods_state = C('goods_state');
        $this->assign('catList',$catList);
        $this->assign('goodsList',$goodsList);
        $this->assign('goods_state',$goods_state);
        $this->assign('page',$show);// 赋值分页输出
        return $this->fetch();
    }

    /**
     * 库存日志
     * @return mixed
     */
    public function stock_list(){
    	$model = M('stock_log');
    	$map = array();
    	$mtype = I('mtype');
    	if($mtype == 1){
    		$map['stock'] = array('gt',0);
    	}
    	if($mtype == -1){
    		$map['stock'] = array('lt',0);
    	}
    	$goods_name = I('goods_name');
    	if($goods_name){
    		$map['goods_name'] = array('like',"%$goods_name%");
    	}
    	$ctime = urldecode(I('ctime'));
    	if($ctime){
    		$gap = explode(' - ', $ctime);
            $this->assign('start_time',$gap[0]);
            $this->assign('end_time',$gap[1]);
    		$this->assign('ctime',$gap[0].' - '.$gap[1]);
    		$map['ctime'] = array(array('gt',strtotime($gap[0])),array('lt',strtotime($gap[1])));
    	}
    	$count = $model->where($map)->count();
    	$Page  = new Page($count,20);
    	$show = $Page->show();
        $this->assign('pager',$Page);
    	$this->assign('page',$show);// 赋值分页输出
    	$stock_list = $model->where($map)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
    	$this->assign('stock_list',$stock_list);
    	if($stock_list){
    		$uids = get_arr_column($stock_list,'store_id');
    		$store = M('store')->where('store_id','in',$uids) //->where("store_id in (".  implode(',', $uids).")")
                ->getField('store_id,store_name');
    		$this->assign('store',$store);
    	}
    	return $this->fetch();
    }

    /**
     * 商品类型  用于设置商品的属性
     */
    public function goodsTypeList(){
        $map = [];
        $search = I('search/s');
        if($search){
            $map['name'] = array('like',"%$search%");
        }
        $model = M("GoodsType");        
        $count = $model->where($map)->count();        
        $pager  = new Page($count,10);        
        $goodsTypeList = $model->where($map)->order("id desc")->limit($pager->firstRow.','.$pager->listRows)->select();
        $this->assign('pager',$pager);
        $this->assign('search',$search);
        $this->assign('goodsTypeList',$goodsTypeList);
        return $this->fetch('goodsTypeList');
    }
    
    
    /**
     * 添加修改编辑  商品属性类型
     */
    public function addEditGoodsType()
    {
        $id = I('id') ? I('id') : 0;
        $model = M("GoodsType");
        if (IS_POST) {
            $spec_id_array = I('post.spec_id/a',[]);//规格数组
            $brand_id_array = I('post.brand_id/a',[]);//品牌数组
            $attr_name_array = I('post.attr_name/a',[]);//属性名数组
            $attr_values_array = I('post.attr_values/a',[]);//属性值数组
            $attr_index_array = I('post.attr_index/a',[]);//属性显示
            $attr_input_type_array = I('post.attr_input_type/a',[]);//是否自定义属性
            $order_array = I('post.order/a',[]);//属性排序数组
            $attr_id_array = I('post.attr_id/a',[]);//属性id数组
			
			$data = $this->request->post();
            
            if ($id) {
                // 编辑操作
                DB::name('GoodsType')->update($data); //$model->save();
            } else {
                // 添加操作
                $exists = M('GoodsType')->where(['name'=>$data['name']])->find();
                $exists &&  $this->error("相同的模型名称已经存在");
                DB::name('GoodsType')->insert($data);//$model->add();
				$id = DB::name('GoodsType')->getLastInsID();
            }
            if ($id) {
                // 类型规格对应关系表
                $spec_data_list = array();
                if(!empty($spec_id_array)){
                    foreach ($spec_id_array as $k => $v){
                        $spec_data_list[] = array('type_id' => $id, 'spec_id' => $v);
                    }
                }
                M('spec_type')->where("type_id = $id")->delete(); // 先把类型规格 表对应的 删除掉 然后再重新添加
                if(count($spec_data_list) > 0){
                    M('spec_type')->insertAll($spec_data_list);
                }
                // 类型品牌对应关系表
                $brand_id_list = array();
                if(!empty($spec_id_array)){
                    foreach ($brand_id_array as $k => $v){
                        $brand_id_list[] = array('type_id' => $id, 'brand_id' => $v);
                    }
                }
                M('brand_type')->where("type_id = $id")->delete(); // 先把类型规格 表对应的 删除掉 然后再重新添加
                if(count($brand_id_list) > 0) {
                    M('brand_type')->insertAll($brand_id_list);
                }
                //处理商品属性
                $attr_name_list = array();
                foreach ($attr_name_array as $k => $v) {
                    $attr_values_array[$k] = str_replace('_', '', $attr_values_array[$k]); // 替换特殊字符
                    $attr_values_array[$k] = str_replace('@', '', $attr_values_array[$k]); // 替换特殊字符
                    $attr_values_array[$k] = trim($attr_values_array[$k]);
                    $attr_index_array[$k] = $attr_index_array[$k] ? $attr_index_array[$k] : 0; // 是否显示
                    $attr_input_type_array[$k] = $attr_input_type_array[$k] ? $attr_input_type_array[$k] : 0; // 是否自定义属性
                    $attribute = array(
                        'attr_name' => $v,
                        'type_id' => $id,
                        'attr_index' => $attr_index_array[$k],
                        'attr_values' => $attr_values_array[$k],
                        'attr_input_type' => $attr_input_type_array[$k],
                        'order' => $order_array[$k],
                    );
                    if (empty($attr_id_array[$k])) {
                        $attr_name_list[] = $attribute;
                    } else {
                        $attribute['attr_id'] = $attr_id_array[$k];
                        M('goods_attribute')->update($attribute);
                    }
                }
                if (count($attr_name_list)>0){
                    // 插入属性
                    M('goods_attribute')->insertAll($attr_name_list);
                }
            }
            $this->success("操作成功!!!", U('Admin/Goods/addEditGoodsType',array('id'=>$id)));
            exit;
        }
        $goodsType = $model->where("id = $id")->find();
        $cat_list = M('goods_category')->where("parent_id = 0")->select(); // 已经改成联动菜单
        $attributeList = M('goods_attribute')->where(["type_id" => $id, 'attr_input_type'=>['exp' , '<> 2 ' ]])->select();//固定属性
        $customerAttributeList = M('goods_attribute')->where(["type_id" => $id, 'attr_input_type'=>2])->select();//自定义属性
        $this->assign('attributeList', $attributeList);
        $this->assign('customerAttributeList', $customerAttributeList);
        $this->assign('cat_list', $cat_list);
        $this->assign('goodsType', $goodsType);
        return $this->fetch('_goodsType');
    }

    /**
     * 商品属性列表
     */
    public function goodsAttributeList(){       
        $goodsTypeList = M("GoodsType")->select();
        $this->assign('goodsTypeList',$goodsTypeList);
        return $this->fetch();
    }   
    
    /**
     *  商品属性列表
     */
    public function ajaxGoodsAttributeList(){            
        //ob_start('ob_gzhandler'); // 页面压缩输出
        $where = ' 1 = 1 '; // 搜索条件                        
        I('type_id')   && $where = "$where and type_id = ".I('type_id') ;                
        // 关键词搜索               
        $model = M('GoodsAttribute');
        $count = $model->where($where)->count();
        $Page       = new AjaxPage($count,13);
        $show = $Page->show();
        $goodsAttributeList = $model->where($where)->order('`order` desc,attr_id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $goodsTypeList = M("GoodsType")->getField('id,name');
        $attr_input_type = array(1=>' 从列表中选择',2=>'手工录入');
        $this->assign('attr_input_type',$attr_input_type);
        $this->assign('goodsTypeList',$goodsTypeList);        
        $this->assign('goodsAttributeList',$goodsAttributeList);
        $this->assign('page',$show);// 赋值分页输出
        return $this->fetch();
    }   
    
    /**
     * 添加修改编辑  商品属性
     */
    public  function addEditGoodsAttribute(){
                        
            $model = D("GoodsAttribute");                      
            $type = $_POST['attr_id'] > 0 ? 2 : 1; // 标识自动验证时的 场景 1 表示插入 2 表示更新         
            $_POST['attr_values'] = str_replace('_', '', $_POST['attr_values']); // 替换特殊字符
            $_POST['attr_values'] = str_replace('@', '', $_POST['attr_values']); // 替换特殊字符            
            $_POST['attr_values'] = trim($_POST['attr_values']);

            if(($_GET['is_ajax'] == 1) && IS_POST)//ajax提交验证
            {                
                C('TOKEN_ON',false);
                if(!$model->create(NULL,$type))// 根据表单提交的POST数据创建数据对象                 
                {
                    //  编辑
                    $return_arr = array(
                        'status' => -1,
                        'msg'   => '',
                        'data'  => $model->getError(),
                    );
                    $this->ajaxReturn(json_encode($return_arr));
                }else {                   
                   // C('TOKEN_ON',true); //  form表单提交
                    if ($type == 2)
                    {
                        $model->save(); // 写入数据到数据库                        
                    }
                    else
                    {
                        $insert_id = $model->add(); // 写入数据到数据库                        
                    }
                    $return_arr = array(
                        'status' => 1,
                        'msg'   => '操作成功',                        
                        'data'  => array('url'=>U('Admin/Goods/goodsAttributeList')),
                    );
                    $this->ajaxReturn(json_encode($return_arr));
                }  
            }                
           // 点击过来编辑时                 
           $_GET['attr_id'] = $_GET['attr_id'] ? $_GET['attr_id'] : 0;       
           $goodsTypeList = M("GoodsType")->select();           
           $goodsAttribute = $model->find($_GET['attr_id']);           
           $this->assign('goodsTypeList',$goodsTypeList);                   
           $this->assign('goodsAttribute',$goodsAttribute);
           return $this->fetch('_goodsAttribute');
    }  
    
    /**
     * 更改指定表的指定字段
     */
    public function updateField(){
        $primary = array(
                'goods' => 'goods_id',
                'goods_category' => 'id',
                'brand' => 'id',            
                'goods_attribute' => 'attr_id',
        		'ad' =>'ad_id',            
        );        
        $model = D($_POST['table']);
        $model->$primary[$_POST['table']] = $_POST['id'];
        $model->$_POST['field'] = $_POST['value'];        
        $model->save();   
        $return_arr = array(
            'status' => 1,
            'msg'   => '操作成功',                        
            'data'  => array('url'=>U('Admin/Goods/goodsAttributeList')),
        );
        $this->ajaxReturn(json_encode($return_arr));
    }

    /**
     * 删除商品
     */
    public function delGoods()
    {
        $goods_id = $_GET['id'];
        $error = '';
        
        // 判断此商品是否有订单
        $c1 = M('OrderGoods')->where("goods_id = $goods_id")->count('1');
        $c1 && $error .= '此商品有订单,不得删除! <br/>';
        
        
         // 商品团购
        $c1 = M('group_buy')->where("goods_id = $goods_id")->count('1');
        $c1 && $error .= '此商品有团购,不得删除! <br/>';   
        
         // 商品退货记录
        $c1 = M('return_goods')->where("goods_id = $goods_id")->count('1');
        $c1 && $error .= '此商品有退货记录,不得删除! <br/>';
        
        //TODO: 判断是否有分销产品
        
        if($error)
        {
            $return_arr = array('status' => -1,'msg' =>$error,'data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);        
            $this->ajaxReturn(json_encode($return_arr));            
        }
        
        // 删除此商品        
        M("Goods")->where('goods_id ='.$goods_id)->delete();  //商品表
        M("cart")->where('goods_id ='.$goods_id)->delete();  // 购物车
        M("comment")->where('goods_id ='.$goods_id)->delete();  //商品评论
        M("goods_consult")->where('goods_id ='.$goods_id)->delete();  //商品咨询
        M("goods_images")->where('goods_id ='.$goods_id)->delete();  //商品相册
        M("spec_goods_price")->where('goods_id ='.$goods_id)->delete();  //商品规格
        M("spec_image")->where('goods_id ='.$goods_id)->delete();  //商品规格图片
        M("goods_attr")->where('goods_id ='.$goods_id)->delete();  //商品属性     
        M("goods_collect")->where('goods_id ='.$goods_id)->delete();  //商品收藏          
                     
        $return_arr = array('status' => 1,'msg' => '操作成功','data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);        
        $this->ajaxReturn(json_encode($return_arr));
    }
    
    /**
     * 删除商品类型 
     */
    public function delGoodsType()
    {
        // 判断 商品规格        `tp_spec_type`   `tp_brand_type` 
        $count = M("spec_type")->where("type_id = {$_GET['id']}")->count("1");   
        $count > 0 && $this->error('该类型下有商品规格不得删除!',U('Admin/Goods/goodsTypeList'));
        
        $count = M("brand_type")->where("type_id = {$_GET['id']}")->count("1");   
        $count > 0 && $this->error('该类型下有管理品牌不得删除!',U('Admin/Goods/goodsTypeList'));       
        
        // 判断 商品属性        
        $count = M("GoodsAttribute")->where("type_id = {$_GET['id']}")->count("1");   
        $count > 0 && $this->error('该类型下有商品属性不得删除!',U('Admin/Goods/goodsTypeList'));
        //把该模型下面绑定的分类取消绑定
        model('goods_category')->save(['type_id'=>0],['type_id'=>$_GET['id']]);
        // 删除分类
        M('GoodsType')->where("id = {$_GET['id']}")->delete();


        $this->success("操作成功!!!",U('Admin/Goods/goodsTypeList'));
    }    

    /**
     * 删除商品属性
     */
    public function delGoodsAttribute()
    {
        $id = I('id');
        if(empty($id))  return;
        // 删除 属性
        M("GoodsAttr")->where("attr_id = $id")->delete();
        M('GoodsAttribute')->where("attr_id = $id")->delete();
    }

    /**
     * 删除商品规格
     */
    public function delGoodsSpec()
    {
        $ids = I('post.ids','');
        empty($ids) &&  $this->ajaxReturn(['status' => -1,'msg' =>"非法操作！"]);
        $aspec_ids = rtrim($ids,",");
        // 判断 商品规格项
        $count_ids = Db::name("SpecItem")->whereIn('spec_id',$aspec_ids)->group('spec_id')->getField('spec_id',true);
        if($count_ids){
            $count_ids = implode(',',$count_ids);
            $this->ajaxReturn(['status' => -1,'msg' => "ID为【{$count_ids}】的规格，有规格值不得删除!"]);
        }
        // 删除分类
        Db::name('Spec')->whereIn('id',$aspec_ids)->delete();
        Db::name('SpecType')->whereIn('spec_id',$aspec_ids)->delete();
        $this->ajaxReturn(['status' => 1,'msg' => "操作成功!!!",'url'=>U('Admin/Goods/specList')]);
    }


    /**
     * 品牌列表
     */
    public function brandList(){
        $status = I('status','');
        $keyword = I('keyword');
        $status !== '' && $where['status'] = $status;
        $keyword && $where['name'] = ['like',"%$keyword%"];
        $count = Db::name('brand')->where($where)->count();
        $pager  = new Page($count,10);        
        $brandList = Db::name('brand')->where($where)->order('sort desc')->limit($pager->firstRow.','.$pager->listRows)->select();
        $cat_list = M('goods_category')->where("parent_id = 0")->getField('id,name'); // 已经改成联动菜单
        $this->assign('cat_list',$cat_list);
        $this->assign('pager',$pager);
        $this->assign('brandList',$brandList);
        return $this->fetch('brandList');
    }
    
    /**
     * ajax 获取 品牌列表
     */
    public function getBrandByCat(){
        $db_prefix = C('database.prefix');
        $cat_id = I('cat_id');
        $level = I('l');
        $type_id = I('type_id');        

        if($type_id)
            //$list = M('brand')->join("left join {$db_prefix}brand_type on {$db_prefix}brand.id = {$db_prefix}brand_type.brand_id and  type_id = $type_id")->order('id')->select();    
			 $list = DB::query("SELECT * FROM `__PREFIX__brand` `b` LEFT JOIN `__PREFIX__brand_type` `t` ON `b`.`id`=`t`.`brand_id` and  type_id = :type_id where `b`.`status`=0 ORDER BY id",["type_id"=>$type_id]);
        else    
            $list = M('brand')->where(['status'=>0])->order('id')->select();
        
        $goods_category_list = M('goods_category')->where("id in(select cat_id1 from {$db_prefix}brand) ")->getField("id,name,parent_id");
        $goods_category_list[0] = array('id'=>0, 'name'=>'默认');
        asort($goods_category_list);
        $this->assign('goods_category_list',$goods_category_list);        
        $this->assign('type_id',$type_id);
        $this->assign('list',$list);
        return $this->fetch();
    }
    
    
    /**
     * ajax 获取 规格列表
     */
    public function getSpecByCat(){
        
        $db_prefix = C('database.prefix');
        $cat_id = I('cat_id');
        $level = I('l');
        $type_id = I('type_id');
       	   	   	 
	   
        if($type_id)            
            //$list = M('spec')->join("left join {$db_prefix}spec_type on {$db_prefix}spec.id = {$db_prefix}spec_type.spec_id  and  type_id = $type_id")->order('id')->select();
			$list = DB::query("SELECT * FROM `__PREFIX__spec` `s` LEFT JOIN `__PREFIX__spec_type` `t` ON `s`.`id`=`t`.`spec_id` and  type_id = :type_id ORDER BY id",["type_id"=>$type_id]);
        else    
            $list = M('spec')->order('id')->select();        
                       
        $goods_category_list = M('goods_category')->where("id in(select cat_id1 from {$db_prefix}spec) ")->getField("id,name,parent_id");
        $goods_category_list[0] = array('id'=>0, 'name'=>'默认');
        asort($goods_category_list);               
        $this->assign('goods_category_list',$goods_category_list);
        $this->assign('type_id',$type_id);
        $this->assign('list',$list);
        return $this->fetch();
    }    
    
    /**
     * 添加修改编辑  商品品牌
     */
    public  function addEditBrand(){        
            $id = I('id',0);
           
            if(IS_POST)
            {
                    $data = input('post.');
                    if($id)
                        M("Brand")->update($data);
                    else
                        M("Brand")->insert($data);

                    $this->success("操作成功!!!",U('Admin/Goods/brandList',array('p'=>$_GET['p'])));
                    exit;
            }           
           $cat_list = M('goods_category')->where("parent_id = 0")->select(); // 已经改成联动菜单
           $this->assign('cat_list',$cat_list);
           $brand = M("Brand")->where("id = $id")->find();           
           $this->assign('brand',$brand);
           return $this->fetch('_brand');           
    }

    /**
     * 删除品牌
     */
    public function delBrand()
    {
        $ids = I('post.ids','');
        empty($ids) && $this->ajaxReturn(['status' => -1,'msg' => '非法操作！']);
        $brind_ids = rtrim($ids,",");
        // 判断此品牌是否有商品在使用
        $goods_count = Db::name('Goods')->whereIn("brand_id",$brind_ids)->group('brand_id')->getField('brand_id',true);
        $use_brind_ids = implode(',',$goods_count);
        if($goods_count)
        {
            $this->ajaxReturn(['status' => -1,'msg' => 'ID为【'.$use_brind_ids.'】的品牌有商品在用不得删除!','data'  =>'']);
        }
        $res=Db::name('Brand')->whereIn('id',$brind_ids)->delete();
        if($res){
            $this->ajaxReturn(['status' => 1,'msg' => '操作成功','url'=>U("Admin/goods/brandList")]);
        }
        $this->ajaxReturn(['status' => -1,'msg' => '操作失败','data'  =>'']);
    }

    /**
     * 商品规格列表    
     */
    public function specList(){               
        $cat_list = M('goods_category')->where("parent_id = 0")->getField('id,name,parent_id'); // 已经改成联动菜单                
        $this->assign('cat_list',$cat_list);        
        return $this->fetch();
    }
    
    
    /**
     *  商品规格列表
     */
    public function ajaxSpecList(){ 
        //ob_start('ob_gzhandler'); // 页面压缩输出
        $where = ' 1 = 1 '; // 搜索条件                        
        I('cat_id1')   && $where = "$where and cat_id1 = ".I('cat_id1') ;        
        // 关键词搜索
        $count = Db::name('spec')->where($where)->count();
        $pager  = new AjaxPage($count,13);
        //$show = $pager->show();
        
        $cat_list = Db::name('goods_category')->getField('id,name'); // 已经改成联动菜单
        $specList = Db::name('spec')->where($where)->order('`cat_id1` desc')->limit($pager->firstRow.','.$pager->listRows)->select();
        $this->assign('cat_list',$cat_list);
        $this->assign('specList',$specList);
        $this->assign('pager',$pager);// 赋值分页输出                        
        return $this->fetch();
    }      
    /**
     * 添加修改编辑  商品规格
     */
    public  function addEditSpec(){

            $model = D("spec");
            $type = $_POST['id'] > 0 ? 2 : 1; // 标识自动验证时的 场景 1 表示插入 2 表示更新             
            if(($_GET['is_ajax'] == 1) && IS_POST)//ajax提交验证
            {                
               
                // 数据验证
                $validate = \think\Loader::validate('Spec');
                $post_data = input('post.');
                if(Db::name('spec_item')->where(['spec_id'=>$post_data['id']])->count() >0){
                    $this->ajaxReturn(['status'=>0,'msg'=>'当前规格正在使用，不得编辑']);
                }
                   // C('TOKEN_ON',true); //  form表单提交
                    if ($type == 2){
						//更新数据
						$check = $validate->scene('edit')->batch()->check($post_data);                
                    }else{
						//插入数据
						$check = $validate->batch()->check($post_data);
                    }   
					if (!$check) {
						$error = $validate->getError();
						$error_msg = array_values($error);
						$return_arr = array(
							'status' => -1,
							'msg' => $error_msg[0],
							'data' => $error,
						);
						$this->ajaxReturn($return_arr);
					}
					$model->data($post_data, true); // 收集数据
					if ($type == 2) {
						$model->isUpdate(true)->save(); // 写入数据到数据库
						$model->afterSave(I('id'));
					} else {
						$model->save(); // 写入数据到数据库
						$insert_id = $model->getLastInsID();
						$model->afterSave($insert_id);
					}
				
                    $return_arr = array(
                        'status' => 1,
                        'msg'   => '操作成功',                        
                        'data'  => array('url'=>U('Admin/Goods/specList')),
                    );
                    $this->ajaxReturn($return_arr);
                  
            }                
           // 点击过来编辑时                 
           $id = I('id/d',0);   
           $spec = $model->find($id);         
           $cat_list = M('goods_category')->where("parent_id = 0")->getField('id,name,parent_id'); // 已经改成联动菜单
           $this->assign('cat_list',$cat_list);
           $this->assign('spec',$spec);                                 
           return $this->fetch('_spec');           
    }
    /**
     * 商品批量操作
     */
    public function act()
    {
        $act = I('post.act', '');
        $goods_ids = I('post.goods_ids');
        $goods_state = I('post.goods_state');
        $reason = I('post.reason','无备注');
        $return_success = array('status' => 1, 'msg' => '操作成功', 'data' => '');
        if ($act == 'hot') {
            $hot_condition['goods_id'] = array('in', $goods_ids);
            M('goods')->where($hot_condition)->save(array('is_hot' => 1));
            $this->ajaxReturn($return_success);
        }
        if ($act == 'recommend') {
            $recommend_condition['goods_id'] = array('in', $goods_ids);
            M('goods')->where($recommend_condition)->save(array('is_recommend' => 1));
            $this->ajaxReturn($return_success);
        }
        if ($act == 'new') {
            $new_condition['goods_id'] = array('in', $goods_ids);
            M('goods')->where($new_condition)->save(array('is_new' => 1));
            $this->ajaxReturn($return_success);
        }
        if($act =='takeoff'){
        	$goods = M('goods')->field('store_id,goods_name,goods_sn,purpose')->where(array('goods_id'=>$goods_ids))->find();
			if ($goods['purpose'] == 1) {
				$key = 'is_on_sale';
			} else if ($goods['purpose'] == 2) {
				$key = 'is_supply';
                Db::name('goods')->where(['root_goods_id' => $goods_ids, 'supplier_goods_status' => 0])->update(['supplier_goods_status' => 2,'is_on_sale' => 0]);
                Db::name('goods')->where(['root_goods_id' => $goods_ids, 'supplier_goods_status' => 1])->update(['supplier_goods_status' => 3,'is_on_sale' => 0]);
			}
        	$takeoff_res=M('goods')->where(array('goods_id'=>$goods_ids))->save(array($key =>2,'goods_state'=>0,'close_reason'=>$reason));
            if($takeoff_res){
                adminLog('违规下架商品ID('.$goods_ids.')',6);
                /*$store_msg = array(
                    'store_id' => $goods['store_id'],
                    'content' => "您的商品\"{$goods['goods_name']}\",原因：$reason",
                    'addtime' => time(),
                );
                M('store_msg')->add($store_msg);*/
                $send_data = [
                    'smt_code' =>'goods_violation',
                    'store_id' => $goods['store_id'],
                    'addtime' => time(),
                    'message_val' => ['goods_name' =>$goods['goods_name'], 'reason' => $reason],
                    'content' => '',
                    'message_title'=>'下架'
                ];
                $message_store = new \app\common\logic\MessageStoreLogic($send_data);
                $message_store->sendMessage();

                $this->ajaxReturn(['status' => 1, 'msg' => '操作成功', 'data' => '']);
            }
            $this->ajaxReturn(['status' => 0, 'msg' => '操作失败', 'data' => '']);
        }
        if ($act == 'examine') {
            $goods_array = explode(',', $goods_ids);
            $goods_state_cg = C('goods_state');
            if (!array_key_exists($goods_state, $goods_state_cg)) {
                $return_success = array('status' => -1, 'msg' => '操作失败，商品没有这种属性', 'data' => '');
                $this->ajaxReturn($return_success);
            }
			$goodsList = M('goods')->where(array('goods_id'=>$goods_ids,'supplier_goods_status'=>0))->getField('goods_id,goods_id,store_id,purpose,goods_name,cat_id3,shop_price');
            $store_id_arr = array_column($goodsList, 'store_id');
            $store_bind_all_gc = Db::name('store')->where(['store_id' => ['in', $store_id_arr]])->getField('store_id,bind_all_gc');
            foreach ($goods_array as $key => $val) {
                if (isset($goodsList[$val])) {
                    //判断商品的价格是不是0
                    if ($goodsList[$val]['shop_price'] == 0) {
                        unset($goodsList[$val]);
                        continue;
                    }
                    //判断商品的分类有没审核通过
                    if (!$store_bind_all_gc[$goodsList[$val]['store_id']]) {
                        $store_bind_class = Db::name('store_bind_class')->where(['store_id' => $goodsList[$val]['store_id'], 'class_3' => $goodsList[$val]['cat_id3'], 'state' => 1])->find();
                        if (!$store_bind_class) {
                            unset($goodsList[$val]);
                            continue;
                        }
                    }
                    //客户的特殊要求 --总后台审核通过后，直接上架，不用在商家后台点击上架
    				if ($goodsList[$val]['purpose'] == 1) {
    					$data = $goods_state == 1 ? ['goods_state' => $goods_state, 'is_on_sale'=>1 ] : ['goods_state' => $goods_state];
    				} else {
    					$data = $goods_state == 1 ? ['goods_state' => $goods_state, 'is_supply'=>1 ] : ['goods_state' => $goods_state];

                        $res = Db::name('supplier_goods_modify')->where('goods_id',$goodsList[$val]['goods_id'])->find();
                        if ($res) {//如果有该供应商品的修改记录，就直接覆盖，不管销售商有没全部同意
                            Db::name('supplier_goods_modify')->where('modify_id', $res['modify_id'])->update(['status' => $goods_state]);
                            Db::name('goods')->where(['root_goods_id' => $goodsList[$val]['goods_id'], 'supplier_goods_status' => 2])->update(['supplier_goods_status' => 0]);
                            Db::name('goods')->where(['root_goods_id' => $goodsList[$val]['goods_id'], 'supplier_goods_status' => 3])->update(['supplier_goods_status' => 1]);
                        }
    				}
                    $update_goods_state = M('goods')->where("goods_id = $val")->save($data);
                    if ($update_goods_state) {
                        $update_goods = M('goods')->where(array('goods_id' => $val))->find();
                        // 给商家发站内消息 告诉商家商品被批量操作
                        $send_data = [
                            'smt_code' =>'goods_violation',
                            'store_id' => $goodsList[$val]['store_id'],
                            'addtime' => time(),
                            'message_val' => ['goods_name' =>$goodsList[$val]['goods_name'], 'reason' => $reason],
                            'content' => '',
                            'message_title'=>'审核'
                        ];
                        $message_store = new \app\common\logic\MessageStoreLogic($send_data);
                        $message_store->sendMessage();
                    }
                }
            }
            if (count($goodsList) < count($goods_array)) {
                if (!$goodsList) {
                    $return_success = array('status' => 1, 'msg' => '操作失败，商品暂时无法审核（供应商品的修改，商品类目未通过或售价为零等原因）', 'data' => '');
                } else {
                    $return_success = array('status' => 1, 'msg' => '部分操作成功，部分商品暂时无法审核暂时无法审核（供应商品的修改，商品类目未通过或售价为零等原因）', 'data' => '');
                }
            }
            $this->ajaxReturn($return_success);
        }
        $return_fail = array('status' => -1, 'msg' => '没有找到该批量操作', 'data' => '');
        $this->ajaxReturn($return_fail);
    }

    /**
     * 初始化商品关键词搜索
     */
    public function initGoodsSearchWord(){
        $searchWordLogic = new SearchWordLogic();
        $successNum = $searchWordLogic->initGoodsSearchWord();
        $this->success('成功初始化'.$successNum.'个搜索关键词');
    }

    /**
     * 删除旧的，生成新的商品缩略图
     */
    public function initGoodsImg(){
        $goods_ids = Db::name('goods')->where('is_on_sale',1)->column('goods_id');
        foreach($goods_ids as $goods_id){
            refresh_goods_thumb_by_goodsid($goods_id);
        }
    }

    /**
     * 初始化地址json文件
     */
    public function initLocationJsonJs()
    {
        $goodsLogic = new GoodsLogic();
        $region_list = $goodsLogic->getRegionList();//获取配送地址列表
        $area_list = $goodsLogic->getAreaList();
        $data = "var locationJsonInfoDyr = ".json_encode($region_list, JSON_UNESCAPED_UNICODE).';'."var areaListDyr = ".json_encode($area_list, JSON_UNESCAPED_UNICODE).';';
        file_put_contents(ROOT_PATH."public/js/locationJson.js", $data);
        $this->success('初始化地区json.js成功。文件位置为'.ROOT_PATH."public/js/locationJson.js");
    }
	
	
	/**
     * 检测子级分类是否正常，自动修正
     * @param $id
     */
    function checkGoodsCategory($id=''){
		echo $id;
        if(empty($id)){
            $id = input('id');
			
        }
		$end = input('end');
		
		if($end && $id && $id<$end){
			
			for($i=$id;$i<=$end;$i++){
				$arr = Db::name('goods_category')->where('id',$i)->find();
				if($arr)
				$this->get_up_path($arr);
			}
			echo $id,' ',$end;
			exit;
		}
        //先往上查
        $arr = Db::name('goods_category')->where('id',$id)->find();
        dump($arr);
        $arr = $this->get_up_path($arr);
        dump($arr);
		$arr = Db::name('goods_category')->where('id',$id)->find();
        dump($arr);
        //再下查
        //$list = Db::name('goods_category')->where('parent_id',$arr['id'])->select();

    }
	function get_up_path($arr){
        if(!isset($arr['p'])){
            $arr['p'] = $arr['id'];
            $arr['pl'] = 1;
			$arr['iid'] = $arr['id'];
        }
        $arr2 = Db::name('goods_category')->where('id',$arr['parent_id'])->find();
        if($arr2){
            $arr['p'] = $arr2['id'] . '_' . $arr['p'];
            $arr['pl'] =  $arr['pl'] + 1;
            $arr2['p'] = $arr['p'];
            $arr2['pl'] = $arr['pl'];
			$arr2['iid'] = $arr['iid'];
            return $this->get_up_path($arr2);
        }
        $arr['p'] = '0_'.$arr['p'];
        if($arr['p'] != $arr['parent_id_path'] || $arr['pl'] != $arr['level']){
            $arr['parent_id_path'] = $arr['p'];
            $arr['level'] = $arr['pl'];
            Db::name('goods_category')->where('id',$arr['iid'])->update(['parent_id_path'=>$arr['p'],'level'=>$arr['pl']]);
        }
        return $arr;
    }


}