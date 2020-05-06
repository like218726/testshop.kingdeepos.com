<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:42:"./application/admin/view/report\index.html";i:1588218728;s:72:"D:\www\testshop.kingdeepos.com\application\admin\view\public\layout.html";i:1588218727;}*/ ?>
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
<script src="/public/static/js/layer/laydate/laydate.js"></script>
<body style="background-color: rgb(255, 255, 255); overflow: auto; cursor: default; -moz-user-select: inherit;">
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
	<div class="fixed-bar">
		<div class="item-title">
			<div class="subject">
				<h3>统计报表 - 销售概况</h3>
				<h5>网站系统销售概况</h5>
			</div>
			
		</div>
	</div>
	<!-- 操作说明 -->
	<div class="explanation">
		<div id="checkZoom" class="title"><i class="fa fa-lightbulb-o"></i>
			<h4 title="提示相关设置操作时应注意的要点">操作提示</h4>
			<span title="收起提示" id="explanationZoom" style="display: block;"></span>
		</div>
		<ul>
			<li>统计分析历史订单交易数, 订单金额, 客单价</li>
		</ul>
	</div>
	<div class="flexigrid">
		<div class="mDiv">
			<div class="ftitle">
				<h3>销售概况</h3>
				<h5>今日销售总额：￥<?php if(empty($today['today_amount']) || (($today['today_amount'] instanceof \think\Collection || $today['today_amount'] instanceof \think\Paginator ) && $today['today_amount']->isEmpty())): ?>0<?php else: ?><?php echo $today['today_amount']; endif; ?>|人均客单价：￥<?php echo $today['sign']; ?>|今日订单数：<?php echo $today['today_order']; ?>|今日取消订单：<?php echo $today['cancel_order']; ?></h5>
			</div>
			<div title="刷新数据" class="pReload"><i class="fa fa-refresh"></i></div>
			<form class="navbar-form form-inline" id="search-form" method="get" action="<?php echo U('Report/index'); ?>" onSubmit="return check_form();">				 
				<div class="sDiv">
					<div class="sDiv2" style="margin-right: 10px;">
						<input type="text" size="30" name="start_time"  id="start_time" value="<?php echo $start_time; ?>" placeholder="起始时间" class="qsbox">
						<input type="button" class="btn" value="起始时间">
					</div>
					<div class="sDiv2" style="margin-right: 10px;">
						<input type="text" size="30" name="end_time"  id="end_time" value="<?php echo $end_time; ?>" placeholder="截止时间" class="qsbox">
						<input type="button" class="btn" value="截止时间">
					</div>
					<div class="sDiv2">
						<input class="btn" value="搜索" type="submit">
					</div>
				</div>
			</form>
		</div>
		<div id="statistics" style="height: 400px;"></div>
		<div class="hDiv">
			<div class="hDivBox">
				<table cellspacing="0" cellpadding="0">
					<thead>
					<tr>
						<th class="sign" axis="col0">
							<div style="width: 24px;"><i class="ico-check"></i></div>
						</th>
						<th align="center" abbr="article_title" axis="col3" class="">
							<div style="text-align: center; width: 120px;" class="">时间</div>
						</th>
						<th align="center" abbr="ac_id" axis="col4" class="">
							<div style="text-align: center; width: 100px;" class="">订单数</div>
						</th>
						<th align="center" abbr="article_show" axis="col5" class="">
							<div style="text-align: center; width: 100px;" class="">销售总额</div>
						</th>
						<th align="center" abbr="article_time" axis="col6" class="">
							<div style="text-align: center; width: 100px;" class="">客单价</div>
						</th>
						<th align="center" axis="col1" class="handle">
							<div style="text-align: center; width: 150px;">操作</div>
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
					<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $k=>$vo): ?>
						<tr>
							<td class="sign">
								<div style="width: 24px;"><i class="ico-check"></i></div>
							</td>
							<td align="center" class="">
								<div style="text-align: center; width: 120px;"><?php echo $vo['day']; ?></div>
							</td>
							<td align="center" class="">
								<div style="text-align: center; width: 100px;"><?php echo $vo['order_num']; ?></div>
							</td>
							<td align="center" class="">
								<div style="text-align: center; width: 100px;"><?php echo $vo['amount']; ?></div>
							</td>
							<td align="center" class="">
								<div style="text-align: center; width: 100px;"><?php echo $vo['sign']; ?></div>
							</td>
							<td align="center" class="handle">
								<div style="text-align: center; width: 170px; max-width:170px;">
									<a href="<?php echo U('Report/saleOrder',array('start_time'=>$vo['day'],'end_time'=>$vo['end'])); ?>" class="btn blue"><i class="fa fa-search"></i>查看订单列表</a>
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
			<div class="iDiv" style="display: none;"></div>
		</div>
	 </div>
</div>
<script src="/public/js/echart/echarts.min.js" type="text/javascript"></script>
<script src="/public/js/echart/macarons.js"></script>
<script src="/public/js/echart/china.js"></script>
<script src="/public/dist/js/app.js" type="text/javascript"></script>
<script type="text/javascript">
	var res = <?php echo $result; ?>;
	var myChart = echarts.init(document.getElementById('statistics'),'macarons');
	option = {
		tooltip : {
			trigger: 'axis'
		},
		toolbox: {
			show : true,
			feature : {
				mark : {show: true},
				dataView : {show: true, readOnly: false},
				magicType: {show: true, type: ['line', 'bar']},
				restore : {show: true},
				saveAsImage : {show: true}
			}
		},
		calculable : true,
		legend: {
			data:['交易金额','订单数','客单价']
		},
		xAxis : [
			{
				type : 'category',
				data : res.time
			}
		],
		yAxis : [
			{
				type : 'value',
				name : '金额',
				axisLabel : {
					formatter: '{value} ￥'
				}
			},
			{
				type : 'value',
				name : '客单价',
				axisLabel : {
					formatter: '{value} ￥'
				}
			}
		],
		series : [
			{
				name:'交易金额',
				type:'bar',
				data:res.amount
			},
			{
				name:'订单数',
				type:'bar',
				data:res.order
			},
			{
				name:'客单价',
				type:'line',
				yAxisIndex: 1,
				data:res.sign
			}
		]
	};
	myChart.setOption(option);
	$(document).ready(function(){
		// 表格行点击选中切换
		$('#flexigrid > table>tbody >tr').click(function(){
			$(this).toggleClass('trSelected');
		});

		// 点击刷新数据
		$('.fa-refresh').click(function(){
			location.href = location.href;
		});

		// 起始位置日历控件
		$('#start_time').layDate(); 
	    $('#end_time').layDate();
		laydate.render({
		  elem: '#start_time',
		  format: 'YYYY-MM-DD', // 分隔符可以任意定义，该例子表示只显示年月
		  festival: true, //显示节日
		  istime: false,
		  choose: function(datas){ //选择日期完毕的回调
			 compare_time($('#start_time').val(),$('#end_time').val());
		  }
		});
		 
		 // 结束位置日历控件
		laydate.render({
		  elem: '#end_time',
		  format: 'YYYY-MM-DD', // 分隔符可以任意定义，该例子表示只显示年月
		  festival: true, //显示节日
		  istime: false,
		  choose: function(datas){ //选择日期完毕的回调
			   compare_time($('#start_time').val(),$('#end_time').val());
		  }
		});	 		 
	});

	function check_form(){
		var start_time = $.trim($('#start_time').val());
		var end_time =  $.trim($('#end_time').val());
		if(start_time == '' ^ end_time == ''){
			layer.alert('请选择完整的时间间隔', {icon: 2});
			return false;
		}		 
		return true;
	}
</script>
</body>
</html>