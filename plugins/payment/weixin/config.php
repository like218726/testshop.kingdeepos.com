<?php

return array(
    'code'=> 'weixin',
    'name' => '微信支付(PC&微商城)',
    'version' => '1.0',
    'author' => 'IT宇宙人',
    'desc' => 'PC扫码支付&微信商城支付',
    'icon' => 'logo.jpg',
    'scene' => 0,  // 使用场景 0 PC+手机 1 手机 2 PC
    'config' => array(
        array('name' => 'appid','label'=>'绑定支付的APPID','type' => 'text',   'value' => '','hint'=>''), // * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
        array('name' => 'mchid',   'label'=>'商户号', 'type' => 'text',   'value' => ''), // * MCHID：商户号（必须配置，开户邮件中可查看）
        array('name' => 'key',  'label'=>'商户支付密钥', 'type' => 'text',   'value' => ''), // KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
        array('name' => 'appsecret', 'label'=>'公众帐号secert（仅JSAPI支付的时候需要配置)', 'type' => 'text',   'value' => ''), // 公众帐号secert（仅JSAPI支付的时候需要配置)，
        array('name' => 'apiclient_cert',  'label'=>'支付商户证书apiclient_cert', 'type' => 'file',   'value' => ''),// 支付商户api证书（必须配置，企业付款时使用)，
        array('name' => 'apiclient_key',  'label'=>'支付商户证书密钥apiclient_key', 'type' => 'file',   'value' => ''),// 支付商户api证书密钥（必须配置，企业付款时使用)，
    ),
);