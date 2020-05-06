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

namespace app\home\controller;
use think\Db;
class Newjoin extends Base
{
    public $user_id;
    public $apply = array();

    public function _initialize()
    {
        parent::_initialize();
        $this->user_id = cookie('user_id');
        if (empty($this->user_id) && ACTION_NAME != 'index') {
            $this->redirect(U('User/login'));
        } else if (!empty($this->user_id)) {
            $this->apply = Db::name('store_apply')->where(array('user_id' => $this->user_id))->find();
            $store = Db::name('store')->where(array('user_id' => $this->user_id))->find();
            if ($store){
                $this->redirect(U("Store/index",['store_id'=>$store['store_id']]));
            }
        }
        $user = get_user_info($this->user_id);
        if ($user && empty($user['password'])) {
            $this->error('您使用的是第三方账号登陆，请先设置账号密码', U('User/password'));
        }
        $this->assign('user', $user);
    }

    public function index()
    {
        return $this->fetch();
    }

    public function contact()
    {
        if ($this->apply['apply_state'] == 1) $this->redirect(U('Newjoin/apply_info'));
        if (IS_POST) {
            $data = I('post.');
            if (empty($this->apply)) {
                $data['user_id'] = $this->user_id;
//                $data['add_time'] = time();
                if (M('store_apply')->add($data)) {
                    if ($data['apply_type'] == 0) {
                        $this->redirect(U('Newjoin/basic_info'));
                    } else {
                        $this->redirect(U('Newjoin/basic_info', array('apply_type' => 1)));
                    }
                } else {
                    $this->error('服务器繁忙,请联系官方客服');
                }
            } else {
                M('store_apply')->where(array('user_id' => $this->user_id))->save($data);
                $this->redirect(U('Newjoin/basic_info', array('apply_type' => $data['apply_type'])));
            }
        }
        $this->assign('apply', $this->apply);
        return $this->fetch();
    }

    public function basic_info()
    {
         if ($this->apply['apply_state'] == 1) $this->redirect(U('Newjoin/apply_info'));
        if (IS_POST) {
            $data = I('post.supplier/a');
            empty($data['business_date_end']) && $data['business_date_end']='长期';
            M('store_apply')->where(array('user_id' => $this->user_id))->save($data);
            $this->redirect(U('Newjoin/seller_info'));
        }
        $rate_list = config('rate_list');
        $company_type = config('company_type');
        $this->assign('company_type', $company_type);
        $this->assign('apply', $this->apply);
        $this->assign('rate_list', $rate_list);
        $province = M('region')->where(array('parent_id' => 0))->select();
        $this->assign('province', $province);
        if (!empty($this->apply['store_class_ids'])) {  //经营类目
            $goods_cates = M('goods_category')->getField('id,name,commission');
            $store_class_ids = unserialize($this->apply['store_class_ids']);
            foreach ($store_class_ids as $val) {
                $cat = explode(',', $val);
                $bind_class_list[] = array('class_1' => $goods_cates[$cat[0]]['name'], 'class_2' => $goods_cates[$cat[1]]['name'],
                    'class_3' => $goods_cates[$cat[2]]['name'] . '(分佣比例：' . $goods_cates[$cat[2]]['commission'] . '%)', 'value' => $val
                );
            }
            $this->assign('bind_class_list', $bind_class_list);
        }
        if (!empty($this->apply['company_province'])) {
            $city = Db::name('region')->where(array('parent_id' => $this->apply['company_province']))->select();
            $district = Db::name('region')->where(array('parent_id' => $this->apply['company_city']))->select();
            $this->assign('city',$city);
            $this->assign('district',$district);
        }
        if (!empty($this->apply['bank_province'])) {
            $bank_city = Db::name('region')->where(array('parent_id' => $this->apply['bank_province']))->select();
            $this->assign('bank_city',$bank_city);
        }
        $apply_type = I('apply_type', 0);
        if ($apply_type == 1 || $this->apply['apply_type'] == 1) {
            $this->assign('store_class', M('store_class')->getField('sc_id,sc_name'));
            $this->assign('goods_category', M('goods_category')->where(array('parent_id' => 0))->getField('id,name'));
            $this->assign('province', M('region')->where(array('parent_id' => 0, 'level' => 1))->select());
            return $this->fetch('basic');
        } else {
            return $this->fetch();
        }
    }

    public function agreement()
    {

        if (empty($this->user_id)) $this->success('请先登录', U('Home/User/login'));

        if (!empty($this->apply)) {
            if ($this->apply['apply_state'] == 1) {
                $this->redirect(U('Newjoin/apply_info'));
            } else if ($this->apply['apply_state'] == 0 && empty($this->apply['company_name'])) {
                $this->redirect(U('Newjoin/basic_info'));
            } else if (empty($this->apply['store_name'])) {
                if ($this->apply['apply_type'] == 1) {
                    $this->redirect(U('Newjoin/contact'));
                } else {
                    $this->redirect(U('Newjoin/seller_info'));
                }
            } else if ($this->apply['apply_state'] == 0 && empty($this->apply['business_licence_cert'])) {
                $this->redirect(U('Newjoin/remark'));
            } else {
                $this->redirect(U('Newjoin/apply_info'));
            }
        }
        if (IS_POST) {
            $this->redirect(U('Newjoin/contact'));
        }
        $agreement =  Db::name('system_article')->where('doc_code','open_store')->find();
        $this->assign('agreement',$agreement);
        return $this->fetch();
    }

    public function seller_info()
    {
        if ($this->apply['apply_state'] == 1) $this->redirect(U('Newjoin/apply_info'));
        if (IS_POST) {
            $data = I('post.');
			//判断商家账号有没重复
			$seller = Db::name('seller')->where(['seller_name' => $data['seller_name']])->find();
			if ($seller) {
				$this->error('店铺登录账号重复');
			}
            $data['add_time'] = time();  //全部填写完才记录申请时间
            if (!empty($data['store_class_ids'])) {
                $data['store_class_ids'] = serialize($data['store_class_ids']);
            }else{
                $this->error('请填写经营类目');
            }
            if ($this->apply['apply_type'] == 1) {
                //个人申请
                if (empty($this->apply['legal_identity_cert']) || empty($this->apply['store_person_cert'])) {
                    foreach ($_FILES as $k => $v) {
                        if (empty($v['tmp_name'])) {
                            $this->error('请上传必要证件');
                        }
                    }

                    $files = $this->request->file();
                    $savePath = UPLOAD_PATH.'store/cert/'.date('Y-m-d').'/';
                    if (!($_exists = file_exists($savePath))) {
                        $isMk = mkdir($savePath,0777,true);
                    }
                    $image_upload_limit_size = config('image_upload_limit_size');
                    if (is_array($files)) {
                        foreach ($files as $key => $file) {
                            $info = $file->rule(function ($file) {    
                                return  md5(mt_rand()); // 使用自定义的文件保存规则
                            })->validate(['size' => $image_upload_limit_size, 'ext' => 'jpg,png,gif,jpeg'])->move($savePath, true);
                            if ($info) {
                                $filename = $info->getFilename();
                                $new_name = '/'.$savePath.$filename;
                                $data[$key] = $new_name;
                            } else {
                                $this->error($file->getError());//上传错误提示错误信息
                            }
                        }
                    }
                }
            }

            $data['apply_state'] = 0;
            M('store_apply')->where(array('user_id' => $this->user_id))->save($data);
            if ($this->apply['apply_type'] == 1) {
                $this->redirect(U('Newjoin/apply_info'));
            } else {
                $this->redirect(U('Newjoin/remark'));
            }
        }
        $this->assign('apply', $this->apply);
        $this->assign('store_class', M('store_class')->getField('sc_id,sc_name'));
        if (!empty($this->apply['store_class_ids'])) {
            $goods_cates = M('goods_category')->getField('id,name,commission');
            $store_class_ids = unserialize($this->apply['store_class_ids']);
            foreach ($store_class_ids as $val) {
                $cat = explode(',', $val);
                $bind_class_list[] = array('class_1' => $goods_cates[$cat[0]]['name'], 'class_2' => $goods_cates[$cat[1]]['name'],
                    'class_3' => $goods_cates[$cat[2]]['name'] . '(分佣比例：' . $goods_cates[$cat[2]]['commission'] . '%)', 'value' => $val
                );
            }
            $this->assign('bind_class_list', $bind_class_list);
        }
        $this->assign('goods_category', M('goods_category')->where(array('parent_id' => 0))->getField('id,name'));
        $this->assign('province', M('region')->where(array('parent_id' => 0, 'level' => 1))->select());
        if (!empty($this->apply['bank_province'])) {
            $this->assign('city', M('region')->where(array('parent_id' => $this->apply['bank_province']))->select());
        }
        return $this->fetch();
    }

    public function query_progress()
    {
        return $this->fetch();
    }

    public function remark()
    {
        if ($this->apply['apply_state'] == 1) $this->redirect(U('Newjoin/apply_info'));
        if (IS_POST) {
            $data = I('post.');
            $data['apply_state'] = 0;//每次提交资料回到待审核状态
            M('store_apply')->where(array('user_id' => $this->user_id))->save($data);
            $this->success('提交成功', U('Newjoin/apply_info'));
        }

        $this->assign('apply', $this->apply);
        return $this->fetch();
    }

    public function apply_info()
    {
        $this->assign('apply', $this->apply);
        if (IS_POST) {
            $paying_amount_cert = I('paying_amount_cert');
            $paying_amount_cert['apply_state'] = 0;
            if (empty($paying_amount_cert)) {
                $this->error('请上传支付凭证');
            } else {
                M('store_apply')->where(array('user_id' => $this->user_id))->save(array('paying_amount_cert' => $paying_amount_cert));
                $this->success('提交成功');
            }
        }
        return $this->fetch();
    }

    public function check_company()
    {
        $company_name = I('company_name');
        if (empty($company_name)) exit('fail');
        if ($company_name && M('store_apply')->where(array('company_name' => $company_name, 'user_id' => array('neq', $this->user_id)))->count() > 0) {
            exit('fail');
        }
        exit('success');
    }

    public function check_store()
    {
        $store_name = I('store_name');
        if (empty($store_name)) exit('fail');
        if (M('store_apply')->where(array('store_name' => $store_name))->count() > 0) {
            exit('fail');
        }
        exit('success');
    }

    public function check_seller()
    {
        $seller_name = I('seller_name');
        if (empty($seller_name)) exit('fail');
        if (M('seller')->where(array('seller_name' => $seller_name))->count() > 0) {
            exit('fail');
        }
        exit('success');
    }

    public function question()
    {
        $cat_id = I('cat_id/d');
        $article = M('article')->where("cat_id", $cat_id)->select();
        if ($article) {
            $parent = M('article_cat')->where(array('cat_id' => $cat_id))->find();
            $this->assign('cat_name', $parent['cat_name']);
            $this->assign('article', $article[0]);
            $this->assign('article_list', $article);
        }
        return $this->fetch('article/detail');
    }
}