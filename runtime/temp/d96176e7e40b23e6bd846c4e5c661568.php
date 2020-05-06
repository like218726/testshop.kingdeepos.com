<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:42:"./application/admin/view/tools/region.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<body style="background-color: rgb(255, 255, 255); overflow: auto; cursor: default; -moz-user-select: inherit;">
<style>
	.flexigrid .pReload_district {
		width: 300px;
		text-align: left;
		text-overflow: ellipsis;
		overflow: hidden;
		white-space: nowrap;
	}
</style>
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
	<div id="list">
		<div class="fixed-bar">
			<div class="item-title">
				<div class="subject">
					<h3>地区设置</h3>
					<h5>可对系统内置的地区进行编辑</h5>
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
				<li>全站所有涉及的地区均来源于此处，强烈建议对此处谨慎操作。</li>
				<li>编辑地区信息后，需手动更新地区缓存(平台  &gt; 设置 &gt; 清理缓存 &gt; 地区)，前台才会生效。</li>
				<li>所属大区为默认的全国性的几大区域，只有省级地区才需要填写大区域，目前全国几大区域有：华北、东北、华东、华南、华中、西南、西北、港澳台、海外</li>
				<li>所在层级为该地区的所在的层级深度，如北京&gt;北京市&gt;朝阳区,其中北京层级为1，北京市层级为2，朝阳区层级为3</li>
				<!--<li>城市分站：可在城市分站切换列表显示</li>-->
			</ul>
		</div>
		<div class="flexigrid">
			<div class="mDiv">
				<div class="ftitle">
					<h3>地区列表</h3>
					<h5>(共<?php echo count($region); ?>张记录)</h5>
				</div>
				<div title="刷新数据" class="pReload"><i class="fa fa-refresh"></i></div>
				<form class="navbar-form form-inline" action="/index.php/Admin/Tools/region" method="get">
					<div class="sDiv">
						<div class="sDiv2">
							<input type="text" size="30" name="name" value="<?php echo $name; ?>" class="qsbox" placeholder="搜索地区">
							<input type="submit" class="btn" value="搜索">
						</div>
					</div>
				</form>
				<?php if(!(empty($parent_path) || (($parent_path instanceof \think\Collection || $parent_path instanceof \think\Paginator ) && $parent_path->isEmpty()))): ?><div title="上级区域" class="pReload" style="width: 250px;text-align: left">上级区域:<?php echo $parent_path; ?></div><?php endif; ?>
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
								<div style="text-align: left; width: 200px;">地区</div>
							</th>
							<th axis="col4" class="" align="left">
								<div style="text-align: left; width: 100px;">所在层级</div>
							</th>
							<th axis="col5" class="" align="center">
								<div style="text-align: center; width: 140px;">上级地区ID</div>
							</th>
							<!--<th axis="col5" class="" align="center">-->
								<!--<div style="text-align: center; width: 140px;">热门城市</div>-->
							<!--</th>-->
							<th style="width:100%" axis="col6"><div></div></th>
						</tr>
						</thead>
					</table>
				</div>
			</div>
			<div class="tDiv">
				<div class="tDiv2">
					<div class="fbutton">
						<div class="add" title="新增数据">
							<span onclick="add_region(1);"><i class="fa fa-plus"></i>新增数据</span>
						</div>
					</div>
					<div class="fbutton">
						<div class="up" title="返回上级地区">
							<span onclick="return_top_level();"><i class="fa fa-level-up"></i>返回上级地区</span>
						</div>
					</div>
					<div class="fbutton">
						<a href="<?php echo U('Admin/Goods/initLocationJsonJs'); ?>">
							<div class="add" title="初始化地址json文件">
								<span><i class="fa fa-plus"></i>初始化地址json文件</span>
							</div>
						</a>
					</div>
				</div>
				<div style="clear:both"></div>
			</div>
			<div class="bDiv" style="height: auto;">
				<div id="flexigrid" cellpadding="0" cellspacing="0" border="0">
					<table>
						<tbody>
						<?php if(is_array($region) || $region instanceof \think\Collection || $region instanceof \think\Paginator): if( count($region)==0 ) : echo "" ;else: foreach($region as $k=>$vo): ?>
							<tr id="row130" data-id="130" class="">
								<td class="sign">
									<div style="width: 24px;"><i class="ico-check"></i></div>
								</td>
								<td class="handle" align="center">
									<div style="text-align: center; width: 150px;">
										<a class="btn red" data-url="<?php echo U('Tools/del_region'); ?>" data-id="<?php echo $vo[id]; ?>" onclick="del_region(this)"><i class="fa fa-trash-o"></i>删除</a>
										<span class="btn"><em><i class="fa fa-cog"></i>设置 <i class="arrow"></i></em><ul>
										<li><a href="<?php echo U('Admin/Tools/region',array('op'=>'add','parent_id'=>$vo['id'])); ?>">新增下级</a></li>
										<li><a href="<?php echo U('Admin/Tools/region',array('parent_id'=>$vo['id'])); ?>">查看下级</a></li>
									</ul></span></div>
								</td>
								<td class="" align="left">
									<div style="text-align: left; width: 200px;"><?php echo $vo['name']; ?></div>
								</td>
								<td class="" align="left">
									<div style="text-align: left; width: 100px;"><?php echo $vo['level']; ?></div>
								</td>
								<td class="" align="center">
									<div style="text-align: center; width: 140px;"><?php echo $vo['parent_id']; ?></div>
								</td>
								<!--<td align="center" class="">-->
									<!--<div style="text-align: center; width: 140px;">-->
										<!--<?php if($vo[is_hot] == 1): ?>-->
											<!--<span class="yes" onClick="changeTableVal('region','id','<?php echo $vo['id']; ?>','is_hot',this)" ><i class="fa fa-check-circle"></i>是</span>-->
											<!--<?php else: ?>-->
											<!--<span class="no" onClick="changeTableVal('region','id','<?php echo $vo['id']; ?>','is_hot',this)" ><i class="fa fa-ban"></i>否</span>-->
										<!--<?php endif; ?>-->
									<!--</div>-->
								<!--</td>-->
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
	<div id="add_region" style="display: none">
		<div class="page">
			<div class="fixed-bar">
				<div class="item-title"><a class="back" onclick="add_region(0);" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
					<div class="subject">
						<h3>地区设置 - 新增</h3>
						<h5>地区新增与编辑</h5>
					</div>
				</div>
			</div>
			<form id="add_region_form" method="post" action="<?php echo U('Tools/regionHandle'); ?>">
				<input type="hidden" name="level" value="<?php echo $parent['level']; ?>">
				<input type="hidden" name="parent_id" value="<?php echo $parent['id']; ?>">
				<div class="ncap-form-default">
					<?php if(!(empty($parent_path) || (($parent_path instanceof \think\Collection || $parent_path instanceof \think\Paginator ) && $parent_path->isEmpty()))): ?>
					<dl class="row">
						<dt class="tit">
							<label for="name"><em></em>上级区域:</label>
						</dt>
						<dd class="opt"><label for="name"><?php echo $parent_path; ?></label></dd>
					</dl>
					<?php endif; ?>
					<dl class="row">
						<dt class="tit">
							<label for="name"><em>*</em>地区名</label>
						</dt>
						<dd class="opt">
							<input id="name" name="name" value="" maxlength="20" class="input-txt" type="text">
							<span class="err"></span>
							<p class="notic">请认真填写地区名称，地区设定后将直接影响订单、收货地址等重要信息，请谨慎操作。</p>
						</dd>
					</dl>
					<div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="$('#add_region_form').submit();">确认提交</a></div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	<?php if(\think\Request::instance()->get('op') == 'add'): ?>
			add_region(1);
	<?php endif; ?>
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
	function add_region(mode){
		if(mode == 1){
			$('#add_region').show();
			$('#list').hide();
		}else{
			$('#add_region').hide();
			$('#list').show();
		}
	}
	function return_top_level()
	{
		window.location.href = "<?php echo U('Tools/region',array('parent_id'=>$parent[parent_id])); ?>";
	}
	function del_region(obj){
		// 删除按钮
		layer.confirm('确认删除？', {
			btn: ['确定','取消'] //按钮
		}, function(){
			$.ajax({
				type : 'post',
				url : $(obj).attr('data-url'),
				data : {id:$(obj).attr('data-id')},
				dataType : 'json',
				success : function(data){
					layer.closeAll();
					if(data.status == 1){
						$(obj).parent().parent().parent().remove();
					}else{
						layer.alert(data.msg, {icon: 2});
					}
				}
			})
		}, function(){
			layer.closeAll();
		});
	}
</script>
</body>
</html>