<?php
namespace app\seller\validate;

use think\Validate;
use think\Db;

class StoreAddress extends Validate
{
    // 验证规则
    protected $rule = [
        'consignee' => 'require|max:50',
        'province_id' => 'require',
        'city_id' => 'require|number|gt:0',
        'district_id' => 'require|number|gt:0',
        'address' => 'require|max:250',
        'zip_code' => ['require','regex'=>'\d{6}'],
        'mobile' => ['require','regex'=>'/1[3456789]\d{9}$/'],
        'is_default' => 'require',
        'type' => 'require',
    ];
    //错误信息
    protected $message = [
        'consignee.require' => '收货人必须',
        'consignee.max' => '名字不得超过50字符',
        'province_id.require' => '请选择省',
        'city_id.require' => '请选择市',
        'district_id.require' => '请选择区/县',
        'address.require' => '请填写地址',
        'address.max' => '地址不能超过250个字符',
        'zip_code.require' => '邮政编码必须',
        'zip_code.regex' => '邮政编码格式错误',
        'mobile.require' => '手机号码必须',
        'mobile.regex' => '手机号码格式错误',
        'is_default.require' => '是否选择默认必须',
        'type.require' => '类型必须',
    ];


}