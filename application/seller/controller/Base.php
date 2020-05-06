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

namespace app\seller\controller;

use app\admin\logic\UpgradeLogic;
use think\Controller;
use think\Session;
use think\Db;

class Base extends Controller
{
    public $storeInfo = array();
    public $store = array();
    public $begin;
    public $end;
    public $select_year;
	public $store_type;


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
        $seller = session('seller');
        tpversion();
        $this->assign('seller', $seller);

    }

    /*
     * 初始化操作
     */
    public function _initialize()
    {
        $this->assign('action', ACTION_NAME);
        //过滤不需要登陆的行为
        if (in_array(ACTION_NAME, array('login', 'logout', 'vertify','ajaxAlbumList'))) {
            //return;
        } else {
            if (session('seller_id') > 0) {
                define('STORE_ID', session('store_id')); //将当前的session_id保存为常量，供其它方法调用
                $store = (new \app\common\model\Store())->where(array('store_id' => STORE_ID))->find();
                $store_grade = Db::name('store_grade')->where(['sg_id'=>$store['grade_id']])->find();
                $store['grade_name'] = $store_grade['sg_name'];
                $store['sg_act_limits'] = $store_grade['sg_act_limits'];
                $this->storeInfo = $store;
                if($store['store_state'] == 0 && CONTROLLER_NAME != 'Index')
                    $this->error('店铺已关闭', U('Index/index'), 1);
				
				if ($store['is_dealer'] == 1) {
					if ($store['is_supplier'] == 1) {
						$this->store_type = 2; //是普通店铺+供应商
					} else {
						$this->store_type = 0;  //普通店铺
					}
				} else {
					$this->store_type = 1;  // 供应商
				}
				$this->assign('store_type', $this->store_type);
                $this->check_priv($store_grade);//检查管理员菜单操作权限
                $menu = include APP_PATH . 'seller/conf/menu.php';
                $menuArr = $menu;
				$right = $this->getSystemMenuRight($store_grade);
				$role_right = '';
				if (count($right) > 0) {
					foreach ($right as $val) {
						$role_right .= $val . ',';
					}
				}
				$role_right = explode(',', $role_right);
				foreach($menu as $menuKey=>$menuVal){
					$childArr = [];
					foreach($menuVal['child'] as $childKey => $childVal){
						$op_act = $childVal['op'].'@'.$childVal['act'];
						if (in_array($op_act, $role_right)) {
							array_push($childArr, $childVal);
						}
					}
					if(count($childArr) == 0){
						unset($menuArr[$menuKey]);
					}elseif (!$switch1688 && 'OpenAlibaba' == $childVal['op']) {
                        unset($menuArr[$menuKey]);
                    }else{
						$menuArr[$menuKey]['child'] = $childArr;
					}
				}
                $this->assign('menuArr', $menuArr);//所有菜单
                $this->assign('leftMenu', get_left_menu($menuArr));//左侧导航菜单
                if(is_array($_SESSION['seller_quicklink'])){
                    $this->assign('quicklink',array_keys($_SESSION['seller_quicklink']));//快捷操作菜单
                }
                $store['full_address'] = getRegionName($store['province_id']) . ' ' . getRegionName($store['city_id']) . ' ' . getRegionName($store['district']);
                $storeMsgNoReadCount = Db::name('store_msg')->where(['store_id'=>STORE_ID,'open'=>0])->count();
                $this->assign('storeMsgNoReadCount', $storeMsgNoReadCount);
                $this->store = $store;
                $this->assign('store', $store);
            } else {
                 (ACTION_NAME == 'index') && $this->redirect( U('Seller/Admin/login'));
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
        if(I('start_time')){
            $begin = I('start_time');
            $end = I('end_time');
        }else{
            $begin = date('Y-m-d', strtotime("-3 month"));//30天前
            $end = date('Y-m-d', strtotime('+1 days'));
        }
        $this->assign('start_time',$begin);
        $this->assign('end_time',$end);
        $this->select_year = getTabByTime(I('start_time')); // 表后缀
        $this->begin = strtotime($begin);
        $this->end = strtotime($end)+86399;
        $this->assign('tpshop_config', $tpshop_config);
    }

    public function check_priv($store_grade)
    {
        $seller = session('seller');
        $ctl = request()->controller();
        $act = request()->action();
        $uneed_check = array('login', 'logout', 'vertifyHandle', 'vertify', 'imageUp','delupload','videoUp','upload', 'login_task', 'modify_pwd','index');//修改密码不需要验证权限
        if ($seller['is_admin'] == 0) {
            $act_list = $seller['act_limits'];
            //无需验证的操作
            if ($ctl == 'Index' || $act_list == 'all') {
                //后台首页控制器无需验证,超级管理员无需验证
                return true;
            }elseif(request()->isAjax() || strpos($act,'ajax')!== false || in_array($act,$uneed_check)){
                //所有ajax请求不需要验证权限
                return true;
            } else {
                $right = Db::name('system_menu')->where("id", "in", $act_list)->cache(true)->getField('right', true);
                $role_right = '';
                if (count($right) > 0) {
                    foreach ($right as $val) {
                        $role_right .= $val . ',';
                    }
                }
                $role_right = explode(',', $role_right);
                //检查是否拥有此操作权限
                if (!in_array($ctl.'@'.$act, $role_right)) {
                    $this->error('您没有操作权限'.($ctl.'@'.$act).',请联店铺超级管理员分配权限', U('Index/index'));
                }
            }
        }
        if($ctl == 'Index' || request()->isAjax() || strpos($act,'ajax')!== false || in_array($act,$uneed_check)){
            return true;
        }
		$right = $this->getSystemMenuRight($store_grade);
            $role_right = '';
            if (count($right) > 0) {
                foreach ($right as $val) {
                    $role_right .= $val . ',';
                }
            }
            $role_right = explode(',', $role_right);
            //检查是否拥有此操作权限
            if (!in_array($ctl.'@'.$act, $role_right)) {
                $this->error('店铺等级没有操作权限'.($ctl.'@'.$act).',请联系平台管理员分配权限', U('Index/index'));
            }
        return true;
    }

    public function ajaxReturn($data, $type = 'json')
    {
        ob_end_clean();
        exit(json_encode($data));
    }

	private function getSystemMenuRight() {
		$map = [
			'is_del' => 0
		];
		if ($this->store_type == 0) {
			//销售商权限条件
			if($store_grade && $store_grade['sg_act_limits']){
				if($store_grade['sg_act_limits'] == 'all'){
					$map['type'] = '1';
				}else{
					$map['id'] = ['in', $store_grade['sg_act_limits']];
				}
			} else {
				$map['type'] = '1';
			}
		} else if ($this->store_type == 1) {
			//供应商权限条件
			if($store_grade && $store_grade['sg_supplier_act_limits']){
				if($store_grade['sg_supplier_act_limits'] == 'all'){
					$map['type'] = '6';
				}else{
					$map['id'] = ['in', $store_grade['sg_supplier_act_limits']];
				}
			} else {
				$map['type'] = '6';
			}
		} else {
			//销售商+供应商权限条件
			if($store_grade){
				if (!$store_grade['sg_act_limits'] || $store_grade['sg_act_limits'] == 'all') {
					$map['type'] = '1';
				} else {
					$map['id'] = ['in', $store_grade['sg_act_limits']];
				}
				if (!$store_grade['sg_supplier_act_limits'] || $store_grade['sg_supplier_act_limits'] == 'all') {
					$mapOr['type'] = '6';
				} else {
					$mapOr['id'] = ['in', $store_grade['sg_supplier_act_limits']];
				}
			} else {
				$map['type'] = '1';
				$mapOr['type'] = '6';
			}
		}
		if ($this->store_type == 2) {
			$right = Db::name('system_menu')->where($map)->whereOr($mapOr)->cache(true)->getField('right', true);
		} else {
			$right = Db::name('system_menu')->where($map)->cache(true)->getField('right', true);
		}
		return $right;
	}
}