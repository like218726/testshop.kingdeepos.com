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
 * Date: 2016-06-09
 */

namespace app\shop\controller;

use app\shop\logic\GoodsCategoryLogic;
use think\Db;
use think\Page;

class Index extends Base
{

    public function index()
    {
        $this->pushVersion();
        $shop = session('shop');
        $menu_list = getMenuList($shop['act_limits']);
        $this->assign('menu_list', $menu_list);
        $this->assign('shop', $shop);
        $this->redirect('Shop/Order/index');
        return $this->fetch();
    }

    public function pushVersion()
    {
		//在线升级 
		$isset_push = session('isset_push');         
		if(!empty($isset_push))
			return false;        
		session('isset_push',1);
        error_reporting(0);//关闭所有错误报告
        $app_path = dirname($_SERVER['SCRIPT_FILENAME']) . '/';
        $version_txt_path = $app_path . '/Application/Admin/Conf/version.txt';
        $curent_version = file_get_contents($version_txt_path);

        $vaules = array(
            'domain' => $_SERVER['SERVER_NAME'],
            'last_domain' => $_SERVER['SERVER_NAME'],
            'key_num' => $curent_version,
            'install_time' => INSTALL_DATE,
            'cpu' => '0001',
            'mac' => '0002',
            'serial_number' => SERIALNUMBER,
        );
        $url = "http://service.tp" . '-' . "shop" . '.' . "cn/index.php?m=Home&c=Index&a=user_push&" . http_build_query($vaules);
        stream_context_set_default(array('http' => array('timeout' => 3)));
        file_get_contents($url);
    }

    /**
     * ajax 修改指定表数据字段  一般修改状态 比如 是否推荐 是否开启 等 图标切换的
     * table,id_name,id_value,field,value
     */
    public function changeTableVal()
    {
        $table = I('table'); // 表名
        $id_name = I('id_name'); // 表主键id名
        $id_value = I('id_value'); // 表主键id值
        $field = I('field'); // 修改哪个字段
        $value = I('value'); // 修改字段值
        M($table)->where([$id_name => $id_value, 'store_id' => STORE_ID])->save(array($field => $value)); // 根据条件保存修改的数据
    }

    /**
     * 获取店铺商品分类
     */
    public function goods_category()
    {
        $parent_id = input('parent_id/d', 0); // 商品分类 父id
        $GoodsCategoryLogic = new GoodsCategoryLogic();
        $GoodsCategoryLogic->setStore($this->storeInfo);
        $goods_category_list = $GoodsCategoryLogic->getStoreGoodsCategory($parent_id);
        $this->ajaxReturn($goods_category_list);
    }
}