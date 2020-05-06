<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:42:"./application/seller/new/goods/_goods.html";i:1587634376;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/head.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/left.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/foot.html";i:1587634378;}*/ ?>
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
    .ncsc-goods-default-pic .goodspic-uplaod .upload-thumb{width: 180px;height:180px; display: inline-block;}
    .ncsc-goods-default-pic .goodspic-uplaod .upload-thumb img{ height: 160px; width: 160px;}
    .ncsc-goods-default-pic .goodspic-uplaod .upload-thumb{line-height: 20px;margin-right: 6px;}
    .ncsc-goods-default-pic .goodspic-uplaod .upload-thumb:nth-child(5n){margin-right: 0;}
    .ncsc-goods-default-pic{display: inherit;}
    /*.ncsc-form-goods dl dd{width: 98%;}*/
    .text-warning {color: #8a6d3b;}a{ color:#3BAEDA}
    /*.ncsc-form-goods{padding: 10px;}*/
    .table-bordered {border: 1px solid #f4f4f4;}
    .table { width: 100%;max-width: 100%;margin-bottom: 20px;}
    ul.group-list {width: 96%;min-width: 1000px; margin: auto 5px;list-style: disc outside none;}
    ul.group-list li { white-space: nowrap;float: left; width: 150px; height: 25px;padding: 3px 5px;list-style-type: none;list-style-position: outside;border: 0px;margin: 0px;}
    .row .table-bordered td .btn,.row .table-bordered td img{vertical-align: middle;}
    .row .table-bordered td{padding: 8px;line-height: 1.42857143;}
    .table-bordered{width: 100%}
    .table-bordered tr td{border: 1px solid #f4f4f4;}
    .btn-success {color: #fff;background-color: #48CFAE;border-color: #398439 solid 1px;}
    .btn {display: inline-block;padding: 6px 12px;margin-bottom: 0;font-size: 14px;
        font-weight: 400;line-height: 1.42857143;text-align: center;white-space: nowrap; vertical-align: middle;
        -ms-touch-action: manipulation;touch-action: manipulation;cursor: pointer;-webkit-user-select: none;-moz-user-select: none;
        -ms-user-select: none;user-select: none;background-image: none;border: 1px solid transparent; border-radius: 4px;margin-bottom: 10px;
    }
    .col-xs-8 {width: 66.66666667%;}
    .col-xs-4 {width: 33.33333333%;}
    .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {float: left;}
    .row .tab-pane h4{padding: 10px 0;}
    .row .tab-pane h4 input{vertical-align: middle;}
    .table-striped>tbody>tr:nth-of-type(odd) {background-color: #f9f9f9;}
    .ncap-form-default .title{border-bottom: 0}
    .ncap-form-default dl.row, .ncap-form-all dd.opt/*border-color: #F0F0F0;*/border: none;
    .ncap-form-default dl.row:hover, .ncap-form-all dd.opt:hover{border: none;box-shadow: inherit;}
    a:hover {color: #3BAEDA;text-decoration: none;}
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd;}
    ul.group-list{min-width: 100%;}
    input{vertical-align:middle;}
    .ncsc-form-goods h4{margin: 10px 0 10px 10px;}
    .clabackkj{background-color: #F5F5F5;border-bottom: solid 1px #E7E7E7;overflow: hidden;}
    .clabackkj h3{border-bottom: 0;display: inline-block;}
    .clabackkj .ncbtn{float: right;margin-right: 15px;height: 12px;line-height: 12px;}
    #tab_goods_images dl dd{width: 99%;}
    .alert-block{margin-top: 0;}
    select{min-width:120px;}
    .ui-tabs-nav{height:30px;padding-top:5px;background:#f5f5f5;}
    .ui-tabs-nav>li{float:left;height:30px;padding:0 10px;}
    .ui-tabs-nav>.ui-tabs-selected{background:#ddd;}
    .btn{margin-bottom: 10px!important;}
</style>
<!--以下是在线编辑器 代码 -->
<script type="text/javascript" charset="utf-8" src="/public/plugins/Ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="/public/plugins/Ueditor/ueditor.all.min.js"> </script>
<script type="text/javascript" charset="utf-8" src="/public/plugins/Ueditor/lang/zh-cn/zh-cn.js"></script>
<script type="text/javascript">
var url="<?php echo url('Uploadify/index',array('savepath'=>'goods')); ?>";
var ue = UE.getEditor('goods_content',{
    toolbars: [[
        'fullscreen', 'source', '|', 'undo', 'redo', '|',
        'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
        'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
        'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
        'directionalityltr', 'directionalityrtl', 'indent', '|',
        'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
         'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
        'simpleupload', 'insertimage', 'emotion', 'scrawl', 'music', 'attachment', 'map', 'gmap', 'insertframe', 'insertcode', 'webapp', 'pagebreak', 'template', 'background', '|',
        'horizontal', 'date', 'time', 'spechars', 'snapscreen', 'wordimage', '|',
        'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
        'print', 'preview', 'searchreplace', 'drafts', 'help'
    ]],
    serverUrl :url,
    zIndex: 999,
    initialFrameWidth: "100%", //初化宽度
    initialFrameHeight: 300, //初化高度            
    focus: false, //初始化时，是否让编辑器获得焦点true或false
    maximumWords: 99999, removeFormatAttributes: 'class,style,lang,width,height,align,hspace,valign',//允许的最大字符数 'fullscreen',
    pasteplain:false, //是否默认为纯文本粘贴。false为不使用纯文本粘贴，true为使用纯文本粘贴
    autoHeightEnabled: true
});
</script>
<!--以上是在线编辑器 代码  end-->
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
        <div class="ncsc-path"><i class="icon-desktop"></i>商家管理中心<i class="icon-angle-right"></i>商品<i class="icon-angle-right"></i>商品发布</div>
        <div class="main-content" id="mainContent">
            <div class="tabmenu-fixed-wrap">
                <div class="tabmenu">
                    <ul class="tab pngFix">
                        <li class="active"><a onclick="select_nav(this);" data-id="tab_tongyong">通用信息</a></li>
                        <li class="normal"><a onclick="select_nav(this);" data-id="tab_goods_images">商品相册</a></li>
                        <li class="normal"><a onclick="select_nav(this);" data-id="tab_goods_spec">商品规格</a></li>
                        <li class="normal"><a onclick="select_nav(this);" data-id="tab_goods_attr">商品属性</a></li>
                    </ul>
                </div>
            </div>
            <div class="item-publish">
                <form method="post" id="addEditGoodsForm">
                    <input type="hidden" name="goods_id" value="<?php echo $goodsInfo['goods_id']; ?>">
                    <input type="hidden" name="cat_id1" value="<?php echo $goods_cat[2][id]; ?>">
                    <input type="hidden" name="cat_id2" value="<?php echo $goods_cat[1][id]; ?>">
                    <input type="hidden" name="cat_id3" value="<?php echo $goods_cat[0][id]; ?>">
                    <input type="hidden" name="purpose" value="<?php echo (\think\Request::instance()->param('purpose') ?: 1); ?>">
                    <?php if($goodsInfo['is_virtual'] == 1): ?>
                        <!--虚拟商品默认包邮的-->
                        <input type="hidden" name="is_free_shipping" value="1">
                    <?php endif; ?>
                    <div class="ncsc-form-goods active" id="tab_tongyong">
                        <?php if(!(empty($goodsInfo['goods_id']) || (($goodsInfo['goods_id'] instanceof \think\Collection || $goodsInfo['goods_id'] instanceof \think\Paginator ) && $goodsInfo['goods_id']->isEmpty()))): if($goodsInfo['supplier_goods_status'] == 1): ?>
                        <table class="table table-bordered" id="goods_spec_table">
                            <tr>
                                <td colspan="2">
                                    <div class="alert mt15 mb5"><strong>操作提示：</strong>
                                      <ul>
                                        <li style="color:red">1、此商品的源供应商品的供货数据有修改，需要同意后才能再次上架</li>
                                      </ul>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <?php endif; endif; ?>
                        <h3 id="demo1">商品基本信息</h3>
                        <dl>
                            <dt>商品分类：</dt>
                            <dd id="gcategory"> <?php echo $goods_cat[2][name]; ?> &gt;<?php echo $goods_cat[1][name]; ?> &gt;<?php echo $goods_cat[0][name]; if(empty($goodsInfo['goods_id']) || (($goodsInfo['goods_id'] instanceof \think\Collection || $goodsInfo['goods_id'] instanceof \think\Paginator ) && $goodsInfo['goods_id']->isEmpty())): ?>
                                    <a class="ncbtn" href="<?php echo U('Seller/Goods/addStepOne'); ?>">编辑</a>
                                <?php else: if($store['bind_all_gc'] == 1): elseif($goodsInfo['bind_class_state'] == -1): ?>
                                        <input class="submit" onclick="applyClass()" value="一键申请" type="submit">
                                    <?php elseif($goodsInfo['bind_class_state'] == 0): ?>
                                        （类目申请中）
                                    <?php endif; endif; ?>
                                <input id="cate_id" name="cate_id" value="156" class="text" type="hidden">
                                <input name="cate_name" value="<?php echo $goods_cat[2][name]; ?> ><?php echo $goods_cat[1][name]; ?> ><?php echo $goods_cat[0][name]; ?>" class="text" type="hidden">
                            </dd>
                        </dl>
                        <dl>
                            <dt><i class="required">*</i>商品名称：</dt>
                            <dd>
                                <input type="text" value="<?php echo $goodsInfo['goods_name']; ?>" name="goods_name"  class="text w400">
                                <span id="err_goods_name"></span>
                                <p class="hint">商品标题名称长度至少3个字符，最长50个汉字</p>
                            </dd>
                        </dl>
                        <dl>
                            <dt>商品简介：</dt>
                            <dd>
                                <textarea name="goods_remark" class="textarea h60 w400"><?php echo $goodsInfo['goods_remark']; ?></textarea>
                                <span id="err_goods_remark"></span>
                                <p class="hint">商品简介最长不能超过140个汉字</p>
                            </dd>
                        </dl>
                        <dl>
                            <dt>商品货号：</dt>
                            <dd>
                                <p>
                                    <input type="text" value="<?php echo $goodsInfo['goods_sn']; ?>" name="goods_sn" class="text" maxlength="20">
                                </p>
                                <span id="err_goods_sn"></span>
                                <p class="hint">商家货号是指商家管理商品的编号<br>最多可输入20个字符，支持输入中文、字母、数字、_、/、-和小数点</p>
                            </dd>
                        </dl>
                        <dl>
                            <dt>SPU：</dt>
                            <dd>
                                <p>
                                    <input type="text" value="<?php echo $goodsInfo['spu']; ?>" name="spu" class="text">
                                </p>
                                <p class="hint">可不填</p>
                            </dd>
                        </dl>
                        <dl>
                            <dt>SKU：</dt>
                            <dd>
                                <p>
                                    <input type="text" value="<?php echo $goodsInfo['sku']; ?>" name="sku" class="text">
                                </p>
                                <p class="hint">可不填</p>
                            </dd>
                        </dl>
                        <dl>
                            <dt>本店分类：</dt>
                            <dd>
                                <select name="store_cat_id1" id="store_cat_id1" onchange="get_store_category(this.value,'store_cat_id2','0');">
                                    <option value="0">请选择分类</option>
                                    <?php if(is_array($store_goods_class_list) || $store_goods_class_list instanceof \think\Collection || $store_goods_class_list instanceof \think\Paginator): if( count($store_goods_class_list)==0 ) : echo "" ;else: foreach($store_goods_class_list as $k=>$v): ?>
                                        <option value="<?php echo $v['cat_id']; ?>" <?php if($v['cat_id'] == $goodsInfo['store_cat_id1']): ?>selected="selected"<?php endif; ?> >
                                        <?php echo $v['cat_name']; ?>
                                        </option>
                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                                <select name="store_cat_id2" id="store_cat_id2">
                                    <option value="0">请选择分类</option>
                                </select>
                                <span id="err_cat_id" style="color:#F00; display:none;"></span>
                                <p class="hint">可不选,为了用户更好检索到该商品，最好选择</p>
                            </dd>
                        </dl>
                        <dl>
                            <dt>商品品牌：</dt>
                            <dd>
                                <select name="brand_id" id="brand_id">
                                    <option value="0">选择品牌</option>
                                    <?php if(is_array($brandList) || $brandList instanceof \think\Collection || $brandList instanceof \think\Paginator): if( count($brandList)==0 ) : echo "" ;else: foreach($brandList as $k=>$v): if($v['status'] == 0): ?>
                                            <option value="<?php echo $v['id']; ?>"  data-cat_id1="<?php echo $v['cat_id1']; ?>" <?php if($v['id'] == $goodsInfo['brand_id']): ?>selected="selected"<?php endif; ?>>
                                            <?php echo $v['name']; ?>
                                            </option>
                                        <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                                <p class="hint">可不选,为了用户更好检索到该商品，最好选择</p>
                            </dd>
                        </dl>
                        <dl>
                            <dt><i class="required">*</i>本店售价：</dt>
                            <dd>
                                <input name="shop_price" id="shop_price" value="<?php echo $goodsInfo['shop_price']; ?>" class="text w60" type="text" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')"><em class="add-on"><i class="icon-renminbi"></i></em> <span></span>
                                <p class="hint">价格必须是0.01~9999999之间的数字。<br>
                                    此价格为商品实际销售价格。该价格影响到积分赠送</p>
                            </dd>
                        </dl>
                        <dl>
                            <dt><i class="required">*</i>市场价：</dt>
                            <dd>
                                <input name="market_price" value="<?php echo $goodsInfo['market_price']; ?>" class="text w60" type="text" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')"><em class="add-on"><i class="icon-renminbi"></i></em> <span></span>

                                <p class="hint">价格必须是0.01~9999999之间的数字，此价格仅为市场参考售价，请根据该实际情况认真填写。</p>
                            </dd>
                        </dl>
                        <dl>
                            <dt>成本价（供货价）：</dt>
                            <dd>
                                <input value="<?php echo $goodsInfo['cost_price']; ?>" name="cost_price" class="text w60" type="text" 
									<?php if($goodsInfo['root_goods_id'] != 0): ?>readonly="readonly"<?php endif; ?> 
									onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')"><em class="add-on"><i class="icon-renminbi"></i></em> <span></span>

                                <p class="hint">价格必须是0.00~9999999之间的数字，此价格为商户对所销售的商品实际成本价格进行备注记录，非必填选项，不会在前台销售页面中显示。</p>
                            </dd>
                        </dl>
                        <dl>
                            <dt>分销金：</dt>
                            <dd>
                                <input value="<?php echo $goodsInfo['distribut']; ?>" name="distribut" class="text w60" type="text" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')"><em class="add-on"><i class="icon-renminbi"></i></em> <span></span>

                                <p class="hint">价格必须是0.00~9999999之间的数字，此价格用于前台会员帮助商家（本店）分销产品，返佣给分销会员的金额。</p>
                            </dd>
                        </dl>
                        <dl>
                            <dt><i class="required">*</i>商品图片：</dt>
                            <dd>
                                <div class="ncsc-goods-default-pic">
                                    <div class="goodspic-uplaod">
                                        <div class="upload-thumb">
                                            <img id="original_img2" src="<?php echo (isset($goodsInfo['original_img']) && ($goodsInfo['original_img'] !== '')?$goodsInfo['original_img']:'/public/images/default_goods_image_240.gif'); ?>">
                                        </div>
                                        <input name="original_img" id="original_img" value="<?php echo $goodsInfo['original_img']; ?>" type="hidden">
                                        <p class="hint">上传商品默认主图，如多规格值时将默认使用该图或分规格上传各规格主图；支持jpg、gif、png格式上传或从图片空间中选择，建议使用<font color="red">尺寸800x800像素以上、大小不超过1M的正方形图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
                                        <div class="handle">
                                            <div class="ncsc-upload-btn">
                                                <a onclick="GetUploadify3(1,'','goods','call_back');">
                                                    <p><i class="icon-upload-alt"></i>图片上传</p>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="demo"></div>
                            </dd>
                        </dl>
                        <dl>
                            <dt>商品视频：</dt>
                            <dd>
                                <div class="ncsc-goods-default-pic">
                                    <div class="goodspic-uplaod" >
                                        <div class="upload-thumb" id="video1">
                                            <video width="174" height="174" controls="controls">
                                                <source src="<?php echo $goodsInfo['video']; ?>" TYPE="video/mp4"/>
                                            </video>
                                        </div>
                                        <input name="video" id="video2" value="<?php echo $goodsInfo['video']; ?>" type="hidden">
                                        <p class="hint">上传商品视频，支持mp4,3gp,flv,avi,wmv格式</p>
                                        <div class="handle">
                                            <div class="ncsc-upload-btn" id="video_button">
                                                <?php if($goodsInfo['video']): ?>
                                                    <a onclick="delupload()"><p><i class="icon-upload-alt"></i>删除视频</p></a>
                                                <?php else: ?>
                                                    <a onclick="GetUploadify3(1,'','video','video_call_back','Flash');"><p><i class="icon-upload-alt"></i>选择文件</p></a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="video_demo"></div>
                            </dd>
                        </dl>
                        <?php if($goodsInfo['is_virtual'] != 1): ?>
                            <dl class="goods_shipping">
                                <dt>商品重量：</dt>
                                <dd>
                                    <p>
                                        <input type="text"  value="<?php echo $goodsInfo['weight']; ?>" name="weight" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" class="text">
                                    </p>
                                    <p class="hint">克 (以克为单位)</p>
                                </dd>
                            </dl>
                            <dl class="goods_shipping">
                                <dt>商品体积：</dt>
                                <dd>
                                    <p>
                                        <input type="text"  value="<?php echo $goodsInfo['volume']; ?>" name="volume" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" class="text">
                                    </p>
                                    <p class="hint">立方米 (以m³为单位)</p>
                                </dd>
                            </dl>
                            <dl class="goods_shipping">
                                <dt>是否包邮：</dt>
                                <dd>
									<ul class="ncsc-form-radio-list">
									<?php if($goodsInfo['root_goods_id'] != 0): if($goodsInfo[is_free_shipping] == 1): ?>
										<li>
											<label>
												<input class="is_free_shipping" type="hidden" value="1" name="is_free_shipping">
												是
											</label>
										</li>
										<?php else: ?>
										<li>
											<label>
												<input class="is_free_shipping" type="hidden" value="0" name="is_free_shipping">
												否
											</label>
										</li>
										<li class="freight_template">
											运费模板
											<select name="template_id">
												<?php if(is_array($freight_template) || $freight_template instanceof \think\Collection || $freight_template instanceof \think\Paginator): $i = 0; $__LIST__ = $freight_template;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$template): $mod = ($i % 2 );++$i;if($template['template_id'] == $goodsInfo['template_id']): ?>
														<option value="<?php echo $template['template_id']; ?>"><?php echo $template['template_name']; ?></option>
													<?php endif; endforeach; endif; else: echo "" ;endif; ?>
											</select>
										</li>
										<?php endif; else: ?>
										<li>
											<label>
												<input class="is_free_shipping" type="radio" <?php if($goodsInfo[is_free_shipping] == 1 || $goodsInfo[is_free_shipping] == ''): ?>checked="checked"<?php endif; ?> value="1" name="is_free_shipping">
												是</label>
										</li>
										<li>
											<label>
												<input class="is_free_shipping" type="radio" <?php if($goodsInfo[is_free_shipping] == 0): ?>checked="checked"<?php endif; ?> value="0" name="is_free_shipping">
												否</label>
										</li>
										<li class="freight_template" style="display: none;">
											运费模板
											<select name="template_id">
												<option value="0">请选择运费模板</option>
												<?php if(is_array($freight_template) || $freight_template instanceof \think\Collection || $freight_template instanceof \think\Paginator): $i = 0; $__LIST__ = $freight_template;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$template): $mod = ($i % 2 );++$i;?>
													<option value="<?php echo $template['template_id']; ?>" <?php if($template['template_id'] == $goodsInfo['template_id']): ?>selected="selected"<?php endif; ?>><?php echo $template['template_name']; ?></option>
												<?php endforeach; endif; else: echo "" ;endif; ?>
											</select>
											<?php if(empty($freight_template) || (($freight_template instanceof \think\Collection || $freight_template instanceof \think\Paginator ) && $freight_template->isEmpty())): ?><span style="color: red;">没有可选的运费模板，请前去<a href="<?php echo U('Freight/index'); ?>" target="_blank">添加</a></span><?php endif; ?>
										</li>
									<?php endif; ?>
									</ul>
                                    <p class="hint"></p>
                                </dd>
                            </dl>
                        <?php endif; ?>
                        <dl>
                            <dt nc_type="no_spec"><i class="required">*</i>总库存：</dt>
                            <dd nc_type="no_spec">
                                <?php if($goodsInfo[goods_id] > 0): ?>
                                    <input name="store_count" value="<?php echo $goodsInfo['store_count']; ?>" onkeyup="this.value=this.value.replace(/\D/g,'')" onpaste="this.value=this.value.replace(/\D/g,'')" class="text w60" type="text"
										<?php if($goodsInfo['root_goods_id'] != 0): ?>readonly="readonly"<?php endif; ?>>
                                <?php else: ?>
                                    <input name="store_count" value="<?php echo $tpshop_config[basic_default_storage]; ?>" onkeyup="this.value=this.value.replace(/\D/g,'')" onpaste="this.value=this.value.replace(/\D/g,'')" class="text w60" type="text">
                                <?php endif; ?>
                                    <span></span>
                                <p class="hint">商铺库存数量必须为0~999999999之间的整数<br>若启用了库存默认配置，则系统自动计算商品的总数，此处无需卖家填写</p>
                            </dd>
                        </dl>
                        <dl>
                            <dt>虚拟销售量：</dt>
                            <dd>
                                <p>
                                    <input type="text" value="<?php echo $goodsInfo['virtual_sales_sum']; ?>" name="virtual_sales_sum" id="virtual_sales_sum" onkeyup="this.value=this.value.replace(/\D/g,'')" onpaste="this.value=this.value.replace(/\D/g,'')" class="text">
                                <p class="hint" id="virtual_sales_sum_hint"></p>
                                </p>
                                <p class="hint">销售量：虚拟销售量+真实销售量</p>
                            </dd>
                        </dl>
                        <dl>
                            <dt>赠送积分：</dt>
                            <dd>
                                <p>
                                    <input type="text" value="<?php echo $goodsInfo['give_integral']; ?>" name="give_integral" id="give_integral" onkeyup="this.value=this.value.replace(/\D/g,'')" onpaste="this.value=this.value.replace(/\D/g,'')" class="text">
                                    <p class="hint" id="give_integral_hint">赠送积分不能超过100</p>
                                </p>
                                <p class="hint">购买商品赠送用户积分，积分比例1元:<?php echo $tpshop_config[shopping_point_rate]; ?>分</p>
                            </dd>
                        </dl>
                        <dl>
                            <dt>兑换积分：</dt>
                            <dd>
                                <p>
                                    <input type="text" value="<?php echo $goodsInfo['exchange_integral']; ?>" name="exchange_integral" onkeyup="this.value=this.value.replace(/\D/g,'')" onpaste="this.value=this.value.replace(/\D/g,'')" class="text">
                                </p>
                                <p class="hint">兑换该商品可使用多少积分，积分比例1元:<?php echo $tpshop_config[shopping_point_rate]; ?>分,虚拟商品填写此项无效. </p>
                                <P class="hint">兑换积分大于0时，不能参与任何活动。</P>
                            </dd>
                        </dl>
                        <dl>
                            <dt>商品关键词：</dt>
                            <dd>
                                <p>
                                    <input type="text" value="<?php echo $goodsInfo['keywords']; ?>" name="keywords" class="text">
                                </p>
                                <p class="hint">多个关键词，用空格隔开</p>
                            </dd>
                        </dl>
                            <?php if(($_GET[goods_id] == 0) OR ($goodsInfo[is_virtual] == 1)): ?>
                        <h3 id="demo3">特殊商品</h3>
                        <dl class="special-01">
					        <dt>虚拟商品：</dt>
					        <dd>
					          <ul class="ncsc-form-radio-list">
					            <li>
					               <input type="radio" name="is_virtual" value="1" id="is_virtual_1" <?php if($goodsInfo[is_virtual] == 1): ?>disabled  checked<?php endif; ?>><label for="is_virtual_1">是</label>
					            </li>
					            <li>
					               <input type="radio" name="is_virtual" value="0" id="is_virtual_0" <?php if($goodsInfo[is_virtual] == 1): ?>disabled<?php else: ?>checked<?php endif; ?>><label for="is_virtual_0">否</label>
					            </li>
					          </ul>
					          <p class="hint vital">*虚拟商品不能参加限时折扣和组合销售两种促销活动。也不能赠送赠品和推荐搭配。</p>
					          <p class="hint vital">*勾选发布虚拟商品后，该商品交易类型为“虚拟兑换码”验证形式,无需物流发货。</p>
					        </dd>
				     	</dl>
				     	<dl class="special-01" nctype="virtual_valid" style="display: none;">
					        <dt><i class="required">*</i>虚拟商品有效期至：</dt>
					        <dd>
					          <input type="text" name="virtual_indate" id="virtual_indate" class="w80 text hasDatepicker" value="<?php echo date('Y-m-d',$goodsInfo[virtual_indate]); ?>" readonly="readonly" >
                                <em class="add-on"><i class="icon-calendar"></i></em>
					          <span id="err_virtual_indate" style="color:#ff0000"></span>
					          <p class="hint">虚拟商品可兑换的有效期，过期后商品不能购买，电子兑换码不能使用。</p>
					        </dd>
					    </dl>
					    <dl class="special-01" nctype="virtual_valid" style="display: none;">
					        <dt><i class="required">*</i>虚拟商品购买上限：</dt>
					        <dd>
					          <input type="text" name="virtual_limit" id="virtual_limit" class="w80 text" value="<?php echo (isset($goodsInfo['virtual_limit']) && ($goodsInfo['virtual_limit'] !== '')?$goodsInfo['virtual_limit']:'1'); ?>"  onpaste="this.value=this.value.replace(/[^\d]/g,'')"  onblur="checkInputNum(this.name,1,10,'',1);" >
					          <span></span>
					          <p class="hint">请填写1~10之间的数字，虚拟商品最高购买数量不能超过10个。</p>
					        </dd>
					    </dl>
                        <input type="hidden" name="virtual_refund" id="virtual_refund_0" value="0" >
					    <?php endif; ?>
                        <h3 id="demo2">商品详情描述</h3>
                        <dl>
                            <dt>商品详情描述：</dt>
                           <dd id="ncProductDetails">
				           <div class="tabs ui-tabs ui-widget ui-widget-content ui-corner-all">
				            <ul class="ui-tabs-nav">
				              <li class="ui-tabs-selected"><a href="#panel-1" onclick="setTab(this);"><i class="icon-desktop"></i> 电脑端</a></li>
				              <li class="selected"><a href="#panel-2" onclick="setTab(this);"><i class="icon-mobile-phone"></i>手机端</a></li>
				            </ul>
				            </div>
                               <div id="panel-1" class="ui-tabs-panel">
                                    <textarea id="goods_content" name="goods_content" class="txt"><?php echo $goodsInfo['goods_content']; ?></textarea>
                                </div>
                                <div id="panel-2" class="ui-tabs-panel" style="display: none;">
						              <div class="ncsc-mobile-editor">
						                <div class="pannel">
						                  <div class="size-tip"><span nctype="img_count_tip">图片总数不得超过<em>20</em>张</span><i>|</i><span nctype="txt_count_tip">文字不得超过<em>5000</em>字</span></div>
						                  <div class="control-panel" nctype="mobile_pannel">
						                  <?php if(!(empty($goodsInfo['mobile_content']) || (($goodsInfo['mobile_content'] instanceof \think\Collection || $goodsInfo['mobile_content'] instanceof \think\Paginator ) && $goodsInfo['mobile_content']->isEmpty()))): if(is_array($goodsInfo['mobile_content']) || $goodsInfo['mobile_content'] instanceof \think\Collection || $goodsInfo['mobile_content'] instanceof \think\Paginator): if( count($goodsInfo['mobile_content'])==0 ) : echo "" ;else: foreach($goodsInfo['mobile_content'] as $key=>$mb): if($mb['type'] == 'text'): ?>
						                        <div class="module m-text">
							                      <div class="tools"><a nctype="mp_up" href="javascript:void(0);">上移</a><a nctype="mp_down" href="javascript:void(0);">下移</a><a nctype="mp_edit" href="javascript:void(0);">编辑</a><a nctype="mp_del" href="javascript:void(0);">删除</a></div>
							                      <div class="content">
							                        <div class="text-div"><?php echo $mb['value']; ?></div>
							                      </div>
							                      <div class="cover"></div>
							                    </div>
							                    <?php else: ?>
						                        <div class="module m-image">
							                      <div class="tools"><a nctype="mp_up" href="javascript:void(0);">上移</a><a nctype="mp_down" href="javascript:void(0);">下移</a><a nctype="mp_rpl" href="javascript:void(0);">替换</a><a nctype="mp_del" href="javascript:void(0);">删除</a></div>
							                      <div class="content">
							                        <div class="image-div"><img src="<?php echo $mb['value']; ?>"></div>
							                      </div>
							                      <div class="cover"></div>
							                    </div>
							                    <?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>      
						                  </div>
						                  <div class="add-btn">
						                    <ul class="btn-wrap">
						                      <li><a href="javascript:void(0);" nctype="mb_add_img"><i class="icon-picture"></i>
						                        <p>图片</p>
						                        </a></li>
						                      <li><a href="javascript:void(0);" nctype="mb_add_txt"><i class="icon-font"></i>
						                        <p>文字</p>
						                        </a></li>
						                    </ul>
						                  </div>
						                </div>
						                <div class="explain">
						                  <dl>
						                    <dt>1、基本要求：</dt>
						                    <dd>（1）手机详情总体大小：图片+文字，图片不超过20张，文字不超过5000字；</dd>
						                    <dd>建议：所有图片都是本宝贝相关的图片。</dd>
						                  </dl><dl>
						                    <dt>2、图片大小要求：</dt>
						                    <dd>（1）建议使用宽度480 ~ 620像素、高度小于等于960像素的图片；</dd>
						                    <dd>（2）格式为：JPG\JEPG\GIF\PNG；</dd>
						                    <dd>举例：可以上传一张宽度为480，高度为960像素，格式为JPG的图片。</dd>
						                  </dl><dl>
						                    <dt>3、文字要求：</dt>
						                    <dd>（1）每次插入文字不能超过500个字，标点、特殊字符按照一个字计算；</dd>
						                    <dd>（2）请手动输入文字，不要复制粘贴网页上的文字，防止出现乱码；</dd>
						                    <dd>（3）以下特殊字符“&lt;”、“&gt;”、“"”、“'”、“\”会被替换为空。</dd>
						                    <dd>建议：不要添加太多的文字，这样看起来更清晰。</dd>
						                  </dl>
						                </div>
						              </div>
						              <div class="ncsc-mobile-edit-area" nctype="mobile_editor_area">
						                <div nctype="mea_img" class="ncsc-mea-img" style="display: none;"></div>
						                <div class="ncsc-mea-text" nctype="mea_txt" style="display: none;">
						                  <p id="meat_content_count" class="text-tip"></p>
						                  <textarea class="textarea valid" nctype="meat_content"></textarea>
						                  <div class="button"><a class="ncbtn ncbtn-bluejeansjeansjeans" nctype="meat_submit" href="javascript:void(0);">确认</a><a class="ncbtn ml10" nctype="meat_cancel" href="javascript:void(0);">取消</a></div>
						                  <a class="text-close" nctype="meat_cancel" href="javascript:void(0);">X</a>
						                </div>
						              </div>
						              <input name="m_body" autocomplete="off" type="hidden" value='<?php echo $goodsInfo['mobile_body']; ?>'>
            					</div>
                                <p class="hint">商品详情描述</p>
                            </dd>
                        </dl>
                        <dl style="display: none">
                            <dt>关联版式：</dt>
                            <dd> 
                            <span class="mr50">
                              <label>顶部版式</label>
                              <select name="plate_top">
                                <option value="0">请选择</option>
                                <?php if(is_array($plate_1) || $plate_1 instanceof \think\Collection || $plate_1 instanceof \think\Paginator): $i = 0; $__LIST__ = $plate_1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                <option value="<?php echo $vo['plate_id']; ?>" <?php if($vo['plate_id'] == $goodsInfo['plate_top']): ?>selected="selected"<?php endif; ?>><?php echo $vo['plate_name']; ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>

                              </select>
                              </span> 
                              <span class="mr50">
                              <label>底部版式</label>
                              <select name="plate_bottom">
                                <option value="0">请选择</option>
                                <?php if(is_array($plate_0) || $plate_0 instanceof \think\Collection || $plate_0 instanceof \think\Paginator): $i = 0; $__LIST__ = $plate_0;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                <option value="<?php echo $vo['plate_id']; ?>" <?php if($vo['plate_id'] == $goodsInfo['plate_bottom']): ?>selected="selected"<?php endif; ?>><?php echo $vo['plate_name']; ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                               </select>
                              </span> 
                              </dd>
                         </dl>
                    </div>

                    <div class="ncsc-form-goods" id="tab_goods_images" style="display: none;">
                        <dl>
                            <dd>
                                <div class="ncsc-form-goods-pic">
									<div class="container">
							            <div class="ncsc-goodspic-list" style="opacity: 1;">
								        <div class="clabackkj">
								          <h3>管理缩略图</h3>
								          <a ata-original-title="添加商品" onclick="add_image();" class="ncbtn ncbtn-grapefruit mt5"><i class="fa fa-plus"></i>添加缩略图</a>
								         </div>
								        	<ul nctype="ul0" class="goods-pic-list">
								        	<?php if(is_array($goodsImages) || $goodsImages instanceof \think\Collection || $goodsImages instanceof \think\Paginator): if( count($goodsImages)==0 ) : echo "" ;else: foreach($goodsImages as $k=>$vo): ?>
							                    <li class="ncsc-goodspic-upload">
									            <div class="upload-thumb"><a onclick="" href="<?php echo $vo['image_url']; ?>" target="_blank"><img nctype="file_<?php echo $k; ?>" src="<?php echo $vo['image_url']; ?>"></a>
									              <input type="hidden" value="<?php echo $vo['image_url']; ?>" name="goods_images[]" data-id="file_<?php echo $k; ?>">
									            </div>
									            <div nctype="file_00" class="show-default">
									              <p><i class="icon-ok-circle"></i>
									              </p><a title="移除" onclick="ClearPicArr2(this,'<?php echo $vo['image_url']; ?>')" class="del" <?php if($k >= 5): ?>ncaction="del"<?php endif; ?> nctype="del" href="javascript:void(0)">X</a>
									            </div>
									            <div class="show-sort">排序：<input type="text" maxlength="1" size="1"class="text" name="img_sorts[]" value="<?php echo $vo['img_sort']; ?>">
									            </div>
									            <div class="ncsc-upload-btn"><a href="javascript:void(0);"   onclick="img_upload(1, 'file_<?php echo $k; ?>', 'goods_album', 'call_back2');"><p><i class="icon-upload-alt"></i>上传</p></a>
									             </div>
									          </li>
									      <?php endforeach; endif; else: echo "" ;endif; if(count($goodsImages) < 5): $__FOR_START_1861678007__=count($goodsImages);$__FOR_END_1861678007__=5;for($i=$__FOR_START_1861678007__;$i < $__FOR_END_1861678007__;$i+=1){ ?>
                                                        <li class="ncsc-goodspic-upload">
                                                            <div class="upload-thumb"><a onclick="" href="#" target="_blank"><img nctype="file_<?php echo $i; ?>"
                                                                                                                                  src="/public/static/images/default_goods_image_240.gif"></a>
                                                                <input type="hidden" value="" name="goods_images[]" data-id="file_<?php echo $i; ?>">
                                                            </div>
                                                            <div class="show-default">
                                                                <p><i class="icon-ok-circle"></i>
                                                                </p><a title="移除" onclick="ClearPicArr2(this,'')" class="del" nctype="del" href="javascript:void(0)">X</a>
                                                            </div>
                                                            <div class="show-sort">排序 ：<input type="text" maxlength="1" size="1" class="text" name="img_sorts[]">
                                                            </div>
                                                            <div class="ncsc-upload-btn"><a href="javascript:void(0);"
                                                                                            onclick="img_upload(1, 'file_<?php echo $i; ?>', 'goods_album', 'call_back2');"><span></span>

                                                                <p><i class="icon-upload-alt"></i>上传</p>
                                                            </a>
                                                            </div>
                                                        </li>
                                                    <?php } endif; ?>
									      </ul>
									      <input type="hidden" value="" name="goods_images[]" >
									      </div>
								      </div>
								      <div class="sidebar"><div class="alert alert-info alert-block" id="uploadHelp">
									    <div class="faq-img"></div>
									    <h4>上传要求：</h4><ul>
									    <li>1. 请使用jpg\jpeg\png等格式、单张大小不超过1M的正方形图片。</li>
									    <li>3. 最多可上传10张图片，默认前面5张上传框不可删除, 新增加的上传框可删除, 但对上传的图片无影响, 已实际上传图片数量为准</li>
									    <li>4. 更改排序数字修改商品图片的排列显示顺序, 数字越小的越靠前显示</li>
									    <li>5. 图片质量要清晰，不能虚化，要保证亮度充足。</li>
									    <li>6. 操作完成后请点击"保存"按钮 , 否则上传的图片不会被保存</li>
									    </ul><h4>建议:</h4><ul><li>1. 主图为白色背景正面图。</li><li>2. 排序依次为正面图-&gt;背面图-&gt;侧面图-&gt;细节图。</li></ul></div>
									   </div>
								</div>
                            </dd>
                        </dl>
                    </div>
                    <div class="ncsc-form-goods" id="tab_goods_spec" style="display: none;">
                        <table class="table table-bordered" id="goods_spec_table">
                            <tr>
                                <td colspan="2">
                                    <div class="alert mt15 mb5"><strong>操作提示：</strong>
									  <ul>
										<li style="color:red">发布商品时, 如果规格没有显示出来请检查以下步骤</li>
									    <li>1、"通用信息"选项卡中是否选择商品分类</li>
									    <li>2、如果已选择商品分类，还没有显示出规格，请联系平台确认该商品分类是否绑定商品模型</li>
									    <li>3、如果没有合适的规格名称，请联系平台</li>
									  </ul>
									</div>
                                </td>
                            </tr>
                        </table>
                        <div id="ajax_spec_data"><!-- ajax 返回规格--></div>
                    </div>
                    <div class="ncsc-form-goods" id="tab_goods_attr" style="display: none;">
                        <table class="table table-bordered" id="goods_attr_table">
                            <tr>
                                <td colspan="2">
                                    <div class="alert mt15 mb5"><strong>操作提示：</strong>
									  <ul>
										<li style="color:red">发布商品时, 如果属性没有显示出来请检查以下步骤</li>
									    <li>1、"通用信息"选项卡中是否选择商品分类</li>
									    <li>2、如果已选择商品分类，还没有显示出属性，请联系平台确认该商品分类是否绑定商品模型</li>
									    <li>3、如果没有合适的属性名称，请联系平台</li>
									  </ul>
									</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="bottom tc hr32">
                        <label class="submit-border">
                            <input nctype="formSubmit" class="submit" id="submit" value="保存" type="submit">
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
	var root_goods_id = <?php echo (isset($goodsInfo['root_goods_id']) && ($goodsInfo['root_goods_id'] !== '')?$goodsInfo['root_goods_id']:'0'); ?>;
    /**
     * 一键申请经营类目
     */
    function applyClass(){
        var cat_id = <?php echo $goods_cat[2][id]; ?>;
        var cat_id2 = <?php echo $goods_cat[1][id]; ?>;
        var cat_id3 = <?php echo $goods_cat[0][id]; ?>;
        $.ajax({
            type:'post',
            url:"/index.php/seller/Store/get_bind_class",
            data:{class_1:cat_id,class_2:cat_id2,class_3:cat_id3},
            dataType : 'json',
            success : function(data){
                if(data.status==1){
                    layer.msg('操作成功', {icon: 1});
                    window.location.reload();
                }else{
                    layer.alert(data.msg, {icon: 2});
                }
            }
        });
    }
    /*
     * 上传之后删除组图input
     * @access   public
     * @val      string  删除的图片input
     */
    function ClearPicArr2(obj,path){
        var action = $(obj).attr('ncaction');
        if(action != undefined && action =='del'){
            $(obj).parent().parent().remove();
        }
        //删除图片文件
        if(path == '' || path == undefined){
            return;
        }
        // 删除数据库记录
         $.ajax({
            type:'GET',
            url:"<?php echo U('Seller/Goods/del_goods_images'); ?>",
            data:{filename:path},
            success:function(){
                 $(obj).parent().siblings('.upload-thumb').find('img').attr("src", '/public/static/images/default_goods_image_240.gif'); // 删除完服务器的, 再删除 html上的图片
                //删除input goods_image
                $(obj).parent().siblings('.upload-thumb').find('input[type=hidden]').val("");
                $(obj).parent().siblings('.show-sort').find('input[type=text]').val("0");
                
                //如果删除的是商品主图, 则把商品主图隐藏域删掉
                if($("#original_img").val() == path){
                	$("#original_img").val("");
                    $("#original_img2").attr("src" , '/Public/static/images/default_goods_image_240.gif');
                }
            }
        });
    }

    function select_nav(obj){
        var data_id = $(obj).attr('data-id');
        $('.ncsc-form-goods').hide();
        $('#'+data_id).show();
        $(obj).parent().parent().find('li').removeClass('active');
        $(obj).parent().addClass('active');
    }
    // 上传商品图片成功回调函数
    function call_back(fileurl_tmp){
        $("#original_img").val(fileurl_tmp);
        $("#original_img2").attr('src', fileurl_tmp);
    }

    var cur_img_id = "";
    function img_upload(num,elementid,path,callback){
    	cur_img_id = elementid;
    	GetUploadify3(num,elementid,path,callback);
    }

    // 上传商品相册回调函数
    function call_back2(paths){
        var a = Object.prototype.toString.call(paths)
        if(a == '[object String]'){
            // 只一张图片
            $("img[nctype="+cur_img_id+"]").attr("src" , paths);
            $("input[data-id="+cur_img_id+"]").val(paths);

            //重新绑定删除事件
            $("input[data-id="+cur_img_id+"]").parent().siblings(".show-default").find("a:eq(0)").removeAttr('onclick').click(function(){  ClearPicArr2(this, paths) }); ;

        }else{
            //多张图片
            if(paths == undefined || paths[0] == undefined) return ;
            $("img[nctype="+cur_img_id+"]").attr("src" , paths[0]);
            $("input[data-id="+cur_img_id+"]").val(paths[0]);

            //重新绑定删除事件
            $("input[data-id="+cur_img_id+"]").parent().siblings(".show-default").find("a:eq(0)").removeAttr('onclick').click(function(){  ClearPicArr2(this, paths[0]) }); ;
        }

    }

    //上传视频回调
    function video_call_back(fileurl_tmp)
    {
        $("#video2").val(fileurl_tmp);
        var html = '<video width="174" height="174" controls="controls"><source src="'+fileurl_tmp+'" TYPE="video/mp4" /></video>'
        $("#video1").html(html)
        if(typeof(fileurl_tmp) !='undefined') {
            $('#video_button').html('<a onclick="delupload()"><p><i class="icon-upload-alt"></i>删除视频</p></a>')
        }
    }

    //删除视频
    function delupload(){
        $.ajax({
            url:"<?php echo U('Uploadify/delupload'); ?>",
            data:{url:$('#video2').val()},
            success:function(data){
                if (data ==1 ){
                    layer.msg('删除成功！',{icon:1});
                    $('#video2').val('');
                    var html = '<video width="174" height="174" controls="controls"><source src="" TYPE="video/mp4" /></video>'
                    $("#video1").html(html)
                    var video_button_html = '    <a onclick="GetUploadify3(1,\'\',\'video\',\'video_call_back\',\'Flash\');"><p><i class="icon-upload-alt"></i>选择文件</p></a>';
                    $('#video_button').html(video_button_html);
                }else{
                    layer.msg('删除失败',{icon:2});
                }
            },
            error:function () {
                layer.msg('网络繁忙，请稍后再试!',{icon:2});
            }
        })
    }

    /**
     *	添加图片
     */
    function add_image(){
    	var length = $('.goods-pic-list>.ncsc-goodspic-upload').length;
    	if(length >= 10){
    		layer.alert("缩略图数量不能超过10个!", {icon:2});
    		return;
    	}
    	var new_id = "file_"+(length);
    	var  last_div = $(".goods-pic-list:last").children("li:first-child").prop("outerHTML");
    	$(".goods-pic-list:last").children("li:last-child").after(last_div);

    	var last_li = $(".goods-pic-list").children("li:last-child");
    	//第一个: a标签
    	last_li.find("a:eq(0)").attr("href" ,  '/public/static/images/default_goods_image_240.gif');
    	//img标签
    	last_li.find("img:eq(0)").attr("nctype" , new_id).attr("src" ,  '/public/static/images/default_goods_image_240.gif'); //src
    	//隐藏域: goods_images
    	last_li.find("input[type=hidden]:eq(0)").attr("data-id" , new_id);
    	//排序字段:
    	last_li.find("input.text").val(0);

    	//第二个: a标签 移除, 图片上传后, 修改ClearPicArr2参数, 添加ncaction属性, 如果该属性是del, 说明是超过5个的上传框, 可以删除.
    	last_li.find("a:eq(1)").attr("ncaction" , "del").removeAttr('onclick').click(function(){  ClearPicArr2(this,'') });
    	//第三个: a标签, 上传图片按钮
    	last_li.find("a:eq(2)").unbind('click').removeAttr('onclick').click(function(){  img_upload(1,  new_id, 'goods_album', 'call_back2') });

    }

    /**
     * ajax 加载规格 和属性
     */
    function ajaxGetSpecAttr(spec_item)
    {
        // ajax调用 返回规格
        var goods_id = $('input[name=goods_id]').val();
        var cat_id3 = $('input[name=cat_id3]').val();
        $.ajax({
            type:'GET',
//			data:{goods_id:goods_id,cat_id3:cat_id3},
            url:"/index.php?m=Seller&c=Goods&a=ajaxGetSpecSelect&purpose=1&root_goods_id=<?php echo $goodsInfo['root_goods_id']; ?>&goods_id="+goods_id+"&cat_id3="+cat_id3,
            success:function(data){
                $("#ajax_spec_data").empty().html(data);
                if($.trim(data) != ''){
                    ajaxGetSpecInput(spec_item);	// 触发完  马上触发 规格输入框
                }
            }
        });

        // 商品类型切换时 ajax 调用  返回不同的属性输入框
        $.ajax({
            type:'GET',
            url:"/index.php?m=Seller&c=Goods&a=ajaxGetAttrInput&goods_id="+goods_id+"&cat_id3="+cat_id3,
            success:function(data){
                $("#goods_attr_table tr:gt(0)").remove();
                $("#goods_attr_table").append(data);
            }
        });
    }
    
   
    /** 以下是编辑时默认选中某个商品分类*/
    $(document).ready(function(){
    	$("#shop_price").blur(function(){  
    		//可赠送积分    			
			var send_point = calc_send_point();
			$("#give_integral_hint").html("可赠送积分不能超过"+send_point);
        });
    	$('#shop_price').trigger("blur");

        // 店铺内部分类
        <?php if($goodsInfo['store_cat_id2'] > 0): ?>
                get_store_category("<?php echo $goodsInfo['store_cat_id1']; ?>",'store_cat_id2',"<?php echo $goodsInfo['store_cat_id2']; ?>");
        <?php endif; ?>
        ajaxGetSpecAttr();
        // 商品品牌根据分类显示相关的品牌
        $('#brand_id option').each(function(){
            var cat_id1 = $('input[name=cat_id1]').val();
            // if($(this).data('cat_id1') != cat_id1 && $(this).val() > 0){
            //     $(this).hide();
            // }
        });
        
        <?php if($goodsInfo['is_virtual'] == 1): ?>
        	$('[nctype="virtual_valid"]').show();
        	$('[nctype="virtual_null"]').hide();
		<?php endif; ?>
		
		$("#addEditGoodsForm").validate({
    		debug: false, //调试模式取消submit的默认提交功能
    		focusInvalid: false, //当为false时，验证无效时，没有焦点响应  
            onkeyup: false,
            submitHandler: function(form){   //表单提交句柄,为一回调函数，带一个参数：form
                var item_array = new Array();
                $("img[id^=item_img_]").parent("span[data-img_id]").prevAll("button[class='btn btn-success']").each(function (i,v) {
                    item_array[i] = parseInt($(v).attr('data-item_id'));
                })//所有选中的item
    		    var item_img_array = new Array();
    		    $("button[class='btn btn-success']").next().next("span[data-img_id]").find("img[id^=item_img_][src!='/public/images/add-button.jpg']").each(function (index,value) {
                    item_img_array[index] = parseInt($(value).attr('id').slice(9));
                })//所有选中item下面上传了图片的
                //判断：所有算中item上传的图片为空 或者所有选中item=所有选中的图片
                if (item_array.sort().toString() != item_img_array.sort().toString() && item_img_array.length != 0) {
                    layer.alert("已选规格必须全部都传图或都不传图" , {icon:2, time:2000});
                    return;
                }
                $('#submit').attr('disabled',true);
            	var send_point = calc_send_point();   
            	var give_integral = $("input[name='give_integral']").val();
            	if(give_integral > send_point){
            		layer.alert("最多可赠送积分不能超过"+send_point , {icon:2, time:2000});
                    $('#submit').attr('disabled',false);
            		return;
            	}
                $.ajax({
                    type: "POST",
                    url: "<?php echo U('Goods/save'); ?>",
                    data: $('#addEditGoodsForm').serialize(),
                    dataType: "json",
                    error: function(request) {
                        layer.alert("服务器繁忙, 请联系管理员!",{icon:2});
                        return false;
                    },
                    success: function (data) {
                        if (data.status == 1) {
                            $("input[name=goods_id]").attr('value',data.result.goods_id);
                            layer.msg(data.msg,{icon: 1,time: 2000},function(){
                                    window.location.href="<?php echo U('/seller/Goods/goodsList/redirect/1'); ?>";
                                });
                        } else {
                            layer.msg(data.msg,{icon: 2,time:2000},function () {
                                $('#submit').attr('disabled',false);
                            });
                            // 验证失败提示错误
                            for (var i in data.result) {
                                $("#err_" + i).text(data.result[i]).show(); // 显示对于的 错误提示
                            }
                        }
                    }
                });
            },
            ignore:":button,:checkbox",	//不验证的元素
            rules:{
            	goods_name:{
            		required:true
            	},
            	shop_price:{
            		required:true,
            		number:true,
            		min:0
            	},
            	market_price:{
            		required:true,
            		number:true,
            		min:0
            	},
            	store_count:{
            		required:true,
            		digits:true,
            		min:0
            	}
            },
            messages:{
            	goods_name:{
            		required:"请填写商品名称"
            	},
            	shop_price:{
            		required:"请填写商品售价",
            		number:"请输入数字",
            		min:"商品价格不能小于0"
            	},
            	market_price:{
            		required:"请填写市场售价",
            		number:"请输入数字",
            		min:"商品价格不能小于0"
            	},
            	store_count:{
            		required:"请输入库存",
            		digits:"库存必须是正数",
            		min:"库存数量不能小于0"
            	}
            }
    	});
        initFreight();
    });
    
    /** 计算最多可赠送积分数 */
    function calc_send_point(){
    	
    	var point_rate = "<?php echo (isset($tpshop_config['shopping_point_rate']) && ($tpshop_config['shopping_point_rate'] !== '')?$tpshop_config['shopping_point_rate']:1); ?>";
    	var point_send_limit = "<?php echo (isset($tpshop_config[shopping_point_send_limit]) && ($tpshop_config[shopping_point_send_limit] !== '')?$tpshop_config[shopping_point_send_limit]:1); ?>";
    	 
    	var shop_price = $("#shop_price").val();
		//可赠送积分    			
		var send_point = shop_price * point_rate * point_send_limit / 100;
		return send_point;
    }
    
    function get_store_category(id,next,select_id){
        var url = '/index.php?m=Home&c=api&a=get_store_category';
        var store_id = "<?php echo $store_id; ?>";
        $.ajax({
            type : "GET",
            url : url,
            data:{'store_id':store_id,'parent_id':id},
            error: function(request) {
                layer.alert("服务器繁忙, 请联系管理员!",{icon:2});
                return;
            },
            success: function(v) {
                v = "<option value='0'>请选择商品分类</option>" + v;
                $('#'+next).empty().html(v);
                (select_id > 0) && $('#'+next).val(select_id);//默认选中
            }
        });
    }

    // 属性输入框的加减事件
    function addAttr(a)
    {
        var attr = $(a).parent().parent().prop("outerHTML");
        attr = attr.replace('addAttr','delAttr').replace('+','-');
        $(a).parent().parent().after(attr);
    }
    // 属性输入框的加减事件
    function delAttr(a)
    {
        $(a).parent().parent().remove();
    }
    function choosebox(o){
        var vt = $(o).is(':checked');
        if(vt){
            $('input[type=checkbox]').prop('checked',vt);
        }else{
            $('input[type=checkbox]').removeAttr('checked');
        }
    }
    $(document).ready(function(){
        $(":checkbox[cka]").click(function(){
            var $cks = $(":checkbox[ck='"+$(this).attr("cka")+"']");
            if($(this).is(':checked')){
                $cks.each(function(){$(this).prop("checked",true);});
            }else{
                $cks.each(function(){$(this).removeAttr('checked');});
            }
        });
    });
    
    
    var is_virtual = <?php echo ($_GET[goods_id] == 0) || ($goodsInfo[is_virtual] == 1) ? "1" : "0"; ?>;
    
    /* 虚拟控制 // 虚拟商品有效期 */
    if(is_virtual == '1'){	//虚拟商品属性
    	$('#virtual_indate').layDate();
        $('[name="is_virtual"]').change(function(){
            if ($('#is_virtual_1').prop("checked")) {
                $('[nctype="virtual_valid"]').show();
                $('[nctype="virtual_null"]').hide();
                $('.goods_shipping').hide();
                $("input[name='is_free_shipping'][value='1']").attr("checked",true);
            } else {
                $('[nctype="virtual_valid"]').hide();
                $('[nctype="virtual_null"]').show();
                $('.goods_shipping').show();
                $('#virtual_limit').val(1);
            }
            initFreight();
        });
    }

    $(function () {
        $(document).on("click", '.is_free_shipping', function (e) {
            initFreight();
        })
    })
    function initFreight(){
		if (root_goods_id == 0) { 
			var is_free_shipping = $("input[name='is_free_shipping']:checked").val();
			if(is_free_shipping == 0){
				$('.freight_template').show();
			}else{
				$('.freight_template').hide();
			}
		}
    }

    
    /* 插入商品图片 */
    function insert_img(name, src) {
        $('input[nctype="goods_image"]').val(name);
        $('img[nctype="goods_image"]').attr('src',src);
    }
    
    function setTab(obj){
    	$('.ui-tabs-panel').hide();
    	$(obj).parents('li').addClass('ui-tabs-selected').siblings().removeClass('ui-tabs-selected')
    	$($(obj).attr('href')).show();
    }
    
    $(function(){
        // 取消回车提交表单
        $('input').keypress(function(e){
            var key = window.event ? e.keyCode : e.which;
            if (key.toString() == "13") {
             	return false;
            }
        });
		
        /* 手机端 商品描述 */
        // 显示隐藏控制面板
        $('div[nctype="mobile_pannel"]').on('click', '.module', function(){
            mbPannelInit();
            $(this).siblings().removeClass('current').end().addClass('current');
        });
        // 上移
        $('div[nctype="mobile_pannel"]').on('click', '[nctype="mp_up"]', function(){
            var _parents = $(this).parents('.module:first');
            _rs = mDataMove(_parents.index(), 0);
            if (!_rs) {
                return false;
            }
            _parents.clone().insertBefore(_parents.prev()).end().remove();
            mbPannelInit();
        });
        // 下移
        $('div[nctype="mobile_pannel"]').on('click', '[nctype="mp_down"]', function(){
            var _parents = $(this).parents('.module:first');
            _rs = mDataMove(_parents.index(), 1);
            if (!_rs) {
                return false;
            }
            _parents.clone().insertAfter(_parents.next()).end().remove();
            mbPannelInit();
        });
        // 删除
        $('div[nctype="mobile_pannel"]').on('click', '[nctype="mp_del"]', function(){
            var _parents = $(this).parents('.module:first');
            mDataRemove(_parents.index());
            _parents.remove();
            mbPannelInit();
        });
        // 编辑
        $('div[nctype="mobile_pannel"]').on('click', '[nctype="mp_edit"]', function(){
            $('a[nctype="meat_cancel"]').click();
            var _parents = $(this).parents('.module:first');
            var _val = _parents.find('.text-div').html();
            $(this).parents('.module:first').html('')
                .append('<div class="content"></div>').find('.content')
                .append('<div class="ncsc-mea-text" nctype="mea_txt"></div>')
                .find('div[nctype="mea_txt"]')
                .append('<p id="meat_content_count" class="text-tip">')
                .append('<textarea class="textarea valid" data-old="' + _val + '" nctype="meat_content">' + _val + '</textarea>')
                .append('<div class="button"><a class="ncsc-btn ncsc-btn-blue" nctype="meat_edit_submit" href="javascript:void(0);">确认</a><a class="ncsc-btn ml10" nctype="meat_edit_cancel" href="javascript:void(0);">取消</a></div>')
                .append('<a class="text-close" nctype="meat_edit_cancel" href="javascript:void(0);">X</a>')
                .find('#meat_content_count').html('').end()
                .find('textarea[nctype="meat_content"]').unbind().charCount({
                    allowed: 500,
                    warning: 50,
                    counterContainerID: 'meat_content_count',
                    firstCounterText:   '还可以输入',
                    endCounterText:     '字',
                    errorCounterText:   '已经超出'
                });
        });
        // 编辑提交
        $('div[nctype="mobile_pannel"]').on('click', '[nctype="meat_edit_submit"]', function(){
            var _parents = $(this).parents('.module:first');
            var _c = toTxt(_parents.find('textarea[nctype="meat_content"]').val().replace(/[\r\n]/g,''));
            var _cl = _c.length;
            if (_cl == 0 || _cl > 500) {
                return false;
            }
            _data = new Object;
            _data.type = 'text';
            _data.value = _c;
            _rs = mDataReplace(_parents.index(), _data);
            if (!_rs) {
                return false;
            }
            _parents.html('').append('<div class="tools"><a nctype="mp_up" href="javascript:void(0);">上移</a><a nctype="mp_down" href="javascript:void(0);">下移</a><a nctype="mp_edit" href="javascript:void(0);">编辑</a><a nctype="mp_del" href="javascript:void(0);">删除</a></div>')
                .append('<div class="content"><div class="text-div">' + _c + '</div></div>')
                .append('<div class="cover"></div>');

        });
        // 编辑关闭
        $('div[nctype="mobile_pannel"]').on('click', '[nctype="meat_edit_cancel"]', function(){
            var _parents = $(this).parents('.module:first');
            var _c = _parents.find('textarea[nctype="meat_content"]').attr('data-old');
            _parents.html('').append('<div class="tools"><a nctype="mp_up" href="javascript:void(0);">上移</a><a nctype="mp_down" href="javascript:void(0);">下移</a><a nctype="mp_edit" href="javascript:void(0);">编辑</a><a nctype="mp_del" href="javascript:void(0);">删除</a></div>')
            .append('<div class="content"><div class="text-div">' + _c + '</div></div>')
            .append('<div class="cover"></div>');
        });
        // 初始化控制面板
        mbPannelInit = function(){
            $('div[nctype="mobile_pannel"]')
                .find('a[nctype^="mp_"]').show().end()
                .find('.module')
                .first().find('a[nctype="mp_up"]').hide().end().end()
                .last().find('a[nctype="mp_down"]').hide();
        }
        // 添加文字按钮，显示文字输入框
        $('a[nctype="mb_add_txt"]').click(function(){
            $('div[nctype="mea_txt"]').show();
            $('a[nctype="meai_cancel"]').click();
        
        $('div[nctype="mobile_editor_area"]').find('textarea[nctype="meat_content"]').unbind().charCount({
            allowed: 500,
            warning: 50,
            counterContainerID: 'meat_content_count',
            firstCounterText:   '还可以输入',
            endCounterText:     '字',
            errorCounterText:   '已经超出'
        })});
        // 关闭 文字输入框按钮
        $('a[nctype="meat_cancel"]').click(function(){
            $(this).parents('div[nctype="mea_txt"]').find('textarea[nctype="meat_content"]').val('').end().hide();
        });
        // 提交 文字输入框按钮
        $('a[nctype="meat_submit"]').click(function(){
            var _c = toTxt($('textarea[nctype="meat_content"]').val().replace(/[\r\n]/g,''));
            var _cl = _c.length;
            if (_cl == 0 || _cl > 500) {
                return false;
            }
            _data = new Object;
            _data.type = 'text';
            _data.value = _c;
            
            _rs = mDataInsert(_data);
            if (!_rs) {
                return false;
            }
            $('<div class="module m-text"></div>')
                .append('<div class="tools"><a nctype="mp_up" href="javascript:void(0);">上移</a><a nctype="mp_down" href="javascript:void(0);">下移</a><a nctype="mp_edit" href="javascript:void(0);">编辑</a><a nctype="mp_del" href="javascript:void(0);">删除</a></div>')
                .append('<div class="content"><div class="text-div">' + _c + '</div></div>')
                .append('<div class="cover"></div>').appendTo('div[nctype="mobile_pannel"]');
            
            $('a[nctype="meat_cancel"]').click();
        });
        // 添加图片按钮，显示图片空间文字
        $('a[nctype="mb_add_img"]').click(function(){
            $('a[nctype="meat_cancel"]').click();
            $('div[nctype="mea_img"]').show().load('/index.php?m=Seller&c=Goods&a=pic_list');
        });
        // 关闭 图片选择
        $('div[nctype="mobile_editor_area"]').on('click', 'a[nctype="meai_cancel"]', function(){
            $('div[nctype="mea_img"]').html('');
        });
        // 插图图片
        insert_mobile_img = function(data){
            _data = new Object;
            _data.type = 'image';
            _data.value = data;
            _rs = mDataInsert(_data);
            if (!_rs) {
                return false;
            }
            $('<div class="module m-image"></div>')
                .append('<div class="tools"><a nctype="mp_up" href="javascript:void(0);">上移</a><a nctype="mp_down" href="javascript:void(0);">下移</a><a nctype="mp_rpl" href="javascript:void(0);">替换</a><a nctype="mp_del" href="javascript:void(0);">删除</a></div>')
                .append('<div class="content"><div class="image-div"><img src="' + data + '"></div></div>')
                .append('<div class="cover"></div>').appendTo('div[nctype="mobile_pannel"]');
            
        }
        // 替换图片
        $('div[nctype="mobile_pannel"]').on('click', 'a[nctype="mp_rpl"]', function(){
            $('a[nctype="meat_cancel"]').click();
            $('div[nctype="mea_img"]').show().load('/index.php?m=Seller&c=Goods&a=pic_list&type=replace');
        });
        // 插图图片
        replace_mobile_img = function(data){
            var _parents = $('div.m-image.current');
            _parents.find('img').attr('src', data);
            _data = new Object;
            _data.type = 'image';
            _data.value = data;
            mDataReplace(_parents.index(), _data);
        }
        // 插入数据
        mDataInsert = function(data){
            _m_data = mDataGet();
            _m_data.push(data);
            return mDataSet(_m_data);
        }
        // 数据移动 
        // type 0上移  1下移
        mDataMove = function(index, type) {
            _m_data = mDataGet();
            _data = _m_data.splice(index, 1);
            if (type) {
                index += 1;
            } else {
                index -= 1;
            }
            _m_data.splice(index, 0, _data[0]);
            return mDataSet(_m_data);
        }
        // 数据移除
        mDataRemove = function(index){
            _m_data = mDataGet();
            _m_data.splice(index, 1);     // 删除数据
            return mDataSet(_m_data);
        }
        // 替换数据
        mDataReplace = function(index, data){
            _m_data = mDataGet();
            _m_data.splice(index, 1, data);
            return mDataSet(_m_data);
        }
        // 获取数据
        mDataGet = function(){
            _m_body = $('input[name="m_body"]').val();
            if (_m_body == '' || _m_body == 'false') {
                var _m_data = new Array;
            } else {
                eval('var _m_data = ' + _m_body);
            }
            return _m_data;
        }
        // 设置数据
        mDataSet = function(data){
            var _i_c = 0;
            var _i_c_m = 20;
            var _t_c = 0;
            var _t_c_m = 5000;
            var _sign = true;
            $.each(data, function(i, n){
                if (n.type == 'image') {
                    _i_c += 1;
                    if (_i_c > _i_c_m) {
                        alert('只能选择'+_i_c_m+'张图片');
                        _sign = false;
                        return false;
                    }
                } else if (n.type == 'text') {
                    _t_c += n.value.length;
                    if (_t_c > _t_c_m) {
                        alert('只能输入'+_t_c_m+'个字符');
                        _sign = false;
                        return false;
                    }
                }
            });
            if (!_sign) {
                return false;
            }
            $('span[nctype="img_count_tip"]').html('还可以选择图片<em>' + (_i_c_m - _i_c) + '</em>张');
            $('span[nctype="txt_count_tip"]').html('还可以输入<em>' + (_t_c_m - _t_c) + '</em>字');
            _data = JSON.stringify(data);
            $('input[name="m_body"]').val(_data);
            return true;
        }
        // 转码
        toTxt = function(str) {
            var RexStr = /\<|\>|\"|\'|\&|\\/g
            str = str.replace(RexStr, function(MatchStr) {
                switch (MatchStr) {
                case "<":
                    return "";
                    break;
                case ">":
                    return "";
                    break;
                case "\"":
                    return "";
                    break;
                case "'":
                    return "";
                    break;
                case "&":
                    return "";
                    break;
                case "\\":
                    return "";
                    break;
                default:
                    break;
                }
            })
            return str;
        }
    });
</script>
</body>
</html>
