<?php
/*
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 商业用途务必到官方购买正版授权, 使用盗版将严厉追究您的法律责任。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * 2015-11-21
 */

namespace app\mobile\controller;

use think\Db;

class Newjoin extends MobileBase
{
    /*
     * 初始化操作
     */

    public $user_id;
    public $apply = array();

    public function _initialize()
    {
        if(input('store_id/d',0)){@cookie('store_id',input('store_id/d',0));}
        if(input('first_leader/d',0)){@cookie('first_leader',input('first_leader/d',0));}
        parent::_initialize();
        $this->user_id = cookie('user_id');
        if (empty($this->user_id) && ACTION_NAME != 'index') {
            $this->redirect(U('User/login'));
        } else if (!empty($this->user_id)) {
            $this->apply = M('store_apply')->where(array('user_id' => $this->user_id))->find();
        }
        ($this->apply['apply_state'] == 1) && $this->success('您的资料已经审核通过，现在您可以去经营您的店铺了，赶紧去商铺发布商品吧', U('User/index'), '', 5);
        $user = get_user_info($this->user_id);
        $this->assign('user', $user);
    }

    /**
     * 我要开店
     */
    public function guidance()
    {
        ($this->apply['apply_state'] == 2) && $this->error('抱歉，您的申请没有通过，系统将导自动导引到入驻页面,请您重新填写入驻信息', U('Newjoin/basic_info'), '', 5);
        if(input('first_leader/d',0)){
            //说明扫进来的
            $id = db('store_invite')->where(['user_id'=>$this->user_id])->value('id');
            if($id){
                $data['invite_user_id'] = input('first_leader/d',0);
                $data['update_time'] = time();
                db('store_invite')->where(['user_id'=>$this->user_id])->save($data);
            }else{
                $data['invite_user_id'] = input('first_leader/d',0);
                $data['user_id'] = $this->user_id;
                $data['update_time'] = time();
                $data['add_time'] = time();
                db('store_invite')->add($data);
            }
        }
        $info = M('help')->where('help_id=5 and is_show=1')->find();
        $this->assign('info',$info);
        return $this->fetch();
    }

    public function basic_info()
    {
        if (IS_POST) {
            $data = I('post.');
            $data['user_id'] = $this->user_id;
            $data['add_time'] = time();
            $data['apply_type'] = 0;
            foreach ($_FILES as $k => $v) {
                if (empty($v['tmp_name'])) {
                    $this->error('请上传必要证件');
                }
            }
            $files = $this->request->file();
            $savePath = 'public/upload/store/cert/' . date('Y-m-d') . '/';
            if(!file_exists('public/upload/store/cert/')){
                mkdir('public/upload/store/cert/', 0777, true);
            }
            if (!($_exists = file_exists($savePath))) {
                mkdir($savePath, 0777, true);
            }
            $image_upload_limit_size = config('image_upload_limit_size');
            if (is_array($files)) {
                foreach ($files as $key => $file) {
                    $info = $file->rule(function ($file) {
                        return md5(mt_rand()); // 使用自定义的文件保存规则
                    })->validate(['size' => $image_upload_limit_size, 'ext' => 'jpg,png,gif,jpeg'])->move($savePath, true);
                    if ($info) {
                        $filename = $info->getFilename();
                        $new_name = '/' . $savePath . $filename;
                        $data[$key] = $new_name;
                    } else {
                        $this->error($file->getError()); //上传错误提示错误信息
                    }
                }
            }
            //推荐开店判断
            $day = $this->tpshop_config['distribut_open_store_time'];
            if($day){
                $store_id = db('store_invite')->where(['user_id'=>$this->user_id,'update_time'=>['>',time()-($day*86400)]])->value('invite_user_id');
                $data['invite_user_id'] = $store_id?$store_id:0;
            }else{
                //长期有效
                $store_id = db('store_invite')->where(['user_id'=>$this->user_id])->value('invite_user_id');
                $data['invite_user_id'] = $store_id?$store_id:0;
            }

			//判断商家账号有没重复
			$seller = Db::name('seller')->where(['seller_name' => $data['seller_name']])->find();
			if ($seller) {
				$this->error('店铺登录账号重复');
			}
			
            $data['business_licence_cert'] = $data['comment_img_file'];
            if (!empty($data['supplier'])) {
                $data['business_date_start'] = $data['supplier']['business_date_start'];
                $data['business_date_end'] = $data['supplier']['business_date_end'];
                unset($data['supplier']);
            }
            $data['business_permanent'] == 1 && $data['business_date_end'] = '长期';
            if (DB::name('store_apply')->add($data)) {
                (Db::name('store_apply')->where('id', $this->apply['id'])->find()) && DB::name('store_apply')->where('id', $this->apply['id'])->delete();
                $this->success('提交成功,请等待审核结果', U('User/index'));
            } else {
                $this->error('服务器繁忙,请联系官方客服');
            }
        }
        if (!empty($this->apply)) {
            $province_name = M('region')->where(array('id' => $this->apply['company_province']))->getField('name');
            $city_name     = M('region')->where(array('id' => $this->apply['company_city']))->getField('name');
            $district_name = M('region')->where(array('id' => $this->apply['company_district']))->getField('name');
            $this->assign('area', $province_name . $city_name . $district_name);
            $this->assign('apply', $this->apply);
        }
        //主营类目
        $this->assign('store_class', M('store_class')->select());
        return $this->fetch();
    }

    //入驻协议
    public function agreement()
    {
        return $this->fetch();
    }

    /**
     * 检验信息
     */
    public function checkBasicInfo()
    {
        $name = I('post.name','');
        $value = I('post.value','');
        if (empty($value)) $this->ajaxReturn(['status'=>1]);
        if (Db::name('store_apply')->where(["$name"=>$value,'user_id' => ['neq',$this->user_id]])->count() > 0) {
            $this->ajaxReturn(['status'=>-1]);
        }
        $this->ajaxReturn(['status'=>1]);
    }
}


