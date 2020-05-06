<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:49:"./application/seller/new/index/store_service.html";i:1587634376;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/head.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/left.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/foot.html";i:1587634378;}*/ ?>
<style>
	.btn {
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
}
.btn-primary {
    color: #fff;
    background-color: #36BC9B;
    border-color: #36BC9B;
    margin: 10px 0;
}
.btn {
    border-radius: 3px;
    -webkit-box-shadow: none;
    box-shadow: none;
    border: 1px solid transparent;
}
.table-bordered .form-control{
	height: 30px;
}
.table-bordered tr td{
	padding: 5px 0;
}
.presales_tr,.aftersales_tr{
	display: none;
}
</style>
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
		<div class="ncsc-path"><i class="icon-desktop"></i>商家管理中心<i class="icon-angle-right"></i>客服消息<i class="icon-angle-right"></i>客服设置</div>
		<div class="main-content" id="mainContent">
			<div class="alert alert-block mt10">
				<ul class="mt5">
					<li>1、客服信息需要填写完整，不完整信息将不会被保存.</li>
				</ul>
			</div>
			<div class="item-publish">
				<form method="post" id="handlepost" action="<?php echo U('Index/store_service'); ?>">
					<div class="tab-content">
						<div class="row" style="margin:20px auto;text-align:center;">
							<table class="table table-bordered">
								<tbody>
								<tr>
									<td>售前客服：</td>
									<td>客服名称</td>
									<td>客服工具</td>
									<td>客服账号</td>
									<td></td>
								</tr>

								<?php if(is_array($store[store_presales]) || $store[store_presales] instanceof \think\Collection || $store[store_presales] instanceof \think\Paginator): if( count($store[store_presales])==0 ) : echo "" ;else: foreach($store[store_presales] as $k=>$v): ?>
									<tr class="presales_tr" style="display:table-row;">
										<td></td>
										<td><input type="text" class="form-control" placeholder="客服名称" name="pre[<?php echo $k; ?>][name]" value="<?php echo $v['name']; ?>" /></td>
										<td>
											<select class="form-control"  name="pre[<?php echo $k; ?>][type]">
												<option value="ww" <?php if($v['type'] == 'ww'): ?>selected="selected"<?php endif; ?> >旺旺</option>
												<option value="qq" <?php if($v['type'] == 'qq'): ?>selected="selected"<?php endif; ?> >QQ</option>
												<option value="IM" <?php if($v['type'] == 'IM'): ?>selected="selected"<?php endif; ?> >站内IM</option>
											</select>
										</td>
										<td><input type="text" class="form-control" placeholder="客服账号"  name="pre[<?php echo $k; ?>][account]" value="<?php echo $v['account']; ?>" /></td>
										<td><input type="button" class="btn btn-default del_sales_btn" value="- 删除" /></td>
									</tr>
								<?php endforeach; endif; else: echo "" ;endif; ?>

								<tr class="presales_tr">
									<td></td>
									<td><input type="text" class="form-control" placeholder="客服名称" name="pre[0][name]"></td>
									<td>
										<select class="form-control"  name="pre[0][type]">
											<option value="ww">旺旺</option>
											<option value="qq">QQ</option>
											<option value="IM">站内IM</option>
										</select>
									</td>
									<td><input type="text" class="form-control" placeholder="客服账号"  name="pre[0][account]" value="" /></td>
									<td><input type="button" class="btn btn-default del_sales_btn" value="- 删除" /></td>
								</tr>
								<tr class="bd-line">
									<td></td>
									<td colspan="4" align="left">
										<input type="button" class="btn btn-primary" value="+添加售前客服" id="presales_btn">
									</td>
								</tr>
								<tr>
									<td>售后客服：</td>
									<td>客服名称</td>
									<td>客服工具</td>
									<td>客服账号</td>
									<td></td>
								</tr>
								<?php if(is_array($store[store_aftersales]) || $store[store_aftersales] instanceof \think\Collection || $store[store_aftersales] instanceof \think\Paginator): if( count($store[store_aftersales])==0 ) : echo "" ;else: foreach($store[store_aftersales] as $k=>$v): ?>
									<tr  class="aftersales_tr" style="display:table-row;">
										<td></td>
										<td><input type="text" class="form-control" placeholder="客服名称" name="after[<?php echo $k; ?>][name]" value="<?php echo $v['name']; ?>" /></td>
										<td>
											<select class="form-control"  name="after[<?php echo $k; ?>][type]">
												<option value="ww" <?php if($v['type'] == 'ww'): ?>selected="selected"<?php endif; ?> >旺旺</option>
												<option value="qq" <?php if($v['type'] == 'qq'): ?>selected="selected"<?php endif; ?> >QQ</option>
												<option value="IM" <?php if($v['type'] == 'IM'): ?>selected="selected"<?php endif; ?> >站内IM</option>
											</select>
										</td>
										<td><input type="text" class="form-control" placeholder="客服账号"  name="after[<?php echo $k; ?>][account]" value="<?php echo $v['account']; ?>" /></td>
										<td><input type="button" class="btn btn-default del_sales_btn" value="- 删除" /></td>
									</tr>
								<?php endforeach; endif; else: echo "" ;endif; ?>
								<tr  class="aftersales_tr" >
									<td></td>
									<td><input type="text" class="form-control" placeholder="客服名称" name="after[0][name]" /></td>
									<td>
										<select class="form-control"  name="after[0][type]">
											<option value="ww" <?php if($v['type'] == 'ww'): ?>selected="selected"<?php endif; ?> >旺旺</option>
											<option value="qq" <?php if($v['type'] == 'qq'): ?>selected="selected"<?php endif; ?> >QQ</option>
											<!--<option value="IM" <?php if($v['type'] == 'IM'): ?>selected="selected"<?php endif; ?> >站内IM</option>-->
										</select>
									</td>
									<td><input type="text" class="form-control" placeholder="客服账号"  name="after[0][account]" value="" /></td>
									<td><input type="button" class="btn btn-default del_sales_btn" value="- 删除" /></td>
								</tr>

								<tr class="bd-line">
									<td></td>
									<td colspan="4" align="left">
										<input type="button" class="btn btn-primary" value="+添加售后客服" id="aftersales_btn">
									</td>
								</tr>
								<tr>
									<td>工作时间：</td>
									<td colspan="4" align="left">例：（工作时间 AM 10:00 - PM 18:00）</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td colspan="4" align="left">
										<textarea rows="3" cols="100" name="working_time"><?php echo $store['store_workingtime']; ?></textarea>
									</td>
								</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="bottom tc hr32">
						<label class="submit-border">
							<input class="submit" value="保存" type="submit" onclick="adsubmit()">
						</label>
					</div>
				</form>
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
<script>
	function adsubmit(){
		// 表单提交
		$('#handlepost').submit();
	}
	$(document).ready(function(){
		// 添加售前客服
		$('#presales_btn').click(function(){
			var count = $('.presales_tr').length; // 当前的数量
			var presales_tr = $(this).parent().parent().prev('.presales_tr'); // 找到这个按钮的所在 tr 的前面的那个tr
			var clont_tr = presales_tr.clone().css('display','table-row'); // 克隆一份出来
			clont_tr.find('input[name="pre\[0\]\[name\]"]').val('售前'+count).attr('name',"pre["+count+"][name]");// 设置刚刚克隆出来里面 input 的值
			clont_tr.find('select[name="pre\[0\]\[type\]"]').attr('name',"pre["+count+"][type]");
			clont_tr.find('input[name="pre\[0\]\[account\]"]').attr('name',"pre["+count+"][account]");
			presales_tr.before(clont_tr); // 在塞到 刚刚前面那个tr 里面去
		});

		// 添加售后客服
		$('#aftersales_btn').click(function(){
			var count = $('.aftersales_tr').length; // 当前的数量
			var aftersales_tr = $(this).parent().parent().prev('.aftersales_tr'); // 找到这个按钮的所在 tr 的前面的那个tr
			var clont_tr = aftersales_tr.clone().css('display','table-row'); // 克隆一份出来
			clont_tr.find('input[name="after\[0\]\[name\]"]').val('售后'+count).attr('name',"after["+count+"][name]");  // 设置刚刚克隆出来里面 input 的值
			clont_tr.find('select[name="after\[0\]\[type\]"]').attr('name',"after["+count+"][type]");
			clont_tr.find('input[name="after\[0\]\[account\]"]').attr('name',"after["+count+"][account]");
			aftersales_tr.before(clont_tr); // 在塞到 刚刚前面那个tr 里面去
		});

		// 删除某一行
		$(document).on('click','.del_sales_btn',function(){
			$(this).parent().parent().remove();
		});
	});
</script>
</body>
</html>
