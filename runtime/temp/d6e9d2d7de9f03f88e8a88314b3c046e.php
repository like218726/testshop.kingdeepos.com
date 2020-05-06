<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:49:"./application/seller/new/order/delivery_list.html";i:1587634376;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/head.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/left.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/foot.html";i:1587634378;}*/ ?>
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
    <div class="ncsc-path"><i class="icon-desktop"></i>商家管理中心<i class="icon-angle-right"></i>订单物流<i class="icon-angle-right"></i>发货列表</div>
    <div class="main-content" id="mainContent">
      
<div class="tabmenu">
  <ul id="tab" class="tab pngFix">
  	<li class="<?php if(\think\Request::instance()->param('shipping_status') == 0): ?>active<?php endif; ?>" data-val="0"><a  href="#">待发货<?php echo \think\Request::instance()->param('shipping_status'); ?></a></li>
  	<li class="normal  <?php if(\think\Request::instance()->param('shipping_status') == 2): ?>active<?php endif; ?>" data-val="2"><a  href="#">部分发货</a></li>
  	<li class="normal <?php if(\think\Request::instance()->param('shipping_status') == 1): ?>active<?php endif; ?>" data-val="1"><a  href="#">已发货<?php echo \think\Request::instance()->param('shipping_status'); ?></a></li>
  	<?php if(\think\Request::instance()->action() == 'supplierDeliveryList' OR \think\Request::instance()->action() == 'supplierReturnList'): ?>
  		<li class="normal" ><a href="<?php echo U('Service/supplierReturnList'); ?>">已退换货</a></li>
  	<?php else: ?>
	  	<li class="normal" ><a href="<?php echo U('Service/return_list'); ?>">已退换货</a></li>
	<?php endif; ?>
  </ul>
  </div>
  <div class="alert alert-block mt10">
	  <ul class="mt5">
	    <li>1、发货列表包含"待发货"、"部分发货"、"已发货"三个状态</li>
	    <li>2、待发货订单可发货, 部分发货订单可继续发货, 已发货订单可打印发货单</li>
	    <li>3、已经发货订单, 如果买家没有确认收货, 系统会根据设置结算周期自动结算订单</li>
	  </ul>
</div>
<form method="post" action="<?php echo U('Order/ajaxdelivery'); ?>" id="search-form2" onsubmit="return false">
  <input type="hidden" name="shipping_status" id="shipping_status" value="<?php echo \think\Request::instance()->param('shipping_status'); ?>">
  <input type="hidden" name="seller_order_type" id="seller_order_type" value="<?php echo !empty($seller_order_type)?$seller_order_type:'1'; ?>">
  <table class="search-form">
    <tr>
      <th>收货人</th>
      <td class="w120"><input type="text" class="text w150" name="consignee" placeholder="收货人" value=""/></td>
      <th>订单编号</th>
      <td class="w120"><input type="text" class="text w150" name="order_sn" placeholder="订单编号" value=""/></td>
      <th>下单时间</th>
      <td class="w340">
	      <input type="text" class="text w120" name="start_time" id="start_time" placeholder="开始时间" value="<?php echo $begin; ?>"/>&nbsp;&#8211;&nbsp;
	      <input type="text" class="text w120" name="end_time" id="end_time" placeholder="结束时间" value="<?php echo $end; ?>"/>
	   </td>
	   <td class="w70 tc"><label class="submit-border"><input type="button" onclick="ajax_get_table('search-form2',1)" class="submit" value="搜索" /></label></td>


     </tr>
  </table>
</form>

<div style="margin-top:8px;margin-bottom:-6px;float:left;margin-left:8px;">
<input id="all" type="checkbox" value="" title="全选" />
</div>


<div id="fh" style="margin-top:6px;margin-bottom:-6px;float:left;margin-left:25px">
<a id="S2" href="javascript:void(0)"  class="ncbtn-mini" title="批量发货" style="background-color:#48CFAE">批量发货</a>
</div>


<div id="kd" style="margin-top:6px;margin-bottom:-6px;float:left;margin-left:25px;display:none">
<a id="S3" href="javascript:void(0)"  class="ncbtn-mini" title="批量发货" style="background-color:#48CFAE">批量打印快递单</a>
</div>

<div style="margin-top:6px;margin-bottom:-6px;float:left;margin-left:25px">
<a id="S1" href="javascript:void(0)" class="ncbtn-mini" title="批量发货" style="background-color:#48CFAE">批量打印配货单</a>
</div>

<div id="ajax_return">

</div>
<script>
$(document).ready(function(){	  
 	$('#start_time').layDate();
 	$('#end_time').layDate();
 	 
 	ajax_get_table('search-form2',1);
 	
 	$("#tab > li").each(function(){
		$(this).click(function(){
			console.log($(this).attr('data-val'));
			if($(this).attr('data-val')==0){
				$('#fh').show();
				$('#kd').hide();
			}else{
				$('#fh').hide();
			}

			if($(this).attr('data-val')==1){
				$('#kd').show();
				$('#fh').hide();
			}else{
				$('#kd').hide();
			}

			tabSelect(this);
		});
	});
});

var tmp=0;
$("#all").click(function(){
	if(tmp==0){
		$("input[type='checkbox']").attr("checked","true");
		tmp=1;
	}else{
		$("input[type='checkbox']").removeAttr("checked"); 
		tmp=0;
	}
}) 

var url3='<?php echo U("Order/order_print_pl"); ?>';
$("#S1").click(function(){ 
  var ids='';
  $("input[type='checkbox']:checkbox:checked").each(function(){ 
    ids+=$(this).val()+',';
  }) 
  if(!ids){
  	layer.msg('未选择订单', {icon: 2, time: 1000});
  	return false;
  }

  url3+='/ids/'+ids+'/template/picking';
  window.open(url3); 
}) 

var url='<?php echo U("Order/send_show"); ?>';
$("#S2").click(function(){ 
	var ids='';
	$("input[type='checkbox']:checkbox:checked").each(function(){ 
	ids+=$(this).val()+',';
	}) 

    if(!ids){
  	  layer.msg('未选择订单', {icon: 2, time: 1000});
  	  return false;
    }
    url+='/ids/'+ids;
	layer.open({
	    type: 2,
	    title: '批量发货',
	    shadeClose: true,
	    shade: 0.5,
	    area: ['50%', '70%'],
	    // skin: 'layui-layer-rim',
	    content: [url],
	});
}) 

//回调函数
function call_back(msg){
		layer.closeAll('iframe');
		window.location.reload();
}

$("#S3").click(function(){
	var url2='<?php echo U("Order/shipping_print_batch"); ?>'; 
	var ids='';
	$("input[type='checkbox']:checkbox:checked").each(function(){ 
	ids+=$(this).val()+',';
	}) 
	
	url2+='/ids/'+ids;
	window.location.href=url2;
}) 


function tabSelect(obj){
	var currHasClass = $(obj).hasClass('active');
	if(currHasClass)return;
	
	$("#tab > li").each(function(){
		$(this).removeClass('active');
	});
	
	$(obj).addClass('active');
	var shippingStatus = $(obj).attr("data-val");
	 
	$("#shipping_status").val(shippingStatus);
	ajax_get_table('search-form2',1);
}

//ajax 抓取页面
function ajax_get_table(tab,page,listRows=20){
    var start_time = $.trim($('#start_time').val());
    var end_time =  $.trim($('#end_time').val());
    if(start_time == '' ^ end_time == ''){
        layer.alert('请选择完整的时间间隔', {icon: 2});
        return false;
    }
    cur_page = page; //当前页面 保存为全局变量
        $.ajax({
            type : "POST",
            url:"/index.php/Seller/order/ajaxdelivery/p/"+page+"/listRows/"+listRows,//+tab,
            data : $('#'+tab).serialize(),// 你的formid
            success: function(data){
                $("#ajax_return").html('');
                $("#ajax_return").append(data);
            }
        });
}

    // 起始位置日历控件
	$('#start_time').layDate(); 
    $('#end_time').layDate();
	laydate({
	  elem: '#start_time',
	  format: 'YYYY-MM-DD', // 分隔符可以任意定义，该例子表示只显示年月
	  festival: true, //显示节日
	  istime: false,
	  choose: function(datas){ //选择日期完毕的回调
		 compare_time($('#start_time').val(),$('#end_time').val());
	  }
	});
	 
	 // 结束位置日历控件
	laydate({
	  elem: '#end_time',
	  format: 'YYYY-MM-DD', // 分隔符可以任意定义，该例子表示只显示年月
	  festival: true, //显示节日
	  istime: false,
	  choose: function(datas){ //选择日期完毕的回调
		   compare_time($('#start_time').val(),$('#end_time').val());
	  }
	});	 
</script>    
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
</body>
</html>
