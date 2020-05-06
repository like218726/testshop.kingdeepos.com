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

use app\admin\logic\UpgradeLogic;
use think\Controller;
use think\Session;
use think\Db;

class Base extends Controller
{
    public $shopInfo = array();

    /**
     * 析构函数
     */
    function __construct()
    {
        Session::start();
        header("Cache-control: private");
        parent::__construct();
        $upgradeLogic = new UpgradeLogic();
        $upgradeMsg = $upgradeLogic->checkVersion(); //升级包消息        
        $this->assign('upgradeMsg', $upgradeMsg);
        //用户中心面包屑导航
        tpversion();
    }

    /*
     * 初始化操作
     */
    public function _initialize()
    {
        $this->assign('action', ACTION_NAME);
        //过滤不需要登陆的行为
        if (in_array(ACTION_NAME, array('login', 'logout', 'vertify'))) {
            //return;
        } else {
            if (session('shop_id') > 0) {
                define('SHOP_ID', session('shop_id')); //将当前的session_id保存为常量，供其它方法调用
                $shop = Db::name('shop')->where(array('shop_id' => SHOP_ID))->find();
                define('STORE_ID',$shop['store_id']);
                $this->shopInfo = $shop;
                if($shop['shop_status'] == 0 && CONTROLLER_NAME != 'Index'){
                    $this->error('店铺已关闭', U('Index/index'), 1);
                }
                $menuArr = include APP_PATH . 'shop/conf/menu.php';
                $this->assign('menuArr', $menuArr);//所有菜单
                $this->assign('leftMenu', get_left_menu($menuArr));//左侧导航菜单
                $this->assign('shop', $shop);
            } else {
                $this->error('请先登录', U('Admin/login'), 1);
            }
        }
        $this->public_assign();
    }

    /**
     * 保存公告变量到 smarty中 比如 导航
     */
    public function public_assign()
    {
        $tpshop_config = array();
        $tp_config = M('config')->cache(true)->select();
        foreach ($tp_config as $k => $v) {
            $tpshop_config[$v['inc_type'] . '_' . $v['name']] = $v['value'];
        }
        $this->assign('tpshop_config', $tpshop_config);
    }

    public function ajaxReturn($data, $type = 'json')
    {
        exit(json_encode($data));
    }

}