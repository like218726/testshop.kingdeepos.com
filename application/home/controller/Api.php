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
 * Author: JY
 * Date: 2015-09-23
 */

namespace app\home\controller;

use app\common\logic\User;
use app\common\logic\UsersLogic;
use app\common\model\Shop;
use app\common\model\Users;
use app\common\util\TpshopException;
use think\Cookie;
use think\Verify;
use think\Db;
use app\common\logic\Express;

class Api extends Base
{

    public $send_scene;

    public function _initialize()
    {
        parent::_initialize();
        session('user');
    }

    /*
     * 获取地区
     */
    public function getRegion()
    {
        $parent_id = I('get.parent_id/d');
        $selected = I('get.selected', 0);
        $data = M('region')->where("parent_id", $parent_id)->select();
        $html = '';
        if ($data) {
            foreach ($data as $h) {
                if ($h['id'] == $selected) {
                    $html .= "<option value='{$h['id']}' selected>{$h['name']}</option>";
                }
                $html .= "<option value='{$h['id']}'>{$h['name']}</option>";
            }
        }
        echo $html;
    }
 

    public function getTwon()
    {
        $parent_id = I('get.parent_id/d');
        $data = M('region')->where("parent_id", $parent_id)->select();
        $html = '';
        if ($data) {
            foreach ($data as $h) {
                $html .= "<option value='{$h['id']}'>{$h['name']}</option>";
            }
        }
        if (empty($html)) {
            echo '0';
        } else {
            echo $html;
        }
    }

    /**
     * 获取省
     */
    public function getProvince()
    {
        $province = Db::name('region')->field('id,name')->where(array('level' => 1))->cache(true)->select();
        $res = array('status' => 1, 'msg' => '获取成功', 'result' => $province);
        $this->ajaxReturn($res);
    }
    public function area()
    {
        $province_id = input('province_id/d');
        $city_id = input('city_id/d');
        $district_id = input('district_id/d');
        $province_list = Db::name('region')->field('id,name')->where('level', 1)->cache(true)->select();
        $city_list = Db::name('region')->field('id,name')->where('parent_id', $province_id)->cache(true)->select();
        $district_list = Db::name('region')->field('id,name')->where('parent_id', $city_id)->cache(true)->select();
        $town_list = Db::name('region')->field('id,name')->where('parent_id', $district_id)->cache(true)->select();
        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功',
            'result' => ['province_list' => $province_list, 'city_list' => $city_list, 'district_list' => $district_list, 'town_list' => $town_list]]);
    }
    /**
     * 获取市或者区
     */
    public function getRegionByParentId()
    {
        $parent_id = input('parent_id');
        $res = array('status' => 0, 'msg' => '获取失败，参数错误', 'result' => '');
        if($parent_id){
            $region_list = Db::name('region')->field('id,name')->where(['parent_id'=>$parent_id])->select();
            $res = array('status' => 1, 'msg' => '获取成功', 'result' => $region_list);
        }
         $this->ajaxReturn($res);
    }

    public function getArea()
    {
        $id = I('id/d');
        if ($id) {
            $area = M('region')->field('id,name,parent_id as pid')->where(array('parent_id' => $id))->cache(true)->select();
            $res = array('status' => 1, 'msg' => '获取成功', 'result' => $area);
        } else {
            $res = array('status' => 0, 'msg' => '获取失败,参数有误', 'result' => '');
        }
         $this->ajaxReturn($res);
    }

    /*
     * 获取商品分类
     */
    public function get_category()
    {
        $parent_id = I('get.parent_id/d', '0'); // 商品分类 父id
        empty($parent_id) && exit('');
        $list = M('goods_category')->where(array('parent_id' => $parent_id))->select();
        foreach ($list as $k => $v) {
            $html .= "<option value='{$v['id']}' rel='{$v['commission']}'>{$v['name']}</option>";
        }
        exit($html);
    }

    public function get_cates()
    {
        $parent_id = I('get.parent_id/d', '0'); // 商品分类 父id
        empty($parent_id) && exit('');
        $list = M('goods_category')->where(array('parent_id' => $parent_id))->select();
        $html = '';
        foreach ($list as $k => $v) {
            $html .= "<input type='checkbox' name='subcate[]' rel='{$v['commission']}' data-name='{$v['name']}' value='{$v['id']}'>" . $v['name'];
        }
        if($html != '')
            $html .= "<input type='checkbox' id='check_alls' onclick=\"check_all()\">全选";
        exit($html);
    }

    /*
     * 获取店铺内分类
     */
    public function get_store_category()
    {
        // 店铺id
        $store_id = session('store_id');
        $store_id = $store_id ? $store_id : $store_id = $store_id ? $store_id : I('store_id/d',0);;
        $parent_id = I('get.parent_id/d', 0); // 商品分类 父id

        ($parent_id == 0) && exit('');

        $list = M('store_goods_class')->where(['parent_id' => $parent_id, 'store_id' => $store_id])->select();
        $html = '';
        foreach ($list as $k => $v)
            $html .= "<option value='{$v['cat_id']}'>{$v['cat_name']}</option>";
        exit($html);
    }


    /**
     * 前端发送短信方法: APP/WAP/PC 共用发送方法
     */
    public function send_validate_code()
    { 
        
        $this->send_scene = C('SEND_SCENE');
        $type = I('type');
        $scene = I('scene');    //发送短信验证码使用场景
        $mobile = I('mobile');
        $sender = I('send');
        $verify_code = I('verify_code');
        $mobile = !empty($mobile) ? $mobile : $sender;
        $session_id = I('unique_id', session_id());
        //注册 修改号码
        if (($scene == 1 && !empty($verify_code))) {
            $verify = new Verify();
            if (!$verify->check($verify_code, 'user_reg')) {
                $res = array('status'=>-1,'msg'=>'图像验证码错误');
                ajaxReturn($res);
            }
        }
        if ($type == 'email') {
            //发送邮件验证码
            $logic = new UsersLogic();
            $res = $logic->send_email_code($sender);
             $this->ajaxReturn($res);
        } else {
            //发送短信验证码
            $res = checkEnableSendSms($scene);
            if ($res['status'] != 1) {
                //print_r($res);
                ajaxReturn($res);
            }

            //判断是否存在验证码
            $data = M('sms_log')->where(array('mobile' => $mobile, 'session_id' => $session_id , 'status'=>1))->order('id DESC')->find();
            //获取时间配置
            $sms_time_out = tpCache('sms.sms_time_out');
            $sms_time_out = $sms_time_out ? $sms_time_out : 120;
            //120秒以内不可重复发送
            if ($data && (time() - $data['add_time']) < $sms_time_out) {
                ajaxReturn(array('status' => -1, 'msg' => $sms_time_out . '秒内不允许重复发送'));
            }
            //随机一个验证码
            $code = rand(1000, 9999);  
            
            $params['code'] = $code;
            
            //发送短信
            $resp = sendSms($scene, $mobile, $params, $session_id);

            if ($resp['status'] == 1) {
                //发送成功, 修改发送状态位成功
                M('sms_log')->where(array('mobile'=>$mobile,'code'=>$code,'session_id'=>$session_id , 'status' => 0))->save(array('status' => 1));
                ajaxReturn(array('status' => 1, 'msg' => '发送成功,请注意查收'));
            } else {
                ajaxReturn(array('status' => -1, 'msg' => '发送失败' . $resp['msg']));
            }
        }
    }

    /**
     * 验证短信验证码: APP/WAP/PC 共用发送方法
     */
    public function check_validate_code(){
       
        $code = I('post.code');
        $mobile = I('mobile');
        $send = I('send');
        $sender = empty($mobile) ? $send : $mobile;
        $type = I('type');
        $session_id = I('unique_id', session_id());
        $scene = I('scene', -1);
        $logic = new UsersLogic();
        $res = $logic->check_validate_code($code, $sender, $type ,$session_id, $scene);
        ajaxReturn($res);
    }

    /**
     * 检测手机号是否已经存在
     */
    public function issetMobile()
    {
        $mobile = I("mobile", '0');
        $users = M('users')->where("mobile", $mobile)->find();
        if ($users)
            exit ('1');
        else
            exit ('0');
    }


    /**
     * 检测邮件是否已经存在
     */
    public function issetEmail()
    {
        $mobile = I("email", '0');
        $users = M('users')->where("email", $mobile)->find();
        if ($users)
            exit ('1');
        else
            exit ('0');
    }

    /**
     * 查询物流
     */
    public function queryExpress()
    {
    	$express_switch = tpCache('express.express_switch');
    	if($express_switch == 1){
    		require_once(PLUGIN_PATH . 'kdniao/kdniao.php');
    		$kdniao = new \kdniao();
    		$data['OrderCode'] = empty(I('order_sn')) ? date('YmdHis') : I('order_sn');
    		$data['ShipperCode'] = I('shipping_code');
    		$data['LogisticCode'] = I('invoice_no');
            // 顺丰的有可能需要手机后四位
            if($data['ShipperCode'] == 'SF'){
                $mobile = input('mobile');
                if($mobile){
                    $data['CustomerName'] = substr($mobile,-4);
                }else{
                    $mobile = Db::name('delivery_doc')->where('invoice_no',$data['LogisticCode'])->order('id desc')->value('mobile');
                    if($mobile){
                        $data['CustomerName'] = substr($mobile,-4);
                    }
                }
            }
    		$res = $kdniao->getOrderTracesByJson(json_encode($data));
    		$res =  json_decode($res, true);
            if (isset($res['Traces'])) {
    			foreach ($res['Traces'] as $val){
    				$tmp['context'] = $val['AcceptStation'];
    				$tmp['time'] = $val['AcceptTime'];
    				$res['data'][] = $tmp;
    			}
    			$res['status'] = "200";
    		}else{
    			$res['message'] = $res['Reason'];
    		}
    		 $this->ajaxReturn($res);
    	}else{
    		$logistics_id   = '1';
    	    $coding         = trim(I('shipping_code'));
    	    $num            = trim(I('invoice_no'));
    	    $obj            = new Express($logistics_id);
    	    $express            = $obj->getExpressList(['coding'=>$coding,'num'=>$num]);
    		exit(json_encode($express));
    	}
    }

    /**
     * 检查订单状态
     */
    public function check_order_pay_status()
    {
        $master_order_id = I('master_order_id/s');
        $order_id = I('order_id/d');

        if (empty($master_order_id) && empty($order_id)) {
            $res = array('message' => '参数错误', 'status' => -1, 'result' => '');
             $this->ajaxReturn($res);
        }

        if (!empty($master_order_id)) {
            $order = M('order')->field('pay_status')->where(array('master_order_sn' => $master_order_id))->find();
            if ($order['pay_status'] != 0) {
                $res = array('message' => '已支付', 'status' => 1, 'result' => $order);
            } else {
                $res = array('message' => '未支付', 'status' => 0, 'result' => $order);
            }
             $this->ajaxReturn($res);
        }
        if (!empty($order_id)) {
            $order = M('order')->field('pay_status')->where(array('order_id' => $order_id))->find();
            if ($order['pay_status'] != 0) {
                $res = array('message' => '已支付', 'status' => 1, 'result' => $order);
            } else {
                $res = array('message' => '未支付', 'status' => 0, 'result' => $order);
            }
             $this->ajaxReturn($res);
        }
    }

    /**
     * 检测充值状态
     */
    public function check_recharge_pay_status()
    {
        $order_id = I('order_id/d');

        if (empty($order_id)) {
            $res = array('message' => '参数错误', 'status' => -1, 'result' => '');
            $this->ajaxReturn($res);
        }

        $order = M('recharge')->field('pay_status,order_id')->where(array('order_id' => $order_id))->find();
        if ($order['pay_status'] == 1) {
            $res = array('message' => '已支付', 'status' => 1, 'result' => $order);
        } else {
            $res = array('message' => '未支付', 'status' => 0, 'result' => $order);
        }
        $this->ajaxReturn($res);
    }

    /**
     * 广告位js
     */
    public function ad_show()
    {
        $pid = I('pid/d', 1);
        $limit = I('limit/d', 1);
        $where = array(
            'pid' => $pid,
            'enabled' => 1,
            'start_time' => array('lt', strtotime(date('Y-m-d H:00:00'))),
            'end_time' => array('gt', strtotime(date('Y-m-d H:00:00'))),
        );
        $ad = Db::name("ad")->where($where)->order("orderby desc")->limit($limit)->select();
        $this->assign('ad', $ad);
        return $this->fetch();
    }

    /**
     *  搜索关键字
     * @return array
     */
    public function searchKey(){
        $searchKey = input('key');
        $searchKeyList = Db::name('search_word')
            ->where('keywords','like',$searchKey.'%')
            ->whereOr('pinyin_full','like',$searchKey.'%')
            ->whereOr('pinyin_simple','like',$searchKey.'%')
            ->limit(10)
            ->select();
        if($searchKeyList){
            return json(['status'=>1,'msg'=>'搜索成功','result'=>$searchKeyList]);
        }else{
            return json(['status'=>0,'msg'=>'没记录','result'=>$searchKeyList]);
        }
    }

    public function doCookieArea(){
        $address = input('address/a',[]);
        if(empty($address) || empty($address['province'])){
            $this->setCookieArea();
            return;
        }
        $province_id = Db::name('region')->where(['level' => 1, 'name' => ['like', '%' . $address['province'] . '%']])->limit('1')->value('id');
        if(empty($province_id)){
            $this->setCookieArea();
            return;
        }
        if (empty($address['city'])) {
            $city_id = Db::name('region')->where(['level' => 2, 'parent_id' => $province_id])->limit('1')->order('id')->value('id');
        } else {
            $city_id = Db::name('region')->where(['level' => 2, 'parent_id' => $province_id, 'name' => ['like', '%' . $address['city'] . '%']])->limit('1')->value('id');
        }
        if (empty($address['district'])) {
            $district_id = Db::name('region')->where(['level' => 3, 'parent_id' => $city_id])->limit('1')->order('id')->value('id');
        } else {
            $district_id = Db::name('region')->where(['level' => 3, 'parent_id' => $city_id, 'name' => ['like', '%' . $address['district'] . '%']])->limit('1')->value('id');
        }
        $this->setCookieArea($province_id, $city_id, $district_id);
    }

    /**
     * 设置地区缓存
     * @param $province_id
     * @param $city_id
     * @param $district_id
     */
    private function setCookieArea($province_id = 1, $city_id = 2, $district_id = 3)
    {
        Cookie::set('province_id', $province_id);
        Cookie::set('city_id', $city_id);
        Cookie::set('district_id', $district_id);
    }

    public function shop()
    {
        $province_id = input('province_id/d', 0);
        $city_id = input('city_id/d', 0);
        $district_id = input('district_id/d', 0);
        $shop_address = input('shop_address/s', '');
        $longitude = input('longitude/s', 0);
        $latitude = input('latitude/s', 0);
        $store_id = input('store_id/d',0);
        $week = input('week',0);//星期 1 2 3 4 5 6 7， 7代表星期天
        if (empty($province_id) && empty($province_id) && empty($district_id)) {
            $this->ajaxReturn([]);
        }
        $where = ['deleted' => 0, 'shop_status' => 1, 'province_id' => $province_id, 'city_id' => $city_id, 'district_id' => $district_id];
        if ($store_id) {
            $where['store_id'] = $store_id;
        }
        switch ($week){
            case 1:
                $where['monday'] = 1;
            break;
            case 2:
                $where['tuesday'] = 1;
            break;
            case 3:
                $where['wednesday'] = 1;
            break;
            case 4:
                $where['thursday'] = 1;
            break;
            case 5:
                $where['friday'] = 1;
            break;
            case 6:
                $where['saturday'] = 1;
            break;
            case 7:
                $where['sunday'] = 1;
            break;
        }
        $field = '*';
        $order = 'shop_id desc';
        if ($longitude) {
            $field .= ',round(SQRT((POW(((' . $longitude . ' - longitude)* 111),2))+  (POW(((' . $latitude . ' - latitude)* 111),2))),2) AS distance';
            $order = 'distance ASC';
        }
        if($shop_address){
            $where['shop_name|shop_address'] = ['like', '%'.$shop_address.'%'];
        }
        $Shop = new Shop();
        $shop_list = $Shop->field($field)->where($where)->order($order)->select();
        $origins = $destinations = [];
        if ($shop_list) {
            $shop_list = collection($shop_list)->append(['phone','area_list','work_time','work_day'])->toArray();
            $shop_list_length = count($shop_list);
            for ($shop_cursor = 0; $shop_cursor < $shop_list_length; $shop_cursor++) {
                $origin = $latitude . ',' . $longitude;
                array_push($origins, $origin);
                $destination = $shop_list[$shop_cursor]['latitude'] . ',' . $shop_list[$shop_cursor]['longitude'];
                array_push($destinations, $destination);
            }
            $url = 'http://api.map.baidu.com/routematrix/v2/driving?output=json&origins=' . implode('|', $origins) . '&destinations=' . implode('|', $destinations) . '&ak=Sgg73Hgc2HizzMiL74TUj42o0j3vM5AL';
            $result = httpRequest($url, "get");
            $data = json_decode($result, true);
            if (!empty($data['result'])) {
                for ($shop_cursor = 0; $shop_cursor < $shop_list_length; $shop_cursor++) {
                    $shop_list[$shop_cursor]['distance_text'] = $data['result'][$shop_cursor]['distance']['text'];
                    $shop_list[$shop_cursor]['distance_number'] = $data['result'][$shop_cursor]['distance']['value'];
                }
            }else{
                for ($shop_cursor = 0; $shop_cursor < $shop_list_length; $shop_cursor++) {
                    $shop_list[$shop_cursor]['distance_number'] = 99999;
                    $shop_list[$shop_cursor]['distance_text'] = $data['message'];
                }
            }
        }
        array_multisort(array_column($shop_list,'distance_number'),SORT_ASC,$shop_list);
        $this->ajaxReturn(['status'=>1,'msg'=>'获取成功','result'=>$shop_list]);
    }

    /**
     * 检查绑定账号的合法性
     */
    public function checkBindMobile()
    {
        $mobile = input('mobile/s');
        if(empty($mobile)){
            $this->ajaxReturn(['status' => 0, 'msg' => '参数错误', 'result' => '']);
        }
        if(!check_mobile($mobile)){
            $this->ajaxReturn(['status' => 0, 'msg' => '手机格式错误', 'result' => '']);
        }
        //1.检查账号密码是否正确
        $users = Users::get(['mobile'=>$mobile]);
        if (empty($users)) {
            $this->ajaxReturn(['status' => 0, 'msg' => '账号不存在', 'result' => '']);
        }
        $user = new User();
        try{
            $user->setUser($users);
            $user->checkOauthBind();
            $this->ajaxReturn(['status' => 1, 'msg' => '该手机可绑定', 'result' => '']);
        }catch (TpshopException $t){
            $error = $t->getErrorArr();
            if(isset($error['0']) && !isset($error['msg'])){
                $error['msg'] = $error['0'];
            }
            $this->ajaxReturn($error);
        }
    }

    /**
     * 检查绑定账号的合法性
     */
    public function checkBindMobile_news()
    {
        $mobile = input('mobile/s');
        if(empty($mobile)){
            $this->ajaxReturn(['status' => 0, 'msg' => '参数错误', 'result' => '']);
        }
        if(!check_mobile($mobile)){
            $this->ajaxReturn(['status' => 0, 'msg' => '手机格式错误', 'result' => '']);
        }
        //1.检查账号密码是否正确
//        $users = Db::name('users')->where("mobile", $mobile)->find();
//        if ($users) {
//            $this->ajaxReturn(['status' => 0, 'msg' => '该手机号已被注册', 'result' => '']);
//        }
        $this->ajaxReturn(['status' => 1, 'msg' => '该手机可注册', 'result' => '']);
    }

    /**
     * 检查注册账号的合法性
     */
    public function checkRegMobile()
    {
        $mobile = input('mobile/s');
        if(empty($mobile)){
            $this->ajaxReturn(['status' => 0, 'msg' => '参数错误', 'result' => '']);
        }
        if(!check_mobile($mobile)){
            $this->ajaxReturn(['status' => 0, 'msg' => '手机格式错误', 'result' => '']);
        }
        //1.检查账号密码是否正确
        $users = Db::name('users')->where("mobile", $mobile)->find();
        if ($users) {
            $this->ajaxReturn(['status' => 0, 'msg' => '该手机号已被注册', 'result' => '']);
        }
        $this->ajaxReturn(['status' => 1, 'msg' => '该手机可注册', 'result' => '']);
    }

    /**
     * 自定义底部菜单数据
     */
    public function get_mobile_foot_menu(){
        $menu = Db::name('mobile_template')
            ->where('is_index',1)
            ->find();
        if(!empty($menu['footmenu'])){
            $data['footmenu'] = json_decode($menu['footmenu'],true);
            $data['footmenu_html'] = htmlspecialchars_decode($menu['footmenu_html']);
            $status = 1;
        }else{
            $data = [];
            $status = 0;
        }
        $this->ajaxReturn(['status' => $status, 'msg' => '自定义底部菜单数据', 'result' =>$data,'$menu'=>$menu]);
    }
}