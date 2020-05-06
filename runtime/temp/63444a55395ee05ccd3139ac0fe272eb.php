<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:45:"./application/admin/view/order/ajaxindex.html";i:1587634374;}*/ ?>
<table>
 	<tbody>
 	<?php if(empty($orderList) == true): ?>
 		<tr data-id="0">
	        <td class="no-data" align="center" axis="col0" colspan="50">
	        	<i class="fa fa-exclamation-circle"></i>没有符合条件的记录
	        </td>
	     </tr>
	<?php else: if(is_array($orderList) || $orderList instanceof \think\Collection || $orderList instanceof \think\Paginator): $i = 0; $__LIST__ = $orderList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?>
  	<tr data-order-id="<?php echo $list['order_id']; ?>">
        <td class="sign" axis="col0">
          <div style="width: 24px;"><i class="ico-check"></i></div>
        </td>
        <td align="left" abbr="order_sn" axis="col3" class="">
          <div style="text-align: left; width: 130px;" class=""><?php echo $list['order_sn']; ?></div>
        </td>
        <td align="left" abbr="consignee" axis="col4" class="">
          <div style="text-align: left; width: 120px;" class=""><?php echo $list['consignee']; ?>:<?php echo $list['mobile']; ?></div>
        </td>
        <td align="center" abbr="article_show" axis="col5" class="">
          <div style="text-align: center; width: 60px;" class=""><?php echo $list['goods_price']; ?></div>
        </td>
        <td align="center" abbr="article_time" axis="col6" class="">
          <div style="text-align: center; width: 60px;" class=""><?php echo $list['order_amount']; ?></div>
        </td>
        <td align="center" abbr="article_time" axis="col6" class="">
          <div style="text-align: center; width: 60px;" class=""><?php echo $order_status[$list[order_status]]; if($list['is_cod'] == '1'): ?><span style="color: red">(货到付款)</span><?php endif; ?></div>
        </td>
        <td align="center" abbr="article_time" axis="col6" class="">
          <div style="text-align: center; width: 60px;" class=""><?php echo $pay_status[$list[pay_status]]; ?></div>
        </td>
        <td align="center" abbr="article_time" axis="col6" class="">
          <div style="text-align: center; width: 60px;" class=""><?php echo $shipping_status[$list[shipping_status]]; ?></div>
        </td>
        <td align="center" abbr="article_time" axis="col6" class="">
          <div style="text-align: center; width: 60px;" class=""><?php echo (isset($list['pay_name']) && ($list['pay_name'] !== '')?$list['pay_name']:'其他方式'); ?></div>
        </td>
        <td align="center" abbr="article_time" axis="col6" class="">
          <div style="text-align: center; width: 60px;" class=""><?php echo $list['shipping_name']; ?></div>
        </td>
        <td align="center" abbr="article_time" axis="col6" class="">
          <div style="text-align: center; width: 120px;" class=""><?php echo date('Y-m-d H:i',$list['add_time']); ?></div>
        </td>
        <td align="center" abbr="article_time" axis="col6" class="">
          <div style="text-align: center; width: 160px;" class=""><?php echo $store_list[$list[store_id]]; ?></div>
        </td>
        <td align="center" axis="col1" class="handle" align="center">
        		<div style="text-align: center; ">
        			<a class="btn green" href="<?php echo U('Admin/order/detail',array('order_id'=>$list['order_id'])); ?>"><i class="fa fa-list-alt"></i>查看</a>
        		</div>
         </td>
         <td align="" class="" style="width: 100%;">
            <div>&nbsp;</div>
          </td>
      </tr>
      <?php endforeach; endif; else: echo "" ;endif; endif; ?>
    </tbody>
</table>
<div class="row">
    <div class="col-sm-6 text-left"></div>
    <div class="col-sm-6 text-right"><?php echo $page; ?></div>
</div>
<script>
    $(".pagination  a").click(function(){
        var page = $(this).data('p');
        ajax_get_table('search-form2',page);
    });
    
    $( 'h5', '.ftitle').empty().html("(共<?php echo $pager->totalRows; ?>条记录)");
</script>