<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:38:"./template/pc/rainbow/index\index.html";i:1588741982;s:69:"D:\www\testshop.kingdeepos.com\template\pc\rainbow\public\header.html";i:1588741986;s:76:"D:\www\testshop.kingdeepos.com\template\pc\rainbow\public\header_search.html";i:1588741987;s:69:"D:\www\testshop.kingdeepos.com\template\pc\rainbow\public\footer.html";i:1588741986;s:75:"D:\www\testshop.kingdeepos.com\template\pc\rainbow\public\sidebar_cart.html";i:1588741987;}*/ ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>首页-<?php echo $seo['title']; ?></title>
        <meta name="keywords" content="<?php echo $seo['keywords']; ?>"/>
		<meta name="description" content="<?php echo $seo['description']; ?>"/>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<link rel="shortcut  icon" type="image/x-icon" href="<?php echo (isset($tpshop_config['shop_info_store_ico']) && ($tpshop_config['shop_info_store_ico'] !== '')?$tpshop_config['shop_info_store_ico']:'/public/static/images/logo/storeico_default.png'); ?>" media="screen"/>
		<link rel="stylesheet" type="text/css" href="/template/pc/rainbow/static/css/tpshop.css"/>
		<link rel="stylesheet" type="text/css" href="/template/pc/rainbow/static/css/swiper.min.css"/>
		<script src="/template/pc/rainbow/static/js/jquery-1.11.3.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="/public/js/global.js"></script>
		<script src="/public/js/swiper.min.js"></script>
		<style>
			body {
				background-color: #f0f3ef !important;
			}
			.coupon:hover .swiper-button-next,.coupon:hover .swiper-button-prev{
				display:block;
			}
			.conditions:hover .swiper-button-next,.conditions:hover .swiper-button-prev{
				display:block;
			}
			.next100:hover .swiper-button-next,.next100:hover .swiper-button-prev{
				display:block;
			}
			.prev100:hover .swiper-button-next,.prev100:hover .swiper-button-prev{
				display:block;
			}
			.next100,.prev100{
				font-size: 0;
				width: 30px;
				height: 50px;
				background: #000;
				padding-top: 0;
				opacity: .2;
				display: none;
			}
			.prev100 span{
				display: inline-block;
				width: 12px;
				height: 12px;
				border-top: 2px solid #fff;
				border-left: 2px solid #fff;
				transform: rotate(-45deg);
				margin-top: 18px;
				margin-left: 10px;
			}
			.next100 span{
				display: inline-block;
				width: 12px;
				height: 12px;
				border-top: 2px solid #fff;
				border-left: 2px solid #fff;
				margin-top: 18px;
				margin-left: 5px;
				transform: rotate(135deg);
			 }
			.prev100{
				left: 0;
				z-index: 999;
			}
			.next100{
				left: 322px;
				z-index: 999;
			}
			.next101,.prev101{
				font-size: 0;
				width: 30px;
				height: 50px;
				background: #000;
				padding-top: 0;
				opacity: .2;
				display: none;
			}
			.prev101 span{
				display: inline-block;
				width: 12px;
				height: 12px;
				border-top: 2px solid #fff;
				border-left: 2px solid #fff;
				transform: rotate(-45deg);
				margin-top: 18px;
				margin-left: 10px;
			}
			.next101 span{
				display: inline-block;
				width: 12px;
				height: 12px;
				border-top: 2px solid #fff;
				border-left: 2px solid #fff;
				margin-top: 18px;
				margin-left: 5px;
				transform: rotate(135deg);
			}
			.prev101{
				left: 0;
				z-index: 999;
			}
			.next101{
				left: 322px;
				z-index: 999;
			}
			.categorys .subitems dl:nth-child(1) dd{
				border-top:none;
			}
			#cata-nav .item .item-left{
				/* opacity: 0.7; */
			}
		</style>
	</head>
	<body>
		<!--顶部广告-s-->
		<?php $pid =1;$ad_position = M("ad_position")->cache(true,TPSHOP_CACHE_TIME)->column("position_id,position_name,ad_width,ad_height","position_id");$result = M("ad")->where("pid=$pid  and enabled = 1 and start_time <= 1588744914 and end_time >= 1588744914 ")->order("orderby desc")->cache(true,TPSHOP_CACHE_TIME)->limit("1")->select();
if(is_array($ad_position) && !in_array($pid,array_keys($ad_position)) && $pid)
{
  M("ad_position")->insert(array(
         "position_id"=>$pid,
         "position_name"=>CONTROLLER_NAME."页面自动增加广告位 $pid ",
         "is_open"=>1,
         "position_desc"=>CONTROLLER_NAME."页面",
  ));
  delFile(RUNTIME_PATH); // 删除缓存
  \think\Cache::clear();  
}


$c = 1- count($result); //  如果要求数量 和实际数量不一样 并且编辑模式
if($c > 0 && I("get.edit_ad"))
{
    for($i = 0; $i < $c; $i++) // 还没有添加广告的时候
    {
      $result[] = array(
          "ad_code" => "/public/images/not_adv.jpg",
          "ad_link" => "/index.php?m=Admin&c=Ad&a=ad&pid=$pid",
          "title"   =>"暂无广告图片",
          "not_adv" => 1,
          "target" => 0,
      );  
    }
}
foreach($result as $key=>$v):       
    
    $v[position] = $ad_position[$v[pid]]; 
    if(I("get.edit_ad") && $v[not_adv] == 0 )
    {
        $v[style] = "filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity: 0.5"; // 广告半透明的样式
        $v[ad_link] = "/index.php?m=Admin&c=Ad&a=ad&act=edit&ad_id=$v[ad_id]";        
        $v[title] = $ad_position[$v[pid]][position_name]."===".$v[ad_name];
        $v[target] = 0;
    }
    ?>
			<div class="topic-banner" style="background-color:<?php echo (isset($v['bgcolor']) && ($v['bgcolor'] !== '')?$v['bgcolor']:gray); ?>;">
				<div class="w1224">
					<a href="<?php echo $v['ad_link']; ?><?php echo !empty($edit_ad)?'&suggestion=1224*82' : ''; ?>" <?php if($v['target'] == 1): ?>target="_blank"<?php endif; ?>>
						<img src="<?php echo $v[ad_code]; ?>"/>
					</a>
					<i onclick="$('.topic-banner').hide();"></i>
				</div>
			</div>
		<?php endforeach; ?>
		<!--顶部广告-e-->
		<!--header-s-->
		
<div class="tpshop-tm-hander p">
	<div class="top-hander p">
		<div class="w1224 pr">
			<link rel="stylesheet" href="/template/pc/rainbow/static/css/location.css" type="text/css"><!-- 收货地址，物流运费 -->
			<div class="fl">
				<div class="ls-dlzc fl nologin">
					<a href="<?php echo U('Home/user/login'); ?>">登录</a>
					<a class="red" href="<?php echo U('Home/user/reg'); ?>">注册</a>
				</div>
				<div class="ls-dlzc fl islogin">
					<a class="red userinfo" href="<?php echo U('Home/user/index'); ?>"></a>
					<a href="<?php echo U('Home/user/logout'); ?>">退出</a>
				</div>
				<div class="fl spc" style="margin-top:10px"></div>
				<div class="sendaddress pr fl">
					<?php if(strtolower(ACTION_NAME) != 'goodsinfo'): ?>
						<!-- 收货地址，物流运费 -start-->
						<ul class="list1" >
							<li class="jaj"><span>送货至：</span></li>
							<li class="summary-stock though-line" style="margin-top:-1px">
								<div class="dd" style="border-right:0px;">
									<div class="store-selector add_cj_p">
										<div class="text" style="margin-top:3px;border-left: 0 !important; cursor: pointer;"><div></div><b></b></div>
										<div onclick="$(this).parent().removeClass('hover')" class="close"></div>
									</div>
								</div>
							</li>
						</ul>
						<!--<i class="jt-x"></i>-->
						<!-- 收货地址，物流运费 -end-->
						<!--------收货地址，物流运费-开始-------------->
						<script src="/public/js/locationJson.js"></script>
						<script src="/template/pc/rainbow/static/js/location.js"></script>
						<script>doInitRegion();</script>
						<!--------收货地址，物流运费--结束-------------->
					<?php endif; ?>
				</div>
			</div>
			<div class="top-ri-header fr">
				<ul>
					<li><a target="_blank" href="<?php echo U('Home/Order/order_list'); ?>">我的订单</a></li>
					<li class="spacer"></li>
					<li><a target="_blank" href="<?php echo U('Home/User/account'); ?>">我的积分</a></li>
					<li class="spacer"></li>
					<li><a target="_blank" href="<?php echo U('Home/User/coupon'); ?>">我的优惠券</a></li>
					<li class="spacer"></li>
					<li><a target="_blank" href="<?php echo U('Home/User/goods_collect'); ?>">我的收藏</a></li>
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
					<li class="navoxth">
						<div class="nav-dh">
							<i class="fl ico"></i>
							<span class="moblie_right">手机<?php echo (isset($tpshop_config['shop_info_store_name']) && ($tpshop_config['shop_info_store_name'] !== '')?$tpshop_config['shop_info_store_name']:'TPshop商城'); ?></span>
							<i class="jt-x"></i>
						</div>
						<div class="sub-panel m-lst">
							<p>扫一扫，下载<?php echo (isset($tpshop_config['shop_info_store_name']) && ($tpshop_config['shop_info_store_name'] !== '')?$tpshop_config['shop_info_store_name']:'TPshop商城'); ?>客户端</p>
							<dl>
								<dt class="fl mr10"><a target="_blank" href="">
                                <img height="80" width="80" img-url="/index.php?m=Home&c=Index&a=qr_code&data=<?php echo $mobile_url; ?>&head_pic=<?php echo $head_pic; ?>&back_img=<?php echo $back_img; ?>" id="qcode_img"/>
                                </a></dt>
								<dd class="fl mb10"><a target="_blank" href="javscript:;" img-url="/public/images/919and.png" onclick="set_img(this)" id="qcode_img_and"><i class="andr"></i> Andiord</a></dd>
								<dd class="fl"><a target="_blank" href="javscript:;" img-url="/public/images/919ios.png" onclick="set_img(this)"><i class="iph"></i> iPhone</a></dd>
							</dl>
						</div>
					</li>
					<li class="spacer"></li>
					<!--<li class="wxbox-hover">
						<a target="_blank" href="">关注我们：</a>
						<img class="wechat-top" src="/template/pc/rainbow/static/images/wechat.png" alt="">
						<div class="sub-panel wx-box">
							<span class="arrow-b">◆</span>
							<span class="arrow-a">◆</span>
							<p class="n"> 扫描二维码 <br>  关注<?php echo (isset($tpshop_config['shop_info_store_name']) && ($tpshop_config['shop_info_store_name'] !== '')?$tpshop_config['shop_info_store_name']:'TPshop商城'); ?>官方微信 </p>
							<img src="/template/pc/rainbow/static/images/qrcode_vmall_app01.png">
						</div>
					</li>-->
					<script>
						function set_img(obj){
							var img = $(obj).attr('img-url');
							$('#qcode_img').attr('src',img)
						}
						$(function(){
							var img = $('#qcode_img_and').attr('img-url');
							$('#qcode_img').attr('src',img)
						})
					</script>
				</ul>
			</div>
		</div>
	</div>
	<div class="nav-middan-z tphsop2_0 p">
		<div class="header w1224">
			<div class="ecsc-logo">
	<a href="/" class="logo">
        <img src="<?php echo (isset($tpshop_config['shop_info_store_logo']) && ($tpshop_config['shop_info_store_logo'] !== '')?$tpshop_config['shop_info_store_logo']:'/public/static/images/logo/pc_home_logo_default.png'); ?>" style="max-width: 240px;max-height: 80px;">
    </a>
</div>
<div class="ecsc-search">
	<form id="sourch_form" name="sourch_form" method="get" action="<?php echo U('Home/Goods/search'); ?>" class="ecsc-search-form">
		<div class="search-select-h">
			<span><em>商品</em><i class="jt-x"></i></span>
			<ul id="select-h">
				<li rel="<?php echo U('Home/Goods/search'); ?>">商品</li>
				<li rel="<?php echo U('Home/Index/street'); ?>">店铺</li>
				<!--<li>服务</li>-->
			</ul>
			<script>
				var select = $('#select-h');
				$('.search-select-h').mouseenter(function(){
					select.show();
				});
				$('.search-select-h').mouseleave(function(){
					select.hide();
				});
				select.find('li').click(function() {
					select.hide();
					$('#sourch_form').attr('action',$(this).attr("rel"));
					$('.search-select-h').find('em').text($(this).text());
				});
				<?php if($action == 'street'): ?>
					$('.search-select-h').find('em').text("店铺");
					$('#sourch_form').attr('action',"<?php echo U('Home/Index/street'); ?>");
				<?php else: ?>
					$('.search-select-h').find('em').text("商品");
					$('#sourch_form').attr('action',"<?php echo U('Home/Goods/search'); ?>");
				<?php endif; ?>
			</script>
		</div>
		<input autocomplete="off" name="q" id="q" type="text" value="<?php echo \think\Request::instance()->param('q'); ?>" placeholder="搜索关键字" class="ecsc-search-input">
		<button type="button" class="ecsc-search-button" >搜索 </button>
		<div class="candidate p">
			<ul id="search_list"></ul>
		</div>
		<script type="text/javascript">

            $('.ecsc-search-button').on('click',function(){
                if($.trim($('#q').val()) != ''){
                    $('#sourch_form').submit();
                }else{
                    $('#q').css('background-color','#F6D4CB');
                    $('#q').attr('placeholder','请输入关键字');
                }
            })
			;(function($){
				$.fn.extend({
					donetyping: function(callback,timeout){
						timeout = timeout || 1e3;
						var timeoutReference,
								doneTyping = function(el){
									if (!timeoutReference) return;
									timeoutReference = null;
									callback.call(el);
								};
						return this.each(function(i,el){
							var $el = $(el);
							$el.is(':input') && $el.on('keyup keypress',function(e){
								if (e.type=='keyup' && e.keyCode!=8) return;
								if (timeoutReference) clearTimeout(timeoutReference);
								timeoutReference = setTimeout(function(){
									doneTyping(el);
								}, timeout);
							}).on('blur',function(){
								doneTyping(el);
							});
						});
					}
				});
			})(jQuery);

			$('.ecsc-search-input').donetyping(function(){
				search_key();
			},500).focus(function(){
				var search_key = $.trim($('#q').val());
				if(search_key != ''){
					$('.candidate').show();
				}
			});
			$('.candidate').mouseleave(function(){
				$(this).hide();
			});

			function searchWord(words){
				$('#q').val(words);
				$('#sourch_form').submit();
			}
			function search_key(){
				var search_key = $.trim($('#q').val());
				if(search_key != ''){
					$.ajax({
						type:'post',
						dataType:'json',
						data: {key: search_key},
						url:"<?php echo U('Home/Api/searchKey'); ?>",
						success:function(data){
							if(data.status == 1){
								var html = '';
								$.each(data.result, function (n, value) {
									html += '<li onclick="searchWord(\''+value.keywords+'\');"><div class="search-item">'+value.keywords+'</div><div class="search-count">约'+value.goods_num+'个商品</div></li>';
								});
//								html += '<li class="close"><div class="search-count">关闭</div></li>';
								$('#search_list').empty().append(html);
								$('.candidate').show();
							}else{
								$('#search_list').empty();
							}
						}
					});
				}
			}
		</script>
	</form>
	<div class="keyword">
		<ul>
			<?php if(is_array($tpshop_config['hot_keywords']) || $tpshop_config['hot_keywords'] instanceof \think\Collection || $tpshop_config['hot_keywords'] instanceof \think\Paginator): if( count($tpshop_config['hot_keywords'])==0 ) : echo "" ;else: foreach($tpshop_config['hot_keywords'] as $k=>$wd): ?>
				<li>
					<a href="<?php echo U('Home/Goods/search',array('q'=>$wd)); ?>" target="_blank"><?php echo $wd; ?></a>
				</li>
			<?php endforeach; endif; else: echo "" ;endif; ?>
		</ul>
	</div>
</div>
<div class="shopingcar-index fr">
	<div class="u-g-cart fr fixed" id="hd-my-cart">
		<a href="<?php echo U('Home/Cart/index'); ?>">
			<p class="c-num">
				<i class="car2_0"></i>
				<span>我的购物车</span>
				<span class="count cart_quantity" id="cart_quantity"></span>
			</p>
		</a>
		<div class="u-fn-cart u-mn-cart" id="show_minicart">
			<div class="minicartContent" id="minicart">
			</div>
		</div>
	</div>
</div>
		</div>
	</div>
	<div class="nav tpshop2_0_nav p">
		<div class="w1224 p">
			<div class="categorys home_categorys">
				<div class="dt">
					<img src="/template/pc/rainbow/static/images/nav_new.png" alt="">
					<a href="<?php echo U('Home/Goods/goodsList'); ?>" target="_blank">全部商品分类</a>
				</div>
				<!--全部商品分类-s-->
				<div class="dd">
					<div class="cata-nav" id="cata-nav">
						<?php if(is_array($goods_category_tree) || $goods_category_tree instanceof \think\Collection || $goods_category_tree instanceof \think\Paginator): $k = 0; $__LIST__ = $goods_category_tree;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?>
							<div class="item fore1">
								<div class="item-left">
									<div class="cata-nav-name">
										<a href="<?php echo U('Home/Goods/goodsList',array('id'=>$vo[id])); ?>" title="<?php echo $vo['name']; ?>"><?php echo $vo['name']; ?></a>

										<?php if(is_array($vo['tmenu']) || $vo['tmenu'] instanceof \think\Collection || $vo['tmenu'] instanceof \think\Paginator): if( count($vo['tmenu'])==0 ) : echo "" ;else: foreach($vo['tmenu'] as $key=>$tm): ?>
											<a href="<?php echo U('Home/Goods/goodsList',array('id'=>$tm[id])); ?>" target="_blank"><em></em><?php echo $tm['name']; ?></a>
										<?php endforeach; endif; else: echo "" ;endif; ?>
									</div>
								</div>
								<div class="cata-nav-layer">
									<div class="cata-nav-left">
										<div class="item-channels">
											<div class="channels">
												<?php if(is_array($vo['hmenu']) || $vo['hmenu'] instanceof \think\Collection || $vo['hmenu'] instanceof \think\Paginator): if( count($vo['hmenu'])==0 ) : echo "" ;else: foreach($vo['hmenu'] as $key=>$hm): ?>
													<a href="<?php echo U('Home/Goods/goodsList',array('id'=>$hm[id])); ?>" target="_blank"><?php echo $hm['name']; ?><i>&gt;</i></a>
												<?php endforeach; endif; else: echo "" ;endif; ?>
											</div>
										</div>
										<div class="subitems">
											<?php if(is_array($vo['tmenu']) || $vo['tmenu'] instanceof \think\Collection || $vo['tmenu'] instanceof \think\Paginator): if( count($vo['tmenu'])==0 ) : echo "" ;else: foreach($vo['tmenu'] as $k2=>$v2): ?>
											<dl>
												<dt><a href="<?php echo U('Home/Goods/goodsList',array('id'=>$v2[id])); ?>" target="_blank"><?php echo $v2['name']; ?><i>&gt;</i></a></dt>
												<?php if(!(empty($v2['sub_menu']) || (($v2['sub_menu'] instanceof \think\Collection || $v2['sub_menu'] instanceof \think\Paginator ) && $v2['sub_menu']->isEmpty()))): ?>
													<dd>
														<?php if(is_array($v2['sub_menu']) || $v2['sub_menu'] instanceof \think\Collection || $v2['sub_menu'] instanceof \think\Paginator): if( count($v2['sub_menu'])==0 ) : echo "" ;else: foreach($v2['sub_menu'] as $key=>$v3): ?>
															<a href="<?php echo U('Home/Goods/goodsList',array('id'=>$v3[id])); ?>" target="_blank"><?php echo $v3['name']; ?></a>
														<?php endforeach; endif; else: echo "" ;endif; ?>
													</dd>
												<?php endif; ?>
											</dl>
											<?php endforeach; endif; else: echo "" ;endif; ?>
											<div class="item-brands">
												<ul>
												</ul>
											</div>
											<div class="item-promotions">
											</div>
										</div>
									</div>
									<div class="cata-nav-rigth">
										<div class="item-brands">
											<ul>
												<?php if(is_array($brand_list) || $brand_list instanceof \think\Collection || $brand_list instanceof \think\Paginator): $i = 0; $__LIST__ = $brand_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v2): $mod = ($i % 2 );++$i;if(($v2[cat_id1] == $vo[id]) AND ($v2[is_hot] == 1)): ?>
														<li>
															<a href="<?php echo U('Home/Goods/goodsList',array('brand_id'=>$v2[id])); ?>" target="_blank" title="<?php echo $v2['name']; ?>">
																<img src="<?php echo $v2['logo']; ?>" width="91" height="40">
															</a>
														</li>
													<?php endif; endforeach; endif; else: echo "" ;endif; ?>
											</ul>
										</div>
										<div class="item-promotions">
											<?php
                                   
                                $md5_key = md5("select * from __PREFIX__goods g inner join __PREFIX__flash_sale as f on g.goods_id = f.goods_id where start_time < $template_now_time and end_time > $template_now_time and status = 1 and cat_id1 = $vo[id] limit 2");
                                $result_name = $sql_result_promote = S("sql_".$md5_key);
                                if(empty($sql_result_promote))
                                {                            
                                    $result_name = $sql_result_promote = \think\Db::query("select * from __PREFIX__goods g inner join __PREFIX__flash_sale as f on g.goods_id = f.goods_id where start_time < $template_now_time and end_time > $template_now_time and status = 1 and cat_id1 = $vo[id] limit 2"); 
                                    S("sql_".$md5_key,$sql_result_promote,31104000);
                                }    
                              foreach($sql_result_promote as $promote_key=>$promote): ?>
												<a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$promote[goods_id])); ?>" target="_blank">
													<img width="181" height="120" src="<?php echo goods_thum_images($promote['goods_id'],181,120); ?>">
												</a>
											<?php endforeach; ?>
										</div>
									</div>
								</div>
							</div>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</div>
					<script>
						$('#cata-nav').find('.item').hover(function(){
							$(this).addClass('item-left-active').siblings().removeClass('item-left-active');
						},function(){
							$(this).removeClass('item-left-active');
						})
					</script>
				</div>
				<!--全部商品分类-e-->
			</div>
			<div class="navitems" id="nav">
				<ul>
					<li>
						<a href="/" <?php if(CONTROLLER_NAME == 'Index' AND ACTION_NAME == 'index'): ?>class="selected"<?php endif; ?>>首页</a>
					</li>
					<?php
                                   
                                $md5_key = md5("SELECT * FROM `__PREFIX__navigation` where is_show = 1 AND position = 1 ORDER BY `sort` DESC");
                                $result_name = $sql_result_v = S("sql_".$md5_key);
                                if(empty($sql_result_v))
                                {                            
                                    $result_name = $sql_result_v = \think\Db::query("SELECT * FROM `__PREFIX__navigation` where is_show = 1 AND position = 1 ORDER BY `sort` DESC"); 
                                    S("sql_".$md5_key,$sql_result_v,31104000);
                                }    
                              foreach($sql_result_v as $k=>$v): ?>
						<li>
                            <a href="<?php echo $v[url]; ?>" <?php  if($_SERVER['REQUEST_URI']==str_replace('&amp;','&',$v[url])){ echo "class='selected'";} ?> ><?php echo $v[name]; ?></a>
                        </li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
</div>

		<!--header-e-->

		<div id="myCarousel" class="carousel slide p header-tp tpshop2_0_carousel">
			<ol class="carousel-indicators"></ol>
			<div class="carousel-inner">
				<?php $pid =10;$ad_position = M("ad_position")->cache(true,TPSHOP_CACHE_TIME)->column("position_id,position_name,ad_width,ad_height","position_id");$result = M("ad")->where("pid=$pid  and enabled = 1 and start_time <= 1588744914 and end_time >= 1588744914 ")->order("orderby desc")->cache(true,TPSHOP_CACHE_TIME)->limit("6")->select();
if(is_array($ad_position) && !in_array($pid,array_keys($ad_position)) && $pid)
{
  M("ad_position")->insert(array(
         "position_id"=>$pid,
         "position_name"=>CONTROLLER_NAME."页面自动增加广告位 $pid ",
         "is_open"=>1,
         "position_desc"=>CONTROLLER_NAME."页面",
  ));
  delFile(RUNTIME_PATH); // 删除缓存
  \think\Cache::clear();  
}


$c = 6- count($result); //  如果要求数量 和实际数量不一样 并且编辑模式
if($c > 0 && I("get.edit_ad"))
{
    for($i = 0; $i < $c; $i++) // 还没有添加广告的时候
    {
      $result[] = array(
          "ad_code" => "/public/images/not_adv.jpg",
          "ad_link" => "/index.php?m=Admin&c=Ad&a=ad&pid=$pid",
          "title"   =>"暂无广告图片",
          "not_adv" => 1,
          "target" => 0,
      );  
    }
}
foreach($result as $key=>$v):       
    
    $v[position] = $ad_position[$v[pid]]; 
    if(I("get.edit_ad") && $v[not_adv] == 0 )
    {
        $v[style] = "filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity: 0.5"; // 广告半透明的样式
        $v[ad_link] = "/index.php?m=Admin&c=Ad&a=ad&act=edit&ad_id=$v[ad_id]";        
        $v[title] = $ad_position[$v[pid]][position_name]."===".$v[ad_name];
        $v[target] = 0;
    }
    ?>
					<div class="item <?php if($key == 0): ?>active<?php endif; ?>" style="background-color:<?php echo (isset($v['bgcolor']) && ($v['bgcolor'] !== '')?$v['bgcolor']:gray); ?>;" >
						<a class="item-image" href="<?php echo $v['ad_link']; ?><?php echo !empty($edit_ad)?'&suggestion=820*450' : ''; ?>" <?php if($v['target'] == 1): ?>target="_blank"<?php endif; ?> ><img src="<?php echo $v[ad_code]; ?>" alt="" /></a>
					</div>
				<?php endforeach; ?>
			</div>
			<a class="left carousel-control" href="#myCarousel" data-slide="prev"><span></span></a>
			<a class="right carousel-control" href="#myCarousel" data-slide="next"><span></span></a>
			<div class="right-sidebar p">
				<div class="usertpshop">
					<div class="head_index">
						<a href="<?php echo U('Home/User/index'); ?>" target="_blank">
							<img class="head_pic" src="<?php echo (isset($user['head_pic']) && ($user['head_pic'] !== '')?$user['head_pic']:'/template/pc/rainbow/static/images/default.png'); ?>" alt="" />
						</a>
					</div>
					<p class="welcome nologin">您好，欢迎来到<?php echo (isset($tpshop_config['shop_info_store_name']) && ($tpshop_config['shop_info_store_name'] !== '')?$tpshop_config['shop_info_store_name']:'TPshop商城'); ?>！</p>
					<p class="welcome islogin"><img src="/template/pc/rainbow/static/images/members.png" alt="">&nbsp;Hi，<span class="userinfo"></span></p>
					<div class="login_index">
						<a class="nologin" href="<?php echo U('Home/User/login'); ?>" target="_blank">请登录</a>
						<a class="nologin add_newperson" href="<?php echo U('Home/Activity/coupon_list',array('type'=>4)); ?>">新人有礼</a>
						<a class="islogin" href="<?php echo U('Home/User/index'); ?>" target="_blank">会员中心</a>
						<a class="islogin add_newperson" href="<?php echo U('Home/user/logout'); ?>">退出登录</a>
					</div>
				</div>
				<div class="bulletin">
					<div class="content box_ad_content">
						<div class="gome_news">
							<h2 class="gome_news_title">快讯</h2>
							<a href="<?php echo U('article/detail'); ?>" target="_blank">更多<span></span></a>
						</div>
						<div class="swiper-container swiper-container30" >
							<div style="transition-duration:0ms !important" class="swiper-wrapper swiper-no-swiping">
								<div class="swiper-slide">
									<div class="content-slide">
										<div class="cont4-box">
											<?php if(is_array($notice_list) || $notice_list instanceof \think\Collection || $notice_list instanceof \think\Paginator): $i = 0; $__LIST__ = $notice_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
												<a href="<?php echo U('Home/Article/detail',array('article_id'=>$v['article_id'])); ?>" target="_blank">
													<p><?php echo $v['title']; ?></p>
												</a>
											<?php endforeach; endif; else: echo "" ;endif; ?>

										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="content box_prom_content" style="display: none">
						<?php if(is_array($box_prom) || $box_prom instanceof \think\Collection || $box_prom instanceof \think\Paginator): $i = 0; $__LIST__ = $box_prom;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
							<a href="<?php echo U('Home/Article/detail',array('article_id'=>$v[article_id])); ?>" target="_blank"><?php echo $v[title]; ?></a>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</div>
					<div class="six_entrance">
						<table border="" cellspacing="0" cellpadding="0">
							<tr>
								<td>
									<div class="access">
										<a href="<?php echo U('Home/User/visit_log'); ?>" target="_blank">
											<i class="mybrowse"></i>
											<span>浏览</span>
										</a>
									</div>
								</td>
								<td>
									<div class="access">
										<a href="<?php echo U('Home/User/goods_collect'); ?>" target="_blank">
											<i class="mycollect"></i>
											<span>收藏</span>
										</a>
									</div>
								</td>
								<td class="lastcol">
									<div class="access">
										<a href="<?php echo U('Home/Order/order_list'); ?>" target="_blank">
											<i class="myorders"></i>
											<span>订单</span>
										</a>
									</div>
								</td>
							</tr>
							<tr class="lastcow">
								<td>
									<div class="access">
										<a href="<?php echo U('Home/User/safety_settings'); ?>" target="_blank">
											<i class="account_security"></i>
											<span>账号安全</span>
										</a>
									</div>
								</td>
								<td>
									<div class="access">
										<a href="<?php echo U('Home/User/recharge'); ?>" target="_blank">
											<i class="myshares"></i>
											<span>余额</span>
										</a>
									</div>
								</td>
								<td class="lastcol">
									<div class="access">
										<a href="<?php echo U('Home/Newjoin/index'); ?>" target="_blank">
											<i class="seller_enter"></i>
											<span>商家入驻</span>
										</a>
									</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="fn-mall p ma-to-20">
			<div class="w1224">
				<!--&lt;!&ndash;精品推荐&ndash;&gt;-->
				<div class="conditions top_content">
					<div class="top">
						<p>精品推荐</p>
						<span>甄选优质好物</span>
					</div>
					<div class="swiper-container" id="swiper-container101">
						<div class="swiper-wrapper">
							<div class="swiper-slide">
								<div class="content-slide">
									<div class="cont1-box">
										<?php $pid =52;$ad_position = M("ad_position")->cache(true,TPSHOP_CACHE_TIME)->column("position_id,position_name,ad_width,ad_height","position_id");$result = M("ad")->where("pid=$pid  and enabled = 1 and start_time <= 1588744914 and end_time >= 1588744914 ")->order("orderby desc")->cache(true,TPSHOP_CACHE_TIME)->limit("1")->select();
if(is_array($ad_position) && !in_array($pid,array_keys($ad_position)) && $pid)
{
  M("ad_position")->insert(array(
         "position_id"=>$pid,
         "position_name"=>CONTROLLER_NAME."页面自动增加广告位 $pid ",
         "is_open"=>1,
         "position_desc"=>CONTROLLER_NAME."页面",
  ));
  delFile(RUNTIME_PATH); // 删除缓存
  \think\Cache::clear();  
}


$c = 1- count($result); //  如果要求数量 和实际数量不一样 并且编辑模式
if($c > 0 && I("get.edit_ad"))
{
    for($i = 0; $i < $c; $i++) // 还没有添加广告的时候
    {
      $result[] = array(
          "ad_code" => "/public/images/not_adv.jpg",
          "ad_link" => "/index.php?m=Admin&c=Ad&a=ad&pid=$pid",
          "title"   =>"暂无广告图片",
          "not_adv" => 1,
          "target" => 0,
      );  
    }
}
foreach($result as $key=>$v):       
    
    $v[position] = $ad_position[$v[pid]]; 
    if(I("get.edit_ad") && $v[not_adv] == 0 )
    {
        $v[style] = "filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity: 0.5"; // 广告半透明的样式
        $v[ad_link] = "/index.php?m=Admin&c=Ad&a=ad&act=edit&ad_id=$v[ad_id]";        
        $v[title] = $ad_position[$v[pid]][position_name]."===".$v[ad_name];
        $v[target] = 0;
    }
    ?>
											<a href="<?php echo $v['ad_link']; ?><?php echo !empty($edit_ad)?'&suggestion=350*130' : ''; ?>" <?php if($v['target'] == 1): ?>target="_blank"<?php endif; ?>><img class="wbg1_img" src="<?php echo $v[ad_code]; ?>"/></a>
										<?php endforeach; ?>
										<ul>
											<?php if(is_array($is_recommend) || $is_recommend instanceof \think\Collection || $is_recommend instanceof \think\Paginator): $i = 0; $__LIST__ = $is_recommend;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if($key < 3): ?>
													<li>
														<a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$v[goods_id])); ?>">
															<img src="<?php echo $v['original_img']; ?>" alt="">
															<p><?php echo $v['goods_name']; ?></p>
															<p><span>￥</span><span class="price"><?php echo $v['shop_price_new'][0]; ?></span><span>.<?php echo $v['shop_price_new'][1]; ?></span></p>
														</a>
													</li>
												<?php endif; endforeach; endif; else: echo "" ;endif; ?>
										</ul>
									</div>
								</div>
							</div>
							<?php if($is_recommend['3']): ?>
								<div class="swiper-slide">
									<div class="content-slide">
										<div class="cont1-box">
											<?php $pid =53;$ad_position = M("ad_position")->cache(true,TPSHOP_CACHE_TIME)->column("position_id,position_name,ad_width,ad_height","position_id");$result = M("ad")->where("pid=$pid  and enabled = 1 and start_time <= 1588744914 and end_time >= 1588744914 ")->order("orderby desc")->cache(true,TPSHOP_CACHE_TIME)->limit("1")->select();
if(is_array($ad_position) && !in_array($pid,array_keys($ad_position)) && $pid)
{
  M("ad_position")->insert(array(
         "position_id"=>$pid,
         "position_name"=>CONTROLLER_NAME."页面自动增加广告位 $pid ",
         "is_open"=>1,
         "position_desc"=>CONTROLLER_NAME."页面",
  ));
  delFile(RUNTIME_PATH); // 删除缓存
  \think\Cache::clear();  
}


$c = 1- count($result); //  如果要求数量 和实际数量不一样 并且编辑模式
if($c > 0 && I("get.edit_ad"))
{
    for($i = 0; $i < $c; $i++) // 还没有添加广告的时候
    {
      $result[] = array(
          "ad_code" => "/public/images/not_adv.jpg",
          "ad_link" => "/index.php?m=Admin&c=Ad&a=ad&pid=$pid",
          "title"   =>"暂无广告图片",
          "not_adv" => 1,
          "target" => 0,
      );  
    }
}
foreach($result as $key=>$v):       
    
    $v[position] = $ad_position[$v[pid]]; 
    if(I("get.edit_ad") && $v[not_adv] == 0 )
    {
        $v[style] = "filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity: 0.5"; // 广告半透明的样式
        $v[ad_link] = "/index.php?m=Admin&c=Ad&a=ad&act=edit&ad_id=$v[ad_id]";        
        $v[title] = $ad_position[$v[pid]][position_name]."===".$v[ad_name];
        $v[target] = 0;
    }
    ?>
												<a href="<?php echo $v['ad_link']; ?><?php echo !empty($edit_ad)?'&suggestion=350*130' : ''; ?>" <?php if($v['target'] == 1): ?>target="_blank"<?php endif; ?>><img src="<?php echo $v[ad_code]; ?>"  class="wbg1_img"  /></a>
											<?php endforeach; ?>
												<ul>
													<?php if(is_array($is_recommend) || $is_recommend instanceof \think\Collection || $is_recommend instanceof \think\Paginator): $i = 0; $__LIST__ = $is_recommend;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if($key <= 5 && $key >= 3): ?>
															<li>
																<a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$v[goods_id])); ?>">
																	<img src="<?php echo $v['original_img']; ?>" alt="">
																	<p><?php echo $v['goods_name']; ?></p>
																	<p><span>￥</span><span class="price"><?php echo $v['shop_price_new'][0]; ?></span><span>.<?php echo $v['shop_price_new'][1]; ?></span></p>
																</a>
															</li>
														<?php endif; endforeach; endif; else: echo "" ;endif; ?>
												</ul>
										</div>
									</div>
								</div>
							<?php endif; if($is_recommend['6']): ?>
								<div class="swiper-slide">
									<div class="content-slide">
										<div class="cont1-box">
											<?php $pid =54;$ad_position = M("ad_position")->cache(true,TPSHOP_CACHE_TIME)->column("position_id,position_name,ad_width,ad_height","position_id");$result = M("ad")->where("pid=$pid  and enabled = 1 and start_time <= 1588744914 and end_time >= 1588744914 ")->order("orderby desc")->cache(true,TPSHOP_CACHE_TIME)->limit("1")->select();
if(is_array($ad_position) && !in_array($pid,array_keys($ad_position)) && $pid)
{
  M("ad_position")->insert(array(
         "position_id"=>$pid,
         "position_name"=>CONTROLLER_NAME."页面自动增加广告位 $pid ",
         "is_open"=>1,
         "position_desc"=>CONTROLLER_NAME."页面",
  ));
  delFile(RUNTIME_PATH); // 删除缓存
  \think\Cache::clear();  
}


$c = 1- count($result); //  如果要求数量 和实际数量不一样 并且编辑模式
if($c > 0 && I("get.edit_ad"))
{
    for($i = 0; $i < $c; $i++) // 还没有添加广告的时候
    {
      $result[] = array(
          "ad_code" => "/public/images/not_adv.jpg",
          "ad_link" => "/index.php?m=Admin&c=Ad&a=ad&pid=$pid",
          "title"   =>"暂无广告图片",
          "not_adv" => 1,
          "target" => 0,
      );  
    }
}
foreach($result as $key=>$v):       
    
    $v[position] = $ad_position[$v[pid]]; 
    if(I("get.edit_ad") && $v[not_adv] == 0 )
    {
        $v[style] = "filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity: 0.5"; // 广告半透明的样式
        $v[ad_link] = "/index.php?m=Admin&c=Ad&a=ad&act=edit&ad_id=$v[ad_id]";        
        $v[title] = $ad_position[$v[pid]][position_name]."===".$v[ad_name];
        $v[target] = 0;
    }
    ?>
												<a href="<?php echo $v['ad_link']; ?><?php echo !empty($edit_ad)?'&suggestion=350*130' : ''; ?>" <?php if($v['target'] == 1): ?>target="_blank"<?php endif; ?>><img src="<?php echo $v[ad_code]; ?>"  class="wbg1_img"  /></a>
											<?php endforeach; ?>
												<ul>
													<?php if(is_array($is_recommend) || $is_recommend instanceof \think\Collection || $is_recommend instanceof \think\Paginator): $i = 0; $__LIST__ = $is_recommend;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if($key > 5): ?>
															<li>
																<a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$v[goods_id])); ?>">
																	<img src="<?php echo $v['original_img']; ?>" alt="">
																	<p><?php echo $v['goods_name']; ?></p>
																	<p><span>￥</span><span class="price"><?php echo $v['shop_price_new'][0]; ?></span><span>.<?php echo $v['shop_price_new'][1]; ?></span></p>
																</a>
															</li>
														<?php endif; endforeach; endif; else: echo "" ;endif; ?>
												</ul>
										</div>
									</div>
								</div>
							<?php endif; ?>
						</div>
						<div class="swiper-button-next next101"><span></span></div>
						<div class="swiper-button-prev prev101"><span></span></div>
					</div>
					<div class="swiper-pagination pagination101"></div>
				</div>
				<!--好货上新-->
				<div class="good_goods top_content">
					<div class="top">
						<p>好货上新</p>
						<span>发现品质生活</span>
					</div>
					<ul>
						<?php if(is_array($is_new) || $is_new instanceof \think\Collection || $is_new instanceof \think\Paginator): $i = 0; $__LIST__ = $is_new;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
							<li>
								<a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$v[goods_id])); ?>">
									<img src="<?php echo $v['original_img']; ?>" alt="">
									<p><?php echo $v['goods_name']; ?></p>
									<p><?php echo $v['goods_remark']; ?></p>
								</a>
							</li>
						<?php endforeach; endif; else: echo "" ;endif; ?>

					</ul>
				</div>
				<!--领券中心-->
				<div class="coupon top_content">
					<div class="top">
						<a href="<?php echo U('Home/Activity/coupon_list'); ?>">
							<p>领券中心</p>
							<img src="/template/pc/rainbow/static/images/right.png" alt="">
						</a>

						<span>买的多，省的多</span>
					</div>
					<div class="swiper-container" id="swiper-container100">
						<div class="swiper-wrapper">
							<div class="swiper-slide">
								<div class="content-slide">
									<div class="cont1-box">
										<ul>
											<?php if(is_array($couponList) || $couponList instanceof \think\Collection || $couponList instanceof \think\Paginator): $i = 0; $__LIST__ = $couponList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if($key < 3): ?>
													<a href="<?php echo U('Home/Activity/coupon_list'); ?>">
														<li>
															<img src="<?php echo !empty($v['coupon_img'])?$v['coupon_img']:'/template/pc/rainbow/static/images/coupon_1.png'; ?>" alt="">
															<div>
																<p><span>￥</span><?php echo $v['money']; ?></p>
																<p>
																	<?php if($v['condition'] > 0): ?>
																		满<?php echo $v['condition']; ?>元可用
																	<?php endif; ?>
																</p>
																<p><?php echo $v['use_type_title']; ?></p>
															</div>
														</li>
													</a>
												<?php endif; endforeach; endif; else: echo "" ;endif; ?>
										</ul>
									</div>
								</div>
							</div>
							<?php if($couponList['3']): ?>
								<div class="swiper-slide">
									<div class="content-slide">
										<div class="cont1-box">
											<ul>
												<?php if(is_array($couponList) || $couponList instanceof \think\Collection || $couponList instanceof \think\Paginator): $i = 0; $__LIST__ = $couponList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if($key <= 5 && $key >= 3): ?>
														<li>
															<img src="<?php echo !empty($v['coupon_img'])?$v['coupon_img']:'/template/pc/rainbow/static/images/coupon_1.png'; ?>" alt="">
															<div>
																<p><span>￥</span><?php echo $v['money']; ?></p>
																<p>
																	<?php if($v['condition'] > 0): ?>
																		满<?php echo $v['condition']; ?>元可用
																	<?php endif; ?>
																</p>
																<p><?php echo $v['use_type_title']; ?></p>
															</div>
														</li>
													<?php endif; endforeach; endif; else: echo "" ;endif; ?>
											</ul>
										</div>
									</div>
								</div>
							<?php endif; if($couponList['6']): ?>
								<div class="swiper-slide">
									<div class="content-slide">
										<div class="cont1-box">
											<ul>
												<?php if(is_array($couponList) || $couponList instanceof \think\Collection || $couponList instanceof \think\Paginator): $i = 0; $__LIST__ = $couponList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if($key > 5): ?>
														<li>
															<img src="<?php echo !empty($v['coupon_img'])?$v['coupon_img']:'/template/pc/rainbow/static/images/coupon_1.png'; ?>" alt="">
															<div>
																<p><span>￥</span><?php echo $v['money']; ?></p>
																<p>
																	<?php if($v['condition'] > 0): ?>
																		满<?php echo $v['condition']; ?>元可用
																	<?php endif; ?>
																</p>
																<p><?php echo $v['use_type_title']; ?></p>
															</div>
														</li>
													<?php endif; endforeach; endif; else: echo "" ;endif; ?>

											</ul>
										</div>
									</div>
								</div>
							<?php endif; ?>
						</div>
						<div class="swiper-button-next next100"><span></span></div>
						<div class="swiper-button-prev prev100"><span></span></div>
					</div>
					<div class="swiper-pagination pagination100"></div>
				</div>
				<div class="advertisement p">
					<ul style="margin-top: 20px;">
						<?php $pid =50;$ad_position = M("ad_position")->cache(true,TPSHOP_CACHE_TIME)->column("position_id,position_name,ad_width,ad_height","position_id");$result = M("ad")->where("pid=$pid  and enabled = 1 and start_time <= 1588744914 and end_time >= 1588744914 ")->order("orderby desc")->cache(true,TPSHOP_CACHE_TIME)->limit("4")->select();
if(is_array($ad_position) && !in_array($pid,array_keys($ad_position)) && $pid)
{
  M("ad_position")->insert(array(
         "position_id"=>$pid,
         "position_name"=>CONTROLLER_NAME."页面自动增加广告位 $pid ",
         "is_open"=>1,
         "position_desc"=>CONTROLLER_NAME."页面",
  ));
  delFile(RUNTIME_PATH); // 删除缓存
  \think\Cache::clear();  
}


$c = 4- count($result); //  如果要求数量 和实际数量不一样 并且编辑模式
if($c > 0 && I("get.edit_ad"))
{
    for($i = 0; $i < $c; $i++) // 还没有添加广告的时候
    {
      $result[] = array(
          "ad_code" => "/public/images/not_adv.jpg",
          "ad_link" => "/index.php?m=Admin&c=Ad&a=ad&pid=$pid",
          "title"   =>"暂无广告图片",
          "not_adv" => 1,
          "target" => 0,
      );  
    }
}
foreach($result as $key=>$v):       
    
    $v[position] = $ad_position[$v[pid]]; 
    if(I("get.edit_ad") && $v[not_adv] == 0 )
    {
        $v[style] = "filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity: 0.5"; // 广告半透明的样式
        $v[ad_link] = "/index.php?m=Admin&c=Ad&a=ad&act=edit&ad_id=$v[ad_id]";        
        $v[title] = $ad_position[$v[pid]][position_name]."===".$v[ad_name];
        $v[target] = 0;
    }
    ?>
							<li>
								<a href="<?php echo $v['ad_link']; ?><?php echo !empty($edit_ad)?'&suggestion=300*130' : ''; ?>" <?php if($v['target'] == 1): ?>target="_blank"<?php endif; ?>><img src="<?php echo $v[ad_code]; ?>"/></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<!--猜你喜欢-->
				<div class="wbox gm_guess">
					<div class="gm_title">
						<h2>猜你喜欢</h2>
						<span>YOU MAY LIKE</span>
						<div class="change_btn">
							<a class="pre" onclick="like_page(-1)"><span></span></a>
							<a class="nex" onclick="like_page(1)"><span></span></a>
						</div>
					</div>
					<div id="guess_main" class="guess_main">
						<div id="j-imgload-recomm">
							<ul id="likeGoods">
								<?php if(is_array($getLikeGoods) || $getLikeGoods instanceof \think\Collection || $getLikeGoods instanceof \think\Paginator): $i = 0; $__LIST__ = $getLikeGoods;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$like_v): $mod = ($i % 2 );++$i;?>
								<li>
									<a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$like_v[goods_id])); ?>">
										<img src="<?php echo $like_v['original_img']; ?>">
										<p class="guess_title"><?php echo $like_v['goods_name']; ?></p>
										<p class="guess_price"><span class="yuan">¥</span><?php echo $like_v['shop_price']; ?></p>
									</a>
								</li>
								<?php endforeach; endif; else: echo "" ;endif; ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!--1F-s-->
		<?php if(is_array($web_list) || $web_list instanceof \think\Collection || $web_list instanceof \think\Paginator): if( count($web_list)==0 ) : echo "" ;else: foreach($web_list as $wk=>$wb): ?>
		<div class="tpshop2_0_floor p" id="floor<?php echo $wk+1; ?>">
			<div class="w1224">
				<div class="uantit fixedu p <?php echo $wb['style_name']; ?>" >
					<h3 class="fl"><?php echo $wb[tit][title]; ?> <i><?php echo $wb[tit][floor]; ?></i></h3>
					<ul class="f-tab <?php echo $wb['style_name']; ?> fr">
						<li class="z-select">
							<a href="javascript:;" rel="0" fid="<?php echo $wk+1; ?>"  onmouseover="showul(this)">精选推荐</a>
							<span></span>
						</li>
						<?php if(is_array($wb[recommend_list]) || $wb[recommend_list] instanceof \think\Collection || $wb[recommend_list] instanceof \think\Paginator): if( count($wb[recommend_list])==0 ) : echo "" ;else: foreach($wb[recommend_list] as $rck=>$rcd): ?>
						<li>
							<a href="javascript:;" rel="<?php echo $rck+1; ?>" fid="<?php echo $wk+1; ?>" onmouseover="showul(this)"><?php echo $rcd[recommend][name]; ?></a>
							<span></span>
						</li>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</div>
				<div class="uanmain fixedu p">
					<div class="leftcol fl">
						<div style="width: 224px;height: 317px;" class="lc_top">
							<a href="<?php echo $wb[act][url]; ?>" class="adlight" target="_blank">
								<img src="<?php echo $wb[act][pic]; ?>" alt="" title="<?php echo $wb[act][title]; ?>">
							</a>
						</div>
						<div class="lc_bot">
							<ul>
								<li class="lemain <?php echo $wb['style_name']; ?>">
									<?php if(is_array($wb[category_list][goods_class]) || $wb[category_list][goods_class] instanceof \think\Collection || $wb[category_list][goods_class] instanceof \think\Paginator): if( count($wb[category_list][goods_class])==0 ) : echo "" ;else: foreach($wb[category_list][goods_class] as $key=>$gc): ?>
									<a href="<?php echo U('Goods/goodsList',array('id'=>$gc[gc_id])); ?>"><?php echo $gc['gc_name']; ?></a>
									<?php endforeach; endif; else: echo "" ;endif; ?>
								</li>
							</ul>
						</div>
					</div>

					<div class="rightcol fl">
						<div class="content_goods_sh" id="wbg0">
						  <?php if($wb[adv][0][adv_type] == 'upload_advmin'): ?>
							<ul class="floor-list hasSlide floor-list2">
							    <?php if(is_array($wb[adv]) || $wb[adv] instanceof \think\Collection || $wb[adv] instanceof \think\Paginator): if( count($wb[adv])==0 ) : echo "" ;else: foreach($wb[adv] as $key=>$ad): if($ad[adv_type] == 'upload_advbig'): ?>
							    	<li>
										<div id="myCarouselq<?php echo $wk; ?>" class="carousel slide w399 p">
											<ol class="carousel-indicators">
											<?php if(is_array($ad[adv_info]) || $ad[adv_info] instanceof \think\Collection || $ad[adv_info] instanceof \think\Paginator): if( count($ad[adv_info])==0 ) : echo "" ;else: foreach($ad[adv_info] as $sk=>$sd): if(!empty($sd['pic_img'])): ?>
											<li data-target="#myCarouselq<?php echo $wk; ?>" data-slide-to="<?php echo $sk-1; ?>" class="<?php if($sk-1 == 0): ?>active<?php endif; ?>"></li>
                                                    <?php endif; endforeach; endif; else: echo "" ;endif; ?>
											</ol>
											<div class="carousel-inner">
												<?php if(is_array($ad[adv_info]) || $ad[adv_info] instanceof \think\Collection || $ad[adv_info] instanceof \think\Paginator): if( count($ad[adv_info])==0 ) : echo "" ;else: foreach($ad[adv_info] as $sk=>$sd): if(!empty($sd['pic_img'])): ?>
												<div class="item <?php if($sk == 1): ?>active<?php endif; ?>">
													<a href="<?php echo $sd['pic_url']; ?>">
                                                        <img src="<?php echo (isset($sd['pic_img']) && ($sd['pic_img'] !== '')?$sd['pic_img']:'/public/images/icon_goods_thumb_empty_300.png'); ?>">
                                                    </a>
												</div>
                                                    <?php endif; endforeach; endif; else: echo "" ;endif; ?>
											</div>
										</div>
									</li>
							    	<?php else: ?>
							    	<li>
                                    <a href="<?php echo $ad['adv_info'][pic_url]; ?>">
    <img data-original="<?php echo (isset($ad[adv_info][pic_img]) && ($ad[adv_info][pic_img] !== '')?$ad[adv_info][pic_img]:'/public/images/icon_goods_thumb_empty_300.png'); ?>" class="lazy" width="199" height="243"/>
                                    </a>
									</li>
							    	<?php endif; endforeach; endif; else: echo "" ;endif; ?>
							</ul>
						  <?php else: ?>
						 	<ul class="floor-list floor-list1 hasSlide">
						 	<?php if(is_array($wb[adv]) || $wb[adv] instanceof \think\Collection || $wb[adv] instanceof \think\Paginator): if( count($wb[adv])==0 ) : echo "" ;else: foreach($wb[adv] as $ak=>$ad): if($ad[adv_type] == 'upload_advbig'): ?>
								<li>
									<div id="myCarouselq<?php echo $wk; ?>" class="carousel slide w399 p">
										<ol class="carousel-indicators">
											<?php if(is_array($ad[adv_info]) || $ad[adv_info] instanceof \think\Collection || $ad[adv_info] instanceof \think\Paginator): if( count($ad[adv_info])==0 ) : echo "" ;else: foreach($ad[adv_info] as $sk=>$sd): if(!empty($sd['pic_img'])): ?>
                                        <li data-target="#myCarouselq<?php echo $wk; ?>" data-slide-to="<?php echo $sk-1; ?>" class="<?php if($sk-1 == 0): ?>active<?php endif; ?>"></li>
                                                    <?php endif; endforeach; endif; else: echo "" ;endif; ?>
										</ol>
										<div class="carousel-inner">
											<?php if(is_array($ad[adv_info]) || $ad[adv_info] instanceof \think\Collection || $ad[adv_info] instanceof \think\Paginator): if( count($ad[adv_info])==0 ) : echo "" ;else: foreach($ad[adv_info] as $sk=>$sd): if(!empty($sd['pic_img'])): ?>
                                                <div class="item <?php if($sk == 1): ?>active<?php endif; ?>">
                                                    <a href="<?php echo $sd['pic_url']; ?>"  asfadfads>
                                                        <img src="<?php echo (isset($sd['pic_img']) && ($sd['pic_img'] !== '')?$sd['pic_img']:'/public/images/icon_goods_thumb_empty_300.png'); ?>">
                                                    </a>
                                                </div>
                                                <?php endif; endforeach; endif; else: echo "" ;endif; ?>
										</div>
									</div>
								</li>
								<?php else: ?>
								<li>
									<a href="<?php echo $ad[adv_info][pic_url]; ?><?php echo !empty($edit_ad)?'&suggestion=1224*82' : ''; ?>">
                                        <img src="<?php echo (isset($ad[adv_info][pic_img]) && ($ad[adv_info][pic_img] !== '')?$ad[adv_info][pic_img]:'/public/images/icon_goods_thumb_empty_300.png'); ?>" class="lazy" />
                                    </a>
								</li>
								<?php endif; endforeach; endif; else: echo "" ;endif; ?>
							</ul>
						 <?php endif; ?>
						</div>
						<?php if(is_array($wb[recommend_list]) || $wb[recommend_list] instanceof \think\Collection || $wb[recommend_list] instanceof \think\Paginator): if( count($wb[recommend_list])==0 ) : echo "" ;else: foreach($wb[recommend_list] as $gk=>$wg): ?>
						<div class="content_goods_sh content_goods_list" id="wbg<?php echo $gk+1; ?>" style="display:none;">
							<ul class="floor-list-cont">
								<?php if(is_array($wg[goods_list]) || $wg[goods_list] instanceof \think\Collection || $wg[goods_list] instanceof \think\Paginator): if( count($wg[goods_list])==0 ) : echo "" ;else: foreach($wg[goods_list] as $key=>$pd): ?>
								<li>
									<a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$pd[goods_id])); ?>">
										<img src="<?php echo $pd['goods_pic']; ?>" width="160" height="160">
										<p class="goods_name_tp2"><?php echo $pd['goods_name']; ?></p>
										<p class="goods_price_tp2"><em>￥</em><span><?php echo $pd['goods_price']; ?></span></p>
									</a>
								</li>
								<?php endforeach; endif; else: echo "" ;endif; ?>
							</ul>
						</div>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</div>
				</div>
			</div>
		</div>
		<!--1F-e-->
		<div class="tpshop2_0_brand ma-to-10 p">
			<div class="w1224 bggc">
				<ul>
					<?php if(is_array($wb[brand_list]) || $wb[brand_list] instanceof \think\Collection || $wb[brand_list] instanceof \think\Paginator): if( count($wb[brand_list])==0 ) : echo "" ;else: foreach($wb[brand_list] as $key=>$bd): ?>
					<li>
						<a href="<?php echo U('Goods/goodsList',array('brand_id'=>$bd[brand_id])); ?>"><img class="lazy" data-original="<?php echo (isset($bd['brand_pic']) && ($bd['brand_pic'] !== '')?$bd['brand_pic']:'/public/images/icon_goods_thumb_empty_300.png'); ?>" src="" title="<?php echo $bd['brand_name']; ?>"/></a>
					</li>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</div>
		</div>
		<?php endforeach; endif; else: echo "" ;endif; ?>
		<!--左侧边栏-->
		<div class="sideleft-nav" id="sideleft">
			<div class="first-l">楼层导航</div>
			<ul>
				<?php if(is_array($web_list) || $web_list instanceof \think\Collection || $web_list instanceof \think\Paginator): if( count($web_list)==0 ) : echo "" ;else: foreach($web_list as $k=>$vo): ?>
					<li class="<?php if($k == 0): ?>sid-red<?php endif; ?>">
                        <?php if(!empty($vo[tit][title])): ?>
						    <a href="javascript:;"><i></i><?php echo $vo[tit][title]; ?></a>
                        <?php else: ?>
                            <a style="background-image: url(<?php echo $vo['tit']['pic']; ?>);background-size:100% 31px;" href="javascript:;"><i></i></a>
                        <?php endif; ?>
					</li>
				<?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</div>
		<!--左侧边栏-->
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
		<div class="soubao-sidebar">
    <div class="soubao-sidebar-bg"></div>
    <div class="sidertabs tab-lis-1">
        <div class="sider-top-stra sider-midd-1">
            <div class="icon-tabe-chan">
                <a href="<?php echo U('Home/User/index'); ?>">
                    <i class="share-side share-side1"></i>
                    <i class="share-side tab-icon-tip triangleshow"></i>
                </a>
                <div class="dl_login">
                    <div class="hinihdk">
                        <img class="head_pic" src="/template/pc/rainbow/static/images/dl.png"/>
                        <p class="loginafter nologin"><span>你好，请先</span><a href="<?php echo U('Home/user/login'); ?>">登录</a>！</p>
                        <!--未登录-e--->
                        <!--登录后-s--->
                        <p class="loginafter islogin"><span class="id_jq userinfo">陈xxxxxxx</span><span>点击</span><a href="<?php echo U('Home/user/logout'); ?>">退出</a>！</p>
                        <!--未登录-s--->
                    </div>
                </div>
            </div>
            <div class="icon-tabe-chan shop-car">
                <a href="javascript:void(0);" onclick="ajax_side_cart_list()">
                    <div class="tab-cart-tip-warp-box">
                        <div class="tab-cart-tip-warp">
                            <i class="share-side share-side1"></i>
                            <i class="share-side tab-icon-tip"></i>
                            <span class="tab-cart-tip">购物车</span>
                            <span class="tab-cart-num J_cart_total_num" id="tab_cart_num">0</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="icon-tabe-chan massage">
                <a href="<?php echo U('Home/User/message_notice'); ?>" target="_blank">
                    <i class="share-side share-side1"></i>
                    <!--<i class="share-side tab-icon-tip"></i>-->
                    <span class="tab-tip">消息</span>
                </a>
            </div>
        </div>
        <div class="sider-top-stra sider-midd-2">
            <div class="icon-tabe-chan mmm">
                <a href="<?php echo U('Home/User/goods_collect'); ?>" target="_blank">
                    <i class="share-side share-side1"></i>
                    <!--<i class="share-side tab-icon-tip"></i>-->
                    <span class="tab-tip">收藏</span>
                </a>
            </div>
            <div class="icon-tabe-chan hostry">
                <a href="<?php echo U('Home/User/visit_log'); ?>" target="_blank">
                    <i class="share-side share-side1"></i>
                    <!--<i class="share-side tab-icon-tip"></i>-->
                    <span class="tab-tip">足迹</span>
                </a>
            </div>
      <!--      <div class="icon-tabe-chan sign">
                <a href="" target="_blank">
                    <i class="share-side share-side1"></i>
                    &lt;!&ndash;<i class="share-side tab-icon-tip"></i>&ndash;&gt;
                    <span class="tab-tip">签到</span>
                </a>
            </div>-->
        </div>
    </div>
    <div class="sidertabs tab-lis-2">
        <div class="icon-tabe-chan advice">
            <a href="tencent://message/?uin=<?php echo $tpshop_config['shop_info_qq']; ?>&amp;Site=<?php echo $tpshop_config['shop_info_store_name']; ?>&amp;Menu=yes">
                <i class="share-side share-side1"></i>
                <!--<i class="share-side tab-icon-tip"></i>-->
                <span class="tab-tip">在线咨询</span>
            </a>
        </div>
       <!-- <div class="icon-tabe-chan request">
            <a href="" target="_blank">
                <i class="share-side share-side1"></i>
                &lt;!&ndash;<i class="share-side tab-icon-tip"></i>&ndash;&gt;
                <span class="tab-tip">意见反馈</span>
            </a>
        </div>-->
        <div class="icon-tabe-chan qrcode">
            <a href="" target="_blank">
                <i class="share-side share-side1"></i>
                <i class="share-side tab-icon-tip triangleshow"></i>
				<span class="tab-tip qrewm">
					<img img-url="/index.php?m=Home&c=Index&a=qr_code&data=<?php echo $mobile_url; ?>&head_pic=<?php echo $head_pic; ?>&back_img=<?php echo $back_img; ?>"/>
					扫一扫下载APP
				</span>
            </a>
        </div>
        <div class="icon-tabe-chan comebacktop">
            <a href="" target="_blank">
                <i class="share-side share-side1"></i>
                <!--<i class="share-side tab-icon-tip"></i>-->
                <span class="tab-tip">返回顶部</span>
            </a>
        </div>
    </div>
    <div class="shop-car-sider">

    </div>
</div>
<script src="/template/pc/rainbow/static/js/common.js"></script>
<script>
    //侧边栏
    $(function(){
        var auto_bd=0
        $('.shop-car').click(function(){
            //购物车
            if($('.shop-car-sider').hasClass('sh-hi')){
                $('.shop-car-sider').animate({left:'35px',opacity:'hide'},'normal',function(){
                    $('.shop-car-sider').removeClass('sh-hi');
                    $('.shop-car .tab-cart-tip-warp-box').css('background-color','');
                    $('.shop-car .tab-icon-tip').removeClass('jsshow');
                });
            }else{
                $('.shop-car-sider').animate({left:'-280px',opacity:'show'},'normal',function(){
                    $('.shop-car-sider').addClass('sh-hi');
                    $('.shop-car .tab-cart-tip-warp-box').css('background-color','#e23435');
                    $('.shop-car .tab-icon-tip').addClass('jsshow');
                });
            }

        })
        $(".comebacktop").click(function () {
            //回到顶部
            var speed=300;//滑动的速度
            $('body,html').animate({ scrollTop: 0 }, speed);
            return false;
        });
    });
</script>

		<script src="/template/pc/rainbow/static/js/lazyload.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="/template/pc/rainbow/static/js/headerfooter.js" type="text/javascript" charset="utf-8"></script>
		<script src="/template/pc/rainbow/static/js/carousel.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript">
//			页面加载触发
			$(document).on('click','.next100',function(){
				return 0;
			})
			$(document).on('click','.next101',function(){
				return 0;
			})
			//			只保留15个分类
			$(function(){
				for(var i=14;i<30;i++){
					$("#cata-nav .item.fore1:eq(14)").remove();
				}
			})
			function sidebarRollChange() {  //首页侧边栏滚动改变楼层
				var $_floorList=$('.tpshop2_0_floor');
				var $_sidebar=$('#sideleft');
				$_sidebar.find('li').click(function () { //点击切换楼层
					$('html,body').animate({'scrollTop':$_floorList.eq($(this).index()).offset().top},500);
				});
				$(window).scroll(function(){
					if($("div").is(".tpshop2_0_floor")){
						var scrollTop=$(window).scrollTop();
						/*显示左边侧边栏*/
						if(scrollTop<$_floorList.eq(0).offset().top-$(window).height()/2){ //还没滚到到楼层或向上滚出楼层隐藏侧边栏
							$_sidebar.hide();
							return;
						}
						$_sidebar.show(); //左边侧边栏显示
						/*滚动改变侧边栏的状态*/
						for(var j=0; j<$_floorList.length;j++){
							if(scrollTop>$_floorList.eq(j).offset().top-$(window).height()/2){
								$_sidebar.find('li').eq(j).addClass('sid-red').siblings().removeClass('sid-red');
							}
						};
					}

				})
			}
			sidebarRollChange();

			$(function() {
				$('.categorys .dd').show();//首页商品分类显示
				$(".carousel").carousel();//轮播自动播放
			});

			//轮播图小圆点
			var imgle = $("#myCarousel .carousel-inner .item").length;
			for(var i = 0; i < imgle; i++) {
				$('#myCarousel ol.carousel-indicators').append("<li onclick='yuandian()' data-target=" + "#myCarousel" + " data-slide-to=" + i + " class=" + "" + "></li>")
			}
			$('ol.carousel-indicators li:first').addClass('active');
//			圆点切换
			function yuandian(){
				$(this).addClass('active').siblings().removeClass('active');
			}
			//品牌logo
			$(function() {
				var op = 500;
				$('.tpshop2_0_brand ul li').hover(function() {
					if(!$(this).hasClass('b')) {
						$(this).stop().animate({
							opacity: "1"
						}, op).siblings().stop().animate({
							opacity: "0.5"
						}, op);
					}
				}, function() {
					if(!$(this).hasClass('b')) {
						$(this).stop().animate({
							opacity: "1"
						}, op).siblings().stop().animate({
							opacity: "1"
						}, op);
					}
				})
			})
			//楼层横向导航
			$('ul.f-tab li').hover(function(){
				$(this).addClass('z-select').siblings().removeClass('z-select');
				var page_id = $(this).data('id');
				var floot_page = $(this).data('floot');
				$('.'+floot_page).hide();
				$('#'+page_id).show();
			})
			//公告/促销切换
			$(function(){
				$('.bn_box span').hover(function(){
					$(this).addClass('action').siblings().removeClass('action');
					$('.bulletin .content').hide();
					if($(this).hasClass('box_prom')){
						$('.box_prom_content').show();
					}else{
						$('.box_ad_content').show();
					}
				})
			})

			function showul(obj){
				var fid = $(obj).attr('fid');
				var nky = $(obj).attr('rel');
				$('#floor'+fid).find('.content_goods_sh').hide();
				$('#floor'+fid).find('#wbg'+nky).show();
			}

		//			swiper插件轮播精品推荐
		var mySwiper101 = new Swiper('#swiper-container101',{
			centeredSlides: true,
			spaceBetween: 10,
			loop:true,
			pagination: {
				el: '.conditions .pagination101',
				clickable: true,
			},
			navigation: {
				nextEl: '.conditions .swiper-button-next',
				prevEl: '.conditions .swiper-button-prev',
			},
		})
		//			swiper插件轮播领券中心
		var mySwiper100 = new Swiper('#swiper-container100',{
			centeredSlides: true,
			spaceBetween: 10,
			loop:true,
			pagination: {
				el: '.coupon .pagination100',
				clickable: true,
			},
			navigation: {
				nextEl: '.coupon .swiper-button-next',
				prevEl: '.coupon .swiper-button-prev',
			},
		})
		</script>
		<style>
			.tpshop2_0_floor .uantit.default{
				border-bottom: 1px solid #96cb85;
			}
			.lc_bot ul .lemain.default{
				background: #96cb85;
			}
			.p.default{
				border-bottom: 1px solid #e23435;
			}
			.tpshop2_0_floor .f-tab li.z-select a{
				border-color: #e23435;
				color: #e23435;
			}


			.tpshop2_0_floor .uantit.orange{
				border-bottom: 1px solid orange;
			}
			.lc_bot ul .lemain.orange{
				background: orange;
			}
			.p.orange{
				border-bottom: 1px solid orange;
			}
			.tpshop2_0_floor .f-tab.orange li.z-select a{
				border-color: orange;
				color: orange;
			}

			.tpshop2_0_floor .uantit.green{
				border-bottom: 1px solid green;
			}
			.lc_bot ul .lemain.green{
				background: green;
			}
			.p.green{
				border-bottom: 1px solid green;
			}
			.tpshop2_0_floor .f-tab.green li.z-select a{
				border-color: green;
				color: green;
			}

			.tpshop2_0_floor .uantit.purple{
				border-bottom: 1px solid purple;
			}
			.lc_bot ul .lemain.purple{
				background: purple;
			}
			.p.purple{
				border-bottom: 1px solid purple;
			}
			.tpshop2_0_floor .f-tab.purple li.z-select a{
				border-color: purple;
				color: purple;
			}

			.tpshop2_0_floor .uantit.blue{
				border-bottom: 1px solid blue;
			}
			.lc_bot ul .lemain.blue{
				background: blue;
			}
			.p.blue{
				border-bottom: 1px solid blue;
			}
			.tpshop2_0_floor .f-tab.blue li.z-select a{
				border-color: blue;
				color: blue;
			}


			.tpshop2_0_floor .uantit.pink{
				border-bottom: 1px solid pink;
			}
			.lc_bot ul .lemain.pink{
				background: pink;
			}
			.p.pink{
				border-bottom: 1px solid pink;
			}
			.tpshop2_0_floor .f-tab.pink li.z-select a{
				border-color: pink;
				color: pink;
			}

			.tpshop2_0_floor .uantit.red{
				border-bottom: 1px solid red;
			}
			.lc_bot ul .lemain.red{
				background: red;
			}
			.p.red{
				border-bottom: 1px solid red;
			}
			.tpshop2_0_floor .f-tab.red li.z-select a{
				border-color: red;
				color: red;
			}

		</style>
	</body>
</html>
<script>
    var l_page = 1;
    var new_l_page = 1;
    //猜你喜欢分页
    function like_page(p=1){
        new_l_page = l_page;
        l_page = l_page + p;
        if(l_page <=0){
            l_page = 1;
            return false;
        }
        ajax_page = 0;
        $.ajax({
            type : "get",
            url:"/index.php?m=Home&c=Index&a=ajaxLikePage&p="+l_page,
            dataType:'json',
            success: function(data)
            {
                if (data.status == 1 && data.result.length >0){
                    var html = '';
                    $.each(data.result,function (k,v) {
                        html +='<li><a href="/Home/Goods/goodsInfo/id/'+v.goods_id+'"><img src="'+v.original_img+'"><p class="guess_title">'+v.goods_name+'</p><p class="guess_price"><span class="yuan">¥</span>'+v.shop_price+'</p></a></li>';
                    });
                    $('#likeGoods').html(html);
                }else{
                    l_page = new_l_page;
                }

            }
        });
    }
</script>