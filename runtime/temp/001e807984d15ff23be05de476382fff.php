<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:42:"./template/app/default/ad/ad_category.html";i:1587634408;s:77:"/home/wwwroot/testshop.kingdeepos.com/template/app/default/public/header.html";i:1587634408;}*/ ?>
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

<body style="background-color: #FFF; overflow: auto;"> 
<div class="pos-wrap" style="">
    <img src="/template/app/default/static/images/ad/ad_category.png" style="width:375px; height: auto;"> 
    <?php $pid =531;$ad_position = M("ad_position")->cache(true,TPSHOP_CACHE_TIME)->column("position_id,position_name,ad_width,ad_height","position_id");$result = M("ad")->where("pid=$pid  and enabled = 1 and start_time <= 1588734873 and end_time >= 1588734873 ")->order("orderby desc")->cache(true,TPSHOP_CACHE_TIME)->limit("1")->select();
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
            <a class="inset-banner1"  href="<?php echo $v['ad_link']; ?>&is_app_ad=1&suggestion=520*180">
            <img class="inset-banner1" src="<?php echo $v[ad_code]; ?>" title="<?php echo $v[title]; ?>" style="<?php echo $v[style]; ?>"/>
            </a>
    <?php endforeach; ?> 
</div>
</body>
</html>

 
