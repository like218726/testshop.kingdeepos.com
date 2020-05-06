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
 * 订单以及售后中心
 * @author soubao 当燃
 *  @Date: 2016-12-20
 */
namespace app\home\controller;
use app\common\logic\Message;
use app\common\logic\OrderLogic;
use app\common\logic\OrderGoodsLogic;
use app\common\logic\CommentLogic;
use app\common\model\ReturnGoods;
use app\common\model\Store;
use app\common\model\Complain;
use app\common\model\StoreAddress;
use think\Db;
use think\Page;
use think\AjaxPage;

class Order extends Base {

	public $user_id = 0;
	public $user = array();

    public function _initialize() {      
        parent::_initialize();
        if(session('?user'))
        {
        	$user = session('user');
            $user = M('users')->where("user_id", $user['user_id'])->find();
            session('user',$user);  //覆盖session 中的 user               
        	$this->user = $user;
        	$this->user_id = $user['user_id'];
        	$this->assign('user',$user); //存储用户信息
        	$this->assign('user_id',$this->user_id);
            //获取用户信息的数量
            $messageLogic = new Message();
            $user_message_count = $messageLogic->getUserMessageNoReadCount();
            $this->assign('user_message_count', $user_message_count);
        }else{
            redirect()->remember();
            $this->redirect('Home/User/login');
        }
        //用户中心面包屑导航
        $navigate_user = navigate_user();
        $this->assign('navigate_user',$navigate_user);        
    }

    //咨询列表
    public function consult_list(){
        $user_id = $this->user_id;
        $where = array('parent_id' => 0);
        if ($user_id) {
            $where['user_id'] = $user_id;
        }
        $count = db('goods_consult')->where($where)->count();
        $page = new Page($count, 10);
        $show = $page->show();
        $consultList = db('goods_consult')->where($where)->order("id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach($consultList as $key =>$list){
            $consultList[$key]['replyList'] = db('goods_consult')->where(['parent_id' => $list['id'],'is_show'=>1])->order("id desc")->select();
            $consultList[$key]['goods'] = db('goods')->where('goods_id',$list['goods_id'])->value('goods_name');
        }
        $this->assign('consultCount', $count);// 商品咨询数量
        $this->assign('consultList', $consultList );// 商品咨询
        $this->assign('page', $show);// 赋值分页输出
        return $this->fetch();
    }

    /*
     * 订单列表
     */
    public function order_list()
    {
        $type = input('type');
        $search_key = input('search_key');
        $search_key = trim($search_key);// 搜索订单 根据商品名称 或者 订单编号
        $select_year = select_year(); // 查询 三个月,今年内,2016年等....订单
        $order = new \app\common\model\Order();
        $where_arr = [
            'user_id'=>$this->user_id,
            'deleted'=>0,//删除的订单不列出来
//            'prom_type'=>['lt',5],//虚拟拼团订单不列出来
            'prom_type'=>['in',[0,1,2,3,4,9]] // 加上9为自提订单
        ];
        //条件搜索
        if (C('buy_version') != 1) {
            if ($select_year == '')// 近三个月
            {
                $add_start_time = time() - (86400 * 90);
                $add_end_time = time();
            } elseif ($select_year == '_this_year')// 今年内
            {
                $add_start_time = strtotime(date('Y-01-01'));
                $add_end_time = time();
            } else {
                $search_year = substr($select_year, 1);
                $add_start_time = strtotime(date("$search_year-01-01"));
                $search_year += 1;
                $add_end_time = strtotime(date("$search_year-01-01"));
            }
            $where_arr['add_time'] = ['BETWEEN',[$add_start_time,$add_end_time]];
        }
        $count = $order->where($where_arr)
            ->where(function ($query) use ($type) {
                if ($type) {
                    $query->where("1=1".C(strtoupper($type)));
                }
            })
            ->where(function ($query) use ($search_key,$select_year) {
                if ($search_key) {
                    $query->where('order_sn', 'like', $search_key)->whereOr('order_id', 'IN', function ($query) use ($search_key,$select_year) {
                        $query->name('order_goods' . $select_year)->where('goods_name', 'like', $search_key)->field('order_id');
                    });
                }
            })
            ->count();
        $Page = new Page($count, 10);
        $order_list = $order->where($where_arr)
            ->where(function ($query) use ($type) {
                if ($type) {
                    $query->where("1=1".C(strtoupper($type)));
                }
            })
            ->where(function ($query) use ($search_key,$select_year) {
                if ($search_key) {
                    $query->where('order_sn', 'like', $search_key)->whereOr('order_id', 'IN', function ($query) use ($search_key,$select_year) {
                        $query->name('order_goods' . $select_year)->where('goods_name', 'like', $search_key)->field('order_id');
                    });
                }
            })
            ->limit($Page->firstRow . ',' . $Page->listRows)->order("order_id DESC")->select();
        $orderLogic = new \app\common\logic\OrderLogic();// 将超时未支付订单给取消掉
        $orderLogic->setUserId($this->user_id);
        $orderLogic->abolishOrder();

        $this->assign('years', buyYear()); // 获取年限
        $this->assign('page', $Page);
        $this->assign('order_list', $order_list);
        return $this->fetch();
    }

    public function del_order()
    {
        $order_id = I('order_id/d',0);
        $order_type = I('type/s');
        
        $orderLogic = new \app\common\logic\OrderLogic;
        $orderLogic->setUserId($this->user_id);
        $return = $orderLogic->delOrder($order_id);
        $this->ajaxReturn($return);
    }
    
    /*
     * 订单详情
     */
    public function order_detail()
    {

        $Order = new \app\common\model\Order();
        $id = I('get.id/d', 0);
        $order = $Order::get(['order_id' => $id, 'user_id' => $this->user_id]);
        if (!$order) {
            $this->error('没有获取到订单信息');
            exit;
        }
        //获取订单
        if ($order['prom_type'] == 5) {   //虚拟订单
            $this->redirect(U('virtual/virtual_order', ['order_id' => $id]));
        }
        $this->assign('order', $order);
        $template = input('act', 'order_detail');
        if ($order['order_status'] == 3) {
            $template = 'cancel_order';
        }
        if(empty($order['pay_status'])){
            $orderLogic = new \app\common\logic\OrderLogic();// 将超时未支付订单给取消掉
            $orderLogic->setUserId($this->user_id);
            $orderLogic->abolishOrder();
        }
        if (strstr($template,'.')||strstr($template,'/') || strstr($template,'\\')) {
            $this->error('非法模板名称');
        }
        return $this->fetch($template);
    }
    
    public function cancel_order_info(){
    	$order_id = I('order_id/d',0);
    	$order = M('order')->where(array('order_id'=>$order_id,'order_status'=>3,'pay_status'=>['gt',0]))->find();
    	if(!$order){
            $this->error('非法操作！');
    	}
        $store = M('store')->where(array('store_id'=>$order['store_id']))->find();
        $this->assign('store',$store);
    	$this->assign('order', $order);
    	return $this->fetch();
    }

    /*
     * 取消订单
     */
    public function cancel_order(){
        $id = I('id');
        //检查是否有积分，余额支付
        $logic = new OrderLogic();
        $data = $logic->cancel_order($this->user_id,$id);
        $this->ajaxReturn($data);
    }
    
    //取消订单弹窗
    public function refund_order()
    {
    	$order_id = I('get.order_id/d');
    	$order = M('order')
                ->field('order_id,pay_code,pay_name,user_money,integral_money,coupon_price,order_amount,total_amount')
                ->where(['order_id' => $order_id, 'user_id' => $this->user_id])
                ->find();
        if (!$order) {
            return $this->error('订单不存在');
        }
        
        $this->assign('user',  $this->user);
        $this->assign('order', $order);
    	return $this->fetch();
    }

    //申请取消订单
    public function record_refund_order()
    {
        $order_id   = input('post.order_id', 0);
        $user_note  = input('post.user_note', '');
        $consignee  = input('post.consignee', '');
        $mobile     = input('post.mobile', '');
        $logic = new \app\common\logic\OrderLogic;
        $return = $logic->recordRefundOrder($this->user_id, $order_id, $user_note, $consignee, $mobile);
        
        $this->ajaxReturn($return);
    }

    /*
     * 评论晒单
     */
    public function comment()
    {
        $user_id = $this->user_id;
        $status = I('get.status', -1);
        $logic = new CommentLogic;
        $data = $logic->getComment($user_id, $status); //获取评论列表
        $store_id_list = get_arr_column($data['result'], 'store_id');
        if (!empty($store_id_list)) {
            $store_list = M('store')->cache(true)->where("store_id", "in", implode(',', $store_id_list))->getField('store_id,store_name,store_qq');
        }
        $this->assign('page', $data['page']);// 赋值分页输出
        $this->assign('store_list', $store_list);
        $this->assign('comment_list', $data['result']);
        $this->assign('active', 'comment');
        return $this->fetch();
    }

    /**
     * 删除评价
     */
    public function delComment()
    {
        $comment_id = I('comment_id');
        if (empty($comment_id)) {
            $this->ajaxReturn(['status'=>-1,'msg'=>'参数错误']);
        }
        $comment = Db::name('comment')->where('comment_id', $comment_id)->find();
        if ($this->user_id != $comment['user_id']) {
            $this->ajaxReturn(['status'=>-1,'msg'=>'参数错误']);
        }
        Db::name('reply')->where('comment_id', $comment_id)->delete();
        Db::name('comment')->where('comment_id', $comment_id)->delete();
        $this->ajaxReturn(['status'=>1,'msg'=>'删除评论成功','url'=>U('Home/Order/comment',['status'=>1])]);
    }


    /**
     * @time 2016/8/5
     * @author dyr
     * 订单评价列表
     */
    public function comment_list()
    {
        $order_id = I('get.order_id/d');
        $store_id = I('get.store_id/d');
        $part_finish = I('get.part_finish', 0);
        if (empty($order_id) || empty($store_id)) {
            $this->error("参数错误");
        } else {
            //查找店铺信息
            $store_where['store_id'] = $store_id;
            $store_info = M('store')->field('store_id,store_name,store_phone,store_address,store_logo')->where($store_where)->find();
            if (empty($store_info)) {
                $this->error("该商家不存在");
            }
            //查找订单是否已经被用户评价
            $order_comment_where['order_id'] = $order_id;
            $order_comment_where['deleted'] = 0;
            $order_info = M('order')->field('order_id,order_sn,is_comment,add_time,prom_type')->where($order_comment_where)->find();
            //查找订单下的所有未评价的商品
            $order_goods_logic = new OrderGoodsLogic();
            $no_comment_goods_list = $order_goods_logic->get_no_comment_goods_list($order_id);
            $goods_id_list = array();
            foreach ($no_comment_goods_list as $key => $value) {
                array_push($goods_id_list, $value['goods_id']);
            }
            $this->assign('goods_id_list', $goods_id_list);
            $this->assign('store_info', $store_info);
            $this->assign('order_info', $order_info);
            $this->assign('no_comment_goods_list', $no_comment_goods_list);
            $this->assign('no_comment_goods_list_count', count($no_comment_goods_list));
            $this->assign('part_finish', $part_finish);
            return $this->fetch();
        }
    }

    /**
     * @time 2016/8/5
     * @author dyr
     *  添加评论
     */
    public function addComment()
    {
        $remark                         = I("post.remark/a");
        $hide_username                  = I('post.hide_username');
        $store_score['describe_score']  = I('post.store_packge_hidden');
        $store_score['seller_score']    = I('post.store_speed_hidden');
        $store_score['logistics_score'] = I('post.store_sever_hidden');
        $order_sn        = I('post.order_sn');
        $store_id = $store_score['store_id'] = I('post.store_id');
        $order_id = $store_score['order_id'] = $store_score_where['order_id'] = I('post.order_id/d');
        $store_score['user_id'] = $store_score_where['user_id'] = $this->user_id;
        $store_score_where['deleted'] = 0;
        //处理订单评价
        if (!empty($store_score['describe_score']) && !empty($store_score['seller_score']) && !empty($store_score['logistics_score'])) {
            $order_comment = M('order_comment')->where($store_score_where)->find();
            if ($order_comment) {
                M('order_comment')->where($store_score_where)->save($store_score);
                M('order')->where(array('order_id' => $order_id))->save(array('is_comment' => 1));
            } else {
                M('order_comment')->add($store_score);//订单打分
                M('order')->where(array('order_id' => $order_id))->save(array('is_comment' => 1));
            }
            //订单打分后更新店铺评分
            $store = new \app\common\logic\Store($store_id);
            $store->updateStoreScore();
        }
        //处理商品评价
        if (is_array($remark)) {
            foreach ($remark as $key => $value) {
                if (!empty($value['rank']) && !empty($value['content'])) {
                    $comment['goods_id'] = $value['goods_id'];
                    $comment['order_id'] = $store_score['order_id'];
                    $comment['order_sn'] = $order_sn;
                    $comment['store_id'] = $store_id;
                    $comment['rec_id'] = $key;
                    $comment['user_id'] = $this->user_id;
                    $comment['content'] = $value['content'];
                    $comment['ip_address'] = getIP();
                    $comment['spec_key_name'] = $value['spec_key_name'];
                    $comment['goods_rank'] = $value['rank'];
                    $comment['img'] = (empty($value['commment_img'][0])) ? '' : serialize($value['commment_img']);
                    $comment['impression'] = (empty($value['tag'][0])) ? '' : implode(',', $value['tag']);
                    $comment['is_anonymous'] = empty($hide_username) ? 1 : 0;
                    $comment['add_time'] = time();
                    M('comment')->add($comment);//向评论表插入数据
                    M('order_goods')->where(['rec_id'=>$key])->save(array('is_comment' => 1));
                    M('goods')->where(array('goods_id' => $value['goods_id']))->setInc('comment_count', 1);
                    unset($comment);
                }
            }
        }
        clearCache();//清除缓存
        //查找订单下是否有没有评价的商品
        $order_goods_logic = new OrderGoodsLogic();
        $no_comment_goods_list = $order_goods_logic->get_no_comment_goods_list($order_id);
        $no_comment_goods_count = count($no_comment_goods_list);
        if ($no_comment_goods_count > 0) {
            $url = U('Order/comment_list',['part_finish' => 1, 'order_id' => $order_id, 'store_id' => $store_id]);
            $this->ajaxReturn(['status'=>1,'msg'=>'','url'=> $url]);
        } else {
            M('order')->where(['order_id' => $order_id])->save(array('order_status' => 4));
           $url = U('Order/comment_list',['order_id' => $order_id, 'store_id' => $store_id]);
            $this->ajaxReturn(['status'=>1,'msg'=>'','url'=> $url]);
        }
    }

    /**
     *  点赞
     * @author dyr
     */
    public function ajaxZan()
    {
        $comment_id = I('post.comment_id/d');
        $user_id = $this->user_id;
        $comment_info = M('comment')->where(array('comment_id' => $comment_id))->find();
        $comment_user_id_array = explode(',', $comment_info['zan_userid']);
        if (in_array($user_id, $comment_user_id_array)) {
            $result['success'] = 0;
        } else {
            array_push($comment_user_id_array, $user_id);
            $comment_user_id_string = implode(',', $comment_user_id_array);
            $comment_data['zan_num'] = $comment_info['zan_num'] + 1;
            $comment_data['zan_userid'] = $comment_user_id_string;
            M('comment')->where(array('comment_id' => $comment_id))->save($comment_data);
            $result['success'] = 1;
        }
        $this->ajaxReturn($result);
    }

    /**
     * 添加回复
     * @author dyr
     */
    public function reply_add()
    {
        $comment_id = I('post.comment_id/d');
        $reply_id = I('post.reply_id/d', 0);
        $content = I('post.content');
        $to_name = I('post.to_name', '');
        $reply_data = array(
            'comment_id' => $comment_id,
            'parent_id' => $reply_id,
            'content' => $content,
            'user_name' => $this->user['nickname'],
            'to_name' => $to_name,
            'reply_time' => time()
        );
        $add = Db::name('reply')->insert($reply_data);
        M('comment')->where(array('comment_id' => $comment_id))->setInc('reply_num');
        if($add !== false){
            $this->ajaxReturn(['status'=>1,'msg'=>'回复提交成功','result'=>'']);
        }else{
            $this->ajaxReturn(['status'=>1,'msg'=>'回复提交失败','result'=>'']);
        }
    }

    /**
     * 确定收货
     */
    public function order_confirm()
    {
    	$id = I('post.order_id/d', 0);
    	$data = confirm_order($id, $this->user_id);
        $this->ajaxReturn($data);
    }

    /**
     * 可申请退换货
     */
    public function return_goods_index(){
        $sale_t = I('sale_t/i',0);
        $keywords = I('keywords');
        $model = new OrderLogic();
        $data = $model->getReturnGoodsIndex($sale_t,$keywords,$this->user_id);
    	$this->assign('store_list',$data['store_list']);
    	$this->assign('order_list',$data['order_list']);
    	$this->assign('page',$data['show']);
    	return $this->fetch();
    }
    
    /**
     * 申请退货
     */
    public function return_goods()
    {
        $rec_id = I('rec_id',0);
        $return_goods = Db::name('return_goods')->where(array('rec_id'=>$rec_id))->find();
        if(!empty($return_goods))
        {
            $this->error('已经提交过退货申请!',U('Order/return_goods_info',array('id'=>$return_goods['id'])));
        }
        $order_goods = Db::name('order_goods')->where(array('rec_id'=>$rec_id))->find();
        $order = Db::name('order')->where(array('order_id'=>$order_goods['order_id'],'user_id'=>$this->user_id))->find();
        $store = Db::name('store')->where(array('store_id'=>$order['store_id']))->find();
        if(empty($order))$this->error('非法操作');
        $auto_service_date = tpCache('shopping.auto_service_date'); //申请售后时间段
        $confirm_time = strtotime("-$auto_service_date day");
        $this->assign('store', $store);
        if($order['order_status'] == 2){
            //确认收货了，才走这判断
            if ($order['confirm_time'] < $confirm_time) {
                return $this->fetch('return_error');
            }
        }

    	if(IS_POST)
    	{
            $model = new OrderLogic();
            $res = $model->addReturnGoods($rec_id,$order);  //申请售后
            if($res['status']==1){

                $this->success($res['msg'],U('Order/return_goods_list'));
            }else{
                $this->error($res['msg']);
            }

    	}
        $delivery_doc= Db::name('delivery_doc')->where(['order_id'=>$order['order_id']])->find();
        $store_address_province_id = $delivery_doc['store_address_province_id'];
        $store_address_city_id     = $delivery_doc['store_address_city_id'];
        $store_address_district_id = $delivery_doc['store_address_district_id'];
        $storeAddress['mobile'] = $delivery_doc['store_address_mobile'];
        $storeAddress['consignee'] = $delivery_doc['store_address_consignee'];
        //若是自提订单
        if ($order['shop_id']) {
            $shop = db('shop')->where('shop_id',$order['shop_id'])->find();
            $store_address_province_id = $shop['province_id'];
            $store_address_city_id     = $shop['city_id'];
            $store_address_district_id = $shop['district_id'];
            $storeAddress['mobile'] = $shop['shop_phone_code'].'-'.$shop['shop_phone'];
            $storeAddress['consignee'] = $shop['shopper_name'];
        }

        $region = Db::name('region')
            ->where("id in($store_address_province_id,$store_address_city_id,{$store_address_district_id})")
            ->getField('id,name');
        $storeAddress['full_address'] = $region["$store_address_province_id"].$region["$store_address_city_id"].$region["$store_address_district_id"].$delivery_doc['store_address'];

        $this->assign('storeAddress', $storeAddress);
        $this->assign('goods', $order_goods);
    	$this->assign('order',$order);
    	return $this->fetch();
    }
    
    /**
     * 退换货列表
     */
    public function return_goods_list()
    {
    	$order_sn = I('order_sn');
    	$addtime = I('addtime');
    	$where = array('user_id'=>$this->user_id);
    	if($order_sn){
    		$where['order_sn'] = $order_sn;
    	}
    	$status = I('status');
    	if($status === '0' || !empty($status)){
    		$where['status'] = $status;
    	}
    	if($addtime == 1){
    		$where['addtime'] = array('gt',(time()-90*24*3600));
    	}
    	if($addtime == 2){
    		$where['addtime'] = array('lt',(time()-90*24*3600));
    	}
        $returnGoodsModel =new ReturnGoods();
        $count = $returnGoodsModel->where($where)->count();
    	$page = new Page($count,10);
    	$list = $returnGoodsModel->where($where)->order("id desc")->limit($page->firstRow, $page->listRows)->select();
    	$goods_id_arr = get_arr_column($list, 'goods_id');
    	if(!empty($goods_id_arr))
    		$goodsList = Db::name('goods')->where("goods_id in (".  implode(',',$goods_id_arr).")")->getField('goods_id,goods_name');
    	$this->assign('goodsList', $goodsList);
    	$this->assign('list', $list);
    	$this->assign('page', $page->show());// 赋值分页输出
    	return $this->fetch();
    }
    
    /**
     *  退货详情
     */
    public function return_goods_info()
    {
    	$id = I('id/d',0);
        $user_id = $this->user_id;
        $ReturnGoodsModel = new \app\common\model\ReturnGoods();
        $return_goods=$ReturnGoodsModel::get(['id' => $id,'user_id'=>$user_id]);
        if(empty($return_goods)) $this->error('参数错误');
    	if(IS_POST){
    		$data = I('post.');
    		$data['delivery'] = serialize($data['delivery']);
    		$data['status'] = 2;
    		M('return_goods')->where(['id'=>$data['id'],'user_id'=>$user_id])->save($data);
    		$this->success('发货提交成功',U('Home/Order/return_goods_info',array('id'=>$data['id'])));
    	}
    	if($return_goods['imgs']) $return_goods['imgs'] = explode(',', $return_goods['imgs']);
    	if($return_goods['seller_delivery']) {
    		$return_goods['seller_delivery'] = unserialize($return_goods['seller_delivery']);
    	}
    	if($return_goods['delivery']) {
    		$return_goods['delivery'] = unserialize($return_goods['delivery']);
    	}
    	$goods = M('goods')->where("goods_id = {$return_goods['goods_id']} ")->find();
    	$this->assign('goods',$goods);
    	$this->assign('return_goods',$return_goods);

        $refued_address= Db::name('delivery_doc')->where(['order_id'=>$return_goods['order_id']])->find();
        if ($refued_address) {
            $address_province_id = $refued_address['store_address_province_id'];
            $address_city_id     = $refued_address['store_address_city_id'];
            $address_district_id = $refued_address['store_address_district_id'];
            $address['store_phone'] = $refued_address['store_address_mobile'];
            $address['store_name'] = $refued_address['store_address_consignee'];
        }else{
            $order = db('order')->where('order_id',$return_goods['order_id'])->field('shop_id')->find();
            $refued_address = db('shop')->where('shop_id',$order['shop_id'])->find();
            $address_province_id = $refued_address['province_id'];
            $address_city_id     = $refued_address['city_id'];
            $address_district_id = $refued_address['district_id'];
            $address['store_phone'] = $refued_address['shop_phone_code'].'-'.$refued_address['shop_phone'];
            $address['store_name'] = $refued_address['shopper_name'];
        }

        $region = Db::name('region')
            ->where("id in($address_province_id,$address_city_id,{$address_district_id})")
            ->getField('id,name');
        $address['store_address'] = $region["$address_province_id"].$region["$address_city_id"].$region["$address_district_id"].$refued_address['store_address'];

    	$this->assign('store',$address);
        $this->assign('return_type',C('RETURN_TYPE'));
    	return $this->fetch();
    }
    
    public function return_goods_refund()
    {
    	$order_sn = I('order_sn');
    	$where = array('user_id'=>$this->user_id);
    	if($order_sn){
    		$where['order_sn'] = $order_sn;
    	}
    	$where['status'] = 5;
    	$count = M('return_goods')->where($where)->count();
    	$page = new Page($count,10);
    	$list = M('return_goods')->where($where)->order("id desc")->limit($page->firstRow, $page->listRows)->select();
    	$goods_id_arr = get_arr_column($list, 'goods_id');
    	if(!empty($goods_id_arr))
    		$goodsList = M('goods')->where("goods_id in (".  implode(',',$goods_id_arr).")")->getField('goods_id,goods_name');
    	$this->assign('goodsList', $goodsList);
    	$state = C('RETURN_STATUS');
    	$this->assign('list', $list);
    	$this->assign('state',$state);
    	$this->assign('page', $page->show());// 赋值分页输出
    	return $this->fetch();
    }

    /**
     * 取消服务单
     */
    public function return_goods_cancel(){
    	$id = I('id/d',0);
    	if(empty($id))$this->ajaxReturn(['status'=>0,'msg'=>'参数错误']);
    	$res=M('return_goods')->where(['id'=>$id,'user_id'=>$this->user_id])->save(array('status'=>-2,'canceltime'=>time()));
        if($res){
            $this->ajaxReturn(['status'=>1,'msg'=>'成功取消服务单']);
        }
            $this->ajaxReturn(['status'=>0,'msg'=>'服务单不存在']);
    }
    
    public function dispute(){
        $Order = new \app\common\model\Order();
    	$condition['user_id'] = $this->user_id;
    	$condition['pay_status'] = 1;
        //查找申请过的订单ID
        $complain_order_ids = DB::name('complain')->where(['user_id'=>$condition['user_id']])->getField('order_id',true);
        //申请过的订单不显示
        $count = DB::name('order')
            ->whereNotIn('order_id',$complain_order_ids,'and')
            ->where($condition)->count();
        $Page  = new Page($count,5);
        $show = $Page->show();
        $order_str = "order_id DESC";
        //获取订单
        $order_list_obj = $Order->order($order_str)
            ->whereNotIn('order_id',$complain_order_ids,'and')
            ->where($condition)->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();
        if($order_list_obj){
            //转为数组，并获取订单状态，订单状态显示按钮，订单商品
            $order_list=collection($order_list_obj)->append(['order_status_detail','order_button','order_goods','store'])->toArray();
        }
        $store_list = Db::name('store')->getField('store_id,store_name,store_qq');
        $this->assign('order_list',$order_list);
        $this->assign('store_list',$store_list);
        $this->assign('page',$show);
        return $this->fetch();
    }

    public function dispute_list(){
        $complain_time_out = input('complain_time_out');
        $complain_state_type = input('complain_state_type');
        $where = array('user_id'=>$this->user_id);
        $three_months_ago = strtotime(date("Y-m-d", strtotime("-3 months",  strtotime(date("Y-m-d",time())))));

        $complainModel = new Complain();
        if(empty($complain_time_out)){
            //三个月内纠纷单
            $where['complain_time'] = ['>',$three_months_ago];
        }
        if($complain_time_out){
            //三个月前纠纷单
            $where['complain_time'] = ['<',$three_months_ago];
        }
        if($complain_state_type == 1){
            //处理中
            $where['complain_state'] = ['<',4];
        }
        if($complain_state_type == 2){
            //已完成
            $where['complain_state'] = 4;
        }
        $count = $complainModel->where($where)->count();
        $page = new Page($count,10);
        $complainObj = $complainModel->where($where)->order("complain_id desc")->limit($page->firstRow, $page->listRows)->select();
        if ($complainObj){
            $list = collection($complainObj)->append(['goods','complain_state','complain_pic'])->toArray();
        }
        $this->assign('list', $list);
        $this->assign('page', $page->show());
        return $this->fetch();
    }
    
    public function dispute_apply(){
    	$order_id = I('order_id');
    	$order = M('order')->where(array('order_id'=>$order_id,'user_id'=>$this->user_id))->find();
    	$order_goods = M('order_goods')->where(array('order_id'=>$order_id))->select();
    	$this->assign('order',$order);
    	$this->assign('order_goods',$order_goods);
    	$complain_subject = M('complain_subject')->where(array('subject_state'=>1))->select();
    	$this->assign('complain_subject',$complain_subject);
    	$store = M('store')->where(array('store_id'=>$order['store_id']))->find();
    	$this->assign('store',$store);
    	return $this->fetch();
    }

    public function addDisputeApply(){
        $data = I('post.');
        $order = M('order')->where(array('order_id'=>$data['order_id']))->find();
        if($order['store_id'] != $data['store_id'] || $order['user_id'] != $this->user_id){
            $this->ajaxReturn(['status'=>0,'msg'=>'严禁非法提交数据']);
        }
        $complain_time_limit = tpCache('complain.complain_time_limit');
        if(($complain_time_limit*3600*24 + $order['add_time']) < time()){
            $this->ajaxReturn(['status'=>0,'msg'=>'该订单已超过投诉有效期']);
        }
        $complain = M('complain')->where(array('order_id'=>$data['order_id'],'user_id'=>$this->user_id,'order_goods_id'=>$data['order_goods_id']))->find();
        if($complain)
            $this->ajaxReturn(['status'=>0,'msg'=>'此服务单您已申请过交易投诉',
                'url'=>U('Order/dispute_info',['complain_id'=>$complain['complain_id']])]);
        if(!empty($data['complain_pic'])){
            $data['complain_pic'] = serialize($data['complain_pic']);
        }
        $complain_subject = M('complain_subject')->where(array('subject_id'=>$data['complain_subject_id']))->find();
        $data['complain_subject_name'] = $complain_subject['subject_name'];
        $data['user_id'] = $this->user_id;
        $data['user_name'] = $this->user['nickname'];
        $data['complain_time'] = time();
        $data['user_handle_time'] = time();
        $data['order_sn'] = $order['order_sn'];
        if(M('complain')->add($data)){
            $this->ajaxReturn(['status'=>1,'msg'=>'投诉成功','url'=>U('Order/dispute_list')]);
        }else{
            $this->ajaxReturn(['status'=>0,'msg'=>'投诉失败，请联系平台客服','url'=>U('Order/dispute')]);
        }
    }
    
    public function checkType(){
    	$order_id = I('order_id/d');
    	$complain_subject_id = I('complain_subject_id/d');
    	if($order_id && $complain_subject_id){
    		$orderLogic = new OrderLogic();
    		$res = $orderLogic->check_dispute_order($order_id, $complain_subject_id,$this->user_id);
    		$this->ajaxReturn($res);
    	}else{
    		exit(json_encode("参数错误，非法操作"));
    	}
    }

    /**
     * 交易投诉单详情
     * @return mixed
     */
    public function dispute_info(){
    	$complain_id = I('complain_id/d');
    	$complain = M('complain')->where(array('complain_id'=>$complain_id,'user_id'=>$this->user_id))->find();
    	if($complain){
    		if(!empty($complain['complain_pic'])){
    			$complain['complain_pic'] = unserialize($complain['complain_pic']);
    		}
    		if(!empty($complain['appeal_pic'])){
    			$complain['appeal_pic'] = unserialize($complain['appeal_pic']);
    		}
    	}else{
    		$this->error("您的投诉单不存在");
    	}
        $last_complain_talk = Db::name('complain_talk')->where(['complain_id'=>$complain['complain_id']])->order('talk_time desc')->find();
        $complain_handle_time = $complain['appeal_time']+7*24*60*60;  //先写死7天就过期，以后弄个后台设置什么的
        $last_talk_time = $last_complain_talk['talk_time']+7*24*60*60;  //先写死7天就过期，以后弄个后台设置什么的
        $todate = strtotime(date('Y-m-d'));
        if(($todate > $complain_handle_time || (!empty($last_complain_talk) && $todate>$last_talk_time)) ){  //用户超时没反应
            if($last_complain_talk['talk_member_id'] !=$this->user_id  && $complain['complain_state']==2){  //最后回复的不是用户，且还是对话中
                M('complain')->where(['complain_id'=>$complain_id,'user_id'=>$this->user_id])->update(['final_handle_msg'=>'用户过期没任何回复,系统默认已完成','complain_state'=>4,'final_handle_time'=>time()]);
            };
        }

    	$order = M('order')->where(array('order_id'=>$complain['order_id']))->find();
    	$order_goods = M('order_goods')->where(array('order_id'=>$complain['order_id'],'goods_id'=>$complain['order_goods_id']))->find();
        $store_avatar = Db::name('store')->field('store_avatar')->where(['store_id'=>$order['store_id']])->find();
    	$this->assign('complain',$complain);
    	$this->assign('order',$order);
    	$this->assign('store',$store_avatar);
    	$this->assign('order_goods',$order_goods);
    	$complain_state = array(1=>'待卖家处理',2=>'待客户确认',3=>'待管理员仲裁',4=>'已关闭完成');
    	$this->assign('state',$complain_state);
    	$this->assign('head_pic',$this->user['head_pic']);
    	return $this->fetch();
    }

    public function get_complain_talk(){
    	$complain_id = I('complain_id/d');
    	$complain_info = M('complain')->where(array('complain_id'=>$complain_id,'user_id'=>$this->user_id))->find();
    	$talkhtml = '';
    	if(!$complain_info){
    		$talkhtml = '';
    	}else{
    		$complain_info['member_status'] = 'accused';
    		$complain_talk_list = M('complain_talk')->where(array('complain_id'=>$complain_id))->order('talk_id asc')->select();
            $store = Db::name('store')->field('store_avatar')->where(['store_id'=>$complain_info['store_id']])->find();
    		if(!empty($complain_talk_list)){
    			foreach($complain_talk_list as $i=>$talk) {
    				$talk_time = date("Y-m-d H:i:s",$talk['talk_time']);
    				$myself_right = '';
    				$talker_name = $talk['talk_member_name'];
    				$path = C('view_replace_str.__STATIC__');
    				switch($talk['talk_member_type']){
    					case 'accuser':
    						$talker = '我';
    						$talker_pic = empty($this->user['head_pic']) ? $path.'/images/pers.png' : $this->user['head_pic'] ;
    						$myself_right = 'myself_right';
    						break;
    					case 'accused':
    						$talker = '卖家';
    						$talker_pic = $store['store_avatar'];  //'/images/oppositehead.png'
    						break;
    					case 'admin':
    						$talker = '管理员';
    						$talker_pic = $path.'/images/pers.png';
    						break;
    				}
    				if(intval($talk['talk_state']) === 2) {
    					$talk['talk_content'] = '<该对话被管理员屏蔽>';
    				}
    				$talkhtml .= '<div class="opposite_left '.$myself_right.' p">
	    			<div class="sales_head p"><div class="sales_head_logo">
	    				<img class="" src="'.$talker_pic.'">
	    			</div>
	    			<div class="explay_sales_head">
	    			<i></i>
	    			<span class="sales_manage">'.$talker.'</span>
	    			<span class="store_name">'.$talker_name.'&nbsp;&nbsp;'.$talk_time.'</span>
	    			</div></div>
	    			<div class="myself_head">'.$talk['talk_content'].'</div></div>';
    			}
    		}
    	}

    	echo json_encode($talkhtml);
    }
    
    public function publish_complain_talk(){
    	$complain_id = I('complain_id/d');
    	$complain_talk = trim(I('complain_talk'));
    	$complain_info = M('complain')->where(array('complain_id'=>$complain_id,'user_id'=>$this->user_id))->find();
    	$complain_state = intval($complain_info['complain_state']);
    	if(is_array($complain_info) && $complain_state==2){
    		$talk_len = strlen($complain_talk);
    		if($talk_len>0 && $talk_len<255){
    			$param = array();
    			$param['complain_id'] = $complain_id;
    			$param['talk_member_id'] = $this->user_id;
    			$param['talk_member_name'] = $this->user['nickname'];
    			$param['talk_member_type'] = 'accuser';
    			$param['talk_content'] = $complain_talk;
    			$param['talk_state'] = 1;
    			$param['talk_admin'] = 0;
    			$param['talk_time'] = time();
    			if(M('complain_talk')->add($param)){
    				echo json_encode('success');
    			}else{
    				echo json_encode('error2');
    			}
    		}else{
    			echo json_encode('error1');
    		}
    	}else{
    		echo json_encode('error');
    	}
    }
    
    public function complain_handle(){
    	$complain_id = I('complain_id/d');
    	$complain_state = I('state/d');
    	$complain_info = M('complain')->where(array('complain_id'=>$complain_id,'user_id'=>$this->user_id))->find();
    	if($complain_info){
    		$updata['complain_state'] = $complain_state;
    		if($complain_state == 3){
    			M('return_goods')->where(array('user_id'=>$this->user_id,'order_id'=>$complain_info['order_id']))->save(array('status'=>6));
    			$updata['user_handle_time'] = time();
    		}else{
    			$updata['final_handle_time'] = time();
    			$updata['final_handle_msg'] = '用户提交问题已解决';
    		}
    		M('complain')->where(array('complain_id'=>$complain_id,'user_id'=>$this->user_id))->save($updata);
    		$this->success('操作成功',U('Order/dispute_list'));exit;
    	}else{
    		$this->error('操作失败，请联系平台客服');
    	}
    }
    
    public function expose(){
    	if(IS_POST){
    		$data = I('post.');
    		if(!empty($data['expose_pic'])){
    			$data['expose_pic'] = serialize($data['expose_pic']);
    		}
    		$data['expose_user_id'] = $this->user_id;
    		$data['expose_user_name'] = empty($this->user['nickname']) ? $this->user['mobile'] : $this->user['nickname'];
    		$data['expose_time'] = time();
    		if(Db::name('expose')->where(['expose_user_id'=>$this->user_id,'expose_goods_id'=>$data['expose_goods_id']])->count()>0){
    			$this->ajaxReturn(['status'=>'0','msg'=>'该商品您已举报过，请不要重复提交']);
    		}else{
                Db::name('expose')->add($data);
                $this->ajaxReturn(['status'=>'1','msg'=>'举报成功','url'=>U('Order/expose_list')]);exit;
    		}
    	}
    	$goods_id = I('goods_id/d');
    	$goods = Db::name('goods')->where(array('goods_id'=>$goods_id))->find();
    	if($goods){
    		$store = Db::name('store')->where(array('store_id'=>$goods['store_id']))->find();
    		$expose_type = Db::name('expose_type')->where(['expose_type_state'=>1])->select();
    		$goods['category'] = Db::name('goods_category')->where(array('id'=>$goods['cat_id3']))->getField('name');
    		$this->assign('goods',$goods);
    		$this->assign('store',$store);
    		$this->assign('expose_type',$expose_type);
    		return $this->fetch();
    	}else{
            $this->ajaxReturn(['status'=>'0','msg'=>'参数错误']);
    	}
    }
    
    public function expose_list(){
    	$where = array('expose_user_id'=>$this->user_id);
    	$count = M('expose')->where($where)->count();
    	$page = new Page($count,20);
    	$expose_list = M('expose')->where($where)->order("expose_id desc")->limit($page->firstRow, $page->listRows)->select();
    	$this->assign('expose_list', $expose_list);
    	$this->assign('page', $page->show());
    	return $this->fetch();
    }
    
    public function expose_info(){
    	$expose_id = I('expose_id/d');
    	$expose = M('expose')->where(array('expose_id'=>$expose_id,'expose_user_id'=>$this->user_id))->find();
    	if(!$expose){
    		$this->error('该举报不存在');
    	}
    	if(!empty($expose['expose_pic'])){
    		$expose['expose_pic'] = unserialize($expose['expose_pic']);
    	}
    	$store = M('store')->where(array('store_id'=>$expose['expose_store_id']))->find();
        $expose_handle = [
            '1'=>'经核查，该举报无效，商品会正常销售',
            '2'=>'经核查，该举报属于恶意举报',
            '3'=>'经核查，该举报有效，被举报商品将被违规下架。',
        ];
        $expose['expose_handle'] =$expose_handle[$expose['expose_handle_type']];
    	$this->assign('store',$store);
    	$this->assign('expose',$expose);
    	return $this->fetch();
    }
    
    public function get_expose_subject(){
    	$expose_type_id = I('expose_type_id/d');
    	$expose_subject = Db::name('expose_subject')->where(['expose_subject_type_id'=>$expose_type_id,'expose_subject_state'=>1])->select();
    	$subject = '';
    	if(empty($expose_subject)){
    		$subject = '<txt style="position: absolute; z-index: 2; line-height: 1; margin-left: 11px; margin-top: 11px; font-size: 13.3333px; font-family: monospace; color: rgb(205, 205, 205); display: inline;"></txt>
    				<textarea name="expose_content" id="note" cols="30" rows="10" style="border: 1px solid #E6E6E6;width: 935px; height: 144px;margin-bottom: 8px;padding: 5px;" placeholder="请填写您认为该商品存在价格违规现象的理由"></textarea>
    				<div class="msg-care">(注意：请您注意不要在此框填写会员名、订单号、运单号等任何可能泄露身份的信息)</div>';
    	}else{
    		$subject .= '<ul class="re-jbtype-box re-jbtype-s01">';
    		foreach ($expose_subject as $val){
    			$subject .='<li class="li-item" onclick="subject_onclick(this)" data-type="'.$val['expose_subject_id'].'">'.$val['expose_subject_content'].'<s class="icon-on"></s></li>';
    		}
    		$subject .= '</ul>';
    	}
    	exit($subject);
    }
    
    public function lower_list(){
    	$level = I('get.level',1);
    	$q = I('post.q','','trim');
    	$condition = array(1=>'first_leader',2=>'second_leader',3=>'third_leader');
    	$where = "{$condition[$level]} = {$this->user_id}";
    	$q && $where .= " and (nickname like '%$q%' or user_id = '$q' or mobile = '$q')";
    
    	$count = M('users')->where($where)->count();
    	$page = new Page($count,10);
    	$list = M('users')->where($where)->limit("{$page->firstRow},{$page->listRows}")->order('user_id desc')->select();
    
    	$this->assign('count', $count);// 总人数
    	$this->assign('level',$level);
    	$this->assign('page', $page->show());// 赋值分页输出
    	$this->assign('member',$list); // 下线
    	return $this->fetch();
    }
    
    public function income(){
    	$result = Db::query("select sum(goods_price) as goods_price, sum(money) as money from __PREFIX__rebate_log where user_id = {$this->user_id}");
    	$result = $result[0];
    	$result['goods_price'] = $result['goods_price'] ? $result['goods_price'] : 0;
    	$result['money'] = $result['money'] ? $result['money'] : 0;
    	$status = I('get.status',-2);
    
    	if($status=='0' || $status>0){
    		$condition['status'] = $status;
    	}
    
    	$condition['user_id'] = $this->user_id;
    	$count = M('rebate_log')->where($condition)->count();
    	$page = new Page($count,10);
    	$rebate_log = M('rebate_log')->where($condition)->limit("{$page->firstRow},{$page->listRows}")->order('user_id desc')->select();
    	$this->assign('page', $page->show());// 赋值分页输出
    	$this->assign('rebate_log',$rebate_log);
    	$this->assign('status',$status);
    	$this->assign('result',$result);
    	return $this->fetch();
    }
}