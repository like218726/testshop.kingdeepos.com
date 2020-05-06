<?php


namespace app\common\validate;

use think\Validate;
class MomentsClassify extends Validate
{
    protected $rule = [
//        'user_id' => 'require|number',
        'sort_order' => 'require|number',
        'name' => 'require|max:5',

    ];

    protected $message = [
        'name.max' => '名称不能超过5个字',
        'sort_order.number'=>'排序必须是数字'
    ];
}