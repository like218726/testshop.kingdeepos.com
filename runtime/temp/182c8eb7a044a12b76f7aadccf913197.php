<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:41:"./application/admin/view/index\index.html";i:1588218716;s:70:"D:\www\testshop.kingdeepos.com\application\admin\view\public\left.html";i:1588218727;}*/ ?>
<!doctype html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<!-- Apple devices fullscreen -->
<meta name="apple-mobile-web-app-capable" content="yes">
<!-- Apple devices fullscreen -->
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<link rel="shortcut icon" type="image/x-icon" href="<?php echo (isset($tpshop_config['shop_info_store_ico']) && ($tpshop_config['shop_info_store_ico'] !== '')?$tpshop_config['shop_info_store_ico']:'/public/static/images/logo/storeico_default.png'); ?>" media="screen"/>
<title><?php echo $tpshop_config['shop_info_company_name']; ?></title>
<script type="text/javascript">
var SITEURL = window.location.host +'/index.php/admin';
</script>
<link href="/public/static/css/main.css" rel="stylesheet" type="text/css">
<link href="/public/static/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css">
<link href="/public/static/font/css/font-awesome.min.css" rel="stylesheet" />
<script type="text/javascript" src="/public/static/js/jquery.js"></script>
<script type="text/javascript" src="/public/static/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="/public/static/js/dialog/dialog.js" id="dialog_js"></script>
<script type="text/javascript" src="/public/static/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/public/static/js/admincp.js"></script>
<script type="text/javascript" src="/public/static/js/jquery.validation.min.js"></script>
<script src="/public/js/layer/layer.js"></script><!--弹窗js 参考文档 http://layer.layui.com/-->
<script src="/public/js/upgrade.js"></script>
</head>
<body>

<div class="novice-guide" <?php if(!$admin_info['open_teach']){echo('style="display:none"');}?>>
    <div class="novice-guide-mask"></div>
    <div class="novice-guide-box">
        <div class="novice-guide-header">
            <span>新手向导</span>
            <a href="#"  onclick="close_teach()" class="novice-guide-close"></a>
        </div>
        <p>初次使用时，新手向导帮助您快速掌握商城系统使用方法</p>
        <div class="novice-guide-container novice-guide-container2">
            <div class="novice-guide-container-flowpath">
                <a href="#" class="ncap-btn-green ncap-btn-big">系统设置</a>
                <a href="#" class="ncap-btn-big">商品数据</a>
                <a href="#" class="ncap-btn-big">营销推广</a>
                <a href="#" class="ncap-btn-big">业务管理</a>
                <a href="#" class="ncap-btn-big">完成</a>
            </div>
            <div class="novice-guide-container-flowpath-content novice-guide-container-flowpath-content1">
                <div class="novice-guide-body">
                    <h3>基本设置</h3>
                    <p>基本设置用来设置商城的基本信息，你可以在这里填写商城名称、标志、域名等信息，请务必如实填写
                        以免后续造成不良影响。<a href="#" class="fillin">现在填写</a></p>
                    <h3>商城装修</h3>
                    <p>TPshop系统内置了多套精美模版，你可以挑选最喜欢的模板，也可以使用默认模板。<a href="#" class="fillin">现在装修</a></p>
                </div>
                <div class="novice-guide-select">
                    <label><input id="is_teach" type="checkbox">下次不再显示此向导</label>
                    <a href="#" class="ncap-btn-big ncap-btn-green">下一步<i></i></a>
                </div>
            </div>
            <div class="novice-guide-container-flowpath-content novice-guide-container-flowpath-content2" style="display: none;">
                <div class="novice-guide-body">
                    <h3>商品列表</h3>
                    <p>通过查看商品列表，可以全面的了解到整个平台商品的大致情况，有利于商家掌控平
                        台的运营。<a href="#" class="fillin">现在发布</a></p>
                    <h3>商品模型规格</h3>
                    <p>商品模型和规格可供顾客查看，如鞋子的种类和供客户购买时选择的如码数、颜色等。<a href="#" class="fillin">现在添加</a></p>
                    <h3>添加品牌</h3>
                    <p>品牌定位可以有效地建立品牌与竞品的差异性，提升消费者的购买印象。<a href="#" class="fillin">现在添加</a></p>
                    <h3>设置商家店铺</h3>
                    <p>通过设置店铺分类和等级等信息，才能建立一个完整的多商家平台中心。<a href="#" class="fillin">现在添加</a></p>
                </div>
                <div class="novice-guide-select">
                    <a href="#" class="ncap-btn-big ncap-btn-green" style="margin-top: 0">下一步<i></i></a>
                    <a href="#" class="ncap-btn-big"><i></i>上一步</a>
                </div>
            </div>
            <div class="novice-guide-container-flowpath-content novice-guide-container-flowpath-content3" style="display: none;">
                <div class="novice-guide-body">
                    <h3>促销管理</h3>
                    <p>通过细心管理商家的各类促销活动，能够让平台店铺流量和销量发展得更加快速。<a href="#" class="fillin">现在设置</a></p>
                    <h3>广告推广</h3>
                    <p>精彩的文案和精美的设计广告，能瞬间抓住消费者的眼球，引导其购买。<a href="#" class="fillin">现在添加</a></p>
                    <h3>分销系统</h3>
                    <p>设置会员分销体系，通过会员提成激励更多人来推广购买商品。<a href="#" class="fillin">现在设置</a></p>
                </div>
                <div class="novice-guide-select">
                    <a href="#" class="ncap-btn-big ncap-btn-green" style="margin-top: 0">下一步<i></i></a>
                    <a href="#" class="ncap-btn-big"><i></i>上一步</a>
                </div>
            </div>
            <div class="novice-guide-container-flowpath-content novice-guide-container-flowpath-content4" style="display: none;">
                <div class="novice-guide-body">
                    <h3>支付配置</h3>
                    <p>TPshop系统内置支付宝、微信支付、银联支付等多种支付方式供用户选择。<a href="#" class="fillin">现在设置</a></p>
                    <h3>SEO设置</h3>
                    <p>搜索引擎优化设置，是丰富一个综合商城店铺业务流转的重要工具。<a href="#" class="fillin">现在设置</a></p>
                    <h3>物流设置</h3>
                    <p>TPshop系统已内置了国内多种主要流物流公司接口，方便商家选择心怡的物流公司。<a href="#" class="fillin">现在设置</a></p>
                    <h3>订单管理</h3>
                    <p>商品订单管理可以批量快捷查看商城订单，可以快速查看整个平台的订单情况。<a href="#" class="fillin">现在查看</a></p>
                </div>
                <div class="novice-guide-select">
                    <a href="#" class="ncap-btn-big ncap-btn-green" style="margin-top: 0">下一步<i></i></a>
                    <a href="#" class="ncap-btn-big"><i></i>上一步</a>
                </div>
            </div>
            <div class="novice-guide-container-flowpath-content novice-guide-container-flowpath-content5" style="display: none;">
                <div class="novice-guide-body">
                    <p>恭喜您！您已经完成了商城系统的基本设置，您可以去前台看看购物流程是否顺畅</p>
                    <table>
                        <tr>
                            <td>角色管理</td>
                            <td>有助于商家更合理的分配商城
                                管理人手，提升管理效率</td>
                            <td>会员等级</td>
                            <td>会员等级的设置是一种给会员
                                成就感和提升黏度的措施</td>
                        </tr>
                        <tr>
                            <td>库存日志</td>
                            <td>分析整个平台商家的库存数据
                                管控商家销售风险</td>
                            <td>短信模板</td>
                            <td>您可在后台设置通过短信、邮
                                件自动发送消息动态给会员</td>
                        </tr>
                        <tr>
                            <td>会员卡</td>
                            <td>设置不同的会员卡和相应的优
                                惠，能有效促进客户消费</td>
                            <td>数据统计</td>
                            <td>流量统计、销售统计、生意分
                                析等，商城数据一览无余</td>
                        </tr>
                        <tr>
                            <td>新闻</td>
                            <td>新闻和文章的编辑，可以提升
                                整个平台的活跃度</td>
                            <td>商城装修</td>
                            <td>TPshop后台拥有丰富的装修组
                                件，发挥想象力自由DIY吧</td>
                        </tr>
                    </table>
                </div>
                <div class="novice-guide-select">
                    <a href="#" class="ncap-btn-big ncap-btn-green" style="margin-top: 0" onclick="close_teach()">完成</a>
                    <a href="#" class="ncap-btn-big"><i></i>上一步</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="admincp-header">
  <div class="bgSelector"></div>



<div class="admincp-name" onClick="javascript:openItem('welcome|Index');"><img src="<?php echo (isset($tpshop_config['shop_info_admin_home_logo']) && ($tpshop_config['shop_info_admin_home_logo'] !== '')?$tpshop_config['shop_info_admin_home_logo']:'/public/static/images/logo/admin_home_logo_default.png'); ?>" alt=""></div>

  <div class="nc-module-menu">
    <ul>
        <li data-param="index"><a href="javascript:void(0);"><img src="/public/static/images/iconhead1.png" alt="">首页</a></li>
        <li data-param="system"><a href="javascript:void(0);"><img src="/public/static/images/iconhead2.png" alt="">系统</a></li>
        <li data-param="decorate"><a href="javascript:void(0);"><img src="/public/static/images/iconhead3.png" alt="">页面</a></li>
        <li data-param="goods"><a href="javascript:void(0);"><img src="/public/static/images/iconhead4.png" alt="">商城</a></li>
        <li data-param="order"><a href="javascript:void(0);"><img src="/public/static/images/iconhead10.png" alt="">订单</a></li>
        <li data-param="marketing"><a href="javascript:void(0);"><img src="/public/static/images/iconhead5.png" alt="">营销</a></li>
        <li data-param="distribution"><a href="javascript:void(0);"><img src="/public/static/images/iconhead6.png" alt="">分销</a></li>
        <li data-param="data"><a href="javascript:void(0);"><img src="/public/static/images/iconhead9.png" alt="">统计</a></li>
        <li data-param="store"><a href="javascript:void(0);"><img src="/public/static/images/iconhead8.png" alt="">商家</a></li>
    </ul>
  </div>
  <div class="admincp-header-r">
    <div class="message">
        <a href="javascript:void(0);" class="msg" title="">&nbsp;</a>
        <s  class="total"><?php echo $message['total_count']>99?'...':$message['total_count']; ?></s>
        <div id="msg_Container">
            <div class="item">
                <h3 class="order_msg">商品提示
                    <em class="iconfont icon-down"></em>
                </h3>
                <s class="total"><?php echo $message['goods_count'] + $message['refund_order_count'] + $message['refund_count']>99?'...':($message['goods_count'] + $message['refund_order_count'] + $message['refund_count']); ?></s>
                <div class="msg_content">
                    <p onclick="switch_path('/admin/goods/goodsList')" >
                        <a href="javascript:;">待审核商品</a>
                        <span class="tiptool">（<em > <?php echo $message['goods_count']; ?> </em>）</span>
                    </p>
                    <p onclick="switch_path('/admin/order/refund_order_list')">
                        <a href="javascript:;">退款单</a>
                        <span class="tiptool">（<em > <?php echo $message['refund_order_count']; ?> </em>）</span>
                    </p>
                    <p onclick="switch_path('/admin/service/refund_list')">
                        <a href="javascript:;">售后退货</a>
                        <span class="tiptool">（<em > <?php echo $message['refund_count']; ?> </em>）</span>
                    </p>
                </div>
            </div>
            <div class="item">
                <h3 class="order_msg">商家审核提示
                    <em class="iconfont icon-down"></em>
                </h3>
                <s class="total"><?php echo $message['store_count'] + $message['store_reopen_count'] + $message['class_count'] + $message['store_withdrawls_count']>99?'...':($message['store_count'] + $message['store_reopen_count'] + $message['class_count'] + $message['store_withdrawls_count']); ?></s>
                <div class="msg_content">
                    <p onclick="switch_path('/admin/store/apply_list')">
                        <a href="javascript:;">开店申请</a>
                        <span class="tiptool">（<em > <?php echo $message['store_count']; ?> </em>）</span>
                    </p>
                    <p onclick="switch_path('/admin/store/reopen_list')">
                        <a href="javascript:;">签约申请</a>
                        <span class="tiptool">（<em > <?php echo $message['store_reopen_count']; ?> </em>）</span>
                    </p>
                    <p onclick="switch_path('/admin/store/apply_class_list')">
                        <a href="javascript:;">经营类目申请</a>
                        <span class="tiptool">（<em > <?php echo $message['class_count']; ?> </em>）</span>
                    </p>
                    <p onclick="switch_path('/admin/Finance/store_withdrawals')">
                        <a href="javascript:;">商家提现申请</a>
                        <span class="tiptool">（<em > <?php echo $message['store_withdrawls_count']; ?> </em>）</span>
                    </p>
                </div>
            </div>
            <div class="item">
                <h3 class="order_msg">会员提醒
                    <em class="iconfont icon-down"></em>
                </h3>
                <s class="total"><?php echo $message['withdrawls_count'] + $message['complain_count'] + $message['expose_count']>99?'...':($message['withdrawls_count'] + $message['complain_count'] + $message['expose_count']); ?></s>
                <div class="msg_content">
                    <p onclick="switch_path('/admin/Finance/withdrawals?status=0')">
                        <a href="javascript:;">会员提现申请</a>
                        <span class="tiptool">（<em > <?php echo $message['withdrawls_count']; ?> </em>）</span>
                    </p>
                    <p onclick="switch_path('/admin/Service/complain_list')">
                        <a href="javascript:;">投诉提醒</a>
                        <span class="tiptool">（<em > <?php echo $message['complain_count']; ?> </em>）</span>
                    </p>
                    <p onclick="switch_path('/admin/Service/expose_list')">
                        <a href="javascript:;">举报提醒</a>
                        <span class="tiptool">（<em > <?php echo $message['expose_count']; ?> </em>）</span>
                    </p>
                </div>
            </div>
            <div class="item">
                <h3 class="order_msg">活动提醒
                    <em class="iconfont icon-down"></em>
                </h3>
                <s class="total"><?php echo $message['flash_count'] + $message['team_count'] + $message['pre_sell_count']>99?'...':($message['flash_count'] + $message['team_count'] + $message['pre_sell_count']); ?></s>
                <div class="msg_content">
                    <p onclick="switch_path('/admin/Promotion/flash_sale?status=0')">
                        <a href="javascript:;">抢购审核</a>
                        <span class="tiptool">（<em > <?php echo $message['flash_count']; ?> </em>）</span>
                    </p>
                    <p onclick="switch_path('/admin/Team/index?status=0')">
                        <a href="javascript:;">拼团审核</a>
                        <span class="tiptool">（<em > <?php echo $message['team_count']; ?> </em>）</span>
                    </p>
                    <!--<p>-->
                        <!--<a href="javascript:void(0);">优惠劵审核</a>-->
                        <!--<span class="tiptool">（<em > <?php echo $message['team_count']; ?> </em>）</span>-->
                    <!--</p>-->
                    <p onclick="switch_path('/admin/PreSell/index?status=0')">
                        <a href="javascript:;">预售活动</a>
                        <span class="tiptool">（<em > <?php echo $message['pre_sell_count']; ?> </em>）</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="manager">
      <!--服务器升级-->
      <textarea id="textarea_upgrade" style="display:none;"><?php echo $upgradeMsg['1']; ?></textarea>
      <?php if($upgradeMsg[0] != null): ?>
      <dl style="text-align:left; font-size:15px;">
        <dd class="group"><a href="javascript:void(0);" id="a_upgrade" style="color:#FF0;"><?php echo $upgradeMsg['0']; ?></a></dd>
      </dl>
      <?php endif; ?>
      <!--服务器升级 end-->

        <a href="http://help.tp-shop.cn/Index/Help/channel/cat_id/24.html" class="manual"  target="_blank">帮助手册</a>

      </div>

	<div class="operate bgd-opa">
        	<span class="bgdopa-t"><?php echo $admin_info['user_name']; ?><i class="opa-arow"></i></span>
            <ul class="bgdopa-list">
                <li><a class="sitemap show-option" id="trace_show" href="<?php echo U('System/cleanCache',array('quick'=>1)); ?>" target="workspace">更新缓存</a></li>
                <li style="display: none !important;" tptype="pending_matters"><a class="toast show-option" href="javascript:void(0);" onClick="$.cookie('commonPendingMatters', 0, {expires : -1});ajax_form('pending_matters', '待处理事项', 'http://www.tpshop.cn/admin/index.php?act=common&op=pending_matters', '480');" title="查看待处理事项">&nbsp;<em>0</em></a></li>
                <li><a class="homepage show-option" target="_blank" href="/">打开商城</a></li>
                <li><a class="changepasd" onClick="CUR_DIALOG = ajax_form('modifypw', '修改密码', '<?php echo U('Admin/modify_pwd',array('admin_id'=>$admin_info['admin_id'])); ?>');"  href="javascript:void(0);">修改密码</a></li>
                <li><a href="#" class="novice">新手向导</a></li>
                <li><a class="login-out show-option" href="<?php echo U('Admin/logout'); ?>">退出系统</a></li>
            </ul>
        </div>
</div>
  <div class="clear"></div>
</div>
<div class="admincp-container unfold">
<div class="admincp-container-left">
<!--<div class="top-border"><span class="nav-side"></span><span class="sub-side"></span></div>-->
    <div id="admincpNavTabs_index" class="nav-tabs">
    	<dl>
		    <dt><a href="javascript:void(0);"><span class="ico-microshop-1"></span><b>概览</b></a></dt>
		    <dd class="sub-menu">
			    <ul>
				    <li><a href="javascript:void(0);" data-param="welcome|Index"><i>.</i>系统后台</a></li>
					<li><a href="javascript:void(0);" data-param="explain|Index"><i>.</i>业务流程</a></li>
			    </ul>
		    </dd>
	    </dl>
    </div>
    <?php if(is_array($menu) || $menu instanceof \think\Collection || $menu instanceof \think\Paginator): if( count($menu)==0 ) : echo "" ;else: foreach($menu as $mk=>$vo): ?>
    <div id="admincpNavTabs_<?php echo $mk; ?>" class="nav-tabs">
    	<?php if(is_array($vo[child]) || $vo[child] instanceof \think\Collection || $vo[child] instanceof \think\Paginator): if( count($vo[child])==0 ) : echo "" ;else: foreach($vo[child] as $key=>$v2): ?>
	    <dl>
		    <dt><a href="javascript:void(0);"><span class="ico-<?php echo $mk; ?>-<?php echo $key; ?>"></span><b><?php echo $v2['name']; ?></b></a></dt>
		    <dd class="sub-menu">
			    <ul>
			    	<?php if(is_array($v2[child]) || $v2[child] instanceof \think\Collection || $v2[child] instanceof \think\Paginator): if( count($v2[child])==0 ) : echo "" ;else: foreach($v2[child] as $key=>$v3): ?>
				    	<li><a href="javascript:void(0);" data-param="<?php echo $v3['act']; ?>|<?php echo $v3['op']; ?>"><?php echo $v3['name']; ?></a></li>
				    <?php endforeach; endif; else: echo "" ;endif; ?>
			    </ul>
		    </dd>
	    </dl>
    	<?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
    <?php endforeach; endif; else: echo "" ;endif; ?>
    <div class="about" title="关于系统" onclick="javascript:layer.open({type: 2,title: '关于我们',shadeClose: true,shade: 0.3,area: ['50%', '60%'],content:'<?php echo U("Index/about"); ?>', });"><i class="fa fa-copyright"></i><span>tpshop.cn</span></div>
</div>

  <div class="admincp-container-right">
    <!--<div class="top-border"></div>-->
    <iframe src="" id="workspace" name="workspace" style="margin-bottom: 20px" frameborder="0" width="100%" height="94%" scrolling="yes" onload="window.parent"></iframe>
  </div>
</div>
<script type="text/javascript">
    //新手导航模块
    // 打开新手向导
    $('.bgdopa-list .novice').on('click',function () {
        $('.novice-guide').show()
        $('.novice-guide-container2').show()
        $('.novice-guide-container-flowpath-content').hide()
        $('.novice-guide-container-flowpath-content1').show()
        $('.novice-guide-container-flowpath > a').eq(0).addClass('ncap-btn-green').siblings().removeClass('ncap-btn-green')
    })
    //关闭新手引导
    $('.novice-guide-close').on('click',function () {
        $('.novice-guide').hide()
    })
    $('.novice-guide-container2 .novice-guide-container-flowpath-content1 .novice-guide-select a').on('click',function () {
        $('.novice-guide-container-flowpath-content1').hide()
        $('.novice-guide-container-flowpath-content2').show()
        $('.novice-guide-container-flowpath > a:eq(1)').addClass('ncap-btn-green').siblings().removeClass('ncap-btn-green')
    })
    //点击上一步 下一步
    for(var i = 2; i <= 4; i++) {
        $('.novice-guide-container-flowpath-content'+i+' .novice-guide-select a:eq(0)').on('click',{index: i},function (e) {
            $('.novice-guide-container-flowpath-content'+e.data.index).hide()
            $('.novice-guide-container-flowpath-content'+(e.data.index+1)).show()
            $('.novice-guide-container-flowpath > a').eq(e.data.index).addClass('ncap-btn-green').siblings().removeClass('ncap-btn-green')
        })
        $('.novice-guide-container-flowpath-content'+i+' .novice-guide-select a:eq(1)').on('click',{index: i},function (e) {
            $('.novice-guide-container-flowpath-content'+e.data.index).hide()
            $('.novice-guide-container-flowpath-content'+(e.data.index-1)).show()
            $('.novice-guide-container-flowpath > a').eq(e.data.index-2).addClass('ncap-btn-green').siblings().removeClass('ncap-btn-green')
        })
    }
    //点击完成
    $('.novice-guide-container-flowpath-content5 .novice-guide-select a:eq(0)').on('click',{index: i},function (e) {
        $('.novice-guide').hide()
    })
    $('.novice-guide-container-flowpath-content5 .novice-guide-select a:eq(1)').on('click',{index: i},function (e) {
        $('.novice-guide-container-flowpath-content5').hide()
        $('.novice-guide-container-flowpath-content4').show()
        $('.novice-guide-container-flowpath > a').eq(3).addClass('ncap-btn-green').siblings().removeClass('ncap-btn-green')
    })

    $(function () {
        $('.novice-guide-container-flowpath-content1 .fillin').eq(0).on('click',function () {
            $('.novice-guide').hide()
            $('.admincp-container-right > iframe').attr('src','/index.php?m=Admin&c=System&a=index')
        })
        $('.novice-guide-container-flowpath-content1 .fillin').eq(1).on('click',function () {
            $('.novice-guide').hide()
            $('.admincp-container-right > iframe').attr('src','/index.php?m=Admin&c=Block&a=pageList')
        })
        $('.novice-guide-container-flowpath-content2 .fillin').eq(0).on('click',function () {
            $('.novice-guide').hide()
            $('.nc-module-menu').find('li').eq(1).find('a').trigger('click')
            $('.admincp-container-right > iframe').attr('src','/index.php?m=Admin&c=Goods&a=goodsList')
        })
        $('.novice-guide-container-flowpath-content2 .fillin').eq(1).on('click',function () {
            $('.novice-guide').hide()
            $('.nc-module-menu').find('li').eq(1).find('a').trigger('click')
            $('.sub-menu > ul > li').removeClass('active')
            $('.admincp-container-right > iframe').attr('src','/index.php?m=Admin&c=Goods&a=goodsTypeList')
        })
        $('.novice-guide-container-flowpath-content2 .fillin').eq(2).on('click',function () {
            $('.novice-guide').hide()
            $('.nc-module-menu').find('li').eq(1).find('a').trigger('click')
            $('.sub-menu > ul > li').removeClass('active')
            $('.admincp-container-right > iframe').attr('src','/index.php?m=Admin&c=Goods&a=brandList')
        })
        $('.novice-guide-container-flowpath-content2 .fillin').eq(3).on('click',function () {
            $('.novice-guide').hide()
            $('.nc-module-menu').find('li').eq(1).find('a').trigger('click')
            $('.sub-menu > ul > li').removeClass('active')
            $('.admincp-container-right > iframe').attr('src','/index.php?m=Admin&c=Store&a=store_list')
        })
        $('.novice-guide-container-flowpath-content3 .fillin').eq(0).on('click',function () {
            $('.novice-guide').hide()
            $('.sub-menu > ul > li').removeClass('active')
            $('.nc-module-menu').find('li').eq(5).find('a').trigger('click')
            $('.admincp-container-right > iframe').attr('src','/index.php?m=Admin&c=Promotion&a=prom_goods_list')
        })
        $('.novice-guide-container-flowpath-content3 .fillin').eq(1).on('click',function () {
            $('.novice-guide').hide()
            $('.sub-menu > ul > li').removeClass('active')
            $('.admincp-container-right > iframe').attr('src','/index.php?m=Admin&c=Ad&a=adList')
        })
        $('.novice-guide-container-flowpath-content3 .fillin').eq(2).on('click',function () {
            $('.novice-guide').hide()
            $('.nc-module-menu').find('li').eq(1).find('a').trigger('click')
            $('.sub-menu > ul > li').removeClass('active')
            $('.admincp-container-right > iframe').attr('src','/index.php?m=Admin&c=Distribut&a=goods_list')
        })
        $('.novice-guide-container-flowpath-content4 .fillin').eq(0).on('click',function () {
            $('.novice-guide').hide()
            $('.nc-module-menu').find('li').eq(1).find('a').trigger('click')
            $('.sub-menu > ul > li').removeClass('active')
            $('.admincp-container-right > iframe').attr('src','/index.php?m=Admin&c=Plugin&a=index')
        })
        $('.novice-guide-container-flowpath-content4 .fillin').eq(1).on('click',function () {
            $('.novice-guide').hide()
            $('.nc-module-menu').find('li').eq(0).find('a').trigger('click')
            $('.sub-menu > ul > li').removeClass('active')
            $('.admincp-container-right > iframe').attr('src','/index.php?m=Admin&c=System&a=seo')
        })
        $('.novice-guide-container-flowpath-content4 .fillin').eq(2).on('click',function () {
            $('.novice-guide').hide()
            $('.nc-module-menu').find('li').eq(0).find('a').trigger('click')
            $('.sub-menu > ul > li').removeClass('active')
            $('.admincp-container-right > iframe').attr('src','/index.php?m=Admin&c=Shipping&a=index')
        })
        $('.novice-guide-container-flowpath-content4 .fillin').eq(3).on('click',function () {
            $('.novice-guide').hide()
            $('.nc-module-menu').find('li').eq(1).find('a').trigger('click')
            $('.admincp-container-right > iframe').attr('src','/index.php?m=Admin&c=Order&a=index')
        })
    })
	// 没有点击收货确定的按钮让他自己收货确定
	var timestamp = Date.parse(new Date());
	window.onload=function(){
      test_task();
	}
    function close_teach(){
        var teach=$('#is_teach:checked').val();
        if(teach=='on'){
            $.ajax({
                dataType: 'get',
                url:"<?php echo U('Admin/index/close_teach'); ?>",
                success: function (data) {
                }
            });
        }
    }
    function test_task(){
      $.ajax({
        type:'post',
        url:"<?php echo U('Admin/System/login_task'); ?>",
        data:{timestamp:timestamp},
        timeout : 1000*60, //超时时间设置，单位毫秒100000000
        success:function(){
          // 执行定时任务
        }
      });
      setTimeout(function() {
        test_task()
      }, 60000);
    }
//    导航消息模块
    $(".order_msg").click(function(){
        $(this).children().toggleClass('show');
        $(this).next().next().toggle(function(){
            $(this).next().next().css({" transition": "50ms liner",
                "-moz-transition": "50ms liner", /* Firefox 4 */
                "-webkit-transition": "50ms liner", /* Safari 和 Chrome */
                "-o-transition":"50ms liner", /* Opera */
            })
        });
    })
  $(function(){
//    setTimeout(function(){
      var Plugin = false;
      $("li a").each(function(){
        var t = $(this).html();
        //console.log(t)
        if(t=='插件库'){
          Plugin = true;
        }
      })
      if(!Plugin){
        $("#resource").remove();
      }
//    },1000)

  })
    function switch_path(url) {
        $('.admincp-container-right > iframe').attr('src',url);
    }
</script>
</body>
</html>
