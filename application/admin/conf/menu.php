<?php
return	array(	
	'system'=>array('name'=>'平台','child'=>array(
				array('name' => '设置','child' => array(
						array('name'=>'商城设置','act'=>'index','op'=>'System'),
						//array('name'=>'支付方式','act'=>'index1','op'=>'System'),
						array('name'=>'地区&配送','act'=>'region','op'=>'Tools'),
						array('name'=>'短信模板','act'=>'index','op'=>'SmsTemplate'),
						array('name'=>'消息通知','act'=>'index','op'=>'MessageTemplate'),
						//array('name'=>'接口对接','act'=>'index3','op'=>'System'),
						//array('name'=>'验证码设置','act'=>'index4','op'=>'System'),
						array('name'=>'自定义导航栏','act'=>'navigationList','op'=>'System'),
						array('name'=>'首页管理','act'=>'floorList','op'=>'Web'),
						array('name'=>'SEO设置','act'=>'seo','op'=>'System'),
						array('name'=>'快递公司', 'act'=>'index', 'op'=>'Shipping'),
						array('name'=>'清除缓存','act'=>'cleanCache','op'=>'System')
				)),

				array('name' => '权限','child'=>array(
						array('name' => '管理员列表', 'act'=>'index', 'op'=>'Admin'),
						array('name' => '角色管理', 'act'=>'role', 'op'=>'Admin'),
						array('name'=>'权限资源列表','act'=>'right_list','op'=>'System'),
						//array('name' => '管理员日志', 'act'=>'log', 'op'=>'Admin'),
						//array('name' => '供应商列表', 'act'=>'supplier', 'op'=>'Admin'),
				)),

				array('name' => '数据','child'=>array(
						array('name' => '数据备份', 'act'=>'index', 'op'=>'Tools'),
						array('name' => '数据恢复', 'act'=>'restore', 'op'=>'Tools'),
						array('name' => '清空演示数据', 'act'=>'clear_demo_data', 'op'=>'Tools'),
				)),
				array('name' => '云服务','child' => array(
                    array('name' => '插件库', 'act'=>'index', 'op'=>'Plugin'),
                    array('name' => '数据备份', 'act'=>'index', 'op'=>'Tools'),
                    array('name' => '数据还原', 'act'=>'restore', 'op'=>'Tools'),
                )),
                array('name' => 'App','child' => array(
                    array('name' => 'APP基础设置', 'act'=>'basic', 'op'=>'MobileApp'),
                    array('name' => '安卓APP管理', 'act'=>'android_panel', 'op'=>'MobileApp'),
                    array('name' => '苹果APP管理', 'act'=>'ios_panel', 'op'=>'MobileApp'),
                    array('name' => '小程序管理', 'act'=>'mini_app', 'op'=>'MobileApp'),
                )),
                array('name' => '微信','child' => array(
                    array('name' => '公众号配置', 'act'=>'index', 'op'=>'Wechat'),
                    array('name' => '微信菜单管理', 'act'=>'menu', 'op'=>'Wechat'),
                    array('name' => '自动回复', 'act'=>'auto_reply', 'op'=>'Wechat'),
                    array('name' => '粉丝列表', 'act'=>'fans_list', 'op'=>'Wechat'),
                    array('name' => '模板消息', 'act'=>'template_msg', 'op'=>'Wechat'),
                    array('name' => '素材管理', 'act'=>'materials', 'op'=>'Wechat'),
                )),
	)),

//	'extension'=>array('name'=>'推广','child'=>array(
//			array('name' => '广告','child' => array(
//					array('name'=>'广告列表','act'=>'adList','op'=>'Ad'),
//					array('name'=>'广告位置','act'=>'positionList','op'=>'Ad'),
//			)),
//			array('name' => '文章','child'=>array(
//					array('name' => '文章列表', 'act'=>'articleList', 'op'=>'Article'),
//					array('name' => '文章分类', 'act'=>'categoryList', 'op'=>'Article'),
//					array('name'=>'友情链接','act'=>'linkList','op'=>'Article'),
//					array('name' => '会员协议', 'act'=>'agreement', 'op'=>'Article'),
//					array('name' => '专题列表', 'act'=>'topicList', 'op'=>'Topic'),
//
//			)),
//			array('name' => '新闻','child'=>array(
//					array('name' => '新闻列表', 'act'=>'newsList', 'op'=>'News'),
//					array('name' => '新闻分类', 'act'=>'categoryList', 'op'=>'News'),
//			)),
//
//	)),

	 'decorate'=>array('name'=>'页面','child'=>array(
            array('name' => '模板','child'=>array(
                    array('name' => '模板设置', 'act'=>'templateList', 'op'=>'Template'),
                    array('name' => '自定义页面', 'act'=>'pageList', 'op'=>'Block'),
                    array('name' => '智能表单', 'act'=>'form_list', 'op'=>'Block'),
                    array('name' => '个人中心自定义', 'act'=>'mp_center_menu', 'op'=>'System'),
            )),
           	array('name' => '新闻','child'=>array(
                    array('name' => '新闻列表', 'act'=>'newsList', 'op'=>'News'),
                    array('name' => '新闻分类', 'act'=>'categoryList', 'op'=>'News'),
            )),
            array('name' => '文章','child'=>array(
                    array('name' => '文章列表', 'act'=>'articleList', 'op'=>'Article'),
                    array('name' => '文章分类', 'act'=>'categoryList', 'op'=>'Article'),
                    array('name'=>'友情链接','act'=>'linkList','op'=>'Article'),
                    array('name'=>'会员动态','act'=>'momentsList','op'=>'User'),
                    array('name' => '会员协议', 'act'=>'agreement', 'op'=>'Article'),
                    array('name' => '专题列表', 'act'=>'topicList', 'op'=>'Topic'),
                    array('name' => '商家入驻', 'act'=>'helpList', 'op'=>'Article'),
            )),
            array('name' => '广告','child' => array(
                    array('name'=>'广告列表','act'=>'adList','op'=>'Ad'),
            )),

        )),

	 'goods'=>array('name'=>'商城','child'=>array(
           array('name' => '商品','child' => array(
                    array('name' => '商品列表', 'act'=>'goodsList', 'op'=>'Goods'),
                    array('name' => '商品分类', 'act'=>'categoryList', 'op'=>'Goods'),
                    array('name' => '库存日志', 'act'=>'stock_list', 'op'=>'Goods'),
                    array('name' => '商品模型', 'act'=>'goodsTypeList', 'op'=>'Goods'),
   //					array('name' => '商品规格', 'act' =>'specList', 'op' => 'Goods'),
                    array('name' => '品牌列表', 'act'=>'brandList', 'op'=>'Goods'),
                    // array('name' => '库存日志', 'act'=>'brandList', 'op'=>'Goods'),
            )),
           	array('name' => '会员','child'=>array(
                    array('name'=>'会员列表','act'=>'index','op'=>'User'),
                    array('name'=>'会员等级','act'=>'levelList','op'=>'User'),
                    array('name'=>'充值记录','act'=>'recharge','op'=>'User'),
                    //array('name'=>'提现申请','act'=>'withdrawals','op'=>'User'),
                    array('name'=>'会员签到','act'=>'signList','op'=>'User'),
                    //array('name'=>'会员标签','act'=>'labels','op'=>'User'),
            )),
        )),

      'order'=>array('name'=>'订单','child'=>array(
            array('name' => '订单管理','child'=>array(
                array('name' => '订单列表', 'act'=>'index', 'op'=>'Order'),
                array('name' => '虚拟订单', 'act'=>'virtual_list', 'op'=>'Order'),
                array('name' => '拼团列表','act'=>'found_order_list','op'=>'Team'),
                array('name' => '拼团订单','act'=>'order_list','op'=>'Team'),
                array('name' => '订单日志','act'=>'order_log','op'=>'Order'),
            )),
            array('name' => '订单处理','child'=>array(
                array('name' => '退款单', 'act'=>'refund_order_list', 'op'=>'Order'),
                array('name' => '退换货', 'act'=>'return_list', 'op'=>'Order'),
                array('name' => '发票管理','act'=>'index', 'op'=>'Invoice'),
                array('name' => '换货维修', 'act'=>'return_list', 'op'=>'Service'),
                array('name' => '售后退货', 'act'=>'refund_list', 'op'=>'Service'),
                array('name' => '商品评论','act'=>'index','op'=>'Comment'),
                array('name' => '商品咨询','act'=>'ask_list','op'=>'Comment'),
                array('name' => '投诉管理','act'=>'complain_list', 'op'=>'Service'),
                array('name' => '举报管理','act'=>'expose_list', 'op'=>'Service'),
            )),
            array('name' => '配送服务','child'=>array(
                array('name' => '上门自提','act'=>'index','op'=>'ShopOrder'),
            )),
        )),

    	'marketing'=>array('name'=>'营销','child'=>array(
            array('name' => '常用促销','child' => array(
                array('name' => '营销菜单', 'act'=>'index_list', 'op'=>'Promotion'),
                array('name' => '抢购管理', 'act'=>'flash_sale', 'op'=>'Promotion'),
                array('name' => '团购管理', 'act'=>'group_buy_list', 'op'=>'Promotion'),
                array('name' => '优惠促销', 'act'=>'prom_goods_list', 'op'=>'Promotion'),
                array('name' => '订单促销', 'act'=>'prom_order_list', 'op'=>'Promotion'),
                array('name' => '预售管理','act'=>'index', 'op'=>'PreSell'),
                array('name' => '砍价管理','act'=>'bargain_list', 'op'=>'Promotion'),
            )),
            array('name' => '拼团购','child' => array(
                array('name' => '分享团','act'=>'index', 'op'=>'Team'),
                array('name' => '佣金团','act'=>'index', 'op'=>'Team'),
                array('name' => '抽奖团','act'=>'index', 'op'=>'Team'),
            )),
            array('name' => '优惠券','child' => array(
                array('name' => '优惠券','act'=>'index', 'op'=>'Coupon'),
                array('name'=>'新人优惠券','act'=>'noob','op'=>'Coupon'),

            )),
    //			array('name' => '互动营销','child' => array(   功能未开发，暂时隐藏
    //                array('name' => '砸金蛋','act'=>'', 'op'=>''),
    //                array('name' => '猜单双','act'=>'', 'op'=>''),
    //                array('name' => '爱心助力','act'=>'', 'op'=>''),
    //                array('name' => '拆红包','act'=>'', 'op'=>''),
    //                array('name' => '刮刮奖','act'=>'', 'op'=>''),
    //                array('name' => '大转盘抽奖','act'=>'', 'op'=>''),
    //            )),
    	)),

    'distribution'=>array('name'=>'分销','child'=>array(
        array('name' => '分销管理','child' => array(
            array('name' => '分销商品', 'act'=>'goods_list', 'op'=>'Distribut'),
            array('name' => '分销商列表', 'act'=>'distributor_list', 'op'=>'Distribut'),
            array('name' => '分销关系', 'act'=>'tree', 'op'=>'Distribut'),
            array('name' => '分销设置', 'act'=>'setting', 'op'=>'Distribut'),
            array('name' => '分销商等级', 'act'=>'grade_list', 'op'=>'Distribut'),
        )),
        array('name' => '佣金管理','child' => array(
            array('name' => '分成日志', 'act'=>'rebate_log', 'op'=>'Distribut'),
        )),
    )),

    'data'=>array('name'=>'统计','child'=>array(
        array('name' => '交易数据','child' => array(
            array('name' => '销售概况', 'act'=>'index', 'op'=>'Report'),
            array('name' => '销售排行', 'act'=>'saleTop', 'op'=>'Report'),
            array('name' => '销售明细', 'act'=>'saleList', 'op'=>'Report'),
        )),
        array('name' => '运营数据','child' => array(
            array('name' => '运营概览', 'act'=>'finance', 'op'=>'Report'),     //？
            array('name' => '商家提现申请', 'act'=>'store_withdrawals', 'op'=>'Finance'),
            array('name' => '商家转款列表', 'act'=>'store_remittance', 'op'=>'Finance'),
            array('name' => '商家结算记录', 'act'=>'order_statis', 'op'=>'Finance'),
            array('name' => '平台支出记录', 'act'=>'expense_log', 'op'=>'Finance'),
            //array('name' => '平台充值卡', 'act'=>'index', 'op'=>'Rechargecard'),
        )),
         array('name' => '会员数据','child' => array(
            array('name' => '会员统计', 'act'=>'user', 'op'=>'Report'),
            array('name' => '会员排行', 'act'=>'userTop', 'op'=>'Report'),
            array('name' => '会员提现申请', 'act'=>'withdrawals', 'op'=>'Finance'),
            array('name' => '会员转款列表', 'act'=>'remittance', 'op'=>'Finance'),
        )),
    )),

     'store'=>array('name'=>'商家','child'=>array(
           array('name' => '店铺管理','child' => array(
               array('name' => '店铺列表', 'act'=>'store_list', 'op'=>'Store'),
               array('name' => '自营店铺', 'act'=>'store_own_list', 'op'=>'Store'),
               array('name' => '店铺分类', 'act'=>'store_class', 'op'=>'Store'),
               array('name' => '店铺等级', 'act'=>'store_grade', 'op'=>'Store'),
               array('name' => '经营类目审核', 'act'=>'apply_class_list', 'op'=>'Store'),
               array('name' => '消费者保障服务', 'act'=>'index', 'op'=>'Guarantee'),
            )),
            array('name' => '店铺设置','child' => array(
                  array('name' => '二级域名', 'act'=>'domain_list', 'op'=>'Store'),
                  array('name' => '店铺帮助', 'act'=>'helpList', 'op'=>'Article'),
                  array('name' => '店铺满意度', 'act'=>'satisfaction', 'op'=>'Store'),
            )),
             array('name' => '店铺门店','child' => array(
                    array('name' => '店铺列表', 'act'=>'shop_list', 'op'=>'Store'),
            )),
      )),

    'mobile'=>array('name'=>'手机端','child'=>array(
        array('name' => '设置','child' => array(
            array('name' => '模板设置', 'act'=>'templateList', 'op'=>'Template'),
            array('name' => '手机支付', 'act'=>'templateList', 'op'=>'Template'),
            array('name' => '微信二维码', 'act'=>'templateList', 'op'=>'Template'),
            array('name' => '第三方登录', 'act'=>'templateList', 'op'=>'Template'),
            array('name' => '导航管理', 'act'=>'finance', 'op'=>'Report'),
            array('name' => '广告管理', 'act'=>'finance', 'op'=>'Report'),
            array('name' => '广告位管理', 'act'=>'finance', 'op'=>'Report'),
        )),
    )),
);