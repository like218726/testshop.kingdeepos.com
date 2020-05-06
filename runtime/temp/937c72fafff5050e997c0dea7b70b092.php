<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:42:"./template/mobile/default/index/index.html";i:1587634412;s:80:"/home/wwwroot/testshop.kingdeepos.com/template/mobile/default/public/header.html";i:1587634412;s:80:"/home/wwwroot/testshop.kingdeepos.com/template/mobile/default/public/footer.html";i:1587634412;s:84:"/home/wwwroot/testshop.kingdeepos.com/template/mobile/default/public/footer_nav.html";i:1587634412;s:82:"/home/wwwroot/testshop.kingdeepos.com/template/mobile/default/public/wx_share.html";i:1587634412;}*/ ?>
<link rel="stylesheet" href="/template/mobile/default/static/css/home.css">
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>首页--<?php echo $tpshop_config['shop_info_store_title']; ?>--<?php echo $seo['title']; ?></title>
    <meta name="keywords" content="<?php echo $seo['keywords']; ?>"/>
    <meta name="description" content="<?php echo $seo['description']; ?>"/>
    <link rel="stylesheet" href="/template/mobile/default/static/css/style.css">
    <link rel="stylesheet" type="text/css" href="/template/mobile/default/static/css/iconfont.css"/>
    <link rel="stylesheet" type="text/css" href="/template/mobile/default/static/css/all_page.css"/>
    <script src="/template/mobile/default/static/js/jquery-3.1.1.min.js" type="text/javascript" charset="utf-8"></script>
    <link href="/public/static/js/video.7.3.0/video-js.min.css" rel="stylesheet">
    <script src="/public/static/js/video.7.3.0/video.min.js"></script>
    <script src="/template/mobile/default/static/js/mobile-util.js" type="text/javascript" charset="utf-8"></script>
    <script src="/public/js/global.js"></script>
    <script src="/template/mobile/default/static/js/layer/layer.js" type="text/javascript" charset="utf-8"></script>
    <script src="/template/mobile/default/static/js/swipeSlide.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="/public/js/mobile_common.js"></script>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo (isset($tpshop_config['shop_info_store_ico']) && ($tpshop_config['shop_info_store_ico'] !== '')?$tpshop_config['shop_info_store_ico']:'/public/static/images/logo/storeico_default.png'); ?>" media="screen"/>
</head>
<body class="[body]">

    <!--顶部搜索栏-s-->
    <header>
        <div class="content">
            <div class="ds-in-bl search">
                <div class="sea-box">
                    <span ></span>
                    <form action=""  method="post">
                        <div class="sear-input">
                            <a href="<?php echo U('Goods/ajaxSearch'); ?>">
                                <input type="text" name="q" id="search_text" class="search_text"   value="" placeholder="请输入您所搜索的商品">
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </header>
    <!--顶部搜索栏-e-->

    <!--顶部滚动广告栏-s-->
    <div class="banner banner_auto">
        <div class="banner_bg"></div>
        <div class="mslide" id="slideTpshop">
            <ul>
                <!--广告表-->
                <?php $pid =2;$ad_position = M("ad_position")->cache(true,TPSHOP_CACHE_TIME)->column("position_id,position_name,ad_width,ad_height","position_id");$result = M("ad")->where("pid=$pid  and enabled = 1 and start_time <= 1588693992 and end_time >= 1588693992 ")->order("orderby desc")->cache(true,TPSHOP_CACHE_TIME)->limit("5")->select();
if(is_array($ad_position) && !in_array($pid,array_keys($ad_position)) && $pid)
{
  M("ad_position")->insert(array(
         "position_id"=>$pid,
         "position_name"=>CONTROLLER_NAME."页面自动增加广告位 $pid ",
         "is_open"=>1,
         "position_desc"=>CONTROLLER_NAME."页面",
  ));
  delFile(RUNTIME_PATH); // 删除缓存
  \think\Cache::clear();  
}


$c = 5- count($result); //  如果要求数量 和实际数量不一样 并且编辑模式
if($c > 0 && I("get.edit_ad"))
{
    for($i = 0; $i < $c; $i++) // 还没有添加广告的时候
    {
      $result[] = array(
          "ad_code" => "/public/images/not_adv.jpg",
          "ad_link" => "/index.php?m=Admin&c=Ad&a=ad&pid=$pid",
          "title"   =>"暂无广告图片",
          "not_adv" => 1,
          "target" => 0,
      );  
    }
}
foreach($result as $key=>$v):       
    
    $v[position] = $ad_position[$v[pid]]; 
    if(I("get.edit_ad") && $v[not_adv] == 0 )
    {
        $v[style] = "filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity: 0.5"; // 广告半透明的样式
        $v[ad_link] = "/index.php?m=Admin&c=Ad&a=ad&act=edit&ad_id=$v[ad_id]";        
        $v[title] = $ad_position[$v[pid]][position_name]."===".$v[ad_name];
        $v[target] = 0;
    }
    ?>
                    <li><a href="<?php echo $v['ad_link']; ?><?php echo !empty($edit_ad)?'&suggestion=710*340' : ''; ?>">
                        <img src="<?php echo $v[ad_code]; ?>" title="<?php echo $v[title]; ?>" style="<?php echo $v[style]; ?>" alt="">
                    </a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <!--顶部滚动广告栏-e-->

    <!--菜单-start-->
    <div class="floor">
        <nav>
            <a href="<?php echo U('Index/street'); ?>">
                <span>
                    <img src="/template/mobile/default/static/images/icon_05.png" alt="店铺街" />
                    <span>店铺街</span>
                </span>
            </a>
            <a href="<?php echo U('Index/brand'); ?>">
                <span>
                    <img src="/template/mobile/default/static/images/icon_07.png" alt="品牌街" />
                    <span>品牌街</span>
                </span>
            </a>
            <a href="<?php echo U('Activity/promote_goods'); ?>">
                <span>
                    <img src="/template/mobile/default/static/images/icon_09.png" alt="优惠活动" />
                    <span>优惠活动</span>
                </span>
            </a>
            <a href="<?php echo U('Activity/group_list'); ?>">
                <span>
                    <img src="/template/mobile/default/static/images/icon_15.png" alt="团购" />
                    <span>团购</span>
                </span>
            </a>
            <a href="<?php echo U('Activity/coupon_list'); ?>">
                <span>
                    <img src="/template/mobile/default/static/images/icon_16.png" alt="领券中心" />
                    <span>领券中心</span>
                </span>
            </a>
            <a href="<?php echo U('Goods/integralMall'); ?>">
                <span>
                    <img src="/template/mobile/default/static/images/icon_17.png" alt="积分商城" />
                    <span>积分商城</span>
                </span>
            </a>
			
            <a href="<?php echo U('Team/index'); ?>">
                <span>
                    <img src="/template/mobile/default/static/images/icon_19.png" alt="拼团" />
                    <span>拼团</span>
                </span>
            </a>
            <a href="<?php echo U('Goods/categoryList'); ?>">
                <span>
                    <img src="/template/mobile/default/static/images/icon_03.png" alt="全部分类" />
                    <span>全部分类</span>
                </span>
            </a>
        </nav>
    </div>
    <!--菜单-end-->
<!--秒杀-start-->
<?php if($flash_sale_list): ?>
    <div class="secondkill">
        <div class="content_1">
            <div class="time p">
                <div class="djs lightning fl">
                    <span class="add fl"><img src="/template/mobile/default/static/images/mszc.png" alt=""></span>
                    <div class="fl fl_seckill"><span class="red fl" id=""><?php echo date('H',$start_time); ?>点场</span><span class="hms fl"></span></div>
                </div>
                <div class="xsxl fr">
                    <a href="<?php echo U('Mobile/Activity/flash_sale_list'); ?>">
                        <span>限时抢购</span>
                    </a>
                </div>
            </div>
            <div class="shop_p clearfix">
                <?php if(is_array($flash_sale_list) || $flash_sale_list instanceof \think\Collection || $flash_sale_list instanceof \think\Paginator): if( count($flash_sale_list)==0 ) : echo "" ;else: foreach($flash_sale_list as $key=>$v): ?>
                    <a class="mian_width_3" href="<?php echo U('Mobile/Activity/flash_sale_list'); ?>">
                        <div class="timerBox shopnum">
                            <img src="<?php echo goods_thum_images($v[goods_id],200,200); ?>"/>
                            <p>￥<span><?php echo $v[price]; ?></span>元</p>
                        </div>
                    </a>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
    </div>
<?php endif; if(!$flash_sale_list): ?>
    <div class="flash_none content_1 mian_width_3">
        <div class="qiag_1"><img src="/template/mobile/default/static/images/mszc.png" alt=""></div>
        <div class="xsxl fr">
            <img src="/template/mobile/default/static/images/icon_flash_sale.png" alt="">
            <a href="<?php echo U('Mobile/Activity/flash_sale_list'); ?>">
                <span>限时抢购<img src="/template/mobile/default/static/images/z-package-left.png" alt="" /></span>
            </a>
        </div>
    </div>
<?php endif; ?>
<!--秒杀-end-->
<style>
    .advertisement {
        margin-top: 0.42677rem;
    }
</style>
    <!--广告位-start-->
    <div class="advertisement">
        <div class="content_2">
            <div class="le lefhe fl">
                <?php $pid =301;$ad_position = M("ad_position")->cache(true,TPSHOP_CACHE_TIME)->column("position_id,position_name,ad_width,ad_height","position_id");$result = M("ad")->where("pid=$pid  and enabled = 1 and start_time <= 1588693992 and end_time >= 1588693992 ")->order("orderby desc")->cache(true,TPSHOP_CACHE_TIME)->limit("1")->select();
if(is_array($ad_position) && !in_array($pid,array_keys($ad_position)) && $pid)
{
  M("ad_position")->insert(array(
         "position_id"=>$pid,
         "position_name"=>CONTROLLER_NAME."页面自动增加广告位 $pid ",
         "is_open"=>1,
         "position_desc"=>CONTROLLER_NAME."页面",
  ));
  delFile(RUNTIME_PATH); // 删除缓存
  \think\Cache::clear();  
}


$c = 1- count($result); //  如果要求数量 和实际数量不一样 并且编辑模式
if($c > 0 && I("get.edit_ad"))
{
    for($i = 0; $i < $c; $i++) // 还没有添加广告的时候
    {
      $result[] = array(
          "ad_code" => "/public/images/not_adv.jpg",
          "ad_link" => "/index.php?m=Admin&c=Ad&a=ad&pid=$pid",
          "title"   =>"暂无广告图片",
          "not_adv" => 1,
          "target" => 0,
      );  
    }
}
foreach($result as $key=>$v):       
    
    $v[position] = $ad_position[$v[pid]]; 
    if(I("get.edit_ad") && $v[not_adv] == 0 )
    {
        $v[style] = "filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity: 0.5"; // 广告半透明的样式
        $v[ad_link] = "/index.php?m=Admin&c=Ad&a=ad&act=edit&ad_id=$v[ad_id]";        
        $v[title] = $ad_position[$v[pid]][position_name]."===".$v[ad_name];
        $v[target] = 0;
    }
    ?>
                    <a href="<?php echo $v['ad_link']; ?><?php echo !empty($edit_ad)?'&suggestion=350*150' : ''; ?>">
                        <div class="td" style="margin-bottom: 0.213rem">
                            <img src="<?php echo $v[ad_code]; ?>" title="<?php echo $v[title]; ?>" style="<?php echo $v[style]; ?>">
                        </div>
                    </a>
                <?php endforeach; $pid =302;$ad_position = M("ad_position")->cache(true,TPSHOP_CACHE_TIME)->column("position_id,position_name,ad_width,ad_height","position_id");$result = M("ad")->where("pid=$pid  and enabled = 1 and start_time <= 1588693992 and end_time >= 1588693992 ")->order("orderby desc")->cache(true,TPSHOP_CACHE_TIME)->limit("1")->select();
if(is_array($ad_position) && !in_array($pid,array_keys($ad_position)) && $pid)
{
  M("ad_position")->insert(array(
         "position_id"=>$pid,
         "position_name"=>CONTROLLER_NAME."页面自动增加广告位 $pid ",
         "is_open"=>1,
         "position_desc"=>CONTROLLER_NAME."页面",
  ));
  delFile(RUNTIME_PATH); // 删除缓存
  \think\Cache::clear();  
}


$c = 1- count($result); //  如果要求数量 和实际数量不一样 并且编辑模式
if($c > 0 && I("get.edit_ad"))
{
    for($i = 0; $i < $c; $i++) // 还没有添加广告的时候
    {
      $result[] = array(
          "ad_code" => "/public/images/not_adv.jpg",
          "ad_link" => "/index.php?m=Admin&c=Ad&a=ad&pid=$pid",
          "title"   =>"暂无广告图片",
          "not_adv" => 1,
          "target" => 0,
      );  
    }
}
foreach($result as $key=>$v):       
    
    $v[position] = $ad_position[$v[pid]]; 
    if(I("get.edit_ad") && $v[not_adv] == 0 )
    {
        $v[style] = "filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity: 0.5"; // 广告半透明的样式
        $v[ad_link] = "/index.php?m=Admin&c=Ad&a=ad&act=edit&ad_id=$v[ad_id]";        
        $v[title] = $ad_position[$v[pid]][position_name]."===".$v[ad_name];
        $v[target] = 0;
    }
    ?>
                    <a href="<?php echo $v['ad_link']; ?><?php echo !empty($edit_ad)?'&suggestion=350*150' : ''; ?>">
                        <div class="td">
                            <img src="<?php echo $v[ad_code]; ?>" title="<?php echo $v[title]; ?>" style="<?php echo $v[style]; ?>">
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="le lefhe fr">
                <?php $pid =300;$ad_position = M("ad_position")->cache(true,TPSHOP_CACHE_TIME)->column("position_id,position_name,ad_width,ad_height","position_id");$result = M("ad")->where("pid=$pid  and enabled = 1 and start_time <= 1588693992 and end_time >= 1588693992 ")->order("orderby desc")->cache(true,TPSHOP_CACHE_TIME)->limit("1")->select();
if(is_array($ad_position) && !in_array($pid,array_keys($ad_position)) && $pid)
{
  M("ad_position")->insert(array(
         "position_id"=>$pid,
         "position_name"=>CONTROLLER_NAME."页面自动增加广告位 $pid ",
         "is_open"=>1,
         "position_desc"=>CONTROLLER_NAME."页面",
  ));
  delFile(RUNTIME_PATH); // 删除缓存
  \think\Cache::clear();  
}


$c = 1- count($result); //  如果要求数量 和实际数量不一样 并且编辑模式
if($c > 0 && I("get.edit_ad"))
{
    for($i = 0; $i < $c; $i++) // 还没有添加广告的时候
    {
      $result[] = array(
          "ad_code" => "/public/images/not_adv.jpg",
          "ad_link" => "/index.php?m=Admin&c=Ad&a=ad&pid=$pid",
          "title"   =>"暂无广告图片",
          "not_adv" => 1,
          "target" => 0,
      );  
    }
}
foreach($result as $key=>$v):       
    
    $v[position] = $ad_position[$v[pid]]; 
    if(I("get.edit_ad") && $v[not_adv] == 0 )
    {
        $v[style] = "filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity: 0.5"; // 广告半透明的样式
        $v[ad_link] = "/index.php?m=Admin&c=Ad&a=ad&act=edit&ad_id=$v[ad_id]";        
        $v[title] = $ad_position[$v[pid]][position_name]."===".$v[ad_name];
        $v[target] = 0;
    }
    ?>
                    <a href="<?php echo $v['ad_link']; ?><?php echo !empty($edit_ad)?'&suggestion=350*150' : ''; ?>">
                        <div class="td" style="margin-bottom: 0.213rem">
                            <img src="<?php echo $v[ad_code]; ?>" title="<?php echo $v[title]; ?>" style="<?php echo $v[style]; ?>">
                        </div>
                    </a>
                <?php endforeach; $pid =303;$ad_position = M("ad_position")->cache(true,TPSHOP_CACHE_TIME)->column("position_id,position_name,ad_width,ad_height","position_id");$result = M("ad")->where("pid=$pid  and enabled = 1 and start_time <= 1588693992 and end_time >= 1588693992 ")->order("orderby desc")->cache(true,TPSHOP_CACHE_TIME)->limit("1")->select();
if(is_array($ad_position) && !in_array($pid,array_keys($ad_position)) && $pid)
{
  M("ad_position")->insert(array(
         "position_id"=>$pid,
         "position_name"=>CONTROLLER_NAME."页面自动增加广告位 $pid ",
         "is_open"=>1,
         "position_desc"=>CONTROLLER_NAME."页面",
  ));
  delFile(RUNTIME_PATH); // 删除缓存
  \think\Cache::clear();  
}


$c = 1- count($result); //  如果要求数量 和实际数量不一样 并且编辑模式
if($c > 0 && I("get.edit_ad"))
{
    for($i = 0; $i < $c; $i++) // 还没有添加广告的时候
    {
      $result[] = array(
          "ad_code" => "/public/images/not_adv.jpg",
          "ad_link" => "/index.php?m=Admin&c=Ad&a=ad&pid=$pid",
          "title"   =>"暂无广告图片",
          "not_adv" => 1,
          "target" => 0,
      );  
    }
}
foreach($result as $key=>$v):       
    
    $v[position] = $ad_position[$v[pid]]; 
    if(I("get.edit_ad") && $v[not_adv] == 0 )
    {
        $v[style] = "filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity: 0.5"; // 广告半透明的样式
        $v[ad_link] = "/index.php?m=Admin&c=Ad&a=ad&act=edit&ad_id=$v[ad_id]";        
        $v[title] = $ad_position[$v[pid]][position_name]."===".$v[ad_name];
        $v[target] = 0;
    }
    ?>
                    <a href="<?php echo $v['ad_link']; ?><?php echo !empty($edit_ad)?'&suggestion=350*150' : ''; ?>">
                        <div class="td">
                            <img src="<?php echo $v[ad_code]; ?>" title="<?php echo $v[title]; ?>" style="<?php echo $v[style]; ?>">
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="banner mian_width_3 width_an">
        <?php $pid =400;$ad_position = M("ad_position")->cache(true,TPSHOP_CACHE_TIME)->column("position_id,position_name,ad_width,ad_height","position_id");$result = M("ad")->where("pid=$pid  and enabled = 1 and start_time <= 1588693992 and end_time >= 1588693992 ")->order("orderby desc")->cache(true,TPSHOP_CACHE_TIME)->limit("1")->select();
if(is_array($ad_position) && !in_array($pid,array_keys($ad_position)) && $pid)
{
  M("ad_position")->insert(array(
         "position_id"=>$pid,
         "position_name"=>CONTROLLER_NAME."页面自动增加广告位 $pid ",
         "is_open"=>1,
         "position_desc"=>CONTROLLER_NAME."页面",
  ));
  delFile(RUNTIME_PATH); // 删除缓存
  \think\Cache::clear();  
}


$c = 1- count($result); //  如果要求数量 和实际数量不一样 并且编辑模式
if($c > 0 && I("get.edit_ad"))
{
    for($i = 0; $i < $c; $i++) // 还没有添加广告的时候
    {
      $result[] = array(
          "ad_code" => "/public/images/not_adv.jpg",
          "ad_link" => "/index.php?m=Admin&c=Ad&a=ad&pid=$pid",
          "title"   =>"暂无广告图片",
          "not_adv" => 1,
          "target" => 0,
      );  
    }
}
foreach($result as $key=>$v):       
    
    $v[position] = $ad_position[$v[pid]]; 
    if(I("get.edit_ad") && $v[not_adv] == 0 )
    {
        $v[style] = "filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity: 0.5"; // 广告半透明的样式
        $v[ad_link] = "/index.php?m=Admin&c=Ad&a=ad&act=edit&ad_id=$v[ad_id]";        
        $v[title] = $ad_position[$v[pid]][position_name]."===".$v[ad_name];
        $v[target] = 0;
    }
    ?>
            <a href="<?php echo $v['ad_link']; ?><?php echo !empty($edit_ad)?'&suggestion=710*190' : ''; ?>">
                <img src="<?php echo $v[ad_code]; ?>" title="<?php echo $v[title]; ?>" style="<?php echo $v[style]; ?>"/>
            </a>
        <?php endforeach; ?>
    </div>
    <!--广告位-end-->
    <!--热销商品-start-->
    <?php if($hot_goods): ?>
    <div class="plate">
        <img class="plate_img" src="/template/mobile/default/static/images/hotgoods.png" alt="">
        <div class="plate_item">
            <ul>
                <?php if(is_array($hot_goods) || $hot_goods instanceof \think\Collection || $hot_goods instanceof \think\Paginator): $i = 0; $__LIST__ = $hot_goods;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <li>
                        <div class="mian_width_1">
                            <a href="<?php echo U('Mobile/Goods/goodsInfo',array('id'=>$vo['goods_id'])); ?>" title="<?php echo $vo['goods_name']; ?>">
                                <img class="mian_img" src="<?php echo $vo['original_img']; ?>">
                                <div class="mian_h2 mian_hidde"><?php echo $vo['goods_name']; ?></div>
                                <div class="goods_name_cen mian_flex_4">
                                    <span class="big-price"><b>¥</b><?php echo $vo['shop_price']; ?></span>
                                    <span class="has-sold">已售出<?php echo $vo['sales_sum']; ?>件</span>
                                </div>
                            </a>
                        </div>
                    </li>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>
    <!--热销商品-end-->
    <!--特色推荐-start-->
    <?php if($favourite_goods): ?>
    <div class="plate">
        <img class="plate_img" src="/template/mobile/default/static/images/feature.png" alt="">
        <div class="hot_sell">
            <ul>
                <?php if(is_array($favourite_goods) || $favourite_goods instanceof \think\Collection || $favourite_goods instanceof \think\Paginator): $i = 0; $__LIST__ = $favourite_goods;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <li>
                        <a href="<?php echo U('Mobile/Goods/goodsInfo',array('id'=>$vo['goods_id'])); ?>" title="<?php echo $vo['goods_name']; ?>">
                            <div class="hot_img">
                                <img class="hot_img" src="<?php echo $vo['original_img']; ?>">
                            </div>
                            <div class="hot_right">
                                <div class="mian_h2 mian_hidde"><?php echo $vo['goods_name']; ?></div>
                                <div class="goods_name_cen mian_flex_4">
                                    <span class="big-price"><b>¥</b><?php echo $vo['shop_price']; ?></span>
                                    <span class="has-sold">已售出<?php echo $vo['sales_sum']; ?>件</span>
                                </div>
                            </div>
                        </a>
                    </li>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>
    <!--特色推荐-end-->
    <!--拼团列表-start-->
    <?php if($team_goods_items): ?>
    <div class="plate team">
        <img class="plate_img" src="/template/mobile/default/static/images/team.png" alt="">
        <div class="hot_sell">
            <ul>
                <?php if(is_array($team_goods_items) || $team_goods_items instanceof \think\Collection || $team_goods_items instanceof \think\Paginator): $i = 0;$__LIST__ = is_array($team_goods_items) ? array_slice($team_goods_items,0,2, true) : $team_goods_items->slice(0,2, true); if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <li>
                        <!-- /index.php?m=Mobile&c=Team&a=info&team_id=" + data.result[i].team_id + "&goods_id=" + data.result[i].goods_id -->
                        <!-- <a href="<?php echo U('Mobile/Team/info',array('id'=>$vo['goods_id'],'team_id'=>$vo['team_id'])); ?>" title="<?php echo $vo['goods_name']; ?>"> -->
                        <a href="<?php echo U('Mobile/Goods/goodsInfo',array('id'=>$vo['goods_id'])); ?>" title="<?php echo $vo['goods_name']; ?>">
                            <div class="hot_img">
                                <img class="hot_img" src="<?php echo $vo['share_img']; ?>">
                            </div>
                            <div class="hot_right">
                                <div class="mian_h2 mian_hidde"><?php echo $vo['goods_name']; ?> </div>
                                <div class="group_img">
                                    <?php if(is_array($vo['follow_users_head_pic']) || $vo['follow_users_head_pic'] instanceof \think\Collection || $vo['follow_users_head_pic'] instanceof \think\Paginator): $i = 0;$__LIST__ = is_array($vo['follow_users_head_pic']) ? array_slice($vo['follow_users_head_pic'],0,2, true) : $vo['follow_users_head_pic']->slice(0,2, true); if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$head_pic): $mod = ($i % 2 );++$i;?>
                                        <img src="<?php echo $head_pic; ?>" alt="">
                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                </div>
                                <div class="goods_name_cen mian_flex_4">
                                    <span class="big-price"><b>¥</b><?php echo $vo['team_price']; ?></span>
                                    <span class="has-sold">已拼<?php echo $vo['virtual_num']; ?>件</span>
                                </div>
                            </div>
                        </a>
                    </li>
                <?php endforeach; endif; else: echo "" ;endif; ?>


            </ul>
        </div>
        <div class="plate_item">
            <ul>
                <?php if(is_array($team_goods_items) || $team_goods_items instanceof \think\Collection || $team_goods_items instanceof \think\Paginator): $i = 0;$__LIST__ = is_array($team_goods_items) ? array_slice($team_goods_items,2,null, true) : $team_goods_items->slice(2,null, true); if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <li>
                    <div class="mian_width_1">
                        <a href="<?php echo U('Mobile/Goods/goodsInfo',array('id'=>$vo['goods_id'])); ?>" title="<?php echo $vo['goods_name']; ?>">
                            <img class="mian_img" src="<?php echo $vo['share_img']; ?>">
                            <div class="mian_h2 mian_hidde"><?php echo $vo['goods_name']; ?></div>
                            <div class="group_img">
                                <?php if(is_array($vo['follow_users_head_pic']) || $vo['follow_users_head_pic'] instanceof \think\Collection || $vo['follow_users_head_pic'] instanceof \think\Paginator): $i = 0;$__LIST__ = is_array($vo['follow_users_head_pic']) ? array_slice($vo['follow_users_head_pic'],0,2, true) : $vo['follow_users_head_pic']->slice(0,2, true); if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$head_pic): $mod = ($i % 2 );++$i;?>
                                    <img src="<?php echo $head_pic; ?>" alt="">
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </div>
                            <div class="goods_name_cen mian_flex_4">
                                <span class="big-price"><b>¥</b><?php echo $vo['team_price']; ?></span>
                                <span class="has-sold">已拼<?php echo $vo['virtual_num']; ?>件</span>
                            </div>
                        </a>
                    </div>
                </li>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <!--底部-start-->
    <!-- <footer>
    <div class="flool1">
        <ul>
            <?php if(!empty($_COOKIE['user_id'])): ?>
                <li><a href="<?php echo U('Mobile/User/index'); ?>"><?php echo getSubstr(urldecode($_COOKIE['uname']),0,10);?></a></li>
                <li id="logout"><a href="<?php echo U('Mobile/User/logout'); ?>">退出</a></li>
            <?php else: ?>
                <li><a href="<?php echo U('Mobile/User/login'); ?>">登录</a></li>
                <li><a href="<?php echo U('Mobile/User/reg'); ?>">注册</a></li>
            <?php endif; ?>
            <li><a href="tel:<?php echo $tpshop_config['shop_info_phone']; ?>">反馈</a></li>
            <li class="comebackTop">回到顶部</li>
        </ul>
    </div>
    <div class="flool2">
        <ul>
            <li>
                <a href="?">
                    <div class="icon">
                        <img src="/template/mobile/default/static/images/ind_70.png"/>
                        <p>客户端</p>
                    </div>
                </a>
            </li>
            <li>
                <a href="<?php echo U('Mobile/Index/index'); ?>">
                    <div class="icon black">
                        <img src="/template/mobile/default/static/images/ind_72.png"/>
                        <p>触屏版</p>
                    </div>
                </a>
            </li>
            <li>
                <a href="<?php echo U('Home/Index/index'); ?>">
                    <div class="icon">
                        <img src="/template/mobile/default/static/images/ind_74.png"/>
                        <p>电脑版</p>
                    </div>
                </a>
            </li>
        </ul>
    </div>
    <div class="flool3">
        <p>Copyright © 2016-2025 <?php echo (isset($tpshop_config['shop_info_store_name']) && ($tpshop_config['shop_info_store_name'] !== '')?$tpshop_config['shop_info_store_name']:'www.tpshop.cn'); ?>版权所有  备案号:<?php echo $tpshop_config['shop_info_record_no']; ?></p>
    </div>
    <a href="javascript:void (0);" onclick="$('html,body').animate({'scrollTop':0},600);" style="display: block;width: 1.706667rem;height: 1.706667rem;position: fixed; bottom: 2.56rem;right: 0.42rem; background-color: rgba(243,241,241,0.5);border: 1px solid #CCC;-webkit-border-radius: 50%;-moz-border-radius: 50%;border-radius: 50%;">
    	<img src="/template/mobile/default/static/images/topup.png" style="display: block;width: 1.706667rem;height: 1.706667rem;">
    </a>
</footer>
<script type="text/javascript">
$(function(){
    var ua = window.navigator.userAgent.toLowerCase();
    if(ua.match(/MicroMessenger/i) == 'micromessenger'){
        $('#logout').remove();
    }else{
        return false;
    }
})
</script> -->
    <div class="c-line">
        <p>已显示完该类商品</p>
    </div>
    <div class="flool3">
        <div>
            <span>搜豹网络提供技术支持</span>
        </div>
    </div>
    <!--底部-end-->
    <?php if($noob_gift): ?>
    <!-- 新人好礼专享弹窗s -->
    <div class="new-pople-1 clearfix">
        <div class="alter-center clearfix">
            <div class="bg-ams"></div>
            <div class="ul-1">
                <div class="li-gitl">
                    <?php if(is_array($noob_gift) || $noob_gift instanceof \think\Collection || $noob_gift instanceof \think\Paginator): $i = 0; $__LIST__ = $noob_gift;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <div class="li-1"><span>¥<b><?php echo (int)$vo['money'];?></b>元新人红包</span></div>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
                <a href="javascript:;"><div class="button-1"></div></a>
                <div class="dax-1"></div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <!-- 新人好礼专享弹窗e -->
  
    <!--底部导航-start-->
    <div class="foohi">
    <div class="footer">
        <ul  id="get_mobile_foot_menu" style="display: none;">
            <li>
                <a <?php if(CONTROLLER_NAME == 'Index'): ?>class="yello" <?php endif; ?>  href="<?php echo U('Index/index'); ?>">
                    <div class="icon">
                        <div class="icon_tp1 icon_tps"><img src="/template/mobile/default/static/images/home1.png"/> </div>
                        <div class="icon_tp2 icon_tps"><img src="/template/mobile/default/static/images/home2.png"/> </div>
                        <p>首页</p>
                    </div>
                </a>
            </li>
            <li>
                <a <?php if(CONTROLLER_NAME == 'Goods'): ?>class="yello" <?php endif; ?> href="<?php echo U('Goods/categoryList'); ?>">
                    <div class="icon">
                    	<div class="icon_tp1 icon_tps"><img src="/template/mobile/default/static/images/category1.png"/> </div>
                        <div class="icon_tp2 icon_tps"><img src="/template/mobile/default/static/images/category2.png"/> </div>
                        <p>分类</p>
                    </div>
                </a>
            </li>
            <li>
                <a <?php if(CONTROLLER_NAME == 'Dynamics'): ?>class="yello" <?php endif; ?>  href="<?php echo U('Dynamics/find'); ?>">
                    <div class="icon">
                    	<div class="icon_tp1 icon_tps"><img src="/template/mobile/default/static/images/find-1.png"/> </div>
                        <div class="icon_tp2 icon_tps"><img src="/template/mobile/default/static/images/find-2.png"/> </div>
                        <p>发现</p>
                    </div>
                </a>
            </li>
            <li>
                <a <?php if(CONTROLLER_NAME == 'Cart'): ?>class="yello" <?php endif; ?> href="<?php echo U('Cart/index'); ?>">
                    <div class="icon">
                    	<div class="icon_tp1 icon_tps"><img src="/template/mobile/default/static/images/cart1.png"/> </div>
                        <div class="icon_tp2 icon_tps"><img src="/template/mobile/default/static/images/cart2.png"/> </div>
                        <p>购物车</p>
                    </div>
                </a>
            </li>
            <li>
                <a <?php if(CONTROLLER_NAME == 'User'): ?>class="yello" <?php endif; ?> href="<?php echo U('User/index'); ?>">
                    <div class="icon">
                     	<div class="icon_tp1 icon_tps"><img src="/template/mobile/default/static/images/user1.png"/> </div>
                        <div class="icon_tp2 icon_tps"><img src="/template/mobile/default/static/images/user2.png"/> </div>
                        <p>我的</p>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</div>
<script src="/public/js/jqueryUrlGet.js"></script><!--获取get参数插件-->
<script type="text/javascript">
$(document).ready(function(){
	  var cart_cn = getCookie('cn');
	  if(cart_cn == ''){
		$.ajax({
			type : "GET",
			url:"/index.php?m=Home&c=Cart&a=header_cart_list",//+tab,
			success: function(data){								 
				cart_cn = getCookie('cn');
				$('#cart_quantity').html(cart_cn);						
			}
		});	
	  }
	  $('#cart_quantity').html(cart_cn);
});
$(function(){

    $.ajax({
        type : "GET",
        dataType:"json",
        url:"/index.php?m=Home&c=Api&a=get_mobile_foot_menu",//+tab,
        success: function(data){
            if(data.status == 1){
                var html = '';
                var Things = data.result.footmenu.nav;
                for (var i = 0; i < Things.length; i++) {
                    html += get_html(Things[i]);
                }
                $("#get_mobile_foot_menu").html(html).find('li').css('width',(100/Things.length)+'%')
            }
            $("#get_mobile_foot_menu").show();
        }
    });
    function get_html(data){
        var controller_name = '<?php echo strtolower(CONTROLLER_NAME); ?>/<?php echo strtolower(ACTION_NAME); ?>';
        var html = ''
        var cls = '';
        if(data.url.toLowerCase().indexOf(controller_name) > 0){
            data.pic1 = data.pic2;
            cls = 'class="yello"';
        }
        html += '<li>'
        html += '    <a href="'+data.url+'" '+cls+'>'
        html += '        <div class="icon">'
        html += '            <div class="icon_tp1 icon_tps"><img src="'+data.pic1+'"> </div>'
        html += '            <p>'+data.title_name+'</p>'
        html += '        </div>'
        html += '    </a>'
        html += '</li>'
        return html;
    }
})
set_first_leader();//设置推荐人
//切换图标
if($(".footer ul li a").hasClass("yello")){
	$(".footer ul li .yello").find(".icon_tp2").show();
	$(".footer ul li .yello").find(".icon_tp1").hide();
}
</script>
<!-- 微信浏览器 调用微信 分享js-->
<script type="text/javascript" src="<?php echo HTTP; ?>://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script type="text/javascript">
	var httpPrefix = "<?php echo SITE_URL; ?>";
<?php if(ACTION_NAME == 'goodsInfo'): ?>
   var ShareLink = httpPrefix+"/index.php?m=Mobile&c=Goods&a=goodsInfo&id=<?php echo $goods[goods_id]; ?>"; //默认分享链接
   var ShareImgUrl = "<?php echo goods_thum_images($goods[goods_id],400,400); ?>"; // 分享图标
   var ShareTitle = "<?php echo (isset($goods['goods_name']) && ($goods['goods_name'] !== '')?$goods['goods_name']:$tpshop_config['shop_info_store_title']); ?>"; // 分享标题
   var ShareDesc = "<?php echo getSubstr($goods['goods_remark'],0,30); ?>"; // 分享描述
<?php elseif(ACTION_NAME == 'info'): ?>
	var ShareLink = "<?php echo $team['bd_url']; ?>"; //默认分享链接
	var ShareImgUrl = "<?php echo $team['bd_pic']; ?>"; //分享图标
	var ShareTitle = "<?php echo $team[share_title]; ?>"; //分享标题
	var ShareDesc = "<?php echo $team[share_desc]; ?>"; //分享描述
<?php elseif(ACTION_NAME == 'my_store'): ?>
	var ShareLink = httpPrefix+"/index.php?m=Mobile&c=Distribut&a=my_store"; 
	var ShareImgUrl = "<?php echo $tpshop_config['shop_info_store_logo']; ?>";
	var ShareTitle = "<?php echo $share_title; ?>"; 
	var ShareDesc = httpPrefix+"/index.php?m=Mobile&c=Distribut&a=my_store}"; 
<?php elseif(ACTION_NAME == 'found'): ?>
	var ShareLink = httpPrefix+"/index.php?m=Mobile&c=Team&a=found&id=<?php echo $teamFound[found_id]; ?>"; //默认分享链接
	var ShareImgUrl = "<?php echo $team[bd_pic]; ?>"; //分享图标
	var ShareTitle = "<?php echo $team[share_title]; ?>"; //分享标题
	var ShareDesc = "<?php echo $team[share_desc]; ?>"; //分享描述
<?php elseif(strtolower(CONTROLLER_NAME) == 'store' and ACTION_NAME == 'store_index'): ?>
	var ShareLink = httpPrefix+"/index.php/mobile/Store/store_index/store_id/<?php echo \think\Request::instance()->get('store_id'); ?>"; // 店铺分享
	var ShareImgUrl = "<?php echo (isset($store['store_logo']) && ($store['store_logo'] !== '')?$store['store_logo']:'/template/mobile/default/static/images/logo.png'); ?>";
	var ShareTitle = "<?php echo $store['store_name']; ?>";
	var ShareDesc = "<?php echo $store['seo_description']; ?>"; // seo_description
<?php else: ?>
   var ShareLink = httpPrefix+"/index.php?m=Mobile&c=Index&a=index"; //默认分享链接
   var ShareImgUrl = "<?php echo $tpshop_config['shop_info_wap_home_logo']; ?>"; //分享图标
   var ShareTitle = "<?php echo $tpshop_config['shop_info_store_title']; ?>"; //分享标题
   var ShareDesc = "<?php echo $tpshop_config['shop_info_store_desc']; ?>"; //分享描述
<?php endif; ?>
if(ShareDesc==''){
	ShareDesc = "<?php echo $tpshop_config['shop_info_store_desc']; ?>";
}
if(ShareImgUrl.indexOf('http') < 0){
	ShareImgUrl = httpPrefix+ShareImgUrl
}
var is_distribut = getCookie('is_distribut'); // 是否分销代理
var user_id = getCookie('user_id'); // 当前用户id
var subscribe = getCookie('subscribe'); // 当前用户是否关注了公众号
 
// 如果已经登录了, 并且是分销商
if(parseInt(is_distribut) == 1 && parseInt(user_id) > 0)
{
	if(ShareLink.indexOf('&')>0){
		ShareLink = ShareLink + "&first_leader="+user_id;
	}else{
		ShareLink = ShareLink + "/first_leader/"+user_id;
	}
}

$(function() {
	if(isWeiXin() && parseInt(user_id)>0){
		$.ajax({
			type : "POST",
			url:"/index.php?m=Mobile&c=Index&a=ajaxGetWxConfig&t="+Math.random(),
			data:{'askUrl':encodeURIComponent(location.href.split('#')[0])},		
			dataType:'JSON',
			success: function(res)
			{
				//微信配置
				wx.config({
				    debug: false, 
				    appId: res.appId,
				    timestamp: res.timestamp, 
				    nonceStr: res.nonceStr, 
				    signature: res.signature,
				    jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage','onMenuShareQQ','onMenuShareQZone','hideOptionMenu'] // 功能列表，我们要使用JS-SDK的什么功能
				});
			},
			error:function(res){
				console.log("wx.config error:");
				console.log(res);
				return false;
			}
		}); 

		// config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在 页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready 函数中。
		wx.ready(function(){
		    // 获取"分享到朋友圈"按钮点击状态及自定义分享内容接口
		    wx.onMenuShareTimeline({
		        title: ShareTitle, // 分享标题
		        link:ShareLink,
		        desc: ShareDesc,
		        imgUrl:ShareImgUrl // 分享图标
		    });

		    // 获取"分享给朋友"按钮点击状态及自定义分享内容接口
		    wx.onMenuShareAppMessage({
		        title: ShareTitle, // 分享标题
		        desc: ShareDesc, // 分享描述
		        link:ShareLink,
		        imgUrl:ShareImgUrl // 分享图标
		    });
			// 分享到QQ
			wx.onMenuShareQQ({
		        title: ShareTitle, // 分享标题
		        desc: ShareDesc, // 分享描述
		        link:ShareLink,
		        imgUrl:ShareImgUrl // 分享图标
			});	
			// 分享到QQ空间
			wx.onMenuShareQZone({
		        title: ShareTitle, // 分享标题
		        desc: ShareDesc, // 分享描述
		        link:ShareLink,
		        imgUrl:ShareImgUrl // 分享图标
			});

		   <?php if(CONTROLLER_NAME == 'User'): ?> 
				wx.hideOptionMenu();  // 用户中心 隐藏微信菜单
		   <?php endif; ?>	
		});
	}
	
	if(!isWeiXin() || subscribe == 1){
		$('.guide').hide(); // 非微信浏览 不提示关注公众号		
	}

});

function isWeiXin(){
    var ua = window.navigator.userAgent.toLowerCase();
    if(ua.match(/MicroMessenger/i) == 'micromessenger'){
        return true;
    }else{
        return false;
    }
}
</script>
<!--微信关注提醒 start-->
<?php if(\think\Session::get('subscribe') == 0): ?>
<button class="guide" onclick="follow_wx()">关注公众号</button>
<style type="text/css">
.guide{width:0.627rem;height:2.83rem;text-align: center;border-radius: 8px ;font-size:0.512rem;padding:8px 0;border:1px solid #adadab;color:#000000;background-color: #fff;position: fixed;right: 6px;bottom: 200px;z-index: 99;}
#cover{display:none;position:absolute;left:0;top:0;z-index:18888;background-color:#000000;opacity:0.7;}
#guide{display:none;position:absolute;top:5px;z-index:19999;}
#guide img{width: 70%;height: auto;display: block;margin: 0 auto;margin-top: 10px;}
div.layui-m-layerchild h3{font-size:0.64rem;height:1.24rem;line-height:1.24rem;}
.layui-m-layercont img{height:8.96rem;width:8.96rem;}
</style>
<script type="text/javascript">
  //关注微信公众号二维码	 
function follow_wx()
{
	layer.open({
		type : 1,  
		title: '关注公众号',
		content: '<img src="<?php echo $wechat_config['qr']; ?>">',
		style: ''
	});
}
</script> 
<?php endif; ?>
<!--微信关注提醒  end-->
<!-- 微信浏览器 调用微信 分享js  end-->
    <!--底部导航-end-->

<script src="/template/mobile/default/static/js/style.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    /**
     * 秒杀模块倒计时
     * */
    function GetRTime(end_time){
        var NowTime = new Date();
        var t = (end_time*1000) - NowTime.getTime();
        var d=Math.floor(t/1000/60/60/24);
        var h=Math.floor(t/1000/60/60%24);
        var m=Math.floor(t/1000/60%60);
        var s=Math.floor(t/1000%60);
        var temp = (d * 24 + h) < 10 ? '0' + (d * 24 + h) : d * 24 + h
        m = m < 10 ? '0' + m : m
        s = s < 10 ? '0' + s : s
        if(s >= 0)
            return temp + ':' + m + ':' +s;
    }

    function GetRTime2(){
        var text = GetRTime('<?php echo $end_time; ?>');
        if (text== 0){
            $(".hms").text('活动已结束');
        }else{
            $(".hms").text(text);
        }
    }
    setInterval(GetRTime2,1000);

    //轮播
    $(function(){
        $('#slideTpshop').swipeSlide({
            continuousScroll:true,
            speed : 3000,
            transitionType : 'cubic-bezier(0.22, 0.69, 0.72, 0.88)',
            firstCallback : function(i,sum,me){
                me.find('.dot').children().first().addClass('cur');
            },
            callback : function(i,sum,me){
                me.find('.dot').children().eq(i).addClass('cur').siblings().removeClass('cur');
            }
        });
        //圆点
        var ed = $('.mslide ul li').length - 2;
        $('.mslide').append("<div class=" + "dot" + "></div>");
        for(var i = 0; i<ed ;i++){
            $('.mslide .dot').append("<span></span>");
        };
        $('.mslide .dot span:first').addClass('cur');
        var wid = - ($('.mslide .dot').width() / 2);
        $('.mslide .dot').css('position','absolute').css('left','50%').css('margin-left',wid);
    });

    // 判断新人专享礼包个数事件
    <?php if(noob_gift): ?>
    $(function(){
        $('.new-pople-1 .dax-1 , .button-1').on('click',function(){
           $('.new-pople-1').hide();
        })
        $(' .button-1').on('click',function(){
            $('.new-pople-1').hide();
        $.ajax({
                    type : "get",
                    url:"/index.php?m=Mobile&c=User&a=getCoupon&coupon_id="<?php if(is_array($noob_gift) || $noob_gift instanceof \think\Collection || $noob_gift instanceof \think\Paginator): $i = 0; $__LIST__ = $noob_gift;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>+'_'+<?php echo $vo['id']; endforeach; endif; else: echo "" ;endif; ?>,
                    success: function(data){}
            })
        })
        $(document).each(function(){
            var youhulish = $('.new-pople-1 .li-gitl .li-1').length
            if(youhulish <2){
                $('.new-pople-1 .li-gitl').css({
                    'display': 'flex',
                    'align-items': 'center',
                })
            }else{
                $('.new-pople-1 .li-gitl').removeAttr('style');
            }
        })
    })
    <?php endif; ?>

</script>
	</body>
</html>
<script> set_first_leader(); //设置推荐人 </script>