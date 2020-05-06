<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:63:"./application/seller/new/decoration/store_decoration_block.html";i:1587634376;}*/ ?>
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