<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:43:"./application/admin/view/user/signRule.html";i:1587634376;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
        <h3>签到管理</h3>
        <h5>会员签到及管理设置</h5>
      </div>
      <ul class="tab-base nc-row">
      <li><a  href="<?php echo U('user/signList'); ?>" data-state="" ><span>签到列表</span></a></li>
      <li><a href="<?php echo U('user/signRule'); ?>" data-state="0" class="current" ><span>签到规则</span></a></li>
      
      </ul>
    </div>
  </div>
  <!-- 操作说明 -->
  <div id="explanation" class="explanation" style="color: rgb(44, 188, 163); background-color: rgb(237, 251, 248); width: 99%; height: 100%;">
    <div id="checkZoom" class="title"><i class="fa fa-lightbulb-o"></i>
      <h4 title="提示相关设置操作时应注意的要点">操作提示</h4>
      <span title="收起提示" id="explanationZoom" style="display: block;"></span>
    </div>
    <ul>
        <li>签到列表：签到人员记录，签到记录最多保持一个月</li>
        <li>签到规则：可设置功能开启关闭，签到积分和连续签到额外奖励</li>
    </ul>
  </div>
    <form method="post" enctype="multipart/form-data" id="signRuleForm" action="<?php echo U('User/signRule'); ?>">
        <input type="hidden" name="inc_type" value="sign">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label>签到送积分开关</label>
                </dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="switch1" class="cb-enable  <?php if($config['sign_on_off'] == 1): ?>selected<?php endif; ?>">开启</label>
                        <label for="switch0" class="cb-disable <?php if($config['sign_on_off'] == 0): ?>selected<?php endif; ?>">关闭</label>
                        <input type="radio" onclick="$('#switch_on_off').show();"  id="switch1"  name="sign_on_off" value="1" <?php if($config['sign_on_off'] == 1): ?>checked="checked"<?php endif; ?>>
                        <input type="radio" onclick="$('#switch_on_off').hide();" id="switch0" name="sign_on_off" value="0" <?php if($config['sign_on_off'] == 0): ?>checked="checked"<?php endif; ?> >
                    </div>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="sign_integral">签到送积分</label>
                </dt>
                <dd class="opt">
                    <input onkeyup="value=value.replace(/[^\d]/g,'')" onpaste="this.value=this.value.replace(/[^\d]/g,'')" pattern="^\d{1,}$" id="reg_integral" name="sign_integral" value="<?php echo (isset($config['sign_integral']) && ($config['sign_integral'] !== '')?$config['sign_integral']:1); ?>" class="input-txt" type="text">
                    <span class="err">只能输入整数</span>

                    <p class="notic">会员每次签到赠送的积分</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="sign_signcount">连续签到（天）</label>
                </dt>
                <dd class="opt">
                    <input  onkeyup="value=value.replace(/[^\d]/g,'')" onpaste="this.value=this.value.replace(/[^\d]/g,'')" pattern="^\d{1,}$" id="sign_signcount" name="sign_signcount" value="<?php echo (isset($config['sign_signcount']) && ($config['sign_signcount'] !== '')?$config['sign_signcount']:1); ?>" class="input-txt" type="text">
                    <span class="err">只能输入整数</span>

                    <p class="notic">会员连续签到可作为额外积分奖励</p>
                </dd>
            </dl>
            
            <dl class="row">
                <dt class="tit">
                    <label for="sign_award">连续签到积分奖励</label>
                </dt>
                <dd class="opt">
                    <input onkeyup="value=value.replace(/[^\d]/g,'')" onpaste="this.value=this.value.replace(/[^\d]/g,'')" pattern="^\d{1,}$" id="sign_award" name="sign_award" value="<?php echo (isset($config['sign_award']) && ($config['sign_award'] !== '')?$config['sign_award']:1); ?>" class="input-txt" type="text">
                    <span class="err">只能输入整数</span>

                    <p class="notic">会员连续签到积分奖励，满足连续签将按此积分设置每天增加此积分，直到断签为止</p>
                </dd>
            </dl>
            
         

            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="form_submit()">确认提交</a></div>
        </div>
    </form>
</div>
<script type="text/javascript">
    function form_submit() {
        var reg_integral =$('#reg_integral').val()
        var sign_signcount =$('#sign_signcount').val()
        var sign_award =$('#sign_award').val()
        if (sign_award < 1 || sign_signcount<1 || reg_integral<1){
            layer.msg('所有值只能输入大于1的整数',{icon:2});
            return false;
        }
        $('#signRuleForm').submit()
    }
</script>
<div id="goTop">
    <a href="JavaScript:void(0);" id="btntop">
        <i class="fa fa-angle-up"></i>
    </a>
    <a href="JavaScript:void(0);" id="btnbottom">
        <i class="fa fa-angle-down"></i>
    </a>
</div>
</body>
</html>