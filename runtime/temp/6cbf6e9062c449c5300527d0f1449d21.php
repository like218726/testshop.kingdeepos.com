<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:50:"./application/admin/view/distribut/goods_list.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
<!doctype html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<!-- Apple devices fullscreen -->
<meta name="apple-mobile-web-app-capable" content="yes">
<!-- Apple devices fullscreen -->
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<link href="/public/static/css/main.css" rel="stylesheet" type="text/css">
<link href="/public/static/css/page.css" rel="stylesheet" type="text/css">
<link href="/public/static/font/css/font-awesome.min.css" rel="stylesheet" />
<!--[if IE 7]>
  <link rel="stylesheet" href="/public/static/font/css/font-awesome-ie7.min.css">
<![endif]-->
<link href="/public/static/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
<link href="/public/static/js/perfect-scrollbar.min.css" rel="stylesheet" type="text/css"/>
<style type="text/css">html, body { overflow: visible;}</style>
<script type="text/javascript" src="/public/static/js/jquery.js"></script>
<script type="text/javascript" src="/public/static/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="/public/static/js/layer/layer.js"></script><!-- 弹窗js 参考文档 http://layer.layui.com/-->
<script type="text/javascript" src="/public/static/js/admin.js"></script>
<script type="text/javascript" src="/public/static/js/jquery.validation.min.js"></script>
<script type="text/javascript" src="/public/static/js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="/public/static/js/jquery.mousewheel.js"></script>
<script src="/public/js/myFormValidate.js"></script>
<script src="/public/js/myAjax2.js"></script>
<script src="/public/js/global.js"></script>
<script src="/public/static/js/layer/laydate/laydate.js"></script>
<script type="text/javascript">
function delfunc(obj){
	layer.confirm('确认删除？', {
		  btn: ['确定','取消'] //按钮
		}, function(){
			$.ajax({
				type : 'post',
				url : $(obj).attr('data-url'),
				data : {act:'del',del_id:$(obj).attr('data-id')},
				dataType : 'json',
				success : function(data){
					layer.closeAll();
					if(data.status==1){
                        $(obj).parent().parent().parent().html('');
						layer.msg('操作成功', {icon: 1});
					}else{
						layer.msg('删除失败', {icon: 2,time: 2000});
					}
				}
			})
		}, function(index){
			layer.close(index);
		}
	);
}

function delAll(obj,name){
	var a = [];
	$('input[name*='+name+']').each(function(i,o){
		if($(o).is(':checked')){
			a.push($(o).val());
		}
	})
	if(a.length == 0){
		layer.alert('请选择删除项', {icon: 2});
		return;
	}
	layer.confirm('确认删除？', {btn: ['确定','取消'] }, function(){
			$.ajax({
				type : 'get',
				url : $(obj).attr('data-url'),
				data : {act:'del',del_id:a},
				dataType : 'json',
				success : function(data){
					layer.closeAll();
					if(data == 1){
						layer.msg('操作成功', {icon: 1});
						$('input[name*='+name+']').each(function(i,o){
							if($(o).is(':checked')){
								$(o).parent().parent().remove();
							}
						})
					}else{
						layer.msg(data, {icon: 2,time: 2000});
					}
				}
			})
		}, function(index){
			layer.close(index);
			return false;// 取消
		}
	);	
}

//表格列表全选反选
$(document).ready(function(){
	$('.hDivBox .sign').click(function(){
	    var sign = $('#flexigrid > table>tbody>tr');
	   if($(this).parent().hasClass('trSelected')){
	       sign.each(function(){
	           $(this).removeClass('trSelected');
	       });
	       $(this).parent().removeClass('trSelected');
	   }else{
	       sign.each(function(){
	           $(this).addClass('trSelected');
	       });
	       $(this).parent().addClass('trSelected');
	   }
	})
});

//获取选中项
function getSelected(){
	var selectobj = $('.trSelected');
	var selectval = [];
    if(selectobj.length > 0){
        selectobj.each(function(){
        	selectval.push($(this).attr('data-id'));
        });
    }
    return selectval;
}

function selectAll(name,obj){
    $('input[name*='+name+']').prop('checked', $(obj).checked);
}   

function get_help(obj){

	window.open("http://www.tp-shop.cn/");
	return false;

    layer.open({
        type: 2,
        title: '帮助手册',
        shadeClose: true,
        shade: 0.3,
        area: ['70%', '80%'],
        content: $(obj).attr('data-url'), 
    });
}

//
///**
// * 全选
// * @param obj
// */
//function checkAllSign(obj){
//    $(obj).toggleClass('trSelected');
//    if($(obj).hasClass('trSelected')){
//        $('#flexigrid > table>tbody >tr').addClass('trSelected');
//    }else{
//        $('#flexigrid > table>tbody >tr').removeClass('trSelected');
//    }
//}
/**
 * 批量公共操作（删，改）
 * @returns {boolean}
 */
function publicHandleAll(type){
    var ids = '';
    $('#flexigrid .trSelected').each(function(i,o){
//            ids.push($(o).data('id'));
        ids += $(o).data('id')+',';
    });
    if(ids == ''){
        layer.msg('至少选择一项', {icon: 2, time: 2000});
        return false;
    }
    publicHandle(ids,type); //调用删除函数
}
/**
 * 公共操作（删，改）
 * @param type
 * @returns {boolean}
 */
function publicHandle(ids,handle_type){
    layer.confirm('确认当前操作？', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                // 确定
                $.ajax({
                    url: $('#flexigrid').data('url'),
                    type:'post',
                    data:{ids:ids,type:handle_type},
                    dataType:'JSON',
                    success: function (data) {
                        layer.closeAll();
                        if (data.status == 1){
                            layer.msg(data.msg, {icon: 1, time: 2000},function(){
                                location.href = data.url;
                            });
                        }else{
                            layer.msg(data.msg, {icon: 2, time: 3000});
                        }
                    }
                });
            }, function (index) {
                layer.close(index);
            }
    );
}
</script>
</head>
<script type="text/javascript" src="/public/static/js/perfect-scrollbar.min.js"></script>
<body style="background-color: rgb(255, 255, 255); overflow: auto; cursor: default; -moz-user-select: inherit;">
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>分销商品管理</h3>
        <h5>商城分销商品管理</h5>
      </div>
    </div>
  </div>
  <!-- 操作说明 -->
  <div id="explanation" class="explanation" style="color: rgb(44, 188, 163); background-color: rgb(237, 251, 248); width: 99%; height: 100%;">
    <div id="checkZoom" class="title"><i class="fa fa-lightbulb-o"></i>
      <h4 title="提示相关设置操作时应注意的要点">操作提示</h4>
      <span title="收起提示" id="explanationZoom" style="display: block;"></span>
    </div>
    <ul>
		<li>参与分销的商品在此列表显示</li>
	</ul>
  </div>
    <div class="flexigrid">
        <div class="mDiv">
            <div class="ftitle">
                <h3>分销商品列表</h3>
                <h5>(共<?php echo $pager->totalRows; ?>条记录)</h5>
            </div>
            <div title="刷新数据" class="pReload"><i class="fa fa-refresh"></i></div>
            <form action="<?php echo U('Distribut/goods_list'); ?>" id="search-form2" class="navbar-form form-inline" method="get">
                <div class="sDiv">
                    <div class="sDiv2" style="margin-right: 10px;border: none;">
                        <select name="cat_id1" id="cat_id">
                            <option value="">所有分类</option>
                            <?php if(is_array($categoryList) || $categoryList instanceof \think\Collection || $categoryList instanceof \think\Paginator): if( count($categoryList)==0 ) : echo "" ;else: foreach($categoryList as $k=>$v): ?>
                                <option value="<?php echo $v['id']; ?>" <?php if(\think\Request::instance()->param('cat_id1') == $v['id']): ?>selected<?php endif; ?>> <?php echo $v['name']; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </div>
                    <div class="sDiv2" style="margin-right: 10px;border: none;">
                        <select name="brand_id" id="brand_id">
                            <option value="">所有品牌</option>
                            <?php if(is_array($brandList) || $brandList instanceof \think\Collection || $brandList instanceof \think\Paginator): if( count($brandList)==0 ) : echo "" ;else: foreach($brandList as $k=>$v): ?>
                                <option value="<?php echo $v['id']; ?>" <?php if(\think\Request::instance()->param('brand_id') == $v['id']): ?>selected<?php endif; ?>><?php echo $v['name']; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </div>
                    <div class="sDiv2">
                        <input type="text" size="30" name="key_word" class="qsbox" value="<?php echo \think\Request::instance()->param('key_word'); ?>" placeholder="商品名称或者货号...">
                        <input type="submit" class="btn" value="搜索">
                    </div>
                </div>
            </form>
        </div>
        <div class="hDiv">
          <div class="hDivBox">
            <table cellspacing="0" cellpadding="0">
              <thead>
                <tr>
                  <th class="sign" axis="col6">
                    <div style="width: 24px;"><i class="ico-check"></i></div>
                  </th>
                  <th align="left" abbr="article_title" axis="col6" class="">
                    <div style="text-align: left; width:50px;" class="">ID</div>
                  </th>
                  <th align="left" abbr="ac_id" axis="col4" class="">
                    <div style="text-align: left; width: 300px;" class="">商品名称</div>
                  </th>
                  <th align="center" abbr="article_show" axis="col6" class="">
                    <div style="text-align: center; width: 50px;" class="">销量</div>
                  </th>
                  <th align="left" abbr="article_time" axis="col6" class="">
                    <div style="text-align: left; width: 200px;" class="" >分类</div>
                  </th>
                  <th align="center" abbr="article_time" axis="col6" class="">
                    <div style="text-align: center; width: 50px;" class="" >价格</div>
                  </th>
                  <th align="center" abbr="article_time" axis="col6" class="">
                    <div style="text-align: center; width: 50px;" class="">库存</div>
                  </th>
                  <th align="center" abbr="article_time" axis="col6" class="">
                    <div style="text-align: center; width: 50px;" class="">分成金额</div>
                  </th>
                  <th align="center" abbr="article_time" axis="col6" class="">
                    <div style="text-align: center; width: 80px;" class="">占商品价格比</div>
                  </th>
                  <th align="center" abbr="article_time" axis="col6" class="">
                    <div style="text-align: center; width: 50px;">操作</div>
                  </th>
                  <th style="width:100%" axis="col7">
                    <div></div>
                  </th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
        <div class="bDiv" style="height: auto;">
         <!--ajax 返回 -->
            <table>
                <tbody>
                <?php if(is_array($goodsList) || $goodsList instanceof \think\Collection || $goodsList instanceof \think\Paginator): $i = 0; $__LIST__ = $goodsList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?>
                    <tr>
                        <td class="sign" axis="col6">
                            <div style="width: 24px;"><i class="ico-check"></i></div>
                        </td>
                        <td align="center" axis="col0">
                            <div style="width: 50px;"><?php echo $list['goods_id']; ?></div>
                        </td>
                        <td align="center" axis="col0">
                            <div style="text-align: left; width: 300px;"><?php echo getSubstr($list['goods_name'],0,33); ?></div>
                        </td>
                        <td align="center" axis="col0">
                            <div style="text-align: center; width: 50px;"><?php echo $list['sales_sum']; ?></div>
                        </td>
                        <td align="center" axis="col0">
                            <div style="text-align: left; width: 200px;"><?php echo $catList[$list[cat_id1]][name]; ?></div>
                        </td>
                        <td align="center" axis="col0">
                            <div style="text-align: center; width: 50px;"><?php echo $list['shop_price']; ?></div>
                        </td>
                        <td align="center" axis="col0">
                            <div style="text-align: center; width: 50px;"><?php echo $list['store_count']; ?></div>
                        </td>
                        <td align="center" axis="col0">
                            <div style="text-align: center; width: 50px;"><?php echo $list['distribut']; ?></div>
                        </td>
                        <td align="center" axis="col0">
                            <div style="text-align: center; width: 80px;"><?php echo round($list['distribut']/$list['shop_price']*100,2); ?>%</div>
                        </td>
                        <td align="center" class="handle">
                            <div style="text-align: center; width: 170px; max-width:170px;">
                                <a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$list['goods_id'])); ?>" target="_blank" class="btn blue"><i class="fa fa-search"></i>查看</a>
                                <a class="btn red"  href="javascript:void(0)" onclick="del('<?php echo $list[goods_id]; ?>')"><i class="fa fa-trash-o"></i>删除</a>
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
          <?php echo $page; ?>
    </div>
</div>
<script>
    $(document).ready(function(){
        // 表格行点击选中切换
        $('#flexigrid > table>tbody >tr').click(function(){
            $(this).toggleClass('trSelected');
        });

        // 点击刷新数据
        $('.fa-refresh').click(function(){
            location.href = location.href;
        });
    });
    // 删除操作
    function del(id) {
        layer.confirm('确定要删除吗?', {
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    // 确定
                    $.ajax({
                        url: "/index.php?m=Admin&c=Distribut&a=delGoods&id=" + id,
                        dataType:'JSON',
                        success: function (data) {
                            layer.closeAll();
                            if (data.status == 1){
                                layer.msg(data.msg, {icon: 1, time: 1000},function () {
                                    window.location.reload()
                                });
                            }else{
                                layer.msg(data.msg, {icon: 2, time: 1000});
                            }
                        }
                    });
                }, function (index) {
                    layer.close(index);
                }
        );
    }
</script>
</body>
</html>