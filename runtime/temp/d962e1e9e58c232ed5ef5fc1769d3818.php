<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:49:"./application/seller/new/store/store_setting.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/head.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/left.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/foot.html";i:1587634378;}*/ ?>
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
    em{
        font: 12px/16px Tahoma;
        color: #F30;
        vertical-align: middle;
        margin-right: 4px;
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
    <div class="ncsc-path">
    	<i class="icon-desktop"></i>商家管理中心
    	<i class="icon-angle-right"></i>店铺
    	<i class="icon-angle-right"></i>店铺设置
    </div>
    <div class="main-content" id="mainContent">     
<div class="tabmenu">
  <ul class="tab pngFix">
  <li <?php if(ACTION_NAME == 'store_setting'): ?>class="active"<?php else: ?>class="normal"<?php endif; ?>><a  href="<?php echo U('Store/store_setting'); ?>">店铺设置</a></li>
  <li <?php if(ACTION_NAME == 'store_slide'): ?>class="active"<?php else: ?>class="normal"<?php endif; ?>><a  href="<?php echo U('Store/store_slide'); ?>">幻灯片设置</a></li>
  <li <?php if(ACTION_NAME == 'store_theme'): ?>class="active"<?php else: ?>class="normal"<?php endif; ?>><a  href="<?php echo U('Store/store_theme'); ?>">店铺主题</a></li>
  <li <?php if(ACTION_NAME == 'mobile_slide'): ?>class="active"<?php else: ?>class="normal"<?php endif; ?>><a  href="<?php echo U('Store/mobile_slide'); ?>">手机店铺设置</a></li>
  <li <?php if(ACTION_NAME == 'getpoint'): ?>class="active"<?php else: ?>class="normal"<?php endif; ?>><a  href="<?php echo U('Store/getpoint'); ?>">拾取店铺坐标</a></li>
  </ul>
</div>
<div class="ncsc-form-default">
  <form method="post" id="handlepost" action="<?php echo U('Store/setting_save'); ?>" enctype="multipart/form-data">
    <dl>
      <dt>店铺等级：</dt>
      <dd><p><?php echo $store['grade_name']; ?></p></dd>
    </dl>
    <dl>
      <dt>店铺名称：</dt>
      <dd><p><?php echo $store['store_name']; ?></p></dd>
    </dl>
    <dl>
      <dt>主营商品：</dt>
      <dd>
          <textarea name="store_zy" rows="2" class="textarea w400"  maxlength="50" ><?php echo $store['store_zy']; ?></textarea>
        <p class="hint">关键字最多可输入50字，请用","进行分隔，例如”男装,女装,童装”</p>
      </dd>
    </dl>
    <dl>
      <dt>店铺logo：</dt>
      <dd>
        <div class="ncsc-upload-thumb store-logo" nctype="store_label">
			<p><img height="60" id="store_logo" src="<?php if(empty($store['store_logo']) || (($store['store_logo'] instanceof \think\Collection || $store['store_logo'] instanceof \think\Paginator ) && $store['store_logo']->isEmpty())): ?>/public/images/not_adv.jpg<?php else: ?><?php echo $store['store_logo']; endif; ?>"></p>
        </div>
        <div class="ncsc-upload-btn"> 
         <a href="javascript:void(0);"><span>
          <input type="hidden" name="store_logo" value="<?php echo $store['store_logo']; ?>">
          <input type="button" onClick="GetUploadify3(1,'store_logo','seller','callback1')" hidefocus="true" size="1" class="input-file" name="store_label" id="storeLablePic" nc_type="change_store_label"/>
          </span>
          <p><i class="icon-upload-alt"></i>图片上传</p>
          </a> 
        </div>
        <p class="hint">此处理店铺页logo；<br/><span style="color:orange;">建议使用宽200像素-高60像素内的GIF或PNG透明图片；点击下方"提交"按钮后生效。</span></p>
      </dd>
    </dl>
    <dl>
      <dt>店铺条幅： </dt>
      <dd>
        <div class="ncsc-upload-thumb store-banner" nctype="store_banner">
          <p><img height="120" id="store_banner" src="<?php if(empty($store['store_banner']) || (($store['store_banner'] instanceof \think\Collection || $store['store_banner'] instanceof \think\Paginator ) && $store['store_banner']->isEmpty())): ?>/public/images/not_adv.jpg<?php else: ?><?php echo $store['store_banner']; endif; ?>"></p>
        </div>
        <div class="ncsc-upload-btn"> <a href="javascript:void(0);"><span>
          <input type="hidden" name="store_banner" value="<?php echo $store['store_banner']; ?>">
          <input type="button" hidefocus="true" onClick="GetUploadify3(1,'store_banner','seller','callback2')" size="1" class="input-file" nc_type="change_store_banner"/>
          </span>
          <p><i class="icon-upload-alt"></i>图片上传</p>
          </a></div>
        <p class="hint">此处为店铺页banner导航；<br/><span style="color:orange;">建议使用宽1200像素*高130像素的图片；点击下方"提交"按钮后生效。</span></p>
      </dd>
    </dl>
    <dl>
        <dt>店铺头像： </dt>
        <dd>
        <div class="ncsc-upload-thumb store-logo" nctype="store_label">
			<p><img height="60" id="store_avatar" src="<?php if(empty($store['store_avatar']) || (($store['store_avatar'] instanceof \think\Collection || $store['store_avatar'] instanceof \think\Paginator ) && $store['store_avatar']->isEmpty())): ?>/public/images/not_adv.jpg<?php else: ?><?php echo $store['store_avatar']; endif; ?>"></p>
        </div>	
        <div class="ncsc-upload-btn">
           <a href="javascript:void(0);"><span>
           <input type="hidden" value="<?php echo $store['store_avatar']; ?>" name="store_avatar">
          	<input type="button" onClick="GetUploadify3(1,'store_avatar','seller','callback3')" hidefocus="true" size="1" class="input-file"  nc_type="change_store_avatar"/>
          </span>
          <p><i class="icon-upload-alt"></i>图片上传</p>
          </a>
          </div> 
          <p class="hint" style="color:red">请上传店铺logo一样的图片；用于需要显示正方形LOGO<br/><span style="color:orange;">建议使用宽100像素*高100像素内的方型图片；点击下方"提交"按钮后生效。</span></p>
        </dd>
     </dl>
     <dl>
      <dt>店铺二级域名：</dt>
      <dd>
        <input class="text w200" name="store_domain" type="text"  id="store_domain" value="<?php echo $store['store_domain']; ?>" />
        <p class="hint">请输入3~50位的字符串, 例如输入'aa',商城主域名是xxx.com,那么可以通过aa.xxx.com访问你的店铺</p>
        <?php if($store['domain_enable'] == 1): ?>
        	<p class="hint" style="color:green"><strong>已启用:可通过二级域名访问此店铺</strong></p>
        <?php else: ?>
        	<p class="hint" style="color:red"><strong>未启用:请联系平台管理员开启</strong></p>
        <?php endif; ?>
      </dd>
    </dl>
     <dl>
      <dt>客服QQ：</dt>
      <dd>
        <input class="w200 text" name="store_qq" type="number"  id="store_qq" value="<?php echo $store['store_qq']; ?>" />
      </dd>
    </dl>
    <dl>
      <dt>阿里旺旺：</dt>
      <dd>
        <input class="text w200" name="store_aliwangwang" type="text"  id="store_ww" value="<?php echo $store['store_aliwangwang']; ?>" maxlength="64" />
      </dd>
    </dl>
      <dl>
      <dt>邮编：</dt>
      <dd>
        <input class="text w200" name="store_zip" onpaste="this.value=this.value.replace(/[^\d]/g,'')"  onkeyup="this.value=this.value.replace(/[^\d]/g,'')"type="text"  id="store_zip" value="<?php echo $store['store_zip']; ?>" maxlength="6"/>
      </dd>
    </dl>
    <dl>
      <dt><em>*</em>店铺电话：</dt>
      <dd>
        <input class="text w200" name="store_phone" maxlength="20" type="text"  id="store_phone" value="<?php echo $store['store_phone']; ?>" />
        <p class="hint">电话号码格式, 例如: 0755-86140485 400-12345678</p>
      </dd>
    </dl>
    <dl>
      <dt><em>*</em>服务电话：</dt>
      <dd>
        <input class="text w200" pattern="^\d{1,}$" name="service_phone" maxlength="20" type="text"  id="service_phone" value="<?php echo $store['service_phone']; ?>" />
        <p class="hint"><span style="color:red;">重要: 服务电话, 客户下单时发送提醒短信到到商家手机, 例如: 13800138006</span></p>
      </dd>
    </dl>
    <dl>
      <dt>库存预警：</dt>
      <dd>
        <input class="text w50" name="store_warning_storage" type="text" maxlength="3" value="<?php echo $store['store_warning_storage']; ?>" onpaste="this.value=this.value.replace(/[^\d]/g,'')" onKeyUp="this.value=this.value.replace(/[^\d]/g,'')"  onblur="checkInputNum(this.name,0,999);" />
        <span></span>
        <p class="hint">库存少于预警数则报警提示</p>
      </dd>
    </dl>
    
    <dl>
      <dt>地址：</dt>
      <dd>
      	 &nbsp;&nbsp;&nbsp;&nbsp;
      	 <select onchange="get_city(this,0)" id="province" name="province_id" class="form-control" style="margin-left:-15px;">
           <option  value="0">选择省份</option>
           <?php if(is_array($province) || $province instanceof \think\Collection || $province instanceof \think\Paginator): $i = 0; $__LIST__ = $province;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
               <option value="<?php echo $vo['id']; ?>" <?php if($store[province_id] == $vo[id]): ?>selected<?php endif; ?>><?php echo $vo['name']; ?></option>
           <?php endforeach; endif; else: echo "" ;endif; ?>
         </select>
         <select onchange="get_area(this)" id="city" name="city_id" class="form-control">
            <option value="0">选择城市</option>
            <?php if(is_array($city) || $city instanceof \think\Collection || $city instanceof \think\Paginator): $i = 0; $__LIST__ = $city;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <option value="<?php echo $vo['id']; ?>" <?php if($store[city_id] == $vo[id]): ?>selected<?php endif; ?>><?php echo $vo['name']; ?></option>
            <?php endforeach; endif; else: echo "" ;endif; ?>
         </select>
         <select id="district" name="district" class="form-control">
            <option value="0">选择区域</option>
            <?php if(is_array($area) || $area instanceof \think\Collection || $area instanceof \think\Paginator): $i = 0; $__LIST__ = $area;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <option value="<?php echo $vo['id']; ?>" <?php if($store[district] == $vo[id]): ?>selected<?php endif; ?>><?php echo $vo['name']; ?></option>
            <?php endforeach; endif; else: echo "" ;endif; ?>
         </select>
         <input type="text" placeholder="详细地址" class="text w300" style="margin-top:3px;" name="store_address" value="<?php echo $store['store_address2']; ?>">
      </dd>
    </dl>
    
    <dl>
      <dt>包邮额度：</dt>
      <dd>
        <input class="text w50" name="store_free_price" type="text" maxlength="8" value="<?php echo $store['store_free_price']; ?>" />
        <span></span>
        <p class="hint">满多少免运费</p>
      </dd>
    </dl>
    <dl>
      <dt>SEO关键字：</dt>
      <dd>
        <p>
          <input class="text w400" name="seo_keywords" type="text"  value="<?php echo $store['seo_keywords']; ?>" maxlength="254"/>
        </p>
        <p class="hint">用于店铺搜索引擎的优化，关键字之间请用英文逗号分隔</p>
      </dd>
    </dl>
    <dl>
      <dt>SEO店铺描述：</dt>
      <dd>
        <p>
          <textarea name="seo_description" rows="3" class="textarea w400" id="remark_input" maxlength="254"><?php echo $store['seo_description']; ?></textarea>
        </p>
        <p class="hint">用于店铺搜索引擎的优化，建议120字以内</p>
      </dd>
    </dl>
    <div class="bottom">
        <label class="submit-border"><input type="button" class="submit" value="提交" onclick="adsubmit()" /></label>
      </div>
  </form>
</div>
<script type="text/javascript">
function adsubmit(){
    var store_phone = $.trim($('#store_phone').val())
    var service_phone = $.trim($('#service_phone').val())
    if(!checkMobile(store_phone) && !checkTelphone(store_phone)){
        layer.alert('请输入正确的店铺电话！',{icon:2});
        return false;
    }
    if(!checkMobile(service_phone) && !checkTelphone(service_phone)){
        layer.alert('请输入正确的服务电话！',{icon:2});
        return false;
    }
	$('#handlepost').submit();
}

function goset(obj){
	window.location.href = $(obj).attr('data-url');
}

function callback1(img_str){
	$('input[name="store_logo"]').val(img_str);
	$('#store_logo').attr('src',img_str);
}

function callback2(img_str){
	$('input[name="store_banner"]').val(img_str);
	$('#store_banner').attr('src',img_str);
}

function callback3(img_str){
	$('input[name="store_avatar"]').val(img_str);
	$('#store_avatar').attr('src',img_str);
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
