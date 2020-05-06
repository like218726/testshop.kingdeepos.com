<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:49:"./application/seller/new/goods/ajaxGoodsList.html";i:1587634376;}*/ ?>
<table class="ncsc-default-table" data-goods-examine="<?php echo $store['goods_examine']; ?>">
    <thead>
    <tr nc_type="table_header">
        <th class="w30"><a href="javascript:sort('goods_id');">ID</a></th>
        <th class="w50">&nbsp;</th>
        <th class="w250">商品名称</th>
        <!--<th class="w150"><a href="javascript:sort('cat_id1');">分类</a></th>-->
        <th class="w80"><a href="javascript:sort('shop_price');">价格</a></th>
        <th class="w30"><a >上架</a></th>
        <th class="w30"><a >新品</a></th>
        <th class="w30"><a >热卖</a></th>
        <th class="w30"><a >推荐</a></th>
        <th class="w30"><a href="javascript:sort('store_count');">库存</a></th>
        <th class="w80"><a href="javascript:sort('on_time');">上架时间</a></th>
        <th class="w30"><a href="javascript:sort('sort');">排序</a></th>
        <th class="w120">操作</th>
    </tr>
    </thead>
    <tbody>
    <?php if(empty($goodsList) || (($goodsList instanceof \think\Collection || $goodsList instanceof \think\Paginator ) && $goodsList->isEmpty())): ?>
        <tr>
            <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span>暂无符合条件的数据记录</span></div></td>
        </tr>
    <?php else: if(is_array($goodsList) || $goodsList instanceof \think\Collection || $goodsList instanceof \think\Paginator): $i = 0; $__LIST__ = $goodsList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?>
        <tr class="bd-line" data-goods-id="<?php echo $list['goods_id']; ?>">
            <td><?php echo $list['goods_id']; ?></td>
            <td>
                <div class="pic-thumb">
                    <a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$list['goods_id'])); ?>" target="_blank">
                        <img style="width:32px;height:32px" src="<?php echo goods_thum_images($list['goods_id'],50,50); ?>" />
                    </a>
                </div>
            </td>
            <td class="tl">
                <dl class="goods-name">
                    <dt style="max-width: 450px !important;">
                    <?php if($list[is_virtual] == 1): ?><span class="type-virtual" title="虚拟兑换商品">虚拟</span><?php endif; ?>
                        <a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$list['goods_id'])); ?>" target="_blank"><?php echo getSubstr($list['goods_name'],0,33); ?></a></dt>
                    <dd>商品货号：<?php echo $list['goods_sn']; ?></dd>
                    <dd class="serve">
                        <?php if($list['is_recommend'] == 1): ?>
                            <span class="open" title="平台推荐商品"><i class="commend">荐</i></span>
                        <?php endif; ?>
                        <a href="<?php echo U('Mobile/Goods/goodsInfo',array('id'=> $list['goods_id'])); ?>"><span title="手机端商品详情"><i class="icon-tablet"></i></span></a>
                        <a onclick="ClearGoodsHtml('<?php echo $list[goods_id]; ?>')" title="清除静态缓存页面"><span title="清除静态缓存页面"><i class="icon-wrench"></i></span></a>
                        <a onclick="ClearGoodsThumb('<?php echo $list[goods_id]; ?>')" title="清除缩略图缓存"><span title="清除缩略图缓存"><i class="icon-picture"></i></span></a>
                    </dd>
                </dl>
            </td>
            <!--<td><span><?php echo $catList[$list[cat_id1]][name]; ?></span></td>-->
            <td><span>&yen;<?php echo $list['shop_price']; ?></span></td>
            <td><img width="20" height="20" src="/public/images/yes.png" class="is_on_sale"/></td>
            <td>
                <?php if($list['is_new'] == 1): ?>
                    <img width="20" height="20" src="/public/images/yes.png" onclick="changeTableVal2('goods','goods_id',<?php echo $list[goods_id]; ?>,'is_new',this)"/>
                <?php else: ?>
                    <img src="/public/images/cancel.png" onclick="changeTableVal2('goods','goods_id',<?php echo $list[goods_id]; ?>,'is_new',this)" width="20" height="20">
                <?php endif; ?>
            </td>
            <td>
                <?php if($list['is_hot'] == 1): ?>
                    <img width="20" height="20" src="/public/images/yes.png" onclick="changeTableVal2('goods','goods_id',<?php echo $list[goods_id]; ?>,'is_hot',this)"/>
                <?php else: ?>
                    <img src="/public/images/cancel.png" onclick="changeTableVal2('goods','goods_id',<?php echo $list[goods_id]; ?>,'is_hot',this)" width="20" height="20">
                <?php endif; ?>
            </td>
            <td>
                <?php if($list['is_recommend'] == 1): ?>
                    <img width="20" height="20" src="/public/images/yes.png" onclick="changeTableVal2('goods','goods_id',<?php echo $list[goods_id]; ?>,'is_recommend',this)"/>
                <?php else: ?>
                    <img src="/public/images/cancel.png" onclick="changeTableVal2('goods','goods_id',<?php echo $list[goods_id]; ?>,'is_recommend',this)" width="20" height="20">
                <?php endif; ?>
            </td>
            <td><span  style="<?php if($list['store_count'] < $store_warning_storage): ?>color: red<?php endif; ?>"><?php echo $list['store_count']; ?></span></td>
            <td><?php echo date('Y-m-d',$list['on_time']); ?></td>
            <td>
                <input class="txt-cen" type="text" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" onpaste="this.value=this.value.replace(/[^\d]/g,'')" onchange="updateSort2('goods','goods_id','<?php echo $list['goods_id']; ?>','sort',this)" size="4" value="<?php echo $list['sort']; ?>" />
            </td>
            <td class="nscs-table-handle">
                <span><a href="<?php echo U('Goods/addEditGoods',array('goods_id'=>$list['goods_id'])); ?>" class="btn-bluejeans"><i class="icon-edit"></i><p>编辑</p></a></span>
                <span><a href="javascript:void(0);" onclick="del('<?php echo $list[goods_id]; ?>')" class="btn-grapefruit"><i class="icon-trash"></i><p>删除</p></a></span>
            </td>
        </tr>
    <?php endforeach; endif; else: echo "" ;endif; endif; ?>
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
    // 点击分页触发的事件
    $(".pagination  a").click(function(){
        cur_page = $(this).data('p');
        ajax_get_table('search-form2',cur_page);
    });

    /*
     * 清除静态页面缓存
     */
    function ClearGoodsHtml(goods_id)
    {
        $.ajax({
            type:'GET',
            url:"<?php echo U('Seller/Admin/ClearGoodsHtml'); ?>",
            data:{goods_id:goods_id},
            dataType:'json',
            success:function(data){
                if(data.status == 1){
                    layer.msg(data.msg, {icon: 1});
                }else{
                    layer.msg(data.msg, {icon: 2});
                }
            }
        });
    }
    /*
     * 清除商品缩列图缓存
     */
    function ClearGoodsThumb(goods_id)
    {
        $.ajax({
            type:'GET',
            url:"<?php echo U('Seller/Admin/ClearGoodsThumb'); ?>",
            data:{goods_id:goods_id},
            dataType:'json',
            success:function(data){
                if(data.status == 1){
                    layer.msg(data.msg, {icon: 1});
                }else{
                    layer.msg(data.msg, {icon: 2});
                }
            }
        });
    }
</script>