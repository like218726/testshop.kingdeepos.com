<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:48:"./template/mobile/default/index/ajaxGetMore.html";i:1587634412;}*/ ?>
<?php if(is_array($favourite_goods) || $favourite_goods instanceof \think\Collection || $favourite_goods instanceof \think\Paginator): if( count($favourite_goods)==0 ) : echo "" ;else: foreach($favourite_goods as $key=>$v): ?>  
    <li>
        <div class="mian_width_1">
            <a href="<?php echo U('Mobile/Goods/goodsInfo',array('id'=>$v[goods_id])); ?>" title="<?php echo $v['goods_name']; ?>">
                <img class="mian_img" src="<?php echo goods_thum_images($v[goods_id],400,400); ?>"/>
                <div class="mian_h2 mian_hidde"><?php echo $v[goods_name]; ?></div>
                <div class="goods_name_cen mian_flex_4">
                    <span class="big-price"><b>¥</b><?php echo $v[shop_price]; ?></span>
                    <!--<a href="<?php echo U('Goods/goodsList',['id'=>$v['cat_id3']]); ?>" title="<?php echo $v['goods_name']; ?>">-->
                        <!--<span class="guess-button J_ping">看相似</span>-->
                    <!--</a>-->
                    <span class="has-sold">已售出<?php echo $v['sales_sum']; ?>件</span>
                </div>
            </a>
        </div>
    </li>
<?php endforeach; endif; else: echo "" ;endif; ?>