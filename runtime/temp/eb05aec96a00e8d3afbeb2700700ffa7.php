<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:55:"./application/seller/new/promotion/flash_sale_info.html";i:1587634376;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/head.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/left.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/foot.html";i:1587634378;}*/ ?>
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
        <div class="ncsc-path"><i class="icon-desktop"></i>商家管理中心<i class="icon-angle-right"></i>促销<i class="icon-angle-right"></i>抢购管理</div>
        <div class="main-content" id="mainContent">
            <div class="tabmenu">
                <ul class="tab pngFix">
                    <li class="normal"><a href="<?php echo U('Promotion/flash_sale'); ?>">抢购列表</a></li>
                    <li class="active"><a href="<?php echo U('Promotion/flash_sale_info'); ?>">新增/编辑抢购</a></li>
                </ul>
            </div>
            <div class="ncsc-form-default">
                <form id="handleposition" method="post" onsubmit="return false;">
                    <input type="hidden" id="goods_id" name="goods_id" value="<?php echo $info['goods_id']; ?>">
                    <input type="hidden" id="goods_name" name="goods_name" value="<?php echo $info['goods_name']; ?>">
                    <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
                    <input type="hidden" name="item_id" value="<?php echo $info['item_id']; ?>">
                    <input type="hidden" name="is_end" disabled value="<?php echo $info['is_end']; ?>">
                    <!--解决商家被拒后不能再次提交申请，或者申请通过后商家随意更改抢购商品-->
                    <input type="hidden" name="status" value="0">
                    <dl>
                        <dt><i class="required">*</i>抢购标题：</dt>
                        <dd>
                            <input class="w400 text" type="text" name="title" id="title" value="<?php echo $info['title']; ?>" maxlength="30"/>
                            <span class="err" id="err_title"></span>
                            <p class="hint">请填写抢购标题</p>
                        </dd>
                    </dl>
                    <dl>
                        <dt><i class="required">*</i>设置抢购商品：</dt>
                        <dd>
                            <div style="overflow: hidden" id="selected_group_goods">
                                <?php if($info['goods_id'] > 0): ?>
                                    <div style="float: left;margin-right: 10px" class="selected-group-goods">
                                        <div class="goods-thumb"><img style="width: 162px;height: 162px"  <?php if(!(empty($info['specGoodsPrice']) || (($info['specGoodsPrice'] instanceof \think\Collection || $info['specGoodsPrice'] instanceof \think\Paginator ) && $info['specGoodsPrice']->isEmpty()))): ?>src="<?php echo $info['specGoodsPrice']['spec_img']; ?>"<?php else: ?>src="<?php echo goods_thum_images($info['goods_id'],162,162); ?>"<?php endif; ?>/></div>
                                        <div class="goods-name">
                                            <a target="_blank" href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$info['goods_id'])); ?>"><?php echo $info['goods_name']; ?></a>
                                        </div>
                                        <div class="goods-price">
                                            <?php if(!(empty($info['specGoodsPrice']) || (($info['specGoodsPrice'] instanceof \think\Collection || $info['specGoodsPrice'] instanceof \think\Paginator ) && $info['specGoodsPrice']->isEmpty()))): ?>
                                                商城价：￥<?php echo $info['specGoodsPrice']['price']; ?>库存:<?php echo $info['specGoodsPrice']['store_count']; else: ?>
                                                商城价：￥<?php echo $info['goods']['shop_price']; ?>库存:<?php echo $info['goods']['store_count']; endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <a href="javascript:void(0);" id="select_goods_button" data-goods-id="<?php echo $info['goods_id']; ?>"  data-prom_type="<?php echo $info['prom_type']; ?>" data-prom_type="<?php echo $info['prom_id']; ?>" class="ncbtn ncbtn-aqua">选择商品</a>
                            <span class="err" id="err_goods_id"></span>
                            <p class="hint">设置抢购商品</p>
                         </dd>
                     </dl>
                    <dl>
                        <dt><i class="required">*</i>限时抢购价格：</dt>
                        <dd>
                            <input class="w70 text" id="price" name="price" value="<?php echo $info['price']; ?>"  onpaste="this.value=this.value.replace(/[^\d.]/g,'')" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" type="text"/><em class="add-on"><i class="icon-renminbi"></i></em>
                            <span class="err" id="err_price"></span>
                            <p class="hint">商品抢购价格为该商品参加活动时的抢购价格<br/>必须是0.01~1000000之间的数字(单位：元)</p>
                        </dd>
                    </dl>
                    <dl>
                        <dt>参加抢购数量：</dt>
                        <dd>
                            <input class="w70 text" id="goods_num" name="goods_num" value="<?php echo $info['goods_num']; ?>"  onpaste="this.value=this.value.replace(/[^\d]/g,'')" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" type="text"/>
                            <span class="err" id="err_goods_num"></span>
                            <p class="hint">请填写自然数，参与抢购商品的数量</p>
                        </dd>
                    </dl>
                    <dl>
                        <dt>限购数量：</dt>
                        <dd>
                            <input class="w70 text" id="buy_limit" name="buy_limit" value="<?php echo $info['buy_limit']; ?>"  onpaste="this.value=this.value.replace(/[^\d]/g,'')" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" type="text"/>
                            <span class="err" id="err_buy_limit"></span>
                            <p class="hint">抢购限购数量!</p>
                        </dd>
                    </dl>
                    <!--<dl>
                        <dt><i class="required">*</i>开始时间：</dt>
                        <dd>
                            <input  id="start_time" name="start_time" value="<?php echo $info['start_time']; ?>" type="text" class="text w130"/><em class="add-on"><i class="icon-calendar"></i></em><span></span>
                            <span class="err" id="err_start_time"></span>
                            <p class="hint">抢购开始时间</p>
                            <p class="hint" style="color:red"><strong>重要:抢购时间是从凌晨0:00:00秒开始, 每隔2小时一个节点, 所以活动时间必须是两小时之内才有效, (例如今天是2017年1月1日下午2点场,那活动开始和结束时间必须是:2017-01-01 14:00:01 ~ 2017-01-01 15:59:59 时间范围内), 否则前端无法显示活动信息</strong></p>
                        </dd>
                    </dl>
                    <dl>
                        <dt><i class="required">*</i>结束时间：</dt>
                        <dd>
                            <input id="end_time" name="end_time" value="<?php echo $info['end_time']; ?>" type="text" class="text w130"/><em class="add-on"><i class="icon-calendar"></i></em><span></span>
                            <span class="err" id="err_end_time"></span>
                            <p class="hint">抢购结束时间</p>
                        </dd>
                    </dl>-->
                    <dl class="row">
                        <dt class="tit">
                            <label><em>*</em>开始时间</label>
                        </dt>
                        <dd class="opt">
                            <input type="text" id="start_time" name="start_time" value="<?php echo date('Y-m-d', $info['start_time']); ?>"  class="input-txt">
                            <select name="start_time_h" id="start_time_h">
                                <?php $__FOR_START_1877731715__=0;$__FOR_END_1877731715__=24;for($i=$__FOR_START_1877731715__;$i < $__FOR_END_1877731715__;$i+=2){ ?>
                                    <option value="<?php echo $i; ?>" <?php if($info['start_time_h'] == $i): ?>selected<?php endif; ?>><?php echo $i; ?>:00:00</option>
                                <?php } ?>
                            </select>
                            <span class="err" id="err_start_time"></span>
                            <p class="notic">抢购开始时间</p>
                            <p class="hint" style="color:red"><strong>重要:抢购时间是从凌晨0:00:00秒开始, 每隔2小时一个节点, 所以活动时间必须是两小时之内才有效, (例如今天是2017年1月1日下午2点场,<br/>那活动开始和结束时间必须是:2017-01-01 14:00:01 ~ 2017-01-01 15:59:59 时间范围内), 否则前端无法显示活动信息</strong></p>
                        </dd>
                    </dl>
                    <dl>
                        <dt>抢购介绍：</dt>
                        <dd>
                            <textarea placeholder="请输入抢购介绍" name="description" rows="6" class="tarea w400"><?php echo $info['description']; ?></textarea>
                            <span class="err" id="err_description"></span>
                            <p class="hint">抢购介绍</p>
                        </dd>
                    </dl>
                    <div class="bottom"><label class="submit-border">
                        <input id="submit" type="submit" class="submit" value="提交"></label>
                        <span class="err" id="err_submit"></span>
                    </div>
                </form>
        </div>
        <script type="text/javascript">
            $(document).ready(function(){
                var is_end = $("input[name='is_end']").val();
                if(is_end == 1){
                    $('#submit').attr('disabled',true);
                    $('#err_submit').html('该活动已结束不能编辑').show();
                }
            });
            $(function () {
                $(document).on("click", '#submit', function (e) {
                    $('#submit').attr('disabled',true);
                    verifyForm();
                })
            })
            function verifyForm(){
                $('span.err').hide();
                $.ajax({
                    type: "POST",
                    url: "<?php echo U('Seller/Promotion/flash_sale_info'); ?>",
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
                                location.href = "<?php echo U('Seller/Promotion/flash_sale'); ?>";
                            });
                        } else {
                            $('#submit').attr('disabled',false);
                            var ss='';
                            $.each(data.result, function (index, item) {
                                ss += item+',</br>';
                            });
                            layer.msg(ss, {icon: 2,time: 3000});
                        }
                    }
                });
            }
            //选择商品按钮点击事件
            $(function () {
                $(document).on("click", '#select_goods_button', function (e) {
                    var prom_id = $(this).attr('data-prom_id')
                    var prom_type = $(this).attr('data-prom_type')
//                    var goods_id = $(this).attr('data-goods-id');
                    var url = "/index.php?m=Seller&c=Promotion&a=search_goods&tpl=select_goods&prom_id="+prom_id+"&prom_type="+prom_type;
                    layer.open({
                        type: 2,
                        title: '选择商品',
                        shadeClose: true,
                        shade: 0.2,
                        area: ['1020px', '75%'],
                        content: url
                    });
                })
            })
            function call_back(goodsItem){
                $('#goods_id').val(goodsItem.goods_id);
                var html = '';
                if(goodsItem.spec != null){
                    //有规格
                    html = '<div style="float: left;margin-right: 10px" class="selected-group-goods"><div class="goods-thumb">' +
                            '<img style="width: 162px;height: 162px" src="'+goodsItem.spec.spec_img+'"/></div> <div class="goods-name"> ' +
                            '<a target="_blank" href="/index.php?m=Home&c=Goods&a=goodsInfo&id='+goodsItem.goods_id+'">'+goodsItem.goods_name+goodsItem.spec.key_name+'</a> </div>' +
                            ' <div class="goods-price">商城价：￥'+goodsItem.spec.price+'库存:'+goodsItem.spec.store_count+'</div> </div>';
                    $('input[name=item_id]').val(goodsItem.spec.item_id)
                    $('input[name=goods_name]').val(goodsItem.goods_name + goodsItem.spec.key_name);
                }else{
                    html = '<div style="float: left;margin-right: 10px" class="selected-group-goods"><div class="goods-thumb">' +
                            '<img style="width: 162px;height: 162px" src="'+goodsItem.goods_image+'"/></div> <div class="goods-name"> ' +
                            '<a target="_blank" href="/index.php?m=Home&c=Goods&a=goodsInfo&id='+goodsItem.goods_id+'">'+goodsItem.goods_name+'</a> </div>' +
                            ' <div class="goods-price">商城价：￥'+goodsItem.goods_price+'库存:'+goodsItem.store_count+'</div> </div>';
                    $('input[name=goods_name]').val(goodsItem.goods_name);
                }
                $('#select_goods_button').attr('data-goods-id',goodsItem.goods_id);
                $('#selected_group_goods').empty().html(html);
                $('.selected-group-goods').show();
                layer.closeAll('iframe');
            }
//            $('#start_time').layDate();
            laydate({
                elem: '#start_time',
                format: 'YYYY-MM-DD ', // 分隔符可以任意定义，该例子表示只显示年月
                //istime: true, //是否开启时间选择
                isclear: true, //是否显示清空
                istoday: true, //是否显示今天
                issure: true, //是否显示确认
                //festival: true, //显示节日
                min: '1970-01-01 00:00:00', //最小日期
                max: '2099-12-31 23:59:59', //最大日期
                //start: laydate.now(0),//开始日期
                fixed: false, //是否固定在可视区域
                zIndex: 99999999, //css z-index
                choose: function(dates){ //选择好日期的回调
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
