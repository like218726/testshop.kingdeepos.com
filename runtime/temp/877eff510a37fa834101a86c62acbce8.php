<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:44:"./application/admin/view/distribut/tree.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
	<div id="list">
		<div class="fixed-bar">
			<div class="item-title">
				<div class="subject">
					<h3>分销关系</h3>
					<h5>查看代理商层级关系</h5>
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
				<li>查看代理商层级关系。</li>
			</ul>
		</div>
		<div class="flexigrid">
			<div class="mDiv">
				<div class="ftitle">
					<h3>分销人员列表</h3>
					<h5>(共<?php echo $count; ?>条记录)</h5>
				</div>
				<div title="刷新数据" class="pReload" ><i class="fa fa-refresh"></i></div>
				<?php if(!(empty($first_leader_user) || (($first_leader_user instanceof \think\Collection || $first_leader_user instanceof \think\Paginator ) && $first_leader_user->isEmpty()))): ?><div title="上级区域" class="pReload" style="width: auto;">
					上级用户(<?php echo $first_leader_user['user_id']; ?>:<?php if($first_leader_user['nickname'] != null): ?>
					<?php echo $first_leader_user['nickname']; elseif($first_leader_user['mobile'] != null): ?>
					<?php echo $first_leader_user['mobile']; elseif($first_leader_user['email'] != null): ?>
					<?php echo $first_leader_user['email']; endif; ?>)</div>
				<?php endif; ?>
			</div>
			<div class="hDiv">
				<div class="hDivBox">
					<table cellspacing="0" cellpadding="0">
						<thead>
						<tr>
							<th class="sign" axis="col0">
								<div style="width: 24px;">
									<i class="ico-check"></i>
								</div>
							</th>
							<th axis="col1" class="handle" align="center">
								<div style="text-align: center; width: 150px;">操作</div>
							</th>
							<th axis="col2" class="" align="left">
								<div style="text-align: left; width: 200px;">会员ID</div>
							</th>
							<th axis="col4" class="" align="left">
								<div style="text-align: left; width: 100px;">会员名称</div>
							</th>
							<th style="width:100%" axis="col6"><div></div></th>
						</tr>
						</thead>
					</table>
				</div>
			</div>

			<div class="tDiv">
				<?php if(count($first_leader_user) > 0): ?>
					<div class="tDiv2">

						<div class="fbutton">
							<div class="up" title="返回分销上级">
								<span onclick="return_top_level();"><i class="fa fa-level-up"></i>返回分销上级</span>
							</div>
						</div>
					</div>
				<?php endif; ?>
				<div style="clear:both"></div>

			</div>
			<div class="bDiv" style="height: auto;">
				<div id="flexigrid" cellpadding="0" cellspacing="0" border="0">
					<table>
						<tbody>
						<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $k=>$v): ?>
							<tr id="row130" data-id="130" class="">
								<td class="sign">
									<div style="width: 24px;"><i class="ico-check"></i></div>
								</td>
								<td class="handle" align="center">
									<div style="text-align: center; width: 150px;">
										<span class="btn"><em><i class="fa fa-cog"></i>设置 <i class="arrow"></i></em>
											<ul>
												<li><a href="<?php echo U('Admin/Distribut/tree',array('first_leader'=>$v['user_id'])); ?>">查看下级</a></li>
											</ul>
										</span>
									</div>
								</td>
								<td class="" align="left">
									<div style="text-align: left; width: 200px;"><?php echo $v['user_id']; ?></div>
								</td>
								<td class="" align="left">
									<div style="text-align: left; width: 100px;">
										<i class="icon-folder-open"></i>
										<?php if($v['nickname'] != null): ?>
											<?php echo $v['nickname']; elseif($v['mobile'] != null): ?>
											<?php echo $v['mobile']; elseif($v['email'] != null): ?>
											<?php echo $v['email']; endif; ?>
									</div>
								</td>
								<td class="" style="width: 100%;" align="">
									<div>&nbsp;</div>
								</td>
							</tr>
						<?php endforeach; endif; else: echo "" ;endif; ?>
						</tbody>
					</table>
				</div>
				<div class="iDiv" style="display: none;"></div>
			</div>
			<!--分页位置-->
			<?php echo $page; ?> </div>
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
	function return_top_level()
	{
		window.location.href = "<?php echo U('Admin/Distribut/tree',array('first_leader'=>$first_leader_user[first_leader])); ?>";
	}
</script>
</body>
</html>