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
 * 拼团控制器
 */

namespace app\seller\controller;

use app\common\model\team\TeamActivity;
use app\common\model\team\TeamFound;
use app\seller\logic\TeamActivityLogic;
use think\Loader;
use think\Db;
use think\Page;

class Team extends Base
{
	public function index()
	{
		
		$key_word = input('key_word');
		$where = " 1 = 1 ";
		if ($key_word) {
			$where .= " and ( act_name like '%" .$key_word. "%' or goods_name like '%" .$key_word."%')";
		}
		$TeamActivity = new TeamActivity();
		$count = $TeamActivity->where($where)->where('store_id',STORE_ID)->where('deleted', 0)->count();
		$Page = new Page($count, 10);
		$show = $Page->show();
		$list = $TeamActivity->append(['team_type_desc','time_limit_hours','status_desc'])->where($where)->where('deleted', 0)->where('store_id',STORE_ID)->order('add_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('page', $show);
		$this->assign('list', $list);
		$this->assign('key_word', $key_word);
		return $this->fetch();
		
	}

	/**
	 * 拼团详情
	 * @return mixed
	 */
	public function info()
	{
		
		$team_id = input('team_id');
		if ($team_id) {
			$TeamActivity = new TeamActivity();
			$teamActivity = $TeamActivity->append(['time_limit_hours'])->with('goods')->where(['team_id'=>$team_id,'store_id'=>STORE_ID])->find();
            $isHaveOrder = Db::name('order_goods')->where(['prom_type' => 6, 'prom_id' => $team_id])->count();
			if(empty($teamActivity)){
				$this->error('非法操作');
			}
            if($teamActivity['deleted'] == 1){
                $this->error('该拼团活动已被删除');
            }
			$this->assign('teamActivity', $teamActivity);
			$this->assign('isHaveOrder', $isHaveOrder);
		}
		return $this->fetch();
		
	}

	/**
	 * 保存
	 * @throws \think\Exception
	 */
	public function save(){
	
		$data = input('post.');
		$teamValidate = Loader::validate('Team');
		if (!$teamValidate->batch()->check($data)) {
			$this->ajaxReturn(['status' => 0, 'msg' => '操作失败', 'result' => $teamValidate->getError()]);
		}
		if($data['team_id']){
			$teamActivity = TeamActivity::get(['team_id' => $data['team_id'], 'store_id' => STORE_ID]);
			if(empty($teamActivity)){
				$this->ajaxReturn(array('status' => 0, 'msg' => '非法操作','result'=>''));
			}
		}else{
			$teamActivity = new TeamActivity();
		}
		$data['add_time'] = time();
		$teamActivity->data($data, true);
		$teamActivity['store_id'] = STORE_ID;
		$row = $teamActivity->allowField(true)->save();
		if($data['team_id']){
            addLog('teamActivity','修改拼团活动', $teamActivity);
        }else{
            addLog('teamActivity','添加拼团活动', $teamActivity);
        }

		//变更拼团表后 - s
        $team_goods_item_ids = db('team_goods_item')->where('team_id', $teamActivity->team_id)->column('item_id');
        if ($team_goods_item_ids){
            db('spec_goods_price')->where('item_id', 'IN', $team_goods_item_ids)->update(['prom_id' => 0, 'prom_type' => 0]);
        }
        $team_goods_goods_ids = db('team_goods_item')->where('team_id', $teamActivity->team_id)->column('goods_id');
        db('goods')->where('goods_id', 'IN', $team_goods_goods_ids)->update(['prom_id' => 0, 'prom_type' => 0]);
        db('team_goods_item')->where('team_id', $teamActivity->team_id)->where('item_id', 'IN', $team_goods_item_ids)->delete();
        db('team_goods_item')->where('team_id', $teamActivity->team_id)->where('goods_id', 'IN', $team_goods_goods_ids)->delete();
        foreach($data['team_goods_item'] as $item){
            db('team_goods_item')->insert(['team_id'=>$teamActivity->team_id,'goods_id'=>$data['goods_id'],'item_id'=>$item['item_id'],'team_price'=>$item['team_price']]);
            if($item['item_id'] > 0){
                db('spec_goods_price')->where('item_id', $item['item_id'])->update(['prom_id' => $teamActivity->team_id, 'prom_type' => 6]);
            }
        }
        if($data['team_goods_item'][0]['item_id'] > 0){
            db('goods')->where(['goods_id' => $teamActivity->goods_id])->update(['prom_type' => 6, 'prom_id' => 0]);
        }else{
            db('goods')->where(['goods_id' => $teamActivity->goods_id])->update(['prom_id' => $teamActivity->team_id, 'prom_type' => 6]);
        }
        db('team_goods_item')->where('goods_id',$teamActivity->goods_id)->where('team_id','<>',$teamActivity->team_id)->update(['deleted'=>1]);
        //变更拼团表后 - e

		if($row !== false){
			$this->ajaxReturn(['status' => 1, 'msg' => '操作成功', 'result' => '']);
		}else{
			$this->ajaxReturn(['status' => 0, 'msg' => '操作失败', 'result' => '']);
		}
		
	}

	/**
	 * 删除拼团
	 */
	public function delete(){
        /**
         * 1.查询订单是否有活动中的未取消未作废的订单
         * 2.如果存在团购商品规格，将规格表的活动清0 /不存在规格直接将商品表活动清0
         * 3.如果规格表没有其他活动，将商品标的活动清0
         * 4.软删除拼团活动里的该活动
         * 5.软删除拼团活动
         * 6.
         */
        
        $team_id = input('team_id');
        if(empty($team_id)){
            $this->ajaxReturn(['status' => 0, 'msg' => '参数错误', 'result' => '']);
        }
        $order_goods = Db::name('order')->whereNotIn('order_status','3,5')->where(['prom_type' => 6, 'prom_id' => $team_id])->find();
        if($order_goods){
            $this->ajaxReturn(['status' => 0, 'msg' => '该活动有未取消、未作废的订单参与不能删除!', 'result' => '']);
        }
        $teamActivity = TeamActivity::get(['team_id'=>$team_id]);
        if(empty($teamActivity)){
            $this->ajaxReturn(['status' => 0, 'msg' => '参数错误', 'result' => '']);
        }
        if($teamActivity['team_goods_item']){
            if($teamActivity['team_goods_item'][0]['item_id'] > 0){
                $item_ids = get_arr_column($teamActivity['team_goods_item'], 'item_id');
                $item_ids = array_unique($item_ids);
                db('spec_goods_price')->where('item_id', 'IN', $item_ids)->save(['prom_type' => 0, 'prom_id' => 0]);
                $goodsPromCount = Db::name('spec_goods_price')->where('goods_id', $teamActivity['goods_id'])->where('prom_type','>',0)->count('item_id');
                if($goodsPromCount == 0){
                    db('goods')->where("goods_id", $teamActivity['goods_id'])->save(['prom_type' => 0, 'prom_id' => 0]);
                }
            }else{
                db('goods')->where("goods_id", $teamActivity['goods_id'])->save(['prom_type' => 0, 'prom_id' => 0]);
            }
        }
        db('team_goods_item')->where('team_id', $teamActivity['team_id'])->update(['deleted' => 1]);
        $row = $teamActivity->save(['deleted' => 1]);
        if($row !== false){
            // 删除挼团通知消息
            $this->ajaxReturn(['status' => 1, 'msg' => '删除成功', 'result' => '']);
        }else{
            $this->ajaxReturn(['status' => 0, 'msg' => '删除失败', 'result' => '']);
        }
        
	}

	/**
	 * 确认拼团
	 * @throws \think\Exception
	 */
	public function confirmFound(){
	
		$found_id = input('found_id');
		if(empty($found_id)){
			$this->ajaxReturn(['status'=>0,'msg'=>'参数错误','result'=>'']);
		}
		$TeamFound = new TeamFound();
		$teamFound = $TeamFound::get(['store_id'=>STORE_ID,'found_id'=>$found_id]);
		if(empty($teamFound)){
			$this->ajaxReturn(['status'=>0,'msg'=>'找不到拼单','result'=>'']);
		}
		if(empty($teamFound->order)){
			$this->ajaxReturn(['status'=>0,'msg'=>'找不到拼单的订单','result'=>'']);
		}
		if($teamFound->Surplus > 0){
			$this->ajaxReturn(['status'=>0,'msg'=>'不满足确认拼团条件，还缺'.$teamFound->Surplus,'result'=>'']);
		}
		if($teamFound->order->order_status > 0){
			$this->ajaxReturn(['status'=>0,'msg'=>'拼单已经确认','result'=>'']);
		}
		$follow_order_id = Db::name('team_follow')->where(['found_id' => $found_id, 'status' => 2])->getField('order_id', true);
		$follow_confirm = Db::name('order')->where('order_id', 'IN', $follow_order_id)->where(['prom_type' => 6, 'store_id' => STORE_ID])->update(['order_status' => 1]);
		if($follow_confirm !== false){
			$teamFound->order->order_status = 1;
			$found_confirm = $teamFound->order->save();
			if($found_confirm !== false){
				$this->ajaxReturn(['status'=>1,'msg'=>'拼单确认成功','result'=>'']);
			}else{
				$this->ajaxReturn(['status'=>0,'msg'=>'拼单确认失败','result'=>'']);
			}
		}else{
			$this->ajaxReturn(['status'=>0,'msg'=>'拼单确认失败','result'=>'']);
		}
		
	}

	/**
	 * 拼团退款
	 */
	public function refundFound(){
	
		$found_id = input('found_id');
		if(empty($found_id)){
			$this->ajaxReturn(['status'=>0,'msg'=>'参数错误','result'=>'']);
		}
		$teamFound = TeamFound::get(['store_id'=>STORE_ID,'found_id'=>$found_id]);
		$TeamActivityLogic = new TeamActivityLogic();
		$TeamActivityLogic->setTeamFound($teamFound);
		$result = $TeamActivityLogic->refundFound();
		$this->ajaxReturn($result);
		
	}

	/**
	 * 拼团抽奖
	 */
	public function lottery(){
	
		$team_id = input('team_id/d');
		if(empty($team_id)){
			$this->ajaxReturn(['status'=>0,'msg'=>'参数错误','result'=>'']);
		}
		$team = TeamActivity::get(['store_id'=>STORE_ID,'team_id'=>$team_id]);
		$TeamActivityLogic = new TeamActivityLogic();
		$TeamActivityLogic->setTeam($team);
		$result = $TeamActivityLogic->lottery(session('seller_id'));
		$this->ajaxReturn($result);
		
	}
}
