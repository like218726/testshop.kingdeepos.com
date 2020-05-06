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
 * $Author: 当燃 2016-01-09
 */

namespace app\mobile\controller;

use think\Db;
use think\Page;
use app\common\logic\wechat\WechatUtil;
use app\common\logic\ColorfulLifeLogin;
use app\common\logic\UsersLogic;
use app\common\logic\MomentsLogic;
use think\Controller;
use app\common\validate\Moments;

class Dynamics extends MobileBase
{
    public $user_id = 0;
    public $user = array();
    
   /*
    * 初始化操作
    */
    public function _initialize()
    {
        parent::_initialize();     
        if (cookie('user_id')) {  
            $this->user_id = cookie('user_id');
            $user = M('users')->where("user_id",$this->user_id )->find();
            session('user', $user);  //覆盖session 中的 user
            $this->user = $user;
            $this->assign('user',$user);
        }      
    }
    

    public function find(){
        $sort=I('sort');
        $this->assign('sort',$sort);
        return  $this->fetch();
    }
    
    public function search(){
        return  $this->fetch();
    }
    
    public function search_list(){
        $keywords=I('keywords');
        $this->assign('keywords',$keywords);
        return  $this->fetch();
    }
    
    public function moment()
    {
        $system_article=Db::name('system_article')->where(['doc_title'=>['like','%用户服务协议%']])->find();

        $cats=Db::name('moments_classify')->where(['is_show'=>1])->order('sort_order','asc')->select();
        $this->assign('cats',$cats);
        $this->assign('system_article',$system_article);
        return  $this->fetch();
    }
    
    public function moment_detail()
    {
        $moments_id=I('moments_id/d');
        $user_id=I('uid/d');
        $data=array('moments_id'=>$moments_id);
        MomentsLogic::add_click($data);

        $this->assign('moments_id',$moments_id);
        $this->assign('user_id',$user_id);
        return $this->fetch();
    }

    public function moment_img(){
        $moments_id=I('moments_id/d');
        $user_id=I('uid/d');
        $index=I('index/d');
        $data=array('moments_id'=>$moments_id);
        MomentsLogic::add_click($data);

        $this->assign('index',$index);
        $this->assign('moments_id',$moments_id);
        $this->assign('user_id',$user_id);
        return $this->fetch();
    }

    public function  moment_img_detail(){
        return $this->fetch();
    }

    public function set(){

        if(request()->isAjax()){
            $data = I('post.');
            Db::name('users')->where(['user_id'=>$this->user_id])->data($data)->save();
            $this->ajaxReturn(['status'=>1,'msg'=>'操作成功']);
        }
        return  $this->fetch();
    }
    
    
    /**
     * 獲取全部朋友圈信息
     */
    public function momentsList()
    {
        if (!request()->isPost()) {
            $this->ajaxReturn(['status' => -101, 'msg' => '请用post请求！！']);
        }      
        if($this->user_id){
            $data['user_id']=$this->user_id;
        }


        // 数据验证
        $validate = \think\Loader::validate('Moments');

        if (!$validate->batch()->scene('momentsList')->check($data)) {
            $this->ajaxReturn(['status' => -1, 'msg' => implode(',',$validate->getError())]);
        }

        $return = MomentsLogic::select_moments($data);
        $this->ajaxReturn($return);
    
    }
    
    
    /**
     * 关注
     */
    public function ajaxAttention(){
        $param=empty(I('get.'))?I('post.'):I('get.');

        if($param['id'] == $this->user_id){
            $this->ajaxReturn(['status' => 0, 'msg' => '不能关注自己哦']);
        }
        
        $where['user_id']=$this->user_id;
        $where['att_user_id']=$param['id'];
        $row=Db::name('user_attention')->where($where)->count();
        if($row){
             Db::name('user_attention')->where($where)->delete();
             $this->ajaxReturn(['status' => 1, 'msg' => '成功','state'=>'关注']);
        }
        else{
            Db::name('user_attention')
            ->save(['user_id'=>$this->user_id,
                'att_user_id'=>$param['id'],
                'add_time'=>time()]);
             $this->ajaxReturn(['status' => 1, 'msg' => '成功','state'=>'已关注']);
        }
    }
    
    /**
     * 点赞某条动态
     */
    public function addLike()
    {
        if (!request()->isPost()) {
            $this->ajaxReturn(['status' => -101, 'msg' => '请用post请求！！']);
        }
        $data = [
            'user_id' => $this->user_id,
            'moments_id' => I('post.moments_id/d'),
            'add_time' => time(),
        ];
    
        // 数据验证
        $validate = new Moments();//\think\Loader::validate('Moments');
    
        if (!$validate->batch()->scene('addLike')->check($data)) {
            $this->ajaxReturn(['status' => -1, 'msg' => implode(',',$validate->getError())]);
        }
        $return = MomentsLogic::add_like($data);
        $this->ajaxReturn($return);
    
    }
    
    /**
     * 刪除朋友圈
     */
    public function delMoments()
    {
        if (!request()->isPost()) {
            $this->ajaxReturn(['status' => -101, 'msg' => '请用post请求！！']);
        }
        $data = [
            'user_id' => $this->user_id,
            'moments_id' => I('post.moments_id/d'),
        ];
        // 数据验证
        $validate = new Moments();//\think\Loader::validate('Moments');
    
        if (!$validate->batch()->scene('delMoments')->check($data)) {
            $this->ajaxReturn(['status' => -1, 'msg' => implode(',',$validate->getError())]);
        }
    
        $return = MomentsLogic::del_moments($data);
        $this->ajaxReturn($return);
    
    
    }
    
    /**
     * 評論某条动态
     */
    public function addComment(){
        if (!request()->isPost()) {
            $this->ajaxReturn(['status' => -101, 'msg' => '请用post请求！！']);
        }
        $data = [
            'user_id' => $this->user_id,
            'moments_id' => I('post.moments_id/d',0),
            'pid' => I('post.pid/d',0),
            'p_name' => I('post.p_name',''),
            'comment_content' => I('post.comment_content'),
            'add_time' => time(),
        ];
     
        // 数据验证
        $validate = new Moments();//\think\Loader::validate('Moments');
    
        if (!$validate->batch()->scene('addComment')->check($data)) {
            $this->ajaxReturn(['status' => -1, 'msg' => implode(',',$validate->getError())]);
        }
    
        $return = MomentsLogic::add_comment($data,$this->user);
        $this->ajaxReturn($return);
    }
    
    
    
    /**
     * 刪除评论
     */
    public function delComment()
    {
        if (!request()->isPost()) {
            $this->ajaxReturn(['status' => -101, 'msg' => '请用post请求！！']);
        }
        $data = [
            'user_id' => $this->user_id,
            'moments_id' => I('post.moments_id/d'),
            'comment_id' => I('post.comment_id/d'),
        ];
        // 数据验证
        $validate = new Moments();//\think\Loader::validate('Moments');
    
        if (!$validate->batch()->scene('delComment')->check($data)) {
            $this->ajaxReturn(['status' => -1, 'msg' => implode(',',$validate->getError())]);
        }
    
        $return = MomentsLogic::del_comment($data);
        $this->ajaxReturn($return);
    
    
    }   
    

    /**
     * pc 只能单个上传图片
     */
    function upImg()
    {
        $moments_imgs = MomentsLogic::uploadMomentsImg();
        $this->ajaxReturn($moments_imgs);
    }
    
    /**
     * 发表朋友圈
     */
    public function addMoments()
    {
        if (!request()->isPost()) {
            $this->ajaxReturn(['status' => -101, 'msg' => '请用post请求！！']);
        }
        $city=I('city');
        $city_id=$city_id = Db::name('region')->where(['name'=>$city])->getField('id');
        $data = [
            'user_id' => $this->user_id,
            'title'=>I('post.title',''),
            'classify_id'=>I('post.cat_id/d',0),
            'moments_imgs' => I('post.moments_imgs', ''),
            'moments_content' => I('post.moments_content', ''), //内容
            'add_time' => time(),
            'city_id'=>$city_id
        ];
        // 数据验证
        $validate = new Moments();//\think\Loader::validate('Moments');
        if (!$validate->batch()->scene('addMoments')->check($data)) {
    
            $this->ajaxReturn(['status' => -1, 'msg' => implode(',',$validate->getError())]);
        }

        if($data['moments_imgs']){
            $data['moments_imgs'] = trim($data['moments_imgs'],',') ;
        }else{
            $moments_imgs = MomentsLogic::uploadMomentsImg();
            if ($moments_imgs['status'] == -1) {
                $this->ajaxReturn($moments_imgs);
            }
            $data['moments_imgs'] = $moments_imgs['result'];
        }
    
        if($data['moments_imgs']=='' && $data['moments_content']==''){
            $this->ajaxReturn(['status' => -1, 'msg' =>'图片和内容必须存在一个']);
        }

        $return = MomentsLogic::add_moments($data);
        $this->ajaxReturn($return);
    }
    
    
    /**
     * 个人中心
     */
    public function homePage()
    {
        $param = empty(I('get.'))?I('post.'):I('get.');   
        $userObj = new UsersLogic();  
        //个人信息
        $result['user'] = Db::name('users')->where(['user_id'=>  $param['id'] ])->find();
        $result['attention_count'] = $userObj->getAttentionCount($result['user']['user_id']);        
        $result['fans_count'] = $userObj->getFansCount($result['user']['user_id']);

        //登录用户是否已关注该用户
        $check = Db::name('user_attention')->where(['user_id'=>$this->user_id,'att_user_id'=>$param['id']])->count();
        //获取地区位置
        $city_name = Db::name('store')->alias('a')->where(['a.user_id'=>$param['id']])->join('tp_region b','a.city_id = b.id','LEFT')->find();
        $result['city_name'] = $city_name['name'];
        $result['attention'] = $check ? 1 : 0;
        $this->assign('result',$result);
        return $this->fetch();
    }
    
    
    /**
     * 獲取某人或者自己所有动态列表
     */
    public function seeAllMoments()
    {
        if (!request()->isPost()) {
            $this->ajaxReturn(['status' => -101, 'msg' => '请用post请求！！']);
        }
    
        // 数据验证
        $validate = new Moments();//\think\Loader::validate('Moments');
        if (!$validate->batch()->scene('momentsList')->check(I('post.'))) {
            $this->ajaxReturn(['status' => -1, 'msg' => implode(',',$validate->getError())]);
        }
    
        $user_id = I('post.uid/d',0);
        
        if($user_id == $this->user_id || $user_id==0){
            //查看自己状态
            $data = [
                'user_id' => $this->user_id,
                'type' => 1,
            ];
        }else{
            //查看别人状态
            $data = [
                'user_id' => $user_id,
                'type' => 0,
            ];
        }
    
        $return = MomentsLogic::see_all_moments($data);
        $this->ajaxReturn($return);
    
    }
    
    
    /**
     * 查看自己或者某条动态信息
     */
    public function seeFindMoments()
    {
        if (!request()->isPost()) {
            $this->ajaxReturn(['status' => -1, 'msg' => '请用post请求！！']);
        }
    
        // 数据验证
        $validate = new Moments();//\think\Loader::validate('Moments');
    
        if (!$validate->batch()->scene('delMoments')->check(I('post.'))) {
            $this->ajaxReturn(['status' => -1, 'msg' => implode(',',$validate->getError())]);
        }
        $user_id = I('post.uid/d',0);

        if($user_id == $this->user_id || $user_id==0){
            //查看自己状态
            $data = [
                'user_id' => $this->user_id,//用来查询数据
                'moments_id' => I('post.moments_id/d'),
                'order_user_id' => $this->user_id,//拿来对比
                'type' => 1,
            ];
        }else{
            //查看别人状态
            $data = [
                'user_id' => $user_id,//用来查询数据
                'moments_id' => I('post.moments_id/d'),
                'order_user_id' => $this->user_id,//拿来对比
                'type' => 0,
            ];
        }
    
        $return = MomentsLogic::see_find_moments($data);
        $this->ajaxReturn($return);
    
    }
    
    /**
     * 个人中心残友上架的商品
     */
    public function goodsList(){
        if (!request()->isPost()) {
            $this->ajaxReturn(['status' => -101, 'msg' => '请用post请求！！']);
        }
        
        $user_id = I('post.uid/d',0);         
        $page = I('post.page/d',0);
        
       $store_id = Db::name('store')->where(['user_id'=> $user_id])->getField('store_id');
       $where['store_id'] = $store_id;
       $where['is_on_sale'] = 1;
       
       $goods_list = M('goods')->where($where)->limit($page,10)->select();
       $this->ajaxReturn(['status'=>1,'msg'=>'获取成功','result'=>$goods_list]);
    }

}