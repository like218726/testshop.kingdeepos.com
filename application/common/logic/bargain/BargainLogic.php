<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: IT宇宙人
 * Date: 2015-09-09
 */

namespace app\common\logic\bargain;

use app\admin\logic\RefundLogic;
use app\common\model\BargainFirst;
use app\common\model\BargainList;
use app\common\model\Order;
use app\common\model\PromotionBargain;
use app\common\model\PromotionBargainGoodsItem;
use app\common\model\Users;
use think\db;
use think\Page;

/**
 * 砍价逻辑定义
 * Class CatsLogic
 * @package admin\Logic
 */
class BargainLogic
{
    protected $bargain_id;//活动id
    protected $user_id;
    protected $bargain_first_id;//活动发起id
    protected $bargain;//活动模型
    protected $bargainFirst;//活动发起者模型
    protected $bargainList;//获取参与该发起者模型
    protected $goods;//商品模型
    protected $promotion_bargain_goods_item;//活动商品


    /**
     * @param $bargain_id
     * @throws \think\exception\DbException
     */
    public function setBargainId($bargain_id){
        $this->bargain_id = $bargain_id;
        $this->bargain = PromotionBargain::get(['id'=>$bargain_id,'is_end'=>0]);
    }
    public function setUserId($user_id){
        $this->user_id = $user_id;
    }

    public function setBargainFirstId($bargain_first_id){
        $this->bargain_first_id = $bargain_first_id;
    }

    /**
     * @param $item_id
     * @throws \think\exception\DbException
     */
    public function setItemId($item_id){
        $this->promotion_bargain_goods_item = PromotionBargainGoodsItem::get(['bargain_id'=>$this->bargain_id,'item_id'=>$item_id]);
    }

    /**
     * 活动列表
     * @param $data
     * @return array
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function bargainList($data)
    {
        $field = '';
        $sort = '';
        switch ($data['type'])
        {
            case 0://综合推荐
                $sort = 'recommend desc ,';
                $field = ',min(p.end_price) as end_price';
                break;
            case 1://
                $field = ',min(p.end_price) as end_price';
                break;
            case 2://价格
                $sort = 'end_price asc ,';
                $field = ',min(p.end_price) as end_price';
                break;
            case 3://热销榜
                $sort = 'buy_num desc ,';
                $field = ',max(p.buy_num) as buy_num ,min(p.end_price) as end_price';
                break;
        }
        $where['b.is_end'] = 0;
        $where['b.start_time'] = ['<',time()];
        $where['b.end_time'] = ['>',time()];
        $where['b.deleted'] = 0;
        $where['b.status'] = 1;
        $where['g.prom_type'] = 8;
        $PromotionBargain = new PromotionBargain();
        $count = $PromotionBargain->alias('b')->field('b.*, sum(p.goods_num) - sum(p.buy_num) as remain_num  '.$field)->where($where)->join('__PROMOTION_BARGAIN_GOODS_ITEM__ p','p.bargain_id=b.id','left')->join('__GOODS__ g','g.goods_id=b.goods_id','left')->order($sort.' b.id desc')->group('id')->count();
        $Page = new Page($count, 10);
        $PromotionBargain = $PromotionBargain->alias('b')->field('b.*,p.item_id,p.end_price, sum(p.goods_num)-sum(p.buy_num) as remain_num  '.$field)->where($where)->join('__PROMOTION_BARGAIN_GOODS_ITEM__ p','p.bargain_id=b.id','left')->join('__GOODS__ g','g.goods_id=b.goods_id','left')->order($sort.' b.id desc')->group('id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($PromotionBargain as $k=>$v)
        {
            $BargainFirst = BargainFirst::get(['bargain_id'=>$v['id'],'user_id'=>$this->user_id,'order_id'=>0]);
            $PromotionBargain[$k]['bargain_first'] = $BargainFirst?$BargainFirst:[];
            $PromotionBargain[$k]['original_img'] = goods_thum_images($v['goods_id'],100,100,$BargainFirst['item_id']);
            if($v['item_id'] > 0){
                $PromotionBargain[$k]['shop_price'] = db('spec_goods_price')->where(['item_id'=>$v['item_id']])->value('price');
            }else{
                $PromotionBargain[$k]['shop_price'] = db('goods')->where(['goods_id'=>$v['goods_id']])->value('shop_price');
            }
        }
        return ['status'=>1,'msg'=>'获取成功','result'=>$PromotionBargain];
    }

    /**
     * @param $data
     * @return array
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function orderList($data)
    {
        $BargainFirst = new BargainFirst();
        $where['f.user_id'] = $this->user_id;
        $whereOr = [];
        $whereOr1 = [];
        $where4 = '';
        $type = $data['type'];

        switch ((int)$type){
            case 0:
                break;
            case 1://砍价中
                $where['f.order_id'] = 0;
                $where['f.is_end'] = 0;
                $where['p.is_end'] = 0;
                $where['p.end_time'] = ['>',time()];
                break;
            case 2://待购买
                $where['f.order_id'] = 0;
                $where['f.is_end'] = 1;
                $where['p.is_end'] = 0;
                $where['p.end_time'] = ['>',time()];
                break;
            case 3://已支付
                $where['f.order_id'] = ['>',0];
                $where['o.pay_status'] = 1;
                $where['o.order_status'] = ['in',[0,1,2,4]];
                break;
            case 4://未支付
                $where['f.order_id'] = ['>',0];
                $where['o.pay_status'] = 0;
                $where['o.add_time'] = ['<',time()];
                $where['p.end_time'] = ['>',time()];
                $where4 = 'o.add_time + p.order_overtime * 60 > '.time();
                break;
            case 5://失败
//                $where['o.pay_status'] = 0;
                $where['o.order_status'] = ['in',[3,5]];
                $whereOr['o.pay_status'] = ['exp',' is null'];
                $whereOr['f.user_id'] = $this->user_id;
                $whereOr['p.end_time'] = ['<',time()];
                $whereOr1['p.is_end'] = 1;
                $whereOr1['f.user_id'] = $this->user_id;
                $whereOr1['o.pay_status'] = ['exp',' is null'];

                break;
        }
        $count = $BargainFirst
            ->alias('f')
            ->join('__ORDER__ o','o.order_id=f.order_id','left')
            ->join('__PROMOTION_BARGAIN__ p','p.id=f.bargain_id','left')
            ->where($where)
            ->where($where4)
            ->whereOr(function ($query) use ($whereOr)  {
            $query->where($whereOr);
        })
            ->whereOr(function ($query) use ($whereOr1)  {
                $query->where($whereOr1);
            })
            ->count();
        $Page = new Page($count, 10);
        $BargainFirst = $BargainFirst
            ->alias('f')
            ->join('__ORDER__ o','o.order_id=f.order_id','left')
            ->join('__PROMOTION_BARGAIN__ p','p.id=f.bargain_id','left')
            ->field('f.*,o.order_status,o.pay_status,o.order_amount,o.user_money,o.add_time as order_addtime')
            ->where($where)
            ->where($where4)
            ->whereOr(function ($query)  use ($whereOr) {
                $query->where($whereOr);
            })
            ->whereOr(function ($query) use ($whereOr1)  {
                $query->where($whereOr1);
            })
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->order('id desc')
//            ->fetchSql(true)
            ->select();
//        halt($BargainFirst);
//fetchSql(true)->
       foreach ($BargainFirst as $k=>$v)
       {
           $BargainFirst[$k]['item_key'] = $v['specGoodsPrice']['key_name'];
           $BargainFirst[$k]['goods_id'] = $v['promotionBargain']['goods_id'];
           $BargainFirst[$k]['original_img'] = goods_thum_images($v['promotionBargain']['goods_id'],100,100,$v['item_id']);

           //砍价进行中才返回参数 start
           if($v['order_id'] == 0 && $v['is_end'] == 0){
               if($v['item_id'] > 0){
                   $BargainFirst[$k]['goods_price'] = $v['specGoodsPrice']['price'];
               }else{
                   $BargainFirst[$k]['goods_price'] = $v['promotionBargain']['promotionBargainGoodsItem'][0]['start_price'];
               }
               $BargainFirst[$k]['remain_num'] = array_sum(array_column(json_decode(json_encode($v['promotionBargain']['promotionBargainGoodsItem']),true),'goods_num')) - array_sum(array_column(json_decode(json_encode($v['promotionBargain']['promotionBargainGoodsItem']),true),'buy_num'));

           }
           //砍价进行中才返回参数 end
           if(($v['order_addtime'] + $v['promotionBargain']['order_overtime']*60 < time() || $v['promotionBargain']['is_end'] == 1) && $v['pay_status'] === 0 && $v['order_status'] === 0 ){

               //过滤掉未支付的订单,订单超时,更改订单状态取消
               Order::update(['order_status'=>3],['order_id'=>$v['order_id']]);
               if($v['user_money'] > 0){
                   //自动退款
                   $refundLogic = new RefundLogic();
                   $refundLogic->updateRefundOrder(Order::get(['order_id'=>$v['order_id']]),0);
                   $messageFactory = new \app\common\logic\MessageFactory();
                   $messageLogic = $messageFactory->makeModule(['category' => 2]);
                   $messageLogic->sendRefundNotice($v['order_id'],$v['order_amount']);
               }

              $v['order_status'] =  3;
           }
           $BargainFirst[$k]['status'] = $this->BargainFirstStatus($v,$type);//订单状态描述
       }
        return ['status'=>1,'msg'=>'获取成功','result'=>$BargainFirst];
    }

    //订单状态描述
    public function BargainFirstStatus($data,$type)
    {
        if($data['order_id'] == 0 && $data['is_end'] == 0  && ($data['promotionBargain']['end_time'] > time() && $data['promotionBargain']['is_end'] == 0 )){
            return 0;//砍价中
        }elseif ($data['order_id'] == 0 && $data['is_end'] == 1  && ($data['promotionBargain']['end_time'] > time() && $data['promotionBargain']['is_end'] == 0 )){
            return 1;//待购买
        }elseif ($data['order_id'] > 0 && $data['pay_status'] == 1 && in_array($data['order_status'],[0,1,2,4])){
            return 2;//已支付
        }elseif ($data['order_id'] > 0 && $data['pay_status'] == 0 && $data['order_status'] == 0  && $data['promotionBargain']['end_time'] > time()){
            return 3;//未支付
        }elseif (($data['order_id'] > 0 && $data['pay_status'] == 0 && $data['order_status'] == 3)|| ($data['pay_status'] == 3 && $data['order_status'] == 3)  ||  ($data['promotionBargain']['end_time'] < time() || $data['promotionBargain']['is_end'] == 1 ) || $data['order_status'] == 5 || $data['order_status'] == 3){
            return 4;//失败
        }
    }


    /**
     * 发起砍价
     * @return array
     * @throws \think\exception\DbException
     */
    public function startBargain($goods_num)
    {
        $this->bargainFirst = BargainFirst::get(['bargain_id'=>$this->bargain_id,'user_id'=>$this->user_id,'order_id'=>0]);
        if(!$this->bargainFirst){
            if($this->checkActivityIsEnd()){
                return ['status'=>0,'msg'=>'活动不存在或者已结束','result'=>[]];
            }
            //判断是否发起数量cut_limit表示不限制
            if($this->getUserCutLimit() >= $this->bargain['cut_limit'] && $this->bargain['cut_limit'] !=0){
                return ['status'=>0,'msg'=>'每人限制发起砍价'.$this->bargain['cut_limit'].'次','result'=>[]];
            }
            //查询已下单数量多少
            $order_goods_num = $this->getUserBargainGoodsNum($this->user_id);
            if($this->promotion_bargain_goods_item['goods_num'] - $this->promotion_bargain_goods_item['buy_num'] < $goods_num){
                return ['status'=>0,'msg'=>'商品数量不足','result'=>[]];
            }
            if($this->bargain['buy_limit'] < $goods_num+$order_goods_num){
                return ['status'=>0,'msg'=>'您已超出该商品可限购数'.$this->bargain['buy_limit'].'件','result'=>[]];
            }

            $BargainFirst = new BargainFirst();
            $BargainFirst->user_id = $this->user_id;
            $BargainFirst->bargain_id = $this->bargain_id ;
            $BargainFirst->goods_num = $goods_num ;
            $BargainFirst->item_id = $this->promotion_bargain_goods_item['item_id'] ;
            $BargainFirst->add_time = time();
            $BargainFirst->end_price = $this->promotion_bargain_goods_item['start_price'];
            if($BargainFirst->save()){
                $this->bargainFirst = $BargainFirst;
                $this->bargain_first_id = $BargainFirst->id;
                $this->cut(Users::get($this->user_id));
                return ['status'=>1,'msg'=>'发起砍价成功','result'=>$this->bargainFirst];
            }
            return ['status'=>0,'msg'=>'发起砍价失败','result'=>[]];
        }
        return ['status'=>1,'msg'=>'已参与','result'=>$this->bargainFirst];
    }

    /**
     * 获取帮助砍价页面信息
     * @return array
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function showBargain()
    {
        $info['bargain_first_info'] = $this->getBargainFirst();
        $info['cut_actor_info'] = $this->getCutActor();
        $info['bargain_info'] = $this->getBargainInfo();
        $info['count_cut_price'] = db('BargainList')->where(['bargain_first_id'=>$this->bargain_first_id])->sum('cut_price');
        $info['user_cut_price'] = db('BargainList')->where(['bargain_first_id'=>$this->bargain_first_id,'user_id'=>$this->user_id])->find();
        if($this->checkActivityIsEnd()){
            return ['status'=>0,'msg'=>'活动已结束','result'=>[]];
        }
        return ['status'=>1,'msg'=>'获取成功','result'=>$info];
    }

    /**
     * 获取参与者
     * @return array|null|static
     * @throws \think\exception\DbException
     */
    public function getCutActor()
    {
        $BargainList = new BargainList();
        $data =  $BargainList->where(['bargain_first_id'=>$this->bargain_first_id])->order('cut_price desc')->select();
        $this->bargainList = $data;
       return $data?$data:[];
    }

    /**
     * 获取发起者
     * @return array|null|static
     * @throws \think\exception\DbException
     */
    public function getBargainFirst($user_id = 0)
    {
        if($user_id){
            $where['user_id'] = $user_id;
        }
        $where['id'] = $this->bargain_first_id;
        $data =  BargainFirst::get($where);
        if($data){
            $data['users'] = $data['users'];
            $this->bargain_id = $data['bargain_id'];
            $this->bargainFirst = $data;
        }
        return $data?$data:[];
    }

    /**
     * 获取活动信息
     * @return array|null|static
     * @throws \think\exception\DbException
     */
    public function getBargainInfo()
    {
        $data =  PromotionBargain::get(['id'=>$this->bargain_id]);
        if($data){
            $PromotionBargainGoodsItem =  PromotionBargainGoodsItem::get(['bargain_id'=>$this->bargain_id,'item_id'=>$this->bargainFirst['item_id']]);
            $data['end_price'] = $PromotionBargainGoodsItem['end_price'];
            $data['start_price'] = $PromotionBargainGoodsItem['start_price'];
            $data['item_id'] = $PromotionBargainGoodsItem['item_id'];
            $data['goods_num'] = $PromotionBargainGoodsItem['goods_num'];
            $url = goods_thum_images($PromotionBargainGoodsItem['goods_id'],300,300);
            if (strpos($url, 'http') !== 0) {
                $url = SITE_URL . $url;
            }
            $data['original_img'] = $url;
        }
        $this->bargain = $data;
        return $data?$data:[];
    }


    /**
     * 砍价价格接口
     * @param $user
     * @return array
     * @throws \think\exception\DbException
     */
    public function cut($user){
        $where['id'] = $this->bargain_first_id;
        $where['is_end'] = 0;
        $data =  BargainFirst::get($where);
        if($data){
            $this->bargain_id = $data['bargain_id'];
            $this->bargain = $data['PromotionBargain'];
            if($this->checkActivityIsEnd()){
                return ['status'=>0,'msg'=>'活动不存在或已结束','result'=>[]];
            }
            $PromotionBargainGoodsItem =  PromotionBargainGoodsItem::get(['bargain_id'=>$this->bargain['id'],'item_id'=>$data['item_id']]);
            $BargainList = new BargainList();
            $BargainList = $BargainList->where(['user_id'=>$user['user_id'],'bargain_first_id'=>$this->bargain_first_id])->count();
            if($BargainList){return ['status'=>0,'msg'=>'已帮助砍过','result'=>$BargainList]; }
            //好友砍价入库
            $BargainList = new BargainList();
            $BargainList->user_id = $user['user_id'];
            $BargainList->nickname = $user['nickname']?$user['nickname']:'';
            $BargainList->head_pic = $user['head_pic']?$user['head_pic']:'';
            $BargainList->bargain_first_id = $this->bargain_first_id;
            $this->bargain['end_price'] = $PromotionBargainGoodsItem['end_price'];
            $BargainList->cut_price = $this->cut_price($this->bargain,$data['end_price']);
            $BargainList->add_time = time();
            if($BargainList->save()){
                $data->cut_count = $data->cut_count + 1;
                $data->end_price = $data->end_price - $BargainList->cut_price;
                if($PromotionBargainGoodsItem['end_price'] ==  $data->end_price){
                    //看到最低价了，改变状态
                    $data->is_end = 1;
                }
                $data->save();
            }
            return ['status'=>1,'msg'=>'砍价成功','result'=>$BargainList];

        }
        return ['status'=>0,'msg'=>'活动不存在或已结束','result'=>[]];
    }

    /**
     * 获取用户已购商品数量
     * @param $user_id
     * @return float|int
     */
    public function getUserBargainGoodsNum($user_id){
        $orderWhere = [
            'user_id'=>$user_id,
            'order_status' => ['<>', 3],
            'add_time' => ['between', [$this->bargain['start_time'], $this->bargain['end_time']]]
        ];
        $order_id_arr = Db::name('order')->where($orderWhere)->getField('order_id', true);
        if ($order_id_arr) {
            $orderGoodsWhere = ['prom_id' => $this->bargain['id'], 'prom_type' => 8, 'order_id' => ['in', implode(',', $order_id_arr)]];
            $goods_num = DB::name('order_goods')->where($orderGoodsWhere)->sum('goods_num');
            if($goods_num){
                return $goods_num;
            }else{
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * 判断用户购买了次数
     * @return BargainFirst|int|string
     */
    public function getUserCutLimit()
    {
        $BargainFirst = new BargainFirst();
        //排除取消订单
        $orderWhere = [
            'o.user_id'=>$this->user_id,
            'o.order_status' => ['<>', 3],
            'b.bargain_id' =>$this->bargain_id,
        ];
        $BargainFirst = $BargainFirst->alias('b')->join('__ORDER__ o','o.order_id = b.order_id','left')->where($orderWhere)->count();
        return $BargainFirst;
    }



    /**
     * 活动是否结束
     * @return bool
     */
    public function checkActivityIsEnd(){
        if(empty($this->bargain)){
            return true;
        }
        $goods_num = array_sum(array_column(json_decode(json_encode($this->bargain['promotionBargainGoodsItem']),true),'goods_num'));
        $buy_num = array_sum(array_column(json_decode(json_encode($this->bargain['promotionBargainGoodsItem']),true),'buy_num'));
        if($goods_num == $buy_num){
            return true;
        }
//        foreach ($this->bargain['promotionBargainGoodsItem'] as $v){
//            if($v['goods_num'] == $v['buy_num']){
//                return true;
//            }
//        }
        if(time() > $this->bargain['end_time']){
            return true;
        }
        if(1 ==  $this->bargain['is_end']){
            return true;
        }
        return false;
    }


    /**
     * 通用普通随机砍价金额
     * @param $data
     * @param $end_price
     * @return float|int
     */
    public function cut_price($data, $end_price){
        $cut_s = $data['cut_statr_range'];
        $cut_e = $data['cut_end_range'];
        //如果剩下的金额比范围的还要小，取剩下的金额做范围
        if($cut_e > $end_price){
            $rand_price = rand($cut_s*100,$end_price*100)/100;
        }else{
            $rand_price = rand($cut_s*100,$cut_e*100)/100;
        }
        if($end_price - $rand_price < $data['end_price'] ){
            $rand_price = $end_price - $data['end_price'];
        }
        return $rand_price;
    }

}