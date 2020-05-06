<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:46:"./template/pc/rainbow/goods/open_add_cart.html";i:1587634420;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>加入购物车-<?php echo $tpshop_config['shop_info_store_title']; ?></title>
<meta name="keywords" content="<?php echo $tpshop_config['shop_info_store_keyword']; ?>" />
<meta name="description" content="<?php echo $tpshop_config['shop_info_store_desc']; ?>" />
    <link rel="shortcut  icon" type="image/x-icon" href="<?php echo (isset($tpshop_config['shop_info_store_ico']) && ($tpshop_config['shop_info_store_ico'] !== '')?$tpshop_config['shop_info_store_ico']:'/public/static/images/logo/storeico_default.png'); ?>" media="screen"/>
</head>
<body>
<link rel="stylesheet" type="text/css" href="/template/pc/rainbow/static/css/detail.css">
<div id="shopdilog">
  <div class="ui-popup ui-popup-modal ui-popup-show ui-popup-focus">
    <div i="dialog" class="ui-dialog">
      <div class="ui-dialog-arrow-a"></div>
      <div class="ui-dialog-arrow-b"></div>
      <table class="ui-dialog-grid">
        <tbody>
          <tr>
            <td i="body" class="ui-dialog-body">
              <div i="content" class="ui-dialog-content" id="content:1459321729418" style="width: 450px; height: auto;">
                <div id="addCartBox" class="collect-public" style="display: block;">
                  <div class="colect-top"> <i class="colect-icon"></i> 
                    <!--<i class="colect-fail"></i>-->
                    <div class="conect-title">
                      <span>添加成功</span>
                      <div class="add-cart-btn fn-clear"> 
                          <a href="javascript:;" onclick="javascript:parent.layer.closeAll('iframe');" class="ui-button ui-button-f80 fl go-shopping">继续购物</a> 
                          <a href="<?php echo U('Home/Cart/index'); ?>" target="_parent" class="ui-button ui-button-122 fl">去购物车结算</a>
                      </div>
                    </div>
                  </div>
                  <div id="watch">
                    <span>热卖产品：</span>
                    <ul class="fn-clear buy-list">
                    <?php
                                   
                                $md5_key = md5("select * from `__PREFIX__goods` where  is_recommend = 1 limit 4");
                                $result_name = $sql_result_v = S("sql_".$md5_key);
                                if(empty($sql_result_v))
                                {                            
                                    $result_name = $sql_result_v = \think\Db::query("select * from `__PREFIX__goods` where  is_recommend = 1 limit 4"); 
                                    S("sql_".$md5_key,$sql_result_v,31104000);
                                }    
                              foreach($sql_result_v as $k=>$v): ?>
                      <li>
                        <a href="<?php echo U('/Home/Goods/goodsInfo',array('id'=>$v[goods_id])); ?>" class="watch-img" target="_parent"><img src="<?php echo goods_thum_images($v['goods_id'],200,200); ?>" /></a>
                        <h4><a href="<?php echo U('/Home/Goods/goodsInfo',array('id'=>$v[goods_id])); ?>" target="_parent"><?php echo $v[goods_name]; ?></a></h4>
                        <p><q class="fn-rmb">¥</q><strong class="fn-rmb-num"><?php echo $v[shop_price]; ?></strong></p>
                      </li>
                     <?php endforeach; ?>  
                    </ul>
                  </div>
                </div>
              </div>
            </td>
          </tr>
          <tr>            
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>