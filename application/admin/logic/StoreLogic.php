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
 * Date: 2016-06-09
 */


namespace app\admin\logic;
use think\Model;
use think\Db;

class StoreLogic extends Model
{    
    
    /**
     * 获取指定店铺信息
     * @param int $store_id 用户UID
     * @param bool $relation 是否关联查询
     * @return mixed 找到返回数组
     */
    public function detail($store_id, $relation = true)
    {
        $user = D('Store')->where(array('store_id' => $store_id))->relation($relation)->find();
        return $user;
    }
    
    /**
     * 修改店铺信息
     * @param int $uid
     * @param array $data
     * @return array

    public function update($store_id = 0, $data = array())
    {
        $db_res = D('User')->where(array("user_id" => $store_id))->data($data)->save();
        if ($db_res) {
            return array(1, "用户信息修改成功");
        } else {
            return array(0, "用户信息修改失败");
        }
    }
     */
    
    /**
     * 添加店铺
     * @param array $store
     * @return array
     */
    public function addStore($store)
    {
    	Db::startTrans();
		//添加店铺信息
		$store_id = Db::name('store')->add($store);
        $store_extend_count = Db::name('store_extend')->where(['store_id'=>$store_id])->count();
        if($store_extend_count == 0){
            Db::name('store_extend')->add(array('store_id'=>$store_id));
        }
		if($store['is_own_shop'] == 0){
			//添加驻外店铺
			$apply = array('seller_name'=>$store['seller_name'],'user_id'=>$store['user_id'],
					'store_name'=>$store['store_name'],'company_province'=>0,'sc_bail'=>0,'apply_state'=>1,
			);
			M('store_apply')->add($apply);
		}
		//添加店铺管理员
		$seller = array('seller_name'=>$store['seller_name'],'store_id'=>$store_id,'user_id'=>$store['user_id'],'is_admin'=>1);
		$seller_id = Db::name('seller')->add($seller);
		if( $store_id && $seller_id){
			Db::commit();
			adminLog('新增店铺：'.$store['store_name']);
			return $store_id;
		}else{
			Db::rollback();
			return false;
		}	
    }
    
    /**
     * 改变用户密码
     * @param $store_id
     * @param $oldPassword
     * @param $newPassword
     * @return string
     */
    public function changePassword($store_id, $oldPassword, $newPassword)
    {
    
        $user = $this->detail($store_id);
        if ($user['user_pass'] != encrypt($oldPassword)) {
            return array(0, "原用户密码不正确");
        }
        $data['user_id'] = $store_id;
        $data['user_pass'] = encrypt($newPassword);
    
        if (D('User')->where(array("user_id" => $store_id))->data($data)->save()) {
            return array(1, "密码修改成功", U("Admin/login/logout"));
        } else {
            return array(0, "密码修改失败");
        }
    
    }
    
    
    /**
     * 生成新的Hash
     * @param $authInfo
     * @return string
     */
    public function genHash(&$authInfo)
    {
        $User = D('User', 'Logic');    
        $condition['user_id'] = $authInfo['user_id'];
        $session_code = encrypt($authInfo['user_id'] . $authInfo['user_pass'] . time());
        $User->where($condition)->setField('user_session', $session_code);
    
        return $session_code;
    }
    
    public function getAuth($role_id)
    {
    	return $role_id;
    }

    /**
     * 自动给商家结算
     * @param $store_id
     * @return bool
     */
    public function auto_transfer($store_id){
        // 确认收货多少天后 自动结算给 商家
        $today_time = time();
        $auto_transfer_date = tpCache('shopping.auto_transfer_date');
        $time = strtotime("-$auto_transfer_date day");  // 计算N天以前的时间戳
		//销售商的结算
        $where =[
            'store_id' => $store_id,
            'order_status'=> ['in','2,4'],
            'confirm_time'=> ['lt',$time],
            'order_statis_id' => 0
        ];
        $list = Db::name('order')->field('order_id,confirm_time')->where($where)->order('order_id')->select();
        $data = array(
            'start_date' => $list[0]['confirm_time'], // 结算开始时间
            'end_date'   => $today_time - $auto_transfer_date, //结算截止时间
            'create_date'=>  $today_time, // 记录创建时间            
            'store_id'   => $store_id, // 店铺id
        );
        if(!empty($list)) {
            foreach ($list as $key => $val) {
                $return_goods = M('return_goods')->where("order_id = {$val['order_id']} and status not in (-2,5)")->select();
                
                if($return_goods) continue;//如果有售后申请未完成，则不结算
                $order_settlement = order_settlement($val['order_id']); // 调用全局结算方法
                $data['order_totals'] += $order_settlement['goods_amount'];// 订单商品金额
                $data['shipping_totals'] += $order_settlement['shipping_price'];// 运费
                $data['return_integral'] +=  $order_settlement['return_integral'];// 退还积分
                $data['commis_totals'] +=  $order_settlement['settlement'];// 平台抽成
                $data['give_integral'] +=  $order_settlement['give_integral'];// 送出积分金额
                $data['result_totals'] +=  $order_settlement['store_settlement'];// 本期应结
                $data['order_prom_amount'] +=  $order_settlement['order_prom_amount'];// 优惠价
                $data['coupon_price'] +=  $order_settlement['coupon_price'];// 优惠券抵扣
                $data['distribut'] +=  $order_settlement['distribut'];// 分销金额
                $data['integral'] +=  $order_settlement['integral'];//订单使用积分
                $data['return_totals'] += $order_settlement['return_totals'];//若订单商品退款，退还金额
                $data['refund_integral'] += $order_settlement['refund_integral'];//若订单商品退款，退还积分
                $data['pay_money'] += $order_settlement['pay_money'];//实付款
                $data['discount'] += $order_settlement['discount'];
                $data['get_shipping_totals'] += $order_settlement['supplier_shipping_price'];//给供应商的运费
                $order_id_arr1[] = $val['order_id'];
            }
	    }
	
	    //供应商的结算
		$where['suppliers_id'] = $store_id;
		$where['supplier_order_statis_id'] = 0;
		unset($where['store_id']);
		unset($where['order_statis_id']);
		$list2 = Db::name('order')->field('order_id,confirm_time,supplier_shipping_price')->where($where)->order('order_id')->select();
		if(!empty($list2)) {
			foreach ($list2 as $key => $val) {
				$return_goods = M('return_goods')->where("order_id = {$val['order_id']} and status not in (-2,5)")->select();
				if($return_goods) continue;//如果有售后申请未完成，则不结算
                $order_goods = M('order_goods')->where(array('order_id' => $val['order_id']))->select();//订单商品
				foreach ($order_goods as $goodsVal) {
					$data['result_totals'] += $goodsVal['cost_price'];
				}
                $data['result_totals'] += $val['supplier_shipping_price'];
                $data['supplier_shipping_totals'] += $val['supplier_shipping_price'];//供应商获得的运费
				$order_id_arr2[] = $val['order_id'];
			}

            if (empty($data['start_date']) || $data['start_date'] > $list2[0]['confirm_time']) {
                $data['start_date'] = $list2[0]['confirm_time'];
            }
		}

        if(!empty($order_id_arr1) || !empty($order_id_arr2)){
            $order_statis_id = M('order_statis')->add($data); // 添加一笔结算统计
			if (!empty($order_id_arr1)) {
				$order_ids1 = implode(',', $order_id_arr1);
				Db::name('order')->whereIn('order_id',$order_ids1)->save(array('order_statis_id' =>$order_statis_id)); // 标识为已经结算
			}
			if (!empty($order_id_arr2)) {
				$order_ids2 = implode(',', $order_id_arr2);
				Db::name('order')->whereIn('order_id',$order_ids2)->save(array('supplier_order_statis_id' =>$order_statis_id)); // 标识为已经结算
			}
            // 给商家加钱 记录日志
            storeAccountLog($store_id,$data['result_totals'],$data['result_totals'] * -1,'平台订单结算');
        }
    }

}