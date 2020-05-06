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
namespace app\seller\controller;

use app\common\model\StoreAddress;
use think\Db;
use think\Loader;
use think\Page;

class Address extends Base
{

    /**
     * 店铺地址
     */
    public function index()
    {
        $StoreAddress = new StoreAddress();
        $count = $StoreAddress->where('store_id', STORE_ID)->count();
        $page = new Page($count, 10);
        $list = $StoreAddress->where('store_id',STORE_ID)->order('store_address_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $page);
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function info()
    {
        $id = input('id');
        if($id){
            $storeAddress = StoreAddress::get(['store_address_id'=>$id,'store_id'=>STORE_ID]);
            if(empty($storeAddress)){
                $this->error('非法操作');
            }
            $city = Db::name('region')->where('parent_id', $storeAddress['province_id'])->select();
            $area = Db::name('region')->where('parent_id', $storeAddress['city_id'])->select();
            $this->assign('city', $city);
            $this->assign('area', $area);
            $this->assign('storeAddress', $storeAddress);
        }
        $province = Db::name('region')->where('parent_id', 0)->select();
        $this->assign('province', $province);
        return $this->fetch();
    }

    public function save()
    {
        $data = input('post.');
        $addressValidate = Loader::validate('StoreAddress');
        if (!$addressValidate->batch()->check($data)) {
            $this->ajaxReturn(['status' => 0, 'msg' => '操作失败', 'result' => $addressValidate->getError()]);
        }
        if($data['store_address_id']){
            $storeAddress = StoreAddress::get(['store_address_id' => $data['store_address_id'], 'store_id' => STORE_ID]);
            if(empty($storeAddress)){
                $this->ajaxReturn(array('status' => 0, 'msg' => '非法操作','result'=>''));
            }
        }else{
            $storeAddress = new StoreAddress();
        }
        $storeAddress->data($data, true);
        $storeAddress['store_id'] = STORE_ID;
        $is_default_count = Db::name('store_address')->where(['store_id' => STORE_ID, 'type'=>$data['type'],'is_default'=>1])->count();
        if($is_default_count == 0){
            $storeAddress->is_default = 1;
        }
        $row = $storeAddress->allowField(true)->save();
        if ($storeAddress['is_default'] == 1) {
            Db::name('store_address')->where(['store_id' => STORE_ID, 'type' => $storeAddress['type'], 'is_default' => 1, 'store_address_id' => ['neq', $storeAddress->store_address_id]])
                ->update(['is_default' => 0]);
        }
        if($row !== false){
            $this->ajaxReturn(['status' => 1, 'msg' => '操作成功', 'result' => '']);
        }else{
            $this->ajaxReturn(['status' => 0, 'msg' => '操作失败', 'result' => '']);
        }
    }

    public function delete()
    {
        $id = input('id');
        if(empty($id)){
            $this->ajaxReturn(['status' => 0, 'msg' => '参数错误', 'result' => '']);
        }
        $delete = Db::name('store_address')->where(['store_address_id'=>$id,'store_id'=>STORE_ID])->delete();
        if($delete !== false){
            $this->ajaxReturn(['status' => 1, 'msg' => '操作成功', 'result' => '']);
        }else{
            $this->ajaxReturn(['status' => 0, 'msg' => '操作失败', 'result' => '']);
        }

    }

}