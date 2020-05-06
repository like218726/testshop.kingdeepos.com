<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/28
 * Time: 11:27
 */

namespace app\admin\validate;

use think\Validate;

class HelpType extends Validate
{

    //验证规则
    protected $rule = [
        'type_id'        => 'require|checkTypeId',
        'type_name'     => 'require|checkTypeName',
    ];

    //错误消息
    protected $message = [
        'type_id'                    => '非法操作',
        'type_name.require'         => '分类名称不能为空',
        'type_name.checkTypeName'  => '分类名称不能重复',
    ];

    //验证场景
    protected $scene = [
        'add'  => ['type_name'],
        'edit' => ['type_name'],
        'del'  => ['type_id'],
    ];

    /**
     * 验证分类名
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     */
    public function checkTypeName($value, $rule ,$data){
        $res = M('help_type')->where(['type_name'=>$value])->find();
        if($res['type_id']==$data['type_id']){
            return true;
        }
        if($res>0){
            return false;
        }
        return true;
    }

    /**
     * * 验证删除分类
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     */
    public function checkTypeId($value, $rule ,$data){
        if($value<11){
            return '系统默认分类不得删除';
        }
        if (M('help_type')->where('pid', $data['type_id'])->count()>0)
        {
            return '还有子分类，不能删除';
        }
        if (M('help')->where('type_id', $data['type_id'])->count()>0)
        {
            return '该分类下有文章，不允许删除，请先删除该分类下的文章';
        }
        return true;
    }
}