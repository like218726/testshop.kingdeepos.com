<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:50:"./application/admin/view/block/ajax_form_list.html";i:1587634374;}*/ ?>
<div id="flexigrid" cellpadding="0" cellspacing="0" border="0">
    <table>
        <tbody>
        <?php if(is_array($userList) || $userList instanceof \think\Collection || $userList instanceof \think\Paginator): if( count($userList)==0 ) : echo "" ;else: foreach($userList as $key=>$list): ?>
            <tr data-id="<?php echo $list['form_id']; ?>" id="<?php echo $list['form_id']; ?>">
                <td class="sign">
                    <div style="width: 24px;"><i class="ico-check"></i></div>
                </td>
                <td align="left" class="">
                    <div style="text-align: center; width: 40px;"><?php echo $list['form_id']; ?></div>
                </td>

                <?php if(is_array($name_list) || $name_list instanceof \think\Collection || $name_list instanceof \think\Paginator): $i = 0; $__LIST__ = $name_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>

                    <td align="left" class="">
                        <div style="text-align: center; width: 100px;" ><a class="atxt" title="<?php echo $list['submit_value'][$v['name']]; ?>"><?php echo $list['submit_value'][$v['name']]; ?></a></div>
                    </td>

                <?php endforeach; endif; else: echo "" ;endif; ?>

                <td align="left" class="">
                    <div style="text-align: center; width: 120px;"><?php echo date('Y-m-d H:i:s',$list['submit_time']); ?> </div>
                </td>

                <td align="center" class="handle">
                    <div style="text-align: center; width: 150px; max-width:250px;">
                        <!--['name'.$k]<a class="btn blue" href="<?php echo U('Admin/user/detail'); ?>">详情</a>
                        <a class="btn blue" href="<?php echo U('Admin/user/account_log'); ?>">资金</a>
                        <a class="btn blue" href="<?php echo U('Admin/user/address'); ?>">收货地址</a>-->
                        <a class="btn red" href="javascript:void(0);" data-order-id="<?php echo $list['form_id']; ?>" onclick="del(this)">删除</a>
                    </div>
                </td>
                <td align="" class="" style="width: 100%;">
                    <div>&nbsp;</div>
                </td>
            </tr>
        <?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
    </table>
</div>
<!--分页位置-->
<?php echo $pager->show(); ?>
<script>
    $(".pagination  a").click(function(){
        var page = $(this).data('p');
        ajax_get_table('search-form2',page);
    });
    $(document).ready(function(){
        // 表格行点击选中切换
        $('#flexigrid >table>tbody>tr').click(function(){
            $(this).toggleClass('trSelected');
        });
        $('#user_count').empty().html("<?php echo $pager->totalRows; ?>");
    });
    function delfun(obj) {
        // 删除按钮
        layer.confirm('确认删除？', {
            btn: ['确定', '取消'] //按钮
        }, function () {
            $.ajax({
                type: 'post',
                url: $(obj).attr('data-url'),
                data: {id : $(obj).attr('data-id')},
                dataType: 'json',
                success: function (data) {
                    layer.closeAll();
                    if (data.status == 1) {
                        $(obj).parent().parent().parent().remove();
                    } else {
                        layer.alert(data.msg, {icon: 2});
                    }
                }
            })
        }, function () {
        });
    }
    // 删除操作
    function del(obj) {
        layer.confirm('确定要删除吗?', function(){
            var id=$(obj).data('order-id');
            $.ajax({
                type : "POST",
                url: "<?php echo U('Admin/Block/delete_form'); ?>",
                data:{form_id:id},
                dataType:'json',
                async:false,
                success: function(data){
                    if(data.status ==1){
                        layer.alert(data.msg, {icon: 1});
                        $('#'+id).remove();
                    }else{
                        layer.alert(data.msg, {icon: 2});
                    }
                },
                error:function(){
                    layer.alert('网络异常，请稍后重试',{icon: 2});
                }
            });
        });
    }
</script>
