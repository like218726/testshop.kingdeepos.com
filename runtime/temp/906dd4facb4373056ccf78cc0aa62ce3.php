<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:38:"./template/pc/rainbow/store/index.html";i:1587634424;s:75:"/home/wwwroot/testshop.kingdeepos.com/template/pc/rainbow/store/header.html";i:1587634424;s:82:"/home/wwwroot/testshop.kingdeepos.com/template/pc/rainbow/public/sidebar_cart.html";i:1587634420;s:91:"/home/wwwroot/testshop.kingdeepos.com/template/pc/rainbow/store/store_decoration_block.html";i:1587634424;s:76:"/home/wwwroot/testshop.kingdeepos.com/template/pc/rainbow/public/footer.html";i:1587634420;}*/ ?>
<!doctype html>
<html lang="zh">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo $store['store_name']; ?></title>
<meta name="keywords" content="<?php echo $seo['keywords']; ?>"/>
<meta name="description" content="<?php echo $seo['description']; ?>"/>
<link rel="shortcut  icon" type="image/x-icon" href="<?php echo (isset($tpshop_config['shop_info_store_ico']) && ($tpshop_config['shop_info_store_ico'] !== '')?$tpshop_config['shop_info_store_ico']:'/public/static/images/logo/storeico_default.png'); ?>" media="screen"/>
<link href="/template/pc/rainbow/static/css/store_base.css" rel="stylesheet" type="text/css">
<link href="/template/pc/rainbow/static/css/store_header.css" rel="stylesheet" type="text/css">
<link href="/template/pc/rainbow/static/css/tpshop.css" rel="stylesheet" type="text/css">
<script src="/public/js/global.js"></script>
<!--[if IE 6]>
<script src="/public/js/IE6_PNG.js"></script>
<script>
DD_belatedPNG.fix('.pngFix');
</script>
<script>
// <![CDATA[
if((window.navigator.appName.toUpperCase().indexOf("MICROSOFT")>=0)&&(document.execCommand))
	try{
		document.execCommand("BackgroundImageCache", false, true);
	}
	catch(e){}
// ]]>
</script>
<![endif]-->
<script>var _CHARSET = 'utf-8';</script>
<script src="/template/pc/rainbow/static/js/jquery-1.11.3.min.js" type="text/javascript" charset="utf-8"></script>
<script src="/template/pc/rainbow/static/js/common.js" charset="utf-8"></script>
<script src="/public/js/layer/layer.js"></script>
<script type="text/javascript">
var PRICE_FORMAT = '&yen;%s';
$(function(){
	$('#button').click(function(){
	    if ($('#keyword').val() == '') {
		    return false;
	    }
	});
});

$(function(){
	//search
	var act = "show_store";
	if (act == "store_list"){
		$("#search").children('ul').children('li:eq(1)').addClass("current");
		$("#search").children('ul').children('li:eq(0)').removeClass("current");
		}
	$("#search").children('ul').children('li').click(function(){
		$(this).parent().children('li').removeClass("current");
		$(this).addClass("current");
		$('#search_act').attr("value",$(this).attr("act"));
		$('#keyword').attr("placeholder",$(this).attr("title"));
	});
	$("#keyword").blur();
});
</script>
</head>
<body>
<!-- PublicTopLayout Begin -->
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
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

<div class="public-top-layout w">
  <div class="topbar wrapper">
    <div class="user-entry">
		<?php if($user['user_id'] > 0): ?>
			您好 <span><a href="<?php echo U('User/index'); ?>"><?php echo (isset($user['nickname']) && ($user['nickname'] !== '')?$user['nickname']:$user['mobile']); ?></a>
	   		<div class="nc-grade-mini" style="cursor:pointer;" onclick="javascript:go();">VO</div></span>
			欢迎回来，<a href="<?php echo U('Index/index'); ?>" title="首页" alt="首页">
			<span><?php echo (isset($tpshop_config['shop_info_store_name']) && ($tpshop_config['shop_info_store_name'] !== '')?$tpshop_config['shop_info_store_name']:'TPshop商城'); ?></span></a> <span>[<a href="<?php echo U('User/logout'); ?>">退出</a>] </span>
			<?php else: ?>
			Hi，欢迎来 <a href="<?php echo U('Index/index'); ?>" title="首页" alt="首页"><?php echo (isset($tpshop_config['shop_info_store_name']) && ($tpshop_config['shop_info_store_name'] !== '')?$tpshop_config['shop_info_store_name']:'TPshop商城'); ?></a>
			<a href="<?php echo U('User/login'); ?>">请登录</a></span> <span><a href="<?php echo U('User/register'); ?>">免费注册</a></span>
		<?php endif; ?>
   </div>
    <div class="quick-menu">
		<dl class="down_app">
	        <dt><em class="ico_tel"></em><a >手机移动端</a><i></i></dt>
	        <dd>
	       	<div class="qrcode"><img img-url="/index.php?m=Home&c=Index&a=qr_code&data=<?php echo $mobile_url; ?>&head_pic=<?php echo $head_pic; ?>&back_img=<?php echo $back_img; ?>"></div>
	        <div class="hint"><h4>扫描二维码</h4> 下载手机客户端</div>
	        <div class="addurl">
	              <a href="http://fir.im/tpshopAndroid" target="_blank"><i class="icon-android"></i>Android</a>
	              <a href="https://itunes.apple.com/cn/app/sou-bao-shang-cheng/id1119059153?mt=8" target="_blank"><i class="icon-apple"></i>iPhone</a>
	        </div>
	    	</dd>
    	</dl>
    	<dl>
        <dt><em class="ico_shop"></em><a href="<?php echo U('Newjoin/index'); ?>" title="商家管理">商家管理</a><i></i></dt>
        <dd>
          <ul>
		    <li><a href="<?php echo U('Newjoin/index'); ?>" title="招商入驻">招商入驻</a></li>
            <li><a href="<?php echo U('Seller/Admin/login'); ?>" target="_blank" title="登录商家管理中心">商家登录</a></li>
          </ul>
        </dd>
      </dl>
      <dl>
        <dt><em class="ico_order"></em><a href="<?php echo U('Order/order_list'); ?>">我的订单</a><i></i></dt>
        <dd>
          <ul>
            <li><a href="<?php echo U('Order/order_list',array('type'=>'WAITPAY')); ?>">待付款订单</a></li>
            <li><a href="<?php echo U('Order/order_list',array('type'=>'WAITRECEIVE')); ?>">待确认收货</a></li>
            <li><a href="<?php echo U('Order/order_list',array('type'=>'WAITCCOMMENT')); ?>">待评价交易</a></li>
          </ul>
        </dd>
      </dl>
      <dl>
        <dt><em class="ico_store"></em><a href="<?php echo U('User/goods_collect'); ?>">我的收藏</a><i></i></dt>
        <dd>
          <ul>
            <li><a href="<?php echo U('User/goods_collect'); ?>">商品收藏</a></li>
            <li><a href="<?php echo U('User/store_collect'); ?>">店铺收藏</a></li>
          </ul>
        </dd>
      </dl>
      <dl>
        <dt><em class="ico_service"></em>客户服务<i></i></dt>
        <dd>
          <ul>
            <li><a href="<?php echo U('Article/detail',array('article_id'=>1)); ?>">帮助中心</a></li>
            <li><a href="<?php echo U('Article/detail',array('article_id'=>2)); ?>">售后服务</a></li>
            <li><a href="<?php echo U('Article/detail',array('article_id'=>3)); ?>">客服中心</a></li>
          </ul>
        </dd>
      </dl>
      	 <!-- <dl class="weixin">
        <dt>关注我们<i></i></dt>
        <dd>
          <h4>扫描二维码<br/>关注商城微信号</h4>
          <img src="/template/pc/rainbow/static/images/weixin.png" > </dd>
        </dl>-->
    </div>
  </div>
</div>
<script type="text/javascript">
//动画显示边条内容区域
$(function() {
	$(function() {
		$('#activator').click(function() {
			$('#content-cart').animate({'right': '-250px'});
			$('#content-compare').animate({'right': '-250px'});
			$('#vToolbar').animate({'right': '-60px'}, 300,
			function() {
				$('#ncHideBar').animate({'right': '59px'},	300);
			});
	        $('div[nctype^="bar"]').hide();
		});
		$('#ncHideBar').click(function() {
			$('#ncHideBar').animate({
				'right': '-86px'
			},
			300,
			function() {
				$('#content-cart').animate({'right': '-250px'});
				$('#content-compare').animate({'right': '-250px'});
				$('#vToolbar').animate({'right': '6px'},300);
			});
		});
	});
    $("#rtoolbar_cart").click(function(){
        if ($("#content-cart").css('right') == '-250px') {
         	$('#content-compare').animate({'right': '-250px'});
    		$("#content-cart").animate({right:'0px'});
    		$.ajax({
    			type: "GET",
    			url: "/index.php?m=Home&c=Cart&a=header_cart_list",//+tab,
    			dataType:'html',
    			success: function (data) {
    				var data = eval('('+data+')');
    				$('#rtoolbar_cartlist').empty().html($.trim(data));
    			}
    		});
        } else {
        	$(".close").click();
        	$(".chat-list").css("display",'none');
        }
	});
	$(".close").click(function(){
		$(".content-box").animate({right:'-250px'});
      });

	$(".quick-menu dl").hover(function() {
		$(this).addClass("hover");
	},
	function() {
		$(this).removeClass("hover");
	});


    $('div[nctype="a-barUserInfo"]').click(function(){
        $('div[nctype="barUserInfo"]').toggle();
    });
    // 右侧bar登录
    $('div[nctype="a-barLoginBox"]').click(function(){
       // $('div[nctype="barLoginBox"]').toggle();
       //document.getElementById('codeimage').src='/index.php?act=seccode&op=makecode&nchash=a8011a99&t=' + Math.random();
    });
    $('a[nctype="close-barLoginBox"]').click(function(){
        $('div[nctype="barLoginBox"]').toggle();
    });

});

function DrawImage(ImgD,FitWidth,FitHeight){
    var image=new Image();
    image.src=ImgD.src;
    if(image.width>0 && image.height>0)
    {
        if(image.width/image.height>= FitWidth/FitHeight)
        {
            if(image.width>FitWidth)
            {
                ImgD.width=FitWidth;
                ImgD.height=(image.height*FitWidth)/image.width;
            }
            else
            {
                ImgD.width=image.width;
                ImgD.height=image.height;
            }
        }
        else
        {
            if(image.height>FitHeight)
            {
                ImgD.height=FitHeight;
                ImgD.width=(image.width*FitHeight)/image.height;
            }
            else
            {
                ImgD.width=image.width;
                ImgD.height=image.height;
            }
        }
    }
}
</script>
<!-- PublicHeadLayout Begin -->
<div class="header-wrap">
  <div class="public-head-layout wrapper">
    <h1 class="site-logo"><a href="<?php echo U('Index/index'); ?>"><img src="<?php echo (isset($store['store_logo']) && ($store['store_logo'] !== '')?$store['store_logo']:'/template/pc/rainbow/static/images/newLogo.png'); ?>" class="pngFix"></a></h1>
	<div class="heade_store_info">
    	<div class="slogo">
        	<a href="<?php echo U('Store/index',array('store_id'=>$store[store_id])); ?>" title="" class="store_name"><?php echo $store['store_name']; ?></a>
            <br>
        </div>
        <div class="pj_info">
        	<div class="shopdsr_item">
                <div class="shopdsr_title">描述相符</div>
                <div class="shopdsr_score"><?php echo $store['store_desccredit']; ?></div>
            </div>
            <div class="shopdsr_item">
                <div class="shopdsr_title">服务态度</div>
                <div class="shopdsr_score"><?php echo $store['store_servicecredit']; ?></div>
            </div>
            <div class="shopdsr_item">
                <div class="shopdsr_title">发货速度</div>
                <div class="shopdsr_score"><?php echo $store['store_deliverycredit']; ?></div>
            </div>
        </div>
		<div class="sub">
		 <div class="store-logo">
		 	<?php if($store[store_logo] != ''): ?>
		 	<img src="<?php echo $store['store_logo']; ?>" alt="<?php echo $store['store_name']; ?>" title="<?php echo $store['store_name']; ?>">
		 	<?php else: ?>
		 	<img src="/template/pc/rainbow/static/images/default_store_logo.gif" alt="<?php echo $store['store_name']; ?>" title="<?php echo $store['store_name']; ?>">
		 	<?php endif; ?>
		 </div>
		 <!--店铺基本信息 S-->
		<div class="ncs-info">
		  <div class="title">
		    <h4><?php echo $store['store_name']; ?></h4>
		  </div>
		  <div class="content">
		  		<?php if($store[is_own_shop] != 1): ?>
			    <dl class="all-rate">
			      <dt>综合评分：</dt>
			      <dd>
			        <div class="rating"><span style="width: {($store[store_desccredit]+$store[store_servicecredit]+$store[store_deliverycredit])*200/3}%"></span></div>
			        <em><?php echo round(($store[store_desccredit]+$store[store_servicecredit]+$store[store_deliverycredit])/3,1); ?></em>分</dd>
			    </dl>
			    <div class="ncs-detail-rate">
			      <h5><strong>店铺动态评分</strong>与行业相比</h5>
				      <ul>
				           <li> 描述相符<span class="credit"><?php echo number_format($store[store_desccredit],1); ?>分</span>
							   <?php if(number_format($store[store_desccredit],1) > $storeStatistics[store_desccredit_avg]): ?>
								   <span class="high"><i></i>大于<em><?php echo $storeStatistics['store_desccredit_avg']; ?></em></span>
								   <?php elseif(number_format($store[store_desccredit],1) < $storeStatistics[store_desccredit_avg]): ?>
								   <span class="low"><i></i>小于<em><?php echo $storeStatistics['store_desccredit_avg']; ?></em></span>
								   <?php else: ?>
								   <span class="equal"><i></i>持平<em><?php echo $storeStatistics['store_desccredit_avg']; ?></em></span>
							   <?php endif; ?>
						   </li>
				           <li> 服务态度<span class="credit"><?php echo number_format($store[store_servicecredit],1); ?>分</span>
							   <?php if(number_format($store[store_servicecredit],1) > $storeStatistics[store_servicecredit_avg]): ?>
								   <span class="high"><i></i>大于<em><?php echo $storeStatistics['store_servicecredit_avg']; ?></em></span>
								   <?php elseif(number_format($store[store_servicecredit],1) < $storeStatistics[store_servicecredit_avg]): ?>
								   <span class="low"><i></i>小于<em><?php echo $storeStatistics['store_servicecredit_avg']; ?></em></span>
								   <?php else: ?>
								   <span class="equal"><i></i>持平<em><?php echo $storeStatistics['store_servicecredit_avg']; ?></em></span>
							   <?php endif; ?>
						   </li>
				           <li> 发货速度<span class="credit"><?php echo number_format($store[store_deliverycredit],1); ?>分</span>
							   <?php if(number_format($store[store_deliverycredit],1) > $storeStatistics[store_deliverycredit_avg]): ?>
								   <span class="high"><i></i>大于<em><?php echo $storeStatistics['store_deliverycredit_avg']; ?></em></span>
								   <?php elseif(number_format($store[store_deliverycredit],1) < $storeStatistics[store_deliverycredit_avg]): ?>
								   <span class="low"><i></i>小于<em><?php echo $storeStatistics['store_deliverycredit_avg']; ?></em></span>
								   <?php else: ?>
								   <span class="equal"><i></i>持平<em><?php echo $storeStatistics['store_deliverycredit_avg']; ?></em></span>
							   <?php endif; ?>
						   </li>
				      </ul>
			    </div>
			    <?php endif; ?>
		        <dl class="messenger">
		      <dt>联系方式：</dt>
		      <dd><span member_id="2">
		         <?php if(!(empty($store['store_qq']) || (($store['store_qq'] instanceof \think\Collection || $store['store_qq'] instanceof \think\Paginator ) && $store['store_qq']->isEmpty()))): ?>
		        	<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $store['store_qq']; ?>;?>&site=qq&menu=yes" title="QQ:<?php echo $store['store_qq']; ?>"><img border="0" src="http://wpa.qq.com/pa?p=2:<?php echo $store['store_qq']; ?>:52" style=" vertical-align: middle;"/></a>
		         <?php endif; ?>
		      </span></dd>
		    </dl>
		      <!--只有实名认证实体店认证后才显示保障体系 by haoid.cn -->
		      <!--保证金金额-->
			  <!--保障体系 by haoid.cn-->
			<?php if(!(empty($store['company_name']) || (($store['company_name'] instanceof \think\Collection || $store['company_name'] instanceof \think\Paginator ) && $store['company_name']->isEmpty()))): ?>
		    <dl class="no-border">
		        <dt>公司名称：</dt><dd><?php echo $store['company_name']; ?></dd>
		    </dl>
		    <?php endif; ?>
		    <dl>
		        <dt>所&nbsp;在&nbsp;地：</dt>
		        <dd><?php echo $store['store_address']; ?></dd>
		    </dl>
		    <div class="goto"><a href="<?php echo U('Store/index',array('store_id'=>$store[store_id])); ?>" >进入商家店铺</a>
		    	<a href="javascript:collect_store('<?php echo $store[store_id]; ?>')">收藏店铺<em nctype="store_collect"></em></a>
		    </div>
		    <div class="shop-other" id="shop-other" style="display: block">
			    <ul>
			      <li class="ncs-info-btn-map"><a href="javascript:void(0)" class="pngFix"><span>店铺地图</span><b></b><!-- 店铺地图 -->
			        <div class="ncs-info-map" id="map_container" style="width:208px;height:208px;"></div>
			        </a></li>
			      <li class="ncs-info-btn-qrcode"><a href="javascript:void(0)" class="pngFix" style="padding-left: 15px; "><span>店铺二维码</span><b></b>
			        <p class="ncs-info-qrcode"><img img-url="/index.php?m=Home&c=Index&a=qr_code&data=<?php echo $store_url; ?>&head_pic=<?php echo $store_head_pic; ?>&back_img=<?php echo $back_img; ?>" style="width:208px;height:208px"></p>
			        </a></li>
			    </ul>
		    </div>
		  </div>
		</div>

		<!--店铺基本信息 E-->
		<script type="text/javascript">
                    var cityName   = "<?php echo $store['address_region']; ?>"; //店铺详细地址
                    var address    = "<?php echo $store['store_address']; ?>";  //省市区
                    var store_name = "<?php echo $store['store_name']; ?>";  //店铺名
                    function initialize() {
			map = new BMap.Map("map_container");
                        // setPoint(1);
			localCity = new BMap.LocalCity();
			map.enableScrollWheelZoom();
			localCity.get(function(cityResult){
			  if (cityResult) {
			  	var level = cityResult.level;
			  	if (level < 13) level = 13;
                  var lnt = "<?php echo $store['longitude']; ?>";
                  var lat = "<?php echo $store['latitude']; ?>";
                  var poi = new BMap.Point(lnt, lat);//定义一个中心点坐标
			    // map.centerAndZoom(cityResult.center, level);
			    map.centerAndZoom(poi, level);
			    cityResultName = cityResult.name;
			    if (cityResultName.indexOf(cityName) >= 0) cityName = cityResult.name;
			    	    getPoint();
			   	}
			});
                    }

		function loadScript() {
			var script = document.createElement("script");
			script.src = "http://api.map.baidu.com/api?v=1.2&callback=initialize";
			document.body.appendChild(script);
		}
		function getPoint(){
			var myGeo = new BMap.Geocoder();
			myGeo.getPoint(address, function(point){
			  if (point) {
                  console.log(point)
			    setPoint(point);
			  }
			}, cityName);
		}
		function setPoint(point){
			  if (point) {
                  var lnt = "<?php echo $store['longitude']; ?>";
                  var lat = "<?php echo $store['latitude']; ?>";
                  var poi = new BMap.Point(lnt, lat);//定义一个中心点坐标
			    map.centerAndZoom(poi, 16);
			    var marker = new BMap.Marker(poi);
			    map.addOverlay(marker);
			  }
		}

		// 当鼠标放在店铺地图上再加载百度地图。
		$(function(){
			$('.ncs-info-btn-map').one('mouseover',function(){
				loadScript();
                // show_map();
			});
			$('.ncs-info-btn-map').one('click',function(){
				loadScript();
                // show_map();
			});
		});

		//收藏店铺
		function collect_store(store_id){
			if(getCookie('user_id') == ''){
				pop_login();
			}else{
				$.ajax({
					type:'post',
					dataType:'json',
					data:{store_id:store_id},
					url:"<?php echo U('Home/Store/collect_store'); ?>",
					success:function(res){
						if(res.status == 1){
							layer.msg('成功添加至收藏夹', {icon: 1});
						}else{
							layer.msg(res.msg, {icon: 3});
						}
					}
				});
			}
		}

		function pop_login(){
		    layer.open({
		        type: 2,
		        title: '<b>账户登录</b>',
		        skin: 'layui-layer-rim',
		        shadeClose: true,
		        shade: 0.5,
		        area: ['490px', '460px'],
		        content: "<?php echo U('Home/User/pop_login'); ?>",
		    });
		}
		$(function(){  //设定头部颜色
			var local = window.location.href
			$('#nav li').each(function(){
				var href = $(this).find('a').attr('href')
				if(local.indexOf(href)>-1){
					$(this).removeClass('normal')
					$(this).addClass('active')
				}
			})
		});
		</script>
		</div>
	</div>
    <div class="heade_service_info">
        <div class="displayed">
            <i></i>在线客服
            <div class="sub">
			  <div class="ncs-message-bar">
			  	<div class="default">
			    	<h5><a href="<?php echo U('Store/index',array('store_id'=>$store[store_id])); ?>" title="进入店铺"><?php echo $store['store_name']; ?></a></h5>
			    	<span member_id="1"></span>
			    </div>
			    <div class="service-list" store_id="1" store_name="<?php echo $store['store_name']; ?>">
				     <dl>
					<?php if(is_array($store[store_presales]) || $store[store_presales] instanceof \think\Collection || $store[store_presales] instanceof \think\Paginator): $i = 0; $__LIST__ = $store[store_presales];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
				      <dt>售前客服：</dt>
						<dd><span><?php echo $vo['name']; ?></span><span>
							<?php if($vo['type'] == 'qq'): ?>
								<a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $vo['account']; ?>&site=qq&menu=yes" title="QQ:<?php echo $vo['account']; ?>" target="_blank">
									<img border="0" src="http://wpa.qq.com/pa?p=2:<?php echo $vo['account']; ?>:52" alt="点击这里给我发消息" style=" vertical-align: middle;">
								</a>
								<?php else: ?>
								<a href="http://amos1.taobao.com/msg.ww?v=2&amp;uid=<?php echo $vo['account']; ?>&amp;s=2" target="_blank">
									<img border="0" src="/template/pc/rainbow/static/images/T1B7m.XeXuXXaHNz_X-16-16.gif" alt="点击这里给我发消息" style=" vertical-align: middle;">
								</a>
							<?php endif; ?>
			                </span>
						</dd>
					<?php endforeach; endif; else: echo "" ;endif; ?>
					 </dl>
				     <dl>
						 <?php if(is_array($store[store_aftersales]) || $store[store_aftersales] instanceof \think\Collection || $store[store_aftersales] instanceof \think\Paginator): $i = 0; $__LIST__ = $store[store_aftersales];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
							 <dt>售后客服：</dt>
							 <dd><span><?php echo $vo['name']; ?></span><span>
							<?php if($vo['type'] == 'qq'): ?>
								<a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $vo['account']; ?>&site=qq&menu=yes" title="QQ:<?php echo $vo['account']; ?>" target="_blank">
									<img border="0" src="http://wpa.qq.com/pa?p=2:<?php echo $vo['account']; ?>:52" alt="点击这里给我发消息" style=" vertical-align: middle;">
								</a>
								<?php else: ?>
								<a href="http://amos1.taobao.com/msg.ww?v=2&amp;uid=<?php echo $vo['account']; ?>&amp;s=2" target="_blank">
									<img border="0" src="/template/pc/rainbow/static/images/T1B7m.XeXuXXaHNz_X-16-16.gif" alt="点击这里给我发消息" style=" vertical-align: middle;">
								</a>
							<?php endif; ?>
			                </span>
							 </dd>
						 <?php endforeach; endif; else: echo "" ;endif; ?>
				      </dl>
			    </div>
			  </div>
            </div>
        </div>
   </div>
    <div id="search" class="head-search-bar">
	<!--商品和店铺-->
      <!--<ul class="tab">
        <li title="请输入您要搜索的商品关键字" act="search" class="current">商品</li>
        <li title="请输入您要搜索的店铺关键字" act="store_list">店铺</li>
      </ul>-->
      <form class="search-form" method="get" action="<?php echo U('Goods/search'); ?>">
        <input type="hidden" value="search" id="search_act" name="act">
         <input placeholder="请输入您要搜索的商品关键字" name="q" id="q" type="text" class="input-text" value="" maxlength="60" x-webkit-speech lang="zh-CN" onwebkitspeechchange="foo()" x-webkit-grammar="builtin:search" />
        <input type="submit" id="button" value="搜索" class="input-submit">
      </form>
    </div>
  </div>
</div>
<!-- PublicHeadLayout End -->
<link href="/template/pc/rainbow/static/css/shop.css" rel="stylesheet" type="text/css">
<link href="/template/pc/rainbow/static/css/shop_custom.css" rel="stylesheet" type="text/css">
<link href="/template/pc/rainbow/static/style/<?php echo $store['store_theme']; ?>/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/template/pc/rainbow/static/js/shop.js" charset="utf-8"></script>
<div id="header" class="ncs-header"></div>
<div id="store_decoration_content" class="background" style="<?php echo $output['decoration_background_style'];?>">
<?php if(!empty($output['decoration_nav']) && $output['decoration_nav']['display'] == 'true' ) {?>
<style><?php echo $output['decoration_nav']['style'];?></style>
<?php } ?>
<div class="ncsl-nav">
      <?php if(isset($output['decoration_banner'])) { ?>
     <!-- 启用店铺装修 -->
     <?php if($output['decoration_banner']['display'] == 'true') { ?>
     <div id="decoration_banner" class="tpsl-nav-banner">
         <img src="<?php echo $output['decoration_banner']['image'];?>" alt="">
     </div>
     <?php } } else { ?>
    <!-- 不启用店铺装修 -->
	<div class="banner">
		<a href="<?php echo U('Store/index',array('store_id'=>$store[store_id])); ?>" class="img">
       	  <?php if(!empty($store['store_banner'])){?>
	      	<img src="<?php echo $store['store_banner'];?>" alt="<?php echo $store['store_name'];?>" title="<?php echo $store['store_name'];?>" class="pngFix" style="height: 100%;width: 100%">
	      <?php }else{?>
	      <div class="ncs-default-banner"></div>
	      <?php }?>
        </a>
    </div>
    <?php } ?>
    <div id="nav" class="ncs-nav">
      <ul>
        <li <?php if($action == 'index'): ?>class="active" <?php else: ?>class="normal"<?php endif; ?> ><a href="<?php echo U('Store/index',array('store_id'=>$store[store_id])); ?>"><span>店铺首页<i></i></span></a></li>
        <li <?php if($action == 'dynamic'): ?>class="active" <?php else: ?>class="normal"<?php endif; ?> ><a href="<?php echo U('Store/dynamic',array('store_id'=>$store[store_id])); ?>"><span>店铺动态<i></i></span></a></li>
        <?php if(!(empty($link_cat) || (($link_cat instanceof \think\Collection || $link_cat instanceof \think\Paginator ) && $link_cat->isEmpty()))): if(is_array($link_cat) || $link_cat instanceof \think\Collection || $link_cat instanceof \think\Paginator): if( count($link_cat)==0 ) : echo "" ;else: foreach($link_cat as $kl=>$vl): ?>
      		<li class="normal"><a href="<?php echo U('Store/goods_list',array('store_id'=>$store[store_id],'cat_id'=>$vl[cat_id])); ?>"><span><?php echo $vl['cat_name']; ?><i></i></span></a></li>
      		<?php endforeach; endif; else: echo "" ;endif; endif; if(is_array($navigation) || $navigation instanceof \think\Collection || $navigation instanceof \think\Paginator): if( count($navigation)==0 ) : echo "" ;else: foreach($navigation as $kk=>$vv): ?>
        	<li class="normal">
        	<?php if(empty($vv[sn_url])): ?>
        	<a href="<?php echo U('Store/store_news',array('store_id'=>$store[store_id],'sn_id'=>$vv[sn_id])); ?>">
        	<?php else: ?>
        	<a href="<?php echo $vv['sn_url']; ?>">
        	<?php endif; ?>
        	<span><?php echo $vv['sn_title']; ?><i></i></span></a>
        	</li>
      	<?php endforeach; endif; else: echo "" ;endif; ?>
      </ul>
    </div>
  </div>

  <?php if($store['store_decoration_switch'] > 0): ?>
	<script type="text/javascript" src="/template/pc/rainbow/static/js/tp_slider.js" charset="utf-8"></script>
	<script type="text/javascript">
	//图片延迟加载
	(function($) {
	    $.fn.nc_lazyload_init = function() {
	        this.each(function() {
	            $(this).after($(this).val().replace(/src=/gi, 'data-src='));
	        }).remove();
	    };
	})(jQuery);
	
	//图片延迟加载
	(function($) {
	    $.fn.nc_lazyload = function() {
	        var lazy_items = [];
	        this.each(function() {
	            if($(this).attr("data-src") !== undefined){
	                var lazy_item = {
	                    object: $(this),
	                    url: $(this).attr("data-src")
	                };
	                lazy_items.push(lazy_item);
	            }
	        });
	
	        var load_img = function() {
	            var window_height = $(window).height();
	            var scroll_top = $(window).scrollTop();
	
	            $.each(lazy_items, function(i, lazy_item) {
	                if(lazy_item.object) {
	                    item_top = lazy_item.object.offset().top - scroll_top;
	                    if(item_top >= 0 && item_top < window_height) {
	                        if(lazy_item.url) {
	                            lazy_item.object.attr("src",lazy_item.url);
	                        }
	                        lazy_item.object = null;
	                    }
	                }
	            });
	        };
	        load_img();
	        $(window).bind("scroll", load_img);
	    };
	})(jQuery);
	</script>
	<div id="store_decoration_area" class="store-decoration-page">
	<textarea class="lazyload_container" rows="10" cols="30" style="display:none;">
	    <?php if(is_array($output[block_list]) || $output[block_list] instanceof \think\Collection || $output[block_list] instanceof \think\Paginator): if( count($output[block_list])==0 ) : echo "" ;else: foreach($output[block_list] as $key=>$block): ?>
	      	<div id="block_<?php echo $block[block_id]; ?>" data-block-id="<?php echo $block[block_id]; ?>" nctype="store_decoration_block" 
class="ncsc-decration-block store-decoration-block-1 <?php if($block[block_full_width] == 1): ?>store-decoration-block-full-width<?php endif; ?> tip" title="<?php echo $block_title; ?>">
    <div nctype="store_decoration_block_content" class="ncsc-decration-block-content store-decoration-block-1-content">
        <div nctype="store_decoration_block_module" class="store-decoration-block-1-module">
            <?php if($block[block_module_type] == 'html'): 
					$block = empty($block) ? $output['block'] : $block;
					$block_content = $block['block_content'];
				?>
            	<?php echo html_entity_decode($block_content);elseif($block[block_module_type] == 'slide'): $block_content = unserialize($block['block_content']);?>
				<ul nctype="store_decoration_slide" style="height:<?php echo $block_content['height']; ?>px; overflow:hidden;">
				    <?php if(is_array($block_content['images']) || $block_content['images'] instanceof \think\Collection || $block_content['images'] instanceof \think\Paginator): if( count($block_content['images'])==0 ) : echo "" ;else: foreach($block_content['images'] as $key=>$value): ?>
					    <li data-image-name="<?php echo $value['image_name']; ?>" data-image-url="<?php echo $value[image_url]; ?>" data-image-link="<?php echo $value['image_link']; ?>" style="height:<?php echo $block_content['height']; ?>px; background: url(<?php echo $value[image_url]; ?>) no-repeat scroll center top transparent;">
					    	<a href="<?php echo $value['image_link']; ?>" target="_blank" style="display:block;width:100%;height:100%;"></a>
					    </li>
				    <?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			<?php elseif($block[block_module_type] == 'goods'): 
   				$block_content = empty($block_content) ? $output['block_content'] : $block_content; 
    			$goods_list = unserialize($block['block_content']);
			if(!(empty($goods_list) || (($goods_list instanceof \think\Collection || $goods_list instanceof \think\Paginator ) && $goods_list->isEmpty()))): ?>
			<ul class="goods-list">
			  <?php if(is_array($goods_list) || $goods_list instanceof \think\Collection || $goods_list instanceof \think\Paginator): if( count($goods_list)==0 ) : echo "" ;else: foreach($goods_list as $key=>$val): ?>
			  <li nctype="goods_item" data-goods-id="<?php echo $val['goods_id']; ?>" data-goods-name="<?php echo $val['goods_name']; ?>" data-goods-price="<?php echo $val['shop_price']; ?>"  data-goods-image="<?php echo $val['goods_image']; ?>">
			    <div class="goods-thumb"> 
			    	<a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$val[goods_id])); ?>" target="_blank" title="<?php echo $val['goods_name']; ?>"> 
			    	<img src="<?php echo goods_thum_images($val['goods_id'],240,240); ?>" alt="<?php echo $val['goods_name']; ?>"> </a> 
			    </div>
			    <dl class="goods-info">
			      <dt><a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$val[goods_id])); ?>" target="_blank" title="<?php echo $val['goods_name']; ?>"><?php echo $val['goods_name']; ?></a></dt>
			      <dd>¥<?php echo $val['shop_price']; ?></dd>
			    </dl>
			  </li>
			  <?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
			<div style="text-align: center; display: block; padding: 15px 0; margin: 0!important;" id="page_list"><?php echo $show_page; ?></div>
			<?php endif; elseif($block[block_module_type] == 'hot_area'): $block_content = unserialize($block['block_content']);?>
				<div>
				    <?php $hot_area_flag = str_replace('.', '',$block_content['image']);?>
				    <img data-image-name="<?php echo $block_content['image'];?>" usemap="#<?php echo $hot_area_flag;?>" src="<?php echo $block_content['image_url'];?>" alt="<?php echo $block_content['image'];?>">
				    <map name="<?php echo $hot_area_flag;?>" id="<?php echo $hot_area_flag;?>">
				        <?php if(!empty($block_content['areas']) && is_array($block_content['areas'])) {foreach($block_content['areas'] as $value) {?>
				        <area target="_blank" shape="rect" coords="<?php echo $value['x1'];?>,<?php echo $value['y1'];?>,<?php echo $value['x2'];?>,<?php echo $value['y2'];?>" href ="<?php echo $value['link'];?>" alt="<?php echo $value['link'];?>" />
				        <?php } } ?>
				    </map>
				</div>
			<?php else: endif; ?>
        </div>
        <?php if($control_flag == 1): ?>
        	<a class="edit" nctype="btn_edit_module" data-module-type="<?php echo $block['block_module_type']; ?>" href="javascript:;" data-block-id="<?php echo $block[block_id]; ?>"><i class="icon-edit"></i>编辑模块</a>
        <?php endif; ?>
    </div>
    <?php if($control_flag == 1): ?>
    	<a class="delete" nctype="btn_del_block" href="javascript:;" data-block-id="<?php echo $block[block_id]; ?>" title="删除该布局块"><i class="icon-trash"></i>删除布局块</a>    
    <?php endif; ?>
</div>
	    <?php endforeach; endif; else: echo "" ;endif; ?>
	</textarea>
	</div>
	<script type="text/javascript">
	    $(document).ready(function(){
	        //图片延迟加载
	        $(".lazyload_container").nc_lazyload_init();
	        $("img").nc_lazyload();
	        //幻灯片
	        $('[nctype="store_decoration_slide"]').nc_slider();
	    });
	</script>
  <?php endif; if(!(empty($coupon) || (($coupon instanceof \think\Collection || $coupon instanceof \think\Paginator ) && $coupon->isEmpty()))): ?>
  	<style>
	.banneryhq-lis{width: 1200px;height: 178px;margin: 0 auto;background: white;}
	.thcoupon3 ul li{
		float: left;background: url(/template/pc/rainbow/static/images/roc_10.jpg) no-repeat;
		width: 303px;height: 140px;padding: 0 18px;margin-left: 2px;box-sizing: border-box;
		-webkit-box-sizing: border-box;-moz-box-sizing: border-box;-o-box-sizing: border-box;-ms-box-sizing: border-box;
	}
	.thcoupon3 ul{overflow: hidden;padding: 3px 37px;}
	.thcoupon3 ul li:first-child{margin-left: 0;}
	.shogetco{float: left;margin-left: 40px;}
	.thcoupon3{float: left;}
	.sewn{font-family:"黑体";color: white;float: left;padding: 24px 0;}
	.sewn em{font-size: 30px;font-style: normal;}
	.sewn span{font-size: 80px;}
	.bunget{float: right;font-family:"黑体";padding: 35px 0;}
	.bunget span{font-size: 28px;display: block;}
	.bunget a{
		font-size: 22px;color: #1ea5a8;display: block;padding: 2px 10px;background: white;border-radius: 16px;margin-top: 8px;
		transition: all .2s linear 0s; -webkit-transition: all .2s linear 0s;-moz-transition: all .2s linear 0s;
	}
	.bunget a:hover{
		box-shadow: 0px 3px 6px #aaaaaa;-webkit-box-shadow: 0px 3px 6px #aaaaaa;-moz-box-shadow: 0px 3px 6px #aaaaaa;-o-box-shadow: 0px 3px 6px #aaaaaa;
	}
	.bunget a i{font: 400 30px/20px consolas;margin-left: 10px;}
	</style>
	<div class="banneryhq-lis">
		<img class="shogetco" src="/template/pc/rainbow/static/images/lis.png"/>
		<div class="thcoupon3">
			<ul>
				<?php if(is_array($coupon) || $coupon instanceof \think\Collection || $coupon instanceof \think\Paginator): if( count($coupon)==0 ) : echo "" ;else: foreach($coupon as $key=>$cp): ?>
				<li>
					<div class="sewn">
						<em>￥</em>
						<span><?php echo intval($cp['money']); ?></span>
					</div>
					<div class="bunget">
						<span>满<?php echo intval($cp['condition']); ?>可用</span>
						<a href="<?php echo U('Activity/get_coupon',array('id'=>$cp[id])); ?>">点击领取<i>></i></a>
					</div>
				</li>
				<?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</div>
   </div>
  <?php endif; if(($store[store_decoration_only] == 0) OR ($store[store_decoration_switch] == 0)): ?>
  <div class="wrapper mt10">
  <div class="ncs-main">
    <div class="flexslider">
      <ul class="slides">
      	 <?php if(is_array($store[store_slide_list]) || $store[store_slide_list] instanceof \think\Collection || $store[store_slide_list] instanceof \think\Paginator): if( count($store[store_slide_list])==0 ) : echo "" ;else: foreach($store[store_slide_list] as $key=>$vimg): if(!(empty($vimg['store_slide_img']) || (($vimg['store_slide_img'] instanceof \think\Collection || $vimg['store_slide_img'] instanceof \think\Paginator ) && $vimg['store_slide_img']->isEmpty()))): ?>
         	<li><a href="<?php echo $vimg['store_slide_url']; ?>"><img src="<?php echo $vimg['store_slide_img']; ?>"></a></li>
         	<?php endif; endforeach; endif; else: echo "" ;endif; ?>
      </ul>
    </div>
    <div class="ncs-main-container">
      <div class="title"> <span><a href="<?php echo U('Store/goods_list',array('store_id'=>$store[store_id],'key'=>'is_recommend')); ?>" class="more">更多</a></span>
        <h4>推荐商品</h4>
      </div>
      <div class="content ncs-goods-list">
      	<?php if(!(empty($recomend_goods) || (($recomend_goods instanceof \think\Collection || $recomend_goods instanceof \think\Paginator ) && $recomend_goods->isEmpty()))): ?>
        	<ul>
	        <?php if(is_array($recomend_goods) || $recomend_goods instanceof \think\Collection || $recomend_goods instanceof \think\Paginator): if( count($recomend_goods)==0 ) : echo "" ;else: foreach($recomend_goods as $key=>$vo): ?>
	           <li>
	            <dl>
	              <dt><a href="<?php echo U('Goods/goodsInfo',array('id'=>$vo[goods_id],'store_id'=>$store['store_id'])); ?>" class="goods-thumb" target="_blank"><img src="<?php echo goods_thum_images($vo['goods_id'],240,240); ?>" alt="<?php echo $vo['goods_name']; ?>"/></a>
	                <ul class="goods-thumb-scroll-show">
	                <?php $i = '0'; if(is_array($goods_images) || $goods_images instanceof \think\Collection || $goods_images instanceof \think\Paginator): if( count($goods_images)==0 ) : echo "" ;else: foreach($goods_images as $k2=>$v2): if($v2[goods_id] == $vo[goods_id]): ?>
	                   	  <li <?php if($i == 0): ?>class="selected"<?php endif; ?>><a href="javascript:void(0); rel=<?php echo $i++; ?>"><img src="<?php echo $v2[image_url]; ?>"/></a></li>
	                   <?php endif; endforeach; endif; else: echo "" ;endif; ?>
	                 </ul>
	              </dt>
	              <dd class="goods-name"><a href="<?php echo U('Goods/goodsInfo',array('id'=>$vo[goods_id],'store_id'=>$store['store_id'])); ?>" title="<?php echo $vo['goods_name']; ?>" target="_blank"><?php echo getSubstr($vo['goods_name'],0,30); ?></a></dd>
	              <dd class="goods-info"><span class="price"><i>&yen;</i> <?php echo $vo['shop_price']; ?> </span> <span class="goods-sold">已售：<strong><?php echo $vo['sales_sum']; ?></strong> 件</span></dd>
	              <?php if($vo[prom_type] > 0): ?>
		              <dd class="goods-promotion">
		              		<span><?php if($vo[prom_type] == 1): ?>抢购商品<?php endif; if($vo[prom_type] == 2): ?>团购商品<?php endif; if($vo[prom_type] == 3): ?>限时折扣<?php endif; ?></span>
		              </dd>
	              <?php endif; ?>
	            </dl>
	          </li>
			<?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
         <?php else: ?>
         <div class="nothing"><p>很抱歉! 没有找到相关商品</p></div>   
         <?php endif; ?>   
        </div>
    </div>
    <div class="ncs-main-container">
      <div class="title"><span><a href="<?php echo U('Store/goods_list',array('store_id'=>$store[store_id],'key'=>'on_time')); ?>" class="more">更多</a></span>
        <h4>新品</h4>
      </div>
      <div class="content ncs-goods-list">
      		<?php if(!(empty($new_goods) || (($new_goods instanceof \think\Collection || $new_goods instanceof \think\Paginator ) && $new_goods->isEmpty()))): ?>
            <ul>
			<?php if(is_array($new_goods) || $new_goods instanceof \think\Collection || $new_goods instanceof \think\Paginator): if( count($new_goods)==0 ) : echo "" ;else: foreach($new_goods as $key=>$vo): ?>
	           <li>
	            <dl>
	              <dt><a href="<?php echo U('Goods/goodsInfo',array('id'=>$vo[goods_id],'store_id'=>$store['store_id'])); ?>" class="goods-thumb" target="_blank"><img src="<?php echo goods_thum_images($vo['goods_id'],240,240); ?>" alt="<?php echo $vo['goods_name']; ?>"/></a>
	                <ul class="goods-thumb-scroll-show">
	                <?php $i = '0'; if(is_array($goods_images) || $goods_images instanceof \think\Collection || $goods_images instanceof \think\Paginator): if( count($goods_images)==0 ) : echo "" ;else: foreach($goods_images as $k2=>$v2): if($v2[goods_id] == $vo[goods_id]): ?>
	                   	  <li <?php if($i == 0): ?>class="selected"<?php endif; ?>><a href="javascript:void(0); rel=<?php echo $i++; ?>"><img src="<?php echo $v2[image_url]; ?>"/></a></li>
	                   <?php endif; endforeach; endif; else: echo "" ;endif; ?>
	                 </ul>
	              </dt>
	              <dd class="goods-name"><a href="<?php echo U('Goods/goodsInfo',array('id'=>$vo[goods_id],'store_id'=>$store['store_id'])); ?>" title="<?php echo $vo['goods_name']; ?>" target="_blank"><?php echo getSubstr($vo['goods_name'],0,30); ?></a></dd>
	              <dd class="goods-info"><span class="price"><i>&yen;</i> <?php echo $vo['shop_price']; ?> </span> <span class="goods-sold">已售：<strong><?php echo $vo['sales_sum']; ?></strong> 件</span></dd>
	              <?php if($vo[prom_type] > 0): ?>
		              <dd class="goods-promotion">
		              		<span><?php if($vo[prom_type] == 1): ?>抢购商品<?php endif; if($vo[prom_type] == 2): ?>团购商品<?php endif; if($vo[prom_type] == 3): ?>限时折扣<?php endif; ?></span>
		              </dd>
	              <?php endif; ?>
	            </dl>
	          </li>
			<?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
            <?php else: ?>
            <div class="nothing"><p>很抱歉! 没有找到相关商品</p></div>
            <?php endif; ?>
       </div>
    </div>
  </div>
  <div class="ncs-sidebar">
    
	<div class="ncs-sidebar-container ncs-class-bar">
	  <div class="title">
	    <h4>商品分类</h4>
	  </div>
	  <div class="content">
	    <p>
		    <span><a href="<?php echo U('Store/goods_list',array('store_id'=>$store[store_id],'key'=>'on_time')); ?>">新品</a></span>
		    <span><a href="<?php echo U('Store/goods_list',array('store_id'=>$store[store_id],'key'=>'shop_price')); ?>">价格</a></span>
		    <span><a href="<?php echo U('Store/goods_list',array('store_id'=>$store[store_id],'key'=>'sales_sum')); ?>">销量</a></span>
		    <span><a href="<?php echo U('Store/goods_list',array('store_id'=>$store[store_id],'key'=>'comment_count')); ?>">人气</a></span>
	    </p>
	    <div class="ncs-search">
	      <form id="" name="searchShop" method="get" action="<?php echo U('Home/Store/goods_list'); ?>" >
	        <input type="hidden" name="store_id" value="<?php echo $store['store_id']; ?>" />
	        <input type="text" class="text w120" name="keyword" value="" placeholder="搜索店内商品">
	        <a href="javascript:document.searchShop.submit();" class="ncs-btn">搜索</a>
	      </form>
	    </div>
	    <ul class="ncs-submenu">
	      <li><span class="ico-none"><em>-</em></span><a href="<?php echo U('Store/goods_list',array('store_id'=>$store[store_id])); ?>">全部商品</a></li>
	      <?php if(is_array($main_cat) || $main_cat instanceof \think\Collection || $main_cat instanceof \think\Paginator): if( count($main_cat)==0 ) : echo "" ;else: foreach($main_cat as $key=>$vo): ?>
	      <li><span class="ico-none"  onclick="class_list(this);" span_id="<?php echo $vo['cat_id']; ?>"><em>-</em></span>
	      		<a href="<?php echo U('Store/goods_list',array('store_id'=>$store[store_id],'cat_id'=>$vo[cat_id])); ?>"><?php echo $vo['cat_name']; ?></a>
	      		<?php if($sub_cat[$vo[cat_id]] != ''): ?>
	      		<ul id="stc_<?php echo $vo['cat_id']; ?>" style="display: block;">
	      			<?php if(is_array($sub_cat[$vo[cat_id]]) || $sub_cat[$vo[cat_id]] instanceof \think\Collection || $sub_cat[$vo[cat_id]] instanceof \think\Paginator): if( count($sub_cat[$vo[cat_id]])==0 ) : echo "" ;else: foreach($sub_cat[$vo[cat_id]] as $key=>$v2): ?>
	                <li><span class="ico-sub">&nbsp;</span><a href="<?php echo U('Store/goods_list',array('store_id'=>$store[store_id],'cat_id'=>$v2[cat_id])); ?>"><?php echo $v2['cat_name']; ?></a></li>
	                <?php endforeach; endif; else: echo "" ;endif; ?>
	            </ul>
	            <?php endif; ?>
	      </li>
	      <?php endforeach; endif; else: echo "" ;endif; ?>
	    </ul> 
	  </div>
	</div>

	<div class="ncs-sidebar-container ncs-top-bar">
	  <div class="title">
	    <h4>商品排行</h4>
	  </div>
	  <div class="content">
	    <ul class="ncs-top-tab pngFix">
	      <li id="hot_sales_tab" class="current"><a href="<?php echo U('Store/goods_list',array('store_id'=>$store[store_id],'key'=>'sales_sum')); ?>">热销商品排行</a></li>
	      <li id="hot_collect_tab"><a href="<?php echo U('Store/goods_list',array('store_id'=>$store[store_id],'key'=>'collect_sum')); ?>">收藏商品排行</a></li>
	    </ul>
	    <div id="hot_sales_list" class="ncs-top-panel">
	        <ol>
	        <?php if(is_array($hot_goods) || $hot_goods instanceof \think\Collection || $hot_goods instanceof \think\Paginator): if( count($hot_goods)==0 ) : echo "" ;else: foreach($hot_goods as $key=>$vo): ?>
	         <li>
	          <dl>
	            <dt><a href="<?php echo U('Goods/goodsInfo',array('id'=>$vo[goods_id],'store_id'=>$store['store_id'])); ?>"><?php echo getSubstr($vo['goods_name'],0,30); ?></a></dt>
	            <dd class="goods-pic"><a href="<?php echo U('Goods/goodsInfo',array('id'=>$vo[goods_id],'store_id'=>$store['store_id'])); ?>"><span class="thumb size40"><i></i><img src="<?php echo goods_thum_images($vo['goods_id'],60,60); ?>" style="width: 100%;height: 100%;"  onload="javascript:DrawImage(this,40,40);"></span></a>
	              <p><span class="thumb size100"><i></i><img src="<?php echo goods_thum_images($vo['goods_id'],240,240); ?>" style="width: 100%;height: 100%;" onload="javascript:DrawImage(this,100,100);" title="<?php echo $vo['goods_name']; ?>"><big></big><small></small></span></p>
	            </dd>
	            <dd class="price pngFix"><?php echo $vo['shop_price']; ?></dd>
	            <dd class="selled pngFix">售出：<strong><?php echo $vo['sales_sum']; ?></strong>笔</dd>
	          </dl>
	         </li>
	        <?php endforeach; endif; else: echo "" ;endif; ?>
	       </ol>
	    </div>
	    <div id="hot_collect_list" class="ncs-top-panel hide">
	        <ol>
	        	<?php if(is_array($collect_goods) || $collect_goods instanceof \think\Collection || $collect_goods instanceof \think\Paginator): if( count($collect_goods)==0 ) : echo "" ;else: foreach($collect_goods as $key=>$vo): ?>
		        <li>
		          <dl>
		            <dt><a href="<?php echo U('Goods/goodsInfo',array('id'=>$vo[goods_id],'store_id'=>$store['store_id'])); ?>"><?php echo getSubstr($vo['goods_name'],0,30); ?></a></dt>
		            <dd class="goods-pic"><a href="<?php echo U('Goods/goodsInfo',array('id'=>$vo[goods_id],'store_id'=>$store['store_id'])); ?>" title=""><span class="thumb size40"><i></i> <img src="<?php echo goods_thum_images($vo['goods_id'],60,60); ?>" style="width: 100%;height: 100%;" onload="javascript:DrawImage(this,40,40);"></span></a>
		              <p><span class="thumb size100"><i></i><img src="<?php echo goods_thum_images($vo['goods_id'],240,240); ?>" style="width: 100%;height: 100%;"  onload="javascript:DrawImage(this,100,100);" title="<?php echo $vo['goods_name']; ?>"><big></big><small></small></span></p>
		            </dd>
		            <dd class="price pngFix"><?php echo $vo['shop_price']; ?></dd>
		            <dd class="collection pngFix">收藏人气：<strong><?php echo $vo['collect_sum']; ?></strong></dd>
		          </dl>
		        </li>
		        <?php endforeach; endif; else: echo "" ;endif; ?>
	         </ol>
	    </div>
	    <p><a href="<?php echo U('Store/goods_list',array('store_id'=>$store[store_id])); ?>">查看本店其他商品</a></p>
	  </div>
	</div>
	<script type="text/javascript">
	    $(document).ready(function(){
	        //热销排行切换
	        $('#hot_sales_tab').on('mouseenter', function() {
	            $(this).addClass('current');
	            $('#hot_collect_tab').removeClass('current');
	            $('#hot_sales_list').removeClass('hide');
	            $('#hot_collect_list').addClass('hide');
	        });
	        $('#hot_collect_tab').on('mouseenter', function() {
	            $(this).addClass('current');
	            $('#hot_sales_tab').removeClass('current');
	            $('#hot_sales_list').addClass('hide');
	            $('#hot_collect_list').removeClass('hide');
	        });
	    });
	    /** left.php **/
	    // 商品分类
	    function class_list(obj){
	    	var stc_id=$(obj).attr('span_id');
	    	var span_class=$(obj).attr('class');
	    	if(span_class=='ico-block') {
	    		$("#stc_"+stc_id).show();
	    		$(obj).html('<em>-</em>');
	    		$(obj).attr('class','ico-none');
	    	}else{
	    		$("#stc_"+stc_id).hide();
	    		$(obj).html('<em>+</em>');
	    		$(obj).attr('class','ico-block');
	    	}
	    }
	</script> 
  </div>
</div>
<?php endif; ?>
<!-- 引入幻灯片JS --> 
<script type="text/javascript" src="/template/pc/rainbow/static/js/jquery.flexslider-min.js"></script> 
<!-- 绑定幻灯片事件 --> 
<script type="text/javascript">
	$(window).load(function() {
		$('.flexslider').flexslider();
	    // 图片切换效果
	    $('.goods-thumb-scroll-show').find('a').mouseover(function(){
	        $(this).parents('li:first').addClass('selected').siblings().removeClass('selected');
	        var _src = $(this).find('img').attr('src');
	        _src = _src.replace('_60.', '_240.');
	        $(this).parents('dt').find('.goods-thumb').find('img').attr('src', _src);
	    });
	});
</script>
  <div class="clear">&nbsp;</div>
</div>
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
