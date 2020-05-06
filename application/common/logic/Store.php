<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * Author: 当燃
 * Date: 2016-03-19
 */
namespace app\common\logic;
use think\Model;
use think\Db;
/**
 *
 * Class orderLogic
 * @package common\Logic
 */
class Store
{
    private $store;

    public function __construct($store_id)
    {
        $this->store = \app\common\model\Store::get($store_id);
    }
    /**
     * 更新店铺评分
     */
    public function updateStoreScore()
    {
        $order_comment = Db::name('order_comment')->field("AVG(describe_score) AS desc_credit,AVG(seller_score) AS service_credit,AVG(logistics_score) AS delivery_credit")
            ->where(['store_id'=>$this->store['store_id']])->find();
        if($order_comment){
            $this->store['store_desccredit'] = $order_comment['desc_credit'];
            $this->store['store_servicecredit'] = $order_comment['service_credit'];
            $this->store['store_deliverycredit'] = $order_comment['delivery_credit'];
            $this->store->save();
        }
    }

}