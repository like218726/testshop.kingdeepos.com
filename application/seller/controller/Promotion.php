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
 * 专题管理
 * Date: 2016-06-09
 */

namespace app\seller\controller;
 
use app\seller\logic\GoodsLogic;
use app\common\model\FlashSale;
use app\common\model\Goods;
use app\common\model\GroupBuy;
use app\common\model\PromGoods;
use app\common\model\PromOrder;
use think\Cache;
use think\Db;
use think\Page;
use think\Loader;

class Promotion extends Base
{
    public $store_id;

    public function __construct()
    {
        parent::__construct();
        $this->store_id = STORE_ID;
    }
    /**
     * 商品活动列表
     */
    public function prom_goods_list()
    {
        $PromGoods = new PromGoods();
        $count = $PromGoods->where("store_id", $this->store_id)->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $prom_list = $PromGoods->where("store_id", $this->store_id)->order('id', desc)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('prom_list', $prom_list);
        return $this->fetch();
    }

    public function prom_goods_info()
    {
        $this->assign('min_date', date('Y-m-d'));
        $level = M('user_level')->select();
        $this->assign('level', $level);
        $prom_id = I('id/d');
        $info['start_time'] = date('Y-m-d');
        $info['end_time'] = date('Y-m-d', time() + 3600 * 60 * 24);
        if ($prom_id > 0) {
            $info = M('prom_goods')->where("id", $prom_id)->where('store_id',$this->store_id)->find();
            if(empty($info)){
                $this->error('该促销记录不翼而飞了');
            }
            $info['start_time'] = date('Y-m-d', $info['start_time']);
            $info['end_time'] = date('Y-m-d', $info['end_time']);
            $Goods = new Goods();
            $prom_goods = $Goods->with('SpecGoodsPrice')->where(['prom_id' => $prom_id, 'prom_type' => 3])->select();
            $this->assign('prom_goods', $prom_goods);
            $info['prom_id'] = $prom_id;
        }
        $info['prom_type'] = 3;
        $this->assign('store_id', $this->store_id);
        $this->assign('info', $info);
        $this->assign('min_date', date('Y-m-d'));
        $coupon_list = M('coupon')
            ->where(['store_id'=>STORE_ID,'type'=>0,'status'=>1,'use_start_time'=>['lt',time()],'use_end_time'=>['gt',time()]])
            ->select();
        $this->assign('coupon_list',$coupon_list);
        return $this->fetch();
    }

    public function prom_goods_save()
    {
        $prom_id = I('id/d');
        $data = I('post.');
        $title = input('title');
        $promGoods = $data['goods'];
        $promGoodsValidate = Loader::validate('PromGoods');
        if(!$promGoodsValidate->batch()->check($data)){
            $return = ['status' => 0,'msg' =>'操作失败',
                'result'    => $promGoodsValidate->getError(),
                'token'       =>  \think\Request::instance()->token(),
            ];
            $this->ajaxReturn($return);
        }
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time'] = strtotime($data['end_time']);
//        $data['group'] = (empty($data['group'])) ? '' : implode(',', $data['group']); //前台暂时不用这个功能，先注释
        $goods_ids = [];
        $item_ids = [];
        foreach ($promGoods as $goodsKey => $goodsVal) {
            if (array_key_exists('goods_id', $goodsVal)) {
                array_push($goods_ids, $goodsVal['goods_id']);
            }
            if (array_key_exists('item_id', $goodsVal)) {
                $item_ids = array_merge($item_ids, $goodsVal['item_id']);
            }
        }
        if ($prom_id) {
            $PromGoods = new \app\common\model\PromGoods();
            $promGoods = $PromGoods->where(['id' => $prom_id, 'store_id' => $this->store_id])->find();
            if(empty($promGoods)){
                $this->ajaxReturn(['status'=>0,'msg'=>'该促销记录不翼而飞啦~','result']);
            }
            $promGoods->save($data);
            $last_id = $prom_id;
            sellerLog("管理员修改了商品促销 " . $title);
        } else {
            $data['store_id'] = $this->store_id;
            $last_id = M('prom_goods')->add($data);
            sellerLog("管理员添加了商品促销 " . $title);
            if($data['mmt_message_switch'] == 1) {
                $PromGoods = new \app\common\model\PromGoods();
                $promGoods = PromGoods::get($last_id);
                $message_logic = new \app\common\logic\MessageActivityLogic();
                $message_logic->sendPromGoods($promGoods);
            }

        }
        M("goods")->where(['prom_id' => $prom_id, 'prom_type' => 3, 'store_id' => $this->store_id])->save(array('prom_id' => 0, 'prom_type' => 0));
        M("goods")->where("goods_id", "in", $goods_ids)->save(array('prom_id' => $last_id, 'prom_type' => 3));
        Db::name('spec_goods_price')->where(['prom_id' => $prom_id, 'prom_type' => 3, 'store_id' => $this->store_id])->update(['prom_id' => 0, 'prom_type' => 0]);
        Db::name('spec_goods_price')->where('item_id','IN',$item_ids)->update(['prom_id' => $last_id, 'prom_type' => 3]);
        $this->ajaxReturn(['status'=>1,'msg'=>'编辑促销活动成功','result']);
    }

    public function prom_goods_del()
    {
        $prom_id = I('id/d');
        $order_goods = M('order_goods')->where(['prom_type' => 3, 'prom_id' => $prom_id])->find();
        if (!empty($order_goods)) {
            $this->ajaxReturn(['status'=>0,'msg'=>'该活动有订单参与不能删除!']);
        }
        $PromGoods = new \app\common\model\PromGoods();
        $promGoods = $PromGoods->where(['id' => $prom_id, 'store_id' => $this->store_id])->find();
        if(empty($promGoods)){
            $this->ajaxReturn(['status'=>0,'msg'=>'该促销记录不翼而飞啦~','result']);
        }
        M("goods")->where(['prom_id' => $prom_id, 'prom_type' => 3, 'store_id' => $this->store_id])->save(array('prom_id' => 0, 'prom_type' => 0));
        Db::name('spec_goods_price')->where(['prom_type' => 3, 'prom_id' => $prom_id])->update(['prom_id' => 0, 'prom_type' => 0]);
        $promGoods->delete();
        $message_logic = new \app\common\logic\MessageActivityLogic([]);
        $message_logic->deletedMessage($prom_id, 3);
        $this->ajaxReturn(['status'=>1,'msg'=>'删除活动成功']);
    }


    /**
     * 订单活动列表
     */
    public function prom_order_list()
    {
        $PromOrder = new PromOrder();
        $count = $PromOrder->where("store_id", $this->store_id)->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $prom_list = $PromOrder->where("store_id", $this->store_id)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('prom_list', $prom_list);
        return $this->fetch();
    }

    public function prom_order_info()
    {
        $this->assign('min_date', date('Y-m-d'));
        $level = M('user_level')->select();
        $this->assign('level', $level);
        $prom_id = I('id/d');
        $info['start_time'] = date('Y-m-d');
        $info['end_time'] = date('Y-m-d', time() + 3600 * 24 * 60);
        if ($prom_id > 0) {
            $info = M('prom_order')->where("id", $prom_id)->where('store_id',$this->store_id)->find();
            if(empty($info)){
                $this->error('该订单优惠记录不翼而飞了');
            }
            $info['start_time'] = date('Y-m-d', $info['start_time']);
            $info['end_time'] = date('Y-m-d', $info['end_time']);
        }
        $this->assign('info', $info);
        $this->assign('min_date', date('Y-m-d'));
        $this->assign('store_id', $this->store_id);
        $coupon_list = M('coupon')
            ->where(['store_id'=>STORE_ID,'type'=>0,'status'=>1,'use_start_time'=>['lt',time()],'use_end_time'=>['gt',time()]])
            ->select();
        $this->assign('coupon_list',$coupon_list);
        return $this->fetch();
    }

    public function prom_order_save()
    {
        $prom_id = I('id/d');
        $data = I('post.');
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time'] = strtotime($data['end_time']);

        $promGoodsValidate = Loader::validate('PromOrder');
        if(!$promGoodsValidate->batch()->check($data)){
            $return = ['status' => 0,'msg' =>'操作失败，请确定各项内容',
                'result' => $promGoodsValidate->getError(),
                'token'  =>  \think\Request::instance()->token(),
            ];
            $this->ajaxReturn($return);
        }
//        $data['group'] = implode(',', $data['group']);  //前台暂时不用这个功能，先注释
        if ($prom_id) {
            $prom_order = M('prom_order')->where(['id' => $prom_id, 'store_id' => $this->store_id])->find();
            if(empty($prom_order)){
                $this->ajaxReturn(['status' => 0,'msg' =>'该订单促销活动记录不翼而飞啦~','url'=>U('Promotion/prom_order_list')]);
            }
            M('prom_order')->where(['id' => $prom_id, 'store_id' => $this->store_id])->save($data);
            sellerLog("管理员修改了商品促销 " . I('name'));
        } else {
            $data['store_id'] = $this->store_id;
            $last_id = M('prom_order')->add($data);
            sellerLog("管理员添加了商品促销 " . I('name'));
            if($data['mmt_message_switch'] == 1) {
                $PromOrder = new \app\common\model\PromOrder();
                $promOrder = $PromOrder::get($last_id);
                $message_logic = new \app\common\logic\MessageActivityLogic();
                $message_logic->sendPromOrder($promOrder);
            }
        }
        $this->ajaxReturn(['status' => 1,'msg' =>'编辑促销活动成功','url'=>U('Promotion/prom_order_list')]);
    }

    public function prom_order_del()
    {
        $prom_id = I('id/d');
        $order = M('order')->where(['order_prom_id' => $prom_id, 'store_id' => $this->store_id])->find();
        if (!empty($order)) {
            $this->ajaxReturn(['status'=>0,'msg'=>'该活动有订单参与不能删除!']);
        }
        $message_logic = new \app\common\logic\MessageActivityLogic([]);
        $message_logic->deletedMessage($prom_id, 9);
        M('prom_order')->where(['id' => $prom_id, 'store_id' => $this->store_id])->delete();
        $this->ajaxReturn(['status'=>1,'msg'=>'删除活动成功']);
    }

    public function group_buy_list()
    {
        $key_word = input('key_word');
        $where = " 1 = 1 ";
        if ($key_word) {
            $where .= " and ( title like '%" .$key_word. "%' or goods_name like '%" .$key_word."%')";
        }
        $GroupBuy = new GroupBuy();
        $count = $GroupBuy->where($where)->where("store_id", $this->store_id)->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $list = $GroupBuy->where($where)->order('id desc')->where("store_id", $this->store_id)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->assign('key_word', $key_word);
        return $this->fetch();
    }

    //团购促销
    public function group_buy()
    {
        $act = I('GET.act', 'add');
        $groupbuy_id = I('get.id/d');
        $group_info = array();
        $group_info['start_time'] = date('Y-m-d');
        $group_info['end_time'] = date('Y-m-d', time() + 3600 * 365);
        if ($groupbuy_id) {
            $GroupBy = new GroupBuy();
            $group_info = $GroupBy->with('groupBuyGoodsItem,goods')->where(['id' => $groupbuy_id, 'store_id' => $this->store_id])->find();
            if (empty($group_info)) {
                $this->error('该团购记录不翼而飞啦~');
            }
            $group_info['start_time'] = date('Y-m-d H:i', $group_info['start_time']);
            $group_info['end_time'] = date('Y-m-d H:i', $group_info['end_time']);
            $act = 'edit';
            $group_info['prom_id'] = $groupbuy_id;
            $isHaveOrder = Db::name('order_goods')->where(['prom_type'=>2,'prom_id'=>$groupbuy_id])->find();
        }
        $group_info['prom_type'] = 2;
        $this->assign('min_date', date('Y-m-d'));
        $this->assign('info', $group_info);
        $this->assign('act', $act);
        $this->assign('isHaveOrder', $isHaveOrder);
        return $this->fetch();
    }

    public function groupbuyHandle()
    {
        $data = I('post.');
        $data['groupbuy_intro'] = htmlspecialchars(stripslashes($_POST['groupbuy_intro']));

        $groupBuyValidate = Loader::validate('GroupBuy');
        if (!$groupBuyValidate->scene($data['act'])->batch()->check($data)) {
            $return = ['status' => 0, 'msg' => '操作失败', 'result' => $groupBuyValidate->getError()];

        if($data['item_id'] > 0){
            $spec_goods_price = Db::name("spec_goods_price")->where(['item_id'=>$data['item_id']])->find();
            $data['goods_price'] = $spec_goods_price['price'];
            $data['store_count'] = $spec_goods_price['store_count'];
        }else{
            $goods = Db::name("goods")->where(['goods_id'=>$data['goods_id']])->find();
            $data['goods_price'] = $goods['shop_price'];
            $data['store_count'] = $goods['store_count'];
        }

        if($data['item_id'] > 0){
            $spec_goods_price = Db::name("spec_goods_price")->where(['item_id'=>$data['item_id']])->find();
            if(!$spec_goods_price){
                $this->ajaxReturn(['status' => 0,'msg' =>'该规格不存在，请重新添加~','result' => '']);
            }
            if($spec_goods_price['goods_id'] != $data['goods_id']){
                $this->ajaxReturn(['status' => 0,'msg' =>'该规格不属于该商品，请重新添加商品','result' => '']);
            }
            $data['goods_price'] = $spec_goods_price['price'];
            $data['store_count'] = $spec_goods_price['store_count'];
        }else{
            $goods = Db::name("goods")->where(['goods_id'=>$data['goods_id']])->find();
            $data['goods_price'] = $goods['shop_price'];
            $data['store_count'] = $goods['store_count'];
        }

            $this->ajaxReturn($return);
        }

        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time'] = strtotime($data['end_time']);

        Db::startTrans();
        try {

            if ($data['act'] == 'del') {
                $group_buy = Db::name('group_buy')->where(['id' => $data['id'], 'store_id' => $this->store_id])->find();
                if (empty($group_buy)) {
                    exit(json_encode(1));
                }
                //查看是否已经有订单了，该活动有订单的不能删除了，只能关闭
                $group_order = db('order_goods')->where(['prom_type' => 2, 'prom_id' => $data['id']])->find();
                if ($group_order) {
                    $this->ajaxReturn(['status' => 2,'msg' =>'操作失败,已有用户下单,不能删除','result' => '']);
                }
                $spec_goods = Db::name('spec_goods_price')->where(['prom_type' => 2, 'prom_id' => $data['id']])->find();
                //有活动商品规格
                if ($spec_goods) {
                    Db::name('spec_goods_price')->where(['prom_type' => 2, 'prom_id' => $data['id']])->save(array('prom_id' => 0, 'prom_type' => 0));
                    //商品下的规格是否都没有活动
                    $goods_spec_num = Db::name('spec_goods_price')->where(['prom_type' => 2, 'goods_id' => $spec_goods['goods_id']])->find();
                    if (empty($goods_spec_num)) {
                        //商品下的规格都没有活动,把商品回复普通商品
                        Db::name('goods')->where(['goods_id' => $spec_goods['goods_id']])->save(array('prom_id' => 0, 'prom_type' => 0));
                    }
                } else {
                    //没有商品规格
                    Db::name('goods')->where(['prom_type' => 2, 'prom_id' => $data['id']])->save(array('prom_id' => 0, 'prom_type' => 0));
                }
                D('group_buy')->where(['id' => $data['id'], 'store_id' => $this->store_id])->delete();
                D('group_buy_goods_item')->where(['group_buy_id' => $data['id']])->delete();
            }

            if ($data['act'] == 'add') {

                //添加团购活动
                $r = db('group_buy')->insertGetId(
                    [
                        'title' => $data['title'],
                        'start_time' => $data['start_time'],
                        'end_time' => $data['end_time'],
                        'goods_id' => $data['goods_id'],
                        'goods_name' => $data['goods_name'],
                        'intro' => $data['intro'],
                        'store_id' => $this->store_id
                    ]
                );

                if ($r) {
                    foreach ($data['team_goods_item'] as $item) {
                        $item['rebate'] = number_format($item['price'] / $item['goods_price'] * 10, 1);
                        $item['group_buy_id'] = $r;
                        //添加团购活动商品
                        db('group_buy_goods_item')->insert($item);

                        if ($item['item_id'] > 0) {
                            //设置商品一种规格为活动
                            Db::name('spec_goods_price')->where('item_id', $item['item_id'])->update(['prom_id' => $r, 'prom_type' => 2]);
                            Db::name('goods')->where("goods_id", $item['goods_id'])->save(array('prom_id' => 0, 'prom_type' => 2));
                        } else {
                            Db::name('goods')->where("goods_id", $item['goods_id'])->save(array('prom_id' => $r, 'prom_type' => 2));
                        }
                    }

                }
            }
            if ($data['act'] == 'edit') {
                $group_buy = Db::name('group_buy')->where(['id' => $data['id'], 'store_id' => $this->store_id])->find();
                if (empty($group_buy)) {
                    $this->ajaxReturn(['status' => 0, 'msg' => '该团购记录不翼而飞啦~', 'result' => '']);
                }
                $r = Db::name('group_buy')->where(['id' => $data['id'], 'store_id' => $this->store_id])->update($data);
                if ($data['item_id'] > 0) {
                    //设置商品一种规格为活动
                    Db::name('spec_goods_price')->where(['prom_type' => 2, 'prom_id' => $data['id']])->update(['prom_id' => 0, 'prom_type' => 0]);
                    Db::name('spec_goods_price')->where('item_id', $data['item_id'])->update(['prom_id' => $data['id'], 'prom_type' => 2]);
                    M('goods')->where("goods_id", $data['goods_id'])->save(array('prom_id' => 0, 'prom_type' => 2));
                } else {
                    M('goods')->where("goods_id", $data['goods_id'])->save(array('prom_id' => $data['id'], 'prom_type' => 2));
                }
            }
            Db::commit(); //提交事务
            $this->ajaxReturn(['status' => 1, 'msg' => '操作成功', 'result' => '']);
        } catch (\PDOException $e) {
            Db::rollback(); //回滚事务
            $this->ajaxReturn(['status' => 0, 'msg' => '操作失败', 'result' => '']);
        }
    }
    
    public function search_goods()
    {
        $brand_id = I('brand_id/d');
        $keywords = I('keywords');
        $tpl = I('get.tpl', 'search_goods');
//        $goods_id = I('goods_id');
        $cat_id = I('cat_id/d');
        $intro = input('intro');
        $prom_id = input('prom_id');
        $prom_type = input('prom_type/d');
        $team_id = input('team_id/d',0);
        $bind_class_id = array();
        $store_bind_class = M('store_bind_class')->where(['store_id' => STORE_ID, 'state' => 1])->select();
        foreach ($store_bind_class as $key => $val) {
            $bind_class_id[] = $val['class_1'];
            $bind_class_id[] = $val['class_2'];
            $bind_class_id[] = $val['class_3'];
        }
        $where = ['is_on_sale' => 1,'store_id' => $this->store_id,'is_virtual'=>0,'store_count'=>['gt',0],'exchange_integral'=>0];//基础搜索条件
        //编辑拼团时会显示
        if (!empty($goods_id)) {  //商品id
            $where['goods_id'] = array('notin', $goods_id);
        }
        if ($cat_id) {   //分类
            $this->assign('cat_id', $cat_id);
            $goods_category = M('goods_category')->where("id", $cat_id)->find();
            $where['cat_id' . $goods_category['level']] = $cat_id;
        }
        if ($brand_id) {  //品牌ID
            $this->assign('brand_id', $brand_id);
            $where['brand_id'] = $brand_id;
        }
        if ($keywords) {  //商品模糊查询
            $keywords = urldecode(urldecode($keywords));
            $this->assign('keywords', $keywords);
            $where['goods_name|keywords'] = array('like', '%' . $keywords . '%');
        }
        if($intro){
            $where[I('intro')] = 1;
        }
        $Goods = new Goods();
        $count = $Goods->where($where)->where(function ($query) use ($prom_type, $prom_id) {
            if(in_array($prom_type,[3,6])){
                //优惠促销,拼团
                if ($prom_id) {
                    $query->where(['prom_id' => $prom_id, 'prom_type' => $prom_type])->whereor('prom_id', 0);
                } else {
                    $query->where('prom_type', 0);
                }
            }else if($prom_type == 7){
                //
                $query->where([ 'prom_type' => $prom_type])->whereor('prom_type', 0);
            }else if(in_array($prom_type,[1,2])){
                //抢购，团购
                $query->where('prom_type','in' ,[0,$prom_type])->where('prom_type',0);
            }else{
                $query->where('prom_type',0);
            }
        })->count();
        $Page = new Page($count, 10);
        $goodsList = $Goods->with('specGoodsPrice')->where($where)->where(function ($query) use ($prom_type, $prom_id) {
            if(in_array($prom_type,[3,6])){
                //优惠促销
                if ($prom_id) {
                    $query->where(['prom_id' => $prom_id, 'prom_type' => $prom_type])->whereor('prom_id', 0);
                } else {
                    $query->where('prom_type', 0);
                }
            }else if($prom_type == 7){
                //
                $query->where([ 'prom_type' => $prom_type])->whereor('prom_type', 0);
            }else if(in_array($prom_type,[1,2])){
                //抢购，团购
                $query->where('prom_type','in' ,[0,$prom_type])->where('prom_type',0);
            }else{
                $query->where('prom_type',0);
            }
        })->order('goods_id DESC')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $GoodsLogic = new GoodsLogic;
        $brandList = $GoodsLogic->getSortBrands();  //品牌
        $categoryList = $GoodsLogic->getSortCategory();  //商品规格
        $show = $Page->show();//分页显示输出
        $this->assign('bind_class_id', $bind_class_id);
        $this->assign('page', $show);//赋值分页输出
        $this->assign('goodsList', $goodsList);
        $this->assign('categoryList', $categoryList);
        $this->assign('brandList', $brandList);
        $this->assign('prom_type', $prom_type);
        if (strstr($tpl,'.')||strstr($tpl,'/') || strstr($tpl,'\\')) {
            $this->error('非法模板名称');
        }
        return $this->fetch($tpl);
    }
    //限时抢购
    public function flash_sale()
    {
        $key_word = input('key_word');
        $where = " 1 = 1 ";
        if ($key_word) {
            $where .= " and ( title like '%" .$key_word. "%' or goods_name like '%" .$key_word."%')";
        }
        $FlashSale = new FlashSale();
        $condition['store_id'] = $this->store_id;
        $count = $FlashSale->where($where)->where($condition)->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $prom_list = $FlashSale->append(['edit_status','status_desc'])->where($where)->where($condition)->order("id desc")->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('prom_list', $prom_list);
        $this->assign('key_word', $key_word);
        $this->assign('page', $show);// 赋值分页输出
        return $this->fetch();
    }

    public function flash_sale_info()
    {
        if (IS_POST) {
            $data = I('post.');
//            $data['start_time'] = strtotime($data['start_time']);
//            $data['end_time'] = strtotime($data['end_time']);
            $data['start_time'] = strtotime($data['start_time'].' '.$data['start_time_h'].':0:0');
            $data['end_time'] = $data['start_time']+7200;
            $flashSaleValidate = Loader::validate('FlashSale');
            if (!$flashSaleValidate->batch()->check($data)) {
                $return = ['status' => 0, 'msg' => '操作失败',
                    'result'    => $flashSaleValidate->getError(),
                    'token'       =>  \think\Request::instance()->token(),
                ];
                $this->ajaxReturn($return);
            }
            if (empty($data['id'])) {
                $data['store_id'] = $this->store_id;
                $flashSaleInsertId = Db::name('flash_sale')->insertGetId($data);
                if($data['item_id'] > 0){
                    //设置商品一种规格为活动
                    Db::name('spec_goods_price')->where('item_id',$data['item_id'])->update(['prom_id' => $flashSaleInsertId, 'prom_type' => 1]);
                    Db::name('goods')->where("goods_id", $data['goods_id'])->save(array('prom_id' => 0, 'prom_type' => 1));
                }else{
                    Db::name('goods')->where("goods_id", $data['goods_id'])->save(array('prom_id' => $flashSaleInsertId, 'prom_type' => 1));
                }
                sellerLog("管理员添加抢购活动 " . $data['name']);
                if ($flashSaleInsertId !== false) {
                    $flash_sale_queue =  Cache::get('flash_sale_queue');
                    $flash_sale_queue[$flashSaleInsertId] = $data['goods_num'];
                    Cache::set('flash_sale_queue',$flash_sale_queue);
                    $this->ajaxReturn(['status' => 1, 'msg' => '添加抢购活动成功', 'result' => '']);
                } else {
                    $this->ajaxReturn(['status' => 0, 'msg' => '添加抢购活动失败', 'result' => '']);
                }
            } else {
                $flash_sale = Db::name('flash_sale')->where(['id' => $data['id'], 'store_id' => $this->store_id])->find();
                if(empty($flash_sale)){
                    $this->ajaxReturn(['status' => 0, 'msg' => '该秒杀记录不翼而飞啦~', 'result' => '']);
                }
                if($flash_sale['is_end'] == 1){
                    $this->ajaxReturn(['status' => 0, 'msg' => '该活动已结束,不可编辑', 'result' => '']);
                }
                $r = M('flash_sale')->where(['id' => $data['id'], 'store_id' => $this->store_id])->save($data);
                M('goods')->where(['prom_type' => 1, 'prom_id' => $data['id']])->save(array('prom_id' => 0, 'prom_type' => 0));
                if($data['item_id'] > 0){
                    //设置商品一种规格为活动
                    Db::name('spec_goods_price')->where(['prom_type' => 1, 'prom_id' => $data['item_id']])->update(['prom_id' => 0, 'prom_type' => 0]);
                    Db::name('spec_goods_price')->where('item_id', $data['item_id'])->update(['prom_id' => $data['id'], 'prom_type' => 1]);
                    M('goods')->where("goods_id", $data['goods_id'])->save(array('prom_id' => 0, 'prom_type' => 1));
                }else{
                    M('goods')->where("goods_id", $data['goods_id'])->save(array('prom_id' => $data['id'], 'prom_type' => 1));
                }
                if ($r !== false) {
                    $flash_sale_queue =  Cache::get('flash_sale_queue');
                    $flash_sale_queue[$flash_sale['id']] = $flash_sale['goods_num'];
                    Cache::set('flash_sale_queue',$flash_sale_queue);
                    $this->ajaxReturn(['status' => 1, 'msg' => '编辑抢购活动成功', 'result' => '']);
                } else {
                    $this->ajaxReturn(['status' => 0, 'msg' => '编辑抢购活动失败', 'result' => '']);
                }
            }
        }
        $id = I('id/d');
        $info['start_time'] = date('Y-m-d H:i:s');
        $info['end_time'] = date('Y-m-d 23:59:59', time() + 3600 * 24 * 60);
        if ($id > 0) {
            $FlashSale = new FlashSale();
            $info = $FlashSale->with('specGoodsPrice,goods')->where('store_id', $this->store_id)->where('id', $id)->find();
            if(empty($info)){
                $this->error('该秒杀记录不翼而飞了');
            }
//            $info['start_time'] = date('Y-m-d H:i', $info['start_time']);
//            $info['end_time'] = date('Y-m-d H:i', $info['end_time']);
            $info['prom_id'] = $id;
            $info['start_time_h'] = date('H',$info['start_time']);
        }else{
            $info['start_time'] = time();
        }
        $info['prom_type'] = 1;
        $this->assign('info', $info);
        $this->assign('min_date', date('Y-m-d'));
        return $this->fetch();
    }

    public function flash_sale_del()
    {
        $id = I('del_id/d');
        if ($id) {
            $flash_sale = M('flash_sale')->where(['id' => $id, 'store_id' => $this->store_id])->find();
            if(empty($flash_sale)){
                exit(json_encode(0));
            }
            $spec_goods = Db::name('spec_goods_price')->where(['prom_type' => 1, 'prom_id' => $id])->find();
            //有活动商品规格
            if($spec_goods){
                Db::name('spec_goods_price')->where(['prom_type' => 1, 'prom_id' => $id])->save(array('prom_id' => 0, 'prom_type' => 0));
                //商品下的规格是否都没有活动
                $goods_spec_num = Db::name('spec_goods_price')->where(['prom_type' => 1, 'goods_id' => $spec_goods['goods_id']])->find();
                if(empty($goods_spec_num)){
                    //商品下的规格都没有活动,把商品回复普通商品
                    Db::name('goods')->where(['goods_id' => $spec_goods['goods_id']])->save(array('prom_id' => 0, 'prom_type' => 0));
                }
            }else{
                //没有商品规格
                Db::name('goods')->where(['prom_type' => 1, 'prom_id' => $id])->save(array('prom_id' => 0, 'prom_type' => 0));
            }
            M('flash_sale')->where(['id' => $id, 'store_id' => $this->store_id])->delete();
            $message_logic = new \app\common\logic\MessageActivityLogic([]);
            $message_logic->deletedMessage($id, 1);
            exit(json_encode(1));
        } else {
            exit(json_encode(0));
        }
    }

}