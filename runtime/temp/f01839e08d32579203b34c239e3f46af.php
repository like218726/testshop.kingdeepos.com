<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:51:"./application/admin/view/team/found_order_list.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<script type="text/javascript" src="/public/static/js/layer/laydate/laydate.js"></script>
<script type="text/javascript" src="/public/static/js/perfect-scrollbar.min.js"></script>

<body style="background-color: rgb(255, 255, 255); overflow: auto; cursor: default; -moz-user-select: inherit;">
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>拼团列表</h3>
                <h5>商城拼团列表查询及管理</h5>
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
            <li>点击查看操作将显示订单（包括订单物品）的详细信息</li>
            <li>点击取消操作可以取消订单（在线支付但未付款的订单和货到付款但未发货的订单）</li>
            <li>如果平台已确认收到买家的付款，但系统支付状态并未变更，可以点击收到货款操作(仅限于下单后7日内可更改收款状态)，并填写相关信息后更改订单支付状态</li>
        </ul>
    </div>
    <div class="flexigrid">
        <div class="mDiv">
            <div class="ftitle">
                <h3>拼团列表</h3>
                <h5>(共<?php echo $page->totalRows; ?>条记录)</h5>
            </div>
            <div title="刷新数据" class="pReload"><i class="fa fa-refresh"></i></div>
            <form class="navbar-form form-inline" method="get" action="<?php echo U('Admin/Team/found_order_list'); ?>" name="search-form2" id="search-form2">
                <!--用于继续检索该团购活动下面的订单-->
                <input type="hidden" value="<?php echo $_GET['team_id']; ?>" name="team_id"/>
                <!--用于查看结算统计 包含了哪些订单-->
                <!--<input type="hidden" value="<?php echo $_GET['order_statis_id']; ?>" name="order_statis_id"/>-->
                <div class="sDiv">
                    <div class="sDiv2">
                        <input type="text" size="30" id="add_time_begin" name="add_time_begin" value="<?php echo $start_time; ?>" class="qsbox" placeholder="开团时间检索起始">
                    </div>
                    <div class="sDiv2">
                        <input type="text" size="30" id="add_time_end" name="add_time_end" value="<?php echo $end_time; ?>" class="qsbox" placeholder="开团时间检索截止">
                    </div>
                    <div class="sDiv2">
                        <select name="status" class="select sDiv3" style="margin-right:5px;margin-left:5px">
                            <option value="">拼团状态</option>
                            <option value="1" <?php if(\think\Request::instance()->param('status') == 1): ?>selected = "selected"<?php endif; ?>>待成团</option>
                            <option value="2" <?php if(\think\Request::instance()->param('status') == 2): ?>selected = "selected"<?php endif; ?>>已成团</option>
                            <option value="3" <?php if(\think\Request::instance()->param('status') == 3): ?>selected = "selected"<?php endif; ?>>未成团</option>
                        </select>
                    </div>
                    <div class="sDiv2">
                        <select name="key_type" class="select">
                            <option value="store_name">店铺名称</option>
                            <option value="consignee">收货人</option>
                            <option value="order_sn">订单编号</option>
                        </select>
                    </div>
                    <div class="sDiv2">
                        <input type="text" size="30" name="keywords" class="qsbox" placeholder="搜索相关数据...">
                    </div>
                    <div class="sDiv2">
                        <input type="submit" class="btn" value="搜索">
                    </div>
                </div>
            </form>
        </div>
        <div class="hDiv">
            <div class="hDivBox" id="ajax_return">
                <table cellspacing="0" cellpadding="0">
                    <thead>
                    <tr>
                        <th class="sign" axis="col0">
                            <div style="width: 24px;"><i class="ico-check"></i></div>
                        </th>
                        <th align="left" abbr="order_sn" axis="col3" class="">
                            <div style="text-align: left; width: 130px;" class="">订单编号</div>
                        </th>
                        <th align="left" abbr="consignee" axis="col4" class="">
                            <div style="text-align: left; width: 120px;" class="">收货人</div>
                        </th>
                        <th align="center" abbr="article_show" axis="col5" class="">
                            <div style="text-align: center; width: 60px;" class="">总金额</div>
                        </th>
                        <th align="center" abbr="article_time" axis="col6" class="">
                            <div style="text-align: center; width: 60px;" class="">应付金额</div>
                        </th>
                        <th align="center" abbr="article_time" axis="col6" class="">
                            <div style="text-align: center; width: 60px;" class="">拼团状态</div>
                        </th>
                        <th align="center" abbr="article_time" axis="col6" class="">
                            <div style="text-align: center; width: 60px;" class="">订单状态</div>
                        </th>
                        <th align="center" abbr="article_time" axis="col6" class="">
                            <div style="text-align: center; width: 60px;" class="">支付状态</div>
                        </th>
                        <th align="center" abbr="article_time" axis="col6" class="">
                            <div style="text-align: center; width: 60px;" class="">发货状态</div>
                        </th>
                        <th align="center" abbr="article_time" axis="col6" class="">
                            <div style="text-align: center; width: 60px;" class="">支付方式</div>
                        </th>
                        <th align="center" abbr="article_time" axis="col6" class="">
                            <div style="text-align: center; width: 60px;" class="">配送方式</div>
                        </th>
                        <th align="center" abbr="article_time" axis="col6" class="">
                            <div style="text-align: center; width: 120px;" class="">下单时间</div>
                        </th>
                        <th align="center" abbr="article_time" axis="col6" class="">
                            <div style="text-align: center; width: 160px;" class="">店铺</div>
                        </th>
                        <th align="center" axis="col1" class="handle">
                            <div style="text-align: center; width: 200px;">操作</div>
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
            <div id="flexigrid" cellpadding="0" cellspacing="0" border="0">
                <table>
                    <tbody>
                    <?php if(empty($teamFound) || (($teamFound instanceof \think\Collection || $teamFound instanceof \think\Paginator ) && $teamFound->isEmpty())): ?>
                        <tr data-id="0">
                            <td class="no-data" align="center" axis="col0" colspan="50">
                                <i class="fa fa-exclamation-circle"></i>没有符合条件的记录
                            </td>
                        </tr>
                    <?php else: if(is_array($teamFound) || $teamFound instanceof \think\Collection || $teamFound instanceof \think\Paginator): $i = 0; $__LIST__ = $teamFound;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$found): $mod = ($i % 2 );++$i;?>
                            <tr>
                                <td class="sign" axis="col0">
                                    <div style="width: 24px;"><i class="ico-check"></i></div>
                                </td>
                                <td align="left" abbr="order_sn" axis="col3" class="">
                                    <div style="text-align: left; width: 130px;" class=""><?php echo $found['order']['order_sn']; ?></div>
                                </td>
                                <td align="left" abbr="consignee" axis="col4" class="">
                                    <div style="text-align: left; width: 120px;" class=""><?php echo $found['order']['consignee']; ?>:<?php echo $found['order']['mobile']; ?></div>
                                </td>
                                <td align="center" abbr="article_show" axis="col5" class="">
                                    <div style="text-align: center; width: 60px;" class=""><?php echo $found['order']['goods_price']; ?></div>
                                </td>
                                <td align="center" abbr="article_time" axis="col6" class="">
                                    <div style="text-align: center; width: 60px;" class=""><?php echo $found['order']['order_amount']; ?></div>
                                </td>
                                <td align="center" abbr="article_time" axis="col6" class="">
                                    <div style="text-align: center; width: 60px;" class=""><?php echo $found[status_desc]; ?></div>
                                </td>
                                <td align="center" abbr="article_time" axis="col6" class="">
                                    <div style="text-align: center; width: 60px;" class=""><?php echo $found['order']['order_status_detail']; ?></div>
                                </td>
                                <td align="center" abbr="article_time" axis="col6" class="">
                                    <div style="text-align: center; width: 60px;" class=""><?php echo $found['order']['pay_status_detail']; ?></div>
                                </td>
                                <td align="center" abbr="article_time" axis="col6" class="">
                                    <div style="text-align: center; width: 60px;" class=""><?php echo $found['order']['shipping_status_detail']; ?></div>
                                </td>
                                <td align="center" abbr="article_time" axis="col6" class="">
                                    <div style="text-align: center; width: 60px;" class=""><?php echo (isset($found['order']['pay_name']) && ($found['order']['pay_name'] !== '')?$found['order']['pay_name']:'其他方式'); ?></div>
                                </td>
                                <td align="center" abbr="article_time" axis="col6" class="">
                                    <div style="text-align: center; width: 60px;" class=""><?php echo $found['order']['shipping_name']; ?></div>
                                </td>
                                <td align="center" abbr="article_time" axis="col6" class="">
                                    <div style="text-align: center; width: 120px;" class=""><?php echo date('Y-m-d H:i',$found['order']['add_time']); ?></div>
                                </td>
                                <td align="center" abbr="article_time" axis="col6" class="">
                                    <div style="text-align: center; width: 160px;" class=""><?php echo $found[store][store_name]; ?></div>
                                </td>
                                <td align="center" axis="col1" class="handle">
                                    <div style="text-align: center;">
                                        <a class="btn green"  href="<?php echo U('Admin/order/detail',array('order_id'=>$found['order_id'])); ?>"><i class="fa fa-list-alt"></i>订单详情</a>
                                        <a class="btn green"  href="<?php echo U('Admin/Team/order_list',array('found_id'=>$found['found_id'])); ?>"><i class="fa fa-search"></i>查看拼单</a>
                                        <?php if($found[order][order_status] >= 1 AND $found[team_activity][team_type] == 1): ?>
                                            <a class="btn green" target="_blank" href="<?php echo U('Admin/Team/bonus',array('found_id'=>$found['found_id'])); ?>"><i class="fa fa-gavel"></i>团长佣金</a>
                                        <?php endif; ?>
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
                    <div class="col-sm-6 text-right"><?php echo $page->show(); ?></div>
                </div>
            </div>
            <div class="iDiv" style="display: none;"></div>
        </div>
        <!--分页位置-->
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function () {
        // 起始位置日历控件
        $('#add_time_begin').layDate(); 
	    $('#add_time_end').layDate();
        laydate.render({
            elem: '#add_time_begin',
            format: 'YYYY-MM-DD', // 分隔符可以任意定义，该例子表示只显示年月
            festival: true, //显示节日
            istime: false,
            choose: function (datas) { //选择日期完毕的回调
                compare_time($('#add_time_begin').val(), $('#add_time_end').val());
            }
        });

        // 结束位置日历控件
        laydate.render({
            elem: '#add_time_end',
            format: 'YYYY-MM-DD', // 分隔符可以任意定义，该例子表示只显示年月
            festival: true, //显示节日
            istime: false,
            choose: function (datas) { //选择日期完毕的回调
                compare_time($('#add_time_begin').val(), $('#add_time_end').val());
            }
        });
        // 点击刷新数据
        $('.fa-refresh').click(function () {
            location.href = location.href;
        });

        $('.ico-check ', '.hDivBox').click(function () {
            $('tr', '.hDivBox').toggleClass('trSelected', function (index, currentclass) {
                var hasClass = $(this).hasClass('trSelected');
                $('tr', '#flexigrid').each(function () {
                    if (hasClass) {
                        $(this).addClass('trSelected');
                    } else {
                        $(this).removeClass('trSelected');
                    }
                });
            });
        });

    });


</script>
</body>
</html>