<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/9
 * Time: 10:59
 */

namespace app\common\model;

use think\Db;
use think\Model;

class Invoice extends Model
{
    /**
     * 用户
     * @return $this
     */
    public function user(){
        return $this->hasOne('users','user_id','user_id');
    }

    /**
     * 用户
     * @return $this
     */
    public function order(){
        return $this->hasOne('order','order_id','order_id');
    }
    /**
     * 店铺
     * @return $this
     */
    public function store(){
        return $this->hasOne('store','store_id','store_id');
    }

    /**
     * 发票类型对应中文
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getInvoiceTypeAttr($value, $data){
        $type=['普通发票','电子发票','增值税发票'];
        $invoice_type = $data['invoice_type'];
        return $type["$invoice_type"];
    }
}