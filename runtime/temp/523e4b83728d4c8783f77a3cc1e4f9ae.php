<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:46:"./application/seller/new/goods/addStepOne.html";i:1587634376;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/head.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/left.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/foot.html";i:1587634378;}*/ ?>
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
        <div class="ncsc-path"><i class="icon-desktop"></i>商家管理中心<i class="icon-angle-right"></i>商品<i class="icon-angle-right"></i>选择商品分类</div>
        <div class="main-content" id="mainContent">
            <!-- S setp -->
            <?php if(!(empty($goods) || (($goods instanceof \think\Collection || $goods instanceof \think\Paginator ) && $goods->isEmpty()))): ?>
                <div class="tabmenu">
                    <ul class="tab pngFix">
                        <li class="active">
                            <a href="<?php echo U('Seller/Goods/addStepOne',array('goods_id'=>$goods['goods_id'])); ?>">选择分类</a>
                        </li>
                        <li class="normal">
                            <a href="<?php echo U('Seller/Goods/addEditGoods',array('goods_id'=>$goods['goods_id'])); ?>">编辑商品</a>
                        </li>
                    </ul>
                </div>
                <?php else: ?>
                <ul class="add-goods-step">
                    <li class="current"><i class="icon icon-list-alt"></i>
                        <h6>STEP.1</h6>
                        <h2>选择商品分类</h2>
                        <i class="arrow icon-angle-right"></i> </li>
                    <li><i class="icon icon-edit"></i>
                        <h6>STEP.2</h6>
                        <h2>填写商品详情</h2>
                        <i class="arrow icon-angle-right"></i> </li>
                    <li><i class="icon icon-camera-retro "></i>
                        <h6>STEP.3</h6>
                        <h2>上传商品图片</h2>
                        <i class="arrow icon-angle-right"></i> </li>
                    <li><i class="icon icon-ok-circle"></i>
                        <h6>STEP.4</h6>
                        <h2>商品发布成功</h2>
                    </li>
                </ul>
            <?php endif; ?>
            <!--S 分类选择区域-->
            <div class="wrapper_search">
                <div class="wp_sort">
                    <div id="dataLoading" class="wp_data_loading">
                        <div class="data_loading">加载中...</div>
                    </div>
                    <div id="class_div" class="wp_sort_block">
                        <div class="sort_list">
                            <div class="wp_category_list">
                                <div id="class_div_1" class="category_list ps-container ps-active-y">
                                    <ul>
                                        <?php if(is_array($goodsCategoryLevelOne) || $goodsCategoryLevelOne instanceof \think\Collection || $goodsCategoryLevelOne instanceof \think\Paginator): $i = 0; $__LIST__ = $goodsCategoryLevelOne;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods_category): $mod = ($i % 2 );++$i;?>
                                            <li> <a href="javascript:void(0)" data-title="<?php echo $goods_category['name']; ?>" data-goods-cat-id="<?php echo $goods_category['id']; ?>"><i class="icon-double-angle-right"></i><?php echo $goods_category['name']; ?></a></li>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="sort_list">
                            <div class="wp_category_list blank">
                                <div id="class_div_2" class="category_list ps-container ps-active-y" style="">
                                    <ul>
                                    </ul>
                                    <div class="ps-scrollbar-x-rail" style="width: 15px; display: none; left: 0px; bottom: 3px;"><div class="ps-scrollbar-x" style="left: 0px; width: 0px;"></div></div><div class="ps-scrollbar-y-rail" style="top: 0px; height: 264px; display: none; right: 3px;"><div class="ps-scrollbar-y" style="top: 0px; height: 0px;"></div></div></div>
                            </div>
                        </div>
                        <div class="sort_list sort_list_last">
                            <div class="wp_category_list blank">
                                <div id="class_div_3" class="category_list ps-container ps-active-y" style="">
                                    <ul>
                                    </ul>
                                    <div class="ps-scrollbar-x-rail" style="width: 15px; display: none; left: 0px; bottom: 3px;"><div class="ps-scrollbar-x" style="left: 0px; width: 0px;"></div></div><div class="ps-scrollbar-y-rail" style="top: 0px; height: 264px; display: none; right: 3px;"><div class="ps-scrollbar-y" style="top: 0px; height: 0px;"></div></div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert">
                    <dl class="hover_tips_cont">
                        <dt id="commodityspan"><span style="color:#F00;">请选择商品类别</span></dt>
                        <dt id="commoditydt" style="display: none;" class="current_sort">您当前选择的商品类别是：</dt>
                        <dd id="commoditydd"></dd>
                    </dl>
                </div>
                <div class="wp_confirm">
                    <form method="get" action="<?php echo U('Seller/Goods/addEditGoods'); ?>">
                        <input name="cat_id3" value="<?php echo $goods['cat_id3']; ?>" type="hidden">
                        <input name="goods_id" value="<?php echo $goods['goods_id']; ?>" type="hidden">
						<div class="">
                    <dl class="hover_tips_cont">
                        <dt>商品种类：</dt>
						<dd>
							<select name="purpose" id="purpose">
								<?php if($store_type == 2): ?>
									<option value="0">选择用途</option>
								<?php endif; if($store_type == 0 or $store_type == 2): ?>
									<option value="1" <?php if($goodsInfo['purpose']==1) echo  'selected="selected"'; ?>>本店售卖</option>
								<?php endif; if($store_type == 1 or $store_type == 2): ?>
									<option value="2" <?php if($goodsInfo['purpose']==2) echo  'selected="selected"'; ?>>供应商品</option>
								<?php endif; ?>
							</select>
							<span id="err_purpose"></span>
							<p class="hint"></p>
						</dd>
                    </dl>
                </div>
                        <div class="bottom tc">
                            <label class="submit-border"><input disabled="disabled" id="submit" value="下一步，填写商品信息" class="submit" style=" width: 200px;" type="submit"></label>
                        </div>
                    </form>
                </div>
            </div>
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
<script type="text/javascript">
    $('#class_div_1').perfectScrollbar();
    $('#class_div_2').perfectScrollbar();
    $('#class_div_3').perfectScrollbar();
    $(function(){
        $(document).on("click", '#class_div_1 a', function (e) {
            var ul3 = $('#class_div_3').find('ul');
            ul3.empty().parents('.wp_category_list').addClass('blank');
            $('#submit').attr('disabled','disabled');
            $('#commodityspan').hide();
            $('#commoditydt').show();
            $('#commoditydd').show().html($(this).attr('data-title'));
            $('#class_div_1').find('a').removeClass('classDivClick');
            $(this).addClass('classDivClick');
			$("input[name=cat_id3]").attr('value', 0);
            $.ajax({
                type: "POST",
                url: "<?php echo U('Seller/Index/goods_category'); ?>",
                dataType: 'json',
                data: {parent_id: $(this).attr('data-goods-cat-id')},
                success: function (data) {
                    var ul = $('#class_div_2').find('ul');
                    ul.empty().parents('.wp_category_list').removeClass('blank');
                    var html = '';
                    $.each(data, function (n, value) {
                        html += '<li> <a href="javascript:void(0)" data-title="'+value.name+'"  data-goods-cat-id="'+value.id+'"><i class="icon-double-angle-right"></i>'+value.name+'</a></li>';
                    });
                    ul.append(html);
                }
            });
        })
        $(document).on("click", '#class_div_2 a', function (e) {
            $('#submit').attr('disabled','disabled');
            $('#class_div_2').find('a').removeClass('classDivClick');
            $(this).addClass('classDivClick');
            var cat_1 = $('#class_div_1').find('.classDivClick');
            $('#commoditydd').empty().html(cat_1.attr('data-title')+'<i class="icon-double-angle-right"></i>'+$(this).attr('data-title'));
			$("input[name=cat_id3]").attr('value', 0);
            $.ajax({
                type: "POST",
                url: "<?php echo U('Seller/Index/goods_category'); ?>",
                dataType: 'json',
                data: {parent_id: $(this).attr('data-goods-cat-id')},
                success: function (data) {
                    var ul = $('#class_div_3').find('ul');
                    ul.empty().parents('.wp_category_list').removeClass('blank');
                    var html = '';
                    $.each(data, function (n, value) {
                        html += '<li> <a href="javascript:void(0)" data-title="'+value.name+'" data-goods-cat-id="'+value.id+'"><i class="icon-double-angle-right"></i>'+value.name+'</a></li>';
                    });
                    ul.append(html);
                }
            });
        })
        $(document).on("click", '#class_div_3 a', function (e) {
            //$('#submit').removeAttr('disabled');
            $('#class_div_3').find('a').removeClass('classDivClick');
            $(this).addClass('classDivClick');
            var cat_1 = $('#class_div_1').find('.classDivClick');
            var cat_2 = $('#class_div_2').find('.classDivClick');
            $('#commoditydd').empty().html(cat_1.attr('data-title')+'<i class="icon-double-angle-right"></i>'+cat_2.attr('data-title')+'<i class="icon-double-angle-right"></i>'+$(this).attr('data-title'));
            $("input[name=cat_id3]").attr('value',$(this).attr('data-goods-cat-id'));
			var purpose = $('#purpose').val();
			if (purpose > 0){
				$('#submit').removeAttr('disabled');
			}
        }) 
		$(document).on("change", '#purpose', function (e) {
			var purpose = $('#purpose').val();
			var cat_id3 = $("input[name=cat_id3]").val();
			if (cat_id3 > 0 && purpose > 0){
				$('#submit').removeAttr('disabled');
			} else {
				$('#submit').attr('disabled','disabled');
			}
        })
    })

</script>
</body>
</html>