<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:56:"./application/seller/new/decoration/decoration_edit.html";i:1587634376;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/head.html";i:1587634378;s:99:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/decoration/store_decoration_block.html";i:1587634376;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/foot.html";i:1587634378;}*/ ?>
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
<script type="text/javascript" src="/public/js/seller/common.js"></script>
<script type="text/javascript" src="/public/js/seller/member.js" charset="utf-8"></script>
<script type="text/javascript" src="/public/js/seller/ToolTip.js"></script>
<style>#dialog_module_goods{overflow:auto;}</style>
<div class="wrapper">
<link href="/public/css/shop_custom.css" rel="stylesheet" type="text/css">
<div class="ncsc-path"><i class="icon-desktop"></i>商家管理中心<i class="icon-angle-right"></i>店铺<i class="icon-angle-right"></i>店铺装修<i class="icon-angle-right"></i>页面设计</div>
<div class="ncsc-decoration-layout">
  <div class="ncsc-decoration-menu" id="waypoints">
    <div class="title"><i class="icon"></i>
      <h3>店铺装修选项</h3>
      <h5>店铺首页模板设计操作</h5>
    </div>
    <ul class="menu">
      <li><a id="btn_edit_background" href="javascript:void(0);"><i class="background"></i>编辑背景</a></li>
      <li><a id="btn_edit_head" href="javascript:void(0);"><i class="head"></i>编辑头部</a></li>
      <li><a id="btn_add_block" href="javascript:void(0);"><i class="block"></i>添加布局块</a></li>
      <li><a id="btn_preview" href="<?php echo U('Home/Store/decoration_preview',array('decoration_id'=>$decoration_id,'store_id'=>$store_id)); ?>" target="_blank"><i class="preview"></i>设计预览</a></li>
      <li><a id="btn_close" href="javascript:void(0);"><i class="close"></i>完成退出</a></li>
    </ul>
    <div class="faq">下方区域为1200像素宽度即时编辑区域；“添加布局块”后选择模块类型进行详细设置；“设计预览”可查看生成后效果；内容将实时保存，设置完成后直接选择“完成退出”。</div>
  </div>
  <div id="store_decoration_content" style="<?php echo $decoration_background_style; ?>">
    <div id="decoration_banner" class="tpsl-nav-banner"> </div>
    <div id="decoration_nav" class="tp-nav">
      <div class="ncs-nav">
        <ul>
          <li class="active"><a href="javascript:void(0);"><span>店铺首页<i></i></span></a></li>
          <li><a href="javascript:void(0);"><span>店铺动态<i></i></span></a></li>
        </ul>
      </div>
    </div>
    <div id="store_decoration_area" class="store-tp-decoration-page">
	    <?php if(is_array($block_list) || $block_list instanceof \think\Collection || $block_list instanceof \think\Paginator): if( count($block_list)==0 ) : echo "" ;else: foreach($block_list as $key=>$block): ?>
	      	<div id="block_<?php echo $block[block_id]; ?>" data-block-id="<?php echo $block[block_id]; ?>" nctype="store_decoration_block" 
class="ncsc-decration-block store-tp-decoration-block-1 <?php if($block[block_full_width] == 1): ?>store-tp-decoration-block-full-width<?php endif; ?> tip" title="<?php echo $block_title; ?>">
    <div nctype="store_decoration_block_content" class="ncsc-decration-block-content store-decoration-block-1-content">
        <div nctype="store_decoration_block_module" class="store-decoration-block-1-module">
            <?php if($block[block_module_type] == 'html'): 
					$block = empty($block) ? $output['block'] : $block;
					$block_content = $block['block_content'];
				?>
            	<?php echo html_entity_decode($block_content);elseif($block[block_module_type] == 'slide'): $block_content = unserialize($block['block_content']);?>
				<ul nctype="store_decoration_slide" style="height:<?php echo $block_content['height']; ?>px; overflow:hidden;">
				    <?php if(is_array($block_content['images']) || $block_content['images'] instanceof \think\Collection || $block_content['images'] instanceof \think\Paginator): if( count($block_content['images'])==0 ) : echo "" ;else: foreach($block_content['images'] as $key=>$value): ?>
					    <li data-image-name="<?php echo $value['image_name']; ?>" data-image-url="<?php echo $value[image_url]; ?>" data-image-link="<?php echo $value['image_link']; ?>" style="height:<?php echo $block_content['height']; ?>px; background: url(<?php echo $value[image_url]; ?>) no-repeat scroll center top transparent;">
					    	<a href="<?php echo $value['image_link']; ?>" target="_blank" style="display:block;width:100%;height:100%;"></a>
					    </li>
				    <?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			<?php elseif($block[block_module_type] == 'goods'): 
   				$block_content = empty($block_content) ? $output['block_content'] : $block_content; 
    			$goods_list = unserialize($block['block_content']);
			if(!(empty($goods_list) || (($goods_list instanceof \think\Collection || $goods_list instanceof \think\Paginator ) && $goods_list->isEmpty()))): ?>
			<ul class="goods-list">
			  <?php if(is_array($goods_list) || $goods_list instanceof \think\Collection || $goods_list instanceof \think\Paginator): if( count($goods_list)==0 ) : echo "" ;else: foreach($goods_list as $key=>$val): ?>
			  <li nctype="goods_item" data-goods-id="<?php echo $val['goods_id']; ?>" data-goods-name="<?php echo $val['goods_name']; ?>" data-goods-price="<?php echo $val['shop_price']; ?>"  data-goods-image="<?php echo $val['goods_image']; ?>">
			    <div class="goods-thumb"> 
			    	<a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$val[goods_id])); ?>" target="_blank" title="<?php echo $val['goods_name']; ?>"> 
			    	<img src="<?php echo goods_thum_images($val['goods_id'],240,240); ?>" alt="<?php echo $val['goods_name']; ?>"> </a> 
			    </div>
			    <dl class="goods-info">
			      <dt><a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$val[goods_id])); ?>" target="_blank" title="<?php echo $val['goods_name']; ?>"><?php echo $val['goods_name']; ?></a></dt>
			      <dd>¥<?php echo $val['shop_price']; ?></dd>
			    </dl>
			  </li>
			  <?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
			<div style="text-align: center; display: block; padding: 15px 0; margin: 0!important;" id="page_list"><?php echo $show_page; ?></div>
			<?php endif; elseif($block[block_module_type] == 'hot_area'): $block_content = unserialize($block['block_content']);?>
				<div>
				    <?php $hot_area_flag = str_replace('.', '',$block_content['image']);?>
				    <img data-image-name="<?php echo $block_content['image'];?>" usemap="#<?php echo $hot_area_flag;?>" src="<?php echo $block_content['image_url'];?>" alt="<?php echo $block_content['image'];?>">
				    <map name="<?php echo $hot_area_flag;?>" id="<?php echo $hot_area_flag;?>">
				        <?php if(!empty($block_content['areas']) && is_array($block_content['areas'])) {foreach($block_content['areas'] as $value) {?>
				        <area target="_blank" shape="rect" coords="<?php echo $value['x1'];?>,<?php echo $value['y1'];?>,<?php echo $value['x2'];?>,<?php echo $value['y2'];?>" href ="<?php echo $value['link'];?>" alt="<?php echo $value['link'];?>" />
				        <?php } } ?>
				    </map>
				</div>
			<?php else: endif; ?>
        </div>
        <?php if($control_flag == 1): ?>
        	<a class="edit" nctype="btn_edit_module" data-module-type="<?php echo $block['block_module_type']; ?>" href="javascript:;" data-block-id="<?php echo $block[block_id]; ?>"><i class="icon-edit"></i>编辑模块</a>
        <?php endif; ?>
    </div>
    <?php if($control_flag == 1): ?>
    	<a class="delete" nctype="btn_del_block" href="javascript:;" data-block-id="<?php echo $block[block_id]; ?>" title="删除该布局块"><i class="icon-trash"></i>删除布局块</a>    
    <?php endif; ?>
</div>
	    <?php endforeach; endif; else: echo "" ;endif; ?>
     </div>
  </div>
</div>

<!-- 背景编辑对话框 -->
<div id="dialog_edit_background" class="eject_con dialog-decoration-edit" style="display:none;">
  <dl>
    <dt>背景颜色：</dt>
    <dd>
      <input id="txt_background_color" class="text w80" type="text" name="" value="<?php echo $decoration_setting['background_color']; ?>" maxlength="7">
      <p class="hint">设置背景颜色请使用十六进制形式(#XXXXXX)，默认留空为白色背景。</p>
    </dd>
  </dl>
  <dl>
    <dt>背景图：</dt>
    <dd>
      <div class="ncsc-upload-btn"> <a href="javascript:void(0);"><span>
        <input type="file" hidefocus="true" size="1" class="input-file" id="file_background_image" name="file"/>
        </span>
        <p><i class="icon-upload-alt"></i>图片上传</p>
        </a> </div>
      <div id="div_background_image" <?php if(empty($decoration_setting['background_image']) || (($decoration_setting['background_image'] instanceof \think\Collection || $decoration_setting['background_image'] instanceof \think\Paginator ) && $decoration_setting['background_image']->isEmpty())): ?> style="display:none;"<?php endif; ?> class="background-image-thumb">
        <img id="img_background_image" src="<?php echo $decoration_setting['background_image_url']; ?>" alt="">
        <input id="txt_background_image" type="hidden" name="" value="<?php echo $decoration_setting['background_image']; ?>">
        <a id="btn_del_background_image" class="del" href="javascript:void(0);" title="移除背景图">X</a>
       </div>
    </dd>
  </dl>
  <dl>
    <dt>背景图定位：</dt>
    <dd>
      <input id="txt_background_position_x" class="text w40" type="text" value="<?php echo $decoration_setting['background_position_x']; ?>"><label class="add-on">X</label>
      &#12288;&#12288;
      <input id="txt_background_position_y" class="text w40" type="text" value="<?php echo $decoration_setting['background_position_y']; ?>"><label class="add-on">Y</label>
      <p class="hint">设置背景图像的起始位置。</p>
    </dd>
  </dl>
  <dl>
    <dt>背景图填充方式：</dt>
    <dd>
      <?php $repeat = $decoration_setting['background_image_repeat'];?>
      <input id="input_no_repeat" type="radio" value="no-repeat" name="background_repeat" <?php if(empty($repeat) || $repeat == 'no-repeat') {echo 'checked';}?>>
      <label for="input_no_repeat">不重复</label>
      <input id="input_repeat" type="radio" value="repeat" name="background_repeat" <?php if($repeat == 'repeat') {echo 'checked';}?>>
      <label for="input_repeat">平铺</label>
      <input id="input_repeat_x" type="radio" value="repeat-x" name="background_repeat" <?php if($repeat == 'repeat-x') {echo 'checked';}?>>
      <label for="input_repeat_x">x轴平铺</label>
      <input id="input_repeat_y" type="radio" value="repeat-y" name="background_repeat" <?php if($repeat == 'repeat-y') {echo 'checked';}?>>
      <label for="input_repeat_y">y轴平铺</label>
    </dd>
  </dl>
  <dl>
    <dt>背景滚动模式：</dt>
    <dd>
      <input id="txt_background_attachment" class="text w80" type="text" value="<?php echo $decoration_setting['background_attachment']; ?>">
      <p class="hint">设置背景随屏幕滚动或固定，例如："scroll"或"fixed"。 </p>
    </dd>
  </dl>
  <div class="bottom">
    <label class="submit-border"><a id="btn_save_background" class="submit" href="javascript:void(0);">保存</a></label>
  </div>
</div>
<!-- 头部编辑对话框 -->
<div id="dialog_edit_head" class="eject_con dialog-decoration-edit" style="display:none;">
  <div id="dialog_edit_head_tabs">
    <ul>
      <li><a href="#dialog_edit_head_tabs_1">头部导航</a></li>
      <li><a href="#dialog_edit_head_tabs_2">头部图片</a></li>
    </ul>
    <div id="dialog_edit_head_tabs_1">
      <dl>
        <dt>是否显示：</dt>
        <dd>
          <label for="decoration_nav_display_true">
            <input id="decoration_nav_display_true" type="radio" class="radio" value="true" name="decoration_nav_display" <?php if($decoration_nav[display] == 'true'): ?>checked<?php endif; ?>>
            显示</label>
          <label for="decoration_nav_display_false">
            <input id="decoration_nav_display_false" type="radio" class="radio" value="false" name="decoration_nav_display" <?php if($decoration_nav[display] == 'false'): ?>checked<?php endif; ?>>
            不显示</label>
          <p class="hint">“头部导航”为店铺首页店铺导航条，可设置导航条的样式是否显示， 默认为显示。</p>
        </dd>
      </dl>
      <dl>
        <dt>导航样式：</dt>
        <dd>
          <textarea id="decoration_nav_style" class="w400 h100"><?php echo $decoration_nav[style]; ?></textarea>
          <p> <a id="btn_default_nav_style" class="ncsc-btn-mini" href="javascript:void(0);"><i class="icon-refresh"></i>恢复默认</a> </p>
          <p class="hint">导航条对应CSS文件，如修改后显示效果不符可恢复默认值。</p>
        </dd>
      </dl>
      <div class="bottom">
        <label class="submit-border"><a id="btn_save_decoration_nav" class="submit" href="javascript:void(0);">保存</a></label>
      </div>
    </div>
    <div id="dialog_edit_head_tabs_2">
      <dl>
        <dt>是否显示：</dt>
        <dd>
          <label for="decoration_banner_display_true">
            <input id="decoration_banner_display_true" type="radio" class="radio" value="true" name="decoration_banner_display" <?php if($decoration_banner[display] == 'true'): ?>checked<?php endif; ?>>
            显示</label>
          <label for="decoration_banner_display_false">
            <input id="decoration_banner_display_false" type="radio" class="radio" value="false" name="decoration_banner_display" <?php if($decoration_banner[display] == 'false'): ?>checked<?php endif; ?>>
            不显示</label>
          <p class="hint">“头部图片”为店铺首页最上方图片，可设置是否显示。</p>
        </dd>
      </dl>
      <dl>
        <dt>图片：</dt>
        <dd>
          <div id="div_banner_image"  class="background-image-thumb">	<img id="img_banner_image" src="<?php echo $decoration_banner['image']; ?>" alt="">
            <input id="txt_banner_image" type="hidden" name="" value="<?php echo $decoration_banner['image']; ?>">
            <a id="btn_del_banner_image" class="del" href="javascript:void(0);" title="移除">X</a> </div>
          <div class="ncsc-upload-btn"> <a href="javascript:void(0);"> <span>
            <input type="file" hidefocus="true" size="1" class="input-file" id="file_decoration_banner" name="file"/>
            </span>
            <p><i class="icon-upload-alt"></i>图片上传</p>
            </a> </div>
          <p class="hint">选择上传头部图片，建议使用宽度为1000像素，大小不超过1M的gif\jpg\png格式图片。</p>
        </dd>
      </dl>
      <div class="bottom">
        <label class="submit-border"><a id="btn_save_decoration_banner" class="submit" href="javascript:void(0);">保存设置</a></label>
      </div>
    </div>
  </div>
</div>
<!-- 选择模块对话框 -->
<div id="dialog_select_module" class="dialog-decoration-module" style="display:none;">
  <ul>
    <li><a nctype="btn_show_module_dialog" data-module-type="slide" href="javascript:void(0);"><i class="slide"></i>
      <dl>
        <dt>图片和幻灯</dt>
        <dd>添加图片和可切换幻灯</dd>
      </dl>
      </a></li>
    <li><a nctype="btn_show_module_dialog" data-module-type="hot_area" href="javascript:void(0);"><i class="hotarea"></i>
      <dl>
        <dt>图片热点</dt>
        <dd>添加图片并设置热点区域链接</dd>
      </dl>
      </a></li>
    <li> <a nctype="btn_show_module_dialog" data-module-type="goods" href="javascript:void(0);"><i class="goods"></i>
      <dl>
        <dt>店铺商品</dt>
        <dd>选择添加店铺内的在售商品</dd>
      </dl>
      </a> </li>
    <li> <a nctype="btn_show_module_dialog" data-module-type="html" href="javascript:void(0);"><i class="html"></i>
      <dl>
        <dt>自定义</dt>
        <dd>编辑器自定义编辑html</dd>
      </dl>
      </a> </li>
  </ul>
</div>
<!-- 自定义模块编辑对话框 -->
<div id="dialog_module_html" class="eject_con dialog-decoration-edit" style="display:none;">
  <div class="alert">
    <ul>
      <li>1. 可将预先设置好的网页文件内容复制粘贴到文本编辑器内，或直接在工作窗口内进行编辑操作。</li>
      <li>2. 默认为可视化编辑，选择第一个按钮切换到html代码编辑。css文件可以Style=“...”形式直接写在对应的html标签内。</li>
    </ul>
  </div>
  <textarea id="module_html_editor" name="module_html_editor" class="render" style=" width:1016px; height:400px; visibility:hidden;"></textarea>
  <div class="bottom">
    <label class="submit-border"><a id="btn_save_module_html" class="submit" href="javascript:void(0);">保存设置</a></label>
  </div>
</div>
<!-- 幻灯模块编辑对话框 -->
<div id="dialog_module_slide" class="eject_con dialog-decoration-edit" style="display:none;">
  <div class="alert">
    <ul>
      <li>1. 可选择图片以全屏或非全屏形式显示，<strong class="orange">且必须设定图片的高度</strong>，否则将无法正常浏览。</li>
      <li>2. 上传单张图片时系统默认显示为<strong>“图片链接”</strong>形式显示，如一次上传多图将以<strong>“幻灯片”</strong>形式显示。</li>
      <li>3. 增加幻灯片请点击添加图片，在幻灯片保存之前，<strong class="orange">请先点击添加按钮。</strong></li>
    </ul>
  </div>
  <div id="module_slide_html" class="slide-upload-thumb">
    <ul class="module-slide-content">
    </ul>
  </div>
  <h4>相关设置：</h4>
  <dl class="display-set">
    <dt>显示设置：</dt>
    <dd><span>全屏显示
      <input id="txt_slide_full_width" type="checkbox" class="checkobx" name="">
      </span><span><strong class="orange">*</strong> 显示高度
      <input id="txt_slide_height" type="text" class="text w40" value=""><em class="add-on">像素</em></span>
      <p><a id="btn_add_slide_image" class="ncsc-btn mt5" href="javascript:void(0);"><i class="icon-plus"></i>添加图片</a></p>
    </dd>
  </dl>
  <div id="div_module_slide_upload" style="display:none;">
    <form enctype="multipart/form-data" action="">
      <dl>
        <dt>图片上传：</dt>
        <dd>
          <div id="div_module_slide_image" class="module-upload-image-preview"></div>
          <div class="ncsc-upload-btn"> <a href="javascript:void(0);"> <span>
            <input type="file" hidefocus="true" size="1" class="input-file" name="file" id="file"  nctype="btn_module_slide_upload"/>
            </span>
            <p><i class="icon-upload-alt"></i>图片上传</p>
            </a> </div>
          <p class="hint">请上传宽度为1000像素的jpg/gif/png格式图片。</p>
        </dd>
      </dl>
      <dl>
        <dt>图片链接：</dt>
        <dd>
          <input id="module_slide_url" class="text w400" type="text">
          <p class="hint">请输入以http://为开头的图片链接地址，仅作为图片使用时请留空此选项</p>
          <p class="mt5"><a id="btn_save_add_slide_image" class="ncsc-btn ncsc-btn-acidblue" href="javascript:void(0);">添加</a> <a id="btn_cancel_add_slide_image" class="ncsc-btn ncsc-btn-orange" href="javascript:void(0);">取消</a></p>
        </dd>
      </dl>
    </form>
  </div>
  <div class="bottom">
    <label class="submit-border"><a id="btn_save_module_slide" class="submit" href="javascript:void(0);">保存设置</a></label>
  </div>
</div>
<!-- 图片热点模块编辑对话框 -->
<div id="dialog_module_hot_area" class="eject_con dialog-decoration-edit" style="display:none;">
  <div class="alert">
    <ul>
      <li>1. 在已上传的图片范围拖动鼠标形成选择区域，对该区域添加以http://格式开头的链接地址并点击“添加网址”按钮生效。</li>
      <li>2. 对已添加的热点可做编辑链接地址修改，如需调整位置，请删除该热点区域并保存，之后重新选择添加。</li>
    </ul>
  </div>
  <div id="div_module_hot_area_image" class="hot-area-image" style="position: relative;"></div>
  <ul id="module_hot_area_select_list" class="hot-area-select-list" style="min-height: 50px;">
  </ul>
  <h4>相关设置：</h4>
  <form action="">
    <dl>
      <dt>图片上传：</dt>
      <dd>
        <div class="ncsc-upload-btn"> <a href="javascript:void(0);"> <span>
          <input type="file" hidefocus="true" size="1" class="input-file" name="file" id="file"  nctype="btn_module_hot_area_upload"/>
          </span>
          <p><i class="icon-upload-alt"></i>图片上传</p>
          </a> </div>
        <p class="hint">选择上传jpg/gif/png格式图片，建议宽度不超过1000像素，高度不超过400像素，如超出此范围，请先自行对图片进行裁切调整。</p>
      </dd>
    </dl>
  </form>
  <dl>
    <dt>热点链接设置：</dt>
    <dd>
      <input id="module_hot_area_url" class="text w400" type="text" />
      <a id="btn_module_hot_area_add" class="ncsc-btn ml5" href="javascript:void(0);"><i class="icon-anchor"></i>添加网址</a>
      <p class="hint">在输入框中添加以“http://”格式开头的热点区域跳转网址。</p>
    </dd>
  </dl>
  <div class="bottom">
    <label class="submit-border"><a id="btn_save_module_hot_area" class="submit" href="javascript:void(0);">保存设置</a></label>
  </div>
</div>
<!-- 商品模块编辑对话框 -->
<div id="dialog_module_goods" class="eject_con dialog-decoration-edit" style="display:none;">
  <div class="alert">
    <ul>
      <li>1. 搜索店铺内在售商品并“选择添加”，设置窗口上部将出现已选择的商品列表，也可对其进行“取消选择”操作，点击保存设置后生效。</li>
      <li>2. 当已选择的商品超过10个时，系统默认未全部显示，可通过在已选区域滚动鼠标或拉动侧边条进行查看及操作。</li>
    </ul>
  </div>
  <div id="decorationGoods">
    <ul id="div_module_goods_list" class="goods-list">
    </ul>
  </div>
  <h4 class="mt10">店铺在售商品选择</h4>
  <div class="decoration-search-goods">
    <div class="search-bar">输入商品关键字：
      <input id="txt_goods_search_keyword" type="text" class="text w200 vm" name="">
      <a id="btn_module_goods_search" class="ncsc-btn" href="javascript:void(0);">搜索</a><span class="ml10 orange">小提示： 留空搜索显示店铺全部在售商品，每页显示10个。</span></div>
    <div id="div_module_goods_search_list"></div>
  </div>
  <div class="bottom"><label class="submit-border"><a id="btn_save_module_goods" class="submit" href="javascript:void(0);">保存设置</a></label></div>
</div>
<!-- 幻灯模板 --> 
<script id="template_module_slide_image_list" type="text/html">
<li data-image-name="<%=image_name%>" data-image-url="<%=image_url%>" data-image-link="<%=image_link%>">
<span><img src="<%=image_url%>"></span>
<a nctype="btn_del_slide_image" href="javascript:void(0);" title="删除">X</a>
</li>
</script> 
<!-- 热点块控制模板 --> 
<script id="template_module_hot_area_list" type="text/html">
<li data-hot-area-link="<%=link%>" data-hot-area-position="<%=position%>">
<i></i>
<p>热点区域<%=index%></p>
<p><a nctype="btn_module_hot_area_select" data-hot-area-position="<%=position%>" class="ncsc-btn-mini ncsc-btn-acidblue" href="javascript:void(0);">选择</a>
<a data-index="<%=index%>" nctype="btn_module_hot_area_del" class="ncsc-btn-mini ncsc-btn-red" href="javascript:void(0);">删除</a></p>
</li>
</script> 
<!-- 热点块标识模板 --> 
<script id="template_module_hot_area_display" type="text/html">
<div class="store-decoration-hot-area-display" style="width:<%=width%>px;height:<%=height%>px;position:absolute;left:<%=left%>px;top:<%=top%>px;border:1px solid #cccccc;" id="hot_area_display_<%=index%>">热点区域<%=index%></div>
</li>
</script>
<script type="text/javascript" src="/public/js/seller/template.min.js" charset="utf-8"></script> 
<script type="text/javascript" src="/public/js/fileupload/jquery.iframe-transport.js" charset="utf-8"></script> 
<script type="text/javascript" src="/public/js/fileupload/jquery.ui.widget.js" charset="utf-8"></script> 
<script type="text/javascript" src="/public/js/fileupload/jquery.fileupload.js" charset="utf-8"></script> 
<script type="text/javascript" src="/public/plugins/Ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/public/plugins/Ueditor/ueditor.all.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/public/plugins/Ueditor/lang/zh-cn/zh-cn.js"></script>
<link media="all" rel="stylesheet" href="/public/js/jquery.imgareaselect/imgareaselect-animated.css" type="text/css" />
<script type="text/javascript" src="/public/js/jquery.imgareaselect/jquery.imgareaselect.min.js"></script> 
<script src="/public/js/jquery.poshytip.min.js"></script> 
<script type="text/javascript"> 
    //定义api常量
    var uploadurl="<?php echo url('Uploadify/index',array('savepath'=>'decoration')); ?>";
    var DECORATION_ID = "<?php echo $decoration_id; ?>";
    var URL_DECORATION_ALBUM_UPLOAD = "<?php echo U('Decoration/album_upload'); ?>";
    var URL_DECORATION_BACKGROUND_SETTING_SAVE = "<?php echo U('Decoration/background_setting_save'); ?>";
    var URL_DECORATION_NAV_SAVE = "<?php echo U('Decoration/nav_save'); ?>";
    var URL_DECORATION_BANNER_SAVE = "<?php echo U('Decoration/banner_save'); ?>";
    var URL_DECORATION_BLOCK_ADD = "<?php echo U('Decoration/block_add'); ?>";
    var URL_DECORATION_BLOCK_DEL = "<?php echo U('Decoration/block_del'); ?>";
    var URL_DECORATION_BLOCK_SAVE = "<?php echo U('Decoration/block_save'); ?>";
    var URL_DECORATION_BLOCK_SORT = "<?php echo U('Decoration/block_sort'); ?>";
    var URL_DECORATION_GOODS_SEARCH = "<?php echo U('Decoration/goods_search'); ?>";
    var LOADING_IMAGE = '/public/static/images/loading.gif';
    var POSHYTIP = {
        className: 'tip-yellowsimple',
        showTimeout: 1,
        alignTo: 'target',
        alignX: 'top',
        alignY: 'left',
        offsetX: -300,
        offsetY: -5,
        allowTipHover: false
    };

    $(document).ready(function(){
        //浮动导航  waypoints.js
        $("#waypoints").waypoint(function(event, direction) {
            $(this).parent().toggleClass('sticky', direction === "down");
            event.stopPropagation();
        });
        //商品模块已选商品滚动条
        $('#decorationGoods').perfectScrollbar();
//        $('#dialog_module_goods').perfectScrollbar();
		//title提示
    	$('.tip').poshytip(POSHYTIP);

    });		
</script> 
<script type="text/javascript" src="/public/js/seller/store_decoration.js" charset="utf-8"></script> 
</div>
<script type="text/javascript">
$(document).ready(function(){
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
</script>
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