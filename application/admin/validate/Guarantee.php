<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/28
 * Time: 11:27
 */

namespace app\admin\validate;

use think\Validate;

class Guarantee extends Validate
{

    //验证规则
    protected $rule = [
        'grt_id'        => 'require',
        'grt_cost'      => 'egt:0',
        'grt_sort'      => 'gt:0',
        'grt_name'      => 'require|checkTitle',
        'grt_describe'  => 'require',
    ];

    //错误消息
    protected $message = [
        'grt_id'                    => '非法操作',
        'grt_cost.egt'              => '请输入正确的保证金',
        'grt_sort.gt'               => '请输入排序',
        'grt_name.require'          => '保障名称不能为空',
        'grt_name.checkHelpTitle'   => '名称不能重复',
        'grt_describe.require'      => '请填写保障描述内容',
    ];

    //验证场景
    protected $scene = [
        'add'  => ['grt_name','grt_cost','grt_describe'],
        'edit' => ['grt_name','grt_cost','grt_describe'],
        'del'  => ['grt_id'],
    ];

    /**
     * 验证标题
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     */
    public function checkTitle($value, $rule ,$data){
        $res = db('guarantee_item')->where(['grt_name'=>$value])->find();
        if($res['grt_id']==$data['grt_id']){
            return true;
        }
        if($res>0){
            return false;
        }
        return true;
    }
}