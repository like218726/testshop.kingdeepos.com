<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:47:"./application/admin/view/user/ajaxsignList.html";i:1587634374;}*/ ?>
<table>
    <tbody>
    <?php if(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty())): ?>
        <tr>
            <td class="no-data" align="center" axis="col0" colspan="50">
                <i class="fa fa-exclamation-circle"></i>没有符合条件的记录
            </td>
        </tr>
        <?php else: if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?>
            <tr>
                <td align="center" axis="col0">
                    <div style="width: 50px;">                  
                        <?php echo $list['id']; ?>
                    </div>
                </td>                
                <td align="center" axis="col0">
                    <div style="text-align: left; width: 150px;"><?php echo $list['nickname']; ?></div>
                </td>
                <td align="center" axis="col0">
                    <div style="text-align: left; width: 150px;"><?php echo $list['mobile']; ?></div>
                </td>
                <td align="center" axis="col0">
                    <div style="text-align: center; width: 100px;"><?php echo $list['sign_total']; ?></div>
                </td>
                <td align="center" axis="col0">
                    <div style="text-align: center; width: 100px;"><?php echo $list['sign_count']; ?></div>
                </td>
                <td align="center" axis="col0">
                    <div style="text-align: center; width: 100px;"><?php echo $list['sign_last']; ?></div>
                </td>
                <td align="center" axis="col0">
                    <div style="text-align: center; width: 500px;"><?php echo $list['sign_time']; ?></div>
                </td>

                <td align="center" axis="col0">
                    <div style="text-align: center; width: 100px;"><?php echo $list['this_month']; ?></div>
                </td>
                </a>
                <td align="center" axis="col0">
                    <div style="text-align: center; width: 100px;"><?php echo $list['cumtrapz']; ?></div>
                </td>

                <td align="" class="" style="width: 100%;">
                              <div>&nbsp;</div>
<!--                    <div style="text-align: center; width: 100px;">
                        <a class="btn blue" href=javascript:void(0);" onclick="takeoff(this)"><i class="fa fa-search"></i>查看</a>
                    </div>-->
                </td>
            </tr>
        <?php endforeach; endif; else: echo "" ;endif; endif; ?>
</tbody>
</table>
<!--分页位置--> <?php echo $page; ?>
<script>
    // 点击分页触发的事件
    $(".pagination  a").click(function() {
        cur_page = $(this).data('p');
        ajax_get_table('search-form2', cur_page);
    });
</script>        