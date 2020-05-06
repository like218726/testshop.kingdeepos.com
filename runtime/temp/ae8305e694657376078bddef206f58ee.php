<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:46:"./application/admin/view/system/subdomain.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>二级域名</h3>
                <h5>商城店铺使用二级域名管理设置</h5>
            </div>
			<ul class="tab-base nc-row">
	        	<li><a href="<?php echo U('System/index',array('inc_type'=>'subdomain')); ?>" class="current">设置</a></li>
	       	 	<li><a href="<?php echo U('Store/domain_list'); ?>">域名列表</a></li>
	      	</ul>
        </div>
    </div>
    <!-- 操作说明 -->
    <form method="post" id="handlepost" action="<?php echo U('System/handle'); ?>" enctype="multipart/form-data" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
    	<div class="ncap-form-default">
	      <dl class="row">
	        <dt class="tit">
	          <label>是否启用二级域名</label>
	        </dt>
	        <dd class="opt">
	          <div class="onoff">
	          <label for="enabled_subdomain1" class="cb-enable <?php if($config[enabled_subdomain] == 1): ?>selected<?php endif; ?>" title="是">是</label>
	          <label for="enabled_subdomain0" class="cb-disable <?php if($config[enabled_subdomain] == 0): ?>selected<?php endif; ?>" title="否">否</label>
	          <input type="radio" id="enabled_subdomain1" value="1" <?php if($config[enabled_subdomain] == 1): ?> checked <?php endif; ?> name="enabled_subdomain">
	          <input type="radio" id="enabled_subdomain0" <?php if($config[enabled_subdomain] == 0): ?> checked <?php endif; ?> value="0" name="enabled_subdomain">
	          <span class="err"></span>
	          <p class="notic">启用二级域名需要您的服务器支持泛域名解析</p>
	        </div></dd>
	      </dl>
	      <dl class="row">
	        <dt class="tit">
	          <label>是否可修改</label>
	        </dt>
	        <dd class="opt">
	          <div class="onoff">
	            <label for="subdomain_edit1" class="cb-enable <?php if($config[subdomain_edit] == 1): ?>selected<?php endif; ?>" title="是">是</label>
	            <label for="subdomain_edit0" class="cb-disable <?php if($config[subdomain_edit] == 0): ?>selected<?php endif; ?>" title="否">否</label>
	            <input type="radio" id="subdomain_edit1" value="1" <?php if($config[subdomain_edit] == 1): ?> checked <?php endif; ?> name="subdomain_edit">
	            <input type="radio" id="subdomain_edit0" <?php if($config[subdomain_edit] == 0): ?> checked <?php endif; ?> value="0" name="subdomain_edit">
	          </div>
	          <p class="notic">不可修改时店主填写提交后将不可改动</p>
	        </dd>
	      </dl>
	      <dl class="row">
	        <dt class="tit">
	          <label for="subdomain_times"><em>*</em>修改次数</label>
	        </dt>
	        <dd class="opt">
	          <input type="text" value="<?php echo (isset($config['subdomain_times']) && ($config['subdomain_times'] !== '')?$config['subdomain_times']:3); ?>" name="subdomain_times" id="subdomain_times" class="input-txt valid" style=" width:50px;">
	          <span class="err"></span>
	          <p class="notic">可修改时达到设定的次数后将不能再改动</p>
	        </dd>
	      </dl>
	      <dl class="row">
	        <dt class="tit">
	          <label for="subdomain_reserved">保留域名</label>
	        </dt>
	        <dd class="opt">
	          <input type="text" value="<?php echo $config['site_domain']; ?>" name="site_domain" id="site_domain" class="input-txt valid">
	          <span class="err"></span>
	          <p class="notic">保留的二级域名，多个保留域名之间请用","隔开</p>
	        </dd>
	      </dl>
	      <dl class="row">
	        <dt class="tit">
	          <label for="subdomain_length"><em>*</em>长度限制</label>
	        </dt>
	        <dd class="opt">
	          <input type="text" value="<?php echo (isset($config['subdomain_length']) && ($config['subdomain_length'] !== '')?$config['subdomain_length']:'3-12'); ?>" name="subdomain_length" id="subdomain_length" class="input-txt">
	          <span class="err"></span>
	          <p class="notic">如"3-12"，代表注册的域名长度限制在3到12个字符之间</p>
	        </dd>
	      </dl>
            <div class="bot" style="padding-left: 10.8%">
                <input type="hidden" name="inc_type" value="<?php echo $inc_type; ?>">
                <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="check_form();">确认提交</a>
            </div>
        </div>
    </form>
</div>
<div id="goTop"> <a href="JavaScript:void(0);" id="btntop"><i class="fa fa-angle-up"></i></a><a href="JavaScript:void(0);" id="btnbottom"><i class="fa fa-angle-down"></i></a></div>
</body>
<script type="text/javascript">
    function check_form()
    {
        if(!$('#site_domain').val()){
            layer.alert('保留域名 非空！',{icon:2});
            return false;
        }
        document.form1.submit()
    }
</script>
</html>