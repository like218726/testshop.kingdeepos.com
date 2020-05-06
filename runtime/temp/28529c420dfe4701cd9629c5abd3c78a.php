<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:44:"./application/seller/new/pre_sell/index.html";i:1587634376;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/head.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/left.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/foot.html";i:1587634378;}*/ ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php if(!empty($_SERVER['HTTPS']) and strtolower($_SERVER['HTTPS']) != 'off'): ?>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" >
<?php endif; ?>
<title>商家中心</title>
<link href="/public/static/css/base.css" rel="stylesheet" type="text/css">
<link href="/public/static/css/seller_center.css" rel="stylesheet" type="text/css">
<link href="/public/static/font/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
<link rel="shortcut icon" type="image/x-icon" href="<?php echo (isset($tpshop_config['shop_info_store_ico']) && ($tpshop_config['shop_info_store_ico'] !== '')?$tpshop_config['shop_info_store_ico']:'/public/static/images/logo/storeico_default.png'); ?>" media="screen"/>
<!--[if IE 7]>
  <link rel="stylesheet" href="/public/static/font/font-awesome/css/font-awesome-ie7.min.css">
<![endif]-->
<script type="text/javascript" src="/public/static/js/jquery.js"></script>
<script type="text/javascript" src="/public/static/js/seller.js"></script>
<script type="text/javascript" src="/public/static/js/waypoints.js"></script>
<script type="text/javascript" src="/public/static/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="/public/static/js/jquery.validation.min.js"></script>
<script type="text/javascript" src="/public/static/js/layer/layer.js"></script>
<script type="text/javascript" src="/public/js/dialog/dialog.js" id="dialog_js"></script>
<script type="text/javascript" src="/public/js/global.js"></script>
<script type="text/javascript" src="/public/js/myAjax.js"></script>
<script type="text/javascript" src="/public/js/myFormValidate.js"></script>
<script type="text/javascript" src="/public/static/js/layer/laydate/laydate.js"></script>

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
      <script src="/public/static/js/html5shiv.js"></script>
      <script src="/public/static/js/respond.min.js"></script>
<![endif]-->
</head>
<body>
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<header class="ncsc-head-layout w">
  <div class="wrapper">
    <div class="ncsc-admin w252" id="user-info">
      <!-- <dl class="ncsc-admin-info">
        <dt class="admin-avatar"></dt>
      </dl> -->
      <!-- 店铺 -->
      <div class="seller-logo">
         <a class="iconshop" href="<?php echo U('Home/Store/index',array('store_id'=>STORE_ID)); ?>" title="前往店铺" ><i class="icon-home"></i>&nbsp;店铺</a>

      </div>
      
       <!-- 店铺头像 -->
      <div class="seller-img"><img src="/public/static/images/seller/default_user_portrait.gif" width="32" class="pngFix" alt=""/></div>
      <!-- 店铺名 -->
      <div class="storename">
          <div class="bgd-opa"><p class="admin-name" style="height: 72px;line-height: 72px"><a class="seller_name " href=""><?php echo $seller['seller_name']; ?></a>
            <i class="opa-arow"></i>
          </p>
          <ul class="bgdopa-list">
              <li><a class="iconshop" href="<?php echo U('Admin/modify_pwd',array('seller_id'=>$seller['seller_id'])); ?>" title="修改密码" target="_blank">设置</a></li>
              <li><a class="iconshop" href="<?php echo U('Admin/logout'); ?>" title="安全退出">退出</a> </li>
          </ul>
          </div>
      </div>
      


      
        <!--   <div class="bottom"> 
             
              <a class="iconshop" href="<?php echo U('Admin/modify_pwd',array('seller_id'=>$seller['seller_id'])); ?>" title="修改密码" target="_blank"><i class="icon-wrench"></i>&nbsp;设置</a>
              <a class="iconshop" href="<?php echo U('Admin/logout'); ?>" title="安全退出"><i class="icon-signout"></i>&nbsp;退出</a></div> -->
           </div>
     
      <div class="center-logo"> <a href="/" target="_blank">

        <img src="<?php echo (isset($tpshop_config['shop_info_store_logo']) && ($tpshop_config['shop_info_store_logo'] !== '')?$tpshop_config['shop_info_store_logo']:'/public/static/images/logo/pc_home_logo_default.png'); ?>" class="pngFix" alt=""/></a>
        <h1>商家中心</h1>
      </div>
      <nav class="ncsc-nav">
        <dl <?php if(ACTION_NAME == 'index' AND CONTROLLER_NAME == 'Index'): ?>class="current"<?php endif; ?>>
          <dt><a href="<?php echo U('Index/index'); ?>">首页</a></dt>
          <dd class="arrow"></dd>
        </dl>
        <?php if(is_array($menuArr) || $menuArr instanceof \think\Collection || $menuArr instanceof \think\Paginator): if( count($menuArr)==0 ) : echo "" ;else: foreach($menuArr as $kk=>$vo): ?>
        <dl <?php if(ACTION_NAME == $vo[child][0][act] AND CONTROLLER_NAME == $vo[child][0][op]): ?>class="current"<?php endif; ?>>
          <dt><a href="/index.php?m=Seller&c=<?php echo $vo[child][0][op]; ?>&a=<?php echo $vo[child][0][act]; ?>"><?php echo $vo['name']; ?></a></dt>
          <dd>
            <ul>
                <?php if(is_array($vo['child']) || $vo['child'] instanceof \think\Collection || $vo['child'] instanceof \think\Paginator): if( count($vo['child'])==0 ) : echo "" ;else: foreach($vo['child'] as $key=>$vv): ?>
                  <li> <a href='<?php echo U("$vv[op]/$vv[act]"); ?>'> <?php echo $vv['name']; ?> </a> </li>
          <?php endforeach; endif; else: echo "" ;endif; ?>
             </ul>
          </dd>
          <dd class="arrow"></dd>
        </dl>
        <?php endforeach; endif; else: echo "" ;endif; ?>
          <dl>
              <dt><a href="http://help.tp-shop.cn/Index/Help/info/cat_id/24"target="_blank">帮助手册</a></dt>
              <dd class="arrow"></dd>
          </dl>
    </nav>
              <div class="index-sitemap" id="shortcut">
                  <a class="iconangledown" href="javascript:void(0);">快捷方式 <i class="icon-angle-down"></i></a>
                    <div class="sitemap-menu-arrow"></div>
                    <div class="sitemap-menu">
                        <div class="title-bar">
                          <h2>管理导航</h2>
                          <p class="h_tips"><em>小提示：添加您经常使用的功能到首页侧边栏，方便操作。</em></p>
                          <img src="/public/static/images/obo.png" alt="">
                          <span id="closeSitemap" class="close">X</span>
                        </div>
                        <div id="quicklink_list" class="content">
                        <?php if(is_array($menuArr) || $menuArr instanceof \think\Collection || $menuArr instanceof \think\Paginator): if( count($menuArr)==0 ) : echo "" ;else: foreach($menuArr as $k2=>$v2): ?>
                        <dl>
                          <dt><?php echo $v2['name']; ?></dt>
                            <?php if(is_array($v2['child']) || $v2['child'] instanceof \think\Collection || $v2['child'] instanceof \think\Paginator): if( count($v2['child'])==0 ) : echo "" ;else: foreach($v2['child'] as $key=>$v3): ?>
                            <dd class="<?php if(!empty($quicklink)){if(in_array($v3['op'].'_'.$v3['act'],$quicklink)){echo 'selected';}} ?>">
                              <i nctype="btn_add_quicklink" data-quicklink-act="<?php echo $v3[op]; ?>_<?php echo $v3[act]; ?>" class="icon-check" title="添加为常用功能菜单"></i>
                              <a href=<?php echo U("$v3[op]/$v3[act]"); ?>> <?php echo $v3['name']; ?> </a>
                            </dd>
                          <?php endforeach; endif; else: echo "" ;endif; ?>
                        </dl>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                        </div>
                    </div>
                  </div> 
    </div>

 </div>
 
</div>

</header>

</body>
</html>
<style>
	.ncsc-default-table tbody th span {
		margin-right: 2px;
	}
</style>
<div class="ncsc-layout wrapper">
	 <div id="layoutLeft" class="ncsc-layout-left">
   <div id="sidebar" class="sidebar">
     <div class="column-title" id="main-nav"><span class="ico-<?php echo $leftMenu['icon']; ?>"></span>
       <h2><?php echo $leftMenu['name']; ?></h2>
     </div>
     <div class="column-menu">
       <ul id="seller_center_left_menu">
      	 <?php if(is_array($leftMenu['child']) || $leftMenu['child'] instanceof \think\Collection || $leftMenu['child'] instanceof \think\Paginator): if( count($leftMenu['child'])==0 ) : echo "" ;else: foreach($leftMenu['child'] as $key=>$vu): ?>
           <li class="<?php if(ACTION_NAME == $vu[act] AND CONTROLLER_NAME == $vu[op]): ?>current<?php endif; ?>">
           		<a href="<?php echo U("$vu[op]/$vu[act]"); ?>"> <?php echo $vu['name']; ?></a>
           </li>
	 	<?php endforeach; endif; else: echo "" ;endif; ?>
      </ul>
     </div>
   </div>
 </div>
	<div id="layoutRight" class="ncsc-layout-right">
		<div class="ncsc-path"><i class="icon-desktop"></i>商家管理中心<i class="icon-angle-right"></i>促销<i class="icon-angle-right"></i>预售管理
		</div>
		<div class="main-content" id="mainContent">
			<div class="tabmenu">
				<ul class="tab pngFix">
					<li class="active"><a href="<?php echo U('pre_sell/index'); ?>">预售列表</a></li>
				</ul>
				<a href="<?php echo U('pre_sell/info'); ?>" class="ncbtn ncbtn-mint" title="新增预售"><i class="icon-plus-sign"></i>新增预售</a>

			</div>
			<div class="alert alert-block mt10">
				<ul class="mt5">
					<li>1、点击新增预售按钮可以添加预售活动</li>
					<li>2、如果活动到了结束时间还未结束(已过期和结束活动按钮同时存在)，需要点击商品链接去前台触发，或者点击结束按钮,将活动结束</li>
					<li>3、每个商品，只能参与一种活动，但积分商品不能参与活动。</li>
				</ul>
			</div>
			<table class="ncsc-default-table order">
				<thead>
				<tr>
					<th class="w10"></th>
					<th colspan="2">预售商品</th>
					<th class="w200">预售标题</th>
					<th class="w80">预售库存</th>
					<th class="w80">订金</th>
					<th class="w80">当前价格</th>
					<th class="w80">订购商品数</th>
					<th class="w80">订单数</th>
					<th class="w50">状态</th>
					<th class="w120">操作</th>
				</tr>
				</thead>
				<?php if(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty())): ?>
					<tbody>
					<tr>
						<td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span>暂无符合条件的数据记录</span></div></td>
					</tr>
					</tbody>
				<?php else: if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sell): $mod = ($i % 2 );++$i;?>
						<tbody>
						<tr>
							<td colspan="20" class="sep-row"></td>
						</tr>
						<tr>
							<th colspan="20">
								<span class="ml10">id:<?php echo $sell['pre_sell_id']; ?></span>
								<span>活动时间：<em class="goods-time"><?php echo date('Y-m-d H:i:s',$sell['sell_start_time']); ?>至<?php echo date('Y-m-d H:i:s',$sell['sell_end_time']); ?></em></span>
								<?php if($sell['deposit_price'] > 0): ?>
									<span>尾款支付时间：<em class="goods-time"><?php echo date('Y-m-d H:i:s',$sell['pay_start_time']); ?>至<?php echo date('Y-m-d H:i:s',$sell['pay_end_time']); ?></em></span>
								<?php endif; ?>
								<span><a class="ncbtn-mini ncbtn-grapefruit"><?php echo $sell['finish_desc']; ?></a></span>
								<?php if($sell['finish_button']): ?>
									<a href="javascript:void(0)" data-pre-sell-id="<?php echo $sell['pre_sell_id']; ?>" class="ncbtn-mini ncbtn-bittersweet finish_button"><i class="icon-stop"></i>结束活动</a>
								<?php endif; if($sell['success_or_fail_button']): ?>
									<a href="javascript:void(0)" data-pre-sell-id="<?php echo $sell['pre_sell_id']; ?>" class="ncbtn-mini ncbtn-bittersweet success_button"><i class="icon-ok"></i>活动成功</a>
									<a href="javascript:void(0)" data-pre-sell-id="<?php echo $sell['pre_sell_id']; ?>" class="ncbtn-mini ncbtn-bittersweet fail_button"><i class="icon-remove"></i>活动失败</a>
								<?php endif; ?>
							</th>
						</tr>
						<tr>
							<td class="bdl"></td>
							<td class="w70">
								<div class="ncsc-goods-thumb">
									<img src="<?php echo goods_thum_images($sell['goods_id'],60,60,sell.item_id); ?>" style="width: 60px;height: 60px;"/>
								</div>
							</td>
							<td class="tl w150">
								<dl class="goods-name">
									<dt><a href="<?php echo U('Home/Goods/goodsInfo',['id'=>$sell['goods_id'],'item_id'=>$sell['item_id']]); ?>" target="_blank"><?php echo getSubstr($sell['goods_name'],0,20); ?></a></dt>
									<dd><?php echo getSubstr($sell['item_name'],0,20); ?></dd>
								</dl>
							</td>
							<td class="bdl"><p><?php echo getSubstr($sell['title'],0,20); ?></p></td>
							<td class="bdl"><?php echo $sell['stock_num']; ?></td>
							<td class="bdl"><?php echo $sell['deposit_price']; ?></td>
							<td class="bdl"><?php echo $sell['ing_price']; ?></td>
							<td class="bdl"><?php echo $sell['deposit_goods_num']; ?></td>
							<td class="bdl"><?php echo $sell['deposit_order_num']; ?></td>
							<td class="bdl bdr"><?php echo $sell['status_desc']; ?></td>
							<td class="nscs-table-handle">
								<span><a href="<?php echo U('pre_sell/info',array('id'=>$sell['pre_sell_id'])); ?>" class="ncbtn-mint"><i class="icon-edit"></i><p>编辑</p></a></span>
								<span><a data-url="<?php echo U('pre_sell/delete'); ?>" data-id="<?php echo $sell['pre_sell_id']; ?>" onclick="delfun(this)" class="btn-grapefruit"><i class="icon-trash"></i><p>删除</p></a></span>
							</td>
						</tr>
					</tbody>
				<?php endforeach; endif; else: echo "" ;endif; endif; ?>
				<tfoot>
				<tr>
					<td colspan="20"><?php echo $page->show(); ?></td>
				</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>
<div id="cti">
  <div class="wrapper">
    <ul>
          </ul>
  </div>
</div>
<div id="faq">
  <div class="wrapper">
      </div>
</div>

<div id="footer">
  <p>
      <?php $i = 1;
                                   
                                $md5_key = md5("SELECT * FROM `__PREFIX__navigation` where is_show = 1 AND position = 3 ORDER BY `sort` DESC");
                                $result_name = $sql_result_vv = S("sql_".$md5_key);
                                if(empty($sql_result_vv))
                                {                            
                                    $result_name = $sql_result_vv = \think\Db::query("SELECT * FROM `__PREFIX__navigation` where is_show = 1 AND position = 3 ORDER BY `sort` DESC"); 
                                    S("sql_".$md5_key,$sql_result_vv,31104000);
                                }    
                              foreach($sql_result_vv as $kk=>$vv): if($i > 1): ?>|<?php endif; ?>
         <a href="<?php echo $vv[url]; ?>" <?php if($vv[is_new] == 1): ?> target="_blank" <?php endif; ?> ><?php echo $vv[name]; ?></a>
          <?php $i++; endforeach; ?>
      <!--<a href="/">首页</a>-->
      <!--| <a  href="http://<?php echo $_SERVER['HTTP_HOST']; ?>">招聘英才</a>-->
      <!--| <a  href="http://<?php echo $_SERVER['HTTP_HOST']; ?>">合作及洽谈</a>-->
      <!--| <a  href="http://<?php echo $_SERVER['HTTP_HOST']; ?>">联系我们</a>-->
      <!--| <a  href="http://<?php echo $_SERVER['HTTP_HOST']; ?>">关于我们</a>-->
      <!--| <a  href="http://<?php echo $_SERVER['HTTP_HOST']; ?>">物流自取</a>-->
      <!--| <a  href="http://<?php echo $_SERVER['HTTP_HOST']; ?>">友情链接</a>-->
  </p>
  Copyright 2017 <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>" target="_blank"><?php echo (isset($tpshop_config['shop_info_store_name']) && ($tpshop_config['shop_info_store_name'] !== '')?$tpshop_config['shop_info_store_name']:'TPshop商城'); ?></a> All rights reserved.<br />
  
</div>
<script type="text/javascript" src="/public/static/js/jquery.cookie.js"></script>
<link href="/public/static/js/perfect-scrollbar.min.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/public/static/js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="/public/static/js/qtip/jquery.qtip.min.js"></script>
<link href="/public/static/js/qtip/jquery.qtip.min.css" rel="stylesheet" type="text/css">
<div id="tbox">
  <div class="btn" id="msg"><a href="<?php echo U('Seller/index/store_msg'); ?>"><i class="msg"><?php if(!(empty($storeMsgNoReadCount) || (($storeMsgNoReadCount instanceof \think\Collection || $storeMsgNoReadCount instanceof \think\Paginator ) && $storeMsgNoReadCount->isEmpty()))): ?><em><?php echo $storeMsgNoReadCount; ?></em><?php endif; ?></i>站内消息</a></div>
  <div class="btn" id="im">
      <a href="tencent://message/?uin=<?php echo $tpshop_config['shop_info_qq3']; ?>&Site=<?php echo (isset($tpshop_config['shop_info_store_name']) && ($tpshop_config['shop_info_store_name'] !== '')?$tpshop_config['shop_info_store_name']:'TPshop商城'); ?>&Menu=yes" target="_blank">
          <i class="im"><em id="new_msg" style="display:none;"></em></i>
          在线联系</a>
  </div>
  <div class="btn" id="gotop" style="display: block;"><i class="top"></i><a href="javascript:void(0);">返回顶部</a></div>
</div>
<script type="text/javascript">
var current_control = '<?php echo CONTROLLER_NAME; ?>/<?php echo ACTION_NAME; ?>';
$(document).ready(function(){
    //添加删除快捷操作
    $('[nctype="btn_add_quicklink"]').on('click', function() {
        var $quicklink_item = $(this).parent();
        var item = $(this).attr('data-quicklink-act');
        if($quicklink_item.hasClass('selected')) {
            $.post("<?php echo U('Seller/Index/quicklink_del'); ?>", { item: item }, function(data) {
                $quicklink_item.removeClass('selected');
                var idstr = 'quicklink_'+ item;
                $('#'+idstr).remove();
            }, "json");
        } else {
            var scount = $('#quicklink_list').find('dd.selected').length;
            if(scount >= 8) {
                layer.msg('快捷操作最多添加8个', {icon: 2,time: 2000});
            } else {
                $.post("<?php echo U('Seller/Index/quicklink_add'); ?>", { item: item }, function(data) {
                    $quicklink_item.addClass('selected');
                    if(current_control=='Index/index'){
                        var $link = $quicklink_item.find('a');
                        var menu_name = $link.text();
                        var menu_link = $link.attr('href');
                        var menu_item = '<li id="quicklink_' + item + '"><a href="' + menu_link + '">' + menu_name + '</a></li>';
                        $(menu_item).appendTo('#seller_center_left_menu').hide().fadeIn();
                    }
                }, "json");
            }
        }
    });
    //浮动导航  waypoints.js
    $("#sidebar,#mainContent").waypoint(function(event, direction) {
        $(this).parent().toggleClass('sticky', direction === "down");
        event.stopPropagation();
        });
    });
    // 搜索商品不能为空
    $('input[nctype="search_submit"]').click(function(){
        if ($('input[nctype="search_text"]').val() == '') {
            return false;
        }
    });

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
	
    function delfunc(obj){
    	layer.confirm('确认删除？', {
    		  btn: ['确定','取消'] //按钮
    		}, function(){
    		    // 确定
   				$.ajax({
   					type : 'post',
   					url : $(obj).attr('data-url'),
   					data : {act:'del',del_id:$(obj).attr('data-id')},
   					dataType : 'json',
   					success : function(data){
                        layer.closeAll();
   						if(data==1){
   							layer.msg('操作成功', {icon: 1,time: 1000},function () {
                                location.reload();
                                // window.location.href='';
                            });
   						}else{
   							layer.msg(data, {icon: 2,time: 2000});
   						}
   					}
   				})
    		}, function(index){
    			layer.close(index);
    			return false;// 取消
    		}
    	);
    }
</script>
<script>
	function delfun(obj) {
		layer.confirm('确认删除？', {
					btn: ['确定', '取消'] //按钮
				}, function () {
					$.ajax({
						type: 'post',
						url: $(obj).attr('data-url'),
						data: {pre_sell_id: $(obj).attr('data-id')},
						dataType: 'json',
						success: function (data) {
							layer.closeAll();
							if (data.status == 1) {
								layer.msg(data.msg, {icon: 1});
								$(obj).parent().parent().parent().parent().remove();
							} else {
								layer.msg(data.msg, {icon: 2, time: 2000});
							}
						}
					})
				}, function (index) {
					layer.close(index);
				}
		);
	}

	//结束活动
	$(function () {
		$(document).on("click", '.finish_button', function (e) {
			var pre_sell_id = $(this).data('pre-sell-id');
			layer.open({
				content: '结束活动将（修改活动结束时间为当前时间）。该操作不可逆，确定要执行吗？'
				,btn: ['确定', '取消']
				,yes: function(index, layero){
					layer.close(index);
					$.ajax({
						type: "POST",
						url: "<?php echo U('Seller/PreSell/finish'); ?>",//+tab,
						data: {pre_sell_id: pre_sell_id},
						dataType: 'json',
						success: function (data) {
							if (data.status == 1) {
								layer.msg(data.msg, {icon: 1, time: 2000}, function(){
									window.location.reload();
								});
							} else {
								layer.msg(data.msg, {icon: 2, time: 2000});
							}
						}
					});
				}
				,btn2: function(index, layero){
					layer.close(index);
				}
				,cancel: function(){
					//右上角关闭回调
					layer.close();
				}
			});
		})
	})

	//活动成功
	$(function () {
		$(document).on("click", '.success_button', function (e) {
			var pre_sell_id = $(this).data('pre-sell-id');
			layer.open({
				content: '此操作不可逆，您确定要设置该预售活动成功吗？'
				,btn: ['确定', '取消']
				,yes: function(index, layero){
					layer.close(index);
					$.ajax({
						type: "POST",
						url: "<?php echo U('Seller/PreSell/succeed'); ?>",//+tab,
						data: {pre_sell_id: pre_sell_id},
						dataType: 'json',
						success: function (data) {
							if (data.status == 1) {
								layer.msg(data.msg, {icon: 1, time: 2000}, function(){
									window.location.reload();
								});
							} else {
								layer.msg(data.msg, {icon: 2, time: 2000});
							}
						}
					});
				}
				,btn2: function(index, layero){
					layer.close(index);
				}
				,cancel: function(){
					//右上角关闭回调
					layer.close();
				}
			});
		})
	})

	//活动成功
	$(function () {
		$(document).on("click", '.fail_button', function (e) {
			var pre_sell_id = $(this).data('pre-sell-id');
			layer.open({
				content: '此操作不可逆，您确定要设置该预售活动失败吗？'
				,btn: ['确定', '取消']
				,yes: function(index, layero){
					layer.close(index);
					$.ajax({
						type: "POST",
						url: "<?php echo U('Seller/PreSell/fail'); ?>",//+tab,
						data: {pre_sell_id: pre_sell_id},
						dataType: 'json',
						success: function (data) {
							if (data.status == 1) {
								layer.msg(data.msg, {icon: 1, time: 2000}, function(){
									window.location.reload();
								});
							} else {
								layer.msg(data.msg, {icon: 2, time: 2000});
							}
						}
					});
				}
				,btn2: function(index, layero){
					layer.close(index);
				}
				,cancel: function(){
					//右上角关闭回调
					layer.close();
				}
			});
		})
	})
</script>
</body>
</html>
