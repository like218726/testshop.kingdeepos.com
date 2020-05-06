<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:47:"./application/seller/new/comment/ajaxindex.html";i:1587634376;}*/ ?>
<table class="ncsc-default-table">
    <thead>
    <tr>
        <th class="w80">用户</th>
        <th class="w200 tl">评论内容</th>
        <th class="w200">商品</th>
        <th class="w120">评论时间</th>
        <th class="w80">ip地址</th>
        <th class="w100">操作</th>
    </tr>
    </thead>
    <tbody>
    <?php if(is_array($comment_list) || $comment_list instanceof \think\Collection || $comment_list instanceof \think\Paginator): $i = 0; $__LIST__ = $comment_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?>
        <tr class="bd-line">
            <td><?php echo $list['nickname']; ?></td>
            <td class="tl"><?php echo $list['content']; ?></td>
            <td><a target="_blank" href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$list[goods_id])); ?>"><?php echo $goods_list[$list[goods_id]]; ?></a></td>
            <td><?php echo date('Y-m-d H:i',$list['add_time']); ?></td>
            <td><?php echo $list['ip_address']; ?></td>
            <td class="nscs-table-handle">
                <span><a href="<?php echo U('comment/detail',array('id'=>$list[comment_id])); ?>" class="btn-bluejeans"><i class="icon-edit"></i><p>回复</p></a></span>
            </td>
        </tr>
    <?php endforeach; endif; else: echo "" ;endif; ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="20">
            <?php echo $page; ?>
        </td>
    </tr>
    </tfoot>
</table>
<script>
    $(".pagination a").click(function(){
        var page = $(this).data('p');
        ajax_get_table('search-form2',page);
    });
</script>