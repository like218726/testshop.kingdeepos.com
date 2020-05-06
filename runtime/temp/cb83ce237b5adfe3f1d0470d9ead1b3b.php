<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:42:"./application/admin/view/system/basic.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
            <li>网站全局基本设置，商城及其他模块相关内容在其各自栏目设置项内进行操作。</li>
        </ul>
    </div>
    <form method="post" enctype="multipart/form-data" name="form1" action="<?php echo U('System/handle'); ?>">
        <input type="hidden" name="inc_type" value="<?php echo $inc_type; ?>">
        <dl class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="reg_integral">会员注册赠送积分</label>
                </dt>
                <dd class="opt">
                    <input onKeyUp="this.value=this.value.replace(/[^\d]/g,'')" onpaste="this.value=this.value.replace(/[^\d]/g,'')" pattern="^\d{1,}$" id="reg_integral" name="reg_integral" value="<?php echo $config['reg_integral']; ?>" class="input-txt" type="text">
                    <span class="err">只能输入整数</span>

                    <p class="notic">会员注册赠送积分</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="file_size">附件上传大小</label>
                </dt>
                <dd class="opt">
                    <select id="file_size" name="file_size">
                        <option value="1" <?php if($config[file_size] == 1): ?>selected="selected"<?php endif; ?>>1M</option>
                        <option value="2" <?php if($config[file_size] == 2): ?>selected="selected"<?php endif; ?>>2M</option>
                        <option value="3" <?php if($config[file_size] == 3): ?>selected="selected"<?php endif; ?>>3M</option>
                        <option value="5" <?php if($config[file_size] == 4): ?>selected="selected"<?php endif; ?>>5M</option>
                        <option value="10" <?php if($config[file_size] == 10): ?>selected="selected"<?php endif; ?>>10M</option>
                        <option value="50" <?php if($config[file_size] == 50): ?>selected="selected"<?php endif; ?>>50M</option>
                        <option value="100" <?php if($config[file_size] == 100): ?>selected="selected"<?php endif; ?>>100M</option>
                    </select>

                    <p class="notic">附件上传大小限制</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="default_storage">默认库存</label>
                </dt>
                <dd class="opt">
                    <input onKeyUp="this.value=this.value.replace(/[^\d]/g,'')" onpaste="this.value=this.value.replace(/[^\d]/g,'')" pattern="^\d{1,}$" id="default_storage" name="default_storage" value="<?php echo $config['default_storage']; ?>" class="input-txt" type="text">
                    <span class="err">只能输入整数</span>

                    <p class="notic">添加商品的默认库存</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="tax">发票税率</label>
                </dt>
                <dd class="opt">
                    <input pattern="^(?!0+(?:\.0+)?$)(?:[1-9]\d*|0)(?:\.\d{1,2})?$" onkeyup="this.value=/^\d+\.?\d{0,2}$/.test(this.value) ? this.value : ''" name="tax" id="tax" value="<?php echo $config['tax']; ?>" class="input-txt" type="text">%
                    <span class="err">只能输入数字和小数</span>
                    <p class="notic">当买家需要发票的时候就要增加[商品金额]*[税率]的费用</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="hot_keywords">首页热门搜索词</label>
                </dt>
                <dd class="opt">
                    <input id="hot_keywords" name="hot_keywords" value="<?php echo $config['hot_keywords']; ?>" class="input-txt" type="text">
                    <span class="err">例如:衣|手机|内衣</span>
                    <p class="notic">首页或手机搜索页，搜索栏下侧显示</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="default_storage">浏览器端百度ak</label>
                </dt>
                <dd class="opt">
                    <input  name="bd_ak" value="<?php echo (isset($config['bd_ak']) && ($config['bd_ak'] !== '')?$config['bd_ak']:''); ?>" class="input-txt" type="text">
                    <span class="err"></span>
                    <p class="notic">用于浏览器 <a href="http://lbsyun.baidu.com/index.php?title=jspopular/guide/introduction" target="_blank">去申请百度ak</a>,认证成功调用次数多些。</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="default_storage">服务端百度ak</label>
                </dt>
                <dd class="opt">
                    <input  name="bd_serverak" value="<?php echo (isset($config['bd_serverak']) && ($config['bd_serverak'] !== '')?$config['bd_serverak']:""); ?>" class="input-txt" type="text">
                    <span class="err"></span>
                    <p class="notic">用于服务器 <a href="http://lbsyun.baidu.com/index.php?title=jspopular/guide/introduction" target="_blank">去申请百度ak</a></p>
                </dd>
            </dl>
			<dl class="row">
                <dt class="tit">第三方登录是否必须绑定账号</dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="is_bind_account1" class="cb-enable <?php if($config[is_bind_account] == 1): ?>selected<?php endif; ?>">是</label>
                        <label for="is_bind_account0" class="cb-disable <?php if($config[is_bind_account] == 0): ?>selected<?php endif; ?>">否</label>
                        <input id="is_bind_account1" name="is_bind_account"  checked="checked" value="1" type="radio">
                        <input id="is_bind_account0" name="is_bind_account"  value="0" type="radio">
                    </div>
                    <p class="notic">(设置不可更改)<br/>否: 第三方账号首次登录时会自动创建账号, 不需要额外绑定账号<br/>是:(推荐)第三方账号首次登录时必须先绑定一个注册账号, 否则无法购买商品(优点:可以避免微商城, PC端产生多账户问题)</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">配置客服系统</dt>
                <dd class="opt im">
                    <input type="radio" name="im_choose" value="0" <?php if($config[im_choose] == 0): ?> checked <?php endif; ?> >qq客服  &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" id="tpshop-im" name="im_choose" value="1" <?php if($config[im_choose] == 1): ?> checked <?php endif; ?> >TPshop im客服  &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="im_choose" value="2" <?php if($config[im_choose] == 2): ?> checked <?php endif; ?> >小能客服
                    <p class="notic">小能客服为第三方客服系统</p>
                </dd>
            </dl>

            <dl class="row" id="tpshop-im-web" <?php if($config['im_choose'] != 1): ?> style="display: none" <?php endif; ?> >
                <dt class="tit">
                    <label>TPshop IM 网址</label>
                </dt>
                <dd class="opt">
                    <input id="im_website" name="im_website" value="<?php echo $config['im_website']; ?>" class="input-txt" type="text" placeholder="http://im.tp-shop.cn">
                    <p class="notic">该配置项作为店铺入驻审核通过时<?php echo \think\Config::get('shop_info.copyright'); ?> IM自动同步店铺及负责人数据, 客服域名一定要带"http://im.tp-shop.cn"前缀</p>
                </dd>
                <p></p>
                <dt class="tit">
                    <label>IM 连接方式</label>
                </dt>
                <dd class="opt">
                    <input id="ws_socket" name="ws_socket" value="<?php echo $config['ws_socket']; ?>" class="input-txt" type="text" placeholder="ws://im.tp-shop.cn:8283">
                    <p class="notic">im客服的连接方式：ws://im.tp-shop.cn:8283</p>
                </dd>
            </dl>
            <div class="bot" style="padding-left: 11%"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">确认提交</a></div>
        </div>
    </form>
</div>
<script type="text/javascript">
    $('.im input[type="radio"]').click(function(){
        var val=$('#tpshop-im:checked').val();
        if(val == null){
            $('#tpshop-im-web').hide();
        }else {
            $('#tpshop-im-web').show();
        }
    });
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