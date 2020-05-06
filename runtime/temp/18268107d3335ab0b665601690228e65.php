<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:47:"./template/pc/rainbow/activity/promoteList.html";i:1587634420;s:76:"/home/wwwroot/testshop.kingdeepos.com/template/pc/rainbow/public/header.html";i:1587634420;s:83:"/home/wwwroot/testshop.kingdeepos.com/template/pc/rainbow/public/header_search.html";i:1587634420;s:76:"/home/wwwroot/testshop.kingdeepos.com/template/pc/rainbow/public/footer.html";i:1587634420;s:82:"/home/wwwroot/testshop.kingdeepos.com/template/pc/rainbow/public/sidebar_cart.html";i:1587634420;}*/ ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>促销活动商品列表</title>
		<meta name="keywords" content="<?php echo $seo['keywords']; ?>"/>
		<meta name="description" content="<?php echo $seo['description']; ?>"/>
		<link rel="stylesheet" type="text/css" href="/template/pc/rainbow/static/css/tpshop.css" />
		<script src="/template/pc/rainbow/static/js/jquery-1.11.3.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="/public/js/global.js"></script>
        <link rel="shortcut  icon" type="image/x-icon" href="<?php echo (isset($tpshop_config['shop_info_store_ico']) && ($tpshop_config['shop_info_store_ico'] !== '')?$tpshop_config['shop_info_store_ico']:'/public/static/images/logo/storeico_default.png'); ?>" media="screen"/>
	</head>
	<body>
		<style type="text/css">
			.page .fr{float: none;position: relative;left: 50%;right: 50%;margin-left: -350px;}
			.f-total{margin-top: 10px;}
		</style>
		
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

		<div class="search-box p">
			<div class="w1224">
				<div class="search-path fl">
					<a>全部结果</a>
					<i class="litt-xyb"></i>
					<span>促销</span>
				</div>
				<!--<div class="search-clear fr">
					<span><a href="javascript:void(0)">清空筛选条件</a></span>
				</div>-->
			</div>
		</div>
		<script>
			// 倒计时
			function GetRTime2(){
				//var text = GetRTime('2016/02/27 17:34:00');
			<?php if(is_array($goodsList) || $goodsList instanceof \think\Collection || $goodsList instanceof \think\Paginator): if( count($goodsList)==0 ) : echo "" ;else: foreach($goodsList as $k=>$v): ?>
				var text<?php echo $v[goods_id]; ?> = GetRTime('<?php echo date("Y/m/d H:i:s",$v['end_time']); ?>');
				$("#surplus_text<?php echo $v[goods_id]; ?>").text(text<?php echo $v[goods_id]; ?>);
			<?php endforeach; endif; else: echo "" ;endif; ?>
			}
			setInterval(GetRTime2,1000);
		</script>
		<div class="shop-list-tour p">
			<div class="w1224">
				<div class="stsho pre-sts">
					<div class="shop-list-splb pre-set-suma p">
						<ul>
							<?php if(is_array($goodsList) || $goodsList instanceof \think\Collection || $goodsList instanceof \think\Paginator): $i = 0; $__LIST__ = $goodsList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;?>
								<li>
									<div class="s_xsall">
										<div class="xs_img">
											<a target="_blank" href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$goods[goods_id],'item_id'=>$goods[item_id])); ?>">
												<img class="lazy-list" data-original="<?php echo goods_thum_images($goods['goods_id'],262,262,$goods['item_id']); ?>" />
											</a>
										</div>
										<div class="shop_name2">
											<a target="_blank" href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$goods[goods_id],'item_id'=>$goods[item_id])); ?>">
												<?php echo $goods[goods_name]; ?>
											</a>
										</div>
										<div class="price-tag">
											<span class="now"><em class="li_xfo">￥</em><em><?php echo (isset($goods[price]) && ($goods[price] !== '')?$goods[price]:$goods[shop_price]); ?></em></span>
											<span class="pre-per"><em><?php echo $goods[click_count]; ?></em>人已浏览</span>
											<div class="sjs-olde p">
												<!--<p class="old ma0"><em>￥</em><em><?php echo $goods[market_price]; ?></em></p>-->
												<p class="djs-cctime" ><span id="surplus_text<?php echo $goods[goods_id]; ?>"></span><br />(倒计时)</p>
											</div>
										</div>
									</div>
								</li>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</ul>
					</div>
					<!-- page -->
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

		<!--footer-e-->
		<script src="/template/pc/rainbow/static/js/lazyload.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="/template/pc/rainbow/static/js/headerfooter.js" type="text/javascript" charset="utf-8"></script>
	</body>
</html>