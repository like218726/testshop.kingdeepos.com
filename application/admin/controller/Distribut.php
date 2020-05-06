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
 * Author: IT宇宙人      
 * 
 * Date: 2016-03-09
 */

namespace app\admin\controller;
use think\Page;
use think\Db;
use app\admin\logic\GoodsLogic;

class Distribut extends Base {
    
    /*
     * 初始化操作
     */
    public function _initialize() {
       parent::_initialize();
    }    
    
    /**
     * 分销树状关系
     */
    public function tree(){
        $first_leader = I('first_leader/d');
        $user_id = I('user_id/d');
        $where['is_distribut'] = 1;
        $first_leader_user = array();
        if($first_leader){
            $first_leader_user = M('users')->where('user_id',$first_leader)->find();
            $where['first_leader'] = $first_leader;
        }else{
            $where['first_leader'] = 0;
        }

        if($user_id){
            $where['user_id'] = $user_id;
        }
        $count = M('users')->where($where)->count();
        $page = new Page($count,10);
        $list = M('users')->where($where)->limit($page->firstRow,$page->listRows)->select();
        $this->assign('page',$page->show());
        $this->assign('count',$count);
        $this->assign('first_leader_user',$first_leader_user);
        $this->assign('list',$list);
        return $this->fetch();
    }
 
    /**
     * 分销商列表
     */
    public function distributor_list(){
    	$condition['is_distribut']  = 1;
    	$nickname = I('nickname');
    	$user_id = I('user_id');
    	if(!empty($nickname)){
    		$condition['nickname'] = array('like',"%$nickname%");
    	}
    	if(!empty($user_id)){
    	    $condition['user_id'] = $user_id;
    	}
    	$count = M('users')->where($condition)->count();
    	$Page = new Page($count,10);
    	$show = $Page->show();
    	$user_list = M('users')->where($condition)->order('distribut_money DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
    	foreach ($user_list as $k=>$val){
    		$user_list[$k]['fisrt_leader'] = M('users')->where(array('first_leader'=>$val['user_id']))->count();
    		$user_list[$k]['second_leader'] = M('users')->where(array('second_leader'=>$val['user_id']))->count();
    		$user_list[$k]['third_leader'] = M('users')->where(array('third_leader'=>$val['user_id']))->count();
    		$user_list[$k]['lower_sum'] = $user_list[$k]['fisrt_leader'] +$user_list[$k]['second_leader'] + $user_list[$k]['third_leader'];
    	}
    	$this->assign('page',$show);
    	$this->assign('pager',$Page);
    	$this->assign('user_list',$user_list);
    	$this->assign('nickname',$nickname);
    	$this->assign('user_id',$user_id);
    	return $this->fetch();
    }
    
    /**
     * 分销设置
     */
    public function setting(){                       
        header("Location:".U('Admin/System/index',array('inc_type'=>'distribut')));
        exit;
    }
    
    
    public function grade_list(){
    	$grade_list = M('distribut_level')->select();
    	$this->assign('grade_list',$grade_list);
    	$inc_type =  'distribut';
    	$this->assign('inc_type',$inc_type);
    	$config = tpCache($inc_type);
    	$this->assign('config',$config);//当前配置项
    	return $this->fetch();
    }
    
    public function grade_info(){
    	$level_id = I('level_id',0);
    	if($level_id>0){
    		$info = M('distribut_level')->where(array('level_id'=>$level_id))->find();
    		$this->assign('info',$info);
    	}
    	return $this->fetch();
    }
    
    public function gradeInfoSave(){
    	$data =  I('post.');
    	if($data['level_id'] >0 ){
    		$r = M('distribut_level')->where(array('level_id'=>$data['level_id']))->save($data);
    	}else{
    		unset($data['level_id']);
    		$r = M('distribut_level')->add($data);
    	}
    	if($r){
    		$this->ajaxReturn(array('status'=>1,'msg'=>'操作成功'));
    	}else{
    		$this->ajaxReturn(array('status'=>0,'msg'=>'操作失败'));
    	}
    }  
    
    public function goods_list()
    {
    	$GoodsLogic = new GoodsLogic();
    	$brandList = $GoodsLogic->getSortBrands();
    	$categoryList = $GoodsLogic->getSortCategory();
    	$this->assign('categoryList',$categoryList);
    	$this->assign('brandList',$brandList);
    	$where = ' distribut > 0 ';
    	$cat_id = I('cat_id1/d');
        $bind = array();
    	if($cat_id > 0) {
            $grandson_ids = getCatGrandson($cat_id);
            $where .= " and cat_id1 in(".  implode(',', $grandson_ids).") "; // 初始化搜索条件
    	}
    	$key_word = I('key_word') ? trim(I('key_word')) : '';
    	if($key_word) {
            $where = "$where and (goods_name like :key_word1 or goods_sn like :key_word2)" ;
            $bind['key_word1'] = "%$key_word%";
            $bind['key_word2'] = "%$key_word%";
    	}
        $brand_id = I('brand_id');
        if($brand_id) {
            $where = "$where and brand_id = :brand_id";
            $bind['brand_id'] = $brand_id;
        }
    	$model = M('Goods');
    	$count = $model->where($where)->bind($bind)->count();
    	$Page  = new Page($count,10);
    	$show = $Page->show();
    	$goodsList = $model->where($where)->bind($bind)->order('sales_sum desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $catList = D('goods_category')->select();
        $catList = convert_arr_key($catList, 'id');
        $this->assign('catList',$catList);
        $this->assign('pager',$Page);
    	$this->assign('goodsList',$goodsList);
    	$this->assign('page',$show);
    	return $this->fetch();
    }
    
    /**
     * 分成记录
     */
    public function rebate_log()
    { 
        $model = M("rebate_log"); 
        $status = I('status');
        $user_id = I('user_id');
        $order_sn = I('order_sn','','trim');        
        $create_time = I('create_time');
        $start_time = I('start_time');      //日志生成时间: 开始时间
        $end_time = I('end_time');        //日志生成时间: 结束时间
        
        //mobify by wangqh. 兼容新旧模板 @{
        $start_time = $start_time  ? $start_time  : date('Y-m-d',strtotime('-1 year'));
        $end_time = $end_time  ? $end_time  : date('Y-m-d',strtotime('+1 day'));
        $create_time = $create_time ? $create_time :  $start_time .' - '.$end_time;
        // }
        
        $create_time2 = explode(' - ',$create_time);
        $where = " create_time >= '".strtotime($create_time2[0])."' and create_time <= '".strtotime($create_time2[1])."' ";
        if($status === '0' || $status > 0){
            $where .= " and status = $status ";        
        }
        $user_id && $where .= " and user_id = $user_id ";
        $order_sn && $where .= " and order_sn like '%{$order_sn}%' ";
                        
        $count = $model->where($where)->count();
        $Page  = new Page($count,16);        
        $list = $model->where($where)->order("`id` desc")->limit($Page->firstRow.','.$Page->listRows)->select();
        
        $get_user_id = get_arr_column($list, 'user_id'); // 获佣用户
        $buy_user_id = get_arr_column($list, 'user_id'); // 购买用户
        $user_id_arr = array_merge($get_user_id,$buy_user_id);
        if(!empty($user_id_arr))
        $user_arr = M('users')->where("user_id in (".  implode(',', $user_id_arr).")")->getField("user_id,nickname,email,mobile",true);
        $this->assign('user_arr',$user_arr);       
  
        $this->assign('status',$status);
        $this->assign('user_id',$user_id);
        $this->assign('order_sn',$order_sn);
         
        $this->assign('start_time',$start_time);
        $this->assign('end_time',$end_time);
        
        $this->assign('create_time',$create_time);        
        $show  = $Page->show();                 
        $this->assign('show',$show);
        $this->assign('pager',$Page);
        $this->assign('list',$list);
        C('TOKEN_ON',false);
       
        return $this->fetch();
    }
    
    /**
     * 获取某个人下级元素
     */    
    public  function ajax_lower()
    {
        $list = M('users')->where("first_leader =".$_GET[id])->select();
        $this->assign('list',$list);
        return $this->fetch();
    }
    
    /**
     * 修改编辑 分成 
     */
    public  function editRebate(){        
        $id = I('id');
        $model = M("rebate_log");
        $rebate_log = $model->find($id);
        if(IS_POST)
        {
                $model->create();
                
                // 如果是确定分成 将金额打入分佣用户余额
                if($model->status == 3 && $rebate_log['status'] != 3)
                {
                    accountLog($model->user_id, $rebate_log['money'], 0,"订单:{$rebate_log['order_sn']}分佣",$rebate_log['money']);
                }                
                $model->save();                               
                $this->success("操作成功!!!",U('Admin/Distribut/rebate_log'));               
                exit;
        }                      
       
       $user = M('users')->where("user_id = {$rebate_log[user_id]}")->find();       
            
       if($user['nickname'])        
           $rebate_log['user_name'] = $user['nickname'];
       elseif($user['email'])        
           $rebate_log['user_name'] = $user['email'];
       elseif($user['mobile'])        
           $rebate_log['user_name'] = $user['mobile'];            
       
       $this->assign('user',$user);
       $this->assign('rebate_log',$rebate_log);
       return $this->fetch();
    }        

    /**
     * 微店列表
     */
    public function my_store()
    {
        $uid = I('get.id');
        $user = D('users')->where(array('user_id'=>$uid))->find();
        if (!$user) {
            $this->error('会员不存在');
        }
        if($user['is_distribut'] != 1) {
            $this->error('您还不是分销商');
        }
    	$GoodsLogic = new GoodsLogic();
    	$brandList = $GoodsLogic->getSortBrands();
    	$categoryList = $GoodsLogic->getSortCategory();
    	$this->assign('categoryList',$categoryList);
    	$this->assign('brandList',$brandList);
    	$where = 'user_id = '.$uid;
    	$cat_id = I('cat_id/d');
        $bind = array();
    	if($cat_id > 0) {
            $grandson_ids = getCatGrandson($cat_id);
            $where .= " and cat_id in(".  implode(',', $grandson_ids).") "; // 初始化搜索条件
    	}
    	$key_word = I('key_word') ? trim(I('key_word')) : '';
    	if($key_word) {
            $where = " $where and (goods_name like :key_word1 or goods_sn like :key_word2)" ;
            $bind['key_word1'] = "%$key_word%";
            $bind['key_word2'] = "%$key_word%";
    	}
        $brand_id = I('brand_id');
        if($brand_id) {
            $where = " $where and brand_id = :brand_id";
            $bind['brand_id'] = $brand_id;
        }
    	$model = M('user_distribution');
    	$count = $model->alias('d')
            ->field('g.*,d.id,d.share_num,d.sales_num,d.addtime')
            ->join('__GOODS__ g', 'd.goods_id=g.goods_id', 'LEFT')
            ->where($where)->where(['g.is_on_sale'=>1])
            ->bind($bind)->count();
    	$Page  = new Page($count,10);
    	$show = $Page->show();
    	//$goodsList = $model->where($where)->bind($bind)->order('sales_num desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        //SELECT g.*,d.`id`, d.share_num,d.sales_num,d.addtime FROM tp_user_distribution d  LEFT JOIN tp_goods g ON d.`goods_id` = g.`goods_id`
        $goodsList = $model->alias('d')
                        ->field('g.*,d.id,d.share_num,d.sales_num,d.addtime')
                        ->join('__GOODS__ g', 'd.goods_id=g.goods_id', 'LEFT')
                        ->where($where)->where(' d.goods_id > 0 and g.is_on_sale = 1')->bind($bind)->order('sales_num desc')
                        ->limit($Page->firstRow.','.$Page->listRows)->select();
        
        $catList = D('goods_category')->select();
        $catList = convert_arr_key($catList, 'id');
        $this->assign('catList',$catList);
        $this->assign('pager',$Page);
    	$this->assign('goodsList',$goodsList);
    	$this->assign('page',$show);
    	return $this->fetch();
    }
    
    /**
     * 删除分销商品(变成普通商品)
     */
    public function delGoods()
    {
        $goods_id = I('get.id', 0);
        if (empty($goods_id))$this->ajaxReturn(['status' => -1,'msg' => '参数错误','data'  =>'']);
        $auto_service_date = tpCache('shopping.auto_service_date'); //申请售后时间段
        $confirm_time = strtotime ( "-$auto_service_date day" );
        //在最终退货期内还有订单，用户任然可能退货，退货后可能要涉及到分销金额的计算，此时删除商品将无法计算。
        $order_id_arr = Db::name('OrderGoods')->where(['goods_id' => $goods_id,'deleted'=>0,'is_send'=>['lt',2],'distribut'=>['gt',0]])->getField('order_id',true);
        $order_ids = implode(',',$order_id_arr);
        $order_count = Db::name('Order')->where(['confirm_time' =>['gt',$confirm_time]])
            ->whereIn('order_id',$order_ids)->whereNotIn('order_status','3,5')->count();
        $order_count > 0 && $this->ajaxReturn(['status' => -1, 'msg' =>'该商品订单未完成不得删除']);
        $res = Db::name("goods")->where(['goods_id'=>$goods_id])->update(['distribut'=>0]);  //将商品改成普通商品
         Db::name("user_distribution")->where(['goods_id'=>$goods_id])->delete();  //将商品改成普通商品
        if($res === false)$this->ajaxReturn(['status' => -1,'msg' => '操作失败']);
        $this->ajaxReturn(['status' => 1,'msg' => '操作成功']);
    }
}