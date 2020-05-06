<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 商业用途务必到官方购买正版授权, 使用盗版将严厉追究您的法律责任。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * $Author: IT宇宙人 2015-08-10 $
 */
namespace app\mobile\controller;
use app\common\logic\CartLogic;
use app\common\logic\UsersLogic;
use app\common\logic\wechat\WechatUtil;
use think\Controller;
use think\Db;
use think\Session;

class MobileBase extends Controller {
    public $session_id;
    public $weixin_config;
    public $cateTrre = array();
    public $tpshop_config = array();

    public function __construct()
    {
        parent::__construct();
    }
    /*
     * 初始化操作
     */
    public function _initialize() {
        //设置缓存
        cache_result_json(null,1);
//        Session::start();
        session('user');
        header("Cache-control: private");  // history.back返回后输入框值丢失问题 参考文章 http://www.tp-shop.cn/article_id_1465.html  http://blog.csdn.net/qinchaoguang123456/article/details/29852881
        $this->session_id = session_id(); // 当前的 session_id       
        define('SESSION_ID',$this->session_id); //将当前的session_id保存为常量，供其它方法调用
        // 判断当前用户是否手机                
        if(isMobile())
            cookie('is_mobile','1',3600); 
        else 
            cookie('is_mobile','0',3600);
        $first_leader = I('first_leader/d');
        if($first_leader){
            cookie('first_leader',$first_leader,3600*24);
        }
        if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            $this->weixin_config = M('wx_user')->cache(true)->find(); //取微获信配置
            $this->assign('wechat_config', $this->weixin_config);            
            $user_temp = session('user');
            if (isset($user_temp['user_id']) && $user_temp['user_id']) {
                $user = M('users')->where("user_id", $user_temp['user_id'])->find();
                if (!$user) {
                    $_SESSION['openid'] = 0;
                    session('user', null);
                }else{
                    session('user', $user); // 实时更新user,万一突然变成分销商就能生效了
                }
            }

            if (empty($_SESSION['openid'])){
                if(is_array($this->weixin_config) && $this->weixin_config['wait_access'] == 1){
                    $wxuser = $this->GetOpenid(); //授权获取openid以及微信用户信息
		    
		    //过滤特殊字符串
                    $wxuser['nickname'] && $wxuser['nickname'] = replaceSpecialStr($wxuser['nickname']);
		    
                    session('subscribe', $wxuser['subscribe']);
                    setcookie('subscribe',$wxuser['subscribe']);// 当前这个用户是否关注了微信公众号
                    $logic = new UsersLogic(); 
                    $is_bind_account = tpCache('basic.is_bind_account');
                     if ($is_bind_account) {
                         if (CONTROLLER_NAME != 'User' || ACTION_NAME != 'bind_guide') {
                            $data = $logic->thirdLogin_new($wxuser);//微信自动登录
                            if ($data['status'] != 1 && $data['result'] === '100') {
                                 session("third_oauth" , $wxuser);
                                 $first_leader = I('first_leader');
                                 $this->redirect(U('Mobile/User/bind_guide',['first_leader'=>$first_leader]));
                            }
                         }
                    } else { 
                        $data = $logic->thirdLogin($wxuser);
                    }
                    if($data['status'] == 1){
                        session('user',$data['result']);
                        setcookie('token',$data['result']['token'],null,'/');
                        setcookie('user_id',$data['result']['user_id'],null,'/');
                        setcookie('is_distribut',$data['result']['is_distribut'],null,'/');
                        setcookie('uname',$data['result']['nickname'],null,'/');
                        // 登录后将购物车的商品的 user_id 改为当前登录的id
                        M('cart')->where("session_id" ,$this->session_id)->save(array('user_id'=>$data['result']['user_id']));
                        $cartLogic = new CartLogic();
                        $cartLogic->setUserId($data['result']['user_id']);
                        $cartLogic->doUserLoginHandle();  //用户登录后 需要对购物车 一些操作
                    }
                }
            }else{
                setcookie('token',$user_temp['token'],null,'/');
                setcookie('user_id',$user_temp['user_id'],null,'/');
                setcookie('is_distribut',$user_temp['is_distribut'],null,'/');
            }
        }
        
        $this->public_assign();
        $this->redirect_store();
    }
    
    public  function redirect_store(){
        $subdomain = tpCache('subdomain');
        //启用店铺二级域名
        if(!$subdomain['enabled_subdomain']) return;

        $secondary_prefix = get_secondary_prefix();
        //如果是主域名则直接访问
        if(empty($secondary_prefix) || $secondary_prefix === 'MASTER')return ;
        
        //如果是店铺二级域名首页则需要重定向
        if (stristr($_SERVER['REQUEST_URI'],'mobile')){
            $store_id = M('Store')->where(['store_domain'=>$secondary_prefix , 'domain_enable'=>1])->cache(true)->getField('store_id');
            !empty($store_id) && $redirectUrl = SITE_URL . U('Mobile/Store/index',['store_id'=>$store_id]);
        }else if(strtolower(CONTROLLER_NAME) != 'store'){
            //如果二级域名访问控制器不是stroe则需要重定向
            $redirectUrl = $subdomain['site_domain'].$_SERVER['REDIRECT_URL'];
        }
         
        if($redirectUrl){
            if($redirectUrl=='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] || $redirectUrl=='https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) return ;
            @header("Location:$redirectUrl");
            exit();
        }
    }
    
    /**
     * 保存公告变量到 smarty中 比如 导航 
     */   
    public function public_assign()
    {
        $first_login = session('first_login');
        $this->assign('first_login', $first_login);
        if (!$first_login && ACTION_NAME == 'login') {
            session('first_login', 1);
        }
            
       $tpshop_config = array();
       $tp_config = M('config')->cache(true,TPSHOP_CACHE_TIME)->select();       
       foreach($tp_config as $k => $v)
       {
       	  if($v['name'] == 'hot_keywords'){
       	  	 $tpshop_config['hot_keywords'] = explode('|', $v['value']);
       	  }       	  
          $tpshop_config[$v['inc_type'].'_'.$v['name']] = $v['value'];
       }                        
       
       //重新处理site_domain
       empty($tpshop_config['shop_info_site_domain']) && $tpshop_config['shop_info_site_domain'] = U('Index/index');
        
       $goods_category_tree = get_goods_category_tree();    
       $this->cateTrre = $goods_category_tree;
       $this->assign('goods_category_tree', $goods_category_tree);                     
       $brand_list = M('brand')->cache(true,TPSHOP_CACHE_TIME)->field('id,cat_id1,logo,is_hot')->where("cat_id1>0")->select();
       $this->tpshop_config=$tpshop_config;
       $this->assign('brand_list', $brand_list);
       $this->assign('tpshop_config', $tpshop_config);
       /** 修复首次进入微商城不显示用户昵称问题 **/
       $user_id = cookie('user_id');
       $uname = cookie('uname');
       if(empty($user_id) && ($users = session('user')) ){
           $user_id = $users['user_id'];
           $uname = $users['nickname'];
       }
       $this->assign('user_id',$user_id);
       $this->assign('uname',$uname);
        $this->get_seo();
    }

    private function get_seo(){
        $seo_config = Db::name('seo')->cache(true,TPSHOP_CACHE_TIME)->getField('type,title,keywords,description');

        $name = ACTION_NAME;

        if($name == 'flash_sale_list'){ //秒杀
            $name = 'flash';
        }elseif(CONTROLLER_NAME=='Team' && $name=='index'){ //拼团
            $name='group';
        }elseif($name=='info'){ //商品详情
            $name='goodsInfo';
        }elseif(CONTROLLER_NAME=='Store'){ //店铺
            $name='shop';
        }elseif(CONTROLLER_NAME=='Article'){ // 文章
            $name='article';
        }/*elseif($name=='categoryList'){ // 商品列表
            $name='goodsList';
        }*/
        if(isset($seo_config[$name])){
            $store_name = tpCache('shop_info.store_name');
            foreach ($seo_config[$name] as $key=>$value) {
                $this->seo[$key] = str_replace(array('{sitename}'),array($store_name),$value);
            }
            $this->seo['title'] = preg_replace("/{.*}/siU",'',$this->seo['title']);
            $this->seo['keywords'] = preg_replace("/{.*}/siU",'',$this->seo['keywords']);
            $this->seo['description'] = preg_replace("/{.*}/siU",'',$this->seo['description']);
        }else{
            $this->seo['title'] =  tpCache('shop_info.store_title');
            $this->seo['keywords'] =  tpCache('shop_info.store_keyword');
            $this->seo['description'] = tpCache('shop_info.store_desc');
        }

        $this->assign('seo',$this->seo);
    }

    // 网页授权登录获取 OpendId
    public function GetOpenid()
    {
        if($_SESSION['openid'])
            return $_SESSION['data'];
        //通过code获得openid
        if (!isset($_GET['code'])){
            //触发微信返回code码
            //$baseUrl = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
            $baseUrl = urlencode($this->get_url());
            $url = $this->__CreateOauthUrlForCode($baseUrl); // 获取 code地址
            Header("Location: $url"); // 跳转到微信授权页面 需要用户确认登录的页面
            exit();
        } else {
            //上面获取到code后这里跳转回来
            $code = $_GET['code'];
            $data = $this->getOpenidFromMp($code);//获取网页授权access_token和用户openid
            $data2 = $this->GetUserInfo($data['access_token'],$data['openid']);//获取微信用户信息
            $data['nickname'] = empty($data2['nickname']) ? '微信用户' : trim($data2['nickname']);
            $data['sex'] = $data2['sex'];
            $data['head_pic'] = $data2['headimgurl']; 
            $data['subscribe'] = $data2['subscribe'];      
            $data['oauth_child'] = 'mp';
            $_SESSION['openid'] = $data['openid'];
            $data['oauth'] = 'weixin';
            $user_id = Db::name('oauth_users')->where('openid',$data['openid'])->value('user_id');
            if($user_id){
                Db::name('users')->where('user_id',$user_id)->update(['head_pic'=>$data['head_pic']]);
            }
            if(isset($data2['unionid'])){
            	$data['unionid'] = $data2['unionid'];
            }
            $_SESSION['data'] = $data;
            return $data;
        }
    }

    /**
     * 获取当前的url 地址
     * @return type
     */
    private function get_url() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }    
    
    /**
     *
     * 通过code从工作平台获取openid机器access_token
     * @param string $code 微信跳转回来带上的code
     *
     * @return openid
     */
    public function GetOpenidFromMp($code)
    {
        //通过code获取网页授权access_token 和 openid 。网页授权access_token是一次性的，而基础支持的access_token的是有时间限制的：7200s。
    	//1、微信网页授权是通过OAuth2.0机制实现的，在用户授权给公众号后，公众号可以获取到一个网页授权特有的接口调用凭证（网页授权access_token），通过网页授权access_token可以进行授权后接口调用，如获取用户基本信息；
    	//2、其他微信接口，需要通过基础支持中的“获取access_token”接口来获取到的普通access_token调用。
        $url = $this->__CreateOauthUrlForOpenid($code);       
        $ch = curl_init();//初始化curl        
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);//设置超时
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);         
        $res = curl_exec($ch);//运行curl，结果以jason形式返回            
        $data = json_decode($res,true);         
        curl_close($ch);
        return $data;
    }
    
    
        /**
     *
     * 通过access_token openid 从工作平台获取UserInfo      
     * @return openid
     */
    public function GetUserInfo($access_token,$openid)
    {         
        // 获取用户 信息
        $url = $this->__CreateOauthUrlForUserinfo($access_token,$openid);
        $ch = curl_init();//初始化curl        
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);//设置超时
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);         
        $res = curl_exec($ch);//运行curl，结果以jason形式返回            
        $data = json_decode($res,true);            
        curl_close($ch);
        //获取用户是否关注了微信公众号， 再来判断是否提示用户 关注
        //if(!isset($data['unionid'])){
            $wechat = new WechatUtil($this->weixin_config);
        	$fan = $wechat->getFanInfo($openid);//获取基础支持的access_token
            if ($fan !== false) {
                $data['subscribe'] = $fan['subscribe'];
            }
        //}                
        return $data;
    }

    /**
     *
     * 构造获取code的url连接
     * @param string $redirectUrl 微信服务器回跳的url，需要url编码
     *
     * @return 返回构造好的url
     */
    private function __CreateOauthUrlForCode($redirectUrl)
    {
        $urlObj["appid"] = $this->weixin_config['appid'];
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
//        $urlObj["scope"] = "snsapi_base";
        $urlObj["scope"] = "snsapi_userinfo";
        $urlObj["state"] = "STATE"."#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
    }

    /**
     *
     * 构造获取open和access_toke的url地址
     * @param string $code，微信跳转带回的code
     *
     * @return 请求的url
     */
    private function __CreateOauthUrlForOpenid($code)
    {
        $urlObj["appid"] = $this->weixin_config['appid'];
        $urlObj["secret"] = $this->weixin_config['appsecret'];
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
    }

    /**
     *
     * 构造获取拉取用户信息(需scope为 snsapi_userinfo)的url地址     
     * @return 请求的url
     */
    private function __CreateOauthUrlForUserinfo($access_token,$openid)
    {
        $urlObj["access_token"] = $access_token;
        $urlObj["openid"] = $openid;
        $urlObj["lang"] = 'zh_CN';        
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/userinfo?".$bizString;                    
    }    
    
    /**
     *
     * 拼接签名字符串
     * @param array $urlObj
     *
     * @return 返回已经拼接好的字符串
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign"){
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }
    
    public function ajaxReturn($data){
        ob_end_clean();
        //设置缓存
        cache_result_json(json_encode($data));
        exit(json_encode($data));
    }
    

}