<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:50:"./application/admin/view/store/store_own_list.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
				<h3>店铺管理</h3>
				<h5>网站系统店铺索引与管理</h5>
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
			<li>1. 平台在此处统一管理自营店铺，可以新增、编辑、删除平台自营店铺。</li>
			<li>2. 可以设置未绑定全部商品类目的平台自营店铺的经营类目。</li>
			<li>3. 已经发布商品的自营店铺不能被删除。</li>
			<li>4. 删除平台自营店铺将会同时删除店铺的相关图片以及相关商家中心账户，请谨慎操作！</li>
		</ul>
	</div>
	<div class="flexigrid">
		<div class="mDiv">
			<div class="ftitle">
				<h3>店铺列表</h3>
				<h5>(共<?php echo $pager->totalRows; ?>条记录)</h5>
			</div>
			<div title="刷新数据" class="pReload"><i class="fa fa-refresh"></i></div>
			<form class="navbar-form form-inline" id="search-form" action="<?php echo U('Store/store_own_list'); ?>" method="get">
				<div class="sDiv">
					<div class="sDiv2" style="margin-right: 10px;border: none;">
						<select name="grade_id" class="form-control">
							<option value="">所属等级</option>
							<?php if(is_array($store_grade) || $store_grade instanceof \think\Collection || $store_grade instanceof \think\Paginator): $k = 0; $__LIST__ = $store_grade;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($k % 2 );++$k;?>
								<option value="<?php echo $k; ?>" <?php if(\think\Request::instance()->get('grade_id') == $k): ?>selected<?php endif; ?>><?php echo $item; ?></option>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
					</div>
					<div class="sDiv2" style="margin-right: 10px;border: none;">
						<select name="sc_id" class="form-control">
							<option value="">店铺类别</option>
							<?php if(is_array($store_class) || $store_class instanceof \think\Collection || $store_class instanceof \think\Paginator): $i = 0; $__LIST__ = $store_class;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
								<option value="<?php echo $key; ?>" <?php if(\think\Request::instance()->get('sc_id') == $key): ?>selected<?php endif; ?>><?php echo $item; ?></option>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
					</div>
					<div class="sDiv2" style="margin-right: 10px;border: none;">
                        <select name="store_state" class="form-control">
                            <option value="">店铺状态</option>
                            <option value="0" <?php if(\think\Request::instance()->get('store_state') == '0'): ?>selected<?php endif; ?>>关闭</option>
                            <option value="1" <?php if(\think\Request::instance()->get('store_state') == '1'): ?>selected<?php endif; ?>>开启</option>
                            <option value="2" <?php if(\think\Request::instance()->get('store_state') == '2'): ?>selected<?php endif; ?>>审核中</option>
                            <option value="3" <?php if(\think\Request::instance()->get('store_state') == '3'): ?>selected<?php endif; ?>>即将到期</option>
                            <option value="4" <?php if(\think\Request::instance()->get('store_state') == '4'): ?>selected<?php endif; ?>>已到期</option>
                        </select>
					</div>
					<div class="sDiv2" style="margin-right: 10px;">
						<input size="30" name="seller_name" value="<?php echo \think\Request::instance()->get('seller_name'); ?>" placeholder="店主卖家名称" class="qsbox" type="text">
					</div>
					<div class="sDiv2">
						<input size="30" name="store_name" value="<?php echo \think\Request::instance()->get('store_name'); ?>" class="qsbox" placeholder="输入店铺名称" type="text">
						<input class="btn" value="搜索" type="submit">
					</div>
				</div>
			</form>
		</div>
		<div class="hDiv">
			<div class="hDivBox">
				<table cellspacing="0" cellpadding="0">
					<thead>
					<tr>
						<th class="sign" axis="col0">
							<div style="width: 24px;"><i class="ico-check"></i></div>
						</th>
						<th align="left" abbr="article_title" axis="col3" class="">
							<div style="text-align: left; width: 120px;" class="">店铺名称</div>
						</th>
						<th align="left" abbr="ac_id" axis="col4" class="">
							<div style="text-align: left; width: 100px;" class="">店主账号</div>
						</th>
						<th align="left" abbr="article_show" axis="col5" class="">
							<div style="text-align: center; width: 100px;" class="">店主卖家账号</div>
						</th>
						<th align="center" abbr="article_time" axis="col6" class="">
							<div style="text-align: center; width: 120px;" class="">创建日期</div>
						</th>
						<th align="center" abbr="article_time" axis="col6" class="">
							<div style="text-align: center; width: 80px;" class="">状态</div>
						</th>
						<th align="center" abbr="article_time" axis="col6" class="">
							<div style="text-align: center; width: 50px;" class="">推荐</div>
						</th>
						<th align="center" abbr="article_time" axis="col6" class="">
							<div style="text-align: center; width: 100px;" class="">默认同步平台</div>
						</th>
						<th align="center" abbr="article_time" axis="col6" class="">
							<div style="text-align: center; width: 40px;" class="">销售商</div>
						</th>
						<th align="center" abbr="article_time" axis="col6" class="">
							<div style="text-align: center; width: 40px;" class="">供应商</div>
						</th>
                        <th align="center" abbr="article_time" axis="col6" class="">
                            <div style="text-align: center; width: 80px;" class="">店铺类别</div>
                        </th>
						<th align="center" abbr="article_time" axis="col6" class="">
							<div style="text-align: center; width: 80px;" class="">绑定所有类目</div>
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
		<div class="tDiv">
			<div class="tDiv2">
				<div class="fbutton"> <a href="<?php echo U('Store/store_add',array('is_own_shop'=>1)); ?>">
					<div class="add" title="新增店铺">
						<span><i class="fa fa-plus"></i>新增店铺</span>
					</div>
				</a> </div>
			</div>
			<div style="clear:both"></div>
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
							<td align="left" class="">
								<div style="text-align: left; width: 120px;"><?php echo $vo['store_name']; ?></div>
							</td>
							<td align="left" class="">
								<div style="text-align: left; width: 100px;"><?php echo $vo['user_name']; ?></div>
							</td>
							<td align="left" class="">
								<div style="text-align: center; width: 100px;"><?php echo $vo['seller_name']; ?></div>
							</td>
							<td align="center" class="">
								<div style="text-align: center; width: 120px;"><?php echo date('Y-m-d',$vo['store_time']); ?></div>
							</td>
                            <td align="center" class="">
                                <div style="text-align: center; width: 80px;">
                                    <?php if($vo[store_state] == 1): ?>开启<?php else: ?>关闭<?php endif; ?>
                                </div>
                            </td>
                            <td align="center" class="">
								<div style="text-align: center; width: 50px;">
									<?php if($vo[store_recommend] == 1): ?>
										<span class="yes" onClick="changeTableVal('store','store_id','<?php echo $vo['store_id']; ?>','store_recommend',this)" ><i class="fa fa-check-circle"></i>是</span>
										<?php else: ?>
										<span class="no" onClick="changeTableVal('store','store_id','<?php echo $vo['store_id']; ?>','store_recommend',this)" ><i class="fa fa-ban"></i>否</span>
									<?php endif; ?>
								</div>
							</td>
							<td align="center" class="">
								<div style="text-align: center; width: 100px;">
									<?php if($vo[default_store] == 1): ?>
										<span class="yes" onClick="changeDefaultStore('store','store_id','<?php echo $vo['store_id']; ?>','default_store',this)" ><i class="fa fa-check-circle"></i>是</span>
										<?php else: ?>
										<span class="no" onClick="changeDefaultStore('store','store_id','<?php echo $vo['store_id']; ?>','default_store',this)" ><i class="fa fa-ban"></i>否</span>
									<?php endif; ?>
								</div>
							</td>
							<td align="center" class="">
								<div style="text-align: center; width: 40px;">
									<?php if($vo[is_dealer] == 1): ?>
										<span class="yes">是</span>
										<?php else: ?>
										<span class="no">否</span>
									<?php endif; ?>
								</div>
							</td>
							<td align="center" class="">
								<div style="text-align: center; width: 40px;">
									<?php if($vo[is_supplier] == 1): ?>
										<span class="yes">是</span>
										<?php else: ?>
										<span class="no">否</span>
									<?php endif; ?>
								</div>
							</td>
							<td align="center" class="">
								<div style="text-align: center; width: 80px;"><?php echo $store_class[$vo[sc_id]]; ?></div>
							</td>
							<td align="center" class="">
								<div style="text-align: center; width: 80px;">
									<?php if($vo[bind_all_gc] == 1): ?>
										<span class="yes" onClick="changeTableVal('store','store_id','<?php echo $vo['store_id']; ?>','bind_all_gc',this)" ><i class="fa fa-check-circle"></i>是</span>
										<?php else: ?>
										<span class="no" onClick="changeTableVal('store','store_id','<?php echo $vo['store_id']; ?>','bind_all_gc',this)" ><i class="fa fa-ban"></i>否</span>
									<?php endif; ?>
								</div>
							</td>
							<td align="center" class="handle">
								<div style="text-align: center; width: 170px; max-width:170px;">
									<a href="<?php echo U('Store/store_info_edit',array('store_id'=>$vo['store_id'],'is_own_shop'=>1)); ?>" class="btn blue"><i class="fa fa-pencil-square-o"></i>编辑</a>
									<a class="btn red"  href="javascript:void(0)" data-url="<?php echo U('Store/store_del'); ?>" data-id="<?php echo $vo['store_id']; ?>" onClick="delfun(this)"><i class="fa fa-trash-o"></i>删除</a>
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
		<!--分页位置-->
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

    // 修改指定表的指定字段值 包括有按钮点击切换是否 或者 排序 或者输入框文字
    function changeDefaultStore(table,id_name,id_value,field,obj)
    {
        layer.confirm('确定要设置默认平台(可以给店铺代买商品)？只能设置一次，设置之后不能更改，以防数据混乱，慎选！！！！', {
            btn: ['确定', '取消'] //按钮
        }, function () {
            $.ajax({
                url:"/index.php?m=Admin&c=Store&a=changeDefaultStore&table="+table+"&id_name="+id_name+"&id_value="+id_value+"&field="+field+'&value=',
                dataType: 'json',
                success: function(data){
                    if(data.status==1){
                        if(!$(obj).hasClass('no') && !$(obj).hasClass('yes'))
                            layer.msg('更新成功', {icon: 1});
                        var src = "";
                        if($(obj).hasClass('no')) // 图片点击是否操作
                        {
                            //src = '/public/images/yes.png';
                            $(obj).removeClass('no').addClass('yes');
                            $(obj).html("<i class='fa fa-check-circle'></i>是");
                            var value = 1;

                        }else if($(obj).hasClass('yes')){ // 图片点击是否操作
                            $(obj).removeClass('yes').addClass('no');
                            $(obj).html("<i class='fa fa-ban'></i>否");
                            var value = 0;
                        }else{ // 其他输入框操作
                            var value = $(obj).val();
                        }
                    }else if(data.status==0){
                        layer.msg(data.msg, {icon: 2});
                    }

                }
            });

        }, function () {
            layer.closeAll();
        });

    }

	function delfun(obj) {
		// 删除按钮
		layer.confirm('确认删除？', {
			btn: ['确定', '取消'] //按钮
		}, function () {
			$.ajax({
				type: 'post',
				url: $(obj).attr('data-url'),
				data : {act:'del',del_id:$(obj).attr('data-id')},
				dataType: 'json',
				success: function (data) {
					layer.closeAll();
					if (data.status == 1) {
                        layer.alert(data.msg, {icon: 1});
						$(obj).parent().parent().parent().remove();
					} else {
						layer.alert(data.msg, {icon: 2});
					}
				}
			})
		}, function () {
			layer.closeAll();
		});
	}
</script>
</body>
</html>