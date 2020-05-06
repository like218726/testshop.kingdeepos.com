<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:48:"./application/seller/new/coupon/coupon_info.html";i:1587634376;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/head.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/left.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/foot.html";i:1587634378;}*/ ?>
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
        <div class="ncsc-path"><i class="icon-desktop"></i>商家管理中心<i class="icon-angle-right"></i>促销<i class="icon-angle-right"></i>代金券管理</div>
        <div class="main-content" id="mainContent">
            <div class="tabmenu">
                <ul class="tab pngFix">
                    <li class="normal"><a href="<?php echo U('Coupon/index'); ?>">返回优惠券列表</a></li>
                    <li class="active"><a>新增/编辑活动</a></li>
                </ul>
            </div>
            <div class="alert alert-block mt10 mb10">
	            <ul>
					<li>1、下单赠送，一般用在商品优惠活动中赠送给完成订单的会员</li>
					<li>2、免费领取，该类型的优惠券在店铺首页会员可以直接领取</li>
					<li>3、指定发放，则是属于不公开的优惠券,商家可以指定例如关注店铺会员发放</li>
					<li>4、线下发放，则表示通过打印成实体券，并且生成验证劵码，用户凭借券码消费</li>
				</ul>
			</div>
            <div class="ncsc-form-default">
                <form id="handleposition" onsubmit="return false;">
                    <input type="hidden" name="id" value="<?php echo $coupon['id']; ?>">
                    <!--<input type="hidden" id="formtoken" name="__token__" value="<?php echo \think\Request::instance()->token(); ?>">-->
                    <dl>
                        <dt><i class="required">*</i>优惠券名称：</dt>
                        <dd>
                            <input class="w400 text" type="text" id="name" name="name" value="<?php echo $coupon['name']; ?>" maxlength="30"/>
                            <span class="err" id="err_name"></span>
                            <p class="hint">请填写优惠券名称</p>
                        </dd>
                    </dl>
                    <dl>
                        <dt><i class="required">*</i>优惠券面额：</dt>
                        <dd>
                            <select id="money" name="money">
                                <?php if(is_array($coupon_price_list) || $coupon_price_list instanceof \think\Collection || $coupon_price_list instanceof \think\Paginator): $i = 0; $__LIST__ = $coupon_price_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                    <option value="<?php echo $vo['coupon_price_value']; ?>" <?php if($vo['coupon_price_value'] == $coupon['money']): ?>selected="selected"<?php endif; ?> >
                                    <?php echo $vo['coupon_price_value']; ?>元
                                    </option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                            <span class="err" id="err_money"></span>
                            <p class="hint">优惠券面额由平台设置，优惠券可抵扣金额,单位：元</p>
                        </dd>
                    </dl>
                    <dl>
                        <dt><i class="required">*</i>消费金额：</dt>
                        <dd>
                            <input id="condition" name="condition" value="<?php echo $coupon['condition']; ?>"  onpaste="this.value=this.value.replace(/[^\d.]/g,'')" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" type="text" class="text w130" maxlength="8"/>
                            <span class="err" id="err_condition"></span>
                            <p class="hint">订单需满足的最低消费金额(必需为整数)才能使用</p>
                        </dd>
                    </dl>
                    <dl>
                        <dt>发放类型：</dt>
                        <dd>
                            <ul class="ncsc-form-radio-list">
                                <li>
                                    <label><input name="type" type="radio" value="0" <?php if($coupon['type'] == 0): ?>checked<?php endif; ?>>下单赠送</label>
                                </li>
                                <li>
                                    <label><input name="type" type="radio" value="1" <?php if($coupon['type'] == 1): ?>checked<?php endif; ?>>指定发放</label>
                                </li>
                                <li>
                                    <label><input name="type" type="radio" value="2" <?php if($coupon['type'] == 2): ?>checked<?php endif; ?>>免费领取</label>
                                </li>
                                <li>
                                    <label><input name="type" type="radio" value="3" <?php if($coupon['type'] == 3): ?>checked<?php endif; ?>>线下发放</label>
                                </li>
                            </ul>
                            <span class="err" id="err_type"></span>
                            <p class="hint"></p>
                        </dd>
                    </dl>
                    <dl>
                        <dt><i class="required">*</i>可发放总数：</dt>
                        <dd>
                            <input id="createnum" name="createnum" value="<?php echo (isset($coupon['createnum']) && ($coupon['createnum'] !== '')?$coupon['createnum']:0); ?>"  onpaste="this.value=this.value.replace(/[^\d.]/g,'')" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" type="text" class="text w130"/>
                            <span class="err" id="err_createnum"></span>
                            <p class="hint">发放数量限制(默认为0则无限制)</p>
                        </dd>
                    </dl>
                        <dl class="timed" <?php if($coupon['type'] == 0): ?> hidden<?php endif; ?>>
                            <dt><i class="required">*</i>发放起始日期：</dt>
                            <dd>
                                <input id="send_start_time" name="send_start_time" value="<?php echo date('Y-m-d H:i:s',$coupon['send_start_time']); ?>"  type="text" class="text w130"/><em class="add-on"><i class="icon-calendar"></i></em><span></span>
                                <span class="err" id="err_send_start_time"></span>
                                <p class="hint">发放起始日期</p>
                            </dd>
                        </dl>
                        <dl class="timed" <?php if($coupon['type'] == 0): ?> hidden<?php endif; ?>>
                            <dt><i class="required">*</i>发放结束日期：</dt>
                            <dd>
                                <input id="send_end_time" name="send_end_time"  value="<?php echo date('Y-m-d H:i:s',$coupon['send_end_time']); ?>" type="text" class="text w130"/><em class="add-on"><i
                                    class="icon-calendar"></i></em><span></span>
                                <span class="err" id="err_send_end_time"></span>
                                <p class="hint">发放结束日期</p>
                            </dd>
                        </dl>
                    <dl>
                        <dt><i class="required">*</i>使用起始日期：</dt>
                        <dd>
                            <input id="use_start_time" name="use_start_time" value="<?php echo date('Y-m-d H:i:s',$coupon['use_start_time']); ?>"  type="text" class="text w130"/><em class="add-on"><i class="icon-calendar"></i></em><span></span>
                            <span class="err" id="err_use_start_time"></span>
                            <p class="hint">使用起始日期</p>
                        </dd>
                    </dl>
                    <dl>
                        <dt><i class="required">*</i>使用结束日期：</dt>
                        <dd>
                            <input id="use_end_time" name="use_end_time"  value="<?php echo date('Y-m-d H:i:s',$coupon['use_end_time']); ?>" type="text" class="text w130"/><em class="add-on"><i
                                class="icon-calendar"></i></em><span></span>
                            <span class="err" id="err_use_end_time"></span>
                            <p class="hint">使用结束日期</p>
                        </dd>
                    </dl>
                    <dl>
				      <dt><i class="required">*</i>代金券描述：</dt>
				      <dd>
				        <textarea name="coupon_info" class="textarea w400 h600 valid"><?php echo $coupon['coupon_info']; ?></textarea>
                          <span class="err" id="err_coupon_info"></span>
				      </dd>
				    </dl>
				    <dl>
				      <dt><i class="required">*</i>可使用商品：</dt>
				      <dd>
                          <label>
				        <input type="radio" value="0" name="use_type" onclick="use_type_tab(0)" <?php if($coupon['use_type'] == 0): ?>checked<?php endif; ?>>全店通用</label>
                          <label>
				      	<input type="radio" value="1" name="use_type" onclick="javascript:selectGoods();" <?php if($coupon['use_type'] == 1): ?>checked<?php endif; ?>>指定商品
                          </label>
                          <label>
				        <input type="radio" value="2" name="use_type" onclick="use_type_tab(2)" <?php if($coupon['use_type'] == 2): ?>checked<?php endif; ?>>指定分类
                              </label>
                        <span class="err" id="err_use_type"></span>
				      </dd>
				    </dl>
				    <dl id="goods_all_cate" style="display:<?php if($coupon[use_type] == 2): ?>;<?php else: ?>none;<?php endif; ?>">
                            <dt><i class="required">*</i>限制商品分类使用：</dt>
                            <dd>
                                <select name="cat_id1" id="cat_id1" onchange="get_category2(this.value,'cat_id2','0');" class="valid">
                                    <option value="0">请选择商品分类</option>
                                    <?php if(is_array($cat_list) || $cat_list instanceof \think\Collection || $cat_list instanceof \think\Paginator): if( count($cat_list)==0 ) : echo "" ;else: foreach($cat_list as $k=>$v): ?>
                                        <option value="<?php echo $v['id']; ?>" <?php if($v['id'] == $coupon['cat_id1']): ?>selected="selected"<?php endif; ?> >
                                        <?php echo $v['name']; ?>
                                        </option>
                                    <?php endforeach; endif; else: echo "" ;endif; ?> 
                                </select>
                                <select name="cat_id2" id="cat_id2" onchange="get_category2(this.value,'cat_id3','0');" class="valid">
                                	<option value="0">请选择商品分类</option>
                                </select>
                                <select name="cat_id3" id="cat_id3" class="valid">
                                	<option value="0">请选择商品分类</option>
                                </select>
                                <span class="err" id="err_cat_id1"></span>
                                <span class="err" id="err_cat_id2"></span>
                                <span class="err" id="err_cat_id3"></span>
                                <p class="hint">若不选表示不限制，否则请选择到指定三级分类</p>
                            </dd>
                    </dl>
                    
                    <dl id="enable_goods" style="display:<?php if($coupon[use_type] == 1): ?>;<?php else: ?>none;<?php endif; ?>">
                        <dt>指定商品列表：</dt>
                        <dd>
                            <table class="ncsc-default-table">
                                <thead>
                                <tr>
                                    <th class="w80">商品名称</th>
                                    <th class="w80">价格</th>
                                    <th class="w80">库存</th>
                                    <th class="w80">操作</th>
                                </tr>
                                </thead>
                                <tbody id="goods_list">
                                <?php if(is_array($enable_goods) || $enable_goods instanceof \think\Collection || $enable_goods instanceof \think\Paginator): if( count($enable_goods)==0 ) : echo "" ;else: foreach($enable_goods as $key=>$vo): ?>
                                    <tr>
                                        <td style="display:none"><input type="checkbox" name="goods_id[]" class="goods_id" checked="checked" value="<?php echo $vo['goods_id']; ?>"/></td>
                                        <td><?php echo $vo['goods_name']; ?></td>
                                        <td><?php echo $vo['shop_price']; ?></td>
                                        <td><?php echo $vo['store_count']; ?></td>
                                        <td class="nscs-table-handle">
                                            <span><a onclick="$(this).parent().parent().parent().remove();" class="btn-grapefruit"><i class="icon-trash"></i><p>删除</p></a></span>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                </tbody>
                            </table>
                            <span class="err" id="err_goods_id"></span>
                        </dd>
                    </dl>
				    <dl>
				      	<dt><em class="pngFix"></em>状态：</dt>
				      	<dd>
				      		<input type="radio" value="1" name="status" <?php if($coupon['status'] != 2): ?>checked<?php endif; ?>> 有效
				      		<input type="radio" value="2" name="status" <?php if($coupon['status'] == 2): ?>checked<?php endif; ?>> 失效	      	
				      	</dd>
				    </dl>
                    <div class="bottom"><label class="submit-border">
                        <input id="submit" type="submit" class="submit" value="提交"></label>
                    </div>
                </form>
            </div>
            <script type="text/javascript">
                $(function () {
                    $(document).on("click", '#submit', function (e) {
                        verifyForm();
                    })
                })
                function verifyForm(){
                    $('span.err').show();
                    $.ajax({
                        type: "POST",
                        url: "<?php echo U('Seller/Coupon/coupon_info'); ?>",
                        data: $('#handleposition').serialize(),
                        async:false,
                        dataType: "json",
                        error: function () {
                            layer.alert("服务器繁忙, 请联系管理员!");
                        },
                        success: function (data) {
                            if (data.status == 1) {
                                layer.msg(data.msg, {icon: 1});
                                location.href = "<?php echo U('Seller/Coupon/index'); ?>";
                            } else {
                                var ss='';
                                $.each(data.result, function (index, item) {
                                    ss += item+',</br>';
                                });
                                layer.msg(ss, {icon: 2,time: 3000});
//                                $('#formtoken').val(data.token);
                            }
                        }
                    });
                }
                $('input[name="type"]').click(function(){
                    if($(this).val() == 0){
                        $('.timed').hide();
                    }else{
                        $('.timed').show();
                    }
                })

                $(document).ready(function(){
                    $('#send_start_time').layDate();
                    $('#send_end_time').layDate();
                    $('#use_start_time').layDate();
                    $('#use_end_time').layDate();
                    
                    <?php if($coupon['cat_id2'] > 0): ?>
                    	get_category2("<?php echo $coupon['cat_id1']; ?>",'cat_id2',"<?php echo $coupon['cat_id2']; ?>");
		            <?php endif; if($coupon['cat_id3'] > 0): ?>
		                 get_category2("<?php echo $coupon['cat_id2']; ?>",'cat_id3',"<?php echo $coupon['cat_id3']; ?>");
		            <?php endif; ?>
		            
                })

            /**
		     * 获取多级联动的商品分类
		     */
		    function get_category2(id, next, select_id) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo U('Seller/Index/goods_category'); ?>",
                    dataType: 'json',
                    data: {parent_id: id},
                    success: function (data) {
                        var html = '<option value="0">请选择商品分类</option>';
                        $.each(data, function (n, value) {
                            html += '<option value="'+value.id+'">'+value.name+'</option>';
                        });
                        $('#' + next).empty().html(html);
                        (select_id > 0) && $('#' + next).val(select_id);//默认选中
                    }
                });
		    }
                
            function selectGoods(){
            	 use_type_tab(1);
                 var goods_id = [];
                 //过滤选择重复商品
                 $('.goods_id').each(function(i,o){
                     goods_id.push($(o).val());
                 });
                var url = '/index.php?m=Seller&c=Promotion&a=search_goods&exvirtual=1&nospec=1&goods_id='+goods_id+'&t='+Math.random();
                 layer.open({
                     type: 2,
                     title: '选择商品',
                     shadeClose: true,
                     shade: 0.3,
                     area: ['70%', '80%'],
                     content: url,
                 });
            }
            
            function call_back(table_html)
            {
                layer.closeAll('iframe');
                var goods_list_html='';
                console.log(table_html);
                $.each(table_html, function (n, value) {
                    goods_list_html += ' <tr>' +
                            '<td style="display:none"><input type="checkbox" name="goods_id[]" class="goods_id" checked="checked" value="'+value.goods_id+'"/></td>' +
                            '<td>'+value.goods_name+'</td><td>'+value.goods_price+'</td>' +
                            '<td>'+value.store_count+'</td>' +
                            '<td class="nscs-table-handle"><span><a onclick="$(this).parent().parent().parent().remove();" class="btn-grapefruit"><i class="icon-trash"></i><p>删除</p></a></span></td>' +
                            '</tr>';
                });
                $('#goods_list').append(goods_list_html);
            }
            
            function use_type_tab(v){
            	if(v == 0){
            		$('#goods_all_cate').hide();
            		$('#enable_goods').hide();
                    $('#goods_list').html('');
            	}
            	if(v == 1){
            		$('#enable_goods').show()
            		$('#goods_all_cate').hide();
            	}
            	if(v == 2){
            		$('#goods_all_cate').show();
            		$('#enable_goods').hide();
                    $('#goods_list').html('');
            	}
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