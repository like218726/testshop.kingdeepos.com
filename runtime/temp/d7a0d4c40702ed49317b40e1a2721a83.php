<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:45:"./application/seller/new/order/ajaxindex.html";i:1587634376;}*/ ?>
<table class="ncsc-default-table order">
  <thead>
    <tr>
    <th style="width:38px;text-align:ceter">
		<input id="all" type="checkbox" title="全选"/>全选
	</th>
      <th class="w10"></th>
      <th colspan="2">商品</th>
      <th class="w100">单价（元）</th>
      <th class="w40">数量</th>
      <th class="w100">买家</th>
      <th class="w100">订单金额</th>
      <th class="w90">交易状态</th>
      <th class="w120">交易操作</th>
    </tr>
  </thead>
  <?php if(empty($orderList) == true): ?>
  	<tbody>
  		<tr>
      		<td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span>暂无符合条件的数据记录</span></div></td>
    	</tr>
    </tbody>
  <?php else: if(is_array($orderList) || $orderList instanceof \think\Collection || $orderList instanceof \think\Paginator): $i = 0; $__LIST__ = $orderList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?>  
  <tbody>
    <tr>
      <td colspan="20" class="sep-row"></td>
    </tr>
    <tr>
      <th style="width:30px;text-align:center" rowspan="<?php echo count($goodslist)?>">
		  <input type="checkbox" class="checkbox" value="<?php echo $list['order_id']; ?>" />
	  </th>
      <th colspan="20">
      		<span class="ml10">订单编号：<em><?php echo $list['order_sn']; ?></em></span>
		  	<span>下单时间：<em class="goods-time"><?php echo date('Y-m-d H:i',$list['add_time']); ?></em></span>
		  	<span>订单状态：<em class="goods-time"><?php echo \think\Config::get('ORDER_STATUS')[$list[order_status]]; ?></em></span>
			<span>订单类型：<em class="goods-time">
				<?php if($list['prom_type'] == 4): ?>
					预售订单
				<?php elseif($list['prom_type'] == 5): ?>
					虚拟订单
				<?php elseif($list['prom_type'] == 6): ?>
					拼团订单
				<?php else: ?>
					普通订单
				<?php endif; ?>
			</em></span>
      		<span class="fr mr10"><a href="<?php echo U('Order/order_print',array('ids'=>$list['order_id'].',','template'=>'picking')); ?>" target="_blank" class="ncbtn-mini" title="打印配货单"><i class="icon-print"></i>打印配货单</a></span>
       </th>
    </tr>
 
    <?php $goodsList = $goodsArr[$list['order_id']]; if(is_array($goodsList) || $goodsList instanceof \think\Collection || $goodsList instanceof \think\Paginator): $k = 0; $__LIST__ = $goodsList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$good): $mod = ($k % 2 );++$k;if($k == 1): ?>
     	<!--  第一行 -->
     	<tr>
	      <td class="bdl"></td>
	      <td class="w70">
	      <div class="ncsc-goods-thumb"><a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$good['goods_id'])); ?>" target="_blank"><img src="<?php echo goods_thum_images($good['goods_id'],240,240,$good['item_id']); ?>"  ></a></div></td>
	      <td class="tl">
	      	<dl class="goods-name">
	          <dt><a target="_blank" href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$good['goods_id'])); ?>"><?php echo $good['goods_name']; ?></a></dt>
	        </dl>
	      </td>
	      <td><p><?php echo $good['goods_price']; ?></p></td>
	      <td><?php echo $good['goods_num']; ?></td>
	      <!-- S 合并TD -->
	      <td class="bdl" rowspan="<?php echo count($goodsArr[$list['order_id']]); ?>">
	      	<div class="buyer"><?php echo $list['consignee']; ?> <p member_id="5"></p>
	          <div class="buyer-info"> <em></em>
	            <div class="con">
	              <h3><i></i><span>联系信息 </span></h3>
	              <dl>
	                <dt>姓名：</dt>
	                <dd><?php echo $list['consignee']; ?></dd>
	              </dl>
	              <dl>
	                <dt>电话：</dt>
	                <dd><?php echo $list['mobile']; ?></dd>
	              </dl>
	              <dl>
	                <dt>地址：</dt>
	                <dd><?php echo $list['address']; ?></dd>
	              </dl>
	            </div>
	          </div>
	        </div>
	      </td>
	      <td class="bdl" rowspan="<?php echo count($goodsArr[$list['order_id']]); ?>">
	      	<p class="ncsc-order-amount"><?php echo $list['total_amount']; ?></p>
	        <p class="goods-freight"><?php if(($list['shipping_price'] < 0.01)): ?>（免运费）<?php else: ?>邮费:<?php echo $list['shipping_price']; endif; ?></p>
	        <p class="goods-pay" title="支付方式：<?php echo $list['pay_name']; ?>"><?php echo $list['pay_name']; ?></p>
	      </td>
	      <td class="bdl bdr" rowspan="<?php echo count($goodsArr[$list['order_id']]); ?>"><p><?php echo $pay_status[$list[pay_status]]; ?> </p>
	        <!-- 物流跟踪 -->
	        <p></p>
	       </td>
	      <!-- 取消订单 -->
	      <td class="nscs-table-handle" rowspan="<?php echo count($goodsArr[$list['order_id']]); ?>" data-order-id="<?php echo $list['order_id']; ?>">
	      	<span><a href="<?php echo U('order/detail',array('order_id'=>$list['order_id'])); ?>" class="ncbtn-mint"><i class="icon-search"></i><p>详情</p></a></span>
	      	<?php if(($list['order_status'] == 5)): ?>
		      	<span><a href="<?php echo U('order/delete_order',array('order_id'=>$list['order_id'])); ?>" class="btn-grapefruit"><i class="icon-trash"></i><p>删除</p></a></span>
	      	<?php else: ?>
	      		<!--<span><a href="javascript:void(0)" onclick="layer.alert('该订单不得删除!',{icon:2});" class="btn-grapefruit"><i class="icon-trash"></i><p>删除</p></a></span>-->
	      	<?php endif; ?>
	        </td>
	    </tr>
    <?php else: ?>
    	<!--  非第一行 -->
    	<tr>
	      <td class="bdl"></td>
	      <td class="w70">
	      	<div class="ncsc-goods-thumb"><a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$good['goods_id'])); ?>" target="_blank"><img src="<?php echo goods_thum_images($good['goods_id'],240,240,$good['item_id']); ?>"  ></a></div>
	      </td>
	      <td class="tl">
	      	<dl class="goods-name">
	          <dt><a target="_blank" href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$good['goods_id'])); ?>"><?php echo $good['goods_name']; ?></a></dt>
	        </dl>
	      </td>
	      <td><p><?php echo $good['goods_price']; ?></p></td>
	      <td><?php echo $good['goods_num']; ?></td>
	     </tr>
    <?php endif; endforeach; endif; else: echo "" ;endif; ?>
   </tbody> 
   <?php endforeach; endif; else: echo "" ;endif; ?>
  <tfoot>
  	<tr>
		<td colspan="20"><?php echo $page; ?></td>
	</tr>
   </tfoot>
  <?php endif; ?>
</table>
 <script>
 var tmp=0;
    $(".pagination  a").click(function(){
        var page = $(this).data('p');
        ajax_get_table('search-form2',page);
    });

	$("#all").click(function(){
		if(tmp==0){
			$("input[type='checkbox']").attr("checked","true");
			tmp=1;
		}else{
			$("input[type='checkbox']").removeAttr("checked"); 
			tmp=0;
		}
	}) 
</script>