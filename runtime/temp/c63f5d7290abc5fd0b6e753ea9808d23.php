<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:42:"./application/seller/new/import/index.html";i:1587634376;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/head.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/left.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/foot.html";i:1587634378;}*/ ?>
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
<div class="ncsc-layout wrapper">  <div id="layoutLeft" class="ncsc-layout-left">
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
        <div class="ncsc-path"><i class="icon-desktop"></i>商家管理中心<i class="icon-angle-right"></i>商品<i class="icon-angle-right"></i>淘宝CSV导入</div>
<div class="main-content" id="mainContent">


<script type="text/javascript">
function pre_submit()
{
	var sels=$("#gcategory").find("select");
	var i=0;
	var txt="";
	 sels.each(function(){
		 i++;
		 $(this).attr("name","cls_"+i);
		 var tmp=$(this).find("option:selected").text();
		 if(i!=3)tmp+="&gt;";
		 txt+=tmp;
		
	 });
	 $("#cate_name").val(txt);
	 return true;
}
</script>
<!-- S setp -->
<ul class="add-goods-step">
  <li class="current"><i class="icon icon-list-alt"></i>
    <h6>STIP.1</h6>
    <h2>导入CSV</h2>
    <!--i class="arrow icon-angle-right"></i--> </li>
  <!--li class=""><i class="icon icon-camera-retro "></i>
    <h6>STIP.2</h6>
    <h2>上传图片</h2></li>
      <li class=""><i class="icon icon-ok-circle "></i>
    <h6>STIP.3</h6>
    <h2>完成导入</h2></li-->
</ul>
<!--S 分类选择区域-->
<!--S 分类选择区域-->
<div class="alert mt15 mb5"><strong><?php echo $tpshop_config['shop_info_store_name']; ?></strong> 操作提示：
  <ul>
<li>
1.请将解压后的csv文件和图片文件夹里的图片文件同时上传，多图可使用Ctrl键勾选上传<br>    
2.如果修改CSV文件请务必使用微软excel软件，修改时请勿修改其表格结构、行列名称，修改完成后务必不要修改文件编码格式及文件后缀名<br>
3.请务必不要修改图片文件名称，否则将造成图片丢失，商品相册多图排序可在导入成功后点击商品编辑操作<br>
4.上传时请注意商品分类及店铺分类为必选项. 如果导入后报错看<a href="http://help.tp-shop.cn/Index/Help/info/cat_id/5/id/663.html" style="color:red" target="_blank" >文档</a><br>
</li>
  </ul>
</div>
<form method="post" action="<?php echo U('Seller/import/add_data'); ?>" enctype="multipart/form-data" id="goods_form" onsubmit="return check();">
  <div class="ncsc-form-goods">

    <dl>
      <dt><i class="required">*</i>CSV文件：</dt>
      <dd>
        <div class="handle">
        <div class="ncsc-upload-btn"> 
          <a href="javascript:void(0);"><span><input type="file" hidefocus="true" size="15" name="csv"></span></a>
        </div>
        </div>
      </dd>
    </dl>

    <dl>
      <dt><i class="required">*</i>图片文件：</dt>
      <dd>
        <div class="handle">
        <div class="ncsc-upload-btn"> 
          <a href="javascript:void(0);"><span><input type="file" hidefocus="false" size="150" multiple='multiple' name="images[]"></span></a>
        </div>
        </div>
          <p class="hint">选择商品图片和商品详情的图片一起上传</p>
      </dd>
    </dl>

    <dl>
      <dt><i class="required">*</i>商品分类：</dt>
      <dd id="gcategory"> 
      <span nctype="gc1">
        <select name="goods_class_id1" id="goods_cat_id1" onchange="get_category(this.value,'goods_cat_id2','1');">
            <option value="0">请选择分类</option>
            <?php if(is_array($goodsCategoryLevelOne) || $goodsCategoryLevelOne instanceof \think\Collection || $goodsCategoryLevelOne instanceof \think\Paginator): $i = 0; $__LIST__ = $goodsCategoryLevelOne;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                <option value="<?php echo $v['id']; ?>">
                <?php echo $v['name']; ?>
                </option>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </select>
      </span> 

      <span nctype="gc2">
        <select name="goods_class_id2" id="goods_cat_id2" onchange="get_category(this.value,'goods_cat_id3','2');">
            <option value="0">请选择分类</option>
        </select>
      </span> 

      <span nctype="gc3">
        <select name="goods_class_id3" id="goods_cat_id3">
            <option value="0">请选择分类</option>
        </select>
      </span>
      <p class="hint">请选择商品分类（必须选到最后一级）</p>
      <!--input type="hidden" id="gc_id" name="gc_id" value="" class="mls_id">
      <input type="hidden" id="cate_name" name="cate_name" value="" class="mls_names"-->
      </dd>
    </dl>
    
    <!--transport info begin-->
    <dl>
      <dt><i class="required">*</i>本店分类：</dt>
      <dd><span class="new_add"><a href="javascript:void(0)" id="add_sgcategory" class="ncsc-btn">新增分类</a> </span>
              <select name="store_cat_id1" id="store_cat_id1" onchange="get_store_category(this.value,'store_cat_id2','0');">
                  <option value="0">请选择分类</option>
                  <?php if(is_array($store_goods_class_list) || $store_goods_class_list instanceof \think\Collection || $store_goods_class_list instanceof \think\Paginator): if( count($store_goods_class_list)==0 ) : echo "" ;else: foreach($store_goods_class_list as $k=>$v): ?>
                      <option value="<?php echo $v['cat_id']; ?>">
                      <?php echo $v['cat_name']; ?>
                      </option>
                  <?php endforeach; endif; else: echo "" ;endif; ?>
              </select>
              <select name="store_cat_id2" id="store_cat_id2">
                  <option value="0">请选择分类</option>
              </select>
          <p class="hint">商品可以从属于店铺的多个分类之下，店铺分类可以由 "商家中心 -&gt; 店铺 -&gt; 店铺分类" 中自定义</p>
      </dd>
    </dl>

    <!--dl>
      <dt>字符编码：</dt>
      <dd>
        <p>unicode</p>
      </dd>
    </dl-->

    <!--dl>
      <dt>文件格式：</dt>
      <dd>
        <p>csv文件</p>
      </dd>
    </dl-->

    <dl>
      <dt>&nbsp;</dt>
      <dd>
        <input type="submit" class="submit" value="导入">
      </dd>
    </dl>
  </div>
</form>

<script>
$(function() {
  function sgcInit(){
  	var sgcate	= $("select[name='stc_id[]']").clone();
  	$("select[name='stc_id[]']").remove();
  	sgcate.clone().appendTo('#div_sgcate');
  	$("#add_sgcategory").click(function(){
  		sgcate.clone().appendTo('#div_sgcate');
  	});
  }
		sgcInit();
	
    // 查询下级分类，分类不存在显示当前分类绑定的规格
    $('select[nctype="gc"]').change(function(){
        $(this).parents('td:first').nextAll().html('');
        getClassSpec($(this));
    });
});



function get_category(id,next,select_id){
    var url = '/index.php?m=Seller&c=import&a=return_goods_category';
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
          //console.log(v);return false;
            v = "<option value='0'>请选择商品分类</option>" + v;
            $('#'+next).empty().html(v);
            if(select_id==1){
              $('#goods_cat_id3').empty().html("<option value='0'>请选择商品分类</option>");
            }
            //(select_id > 0) && $('#'+next).val(select_id);//默认选中
        }
    });
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
            //(select_id > 0) && $('#'+next).val(select_id);//默认选中
        }
    });
}

function check(){
  if($('#goods_cat_id3').val()==0){
    layer.msg('商品分类必须选到最后一级', {icon: 2, time: 1000});
    return false;
  }
  if($('#store_cat_id2').val()==0){
    layer.msg('店铺分类必须选到最后一级', {icon: 2, time: 1000});
    return false;
  }
  return true;
}    
// ajax选择商品分类
/*function getClassSpec($this) {
    var id = parseInt($this.val());
    var data_str = ''; eval('data_str =' + $this.attr('data-param'));
    var deep = data_str.deep;
    if (isNaN(id)) {
        // 清理分类
        clearClassHtml(parseInt(deep)+1);
    }
	document.getElementById('gc_id').value=id;
    $.getJSON('index.php?act=store_spec&op=ajax_class&id=' + id + '&deep=' + deep, function(data){
  
        if (data) {
            if (data.type == 'class') {
                nextClass(data.data, data.deep);
            } 
        }
    });
	
}*/

// 下一级商品分类
/*function nextClass(data, deep) {
    $('span[nctype="gc' + deep + '"]').html('').append('<select data-param="{deep:' + deep + '}"></select>')
        .find('select').change(function(){
            getClassSpec($(this));
        }).append('<option>请选择...</option>');
    $.each(data, function(i, n){
        if (n != null) {
            $('span[nctype="gc' + deep + '"] > select').append('<option value="' + n.gc_id + '">' + n.gc_name + '</option>');
		
        }
			  document.getElementById('gc_id').value=n.gc_id;
    });
    // 清理分类
    clearClassHtml(parseInt(deep)+1);
	
}*/

// 清理二级分类信息
/*function clearClassHtml(deep) {
    switch (deep) {

        case 2:
            $('span[nctype="gc2"]').empty();
        case 3:
            $('span[nctype="gc3"]').empty();
            break;
    }
}*/

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
</body></html>