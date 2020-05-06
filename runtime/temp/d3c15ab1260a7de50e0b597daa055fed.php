<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:49:"./application/admin/view/wechat/template_msg.html";i:1587634376;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>消息提醒</h3>
                <h5>消息模板列表</h5>
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
            <li>消息提醒，即微信模板消息，需要先登录微信公众号平台，添加插件，申请开通模板消息。</li>
            <li>然后选择填写所在行业： IT科技/互联网|电子商务，如果有其他行业则选填：IT科技/电子技术。每月可更改1次所选行业</li>
            <li>启用列表所需要的模板消息，即可在相应事件触发模板消息；编辑模板消息备注，可增加显示自定义提示消息内容</li>
            <li>每个公众号账号可以同时使用25个模板，超过将无法使用模板消息功能。</li>
            <li>如果在使用中发现使用模板超出了25个，但这里并没有使用这么多，可能是微信后台本来就已有其他的模板，请前往自行删除</li>
        </ul>
    </div>
    <div class="flexigrid">
        <div class="mDiv">
            <div class="ftitle">
                <h3>消息模板列表</h3>
                <h5>(共<?php echo count($tpls); ?>条记录)</h5>
            </div>
            <div title="刷新数据" class="pReload"><i class="fa fa-refresh"></i></div>
        </div>
        <div class="hDiv">
            <div class="hDivBox">
                <table cellspacing="0" cellpadding="0">
                    <thead>
                    <tr>
                        <th class="sign" axis="col6">
                            <div style="width: 24px;"><i class="ico-check"></i></div>
                        </th>
                        <th align="left" abbr="" axis="col6" class="">
                            <div style="text-align: center; width:200px;" class="">模板标题</div>
                        </th>
                        <th align="left" abbr="" axis="col6" class="">
                            <div style="text-align: center; width:150px;" class="">模板编号</div>
                        </th>
                        <th align="center" abbr="" axis="col6" class="">
                            <div style="text-align: center; width: 150px;" class="">添加时间</div>
                        </th>
                        <th align="center" abbr="" axis="col6" class="">
                            <div style="text-align: center; width: 80px;" class="">是否启用</div>
                        </th>
                        <th align="center" abbr="" axis="col6" class="">
                            <div style="text-align: center; width: 150px;">操作</div>
                        </th>
                        <th style="width:100%" axis="col7">
                            <div></div>
                        </th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div  id="flexigrid" class="bDiv" style="height: auto;">
            <!--ajax 返回 -->
            <table>
                <tbody>
                <?php if(is_array($tpls) || $tpls instanceof \think\Collection || $tpls instanceof \think\Paginator): $i = 0; $__LIST__ = $tpls;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?>
                    <tr data-id="<?php echo $list['openid']; ?>">
                        <td class="sign" axis="col6">
                            <div style="width: 24px;"><i class="ico-check"></i></div>
                        </td>
                        <td align="center" axis="col0">
                            <div style="text-align: center; width: 200px;"><?php echo $list['title']; ?></div>
                        </td>
                        <td align="center" axis="col0">
                            <div style="text-align: center; width: 150px;"><?php echo $list['template_sn']; ?></div>
                        </td>
                        <td align="center" axis="col0">
                            <div style="text-align: center; width: 150px;"><?php echo !empty($user_tpls[$list['template_sn']])?date('Y-m-d H:i:s',$user_tpls[$list['template_sn']][add_time]) : ''; ?></div>
                        </td>
                        <td align="center" axis="col0">
                            <div style="text-align: center; width: 80px;">
                                <?php if($user_tpls[$list['template_sn']][is_use] == 1): ?>
                                    <span class="yes" onClick="setUse('<?php echo $list['template_sn']; ?>', this)" data-enable="<?php echo $user_tpls[$list['template_sn']][is_use]; ?>"><i class="fa fa-check-circle"></i>是</span>
                                <?php else: ?>
                                    <span class="no" onClick="setUse('<?php echo $list['template_sn']; ?>', this)" data-enable="<?php echo $user_tpls[$list['template_sn']][is_use]; ?>"><i class="fa fa-ban"></i>否</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td align="center" class="col0">
                            <div style="text-align: center; width: 150px;" >
                                <a href="javascript:;" class="btn blue" onclick="popup('<?php echo $list['template_sn']; ?>')"><i class="fa fa-edit"></i>编辑</a>
                                <a href="javascript:;" class="btn blue" onclick="reset('<?php echo $list['template_sn']; ?>')"><i class="fa fa-recycle"></i>重置</a>
                            </div>
                        </td>
                        <td align="" class="" style="width: 100%;">
                            <div>&nbsp;</div>
                        </td>
                    </tr>
                    <textarea style="display:none" id="remark<?php echo $list['template_sn']; ?>"><?php echo $user_tpls[$list['template_sn']][remark]; ?></textarea>
                <?php endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!--弹窗-->
<div class="ncap-form-default" id="editer" style="display: none">
    <dl class="row">
        <dt class="tit">
            <label><em></em>消息备注:</label>
        </dt>
        <dd class="opt">
            <textarea name="text" rows="6"  placeholder="此备注会附在消息后面一同发给微信用户" id="submit-text" class="tarea"></textarea>
        </dd>
    </dl>
    <div class="bot"><a href="JavaScript:void(0);" onClick="setData()" class="ncap-btn-big ncap-btn-green">设置</a></div>
</div>

<script>
    $(document).ready(function() {
        // 点击刷新数据
        $('.fa-refresh').click(function(){
            location.href = location.href;
        });
    });
    function setUse(sn, el) {
        var isEnable = $(el).data('enable') ? 0 : 1;
        $.ajax({
            type: 'post',
            url: "<?php echo U('set_template_msg'); ?>",
            data: {
                template_sn: sn,
                is_use: isEnable
            },
            dataType: 'json',
            success: function (res) {
                if (!res) {
                    return layer.alert('服务器空响应', {icon:2});
                }
                if (res.status === 1) {
                    return layer.msg(res.msg, {time: 1000, icon: 1}, function () {
                        location.reload(true);
                    });
                }
                var msg = (typeof res.status === 'undefined') ? '数据格式出错' : res.msg;
                layer.alert(msg, {icon:2});
            },
            error: function () {
                layer.alert('服务器繁忙！', {icon: 2});
            }
        })
    }
    var popup_template_sn = 0;
    function setData() {
        var remark = $('#submit-text').val();
        $.ajax({
            type: 'post',
            url: "<?php echo U('set_template_msg'); ?>",
            data: {
                template_sn: popup_template_sn,
                remark: remark
            },
            dataType: 'json',
            success: function (res) {
                if (!res) {
                    return layer.alert('服务器空响应', {icon:2});
                }
                if (res.status === 1) {
                    $('#remark'+popup_template_sn).val(remark);
                    return layer.msg(res.msg, {time: 1000, icon: 1}, function () {
                        location.reload(true);
                    });
                }
                var msg = (typeof res.status === 'undefined') ? '数据格式出错' : res.msg;
                layer.alert(msg, {icon:2});
            },
            error: function () {
                layer.alert('服务器繁忙！', {icon: 2});
            }
        })
    }

    function popup(sn) {
        popup_template_sn = sn;
        $('#submit-text').val($('#remark'+sn).val());
        layer.open({
            type: 1,
            title: '消息模板设置',
            shadeClose: true,
            shade: 0.8,
            area: ['580px', '240px'],
            content: $('#editer')
        });
    }

    function reset(sn) {
        $.ajax({
            type: 'post',
            url: "<?php echo U('reset_template_msg'); ?>",
            data: {
                template_sn: sn
            },
            dataType: 'json',
            success: function (res) {
                if (!res) {
                    return layer.alert('服务器空响应', {icon:2});
                }
                if (res.status === 1) {
                    return layer.msg(res.msg, {time: 1000, icon: 1}, function () {
                        location.reload(true);
                    });
                }
                var msg = (typeof res.status === 'undefined') ? '数据格式出错' : res.msg;
                layer.alert(msg, {icon:2});
            },
            error: function () {
                layer.alert('服务器繁忙！', {icon: 2});
            }
        })
    }
</script>
</body>
</html>