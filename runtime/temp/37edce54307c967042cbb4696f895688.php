<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:48:"./application/admin/view/system/_navigation.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<body style="background-color: #FFF; overflow: auto;">
<div id="toolTipLayer" style="position: absolute; z-index: 9999; display: none; visibility: visible; left: 95px; top: 573px;"></div>
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="javascript:history.back();" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>自定义导航 - 编辑导航</h3>
                <h5>网站系统自定义导航与管理</h5>
            </div>
        </div>
    </div>
    <form class="form-horizontal" id="addEditNavForm" method="post">
        <div class="ncap-form-default">
            <!--选择导航条位置-->
            <dl class="row">
                <dt class="tit">
                    <label for="position"><em>*</em>导航位置</label>
                </dt>
                <dd class="opt">
                    <select class="small form-control" id="position" name="position" <?php if(!empty($navigation)): ?> disabled <?php endif; ?>>
                        <?php if(is_array($position) || $position instanceof \think\Collection || $position instanceof \think\Paginator): if( count($position)==0 ) : echo "" ;else: foreach($position as $kk=>$vv): ?>
                            <option value="<?php echo $kk; ?>" <?php if($kk == $navigation[position]): ?> selected <?php endif; ?> > <?php echo $vv; ?> </option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="system_nav"><em>*</em>系统内容</label>
                </dt>
                <dd class="opt">

                    <?php if($navigation[position] == 1): ?>
                        <select class="small form-control" id="system_nav" name="system_nav">
                            <option value="">自定义导航</option>
                            <?php if(is_array($system_nav) || $system_nav instanceof \think\Collection || $system_nav instanceof \think\Paginator): if( count($system_nav)==0 ) : echo "" ;else: foreach($system_nav as $k=>$v): ?>
                                <option value="<?php echo $k; ?>"<?php if($k == $navigation[url]): ?> selected <?php endif; ?> ><?php echo $v; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    <?php elseif(($navigation[position] == 2) or ($navigation[position] == 3)): ?>
                        <select class="small form-control" id="system_bottom" name="system_bottom">
                            <option value="">自定义导航</option>
                            <?php if(is_array($system_bottom) || $system_bottom instanceof \think\Collection || $system_bottom instanceof \think\Paginator): if( count($system_bottom)==0 ) : echo "" ;else: foreach($system_bottom as $k=>$v): ?>
                                <option value="<?php echo $k; ?>" <?php if($k == $navigation[url]): ?> selected <?php endif; ?> ><?php echo $v; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    <?php else: ?>
                        <select class="small form-control" id="system_nav" name="system_nav">
                            <option value="">自定义导航</option>
                            <?php if(is_array($system_nav) || $system_nav instanceof \think\Collection || $system_nav instanceof \think\Paginator): if( count($system_nav)==0 ) : echo "" ;else: foreach($system_nav as $k=>$v): ?>
                                <option value="<?php echo $k; ?>"<?php if($k == $navigation[url]): ?> selected <?php endif; ?> ><?php echo $v; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>

                        <select class="small form-control" id="system_bottom" name="system_bottom" style="display: none">
                            <option value="">自定义导航</option>
                            <?php if(is_array($system_bottom) || $system_bottom instanceof \think\Collection || $system_bottom instanceof \think\Paginator): if( count($system_bottom)==0 ) : echo "" ;else: foreach($system_bottom as $k=>$v): ?>
                                <option value="<?php echo $k; ?>" <?php if($k == $navigation[url]): ?> selected <?php endif; ?> ><?php echo $v; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>

                    <?php endif; ?>
                    <span class="err"></span>
                    <p class="notic">自定义导航</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="name"><em>*</em>导航名称</label>
                </dt>
                <dd class="opt">
                    <input type="text" value="<?php echo $navigation['name']; ?>" name="name" id="name" class="input-txt">
                    <span class="err" id="name_err" style="display: none">导航名称不能为空!!</span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="url"><em>*</em>链接</label>
                </dt>
                <dd class="opt">
                    <input type="text" value="<?php echo $navigation['url']; ?>" name="url" id="url" class="input-txt">
                    <span class="err" id="link_err" style="display: none;">链接不能为空!!</span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="sort">排序</label>
                </dt>
                <dd class="opt">
                    <input type="text" value="<?php echo $navigation['sort']; ?>" name="sort" id="sort" class="input-txt">
                    <span class="err"></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <div class="bot"><a href="JavaScript:void(0);" onClick="checkForm();" class="ncap-btn-big ncap-btn-green" id="submitBtn">确认提交</a></div>
        </div>
        <input type="hidden" name="id" value="<?php echo $navigation['id']; ?>">
    </form>
</div>
<script type="text/javascript">
    // 判断输入框是否为空
    function checkForm(){
        var name = $("#addEditNavForm").find("input[name='name']").val();
        var link = $("#addEditNavForm").find("input[name='url']").val();
        if($.trim(name) == '')
        {
            $("#name_err").show();
            return false;
        }else {
            $("#name_err").hide();
        }
        if($.trim(link) == '')
        {
            $("#link_err").show();
            return false;
        }
        $('#addEditNavForm').submit();
    }
    //判断位置
    $("#position").change(function(){
        var position = $(this).find("option:selected").val();
        if(position == 1){
            $("#system_nav").show();
            $("#system_bottom").hide();
        }else if(position == 2 || position == 3){
            $("#system_nav").hide();
            $("#system_bottom").show();
        }
    });

    // 更改顶部系统内容
    $("#system_nav").change(function(){
        var text = $(this).find("option:selected").text();
        text = text.replace(/-/ig,"");
        var val  = $(this).find("option:selected").val();
        $("input[name='name']").val(text);
        $("input[name='url']").val(val);
    });

    //更改文章
    $("#system_bottom").change(function(){
        var text = $(this).find("option:selected").text();
        text = text.replace(/-/ig,"");
        var val  = $(this).find("option:selected").val();
        $("input[name='name']").val(text);
        $("input[name='url']").val(val);
    })
</script>
</body>
</html>