<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:43:"./application/admin/view/index/explain.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<style>
.introdiv{position: relative;width:100%;display:none;}
.introimg{text-align: center;background-color: #fff; padding:20px;margin-top: 10px;}
@media only screen and (min-width: 700px) and (max-width: 1200px) {
.introimg{padding: 0;width: 100%;}
.page{min-width: 800px;}
}
@media only screen and (min-width: 1200px) and (max-width: 1512px) {
.introimg{padding: 0;width: 90%;}
.page{min-width: 1000px;}
}
</style>
<body style="background-color: rgb(255, 255, 255); overflow: auto; cursor: default; -moz-user-select: inherit;">
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page" style="min-width:clear;">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>流程介绍</h3>
        <h5>平台主要流程说明</h5>
      </div>
      <ul class="tab-base nc-row">
      <li><a class="current" href="javascript:;" data-id="role" ><span>角色介绍</span></a></li>
      <li><a href="javascript:;" data-id="user"><span>会员来源</span></a></li>
      <li><a href="javascript:;" data-id="login"><span>快捷登陆</span></a></li>
      <li><a href="javascript:;" data-id="storejoin"><span>商家入驻</span></a></li>
      <li><a href="javascript:;" data-id="shopping"><span>购物流程</span></a></li>
      <li><a href="javascript:;" data-id="pintuan"><span>拼团流程</span></a></li>
      <li><a href="javascript:;" data-id="service"><span>订单售后</span></a></li>
      <li><a href="javascript:;" data-id="withdrawals"><span>用户提现</span></a></li>
      <li><a href="javascript:;" data-id="settlement"><span>商家结算</span></a></li>
      <li><a href="javascript:;" data-id="distribution"><span>分销设置</span></a></li>
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
      <li>消费者可以通过平台的PC端、APP端、微商城等前台入口申请账号；平台可自行添加会员信息、调整资金等。</li>
      <li>商家在进行入驻申请时需要先成为平台的会员，然后填写入驻信息、等待平台审核；平台可自行添加自营店铺（联营）。</li>
      <li>平台可设置商家的结算日期和结算佣金比例，商家可通过自己的后台查看自己能分到的佣金比例，方便对账。</li>
    </ul>
  </div>
  <div class="flexigrid">
  	<div style="width: 100%;float: left;">
		<div class="introdiv" style="display:block;" id="role">
			<img class="introimg" src="/public/images/role.png">
		</div>
		<div class="introdiv" id="user">
			<img class="introimg" src="/public/images/user.png">
		</div>
		<div class="introdiv" id="login">
			<img class="introimg" src="/public/images/login.png">
		</div>
		<div class="introdiv" id="shopping">
			<img class="introimg" src="/public/images/shopping.png">
		</div>
		<div class="introdiv" id="storejoin">
			<img class="introimg" src="/public/images/storejoin.png">
		</div>
		<div class="introdiv" id="settlement">
			<img class="introimg" src="/public/images/settlement.png">
		</div>
		<div class="introdiv" id="pintuan">
			<img class="introimg" src="/public/images/pintuan.png">
		</div>
		<div class="introdiv" id="service">
			<img class="introimg" src="/public/images/service.png">
		</div>
		<div class="introdiv" id="withdrawals">
			<img class="introimg" src="/public/images/withdrawals.png">
		</div>
		<div class="introdiv" id="distribution">
			<img class="introimg" src="/public/images/distribution.png">
		</div>
	</div>	
  </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('.tab-base a').click(function(){
		var divid = $(this).attr('data-id');
		$('.introdiv').hide();
		$('#'+divid).show();
		$('.tab-base a').removeClass('current');
		$(this).addClass('current');
	});
});
</script>
</body>
</html>