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
function getAdminInfo($admin_id)
{
    return D('admin')->where("admin_id", $admin_id)->find();
}

function tpversion()
{
    //在线升级 
	$isset_push = session('isset_push');         
	if(!empty($isset_push))
		return false;        
	session('isset_push',1);
    error_reporting(0);//关闭所有错误报告
    $app_path = dirname($_SERVER['SCRIPT_FILENAME']) . '/';
    $version_txt_path = $app_path . '/application/admin/conf/version.txt';
    $curent_version = file_get_contents($version_txt_path);

    $vaules = array(
        'domain' => $_SERVER['HTTP_HOST'],
        'last_domain' => $_SERVER['HTTP_HOST'],
        'key_num' => $curent_version,
        'install_time' => INSTALL_DATE,
        'cpu' => '0001',
        'mac' => '0002',
        'serial_number' => SERIALNUMBER,
    );
    $url = "http://service.tp-shop.cn/index.php?m=Home&c=Index&a=user_push&" . http_build_query($vaules); // 检测版本升级
    stream_context_set_default(array('http' => array('timeout' => 3)));
    file_get_contents($url);
}

/**
 * 导出excel
 * @param $strTable    表格内容
 * @param $filename 文件名
 */
function downloadExcel($strTable, $filename)
{
    header("Content-type: application/vnd.ms-excel");
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=" . $filename . "_" . date('Y-m-d') . ".xls");
    header('Expires:0');
    header('Pragma:public');
    echo '<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . $strTable . '</html>';
}

/**
 * 格式化字节大小
 * @param  number $size 字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '')
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 根据id获取地区名字
 * @param $regionId id
 */
function getRegionName($regionId)
{
    $data = M('region')->where(array('id' => $regionId))->field('name')->find();
    return $data['name'];
}

function respose($res)
{
    header('Content-type:text/json');
    exit(json_encode($res));
}

function getMenuList($act_list)
{
    //根据角色权限过滤菜单
    $menu_list = include APP_PATH . 'seller/conf/menu.php';
    if ($act_list != 'all' && !empty($act_list)) {
        $right = M('system_menu')->where("id", "in", $act_list)->cache(true)->getField('right', true);
        $role_right = '';
        foreach ($right as $val) {
            $role_right .= $val . ',';
        }
        $role_right = explode(',', $role_right);
        foreach ($menu_list as $k => $mrr) {
            foreach ($mrr['child'] as $j => $v) {
                if (!in_array($v['op'] . '@' . $v['act'], $role_right)) {
                    unset($menu_list[$k]['child'][$j]);//过滤菜单
                }
            }
        }
        foreach ($menu_list as $mk => $mr) {
            if (empty($mr['child'])) {
                unset($menu_list[$mk]);
            } else {
				$menu_list[$mk]["child"] = array_values($menu_list[$mk]["child"]);
			}
        }
    }
    return $menu_list;
}

function get_left_menu($menuArr)
{
    $left_menu = $seller_quicklink = array();
    if(CONTROLLER_NAME == 'Index' && ACTION_NAME == 'index'){
    	//首页获取快捷菜单
    	if($_SESSION['seller_quicklink']){
    		$seller_quicklink = array_keys($_SESSION['seller_quicklink']);
    	}else{
    		$seller = session('seller');
    		if(!empty($seller['seller_quicklink'])){
    			$seller_quicklink = explode(',', $seller['seller_quicklink']);
    			if(empty($_SESSION['seller_quicklink'])){
    				foreach ($seller_quicklink as $val){
    					$_SESSION['seller_quicklink'][$val] = $val;
    				}
    			}
    		}
    	}
    	if(!empty($seller_quicklink)){
    		foreach ($menuArr as $kk => $v2) {
    			foreach ($v2['child'] as $val2) {
    				if(in_array($val2['op'].'_'.$val2['act'], $seller_quicklink)){
    					$left_menu[] = array('name'=>$val2['name'],'op'=>$val2['op'],'act'=>$val2['act']);
    				}
    			}
    		}
    	}
    }else{
		$hit = false;
    	foreach ($menuArr as $kk => $v2) {
    		foreach ($v2['child'] as $val2) {
    			if (CONTROLLER_NAME == $val2['op']) {
    				if (ACTION_NAME == $val2['act']) {
    					$menuArr[$kk]['icon'] = $kk;
    					$left_menu = $menuArr[$kk];
						$hit = true;
    					break;
    				}
    			}
    		}
			if ($hit)
				break;
    	}
        if ($hit) {
            session('left_menu',$left_menu);
        } else {
            if (session('?left_menu')) {
                $left_menu = session('left_menu');
            } else {
                $menuArr[$kk]['icon'] = $kk;
                $left_menu = $menuArr[$kk];
                session('left_menu',$left_menu);
            }
        }
    }
    return $left_menu;
}
