<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/28
 * Time: 11:27
 */

namespace app\admin\validate;

use think\Validate;

class Help extends Validate
{

    //验证规则
    protected $rule = [
        'help_id'        => 'require',
        'type_id'        => 'gt:0',
        'help_title'     => 'require|checkHelpTitle',
        'help_info'      => 'require',
    ];

    //错误消息
    protected $message = [
        'help_id'                    => '非法操作',
        'type_id.gt'                 => '请选择所属分类',
        'help_title.require'         => '标题不能为空',
        'help_title.checkHelpTitle'  => '标题不能重复',
        'help_info.require'          => '请填写帮助内容',
    ];

    //验证场景
    protected $scene = [
        'add'  => ['help_title','type_id','help_info'],
        'edit' => ['help_title','type_id','help_info'],
        'del'  => ['help_id'],
    ];

    /**
     * 验证标题
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     */
    public function checkHelpTitle($value, $rule ,$data){
        $res = M('help')->where(['help_title'=>$value])->find();
        if($res['help_id']==$data['help_id']){
            return true;
        }
        if($res>0){
            return false;
        }
        return true;
    }
}