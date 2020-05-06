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
 * Date: 2015-09-09
 */

/**
 * 管理员操作记录
 * @param $log_url 操作URL
 * @param $log_info 记录信息
 * @param $log_type 日志类别
 */
function adminLog($log_info,$log_type=0){
    $add['log_time'] = time();
    $add['admin_id'] = session('admin_id');
    $add['log_info'] = $log_info;
    $add['log_ip'] = getIP();
    $add['log_url'] = \think\Request::instance()->url();
    $add['log_type'] = $log_type;
	$get = input('');
	//过滤掉不需要存的参数
	unset($get['unique_id']);
	unset($get['is_json']);
	unset($get['token']);
	unset($get['m']);
	unset($get['c']);
	unset($get['a']);
	$request_param = '';
	foreach ($get as $k=>$v){
		$request_param .=  "/$k/$v";
	}
	$add['request_param'] = $request_param;
    M('admin_log')->add($add);
}
 
function getAdminInfo($admin_id){
	return M('admin')->where(array('admin_id'=>$admin_id))->find();
}

function tpversion()
{     
    //在线升级 
	$isset_push = session('isset_push');         
	if(!empty($isset_push))
		return false;        
	session('isset_push',1);
    error_reporting(0);//关闭所有错误报告
    $app_path = dirname($_SERVER['SCRIPT_FILENAME']).'/';
    $version_txt_path = $app_path.'/application/admin/conf/version.php';
    $curent_version = file_get_contents($version_txt_path);
    
    $vaules = array(            
            'domain'=>$_SERVER['HTTP_HOST'], 
            'last_domain'=>$_SERVER['HTTP_HOST'], 
            'key_num'=>$curent_version, 
            'install_time'=>INSTALL_DATE, 
            'cpu'=>'0001',
            'mac'=>'0002',
            'serial_number'=>SERIALNUMBER,
            );     
     $url = "http://service.tp-shop.cn/index.php?m=Home&c=Index&a=user_push&".http_build_query($vaules);
     stream_context_set_default(array('http' => array('timeout' => 3)));
     file_get_contents($url);       
}

/**
 * 导出excel
 * @param $strTable	表格内容
 * @param $filename 文件名
 */
function downloadExcel($strTable,$filename)
{
	header("Content-type: application/vnd.ms-excel");
	header("Content-Type: application/force-download");
	header("Content-Disposition: attachment; filename=".$filename."_".date('Y-m-d').".xls");
	header('Expires:0');
	header('Pragma:public');
	echo '<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.$strTable.'</html>';
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
	$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
	return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 根据id获取地区名字
 * @param $regionId id
 */
function getRegionName($regionId){
    $data = M('region')->where(array('id'=>$regionId))->field('name')->find();
    return $data['name'];
}

function getMenuList($act_list){
	//根据角色权限过滤菜单
	$menu_list = getAllMenu();
	if($act_list != 'all' && !empty($act_list)){
		$right = M('system_menu')->where("id in ($act_list)")->cache(true)->getField('right',true);
		foreach ($right as $val){
			$role_right .= $val.',';
		}
		$role_right = explode(',', $role_right);
		foreach($menu_list as $k=>$mrr){
			foreach ($mrr['sub_menu'] as $j=>$v){
				if(!in_array($v['control'].'@'.$v['act'], $role_right)){
					unset($menu_list[$k]['sub_menu'][$j]);//过滤菜单
				}
			}
		}
		
		foreach ($menu_list as $mk=>$mr){
			if(empty($mr['sub_menu'])){
				unset($menu_list[$mk]);
			}
		}
	}
	return $menu_list;
}

function getAllMenu(){
	return	array(
			'system' => array('name'=>'系统设置','icon'=>'fa-cog','sub_menu'=>array(
					array('name'=>'网站设置','act'=>'index','control'=>'System'),
					array('name'=>'自定义导航','act'=>'navigationList','control'=>'System'),
					array('name'=>'区域管理','act'=>'region','control'=>'Tools'),
			        array('name'=>'短信模板','act'=>'index','control'=>'SmsTemplate'),
			)),
			'access' => array('name' => '权限管理', 'icon'=>'fa-gears', 'sub_menu' => array(
			        array('name'=>'权限资源列表','act'=>'right_list','control'=>'System'),
					array('name' => '管理员列表', 'act'=>'index', 'control'=>'Admin'),
					array('name' => '角色管理', 'act'=>'role', 'control'=>'Admin'),
					array('name' => '管理员日志', 'act'=>'log', 'control'=>'Admin'),
					array('name' => '供应商列表', 'act'=>'supplier', 'control'=>'Admin'),
			    
			)),
			'member' => array('name'=>'会员管理','icon'=>'fa-user','sub_menu'=>array(
					array('name'=>'会员列表','act'=>'index','control'=>'User'),
					array('name'=>'会员等级','act'=>'levelList','control'=>'User'),
					array('name'=>'会员充值','act'=>'recharge','control'=>'User'),
					//array('name'=>'会员整合','act'=>'integrate','control'=>'User'),
			)),
			'goods' => array('name' => '商品管理', 'icon'=>'fa-tasks', 'sub_menu' => array(
					array('name' => '商品分类', 'act'=>'categoryList', 'control'=>'Goods'),
					array('name' => '商品列表', 'act'=>'goodsList', 'control'=>'Goods'),
					array('name' => '库存日志', 'act'=>'stock_list', 'control'=>'Goods'),
					array('name' => '商品模型', 'act'=>'goodsTypeList', 'control'=>'Goods'),
					array('name' => '商品规格', 'act' =>'specList', 'control' => 'Goods'),
					array('name' => '品牌列表', 'act'=>'brandList', 'control'=>'Goods'),
			)),
			'order' => array('name' => '订单管理', 'icon'=>'fa-money', 'sub_menu' => array(
					array('name' => '订单列表', 'act'=>'index', 'control'=>'Order'),
					//array('name' => '发货单', 'act'=>'delivery_list', 'control'=>'Order'),
					//array('name' => '快递单', 'act'=>'express_list', 'control'=>'Order'),
					array('name' => '退货单', 'act'=>'return_list', 'control'=>'Order'),
					//array('name' => '订单日志', 'act'=>'order_log', 'control'=>'Order'),
					array('name' => '商品评论','act'=>'index','control'=>'Comment'),
//					array('name' => '商品咨询','act'=>'ask_list','control'=>'Comment'),
					array('name' => '投诉管理','act'=>'complain_list', 'control'=>'Comment'),
			)),
			'Store' => array('name' => '店铺管理', 'icon'=>'fa-home', 'sub_menu' => array(
					array('name' => '店铺等级', 'act'=>'store_grade', 'control'=>'Store'),
					array('name' => '店铺分类', 'act'=>'store_class', 'control'=>'Store'),
					array('name' => '店铺列表', 'act'=>'store_list', 'control'=>'Store'),					
					array('name' => '自营店铺', 'act'=>'store_own_list', 'control'=>'Store'),
					array('name' => '经营类目审核', 'act'=>'apply_class_list', 'control'=>'Store'),
			)),
			'Ad' => array('name' => '广告管理', 'icon'=>'fa-flag', 'sub_menu' => array(
					array('name' => '广告列表', 'act'=>'adList', 'control'=>'Ad'),
					array('name' => '广告位置', 'act'=>'positionList', 'control'=>'Ad'),
			)),			
			'promotion' => array('name' => '活动管理', 'icon'=>'fa-bell', 'sub_menu' => array(
					array('name' => '抢购管理', 'act'=>'flash_sale', 'control'=>'Promotion'),
					array('name' => '团购管理', 'act'=>'group_buy_list', 'control'=>'Promotion'),
					array('name' => '优惠促销', 'act'=>'prom_goods_list', 'control'=>'Promotion'),
					array('name' => '订单促销', 'act'=>'prom_order_list', 'control'=>'Promotion'),
//					array('name' => '代金券','act'=>'index', 'control'=>'Coupon'),
			)),
			'content' => array('name' => '内容管理', 'icon'=>'fa-comments', 'sub_menu' => array(
					array('name' => '文章列表', 'act'=>'articleList', 'control'=>'Article'),
					array('name' => '文章分类', 'act'=>'categoryList', 'control'=>'Article'),
					//array('name' => '帮助管理', 'act'=>'help_list', 'control'=>'Article'),
					array('name'=>'友情链接','act'=>'linkList','control'=>'Article'),
					//array('name' => '公告管理', 'act'=>'notice_list', 'control'=>'Article'),
					array('name' => '专题列表', 'act'=>'topicList', 'control'=>'Topic'),
			)),
			'weixin' => array('name' => '微信管理', 'icon'=>'fa-weixin', 'sub_menu' => array(
					array('name' => '公众号管理', 'act'=>'index', 'control'=>'Wechat'),
					array('name' => '微信菜单管理', 'act'=>'menu', 'control'=>'Wechat'),
					array('name' => '文本回复', 'act'=>'text', 'control'=>'Wechat'),
					array('name' => '图文回复', 'act'=>'img', 'control'=>'Wechat'),
					//array('name' => '组合回复', 'act'=>'nes', 'control'=>'Wechat'),
					//array('name' => '抽奖活动', 'act'=>'nes', 'control'=>'Wechat'),
			)),
			'theme' => array('name' => '模板管理', 'icon'=>'fa-adjust', 'sub_menu' => array(
					array('name' => 'PC端模板', 'act'=>'templateList?t=pc', 'control'=>'Template'),
					array('name' => '手机端模板', 'act'=>'templateList?t=mobile', 'control'=>'Template'),
			)),
        	'distribut' => array('name' => '分销管理', 'icon'=>'fa-cubes', 'sub_menu' => array(
					array('name' => '分销商品列表', 'act'=>'goods_list', 'control'=>'Distribut'),
					array('name' => '分销商列表', 'act'=>'distributor_list', 'control'=>'Distribut'),
					array('name' => '分销关系', 'act'=>'tree', 'control'=>'Distribut'),
					array('name' => '分销设置', 'act'=>'set', 'control'=>'Distribut'),
					array('name' => '分成日志', 'act'=>'rebate_log', 'control'=>'Distribut'),
			)),
			'tools' => array('name' => '插件工具', 'icon'=>'fa-plug', 'sub_menu' => array(
					array('name' => '插件列表', 'act'=>'index', 'control'=>'Plugin'),
					array('name' => '数据备份', 'act'=>'index', 'control'=>'Tools'),
					array('name' => '数据还原', 'act'=>'restore', 'control'=>'Tools'),
			)),
			'finance' => array('name' => '财务管理', 'icon'=>'fa-book', 'sub_menu' => array(
					array('name' => '商家提现申请', 'act'=>'store_withdrawals', 'control'=>'Finance'),
					array('name' => '商家汇款记录', 'act'=>'store_remittance', 'control'=>'Finance'),
					array('name' => '会员提现申请', 'act'=>'withdrawals', 'control'=>'Finance'),
					array('name' => '会员汇款记录', 'act'=>'remittance', 'control'=>'Finance'),
					array('name' => '商家结算记录', 'act'=>'order_statis', 'control'=>'Finance'),
					array('name' => '订单佣金结算', 'act'=>'order_statis', 'control'=>'Finance'),
			)),
			'count' => array('name' => '统计报表', 'icon'=>'fa-signal', 'sub_menu' => array(
					array('name' => '销售概况', 'act'=>'index', 'control'=>'Report'),
					array('name' => '销售排行', 'act'=>'saleTop', 'control'=>'Report'),
					array('name' => '会员排行', 'act'=>'userTop', 'control'=>'Report'),
					array('name' => '销售明细', 'act'=>'saleList', 'control'=>'Report'),
					array('name' => '会员统计', 'act'=>'user', 'control'=>'Report'),
					array('name' => '运营概览', 'act'=>'finance', 'control'=>'Report'),
			))
	);
}

function getMenuArr(){
    //引入平台按钮的数组文件
    $menuArr = include APP_PATH.'admin/conf/menu.php';
	$act_list = session('act_list');    //当前管理员权限组
	if($act_list != 'all' && !empty($act_list)){
		$right = M('system_menu')->where("id in ($act_list)")->cache(true)->getField('right',true);
		foreach ($right as $val){
			$role_right .= $val.',';
		}

		foreach($menuArr as $k=>$val){
			foreach ($val['child'] as $j=>$v){
				foreach ($v['child'] as $s=>$son){
					if(strpos($role_right,$son['op'].'@'.$son['act']) === false){
						unset($menuArr[$k]['child'][$j]['child'][$s]);//过滤菜单
					}
				}
			}
		}

		foreach ($menuArr as $mk=>$mr){
			foreach ($mr['child'] as $nk=>$nrr){
				if(empty($nrr['child'])){
					unset($menuArr[$mk]['child'][$nk]);
				}
			}
		}
	}
	return $menuArr;
}

function respose($res){
	exit(json_encode($res));
}

/**
 * 获得指定分类下的子分类的数组
 * @access  public
 * @param   $data
 * @param   $id
 * @return  mix
 */
function getSortCatArray($data=[],$id)
{
    global $all_type, $all_type2;
    $all_type = convert_arr_key($data,$id);
    foreach ($all_type AS $key => $value)
    {
        if($value['level'] == 0)
            getCatTree($value[$id],$id);  //将下级分类紧挨上级分类排序
    }
    return $all_type2;
}

/**
 * 获取指定id下的 所有分类，将下级分类紧挨上级分类排序
 * @param type $id 当前显示的 菜单id
 * @param $type 分类主键
 * @return 返回数组 Description
 */
function getCatTree($id,$type)
{
    global $all_type, $all_type2;
    $all_type2[$id] = $all_type[$id];
    foreach ($all_type AS $key => $value){
        if($value['pid'] == $id)
        {
            getCatTree($value[$type],$type);
            $all_type2[$id]['have_son'] = 1; // 还有下级
        }
    }
}

/**
 * 总后台操作动作信息
 * @rturn $log_info	动作信息
 */
function action_log_info()
{
	$request = think\Request::instance();
	$url = $request->module().'/'.$request->controller().'/'.$request->action(); // 模块_控制器_方法
	$action_array =
			[
				'Ad' => [
						'admin/Ad/adHandle' 			=>		['add' =>'添加了广告',		'edit' => '编辑了广告',		'del' =>'删除了广告'],
						'admin/Ad/positionHandle' 		=> 		['add' =>'添加了广告位置', 	'edit' =>'编辑了广告位置', 	'del' =>'删除了广告位置'],
				],
				'Admin' => [
						'admin/Admin/adminHandle' 		=> 		['add' =>'添加了管理员', 		'edit' =>'编辑了管理员', 		'del' =>'删除了管理员'],
						'admin/Admin/supplierHandle' 	=> 		['add' =>'添加了供应商', 		'edit' =>'编辑了供应商', 		'del' =>'删除了供应商'],
						'admin/Admin/site_save' 		=> 		['操作了管理员分站'],
						'admin/Admin/site_delete' 		=> 		['删除管理员分站'],
				],
				'Article' => [
						'admin/Article/categoryHandle' 	=> 		['add' =>'添加了文章分类', 	'edit' =>'编辑了文章分类', 	'del' =>'删除了文章分类'],
						'admin/Article/aticleHandle' 	=> 		['add' =>'添加了文章', 		'edit' =>'编辑了文章', 		'del' =>'删除了文章'],
						'admin/Article/linkHandle' 		=> 		['del' =>'删除了友情链接'],
						'admin/Article/addEdit' 		=> 		['操作了友情链接'],
						'admin/Article/helpHandle' 		=> 		['add' =>'添加了帮助内容', 	'edit' =>'编辑了帮助内容', 	'del' =>'删除了帮助内容'],
						'admin/Article/helpTypeHandle' 	=> 		['add' =>'添加了帮助类型', 	'edit' =>'编辑了帮助类型', 	'del' =>'删除了帮助类型'],
						'admin/Article/edit_agreement' 	=> 		['编辑了系统文章'],
				],
				'Block' => [
						'admin/Block/add_data' 			=> 		['编辑了系统文章'],
						'admin/Block/set_index' 		=> 		['编辑了模板'],
						'admin/Block/delete' 			=> 		['删除了模板'],
						'admin/Block/delete_form' 		=> 		['删除了智能表单'],
				],
				'Comment' => [
						'admin/Comment/detail' 			=> 		['添加了商品评论'],
						'admin/Comment/commentHandle' 	=> 		['show' =>'打开商品评论', 	'hide' =>'关闭商品评论', 	'del' =>'删除了商品评论'],
						'admin/Comment/consult_info' 	=> 		['添加了商品评论'],
						'admin/Comment/ask_handle' 		=> 		['show' =>'打开商品咨询', 	'hide' =>'关闭商品咨询', 	'del' =>'删除了商品咨询'],
						'admin/Comment/subject_info' 	=> 		['添加投诉主题'],
				],
				'Coupon' => [
						'admin/Coupon/send_coupon' 		=> 		['发放了优惠券'],
						'admin/Coupon/del_coupon' 		=> 		['删除了优惠券类型'],
						'admin/Coupon/coupon_list_del' 	=> 		['删除了优惠券'],
						'admin/Coupon/coupon_price_del'	=> 		['删除了优惠券面额'],
				],
				'Distribut' => [
						'admin/Coupon/gradeInfoSave' 	=> 		['添加了分销等级类别'],
						'admin/Coupon/delGoods' 		=> 		['删除分销商品(变成普通商品)'],
				],
				'Finance' => [
						'admin/Finance/delStoreWithdrawals'		=> 		['删除商家提现申请记录'],
						'admin/Finance/editStoreWithdrawals' 	=> 		['编辑商家申请提现'],
						'admin/Finance/delWithdrawals' 			=> 		['删除用户提现申请记录'],
						'admin/Finance/editWithdrawals' 		=> 		['编辑用户提现申请记录'],
						'admin/Finance/withdrawals_update' 		=> 		['处理会员提现申请'],
						'admin/Finance/store_withdrawals_update'=> 		['处理商家提现申请'],
						'admin/Finance/transfer'				=> 		['审批用户提现'],
						'admin/Finance/store_transfer'			=> 		['审批商家提现'],
				],
				'Goods' => [
					'admin/Goods/addEditCategory'		=> 		['操作商品分类'],
					'admin/Goods/delGoodsCategory' 		=> 		['删除商品分类'],
					'admin/Goods/delGoods' 				=> 		['删除商品'],
					'admin/Goods/delGoodsType' 			=> 		['删除商品类型'],
					'admin/Goods/delGoodsAttribute' 	=> 		['删除商品属性'],
					'admin/Goods/addEditSpec' 			=> 		['添加修改商品规格'],
					'admin/Goods/delGoodsSpec'			=> 		['删除商品规格'],
					'admin/Goods/addEditBrand'			=> 		['添加或修改商品品牌'],
					'admin/Goods/delBrand'				=> 		['删除品牌'],
					'admin/Goods/act'					=> 		['hot' =>'更新商品热门状态', 		'recommend' =>'更新商品推荐状态', 	'new' =>'更新商品是否为最新',		'examine' => '审核商品'],
				],
				'GoodsType' => [
						'admin/GoodsType/edit'			=> 		['商品模型添加及编辑'],
						'admin/Goods/deleteSpec' 		=> 		['删除商品规格'],
				],
				'Guarantee' => [
						'admin/Guarantee/apply_edit'	=> 		['操作店铺消费者保障服务'],
						'admin/Guarantee/item_delete' 	=> 		['删除消费者保障服务项目'],
						'admin/Guarantee/itemHandle' 	=> 		['add' =>'添加消费者保障服务项目', 	'edit' =>'消费者保障服务项目'],
				],
				'Invoice' => [
						'admin/Invoice/changetime'		=> 		['更新发票创建时间'],
				],
				'MessageTemplate' => [
						'admin/MessageTemplate/editMemberTemplate'	=> 		['add' =>'添加用户模板消息', 	'edit' =>'修改用户模板消息'],
						'admin/MessageTemplate/editStoreTemplate'	=> 		['add' =>'添加商家模板消息', 	'edit' =>'修改商家模板消息'],
				],
				'News' => [
						'admin/News/categoryHandle'		=> 		['add' =>'添加了新闻分类', 	'edit' =>'编辑了新闻分类', 	'del' =>'删除了新闻分类'],
						'admin/News/aticleHandle'		=> 		['add' =>'添加了新闻', 	'edit' =>'编辑了新闻', 	'del' =>'删除了新闻'],
						'admin/News/delNewsList'		=> 		['批量删除新闻'],
						'admin/News/commentHandle'		=> 		['审核新闻评论'],
				],
				'Order' => [
						'admin/Order/refund_order'		=> 		['处理取消订单  订单原路退款'],
						'admin/Order/editprice'			=> 		['修改订单价格'],
						'admin/Order/pay_cancel'		=> 		['订单取消付款'],
						'admin/Order/return_del'		=> 		['删除退换货申请'],
						'admin/Order/return_info'		=> 		['退换货操作'],
						'admin/Order/delOrderLogo'		=> 		['批量删除订单'],
				],
				'Plugin' => [
						'admin/Plugin/install'			=> 		['插件安装卸载'],
						'admin/Plugin/setting'			=> 		['更新插件信息配置'],
						'admin/Plugin/uploadCert'		=> 		['上传商户证书文件'],
				],
				'PreSell' => [
						'admin/PreSell/examine'			=> 		['审核预售商品'],
						'admin/PreSell/setting'			=> 		['更新插件信息配置'],
				],
				'Promotion' => [
						'admin/Promotion/prom_goods_del'		=> 		['删除促销活动'],
						'admin/Promotion/ajax_prom_goods_del'	=> 		['删除促销活动'],
						'admin/Promotion/prom_order_del'		=> 		['删除订单促销活动'],
						'admin/Promotion/ajax_prom_order_del'	=> 		['删除订单促销活动'],
						'admin/Promotion/groupbuyHandle'		=> 		['删除团购活动'],
						'admin/Promotion/flash_sale_info'		=> 		['操作抢购活动'],
						'admin/Promotion/flash_sale_del'		=> 		['删除抢购活动'],
						'admin/Promotion/closeProm'				=> 		['关闭活动'],
						'admin/Promotion/closePromGoods'		=> 		['关闭促销活动'],
				],
				'Service' => [
						'admin/Service/detail'			=> 		['添加评论'],
						'admin/Service/del'				=> 		['删除评论'],
						'admin/Service/op'				=> 		['show' =>'打开评论', 	'hide' =>'关闭评论', 	'del' =>'删除了评论'],
						'admin/Service/refund_info'		=> 		['处理买家退货退款 商品退款原路返回'],
						'admin/Service/consult_info'	=> 		['添加评论'],
						'admin/Service/flash_sale_info'	=> 		['操作抢购活动'],
						'admin/Service/flash_sale_del'	=> 		['删除抢购活动'],
						'admin/Service/closeProm'		=> 		['关闭活动'],
						'admin/Service/closePromGoods'	=> 		['关闭促销活动'],
						'admin/Service/ask_handle'		=> 		['show' =>'打开商品咨询', 	'hide' =>'关闭商品咨询', 	'del' =>'删除了商品咨询'],
						'admin/Service/subject_del'		=> 		['删除投诉主题'],
						'admin/Service/complain_subject_info'=> ['添加投诉主题'],
						'admin/Service/expose_type_del'	=> 		['删除举报类型'],
						'admin/Service/expose_subject_info'	 => ['添加举报主题'],
						'admin/Service/expose_subject_del'	 => ['删除举报主题'],
				],
				'Shipping' => [
						'admin/Shipping/save'			=> 		['添加和更新快递公司'],
						'admin/Shipping/delete'			=> 		['删除快递公司'],
						'admin/Shipping/handle'			=> 		['新增修改快递公司配置'],
				],
				'ShopOrder' => [
						'admin/ShopOrder/writeOff'		=> 		['核销'],
				],
				'SmsTemplate' => [
						'admin/SmsTemplate/addEditSmsTemplate'=>['添加或修改短信模板'],
						'admin/SmsTemplate/delTemplate'		  =>['删除短信模板'],
				],
				'Store' => [
						'admin/Store/grade_info_save'	=>		['add' =>'添加了店铺等级', 	'edit' =>'编辑了店铺等级', 	'del' =>'删除了店铺等级'],
						'admin/Store/store_add'			=> 		['添加店铺'],
						'admin/Store/store_edit'		=> 		['编辑店铺'],
						'admin/Store/changeDefaultStore'=> 		['更新店铺为自营店铺'],
						'admin/Store/store_info_edit'=> 		['编辑外驻店铺'],
						'admin/Store/store_del'			=> 		['删除店铺'],
						'admin/Store/apply_del'			=> 		['删除店铺信息'],
						'admin/Store/apply_class_save'	=> 		['添加店铺可发布商品类目'],
						'admin/Store/reopen_save'		=> 		['审核签约申请'],
						'admin/Store/reopen_del'		=> 		['删除签约申请'],
						'admin/Store/domain_edit'		=> 		['编辑店铺域名'],
				],
				'System' => [
						'admin/System/handle'			=>		[' 新增修改配置'],
						'admin/System/seo_update'		=> 		['编辑SEO信息'],
						'admin/System/addEditNav'		=> 		['添加或修改前台导航'],
						'admin/System/delNav'			=> 		['删除前台导航'],
						'admin/System/edit_right'		=> 		['编辑权限'],
						'admin/System/right_del'		=> 		['删除权限'],
						'admin/System/poster_add'		=> 		['添加自定义海报模板'],
				],
				'Team' => [
						'admin/Team/examine'			=>		[' 审核拼团活动'],
				],
				'User' => [
						'admin/User/detail'				=>		[' 编辑用户信息'],
						'admin/User/add_user'			=>		[' 添加用户信息'],
						'admin/User/delete'				=>		[' 删除用户'],
						'admin/User/ajax_delete'		=>		[' 删除用户'],
						'admin/User/levelHandle'		=>		['add' =>'添加了会员等级', 	'edit' =>'编辑了会员等级', 	'del' =>'删除了会员等级'],
						'admin/User/label'				=>		['操作了会员标签'],
				],

			];

	$action_log_info = '';
	$controller = $request->controller();
	if ($action_array[$controller][$url]) {//在配置中是否有，没有的话手动添加下

		$act = input('act');
		$action_log_info = $action_array[$controller][$url][$act];

		if (IS_POST && empty($action_log_info) && empty($act)) {//针对没有传动作（add、edit、del这种）的一些方法
			$action_log_info = $action_array[$controller][$url][0];
		}
	}

	if (!empty($action_log_info)) {
		return $action_log_info;
	} else {
		return false;
	}

}
