<?php

return array(
    'code'=> 'newalipayMobile',
    'name' => '支付宝支付(WAP站)',
    'version' => '1.0',
    'author' => 'jy_pwn',
    'desc' => 'WAP手机网站支付宝',
    'scene' =>1,  // 使用场景 0 PC+手机 1 手机 2 PC
    'icon' => 'logo.jpg',
    'config' => array(
        array('name' => 'app_id','label'=>'支付宝appid'  , 'description' => '' ,  'type' => 'text',   'value' => '' ),
        array('name' => 'merchant_private_key','label'=>'商户私钥' , 'description' => '商户私钥', 'type' => 'textarea',   'value' => '' ),
        array('name' => 'alipay_public_key','label'=>'支付宝公钥' , 'description' => '支付宝公钥', 'type' => 'textarea',   'value' => '' ),
        array('name' => 'is_bank','label'=>'是否开通网银'  , 'description' => '' ,        'type' => 'select', 'option' => array(
            '0' => '否',
            '1' =>  '是',
        ))   
    ),
);