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
namespace app\common\model;

use think\Model;

class StoreBindPlatformGoods extends Model
{
    public function goods(){
        return $this->hasOne('goods','goods_id','goods_id')->field('goods_id,goods_name,store_cat_id1,store_count,shop_price,sales_sum , 0 as select_num')->where(['prom_type'=>['in',[0,3]],'exchange_integral'=>0,'is_on_sale'=>1,'is_virtual'=>0]);
    }


}
