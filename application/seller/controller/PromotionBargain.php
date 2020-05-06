<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * Author: 当燃
 * 专题管理
 * Date: 2016-03-09
 */

namespace app\seller\controller;

use app\common\model\BargainFirst;
use app\common\model\BargainList;
use think\Page;
use think\Loader;
use think\Db;

class PromotionBargain extends Base
{

    //砍价活动
    public function index()
    {
		/*code_25砍价逻辑代码*/
        $where['deleted'] = 0;
        $where['store_id'] = STORE_ID;
        $PromotionBargain = new \app\common\model\PromotionBargain();
        $count = $PromotionBargain->where($where)->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $prom_list = $PromotionBargain->append(['status_desc'])->where($where)->with('promotionBargainGoodsItem')->order("is_end asc ,id desc")->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('prom_list', $prom_list);
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('pager', $Page);
        $status = [
            '0' => '审核中',
            '1' => '已通过',
            '2' => '未通过',
            '3' => '已关闭',
        ];
        $this->assign('status', $status);
        return $this->fetch();
		/*code_25砍价逻辑代码*/
    }

    /**
     * 发起者列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function bargain_first()
    {
		/*code_25砍价逻辑代码*/
        $bargain_id = input('id');
        $where['bargain_id'] = $bargain_id;
        $BargainFirst = new BargainFirst();
        $count = $BargainFirst->where($where)->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $prom_list = $BargainFirst->where($where)->order("id desc")->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('prom_list', $prom_list);
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('pager', $Page);
        return $this->fetch();
		/*code_25砍价逻辑代码*/
    }

    /**
     * 参与者列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function bargain_list()
    {
		/*code_25砍价逻辑代码*/
        $bargain_id = input('id');
        $where['bargain_first_id'] = $bargain_id;
        $BargainList = new BargainList();
        $count = $BargainList->where($where)->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $prom_list = $BargainList->where($where)->order("cut_price desc")->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('prom_list', $prom_list);
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('pager', $Page);
        return $this->fetch();
		/*code_25砍价逻辑代码*/
    }


    /**
     * 创建和更新砍价
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function bargain_info()
    {
		/*code_25砍价逻辑代码*/
        if (IS_POST) {
            $data = I('post.');
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);
            $data['store_id'] = STORE_ID;
            $validate = Loader::validate('PromotionBargain');
            if (!$validate->batch()->check($data)) {
                $return = ['status' => 0, 'msg' => '操作失败', 'result' => $validate->getError()];
                $this->ajaxReturn($return);
            }
            if($data['id']){
                $PromotionBargain = \app\common\model\PromotionBargain::get(['id' => $data['id'], 'is_end' => 0]);
                if(empty($PromotionBargain)){
                    $this->ajaxReturn(array('status' => 0, 'msg' => '该活动已结束或者不存在','result'=>''));
                }
            }else{
                $PromotionBargain = new \app\common\model\PromotionBargain();
            }
            $PromotionBargain->data($data, true);
            $row = $PromotionBargain->allowField(true)->save();
            $team_goods_item_ids = db('promotion_bargain_goods_item')->where(['bargain_id'=> $PromotionBargain->id])->column('item_id');
            if ($team_goods_item_ids) {
                Db::name('spec_goods_price')->where('item_id', 'IN', $team_goods_item_ids)->update(['prom_id' => 0, 'prom_type' => 0]);
            }
            $team_goods_goods_ids = db('promotion_bargain_goods_item')->where('bargain_id', $PromotionBargain->id)->column('goods_id');
            Db::name('goods')->where('goods_id', 'IN', $team_goods_goods_ids)->update(['prom_id' => 0, 'prom_type' => 0]);
            db('promotion_bargain_goods_item')->where(['bargain_id'=> $PromotionBargain->id])->where('item_id', 'IN', $team_goods_item_ids)->delete();
            db('promotion_bargain_goods_item')->where(['bargain_id'=>$PromotionBargain->id])->where('goods_id', 'IN', $team_goods_goods_ids)->delete();
            foreach($data['team_goods_item'] as $item){
                db('promotion_bargain_goods_item')->insert(['bargain_id'=>$PromotionBargain->id,'goods_id'=>$data['goods_id'],'item_id'=>$item['item_id'],'start_price'=>$item['start_price'],'end_price'=>$item['end_price'],'goods_num'=>$item['goods_num']]);
                if($item['item_id'] > 0){
                    Db::name('spec_goods_price')->where(['item_id'=>$item['item_id']])->update(['prom_id' => $PromotionBargain->id, 'prom_type' => 8]);
                }
            }
            //被返回的数组将使用数值键，从 0 开始并以 1 递增。
            $data['team_goods_item'] = array_values($data['team_goods_item']);
            if($data['team_goods_item'][0]['item_id'] > 0){
                Db::name('goods')->where(['goods_id' => $PromotionBargain->goods_id])->update(['prom_type' => 8, 'prom_id' => 0]);
            }else{
                Db::name('goods')->where(['goods_id' => $PromotionBargain->goods_id])->update(['prom_id' => $PromotionBargain->id, 'prom_type' => 8]);
            }
            if($row !== false){
                $this->ajaxReturn(['status' => 1, 'msg' => '操作成功', 'result' => '']);
            }else{
                $this->ajaxReturn(['status' => 0, 'msg' => '操作失败', 'result' => '']);
            }

        }
        $id = I('id');
        $info['start_time'] = time()+3600;
        $info['end_time'] = time() + 86400;

        if ($id > 0) {
            $PromotionBargain = new \app\common\model\PromotionBargain();
            $info = $PromotionBargain->with('goods')->find($id);
            $isHaveOrder = Db::name('bargain_first')->where(['bargain_id'=>$id])->find();
            if($isHaveOrder || $info['is_end'] == 1){
                $info['is_prohibit'] = 1;
            }else{
                $info['is_prohibit'] = 0;
            }
        }
        $this->assign('info', $info);
        return $this->fetch();
		/*code_25砍价逻辑代码*/
    }

    public function bargain_del()
    {
        $id = I('del_id/d');
        $bargain_first = db('bargain_first')->where(['bargain_id'=>$id])->find();
        if($bargain_first){ $this->ajaxReturn(['status'=>0,'msg'=>'该活动已存在订单，不能删除']);}
        if ($id) {
            $PromotionBargain = \app\common\model\PromotionBargain::get(['id'=>$id]);
            if($PromotionBargain['promotion_bargain_goods_item'][0]['item_id'] > 0){
                //有规格
                $item_ids = get_arr_column($PromotionBargain['promotion_bargain_goods_item'], 'item_id');
                $item_ids = array_unique($item_ids);
                db('spec_goods_price')->where(['item_id'=>['IN', $item_ids],'prom_id'=>$id,'prom_type'=>8])->save(['prom_type' => 0, 'prom_id' => 0]);
                $goodsPromCount = Db::name('spec_goods_price')->where(['goods_id'=>$PromotionBargain['goods_id']])->where('prom_type','>',0)->count('item_id');
                if($goodsPromCount == 0){
                    db('goods')->where("goods_id", $PromotionBargain['goods_id'])->save(['prom_type' => 0, 'prom_id' => 0]);
                }
            }else{
                db('goods')->where(["goods_id"=>$PromotionBargain['goods_id'], 'prom_id' => $id])->save(['prom_type' => 0, 'prom_id' => 0]);
            }
//            $spec_goods = Db::name('spec_goods_price')->where(['prom_type' => 8, 'prom_id' => $id])->find();
//            //有活动商品规格
//            if($spec_goods){
//                Db::name('spec_goods_price')->where(['prom_type' => 8, 'prom_id' => $id])->save(array('prom_id' => 0, 'prom_type' => 0));
//                //商品下的规格是否都没有活动
//                $goods_spec_num = Db::name('spec_goods_price')->where(['prom_type' => 8, 'goods_id' => $spec_goods['goods_id']])->find();
//                if(empty($goods_spec_num)){
//                    //商品下的规格都没有活动,把商品回复普通商品
//                    Db::name('goods')->where(['goods_id' => $spec_goods['goods_id']])->save(array('prom_id' => 0, 'prom_type' => 0));
//                }
//            }else{
//                //没有商品规格
//                Db::name('goods')->where(['prom_type' => 8, 'prom_id' => $id])->save(array('prom_id' => 0, 'prom_type' => 0));
//            }
            $PromotionBargain->save(['deleted'=>1]);
            db('promotion_bargain_goods_item')->where(['bargain_id' => $id])->update(['deleted'=>1]);
            // 删除砍价消息
            $messageLogic = new \app\common\logic\MessageActivityLogic([]);
            $messageLogic->deletedMessage($id, 1);
            $this->ajaxReturn(['status'=>1,'msg'=>'删除成功']);
        } else {
            $this->ajaxReturn(['status'=>0,'msg'=>'删除失败']);
        }
    }

    /**
     * 点击按钮关闭
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function change_prom_is_end()
    {
        $id = input('id/d');
        $PromotionBargain = new \app\common\model\PromotionBargain();
        $PromotionBargain = $PromotionBargain->find($id);
        if ($PromotionBargain['end_time'] < time()) {
            $this->ajaxReturn(['status'=>0,'msg'=>'该活动已经过期']);
        }
        $PromotionBargain['is_end'] == 0 ? $PromotionBargain['is_end'] = 1 : $PromotionBargain['is_end'] = 0;
        $PromotionBargain->save();
        clearCache();
        $this->ajaxReturn(['status'=>1,'msg'=>'成功']);
    }
}