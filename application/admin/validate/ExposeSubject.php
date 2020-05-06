<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/6
 * Time: 14:27
 */

namespace app\admin\validate;

use think\Validate;
use think\Db;
class ExposeSubject  extends Validate
{
    //验证规则
    protected $rule = [
        'expose_subject_type_id'  => 'require',
        'expose_subject_type_name'  => 'require',
        'expose_subject_content'   => 'require|max:100|checkContent'
    ];

    //错误消息
    protected $message = [
        'expose_subject_type_id.require'      => '举报类型必须',
        'expose_subject_type_name.require'    => '举报类型名称必须',
        'expose_subject_content.require'      => '举报主题内容必须',
        'expose_subject_content.max'          => '举报主题内容不能超过100个字符',
        'expose_subject_content.checkContent' => '举报主题内容不能重复',
    ];

    /**
     * 验证分类名
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     */
    protected function checkContent($value,$rule,$data){
        $count = DB::name('expose_subject')->where(['expose_subject_type_id'=>$data['expose_subject_type_id'],'expose_subject_content'=>$value])->count();
        if($count){
            return false;
        }
        return true;
    }

}