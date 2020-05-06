<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:40:"./application/admin/view/system/sms.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<style>
    .ncap-form-default dt.tit {
        min-width: 243px;
    }
</style>
<body style="background-color: #FFF; overflow: auto;">
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>商城设置</h3>
                <h5>网站全局内容基本选项设置</h5>
            </div>
            <ul class="tab-base nc-row">
                <?php if(is_array($group_list) || $group_list instanceof \think\Collection || $group_list instanceof \think\Paginator): if( count($group_list)==0 ) : echo "" ;else: foreach($group_list as $k=>$v): ?>
                    <li><a href="<?php echo U('System/index',['inc_type'=> $k]); ?>" <?php if($k==$inc_type): ?>class="current"<?php endif; ?>><span><?php echo $v; ?></span></a></li>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>
    <!-- 操作说明 -->
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="提示相关设置操作时应注意的要点">操作提示</h4>
            <span id="explanationZoom" title="收起提示"></span></div>
        <ul>
            <li>系统平台全局设置,包括基础设置、购物、短信、邮件、水印和分销等相关模块。</li>
        </ul>
    </div>
    <form method="post" enctype="multipart/form-data" name="form1" action="<?php echo U('System/handle'); ?>">
        <input type="hidden" name="inc_type" value="<?php echo $inc_type; ?>">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="sms_platform">选择短信平台</label>
                </dt>
                <dd class="opt">
                    <select name="sms_platform" id="sms_platform">
                        <option value="1" selected >阿里云</option>
                    </select>                    
                    <p class="notic">短信平台选择</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="sms_appkey">短信平台[appkey]</label>
                </dt>
                <dd class="opt">
                    <input id="sms_appkey" name="sms_appkey" value="<?php echo $config['sms_appkey']; ?>" class="input-txt" type="text"/>
                    <a style="color:red ;font-weight:bold ; margin-left:20px" href="http://help.tp-shop.cn/Index/Help/info/cat_id/5/id/513.html" target="_blank">查看短信接入手册</a>
                    <p class="notic">短信平台配置appkey/keyid</p>
                    
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="sms_secretKey">短信平台[secretKey]</label>
                </dt>
                <dd class="opt">
                    <input id="sms_secretKey" name="sms_secretKey" value="<?php echo $config['sms_secretKey']; ?>" class="input-txt" type="text"/>
                    <p class="notic">短信平台配置secretKey</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="sms_product">公司名/品牌名/产品名</label>
                </dt>
                <dd class="opt">
                    <input id="sms_product" name="sms_product" value="<?php echo $config['sms_product']; ?>" class="input-txt" type="text"/>
                    <p class="notic">公司名/品牌名/产品名</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">用户注册时</dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="regis_sms_enable1" class="cb-enable <?php if($config[regis_sms_enable] == 1): ?>selected<?php endif; ?>">开启</label>
                        <label for="regis_sms_enable0" class="cb-disable <?php if($config[regis_sms_enable] == 0): ?>selected<?php endif; ?>">关闭</label>
                        <input id="regis_sms_enable1" name="regis_sms_enable" <?php if($config['regis_sms_enable'] == 1): ?>checked="checked"<?php endif; ?> value="1" type="radio">
                        <input id="regis_sms_enable0" name="regis_sms_enable" <?php if($config['regis_sms_enable'] == 0): ?>checked="checked"<?php endif; ?> value="0" type="radio">
                    </div>
                    <p class="notic">用户注册时使用短信验证、（修改手机验证、修改支付密码验证）</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">用户找回密码时</dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="forget_pwd_sms_enable1" class="cb-enable <?php if($config[forget_pwd_sms_enable] == 1): ?>selected<?php endif; ?>">开启</label>
                        <label for="forget_pwd_sms_enable0" class="cb-disable <?php if($config[forget_pwd_sms_enable] == 0): ?>selected<?php endif; ?>">关闭</label>
                        <input id="forget_pwd_sms_enable1" name="forget_pwd_sms_enable" <?php if($config['forget_pwd_sms_enable'] == 1): ?>checked="checked"<?php endif; ?> value="1" type="radio">
                        <input id="forget_pwd_sms_enable0" name="forget_pwd_sms_enable" <?php if($config['forget_pwd_sms_enable'] == 0): ?>checked="checked"<?php endif; ?> value="0" type="radio">
                    </div>
                    <p class="notic">用户找回密码时使用短信验证</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">身份验证时</dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="bind_mobile_sms_enable1" class="cb-enable <?php if($config[bind_mobile_sms_enable] == 1): ?>selected<?php endif; ?>">开启</label>
                        <label for="bind_mobile_sms_enable0" class="cb-disable <?php if($config[bind_mobile_sms_enable] == 0): ?>selected<?php endif; ?>">关闭</label>
                        <input id="bind_mobile_sms_enable1" name="bind_mobile_sms_enable" <?php if($config['bind_mobile_sms_enable'] == 1): ?>checked="checked"<?php endif; ?> value="1" type="radio">
                        <input id="bind_mobile_sms_enable0" name="bind_mobile_sms_enable" <?php if($config['bind_mobile_sms_enable'] == 0): ?>checked="checked"<?php endif; ?> value="0" type="radio">
                    </div>
                    <p class="notic">用户身份验证时使用短信验证</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">用户下单时是否发送短信给商家</dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="order_add_sms_enable1" class="cb-enable <?php if($config[order_add_sms_enable] == 1): ?>selected<?php endif; ?>">开启</label>
                        <label for="order_add_sms_enable0" class="cb-disable <?php if($config[order_add_sms_enable] == 0): ?>selected<?php endif; ?>">关闭</label>
                        <input id="order_add_sms_enable1" name="order_add_sms_enable" <?php if($config['order_add_sms_enable'] == 1): ?>checked="checked"<?php endif; ?> value="1" type="radio">
                        <input id="order_add_sms_enable0" name="order_add_sms_enable" <?php if($config['order_add_sms_enable'] == 0): ?>checked="checked"<?php endif; ?> value="0" type="radio">
                    </div>
                    <p class="notic">用户下单时是否发短信给商家</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">客户支付时是否发短信给商家</dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="order_pay_sms_enable1" class="cb-enable <?php if($config[order_pay_sms_enable] == 1): ?>selected<?php endif; ?>">开启</label>
                        <label for="order_pay_sms_enable0" class="cb-disable <?php if($config[order_pay_sms_enable] == 0): ?>selected<?php endif; ?>">关闭</label>
                        <input id="order_pay_sms_enable1" name="order_pay_sms_enable" <?php if($config['order_pay_sms_enable'] == 1): ?>checked="checked"<?php endif; ?> value="1" type="radio">
                        <input id="order_pay_sms_enable0" name="order_pay_sms_enable" <?php if($config['order_pay_sms_enable'] == 0): ?>checked="checked"<?php endif; ?> value="0" type="radio">
                    </div>
                    <p class="notic">客户支付时是否发短信给商家</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">商家发货时是否给客户发短信</dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="order_shipping_sms_enable1" class="cb-enable <?php if($config[order_shipping_sms_enable] == 1): ?>selected<?php endif; ?>">开启</label>
                        <label for="order_shipping_sms_enable0" class="cb-disable <?php if($config[order_shipping_sms_enable] == 0): ?>selected<?php endif; ?>">关闭</label>
                        <input id="order_shipping_sms_enable1" name="order_shipping_sms_enable" <?php if($config['order_shipping_sms_enable'] == 1): ?>checked="checked"<?php endif; ?> value="1" type="radio">
                        <input id="order_shipping_sms_enable0" name="order_shipping_sms_enable" <?php if($config['order_shipping_sms_enable'] == 0): ?>checked="checked"<?php endif; ?> value="0" type="radio">
                    </div>
                    <p class="notic">商家发货时是否给客户发短信</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">购买虚拟商品支付成功后,是否向用户发送短信</dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="virtual_goods_sms_enable1" class="cb-enable <?php if($config[virtual_goods_sms_enable] == 1): ?>selected<?php endif; ?>">开启</label>
                        <label for="virtual_goods_sms_enable0" class="cb-disable <?php if($config[virtual_goods_sms_enable] == 0): ?>selected<?php endif; ?>">关闭</label>
                        <input id="virtual_goods_sms_enable1" name="virtual_goods_sms_enable" <?php if($config['virtual_goods_sms_enable'] == 1): ?>checked="checked"<?php endif; ?> value="1" type="radio">
                        <input id="virtual_goods_sms_enable0" name="virtual_goods_sms_enable" <?php if($config['virtual_goods_sms_enable'] == 0): ?>checked="checked"<?php endif; ?> value="0" type="radio">
                    </div>
                    <p class="notic">用户购买虚拟商品在支付成功后, 发送短信给用户, 提示兑换码已生成</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="sms_time_out">短信码超时时间</label>
                </dt>
                <dd class="opt">
                    <select id="sms_time_out" name="sms_time_out">
                        <option value="60" <?php if($config['sms_time_out'] == 60): ?>selected="selected"<?php endif; ?>>1分钟</option>
                        <option value="120"<?php if($config['sms_time_out'] == 120): ?>selected="selected"<?php endif; ?>>2分钟</option>
                        <option value="300"<?php if($config['sms_time_out'] == 300): ?>selected="selected"<?php endif; ?>>5分钟</option>
                        <option value="600"<?php if($config['sms_time_out'] == 600): ?>selected="selected"<?php endif; ?>>10分钟</option>
                        <option value="1200"<?php if($config['sms_time_out'] == 1200): ?>selected="selected"<?php endif; ?>>20分钟</option>
                        <option value="1800"<?php if($config['sms_time_out'] == 1800): ?>selected="selected"<?php endif; ?>>30分钟</option>
                    </select>
                    <p class="notic">发送短信验证码间隔时间</p>
                </dd>
            </dl>
            <div class="bot" style="padding-left: 16%"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">确认提交</a></div>
        </div>
    </form>
</div>
<div id="goTop"><a href="JavaScript:void(0);" id="btntop"><i class="fa fa-angle-up"></i></a><a href="JavaScript:void(0);" id="btnbottom"><i class="fa fa-angle-down"></i></a></div>
</body>
</html>