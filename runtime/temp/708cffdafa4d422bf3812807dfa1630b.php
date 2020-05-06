<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:40:"./template/pc/rainbow/store/dynamic.html";i:1587634422;s:75:"/home/wwwroot/testshop.kingdeepos.com/template/pc/rainbow/store/header.html";i:1587634424;s:82:"/home/wwwroot/testshop.kingdeepos.com/template/pc/rainbow/public/sidebar_cart.html";i:1587634420;s:76:"/home/wwwroot/testshop.kingdeepos.com/template/pc/rainbow/public/footer.html";i:1587634420;}*/ ?>
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

<script type="text/javascript" src="/public/js/seller/member.js" charset="utf-8"></script>
<script src="/public/js/pc_common.js" charset="utf-8"></script>
<script>
var MAX_RECORDNUM = 20;
</script>
<div class="cms-sns">
  <div class="cms-sns-left">
    <div class="cms-sns-tabmene">
      <ul>
        <li><a href="<?php echo U('Store/dynamic',array('store_id'=>$store[store_id])); ?>" id="all">全部动态<i></i></a></li>
        <li><a href="<?php echo U('Store/dynamic',array('store_id'=>$store[store_id],'type'=>'prom')); ?>" id="prom">促销<i></i></a></li>
        <li><a href="<?php echo U('Store/dynamic',array('store_id'=>$store[store_id],'type'=>'is_new')); ?>" id="is_new">新品<i></i></a></li>
        <li><a href="<?php echo U('Store/dynamic',array('store_id'=>$store[store_id],'type'=>'is_hot')); ?>" id="is_hot">热卖<i></i></a></li>
        <li><a href="<?php echo U('Store/dynamic',array('store_id'=>$store[store_id],'type'=>'is_recommend')); ?>" id="is_recommend">店主推荐<i></i></a></li>
      </ul>
    </div>
      <script>
          $(function(){
              var type = "<?php echo \think\Request::instance()->param('type'); ?>"
              if(type == '')type='all';
              $('#'+type).addClass('selected').parent('li').siblings('li').removeClass('selected');
          })
      </script>
    <div class="cms-sns-content">
            <ul class="cms-sns-content-list">
            <?php if(is_array($goods_list) || $goods_list instanceof \think\Collection || $goods_list instanceof \think\Paginator): if( count($goods_list)==0 ) : echo "" ;else: foreach($goods_list as $key=>$vo): ?>
                <li nc_type="tracerow_4">
          <dl>
            <dt><h5><?php echo getSubstr($vo['goods_remark'],0,30); ?></h5></dt>
            <dd> <div class="fd-media">
					<div class="goodsimg"><a target="_blank" href="<?php echo U('Goods/goodsInfo',array('id'=>$vo[goods_id])); ?>">
					<img src="<?php echo goods_thum_images($vo['goods_id'],240,240); ?>" onload="javascript:DrawImage(this,120,120);" alt=""></a></div>
    					<div class="goodsinfo">
    					<dl>
    					   <dt>
                              <!--<i class="desc-type desc-type-new">新品发布</i>-->
                              <a target="_blank" href="<?php echo U('Goods/goodsInfo',array('id'=>$vo[goods_id])); ?>"><?php echo getSubstr($vo['goods_name'],0,30); ?></a>
                           </dt>
							<dd>价&nbsp;&nbsp;格：&yen;<?php echo $vo['shop_price']; ?></dd>
							<dd>市场价：&yen;<?php echo $vo['market_price']; ?></dd>
	                  		<dd nctype="collectbtn_232"><a href="javascript:void(0);" onclick="collect_goods('<?php echo $vo['goods_id']; ?>');">收藏该宝贝</a></dd>
	                  	</dl>
	                  </div>
	             </div> </dd>
            <dd> <span class="goods-time fl"><?php echo date('Y-m-d H:i',$vo['on_time']); ?></span>
               <!-- <span class="fr">
            <a href="javascript:void(0);" nc_type="sd_forwardbtn" data-param='{"txtid":"4"}'>转发</a>&nbsp;|&nbsp;<a href="javascript:void(0);" nc_type="sd_commentbtn" data-param='{"txtid":"4"}'>评论</a>
                </span> -->
            </dd>
            <dd> 
              <!-- 评论模块start -->
              <div id="tracereply_4" style="display:none;"></div>
              <!-- 评论模块end --> 
              <!-- 转发模块start -->
              <div id="forward_4" style="display:none;">
                <div class="forward-widget">
                  <div class="forward-edit">
                    <form id="forwardform_4" method="post" action="index.php?act=store_snshome&op=addforward">
                      <input type="hidden" name="stid" value="4"/>
                      <div class="forward-add">
                        <textarea resize="none" id="content_forward4" name="forwardcontent"></textarea>
                        <span class="error"></span> 
                        <!-- 验证码 -->
                        <div id="forwardseccode4" class="seccode" style="display: none;">
                          <label for="captcha">验证码：</label>
                          <input name="captcha" class="text" type="text" size="4" maxlength="4"/>
                          <img src="" title="点击更换验证码" name="codeimage" onclick="this.src='index.php?act=seccode&op=makecode&nchash=97c3fab3&t=' + Math.random()"/> <span>您的操作过度频繁，请输入验证码后继续。</span>
                          <input type="hidden" name="nchash" value="97c3fab3"/>
                        </div>
                        <input type="text" style="display:none;" />
                        <!-- 防止点击Enter键提交 -->
                        <div class="act"> <span class="skin-blue"><span class="btn"><a href="javascript:void(0);" nc_type="s_forwardbtn" data-param='{"txtid":"4"}'>转发</a></span></span> <span id="forwardcharcount4" style="float:right;"></span> <a class="face" nc_type="smiliesbtn" data-param='{"txtid":"forward4"}' href="javascript:void(0);" >表情</a> </div>
                      </div>
                    </form>
                  </div>
                  <ul class="forward-list">
                  </ul>
                </div>
              </div>
              <!-- 转发模块end -->
              <div class="clear"></div>
            </dd>
          </dl>
        </li>
        <?php endforeach; endif; else: echo "" ;endif; ?>
              </ul>
                  <div id="pagehtml" class="tc mt10 mb10">
                        <?php echo $page_show; ?>
                  </div>
                  <style>
                      select{
                          height: 36px !important;
                          line-height: 36px !important;
                          padding: 0 12px;
                          border: unset;
                      }
                      .dataTables_paginate .pagination {
                          margin-right: 0;
                      }
                      .dataTables_paginate li a{
                          border-color: unset;
                          border: unset;
                      }
                      .dataTables_paginate .pagination li {
                          margin: 0 3px;
                          box-sizing: border-box;
                          display: block;
                      }


                  </style>
          </div>
    <!-- 表情弹出层 -->
    <div id="smilies_div" class="smilies-module"></div>
  </div>
  <div class="cms-sns-right">
    <div class="cms-sns-right-container">
      <div class="cms-store-pic"><a href="<?php echo U('home/Store/index',['store_id'=>$store[store_id]]); ?>"><img src="<?php echo (isset($store['store_logo']) && ($store['store_logo'] !== '')?$store['store_logo']:'/template/pc/rainbow/static/images/z-icon.png'); ?>" alt="<?php echo $store['store_name']; ?>" title="<?php echo $store['store_name']; ?>" /></a></div>
      <dl class="cms-store-info">
        <dt><?php echo $store['store_name']; ?></dt>
        <dd>已收藏：<em nctype="store_collect"><?php echo $store['store_collect']; ?></em></dd>
      </dl>
      <div class="cms-store-favorites"><a href="javascript:collect_store('<?php echo $store[store_id]; ?>')"><i class="icon-plus"></i>收藏店铺</a></div>
    </div>
    <div class="cms-sns-right-container">
            <div class="title">最新收藏用户</div>
      <div class="cms-favorites-user">
        <ul>
        	<?php if(is_array($store_collect) || $store_collect instanceof \think\Collection || $store_collect instanceof \think\Paginator): if( count($store_collect)==0 ) : echo "" ;else: foreach($store_collect as $key=>$vc): ?>
                <li>
                    <a >
                        <img alt="<?php echo $vc['user_name']; ?>" title="<?php echo $vc['user_name']; ?>" src="<?php echo (isset($user_arr[$vc[user_id]]) && ($user_arr[$vc[user_id]] !== '')?$user_arr[$vc[user_id]]:'/template/pc/rainbow/static/images/default_user_portrait.gif'); ?>" />
                    </a>
                </li>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
      </div>
    </div>
      </div>
</div>
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
<script type="text/javascript">
function fade() {
	$("img[rel='lazy']").each(function () {
		var $scroTop = $(this).offset();
		if ($scroTop.top <= $(window).scrollTop() + $(window).height()) {
			$(this).hide();
			$(this).attr("src", $(this).attr("data-url"));
			$(this).removeAttr("rel");
			$(this).removeAttr("name");
			$(this).fadeIn(500);
		}
	});
}
if($("img[rel='lazy']").length > 0) {
	$(window).scroll(function () {
		fade();
	});
};
fade();
</script>
<script type="text/javascript">
$(function(){
	$('a[nctype="search_in_store"]').click(function(){
		if ($('#keyword').val() == '') {
			return false;
		}
		$('#search_act').val('show_store');
		$('<input type="hidden" value="1" name="store_id" /> <input type="hidden" name="op" value="goods_all" />').appendTo("#formSearch");
		$('#formSearch').submit();
	});
	$('a[nctype="search_in_shop"]').click(function(){
		if ($('#keyword').val() == '') {
			return false;
		}
		$('#formSearch').submit();
	});
	$('#keyword').css("color","#999999");

	var storeTrends	= true;
	$('.favorites').mouseover(function(){
		var $this = $(this);
		if(storeTrends){
			$.getJSON('index.php?act=show_store&op=ajax_store_trend_count&store_id=1', function(data){
				$this.find('li:eq(2)').find('a').html(data.count);
				storeTrends = false;
			});
		}
	});

	$('a[nctype="share_store"]').click(function(){
		login_dialog();
	});
});
</script>
</body>
</html>