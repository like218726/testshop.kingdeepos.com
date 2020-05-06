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

namespace app\seller\controller;

use app\seller\logic\StoreLogic;
use think\Page;
use think\Db;

class Store extends Base
{
    public function store_info()
    {
        $apply = M('store_apply')->where("user_id", $this->storeInfo['user_id'])->find();

        $bind_class_list = M('store_bind_class')->where("store_id", STORE_ID)->select();
        $goods_class = M('goods_category')->getField('id,name');
        for ($i = 0, $j = count($bind_class_list); $i < $j; $i++) {
            $bind_class_list[$i]['class_1_name'] = $goods_class[$bind_class_list[$i]['class_1']];
            $bind_class_list[$i]['class_2_name'] = $goods_class[$bind_class_list[$i]['class_2']];
            $bind_class_list[$i]['class_3_name'] = $goods_class[$bind_class_list[$i]['class_3']];
        }
        $region = Db::name('region')->getField('id,name');
        $this->assign('apply',$apply);
        $this->assign('region',$region);
        $this->assign('store',$this->storeInfo);
        $this->assign('bind_class_list', $bind_class_list);
        return $this->fetch();
    }

    public function store_setting()
    {
        $this->storeInfo = M('store')->where("store_id", STORE_ID)->find();

        if ($this->storeInfo) {
            $this->store->store_address2 = $this->store->getData('store_address'); //获取原始数据

            $grade = M('store_grade')->where("sg_id", $this->storeInfo['grade_id'])->find();
            $this->storeInfo['grade_name'] = $grade['sg_name'];
            $province = M('region')->where(array('parent_id' => 0))->select();
            $city = M('region')->where(array('parent_id' => $this->storeInfo['province_id']))->select();
            $area = M('region')->where(array('parent_id' => $this->storeInfo['city_id']))->select();
            $this->assign('province', $province);
            $this->assign('city', $city);
            $this->assign('area', $area);
        }
        return $this->fetch();
    }

    public function setting_save()
    {
        $data = I('post.');
        $store_domain = $data['store_domain'];
        if($store_domain){
            
           ($store_domain === 'www') && $this->error("店铺二级域名不能设置为www", U('Store/store_setting'));
            $hostDomain = strtolower($_SERVER['HTTP_HOST']);
            $hosts = explode('.',$hostDomain);
            if($store_domain == $hosts[0]) $this->error("店铺二级域名不能跟主域名相同", U('Store/store_setting'));
              
            $domain_where['store_domain'] =$store_domain;
            if(STORE_ID){
               $exists_store_domain = M('Store')->where(['store_domain'=>$store_domain])->where('store_id', '<>', STORE_ID)->getField('store_domain');
            }else{
                $exists_store_domain = M('Store')->where(['store_domain'=>$store_domain])->getField('store_domain');
            }
            $exists_store_domain && $this->error("已经有相同二级域名存在,请重新设置", U('Store/store_setting'));
        } 
        
        if ($_POST['act'] == 'update') {
            if (!file_exists('.' . $data['themepath'] . '/style/' . $data['store_theme'] . '/images/preview.jpg')) {
                respose(array('status' => -1, 'msg' => '缺少模板文件'));
            }
            if (M('store')->where(["store_id"=>STORE_ID])->save($data)) {
                respose(array('status' => 1));
            } else {
                respose(array('status' => -1, 'msg' => '没有修改模板'));
            }
        } else {
            if (M('store')->where(["store_id"=>STORE_ID])->save($data)) {
                $this->success("操作成功", U('Store/store_setting'));
            } else {
                $this->error("没有修改数据", U('Store/store_setting'));
            }
        }
    }

    public function store_slide()
    {
        $store_slide = $store_slide_url = array();
        if (IS_POST) {
            $store_slide = I('post.store_slide/a');
            $store_slide_url = I('post.store_slide_url/a');
            $store_slide = implode(',', $store_slide);
            $store_slide_url = implode(',', $store_slide_url);
            M('store')->where("store_id", STORE_ID)->save(array('store_slide' => $store_slide, 'store_slide_url' => $store_slide_url));
            $this->success("操作成功", U('Store/store_slide'));
            exit;
        }
        if ($this->storeInfo['store_slide']) {
            $store_slide = explode(',', $this->storeInfo['store_slide']);
            $store_slide_url = explode(',', $this->storeInfo['store_slide_url']);
        }
        $this->assign('store_slide', $store_slide);
        $this->assign('store_slide_url', $store_slide_url);
        return $this->fetch();
    }

    public function mobile_slide()
    {
        $store_slide = $store_slide_url = array();
        if (IS_POST) {
            $store_slide = I('post.store_slide/a');
            $store_slide_url = I('post.store_slide_url/a');
            $store_slide = implode(',', $store_slide);
            $store_slide_url = implode(',', $store_slide_url);
            M('store')->where("store_id", STORE_ID)->save(array('mb_slide' => $store_slide, 'mb_slide_url' => $store_slide_url));
            $this->success("操作成功", U('Store/mobile_slide'));
            exit;
        }
        if ($this->storeInfo['mb_slide']) {
            $store_slide = explode(',', $this->storeInfo['mb_slide']);
            $store_slide_url = explode(',', $this->storeInfo['mb_slide_url']);
        }
        $this->assign('store_slide', $store_slide);
        $this->assign('store_slide_url', $store_slide_url);
        return $this->fetch();
    }

    public function store_theme()
    {
        $template = include APP_PATH . 'seller/conf/style_inc.php';
        $theme = include APP_PATH . 'home/html.php';
        $storeGrade = M('store_grade')->where("sg_id", $this->storeInfo['grade_id'])->find();
        $this->assign('static_path', $theme['view_replace_str']['__STATIC__']);
        if($storeGrade['sg_template_limit']>0)
            $template=array_slice($template,0,$storeGrade['sg_template_limit']); //限制模板使用数量
        $this->assign('template', $template);
        return $this->fetch();
    }

    public function bind_class_list()
    {
        $where=[];
        $goods_class = Db::name('goods_category')->alias('gc')->getField('gc.id,gc.*');
        if ($this->store['bind_all_gc'] == 0){
            $where['store_id']=STORE_ID;
            $bind_class_list = Db::name('store_bind_class')->where($where)->select();
            $count = count($bind_class_list);
            for ($i = 0, $j = $count; $i < $j; $i++) {
                $bind_class_list[$i]['class_1_name'] = $goods_class[$bind_class_list[$i]['class_1']]['name'];
                $bind_class_list[$i]['class_2_name'] = $goods_class[$bind_class_list[$i]['class_2']]['name'];
                $bind_class_list[$i]['class_3_name'] = $goods_class[$bind_class_list[$i]['class_3']]['name'];
                $bind_class_list[$i]['commission']  = $goods_class[$bind_class_list[$i]['class_3']]['commission'];
            }
        }else{   //自营店铺
            $goods_class1 = Db::name('goods_category')->alias('gc')->where(['level'=>1])->getField('gc.id,gc.*');
            $goods_class2 = Db::name('goods_category')->alias('gc')->where(['level'=>2])->getField('gc.id,gc.*');
            $goods_class3 = Db::name('goods_category')->alias('gc')->where(['level'=>3])->getField('gc.id,gc.*');
                foreach ($goods_class1 as $k1 => $clv1) {
                    foreach ($goods_class2 as $k2 => $clv2) {
                        if ($clv2['parent_id'] == $k1) {
                            foreach ($goods_class3 as $k3 => $clv3) {
                                if ($clv3['parent_id'] == $k2) {
                                    $bind_class_list[$k3] = [
                                        "store_id" =>STORE_ID,
                                        "commission" =>$clv3['commission'],
                                        "class_1" =>$k1,
                                        "class_2" =>$k2,
                                        "class_3" =>$k3,
                                        "state" =>1,
                                        'class_1_name' => $clv1['name'],
                                        'class_2_name' => $clv2['name'],
                                        'class_3_name' => $clv3['name'],
                                    ];
                                }
                            }
                        }
                    }
                }
        }
        $this->assign('bind_class_list', $bind_class_list);
        $this->assign('store', $this->store);
        return $this->fetch();
    }

    public function get_bind_class()
    {
        $cat_list = M('goods_category')->where("parent_id = 0")->select();
        $this->assign('cat_list', $cat_list);
        if (IS_POST) {
            $data = I('post.');
            $where = ['class_3' => $data['class_3'], 'store_id' => STORE_ID];
            if (M('store_bind_class')->where($where)->count() > 0) {
                respose(array('status' => -1, 'msg' => '您已申请过该类目'));
            }
            $data['store_id'] = STORE_ID;
            $data['commis_rate'] = M('goods_category')->where("id", $data['class_3'])->getField('commission');
            if (M('store_bind_class')->add($data)) {
                respose(array('status' => 1));
            } else {
                respose(array('status' => -1, 'msg' => '操作失败'));
            }
        }
        return $this->fetch();
    }

    public function bind_class_del()
    {
        $data = I('post.');
        $r = M('store_bind_class')->where(array('bid' => $data['bid']))->delete();
        if ($r) {
            $res = array('status' => 1);
        } else {
            $res = array('status' => -1, 'msg' => '操作失败');
        }
        respose($res);
    }

    public function navigation_list()
    {
        $res = Db::name('store_navigation')->where("sn_store_id", STORE_ID)->order('sn_sort')->page($_GET['p'] . ',10')->select();
        if ($res) {
            foreach ($res as $val) {
                $val['sn_new_open'] = $val['sn_new_open'] > 0 ? '开启' : '关闭';
                $val['sn_is_show'] = $val['sn_is_show'] > 0 ? '是' : '否';
                $list[] = $val;
            }
        }
        $this->assign('list', $list);
        $count = Db::name('store_navigation')->where("sn_store_id", STORE_ID)->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $this->assign('page', $show);
        return $this->fetch();
    }

    public function navigation()
    {
        $sn_id = I('sn_id/d', 0);
        if ($sn_id > 0) {
            $info = M('store_navigation')->where("sn_id", $sn_id)->find();
            $this->assign('info', $info);
        }
        return $this->fetch();
    }

    public function navigationHandle()
    {
        $data = I('post.');
        if ($data['act'] == 'del') {
            $r = M('store_navigation')->where('sn_id', $data['sn_id'])->delete();
            if ($r) exit(json_encode(1));
        }
        $data['sn_add_time'] = time();
        if (empty($data['sn_id'])) {
            $data['sn_store_id'] = STORE_ID;
            $r = M('store_navigation')->add($data);
        } else {
            $r = M('store_navigation')->where('sn_id', $data['sn_id'])->save($data);
        }
        if ($r) {
            $this->success("操作成功", U('Store/navigation_list'));
        } else {
            $this->error("操作失败", U('Store/navigation_list'));
        }
    }

    /*public function suppliers_list()
    {
        $map = array();
		$map['store_id'] = STORE_ID;
        $suppliers_name = trim(I('suppliers_name'));
        if ($suppliers_name) {
            $map['suppliers_name'] = array('like', "%$suppliers_name%");
        }
        $suppliers_list = M('suppliers')->where($map)->select();
        $this->assign('suppliers_list', $suppliers_list);
        return $this->fetch();
    }

    public function suppliers_info()
    {
        if (IS_POST) {
            $data = I('post.');
            $data['store_id'] = STORE_ID;
            if ($data['act'] == 'del') {
                Db::name('goods')->where(array('suppliers_id' => $data['suppliers_id']))->update(['suppliers_id'=>0]);
                $r = M('suppliers')->where(array('suppliers_id' => $data['suppliers_id']))->delete();
            } elseif ($data['suppliers_id'] > 0) {
                $r = M('suppliers')->where(array('suppliers_id' => $data['suppliers_id']))->save($data);
            } else {
                $r = M('suppliers')->add($data);
            }
            if ($r) {
                $this->ajaxReturn(1, 'json');
            } else {
                $this->ajaxReturn(0, 'json');
            }
        }
        $suppliers_id = I('suppliers_id/d');
        if ($suppliers_id) {
            $suppliers = M('suppliers')->where(array('suppliers_id' => $suppliers_id))->find();
            $this->assign('suppliers', $suppliers);
        }
        return $this->fetch();
    }*/

    public function goods_class()
    {
        $Model = M('store_goods_class');
        $res = $Model->where(['store_id' => STORE_ID])->select();
        $cat_list = $this->getTreeClassList(2, $res);
        $this->assign('cat_list', $cat_list);
        return $this->fetch();
    }

    public function goods_class_info()
    {
        $cat_id = I('get.cat_id/d', 0);
        $info['parent_id'] = I('get.parent_id/d', 0);
        if ($cat_id > 0) {
            $info = M('store_goods_class')->where("cat_id", $cat_id)->find();
        }
        $this->assign('info', $info);
        $parent = M('store_goods_class')->where(['parent_id' => 0, 'store_id' => STORE_ID])->select();
        $this->assign('parent', $parent);
        return $this->fetch();
    }

    public function goods_class_save()
    {
        $data = I('post.');
        if ($data['act'] == 'del') {
            $r = M('store_goods_class')->where(['cat_id|parent_id' => $data['cat_id']])->delete();
        } else {
            if (empty($data['cat_id'])) {
                $data['store_id'] = STORE_ID;
                $r = M('store_goods_class')->add($data);
            } else {
                $r = M('store_goods_class')->where('cat_id', $data['cat_id'])->save($data);
            }
        }
        if ($r) {
            $res = array('status' => 1);
        } else {
            $res = array('status' => -1, 'msg' => '操作失败');
        }
        respose($res);
    }

    public function store_im()
    {
        $chat_msg = M('chat_msg')->select();
        $this->assign('chat_msg', $chat_msg);
        return $this->fetch();
    }

    function store_collect()
    {
        $keywords = I('keywords');
        $map['store_id'] = STORE_ID;
        if (!empty($keywords)) {
            $map['user_name'] = array('like', "%$keywords%");
        }
        $count = M('store_collect')->where($map)->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $collect = M('store_collect')->where(array('store_id' => STORE_ID))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $show);
        $this->assign('collect', $collect);
        return $this->fetch();
    }

    public function store_decoration()
    {
        if (IS_POST) {
            //店铺装修设置
            $data = I('post.');
            M('store')->where(array('store_id' => STORE_ID))->save($data);
            $this->success("操作成功", U('Store/store_decoration'));
            exit;
        }
        $decoration = M('store_decoration')->where(array('store_id' => STORE_ID))->find();
        if (empty($decoration)) {
            $decoration = array('decoration_name' => '默认装修', 'store_id' => STORE_ID);
            $decoration['decoration_id'] = M('store_decoration')->add($decoration);
        }
        $this->assign('decoration', $decoration);
        return $this->fetch();
    }

    /**
     * 递归 整理分类
     *
     * @param int $show_deep 显示深度
     * @param array $class_list 类别内容集合
     * @param int $deep 深度
     * @param int $parent_id 父类编号
     * @param int $i 上次循环编号
     * @return array $show_class 返回数组形式的查询结果
     */
    private function getTreeClassList($show_deep = 2, $class_list, $deep = 1, $parent_id = 0, $i = 0)
    {
        static $show_class = array();//树状的平行数组
        if (is_array($class_list) && !empty($class_list)) {
            $size = count($class_list);
            if ($i == 0) $show_class = array();//从0开始时清空数组，防止多次调用后出现重复
            for ($i; $i < $size; $i++) {//$i为上次循环到的分类编号，避免重新从第一条开始
                $val = $class_list[$i];
                $cat_id = $val['cat_id'];
                $cat_parent_id = $val['parent_id'];
                if ($cat_parent_id == $parent_id) {
                    $val['deep'] = $deep;
                    $show_class[] = $val;
                    if ($deep < $show_deep && $deep < 2) {//本次深度小于显示深度时执行，避免取出的数据无用
                        $this->getTreeClassList($show_deep, $class_list, $deep + 1, $cat_id, $i + 1);
                    }
                }
                //if($cat_parent_id > $parent_id) break;//当前分类的父编号大于本次递归的时退出循环
            }
        }
        return $show_class;
    }

    /**
     * 三级分销设置
     */
    public function distribut()
    {
        // 每个店铺有一个分销 记录
        $store_distribut = M('store_distribut')->where("store_id", STORE_ID)->find();
        $result_url = I('result_url', 'Store/distribut');
        if (IS_POST) {
            $distribut =  tpCache('distribut');
            $distribut['open_store_time'] = input('open_store_time/d',0);
            tpCache('distribut',$distribut);
            $Model = M('store_distribut');
            $data = input('post.');
            $data['store_id'] = STORE_ID;
            if ($store_distribut)
                $Model->update($data);
            else
                $Model->insert($data);
            $this->success("操作成功", U($result_url));
            exit;
        }
        $distribut_set_by = M('config')->where("name = 'distribut_set_by'")->getField('value');
        $this->assign('distribut_set_by', $distribut_set_by);
        $this->assign('config', $store_distribut);
        return $this->fetch();
    }

    /*
     * 设置店铺经纬度
     * */
    public function getpoint(){
        if(IS_POST){
            $coordinate  = trim(I('coordinate/s'));
            $coordinate = explode(',',$coordinate);  //以,炸开获得经纬度
            if(empty($coordinate[0]) ||  $coordinate[0]==0){
                $this->success('请输入正确的经度');
            }
            if(empty($coordinate[1]) ||  $coordinate[1]==0){
                $this->success('请输入正确的纬度');
            }
            $data['longitude'] = $coordinate[0];
            $data['latitude'] = $coordinate[1];
            $res=M('store')->where(array('store_id' => STORE_ID))->save($data);  //修改
            if($res)
                $this->success('成功');
                $this->success('失败');
            exit();
        }
        $coordinate = M('store')->field('longitude,latitude')->where("store_id", STORE_ID)->find();
        $this->assign('coordinate', $coordinate);
        return $this->fetch();
    }

    /**
     * 申请升级列表
     */
    public function store_reopen()
    {
        $StoreReopenModel =new  \app\common\model\StoreReopen();
        $count = $StoreReopenModel->where(['re_store_id'=>STORE_ID])->count();
        $page = new Page($count,20);
        $StoreReopenObj = $StoreReopenModel->where(['re_store_id'=>STORE_ID])->order('re_id desc')->limit($page->listRows,$page->firstRow)->select();
        if ($StoreReopenObj){
            $store_reopen = collection($StoreReopenObj)->append(['reopen_state'])->toArray();
        }
        $info['re_end_time']=$re_end_time = $this->store['store_end_time'];  //到期时间
        $info['earlier']=$earlier= 30; //可提前申请时间
        $info['start_apply_time'] = $re_end_time-($earlier*60*60*24);  //继续续期开始时间
        $reopen_count = Db::name('store_reopen')->where(['re_store_id'=>STORE_ID,'re_state'=>['notIn','-1,2']])->count();  //店铺等级
        if($info['start_apply_time'] < time() && $reopen_count < 1){    //是否可续签时间
            $info['apply_status']= true;
        }else{
            $info['apply_status']= false;
        }
        $this->assign('page', $page->show());
        $this->assign('info', $info);
        $this->assign('store_reopen', $store_reopen);
        return $this->fetch();
    }

    /**
     * 店铺当前等级，获取所有等级
     */
    public function getStoreGrade(){
        $store_grade_id =$this->storeInfo['grade_id'];
        $earlier= 30; //可提前申请时间
        $start_apply_time = $this->storeinfo['store_end_time']-($earlier*60*60*24);  //继续续期开始时间
        if($start_apply_time < time()){    //是否可续签时间
            $store_grade['apply_status']= true;
        }else{
            $store_grade['apply_status']=false;
        }
        $grade = Db::name('store_grade')->alias('sg')->order('sg_id')->getField('sg_id,sg.*');
        $store_grade = $grade["$store_grade_id"];
        $this->assign('store_grade',$store_grade);
        $this->assign('grade',$grade);
        return $this->fetch();
    }

    /**
     * 申请升级店铺等级
     */
    public function applyStoreGrade(){
        $post_data = I('post.');
        $StoreLogic =new StoreLogic();
        $StoreLogic ->setStoreInfo($this->storeInfo);
        $res = $StoreLogic ->editStoreReopen($post_data);
        $this->ajaxReturn($res);
    }

    /**
     * 申请升级店铺等级
     */
    public function reopen_info(){
        $re_id = I('id/d',0);
        !$re_id && $this->error('非法操作！！');
        $StoreReopenModel =new  \app\common\model\StoreReopen();
        $reopen = $StoreReopenModel::get(['re_id'=>$re_id,'re_store_id'=>STORE_ID]);
        $data = $reopen->append(['reopen_state'])->toArray();
        $this->assign('data',$data);
        return $this->fetch();
    }

    /*
     *关联版式
     */
    public function plate_list(){
        $sql='store_id='.STORE_ID;
        if(I('get.p_name')){
            $name=I('get.p_name');
            $sql.=' and plate_name like "%'.$name.'%"';
            $this->assign('p_name',$name);
        }
        if(is_numeric(I('get.p_position'))){
            $type=I('get.p_position');
            $sql.=' and plate_position='.$type;
            $this->assign('p_position',$type);
        }
        $list=M('store_plate')->where($sql)->select();
        $this->assign('list',$list);
        return $this->fetch();
    }
    
    public function plate_edit(){
        $id=I('get.plate_id');
        if($id){
            $info=M('store_plate')->where('plate_id='.$id)->find();
            $this->assign('info',$info);
        }
        return $this->fetch();
    }

    //添加 更新操作
    public function plate_handle(){
        $data=I('post.');
        $id=$data['plate_id'];
        $data['store_id']=STORE_ID;
        if(!$id){
            unset($data['plate_id']);
            $res=M('store_plate')->add($data);
        }
        if($id){
            $res=M('store_plate')->where('plate_id='.$id)->save($data);
        }

        if($res){
            $this->success("操作成功", U('Store/plate_list'));
        }else{
            $this->error("操作失败", U('Store/plate_list'));
        }
    }

    public function plate_delete(){
        $plate_id=I('post.plate_id');
        $res=Db::name('store_plate')->where('plate_id='.$plate_id)->delete();
        if($res){
            echo "1";
        }else{
            echo "0";
        }
    }

    //移动端店铺首页
    public function template_index(){
        return $this->fetch();
    }

    //移动端店铺自定义页面
    public function mobile_template(){
        $list=Db::name('mobile_template')->where('store_id='.STORE_ID)->field('id,template_name,add_time,is_index')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    //店铺首页编辑页面
    public function template_edit(){
        $is_index=I('get.is_index');
        
        $this->assign('is_index',$is_index);
        return $this->fetch();
    }

    //店铺首页数据添加
    public function add_data(){
        $param=I('post.');
        $html=$param['html'];
        $html=str_replace("\n"," ",$html);
        //dump(STORE_ID);exit();
        //dump($param);exit();

        $data['add_time']=time();
        $data['store_id']=STORE_ID;
        $data['template_html']=$html;
        $data['block_info']=$param['info'];
        $data['template_name']=$param['template_name'];
        $data['store_id']=STORE_ID;

        if($param['is_index']){
            $data['is_index']=1;
        }

        $id=I('post.edit_id');

        if($id){    //若传递过来的有id则作更新操作
            $res=M('mobile_template')->where('id='.$id)->save($data);
        }else{
            $res=M('mobile_template')->add($data);
        }
  
        //传递id回去防止重复添加 
        if($res){
            if($id){
                echo json_encode($id);
            }else{
                echo json_encode($res);
            }
        }else{
            echo json_encode(0);
        }
    }
	/**
	 * 店铺角色
	 */
    public function common_store_role(){
		$applyStoreRole = Db::name('apply_store_role')->where(['store_id' => $this->store['store_id'], 'status' => ['in', '0,2']])->select();
		$this->assign('apply_store_role', $applyStoreRole);
        return $this->fetch();
    }
	
	/**
	 * 申请店铺角色
	 */
    public function common_apply_store_role(){
        $storeRole = I('store_role', 0);  //1销售商；2供应商
		if ($storeRole == 0) {
			$this->ajaxReturn(['status' => 0, 'msg' => '请选择一个店铺角色']);
		}
		if ($storeRole == 1 && $this->store['is_dealer'] == 1) {
			$this->ajaxReturn(['status' => 0, 'msg' => '你已经是销售商了']);
		}
		if ($storeRole == 2 && $this->store['is_supplier'] == 1) {
			$this->ajaxReturn(['status' => 0, 'msg' => '你已经是供应商了']);
		}
		$apply = Db::name('apply_store_role')->where(['store_id' => $this->store['store_id'], 'store_role' => $storeRole])->find();
		if ($apply) {
			if ($apply['status'] == 0) {
				$this->ajaxReturn(['status' => 0, 'msg' => '你的申请已经在审核中']);
			}
			if ($apply['status'] == 1) {
				$this->ajaxReturn(['status' => 0, 'msg' => '你已经通过了该申请']);
			}
			$applyData = [
				'status' => 0,
				'apply_time' => time(),
				'pass_time' => 0,
				'refuse_time' => 0
			];
			Db::name('apply_store_role')->where(['id' => $apply['id']])->update($applyData);
		} else {
			$applyData = [
				'store_id' => $this->store['store_id'],
				'store_role' => $storeRole,
				'status' => 0,
				'apply_time' => time()
			];
			Db::name('apply_store_role')->add($applyData);
		}
		$this->ajaxReturn(['status' => 1, 'msg' => '申请提交成功']);
    }
	
	/**
	 * 销售商列表
	 */
	public function dealer_list()
    {
		$map = [
			'ss.supplier_store_id' => STORE_ID,
			'ss.seller_status' => 1,
			'ss.admin_status' => 1,
		];
		$dealerName = I('dealer_name','');
		$dealerName && $map['s.store_name'] = ['like', "%$dealerName%"];
		
		$count = Db::name('store_supplier')
			->alias('ss')
			->join('store s', 'ss.dealer_store_id=s.store_id', 'left')
			->where($map)
			->count();
		$Page = new Page($count, 10);
        $show = $Page->show();
		$list = Db::name('store_supplier')
			->alias('ss')
			->join('store s', 'ss.dealer_store_id=s.store_id', 'left')
			->where($map)
			->limit($Page->firstRow . ',' . $Page->listRows)
			->select();
		$this->assign('list', $list);
		$this->assign('page', $show);
        return $this->fetch();
    }
	
	/**
	 * 销售商审理列表
	 */
	public function dealer_handle_list()
    {
		$map = [
			'ss.supplier_store_id' => STORE_ID,
			'ss.direction' => 1,
			'ss.seller_status' => ['neq', 2],
			'ss.admin_status' => ['neq', 1],
		];
		
		$count = Db::name('store_supplier')
			->alias('ss')
			->join('store s', 'ss.dealer_store_id=s.store_id', 'left')
			->where($map)
			->count();
		$Page = new Page($count, 10);
        $show = $Page->show();
		$list = Db::name('store_supplier')
			->alias('ss')
			->join('store s', 'ss.dealer_store_id=s.store_id', 'left')
			->where($map)
			->limit($Page->firstRow . ',' . $Page->listRows)
			->select();
		$this->assign('list', $list);
		$this->assign('page', $show);
        return $this->fetch();
    }
	
	/**
	 * 销售商/供应商备注编辑页
	 */
	public function edit_remark()
    {
		if (IS_POST) {
			$reamrk = I('remark');
			$role = I('role');
			$id = I('id');
			if (!$role || !$id) {
				$this->ajaxReturn(['status' => 0, 'msg' => '参数错误']);
			}
			$key = $role . '_remark';
			Db::name('store_supplier')->where('id', $id)->update([$key => $reamrk]);
			$this->ajaxReturn(['status' => 1, 'msg' => '编辑成功']);
		}
        return $this->fetch();
    }
	
	/**
	 * 供应商申请销售商(销售商申请供应商)
	 */
	public function seller_apply()
    {
		if (IS_POST) {
			$storeName = I('store_name', '');
			$remark = I('remark', '');
			$target = I('target');
			if (!$storeName) {
				$this->ajaxReturn(['status' => 0, 'msg' => '店铺名称不能为空']);
			}
			if (!$target) {
				$this->ajaxReturn(['status' => 0, 'msg' => '参数错误']);
			}
			$store = Db::name('store')->where(['store_name' => $storeName])->find();
			if (!$store) {
				$this->ajaxReturn(['status' => 0, 'msg' => '无此店铺名称']);
			}
			if ($store['store_id'] == STORE_ID) {
				$this->ajaxReturn(['status' => 0, 'msg' => '不能向自己申请']);
			}
			if ($store['store_state'] != 1) {
				$this->ajaxReturn(['status' => 0, 'msg' => '此店铺已关闭或在审核中']);
			}
			
			if ($target == 'dealer') {
				//供应商申请销售商
				if (!$store['is_dealer']) {
					$this->ajaxReturn(['status' => 0, 'msg' => '此店铺并不是销售商']);
				}
				$data = [
					'dealer_store_id' => $store['store_id'],
					'supplier_store_id' => STORE_ID
				];
				$bind = Db::name('store_supplier')->where($data)->find();
				if ($bind && ($bind['seller_status'] != 2 && $bind['admin_status'] != 2)) {
				    $this->ajaxReturn(['status' => 0, 'msg' => '你与此店铺的申请正在进行中或已经完成申请']);
				}
				$data['direction'] = 0;
				$data['dealer_remark'] = $remark;
				$data['supplier_remark'] = '';
			} else {
				//销售商申请供应商
				if (!$store['is_supplier']) {
					$this->ajaxReturn(['status' => 0, 'msg' => '此店铺并不是供应商']);
				}
				$data = [
					'dealer_store_id' => STORE_ID,
					'supplier_store_id' => $store['store_id']
				];
				$bind = Db::name('store_supplier')->where($data)->find();
				if ($bind && ($bind['seller_status'] != 2 && $bind['admin_status'] != 2)) {
					$this->ajaxReturn(['status' => 0, 'msg' => '你与此店铺的申请正在进行中或已经完成申请']);
				}
				$data['direction'] = 1;
				$data['supplier_remark'] = $remark;
				$data['dealer_remark'] = '';
			}
			
			$data['seller_status'] = 0;
			$data['admin_status'] = 0;
			$data['seller_apply_time'] = time();
			
			if ($bind) {
				$data['seller_deal_time'] = 0;
				$data['admin_deal_time'] = 0;
				Db::name('store_supplier')->where(['id' => $bind['id']])->add($data);
			} else {
				Db::name('store_supplier')->add($data);
			}
			$this->ajaxReturn(['status' => 1, 'msg' => '已向对方申请，请耐心等待']);
		}
        return $this->fetch();
    }
	
	/**
	 * 供应商的销售商申请列表
	 */
	public function dealer_apply_list()
    {
		$map = [
			'ss.supplier_store_id' => STORE_ID,
			'ss.direction' => 0,
			'ss.admin_status' => ['neq', 1],
		];
		
		$count = Db::name('store_supplier')
			->alias('ss')
			->join('store s', 'ss.dealer_store_id=s.store_id', 'left')
			->where($map)
			->count();
		$Page = new Page($count, 10);
        $show = $Page->show();
		$list = Db::name('store_supplier')
			->alias('ss')
			->join('store s', 'ss.dealer_store_id=s.store_id', 'left')
			->where($map)
			->limit($Page->firstRow . ',' . $Page->listRows)
			->select();
		$this->assign('list', $list);
		$this->assign('page', $show);
        return $this->fetch();
    }

	/**
	 * 供应商列表
	 */
	public function supplier_list()
    {
		$map = [
			'ss.dealer_store_id' => STORE_ID,
			'ss.seller_status' => 1,
			'ss.admin_status' => 1,
		];
		$supplierName = I('supplier_name','');
		$supplierName && $map['s.store_name'] = ['like', "%$supplierName%"];
		
		$count = Db::name('store_supplier')
			->alias('ss')
			->join('store s', 'ss.supplier_store_id=s.store_id', 'left')
			->where($map)
			->count();
		$Page = new Page($count, 10);
        $show = $Page->show();
		$list = Db::name('store_supplier')
			->alias('ss')
			->join('store s', 'ss.supplier_store_id=s.store_id', 'left')
			->where($map)
			->limit($Page->firstRow . ',' . $Page->listRows)
			->select();
		$this->assign('list', $list);
		$this->assign('page', $show);
        return $this->fetch();
    }
	
	/**
	 * 销售商的供应商申请列表
	 */
	public function supplier_apply_list()
    {
		$map = [
			'ss.dealer_store_id' => STORE_ID,
			'ss.direction' => 1,
			'ss.admin_status' => ['neq', 1],
		];
		
		$count = Db::name('store_supplier')
			->alias('ss')
			->join('store s', 'ss.supplier_store_id=s.store_id', 'left')
			->where($map)
			->count();
		$Page = new Page($count, 10);
        $show = $Page->show();
		$list = Db::name('store_supplier')
			->alias('ss')
			->join('store s', 'ss.supplier_store_id=s.store_id', 'left')
			->where($map)
			->limit($Page->firstRow . ',' . $Page->listRows)
			->select();
		$this->assign('list', $list);
		$this->assign('page', $show);
        return $this->fetch();
    }
	
	/**
	 * 供应商审理列表
	 */
	public function supplier_handle_list()
    {
		$map = [
			'ss.dealer_store_id' => STORE_ID,
			'ss.direction' => 0,
			'ss.seller_status' => ['neq', 2],
			'ss.admin_status' => ['neq', 1],
		];
		
		$count = Db::name('store_supplier')
			->alias('ss')
			->join('store s', 'ss.supplier_store_id=s.store_id', 'left')
			->where($map)
			->count();
		$Page = new Page($count, 10);
        $show = $Page->show();
		$list = Db::name('store_supplier')
			->alias('ss')
			->join('store s', 'ss.supplier_store_id=s.store_id', 'left')
			->where($map)
			->limit($Page->firstRow . ',' . $Page->listRows)
			->select();
		$this->assign('list', $list);
		$this->assign('page', $show);
        return $this->fetch();
    }
	
	/**
	 * 商家发起的申请的处理
	 */
	public function seller_apply_handle()
    {
		$act = I('act', '');
		$id = I('id', 0);
		if (!$act || !$id) {
			$this->ajaxReturn(['status' => 0, 'msg' => '参数错误']);
		}
		$apply = Db::name('store_supplier')->where(['id' => $id])->find();
		if ($act == 'del') {
			if ($apply['admin_status'] == 1) {
				$this->ajaxReturn(['status' => 0, 'msg' => '商家间已经关联，无法删除']);
			}
			if (($apply['direction'] == 0 && $apply['supplier_store_id'] == STORE_ID) || ($apply['direction'] == 1 && $apply['dealer_store_id'] == STORE_ID)) {
				//发起人和操作者相同时，才有权删除
				Db::name('store_supplier')->where(['id' => $id])->delete();
				$this->ajaxReturn(['status' => 1, 'msg' => '删除成功']);
			} else {
				$this->ajaxReturn(['status' => 0, 'msg' => '你无权删除此申请信息']);
			}
		} else {
			if (($apply['direction'] == 0 && $apply['dealer_store_id'] == STORE_ID) || ($apply['direction'] == 1 && $apply['supplier_store_id'] == STORE_ID)) {
				//申请商家对象和操作者相同时，才有权同意或拒绝操作
				$data = [
					'seller_deal_time' =>time()
				];
				$act == 'agree' ? $data['seller_status'] = 1 : ($act == 'refuse' ? $data['seller_status'] = 2 : false);
				Db::name('store_supplier')->where(['id' => $id])->update($data);
				$this->ajaxReturn(['status' => 1, 'msg' => '操作成功']);
			} else {
				$this->ajaxReturn(['status' => 0, 'msg' => '你无权做此操作']);
			}
		}
    }
}