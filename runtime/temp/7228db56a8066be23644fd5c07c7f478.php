<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:37:"./template/pc/rainbow/user/index.html";i:1587634424;s:74:"/home/wwwroot/testshop.kingdeepos.com/template/pc/rainbow/user/header.html";i:1587634424;s:72:"/home/wwwroot/testshop.kingdeepos.com/template/pc/rainbow/user/menu.html";i:1587634424;s:74:"/home/wwwroot/testshop.kingdeepos.com/template/pc/rainbow/user/footer.html";i:1587634424;s:76:"/home/wwwroot/testshop.kingdeepos.com/template/pc/rainbow/public/footer.html";i:1587634420;}*/ ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>我的账户-<?php echo $tpshop_config['shop_info_store_title']; ?></title>
		<meta name="keywords" content="<?php echo $tpshop_config['shop_info_store_keyword']; ?>" />
		<meta name="description" content="<?php echo $tpshop_config['shop_info_store_desc']; ?>" />
        <link rel="shortcut  icon" type="image/x-icon" href="<?php echo (isset($tpshop_config['shop_info_store_ico']) && ($tpshop_config['shop_info_store_ico'] !== '')?$tpshop_config['shop_info_store_ico']:'/public/static/images/logo/storeico_default.png'); ?>" media="screen"/>
		<link rel="stylesheet" type="text/css" href="/template/pc/rainbow/static/css/tpshop.css" />
		<link rel="stylesheet" type="text/css" href="/template/pc/rainbow/static/css/myaccount.css" />
		<link rel="shortcut  icon" type="image/x-icon" href="<?php echo (isset($tpshop_config['shop_info_store_ico']) && ($tpshop_config['shop_info_store_ico'] !== '')?$tpshop_config['shop_info_store_ico']:'/public/static/images/logo/storeico_default.png'); ?>" media="screen"  />
		<script src="/template/pc/rainbow/static/js/jquery-1.11.3.min.js" type="text/javascript" charset="utf-8"></script>
	</head>
	<body class="bg-f5">
		<script src="/public/js/global.js" type="text/javascript"></script>
<link rel="stylesheet" href="/template/pc/rainbow/static/css/location.css" type="text/css"><!-- 收货地址，物流运费 -->
<script src="/public/static/js/layer/layer.js" type="text/javascript"></script>
<style>
	.list1 li{float:left;}
</style>
<div class="top-hander home-index-top p">
	<div class="w1224 pr">
		<div class="fl">
			<?php if(!(empty($user) || (($user instanceof \think\Collection || $user instanceof \think\Paginator ) && $user->isEmpty()))): ?>
			<div class="fl ler">
				<a href="<?php echo U('Home/User/index'); ?>"><?php echo $user['nickname']; ?></a>
			</div>
			<div class="fl ler">
				<a href="<?php echo U('Home/User/message_notice'); ?>">
					消息<?php if($user_message_count > 0): ?>（<span class="red"> <?php echo $user_message_count; ?> </span>）<?php endif; ?>
				</a>
			</div>
			<div class="fl ler">
				<a href="<?php echo U('Home/User/logout'); ?>">退出</a>
			</div>
			<?php else: ?>
			<div class="fl ler">
		        <a class="red" href="<?php echo U('Home/user/login'); ?>">登录</a>
		        <span class="spacer"></span>
		        <a href="<?php echo U('Home/user/reg'); ?>">注册</a>
		    </div>
			<?php endif; ?>
			<div class="fl spc"></div>
			<div class="sendaddress pr fl">
				<!-- 收货地址，物流运费 -start-->
				<ul class="list1">
					<li class="jaj"><span>配&nbsp;&nbsp;送：</span></li>
					<li class="summary-stock though-line" style="margin-top:2px">
						<div class="dd" style="border-right:0px;">
							<div class="store-selector add_cj_p">
								<div class="text" style="width: 150px;margin-top:2px;"><div></div><b></b></div>
								<div onclick="$(this).parent().removeClass('hover')" class="close"></div>
							</div>
						</div>
					</li>
				</ul>
				<!--<i class="jt-x"></i>-->
				<!-- 收货地址，物流运费 -end-->
				<!--<span>深圳<i class="jt-x"></i></span>-->
			</div>
		</div>
		<div class="top-ri-header fr">
			<ul>
				<li><a href="<?php echo U('Home/Order/order_list'); ?>">我的订单</a></li>
				<li class="spacer"></li>
				<li><a href="<?php echo U('Home/User/visit_log'); ?>">我的浏览</a></li>
				<li class="spacer"></li>
				<li><a href="<?php echo U('Home/User/goods_collect'); ?>">我的收藏</a></li>
				<li class="spacer"></li>
                <li class="hover-ba-navdh">
                    <div class="nav-dh">
                        <span>客户服务</span>
                        <i class="jt-x"></i>
                        <div class="conta-hv-nav">
                            <ul>
                                <li><a href="<?php echo U('Seller/Index/index'); ?>">商家后台</a></li>
                                <li><a href="<?php echo U('Home/Newjoin/index'); ?>">商家帮助</a></li>
                            </ul>
                        </div>
                    </div>
                </li>
				<li class="spacer"></li>
				<li class="hover-ba-navdh">
					<div class="nav-dh">
						<span>网站导航</span>
						<i class="jt-x"></i>
						<div class="conta-hv-nav">
							<ul>
								<li>
									<a href="<?php echo U('/Home/Activity/group_list'); ?>">团购</a>
								</li>
								<li>
									<a href="<?php echo U('Home/Activity/flash_sale_list'); ?>">抢购</a>
								</li>
							</ul>
						</div>
					</div>
				</li>
			</ul>
		</div>
	</div>
</div>
<div class="nav-middan-z p home-index-head">
	<div class="header w1224">
		<div class="ecsc-logo">
			<a href="/" class="logo">
                <img src="<?php echo (isset($tpshop_config['shop_info_store_user_logo']) && ($tpshop_config['shop_info_store_user_logo'] !== '')?$tpshop_config['shop_info_store_user_logo']:'/public/static/images/logo/pc_home_user_logo_default.png'); ?>" style="max-width: 194px;max-height: 70px;">
            </a>
		</div>
		<div class="m-index">
			<a href="<?php echo U('Home/User/index'); ?>" class="index">我的商城</a>
			<a href="/" class="home">返回商城首页</a>
		</div>
		<div class="m-navitems">
			<ul class="fixed p">
				<li><a href="<?php echo U('Home/Index/index'); ?>">首页</a></li>
				<li>
					<div class="u-dl">
						<div class="u-dt">
							<span>账户设置</span>
							<i></i>
						</div>
						<div class="u-dd">
							<a href="<?php echo U('Home/User/info'); ?>">个人信息</a>
							<a href="<?php echo U('Home/User/safety_settings'); ?>">安全设置</a>
							<a href="<?php echo U('Home/User/address_list'); ?>">收货地址</a>
						</div>
					</div>
				</li>
				<li class="u-msg"><a class="J-umsg" href="<?php echo U('Home/User/message_notice'); ?>">消息<span><?php if($user_message_count > 0): ?><?php echo $user_message_count; endif; ?></span></a></li>
				<li><a href="<?php echo U('Home/Goods/integralMall'); ?>">积分商城</a></li>
				<li class="search_li">
				   <form id="sourch_form" id="sourch_form" method="post" action="<?php echo U('Home/Goods/search'); ?>">
		           	<input class="search_usercenter_text" name="q" id="q" type="text" value="<?php echo \think\Request::instance()->param('q'); ?>"  />
		           	<a class="search_usercenter_btn" href="javascript:;" onclick="if($.trim($('#q').val()) != '') $('#sourch_form').submit();">搜索</a>
		           </form>
		        </li>
			</ul>
		</div>
		<div class="shopingcar-index fr">
			<div class="u-g-cart fr fixed" id="hd-my-cart">
				<a href="<?php echo U('Home/Cart/index'); ?>">
					<p class="c-n fl">我的购物车</p>

					<p class="c-num fl">(<span class="count cart_quantity" id="cart_quantity">0</span>)
						<i class="i-c oh"></i>
					</p>
				</a>

				<div class="u-fn-cart u-mn-cart" id="show_minicart">
					<div class="minicartContent" id="minicart">
					</div>
					<!--有商品时-e-->
				</div>
			</div>
		</div>
	</div>
</div>
<script src="/template/pc/rainbow/static/js/common.js"></script>
<!--------收货地址，物流运费-开始-------------->
<script src="/public/js/locationJson.js"></script>
<script src="/template/pc/rainbow/static/js/location.js"></script>
<script>doInitRegion();</script>
<!--------收货地址，物流运费--结束-------------->

		<div class="home-index-middle">
			<div class="w1224">
				<div class="g-crumbs">
			       	<a href="<?php echo U('Home/User/index'); ?>">我的商城</a>
			    </div>
			    <div class="home-main">
					<style>
.menu_check{
	color:#e23435  !important; font-weight:bold
}
</style>
<div class="le-menu fl">
	<div class="menu-ul">
		<ul>
			<li class="ma"><i class="account-acc1"></i>交易中心</li>
			<li><a <?php if(\think\Request::instance()->action() == 'order_list'): ?>class ="menu_check"<?php endif; ?> href="<?php echo U('Home/Order/order_list'); ?>">我的订单</a></li>
			<li><a <?php if(\think\Request::instance()->action() == 'virtual_list'): ?>class ="menu_check"<?php endif; ?> href="<?php echo U('Home/Virtual/virtual_list'); ?>">虚拟订单</a></li>
			<!--<li><a href="">我的预售</a></li>-->
			<li><a <?php if(\think\Request::instance()->action() == 'comment'): ?>class ="menu_check"<?php endif; ?> href="<?php echo U('Home/Order/comment'); ?>">我的评价</a></li>
		</ul>
		<ul>
			<li class="ma"><i class="account-acc2"></i>资产中心</li>
			<li><a <?php if(\think\Request::instance()->action() == 'coupon'): ?>class ="menu_check"<?php endif; ?> href="<?php echo U('Home/User/coupon'); ?>">我的优惠券</a></li>
			<li><a <?php if(\think\Request::instance()->action() == 'recharge'): ?>class ="menu_check"<?php endif; ?> href="<?php echo U('Home/User/recharge'); ?>">账户余额</a></li>
			<li><a <?php if(\think\Request::instance()->action() == 'account'): ?>class ="menu_check"<?php endif; ?> href="<?php echo U('Home/User/account'); ?>">我的积分</a></li>
		</ul>
		<ul>
			<li class="ma"><i class="account-acc3"></i>关注中心</li>
			<li><a <?php if(\think\Request::instance()->action() == 'goods_collect' or \think\Request::instance()->action() == 'store_collect'): ?>class ="menu_check"<?php endif; ?> href="<?php echo U('Home/User/goods_collect'); ?>">我的收藏</a></li>
			<!--<li><a href="">曾经购买</a></li>-->
			<li><a <?php if(\think\Request::instance()->action() == 'visit_log'): ?>class ="menu_check"<?php endif; ?> href="<?php echo U('Home/User/visit_log'); ?>">我的足迹</a></li>
		</ul>
		<ul>
			<li class="ma"><i class="account-acc4"></i>个人中心</li>
			<li><a <?php if(\think\Request::instance()->action() == 'info'): ?>class ="menu_check"<?php endif; ?> href="<?php echo U('Home/User/info'); ?>">个人信息</a></li>
			<li><a <?php if(\think\Request::instance()->action() == 'bind_auth'): ?>class ="menu_check"<?php endif; ?> href="<?php echo U('Home/User/bind_auth'); ?>">账号绑定</a></li>
			<li><a <?php if(\think\Request::instance()->action() == 'address_list'): ?>class ="menu_check"<?php endif; ?> href="<?php echo U('Home/User/address_list'); ?>">地址管理</a></li>
			<li><a <?php if(\think\Request::instance()->action() == 'safety_settings'): ?>class ="menu_check"<?php endif; ?> href="<?php echo U('Home/User/safety_settings'); ?>">安全设置</a></li>
		</ul>
		<ul>
			<li class="ma"><i class="account-acc5"></i>分销中心</li>
			<li><a <?php if(\think\Request::instance()->action() == 'lower_list'): ?>class ="menu_check"<?php endif; ?> href="<?php echo U('Home/Order/lower_list'); ?>">我的推广</a></li>
			<li><a <?php if(\think\Request::instance()->action() == 'income'): ?>class ="menu_check"<?php endif; ?> href="<?php echo U('Home/Order/income'); ?>">我的收益</a></li>
		</ul>
		<ul>
			<li class="ma"><i class="account-acc6"></i>客户服务</li>
			<!--<li><a href="">我的发票</a></li>-->
			<li><a <?php if(\think\Request::instance()->action() == 'return_goods_index'): ?>class ="menu_check"<?php endif; ?> href="<?php echo U('Home/Order/return_goods_index'); ?>">退款换货</a></li>
			<!--<li><a <?php if(\think\Request::instance()->action() == 'consult'): ?>class ="menu_check"<?php endif; ?> href="<?php echo U('Home/Order/consult'); ?>">购买咨询</a></li>-->
			<li><a <?php if(\think\Request::instance()->action() == 'dispute'): ?>class ="menu_check"<?php endif; ?> href="<?php echo U('Home/Order/dispute'); ?>">交易投诉</a></li>
			<li><a <?php if(\think\Request::instance()->action() == 'expose_list'): ?>class ="menu_check"<?php endif; ?> href="<?php echo U('Home/Order/expose_list'); ?>">违规举报</a></li>

			<li><a <?php if(\think\Request::instance()->action() == 'consult_list'): ?>class ="menu_check"<?php endif; ?> href="<?php echo U('Home/Order/consult_list'); ?>">咨询记录</a></li>
		</ul>
	</div>
</div>
			    	<div class="ri-menu fr">
			    		<div class="menu-ri-t p">
			    			<div class="mu-head fl">
			    				<img src="<?php echo (isset($user['head_pic']) && ($user['head_pic'] !== '')?$user['head_pic']:'/template/pc/rainbow/static/images/pers.png'); ?>"/>
			    			</div>
			    			<div class="mu-midd fl">
			    				<a class="mu-m-phone" href="<?php echo U('Home/User/index'); ?>"><?php echo $user['nickname']; ?></a>
			    				<a class="mu-m-vip"><?php echo $level[$user['level']]['level_name']; ?></a>
			    				<p>
			    					<span>账户安全：</span>
			    					<span class="tt-zd">
                                        <?php if(($user['mobile_validated'] == 0) or ($user['email_validated'] == 0)): ?>
                                            <i style="width: 30%;"></i>
                                        <?php endif; if(($user['mobile_validated'] == 1) and ($user['email_validated'] == 1) and ($user[paypwd] == null)): ?>
                                            <i style="width: 60%;"></i>
                                        <?php endif; if(($user['mobile_validated'] == 1) and ($user['email_validated'] == 1) and ($user[paypwd] != null)): ?>
                                            <i style="width: 90%;"></i>
                                        <?php endif; ?>
			    					</span>
                                    <?php if(($user['mobile_validated'] == 0) or ($user['email_validated'] == 0)): ?>
                                        <span class="c_ye">较低</span>
                                    <?php endif; if(($user['mobile_validated'] == 1) and ($user['email_validated'] == 1) and ($user[paypwd] == null)): ?>
                                        <span class="c_ye">一般</span>
                                    <?php endif; if(($user['mobile_validated'] == 1) and ($user['email_validated'] == 1) and ($user[paypwd] != null)): ?>
                                        <span class="c_ye">较高</span>
                                    <?php endif; ?>
			    					<!--<a class="c_bl" href="">提升</a>-->
			    				</p>
			    			</div>
			    			<div class="mu-afte fl">
			    				<ul class="mu-a1">
			    					<li class="" hidden>
			                           <a href="">
			                               <i class="icon-card"></i>
			                               <span>会员折扣</span>
			                               <em class="mu-unit">&nbsp;折</em>
			                               <em class="mu-num"><?php echo $user['discount']*10; ?></em>
			                               <i class="icon-ar"></i>
			                            </a>
			                        </li>
			                        <li class="">
			                            <a href="<?php echo U('Home/User/recharge'); ?>">
			                               <i class="icon-balance"></i>
			                               <span>账户余额</span>
			                               <em class="mu-unit">&nbsp;元</em>
			                               <em class="mu-num"><?php echo $user['user_money']; ?></em>
			                               <i class="icon-ar"></i>
			                            </a>
			                        </li>
                                    <li>  <!--下一个ul里拿上来的-->
                                        <a href="<?php echo U('Home/User/account'); ?>">
                                            <i class="icon-point"></i>
                                            <span>可用积分</span>
                                            <em class="mu-unit">&nbsp;分</em>
                                            <em class="mu-num"><?php echo $user['pay_points']; ?></em>
                                            <i class="icon-ar"></i>
                                        </a>
                                    </li>
			    				</ul>
			    				<ul class="mu-a2">
			    					<!--<li>
			                            <a href="<?php echo U('Home/User/account'); ?>">
			                               <i class="icon-point"></i>
			                               <span>可用积分</span>
			                               <em class="mu-unit">&nbsp;分</em>
			                               <em class="mu-num"><?php echo $user['pay_points']; ?></em>
			                               <i class="icon-ar"></i>
			                            </a>
			                        </li>-->
			                        <li>
			                            <a href="<?php echo U('Home/User/coupon'); ?>">
			                               <i class="icon-coupon"></i>
			                               <span>优惠券</span>
			                               <em class="mu-unit">&nbsp;张</em>
			                               <em class="mu-num"><?php echo $user['coupon_count']; ?></em>
			                               <i class="icon-ar"></i>
			                            </a>
			                        </li>
			    				</ul>
			    			</div>
			    		</div>
			    		<div class="order-list p">
			    			<div class="ddlb-ayh">
			    				<div class="ddlb-tit">
			                       <h1>我的订单</h1>
			                       <a class="u-view-all" href="<?php echo U('Home/Order/order_list'); ?>">查看全部订单</a>
			                       <i class="u-sep"></i>
			                       <!--<a class="u-view-pre" href="">预售单<span class="hide">(待付款 <em>0</em>)</span></a>-->
			    				</div>
								<?php if(empty($order) || (($order instanceof \think\Collection || $order instanceof \think\Paginator ) && $order->isEmpty())): ?>
									<div class="car-none-pl">
										<i class="account-acco1"></i>您最近没有待处理订单，<a href="/">快去逛逛吧~</a>
									</div>
								<?php else: ?>
								    <div class="order-alone-li">
                                    <table width="100%" border="" cellspacing="" cellpadding="">
                                        <tr class="time_or">
                                            <td colspan="6">
                                                <div class="fl_ttmm">
                                                    <span class="time-num">下单时间：<em class="num"><?php echo date('Y-m-d H:i:s',$order['add_time']); ?></em></span>
                                                    <span class="time-num">订单编号：<em class="num"><?php echo $order['order_sn']; ?></em></span>
                                                    <span class="time-num">卖家：<a href="tencent://message/?uin=<?php echo $order['store'][store_qq]; ?>&Site=<?php echo (isset($tpshop_config['shop_info_store_name']) && ($tpshop_config['shop_info_store_name'] !== '')?$tpshop_config['shop_info_store_name']:'TPshop商城'); ?>&Menu=yes"><em class="num"><?php echo $order['store'][store_name]; ?><i class="ear"></i></em></a></span>
                                                    <div class="paysoon">
                                                    <?php if($order['order_button'][pay_btn] == 1): ?>
                                                        <a class="ps_lj" href="<?php echo U('Home/Cart/cart4',array('order_id'=>$order[order_id])); ?>"  target="_blank">立即支付</a>
                                                    <?php endif; if($order['order_button'][receive_btn] == 1): ?>
                                                        <a class="ps_lj" href="javascript:;" onclick="order_confirm(<?php echo $order['order_id']; ?>);">收货确认</a>
                                                    <?php endif; if($order['order_button'][cancel_btn] == 1): ?>
                                                        <a class="consoorder" href="javascript:;" data-url="<?php echo U('Home/Order/refund_order',array('order_id'=>$order[order_id])); ?>" onClick="refund_order(this)" >取消订单</a>
                                                    <?php endif; ?>
                                                    </div>
                                                    <!--<div class="dele"></div>-->
                                                </div>
                                                <div class="fr_ttmm"></div>
                                            </td>
                                        </tr>
                                        <?php if(is_array($order[order_goods]) || $order[order_goods] instanceof \think\Collection || $order[order_goods] instanceof \think\Paginator): if( count($order[order_goods])==0 ) : echo "" ;else: foreach($order[order_goods] as $key=>$goods): ?>
                                            <tr class="conten_or">
                                            <td class="sx1">
                                                <div class="shop-if-dif">
                                                    <div class="shop-difimg">
                                                        <img style="width:100px;height:100px" src="<?php echo goods_thum_images($goods['goods_id'],100,100,$goods['item_id']); ?>"/>
                                                    </div>
                                                    <div class="shop_name"><a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$goods['goods_id'])); ?>"><?php echo $goods['goods_name']; ?></a></div>
                                                </div>
                                            </td>
                                            <td class="sx2"><span>￥</span><span><?php echo $goods['member_goods_price']; ?></span></td>
                                            <td class="sx3"><?php echo $goods['goods_num']; ?></td>
                                            <?php if($key == 0): ?>
                                                <td class="sx4" rowspan="<?php echo count($order['order_goods']); ?>">
                                                    <div class="pric_rhz">
                                                        <p class="d_pri"><span>￥</span><span><?php echo $order['total_amount']; ?></span></p>

                                                        <p class="d_yzo">
                                                            <span>含运费：</span>
                                                            <span><?php echo $order['shipping_price']; ?></span></p>
                                                        <p class="d_yzo"><a href="javascript:void(0);"><?php echo $order['pay_name']; ?></a></p>
                                                    </div>
                                                </td>
                                            <?php endif; ?>
                                            <td class="sx5">
                                                <div class="detail_or">
                                                    <p class="d_yzo">
                                                        <?php if($goods[is_comment] == 1): ?>
                                                            已完成
                                                            <?php elseif($goods[is_comment] != 1 and $order['shipping_status'] == 2): if($goods[is_send] == 0): ?>
                                                                待发货
                                                                <?php else: ?>
                                                                已发货
                                                            <?php endif; else: ?>
                                                            <?php echo $order['order_status_detail']; endif; ?>
                                                    </p>
                                                    <p><?php if($order['prom_type'] == 5): ?><a href="<?php echo U('Order/virtual_order',array('order_id'=>$order['order_id'])); ?>">查看详情</a>
                                                        <?php else: ?><a href="<?php echo U('Home/Order/order_detail',array('id'=>$order['order_id'])); ?>">查看详情</a><?php endif; ?></p>
                                                    <!--<p class="ps_r"><a href="javascript:void(0);">配送</a></p>-->
                                                </div>
                                            </td>
                                            <td class="sx6">
                                                <div class="rbac">
                                                    <?php if(($order['order_button'][return_btn] == 1) and ($goods[is_send] < 2)): ?>
                                                        <p><a href="<?php echo U('Home/Order/return_goods',array('rec_id'=>$goods['rec_id'])); ?>">退货/退款</a></p>
                                                    <?php endif; ?>
                                                    <p class=""><a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$goods['goods_id'])); ?>">再次购买</a></p>
                                                    <?php if(($order['order_button'][comment_btn] == 1) and ($goods[is_comment] == 0)): ?>
                                                        <p class="inspect"><a href="<?php echo U('Home/Order/comment'); ?>">评价</a></p>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                    </table>
								</div>
                                <?php endif; ?>
			    			</div>
			    		</div>
			    		<div class="order-list bgno p">
			    			<div class="ddlb-zy">
			    				<div class="coll-coupon fl">
			    					<div class="coll-etl">
										<div class="ddlb-tit">
					                       <h1>我的收藏</h1>
					                       <a class="cx-fk J-childCollect" href="<?php echo U('Home/User/goods_collect'); ?>"></a>
					                       <a class="u-view-all" href="<?php echo U('Home/User/goods_collect'); ?>">查看更多</a>
					    				</div>
					    				<div class="shop-sc-t">
					    					<ul>
												<?php if(is_array($collect_result) || $collect_result instanceof \think\Collection || $collect_result instanceof \think\Paginator): if( count($collect_result)==0 ) : echo "" ;else: foreach($collect_result as $key=>$v): ?>
													<li>
														<a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$v[goods_id])); ?>">
															<img src="<?php echo goods_thum_images($v['goods_id'],80,80); ?>"/>
															<p><em>￥</em><em><?php echo $v['shop_price']; ?></em></p>
														</a>
													</li>
												<?php endforeach; endif; else: echo "" ;endif; ?>
					    					</ul>
					    				</div>
			    					</div>
			    				</div>
			    				<div class="coll-coupon malrh fl">
			    					<div class="coupon-etl">
			    						<div class="ddlb-tit">
					                       <h1>我的优惠券</h1>
					                       <a class="u-view-all" href="<?php echo U('Home/User/coupon'); ?>">查看更多</a>
					    				</div>
					    				<div class="shop-sc-t">
											<?php if(is_array($coupon_list) || $coupon_list instanceof \think\Collection || $coupon_list instanceof \think\Paginator): if( count($coupon_list)==0 ) : echo "" ;else: foreach($coupon_list as $key=>$v): ?>
												<div class="coupon-bgimg">
													<a href="<?php echo U('Home/store/index',array('store_id'=>$v[store_id])); ?>">
														<div class="cp-jal">
															<h1><em class="li-fh">￥</em><em><?php echo ceil($v['money']); ?></em></h1>
															<span>满<?php echo ceil($v['condition']); ?>减<?php echo ceil($v['money']); ?></span>
														</div>
														<div class="cp-jay">
															<span>立即使用</span>
														</div>
													</a>
												</div>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
					    				</div>
			    					</div>
			    				</div>
			    			</div>
			    		</div>
			    	</div>
			    </div>
			</div>
		</div>
		<!--footer-s-->
<div class="footer p">
    <div class="mod_service_inner">
        <div class="w1224">
            <ul>
                <li>
                    <div class="mod_service_unit">
                        <h5 class="mod_service_duo">多</h5>
                        <p>品类齐全，轻松购物</p>
                    </div>
                </li>
                <li>
                    <div class="mod_service_unit">
                        <h5 class="mod_service_kuai">快</h5>
                        <p>多仓直发，极速配送</p>
                    </div>
                </li>
                <li>
                    <div class="mod_service_unit">
                        <h5 class="mod_service_hao">好</h5>
                        <p>正品行货，精致服务</p>
                    </div>
                </li>
                <li>
                    <div class="mod_service_unit">
                        <h5 class="mod_service_sheng">省</h5>
                        <p>天天低价，畅选无忧</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="w1224">
        <div class="footer-ewmcode">
		    <div class="foot-list-fl">
                <div class="foot-list-wrap p">
                    <?php
                                   
                                $md5_key = md5("select * from `__PREFIX__article_cat`  where cat_type = 1  order by cat_id asc limit 5  ");
                                $result_name = $sql_result_v = S("sql_".$md5_key);
                                if(empty($sql_result_v))
                                {                            
                                    $result_name = $sql_result_v = \think\Db::query("select * from `__PREFIX__article_cat`  where cat_type = 1  order by cat_id asc limit 5  "); 
                                    S("sql_".$md5_key,$sql_result_v,31104000);
                                }    
                              foreach($sql_result_v as $k=>$v): ?>
                        <ul>
                            <li class="foot-th">
                                <?php echo $v[cat_name]; ?>
                            </li>
                            <?php
                                   
                                $md5_key = md5("select * from `__PREFIX__article` where cat_id = $v[cat_id]  and is_open=1 limit 5 ");
                                $result_name = $sql_result_v2 = S("sql_".$md5_key);
                                if(empty($sql_result_v2))
                                {                            
                                    $result_name = $sql_result_v2 = \think\Db::query("select * from `__PREFIX__article` where cat_id = $v[cat_id]  and is_open=1 limit 5 "); 
                                    S("sql_".$md5_key,$sql_result_v2,31104000);
                                }    
                              foreach($sql_result_v2 as $k2=>$v2): ?>
                                <li>
                                    <a href="<?php echo U('Home/Article/detail',array('article_id'=>$v2[article_id])); ?>"><?php echo $v2[title]; ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endforeach; ?>
                </div>
		        <div class="friendship-links p">
                    <span>友情链接 : </span>
                    <div class="links-wrap-h p">
                    <?php
                                   
                                $md5_key = md5("select * from `__PREFIX__friend_link` where is_show=1");
                                $result_name = $sql_result_v = S("sql_".$md5_key);
                                if(empty($sql_result_v))
                                {                            
                                    $result_name = $sql_result_v = \think\Db::query("select * from `__PREFIX__friend_link` where is_show=1"); 
                                    S("sql_".$md5_key,$sql_result_v,31104000);
                                }    
                              foreach($sql_result_v as $k=>$v): ?>
                   	 	 <a href="<?php echo $v[link_url]; ?>" <?php if($v['target'] == 1): ?>target="_blank"<?php endif; ?> ><?php echo $v[link_name]; ?></a>
                    <?php endforeach; ?>
                    </div>
                </div>
		    </div>
			<div class="right-contact-us">
				<h3 class="title">客服热线（9:00-22:00）</h3>
				<span class="phone"><?php echo $tpshop_config['shop_info_phone']; ?></span>
				<p class="tips">官方微信</p>
				<div class="qr-code-list clearfix">
					<!--<a class="qr-code" href="javascript:;"><img src="/template/pc/rainbow/static/images/qrcode.png" alt="" /></a>-->
					<a class="qr-code qr-code-tpshop" href="javascript:;">
						<img src="<?php echo (isset($tpshop_config['shop_info_weixin_qrcode']) && ($tpshop_config['shop_info_weixin_qrcode'] !== '')?$tpshop_config['shop_info_weixin_qrcode']:'/template/pc/rainbow/static/images/qrcode.png'); ?>" alt="" />
					</a>
				</div>
			</div>
		    <!--<div class="QRcode-fr">
		        <ul>
		            <li class="foot-th">客户端</li>
		            <li><img src="/template/pc/rainbow/static/images/qrcode.png"/></li>
		        </ul>
		        <ul>
		            <li class="foot-th">微信</li>
		            <li><img src="/template/pc/rainbow/static/images/qrcode.png"/></li>
		        </ul>
		    </div>-->
		</div>
		<div class="mod_copyright p">
		    <div class="grid-top">
                <?php
                                   
                                $md5_key = md5("SELECT * FROM `__PREFIX__navigation` where is_show = 1 AND position = 2 ORDER BY `sort` DESC");
                                $result_name = $sql_result_vv = S("sql_".$md5_key);
                                if(empty($sql_result_vv))
                                {                            
                                    $result_name = $sql_result_vv = \think\Db::query("SELECT * FROM `__PREFIX__navigation` where is_show = 1 AND position = 2 ORDER BY `sort` DESC"); 
                                    S("sql_".$md5_key,$sql_result_vv,31104000);
                                }    
                              foreach($sql_result_vv as $kk=>$vv): ?>
                    <a href="<?php echo $vv[url]; ?>" <?php if($vv[is_new] == 1): ?> target="_blank" <?php endif; ?> ><?php echo $vv[name]; ?></a><span>|</span>
                <?php endforeach; ?>
		    </div>
		    <p>Copyright © 2016-2025 <?php echo (isset($tpshop_config['shop_info_store_name']) && ($tpshop_config['shop_info_store_name'] !== '')?$tpshop_config['shop_info_store_name']:'TPshop商城'); ?> 版权所有 保留一切权利 备案号:<a href="http://www.beian.miit.gov.cn" ><?php echo $tpshop_config['shop_info_record_no']; ?></a></p>
		    <p class="mod_copyright_auth">
		        <a class="mod_copyright_auth_ico mod_copyright_auth_ico_1" href="" target="_blank">经营性网站备案中心</a>
		        <a class="mod_copyright_auth_ico mod_copyright_auth_ico_2" href="" target="_blank">可信网站信用评估</a>
		        <a class="mod_copyright_auth_ico mod_copyright_auth_ico_3" href="" target="_blank">网络警察提醒你</a>
		        <a class="mod_copyright_auth_ico mod_copyright_auth_ico_4" href="" target="_blank">诚信网站</a>
		        <a class="mod_copyright_auth_ico mod_copyright_auth_ico_5" href="" target="_blank">中国互联网举报中心</a>
		    </p>
		</div>
    </div>
</div>
<script>
    // 延时加载二维码图片
    jQuery(function($) {
        $('img[img-url]').each(function() {
            var _this = $(this),
                    url = _this.attr('img-url');
            _this.attr('src',url);
        });
    });
</script>
<!--footer-e-->
<!--侧边栏-s-->
<div class="slidebar_alo">
	<ul>
		<li class="re_cuso"><a target="_blank" href="" >客服服务</a></li>
		<li class="re_wechat">
			<a target="_blank" href="" >微信关注</a>
			<div class="rtipscont" style=""> 
				<span class="arrowr-bg"></span> 
				<span class="arrowr"></span> 
				<img src="/template/pc/rainbow/static/images/qrcode.png" /> 
				<p class="tiptext">扫码关注官方微信<br>先人一步知晓促销活动</p>
			</div>
		</li>
		<li class="re_phone">
			<a target="_blank" href="" >手机商城</a>
			<div class="rtipscont rstoretips" style=""> 
				<span class="arrowr-bg"></span> 
				<span class="arrowr"></span> 
				<img img-url="/index.php?m=Home&c=Index&a=qr_code&data=<?php echo $mobile_url; ?>&head_pic=<?php echo $head_pic; ?>&back_img=<?php echo $back_img; ?>" />
				<!--<p class="tiptext">扫码关注官方微信<br>先人一步知晓促销活动</p>-->
			</div>
		</li>
		<li class="re_top"><a target="_blank" href="javascript:void(0);" >回到顶部</a></li>
	</ul>
</div>
<!--侧边栏-e-->
<script>
    //用户中心统一确认提示框
    function verConfirm(msg , callback){
        layer.confirm(msg, {
                btn: ['确定','取消'] //按钮
            }, function(){
                location.href=callback;
            }
        );
    }
    //显示密码安全等级
    function securityLevel(sValue) {
        var modes = 0;
        //正则表达式验证符合要求的
        if (sValue.length < 6 ) return modes;
        if (/\d/.test(sValue)) modes++; //数字
        if (/[a-z]/.test(sValue)) modes++; //小写
        if (/[A-Z]/.test(sValue)) modes++; //大写
        if (/\W/.test(sValue)) modes++; //特殊字符
        $('.lowzg').eq(modes-1).addClass('red').siblings('.lowzg').removeClass('red');
    };
//侧边栏 (单首页)
$(function(){
	//鼠标滑过二维码显示隐藏
	$('.slidebar_alo li').hover(function(){
		$(this).find('.rtipscont').animate({
			opacity:"1",
			left:"-182px"
		})
	},function(){
		$(this).find('.rtipscont').animate({
			opacity:"0",
			left:"0px"
		})
	})
	$(".slidebar_alo .re_top").click(function () {
		//回到顶部
	    var speed=300;//滑动的速度
	    $('body,html').animate({ scrollTop: 0 }, speed);
	    return false;
	});
	//回到顶部显示隐藏
	$(window).scroll(function ()
	{
		var st = $(this).scrollTop();
		if(st == 0){
			$('.re_top').hide(300)
		}else{
			$('.re_top').show(300)
		}
	});
});
</script>
<script>
	// 延时加载二维码图片
	jQuery(function($) {
		$('img[img-url]').each(function() {
			var _this = $(this),
					url = _this.attr('img-url');
			_this.attr('src',url);
		});
	});
</script>
	</body>
	<script type="text/javascript">
    /**
     * 确认收货
     * @param orderId
     */
    function order_confirm(orderId)
    {
        layer.confirm('你确定收到货了吗?', {
                    btn: ['是','否']
                }, function(){
                    $.ajax({
                        url:"<?php echo U('Order/order_confirm'); ?>",
                        type:'POST',
                        dataType:'JSON',
                        data:{order_id:orderId},
                        success:function(data){
                            if(data.status == 1){
                                layer.alert(data.msg, {icon: 1});
                                location.href ='/index.php?m=home&c=Order&a=order_detail&id='+orderId;
                            }else{
                                layer.alert(data.msg, {icon: 2});
                                location.href ='/index.php?m=home&c=Order&a=order_list&type=<?php echo \think\Request::instance()->param('type'); ?>&p=<?php echo \think\Request::instance()->param('p'); ?>';
                            }
                        },
                        error : function() {
                            layer.alert('网络失败，请刷新页面后重试', {icon: 2});
                        }
                    })
                }, function(tmp){
                    layer.close(tmp);
                }
        );
    }

    function refund_order(obj){
        layer.open({
            type: 2,
            title: '<b>订单取消申请</b>',
            skin: 'layui-layer-rim',
            shadeClose: true,
            shade: 0.5,
            area: ['600px', '500px'],
            content: $(obj).attr('data-url'),
        });
    }
	</script>
</html>