<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:47:"./application/admin/view/article/agreement.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
        <h3>会员协议</h3>
        <h5>网站会员协议设置管理</h5>
      </div>
    </div>
  </div>
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="提示相关设置操作时应注意的要点">操作提示</h4>
      <span id="explanationZoom" title="收起提示"></span> </div>
    <ul>
      <li>在相关操作处可查看具体内容，例：在注册会员时须查看用户服务协议</li>
    </ul>
  </div>
  <div class="flexigrid">
  <div class="mDiv"><div class="ftitle"><h3>列表</h3><h5></h5></div></div>
      <a href="<?php echo U('Article/edit_agreement'); ?>"><div class="fbutton"><div class="add" title="新增会员协议"><span><i class="fa fa-plus"></i>新增会员协议</span></div></div></a>
  	<div class="hDiv">
  		<div class="hDivBox">
  		<table cellpadding="0" cellspacing="0">
	  		<thead>
		      <tr>
		        <th align="center" class="sign" axis="col0"><div style="text-align: center; width: 24px;"><i class="ico-check"></i></div></th>
		        <th class="handle-s" align="center" axis="col1"><div style="text-align: center; width: 60px;">操作</div></th>
		        <th align="left" axis="col2" class=""><div class="sundefined" style="text-align: left; width: 300px;">标题</div></th>
		        <th align="left" axis="col3"><div style="text-align: left; width: 300px;">时间</div></th>
		        <th axis="col4"><div></div></th>
		      </tr>
	    	</thead>
    	</table>
    	</div>
    </div>
    <div class="cDrag" style="top: 32px;">
    <div style="height: 339px; display: block; left: 39px;"></div>
    <div style="height: 339px; display: block; left: 115px;"></div>
    <div style="height: 339px; display: block; left: 431px;"></div>
    <div style="height: 339px; display: block; left: 747px;"></div>
    <div style="height: 339px; display: block; left: 763px;"></div></div>
    <div class="bDiv" style="height: auto;"><table class="flex-table autoht" cellpadding="0" cellspacing="0" border="0">
    
    <tbody>
    <?php if(is_array($agreement) || $agreement instanceof \think\Collection || $agreement instanceof \think\Paginator): if( count($agreement)==0 ) : echo "" ;else: foreach($agreement as $key=>$vo): ?>
      <tr>
        <td class="sign"><div style="text-align: center; width: 24px;"><i class="ico-check"></i></div></td>
        <td class="handle-s">
        	<div style="text-align: center; width: 60px;"><a class="btn blue" href="<?php echo U('Article/edit_agreement',array('doc_id'=>$vo['doc_id'])); ?>"><i class="fa fa-pencil-square-o"></i>编辑</a></div>
        </td>
        <td><div style="text-align: left; width: 300px;"><?php echo $vo['doc_title']; ?></div></td>
        <td><div style="text-align: left; width: 300px;"><?php echo date('Y-m-d H:i:s',$vo['doc_time']); ?></div></td>
        <td style="width: 100%;"><div>&nbsp;</div></td>
      </tr>
      <?php endforeach; endif; else: echo "" ;endif; ?>
    </tbody>
  </table><div class="iDiv" style="display: none;"></div></div></div>
</div>
</body>
</html>