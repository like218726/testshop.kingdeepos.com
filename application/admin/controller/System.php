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
 * Date: 2015-10-09
 */

namespace app\admin\controller;

use app\admin\logic\GoodsLogic;
use app\common\logic\ModuleLogic;
use think\Db;
use think\Cache;

class System extends Base{
	
	/*
	 * 配置入口
	 */
	public function setting(){
		return $this->fetch();
	}
	
	public function index()
	{          
		/*配置列表*/
		$group_list = [
            'shop_info' => '商城信息',
            'basic'     => '基本设置',
			'cash'      => '提现设置',
            'shopping'  => '购物流程',
            'sms'       => '短信设置',
            'smtp'      => '邮件设置',
            'water'     => '水印设置',
            'distribut' => '分销设置',
            'push'      => '推送设置',
            'oss'       => '对象存储',
		    'poster'	=> '海报设置',
            'subdomain' => '二级域名'
        ];
		$this->assign('group_list',$group_list);
		$inc_type =  I('get.inc_type','shop_info');
		$this->assign('inc_type',$inc_type);
		if($inc_type == 'poster'){
		    $this->assign('poster', DB::name('poster')->find());
		}
		$this->assign('config',tpCache($inc_type));//当前配置项
		return $this->fetch($inc_type);
	}


    /**
     * 会员中心自定义
     * @return mixed
     */
    public function mp_center_menu()
    {
        $menu_list = db('menu_mp')->where('')->select();
        $this->assign('menu_list', $menu_list);
        return $this->fetch();
    }

    public function mp_center_menu_save(){
        $menu_list = input('menu/a', []);
        $header_background = input('header_background/s');
        if ($header_background) {
            tpCache('basic', ['header_background'=>$header_background]);
            Cache::clear('config');
        }
        foreach($menu_list as $menu){
            db('menu_mp')->where('menu_id', $menu['menu_id'])->update($menu);
        }
        $this->ajaxReturn(['status'=>1,'msg'=>'操作成功']);
    }
	/*
	 * 新增修改配置
	 */
	public function handle(){
		$param = I('post.');
		$site_domain = $param['site_domain'];
		$http_type = get_http_type();
		//如果网站域名没加上http或https则自动加上
		if($site_domain && !(strpos($site_domain, 'http://')===0 || strpos($site_domain, 'https://')===0 ))$param['site_domain'] = $http_type.$site_domain;
		 
		$inc_type = $param['inc_type'];
		//unset($param['__hash__']);
		unset($param['inc_type']);
                unset($param['form_submit']);
		tpCache($inc_type,$param);
                // 设置短信商接口
                if($param['sms_platform'] == 2 &&  !empty($param['sms_appkey'])  && !empty($param['sms_secretKey']))
                {                     
                    $sms_appkey = trim($param['sms_appkey']);
                    $sms_secretKey = trim($param['sms_secretKey']);
                    $url = 'http://open.1cloudsp.com:8090/api/admin/setParentId?parentId=14257&accesskey='.urlencode($sms_appkey).'&secret='.urlencode($sms_secretKey);
                    httpRequest($url);                     
                }     
                
		$this->success("操作成功",U('System/index',array('inc_type'=>$inc_type)));
	} 

	public function seo(){
		$all_type = Db::name('goods_category')->where("level<4")->getField('id,name,parent_id');
		if(!empty($all_type)){
			$GoodsLogic = new GoodsLogic();
			$all_type = $GoodsLogic->getCatTree($all_type);
			$cat_select = $GoodsLogic->exportTree($all_type,0,0);
			$this->assign('cat_select',$cat_select);
		}
		$seo_config = Db::name('seo')->getField('type,title,keywords,description');
		$this->assign('seo_config',$seo_config);
		return $this->fetch();
	}

	public function seo_update(){
		$data = I('post.');
		$seo_arr = $data['SEO'];
		foreach($seo_arr as $key=>$val){
			foreach ($seo_arr[$key] as $k=>$v){
				$updata[$key][$k] = $v;
			}
            $type = empty($type) ? $key : $type;
            $updata[$key]['type'] = $key;
            if(!Db::name('seo')->where('type',$key)->find()){
                Db::name('seo')->save($updata[$key]);
            }else{
                Db::name('seo')->where('type',$key)->update($updata[$key]);
            }

		}
		$this->success("操作成功",U('System/seo',array('type'=>$type)));
	}
       /**
        * 自定义导航
        */
    public function navigationList(){
           $model = M("Navigation");
           $navigationList = $model->order("id desc")->select();            
           $this->assign('navigationList',$navigationList);
           return $this->fetch('navigationList');          
     }
    
     /**
     * 添加修改编辑 前台导航
     */
    public  function addEditNav(){                        
            $model = D("Navigation");            
            if(IS_POST)
            {
                    if (I('id'))
                        $model->update(I('post.'));
                    else
                        $model->add(I('post.'));
                    
                    $this->success("操作成功!!!",U('Admin/System/navigationList'));               
                    exit;
            }                    
           // 点击过来编辑时                 
            $id = I('id',0);    
           $navigation = DB::name('navigation')->where('id',$id)->find();
			//导航位置数组
			$system_position = array(
				1 => '前台顶部',
				2 => '前台底部',
				3 => '商家底部'
			);
           // 系统菜单 顶部
           $GoodsLogic = new GoodsLogic();
           $cat_list = $GoodsLogic->goods_cat_list();
           $select_option = array();              
            if(!empty($cat_list))
            {
                foreach ($cat_list AS $key => $value)
                {
                        $strpad_count = $value['level']*4;
                        $select_val = U("/Home/Goods/goodsList",array('id'=>$key));
                        $select_option[$select_val] = str_pad('',$strpad_count,"-",STR_PAD_LEFT).$value['name'];                                        
                }
            }
           $system_nav = array(
               'http://www.tpshop.cn' => 'tpshop官网',                              
               'http://www.99soubao.com' => '搜豹公司',
               '/index.php?m=Home&c=Activity&a=promoteList' => '促销活动',
               '/index.php?m=Home&c=Activity&a=flash_sale_list' => '限时抢购',
               '/index.php?m=Home&c=Activity&a=group_list' => '团购',       
               '/index.php?m=Home&c=Index&a=street' => '店铺街',
               '/index.php?m=Home&c=Goods&a=integralMall' => '积分商城',
               '/index.php?m=Home&c=Activity&a=pre_sell_list' => '预售',
               '/index.php?m=Home&c=Activity&a=coupon_list' => '领卷中心',
           );
           $system_nav = array_merge($system_nav,$select_option);
           $this->assign('system_nav',$system_nav);

			//地下菜单文章
			$system_bottom = array();
			$article = db('article')->where('is_open',1)->select();
			if(!empty($article)){
				foreach($article as $value){
					$system_bottom['/index.php/Home/Article/detail/article_id/'.$value['article_id']] = $value['title'];
				}
			}

			//分配底部文章
			$this->assign('system_bottom',$system_bottom);

			//分配位置数组
			$this->assign('position',$system_position);
           
           $this->assign('navigation',$navigation);
           return $this->fetch('_navigation');
    }
    
    /**
     * 删除前台 自定义 导航
     */
	public function delNav()
	{
            // 删除导航
            M('Navigation')->where("id",I('id'))->delete();
            $this->success("操作成功!!!",U('Admin/System/navigationList'));
	}

	public function ajax_delNav()
	{
            // 删除导航
            M('Navigation')->where("id",I('id'))->delete();                
	    $this->ajaxReturn(array('status' => 1, 'msg' => '操作成功!!'));		 
	}
	
	public function refreshMenu(){
		$pmenu = $arr = array();
		$rs = M('system_module')->where('level>1 AND visible=1')->order('mod_id ASC')->select();
		foreach($rs as $row){
			if($row['level'] == 2){
				$pmenu[$row['mod_id']] = $row['title'];//父菜单
			}
		}

		foreach ($rs as $val){
			if($row['level']==2){
				$arr[$val['mod_id']] = $val['title'];
			}
			if($row['level']==3){
				$arr[$val['mod_id']] = $pmenu[$val['parent_id']].'/'.$val['title'];
			}
		}
		return $arr;
	}

	/**
	 * 清空系统缓存
	 */
	public function cleanCache(){
        if(file_exists('./index.html')){
            unlink('./index.html');
        }
		clearCache();
		$quick = I('quick',0);
		if($quick == 1){
			$script = "<script>parent.layer.msg('缓存清除成功', {time:3000,icon: 1});window.parent.location.reload(-1);</script>";
		}else{
			$script = "<script>parent.layer.msg('缓存清除成功', {time:3000,icon: 1});window.location='/index.php?m=Admin&c=Index&a=welcome';</script>";
		}
        exit($script);
	}

	/**
	 * 清空静态商品页面缓存
	 */
	public function ClearGoodsHtml(){
		$goods_id = I('goods_id');
		if(unlink("./Application/Runtime/Html/Home_Goods_goodsInfo_{$goods_id}.html"))
		{
			// 删除静态文件
			$html_arr = glob("./Application/Runtime/Html/Home_Goods*.html");
			foreach ($html_arr as $key => $val)
			{
				strstr($val,"Home_Goods_ajax_consult_{$goods_id}") && unlink($val); // 商品咨询缓存
				strstr($val,"Home_Goods_ajaxComment_{$goods_id}") && unlink($val); // 商品评论缓存
			}
			$json_arr = array('status'=>1,'msg'=>'清除成功','result'=>'');
		}
		else
		{
			$json_arr = array('status'=>-1,'msg'=>'未能清除缓存','result'=>'' );
		}
		$json_str = json_encode($json_arr);
		exit($json_str);
	}
	/**
	 * 商品静态页面缓存清理
	 */
	public function ClearGoodsThumb(){
		$goods_id = I('goods_id');
		delFile("./public/upload/goods/thumb/$goods_id"); // 删除缩略图
		Cache::clear('original_img_cache');
		$json_arr = array('status'=>1,'msg'=>'清除成功,请清除对应的静态页面','result'=>'');
		$json_str = json_encode($json_arr);
		exit($json_str);
	}
	/**
	 * 清空 文章静态页面缓存
	 */
	public function ClearAritcleHtml(){
		$article_id = I('article_id');
		unlink("./Application/Runtime/Html/Index_Article_detail_{$article_id}.html"); // 清除文章静态缓存
		unlink("./Application/Runtime/Html/Doc_Index_article_{$article_id}_api.html"); // 清除文章静态缓存
		unlink("./Application/Runtime/Html/Doc_Index_article_{$article_id}_phper.html"); // 清除文章静态缓存
		unlink("./Application/Runtime/Html/Doc_Index_article_{$article_id}_android.html"); // 清除文章静态缓存
		unlink("./Application/Runtime/Html/Doc_Index_article_{$article_id}_ios.html"); // 清除文章静态缓存
		$json_arr = array('status'=>1,'msg'=>'操作完成','result'=>'' );
		$json_str = json_encode($json_arr);
		exit($json_str);
	}
        
      //发送测试邮件
      public function send_email(){
        	$param = I('post.');
        	unset($param['inc_type']);
        	//tpCache($param['inc_type'],$param);  //注释掉，不注释会出现重复写入数据库
        	$res = send_email($param['test_eamil'],'后台测试','测试发送验证码:'.mt_rand(1000,9999));
        	exit(json_encode($res));
      }
	        
    /**
     *  管理员登录后 处理相关操作          
     */
     public function login_task()
     {
        set_time_limit(0);
        $today_time = time();		 
        // 随机清空购物车的垃圾数据		
        $time = time() - 3600; // 删除购物车数据  1小时以前的
        M("Cart")->where("user_id = 0 and  add_time < $time")->delete();
		
		// 删除 cart表垃圾数据 删除一个月以前的 
		$time = time() - 2592000; 
        M("cart")->where("add_time < $time")->delete();		
		// 删除 tp_sms_log表垃圾数据 删除一个月以前的短信
        M("sms_log")->where("add_time < $time")->delete();		
        
        // 发货后满多少天自动收货确认
        $auto_confirm_date = tpCache('shopping.auto_confirm_date');
        $auto_confirm_date = $auto_confirm_date * (60 * 60 * 24); // N天的时间戳
		$time = time() - $auto_confirm_date; // 比如7天以前的可用自动确认收货
        $order_id_arr = Db::name('order')->where("order_status = 1 and shipping_status = 1 and shipping_time < $time")->getField('order_id',true);
        foreach ($order_id_arr as $k => $v) {
            confirm_order($v);
        }        
     }     
     
    function ajax_get_action()
    {
        $control = I('controller');
     	$type = I('type',0);
        if ($type == 6) $type = 1; //供应商的方法和普通商家公用
        $module = (new ModuleLogic)->getModule($type);
        if (!$module) {
            exit('模块不存在或不可见');
        }

        $selectControl = [];
        $className = "app\\".$module['name']."\\controller\\".$control;
        $methods = (new \ReflectionClass($className))->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if ($method->class == $className) {
                if ($method->name != '__construct' && $method->name != '_initialize') {
                    $selectControl[] = $method->name;
                }
            }
        }

     	$html = '';
		foreach ($selectControl as $val){
			$html .= "<li style='width: 200px'><label><input class='checkbox' name='act_list' value=".$val." type='checkbox'>".$val."</label></li>";
		}
     	exit($html);
    }
     
    function right_list()
    {
        $type = I('type',0);
        $moduleLogic = new ModuleLogic;
        if (!$moduleLogic->isModuleExist($type)) {
            $this->error('权限类型不存在');
        }
        $modules = $moduleLogic->getModules();
        $group = $moduleLogic->getPrivilege($type);

        $condition['type'] = $type;
        $name = I('name');
        if(!empty($name)){
            $condition['name|right'] = array('like',"%$name%");
        }
        $right_list = M('system_menu')->where($condition)->order('id desc')->select();
        $this->assign('right_list',$right_list);
        $this->assign('group',$group);
         $this->assign('modules',$modules);
        return $this->fetch();
    }
      
    public function edit_right(){
        $type = I('type',0);  //0:平台权限资源;1:商家权限资源
        $moduleLogic = new ModuleLogic;
        if (!$moduleLogic->isModuleExist($type)) {
            $this->error('模块不存在或不可见');
        }

        if(IS_POST){
            $data = I('post.');
            //去空格
            $data['name'] = trim($data['name']);
            empty($data['right']) &&  $this->ajaxReturn(['status' => -1,'msg' => '请添加权限码']);
            $data['right'] = implode(',',$data['right']);
            if(!empty($data['id'])){
                M('system_menu')->where(array('id'=>$data['id']))->save($data);
            }else{
                if(M('system_menu')->where(array('type'=>$data['type'],'name'=>$data['name']))->count()>0){
                    $this->ajaxReturn(['status' => -1,'msg' => '该权限名称已添加，请检查']);
                }
                unset($data['id']);
                M('system_menu')->add($data);
            }
            $this->ajaxReturn(['status' => 1,'msg' => '操作成功']);
            exit;
        }
        $id = I('id');
        if($id){
            $info = M('system_menu')->where(array('id'=>$id))->find();
            $info['right'] = explode(',', $info['right']);
            $this->assign('info',$info);
        }

        $modules = $moduleLogic->getModules();
        $group = $moduleLogic->getPrivilege($type);
        if ($type == 6) {
            $key = 1;
        } else {
            $key = $type;
        }
        $planPath = APP_PATH.$modules[$key]['name'].'/controller'; 
        $planList = array();
        $dirRes   = opendir($planPath);
        while($dir = readdir($dirRes))
        {
            if(!in_array($dir,array('.','..','.svn')))
            {
                $planList[] = basename($dir,'.php');
            }
        }
        sort($planList);
        $this->assign('modules', $modules);
        $this->assign('planList',$planList);
        $this->assign('group',$group);
        return $this->fetch();
    }
      
     public function right_del(){
     	$id = I('del_id');
     	if(is_array($id)){
     		$id = implode(',', $id);
     	}
     	if(!empty($id)){
     		$r = M('system_menu')->where("id in ($id)")->delete();
     		if($r){
     			respose(1);
     		}else{
     			respose('删除失败');
     		}
     	}else{
     		respose('参数有误');
     	}
     }

	//清除所有活动数据
	public function clearProm()
	{
		Db::name('flash_sale')->where('1=1')->delete();
		Db::name('group_buy')->where('1=1')->delete();
		Db::name('prom_goods')->where('1=1')->delete();
		Db::name('prom_order')->where('1=1')->delete();
		Db::name('coupon')->where('1=1')->delete();
		Db::name('coupon_list')->where('1=1')->delete();
		Db::name('goods_coupon')->where('1=1')->delete();
		Db::name('goods')->where('prom_type', '<>', 0)->whereOr('prom_id', '<>', 0)->update(['prom_type' => 0, 'prom_id' => 0]);
		Db::name('spec_goods_price')->where('prom_type', '<>', 0)->whereOr('prom_id', '<>', 0)->update(['prom_type' => 0, 'prom_id' => 0]);
		Db::name('cart')->where('prom_type', '<>', 0)->whereOr('prom_id', '<>', 0)->update(['prom_type' => 0, 'prom_id' => 0]);
		Db::name('order_goods')->where('prom_type', '<>', 0)->whereOr('prom_id', '<>', 0)->update(['prom_type' => 0, 'prom_id' => 0]);

		$this->success('清除活动数据成功');
	}

	//清楚拼团活动数据
	public function clearTeam(){
		Db::name('team_activity')->where('1=1')->delete();
		Db::name('team_follow')->where('1=1')->delete();
		Db::name('team_found')->where('1=1')->delete();
		Db::name('team_lottery')->where('1=1')->delete();
		Db::name('goods')->where('prom_type',6)->update(['prom_type' => 0, 'prom_id' => 0]);
		Db::name('spec_goods_price')->where('prom_type',6)->update(['prom_type' => 0, 'prom_id' => 0]);
		Db::name('order')->where('prom_type',6)->update(['prom_type' => 0, 'prom_id' => 0]);
		Db::name('order_goods')->where('prom_type',6)->update(['prom_type' => 0, 'prom_id' => 0]);
		$this->success('清除拼团活动数据成功');
	}

	//清除预售活动数据
	public function clearPreSell()
	{
		Db::name('pre_sell')->where('1=1')->delete();
		Db::name('goods')->where('prom_type', 4)->update(['prom_type' => 0, 'prom_id' => 0]);
		Db::name('spec_goods_price')->where('prom_type', 4)->update(['prom_type' => 0, 'prom_id' => 0]);
		Db::name('order')->where('prom_type', 4)->update(['prom_type' => 0, 'prom_id' => 0]);
		Db::name('order_goods')->where('prom_type', 4)->update(['prom_type' => 0, 'prom_id' => 0]);
	}
       
	//添加自定义海报模板
	public function poster_add(){
	    //halt($_POST);
	    $data = I('post.');
	    if($data['enabled'] == 1 && ($id = Db::name('poster')->where(['enabled'=>1])->value('id'))){
	        Db::name('poster')->where(['id'=>$id])->setField('enabled',0);
	    }
	    if ($data['id'] >0){
	        unset($data['id']);
	        $data['update_time'] = time();
	        Db::name('poster')->where(['id'=>I('id')])->save($data);
	        $this->ajaxReturn(['status'=>1,'msg'=>'更新成功','url'=>U('Admin/System/posterList')]);
	    }else{
	        $data['add_time'] = time();
	        Db::name('poster')->add($data);
	        $this->ajaxReturn(['status'=>1,'msg'=>'添加成功','url'=>U('Admin/System/posterList')]);
	    }
	}
	
    /**
     * 清空演示数据 用完切记删除
     * http://www.xxx.com/Admin/system/truncate_demo_data
     */
    public function truncate_demo_data(){
        /*
        $result = Db::query('show tables');        
        $prefix   = \think\config::get('database.prefix');
        $database = \think\config::get('database.database');
        $tables = array();        
        foreach($result as $key => $val){
                $tables[] = array_shift($val);
        }	 			    
         
        $bl_table = array('tp_admin','tp_config','tp_region','tp_system_module','tp_admin_role','tp_system_menu','tp_article_cat','tp_article','tp_system_article','tp_wx_user');
        foreach($bl_table as $k => $v)
        {
                $bl_table[$k] = str_replace('tp_',$prefix,$v); 
        }			      
        
        foreach($tables as $key => $val)
        {					
                if(!in_array($val, $bl_table))
                {
                     Db::execute("truncate table ".$val); 
                }		
        }   	
        delFile('../public/upload/goods'); // 清空测试图片			
               
        header("Content-type: text/html; charset=utf-8");  
        echo "数据已清空,请立即删除这个方法";
        */ 
         
    }         
        
}