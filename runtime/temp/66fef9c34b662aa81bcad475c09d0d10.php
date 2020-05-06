<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:44:"./template/pc/rainbow/cart/ajax_address.html";i:1587634420;}*/ ?>
<i class="sprite_le_ri"></i>
<div class="top_leg p">
    <span class="paragraph fl"><i class="ddd"></i>收货人信息</span>
    <a id="addNewAddress" class="newadd fr" href="javascript:void(0);" onClick="add_edit_address(this);" data-address-id="0">新增收货地址</a>
</div>
<div class="consignee-list p">
    <ul>
        <?php if(is_array($address_list) || $address_list instanceof \think\Collection || $address_list instanceof \think\Paginator): $i = 0; $__LIST__ = $address_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$address): $mod = ($i % 2 );++$i;?>
            <!--默认选中的地址-->
            <?php if($address[is_default] == 1): ?>
                <li class="addressItem" data-address-id="<?php echo $address[address_id]; ?>">
                    <div class="item_select_t curtr fl" data-province-id="<?php echo $address['province']; ?>" data-city-id="<?php echo $address['city']; ?>" data-district-id="<?php echo $address['district']; ?>" data-town-id="<?php echo $address['twon']; ?>" data-longitude="<?php echo $address['longitude']; ?>" data-latitude="<?php echo $address['latitude']; ?>">
                        <span><?php echo $address[consignee]; ?>&nbsp;<?php echo $regionList[$address[province]]; ?></span>
                        <b></b>
                    </div>
                    <div class="addrdetail fl">
                        <span class="addr-name" title="<?php echo $address[consignee]; ?>"><?php echo $address[consignee]; ?></span>
                        <span class="addr-info" title="<?php echo $regionList[$address[province]]; ?> <?php echo $regionList[$address[city]]; ?> <?php echo $regionList[$address[district]]; ?> <?php echo $regionList[$address[twon]]; ?> <?php echo $address[address]; ?>">
                            <?php echo $regionList[$address[province]]; ?> <?php echo $regionList[$address[city]]; ?> <?php echo $regionList[$address[district]]; ?> <?php echo $regionList[$address[twon]]; ?> <?php echo $address[address]; ?>
                        </span>
                        <span class="addr-tel" title="<?php echo $address[mobile]; ?>"><?php echo $address[mobile]; ?></span>
                        <span class="addr-default">默认地址</span>
                    </div>
                    <div class="opbtns_editdel">
                        <a href="javascript:void(0);" onclick="add_edit_address(this);" class="ftx">编辑</a>
                        <a href="javascript:void(0);" onclick="del_address(this);" class="ftx">删除</a>
                    </div>
                </li>

                <?php else: ?>
                <li class="addressItem" data-address-id="<?php echo $address[address_id]; ?>">
                    <div class="item_select_t fl" data-province-id="<?php echo $address['province']; ?>" data-city-id="<?php echo $address['city']; ?>" data-district-id="<?php echo $address['district']; ?>" data-town-id="<?php echo $address['twon']; ?>" data-longitude="<?php echo $address['longitude']; ?>" data-latitude="<?php echo $address['latitude']; ?>">
                        <span><?php echo $address[consignee]; ?>&nbsp;<?php echo $regionList[$address[province]]; ?></span>
                        <b></b>
                    </div>
                    <div class="addrdetail fl">
                        <span class="addr-name" title="<?php echo $address[consignee]; ?>"><?php echo $address[consignee]; ?></span>
                         <span class="addr-info" title="<?php echo $regionList[$address[province]]; ?> <?php echo $regionList[$address[city]]; ?> <?php echo $regionList[$address[district]]; ?> <?php echo $regionList[$address[twon]]; ?> <?php echo $address[address]; ?>">
                            <?php echo $regionList[$address[province]]; ?> <?php echo $regionList[$address[city]]; ?> <?php echo $regionList[$address[district]]; ?> <?php echo $regionList[$address[twon]]; ?> <?php echo $address[address]; ?>
                        </span>
                        <span class="addr-tel" title="<?php echo $address[mobile]; ?>"><?php echo $address[mobile]; ?></span>
                    </div>
                    <div class="opbtns_editdel">
                        <a href="javascript:void(0);" onclick="set_address_default(this);" class="ftx">设为默认地址</a>
                        <a href="javascript:void(0);" onclick="add_edit_address(this);" data-address-id="<?php echo $address[address_id]; ?>" class="ftx">编辑</a>
                        <a href="javascript:void(0);" onclick="del_address(this);" class="ftx">删除</a>
                    </div>
                </li>
            <?php endif; endforeach; endif; else: echo "" ;endif; ?>
    </ul>
</div>
<?php if(count($address_list) > 1): ?>
    <div class="addr-switch">
        <span>更多地址</span>
        <b></b>
    </div>
<?php endif; ?>
<script>
    /**
     * 新增修改收货地址
     */
    function add_edit_address(obj) {
        var url = '';
        var id = $(obj).parents('.addressItem').attr('data-address-id');
        if(typeof(id)=="undefined"){
            id = 0;
        }
        if (typeof(id)=="undefined"){
            url = "/index.php?m=Home&c=User&a=add_address&scene=1&call_back=call_back_fun";	// 新增地址
        }else{
            url = "/index.php?m=Home&c=User&a=edit_address&scene=1&call_back=call_back_fun&id=" + id;
        }
        layer.open({
            type: 2,
            title: '添加收货地址',
            shadeClose: true,
            shade: 0.8,
            area: ['880px', '580px'],
            content: url
        });
    }
    // 添加修改收货地址回调函数
    function call_back_fun(v) {
        layer.closeAll(); // 关闭窗口
        ajax_address(); // 刷新收货地址
    }
    /**
     * 删除收货地址
     * @param obj
     */
    function del_address(obj) {
        var id = $(obj).parents('.addressItem').attr('data-address-id');
        layer.confirm("确定要删除吗?", {
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    layer.closeAll();
                    $.ajax({
                        url: "/index.php?m=Home&c=User&a=del_address&id=" + id,
                        success: function (data) {
                            window.parent.ajax_address(); // 刷新收货地址
                        }
                    });
                }, function (index) {
                    layer.close(index);
                }
        );
    }
    //设置默认地址
    function set_address_default(obj){
        var id = $(obj).parents('.addressItem').attr('data-address-id');
        $.ajax({
            url: "/index.php?m=Home&c=User&a=setAddressDefault",
            type: 'post',
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                if (data.status == 1) {
                    window.parent.ajax_address(); // 刷新收货地址
                } else {
                    layer.msg(data.msg, {icon: 2});
                }
            }
        });
    }

</script>