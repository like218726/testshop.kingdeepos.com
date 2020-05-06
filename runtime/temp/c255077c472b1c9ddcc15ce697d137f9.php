<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:48:"./application/seller/new/order/ajaxdelivery.html";i:1587634376;}*/ ?>
<style>
    a.ncbtn-mini i, a.ncbtn i {
        vertical-align: bottom;
    }
</style>
<table class="ncsc-default-table order deliver">
<?php if(empty($orderList) == true): ?>
  	<tbody>
  		<tr>
      		<td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span>暂无符合条件的数据记录</span></div></td>
    	</tr>
      </tbody>
<?php else: if(is_array($orderList) || $orderList instanceof \think\Collection || $orderList instanceof \think\Paginator): $i = 0; $__LIST__ = $orderList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?>
	<tbody>
    <tr>
      <td colspan="21" class="sep-row"></td>
    </tr>
    <tr>
      <th style="width:30px;text-align:center" rowspan="<?php echo count($goodslist)?>"><input type="checkbox" value="<?php echo $list['order_id']; ?>" /></th>
      <th colspan="21"><span class="ml5">订单编号：<strong><?php echo $list['order_sn']; ?></strong></span><span>下单时间：<em class="goods-time"><?php echo date('Y-m-d H:i',$list['add_time']); ?></em></span>
        </em></span> <span class="fr mr10">
                <a href="<?php echo U('Order/order_print',array('ids'=>$list['order_id'],'template'=>'picking')); ?>" target="_blank" class="ncbtn-mini" title="打印配货单"><i class="icon-print"></i>打印配货单</a></span>
      </th>
   	 </tr>
   	 <?php $goodsList = $goodsArr[$list['order_id']]; if(is_array($goodsList) || $goodsList instanceof \think\Collection || $goodsList instanceof \think\Paginator): $k = 0; $__LIST__ = $goodsList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$good): $mod = ($k % 2 );++$k;if($k == 1): ?>
     	<!--  第一行 -->
     	<tr>
	      <td class="bdl w10"></td>
	      <td class="w50"><div class="pic-thumb"><a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$good['goods_id'])); ?>" target="_blank"><img src="<?php echo goods_thum_images($good['goods_id'],240,240,$good['item_id']); ?>" /></a></div></td>
	      <td class="tl">
	      	<dl class="goods-name">
	          <dt><a target="_blank" href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$good['goods_id'])); ?>"><?php echo $good['goods_name']; ?></a></dt>
	          <dd><strong>￥<?php echo $good['goods_price']; ?></strong>&nbsp;x&nbsp;<em><?php echo $good['goods_num']; ?></em>件</dd>
	        </dl>
	      </td>
	      <td class="bdl bdr order-info w500" rowspan="<?php echo count($goodsArr[$list['order_id']]); ?>">
	      	<dl>
	          <dt>买家：</dt>
	          <dd><?php echo $users[$list['user_id']]; ?></dd>
	        </dl>
	        <dl>
	          <dt>收货人：</dt>
	          <dd>
	            <div class="alert alert-info m0">
	              <p><i class="icon-user"></i><?php echo $list['consignee']; ?><span class="ml30" title="电话"><i class="icon-phone"></i><?php echo $list['mobile']; ?></span></p>
	              <p class="mt5" title="收货地址"><i class="icon-map-marker"></i><?php echo $list['address']; ?></p>
				</div>
	          </dd>
	        </dl>
	        <dl>
	          <dt>运费：</dt>
	          <dd><?php if(($list['shipping_price'] < 0.01)): ?>（免运费）<?php else: ?>邮费:<?php echo $list['shipping_price']; endif; if(($list['shipping_status'] != 1)  && (time() - $list['add_time'] < (86400 * 90))): ?>
                	<span><a href="<?php echo U('Seller/Order/delivery_info',array('order_id'=>$list['order_id'])); ?>" class="ncbtn-mini ncbtn-mint fr"><i class="icon-truck"></i>去发货&nbsp;&nbsp;</a></span>
            	<?php else: ?>
            		<span><a href="<?php echo U('Seller/Order/delivery_info',array('order_id'=>$list['order_id'])); ?>" class="ncbtn-mini ncbtn-mint fr"><i class="icon-truck"></i>发货详情&nbsp;&nbsp;</a></span>
            		<span><a href="<?php echo U('Order/shipping_print',array('order_id'=>$list['order_id'])); ?>" class="ncbtn-mini ncbtn-mint fr" style="position: relative;left: -5px;"><i class="icon-print"></i>打印快递单&nbsp;&nbsp;</a></span>
            	<?php endif; ?>
	          	
	          </dd>
	        </dl></td>
	    </tr>
     <?php else: ?>
     	<!--  第二行 -->
       <tr>
	      <td class="bdl w10"></td>
	      <td class="w50"><div class="pic-thumb"><a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$good['goods_id'])); ?>" target="_blank"><img src="<?php echo goods_thum_images($good['goods_id'],240,240,$good['item_id']); ?>"/></a></div></td>
	      <td class="tl"><dl class="goods-name">
	          <dt><a target="_blank" href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$good['goods_id'])); ?>"><?php echo $good['goods_name']; ?></a></dt>
	          <dd><strong>￥<?php echo $good['goods_price']; ?></strong>&nbsp;x&nbsp;<em><?php echo $good['goods_num']; ?></em>件</dd>
	        </dl></td>
	     </tr>
     <?php endif; endforeach; endif; else: echo "" ;endif; ?>
	</tbody>
  <?php endforeach; endif; else: echo "" ;endif; ?>
  <tfoot>
    <tr>
      <td colspan="21"><?php echo $page; ?></td>
    </tr>
      </tfoot>
  <?php endif; ?>
</table>
<script>
    $(".pagination  a").click(function(){
        var page = $(this).data('p');
        var listRows = $('#listRows').val();
        ajax_get_table('search-form2',page,listRows);
    });
</script>