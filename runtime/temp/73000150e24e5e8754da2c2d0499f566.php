<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:43:"./application/admin/view/shipping/info.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<script src="/public/js/jquery.event.drag.js" type="text/javascript"></script>
<script type="text/javascript" src="/public/static/js/perfect-scrollbar.min.js"></script>
<style>
    .waybill_design .item { background-color: #FEF5E6; width: 90px; height: 20px; padding: 1px 5px 4px 5px !important; border-color: #FFBEBC; border-style: solid; border-width: 1px 1px 1px 1px; cursor: move; position: absolute; left: 0; top: 0;}
    .waybill_design .item pre {
        height: 100%;
        float: left;
        padding: 0;
        margin: 0;
        border:0;
        cursor: move;
    }

    .waybill_design textarea {
        padding-left: 0px;
        margin: 0px;
        font-size: 12px;
        resize: none;
        outline: none;
        overflow: hidden;
        border: none;
    }

    .waybill_design .resize {
        height: 6px;
        width: 6px;
        position: absolute;
        bottom: 0px;
        right: 0px;
        overflow: hidden;
        cursor: nw-resize;
        background-color: #aaaaaa;
    }
    .waybill_design .selected {
        filter: alpha(opacity = 100);
        -moz-opacity: 1;
        opacity: 1;
        border: 1px solid #ff6600;
    }

</style>
<body style="background-color: #FFF; overflow: auto;">
<div id="toolTipLayer" style="position: absolute; z-index: 9999; display: none; visibility: visible; left: 95px; top: 573px;"></div>
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="javascript:history.back();" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>物流管理 - 添加物流</h3>
                <h5>网站系统物流索引和管理</h5>
            </div>
        </div>
    </div>
    <form class="form-horizontal" id="handleposition">
        <input type="hidden" name="shipping_id" value="<?php echo $shipping['shipping_id']; ?>">
        <input type="hidden" name="template_html" value="<?php echo $shipping['template_html']; ?>">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="shipping_name"><em>*</em>物流公司名字</label>
                </dt>
                <dd class="opt">
                    <input type="text" value="<?php echo $shipping['shipping_name']; ?>" id="shipping_name" name="shipping_name" class="input-txt" autocomplete = 'off' >
                    <span class="err" id="err_shipping_name"></span>
                    <div id='coding' style='width:280px;position:relative;background:#DDDDDD;' >
					</div>
                    <p class="notic">物流公司大写首字母：如圆通快递：YTKD</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="shipping_code"><em>*</em>快递公司编码</label>
                </dt>
                <dd class="opt" >
                    <input type="text" value="<?php echo $shipping['shipping_code']; ?>" id="shipping_code" name="shipping_code" class="input-txt" readonly="readonly" >
                    <span class="err" id="err_shipping_code"></span>
                 
                    <?php if($express_switch == 1): ?>
                      <p class="notic">物流公司编码由拼音组成,不得含有英文以外的字符。<a href="http://www.kdniao.com/file/2018快递鸟接口支持快递公司编码.xlsx" target="_blank" class="ncap-btn"><i class="fa fa-truck"></i>
                        	快递鸟编码参考</a></p>
                    <?php else: ?>
                    <p class="notic">物流公司编码由拼音组成,不得含有英文以外的字符。<a href="https://www.kuaidi100.com" target="_blank" class="ncap-btn"><i class="fa fa-truck"></i>
                        	快递100编码参考</a></p>
                    <?php endif; ?>
                </dd>
            </dl>
            <!--<dl class="row">-->
                <!--<dt class="tit">-->
                    <!--<label for="shipping_name">物流服务商客户账号</label>-->
                <!--</dt>-->
                <!--<dd class="opt">-->
                    <!--<input type="text" value="<?php echo $shipping['shipping_name']; ?>" id="customer_name" name="customer_name" class="input-txt">-->
                    <!--<span class="err" id="err_customer_name"></span>-->
                    <!--<p class="notic">当需要在线预约物流、申请电子面单时，部分物流运营商需要申请客户账号，申请后在此填写</p>-->
                <!--</dd>-->
            <!--</dl>-->
            <!--<dl class="row">-->
                <!--<dt class="tit">-->
                    <!--<label for="shipping_name">物流服务商客户密码</label>-->
                <!--</dt>-->
                <!--<dd class="opt">-->
                    <!--<input type="text" value="<?php echo $shipping['shipping_name']; ?>" id="customer_pw" name="customer_pw" class="input-txt">-->
                    <!--<span class="err" id="err__customer_pw"></span>-->
                    <!--<p class="notic">当需要在线预约物流、申请电子面单时，部分物流运营商需要申请客户账号，申请后在此填写</p>-->
                <!--</dd>-->
            <!--</dl>-->
            <dl class="row">
                <dt class="tit">
                    <label>简短描述</label>
                </dt>
                <dd class="opt">
                    <textarea placeholder="请输入简短描述" name="shipping_desc" class="tarea w700"><?php echo $shipping['shipping_desc']; ?></textarea>
                    <span class="err" id="err_shipping_desc"></span>
                    <p class="notic">简短描述</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <em>*</em><label>物流图片图标</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show">
                        <span class="show">
                            <a id="img_a" target="_blank" class="nyroModal" rel="gal" href="<?php echo $shipping['shipping_logo']; ?>">
                                <i id="img_i" class="fa fa-picture-o" onmouseover="layer.tips('<img src=<?php echo $shipping['shipping_logo']; ?>>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"></i>
                            </a>
                        </span>
           	            <span class="type-file-box">
                            <input type="text" id="shipping_logo" name="shipping_logo" value="<?php echo $shipping['shipping_logo']; ?>" class="type-file-text">
                            <input type="button" name="button" value="选择上传..." class="type-file-button">
                            <input class="type-file-file" onClick="GetUploadify(1,'','shipping_logo','img_call_back')" size="30" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                        </span>
                    </div>
                    <span class="err" id="err_shipping_logo"></span>
                    <p class="notic">物流图片图标，最佳显示尺寸为240*60像素</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="width">模板宽度</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="template_width" value="<?php echo (isset($shipping['template_width']) && ($shipping['template_width'] !== '')?$shipping['template_width']:'840'); ?>" id="width" class="w100">
                    <span class="err" id="err_template_width"></span>
                    <p class="notic">运单宽度，单位为毫米(mm)</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>模板高度</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="template_height" value="<?php echo (isset($shipping['template_height']) && ($shipping['template_height'] !== '')?$shipping['template_height']:'480'); ?>" class="w100">
                    <span class="err" id="err_template_height"></span>
                    <p class="notic">运单高度度，单位为毫米(mm)</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>偏移量X</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="template_offset_x" value="<?php echo (isset($shipping['template_offset_x']) && ($shipping['template_offset_x'] !== '')?$shipping['template_offset_x']:'0'); ?>" class="w100">
                    <span class="err" id="err_template_offset_x"></span>
                    <p class="notic">运单模板左偏移量，单位为毫米(mm)</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>偏移量Y</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="template_offset_y" value="<?php echo (isset($shipping['template_offset_y']) && ($shipping['template_offset_y'] !== '')?$shipping['template_offset_y']:'0'); ?>" class="w100">
                    <span class="err" id="err_template_offset_y"></span>
                    <p class="notic">运单模板上偏移量，单位为毫米(mm)</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em>模板图片</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show">
                    <span class="show">
                        <a id="template_img_a" target="_blank" class="nyroModal" rel="gal" href="<?php echo $shipping['template_img']; ?>">
                           <i id="template_img_i" class="fa fa-picture-o" onmouseover="layer.tips('<img src=<?php echo $shipping['template_img']; ?>>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"></i>
                        </a>
                    </span>
                    <span class="type-file-box">
                        <input id="template_img" name="template_img" value="<?php echo $shipping['template_img']; ?>" class="type-file-text" type="text">
                        <input name="button" id="button1" value="选择上传..." class="type-file-button" type="button">
                        <input class="type-file-file" onclick="GetUploadify(1,'','plugins','template_img_call_back')" size="30" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                    </span>
                    </div>
                    <span class="err" id="err_template_img"></span>
                    <p class="notic">请上传扫描好的运单图片，图片尺寸必须与快递单实际尺寸相符</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">选择打印项</dt>
                <dd class="opt">
                    <select class="form-control" id="tagOption">
                        <option value="">添加标签</option>
                        <option value="发货点-名称">发货点-名称</option>
                        <option value="发货点-联系人">发货点-联系人</option>
                        <option value="发货点-电话">发货点-电话</option>
                        <option value="发货点-省份">发货点-省份</option>
                        <option value="发货点-城市">发货点-城市</option>
                        <option value="发货点-区县">发货点-区县</option>
                        <option value="发货点-手机">发货点-手机</option>
                        <option value="发货点-详细地址">发货点-详细地址</option>
                        <option value="收件人-姓名">收件人-姓名</option>
                        <option value="收件人-手机">收件人-手机</option>
                        <option value="收件人-电话">收件人-电话</option>
                        <option value="收件人-省份">收件人-省份</option>
                        <option value="收件人-城市">收件人-城市</option>
                        <option value="收件人-区县">收件人-区县</option>
                        <option value="收件人-邮编">收件人-邮编</option>
                        <option value="收件人-详细地址">收件人-详细地址</option>
                        <option value="时间-年">时间-年</option>
                        <option value="时间-月">时间-月</option>
                        <option value="时间-日">时间-日</option>
                        <option value="时间-当前日期">时间-当前日期</option>
                        <option value="订单-订单号">订单-订单号</option>
                        <option value="订单-备注">订单-备注</option>
                        <option value="订单-配送费用">订单-配送费用</option>
                    </select>
                    <a id="deleteTag" class="ncap-btn"><i class="fa fa-trash-o"></i>删除标签</a>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">打印项偏移校正</dt>
                <dd class="opt">
                    <div class="waybill_area" style="overflow: auto;position: relative; width: <?php echo (isset($config['width']) && ($config['width'] !== '')?$config['width']:'840'); ?>px; height: <?php echo (isset($config['height']) && ($config['height'] !== '')?$config['height']:'480'); ?>px;">
                        <div class="waybill_back" style="position: relative; width: <?php echo (isset($shipping['template_width']) && ($shipping['template_width'] !== '')?$shipping['template_width']:'840'); ?>px; height: <?php echo (isset($shipping['template_height']) && ($shipping['template_height'] !== '')?$shipping['template_height']:'480'); ?>px;"> <img id="tp-img" src="<?php echo $shipping['template_img']; ?>" style="width: <?php echo (isset($config['width']) && ($config['width'] !== '')?$config['width']:'840'); ?>px; height: <?php echo (isset($config['height']) && ($config['height'] !== '')?$config['height']:'480'); ?>px;" alt=""> </div>
                        <div class="waybill_design" style="position: absolute; left: <?php echo (isset($shipping['template_offset_x']) && ($shipping['template_offset_x'] !== '')?$shipping['template_offset_x']:'0'); ?>px; top: <?php echo (isset($shipping['template_offset_y']) && ($shipping['template_offset_y'] !== '')?$shipping['template_offset_y']:'0'); ?>px; width: <?php echo (isset($shipping['template_width']) && ($shipping['template_width'] !== '')?$shipping['template_width']:'840'); ?>px; height: <?php echo (isset($shipping['template_height']) && ($shipping['template_height'] !== '')?$shipping['template_height']:'480'); ?>px;">
                            <?php echo htmlspecialchars_decode($shipping['template_html']); ?>
                    </div>
                    </div>
                </dd>
            </dl>

            <div class="bot" style="position: relative;z-index: 10"><a href="JavaScript:void(0);" onclick="verifyForm();" class="ncap-btn-big ncap-btn-green" id="submit">确认提交</a></div>
        </div>
    </form>
</div>
<script type="text/javascript">
    function img_call_back(fileurl_tmp)
    {
        $("#shipping_logo").val(fileurl_tmp);
        $("#img_a").attr('href', fileurl_tmp);
        $("#img_i").attr('onmouseover', "layer.tips('<img src="+fileurl_tmp+">',this,{tips: [1, '#fff']});");
    }

    function template_img_call_back(value) {
        $("#template_img").val(value);
        $("#template_img_a").attr('href', value);
        $("#template_img_i").attr('onmouseover', "layer.tips('<img src="+value+">',this,{tips: [1, '#fff']});");
        $(".waybill_design").css({
            background: "url(" + value + ") 0px 0px no-repeat"
        });
        $('#tp-img').hide();
    }

    $().ready(function () {
        var $tagOption = $("#tagOption");
        var $deleteTag = $("#deleteTag");
        var $container = $(".waybill_design");
        var $background = $("#template_img");
        var $width = $("#width");
        var $height = $("#height");
        var zIndex = 1;
        bind($container.find("div.item"));
        // 标签选项
        $tagOption.change(function () {
            var value = $(this).find("option:selected").val();
            if (value != "") {
                bind($('<div class="item"><pre>' + value + '<\/pre><div class="resize"><\/div><\/div>').appendTo($container));
            }
            return false;
        });

        // 绑定
        function bind($item) {
            $item.drag("start", function (ev, dd) {
                var $this = $(this);
                dd.width = $this.width();
                dd.height = $this.height();
                dd.limit = {
                    right: $container.innerWidth() - $this.outerWidth(),
                    bottom: $container.innerHeight() - $this.outerHeight()
                };
                dd.isResize = $(ev.target).hasClass("resize");
            }).drag(function (ev, dd) {
                var $this = $(this);
                if (dd.isResize) {
                    $this.css({
                        width: Math.max(20, Math.min(dd.width + dd.deltaX, $container.innerWidth() - $this.position().left) - 2),
                        height: Math.max(20, Math.min(dd.height + dd.deltaY, $container.innerHeight() - $this.position().top) - 2)
                    }).find("textarea").blur();
                } else {
                    $this.css({
                        top: Math.min(dd.limit.bottom, Math.max(0, dd.offsetY)),
                        left: Math.min(dd.limit.right, Math.max(0, dd.offsetX))
                    });
                }
            }, {relative: true}).mousedown(function () {
                $(this).css("z-index", zIndex++);
            }).click(function () {
                var $this = $(this);
                $container.find("div.item").not($this).removeClass("selected");
                $this.toggleClass("selected");
            }).dblclick(function () {
                var $this = $(this);
                if ($this.find("textarea").size() == 0) {
                    var $pre = $this.find("pre");
                    var value = $pre.hide().text($pre.html()).html();
                    $('<textarea>' + value + '<\/textarea>').replaceAll($pre).width($this.innerWidth() - 6).height($this.innerHeight() - 6).blur(function () {
                        var $this = $(this);
                        $this.replaceWith('<pre>' + $this.val() + '<\/pre>');
                    }).focus();
                }
            });
        }
        // 删除标签
        $deleteTag.click(function () {
            $container.find("div.selected").remove();
            return false;
        });
        $background.bind("input propertychange change", function () {
            $container.css({
                background: "url(/" + $background.val() + ") 0px 0px no-repeat"
            });
        });

        // 宽度
        $width.bind("input propertychange change", function () {
            $container.width($width.val());
        });

        // 高度
        $height.bind("input propertychange change", function () {
            $container.height($height.val());
        });
    });
    function verifyForm(){
        var template_html = $(".waybill_design").html();
        $("input[name='template_html']").val(template_html);
        $('span.err').hide();
        $.ajax({
            type: "POST",
            url: "<?php echo U('Shipping/save'); ?>",
            data: $('#handleposition').serialize(),
            async:false,
            dataType: "json",
            error: function () {
                layer.alert("服务器繁忙, 请联系管理员!");
            },
            success: function (data) {
                if (data.status == 1) {
                    layer.msg(data.msg,{icon: 1,time: 2000},function(){
                        location.href = "<?php echo U('Shipping/index'); ?>";
                    });
                } else {
                    $('#submit').attr('disabled',false);
                    $.each(data.result, function (index, item) {
                        $('span.err').show();
                        $('#err_'+index).text(item);
                    });
                    layer.msg(data.msg, {icon: 2,time: 3000});
                }
            }
        });
    }
</script>
<script type="text/javascript">
	$('#shipping_name').live('input propertychange', function(e)
	{
		var logistics_id = <?php echo $express_switch; ?>;
		if(logistics_id==0){
			logistics_id=1;
		}else{
			logistics_id=2;
		}
		var coding =  $('#shipping_name').val();
		$.ajax({
			  url: '/admin/Shipping/ajaxCoding?logistics_id='+logistics_id+'&coding='+ coding,
			  type: 'get',
			  dataType: 'json',
			  success: function (res) {
			  	if(res.data.length>0){
			  		var html = '';
			  		for(var i=0;i<res.data.length;i++){
			  			html += '<h5 class="coding_info">'+res.data[i].name+'<input type="hidden" value="'+res.data[i].coding+'" ></input></h5>';
			  		}
			  		$('#coding').html('');
			  		$('#coding').html(html);
			  	}else{
			  		$('#coding').html('');
			  	}
			  	
			  },
			  fail: function (err) {
			    console.log(err)
			  }
			});
	});
	$("body #coding").on("click",".coding_info",function(e){
		var name = $(this).text();
		var coding = $(this).find("input").val();
		$('#coding').html('');
		$('#shipping_code').attr('val',coding).val(coding);
		$('#shipping_name').attr('val',name).val(name);
	});
	$(document).bind('click', function(e) {
		var e = e || window.event; //浏览器兼容性 
		var elem = e.target || e.srcElement;
		while (elem) { //循环判断至跟节点，防止点击的是div子元素 
			if (elem.id && elem.id == 'coding') {
				return;
			}
			elem = elem.parentNode;
		}
		$('#coding').html('');
	});
</script>
</body>
</html>