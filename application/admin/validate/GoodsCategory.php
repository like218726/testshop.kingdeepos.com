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
 * Author: IT宇宙人
 * Date: 2015-09-09
 */
namespace app\admin\validate;
use think\Validate;
class GoodsCategory extends Validate {
    //验证规则
    protected $rule = [
        'name'  => 'require',
        'mobile_name'  => 'require',
        'sort_order'   => 'number',
        'parent_id'=>'require|checkLevel',
    ];

    //错误消息
    protected $message = [
        'name.require'                  => '分类名称必须填写', 
        'mobile_name.require'           => '手机分类名称必须填写',
        'sort_order.number'             => '排序必须为数字',
        'parent_id.checkLevel' => '现分类等级只支持一至三级！',
    ];

    /**
     * 验证分类名
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     */
    protected function checkName($value,$rule,$data){
        if(empty($data['id'])){
            if(M('goods_category')->where(['name'=>$value])->count()){
                return false;
            }
        }else{
            if(M('goods_category')->whereNotIn('id',$data['id'],'AND')->where(['name'=>$value])->count()){
                return false;
            }
        }
        return true;
    }
    /**
     * 验证手机分类名
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     */
    protected function checkMoblieName($value,$rule,$data){
        if(empty($data['id'])){
            if(M('goods_category')->where(['mobile_name'=>$value])->count()){
                return false;
            }
        }else{
            if(M('goods_category')->whereNotIn('id',$data['id'],'AND')->where(['mobile_name'=>$value])->count()){
                return false;
            }
        }
        return true;
    }
    
    /**
     * 检测当前修改的等级是否操过三级
     * @param unknown $value
     * @param unknown $rule
     * @param unknown $data  */
    
    protected function checkLevel($value, $rule ,$data)
    {
        $topCategoryLevel = M('GoodsCategory')->where(['id'=>$data['parent_id']])->value('level');

        return ($topCategoryLevel>=3) ? false : true;
    }
}
