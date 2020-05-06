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
 * Date: 2015-09-21
 */

namespace app\admin\controller;
use think\Db;
use think\Loader;
use think\Page;
use app\common\logic\Express;

class Shipping extends Base{

    /**
     * 快递公司列表
     * @return mixed
     */
    public function index(){
        $shipping_name = input('shipping_name/s');
        $shipping_code = input('shipping_code/s');
        $where = [];
        if($shipping_name){
            $where['shipping_name'] = ['like','%'.$shipping_name.'%'];
        }
        if($shipping_code){
            $where['shipping_code'] = $shipping_code;
        }
        $shipping = new \app\common\model\Shipping();
        $count = $shipping->where($where)->count();
        $Page = new Page($count, 10);
        $list = $shipping->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $Page);
        $this->assign('list', $list);
        return $this->fetch(); 
    }

    /**
     * 快递公司详情页
     * @return mixed
     */
    public function info()
    {
        $shipping_id = input('shipping_id/d');
        if ($shipping_id) {
            $Shipping = new \app\common\model\Shipping();
            $shipping = $Shipping->where(['shipping_id'=>$shipping_id])->find();
            if(empty($shipping)){
                $this->error('没有找到相应记录');
            }
            $this->assign('shipping', $shipping);
        }
        $this->assign('express_switch',tpCache('express.express_switch'));
        return $this->fetch();
    }

    /**
     * 添加和更新快递公司
     */
    public function save()
    {
        $data = input('post.');
        $validate = Loader::validate('Shipping');
        if (!$validate->batch()->check($data)) {
            $this->ajaxReturn(['status' => 0, 'msg' => '操作失败', 'result' => $validate->getError()]);
        }
        if (empty($data['shipping_id'])) {
            $shipping = new \app\common\model\Shipping();
        } else {
            $shipping = \app\common\model\Shipping::get($data['shipping_id']);
        }
        $shipping_save = $shipping->data($data, true)->save();
        if ($shipping_save === false) {
            $this->ajaxReturn(['status' => 0, 'msg' => '操作失败', 'result' => $validate->getError()]);
        }else{
            $this->ajaxReturn(['status' => 1, 'msg' => '操作成功', 'result' => '']);
        }

    }

    /**
     * 删除快递公司
     * @throws \think\Exception
     */
    public function delete()
    {
        $shipping_id = input('shipping_id');
        Db::name('shipping')->where('shipping_id', $shipping_id)->delete();
        Db::name('store_shipping')->where('shipping_id', $shipping_id)->delete();
        $this->ajaxReturn(['status' => 1, 'msg' => '删除成功', 'result' => '']);
    }
    
    
    
    /**
     *物流配置信息
     **/
    public function express()
    {
        $config = tpCache('express');
        $this->assign('config',$config);//当前配置项
        return $this->fetch();
    }
    
    
    /*
     * 新增修改配置
     */
    public function handle()
    {
        $param = I('post.');
        $inc_type = 'express';
        //dump($param);die;
        unset($param['inc_type']);
    
        tpCache($inc_type,$param);
    
        $this->success("操作成功",U('Shipping/express',array('inc_type'=>$inc_type)));
    }
    
    
    public function ajaxCoding()
    {
        $logistics_id = I('logistics_id/d','0');
        $coding = I('coding/s','0');
        $list = Db::name('logistics_code')->where(['logistics_id'=>$logistics_id,'shorthand'=>['like',$coding."%"]])->limit(5)->select();
        $this->ajaxReturn(['status' => 1,'msg' => '操作成功','data'=>$list]);
    }
    
    
    public function getExpressInfo(){
        if(IS_AJAX){
            $logistics_id   = I('logistics_id/d',0);
            $coding         = I('coding/s','');
            $num            = I('num/s','');
            $obj            = new Express($logistics_id);
            $res            = $obj->getExpressList(['coding'=>$coding,'num'=>$num]);
            $this->ajaxReturn($res);
        }
    }
    
}