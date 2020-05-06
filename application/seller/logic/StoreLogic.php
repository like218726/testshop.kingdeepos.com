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
 * Author: 当燃
 * Date: 2016-06-09
 */


namespace app\seller\logic;

use think\Model;
use think\Db;

class StoreLogic extends Model
{
    private $storeInfo=[];
    public function setStoreInfo($storeInfo){
        $this->storeInfo = $storeInfo;
    }
    
    /**
     * 获取指定店铺信息
     * @param $uid int 用户UID
     * @param bool $relation 是否关联查询
     *
     * @return mixed 找到返回数组
     */
    public function detail($store_id, $relation = true)
    {
        $user = D('Store')->where(array('store_id' => $store_id))->relation($relation)->find();
        return $user;
    }

    /**
     * 添加申请店铺等级
     * @param $post_data
     * @return array
     */
    public function editStoreReopen($post_data){
        $re_grade_id = $post_data['re_grade_id'];
        $re_year = $post_data['re_year'];
        $store_id =$this->storeInfo['store_id'];
        $store_grade = Db::name('store_grade')->where(['sg_id'=>$re_grade_id])->find();  //店铺等级
        if (empty($store_grade))return ['status' => -1, 'msg' => '参数错误！'];
        $reopen_count = Db::name('store_reopen')->where(['re_store_id'=>$store_id,'re_state'=>['notIn','-1,2']])->count();  //店铺等级
        if ($reopen_count>0)return ['status' => -1, 'msg' => '您有申请未完成！'];
        $data=[
            're_grade_id'       =>$re_grade_id,    //申请等级
            're_year'           =>$re_year,        //续签时长
            're_grade_price'    =>$store_grade['sg_price'],    //等级收费(元/年)
            're_grade_name'     =>$store_grade['sg_name'],     //等级名称
            're_pay_amount'     =>$store_grade['sg_price']*$re_year,    //应付总金额
            're_store_id'       =>$this->storeInfo['store_id'],     //店铺ID
            're_store_name'     =>$this->storeInfo['store_name'],   //店铺名称
            're_start_time'     =>time(),                           //有效期开始时间
            're_end_time'       =>strtotime("+$re_year year"),      //有效期结束时间
            're_create_time'    =>time(),                           //记录创建时间
            're_state'          =>1,
            're_pay_cert'       =>$post_data['re_pay_cert'],
            're_pay_cert_explain'       =>$post_data['re_pay_cert_explain'],
        ];
        $res = Db::name('store_reopen')->add($data);
        if ($res === false){
            return ['status' => -1, 'msg' => '申请失败！'];
        }else{
            return ['status' => 1, 'msg' => '申请成功！'];
        }
    }
  
}