<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:47:"./application/admin/view/comment/ajaxindex.html";i:1587634374;}*/ ?>
<table>
 	<tbody>
 	<?php if(empty($comment_list) == true): ?>
 		<tr data-id="0">
	        <td class="no-data" align="center" axis="col0" colspan="50">
	        	<i class="fa fa-exclamation-circle"></i>没有符合条件的记录
	        </td>
	     </tr>
	<?php else: if(is_array($comment_list) || $comment_list instanceof \think\Collection || $comment_list instanceof \think\Paginator): $i = 0; $__LIST__ = $comment_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?>
  	<tr data-id="<?php echo $list['comment_id']; ?>">
        <td class="sign" axis="col0">
          <div style="width: 24px;"><i class="ico-check" ></i></div>
        </td>
        <td align="left" abbr="nickname" axis="col3" class="">
          <div style="text-align: left; width: 100px;" class=""><?php echo $list['nickname']; ?></div>
        </td>
        <td style="text-align: left; width: 90px;">
        	<div style="text-align: left; width: 90px;">	 
        		 <span class="raty" data-score="<?php echo $list['goods_rank']; ?>" style="width: 100px;" id="ui-id-2">
        		    <?php  
        		    	for($i = 0;$i < $list['goods_rank'];$i++){
							echo "<i class='fa fa-star'></i>&nbsp;";
						}
						for($j = ($list['goods_rank']) ; $j < 5 ; $j++){
							echo "<i class='fa fa-star-o'></i>&nbsp;";
						}
        		     ?>
        		</span>
        	</div>
        </td>
        <td align="left" abbr=content axis="col4" class="">
          	<div style="text-align: left; width: 200px;" class=""><?php echo stripos($list['content'],'"') == 0 && !is_bool(stripos($list['content'],'"')) ? json_decode($list['content']) : $list['content']; ?></div>
        </td>
        <td align="left" abbr="img" axis="col4" class="">
        	<div style="text-align: left; width: 200px;">
        			<ul class="evaluation-pic-list">
                        <?php $_5eb18b871f7d9=unserialize($list['img']); if(is_array($_5eb18b871f7d9) || $_5eb18b871f7d9 instanceof \think\Collection || $_5eb18b871f7d9 instanceof \think\Paginator): if( count($_5eb18b871f7d9)==0 ) : echo "" ;else: foreach($_5eb18b871f7d9 as $key=>$val): ?>
                            <li><a href="<?php echo $val; ?>"><img src="<?php echo $val; ?>"   onmouseover="layer.tips('<img src=\'<?php echo $val; ?>\'>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"></a></li>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
        			</ul>
        	</div>
        </td>
        <td align="center" abbr="article_show" axis="col5" class="" style="white-space: normal;">
            <div style="text-align: left; width: 200px;white-space: normal;height:inherit;line-height: inherit;" class="">
          		<a target="_blank" href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$list[goods_id])); ?>"><?php echo $goods_list[$list[goods_id]]; ?></a>
          	</div>
        </td>
        <td align="center" abbr="article_time" axis="col6" class="">
          <div style="text-align: center; width: 30px;">
                    <?php if($list[is_show] == 1): ?>
                      <span class="yes" onClick="changeTableVal('Comment','comment_id','<?php echo $list['comment_id']; ?>','is_show',this)" ><i class="fa fa-check-circle"></i>是</span>
                      <?php else: ?>
                      <span class="no" onClick="changeTableVal('Comment','comment_id','<?php echo $list['comment_id']; ?>','is_show',this)" ><i class="fa fa-ban"></i>否</span>
                    <?php endif; ?>
                  </div>
        </td>
        <td align="center" abbr="article_time" axis="col6" class="">
          <div style="text-align: center; width: 120px;" class=""><?php echo date('Y-m-d H:i:s',$list['add_time']); ?></div>
        </td>
        <td align="center" abbr="article_time" axis="col6" class="">
          <div style="text-align: center; width: 80px;" class=""><?php echo $list['ip_address']; ?></div>
        </td>
        <td align="center" abbr="article_time" axis="col6" class="">
               <div style="text-align: center; width: 100px;" class="">
       			<a class="btn green" style="display:none"  href="<?php echo U('Admin/comment/detail',array('id'=>$list[comment_id])); ?>"><i class="fa fa-list-alt"></i>查看</a>
       			<a class="btn red"  href="javascript:void(0);" onclick="publicHandle('<?php echo $list[comment_id]; ?>','del')" ><i class="fa fa-trash-o"></i>删除</a>
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