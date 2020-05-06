<?php
return array(	
	'goods' =>array('name' => '门店商品', 'icon'=>'ico-goods', 'child' => array(
			array('name' => '商品列表', 'act'=>'goodsList', 'op'=>'Goods'),
	)),
	'order'=>array('name' => '门店订单', 'icon'=>'ico-order', 'child' => array(
			array('name' => '订单列表', 'act'=>'index', 'op'=>'Order'),
	)),
//	'account' => array('name' => '门店职员', 'icon'=>'ico-account', 'child' => array(
//			array('name' => '职员列表', 'act'=>'index', 'op'=>'Admin'),
//	)),
);