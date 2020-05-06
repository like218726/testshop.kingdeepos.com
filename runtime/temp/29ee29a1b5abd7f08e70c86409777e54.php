<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:51:"./application/admin/view/store/store_info_edit.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<script type="text/javascript" src="/public/static/js/layer/laydate/laydate.js"></script>
<script type="text/javascript" src="/public/static/js/perfect-scrollbar.min.js"></script>
<body style="background-color: #FFF; overflow: auto;">
<div id="toolTipLayer" style="position: absolute; z-index: 9999; display: none; visibility: visible; left: 95px; top: 573px;"></div>
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
	<div class="fixed-bar">
		<div class="item-title"><a class="back" href="javascript:history.back();" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
			<div class="subject">
				<h3>店铺管理 - 编辑店铺</h3>
				<h5>网站系统编辑店铺</h5>
			</div>
			<ul class="tab-base nc-row">
				<li><a class="current" onclick="$('#tab_store').show();$('#tab_info').hide();$(this).parent().parent().find('a').removeClass('current');$(this).addClass('current');"><span>店铺信息</span></a></li>
				<li><a onclick="$('#tab_info').show();$('#tab_store').hide();$(this).parent().parent().find('a').removeClass('current');$(this).addClass('current');"><span>注册信息</span></a></li>
			</ul>
		</div>
	</div>
	<form class="form-horizontal" method="post" id="store_info">
		<div class="ncap-form-default" id="tab_store">
			<dl class="row">
				<dt class="tit">
					<label>店铺账号</label>
				</dt>
				<dd class="opt">
					<input class="input-txt valid" value="<?php echo $store['seller_name']; ?>" readonly="" type="text">
					<p class="notic">会员昵称不可修改。</p>
				</dd>
			</dl>
			<dl class="row">
				<dt class="tit">
					<label>店铺名称</label>
				</dt>
				<dd class="opt">
					<input type="text" value="<?php echo $store['store_name']; ?>" name="store[store_name]" class="input-txt">
					<span class="err" id="err_goods_remark" style="color:#F00; display:none;"></span>
					<p class="notic">6-16位字母数字符号组合</p>
				</dd>
			</dl>
			<dl class="row">
				<dt class="tit">
					<label>店铺开通时间</label>
				</dt>
				<dd class="opt">
					<input class="input-txt valid" value="<?php echo date('Y-m-d H:i:s',$store['store_time']); ?>" readonly="" type="text">
					<p class="notic">不可修改。</p>
				</dd>
			</dl>
			<dl class="row">
				<dt class="tit">
					<label>店铺有效期</label>
				</dt>
				<dd class="opt">
					<?php if($store['store_end_time'] > 0): ?>
					<input class="input-txt valid" value="<?php echo date('Y-m-d H:i:s',$store['store_end_time']); ?>" id ="store_end_time" name="store_end_time"  type="text">
					<?php else: ?>
					<input class="input-txt valid" value="长期有效" id ="store_end_time" name="store_end_time" type="text">
					<?php endif; ?>
				</dd>
			</dl>
			<dl class="row">
				<dt class="tit">
					<label for="">店铺角色</label>
				</dt>
				<dd class="opt">
					<input type="checkbox" value="1" id="is_dealer" <?php if($store[is_dealer] == 1): ?>checked="checked"<?php endif; ?> name="is_dealer" class="checkbox right clefl right_goods" />销售商&nbsp;&nbsp;
					<input type="checkbox" value="1" id="is_supplier" <?php if($store[is_supplier] == 1): ?>checked="checked"<?php endif; ?> name="is_supplier" class="checkbox right clefl right_goods" />供应商
					<span class="err"></span>
					<p class="notic">选择店铺的角色，可多选</p>
				</dd>
			</dl>
			<dl class="row">
				<dt class="tit">
					<label>所属分类</label>
				</dt>
				<dd class="opt">
					<select name="store[sc_id]" style="width:200px;">
						<option value="0">请选择分类</option>
						<?php if(is_array($store_class) || $store_class instanceof \think\Collection || $store_class instanceof \think\Paginator): if( count($store_class)==0 ) : echo "" ;else: foreach($store_class as $k=>$v): ?>
							<option value="<?php echo $k; ?>" <?php if($k == $store['sc_id']): ?>selected="selected"<?php endif; ?>>
							<?php echo $v; ?>
							</option>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</select>
					<span class="err"></span>
					<p class="notic"></p>
				</dd>
			</dl>
			<dl class="row">
				<dt class="tit">
					<label>所属等级</label>
				</dt>
				<dd class="opt">
					<select name="store[grade_id]" id="grade_id" style="width:200px;">
						<option value="0">选择等级</option>
						<?php if(is_array($store_grade) || $store_grade instanceof \think\Collection || $store_grade instanceof \think\Paginator): if( count($store_grade)==0 ) : echo "" ;else: foreach($store_grade as $k=>$v): ?>
							<option value="<?php echo $k; ?>" <?php if($k == $store['grade_id']): ?>selected="selected"<?php endif; ?>>
							<?php echo $v; ?>
							</option>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</select>
					<span class="err"></span>
					<p class="notic"></p>
				</dd>
			</dl>
			<dl class="row">
				<dt class="tit">
					<label>店铺保证服务</label>
				</dt>
				<dd class="opt">
					<div class="onoff">
						<label for="ensure1" class="cb-enable <?php if($store[ensure] == 1): ?>selected<?php endif; ?>">开启</label>
						<label for="ensure0" class="cb-disable <?php if($store[ensure] == 0): ?>selected<?php endif; ?>">关闭</label>
						<input id="ensure1" name="store[ensure]" <?php if($store[ensure] == 1): ?>checked="checked"<?php endif; ?> value="1" type="radio">
						<input id="ensure0" name="store[ensure]" <?php if($store[ensure] == 0): ?>checked="checked"<?php endif; ?> value="0" type="radio">
					</div>
					<p class="notic"></p>
				</dd>
			</dl>
			<dl class="row">
				<dt class="tit">
					<label>保证金显示</label>
				</dt>
				<dd class="opt">
					<div class="onoff">
						<label for="deposit_icon1" class="cb-enable <?php if($store[deposit_icon] == 1): ?>selected<?php endif; ?>">开启</label>
						<label for="deposit_icon0" class="cb-disable <?php if($store[deposit_icon] == 0): ?>selected<?php endif; ?>">关闭</label>
						<input id="deposit_icon1" name="store[deposit_icon]" <?php if($store[deposit_icon] == 1): ?>checked="checked"<?php endif; ?> value="1" type="radio">
						<input id="deposit_icon0" name="store[deposit_icon]" <?php if($store[deposit_icon] == 0): ?>checked="checked"<?php endif; ?> value="0" type="radio">
					</div>
					<p class="notic"></p>
				</dd>
			</dl>
			<dl class="row">
				<dt class="tit">
					<label>保证金</label>
				</dt>
				<dd class="opt">
					<input type="text" name="store[deposit]" value="<?php echo $store['deposit']; ?>" class="input-txt">
					<span class="err"></span>
					<p class="notic">单位：元</p>
				</dd>
			</dl>
			<!--<dl class="row">
				<dt class="tit">
					<label>正品保障</label>
				</dt>
				<dd class="opt">
					<div class="onoff">
						<label for="certified1" class="cb-enable <?php if($store[certified] == 1): ?>selected<?php endif; ?>">开启</label>
						<label for="certified0" class="cb-disable <?php if($store[certified] == 0): ?>selected<?php endif; ?>">关闭</label>
						<input id="certified1" name="store[certified]" <?php if($store[certified] == 1): ?>checked="checked"<?php endif; ?> value="1" type="radio">
						<input id="certified0" name="store[certified]" <?php if($store[certified] == 0): ?>checked="checked"<?php endif; ?> value="0" type="radio">
					</div>
					<p class="notic"></p>
				</dd>
			</dl>
			-->
			<dl class="row">
				<dt class="tit">
					<label>七天退换</label>
				</dt>
				<dd class="opt">
					<div class="onoff">
						<label for="qitian1" class="cb-enable <?php if($store[qitian] == 1): ?>selected<?php endif; ?>">开启</label>
						<label for="qitian0" class="cb-disable <?php if($store[qitian] == 0): ?>selected<?php endif; ?>">关闭</label>
						<input id="qitian1" name="store[qitian]" <?php if($store[qitian] == 1): ?>checked="checked"<?php endif; ?> value="1" type="radio">
						<input id="qitian0" name="store[qitian]" <?php if($store[qitian] == 0): ?>checked="checked"<?php endif; ?> value="0" type="radio">
					</div>
					<p class="notic"></p>
				</dd>
			</dl>
			<!--
			<dl class="row">
				<dt class="tit">
					<label>两小时发货</label>
				</dt>
				<dd class="opt">
					<div class="onoff">
						<label for="two_hour1" class="cb-enable <?php if($store[two_hour] == 1): ?>selected<?php endif; ?>">开启</label>
						<label for="two_hour0" class="cb-disable <?php if($store[two_hour] == 0): ?>selected<?php endif; ?>">关闭</label>
						<input id="two_hour1" name="store[two_hour]" <?php if($store[two_hour] == 1): ?>checked="checked"<?php endif; ?> value="1" type="radio">
						<input id="two_hour0" name="store[two_hour]" <?php if($store[two_hour] == 0): ?>checked="checked"<?php endif; ?> value="0" type="radio">
					</div>
					<p class="notic"></p>
				</dd>
			</dl>
			<dl class="row">
				<dt class="tit">
					<label>货到付款</label>
				</dt>
				<dd class="opt">
					<div class="onoff">
						<label for="cod1" class="cb-enable <?php if($store[cod] == 1): ?>selected<?php endif; ?>">开启</label>
						<label for="cod0" class="cb-disable <?php if($store[cod] == 0): ?>selected<?php endif; ?>">关闭</label>
						<input id="cod1" name="store[cod]" <?php if($store[cod] == 1): ?>checked="checked"<?php endif; ?> value="1" type="radio">
						<input id="cod0" name="store[cod]" <?php if($store[cod] == 0): ?>checked="checked"<?php endif; ?> value="0" type="radio">
					</div>
					<p class="notic"></p>
				</dd>
			</dl>-->
			<dl class="row">
				<dt class="tit">
					<label>商品是否需要审核</label>
				</dt>
				<dd class="opt">
					<div class="onoff">
						<label for="goods_examine1" class="cb-enable <?php if($store[goods_examine] == 1): ?>selected<?php endif; ?>">开启</label>
						<label for="goods_examine0" class="cb-disable <?php if($store[goods_examine] == 0): ?>selected<?php endif; ?>">关闭</label>
						<input id="goods_examine1" name="store[goods_examine]" <?php if($store[goods_examine] == 1): ?>checked="checked"<?php endif; ?> value="1" type="radio">
						<input id="goods_examine0" name="store[goods_examine]" <?php if($store[goods_examine] == 0): ?>checked="checked"<?php endif; ?> value="0" type="radio">
					</div>
					<p class="notic"></p>
				</dd>
			</dl>
			<dl class="row">
				<dt class="tit">
					<label>店铺二级域名</label>
				</dt>
				<dd class="opt">
					<div class="onoff">
						<label for="domain_enable1" class="cb-enable <?php if($store[domain_enable] == 1): ?>selected<?php endif; ?>">开启</label>
						<label for="domain_enable0" class="cb-disable <?php if($store[domain_enable] == 0): ?>selected<?php endif; ?>">关闭</label>
						<input id="domain_enable1" name="store[domain_enable]" <?php if($store[domain_enable] == 1): ?>checked="checked"<?php endif; ?> value="1" type="radio">
						<input id="domain_enable0" name="store[domain_enable]" <?php if($store[domain_enable] == 0): ?>checked="checked"<?php endif; ?> value="0" type="radio">
					</div>
					<p class="notic"></p>
				</dd>
			</dl>
			<dl class="row">
				<dt class="tit">
					<label>状态</label>
				</dt>
				<dd class="opt">
					<div class="onoff">
						<label for="store_state1" onclick="$('#close_reason').hide();" class="cb-enable <?php if($store[store_state] == 1): ?>selected<?php endif; ?>">开启</label>
						<label for="store_state0" onclick="$('#close_reason').show();" class="cb-disable <?php if($store[store_state] == 0): ?>selected<?php endif; ?>">关闭</label>
						<input id="store_state1" name="store[store_state]" <?php if($store[store_state] == 1): ?>checked="checked"<?php endif; ?> value="1" type="radio">
						<input id="store_state0" name="store[store_state]" <?php if($store[store_state] == 0): ?>checked="checked"<?php endif; ?> value="0" type="radio">
					</div>
					<p class="notic"></p>
				</dd>
			</dl>
			<dl class="row" id="close_reason" <?php if($store[store_state]  == 1): ?>style="display:none;"<?php endif; ?>>
				<dt class="tit">关闭原因</dt>
				<dd class="opt">
					<textarea class="input-txt" name="store[store_close_info]"><?php echo $store['store_close_info']; ?></textarea>
					<span class="err"></span>
					<p class="notic"></p>
				</dd>
			</dl>
			<div class="bot"><a href="JavaScript:void(0);" onclick="actsubmit();" class="ncap-btn-big ncap-btn-green">确认提交</a></div>
		</div>

		<div class="ncap-form-default" id="tab_info" style="display: none">
			<table class="store-joinin" cellspacing="0" cellpadding="0" border="0">
				<thead>
				<tr>
					<th colspan="20">公司及联系人信息</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<th>公司名称：</th>
					<td><input name="company_name" value="<?php echo $apply['company_name']; ?>"></td>
					<th>公司性质：</th>
					<td>
						<select name="company_type">
							<?php if(is_array($company_type) || $company_type instanceof \think\Collection || $company_type instanceof \think\Paginator): if( count($company_type)==0 ) : echo "" ;else: foreach($company_type as $k=>$v): ?>
								<option value="<?php echo $k; ?>" <?php if($apply[company_type] == $k): ?>selected<?php endif; ?>><?php echo $v; ?></option>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
					</td>
					<th>公司网址：</th>
					<td><input name="company_website" value="<?php echo $apply['company_website']; ?>"></td>
				</tr>
				<tr>
					<th class="w150">公司所在地：</th>
					<td colspan="20">
						<select onchange="get_city(this,'<?php echo $apply['company_city']; ?>')" id="province" name="company_province">
							<?php if(is_array($province) || $province instanceof \think\Collection || $province instanceof \think\Paginator): $i = 0; $__LIST__ = $province;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
								<option value="<?php echo $vo['id']; ?>" <?php if($vo[id] == $apply[company_province]): ?>selected<?php endif; ?>><?php echo $vo['name']; ?></option>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
						<select onchange="get_area(this)" id="city" name="company_city">
							<?php if(is_array($city) || $city instanceof \think\Collection || $city instanceof \think\Paginator): $i = 0; $__LIST__ = $city;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
								<option value="<?php echo $vo['id']; ?>" <?php if($vo[id] == $apply[company_city]): ?>selected<?php endif; ?>><?php echo $vo['name']; ?></option>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
						<select id="district" name="company_district">
							<?php if(is_array($area) || $area instanceof \think\Collection || $area instanceof \think\Paginator): $i = 0; $__LIST__ = $area;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
								<option value="<?php echo $vo['id']; ?>" <?php if($vo[id] == $apply[company_district]): ?>selected<?php endif; ?>><?php echo $vo['name']; ?></option>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th>公司详细地址：</th>
					<td><input name="company_address" value="<?php echo $apply['company_address']; ?>"></td>
					<th>固定电话：</th>
					<td colspan="20"><input name="company_telephone" value="<?php echo $apply['company_telephone']; ?>"></td>
				</tr>
				<tr>
					<th>邮政编码：</th>
					<td><input name="company_zipcode" value="<?php echo $apply['company_zipcode']; ?>"></td>
					<th>电子邮箱：</th>
					<td><input name="company_email" value="<?php echo $apply['company_email']; ?>"></td>
					<th>传真：</th>
					<td><input name="company_fax" value="<?php echo $apply['company_fax']; ?>"></td>
				</tr>
				<tr>
					<th>联系人姓名：</th>
					<td><input name="contacts_name" value="<?php echo $apply['contacts_name']; ?>" class="form-control"></td>
					<th>联系人电话：</th>
					<td><input name="contacts_mobile" value="<?php echo $apply['contacts_mobile']; ?>" class="form-control"></td>
					<th>联系人邮箱：</th>
					<td><input name="contacts_email" value="<?php echo $apply['contacts_email']; ?>" class="form-control"></td>
				</tr>
				</tbody>
			</table>
			<table class="store-joinin" cellspacing="0" cellpadding="0" border="0">
				<thead>
				<tr>
					<th colspan="20">营业执照信息（副本）</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<th class="w150">营业执照号：</th>
					<td><input name="business_licence_number" value="<?php echo $apply['business_licence_number']; ?>" class="form-control"></td>
				</tr>
				<tr>
					<th>营业执照有效期：</th>
					<td>
                        <input name="business_date_start" value="<?php echo $apply['business_date_start']; ?>" id="business_date_start" class="form-control"> - <input name="business_date_end" id="business_date_end" value="<?php echo $apply['business_date_end']; ?>" class="form-control" <?php if($apply['business_date_end'] == '长期'): ?>disabled<?php endif; ?>>
                        <label class="fl label_line ml10 mr10" for="3"><input type="checkbox" hidefocus="ture" name="business_permanent" value=""  class="inputcheck" id="3" onclick="changeBusinessDate();" <?php if($apply['business_date_end'] == '长期'): ?>checked<?php endif; ?>>长期</label>
                    </td>
				</tr>
				<tr>
					<th>法定经营范围：</th>
					<td colspan="20"><textarea rows="3" cols="40" name="business_scope"><?php echo $apply['business_scope']; ?></textarea></td>
				</tr>
				</tbody>
			</table>
			<table class="store-joinin" cellspacing="0" cellpadding="0" border="0">
				<thead>
				<tr>
					<th colspan="20">组织机构代码证</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<th>注册资本：</th>
					<td colspan="20"><input name="reg_capital" value="<?php echo $apply['reg_capital']; ?>" class="form-control"></td>
				</tr>
				<tr>
					<th>组织机构代码：</th>
					<td colspan="20"><input name="orgnization_code" value="<?php echo $apply['orgnization_code']; ?>" class="form-control"></td>
				</tr>
				<tr>
					<th>法人代表姓名：</th>
					<td colspan="20"><input name="legal_person" value="<?php echo $apply['legal_person']; ?>" class="form-control"></td>
				</tr>
				<tr>
					<th>纳税类型税码：</th>
					<td colspan="20">
						<select id="tax_rate" name="tax_rate" class="input145 mr10">
							<option value="">请选择</option>
							<?php if(is_array($rate_list) || $rate_list instanceof \think\Collection || $rate_list instanceof \think\Paginator): if( count($rate_list)==0 ) : echo "" ;else: foreach($rate_list as $k=>$vo): ?>
								<option value="<?php echo $k; ?>" <?php if($apply[tax_rate] == $k): ?>selected<?php endif; ?>><?php echo $vo; ?>%</option>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th>税务登记号码：</th>
					<td colspan="20"><input name="attached_tax_number" value="<?php echo $apply['attached_tax_number']; ?>" class="form-control"></td>
				</tr>
				</tbody>
			</table>
			<!--<table class="store-joinin" cellspacing="0" cellpadding="0" border="0">
				<thead>
				<tr>
					<th colspan="20">一般纳税人证明：</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<th>一般纳税人证明：</th>
					<td colspan="20">
						<img src="<?php echo $apply['taxpayer_cert']; ?>" height="120">
					</td>
				</tr>
				</tbody>
			</table>-->
			<table class="store-joinin" cellspacing="0" cellpadding="0" border="0">
				<thead>
				<tr>
					<th colspan="20">开户银行信息：</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<th class="w150">银行开户名：</th>
					<td><input name="bank_account_number" value="<?php echo $apply['bank_account_name']; ?>" class="form-control"></td>
				</tr>
				<tr>
					<th>公司银行账号：</th>
					<td><input name="bank_account_name" value="<?php echo $apply['bank_account_number']; ?>" class="form-control"></td>
				</tr>
				<tr>
					<th>开户人身份证：</th>
					<td><input name="store_person_identity" value="<?php echo $apply['store_person_identity']; ?>" class="form-control"></td>
				</tr>
				<tr>
					<th>开户银行支行名称：</th>
					<td><input name="bank_branch_name" value="<?php echo $apply['bank_branch_name']; ?>" class="form-control"></td>
				</tr>
				<tr>
					<th>开户银行所在地：</th>
					<td colspan="20">
						<select onblur="get_blank_city(this,'<?php echo $apply[bank_city]; ?>')" onchange="get_blank_city(this,'<?php echo $apply[bank_city]; ?>')" name="bank_province" id="bank_province">
							<option value="0">选择省份</option>
							<?php if(is_array($province) || $province instanceof \think\Collection || $province instanceof \think\Paginator): $i = 0; $__LIST__ = $province;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
								<option value="<?php echo $vo['id']; ?>" <?php if($vo[id] == $apply[bank_province]): ?>selected<?php endif; ?>><?php echo $vo['name']; ?></option>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
						<select name="bank_city" id="bank_city">
							<option value="0">选择城市</option>
							<?php if(is_array($city) || $city instanceof \think\Collection || $city instanceof \think\Paginator): $i = 0; $__LIST__ = $city;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
								<option value="<?php echo $vo['id']; ?>"><?php echo $vo['name']; ?></option>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
					</td>
				</tr>
				<script>
					/**
					 * 获取城市
					 */
					function get_blank_city(t,selected){
						var parent_id = $(t).val();
						if(!parent_id > 0){
							return;
						}
						var url = '/index.php?m=Home&c=Api&a=getRegion&level=2&parent_id='+ parent_id+"&selected="+selected;
						$.ajax({
							type : "GET",
							url  : url,
							error: function(request) {
								alert("服务器繁忙, 请联系管理员!");
								return;
							},
							success: function(v) {
								v = '<option value="0">选择城市</option>'+ v;
								$('#bank_city').empty().html(v);
							}
						});
					}

					</script>
				</tbody>
			</table>

			<table class="store-joinin" cellspacing="0" cellpadding="0" border="0">
				<thead>
				<tr>
					<th colspan="20">店铺经营信息</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<th class="w150">店铺负责人：</th>
					<td><input name="store_person_name" value="<?php echo $apply['store_person_name']; ?>" class="form-control"></td>
				</tr>
				<tr>
					<th class="w150">负责人手机号码：</th>
					<td><input name="store_person_mobile" value="<?php echo $apply['store_person_mobile']; ?>" class="form-control"></td>
				</tr>
				<tr>
					<th>负责人QQ号码：</th>
					<td><input name="store_person_qq" value="<?php echo $apply['store_person_qq']; ?>" class="form-control"></td>
				</tr>
				<tr>
					<th class="w150">负责人邮箱：</th>
					<td><input type="text" name="store_person_email" value="<?php echo $apply['store_person_email']; ?>" class="form-control"></td>
				</tr>
				</tbody>
			</table>
			<table class="store-joinin" cellspacing="0" cellpadding="0" border="0">
				<thead>
				<tr>
					<th colspan="20">证件信息：</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<th>企业营业执照副本：</th>
					<td colspan="20">
						<a target="_blank" href="<?php echo $apply['business_licence_cert']; ?>">
							<?php if($apply[business_licence_cert] == ''): ?>
								<img id="business_licence_cert" src="/public/images/not_adv.jpg" height="120">
								<?php else: ?>
								<img id="business_licence_cert" src="<?php echo $apply['business_licence_cert']; ?>" height="120">
							<?php endif; ?>
						</a>
						<input type="hidden" name="business_licence_cert" value="<?php echo $apply['business_licence_cert']; ?>">
						<input type="button" class="btn btn-default" onClick="upload_img('business_licence_cert')"  value="上传图片"/>
					</td>
				</tr>
				<tr <?php if($apply['is_three'] == 1): ?>hidden<?php endif; ?>>
					<th>税务登记证复印件：</th>
					<td colspan="20">
						<a href="<?php echo $apply['taxpayer_cert']; ?>" target="_blank">
							<?php if($apply[taxpayer_cert] == ''): ?>
								<img id="taxpayer_cert" src="/public/images/not_adv.jpg" height="120">
								<?php else: ?>
								<img id="taxpayer_cert" src="<?php echo $apply['taxpayer_cert']; ?>" height="120">
							<?php endif; ?>
						</a>
						<input type="hidden" name="taxpayer_cert" value="<?php echo $apply['taxpayer_cert']; ?>">
						<input type="button" class="btn btn-default" onClick="upload_img('taxpayer_cert')"  value="上传图片"/>
					</td>
				</tr>
				<tr <?php if($apply['is_three'] == 1): ?>hidden<?php endif; ?>>
					<th>织机构代码证复印件：</th>
					<td colspan="20">
						<a href="<?php echo $apply['orgnization_cert']; ?>" target="_blank">
							<?php if($apply[orgnization_cert] == ''): ?>
								<img id="orgnization_cert" src="/public/images/not_adv.jpg" height="120">
								<?php else: ?>
								<img id="orgnization_cert" src="<?php echo $apply['orgnization_cert']; ?>" height="120">
							<?php endif; ?>
						</a>
						<input type="hidden" name="orgnization_cert" value="<?php echo $apply['orgnization_cert']; ?>">
						<input type="button" class="btn btn-default" onClick="upload_img('orgnization_cert')"  value="上传图片"/>
					</td>
				</tr>
				<tr>
					<th>法人身份证：</th>
					<td colspan="20">
						<a href="<?php echo $apply['legal_identity_cert']; ?>" target="_blank">
							<?php if($apply[legal_identity_cert] == ''): ?>
								<img id="legal_identity_cert" src="/public/images/not_adv.jpg" height="120">
								<?php else: ?>
								<img id="legal_identity_cert" src="<?php echo $apply['legal_identity_cert']; ?>" height="120">
							<?php endif; ?>
						</a>
						<input type="hidden" name="legal_identity_cert" value="<?php echo $apply['legal_identity_cert']; ?>">
						<input type="button" class="btn btn-default" onClick="upload_img('legal_identity_cert')"  value="上传图片"/>

						<a href="<?php echo $apply['legal_identity1']; ?>" target="_blank">
							<?php if($apply[legal_identity1] == ''): ?>
								<img id="legal_identity1" src="/public/images/not_adv.jpg" height="120">
								<?php else: ?>
								<img id="legal_identity1" src="<?php echo $apply['legal_identity1']; ?>" height="120">
							<?php endif; ?>
						</a>
						<input type="hidden" name="legal_identity1" value="<?php echo $apply['legal_identity1']; ?>">
						<input type="button" class="btn btn-default" onClick="upload_img('legal_identity1')"  value="上传图片"/>

						<a href="<?php echo $apply['legal_identity2']; ?>" target="_blank">
							<?php if($apply[legal_identity2] == ''): ?>
								<img id="legal_identity2" src="/public/images/not_adv.jpg" height="120">
								<?php else: ?>
								<img id="legal_identity2" src="<?php echo $apply['legal_identity2']; ?>" height="120">
							<?php endif; ?>
						</a>
						<input type="hidden" name="legal_identity2" value="<?php echo $apply['legal_identity2']; ?>">
						<input type="button" class="btn btn-default" onClick="upload_img('legal_identity2')"  value="上传图片"/>
					</td>
				</tr>
				<tr>
					<th>店铺负责人身份证：</th>
					<td colspan="20">
						<a href="<?php echo $apply['store_person_cert']; ?>" target="_blank">
							<?php if($apply[store_person_cert] == ''): ?>
								<img id="store_person_cert" src="/public/images/not_adv.jpg" height="120">
								<?php else: ?>
								<img id="store_person_cert" src="<?php echo $apply['store_person_cert']; ?>" height="120">
							<?php endif; ?>
						</a>
						<input type="hidden" name="store_person_cert" value="<?php echo $apply['store_person_cert']; ?>">
						<input type="button" class="btn btn-default" onClick="upload_img('store_person_cert')"  value="上传图片"/>

						<a href="<?php echo $apply['store_identity1']; ?>" target="_blank">
							<?php if($apply[store_identity1] == ''): ?>
								<img id="store_identity1" src="/public/images/not_adv.jpg" height="120">
								<?php else: ?>
								<img id="store_identity1" src="<?php echo $apply['store_identity1']; ?>" height="120">
							<?php endif; ?>
						</a>
						<input type="hidden" name="store_identity1" value="<?php echo $apply['store_identity1']; ?>">
						<input type="button" class="btn btn-default" onClick="upload_img('store_identity1')"  value="上传图片"/>

						<a href="<?php echo $apply['store_identity2']; ?>" target="_blank">
							<?php if($apply[store_identity2] == ''): ?>
								<img id="store_identity2" src="/public/images/not_adv.jpg" height="120">
								<?php else: ?>
								<img id="store_identity2" src="<?php echo $apply['store_identity2']; ?>" height="120">
							<?php endif; ?>
						</a>
						<input type="hidden" name="store_identity2" value="<?php echo $apply['store_identity2']; ?>">
						<input type="button" class="btn btn-default" onClick="upload_img('store_identity2')"  value="上传图片"/>
					</td>
				</tr>
				<tr>
					<th>行业证件补充:</th>
					<td colspan="20">
						<a href="<?php echo $apply['industry_cert1']; ?>" target="_blank">
							<?php if($apply[industry_cert1] == ''): ?>
								<img id="industry_cert1" src="/public/images/not_adv.jpg" height="120">
								<?php else: ?>
								<img id="industry_cert1" src="<?php echo $apply['industry_cert1']; ?>" height="120">
							<?php endif; ?>
						</a>
						<input type="hidden" name="industry_cert1" value="<?php echo $apply['industry_cert1']; ?>">
						<input type="button" class="btn btn-default" onClick="upload_img('industry_cert1')"  value="上传图片"/>

						<a href="<?php echo $apply['industry_cert2']; ?>" target="_blank">
							<?php if($apply[industry_cert2] == ''): ?>
								<img id="industry_cert2" src="/public/images/not_adv.jpg" height="120">
								<?php else: ?>
								<img id="industry_cert2" src="<?php echo $apply['industry_cert2']; ?>" height="120">
							<?php endif; ?>
						</a>
						<input type="hidden" name="industry_cert2" value="<?php echo $apply['industry_cert2']; ?>">
						<input type="button" class="btn btn-default" onClick="upload_img('industry_cert2')"  value="上传图片"/>
					</td>
				</tr>
				</tbody>
			</table>
			<div class="bot"><a href="JavaScript:void(0);" onclick="actsubmit();" class="ncap-btn-big ncap-btn-green">确认提交</a></div>
		</div>
		<input type="hidden" name="store[user_id]" value="<?php echo $store['user_id']; ?>">
		<input type="hidden" name="store_id" value="<?php echo $store['store_id']; ?>">
	</form>
</div>
<script type="text/javascript">
	$(document).ready(function(){

		// 加载默认选中
		var province_id = $('#province').val();
		if(province_id != 0){
			$('#province').trigger('blur');
		}
		var bank_province_id = $('#bank_province').val();
		if(bank_province_id != 0){
			$('#bank_province').trigger('blur');
		}
		// 加载默认选中
		<?php if($apply[company_district] > 0): ?>
				set_area('<?php echo $apply[company_city]; ?>','<?php echo $apply[company_district]; ?>');
		<?php endif; ?>
	});
	
	$('#store_end_time').layDate();
    $('#business_date_start').layDate();
    $('#business_date_end').layDate();

	/**
	 * 获取地区
	 */
	function set_area(parent_id,selected){
		if(parent_id <= 0){
			return;
		}
		$('#district').empty().css('display','inline');
		var url = '/index.php?m=Home&c=Api&a=getRegion&level=3&parent_id='+ parent_id+"&selected="+selected;
		$.ajax({
			type : "GET",
			url  : url,
			error: function(request) {
				layer.alert('服务器繁忙, 请联系管理员', {icon: 2});
				return;
			},
			success: function(v) {
				v = '<option>选择区域</option>'+ v;
				$('#district').empty().html(v);
			}
		});
	}
	var flag = true;
	function actsubmit(){
		if($('input[name=store_name]').val() == ''){
			layer.msg("店铺名称不能为空", {icon: 2,time: 2000});
			return;
		}
        if($('#store_end_time').val() == ''){
			layer.msg("店铺有效期不能为空", {icon: 2,time: 2000});
			return;
		}
        if($('#business_date_start').val() == ''){
			layer.msg("营业执照有效期不能为空", {icon: 2,time: 2000});
			return;
		}
		if(flag){
			$('#store_info').submit();
		}else{
			layer.msg("请检查店铺名称和卖家账号", {icon: 2,time: 2000});
		}
	}

    function changeBusinessDate(){
        var v = document.getElementById("3");
        if (v.checked==true) {
            document.getElementById("3").value="Y";
            document.getElementById("business_date_end").value="长期";
            document.getElementById("business_date_end").realvalue="";
            document.getElementById("business_date_end").disabled = true;
        } else {
            document.getElementById("3").value="N";
            document.getElementById("business_date_end").disabled = false;
        }
    }

	var tmp_type = '';
	function upload_img(cert_type){
		tmp_type = cert_type;
		GetUploadify(1,'store','cert','callback');
	}

	function callback(img_str){
		$('#'+tmp_type).attr('src',img_str);
		$('input[name='+tmp_type+']').val(img_str);
	}
</script>
</body>
</html>