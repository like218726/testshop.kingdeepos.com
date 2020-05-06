<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:42:"./template/pc/rainbow/goods/goodsInfo.html";i:1587634420;s:76:"/home/wwwroot/testshop.kingdeepos.com/template/pc/rainbow/public/header.html";i:1587634420;s:83:"/home/wwwroot/testshop.kingdeepos.com/template/pc/rainbow/public/header_search.html";i:1587634420;s:76:"/home/wwwroot/testshop.kingdeepos.com/template/pc/rainbow/public/footer.html";i:1587634420;s:82:"/home/wwwroot/testshop.kingdeepos.com/template/pc/rainbow/public/sidebar_cart.html";i:1587634420;}*/ ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
        <title><?php echo $goods['goods_name']; ?>-<?php echo $tpshop_config['shop_info_store_name']; ?></title>
        <meta name="keywords" content="<?php echo $seo['keywords']; ?>"/>
		<meta name="description" content="<?php echo $seo['description']; ?>"/>
        <meta name="keywords" content="<?php echo $goods['keywords']; ?>"/>
        <meta name="description" content="<?php echo $goods['goods_remark']; ?>"/>
		<link rel="stylesheet" type="text/css" href="/template/pc/rainbow/static/css/tpshop.css" />
		<link rel="stylesheet" type="text/css" href="/template/pc/rainbow/static/css/jquery.jqzoom.css">
		<script src="/template/pc/rainbow/static/js/jquery-1.11.3.min.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript" src="/template/pc/rainbow/static/js/jquery.jqzoom.js"></script>
		<script src="/public/js/global.js"></script>
		<script src="/public/js/pc_common.js"></script>
		<link rel="stylesheet" href="/template/pc/rainbow/static/css/location.css" type="text/css"><!-- 收货地址，物流运费 -->
		<script src="/public/js/viewer/viewer.min.js"></script>
		<link rel="stylesheet" href="/public/css/viewer.min.css">
		<script src="/public/js/layer/layer-min.js"></script>
        <link rel="shortcut  icon" type="image/x-icon" href="<?php echo (isset($tpshop_config['shop_info_store_ico']) && ($tpshop_config['shop_info_store_ico'] !== '')?$tpshop_config['shop_info_store_ico']:'/public/static/images/logo/storeico_default.png'); ?>" media="screen"/>
		<?php if((!empty($kf_config['im_choose'])) && ($kf_config['im_choose'] == 1)): ?>
		<link rel="stylesheet" href="<?php echo $tpshop_config['basic_im_website']; ?>/static/test/common/layui/css/layui.css" media="all">
		<?php endif; ?>
	</head>
	<style>
		.allpre-ne-ter .jieti-anniu {
			display: block;
			width: 80px;
			height: 16px;
			line-height: 16px;
			margin: auto;
			text-align: center;
			border: 1px solid #eeeeee;
			border-top: none;
			background: #FBFBFB;
		}
		.allpre-ne-ter .jieti_anniu {
			float: left;
			margin-top: 5px;
			margin-left: 35px;
			background: url(../images/arrow.png) no-repeat;
			width: 10px;
			height: 8px;
		}
		.allpre-ne-ter .jieti-anniu:hover{
			background-color: red;
			color: #FFFFFF;
		}
		.presell_allpri{
			overflow: hidden;
			clear: both;
			border-top: 1px solid #eeeeee;
			width: 264px;
			margin-left: 22px;
		}
		.presell_allpri ul li{
			padding-top: 18px;
			height: 60px;
			line-height: 20px;
			float: left;
			width: 130px;
			text-align: center;
			border-bottom: 1px solid #eeeeee;
			color: #999;
			border-right:1px dashed #eeeeee;
			border-left:1px dashed #eeeeee;
		}
		.presell_allpri ul li.pre_undred{
			margin-bottom: -1px;
		}
		.presell_allpri ul li.elis{
			text-decoration: line-through;
		}
		.presell_allpri ul li.br_pro{
			border-right: 0
		}
		.presell_allpri ul li.cle{
			clear: both;
		}
		.presell_allpri ul{
			overflow: hidden;
			width: 264px;
			background-color: white;
		}
		.buy_bt_disable{
			background: #ebebeb;
			color: #999;
			cursor: not-allowed
		}
		.sav_shop b{
			margin-left: 5px;
			color: #e23435;
		}
	</style>
	<body>
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
		<div class="search-box p">
			<div class="w1224">
				<div class="search-path fl">
					<a>全部结果</a>
					<i class="litt-xyb"></i>
					<a href="<?php echo U('Home/Goods/goodsList',array('id'=>$goods['cat_id1'])); ?>"><?php echo $goods['goods_class1']['name']; ?></a>
					<i class="litt-xyb"></i>
					<a href="<?php echo U('Home/Goods/goodsList',array('id'=>$goods['cat_id2'])); ?>"><?php echo $goods['goods_class2']['name']; ?></a>
					<i class="litt-xyb"></i>
					<a href="<?php echo U('Home/Goods/goodsList',array('id'=>$goods['cat_id3'])); ?>"><?php echo $goods['goods_class3']['name']; ?></a>
					<div class="havedox">
						<span><?php echo $goods['goods_name']; ?></span>
					</div>
				</div>
			</div>
		</div>
		<div class="details-bigimg p">
			<div class="w1224">
				<div class="detail-img">
					<div class="product-gallery">
						<div class="product-photo" id="photoBody">
							<div class="product-video">
                                <?php if($goods['video']): ?>
		                        <video id="video" src="<?php echo $goods['video']; ?>" controls="controls" onended="this.load();">
		                            	您的浏览器不支持查看此视频，请升级浏览器到最新版本
		                        </video>
                                <?php endif; ?>
		                    </div>
		                    <i class="close-video"></i>
		                    <i class="video-play"></i>
							<!-- 商品大图介绍 start [[-->
							<div class="product-img jqzoom">
                                <img id="zoomimg" src="<?php echo goods_thum_images($goods['goods_id'],400,400); ?>" jqimg="<?php echo goods_thum_images($goods['goods_id'],800,800); ?>">
							</div>
							<!-- 商品大图介绍 end ]]-->
							<!-- 商品小图介绍 start [[-->
							<div class="product-small-img fn-clear">
								<a href="javascript:;" class="next-left next-btn fl disabled"><</a>
								<div class="pic-hide-box fl">
									<ul class="small-pic" id="small-pic" style="left:0;">
										<?php if(is_array($goods['goods_images']) || $goods['goods_images'] instanceof \think\Collection || $goods['goods_images'] instanceof \think\Paginator): $i = 0; $__LIST__ = $goods['goods_images'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$img): $mod = ($i % 2 );++$i;?>
											<li class="small-pic-li <?php if($i == 0): ?>active<?php endif; ?>">
											<a href="javascript:;"><img src="<?php echo get_sub_images($img,$img[goods_id],60,60); ?>" data-img="<?php echo get_sub_images($img,$img[goods_id],400,400); ?>" data-big="<?php echo get_sub_images($img,$img[goods_id],800,800); ?>"> <i></i></a>
											</li>
										<?php endforeach; endif; else: echo "" ;endif; ?>
									</ul>
								</div>
								<a href="javascript:;" class="next-right next-btn fl">></a> </div>
							<!-- 商品小图介绍 end ]]-->
						</div>
						<!-- 收藏商品 start [[-->
						<div class="collect">
							<a href="javascript:void(0);" id="collectLink"><i class="collect-ico collect-ico-null"></i>
								<span class="collect-text">收藏商品</span>
								<em class="J_FavCount">（<?php echo $goods['collect_sum']; ?>）</em></a>
							<!--<a href="javascript:void(0);" id="collectLink"><i class="collect-ico collect-ico-ok"></i>已收藏<em class="J_FavCount">(20)</em></a>-->
						</div>
						<!-- 分享商品 -->
						<!-- <div class="share">
							<div class="jiathis_style">
								<div class="bdsharebuttonbox">
									<a href="#" class="bds_more" data-cmd="more"></a>
									<a href="#" class="bds_qzone" data-cmd="qzone"></a>
									<a href="#" class="bds_tsina" data-cmd="tsina"></a>
									<a href="#" class="bds_tqq" data-cmd="tqq"></a>
									<a href="#" class="bds_renren" data-cmd="renren"></a>
									<a href="#" class="bds_weixin" data-cmd="weixin"></a>
								</div>
								<?php if($goods['is_own_shop'] < 2): ?>&nbsp;&nbsp;<a href="<?php echo U('Order/expose',array('goods_id'=>$goods[goods_id])); ?>" class="next-right fr">举报</a><?php endif; ?>
							</div>
							<script>
								var bd_url = "http://<?php echo $_SERVER[HTTP_HOST]; ?>/index.php?m=Home&c=Goods&a=goodsInfo&id=<?php echo $_GET[id]; ?>";
								var bd_pic = "http://<?php echo $_SERVER[HTTP_HOST]; ?><?php echo goods_thum_images($goods[goods_id],400,400); ?>";
								var is_distribut = getCookie('is_distribut');
								var user_id = getCookie('user_id');
								// 如果已经登录了, 并且是分销商
								if (parseInt(is_distribut) == 1 && parseInt(user_id) > 0) {
									bd_url = bd_url + "&first_leader=" + user_id;
								}
								function setShareConfig(id, config) {
									config.bdUrl = bd_url;
									config.bdPic = bd_pic;
									return config;
								}
								window._bd_share_config = {
									"common": {
										onBeforeClick:setShareConfig,
										"bdSnsKey": {},
										"bdText": "",
										"bdMini": "2",
										"bdPic": "",
										"bdStyle": "0",
										"bdSize": "16"
									},
									"share": {},
									"image": {
										"viewList": ["qzone", "tsina", "tqq", "renren", "weixin"],
										"viewText": "分享到：",
										"viewSize": "16"
									},
									"selectShare": {
										"bdContainerClass": null,
										"bdSelectMiniList": ["qzone", "tsina", "tqq", "renren", "weixin"]
									}
								};
								with (document)0[(getElementsByTagName('head')[0] || body).appendChild(createElement('script')).src = 'http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion=' + ~(-new Date() / 36e5)];
							</script>
						</div> -->
					</div>
				</div>
				<form id="buy_goods_form" name="buy_goods_form" method="post" action="">
					<input type="hidden" name="form" value="1"><!-- 标识商品详情表单提交 -->
					<input type="hidden" name="goods_id" value="<?php echo $goods['goods_id']; ?>" id="goods_id"><!-- 商品id -->
					<input type="hidden" name="goods_prom_type" value="<?php echo $goods['prom_type']; ?>"/><!-- 活动类型 -->
					<input type="hidden" name="prom_id" value=""/><!-- 活动id -->
					<input type="hidden" name="shop_price" value="<?php echo $goods['shop_price']; ?>"/><!-- 活动价格 -->
					<input type="hidden" name="store_count" value="<?php echo $goods['store_count']; ?>"/><!-- 活动库存 -->
					<input type="hidden" name="market_price" value="<?php echo $goods['market_price']; ?>"/><!-- 商品原价 -->
					<input type="hidden" name="start_time" value=""/><!-- 活动开始时间 -->
					<input type="hidden" name="end_time" value=""/><!-- 活动结束时间 -->
					<input type="hidden" name="activity_title" value=""/><!-- 活动标题 -->
					<input type="hidden" name="prom_detail" value=""/><!-- 促销活动的促销类型 -->
					<input type="hidden" name="activity_is_on" value=""/><!-- 活动是否正在进行中 -->
					<input type="hidden" name="item_id" value="<?php echo \think\Request::instance()->param('item_id'); ?>"/><!-- 商品规格id -->
					<input type="hidden" name="exchange_integral" value="<?php echo $goods['exchange_integral']; ?>"/><!-- 积分 -->
					<input type="hidden" name="point_rate" value="<?php echo $point_rate; ?>"/><!-- 积分兑换比 -->
					<input type="hidden" name="is_virtual" value="<?php echo $goods['is_virtual']; ?>"/><!-- 是否是虚拟商品 -->
					<input type="hidden" name="virtual_limit" id="virtual_limit" value="<?php echo (isset($goods['virtual_limit']) && ($goods['virtual_limit'] !== '')?$goods['virtual_limit']:0); ?>"/>
					<!-- 预售使用 s-->
					<input type="hidden" name="deposit_price" value=""/><!-- 订金 -->
					<input type="hidden" name="balance_price" value=""/><!-- 尾款 -->
					<input type="hidden" name="ing_amount" value=""/><!-- 已预订了多少 -->
					<!-- 预售使用 e-->
					<div class="detail-ggsl">
						<h1><?php echo $goods['goods_name']; ?></h1>
						<div class="presale-time" style="display: none">
							<div class="pre-icon fl">
								<span class="ys"><i class="detai-ico"></i><span id="activity_type">抢购活动</span></span>
							</div>
							<div class="pre-icon fr">
								<span class="per" style="display: none"><i class="detai-ico"></i><em id="order_user_num">0</em>人预定</span>
								<span class="ti" id="activity_time"><i class="detai-ico"></i>剩余时间：<span id="overTime"></span></span>
								<span class="ti" id="prom_detail"></span>
							</div>
						</div>
						<div class="shop-price-cou p">
							<div class="shop-price-le">
								<ul>
									<li class="jaj"><span id="goods_price_title">商城价：</span></li>
									<li>
										<span class="bigpri_jj" id="goods_price"><em>￥</em>
											<!--<a href=""><em class="sale">（降价通知）</em></a>-->
										</span>
									</li>
								</ul>
								<ul class="pre_sell_div" style="display: none">
									<li class="jaj"><span>订&nbsp;&nbsp;金：</span></li>
									<li>
										<span id="deposit_price"><em>￥</em></span>
									</li>
								</ul>
								<ul class="pre_sell_div" style="display: none">
									<li class="jaj"><span>尾&nbsp;&nbsp;款：</span></li>
									<li>
										<span id="balance_price"><em>￥</em></span>
									</li>
								</ul>
								<ul>
									<li class="jaj"><span id="market_price_title">市场价：</span></li>
									<li class="though-line">
										<span><em>￥</em><span id="market_price"><?php echo $goods['market_price']; ?></span></span>
										<span class="mobile-buy-cheap">
		                                   	 手机购买更便宜
		                                    <i>
												<img class="small-qrcode-h" src="/template/pc/rainbow/static/images/qrcode.png" alt="" />
												<!--<img class="big-qrcode-h" src="/template/pc/rainbow/static/images/qrcode.png" alt="" />-->
												<img class="big-qrcode-h" img-url="/index.php?m=Home&c=Index&a=qr_code&data=<?php echo $ShareLink; ?>&head_pic=<?php echo $head_pic; ?>&back_img=<?php echo $back_img; ?>"/>
											</i>
		                                </span>
									</li>
								</ul>
								<ul id="activity_title_div" style="display: none">
									<li class="jaj"><span id="activity_label"></span></li>
									<li><span id="activity_title" style="color: #df3033;background: 0 0;border: 1px solid #df3033;padding: 2px 3px;"></span></li>
								</ul>
								<?php if($goods['give_integral'] > 0): ?>
									<ul>
										<li class="jaj ls4"><span>赠送积分：</span></li>
										<li><span class="fullminus"><?php echo $goods['give_integral']; ?></span></li>
									</ul>
								<?php endif; ?>
							</div>
							<div class="shop-cou-ri">
								<div class="allcomm"><p>累计评价</p><p class="f_blue"><?php echo $goods['comment_statistics']['total_sum']; ?></p></div>
								<?php if($goods['prom_type'] != 2): ?>
									<div class="br1"></div>
									<div class="allcomm">
										<p>累计销量</p><p class="f_blue"><?php echo $goods['sales_sum']+$goods['virtual_sales_sum']; ?></p>
									</div>
								<?php endif; ?>
							</div>
						</div>
						<?php if($goods[is_virtual] == 0): ?>
							<div class="standard p">
								<!-- 收货地址，物流运费 -start-->
								<ul class="list1">
									<li class="jaj"><span>配&nbsp;&nbsp;送：</span></li>
									<li class="summary-stock though-line">
										<div class="dd shd_address">
											<!--<div class="addrID"><div></div><b></b></div>-->
											<div class="store-selector add_cj_p">
												<div class="text" style="width: 150px;"><div></div><b></b></div>
												<div onclick="$(this).parent().removeClass('hover')" class="close"></div>
											</div>
											<span id="dispatching_msg" style="display: none;">可配送</span>
											<span id="dispatching_desc" style="vertical-align: middle;position: relative;top: -4px;left: 9px;color: #666"></span>
										</div>
									</li>

								</ul>
								<!-- 收货地址，物流运费 -end-->
								<script src="<?php echo HTTP; ?>://api.map.baidu.com/api?v=2.0&ak=xXo650H9zUKh1Lk19uBaNcEWLoG3eGBU" type="text/javascript" c="<?php echo $tpshop_config['basic_bd_ak']; ?>"></script>
								<script type="text/javascript">
									var cur_lng = 114.02597366; // 获取当前坐标 lng
									var cur_lat = 22.54605355; // 获取当前坐标 lat
									var auto_bd = 0; // 是否百度定位
									function current_location() {
										var dis = getCookie('district_name');
										if(!dis && auto_bd==0){
											//console.log('-- 开始百度定位')
											auto_bd = 1;
											var geolocation = new BMap.Geolocation();
											geolocation.getCurrentPosition(function (r) {
												if (this.getStatus() == BMAP_STATUS_SUCCESS) {
													var geoc = new BMap.Geocoder();
													geoc.getLocation(r.point, function (rs) {

														var addressComp = rs.addressComponents;
														setCookies('province_name', addressComp.province, 30 * 24 * 60 * 60 * 1000);
														setCookies('city_name', addressComp.city, 30 * 24 * 60 * 60 * 1000);
														setCookies('district_name', addressComp.district, 30 * 24 * 60 * 60 * 1000);
														$('#address').text(addressComp.province+addressComp.city+addressComp.district);
														auto_bd = 2;

														province_name = getCookie('province_name');
														city_name = getCookie('city_name');
														district_name = getCookie('district_name');
														//console.log(province_name+city_name+district_name,'--百度定位结果')
														ajaxDispatching('',province_name,city_name,district_name);
													});
												}
												else {
													// 定位失败调用默认地址
													auto_bd = 0;
													doInitRegion();
													console.log('getCurrentPosition failed:' + this.getStatus());
												}
											}, {enableHighAccuracy: true})
										}
									}
									current_location();
								</script>
								<!--------收货地址，物流运费-开始-------------->
								<script src="/public/js/locationJson.js"></script>
								<script src="/template/pc/rainbow/static/js/location.js"></script>
								<!--------收货地址，物流运费--结束-------------->
							</div>
						<?php endif; ?>
						<div class="standard p">
							<ul>
								<li class="jaj"><span>服&nbsp;&nbsp;务：</span></li>
								<li class="lawir"><span class="service">由<a href="<?php echo U('Home/Store/index',['store_id'=>$goods['store_id']]); ?>"><?php echo $goods['store']['store_name']; ?></a>发货并提供售后服务</span></li>
							</ul>
						</div>
						<div class="standard p">
							<ul>
								<li class="jaj"><span>品&nbsp;&nbsp;牌：</span></li>
								<li class="lawir"><span class="service"><?php echo $goods['brand']['name']; ?></span></li>
							</ul>
						</div>
						<?php if($goods['is_virtual'] == 0 and $goods['exchange_integral'] > 0): ?>
							<div class="standard p">
								<ul>
									<li class="jaj"><span>可&nbsp;&nbsp;用：</span></li>
									<li class="lawir"><span class="service" id="integral">
                                        <?php echo round($goods['shop_price']-$goods['exchange_integral']/$point_rate,2); ?>+<?php echo $goods['exchange_integral']; ?>积分
                                    </span></li>
								</ul>
							</div>
						<?php endif; ?>
						<!-- 规格 start [[-->
						<?php if(is_array($filter_spec) || $filter_spec instanceof \think\Collection || $filter_spec instanceof \think\Paginator): if( count($filter_spec)==0 ) : echo "" ;else: foreach($filter_spec as $k=>$v): ?>
							<div class="spec_goods_price_div standard p">
								<ul>
									<li class="jaj <?php if(mb_strlen($k) > 4): ?>ls4<?php endif; ?>"><span><?php echo $k; ?>：</span></li>
									<li class="lawir colo">
										<?php if(is_array($v) || $v instanceof \think\Collection || $v instanceof \think\Paginator): if( count($v)==0 ) : echo "" ;else: foreach($v as $k2=>$v2): ?>
											<input type="radio" hidden id="goods_spec_<?php echo $v2[item_id]; ?>" name="goods_spec[<?php echo $k; ?>]" value="<?php echo $v2[item_id]; ?>"/>
											<!--<a id="goods_spec_a_<?php echo $v2[item_id]; ?>" onclick="switch_zooming('<?php echo $v2[src]; ?>'); select_filter(this);" <?php if(!empty($v2['src'])): ?>onclick="$('#zoomimg').attr('src','<?php echo $v2[src]; ?>');"<?php endif; ?>><?php echo $v2[item]; ?></a>-->
											<?php if($v2[src] != ''): ?>
												<a id="goods_spec_a_<?php echo $v2[item_id]; ?>" style="text-align: center;" onclick="switch_zooming('<?php echo $v2[src]; ?>');select_filter(this); $('#zoomimg').attr('src','<?php echo $v2[src]; ?>')">
													<img src="<?php echo $v2[src]; ?>" style="width: 40px;height: 40px;"/>
													<span class="dis_alintro" style="width:auto;"><?php echo $v2[item]; ?></span>
												</a>
											<?php else: ?>
												<a id="goods_spec_a_<?php echo $v2[item_id]; ?>" onclick="switch_zooming('<?php echo $goods[original_img]; ?>'); select_filter(this);"><?php echo $v2[item]; ?></a>
											<?php endif; endforeach; endif; else: echo "" ;endif; ?>
									</li>
								</ul>
							</div>
						<?php endforeach; endif; else: echo "" ;endif; ?>
						<!-- 规格end ]]-->
						<div class="standard p">
							<ul>
								<li class="jaj"><span>数&nbsp;&nbsp;量：</span></li>
								<li class="lawir">
									<div class="minus-plus">
										<a class="mins" href="javascript:void(0);" onclick="altergoodsnum(-1)">-</a>
										<input class="buyNum" id="number" type="text" name="goods_num" value="1" onblur="altergoodsnum(0)" max=""/>
										<a class="add" href="javascript:void(0);" onclick="altergoodsnum(1)">+</a>
									</div>
									<div class="sav_shop"><?php if(empty($goods['store_count']) || (($goods['store_count'] instanceof \think\Collection || $goods['store_count'] instanceof \think\Paginator ) && $goods['store_count']->isEmpty())): ?><b>已售罄</b><?php else: ?>库存：<span id="spec_store_count"><?php echo $goods['store_count']; ?></span> <?php endif; ?></div>
								</li>
							</ul>
						</div>
						
						<!-- 预售 s -->
						<div class="allpre-ne-ter pre_sell_div" style="left: 300px;top:360px;height: 170px;position: absolute;">
							<div class="presell_allpri" style="display:block">
								<ul id="price_ladder_html"></ul>
							</div>
							<a href="javascript:" class="jieti-anniu price_ladder_more" >
								展开
							</a>
							<script>
								function satrhide() {
									var b = $('.presell_allpri ul li').length;
									for(var i = 4 ;i<b;i++){
										$('.presell_allpri ul li').eq(i).hide();
									}
								};
								function satrshow() {
									var b = $('.presell_allpri ul li').length;
									for(var i = 4 ;i<b;i++){
										$('.presell_allpri ul li').eq(i).show();
									}
								};
								satrhide();
								$(function(){
									$('.jieti-anniu').click(function() {
										satrshow();
										$(this).hide();
									});

									$('.allpre-ne-ter').mouseleave (function() {
										satrhide();
										if(price_ladder.length >4){
											$('.jieti-anniu').show();
										}else{
											$('.jieti-anniu').hide();
										}
									});
								})
							</script>
						</div>
						<!-- 预售 e -->
						<div class="standard p">
							<a id="buy_now" class="paybybill buy_button" href="javascript:;" style="display: none">立即购买</a>
							<a id="join_cart" class="addcar buy_button" href="javascript:;" style="display: none"><i class="sk"></i>加入购物车</a>
						</div>
					</div>
				</form>

				<!--自营-s-->
				<?php if($goods['store']['store_id'] == 1): ?>
					<div class="detail-ry p">
						<div class="delogo">
							<p class="ownsj teace">
								<img src="<?php echo (isset($goods['store']['store_logo']) && ($goods['store']['store_logo'] !== '')?$goods['store']['store_logo']:'/template/pc/rainbow/static/images/icon_store_thumb_empty.png'); ?>" />
								<a class="byouself" href="<?php echo U('Home/Store/index',['store_id'=>$goods['store_id']]); ?>">平台自营</a></p>
						</div>
						<div class="quality">
							<p><i class="z-qui"></i>平台保障</p>
							<?php if($goods['store']['certified'] == 1): ?><p><i class="z-qui"></i>正品保障</p><?php endif; if($goods['store']['qitian'] == 1): ?><p><i class="t-qui"></i>七天无理由退货</p><?php endif; ?>
						</div>

						<?php if((!empty($kf_config['im_choose'])) && ($kf_config['im_choose'] == 1)): ?>
							<!--IM客服-->
							<div class="reception p">
								<a href="javascript:;"  user_id="<?php echo \think\Session::get('user.user_id'); ?>" uname="<?php echo \think\Session::get('user.nickname'); ?>" avatar="<?php echo \think\Session::get('user.head_pic'); ?>" sign="" storeid="<?php echo $goods['store']['store_id']; ?>" goods_id="<?php echo $goods['goods_id']; ?>" web_id="<?php echo SITE_URL; ?>" im_href="<?php echo $tpshop_config['basic_im_website']; ?>" ws_socket="<?php echo $tpshop_config['basic_ws_socket']; ?>" id="workerman-kefu" onclick="jump()">
									<i class="detai-ico"></i>在线客服
								</a>
							</div>
							<?php elseif((!empty($kf_config['im_choose'])) && ($kf_config['im_choose'] == 2)): ?>
							<!--小能客服-->
							<div class="reception p">
								<a href="javascript:;">
									<i class="detai-ico"></i>在线客服
								</a>
							</div>
							<?php else: ?>
							<!--qq客服-->
							<div class="reception p">
								<a href="tencent://message/?uin=<?php echo $goods['store']['store_qq']; ?>&Site=TPshop商城&Menu=yes">
									<i class="detai-ico"></i>在线客服
								</a>
							</div>
						<?php endif; ?>
						<div class="guaz_jd"></div>
					</div>
				<?php endif; ?>
				<!--自营-e-->

				<!--进驻店铺-s-->
				<?php if(($goods['store']['is_own_shop'] == 1) AND ($goods['store']['store_id'] > 1)): ?>
					<div class="detail-ry p">
						<div class="delogo">
							<a href="<?php echo U('Home/Store/index',['store_id'=>$goods['store']['store_id']]); ?>">
								<img src="<?php echo (isset($goods['store']['store_logo']) && ($goods['store']['store_logo'] !== '')?$goods['store']['store_logo']:'/template/pc/rainbow/static/images/icon_store_thumb_empty.png'); ?>" />
							</a>
							<p class="ownsj cooperation">
								<a class="co_blue" href="<?php echo U('Home/Store/index',['store_id'=>$goods['store']['store_id']]); ?>"><span><?php echo $goods['store']['store_name']; ?></span></a>
								<a class="byouself">平台自营</a>
							</p>
						</div>
					   	<?php if(!(empty($guarantee) || (($guarantee instanceof \think\Collection || $guarantee instanceof \think\Paginator ) && $guarantee->isEmpty()))): ?>
						<div class="tp-quality clearfixs">
							<p class="tp-quality-title">服务保障：</p>
							<?php if(is_array($guarantee) || $guarantee instanceof \think\Collection || $guarantee instanceof \think\Paginator): if( count($guarantee)==0 ) : echo "" ;else: foreach($guarantee as $key=>$vs): ?>
								<p class="tp-quality-cont"><img src="<?php echo $vs['grt_icon']; ?>" height="24px" width="24px"><?php echo $vs['grt_name']; ?></p>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</div>
						<?php endif; if((!empty($kf_config['im_choose'])) && ($kf_config['im_choose'] == 1)): ?>
							<!--IM客服-->
							<div class="reception p">
								<a href="javascript:;"  user_id="<?php echo \think\Session::get('user.user_id'); ?>" uname="<?php echo \think\Session::get('user.nickname'); ?>" avatar="<?php echo \think\Session::get('user.head_pic'); ?>" sign="" storeid="<?php echo $goods['store']['store_id']; ?>" goods_id="<?php echo $goods['goods_id']; ?>" web_id="<?php echo SITE_URL; ?>" im_href="<?php echo $tpshop_config['basic_im_website']; ?>" ws_socket="<?php echo $tpshop_config['basic_ws_socket']; ?>" id="workerman-kefu" onclick="jump()">
									<i class="detai-ico"></i>在线客服
								</a>
							</div>
							<?php elseif((!empty($kf_config['im_choose'])) && ($kf_config['im_choose'] == 2)): ?>
							<!--小能客服-->
							<div class="reception p">
								<a href="javascript:;">
									<i class="detai-ico"></i>在线客服
								</a>
							</div>
							<?php else: ?>
							<!--qq客服-->
							<div class="reception p">
								<a href="tencent://message/?uin=<?php echo $goods['store']['store_qq']; ?>&Site=TPshop商城&Menu=yes">
									<i class="detai-ico"></i>在线客服
								</a>
							</div>
						<?php endif; ?>
						<div class="intoshop p">
							<a href="<?php echo U('Home/Store/index',['store_id'=>$goods['store']['store_id']]); ?>">
								进入店铺
							</a>
						</div>
						<div class="guaz_jd"></div>
					</div>
				<?php endif; ?>
				<!--进驻店铺-e-->

				<!--合作商家自营-s-->
				<?php if(($goods['store']['is_own_shop'] == 0) AND ($goods['store']['store_id'] > 1)): ?>
					<div class="detail-ry p">
						<div class="delogo">
							<a href="<?php echo U('Home/Store/index',['store_id'=>$goods['store_id']]); ?>">
								<img src="<?php echo $goods['store']['store_logo']; ?>"/>
								<span><?php echo $goods['store']['store_name']; ?></span>
							</a>
						</div>
						<div class="line1 p">
							<span class="f_voc">店铺总分：</span>
							<a class="nel_tt" href="javascript:void(0);"><i style="width: <?php echo $goods['store']['store_servicecredit']*20; ?>%;"></i></a>
							<span class="lasen"><em><?php echo $goods['store']['store_servicecredit']; ?></em>分</span>
						</div>
						<div class="comment_pen p">
							<ul>
								<li><span>评分明细</span></li>
								<li><span>与行业相比</span></li>
							</ul>
							<ul>
								<li><span>商品<em><?php echo $goods['store']['store_desccredit']; ?></em></span></li>
								<li><span><?php echo ceil($goods['store']['store_class_statistics']['store_desccredit_match'] ); ?>%<i class="detai-ico <?php if($goods['store']['store_class_statistics']['store_desccredit_match'] <= 0): ?>ruin<?php endif; ?>"></i></span></li>
							</ul>
							<ul>
								<li><span>服务<em><?php echo $goods['store']['store_servicecredit']; ?></em></span></li>
								<li><span><?php echo ceil($goods['store']['store_class_statistics']['store_servicecredit_match'] ); ?>%<i class="detai-ico <?php if($goods['store']['store_class_statistics']['store_servicecredit_match'] <= 0): ?>ruin<?php endif; ?>"></i></span></li>
							</ul>
							<ul>
								<li><span>时效<em><?php echo $goods['store']['store_deliverycredit']; ?></em></span></li>
								<li><span><?php echo ceil($goods['store']['store_class_statistics']['store_deliverycredit_match'] ); ?>%<i class="detai-ico <?php if($goods['store']['store_class_statistics']['store_deliverycredit_match'] <= 0): ?>ruin<?php endif; ?>"></i></span></li>
							</ul>
						</div>
						<div class="address_com p">
							<ul>
								<li class="compan"><span>公司：</span></li>
								<li class="pan_add"><span><?php echo $goods['store']['company_name']; ?></span></li>
							</ul>
							<ul>
								<li class="compan"><span>所在地：</span></li>
								<li class="pan_add area_add">
									<span><?php echo $goods['store']['province']['name']; ?></span>
									<span><?php echo $goods['store']['city']['name']; ?></span>
								</li>
							</ul>
						</div>
					   	<?php if(!(empty($guarantee) || (($guarantee instanceof \think\Collection || $guarantee instanceof \think\Paginator ) && $guarantee->isEmpty()))): ?>
						<div class="tp-quality clearfixs">
							<p class="tp-quality-title">服务保障：</p>
							<?php if(is_array($guarantee) || $guarantee instanceof \think\Collection || $guarantee instanceof \think\Paginator): if( count($guarantee)==0 ) : echo "" ;else: foreach($guarantee as $key=>$vs): ?>
								<p class="tp-quality-cont"><img src="<?php echo $vs['grt_icon']; ?>" height="24px" width="24px"><?php echo $vs['grt_name']; ?></p>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</div>
						<?php endif; if((!empty($kf_config['im_choose'])) && ($kf_config['im_choose'] == 1)): ?>
							<!--IM客服-->
							<div class="reception p">
								<a href="javascript:;"  user_id="<?php echo \think\Session::get('user.user_id'); ?>" uname="<?php echo \think\Session::get('user.nickname'); ?>" avatar="<?php echo \think\Session::get('user.head_pic'); ?>" sign="" storeid="<?php echo $goods['store']['store_id']; ?>" goods_id="<?php echo $goods['goods_id']; ?>" web_id="<?php echo SITE_URL; ?>" im_href="<?php echo $tpshop_config['basic_im_website']; ?>" ws_socket="<?php echo $tpshop_config['basic_ws_socket']; ?>" id="workerman-kefu" onclick="jump()">
									<i class="detai-ico"></i>在线客服
								</a>
							</div>
							<?php elseif((!empty($kf_config['im_choose'])) && ($kf_config['im_choose'] == 2)): ?>
							<!--小能客服-->
							<div class="reception p">
								<a href="javascript:;">
									<i class="detai-ico"></i>在线客服
								</a>
							</div>
							<?php else: ?>
							<!--qq客服-->
							<div class="reception p">
								<a href="tencent://message/?uin=<?php echo $goods['store']['store_qq']; ?>&Site=TPshop商城&Menu=yes">
									<i class="detai-ico"></i>在线客服
								</a>
							</div>
						<?php endif; ?>

						<div class="guaz_jd">
							<a href="<?php echo U('Home/Store/index',['store_id'=>$goods['store_id']]); ?>">进店逛逛</a>
							<a id="favoriteStore" data-id="<?php echo $goods['store_id']; ?>">关注店铺</a>
						</div>
					</div>
				<?php endif; ?>
				<!--合作商家自营-e-->
			</div>
		</div>
		<div class="detail-main p">
		<div class="w1224">
			<div class="deta-le-ma">
				<div class="type_more">
					<div class="type-top">
						<h2>商品搜索</h2>
					</div>
					<div class="type-bot">
						<form action="<?php echo U('Home/Goods/search'); ?>" method="post" onsubmit="return goods_search();">
							<input type="hidden" name="store_id" value="<?php echo $goods['store_id']; ?>">
							<div class="gjz_de">
								<span class="gjz_fi">关键字</span>
								<input class="srk_fi" type="text" name="q" id="q" value="" />
							</div>
							<div class="gjz_de">
								<span class="gjz_fi">价&nbsp;&nbsp;&nbsp;&nbsp;格</span>
								<input class="pr_fi" type="text" name="start_price" id="start_price" value="" placeholder="￥" />
								<span>-</span>
								<input class="pr_fi" type="text" name="end_price" id="end_price" value="" placeholder="￥" />
							</div>
							<div class="gjz_de">
								<span class="gjz_fi"></span>
								<input class="sub_tj" type="submit" value="搜索"/>
							</div>
							<!--
                            <p class="ti_litt">
                                <?php if(is_array($tpshop_config['hot_keywords']) || $tpshop_config['hot_keywords'] instanceof \think\Collection || $tpshop_config['hot_keywords'] instanceof \think\Paginator): if( count($tpshop_config['hot_keywords'])==0 ) : echo "" ;else: foreach($tpshop_config['hot_keywords'] as $k=>$wd): ?>
                                    <a href="<?php echo U('Home/Goods/search',array('q'=>$wd)); ?>"><?php echo $wd; ?></a>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </p>
                            -->
						</form>
					</div>
				</div>
				<div class="type_more ma-to-20">
					<div class="type-top">
						<h2>相关分类</h2>
					</div>
					<div class="type-bot">
						<ul class="xg_typ">
							<?php if(is_array($goods['store']['store_goods_class_top_parent']) || $goods['store']['store_goods_class_top_parent'] instanceof \think\Collection || $goods['store']['store_goods_class_top_parent'] instanceof \think\Paginator): $i = 0; $__LIST__ = $goods['store']['store_goods_class_top_parent'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$class): $mod = ($i % 2 );++$i;?>
								<li><a href="<?php echo U('Store/goods_list',array('cat_id'=>$class[cat_id],'store_id'=>$goods[store_id])); ?>"><?php echo $class['cat_name']; ?></a></li>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</ul>
					</div>
				</div>
				<div class="type_more ma-to-20">
					<div class="type-top">
						<h2>品牌推荐</h2>
					</div>
					<div class="type-bot">
						<ul class="xg_typ">
							<?php
                                   
                                $md5_key = md5("select * from __PREFIX__brand where is_hot=1 and cat_name='$brand[cat_name]' and status=0 ORDER BY sort");
                                $result_name = $sql_result_brand_list = S("sql_".$md5_key);
                                if(empty($sql_result_brand_list))
                                {                            
                                    $result_name = $sql_result_brand_list = \think\Db::query("select * from __PREFIX__brand where is_hot=1 and cat_name='$brand[cat_name]' and status=0 ORDER BY sort"); 
                                    S("sql_".$md5_key,$sql_result_brand_list,31104000);
                                }    
                              foreach($sql_result_brand_list as $key=>$brand_list): if($brand_list['cat_id1'] == $goods['cat_id1']): ?>
									<li><a href="<?php echo U('Home/Goods/goodsList',['id'=>$brand_list['cat_id1'],'brand_id'=>$brand_list['id']]); ?>"><?php echo $brand_list['name']; ?></a></li>
									<?php else: ?>
									<li><a href="<?php echo U('Home/Goods/goodsList',['brand_id'=>$brand_list['id']]); ?>"><?php echo $brand_list['name']; ?></a></li>
								<?php endif; endforeach; ?>
						</ul>
					</div>
				</div>
				<div class="type_more ma-to-20">
					<div class="type-top">
						<h2>热搜推荐</h2>
					</div>
					<div class="type-bot">
						<ul class="xg_typ">
							<?php if(is_array($tpshop_config['hot_keywords']) || $tpshop_config['hot_keywords'] instanceof \think\Collection || $tpshop_config['hot_keywords'] instanceof \think\Paginator): if( count($tpshop_config['hot_keywords'])==0 ) : echo "" ;else: foreach($tpshop_config['hot_keywords'] as $k=>$wd): ?>
								<li><a href="<?php echo U('Home/Goods/search',array('q'=>$wd)); ?>"><?php echo $wd; ?></a></li>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</ul>
					</div>
				</div>
				<div class="type_more ma-to-20">
					<div class="type-top">
						<h2>看了又看</h2>
					</div>
					<div class="tjhot-shoplist type-bot">
						<?php if(is_array($look_goods_list) || $look_goods_list instanceof \think\Collection || $look_goods_list instanceof \think\Paginator): $i = 0; $__LIST__ = $look_goods_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$look): $mod = ($i % 2 );++$i;?>
							<div class="alone-shop">
								<a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$look[goods_id])); ?>"><img src="<?php echo goods_thum_images($look['goods_id'],206,206); ?>" style="display: inline;"></a>
								<p class="line-two-hidd"><a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$look[goods_id])); ?>"><?php echo getSubstr($look['goods_name'],0,30); ?></a></p>
								<p class="price-tag"><span class="li_xfo">￥</span><span><?php echo $look['shop_price']; ?></span></p>
							</div>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</div>
				</div>
			</div>
			<div class="deta-ri-ma">
				<div class="introduceshop">
					<div class="datail-nav-top">
						<ul>
							<li class="red"><a href="javascript:void(0);">商品介绍</a></li>
							<li><a href="javascript:void(0);">规格及包装</a></li>
							<li><a href="javascript:void(0);">评价<em>(<?php echo $goods['comment_statistics']['total_sum']; ?>)</em></a></li>
							<li onclick="getconsult(0,1)"><a href="javascript:void(0);">售后服务</a></li>
						</ul>
					</div>
					<!--<div class="he-nav"></div>-->
					<div class="shop-describe shop-con-describe p">
						<div class="deta-descri">
							<p class="shopname_de"><span>商品名称：</span><span><?php echo $goods['goods_name']; ?></span></p>
							<div class="ma-d-uli p">
								<ul>
									<li><span>品牌：</span><span><?php echo $goods['brand']['name']; ?></span></li>
									<li><span>货号：</span><span><?php echo $goods['goods_sn']; ?></span></li>
									<?php if(is_array($goods_attribute) || $goods_attribute instanceof \think\Collection || $goods_attribute instanceof \think\Paginator): $i = 0; $__LIST__ = $goods_attribute;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$attr): $mod = ($i % 2 );++$i;?>
										<li><span><?php echo $attr['attr_name']; ?>：</span><span><?php echo $attr[attr_value]; ?></span></li>
									<?php endforeach; endif; else: echo "" ;endif; ?>
								</ul>
							</div>

							<div class="moreparameter">
								<!--
                                <a href="">跟多参数<em>>></em></a>
                                -->
							</div>
						</div>

						<?php if($plate_top_info): ?>
						<div class="detail-img-b">
							<?php echo htmlspecialchars_decode($plate_top_info); ?>
							<div class="moreparameter"></div>
						</div>
						<?php endif; ?>

						<div class="detail-img-b">
                            <?php if($goods['video']): ?>
                                <video controls="controls" style="max-width: 790px;max-height: 500px">
                                    <source src="<?php echo $goods['video']; ?>" type="video/mp4" />
                                </video>
                            <?php endif; ?>
							<?php echo htmlspecialchars_decode($goods['goods_content']); if($plate_bottom_info): ?>
								<div class="moreparameter"></div>
							<?php endif; ?>
						</div>

						<div class="detail-img-b">
							<?php echo htmlspecialchars_decode($plate_bottom_info); ?>
						</div>

					</div>
					<div class="shop-describe shop-con-describe p" style="display: none;">
						<div class="deta-descri">
							<!--
                            <p class="shopname_de"><span>如果您发现商品信息不准确，<a class="de_cb" href="">欢迎纠错</a></span></p>
                            -->
							<h2 class="shopname_de">属性</h2>
							<?php if(is_array($goods_attribute) || $goods_attribute instanceof \think\Collection || $goods_attribute instanceof \think\Paginator): $i = 0; $__LIST__ = $goods_attribute;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$attr): $mod = ($i % 2 );++$i;?>
								<div class="twic_a_alon">
									<p class="gray_t"><?php echo $attr['attr_name']; ?></p>
									<p><?php echo $attr[attr_value]; ?></p>
								</div>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</div>
					</div>
					<div class="shop-con-describe p" style="display: none;">
						<div class="shop-describe p">
							<div class="comm_stsh ma-to-20">
								<div class="deta-descri">
									<h2>商品评价</h2>
								</div>
							</div>
							<div class="deta-descri p">
								<ul class="tebj">
									<li class="percen"><span><?php echo $goods['comment_statistics']['high_rate']; ?>%</span></li>
									<li class="co-cen">
										<div class="comm_gooba">
											<div class="gg_c">
												<span class="hps">好评</span>
												<span class="hp">（<?php echo $goods['comment_statistics']['high_rate']; ?>%）</span>
												<span class="zz_rg"><i style="width: <?php echo $goods['comment_statistics']['high_rate']; ?>%;"></i></span>
											</div>
											<div class="gg_c">
												<span class="hps">中评</span>
												<span class="hp">（<?php echo $goods['comment_statistics']['center_rate']; ?>%）</span>
												<span class="zz_rg"><i style="width: <?php echo $goods['comment_statistics']['center_rate']; ?>%;"></i></span>
											</div>
											<div class="gg_c">
												<span class="hps">差评</span>
												<span class="hp">（<?php echo $goods['comment_statistics']['low_rate']; ?>%）</span>
												<span class="zz_rg"><i style="width: <?php echo $goods['comment_statistics']['low_rate']; ?>%;"></i></span>
											</div>
										</div>
									</li>
									<li class="tjd-sum">
										<p class="tjd">推荐点：</p>
										<div class="tjd-a">
											<?php if(is_array($goods['comment_point']) || $goods['comment_point'] instanceof \think\Collection || $goods['comment_point'] instanceof \think\Paginator): $i = 0;$__LIST__ = is_array($goods['comment_point']) ? array_slice($goods['comment_point'],0,8, true) : $goods['comment_point']->slice(0,8, true); if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
												<a><?php echo $key; ?></a>
											<?php endforeach; endif; else: echo "" ;endif; ?>
										</div>
									</li>
									<li class="te-cen">
										<div class="nchx_com">
											<p>您可以对已购的商品进行评价</p>
											<a class="jfnuv" href="<?php echo U('Home/Order/comment'); ?>">发表评论</a>
											<!--<p class="xja"><span>详见</span><a class="de_cb" href="">积分规则</a></p>-->
										</div>
									</li>
								</ul>
							</div>
							<div class="deta-descri p">
								<div class="cte-deta">
									<ul id="fy-comment-list">
										<li data-t="1" class="red">
											<a href="javascript:void(0);" class="selected">全部评论（<?php echo $goods['comment_statistics']['total_sum']; ?>）</a>
										</li>
										<li data-t="2">
											<a href="javascript:void(0);">好评（<?php echo $goods['comment_statistics']['high_sum']; ?>）</a>
										</li>
										<li data-t="3">
											<a href="javascript:void(0);">中评（<?php echo $goods['comment_statistics']['center_sum']; ?>）</a>
										</li>
										<li data-t="4">
											<a href="javascript:void(0);">差评（<?php echo $goods['comment_statistics']['low_sum']; ?>）</a>
										</li>
										<li data-t="5">
											<a href="javascript:void(0);">晒单（<?php echo $goods['comment_statistics']['img_sum']; ?>）</a>
										</li>

									</ul>
								</div>
							</div>
							<div class="line-co-sunall"  id="ajax_comment_return">

							</div>
						</div>
					</div>
					<div class="shop-con-describe p" style="display: none;">
						<div class="shop-describe p">
							<div class="comm_stsh ma-to-20">
								<div class="deta-descri">
									<h2>售后保障</h2>
								</div>
							</div>
							<div class="deta-descri p">
								<div class="securi-afr">
									<ul>
										<li class="frhe"><i class="detai-ico msz1"></i></li>
										<li class="wnuzsuhe">
											<h2>卖家服务</h2>
											<p>全国联保一年</p>
										</li>
									</ul>
									<ul>
										<li class="frhe"><i class="detai-ico msz2"></i></li>
										<li class="wnuzsuhe">
											<h2>商城承诺</h2>
											<p>商城平台卖家销售并发货的商品，由平台卖家提供发票和相应的售后服务。请您放心购买！
												注：因厂家会在没有任何提前通知的情况下更改产品包装、产地或者一些附件，本司不能确保客户收到的货物与商城图片、产地、附件说明完全一致。
												只能确保为原厂正货！并且保证与当时市场上同样主流新品一致。若本商城没有及时更新，请大家谅解！</p>
										</li>
									</ul>
									<ul>
										<li class="frhe"><i class="detai-ico msz3"></i></li>
										<li class="wnuzsuhe">
											<h2>正品行货</h2>
											<p>商城向您保证所售商品均为正品行货，商城自营商品开具机打发票或电子发票。</p>
										</li>
									</ul>
									<ul>
										<li class="frhe"><i class="detai-ico msz4"></i></li>
										<li class="wnuzsuhe">
											<h2>全国联保</h2>
											<p>凭质保证书及商城发票，可享受全国联保服务（奢侈品、钟表除外；奢侈品、钟表由联系保修，享受法定三包售后服务），与您亲临商场选购的商品享
												受相同的质量保证。商城还为您提供具有竞争力的商品价格和运费政策，请您放心购买！ </p>
										</li>
									</ul>
									<ul>
										<li class="frhe"><i class="detai-ico msz5"></i></li>
										<li class="wnuzsuhe">
											<h2>退货无忧</h2>
											<p>客户购买商城自营商品7日内（含7日，自客户收到商品之日起计算），在保证商品完好的前提下，可无理由退货。（部分商品除外，详情请见各商品细则）</p>
										</li>
									</ul>
								</div>
							</div>
							<div class="comm_stsh ma-to-20">
								<div class="deta-descri">
									<h2>退款说明</h2>
								</div>
							</div>
							<div class="deta-descri p">
								<div class="fetbajc">
									<p>1.若您购买的家电商品已经拆封过，需要退换货，需请联系原厂开具鉴定检测单</p>
									<p>2.签收商品隔日起七日内提交退货申请，2-3天快递员与您联系安排取回商品</p>
									<p>3.商品退回检验，且必须附上检测单</p>
									<p>5.若退回商品有缺件、影响二次销售状况时，退款作业将暂时停止，飞牛网会依商品状况报价，后由客服人员与您联系说明并于订单内扣除费用后退回剩余款项，
										或您可以取消退货申请；若符合退货条件者将于商品取回后约1-2个工作日内完成退款</p>
									<p>4.通过线上支付的订单办理退货，商品退回检验无误后，商城将提交退款申请, 实际款项会依照各银行作业时间返还至您原支付方式 若您采用货到付款，请于
										办理退货时提供退款账户，亦于商品退回检验无误后，将退款汇至您的银行账户中</p>
								</div>
							</div>
						</div>
						<!--商品咨询-status-->
						<div class="consult-h" id="consult-h">
							<div class="consult-menus">
								<a class="consult-ac" href="javascript:;" onclick="getconsult(0,1)">全部咨询</a>
								<a href="javascript:;" onclick="getconsult(1,1)">商品咨询</a>
								<a href="javascript:;" onclick="getconsult(2,1)">支付</a>
								<a href="javascript:;" onclick="getconsult(3,1)">配送</a>
								<a href="javascript:;" onclick="getconsult(4,1)">售后</a>
								<input type="hidden" name="type" id="type" value="0"/>
							</div>
							<div class="consult-cont">
								<div class="consult-item">
									<div class="consult-tips"><span class="c-orange">温馨提示：</span> 因产线可能更改商品包装、产地、附配件等未及时通知，且每位咨询者购买、提问时间等不同。为此，客服给到的回复仅对提问者3天内有效，其他网友仅供参考！给您带来的不便还请谅解，谢谢！</div>
									<div id="consult_content">
									</div>
									<div class="publish-title">发表咨询</div>
									<form method="post" id="consultForm" action="<?php echo U('Goods/goodsConsult'); ?>">
										<input type="hidden" name="goods_id" value="<?php echo $goods['goods_id']; ?>"/>
										<input type="hidden" name="store_id" value="<?php echo $goods['store_id']; ?>"/>
										<div class="publish-cont">
											<p class="check-consult-tpye">
												商品咨询：
												<label> <input  type="radio" name="consult_type" value="1" checked/>商品咨询</label>
												<label> <input  type="radio" name="consult_type" value="2"/>支付</label>
												<label> <input  type="radio" name="consult_type" value="3"/>配送</label>
												<label> <input  type="radio" name="consult_type" value="4"/>售后</label>
											</p>
											<div class="nickname">
												昵称:
												<?php if(empty($username)): ?>
													<input type="text" name="username" placeholder="请输入昵称"  value=""/>
													<?php else: ?>
													<input type="text" name="username" value="<?php echo $username; ?>" readonly/>
												<?php endif; ?>

											</div>
											<textarea class="publish-des" placeholder="请在这里输入你要描述的信息" name="content" id="conten"></textarea>
											<p class="v-code">
												验证码:
												<input type="text" name="verify_code" maxlength="4"/>
												<img id="verify_code" width="80" height="40"  onclick="verify()">
											</p>
											<input class="publish-btn" onclick="goodsConsultForm()" type="button" value="提交" />
										</div>
									</form>
								</div>
							</div>
						</div>
						<!--商品咨询-end-->
					</div>
				</div>
			</div>
		</div>
	</div>
		<!--footer-s-->
		<div class="footer p">
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

		</div>
		<!--footer-e-->
		<script src="/template/pc/rainbow/static/js/lazyload.min.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript" src="/template/pc/rainbow/static/js/headerfooter.js" ></script>
		<script type="text/javascript">
			var commentType = 1;// 默认评论类型
			var spec_goods_price = <?php echo (isset($spec_goods_price) && ($spec_goods_price !== '')?$spec_goods_price:'null'); ?>;//规格库存价格

			$(document).ready(function () {
				/*商品缩略图放大镜*/
				$(".jqzoom").jqueryzoom({
					xzoom: 500,
					yzoom: 500,
					offset:2,
					position: "right",
					preload: 1,
					lens: 1
				});
				initSpec();
				initGoodsPrice();
			});

			var buy_now = $('#buy_now');
			var join_cart = $('#join_cart');
			function buy_button(){
				var is_virtual = $("input[name='is_virtual']").val();//是否是虚拟商品
				var exchange_integral = $("input[name='exchange_integral']").val();//是否是为积分商品
				var goods_prom_type = $('input[name="goods_prom_type"]').attr('value');//活动商品
				var activity_is_on = $('input[name="activity_is_on"]').attr('value'); //活动是否进行中
				buy_now.hide();
				join_cart.hide();
				//未审核或为上架的商品，从总后台可以查看，但不能购买
				var is_on_sale = "<?php echo $goods['is_on_sale']; ?>";
				var goods_state = "<?php echo $goods['goods_state']; ?>";
				if (is_on_sale != 1 || goods_state != 1) {
					return;
				}
				if(is_virtual == 1){
					buy_now.html('立即购买').show();
					return;
				}
				if(exchange_integral > 0){
					buy_now.html('立即兑换').show();
					return;
				}
				if(goods_prom_type == 4 && activity_is_on == 1){
					buy_now.html('立即预订').show();
					return;
				}
				buy_now.html('立即购买').show();
				join_cart.show();
			}

			//购买按钮
			$(function () {
				//立即购买
				$(document).on('click', '#buy_now', function () {
					if ($(this).hasClass('buy_bt_disable')) {
						return;
					}
					if (getCookie('user_id') == '') {
						pop_login();
						return;
					}
					var is_virtual = $("input[name='is_virtual']").val();//是否是虚拟商品
					var exchange_integral = $("input[name='exchange_integral']").val();//是否是积分兑换商品
					var goods_id = $("input[name='goods_id']").val();
					var store_count = $("input[name='store_count']").attr('value');// 商品原始库存
					var goods_num = parseInt($("input[name='goods_num']").val());
					var goods_prom_type = $('input[name="goods_prom_type"]').attr('value');//活动商品
					var activity_is_on = $('input[name="activity_is_on"]').attr('value'); //活动是否进行中
					var form = $('#buy_goods_form');
					if (is_virtual == 1) {
						var virtual_limit = parseInt($('#virtual_limit').val());
						if ((goods_num <= store_count && goods_num <= virtual_limit) || (goods_num < store_count && virtual_limit == 0)) {
							form.attr('action', "<?php echo U('Home/Cart/cart2',['action'=>'buy_now']); ?>").submit();
						} else {
							layer.msg('购买数量超过此商品购买上限', {icon: 3});
						}
						return;
					}
					if (exchange_integral > 0) {
						buyIntegralGoods(goods_id, 1);
						return;
					}
					if(goods_prom_type == 4 && activity_is_on == 1){
						form.attr('action', "<?php echo U('Home/Cart/pre_sell'); ?>").submit();
						return;
					}
					//普通流程
					if (goods_num <= store_count) {
						form.attr('action', "<?php echo U('Home/Cart/cart2',['action'=>'buy_now']); ?>").submit();
					} else {
						layer.msg('购买数量超过此商品购买上限', {icon: 3});
					}
				})
				//加入购物车
				$(document).on('click', '#join_cart', function () {
					if ($(this).hasClass('buy_bt_disable')) {
						return;
					}
					var goods_id = $("input[name='goods_id']").val();
					AjaxAddCart(goods_id, 1);
				})
			})

			//有规格id的时候，解析规格id选中规格
			function initSpec(){
				var item_id = $("input[name='item_id']").val();
				$.each(spec_goods_price,function(i, o){
					if(o.item_id == item_id){
						var spec_key_arr = o.key.split("_");
						$.each(spec_key_arr,function(index,item){
							var spec_radio = $("#goods_spec_"+item);
							var goods_spec_a = $("#goods_spec_a_"+item);
							spec_radio.attr("checked","checked");
							goods_spec_a.addClass('red');
						})
					}
				})
				if(item_id > 0 && !$.isEmptyObject(spec_goods_price)){
					var item_arr = [];
					$.each(spec_goods_price,function(i, o){
						item_arr.push(o.item_id);
					})
					//规格id不存在商品里
					if($.inArray(parseInt(item_id), item_arr) < 0){
						initFirstSpec();
					}else{
						$.each(spec_goods_price,function(i, o){
							if(o.item_id == item_id){
								var spec_key_arr = o.key.split("_");
								$.each(spec_key_arr,function(index,item){
									var spec_radio = $("#goods_spec_"+item);
									var goods_spec_a = $("#goods_spec_a_"+item);
									spec_radio.attr("checked","checked");
									goods_spec_a.addClass('red');
								})
							}
						})
					}
				}else{
					initFirstSpec();
				}
			}
			//默认让每种规格第一个选中
			function initFirstSpec(){
				$('.spec_goods_price_div').each(function (i, o) {
					var firstSpecRadio = $(this).find("input[type='radio']").eq(0);
					firstSpecRadio.attr('checked','checked').prop('checked','checked');
					firstSpecRadio.parent().find('a').eq(0).addClass('red');
				})
			}
            /**
             * 切换规格
             */
            function select_filter(obj)
            {
                $(obj).addClass('red').siblings('a').removeClass('red');
                $(obj).siblings('input').removeAttr('checked');
                $(obj).prev('input').attr('checked','checked').prop('checked','checked');
                if($('#video').length>0){ //判断是否有视频标签
                    $('#photoBody').addClass('picshow-ac');
					video.pause();
                }
                // 更新商品价格
				initGoodsPrice();
            }

			//缩略图切换
			function changeImg(){
				var $picBox=$('#small-pic');
				var $picList=$picBox.find('.small-pic-li');
				var length=$picList.length;
				$picBox.css('width',70*length);
				if($('#video').length>0){ //判断是否有视频标签
			        $('#photoBody').addClass('videoshow-ac');
			    }
			    $('.video-play').click(function(event) { //点击关闭视频
			        $('#photoBody').addClass('videoshow-ac').removeClass('picshow-ac');
			        video.play();
			    });
			    $('.close-video').click(function(event) {  //点击播放视频
			        $('#photoBody').addClass('picshow-ac').removeClass('videoshow-ac');
			        video.pause();
			    });
			    //缩略图切换
				$picList.mouseenter(function(){
					if($('#video').length>0){
			            $('.close-video').trigger('click');
			        }
					$(this).addClass('active').siblings().removeClass('active');
					$('#zoomimg').attr('src', $(this).find('img').attr('data-img'));
					$('#zoomimg').attr('jqimg', $(this).find('img').attr('data-big'));
				})
				var i=0;
				if(length<=5){
					$('.product-gallery .next-btn').css('display','none');
				}else{
					//前一张缩略图
					$('.next-left').click(function (){
						i--;
						if(i<0){
							i=0;
							return;
						}
						$picBox.animate({left:-i*70},500);
					})
					//后前一张缩略图
					$('.next-right').click(function () {
						i++;
						if(i>length-5){
							i=length-5;
							return;
						}
						$picBox.animate({left:-i*70},500);
					})
				}
			}
			changeImg();
			$(function() {
                ajaxComment(1,1)
				$("#area").click(function (e) {
					SelCity(this,e);
				});

				$('.colo a').click(function(){
					$(this).addClass('red').siblings('a').removeClass('red');
				})
				// 好评差评 切换
				$('.cte-deta ul li').click(function(){
					$(this).addClass('red').siblings().removeClass('red');
					commentType = $(this).data('t');// 评价类型   好评 中评  差评
					ajaxComment(commentType,1);
				})

				$('.datail-nav-top ul li').click(function(){
					$(this).addClass('red').siblings().removeClass('red');
					var er = $('.datail-nav-top ul li').index(this);
					$('.shop-con-describe').eq(er).show().siblings('.shop-con-describe').hide();
				})
			});

            /**
             * 加减数量
             * n 点击一次要改变多少
             * maxnum 允许的最大数量(库存)
             * number ，input的id
             */
            function altergoodsnum(n){

                var num = parseInt($('#number').val());
				if(isNaN(num)){
					num = 1;
				}
                var maxnum = parseInt($('#number').attr('max'));
				if(isNaN(maxnum)){
					maxnum = 1;
				}
				if(maxnum > 200){
					maxnum = 200;
				}
                num += n;
                num <= 0 ? num = 1 :  num;
                if(num >= maxnum){
                    $(this).addClass('no-mins');
                    num = maxnum;
                }
                $('#store_count').text(maxnum-num); //更新库存数量
                $('#number').val(num)
            }

			var price_ladder = null;
			//初始化商品价格库存
			function initGoodsPrice() {
				var goods_id = $('input[name="goods_id"]').val();
				if (!$.isEmptyObject(spec_goods_price)) {
					var goods_spec_arr = [];
					$("input[name^='goods_spec']").each(function () {
						if($(this).attr('checked') == 'checked'){
							goods_spec_arr.push($(this).val());
						}
					});
					var spec_key = goods_spec_arr.sort(sortNumber).join('_');  //排序后组合成 key
                    if (spec_goods_price[spec_key] != undefined){
                        var item_id = spec_goods_price[spec_key]['item_id'];
                        $('input[name=item_id]').val(item_id);
                    }else{
                        $("#goods_price").html("<em>￥</em>"+0); //变动价格显示
                        $('#spec_store_count').html(0);
                        $('input[name="shop_price"]').attr('value', 0);//商品价格
                        $('input[name="store_count"]').attr('value', 0);//商品库存
						$('.buy_button').addClass('buy_bt_disable');
                        return false;
                    }
				}
				$.ajax({
					type: 'post',
					dataType: 'json',
					data: {goods_id: goods_id, item_id: item_id},
					url: "<?php echo U('Home/Goods/activity'); ?>",
					success: function (data) {
						if (data.status == 1) {
							$('input[name="goods_prom_type"]').attr('value', data.result.goods.prom_type);//商品活动类型
							$('input[name="prom_id"]').attr('value', data.result.goods.prom_id);//商品活动id
							$('input[name="shop_price"]').attr('value', data.result.goods.shop_price);//商品价格
                            $('input[name="store_count"]').attr('value', data.result.goods.store_count);//商品库存
							$('input[name="market_price"]').attr('value', data.result.goods.market_price);//商品原价
							$('input[name="start_time"]').attr('value', data.result.goods.start_time);//活动开始时间
							$('input[name="end_time"]').attr('value', data.result.goods.end_time);//活动结束时间
							$('input[name="activity_title"]').attr('value', data.result.goods.activity_title);//活动标题
							$('input[name="prom_detail"]').attr('value', data.result.goods.prom_detail);//促销详情
							$('input[name="activity_is_on"]').attr('value', data.result.goods.activity_is_on);//活动是否正在进行中

							$('input[name="deposit_price"]').attr('value', data.result.goods.deposit_price);//订金
							$('input[name="balance_price"]').attr('value', data.result.goods.balance_price);//尾款
							$('input[name="ing_amount"]').attr('value', data.result.goods.ing_amount);//已预订了多少个
							price_ladder = data.result.goods.price_ladder;
							goods_activity_theme();
							buy_button();
						}
						doInitRegion();
					}
				});
			}

			//商品价格库存显示
			function goods_activity_theme(){
				$('.pre_sell_div').hide();
				var goods_prom_type = $('input[name="goods_prom_type"]').attr('value');
				var activity_is_on = $('input[name="activity_is_on"]').attr('value');
				if(activity_is_on == 0){
					setNormalGoodsPrice();
				}else{
					if(goods_prom_type == 0 || goods_prom_type == 6){
						//普通商品
						setNormalGoodsPrice();
					}else if(goods_prom_type == 1){
						//抢购
						setFlashSaleGoodsPrice();
					}else if(goods_prom_type == 2){
						//团购
						setGroupByGoodsPrice();
					}else if(goods_prom_type == 3){
						//优惠促销
						setPromGoodsPrice();
					}else if(goods_prom_type == 4){
						//预售
						setPreSellGoodsPrice();
					}else{

					}
				}
				var store = $('#spec_store_count').html();//实际库存数量
				if (store <= 0) {
					$('.buy_button').addClass('buy_bt_disable');
				} else {
					$('.buy_button').removeClass('buy_bt_disable');
				}
                if(store<=0){
                    $('.buyNum').val(store);
                }else{
                    $('.buyNum').val(1);
                }
			}

			//普通商品库存和价格
			function setNormalGoodsPrice(){
				var goods_price =  $("input[name='shop_price']").attr('value');// 商品本店价
				var market_price =  $("input[name='market_price']").attr('value');// 商品市场价
				var store_count = $("input[name='store_count']").attr('value');// 商品库存
				var exchange_integral = $("input[name='exchange_integral']").attr('value');// 兑换积分
				var point_rate = $("input[name='point_rate']").attr('value');// 积分金额比
				// 如果有属性选择项
				if(!$.isEmptyObject(spec_goods_price))
				{
					var goods_spec_arr = [];
					$("input[name^='goods_spec']").each(function () {
						if($(this).attr('checked') == 'checked'){
							goods_spec_arr.push($(this).val());
						}
					});
					var spec_key = goods_spec_arr.sort(sortNumber).join('_');  //排序后组合成 key
					goods_price = spec_goods_price[spec_key]['price']; // 找到对应规格的价格
					store_count = spec_goods_price[spec_key]['store_count']; // 找到对应规格的库存
				}
				$('#market_price_title').empty().html('市场价：');
				$('#market_price').empty().html(market_price);
				$("#goods_price").html("<em>￥</em>"+goods_price); //变动价格显示
				var integral = round(goods_price - (exchange_integral / point_rate),2);
				$("#integral").html(integral + '+' +exchange_integral + '积分'); //积分显示
				$('#spec_store_count').html(store_count);
				$('.presale-time').hide();
                $('#number').attr('max',store_count);
			}

			//预售商品库存和价格
			function setPreSellGoodsPrice(){
				var pre_sale_price = $("input[name='shop_price']").attr('value');//预售价
				var pre_sale_count = $("input[name='store_count']").attr('value');//预售库存
				var market_price = $("input[name='market_price']").attr('value');
				var start_time = $("input[name='start_time']").attr('value');
				var end_time = $("input[name='end_time']").attr('value');
				var activity_title = $("input[name='activity_title']").attr('value');
				var deposit_price = $("input[name='deposit_price']").attr('value');
				var balance_price = $("input[name='balance_price']").attr('value');
				var ing_amount = $("input[name='ing_amount']").attr('value');
				var price_ladder_html = '';
				if(price_ladder != null){
					$.each(price_ladder,function(i, o){
						if(ing_amount == o.amount){
							price_ladder_html += '<li class="pre_undred">满<span>' + o.amount + '件</span><br/><span>' + o.price + '</span></li>';
						}else{
							price_ladder_html += '<li class="elis">满<span>' + o.amount + '件</span><br/><span>' + o.price + '</span></li>';
						}
					});
					$('#price_ladder_html').empty().html(price_ladder_html);
					if(price_ladder.length > 3){
						$('.price_ladder_more').show();
					}else{
						$('.price_ladder_more').hide();
					}
				}
				$('.pre_sell_div').show();
				$("#goods_price").html("<em>￥</em>"+pre_sale_price); //变动价格显示
				$("#deposit_price").html("<em>￥</em>"+deposit_price);
				$("#balance_price").html("<em>￥</em>"+balance_price);
				$('#spec_store_count').html(pre_sale_count);
				$('#goods_price_title').html('预售价：');
				$('#activity_type').empty().html('预售');
				$('#market_price_title').empty().html('原&nbsp;&nbsp;价：');
				$('#activity_label').empty().html('预&nbsp;&nbsp;售：');
				$('#activity_title').empty().html(activity_title);
				$('#activity_title_div').show();
				$('#market_price').empty().html(market_price);
				$('.presale-time').show();
				$('#prom_detail').hide();
				$('#number').attr('max',pre_sale_count);
				setInterval(activityTime, 1000);
			}

			//秒杀商品库存和价格
			function setFlashSaleGoodsPrice(){
				var flash_sale_price = $("input[name='shop_price']").attr('value');
				var flash_sale_count = $("input[name='store_count']").attr('value');
				var market_price = $("input[name='market_price']").attr('value');
				var start_time = $("input[name='start_time']").attr('value');
				var end_time = $("input[name='end_time']").attr('value');
				var activity_title = $("input[name='activity_title']").attr('value');
				$("#goods_price").html("<em>￥</em>"+flash_sale_price); //变动价格显示
				$('#spec_store_count').html(flash_sale_count);
				$('#goods_price_title').html('抢购价：');
				$('#market_price_title').empty().html('原&nbsp;&nbsp;价：');
				$('#activity_label').empty().html('抢&nbsp;&nbsp;购：');
				$('#activity_title').empty().html(activity_title);
				$('#activity_title_div').show();
				$('#market_price').empty().html(market_price);
                $('.presale-time').show();
				$('#prom_detail').hide();
                $('#number').attr('max',flash_sale_count);
				setInterval(activityTime, 1000);
			}

			//团购商品库存和价格
			function setGroupByGoodsPrice(){
				var group_by_price = $("input[name='shop_price']").attr('value');
				var group_by_count = $("input[name='store_count']").attr('value');
				var market_price = $("input[name='market_price']").attr('value');
				var start_time = $("input[name='start_time']").attr('value');
				var end_time = $("input[name='end_time']").attr('value');
				var activity_title = $("input[name='activity_title']").attr('value');
				$("#goods_price").empty().html("<em>￥</em>"+group_by_price); //变动价格显示
				$('#spec_store_count').empty().html(group_by_count);
				$('#activity_type').empty().html('团购');
				$('#goods_price_title').empty().html('团购价：');
				$('#market_price_title').empty().html('原&nbsp;&nbsp;价：');
				$('#activity_label').empty().html('团&nbsp;&nbsp;购：');
				$('#activity_title').empty().html(activity_title);
				$('#activity_title_div').show();
				$('#market_price').empty().html(market_price);
				$('.presale-time').show();
				$('#prom_detail').hide();
				$('#number').attr('max',group_by_count);
				setInterval(activityTime, 1000);
			}

			//促销商品库存和价格
			function setPromGoodsPrice(){
				var prom_goods_price = $("input[name='shop_price']").attr('value');
				var prom_goods_count = $("input[name='store_count']").attr('value');
				var market_price = $("input[name='market_price']").attr('value');
				var start_time = $("input[name='start_time']").attr('value');
				var end_time = $("input[name='end_time']").attr('value');
				var activity_title = $("input[name='activity_title']").attr('value');
				var prom_detail = $("input[name='prom_detail']").attr('value');
				$("#goods_price").empty().html("<em>￥</em>"+prom_goods_price); //变动价格显示
				$('#spec_store_count').empty().html(prom_goods_count);
				$('#activity_type').empty().html('促销');
				$('.presale-time').show();
				$('#prom_detail').empty().html(prom_detail).show();
				$('#activity_time').hide();
				$('#goods_price_title').empty().html('促销价：');
				$('#market_price_title').empty().html('原&nbsp;&nbsp;价：');
				$('#activity_label').empty().html('促&nbsp;&nbsp;销：');
				$('#activity_title').empty().html(activity_title);
				$('#activity_title_div').show();
				$('#market_price').empty().html(market_price);
				$('#number').attr('max',prom_goods_count);
			}

			// 倒计时
			function activityTime() {
				var end_time = parseInt($("input[name='end_time']").attr('value'));
				var timestamp = Date.parse(new Date());
				var now = timestamp/1000;
				var end_time_date = formatDate(end_time*1000);
				var text = GetRTime(end_time_date);
				//活动时间到了就刷新页面重新载入
				if(now > end_time){
					layer.msg('该商品活动已结束',function(){
						location.reload();
					});
				}
				$("#overTime").text(text);
			}

			//排序
			function sortNumber(a,b)
			{
				return a - b;
			}

			//收藏商品
			$('#collectLink').click(function(){
				if(getCookie('user_id') == ''){
					pop_login();
				}else{
					$.ajax({
						type:'post',
						dataType:'json',
						data:{goods_id:$('input[name="goods_id"]').val()},
						url:"<?php echo U('Home/Goods/collect_goods'); ?>",
						success:function(res){
							if(res.status == 1){
								layer.msg('成功添加至收藏夹', {icon: 1});
								$('.J_FavCount').text('('+res.result.num+')')
							}else{
								layer.msg(res.msg, {icon: 1});
								$('.J_FavCount').text('('+res.result.num+')')
							}
						}
					});
				}
			});

			//收藏店铺
			$('#favoriteStore').click(function () {
				if (getCookie('user_id') == '') {
					pop_login();
				} else {
					$.ajax({
						type: 'post',
						dataType: 'json',
						data: {store_id: $(this).attr('data-id')},
						url: "<?php echo U('Home/Store/collect_store'); ?>",
						success: function (res) {
							if (res.status == 1) {
								layer.msg('成功添加至收藏夹', {icon: 1});
							} else {
								layer.msg(res.msg, {icon: 3});
							}
						}
					});
				}
			});

			//切换图片
			function switch_zooming(img)
			{
				if(img != ''){
					$('#zoomimg').attr('jqimg', img);
					$('#zoomimg').attr('src', img);
				}
			}

			function pop_login(){
				layer.open({
					type: 2,
					title: '<b>登陆<?php echo $tpshop_config['shop_info_store_name']; ?>网</b>',
					skin: 'layui-layer-rim',
					shadeClose: true,
					shade: 0.5,
					area: ['490px', '460px'],
					content: "<?php echo U('Home/User/pop_login'); ?>",
				});
			}

			//用ajax分页显示评论
			function ajaxComment(commentType,page){
				$.ajax({
					type : "GET",
					url:"/index.php?m=Home&c=goods&a=ajaxComment&goods_id=<?php echo $goods['goods_id']; ?>&commentType="+commentType+"&p="+page,//+tab,
					success: function(data){
						$("#ajax_comment_return").html('');
						$("#ajax_comment_return").append(data);
					}
				});
			}
			/****************************************   评论js  ****************************************/
			/****************************************   评论js  ****************************************/
			/****************************************   评论js  ****************************************/
				// 点击分页触发的事件
			$(document).on("click","#ajax_comment_return .pagination  a",function(){
				ajaxComment(commentType,$(this).data('p'));
			});
			/**
			 * 点赞ajax
			 * dyr
			 * @param obj
			 */
			function zan(obj) {
				var comment_id = $(obj).attr('data-comment-id');
				var zan_num = parseInt($("#span_zan_" + comment_id).text());
				$.ajax({
					type: "POST",
					data: {comment_id: comment_id},
					dataType: 'json',
					url: "/index.php?m=Home&c=Order&a=ajaxZan",//
					success: function (res) {
						if (res.success) {
							$("#span_zan_" + comment_id).text(zan_num + 1);
						} else {
							layer.msg('只能点赞一次哟~', {icon: 2});
						}
					},
					error : function(res) {
						if( res.status == "200"){ // 兼容调试时301/302重定向导致触发error的问题
							layer.msg("请先登录!", {icon: 2});
							return;
						}
						layer.msg("请求失败!", {icon: 2});
						return;
					}
				});
			}
			//字数限制
			$(function() {
				$('.c-cen').click(function() {
					$(this).parents('.deta-descri').siblings('.reply-textarea').toggle();
				})
				$(document).on('click','.J-reply-trigger',function(){
					$(this).siblings('.reply-textarea').toggle();
				})
				$('.reply-input').keyup(function() {
					var nums = 120;
					var len = $(this).val().length;
					if(len > nums) {
						$(this).val($(this).val().substring(0, nums));
						layer.msg("超过字数限制！" , {icon: 2});
					}
					var num = nums - len;
					var su = $(this).siblings().find('.txt-countdown em');
					su.text(num);
				})

				$(document).on('click','.operate-box .reply-submit',function() {
					var content = $(this).parents('.inner').find('.reply-input').val();
					var comment_id = $(this).parents('.inner').find('.reply-input').attr('data-id').substr(10);
					var comment_name = $(this).parents('.comment-operate').siblings('.reply-infor').find('.main .user-name').text();
					var reply_id = $(this).attr('data-id');
					submit_reply(comment_id,content,comment_name,reply_id);
					$(this).parents('.reply-textarea').hide();
				});
			})

			/**
			 * 回复
			 * @param comment_id
			 * @param content
			 * @param to_name
			 * @param reply_id
			 */
			function submit_reply(comment_id,content,to_name,reply_id)
			{
				var goods_id = $('input[name="goods_id"]').val();
				if(getCookie('user_id') == ''){
					pop_login();
				}else {
					$.ajax({
						type: 'post',
						dataType: 'json',
						data: {comment_id: comment_id, content: content, to_name: to_name, reply_id: reply_id, goods_id: goods_id},
						url: "<?php echo U('Home/Order/reply_add'); ?>",
						success: function (res) {
							if (res.status == 1) {
								layer.msg(res.msg, {icon: 1});
							} else {
								layer.msg(res.msg, {icon: 2});
							}
						},
						error: function (res) {
							if (res.status == "200") { // 兼容调试时301/302重定向导致触发error的问题
								layer.msg("请先登录!", {icon: 2});
								return;
							}
							layer.msg("请求失败!", {icon: 2});
						}
					});
				}
			}
		</script>
		<!--商品咨询JS-->
        <script>
            // 普通 图形验证码
            function verify(){
                $('#verify_code').attr('src','/index.php?m=Home&c=User&a=verify&type=consult&r='+Math.random());
            }
            var consult=$('#consult-h');
            consult.find('.consult-item').eq(0).addClass('consult-ac');
            consult.find('.consult-menus>a').click(function () {
                $(this).addClass('consult-ac').siblings().removeClass('consult-ac');
                consult.find('.consult-item').eq($(this).index())
                        .addClass('consult-ac').siblings().removeClass('consult-ac');
                $('.check-consult-tpye').find('a').eq($(this).index())
            });
            $(document).ready(function () {
                verify();
            });
            $(document).on('click','.pagination  a',function(){
                var page = $(this).data('p');
                var type = $('#type').val();
                getconsult(type,page)
            });
            /**
             * 获取商品咨询
             * @param type  //咨询类型
             * @param page  //分页
             */
            function getconsult(type,page){
                var goods_id = $('#goods_id').val()
                $.ajax({
                    type : "get",
                    url  : "/index.php?m=Home&c=Goods&a=ajax_consult",
                    data : {goods_id:goods_id,consult_type:type,p:page},
                    dataType: "html",
                    async:false,
                    success: function(data){
                        $('#consult_content').empty().html(data);
                    }
                });
            }
            //提交
            function goodsConsultForm() {
                var conten = $.trim($('#conten').val());
                if (conten.length > 500) {
                    layer.msg('咨询内容不得超过500字符！！', {icon: 3});
                    return false;
                }
                $('#consultForm').submit();
            }

            function goods_search(){
            	var search_name=$.trim($('.srk_fi').val());
            	var price1=$('#start_price').val();
            	var price2=$('#end_price').val();
            	if((!search_name) && (!price1) && (!price2)){
            		layer.msg('搜索内容不能为空',{icon:2});
        			return false;
            	}
            	if((isNaN(price1)) || (isNaN(price2))){
            		layer.msg('价格仅能输入数字',{icon:2});
        			return false;
            	}
            	if(price1 && price2 && (price1>price2)){
            		layer.msg('请输入正确的价格范围',{icon:2});
        			return false;
            	}
            }
        </script>
		<?php if((!empty($kf_config['im_choose'])) && ($kf_config['im_choose'] == 1)): ?>
		<script type="text/javascript" src="<?php echo $tpshop_config['basic_im_website']; ?>/static/test/common/layui/layui.js"></script>
		<script type="text/javascript" src="<?php echo $tpshop_config['basic_im_website']; ?>/static/test/common/js/main.js"></script>
		<?php endif; ?>
	</body>
</html>