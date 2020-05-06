<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:51:"./application/seller/new/service/ajax_ask_list.html";i:1587634378;}*/ ?>
<form id="op" action="<?php echo U('Service/ask_handle'); ?>" method="post">
    <input type="hidden" id="operate" name="type"/>
    <table class="ncsc-default-table">
        <thead>
        <tr  nc_type="table_header">
            <th class="w30">&nbsp;</th>
            <th class="w100">用户</th>
            <th class="w100">商品ID</th>
            <th class="w200">商品</th>
            <th class="w80">显示</th>
            <th class="w80">咨询类型</th>
            <th class="w120">咨询时间</th>
            <th class="w120">操作</th>
        </tr>
        <?php if(count($comment_list) > 0): ?>
            <tr>
                <td class="tc"><input type="checkbox" id="all" class="checkall" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"/></td>
                <td colspan="20">
                    <label for="all">全选</label>
                    <a onclick="check_action('show');" class="ncbtn-mini">显示</a>
                    <a onclick="check_action('hide');" class="ncbtn-mini">隐藏</a>
                    <a onclick="check_action('del');" class="ncbtn-mini">删除</a>
                </td>
            </tr>
        <?php endif; ?>
        </thead>
        <tbody>
        <?php if(count($comment_list) > 0): if(is_array($comment_list) || $comment_list instanceof \think\Collection || $comment_list instanceof \think\Paginator): $i = 0; $__LIST__ = $comment_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?>
            <tr>
                <td class="trigger"><input type="checkbox" class="checkitem tc" name="selected[]" value="<?php echo $list['id']; ?>"/></td>
                <td><?php echo $list['username']; ?></td>
                <td><?php echo $list[goods_id]; ?></td>
                <td><a target="_blank" href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$list[goods_id])); ?>"><?php echo $goods_list[$list[goods_id]]; ?></a></td>
                <td><img width="20" height="20" src="/public/images/<?php if($list[is_show] == 1): ?>yes.png<?php else: ?>cancel.png<?php endif; ?>" onclick="changeTableVal2('goods_consult','id','<?php echo $list['id']; ?>','is_show',this)"/></td>
                <td><?php echo $consult_type[$list[consult_type]]; ?></td>
                <td><?php echo date('Y-m-d H:i:s',$list['add_time']); ?></td>
                <td class="nscs-table-handle">
                                <span><a href="<?php echo U('Comment/consult_info',array('id'=>$list[id])); ?>" class="btn-bluejeans"><i class="icon-edit"></i>
                                    <p>编辑</p></a></span>
                </td>
            </tr>
            <tr>
                <td colspan="20">
                    <div class="ncsc-goods-sku ps-container"></div>
                </td>
            </tr>
        <?php endforeach; endif; else: echo "" ;endif; else: ?>
            <tr>
                <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span>暂无符合条件的数据记录</span></div></td>
            </tr>
        <?php endif; ?>
        </tbody>
        <tfoot>
        <?php if(count($comment_list) > 0): ?>
        <tr>
            <th class="tc"><input type="checkbox" id="all2" class="checkall" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"/></th>
            <th colspan="10">
                <label for="all2">全选</label>
                <a onclick="check_action('show');" class="ncbtn-mini">显示</a>
                <a onclick="check_action('hide');" class="ncbtn-mini">隐藏</a>
                <a onclick="check_action('del');" class="ncbtn-mini">删除</a>
            </th>
        </tr>
        <?php endif; ?>
        <tr>
            <td colspan="20">
                <?php echo $page; ?>
            </td>
        </tr>
        </tfoot>
    </table>
</form>
<script>
    $(".pagination  a").click(function(){
        var page = $(this).data('p');
        ajax_get_table('search-form2',page);
    });
    function check_action(action){
        var selected = $('input[name*="selected"]:checked');
        if(selected.length < 1){
            layer.msg('请至少选择一个条目',{icon:2});
            return false;
        }
        $('#operate').val(action);
        $('#op').submit();
    }
</script>