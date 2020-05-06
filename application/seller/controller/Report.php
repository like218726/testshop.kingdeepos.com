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
 * Date: 2016-06-21
 */

namespace app\seller\controller;

use app\seller\logic\OrderLogic;
use think\Db;
use think\Page;

class Report extends Base{
	public $store_id;
    public function _initialize(){
        parent::_initialize();
		$this->store_id = STORE_ID;
	}
	
	public function index(){
        $OrderMobile = new OrderLogic();
        $today=$OrderMobile->getTodayAmount($this->store_id);
		if($today['today_order'] == 0){
			$today['sign'] = round(0, 2);
		}else{
			$today['sign'] = round($today['today_amount']/$today['today_order'],2);
		}
		$this->assign('today',$today);
		$sql = "SELECT COUNT(*) as tnum,sum(goods_price-order_prom_amount) as amount, FROM_UNIXTIME(add_time,'%Y-%m-%d') as gap from  __PREFIX__order{$this->select_year} ";
		$sql .= " where add_time>$this->begin and add_time<$this->end and store_id=$this->store_id AND (pay_status=1 or pay_code='cod') and order_status in(1,2,4) group by gap order by gap desc";
		$res = Db::query($sql);//订单数,交易额
        $list=[];$order_arr=[];$amount_arr=[];$sign_arr=[];$day=[];
        $tnum=0;$tamount=0;
        if($res){
            foreach ($res as $val){
                $arr[$val['gap']] = $val['tnum'];
                $brr[$val['gap']] = $val['amount'];
                $tnum += $val['tnum'];
                $tamount += $val['amount'];
            }

            for($i=$this->end;$i>$this->begin;$i=$i-24*3600){
                $tmp_num = empty($arr[date('Y-m-d',$i)]) ? 0 : $arr[date('Y-m-d',$i)];
                $tmp_amount = empty($brr[date('Y-m-d',$i)]) ? 0 : $brr[date('Y-m-d',$i)];
                $tmp_sign = empty($tmp_num) ? 0 : round($tmp_amount/$tmp_num,2);
                $order_arr[] = $tmp_num;
                $amount_arr[] = $tmp_amount;
                $sign_arr[] = $tmp_sign;
                $date = date('Y-m-d',$i);
                $list[] = array('day'=>$date,'order_num'=>$tmp_num,'amount'=>$tmp_amount,'sign'=>$tmp_sign,'end'=>date('Y-m-d',$i+24*60*60));
                $day[] = $date;
            }
        }
		
		$this->assign('list',$list);
		$result = array('order'=>$order_arr,'amount'=>$amount_arr,'sign'=>$sign_arr,'time'=>$day,'tnum'=>$tnum,'tamount'=>$tamount);
		$this->assign('result',json_encode($result));
		return $this->fetch();
	}

    /**
     * 销售排行
     * @return mixed
     */
	public function saleTop(){
        $res = Db::name('order_goods'.$this->select_year)
            ->field('goods_name,goods_sn,sum(goods_num) as sale_num,sum(goods_num*goods_price) as sale_amount')
            ->where("is_send = 1 and store_id=$this->store_id ")->group(' goods_id')->order('sale_amount DESC')->limit(100)->cache(true,3600)->select();
		$this->assign('list',$res);
		return $this->fetch();
	}


	public function saleList(){
        $begin = $this->begin;
        $end_time = $this->end;
        $order_where = [
            'o.pay_status'=>1,
            'o.shipping_status'=>1,
            'og.is_send'=>['in','1,2'],
            'o.store_id'=>$this->store_id,
            'o.add_time'=>['between', [$begin,$end_time]],
        ];  //交易成功的有效订单
        $order_count = Db::name('order')->alias('o')
            ->join('order_goods og','o.order_id = og.order_id','left')->where($order_where)
            ->group('o.order_id')->count();
        $Page = new Page($order_count,50);
        $order_list = Db::name('order')->alias('o')
            ->field('o.*,SUM(og.cost_price) as coupon_amount')
            ->join('order_goods og','o.order_id = og.order_id','left')
            ->where($order_where)->group('o.order_id')->limit($Page->firstRow,$Page->listRows)->select();
        $this->assign('order_list',$order_list);
        $this->assign('page',$Page->show());
        return $this->fetch();
	}

    //财务统计
    public function finance(){
        $begin = $this->begin;
        $end_time = $this->end;
        $order = Db::name('order')->alias('o')
            ->where(['o.pay_status'=>1,'o.shipping_status'=>1,'o.order_status'=>['in','1,2,4'],'o.store_id'=>$this->store_id])->whereTime('o.add_time', 'between', [$begin, $end_time])
            ->order('o.add_time asc')->getField('order_id,o.*');  //以时间升序
        $list = [];
        $goods_arr = [];
        $shipping_arr = [];
        $coupon_arr = [];
        $day = [];
        if($order){
            $order_id_arr = get_arr_column($order,'order_id');
            $order_ids = implode(',',$order_id_arr);            //订单ID组
            $order_goods = Db::name('order_goods')->where(['is_send'=>['in','1,2'],'order_id'=>['in',$order_ids]])->group('order_id')
                ->order('order_id asc')->getField('order_id,sum(goods_num*cost_price) as cost_price,sum(goods_num*member_goods_price) as goods_amount');  //订单商品退货的不算
            $frist_key = key($order);  //第一个key
            $sratus_date = strtotime(date('Y-m-d',$order["$frist_key"]['add_time']));  //有数据那天为循环初始时间，大范围查询可以避免前面输出一堆没用的数据
            $key = array_keys($order);
            $lastkey = end($key);//最后一个key
            $end_date = strtotime(date('Y-m-d',$order["$lastkey"]['add_time']))+24*3600;  //数据最后时间为循环结束点，大范围查询可以避免前面输出一堆没用的数据
            for($i=$sratus_date;$i<=$end_date;$i=$i+24*3600){   //循环时间
                $date = $day[] = date('Y-m-d',$i);
                $everyday_end_time = $i+24*3600;
                $goods_amount=$cost_price =$shipping_amount=$coupon_amount=$order_prom_amount=$total_amount=0.00; //初始化变量
                foreach ($order as $okey => $oval){   //循环订单
                    $for_order_id = $oval['order_id'];
                    if (!isset($order_goods["$for_order_id"])){
                        unset($order[$for_order_id]);           //去掉整个订单都了退货后的
                    }
                    if($oval['add_time'] >= $i && $oval['add_time']<$everyday_end_time){      //统计同一天内的数据
                        $goods_amount      += $oval['goods_price'];
                        $cost_price        += $order_goods["$for_order_id"]['cost_price']; //订单成本价
                        $shipping_amount   += $oval['shipping_price'];
                        $coupon_amount     += $oval['coupon_amount'];
                        $order_prom_amount += $oval['order_prom_amount'];
                        unset($order[$okey]);  //省的来回循环
                    }
                }
                //拼装输出到图表的数据
                $goods_arr[]    = $goods_amount;
                $cost_arr[]     = $cost_price ;
                $shipping_arr[] = $shipping_amount;
                $coupon_arr[]   = $coupon_amount;

                $list[] = [
                    'day'=>$date,
                    'goods_amount'      => $goods_amount,
                    'cost_amount'       => $cost_price,
                    'shipping_amount'   => $shipping_amount,
                    'coupon_amount'     => $coupon_amount,
                    'order_prom_amount' => $order_prom_amount,
                    'end'=>$everyday_end_time,
                ];  //拼装列表
            }
            rsort($list);
        }
        $this->assign('list',$list);
        $result = ['goods_arr'=>$goods_arr,'cost_arr'=>$cost_arr,'shipping_arr'=>$shipping_arr,'coupon_arr'=>$coupon_arr,'time'=>$day];
        $this->assign('result',json_encode($result));
        return $this->fetch();
    }
}