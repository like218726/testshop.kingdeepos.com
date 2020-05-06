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
 * 微信交互类
 */
namespace app\mobile\controller;

use app\common\logic\UsersLogic;
use app\common\logic\CartLogic;


class LoginApi extends MobileBase{
    public $config;
    public $oauth;
    public $class_obj;

    public function __construct(){
        parent::__construct();
        $this->oauth = I('get.oauth');
        //获取配置
        $data = M('Plugin')->where("code",$this->oauth)->where('type','login')->find();
        $this->config = unserialize($data['config_value']); // 配置反序列化
        if(!$this->oauth)
            $this->error('非法操作',U('Mobile/User/login'));
        include_once  "plugins/login/{$this->oauth}/{$this->oauth}.class.php";
        $class = '\\'.$this->oauth; //
        $login = new $class($this->config); //实例化对应的登陆插件
        $this->class_obj = $login;
    }

    public function login(){
        if(!$this->oauth)
            $this->error('非法操作',U('Mobile/User/login'));
        include_once  "plugins/login/{$this->oauth}/{$this->oauth}.class.php";
        $this->class_obj->login();
    }

    public function callback(){
        
        $data = $this->class_obj->respon();
        $redata = $data;
        $logic = new UsersLogic();
        
        //手机端登录, 标识该openid来微信自公众号
        if($data['oauth'] == 'weixin')$data['oauth_child'] = 'mp';
         
        $is_bind_account = tpCache('basic.is_bind_account');
        
        //过滤昵称中的特殊字符
        $data['nickname'] && $data['nickname'] = replaceSpecialStr($data['nickname']);
        
        if($is_bind_account){
            
            if($data['unionid']){
                $thirdUser = M('OauthUsers')->where(['unionid'=>$data['unionid'], 'oauth'=>$data['oauth']])->find();
            }else{
                $thirdUser = M('OauthUsers')->where(['openid'=>$data['openid'], 'oauth'=>$data['oauth']])->find();
            }
            
            //1. 第二种方式:第三方账号首次登录必须绑定账号
            if(empty($thirdUser)){
                //用户未关联账号, 跳到关联账号页
                session('third_oauth',$data);
                $this->redirect(U('Mobile/User/bind_guide'));
            }else{
                //微信自动登录
                $data = $logic->thirdLogin_new($data);
            }
        }else{
            //2.第一种方式:第三方账号首次直接创建账号, 不需要额外绑定账号
            $data = $logic->thirdLogin($data);
        }
        
        if($data['status'] == 1){
            session('user',$data['result']);
            setcookie('user_id',$data['result']['user_id'],null,'/');
            setcookie('is_distribut',$data['result']['is_distribut'],null,'/');
            setcookie('uname',$data['result']['nickname'],null,'/');
            // 登录后将购物车的商品的 user_id 改为当前登录的id
            M('cart')->where("session_id" ,$this->session_id)->save(array('user_id'=>$data['result']['user_id']));
            $cartLogic = new CartLogic();
            $cartLogic->doUserLoginHandle();  //用户登录后 需要对购物车 一些操作
            $this->success('登陆成功',U('User/index'));
        }else{
            if ($redata['openid'] && ($redata['oauth'] == 'alipay' or $redata['oauth'] == 'alipaynew')) {
                //支付宝浏览商城，重复登录
                $this->success('登陆成功',U('User/index'));
            }else{
                $this->success('登陆失败: '.$data['msg']);
            }
        }
    }
}