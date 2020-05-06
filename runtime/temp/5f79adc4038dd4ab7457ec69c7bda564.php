<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:51:"./application/admin/view/wechat/materials_news.html";i:1587634376;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<link href="/public/static/css/weixin-mp.css" rel="stylesheet"/>
<body style="background-color: rgb(255, 255, 255); overflow: auto; cursor: default; -moz-user-select: inherit;">
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>图文素材管理</h3>
                <h5>微信素材显示与管理</h5>
            </div>
            <ul class="tab-base nc-row">
                <?php if(is_array($tabs) || $tabs instanceof \think\Collection || $tabs instanceof \think\Paginator): if( count($tabs)==0 ) : echo "" ;else: foreach($tabs as $k=>$v): ?>
                    <li><a href="<?php echo U('materials',['tab'=> $k]); ?>" <?php if($k==$tab): ?>class="current"<?php endif; ?>>
                        <span><?php echo $v; ?></span></a>
                    </li>
                <?php endforeach; endif; else: echo "" ;endif; ?>
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
            <li>发送图文消息请移步粉丝列表</li>
            <li>每个图文素材最多包含8篇文章！</li>
        </ul>
    </div>
    <div class="ma-page">
        <div class="ma-top">
            <a href="<?php echo url('news_edit'); ?>" class="ma-btn"><span class="fa fa-plus"></span> 新增图文</a>
            <div> ( 共 <?php echo $page->totalRows; ?> 条记录 )</div>
        </div>

        <div class="ma-list">
            <?php if(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty())): ?>
                <div class="no-data">
                    <i class="fa fa-exclamation-circle"></i>还没有相关素材~
                </div>
            <?php endif; if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                <div class="ma-card">
                    <?php if(count($item['wx_news']) === 1): ?>
                        <!--单图文-->
                        <div class="title ellipsis-1"><?php echo $item['wx_news']['0']['title']; ?></div>
                        <div class="time"><?php echo $item['update_time']; ?></div>
                        <div class="card-item no-line">
                            <a href="<?php echo url('news_edit', ['material_id' => $item['id'], 'news_id' => $item['wx_news']['0']['id']]); ?>">
                            <div class="cover">
                                <img src="<?php echo $item['wx_news']['0']['thumb_url']; ?>"/>
                            </div>
                            </a>
                        </div>
                        <div class="desc ellipsis-2"><?php echo !empty($item['wx_news']['0']['digest'])?$item['wx_news']['0']['digest']:$item['wx_news']['0']['content_digest']; ?></div>
                    <?php else: ?>
                        <!--多图文-->
                        <div class="time"><?php echo $item['update_time']; ?></div>
                        <?php if(is_array($item['wx_news']) || $item['wx_news'] instanceof \think\Collection || $item['wx_news'] instanceof \think\Paginator): $i = 0; $__LIST__ = $item['wx_news'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$news): $mod = ($i % 2 );++$i;?>
                            <div class="card-item">
                            <a href="<?php echo url('news_edit', ['material_id' => $item['id'], 'news_id' => $news['id']]); ?>">
                            <?php if($i==1): ?>
                                <div class="cover cover-sm">
                                    <img src="<?php echo $news['thumb_url']; ?>"/>
                                    <div class="title-in ellipsis-1"><?php echo $news['title']; ?></div>
                                </div>
                            <?php else: ?>
                                <div class="post">
                                    <div class="post-title ellipsis-2"><?php echo $news['title']; ?></div>
                                    <div class="post-cover">
                                        <img src="<?php echo $news['thumb_url']; ?>"/>
                                    </div>
                                </div>
                            <?php endif; ?>
                            </a>
                            </div>
                        <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                    <div class="btns">
                        <a class="btn left" title="在素材上添加文章" href="<?php echo url('news_edit', ['material_id' => $item['id']]); ?>">
                            <span class="fa fa-plus"></span>
                        </a>
                        <div class="btn" title="删除图文素材" onclick="deleteMaterial('<?php echo $item['id']; ?>')"><span class="fa fa-trash"></span></div>
                    </div>
                </div>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>

        <div class="page p"><?php echo $page->show(); ?></div>
    </div>
</div>
<script>
    function deleteMaterial(id) {
        layer.confirm("素材相关文章将一并删除，确定删除吗？", function(){
            $.ajax({
                url: "<?php echo url('delete_news'); ?>?material_id=" + id,
                type: 'POST',
                dataType: 'json',
                success: function (res) {
                    if (res.status === 1) {
                        return layer.msg(res.msg, {time: 500, icon: 1}, function () {
                            window.location.reload();
                        });
                    }
                    var msg = (typeof res.status === 'undefined') ? '数据格式出错' : res.msg;
                    layer.alert(msg, {icon:2});
                },
                error: function () {
                    layer.alert('服务器繁忙！', {icon: 2});
                }
            });
        });
    }
</script>
</body>
</html>