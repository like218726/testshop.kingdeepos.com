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
 * 运费模板管理
 * Date: 2017-11-14
 */

namespace app\seller\controller;

use app\seller\logic\FreightLogic;
use app\common\model\FreightTemplate;
use think\Db;
use think\Page;

class Freight extends Base
{

    public function index()
    {
        $FreightTemplate = new FreightTemplate();
        $count = $FreightTemplate->where('store_id', STORE_ID)->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $template_list = $FreightTemplate->append(['type_desc'])->with('freightConfig')->where('store_id', STORE_ID)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $show);
        $this->assign('template_list', $template_list);
        return $this->fetch();
    }

    public function info()
    {
        $template_id = input('template_id');
        if ($template_id) {
            $FreightTemplate = new FreightTemplate();
            $freightTemplate = $FreightTemplate->with('freightConfig')->where(['template_id' => $template_id, 'store_id' => STORE_ID])->find();
			if ($freightTemplate['root_template_id'] > 0) {
                $this->error('此为供应商运费模板，不允许编辑');
            }
            if (empty($freightTemplate)) {
                $this->error('非法操作');
            }
            $this->assign('freightTemplate', $freightTemplate);
        }
        return $this->fetch();
    }

    /**
     *  保存运费模板
     * @throws \think\Exception
     */
    public function save()
    {
		$templateId = input('template_id/d');
		$freightTemplate = Db::name('freight_template')->where(['template_id' => $templateId, 'store_id' => STORE_ID])->find();
		if ($freightTemplate['root_template_id'] > 0) {
			$this->error('此为供应商运费模板，不允许修改');
		}
        $FreightLogic = new FreightLogic();
        $res = $FreightLogic->addEditFreightTemplate();
		
		//供应商品运费模板修改要通知销售商
		if ($templateId) {
			$this->supplierGoodsModify($templateId);
		}
		
        $this->ajaxReturn($res);
    }

    /**
     * 删除运费模板
     * @throws \think\Exception
     */
    public function delete()
    {
        $template_id = input('template_id');
        $action = input('action');
        if (empty($template_id)) {
            $this->ajaxReturn(['status' => 0, 'msg' => '参数错误', 'result' => '']);
        }
		$freightTemplate = Db::name('freight_template')->where(['template_id' => $template_id, 'store_id' => STORE_ID])->find();
		if ($freightTemplate['root_template_id'] > 0) {
			$this->error('此为供应商运费模板，不允许编辑');
		}
        if ($action != 'confirm') {
            $goods_count = Db::name('goods')->where(['template_id' => $template_id, 'store_id' => STORE_ID])->count();
            $supplier_goods_count = Db::name('goods')->where(['template_id' => $template_id, 'store_id' => STORE_ID, 'purpose' => 2])->count();
            if ($goods_count > 0) {
				if ($supplier_goods_count > 0) {
					$msg = '已有' . $goods_count . '种商品（其中供应商品' . $supplier_goods_count . '种）使用该运费模板，确定删除该模板吗？继续删除将把使用该运费模板的商品设置成包邮（销售商的该商品将下架，销售商审理后可再次上架）。';
				} else {
					$msg = '已有' . $goods_count . '种商品使用该运费模板，确定删除该模板吗？继续删除将把使用该运费模板的商品设置成包邮。';
				}
                $this->ajaxReturn(['status' => -1, 'msg' => $msg, 'result' => '']);
            }
        }
		
		$this->supplierGoodsModify($template_id);
		
		Db::name('goods')->where(['template_id' => $template_id, 'store_id' => STORE_ID])->update(['template_id' => 0, 'is_free_shipping' => 1]);
        Db::name('freight_region')->where(['template_id' => $template_id, 'store_id' => STORE_ID])->delete();
        Db::name('freight_config')->where(['template_id' => $template_id, 'store_id' => STORE_ID])->delete();
        $delete = Db::name('freight_template')->where(['template_id' => $template_id, 'store_id' => STORE_ID])->delete();
        if ($delete !== false) {
            $this->ajaxReturn(['status' => 1, 'msg' => '删除成功', 'result' => '']);
        } else {
            $this->ajaxReturn(['status' => 0, 'msg' => '删除失败', 'result' => '']);
        }
    }


    public function area()
    {
        $select_area = [];
        $ids = input('ids'); // region_id
        $name = input('name');
        if($ids){
            $ids_arr = explode(',',$ids);
            $name_arr = explode(',', $name);
            foreach($ids_arr as $key=>$val){
                $arr['region_id'] = $val;
                $arr['name'] = $name_arr[$key];
                $select_area[] = $arr;
            }
        }
        $province_list = Db::name('region')->where(array('parent_id' => 0, 'level' => 1))->select();
        $this->assign('province_list', $province_list);
        $this->assign('select_area', $select_area);
        return $this->fetch();
    }

	/**
     *  修改模板时下价供应商商品
     */
    public function supplierGoodsModify($templateId)
    {
		//记录原始供应商品的修改状态，用于追踪对应销售商商品的相应数据是否修改
		$supplierGoods = Db::name('goods')->where(['template_id' => $templateId, 'store_id' => STORE_ID, 'purpose' => 2])->select();
        if ($supplierGoods) {
    		$dealerGoodsIdArr = [];
    		foreach ($supplierGoods as $val) {
                $dealerGoodsList = Db::name('goods')->where(['root_goods_id' => $val['goods_id']])->select();
                if (count($dealerGoodsList) > 0){
                    $modify['status'] = 1;  //只是运费模板的修改不需要审核
                    $modify['modify_time'] = time();
                    $modify['check_time'] = time();
                    $modify['goods_id'] = $val['goods_id'];
                    $modify['store_id'] = STORE_ID;
                    $dealerGoodsList = Db::name('goods')->where(['root_goods_id' => $val['goods_id']])->select();
                    $dealerIds = array_column($dealerGoodsList, 'store_id');
                    $dealerCount = count($dealerGoodsList);
                    $status = array_fill(0, $dealerCount, 0);
                    $dealerStatus = array_combine($dealerIds, $status);
                    $modify['dealer_status'] =json_encode($dealerStatus);
                    $res = Db::name('supplier_goods_modify')->where('goods_id',$val['goods_id'])->find();
                    if ($res) {//如果有该供应商品的修改记录，就直接覆盖，不管销售商有没全部同意
                        Db::name('supplier_goods_modify')->where('modify_id', $res['modify_id'])->update($modify);
                    } else {
                        Db::name('supplier_goods_modify')->add($modify);
                    }
                    $dealerGoodsId = array_column($dealerGoodsList, 'goods_id');
                    $dealerGoodsIdArr = array_merge($dealerGoodsIdArr, $dealerGoodsId);
                }
    		}
    		Db::name('goods')->where(['goods_id' => ['in', $dealerGoodsIdArr]])->update(['supplier_goods_status' => 1, 'is_on_sale' => 0]);
        }
    }

}