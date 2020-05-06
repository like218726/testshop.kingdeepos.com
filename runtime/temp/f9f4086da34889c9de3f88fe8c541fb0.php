<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:55:"./application/admin/view/sms_template/_smsTemplate.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<div id="toolTipLayer" style="position: absolute; z-index: 9999; display: none; visibility: visible; left: 95px; top: 573px;"></div>
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="javascript:history.back();" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>短信配置模板 - 编辑模板</h3>
                <h5>网站系统短信配置模板管理</h5>
            </div>
        </div>
    </div>
    <form class="form-horizontal" id="addEditSmsTemplate" method="post">
        <input type="hidden" name="tpl_id" value="<?php echo $smsTpl['tpl_id']; ?>">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="sms_sign"><em>*</em>短信签名</label>
                </dt>
                <dd class="opt">
                    <input type="text" value="<?php echo $smsTpl['sms_sign']; ?>" name="sms_sign" id="sms_sign" class="input-txt">
                    <span class="err" id="err_sms_sign" style="display:none;">短信签名不能为空 </span>
                    <p class="notic">必须与短信平台短信签名一致</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="sms_tpl_code"><em>*</em>短信模板ID</label>
                </dt>
                <dd class="opt">
                    <input type="text" value="<?php echo $smsTpl['sms_tpl_code']; ?>" name="sms_tpl_code" id="sms_tpl_code" class="input-txt">
                    <span id="err_sms_tpl_code" class="err" style="display:none;">短信模板ID不能为空</span>
                    <p class="notic">必须与短信平台短信模板ID一致</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="send_scene"><em>*</em>发送场景</label>
                </dt>
                <dd class="opt">
                    <?php if($send_scene_id > 0): ?>
                        <?php echo $send_name; ?>
                        <input type="hidden" value="<?php echo $send_scene_id; ?>" name="send_scene" id="send_scene" class="form-control" style="width:250px;"/>
                        <?php else: ?>
                        <select  class="small form-control" name="send_scene" id="send_scene" onblur="changeContent(this.value);">
                            <option value="-1">请选择使用场景</option>
                            <?php if(is_array($send_scene) || $send_scene instanceof \think\Collection || $send_scene instanceof \think\Paginator): if( count($send_scene)==0 ) : echo "" ;else: foreach($send_scene as $k=>$v): ?>
                                <option value="<?php echo $k; ?>" <?php if($k == $smsTpl['send_scene']): ?>selected="selected"<?php endif; ?>><?php echo $v[0]; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                        <span class="err" id="err_send_scene" style="display:none;">请选择使用场景</span>
                    <?php endif; ?>
                    <span class="err"></span>
                    <p class="notic">使用场景</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="tpl_content"><em>*</em>短信内容</label>
                </dt>
                <dd class="opt">
                    <textarea id="tpl_content" name="tpl_content" class="tarea" rows="6"><?php echo $smsTpl['tpl_content']; ?></textarea>
                    <span class="err" id="err_tpl_content" style="display:none;">短信内容不能为空</span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <div class="bot"><a href="JavaScript:void(0);" onclick="checkForm();" class="ncap-btn-big ncap-btn-green" id="submitBtn">确认提交</a></div>
        </div>
    </form>
</div>
<script type="text/javascript">
    function changeContent(scene){
        if(scene == -1){
            $("#addEditSmsTemplate").find("textarea[name='tpl_content']").val('');
            return;
        }
        var scenes = <?php echo json_encode($send_scene); ?>;//<?php echo $send_scene; ?> //""<?php echo json_encode($send_scene);  ?>;
        var txt = scenes[scene][1];

        $("#addEditSmsTemplate").find("textarea[name='tpl_content']").val(txt);
    }
    // 判断输入框是否为空
    function checkForm(){

        var smsSign = $("#addEditSmsTemplate").find("input[name='sms_sign']").val();					//短信签名
        var smsTplCode = $("#addEditSmsTemplate").find("input[name='sms_tpl_code']").val();		//模板ID
        var tplContent = $("#addEditSmsTemplate").find("textarea[name='tpl_content']").val();			//模板内容

        var sendscene = $("#send_scene option:selected").val();
        if($.trim(smsSign) == '')
        {
            $("#err_sms_sign").show();
            return false;
        }

        if($.trim(smsTplCode) == '')
        {
            $("#err_sms_tpl_code").show();
            return false;
        }

        if($.trim(tplContent) == '')
        {
            $("#err_tpl_content").show();
            return false;
        }

        if(sendscene == -1){
            $("#err_send_scene").show();
            return false;
        }
        $('#addEditSmsTemplate').submit();
    }
</script>
</body>
</html>