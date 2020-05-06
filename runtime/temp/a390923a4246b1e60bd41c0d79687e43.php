<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:55:"./application/seller/new/promotion/prom_goods_info.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/head.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/left.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/foot.html";i:1587634378;}*/ ?>
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
<script type="text/javascript" src="/public/plugins/Ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/public/plugins/Ueditor/ueditor.all.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/public/plugins/Ueditor/lang/zh-cn/zh-cn.js"></script>
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
        <div class="ncsc-path"><i class="icon-desktop"></i>商家管理中心<i class="icon-angle-right"></i>促销<i class="icon-angle-right"></i>商品促销管理</div>
        <div class="main-content" id="mainContent">
            <div class="tabmenu">
                <ul class="tab pngFix">
                    <li class="normal"><a href="<?php echo U('Promotion/prom_goods_list'); ?>">商品促销列表</a></li>
                    <li class="active"><a href="<?php echo U('Promotion/prom_goods_info'); ?>">新增/编辑活动</a></li>
                </ul>
            </div>
            <div class="ncsc-form-default">
                <form id="handleposition" method="post" onsubmit="return false;">
                    <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
                    <input type="hidden" id="coupon_count" value="<?php echo count($coupon_list); ?>">
                    <input type="hidden" id="is_end" value="<?php echo $info['is_end']; ?>">
                    <dl>
                        <dt><i class="required">*</i>促销活动名称：</dt>
                        <dd>
                            <input class="w400 text" type="text" name="title" id="title" value="<?php echo $info['title']; ?>" maxlength="30"/>
                            <span class="err" id="err_title"></span>
                            <p class="hint">请填写促销活动标题</p>
                        </dd>
                    </dl>
                    <dl>
                        <dt><i class="required">*</i>促销活动类型：</dt>
                        <dd>
                            <select id="prom_type" name="type" class="select">
                                <option value="0" <?php if($info[type] == 0): ?>selected<?php endif; ?>>直接打折</option>
                                <option value="1" <?php if($info[type] == 1): ?>selected<?php endif; ?>>减价优惠</option>
                                <option value="2" <?php if($info[type] == 2): ?>selected<?php endif; ?>>固定金额出售</option>
                                <option value="3" <?php if($info[type] == 3): ?>selected<?php endif; ?>>买就赠代金券</option>
                            </select>
                            <span></span>
                            <p class="hint">请选择促销活动类型</p>
                        </dd>
                    </dl>
                    <dl id="expression">
                        <dt><i class="required">*</i>折扣：</dt>
                        <dd>
                            <input name="expression" value="<?php echo $info['expression']; ?>" type="text" class="text w130"/>
                            <span class="err" id="err_expression"></span>
                            <p class="hint">% 折扣值(1-100 如果打9折，请输入90)</p>
                        </dd>
                    </dl>
                    <dl id="buy_limit">
                        <dt><i class="required">*</i>限购数量：</dt>
                        <dd>
                            <input name="buy_limit" value="<?php echo $info['buy_limit']; ?>" type="text" class="text w130" onkeyup="this.value=this.value.replace(/[^\d]/g,'')"/>
                            <span class="err" id="err_buy_limit"></span>
                            <p class="hint">限购数量</p>
                        </dd>
                    </dl>
                    <dl>
                        <dt><i class="required">*</i>开始时间：</dt>
                        <dd>
                            <input id="start_time" name="start_time" value="<?php echo $info['start_time']; ?>" type="text" class="text w130" onkeyup="this.value=this.value.replace(/[^\d]/g,'')"/><em class="add-on"><i class="icon-calendar"></i></em><span></span>
                            <span class="err" id="err_start_time"></span>
                            <p class="hint">促销开始时间</p>
                        </dd>
                    </dl>
                    <dl>
                        <dt><i class="required">*</i>结束时间：</dt>
                        <dd>
                            <input id="end_time" name="end_time" value="<?php echo $info['end_time']; ?>" type="text" class="text w130"/><em class="add-on"><i
                                class="icon-calendar"></i></em><span></span>
                            <span class="err" id="err_end_time"></span>
                            <p class="hint">促销结束时间</p>
                        </dd>
                    </dl>
                    <dl>
                        <dt><i class="required">*</i>宣传图片：</dt>
                        <dd>
                            <div class="ncsc-goods-default-pic">
                                <div class="goodspic-uplaod">
                                    <div class="upload-thumb">
                                        <img id="prom_img2" src="<?php echo (isset($info['prom_img']) && ($info['prom_img'] !== '')?$info['prom_img']:'/public/images/default_goods_image_240.gif'); ?>">
                                    </div>
                                    <input id="prom_img" name="prom_img" value="<?php echo $info['prom_img']; ?>" type="hidden">

                                    <p class="hint">上传宣传图片；支持jpg、gif、png格式上传，建议使用<font color="red">尺寸680x280像素以上、大小不超过1M的正方形图片</font>。</p>
                                    <div class="handle">
                                        <div class="ncsc-upload-btn">
                                            <a onclick="GetUploadify3(1,'','activity','img_call_back')">
                                                <p><i class="icon-upload-alt"></i>图片上传</p>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="demo"></div>
                            <span class="err" id="err_prom_img"></span>
                        </dd>
                    </dl>
                    <dl hidden>
                        <dt><i class="required">*</i>适合用户范围：</dt>
                        <dd>
                            <ul class="ncsc-form-checkbox-list">
                                <?php if(is_array($level) || $level instanceof \think\Collection || $level instanceof \think\Paginator): if( count($level)==0 ) : echo "" ;else: foreach($level as $key=>$vo): ?>
                                <li>
                                    <label>
                                        <input class="checkbox" type="checkbox" <?php if(strripos($info['group'],$vo['level_id'].'') !== false): ?>checked<?php endif; ?> name="group[]" value="<?php echo $vo['level_id']; ?>">
                                        <?php echo $vo['level_name']; ?></label>
                                </li>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul>
                            <span class="err" id="err_group"></span>
                        </dd>
                    </dl>
                    <dl>
                        <dt><i class="required">*</i>选择促销商品：</dt>
                        <dd>
                            <div style="overflow: hidden;" id="selected_group_goods">
                                <?php if(is_array($prom_goods) || $prom_goods instanceof \think\Collection || $prom_goods instanceof \think\Paginator): $i = 0; $__LIST__ = $prom_goods;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;if(empty($goods[SpecGoodsPrice]) || (($goods[SpecGoodsPrice] instanceof \think\Collection || $goods[SpecGoodsPrice] instanceof \think\Paginator ) && $goods[SpecGoodsPrice]->isEmpty())): ?>
                                        <div style="float: left;margin-right: 20px">
                                            <input type="hidden" name="goods[<?php echo $goods['goods_id']; ?>][goods_id]" value="<?php echo $goods['goods_id']; ?>"/>
                                            <input type="hidden" name="store_count[]" value="<?php echo $goods['store_count']; ?>"/>
                                            <div class="ys-btn-close" style="top: 15px;left: 172px;">×</div>
                                            <div class="selected-group-goods">
                                                <div class="goods-thumb"><img style="width: 162px;height: 162px" src="<?php echo goods_thum_images($goods['goods_id'],162,162,$goods['item_id']); ?>"/></div>
                                                <div class="goods-name">
                                                    <a target="_blank" href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$goods[goods_id])); ?>">
                                                        <?php echo $goods['goods_name']; ?>
                                                    </a>
                                                </div>
                                                <div class="goods-price">商城价：￥<?php echo $goods['shop_price']; ?>库存:<?php echo $goods['store_count']; ?></div>
                                            </div>
                                        </div>
                                        <?php else: if(is_array($goods[SpecGoodsPrice]) || $goods[SpecGoodsPrice] instanceof \think\Collection || $goods[SpecGoodsPrice] instanceof \think\Paginator): $i = 0; $__LIST__ = $goods[SpecGoodsPrice];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$spec): $mod = ($i % 2 );++$i;if($spec['prom_type'] == 3 AND $spec['prom_id'] == $info['id']): ?>
                                                    <div style="float: left;margin-right: 20px">
                                                        <input type="hidden" name="goods[<?php echo $goods['goods_id']; ?>][goods_id]" value="<?php echo $goods['goods_id']; ?>"/>
                                                        <input type="hidden" name="goods[<?php echo $goods['goods_id']; ?>][item_id][]" value="<?php echo $spec['item_id']; ?>"/>
                                                        <input type="hidden" name="store_count[]" value="<?php echo $spec['store_count']; ?>"/>
                                                        <div class="ys-btn-close" style="top: 15px;left: 172px;">×</div>
                                                        <div class="selected-group-goods">
                                                            <div class="goods-thumb">
                                                                <img style="width: 162px;height: 162px" <?php if(!(empty($spec[spec_img]) || (($spec[spec_img] instanceof \think\Collection || $spec[spec_img] instanceof \think\Paginator ) && $spec[spec_img]->isEmpty()))): ?>src="$spec[spec_img]"<?php else: ?>src="<?php echo goods_thum_images($goods['goods_id'],162,162,$goods['item_id']); ?>"<?php endif; ?>/>
                                                            </div>
                                                            <div class="goods-name">
                                                                <a target="_blank" href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$goods[goods_id],'item_id'=>$spec[item_id])); ?>"><?php echo $goods['goods_name']; ?><?php echo $spec['key_name']; ?></a>
                                                            </div>
                                                            <div class="goods-price">商城价：￥<?php echo $spec['price']; ?>库存:<?php echo $spec['store_count']; ?></div>
                                                        </div>
                                                    </div>
                                                <?php endif; endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                            </div>
                            <?php if($info['id'] > 0 AND empty($prom_goods)): ?>
                                <input class="w400 text" disabled value="商品已退出该促销活动" maxlength="30" type="text"/>
                            <?php else: ?>
                                <a href="javascript:void(0);" onclick="selectGoods(this)" class="ncbtn ncbtn-aqua" data-prom_id="<?php echo $info['prom_id']; ?>" data-prom_type="<?php echo $info['prom_type']; ?>">添加商品</a>
                                <span class="err" id="err_goods_id"></span>
                                <p class="hint">设置促销商品</p>
                            <?php endif; ?>
                        </dd>
                    </dl>
                    <dl>
                        <dt>活动描述：</dt>
                        <dd>
                            <textarea placeholder="请输入活动描述" id="post_content" name="description" class="tarea w700"><?php echo $info['description']; ?></textarea>
                            <span class="err" id="err_description"></span>
                            <p class="hint">活动描述</p>
                        </dd>
                    </dl>
                    <?php if(empty(\think\Request::instance()->get('id'))): ?>
                    <dl class="row">
                        <dt class="tit">
                            <label>是否通知用户</label>
                        </dt>
                        <dd class="opt">
                            <div class="onoff">
                                <!--<label for="mmt_message_switch1" class="cb-enable selected">是</label>-->
                                <!--<label for="mmt_message_switch0" class="cb-disable ">否</label>-->
                                <input id="mmt_message_switch1" name="mmt_message_switch" checked="checked" value="1" type="radio">
                                <label for="mmt_message_switch1" class="cb-enable selected">是</label>
                                <input id="mmt_message_switch0" name="mmt_message_switch" value="0" type="radio">
                                <label for="mmt_message_switch0" class="cb-disable ">否</label>
                            </div>
                            <p class="notic"></p>
                        </dd>
                    </dl>
                    <?php endif; ?>
                    <div class="bottom"><label class="submit-border">
                        <input id="submit" type="submit" class="submit" value="提交"></label>
                    </div>
                </form>
            </div>
            <script type="text/javascript">
                $(document).ready(function(){
                    var id = $("input[name='id']").val();
                    var is_end = $("input[id='is_end']").val();
                    if(id >0 && is_end == 1){
                        $('#submit').attr('disabled',true);
                    }
                });
                $(function () {
                    $(document).on("click", '#submit', function (e) {
                        var type = parseInt($("#prom_type").val());
                        var expression = $("[name='expression']").val();
                        if (type == 3 && expression == 0){
                            layer.msg('请选择优惠券',{icon:2});return false;
                        }
                        $('#submit').attr('disabled',true);
                        verifyForm();
                    })
                })
                function verifyForm(){
                    $('#submit').attr('disabled',true);
                    $('span.err').hide();
                    $.ajax({
                        type: "POST",
                        url: "<?php echo U('Seller/Promotion/prom_goods_save'); ?>",
                        data: $('#handleposition').serialize(),
                        async:false,
                        dataType: "json",
                        error: function () {
                            layer.alert("服务器繁忙, 请联系管理员!");
                        },
                        success: function (data) {
                            if (data.status == 1) {
                                layer.msg(data.msg, {
                                    icon: 1,
                                    time: 1000
                                }, function(){
                                    location.href = "<?php echo U('Seller/Promotion/prom_goods_list'); ?>";
                                });
                            } else {
                                $.each(data.result, function (index, item) {
                                    $('#err_' + index).text(item).show();
                                });
                                layer.msg(data.msg, {icon: 2,time: 2000},function () {
                                    $('#submit').attr('disabled',false);
                                });
                            }
                        }
                    });
                }
                
                var url="<?php echo url('Uploadify/index',array('savepath'=>'activity')); ?>";
                var ue = UE.getEditor('post_content',{
                    serverUrl :url,
                    zIndex: 999,
                    initialFrameWidth: "100%", //初化宽度
                    initialFrameHeight: 350, //初化高度            
                    focus: false, //初始化时，是否让编辑器获得焦点true或false
                    maximumWords: 99999, removeFormatAttributes: 'class,style,lang,width,height,align,hspace,valign',//允许的最大字符数 'fullscreen',
                    pasteplain:false, //是否默认为纯文本粘贴。false为不使用纯文本粘贴，true为使用纯文本粘贴
                    autoHeightEnabled: true
                });
                /**
                 * 选择商品弹出框
                 */
                function selectGoods(obj){
                    var prom_id = $(obj).data('prom_id');  //活动ID
                    var prom_type = $(obj).data('prom_type'); //活动类型
                    var url = "/index.php?m=Seller&c=Promotion&a=search_goods&prom_id="+prom_id+"&prom_type="+prom_type+"&t="+Math.random();
//                    var url = '/index.php?m=Seller&c=Promotion&a=search_goods&t='+Math.random();
                    layer.open({
                        type: 2,
                        title: '选择商品',
                        shadeClose: true,
                        shade: 0.3,
                        area: ['70%', '80%'],
                        content: url,
                    });
                }
                /**
                 * 选择商品回调时间
                 * @param goodsItem
                 */
                function call_back(goodsItem){
                    var html = '';
                    $.each(goodsItem, function (index, item) {
                        if (item.goods_id != 'on') {
                            if (item.spec != null) {
                                //有规格
                                $.each(item.spec, function (i, o) {
                                    html += '<div style="float: left;margin-right: 20px"><div class="ys-btn-close" style="top: 15px;left: 172px;">×</div>' +
                                            '<input type="hidden" name="goods[' + item.goods_id + '][goods_id]" value="' + item.goods_id + '"/>' +
                                            '<input type="hidden" name="goods[' + item.goods_id + '][item_id][' + i + ']" value="' + o.item_id + '"/>' +
                                            '<div class="selected-group-goods"><div class="goods-thumb">' +
                                            '<img style="width: 162px;height: 162px" src="' + item.goods_image + '"/></div> <div class="goods-name"> ' +
                                            '<a target="_blank" href="/index.php?m=Home&c=Goods&a=goodsInfo&id=' + item.goods_id + '">' + item.goods_name + o.key_name + '</a> </div>' +
                                            '<input type="hidden" name="store_count[]" value="'+o.store_count+'"/>' +
                                            ' <div class="goods-price">商城价：￥' + o.price + '库存:' + o.store_count + '</div> </div></div>';
                                });
                            } else {
                                html += '<div style="float: left;margin-right: 20px"><div class="ys-btn-close" style="top: 15px;left: 172px;">×</div>' +
                                        '<input type="hidden" name="goods[' + item.goods_id + '][goods_id]" value="' + item.goods_id + '"/>' +
                                        '<div class="selected-group-goods"><div class="goods-thumb">' +
                                        '<img style="width: 162px;height: 162px" src="' + item.goods_image + '"/></div> <div class="goods-name"> ' +
                                        '<a target="_blank" href="/index.php?m=Home&c=Goods&a=goodsInfo&id=' + item.goods_id + '">' + item.goods_name + '</a> </div>' +
                                        '<input type="hidden" name="store_count[]" value="'+item.store_count+'"/>' +
                                        ' <div class="goods-price">商城价：￥' + item.goods_price + '库存:' + item.store_count + '</div> </div></div>';
                            }
                        }
                    });
                    $('#selected_group_goods').append(html);
                    layer.closeAll('iframe');
                }

                function img_call_back(fileurl_tmp) {
                    $("#prom_img").attr('value',fileurl_tmp);
                    $("#prom_img2").attr('src', fileurl_tmp);
                }

                $("#prom_type").on("change",function(){
                    var type = parseInt($("#prom_type").val());
                    var coupon_count = $('#coupon_count').val()
                    if (type == 3 && coupon_count <= 0){
                        layer.msg('没有可选择的优惠券',{icon:2});
                    }
                    var expression = '';
                    switch(type){
                        case 0:{
                            expression = '<dt><i class="required">*</i>折扣：</dt>'
                                    + '<dd><input type="text" name="expression" pattern="^\\d+$" value="" class="input-txt">'
                                    + '<span class="err" id="err_expression"></span><p class="hint">% 折扣值(1-100 如果打9折，请输入90)</p></dd>';
                            break;
                        }
                        case 1:{
                            expression = '<dt><i class="required">*</i>立减金额：</dt>'
                                    + '<dd><input type="text" name="expression" pattern="^\\d+(\\.\\d+)?$" value="" class="input-txt">'
                                    + '<span class="err" id="err_expression"></span><p class="hint">立减金额（元）</p></dd>';
                            break;
                        }
                        case 2:{
                            expression = '<dt><i class="required">*</i>出售金额：</dt>'
                                    + '<dd><input type="text" name="expression" pattern="^\\d+(\\.\\d+)?$" value="" class="input-txt">'
                                    + '<span class="err" id="err_expression"></span><p class="hint">出售金额（元）</p></dd>';
                            break;
                        }
                        case 3:{
                            expression = '<dt><i class="required">*</i>代金券：</dt><dd><select name="expression"><option value="0">请选择</option>'
                                + '<?php if(is_array($coupon_list) || $coupon_list instanceof \think\Collection || $coupon_list instanceof \think\Paginator): if( count($coupon_list)==0 ) : echo "" ;else: foreach($coupon_list as $key=>$v): ?><option value="<?php echo $v['id']; ?>" <?php if($v[id] == $info[expression]): ?>selected<?php endif; ?>><?php echo $v['name']; ?></option><?php endforeach; endif; else: echo "" ;endif; ?></select>'
                                + '<span class="err" id="err_expression"></span><p class="hint">请选择代金券</p></dd>';
                            break;
                        }
                        case 4:{
                            expression = '<dt><label><i class="required">*</i>买M送N：</dt>'
                                    + '<dd><input type="text" name="expression" pattern="\\d+\/\\d+" value="" class="input-txt">'
                                    + '<span class="err" id="err_expression"></span><p class="hint">买几件送几件（如买3件送1件: 3/1）</p></dd>';
                            break;
                        }
                    }
                    $("#expression").html(expression);
                });

                $(document).ready(function(){
                    $('#start_time').layDate();
                    $('#end_time').layDate();

                    $("#prom_type").trigger('change');
                    $('input[name=expression]').val("<?php echo $info['expression']; ?>");
                })
                //商品删除按钮事件
                $(function () {
                    $(document).on("click", '.ys-btn-close', function (e) {
                        $(this).parent().remove();
                    })
                    $(document).on("keyup", '.input-txt', function (e) {
                        this.value=/^\d+\.?\d{0,2}$/.test(this.value) ? this.value : ''
                    })
                })
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
