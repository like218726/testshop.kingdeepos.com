<?php
namespace app\common\validate;
use think\Validate;
use think\Db;
class Store extends Validate
{
    // 验证规则
    protected $rule = [
        'longitude' => 'require',
        'latitude' => 'require',
        'region_id' => 'require|number',
    ];
    //错误信息
    protected $message  = [
//        'longitude.require'    => '不能为空',
//        'latitude.require'    => '不能为空',
    ];



}