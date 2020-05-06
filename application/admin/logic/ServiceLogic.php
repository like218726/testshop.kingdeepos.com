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
 * Date: 2015-09-09
 */

namespace app\admin\logic;
use think\Model;
use think\Db;

class ServiceLogic extends Model
{
    /**
     * 获取筛选框搜索条件对应的ID
     * 如store_name就去store表获取store_id
     * @param $type
     * @param $qv
     * @return mixed
     */
    public function getConditionId($type,$qv){
            $where["$type"] = array('like','%'.$qv.'%');
            $model = explode('_',$type);
            $column = $model[0].'_id';
            if($type !='order_sn'){
                $id_arr=Db::name("$model[0]")->where($where)->getField("$column",true);
                $data["$column"]=['in',$id_arr];
            }else{
                $data["$type"] = array('like','%'.$qv.'%');
            }
        return $data;
    }

}