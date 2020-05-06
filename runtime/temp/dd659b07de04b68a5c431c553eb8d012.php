<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:42:"./application/seller/new/freight/info.html";i:1587634376;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/head.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/left.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/foot.html";i:1587634378;}*/ ?>
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
<script src="/public/static/js/layer/laydate/laydate.js"></script>
<style>
    .ncsc-default-table td span{
        vertical-align: -moz-middle-with-baseline;
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
        <div class="ncsc-path"><i class="icon-desktop"></i>商家管理中心<i class="icon-angle-right"></i>订单物流<i class="icon-angle-right"></i>运费模板</div>
        <div class="main-content" id="mainContent">
            <div class="tabmenu">
                <ul class="tab pngFix">
                    <li class="normal"><a href="<?php echo U('Freight/index'); ?>">模板列表</a></li>
                    <li class="active"><a>新增/编辑运费模板</a></li>
                </ul>
            </div>
			<?php if($store['is_supplier'] == 1): ?>
			<div class="alert mt15 mb5">
				操作提示：
				<ul>
					<li>1、如果有供应商品使用该运费模板，当运费模板修改时，将会下架对应销售商的商品，待销售商审理后才能再次上架售卖</li>
				</ul>
			</div>
			<?php endif; ?>
            <div class="ncsc-form-default">
                <form id="handleposition" method="post" onsubmit="return false;">
                    <input type="hidden" name="template_id" value="<?php echo $freightTemplate['template_id']; ?>">
                    <dl>
                        <dt><i class="required">*</i>模板名称：</dt>
                        <dd>
                            <input class="w400 text" type="text" name="template_name" value="<?php echo $freightTemplate['template_name']; ?>" maxlength="30"/>
                            <span class="err" id="err_template_name"></span>
                            <span class="err" id="err_template_id"></span>
                            <p class="hint">请填写模板名称</p>
                        </dd>
                    </dl>
                    <dl>
                        <dt><i class="required">*</i>计价方式：</dt>
                        <dd>
                            <ul class="ncsc-form-radio-list">
                                <?php if(is_array(\think\Config::get('FREIGHT_TYPE')) || \think\Config::get('FREIGHT_TYPE') instanceof \think\Collection || \think\Config::get('FREIGHT_TYPE') instanceof \think\Paginator): $i = 0; $__LIST__ = \think\Config::get('FREIGHT_TYPE');if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$type): $mod = ($i % 2 );++$i;?>
                                    <li><label><input name="type" class="type" type="radio" value="<?php echo $key; ?>" <?php if($freightTemplate['type'] === $key): ?>checked='checked'<?php endif; ?>><?php echo $type; ?></label></li>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul>
                            <span class="err" id="err_type"></span>
                            <p class="hint"></p>
                        </dd>
                    </dl>
                    <dl>
                        <dt><i class="required">*</i>是否启用默认配送配置：</dt>
                        <dd>
                            <ul class="ncsc-form-radio-list">
                                <li><label><input name="is_enable_default" class="is_enable_default" type="radio" value="0" <?php if($freightTemplate['is_enable_default'] === 0): ?>checked='checked'<?php endif; ?>>否</label></li>
                                <li><label><input name="is_enable_default" class="is_enable_default" type="radio" value="1" <?php if($freightTemplate['is_enable_default'] === 1): ?>checked='checked'<?php endif; ?>>是</label></li>
                            </ul>
                            <span class="err" id="err_enable_default"></span>
                            <p class="hint"></p>
                        </dd>
                    </dl>
                        <table class="ncsc-default-table" id="config_table" style="display: none;">
                            <thead>
                            <tr>
                                <th class="w50"></th>
                                <th class="w200">配送区域</th>
                                <th class="w100 first_unit">首件</th>
                                <th class="w100">运费</th>
                                <th class="w100 continue_unit">续件</th>
                                <th class="w80">运费</th>
                                <th class="w80">操作</th>
                            </tr>
                            </thead>
                            <tbody id="config_list">
                                <?php if(is_array($freightTemplate[freightConfig]) || $freightTemplate[freightConfig] instanceof \think\Collection || $freightTemplate[freightConfig] instanceof \think\Paginator): $i = 0; $__LIST__ = $freightTemplate[freightConfig];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$config): $mod = ($i % 2 );++$i;if($config[is_default] == 1): ?>
                                        <tr class="bd-line default_config">
                                            <td>默认配置<input name="is_default[]" value="<?php echo $config['is_default']; ?>" type="hidden"></td>
                                            <td><input class="select_area" readonly name="" value="中国" type="text"><input name="area_ids[]" class="area_ids" value="0" type="hidden"><input name="config_id[]" value="<?php echo $config['config_id']; ?>" type="hidden"></td>
                                            <td><input name="first_unit[]" value="<?php echo $config['first_unit']; ?>" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" class="w80" type="text"><span class="first_unit_span">克</span></td>
                                            <td><input name="first_money[]" value="<?php echo $config['first_money']; ?>" class="w80" type="text"><span>元</span></td>
                                            <td><input name="continue_unit[]" value="<?php echo $config['continue_unit']; ?>" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" class="w80" type="text"><span class="continue_unit_span">克</span></td>
                                            <td><input name="continue_money[]" value="<?php echo $config['continue_money']; ?>" class="w80" type="text"><span>元</span></td>
                                            <td class="nscs-table-handle"> <span><a onclick="$(this).parent().parent().parent().remove();" class="btn-grapefruit"><i class="icon-trash"></i><p>删除</p></a></span> </td>
                                        </tr>
                                    <?php else: ?>
                                        <tr class="bd-line">
                                            <td><input name="is_default[]" value="<?php echo $config['is_default']; ?>" type="hidden"></td>
                                            <?php $region_name = '';$region_id = ''; if(is_array($config[freightRegion]) || $config[freightRegion] instanceof \think\Collection || $config[freightRegion] instanceof \think\Paginator): $i = 0; $__LIST__ = $config[freightRegion];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$freight_region): $mod = ($i % 2 );++$i;$region_name = $region_name . $freight_region->region[name] . ',';$region_id = $region_id . $freight_region->region[id] . ','; endforeach; endif; else: echo "" ;endif; $region_name = trim($region_name,',');$region_id = trim($region_id,','); ?>
                                            <td><input class="select_area" readonly name="" value="<?php echo $region_name; ?>" type="text"><input name="area_ids[]" class="area_ids" value="<?php echo $region_id; ?>" type="hidden"><input name="config_id[]" value="<?php echo $config['config_id']; ?>" type="hidden"></td>
                                            <td><input name="first_unit[]" value="<?php echo $config['first_unit']; ?>" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" class="w80" type="text"><span class="first_unit_span">克</span></td>
                                            <td><input name="first_money[]" value="<?php echo $config['first_money']; ?>" class="w80" type="text"><span>元</span></td>
                                            <td><input name="continue_unit[]" value="<?php echo $config['continue_unit']; ?>" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" class="w80" type="text"><span class="continue_unit_span">克</span></td>
                                            <td><input name="continue_money[]" value="<?php echo $config['continue_money']; ?>" class="w80" type="text"><span>元</span></td>
                                            <td class="nscs-table-handle"> <span><a onclick="$(this).parent().parent().parent().remove();" class="btn-grapefruit"><i class="icon-trash"></i><p>删除</p></a></span> </td>
                                        </tr>
                                    <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="20" style="padding-left: 60px;padding-top: 10px;">
                                    <a class="ncbtn ncbtn-mint new_config" title="新增自定义区域"><i class="icon-plus-sign"></i>新增自定义区域</a>
                                    <span class="err" id="err_config_list"></span>
                                </td>
                            </tr>
                            </tfoot>
                        </table>

                    <div class="bottom"><label class="submit-border">
                        <input id="submit" type="submit" class="submit" value="提交"></label>
                    </div>
                </form>
            </div>
            <script type="text/javascript">
                var type;//计价方式
                var unit = '件';
                $(function () {
                    $(document).on("click", '#submit', function (e) {
                        $('#submit').attr('disabled',true);
                        verifyForm();
                    })
                })
                //运费配置单个对象
                function ConfigItem(config_id, area_ids, first_unit, first_money, continue_unit, continue_money, is_default) {
                    this.config_id = config_id;
                    this.area_ids = area_ids;
                    this.first_unit = first_unit;
                    this.first_money = first_money;
                    this.continue_unit = continue_unit;
                    this.continue_money = continue_money;
                    this.is_default = is_default;
                }
                function verifyForm(){
                    $('span.err').hide();
                    var config_list = new Array();
                    var template_id = $("input[name='template_id']").val();
                    var template_name = $("input[name='template_name']").val();
                    var type = $("input[name='type']:checked").val();
                    var is_enable_default = $("input[name='is_enable_default']:checked").val();
                    var config_item = $(".bd-line");
                    config_item.each(function(i,o){
                        var area_ids_input = $(this).find("input[name^='area_ids']");
                        var first_unit_val = $(this).find("input[name^='first_unit']").val();
                        var config_id_val = $(this).find("input[name^='config_id']").val();
                        var first_money_val = $(this).find("input[name^='first_money']").val();
                        var continue_unit_val = $(this).find("input[name^='continue_unit']").val();
                        var continue_money_val = $(this).find("input[name^='continue_money']").val();
                        var is_default_val = $(this).find("input[name^='is_default']").val();
                        if (area_ids_input.val().length > 0 || $('.default_config').length > 0) {
                            var configItem = new ConfigItem(config_id_val, area_ids_input.val(), first_unit_val, first_money_val, continue_unit_val, continue_money_val, is_default_val);
                            config_list.push(configItem);
                        }
                    })
                    $.ajax({
                        type: "POST",
                        url: "<?php echo U('Freight/save'); ?>",
                        data: {template_id:template_id,template_name:template_name,type:type,config_list:config_list,is_enable_default:is_enable_default},
                        async:false,
                        dataType: "json",
                        error: function () {
                            layer.alert("服务器繁忙, 请联系管理员!");
                            $('#submit').attr('disabled',false);
                        },
                        success: function (data) {
                            if (data.status == 1) {
                                layer.msg(data.msg,{icon: 1,time: 2000},function(){
                                    location.href = "<?php echo U('Seller/Freight/index'); ?>";
                                });
                            } else {
                                $('#submit').attr('disabled',false);
                                $('span.err').text('');
                                $.each(data.result, function (index, item) {
                                    $('span.err').show();
                                    $('#err_'+index).text(item);
                                });
                                layer.msg(data.msg, {icon: 2,time: 3000});
                            }
                            $('#submit').attr('disabled',false);
                        }
                    });
                }
                $(function () {
                    $(document).on("click", '.select_area', function (e) {
                        var area_focus = $(this);
                        console.log(area_focus.val(),area_focus.parent().find('.area_ids').val());
                        var name = area_focus.val();
                        var ids = area_focus.parent().find('.area_ids').val()

                        $('.select_area').removeClass('select_area_focus');
                        $(this).addClass('select_area_focus');
                        var url = "/index.php?m=Seller&c=Freight&a=area&name="+name+"&ids="+ids;
                        layer.open({
                            type: 2,
                            title: '选择商品',
                            shadeClose: true,
                            shade: 0.2,
                            area: ['420px', '400px'],
                            content: url
                        });
                    })
                })
                $(function () {
                    $(document).on("click", '.new_config', function (e) {
                       var html = '<tr class="bd-line"> <td><input name="is_default[]" value="0" type="hidden"></td><td><input class="select_area" readonly type="text" name="" value=""/>' +
                               '<input type="hidden" name="area_ids[]" class="area_ids" value=""/><input name="config_id[]" value="" type="hidden"></td> ' +
                               '<td><input type="text" name="first_unit[]" value="" onpaste="this.value=this.value.replace(/[^\\d.]/g,\'\')" onkeyup="this.value=this.value.replace(/[^\\d.]/g,\'\')" class="w80"/>' +
                               '<span class="first_unit_span">'+unit+'</span></td> ' +
                               '<td><input type="text" name="first_money[]" value="" class="w80"/><span>元</span></td> ' +
                               '<td><input type="text" name="continue_unit[]" value="" onpaste="this.value=this.value.replace(/[^\\d.]/g,\'\')" onkeyup="this.value=this.value.replace(/[^\\d.]/g,\'\')" class="w80"/>' +
                               '<span class="continue_unit_span">'+unit+'</span></td> ' +
                               '<td><input type="text" name="continue_money[]" value="" class="w80"/><span>元</span></td> <td class="nscs-table-handle"> ' +
                               '<span><a onclick="$(this).parent().parent().parent().remove();" class="btn-grapefruit">' +
                               '<i class="icon-trash"></i><p>删除</p></a></span> </td> </tr>';
                        $('#config_list').append(html);
                    })
                })

                $(function () {
                    $(document).on("click", '.is_enable_default', function (e) {
                        initDefault();
                    })
                })
                function initDefault(){
                    var default_config_length = $('.default_config').length;
                    var is_enable_default = $("input[name='is_enable_default']:checked").val();
                    if (is_enable_default == 1 && default_config_length == 0) {
                        var html = '<tr class="bd-line default_config"><td>默认配置<input name="is_default[]" value="1" type="hidden"></td><td><input readonly type="text" name="" value="中国"/>' +
                                '<input type="hidden" name="area_ids[]" class="area_ids" value=""/><input name="config_id[]" value="" type="hidden"></td> ' +
                                '<td><input type="text" name="first_unit[]" value="" onpaste="this.value=this.value.replace(/[^\\d.]/g,\'\')" onkeyup="this.value=this.value.replace(/[^\\d.]/g,\'\')" class="w80"/>' +
                                '<span class="first_unit_span">' + unit + '</span></td> ' +
                                '<td><input type="text" name="first_money[]" value="" class="w80"/><span>元</span></td> ' +
                                '<td><input type="text" name="continue_unit[]" value="" onpaste="this.value=this.value.replace(/[^\\d.]/g,\'\')" onkeyup="this.value=this.value.replace(/[^\\d.]/g,\'\')" class="w80"/>' +
                                '<span class="continue_unit_span">' + unit + '</span></td> ' +
                                '<td><input type="text" name="continue_money[]" value="" class="w80"/><span>元</span></td> <td class="nscs-table-handle"></td> </tr>';
                        $('#config_list').prepend(html);
                    }else if(is_enable_default == 0){
                        $('.default_config').remove();
                    }
                }
                $(document).ready(function(){
                    type = $("input[name='type']:checked").val();
                    console.log(type);
                    initType();
                    initDefault();
                });
                $(function () {
                    $(document).on("click", ".type", function (e) {
                        if(typeof(type) != 'undefined' && type != $(this).val()){
                            type = $(this).val();
                            clear_freight_config();
                        }else{
                            type = $("input[name='type']:checked").val();
                            initType();
                        }
                    })
                })
                function initType(){
                    var config_table = $('#config_table');
                    if(parseInt(type) >= 0){
                        config_table.show();
                    }
                    var first_unit = $('.first_unit');
                    var continue_unit = $('.continue_unit');
                    var first_unit_span = $('.first_unit_span');
                    var continue_unit_span = $('.continue_unit_span');
                    console.log(type);
                    switch(parseInt(type))
                    {
                        case 0:
                            unit = "件";
                            first_unit.html('首件');
                            continue_unit.html('续件');
                            break;
                        case 1:
                            unit = "克";
                            first_unit.html('首重');
                            continue_unit.html('续重');
                            break;
                        case 2:
                            unit = "立方米";
                            first_unit.html('首体积');
                            continue_unit.html('续体积');
                            break;
                    }
                    first_unit_span.html(unit);
                    continue_unit_span.html(unit);
                }

                /**
                 * 清空运费模板信息
                 */
                function clear_freight_config() {
                    var template_id = $("input[name='template_id']").val();
                    layer.confirm('切换计价方式后，当前模板的运费信息将被清空，确定继续吗？', {
                        btn: ['确定', '取消']
                    }, function () {
                        if (template_id > 0) {
                            $('#config_list').empty();
                            initType();
                            layer.closeAll();
                        }else{
                            layer.closeAll();
                            type = $("input[name='type']:checked").val();
                            initType();
                        }
                    }, function (index) {
                        $("input[name='type']").attr("checked",false);
                        $("input[name='type'][value="+type+"]").attr("checked",true);
                        type = $("input[name='type']:checked").val();
                        initType();
                        layer.close(index);
                    });
                }
                function call_back(area_list) {

                    var area_list_name = '';
                    var area_list_id = '';
                    $.each(area_list, function (index, item) {
                        area_list_name += item.name + ',';
                        area_list_id += item.id + ',';
                    });
                    var area_focus = $('.select_area_focus');
                    console.log(area_focus.val(),area_focus.parent().find('.area_ids').val());
                    if(area_list_id.length > 1){
                        area_list_id = area_list_id.substr(0,area_list_id.length-1);
                        area_list_name = area_list_name.substr(0,area_list_name.length-1);
                    }
                    area_focus.val(area_list_name);
                    area_focus.parent().find('.area_ids').val(area_list_id);
                    layer.closeAll('iframe');
                }
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
