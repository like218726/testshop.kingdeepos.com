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
 * Author: lhb
 * Date: 2017-05-15
 */

namespace app\seller\logic;
use app\common\model\Goods;
use app\common\model\SpecGoodsPrice;
use app\common\model\team\TeamFollow;
use think\Db;
use think\Model;

/**
 * 拼团活动逻辑类
 */
class TeamActivityLogic extends Model
{
    protected $team;//拼团模型
    protected $teamFound;//团长模型
    public function setTeam($team){
        $this->team = $team;
    }
    public function setTeamFound($teamFound){
        $this->teamFound = $teamFound;
    }
    /**
     * 抽奖
     * @return array
     * @throws \think\Exception
     */
    public function lottery($seller_id){
        if(empty($this->team)){
            return ['status'=>0,'msg'=>'拼团活动记录不翼而飞啦~','result'=>''];
        }
        if($this->team['status'] != 1){
            return ['status'=>0,'msg'=>'活动状态不容许~','result'=>''];
        }
        if($this->team['team_type'] != 2){
            return ['status'=>0,'msg'=>'不属于抽奖团类型','result'=>''];
        }
        if($this->team['is_lottery'] == 1){
            return ['status'=>0,'msg'=>'已经抽过奖了','result'=>''];
        }
        //开始抽奖
        //先找到成团的团长们
        $founds = $this->team->teamFound()->where(['status' => 2])->select();
        if(empty($founds)){
            return['status'=>0,'msg'=>'没有成团记录','result'=>''];
        }
        //然后找到团长们的团员们
        $founds_ids = get_arr_column($founds,'found_id');
        $TeamFollow = new TeamFollow();
        $follows = $TeamFollow->where(['found_id' => ['IN', $founds_ids], 'status' => 2])->select();
        $follows_count = count($follows);
        $founds_count = count($founds);
        $lotteryFoundAllData = [];//中奖团长
        $lotteryFollowAllData = [];//中奖会员
        $lotteryFoundOrderIds = [];//中奖团长订单
        $lotteryFollowOrderIds = [];//中奖会员订单
        if(($follows_count + $founds_count) < $this->team['stock_limit']){
            //如果购买人数小于中奖限量,那么就直接将购买人中奖。
            foreach($founds as $foundKey=>$foundVal){
                $lotteryFoundAllData[] = [
                    'user_id'=>$foundVal['user_id'],
                    'order_id'=>$foundVal['order_id'],
                    'order_sn'=>$foundVal->order['order_sn'],
                    'mobile'=>$foundVal->order['mobile'],
                    'team_id'=>$this->team['team_id'],
                    'nickname'=>$foundVal['nickname'],
                    'head_pic'=>$foundVal['head_pic'],
                ];
                array_push($lotteryFoundOrderIds,$foundVal['order_id']);
            }
            foreach($follows as $followKey=>$followVal){
                $lotteryFollowAllData[] = [
                    'user_id'=>$followVal['follow_user_id'],
                    'order_id'=>$followVal['order_id'],
                    'order_sn'=>$followVal->order['order_sn'],
                    'mobile'=>$followVal->order['mobile'],
                    'team_id'=>$this->team['team_id'],
                    'nickname'=>$followVal['follow_user_nickname'],
                    'head_pic'=>$followVal['follow_user_head_pic'],
                ];
                array_push($lotteryFollowOrderIds,$followVal['order_id']);
            }
        }else{
            //如果购买人数大于中奖限量
            $lotteryFoundData = [];//定义抽奖数组
            $lotteryFollowData = [];//定义抽奖数组
            foreach($founds as $foundKey=>$foundVal){
                $lotteryFoundData[] = [
                    'found_id'=>$foundVal['found_id'],
                ];
            }
            foreach($follows as $followKey=>$followVal){
                $lotteryFollowData[] = [
                    'follow_id'=>$followVal['follow_id'],
                ];
            }
            $lotteryData = array_merge($lotteryFoundData,$lotteryFollowData);
            shuffle($lotteryData);//打乱抽奖数组
            $lotteryFoundAllData = [];
            $lotteryFollowAllData = [];
            $lotteryFoundOrderIds = [];
            $lotteryFollowOrderIds = [];
            //抽多少个中奖人就循环多少个
            for ($i = 0; $i < $this->team['stock_limit']; $i++) {
                $lotteryTmp = $lotteryData[$i];
                if(array_key_exists('found_id',$lotteryTmp)){
                    //团长中奖了
                    foreach($founds as $foundKey=>$foundVal){
                        if($lotteryTmp['found_id'] == $foundVal['found_id']){
                            $lotteryFoundAllData[] = [
                                'user_id'=>$foundVal['user_id'],
                                'order_id'=>$foundVal['order_id'],
                                'order_sn'=>$foundVal->order['order_sn'],
                                'mobile'=>$foundVal->order['mobile'],
                                'team_id'=>$this->team['team_id'],
                                'nickname'=>$foundVal['nickname'],
                                'head_pic'=>$foundVal['head_pic'],
                            ];
                            array_push($lotteryFoundOrderIds,$foundVal['order_id']);
                        }
                    }
                }else{
                    //团员中奖
                    foreach($follows as $followKey=>$followVal){
                        if($lotteryTmp['follow_id'] == $followVal['follow_id']){
                            $lotteryFollowAllData[] = [
                                'user_id'=>$followVal['follow_user_id'],
                                'order_id'=>$followVal['order_id'],
                                'order_sn'=>$followVal->order['order_sn'],
                                'mobile'=>$followVal->order['mobile'],
                                'team_id'=>$this->team['team_id'],
                                'nickname'=>$followVal['follow_user_nickname'],
                                'head_pic'=>$followVal['follow_user_head_pic'],
                            ];
                            array_push($lotteryFollowOrderIds,$followVal['order_id']);
                        }
                    }
                }
            }

        }
        $lotteryAllData = array_merge($lotteryFoundAllData,$lotteryFollowAllData);
        $lotteryInsert = Db::name('team_lottery')->insertAll($lotteryAllData);
        if($lotteryInsert !== false){
            $lotteryOrderIds = array_merge($lotteryFoundOrderIds,$lotteryFollowOrderIds);
            $goods_num_group = Db::name('order_goods')->field('goods_id, goods_name, spec_key_name, spec_key,SUM(goods_num) as goods_num_sum')->where('order_id', 'IN', $lotteryOrderIds)->group('spec_key')->select();



            $lotteryGoodsNum = count($lotteryOrderIds);//中奖商品购买库存
            $lotteryOrderConfirm = Db::name('order')->where(['order_id'=>['IN',$lotteryOrderIds]])->update(['order_status' => 1]);//中奖订订单确认
            if($lotteryOrderConfirm !== false){
                //未中奖订单执行退款操作
                $NoLotteryFollowOrderWhere = ['prom_id' => $this->team['team_id'], 'prom_type' => 6, 'order_status' => 0, 'pay_status' => 1];
                $NoLotteryFollowOrderList = Db::name('order')->where($NoLotteryFollowOrderWhere)->select();
                $orderLogic = new OrderLogic();
                $seller_id = session('seller_id');
                if($NoLotteryFollowOrderList){
                    $NoLotteryFollowOrderIds = [];//未中奖订单ID
                    foreach($NoLotteryFollowOrderList as $orderKey => $orderVal){
                        $orderLogic->orderActionLog($orderVal, '取消订单', '拼团退款', $seller_id, 1);
                        array_push($NoLotteryFollowOrderIds,$orderVal['order_id']);
                    }
                    Db::name('order')->where('order_id','IN',$NoLotteryFollowOrderIds)->update(['order_status' => 3]);
                }
            }
            //减库存操作
            $goods = Goods::get($this->team['goods_id']);
            if($goods_num_group){
                $goods_num_sum_total = 0;
                $goods = Goods::get($this->team['goods_id']);
                foreach($goods_num_group as $goods_num_group_key=>$goods_num_group_val){
                    $goods_num_sum_total += $goods_num_group_val['goods_num_sum'];//商品总数量
                    if(!empty($goods_num_group_val['spec_key'])){
                        $specGoodsPrice = SpecGoodsPrice::get(['key'=>$goods_num_group_val['spec_key'],'goods_id'=>$this->team['goods_id']]);
                        $specGoodsPrice->store_count = $specGoodsPrice->store_count - $goods_num_group_val['goods_num_sum'];// 减去商品规格数量
                        $specGoodsPrice->save();
                    }else{
                        $goods->prom_id = 0;
                        $goods->prom_type = 0;//恢复成普通商品
                    }
                }
                //更新库存日志操作
                foreach ($lotteryAllData as $val)  //获取抽奖记录信息
                {
                    $goods_info= [
                        'goods_id'=>$goods_num_group[0]['goods_id'], //抽奖商品取下标0的即可
                        'goods_name'=>$goods_num_group[0]['goods_name'],
                        'goods_spec'=>$goods_num_group[0]['spec_key_name'],
                    ];
                    update_stock_log($seller_id, -$goods_num_group[0]['goods_num_sum'], $goods_info, $val['order_sn'] );   //更新库存日志统一函数
                }


                //恢复商品
                db('spec_goods_price')->where(['prom_type' => 6, 'prom_id' => $this->team['team_id']])->update(['prom_type' => 0, 'prom_id' => 0]);
                $goodsPromCount = Db::name('spec_goods_price')->where('goods_id', $this->team['goods_id'])->where('prom_type','>',0)->count('item_id');
                if($goodsPromCount == 0){
                    $goods->prom_id = 0;
                    $goods->prom_type = 0;//恢复成普通商品
                }
                $goods->store_count = $goods->store_count - $goods_num_sum_total;// 减去商品总数量
                $goods->sales_sum = $goods->sales_sum + $goods_num_sum_total;// 增加商品销售量
                $goods->save();
                refresh_stock($this->team['goods_id']);
            }
            $goods->sales_sum = $goods->sales_sum + $lotteryGoodsNum;// 增加商品销售量
            $goods->save();

            db('team_goods_item')->where('team_id', $this->team['team_id'])->update(['deleted' => 1]);
            $this->team->is_lottery = 1;//抽奖结束
            $this->team->save();
            return ['status'=>1,'msg'=>'抽奖成功','result'=>''];
        }else{
            return ['status'=>0,'msg'=>'抽奖失败','result'=>''];
        }
    }

    /**
     * 拼团退款
     * @return array
     * @throws \think\Exception
     */
    public function refundFound(){
        if(empty($this->teamFound)){
            return ['status'=>0,'msg'=>'找不到拼单','result'=>''];
        }
        if(empty($this->teamFound->order)){
            return ['status'=>0,'msg'=>'找不到拼单的订单','result'=>''];
        }
        if($this->teamFound->status != 3){
            return ['status'=>0,'msg'=>'拼单状态不符合退款需求','result'=>''];
        }
        if($this->teamFound->order->pay_status == 0){
            return ['status'=>0,'msg'=>'拼单订单状态不符合退款需求','result'=>''];
        }
        $teamOrderId = [];//拼团Order_id集合
        array_push($teamOrderId,$this->teamFound->order_id);
        $followOrderId = $this->teamFound->teamFollow()->where('status','gt',0)->column('order_id');;//拼单成功的会员
        if($followOrderId){
            $teamOrderId = array_merge($teamOrderId,$followOrderId);
        }
        $orderRefund = Db::name('order')->where('order_id', 'IN', $teamOrderId)->where('pay_status',1)->update(['order_status' => 3]);//订单取消,平台后台处理退款
        $orderLogic = new OrderLogic();
        $seller_id = session('seller_id');
        $TeamOrderList = Db::name('order')->where('order_id', 'IN', $teamOrderId)->where('pay_status',1)->select();
        if($TeamOrderList){
            foreach($TeamOrderList as $orderKey => $orderVal){
                $orderLogic->orderActionLog($orderVal, '取消订单', '拼团退款', $seller_id, 1);
            }
        }
        if($orderRefund !== false){
            return ['status'=>1,'msg'=>'拼团退款已提交至平台，坐等审核','result'=>''];
        }else{
            return ['status'=>0,'msg'=>'拼团退款失败','result'=>''];
        }
    }

    public function refundFoundOrderId(){
        if(empty($this->teamFound)){
            return ['status'=>0,'msg'=>'找不到拼单','result'=>''];
        }
        if(empty($this->teamFound->order)){
            return ['status'=>0,'msg'=>'找不到拼单的订单','result'=>''];
        }
        if($this->teamFound->status != 3){
            return ['status'=>0,'msg'=>'拼单状态不符合退款需求','result'=>''];
        }
        if($this->teamFound->order->pay_status == 0){
            return ['status'=>0,'msg'=>'拼单订单状态不符合退款需求','result'=>''];
        }
        $teamOrderId = [];//拼团Order_id集合
        array_push($teamOrderId,$this->teamFound->order_id);
        $followOrderId = $this->teamFound->teamFollow()->where('status','gt',0)->column('order_id');;//拼单成功的会员
        if($followOrderId){
            $teamOrderId = array_merge($teamOrderId,$followOrderId);
        }
        Db::name('order')->where('order_id', 'IN', $teamOrderId)->where('pay_status',1)->update(['order_status' => 3]);//订单取消,平台后台处理退款
        $orderLogic = new OrderLogic();
        $seller_id = session('seller_id');
        $TeamOrderList = Db::name('order')->where('order_id', 'IN', $teamOrderId)->where('pay_status',1)->select();
        if($TeamOrderList){
            foreach($TeamOrderList as $orderKey => $orderVal){
                $orderLogic->orderActionLog($orderVal, '取消订单', '拼团退款', $seller_id, 1);
            }
        }
       return $teamOrderId;
    }
}