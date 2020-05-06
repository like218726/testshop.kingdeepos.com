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
 * Date: 2019-08-30
 */

namespace app\admin\logic;

use think\Db;
use think\Model;


class CensusLogic extends Model
{
    /**
	 * 获取待审核 商品数量
	 * @return int $count
     */
    public function getWaitGoodsCount()
	{
		$count = db('goods')->where('goods_state = 0 and is_on_sale < 2')->count();
        return $count;
    }

	/**
	 * 获取待处理 退款订单数
	 * @return int $count
	 */
	public function getWaitRefundOrderCount()
	{
		$count = db('order')->where('shipping_status = 0 and order_status = 3 and pay_status > 0 and pay_status < 3')->count();
		return $count;
	}

	/**
	 * 获取待处理 售后退货订单数
	 * @return int $count
	 */
	public function getWaitRefundCount()
	{
		$count = db('return_goods')->where('type < 2 and status = 3')->count();
		return $count;
	}

	/**
	 * 获取待审核 开店申请数
	 * @return int $count
	 */
	public function getWaitStoreCount()
	{
		$count = db('store_apply')->where('apply_state = 0 and add_time > 0')->count();
		return $count;
	}

	/**
	 * 获取待审核 签约申请数
	 * @return int $count
	 */
	public function getWaitStoreReopenCount()
	{
		$count = db('store_reopen')->where('re_state = 1')->count();
		return $count;
	}

	/**
	 * 获取待审核 经营类目数
	 * @return int $count
	 */
	public function getWaitClassCount()
	{
		$count = db('store_bind_class')->where('state = 0')->count();
		return $count;
	}

	/**
	 * 获取待审核 商家提现申请数
	 * @return int $count
	 */
	public function getWaitStoreWithdrawalsCount()
	{
		$count = db('store_withdrawals')->where('status = 0')->count();
		return $count;
	}

	/**
	 * 获取待审核 会员提现申请数
	 * @return int $count
	 */
	public function getWaitWithdrawalsCount()
	{
		$count = db('withdrawals')->where('status = 0')->count();
		return $count;
	}

	/**
	 * 获取未处理 投诉数
	 * @return int $count
	 */
	public function getWaitComplainCount()
	{
		$count = db('complain')->where('complain_state = 1')->count();
		return $count;
	}

	/**
	 * 获取未处理 举报数
	 * @return int $count
	 */
	public function getWaitExposeCount()
	{
		$count = db('expose')->where('expose_state = 1')->count();
		return $count;
	}

	/**
	 * 获取待审核 抢购活动数
	 * @return int $count
	 */
	public function getWaitFlashCount()
	{
		$count = db('flash_sale')->where('status = 0')->count();
		return $count;
	}

	/**
	 * 获取待审核 拼团活动数
	 * @return int $count
	 */
	public function getWaitTeamCount()
	{
		$count = db('team_activity')->where('status = 0 and deleted = 0')->count();
		return $count;
	}

	/**
	 * 获取待审核 预售活动数
	 * @return int $count
	 */
	public function getWaitPreSellCount()
	{
		$count = db('pre_sell')->where('status = 0 ')->count();
		return $count;
	}

	/**
	 * 订单数、销售统计 (订单是已付款，且状态是1,2,4的才统计) (柱状图)
	 * @param  int $time_type    1=7天 2=一个月 3=半年
	 * @return mixed
	 */
	public function getOrderStatistic($time_type)
	{
		if (empty($time_type) || 1 == $time_type) {
			$start_time = date('Y-m-d', strtotime("-7 day"));//7天前
		}else if (2 == $time_type) {
			$start_time = date('Y-m-d', strtotime("-1 month"));//一个月前
		} if (3 == $time_type) {
			$start_time = date('Y-m-d', strtotime("-6 month"));//半年前
		}

		$start_time = strtotime($start_time);
		$end_time = strtotime(date('Y-m-d'));

		$res = Db::name("order")
				->field(" COUNT(*) as tnum, sum(total_amount-shipping_price) as amount, FROM_UNIXTIME(add_time,'%Y-%m-%d') as gap ")
				->where(" add_time >$start_time and add_time < $end_time AND (pay_status=1 or pay_code='cod') and order_status in(1,2,4) ")
				->group('gap')
				->select();
		foreach ($res as $val){
			$arr[$val['gap']] = $val['tnum'];
			$brr[$val['gap']] = $val['amount'];
		}
		for($i=$start_time;$i<=$end_time;$i=$i+24*3600){
			$tmp_num = empty($arr[date('Y-m-d',$i)]) ? 0 : $arr[date('Y-m-d',$i)];
			$tmp_amount = empty($brr[date('Y-m-d',$i)]) ? 0 : $brr[date('Y-m-d',$i)];
			$order_arr[] = $tmp_num;
			$amount_arr[] = $tmp_amount;
			$date = date('Y-m-d',$i);
			$day[] = $date;
		}
		$result = array('order'=>$order_arr, 'amount'=>$amount_arr, 'time'=>$day);
		return json_encode($result);
	}

}