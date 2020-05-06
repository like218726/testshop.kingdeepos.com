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
 * Date: 2016-05-29
 */
namespace app\api\controller;

use think\Db;
use app\common\logic\NewjoinLogic;

class Newjoin extends Base
{
    private  $apply = [];

    public function _initialize()
    {
        parent::_initialize();
    }

    public function __construct(){
        parent::__construct();
        $this->apply = Db::name('store_apply')->where(['user_id' => $this->user_id])->find();
        ($this->apply['apply_state'] == 1) && $this->fetch('agreement');
    }

    /**
     * 入驻申请首页
     * @return mixed
     */
    public function agreement()
    {
        return $this->fetch();
    }

    /**
     * 卖家入驻商家信息
     * @return mixed
     */
    public function basicInfo()
    {
        if (IS_POST) {
            $post_data['contacts_name'] = trim(I('post.contacts_name'));     //店主名称
            $post_data['contacts_mobile'] = trim(I('post.contacts_mobile',''));   //手机号
            $post_data['company_province'] = I('post.company_province/d');  //所在省份
            $post_data['company_city'] = I('post.company_city/d');      //所在城市
            $post_data['company_district'] = I('post.company_district/d');  //所在地区
            $post_data['company_address'] = trim(I('post.company_address'));   //详细地址
            if(empty($post_data['contacts_name']) && empty($post_data['contacts_mobile'])){
                $this->ajaxReturn(['status'=>-1,'msg'=>'请求方式错误！']);
            }
            if(!check_mobile($post_data['contacts_mobile'])){
                $this->ajaxReturn(['status'=>-1,'msg'=>'手机号格式错误！']);
            }
            if (empty($this->apply)) {
                $post_data['user_id'] = $this->user_id;
                $post_data['apply_type'] = 0; //默认是企业入驻
                $res = Db::name('store_apply')->add($post_data);
            } else {
                $res = Db::name('store_apply')->where(['user_id' => $this->user_id])->save($post_data);
            }
            if ($res !== false) {
                $this->ajaxReturn(['status'=>1,'msg'=>'提交成功']);// 下一个页面U('Newjoin/basic_info')
            } else {
                $this->ajaxReturn(['status'=>-1,'msg'=>'服务器繁忙,请联系官方客服']);
            }
        }else{
            $this->ajaxReturn(['status'=>-1,'msg'=>'请求方式错误！']);
        }
    }

    /**
     * 填写店铺信息
     * @return mixed
     */
    public function storeInfo()
    {
        if (IS_POST) {
            $post_data['store_name'] = trim(I('post.store_name'));
            $post_data['seller_name'] = trim(I('post.seller_name'));
            $post_data['store_type'] = I('post.store_type/d');
            $post_data['store_class_ids'] = I('post.store_class_ids/a');
            $post_data['sc_id'] = I('post.sc_id');
            $post_data['sc_name'] = I('post.sc_name');
            $verify_srore_name=Db::name('store_apply')->where(['store_name'=>$post_data['store_name'],'user_id'=>['neq',$this->user_id]])->count(); //检查店铺名称是否重复
            if(empty($post_data['store_name'])){
                $this->ajaxReturn(['status'=>-1,'msg'=>'请填写店铺名称！']);
            }elseif ($verify_srore_name>0){
                $this->ajaxReturn(['status'=>-1,'msg'=>'当前店铺名称已被使用！']);
            };
            if(empty($post_data['store_name'])){
                $this->ajaxReturn(['status'=>-1,'msg'=>'请填写店铺名称！']);
            };
            if(empty($post_data['sc_id'])){
                $this->ajaxReturn(['status'=>-1,'msg'=>'请填写店铺分类！']);
            };
            if(empty($post_data['seller_name'])){
                $this->ajaxReturn(['status'=>-1,'msg'=>'请填写卖家账号！']);
            };
            if(empty($post_data['store_type'])){
                $this->ajaxReturn(['status'=>-1,'msg'=>'请填选择店铺类型！']);
            };
            if (empty($post_data['store_class_ids'])) {
                $this->ajaxReturn(['status'=>-1,'msg'=>'请填写经营类目']);
            }else{
                $post_data['store_class_ids'] = serialize($post_data['store_class_ids']);  //序列化已选择经营类目
            }
            $res = Db::name('store_apply')->where(array('user_id' => $this->user_id))->save($post_data);
            if ($res !== false) {
                $this->ajaxReturn(['status'=>1,'msg'=>'提交成功']); // 下一个页面(U('Newjoin/remark'));
            } else {
                $this->ajaxReturn(['status'=>-1,'msg'=>'服务器繁忙,请联系官方客服']);
            }
        }else{
            $this->ajaxReturn(['status'=>-1,'msg'=>'请求方式错误！']);
        }
    }

    /**
     * 营业执照
     * @return mixed
     */
    public function remark()
    {
        if (IS_POST) {
            $data = I('post.');
            if ($data['business_permanent'] == 1) {   //选择了长期有效，营业执照起始日期就不用了
                $data['business_date_end'] = '长期';
                $data['business_date_start'] = '';
            } elseif (strtotime($data['business_date_end']) <= strtotime($data['business_date_start'])) {
                $this->ajaxReturn(['status'=>-1,'msg'=>'有效结束时间要大于起始时间']);
            }

            $logic = new NewjoinLogic;
            $return = $logic->uploadBusinessCertificate();
            if ($return['status'] != 1) {
                $this->ajaxReturn($return);
            }

            if ($return['result']) {
                $data['business_licence_cert'] = $return['result'];
            } elseif ($data['business_img']) {
                $data['business_licence_cert'] = $data['business_img']; //兼容小程序
                unset($data['business_img']);
            }

            $data['apply_state'] = 0;//每次提交资料回到待审核状态
            $data['add_time'] = time();  //全部填写完才记录申请时间
            $res = Db::name('store_apply')->where(array('user_id' => $this->user_id))->save($data);
            if ($res !== false) {
                $this->ajaxReturn(['status' => 1, 'msg' => '提交成功']);
            } else {
                $this->ajaxReturn(['status' => -1, 'msg' => '服务器繁忙,请联系官方客服']);
            }
        } else{
            $this->ajaxReturn(['status'=>-1,'msg'=>'请求方式错误！']);
        }
    }

    /**
     * 上传营业执照（小程序只能单独上传图片）
     */
    public function uploadBusinessCertificate()
    {
        $logic = new NewjoinLogic;
        $return = $logic->uploadBusinessCertificate(true);
        $this->ajaxReturn($return);
    }


    /*
     * 获取入驻信息(检查用户填写到哪个页面)
     */
    public function getApply()
    {
        $apply = $this->apply;
        $apply['store_class']=Db::name('store_class')->field('sc_id,sc_name')->select();
        if (!empty($apply)) {
            $apply['province'] = Db::name('region')->where(['level'=>1,'id'=>$apply['company_province']])->getField('name');  //省
            $apply['city'] = Db::name('region')->where(['level'=>2,'id'=>$apply['company_city']])->getField('name'); //市
            $apply['district']  = Db::name('region')->where(['level'=>3,'id'=>$apply['company_district']])->getField('name');  //区

            //店铺经营类目
            if (!empty($apply['store_class_ids'])) {
                $goods_cates = Db::name('goods_category')->getField('id,mobile_name,commission');
                $store_class_ids = unserialize($apply['store_class_ids']);
                if(!is_array($store_class_ids)){$this->ajaxReturn(array('status'=>-1,'msg'=>'获取失败，经营分类数据错误！'));}
                foreach ($store_class_ids as $val) {
                    $cat = explode(',', $val);
                    $bind_class_list = $goods_cates[$cat[0]]['mobile_name'].'/'.$goods_cates[$cat[1]]['mobile_name'].'/'.$goods_cates[$cat[2]]['mobile_name'];
                }
                $apply['store_class_ids'] = $store_class_ids;
                $apply['bind_class_list'] = $bind_class_list;
            }
            if (!empty($apply['business_licence_number']) && !empty($apply['legal_person'])) {
                //审核结果页
                $apply['status']='4';
            } else if (!empty($apply['store_class_ids']) && empty($apply['business_licence_number'])) {  //填写了申请分类，没有营业执照号
                //填写入驻店铺信息
                $apply['status']='3';
            }else if (!empty($apply['contacts_mobile']) && empty($apply['store_class_ids'])) {  //填写了店铺手机号，没有申请分类
                //填写入驻店铺信息
                $apply['status']='2';
            } else{
                $apply['status']='1';
            }
        }else{
            $apply['status']='1';//没有申请过
        } 
        $apply = filterNullAttribute($apply);
        $this->ajaxReturn(array('status'=>1,'msg'=>'获取成功','result'=>$apply));
    }
}