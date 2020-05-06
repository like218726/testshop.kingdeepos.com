<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:52:"./application/admin/view/wechat/auto_reply_edit.html";i:1587634376;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<body style="background-color: #FFF; overflow: auto;">
<div id="toolTipLayer" style="position: absolute; z-index: 9999; display: none; visibility: visible; left: 95px; top: 573px;"></div>
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <?php if($type == 'keyword'): ?>
                <a class="back" href="javascript:history.back();" title="微信公众号配置"><i class="fa fa-arrow-circle-o-left"></i></a>
            <?php endif; ?>
            <div class="subject">
                <h3>自动回复</h3>
                <h5>添加或编辑回复内容</h5>
            </div>
            <?php if($type != 'keyword'): ?>
                <ul class="tab-base nc-row">
                    <?php if(is_array($types) || $types instanceof \think\Collection || $types instanceof \think\Paginator): if( count($types)==0 ) : echo "" ;else: foreach($types as $k=>$v): ?>
                        <li><a href="<?php echo $v['url']; ?>" <?php if($k==$type): ?>class="current"<?php endif; ?>>
                            <span><?php echo $v['menu']; ?></span></a>
                        </li>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <form class="form-horizontal" method="post" id="handlepost" action="">
        <div class="ncap-form-default">
            <?php if($type == 'keyword'): ?>
                <dl class="row">
                    <dt class="tit">
                        <label><em>*</em>规则</label>
                    </dt>
                    <dd class="opt">
                        <input type="text" name="rule" value="<?php echo $reply['rule']; ?>" class="input-txt">
                        <p class="notic">最多32个字</p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label><em>*</em>关键词</label>
                    </dt>
                    <dd class="opt">
                        <input type="text" name="keywords" value="<?php echo $reply['keywords']; ?>" class="input-txt">
                        <p class="notic">多个关键字，请以英文逗号隔开(',')</p>
                    </dd>
                </dl>
            <?php endif; ?>
            <dl class="row">
                <dt class="tit">
                    <label><em></em>回复类型</label>
                </dt>
                <dd class="opt">
                    <input type="radio" name='msg_type' value="text" onchange="selectType('text')" <?php if(!$reply['msg_type'] || $reply['msg_type']=='text'): ?>checked<?php endif; ?>> 文本
                    <input type="radio" name='msg_type' value="news" onchange="selectType('news')" <?php if($reply['msg_type']=='news'): ?>checked<?php endif; ?>> 图文
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em>回复内容</label>
                </dt>
                <dd class="opt">
                    <textarea name="content" rows="6" placeholder="回复内容" class="tarea" id="content_text" style="display: none"><?php echo $reply['data']; ?></textarea>
                    <div id="content_news" style="display: none" onclick="popupNews()">
                        <?php if($reply['msg_type']=='news'): ?>
                            <div class="ma-card" style="cursor: pointer;">
                                <?php if(count($news['wx_news']) === 1): ?>
                                    <!--单图文-->
                                    <div class="title ellipsis-1"><?php echo $news['wx_news']['0']['title']; ?></div>
                                    <div class="time"><?php echo $news['update_time']; ?></div>
                                    <div class="card-item no-line">
                                        <div class="cover">
                                            <img src="<?php echo $news['wx_news']['0']['thumb_url']; ?>"/>
                                        </div>
                                    </div>
                                    <div class="desc ellipsis-2"><?php echo !empty($news['wx_news']['0']['digest'])?$news['wx_news']['0']['digest']:$news['wx_news']['0']['content_digest']; ?></div>
                                    <?php else: ?>
                                    <!--多图文-->
                                    <div class="time"><?php echo $news['update_time']; ?></div>
                                    <?php if(is_array($news['wx_news']) || $news['wx_news'] instanceof \think\Collection || $news['wx_news'] instanceof \think\Paginator): $i = 0; $__LIST__ = $news['wx_news'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$news): $mod = ($i % 2 );++$i;?>
                                        <div class="card-item">
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
                                        </div>
                                    <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                            </div>
                        <?php else: ?>
                            <a href="JavaScript:void(0);" class="ncap-btn ncap-btn-green">选择</a>
                        <?php endif; ?>
                    </div>
                    <input type="hidden" id='material_id' value="<?php echo $reply['material_id']; ?>" name="material_id">
                    <input type="hidden" value="<?php echo $type; ?>" name="type">
                </dd>
            </dl>
            <div class="bot">
                <a href="JavaScript:void(0);" onClick="formSubmit()" class="ncap-btn-big ncap-btn-green">提 交</a>
            </div>
        </div>
    </form>
</div>

<!-- 图文素材弹框 -->
<div class="ncap-form-default" id="news-select" style="display: none">
    <div id="news-list"></div>
    <div class="bot" style="text-align:right;padding-right: 20px;">
        <a href="JavaScript:void(0);" onClick="selectNews()" class="ncap-btn-big ncap-btn-green">确定</a>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        selectType("<?php echo (isset($reply['msg_type']) && ($reply['msg_type'] !== '')?$reply['msg_type']:'text'); ?>");
    });
    function formSubmit() {
        var url = "<?php echo !empty($reply['id'])?U('update_auto_reply') : U('add_auto_reply'); ?>";
        $.ajax({
            url: url + '?id=<?php echo $reply['id']; ?>',
            type: 'POST',
            data: $("#handlepost").serialize(),
            dataType: 'json',
            success: function (res) {
                if (res.status === 1) {
                    return layer.msg(res.msg, {time: 500, icon: 1});
                }
                var msg = (typeof res.status === 'undefined') ? '数据格式出错' : res.msg;
                layer.alert(msg, {icon:2});
            },
            error: function () {
                layer.alert('服务器繁忙！', {icon: 2});
            }
        });
    }
    function selectType(type) {
        console.log(type);
        if (type === 'text') {
            $('#content_news').hide();
            $('#content_text').show();
        } else {
            $('#content_text').hide();
            $('#content_news').show();
            if (!$('#content_news').children('.ma-card').length) {
                popupNews();
            }
        }
    }

    function ajaxNews(page) {
        $.ajax({
            type : "get",
            url:"<?php echo U('ajax_news'); ?>?p=" + page,
            success: function (data) {
                $("#news-list").html(data);
            }
        });
    }
    function popupNews() {
        ajaxNews(1);
        layer.open({
            type: 1,
            title: '选择图文素材',
            shadeClose: true,
            shade: 0.8,
            area: ['750px', '600px'],
            content: $('#news-select')
        });
    }
    function selectNews() {
        var selectNews = $('.ma-card-mask:not(.hidden)');
        if ( ! selectNews.length) {
            return;
        }
        var news = selectNews.parent().clone();
        news.children('.ma-card-mask').remove();
        $('#content_news').html('').append(news);
        $('#material_id').val(news.data('mid'));
        layer.closeAll();
    }
</script>
</body>
</html>