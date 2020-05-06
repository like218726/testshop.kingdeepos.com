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
 * 专题管理
 * Date: 2016-06-09
 */

namespace app\seller\controller;

use app\common\model\Shopper;
use think\Db;
use think\Loader;
use think\Page;

class Shop extends Base
{

	public function index()
	{
	    $data = input();
		$Shop = new \app\common\model\Shop();

		$where = ['deleted'=>0];
		$where['store_id'] = STORE_ID;
        if($data['province_id']){
            $where['province_id'] = $data['province_id'];
            $city_list = Db::name('region')->where(['parent_id'=>$data['province_id']])->cache(true)->select();
            $this->assign('city_list', $city_list);
        }
        if($data['city_id']){
            $where['city_id'] = $data['city_id'];
            $district_list = Db::name('region')->where(['parent_id'=>$data['city_id']])->cache(true)->select();
            $this->assign('district_list', $district_list);
        }
        if($data['district_id'] && is_numeric($data['district_id'])){
            $where['district_id'] = $data['district_id'];
        }
        if($data['shop_name']){
            $where['shop_name'] = ['like','%'.$data['shop_name'].'%'];
        }
        $count = $Shop->where($where)->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
		$list = $Shop->append(['address_region'])->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order('shop_id DESC')->select();
        $province_list = db('region')->where(['parent_id'=>0,'level'=> 1])->cache(true)->select();
        $this->assign('province_list', $province_list);
        $this->assign('page', $show);
		$this->assign('list', $list);
		return $this->fetch();
	}

	public function info()
	{
		$shop_id = input('shop_id');
		$province_list = db('region')->where(array('parent_id' => 0))->select();
		if($shop_id){
			$Shop = new \app\common\model\Shop();
			$shop = $Shop->where(['shop_id'=>$shop_id, 'store_id'=>STORE_ID])->find();
			if(empty($shop)){
				$this->error('非法操作');
			}
			$city_list = db('region')->where(array('parent_id' => $shop['province_id']))->select();
			$district_list = db('region')->where(array('parent_id' => $shop['city_id']))->select();
            $shop_image_list = db('shop_images')->where(['shop_id'=>$shop['shop_id']])->select();
            $this->assign('shop_image_list', $shop_image_list);
            $this->assign('city_list', $city_list);
			$this->assign('district_list', $district_list);
		}
		$this->assign('province_list', $province_list);
		$this->assign('shop', $shop);
		return $this->fetch();
	}

	public function save(){
		$data = input('post.');
        $shop_images = input('shop_images/a',[]);
		$shopValidate = Loader::validate('Shop');
		if (!$shopValidate->scene($data['shop_id']?'edit':'add')->batch()->check($data)) {
			$this->ajaxReturn(['status' => 0, 'msg' => array_values($shopValidate->getError())[0], 'result' => $shopValidate->getError()]);
		}
        if (empty($data['monday']) && empty($data['tuesday']) && empty($data['wednesday']) && empty($data['thursday']) && empty($data['friday']) && empty($data['saturday']) && empty($data['sunday'])) {
            $this->ajaxReturn(['status' => 0, 'msg' => '营业时间一周至少选择一天', 'result' => ['monday'=>'营业时间一周至少选择一天']]);
        }
        $Shop = new \app\common\model\Shop();
        $Shopper = new Shopper();
        $data['add_time'] = time();
        $Shop->data($data, true);
        $Shop['store_id'] = STORE_ID;
        //添加
        if (!$data['shop_id']) {
            $user_id = db('users')->where(['email|mobile'=>$data['user_name']])->getField('user_id');
            if(empty($user_id)){
                if(check_email($data['user_name'])){
                    $user_data['email'] = $data['user_name'];
                }else{
                    $user_data['mobile'] = $data['user_name'];
                };
                $user_data['password'] = $data['password'];
                $user_obj = new \app\admin\logic\UsersLogic();
                $add_user_res = $user_obj->addUser($user_data);
                $user_id = $add_user_res['user_id'];
            }
            $Shop['user_id'] = $user_id;
            $Shop->allowField(true)->save($Shop);
            $shopper_data = ['shopper_name'=>$data['shopper_name'],'user_id'=>$user_id,'store_id'=>STORE_ID,'shop_id'=>$Shop->shop_id,'add_time'=>time()];
            $Shopper->save($shopper_data);
        }else{
            //编辑
            db('shop_images')->where('shop_id',$data['shop_id'])->delete();
            $Shop->allowField(true)->save($Shop,['shop_id'=>$data['shop_id']]);
        }
        foreach($shop_images as $image){
            if(!empty($image)){
                db('shop_images')->insert(['shop_id'=>$Shop->shop_id,'image_url'=>$image]);
            }
        }
        $this->ajaxReturn(['status' => 1, 'msg' => '添加成功', 'result' => '']);
	}

    /**
     * 删除
     */
    public function delete()
    {
        $shop_id = input('shop_id/d');
        if(empty($shop_id)){
            $this->ajaxReturn(['status' => 0, 'msg' => '参数错误']);
        }
        $Shop = new \app\common\model\Shop();
        $shop = $Shop->where(['shop_id'=>$shop_id])->find();
        if(empty($shop)){
            $this->ajaxReturn(['status' => 0, 'msg' => '非法操作', 'result' => '']);
        }
        $row = $shop->save(['deleted'=>1]);
        if($row !== false){
            $this->ajaxReturn(['status' => 1, 'msg' => '删除成功', 'result' => '']);
        }else{
            $this->ajaxReturn(['status' => 0, 'msg' => '删除失败', 'result' => '']);
        }
    }

    public function shopImageDel()
    {
        $path = input('filename','');
        db('shop_images')->where("image_url",$path)->delete();
    }
}
