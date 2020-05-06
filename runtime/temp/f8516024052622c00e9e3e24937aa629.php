<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:38:"./template/app/default/ad/ad_home.html";i:1587634408;s:77:"/home/wwwroot/testshop.kingdeepos.com/template/app/default/public/header.html";i:1587634408;s:77:"/home/wwwroot/testshop.kingdeepos.com/template/app/default/public/footer.html";i:1587634408;s:81:"/home/wwwroot/testshop.kingdeepos.com/template/app/default/public/footer_nav.html";i:1587634408;}*/ ?>
<style>
    .guesslike ul li:nth-of-type(odd){
        margin-right: .213rem;
    }
</style>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>首页--<?php echo $tpshop_config['shop_info_store_title']; ?></title>
    <link rel="stylesheet" href="/template/app/default/static/css/style.css">
    <link rel="stylesheet" type="text/css" href="/template/app/default/static/css/iconfont.css"/>
    <script src="/template/app/default/static/js/jquery-3.1.1.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="/template/app/default/static/js/mobile-util.js" type="text/javascript" charset="utf-8"></script>
    <script src="/public/js/global.js"></script>
    <script src="/template/app/default/static/js/layer.js"  type="text/javascript" ></script>
    <script src="/template/app/default/static/js/swipeSlide.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="/public/js/mobile_common.js"></script>
    <script src="/template/app/default/static/js/style.js" type="text/javascript" charset="utf-8"></script>
</head>
<body class="[body]">

<!--顶部搜索栏-s-->
<header>
    <div class="content">
        <div class="ds-in-bl search">
            <div class="sea-box">
                <!--<div class="logo">
                    <a href=""><img src="<?php echo (isset($tpshop_config['shop_info_wap_home_logo']) && ($tpshop_config['shop_info_wap_home_logo'] !== '')?$tpshop_config['shop_info_wap_home_logo']:'/public/static/images/logo/wap_home_logo_default.png'); ?>" alt="LOGO"></a>
                </div>-->
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
        <!--      <div class="ds-in-bl login">
                  <?php if($user_id > 0): ?> <a href="<?php echo U('User/index'); ?>"><?php else: ?>
                  <a href="<?php echo U('User/login'); ?>"><?php endif; ?>
                  <span><?php if($user_id > 0): ?><img class="after_login" src="/template/app/default/static/images/my.png"><?php else: ?>登录<?php endif; ?></span>
                  </a>
              </div>-->
    </div>
</header>
<!--顶部搜索栏-e-->

<!--顶部滚动广告栏-s-->
<div class="banner ban1">
    <div class="mslide" id="slideTpshop">
        <ul>
            <!--广告表-->
            <?php $pid =500;$ad_position = M("ad_position")->cache(true,TPSHOP_CACHE_TIME)->column("position_id,position_name,ad_width,ad_height","position_id");$result = M("ad")->where("pid=$pid  and enabled = 1 and start_time <= 1588733486 and end_time >= 1588733486 ")->order("orderby desc")->cache(true,TPSHOP_CACHE_TIME)->limit("5")->select();
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
    <div class="floor dh">
        <nav>
            <a href="<?php echo U('Goods/categoryList'); ?>">
                <span>
                    <img src="/template/app/default/static/images/icon_03.png" alt="全部分类" /><br />
                    <span>全部分类</span>
                </span>
            </a>
            <a href="<?php echo U('Index/street'); ?>">
                <span>
                    <img src="/template/app/default/static/images/icon_05.png" alt="店铺街" /><br />
                    <span>店铺街</span>
                </span>
            </a>
            <a href="<?php echo U('Index/brand'); ?>">
                <span>
                    <img src="/template/app/default/static/images/icon_07.png" alt="品牌街" /><br />
                    <span>品牌街</span>
                </span>
            </a>
            <a href="<?php echo U('Activity/promote_goods'); ?>">
                <span>
                    <img src="/template/app/default/static/images/icon_09.png" alt="优惠活动" /><br />
                    <span>优惠活动</span>
                </span>
            </a>
            <a href="<?php echo U('Activity/group_list'); ?>">
                <span>
                    <img src="/template/app/default/static/images/icon_15.png" alt="团购" /><br />
                    <span>团购</span>
                </span>
            </a>
            <a href="<?php echo U('Activity/coupon_list'); ?>">
                <span>
                    <img src="/template/app/default/static/images/icon_16.png" alt="领券中心" /><br />
                    <span>领券中心</span>
                </span>
            </a>
            <!--<a href="shopcar.html">-->
            <a href="<?php echo U('Goods/integralMall'); ?>">
                <span>
                    <img src="/template/app/default/static/images/icon_17.png" alt="积分商城" /><br />
                    <span>积分商城</span>
                </span>
            </a>
            <!--<a href="my.html">-->
            <!--code_15拼团链接-->
            <a href="<?php echo U('Team/index'); ?>">
                <!--code_15拼团链接-->
                <span>
                    <img src="/template/app/default/static/images/icon_19.png" alt="我要拼团" /><br />
                    <span>我要拼团</span>
                </span>
            </a>
        </nav>
    </div>
    <!--菜单-end-->

    <!--秒杀-start-->
    <div class="floor secondkill">
        <div class="content">
            <div class="time p">
                <div class="djs lightning fl">
                    <span class="add fl">秒杀专场</span>
                    <!--                -->
                    <div class="fl"><span class="red fl" id=""><?php echo date('H',$start_time); ?>点场</span><span class="hms fl"></span></div>
                </div>
                <div class="xsxl fr">
                    <a href="<?php echo U('Mobile/Activity/flash_sale_list'); ?>">
                        <span>更多秒杀<img src="/template/app/default/static/images/or.png" alt="" /></span>
                    </a>
                </div>
            </div>
            <div class="shop p">
                <?php if(count($flash_sale_list) == nll): ?>
                    <div style="text-align: center;font-size: .512rem;color: #999">暂无抢购商品...</div>
                <?php endif; if(is_array($flash_sale_list) || $flash_sale_list instanceof \think\Collection || $flash_sale_list instanceof \think\Paginator): if( count($flash_sale_list)==0 ) : echo "" ;else: foreach($flash_sale_list as $key=>$v): ?>
                    <!--<a href="<?php echo U('Mobile/Goods/goodsInfo',array('id'=>$v[goods_id],'item_id'=>$v[item_id])); ?>">-->
                    <a href="<?php echo U('Mobile/Activity/flash_sale_list'); ?>">
                        <div class="timerBox shopnum">
                            <img src="<?php echo goods_thum_images($v[goods_id],200,200,$v['item_id']); ?>"/>
                            <p>￥<span><?php echo $v[price]; ?></span>元</p>
                        </div>
                    </a>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
    </div>
    <!--秒杀-end-->
    <style>
        .advertisement {
            margin-top: 0.512rem;
        }
    </style>
    <!--广告位-start-->
    <div class="floor advertisement">
        <div class="content">
            <div class="le lefhe fl">
                <?php $pid =506;$ad_position = M("ad_position")->cache(true,TPSHOP_CACHE_TIME)->column("position_id,position_name,ad_width,ad_height","position_id");$result = M("ad")->where("pid=$pid  and enabled = 1 and start_time <= 1588733486 and end_time >= 1588733486 ")->order("orderby desc")->cache(true,TPSHOP_CACHE_TIME)->limit("1")->select();
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
                <?php endforeach; $pid =507;$ad_position = M("ad_position")->cache(true,TPSHOP_CACHE_TIME)->column("position_id,position_name,ad_width,ad_height","position_id");$result = M("ad")->where("pid=$pid  and enabled = 1 and start_time <= 1588733486 and end_time >= 1588733486 ")->order("orderby desc")->cache(true,TPSHOP_CACHE_TIME)->limit("1")->select();
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
                <?php $pid =508;$ad_position = M("ad_position")->cache(true,TPSHOP_CACHE_TIME)->column("position_id,position_name,ad_width,ad_height","position_id");$result = M("ad")->where("pid=$pid  and enabled = 1 and start_time <= 1588733486 and end_time >= 1588733486 ")->order("orderby desc")->cache(true,TPSHOP_CACHE_TIME)->limit("1")->select();
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
                <?php endforeach; $pid =509;$ad_position = M("ad_position")->cache(true,TPSHOP_CACHE_TIME)->column("position_id,position_name,ad_width,ad_height","position_id");$result = M("ad")->where("pid=$pid  and enabled = 1 and start_time <= 1588733486 and end_time >= 1588733486 ")->order("orderby desc")->cache(true,TPSHOP_CACHE_TIME)->limit("1")->select();
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
    <div class="banner" style="margin-top: 0.213rem;height: 4.052rem;overflow: hidden">
        <?php $pid =510;$ad_position = M("ad_position")->cache(true,TPSHOP_CACHE_TIME)->column("position_id,position_name,ad_width,ad_height","position_id");$result = M("ad")->where("pid=$pid  and enabled = 1 and start_time <= 1588733486 and end_time >= 1588733486 ")->order("orderby desc")->cache(true,TPSHOP_CACHE_TIME)->limit("1")->select();
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

    <!--猜您喜欢-start-->
    <div class="floor guesslike">
        <div class="banner banner_imgs">
            <img src="/template/app/default/static/images/tp-cainixih.png" alt="猜您喜欢"/>
        </div>
        <div class="likeshop">
            <div id="J_ItemList" style="padding: 0 .21333rem">
                <ul class="product single_item info">
                </ul>
                <a href="javascript:;" class="get_more" style="text-align:center; display:block;">
                    <img src='/template/app/default/static/images/category/loader.gif' width="12" height="12">
                </a>
            </div>
        </div>
        <!--<div class="add" onClick="getGoodsList()">点击继续加载</div>-->
        <div class="loadbefore">
            <img class="ajaxloading" src="/template/app/default/static/images/category/loader.gif" alt="loading...">
        </div>
        <div id="getmore"  style="font-size:.32rem;text-align: center;color:#888;padding:.25rem .24rem .4rem; clear:both;display: none"><a >已显示完所有记录</a></div>
    </div>
    <!--猜您喜欢-end-->

    <!--底部-start-->
    <footer>
    <div class="flool1">
        <ul>
            <?php if(!empty($_COOKIE['user_id'])): ?>
                <li><a href="<?php echo U('Mobile/User/index'); ?>"><?php echo getSubstr($_COOKIE['uname'],0,3); ?></a></li>
                <li><a href="<?php echo U('Mobile/User/logout'); ?>">退出</a></li>
            <?php else: ?>
                <li><a href="<?php echo U('Mobile/User/login'); ?>">登录</a></li>
                <li><a href="<?php echo U('Mobile/User/reg'); ?>">注册</a></li>
            <?php endif; ?>
            <li><a href="">反馈</a></li>
            <li class="comebackTop">回到顶部</li>
        </ul>
    </div>
    <div class="flool2">
        <ul>
            <li>
                <a href="?">
                    <div class="icon">
                        <img src="/template/app/default/static/images/ind_70.png"/>
                        <p>客户端</p>
                    </div>
                </a>
            </li>
            <li>
                <a href="<?php echo U('Mobile/Index/index'); ?>">
                    <div class="icon black">
                        <img src="/template/app/default/static/images/ind_72.png"/>
                        <p>触屏版</p>
                    </div>
                </a>
            </li>
            <li>
                <a href="<?php echo U('Home/Index/index'); ?>">
                    <div class="icon">
                        <img src="/template/app/default/static/images/ind_74.png"/>
                        <p>电脑版</p>
                    </div>
                </a>
            </li>
        </ul>
    </div>
    <div class="flool3">
        <p>Copyright © 2004-2016 TPshop开源商城99soubao.com版权所有</p>
    </div>
    <a href="javascript:$('html,body').animate({'scrollTop':0},600);" style="display: block;width: 40px;height: 40px;position: fixed; bottom: 70px;right: 8px; background-color: rgba(243,241,241,0.5);border: 1px solid #CCC;-webkit-border-radius: 50%;-moz-border-radius: 50%;border-radius: 50%;">
    	<img src="/template/app/default/static/images/topup.png" style="display: block;width: 40px;height: 40px;">
    </a>
</footer>
    <!--底部-end-->

    <!--底部导航-start-->
    <div class="foohi tpnavf">
    <div class="footer">
        <ul>
            <li>
                <a <?php if(CONTROLLER_NAME == 'Index'): ?>class="yello" <?php endif; ?>  href="<?php echo U('Index/index'); ?>">
                    <div class="icon">
                        <i class="icon-shouye iconfont"></i>
                        <p>首页</p>
                    </div>
                </a>
            </li>
            <li>
                <a href="<?php echo U('Goods/categoryList'); ?>">
                    <div class="icon">
                        <i class="icon-fenlei iconfont"></i>
                        <p>分类</p>
                    </div>
                </a>
            </li>
            <li>
                <a href="<?php echo U('Cart/index'); ?>">
                    <div class="icon">
                        <i class="icon-gouwuche iconfont"></i>
                        <p>购物车</p>
                    </div>
                </a>
            </li>
            <li>
                <a <?php if(CONTROLLER_NAME == 'User'): ?>class="yello" <?php endif; ?> href="<?php echo U('User/index'); ?>">
                    <div class="icon">
                        <i class="icon-wode iconfont"></i>
                        <p>我的</p>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</div>
    <!--底部导航-end-->

    <script type="text/javascript" src="/template/app/default/static/js/sourch_submit.js"></script>
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

        /**
         * 继续加载猜您喜欢
         * */

        var before_request = 1; // 上一次请求是否已经有返回来, 有才可以进行下一次请求
        var page = 0;
        function ajax_sourch_submit(){
            if(before_request == 0)// 上一次请求没回来 不进行下一次请求
                return false;
            before_request = 0;
            ++page;
            $('.get_more').show();
            $.ajax({
                type : "get",
                url:"/index.php?m=Mobile&c=Index&a=ajaxGetMore&p="+page,
                success: function(data)
                {
                    if(data){
                        $("#J_ItemList>ul").append(data);
                        $('.get_more').hide();
                        before_request = 1;
                    }else{
                        $('.get_more').hide();
                    }
                }
            });
        }
    </script>
    </body>
    </html>
    <script> set_first_leader(); //设置推荐人 </script>