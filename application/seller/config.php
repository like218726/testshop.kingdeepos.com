<?php
$home_config = [
    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------
	//默认错误跳转对应的模板文件
	'dispatch_error_tmpl' => 'public:dispatch_jump',
	//默认成功跳转对应的模板文件
	'dispatch_success_tmpl' => 'public:dispatch_jump', 
    // URL伪静态后缀
    'url_html_suffix'        => '',
    'NEWS_TAG'=>array(
		'0'=>'最新',
		'1'=>'热门',
		'2'=>'推荐',
		'3'=>'精品'
	),
    'teach'=>false,
];

$html_config = include_once 'html.php';
return array_merge($home_config,$html_config);
?>