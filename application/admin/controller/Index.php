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
namespace app\admin\controller; 

use think\Db;
use app\admin\logic\CensusLogic;

class Index extends Base {

    public function index(){
        $this->pushVersion();
        $act_list = session('act_list');
        $menu_list = getMenuList($act_list);
        $this->assign('menu_list',$menu_list);
        $admin_info = getAdminInfo(session('admin_id'));
		$order_amount = M('order')->where("order_status=0 and (pay_status=1 or pay_code='cod')")->count();
		$message = array();
		$CensusLogic = new CensusLogic;
		$message['goods_count'] 			=		$CensusLogic->getWaitGoodsCount();//待审核 商品数量
		$message['refund_order_count'] 		= 		$CensusLogic->getWaitRefundOrderCount();//待处理 退款订单数
		$message['refund_count'] 			= 		$CensusLogic->getWaitRefundCount();//待处理 售后退货订单数
		$message['store_count'] 			= 		$CensusLogic->getWaitStoreCount();//待审核 开店申请数
		$message['store_reopen_count'] 		= 		$CensusLogic->getWaitStoreReopenCount();//待审核 签约申请数
		$message['class_count'] 			= 		$CensusLogic->getWaitClassCount();//待审核 经营类目数
		$message['store_withdrawls_count'] 	= 		$CensusLogic->getWaitStoreWithdrawalsCount();//待审核 商家提现申请数
		$message['withdrawls_count'] 		= 		$CensusLogic->getWaitWithdrawalsCount();//待审核 会员提现申请数
		$message['complain_count'] 			= 		$CensusLogic->getWaitComplainCount();//待处理 投诉数
		$message['expose_count'] 			= 		$CensusLogic->getWaitExposeCount();//待处理 举报数
		$message['flash_count'] 			= 		$CensusLogic->getWaitFlashCount();//待审核 抢购活动数
		$message['team_count'] 				= 		$CensusLogic->getWaitTeamCount();//待审核 拼团活动数
		$message['pre_sell_count'] 			= 		$CensusLogic->getWaitPreSellCount();//待审核 预售活动数
		$message['total_count'] 			= 		array_sum($message);//总消息数量

		$this->assign('order_amount',$order_amount);
		$this->assign('admin_info',$admin_info);
		$this->assign('menu',getMenuArr());
		$this->assign('message',$message);
        return $this->fetch();
    }
   
    public function welcome(){
    	$this->assign('sys_info',$this->get_sys_info());
//    	$today = strtotime("-1 day");
    	$today = strtotime(date('y-m-d'));
    	$count['handle_order'] = M('order')->where("order_status=0 and (pay_status=1 or pay_code='cod')")->count();//待处理订单
    	$count['new_order'] = M('order')->where("add_time>$today")->count();//今天新增订单
    	$count['goods'] =  M('goods')->where("1=1")->count();//商品总数
    	$count['article'] =  M('article')->where("1=1")->count();//文章总数
    	$count['users'] = M('users')->where("1=1")->count();//会员总数
    	$count['today_login'] = M('users')->where("last_login>$today")->count();//今日访问
    	$count['new_users'] = M('users')->where("reg_time>$today")->count();//新增会员
    	$count['yesterday_users'] = M('users')->where("reg_time",'between', [strtotime(date('y-m-d', strtotime("-1 day"))), $today])->count();//昨天新增会员
    	$count['month_users'] = M('users')->where("reg_time",'between', [mktime(0,0,0,date('m'),1,date('Y')), mktime(23,59,59,date('m'),date('t'),date('Y'))])->count();//本月新增会员
    	$count['comment'] = M('comment')->where("is_show=0")->count();//最新评论
    	$count['store'] = M('store_apply')->where("apply_state=0 AND add_time > 0")->count();//店铺审核
    	$count['bind_class'] = M('store_bind_class')->where("state=0")->count();//申请经营类目
    	$count['brand'] = M('brand')->where("status=1 and store_id>0")->count();//申请品牌
		$orderObject = $this->get_order_statistic(1);
    	$this->assign('orderObject',$orderObject);
    	$this->assign('count',$count);
        return $this->fetch();
    }
    
    public function get_sys_info(){
		$sys_info['os']             = PHP_OS;
		$sys_info['zlib']           = function_exists('gzclose') ? 'YES' : 'NO';//zlib
		$sys_info['safe_mode']      = (boolean) ini_get('safe_mode') ? 'YES' : 'NO';//safe_mode = Off		
		$sys_info['timezone']       = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
		$sys_info['curl']			= function_exists('curl_init') ? 'YES' : 'NO';	
		$sys_info['web_server']     = $_SERVER['SERVER_SOFTWARE'];
		$sys_info['phpv']           = phpversion();
		$sys_info['ip'] 			= GetHostByName($_SERVER['SERVER_NAME']);
		$sys_info['fileupload']     = @ini_get('file_uploads') ? ini_get('upload_max_filesize') :'unknown';
		$sys_info['max_ex_time'] 	= @ini_get("max_execution_time").'s'; //脚本最大执行时间
		$sys_info['set_time_limit'] = function_exists("set_time_limit") ? true : false;
		$sys_info['domain'] 		= $_SERVER['HTTP_HOST'];
		$sys_info['memory_limit']   = ini_get('memory_limit');		
        $sys_info['sys_version']   	    = file_get_contents(APP_PATH.'admin/conf/version.php');
		$mysqlinfo = Db::query("SELECT VERSION() as version");
		$sys_info['mysql_version']  = $mysqlinfo[0]['version'];
		if(function_exists("gd_info")){
			$gd = gd_info();
			$sys_info['gdinfo'] 	= $gd['GD Version'];
		}else {
			$sys_info['gdinfo'] 	= "未知";
		}
		return $sys_info;
    }
    
    
    public function pushVersion()
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
                'domain'=>$_SERVER['SERVER_NAME'], 
                'last_domain'=>$_SERVER['SERVER_NAME'], 
                'key_num'=>$curent_version, 
                'install_time'=>INSTALL_DATE,
                'serial_number'=>SERIALNUMBER,
         );     
         $url = "http://service.tp-shop.cn/index.php?m=Home&c=Index&a=user_push&".http_build_query($vaules);
         stream_context_set_default(array('http' => array('timeout' => 3)));
         file_get_contents($url);         
    }
    
    /**
     * ajax 修改指定表数据字段  一般修改状态 比如 是否推荐 是否开启 等 图标切换的
     * table,id_name,id_value,field,value
     */
    public function changeTableVal(){  
            $table = I('table'); // 表名
            $id_name = I('id_name'); // 表主键id名
            $id_value = I('id_value'); // 表主键id值
            $field  = I('field'); // 修改哪个字段
            $value  = I('value'); // 修改字段值
            M($table)->where([$id_name => $id_value])->save(array($field=>$value)); // 根据条件保存修改的数据
    }	

    public function get_category(){
    	$parent_id = I('get.parent_id',0); // 商品分类 父id
    	empty($parent_id) && exit('');
    	$list = M('goods_category')->where(array('parent_id'=>$parent_id))->select();
        $html='';
    	foreach($list as $k => $v)
    	{
    		$html .= "<option value='{$v['id']}' rel='{$v['commission']}'>{$v['name']}</option>";
    	}
    	exit($html);
    }
	
    public function about(){
    	return $this->fetch();
    }
    
    public function explain(){
    	return $this->fetch();
    }

    public function close_teach(){
        Db::name('admin')->where(['admin_id'=>session('admin_id')])->save(['open_teach'=>0]);
    }

	/**
	 * 拉取订单数、销售额统计数据
	 */
	public function get_order_statistic()
	{
		$time_type = input('time_type', 1);
		$CensusLogic = new CensusLogic;
		$orderObject = $CensusLogic->getOrderStatistic($time_type);
		return $orderObject;
	}
}