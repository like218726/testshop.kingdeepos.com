<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:58:"./application/seller/new/promotion/group_select_goods.html";i:1587634378;}*/ ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>选择商品</title>
    <link href="/public/static/css/base.css" rel="stylesheet" type="text/css">
    <link href="/public/static/css/seller_center.css" rel="stylesheet" type="text/css">
    <link href="/public/static/font/css/font-awesome.min.css" rel="stylesheet" />
    <!--[if IE 7]>
    <link rel="stylesheet" href="/public/static/font/css/font-awesome-ie7.min.css">
    <![endif]-->
    <script type="text/javascript" src="/public/static/js/jquery.js"></script>
    <script type="text/javascript" src="/public/static/js/waypoints.js"></script>
    <script type="text/javascript" src="/public/static/js/jquery-ui/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/public/static/js/jquery.validation.min.js"></script>
    <script type="text/javascript" src="/public/static/js/layer/layer.js"></script>
    <script type="text/javascript" src="/public/js/global.js"></script>
    <script type="text/javascript" src="/public/js/myFormValidate.js"></script>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/public/static/js/html5shiv.js"></script>
    <script src="/public/static/js/respond.min.js"></script>
    <![endif]-->
    <style>
        .search-form {
            border-top: solid 1px #E6E6E6;
            border-bottom-width: 1px;
            border-bottom-style: solid;
            border-bottom-color: rgb(230, 230, 230);
        }
        a.ncap-btn-dis {
            font: normal 12px/20px "microsoft yahei";
            text-decoration: none;
            text-align: center;
            vertical-align: middle;
            display: inline-block;
            height: 20px;
            padding: 2px 9px;
            border: solid 1px;
            border-top-color: currentcolor;
            border-right-color: currentcolor;
            border-bottom-color: currentcolor;
            border-left-color: currentcolor;
            border-radius: 3px;
            background-color: #c5c5c5;
            color: rgb(119, 119, 119);
            border-color: #c5c5c5;
            cursor: auto;
            text-decoration: none;
        }
    </style>
</head>
<body style="min-width:0px;">
<div class="ncsc-layout wrapper" style="width: 1000px;margin: 0px;">
    <div id="layoutRight" class="ncsc-layout-right">
        <div class="main-content" id="mainContent">
            <form id="search-form2" method="get" action="<?php echo U('Promotion/search_goods',array('tpl'=>'group_select_goods', 'prom_id'=>2)); ?>">
                <input name="prom_id" type="hidden" value="<?php echo \think\Request::instance()->param('prom_id'); ?>">
                <input name="nospec" type="hidden" value="<?php echo \think\Request::instance()->param('nospec'); ?>">
                <input name="prom_type" type="hidden" value="<?php echo $prom_type; ?>">
                <table class="search-form">
                    <tr>
                        <td class="w100">
                            <select name="cat_id" id="cat_id" class="select">
                                <option value="">所有分类</option>
                                <?php if(is_array($categoryList) || $categoryList instanceof \think\Collection || $categoryList instanceof \think\Paginator): if( count($categoryList)==0 ) : echo "" ;else: foreach($categoryList as $k=>$v): if(in_array($v[id],$bind_class_id)  or $store['bind_all_gc'] == 1): ?>
                                        <option value="<?php echo $v['id']; ?>" <?php if($v[id] == $cat_id): ?>selected<?php endif; ?>><?php echo $v['name']; ?></option>
                                    <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </td>

                        <td class="w100">
                            <select name="brand_id" id="brand_id" class="select">
                                <option value="">所有品牌</option>
                                <?php if(is_array($brandList) || $brandList instanceof \think\Collection || $brandList instanceof \think\Paginator): if( count($brandList)==0 ) : echo "" ;else: foreach($brandList as $k=>$v): ?>
                                    <option value="<?php echo $v['id']; ?>" <?php if($v[id] == $brand_id): ?>selected<?php endif; ?>><?php echo $v['name']; ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </td>
                        <th class="w70">新品/推荐</th>
                        <td class="w50">
                            <select name="intro" class="select">
                                <option value="0">全部</option>
                                <option value="is_new">新品</option>
                                <option value="is_recommend">推荐</option>
                            </select>
                        </td>
                        <th class="w40">关键词</th>
                        <td class="w100"><input style="width: 90px;" class="text" type="text" name="keywords" value="<?php echo $keywords; ?>" placeholder="搜索词"/></td>
                        <td class="w70 tc"><label class="submit-border"><input type="submit" class="submit" value="搜索" /></label></td>
                    </tr>
                </table>
            </form>
            <table class="ncsc-default-table">
                <thead>
                <tr>
                    <th class="w20"></th>
                    <th class="w50">选择</th>
                    <th class="w200 tl">商品名称</th>
                    <th class="w100">价格</th>
                    <th class="w100">库存</th>
                    <th class="w100">商品ID</th>
                </tr>
                </thead>
                <tbody>
                <?php if(is_array($goodsList) || $goodsList instanceof \think\Collection || $goodsList instanceof \think\Paginator): $i = 0; $__LIST__ = $goodsList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?>
                    <tr class="bd-line">
                        <td></td>
                        <td>
                            <input type="radio" name="goods_id" data-img="<?php echo goods_thum_images($list['goods_id'],160,160); ?>" onclick="select_goods(this);"
                                   data-id="<?php echo $list['goods_id']; ?>" data-name="<?php echo $list['goods_name']; ?>" data-count="<?php echo $list['store_count']; ?>"
                                   data-price="<?php echo $list['shop_price']; ?>" <?php if($list['goods_id'] == \think\Request::instance()->param('goods_id')): ?>checked='checked'<?php endif; ?>/>
                        </td>
                        <td class="tl"><?php echo $list['goods_name']; ?></td>
                        <td><?php echo $list['shop_price']; ?></td>
                        <td><?php echo $list['store_count']; ?></td>
                        <td class="nscs-table-handle">
                            <span>
                                <!--<a onclick="$(this).parent().parent().parent().remove();" class="btn-grapefruit"><i class="icon-trash"></i><p>删除</p></a>-->
                                <a class="btn-grapefruit"><i class="icon-trash"></i><p><?php echo $list['goods_id']; ?></p></a>
                            </span>
                        </td>
                    </tr>
                    <?php if(!(empty($list[specGoodsPrice]) || (($list[specGoodsPrice] instanceof \think\Collection || $list[specGoodsPrice] instanceof \think\Paginator ) && $list[specGoodsPrice]->isEmpty()))): ?>
                        <tr class="bd-line" style="" id="spec_goods_id_<?php echo $list['goods_id']; ?>">
                            <td></td>
                            <td></td>
                            <td class="tl" colspan=3>
                                <?php if(is_array($list[specGoodsPrice]) || $list[specGoodsPrice] instanceof \think\Collection || $list[specGoodsPrice] instanceof \think\Paginator): $i = 0; $__LIST__ = $list[specGoodsPrice];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$spec): $mod = ($i % 2 );++$i;?>
                                    <a data-item-id="<?php echo $spec['item_id']; ?>"
                                       data-key-name="<?php echo $spec['key_name']; ?>" data-store-count="<?php echo $spec['store_count']; ?>" data-price="<?php echo $spec['price']; ?>" data-spec-img="<?php echo $spec['spec_img']; ?>"
                                       title="<?php echo $spec['key_name']; ?>" class="<?php if($spec['prom_id'] == 0): ?>ncbtn specBtn <?php else: ?>ncap-btn-dis<?php endif; ?>"><?php echo $spec['key_name']; ?></a>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </td>
                            <td class="nscs-table-handle">
                            </td>
                        </tr>
                    <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="20">
                        <?php echo $page; ?>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $("input[type='radio']:checked").each(function(i,o){
            var goods_id = $(this).data('id');
            $('#spec_goods_id_'+goods_id).show();
        })
    });
    //商品对象
    function GoodsItem(goods_id, goods_name, store_count, goods_price ,goods_image,spec) {
        this.goods_id = goods_id;
        this.goods_name = goods_name;
        this.store_count = store_count;
        this.goods_price = goods_price;
        this.goods_image = goods_image;
        this.spec = spec;
    }
    //商品对象
    function GoodsSpecItem(item_id, key_name, store_count, price ,spec_img) {
        this.item_id = item_id;
        this.key_name = key_name;
        this.store_count = store_count;
        this.price = price;
        this.spec_img = spec_img;
    }

    //规格按钮点击事件
    $(function () {
        $(document).on("click", '.specBtn', function (e) {
            $(this).hasClass('ncbtn-aqua')?$(this).removeClass('ncbtn-aqua'):$(this).addClass('ncbtn-aqua');
        })
    })

    function select_goods(obj)
    {
        var input = $("input[type='radio']:checked");
        if (input.length == 0) {
            layer.alert('请选择商品', {icon: 2}); //alert('请选择商品');
            return false;
        }
        var goods_id = input.data('id');
        var spec = $('#spec_goods_id_'+goods_id);
        var goodsItem = null;
        if(spec.length == 0){
            goodsItem = new GoodsItem(input.data('id'), input.data('name'),input.data('count'), input.data('price'), input.data('img'), null);
        }else{
            var spec_a = spec.find('.ncbtn-aqua');
            if(spec_a.length == 0){
            	$(obj).attr("checked",false);
                layer.alert('请选择要参与活动的商品规格', {icon: 2});
                return false;
            }else{
                var goodsSpecItemArr = new Array();
                spec_a.each(function (index,item) {
                    var spec_img = $(item).data('spec-img');
                    if(spec_img == ''){
                        spec_img = input.data('img');
                    }
                    var goodsSpecItem = new GoodsSpecItem($(item).data('item-id'),$(item).data('key-name'),$(item).data('store-count'),$(item).data('price'),$(item).data('cost-price'),spec_img);
                    goodsSpecItemArr.push(goodsSpecItem);
                })
                goodsItem = new GoodsItem(input.data('id'), input.data('name'), input.data('count'),input.data('price'), input.data('img'), goodsSpecItemArr);
            }
        }
        window.parent.call_back(goodsItem);
    }
</script>
</body>
</html>
