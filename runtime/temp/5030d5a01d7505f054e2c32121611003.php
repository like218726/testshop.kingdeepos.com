<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:46:"./application/admin/view/web/settingFloor.html";i:1587634376;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<body style="background-color: #FFF; overflow: auto;">
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="<?php echo U('Web/floorList'); ?>" title="返回板块区列表"><i class="fa fa-arrow-circle-o-left"></i></a>
      <div class="subject">
        <h3>首页管理 - 设置“红色”板块</h3>
        <h5>商城首页模板及广告设计</h5>
      </div>
     
    </div>
  </div>
  <form id="web_form" method="post" name="form1">
    <input type="hidden" name="web_id" value="<?php echo $web_config['web_id']; ?>" />
    <div class="ncap-form-default">
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>板块名称</label>
        </dt>
        <dd class="opt">
          <input id="web_name" name="web_name" value="<?php echo $web_config['web_name']; ?>" class="input-txt" type="text" maxlength="20">
          <span class="err"></span>
          <p class="notic">板块名称只在后台首页模板设置中作为板块标识出现，在前台首页不显示。</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>色彩风格</label>
        </dt>
        <dd class="opt">
          <input type="hidden" value="<?php echo (isset($web_config['style_name']) && ($web_config['style_name'] !== '')?$web_config['style_name']:'red'); ?>" name="style_name" id="style_name">
          <ul class="home-templates-board-style">
            <li class="red"><em></em><i class="fa fa-check-circle"></i>红色</li>
            <li class="pink"><em></em><i class="fa fa-check-circle"></i>粉色</li>
            <li class="orange"><em></em><i class="fa fa-check-circle"></i>橘色</li>
            <li class="green"><em></em><i class="fa fa-check-circle"></i>绿色</li>
            <li class="blue"><em></em><i class="fa fa-check-circle"></i>蓝色</li>
            <li class="purple"><em></em><i class="fa fa-check-circle"></i>紫色</li>
            <li class="brown"><em></em><i class="fa fa-check-circle"></i>褐色</li>
            <li class="default"><em></em><i class="fa fa-check-circle"></i>默认</li>
          </ul>
          <span class="err"></span>
          <p class="notic">选择板块色彩风格将影响商城首页模板该区域的边框、背景色、字体色彩，但不会影响板块的内容布局。</p>
        </dd>
      </dl>
      <?php if(empty($web_config) || (($web_config instanceof \think\Collection || $web_config instanceof \think\Paginator ) && $web_config->isEmpty())): ?>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>广告样式</label>
        </dt>
        <dd class="opt">
          <input type="radio" value="1" name="style" checked>样式一
          <input type="radio" value="2" name="style">样式二
        </dd>
      </dl>
      <?php endif; ?>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>排序</label>
        </dt>
        <dd class="opt">
          <input type="text" value="<?php echo $web_config['web_sort']; ?>" name="web_sort" id="web_sort" class="input-txt" onkeyup="this.value = this.value.replace(/[^\d]/g,'')">
          <span class="err"></span>
          <p class="notic">数字范围为0~255，数字越小越靠前</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">显示</dt>
        <dd class="opt">
          <div class="onoff">
            <label for="show1" class="cb-enable selected" title="是">是</label>
            <label for="show0" class="cb-disable " title="否">否</label>
            <input id="show1" name="web_show" checked="checked"  value="1" type="radio">
            <input id="show0" name="web_show"  value="0" type="radio">
          </div>
          <p class="notic"></p>
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn">确认提交</a></div>
    </div>
  </form>
</div>
<script>
//按钮先执行验证再提交表单
$(function(){
	var style_name = $('#style_name').val();
	$(".home-templates-board-style ."+style_name).addClass("selected");
	$("#submitBtn").click(function(){
    	if($("#web_form").valid()){
     		$("#web_form").submit();
		}
	});
	$(".home-templates-board-style li").click(function(){
    $(".home-templates-board-style li").removeClass("selected");
    $("#style_name").val($(this).attr("class"));
    	$(this).addClass("selected");
	});
	$("#web_form").validate({
		errorPlacement: function(error, element){
			var error_td = element.parent('dd').children('span.err');
            error_td.append(error);
        },
        rules : {
            web_name : {
                required : true
            },
            web_sort : {
                required : true,
                digits   : true
            }
        },
        messages : {
            web_name : {
                required : '<i class="fa fa-exclamation-circle"></i>板块名称不能为空'
            },
            web_sort  : {
                required : '<i class="fa fa-exclamation-circle"></i>排序仅可以为数字',
                digits   : '<i class="fa fa-exclamation-circle"></i>排序仅可以为数字'
            }
        }
	});
});

</script> 
<div id="goTop"> <a href="JavaScript:void(0);" id="btntop"><i class="fa fa-angle-up"></i></a><a href="JavaScript:void(0);" id="btnbottom"><i class="fa fa-angle-down"></i></a></div>
</body>
</html>