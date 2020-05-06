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
 * Date: 2016-05-09
 */
namespace app\shop\controller;

use app\common\model\Shopper;
use think\Loader;
use think\Page;
use think\Verify;
use think\Db;
use think\Session;

class Admin extends Base
{

    public function index()
    {
        $shopper_name = input('shopper_name');
        $shopper_where = ['shop_id' => SHOP_ID,'is_admin'=>0];
        if($shopper_name){
            $shopper_where['shopper_name'] =  ['like','%' . $shopper_name . '%'];
        }
        $Shopper = new Shopper();
        $shopper_count = $Shopper->where($shopper_where)->count();
        $page = new Page($shopper_count, 10);
        $shopper_list = $Shopper->with('users')->where($shopper_where)->limit($page->firstRow . ',' . $page->listRows)->select();
        $this->assign('shopper_list', $shopper_list);
        $this->assign('page', $page);
        return $this->fetch();
    }

    /**
     * 修改管理员密码
     * @return \think\mixed
     */
    public function modify_pwd()
    {
        $shop_id = session('shop_id');
        if ($shop_id > 0) {
            $info = D('shop')->where(array('shop_id' => $shop_id))->find();
            if ($info) {
                $user = M('users')->where("user_id", $info['user_id'])->find();
            } else {
                $this->error('找不到该管理员', U('shop/index/index'));
            }
            $info['user_name'] = empty($user['mobile']) ? $user['email'] : $user['mobile'];
            $this->assign('info', $info);
        } 
        $data = I('post.');
        if(IS_POST){
            if ($data['shop_id'] > 0) {
                $password_len = strlen(input('password2'));
                if($password_len < 6 || $password_len > 18){
                    $this->ajaxReturn(['status' =>-1,'msg'=>"密码长度必须在6到18之间",]);
                }
                if ($data['shop_id'] == $shop_id) {
                        if (M('users')->where(array('user_id' => $info['user_id'], 'password' => encrypt($data['password'])))->count() > 0) {
                            M('users')->where(array('user_id' => $info['user_id']))->save(array('password' => encrypt($data['password2'])));
                            $this->ajaxReturn(['status' =>1, 'msg'=>"修改成功", 'url'=>U('shop/index/index')]);
                        } else {
                            $this->ajaxReturn(['status' =>-1,'msg'=>"原密码错误",]);
                        }
                } else {
                    $this->ajaxReturn(['status' =>-1,'msg'=>"非法操作,只能修改自己的密码",]);
                }
            }
        }
         
        return $this->fetch();
    }
    
    public function info()
    {
        $shopper_id = input('shopper_id');
        if ($shopper_id > 0) {
            $Shopper = new Shopper();
            $shopper = $Shopper->where(['shopper_id'=>$shopper_id,'shop_id'=>SHOP_ID,'is_admin'=>'0'])->find();
            if(empty($shopper)){
                $this->error('找不到该门店职员', U('Shop/admin/index'));
            }
            $this->assign('shopper', $shopper);
        }
        return $this->fetch();
    }

    public function add()
    {
        $data = input('post.');
        $shopperValidate = Loader::validate('Shopper');
        if (!$shopperValidate->batch()->check($data)) {
            $this->ajaxReturn(['status' => 0, 'msg' => '添加失败', 'result' => $shopperValidate->getError()]);
        }
        $isEmail = check_email($data['user_name']);
        if ($isEmail) {
            $user_where['email'] = $data['user_name'];
        } else {
            $user_where['mobile'] = $data['user_name'];
        }
        //查找验证绑定用户
        $user = Db::name('users')->field('password,user_id')->where($user_where)->find();
        $data = [
            'shopper_name' => $data['shopper_name'],
            'user_id' => $user['user_id'],
            'shop_id' => SHOP_ID,
            'store_id' => $this->shopInfo['store_id'],
            'add_time' => time(),
        ];
        $r = Db::name('shopper')->add($data);
        if ($r !== false) {
            $this->ajaxReturn(['status' => 1, 'msg' => "添加成功", 'url' => U('Admin/index')]);
        } else {
            $this->ajaxReturn(['status' => -1, 'msg' => "添加失败",]);
        }
    }

    public function save()
    {
        $data = input('post.');
        if (empty($data['shopper_id'])) {
            $this->ajaxReturn(['status' => 0, 'msg' => '参数有误', 'result' => '']);
        }
        $Shopper = new Shopper();
        $editShopper = $Shopper->where(['shopper_id' => $data['shopper_id'], 'shop_id' => SHOP_ID])->find();
        if (empty($editShopper)) {
            $this->ajaxReturn(['status' => 0, 'msg' => "非法操作", 'result' => '']);
        }
        $shopper = session('shopper');
        //检查是否为管理员
        if ($shopper['is_admin'] == 0) {
            $this->ajaxReturn(['status' => 0, 'msg' => "只有门店管理员才能修改编辑门店职员", 'result' => '']);
        }
        $shopperValidate = Loader::validate('Shopper');
        if (!$shopperValidate->batch()->check($data)) {
            $this->ajaxReturn(['status' => 0, 'msg' => '添加失败', 'result' => $shopperValidate->getError()]);
        }
        $isEmail = check_email($data['user_name']);
        if ($isEmail) {
            $user_where['email'] = $data['user_name'];
        } else {
            $user_where['mobile'] = $data['user_name'];
        }
        //查找验证绑定用户
        $user = Db::name('users')->field('password,user_id')->where($user_where)->find();
        $editShopper->shopper_name = $data['shopper_name'];
        $editShopper->user_id = $user['user_id'];
        $r = $editShopper->save();
        if ($r !== false) {
            $this->ajaxReturn(['status' => 1, 'msg' => "修改成功", 'result' => '']);
        } else {
            $this->ajaxReturn(['status' => 0, 'msg' => "修改失败", 'result' => '']);
        }
    }

    public function delete(){
        $shopper_id = input('shopper_id');
        if(empty($shopper_id)){
            $this->ajaxReturn(['status' => 0, 'msg' => "参数错误", 'result' => '']);
        }
        $Shopper = new Shopper();
        $shopper = session('shopper');
        //检查是否为管理员
        if ($shopper['is_admin'] == 0) {
            $this->ajaxReturn(['status' => 0, 'msg' => "只有门店管理员才能删除门店职员", 'result' => '']);
        }
        $deleteShopper = $Shopper->where(['shopper_id'=>$shopper_id,'shop_id'=>SHOP_ID])->find();
        if(empty($deleteShopper)){
            $this->ajaxReturn(['status' => 0, 'msg' => '非法操作', 'result' => '']);
        }
        $deleted = $deleteShopper->delete();
        if($deleted !== false){
            $this->ajaxReturn(['status' => 1, 'msg' => '删除成功', 'result' => '']);
        }else{
            $this->ajaxReturn(['status' => 0, 'msg' => '删除失败', 'result' => '']);
        }
    }

    /*
     * 管理员登陆
     */
    public function login()
    {
        if (session('?shopper_id') && session('shopper_id') > 0) {
            $this->error("您已登录", U('Index/index'));
        }
        if (IS_POST) {
//            $verify = new Verify();
//            if (!$verify->check(I('post.vertify'), "shopper_login")) {
//                exit(json_encode(array('status' => 0, 'msg' => '验证码错误')));
//            }
            $shopper_name = I('post.username');
            $password = I('post.password');
            if (!empty($shopper_name) && !empty($password)) {
                $Shopper = new Shopper();
                $shopper = $Shopper->where(['shopper_name' => $shopper_name])->find();
                if ($shopper) {
                	$shop = Db::name('shop')->where(array('shop_id'=>$shopper['shop_id'],'shop_status'=>1))->find();
                	if(!$shop) exit(json_encode(array('status' => 0, 'msg' => '门店已关闭，请联系店铺客服')));
                    $user_where = array(
                        'user_id' => $shop['user_id'],
                        'password' => encrypt($password)
                    );
                    $user = Db::name('users')->where($user_where)->find();
                    if ($user) {
//                        if ($shopper['is_admin'] == 0 && $shopper['enabled'] == 1) {
//                            exit(json_encode(array('status' => 0, 'msg' => '该账户还没启用激活')));
//                        }
                        session('shopper', $shopper->toArray());
                        session('shopper_id', $shopper['shopper_id']);
                        session('shop_id', $shop['shop_id']);
                        $shopper->last_login_time = time();
                        $shopper->save();
                        shopperLog('门店管理中心登录');
                        $url = session('from_url') ? session('from_url') : U('Index/index');
                        exit(json_encode(array('status' => 1, 'url' => $url)));
                    } else {
                        exit(json_encode(array('status' => 0, 'msg' => '账号密码不正确')));
                    }
                } else {
                    exit(json_encode(array('status' => 0, 'msg' => '账号不存在')));
                }
            } else {
                exit(json_encode(array('status' => 0, 'msg' => '请填写账号密码')));
            }
        }
        return $this->fetch();
    }

    /**
     * 退出登陆
     */
    public function logout()
    {
        session_unset();
        session_destroy();
        $this->success("退出成功", U('Shop/Admin/login'));
    }

    /**
     * 验证码获取
     */
    public function vertify()
    {
        $config = array(
            'fontSize' => 30,
            'length' => 4,
            'useCurve' => true,
            'useNoise' => false,
            'reset' => false
        );
        $Verify = new Verify($config);
        $Verify->entry("shopper_login");
		exit();
    }

    public function log()
    {
        $Log = M('seller_log');
        $p = I('p', 1);
        $seller_id = session('seller_id');
        $logs = Db::name('seller_log')->alias('sl')
            ->join('__SELLER__ s', 's.seller_id = sl.log_seller_id')
            ->where('s.seller_id', $seller_id)->order('log_time DESC')
            ->page($p . ',20')
            ->select();
        $this->assign('list', $logs);
        $count = $Log->alias('sl')
            ->join('__SELLER__ s', 's.seller_id = sl.log_seller_id')
            ->where('s.seller_id', $seller_id)
            ->count();
        $Page = new Page($count, 20);
        $show = $Page->show();
        $this->assign('page', $show);
        return $this->fetch();
    }
    /**
     * 清空系统缓存
     */
    public function cleanCache()
    {
        delFile('./public/upload/goods/thumb');// 删除缩略图
        clearCache();
        //$html_arr = glob("./Application/Runtime/Html/*.html");
        //foreach ($html_arr as $key => $val) {
            // 删除详情页
        //    if (strstr($val, 'Home_Goods_goodsInfo') || strstr($val, 'Home_Goods_ajaxComment') || strstr($val, 'Home_Goods_ajax_consult'))
        //        unlink($val);
        //}
        $this->success("清除成功!!!", U('Index/index'));
    }

    /**
     * 商品静态页面缓存清理
     */
    public function ClearGoodsThumb()
    {
        $goods_id = I('goods_id/d');
        delFile("./public/upload/goods/thumb/$goods_id"); // 删除缩略图
        $json_arr = array('status' => 1, 'msg' => '清除成功,请清除对应的缩略图', 'result' => '');
        $json_str = json_encode($json_arr);
        exit($json_str);
    }

    /**
     * 清空静态商品页面缓存
     */
    public function ClearGoodsHtml()
    {
        $goods_id = I('goods_id/d');
        clearCache();
        $json_arr = array('status' => 1, 'msg' => '清除成功', 'result' => '');       
        $json_str = json_encode($json_arr);
        exit($json_str);
    }

}