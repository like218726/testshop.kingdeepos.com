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
 * 2016-11-21
 */
namespace app\api\controller;

use app\common\logic\VirtualLogic;
use app\common\logic\OrderLogic;
use think\Page;

class Virtual extends Base
{
     
    public $user = array();
    public $virtualLogic;

    public function _initialize()
    {
        parent::_initialize();
        $this->virtualLogic = new VirtualLogic();
        if (session('?user')) {
            $user = session('user');
            /* $user = M('users')->where("user_id", $user['user_id'])->find();
            session('user', $user);  //覆盖session 中的 user
            $this->user = $user;
            $this->user_id = $user['user_id'];
            $this->assign('user', $user); //存储用户信息
            $this->assign('user_id', $this->user_id); */
        }  
        
    }
  
    public function add_order()
    {
        $data = I('post.');
        $result = $this->virtualLogic->check_virtual_goods($data['goods_id'] , $data['item_id'] , $data['goods_num']);
    	if ($result['status'] !== 1) {
    	    $this->ajaxReturn($result);
    	}
    	$goods = $result['goods'];
    	
    	$CartLogic = new OrderLogic();
    	$goods_price = $goods['shop_price']*$goods['goods_num'];
        $isbuyWhere = [
            'og.goods_id'=>$data['goods_id'],
            'o.user_id'=>$this->user_id,
            'o.deleted'=>0,
            'o.order_status'=>['neq',3]
        ];
    	$isbuy = M('order_goods')->alias('og')
            ->join(C('DB_PREFIX').'order o','og.order_id = o.order_id','LEFT')
    	    ->where($isbuyWhere)
            ->sum('og.goods_num');
    	if(($goods['goods_num']+$isbuy)>$goods['virtual_limit']){//查询限购数量
    		$this->ajaxReturn(['status'=>'-1','msg'=>'您已超过该商品的限制购买数']);
    	}
    
    	$data['consignee'] = empty($this->user['nickname']) ? $this->user['realname'].$this->user['mobile'] : $this->user['nickname'];
    	$data['user_note'] = empty($data['user_note'])?'无备注':$data['user_note'];
    	$orderArr = array('user_id'=>$this->user_id,'mobile'=>$data['mobile'],'user_note'=>$data['user_note'],
    			'order_sn'=>$CartLogic->get_order_sn(),'goods_price'=>$goods_price,'consignee'=>$data['consignee'],
    			'prom_type'=>5,'add_time'=>time(),'store_id'=>$goods['store_id'],
    			'order_amount'=>$goods_price,'total_amount'=>$goods_price,'shipping_time'=>$goods['virtual_indate']//有效期限
    	);
    	$order_id = M('order')->add($orderArr);
        
    	//变更成本价
    	$cost = M('spec_goods_price')->where(array('goods_id'=>$goods['goods_id'],'key'=>$goods['goods_spec_key']))->value('cost');
    	
    	$data2['order_id'] = $order_id; // 订单id
        $data2['item_id']            = $goods['item_id'];//商品规格
    	$data2['goods_id']           = $goods['goods_id']; // 商品id
    	$data2['goods_name']         = $goods['goods_name']; // 商品名称
    	$data2['goods_sn']           = $goods['goods_sn']; // 商品货号
    	$data2['goods_num']          = $goods['goods_num']; // 购买数量
    	$data2['market_price']       = $goods['market_price']; // 市场价
    	$data2['goods_price']        = $goods['shop_price']; // 商品价
    	$data2['spec_key']           = $goods['goods_spec_key']; // 商品规格
    	$data2['spec_key_name']      = $goods['spec_key_name']; // 商品规格名称
    	$data2['sku']                = $goods['sku']; // 商品条码
    	$data2['member_goods_price'] = $goods['shop_price']; // 会员折扣价
    	$data2['cost_price']         = $cost?$cost:$goods['cost_price']; // 成本价
    	$data2['give_integral']      = $goods['give_integral']; // 购买商品赠送积分
    	$data2['prom_type']          = $goods['prom_type']; // 0 普通订单,1 限时抢购, 2 团购 , 3 促销优惠
    	$data2['store_id']           = $goods['store_id']; // 店铺id
    	$data2['distribut']          = $goods['distribut']; // 三级分销金额
    	$data2['commission']         = M('goods_category')->where("id = {$goods['cat_id3']}")->cache(true,TPSHOP_CACHE_TIME)->getField('commission'); //商品抽成比例
    	$order_goods_id              = M("OrderGoods")->add($data2);

        $orderObj = db('order')->where('order_id', $order_id)->find();
        $reduce = tpCache('shopping.reduce');
        if($reduce== 1 || empty($reduce)){
            minus_stock($orderObj);//下单减库存
        }

    	if($order_goods_id){
            $this->ajaxReturn(['status'=>'1','msg'=>'虚拟商品成功','result'=>$orderArr['order_sn']]);
    	}else{
    		$this->ajaxReturn(['status'=>'-1','msg'=>'虚拟商品下单失败']);
    	}
    }

    /**
     * 虚拟订单列表
     */
    public function virtual_list()
    {
        $type = I('get.type');
        $search_key = I('search_key');
        $virtualLogic = new \app\common\logic\VirtualLogic;
        $result = $virtualLogic->orderList($this->user_id, $type, $search_key);        
        $this->ajaxReturn(['status'=>1,'msg'=>'获取成功','result'=>$result['order_list']]);
    }

    /**
     * 虚拟订单详情
     */
    public function virtual_order(){
        $Order = new \app\common\model\Order();
        $order_id = I('get.order_id/d');
        $map['order_id'] = $order_id;
        $map['user_id'] = $this->user_id;
        $orderobj = $Order->where($map)->find();
        if(!$orderobj){
            ('没有获取到订单信息');
        }
        // 添加属性  包括按钮显示属性 和 订单状态显示属性
        $order_info = $orderobj->append(['order_status_detail','virtual_order_button','order_goods','store'])->toArray();
        if($order_info['prom_type'] != 5){   //普通订单
            $this->redirect(U('Order/order_detail',['id'=>$order_id]));
        }
        //获取订单操作记录
        $order_action = M('order_action')->where(array('order_id'=>$order_id))->select();
        $vrorder = M('vr_order_code')->where(array('order_id'=>$order_id))->find();
        $this->assign('vrorder',$vrorder);
        $this->assign('order_status',C('ORDER_STATUS'));
        $this->assign('pay_status',C('PAY_STATUS'));
        $this->assign('order_info',$order_info);
        $this->assign('order_action',$order_action);
        return $this->fetch();
    }
}  