<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:46:"./application/admin/view/system/distribut.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
                <h3>分销设置</h3>
                <h5>平台分销选项设置</h5>
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
            <span id="explanationZoom" title="收起提示"></span> </div>
        <ul>
            <li>如果分销开关关闭，那么不会平台启用分销模块，每个商品独立设置分销金</li>
            <li>如果规则由平台设置，那么分销规则统一按平台设置的规则返利，否则按各商家自行设置的比例规则</li>
            <li>所有分销商获佣比例之和不超过100%，比例为0则也视为不参与分佣，平台分销规则可针对分销商设置等级</li>
        </ul>
    </div>
    <form method="post" enctype="multipart/form-data" name="form1" id="handlepost" action="<?php echo U('System/handle'); ?>">
        <input type="hidden" name="inc_type" value="<?php echo $inc_type; ?>">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">分销开关</dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="switch1" class="cb-enable  <?php if($config['switch'] == 1): ?>selected<?php endif; ?>">开启</label>
                        <label for="switch0" class="cb-disable <?php if($config['switch'] == 0): ?>selected<?php endif; ?>">关闭</label>
                        <input type="radio" onclick="$('#switch_on_off').show();"  id="switch1"  name="switch" value="1" <?php if($config['switch'] == 1): ?>checked="checked"<?php endif; ?>>
                        <input type="radio" onclick="$('#switch_on_off').hide();" id="switch0" name="switch" value="0" <?php if($config['switch'] == 0): ?>checked="checked"<?php endif; ?> >
                    </div>
                    <p class="notic">分销开关</p>
                </dd>
            </dl>
            <div id="switch_on_off" <?php if($config['switch'] == 0): ?>style="display: none;"<?php endif; ?>>
            <dl class="row">
                <dt class="tit">
                    <label>分销规则设置</label>
                </dt>
                <dd class="opt">
				    <input type="radio" name="distribut_set_by" value="0" <?php if($config['distribut_set_by'] == 0): ?> checked <?php endif; ?> >由平台设置  &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="distribut_set_by" value="1"<?php if($config['distribut_set_by'] == 1): ?> checked <?php endif; ?> >由商家设置
                </dd>
            </dl>
            <!--
            <dl class="row">
                <dt class="tit">
                    <label>成为分销商条件</label>
                </dt>
                <dd class="opt">
                    <input type="radio" name="condition" value="0" <?php if($config[condition] == 0): ?>checked="checked"<?php endif; ?>>无条件成为分销商 &nbsp;&nbsp;
                    <input type="radio" name="condition" value="1" <?php if($config[condition] == 1): ?>checked="checked"<?php endif; ?>>需下单成为分销商 &nbsp;&nbsp;
                    <input type="radio" name="condition" value="2" <?php if($config[condition] == 2): ?>checked="checked"<?php endif; ?>>需提交申请审核 &nbsp;&nbsp;
                    <p class="notic">分销商条件</p>
                </dd>
            </dl>
            -->
            <dl class="row">
                <dt class="tit">
                    <label for="distribut_regrade">推荐开店有效期</label>
                </dt>
                <dd class="opt">
                    <select name="open_store_time" id="distribut_open_store">
                        <option value="1" <?php if($config['open_store_time'] == 1): ?>selected="selected"<?php endif; ?>>1天</option>
                        <option value="2" <?php if($config['open_store_time'] == 2): ?>selected="selected"<?php endif; ?>>2天</option>
                        <option value="3" <?php if($config['open_store_time'] == 3): ?>selected="selected"<?php endif; ?>>3天</option>
                        <option value="4" <?php if($config['open_store_time'] == 4): ?>selected="selected"<?php endif; ?>>4天</option>
                        <option value="5" <?php if($config['open_store_time'] == 5): ?>selected="selected"<?php endif; ?>>5天</option>
                        <option value="6" <?php if($config['open_store_time'] == 6): ?>selected="selected"<?php endif; ?>>6天</option>
                        <option value="7" <?php if($config['open_store_time'] == 7): ?>selected="selected"<?php endif; ?>>7天</option>
                        <option value="8" <?php if($config['open_store_time'] == 8): ?>selected="selected"<?php endif; ?>>8天</option>
                        <option value="9" <?php if($config['open_store_time'] == 9): ?>selected="selected"<?php endif; ?>>9天</option>
                        <option value="10" <?php if($config['open_store_time'] == 10): ?>selected="selected"<?php endif; ?>>10天</option>
                        <option value="0" <?php if($config['open_store_time'] == 0): ?>selected="selected"<?php endif; ?>>长期</option>
                    </select>
                    <p class="notic">例：几天日内开店成功，才能算是这个人推荐的的店</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="distribut_regrade">返佣级数</label>
                </dt>
                <dd class="opt">  
                    <select name="regrade" id="distribut_regrade">
						<option value="0" <?php if($config['regrade'] == 0): ?>selected="selected"<?php endif; ?>>返一级</option>
						<option value="1" <?php if($config['regrade'] == 1): ?>selected="selected"<?php endif; ?>>返两级</option>
						<option value="2" <?php if($config['regrade'] == 2): ?>selected="selected"<?php endif; ?>>返三级</option>
                    </select>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>一级分销商获佣百分比</label>
                </dt>
                <dd class="opt">
                    <input name="first_rate" id="distribut_first_rate" value="<?php echo (isset($config['first_rate']) && ($config['first_rate'] !== '')?$config['first_rate']:0); ?>" class="input-txt" type="text"  onpaste="this.value=this.value.replace(/[^\d]/g,'')" onKeyUp="this.value=this.value.replace(/[^\d]/g,'')" >
                    <p class="notic">购买者直接推荐人返佣占商品分销金比例</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>二级分销商获佣百分比</label>
                </dt>
                <dd class="opt">
                    <input name="second_rate" id="distribut_second_rate" value="<?php echo (isset($config['second_rate']) && ($config['second_rate'] !== '')?$config['second_rate']:0); ?>" class="input-txt" type="text"  onpaste="this.value=this.value.replace(/[^\d]/g,'')" onKeyUp="this.value=this.value.replace(/[^\d]/g,'')" >
                    <p class="notic">购买者推荐人的上级返佣占商品分销金比例 </p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>三级分销商获佣百分比</label>
                </dt>
                <dd class="opt">
                    <input name="third_rate" id="distribut_third_rate" value="<?php echo (isset($config['third_rate']) && ($config['third_rate'] !== '')?$config['third_rate']:0); ?>" class="input-txt" type="text"  onpaste="this.value=this.value.replace(/[^\d]/g,'')" onKeyUp="this.value=this.value.replace(/[^\d]/g,'')" >
                    <p class="notic">购买者推荐人的上上级占商品分销金比例</p>
                </dd>
            </dl>
            <dt class="tit">
                <label>提现金额</label>
            </dt>
            <dd class="opt">
                <input name="distribut_withdrawals_money" value="<?php echo $config['distribut_withdrawals_money']; ?>" class="input-txt" type="text">
                <p class="notic">满多少元可提现分销佣金</p>
            </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="distribut_date">分成时间</label>
                </dt>
                <dd class="opt">
                    <select name="date" id="distribut_date">
                        <?php $__FOR_START_165506650__=1;$__FOR_END_165506650__=31;for($i=$__FOR_START_165506650__;$i < $__FOR_END_165506650__;$i+=1){ ?>
                            <option value="<?php echo $i; ?>" <?php if($config[date] == $i): ?>selected="selected"<?php endif; ?>><?php echo $i; ?>天</option>
                        <?php } ?>
                    </select>
                    <p class="notic">订单收货确认后多少天可以分成</p>
                </dd>
            </dl>
            <dl class="row"><dt class="tit"><label><b>二维码相关设定</b></label></dt></dl>
            <dl class="row">
                <dt class="tit">
                  <label>页面二维码背景</label>
                </dt>
                <dd class="opt">
                  <div class="input-file-show">
                      <span class="show">
                          <a id="qr_back_a" target="_blank" class="nyroModal" rel="gal" href="<?php echo $config['qr_back']; ?>">
                            <i id="qr_back_i" class="fa fa-picture-o" onmouseover="layer.tips('<img src=<?php echo $config['qr_back']; ?>>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"></i>
                          </a>
                      </span>
                      <span class="type-file-box">
                          <input type="text" id="qr_back" name="qr_back" value="<?php echo $config['qr_back']; ?>" class="type-file-text">
                          <input type="button" value="选择上传..." class="type-file-button">
                          <input class="type-file-file" onClick="GetUploadify(1,'','weixin','qr_back_call_back')" size="30" hidefocus="true" nc_type="change_site_logo" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                      </span>
                  </div>
                  <span class="err"></span>
                </dd>
              </dl>
              <dl class="row">
                <dt class="tit">
                  <label>‘我的二维码’大背景</label>
                </dt>
                <dd class="opt">
                  <div class="input-file-show">
                      <span class="show">
                          <a id="qr_big_back_a" target="_blank" class="nyroModal" rel="gal" href="<?php echo $config['qr_big_back']; ?>">
                            <i id="qr_big_back_i" class="fa fa-picture-o" onmouseover="layer.tips('<img src=<?php echo $config['qr_big_back']; ?>>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"></i>
                          </a>
                      </span>
                      <span class="type-file-box">
                          <input type="text" id="qr_big_back" name="qr_big_back" value="<?php echo $config['qr_big_back']; ?>" class="type-file-text">
                          <input type="button" value="选择上传..." class="type-file-button">
                          <input class="type-file-file" onClick="GetUploadify(1,'','weixin','qr_big_back_call_back')" size="30" hidefocus="true" nc_type="change_site_logo" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                      </span>
                  </div>
                  <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>微信菜单关键字</label>
                </dt>
                <dd class="opt">
                    <input name="qrcode_menu_word" value="<?php echo $config['qrcode_menu_word']; ?>" class="input-txt" type="text">
                    <p class="notic">用户点击微信菜单中的某项，响应‘我的二维码’图片</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>微信输入关键字</label>
                </dt>
                <dd class="opt">
                    <input name="qrcode_input_word"  value="<?php echo $config['qrcode_input_word']; ?>" class="input-txt" type="text">
                    <p class="notic">用户在微信中输入的文本，响应‘我的二维码’图片</p>
                </dd>
            </dl>
            </div>
            <div class="bot" style="padding-left: 10.8%">
                <input type="hidden" name="inc_type" value="<?php echo $inc_type; ?>">
                <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onClick="formsubmit()">确认提交</a>
            </div>
        </div>
    </form>
</div>
<div id="goTop"> <a href="JavaScript:void(0);" id="btntop"><i class="fa fa-angle-up"></i></a><a href="JavaScript:void(0);" id="btnbottom"><i class="fa fa-angle-down"></i></a></div>
<script type="text/javascript">

function formsubmit(){
	var distribut_first_rate  = $.trim($('#distribut_first_rate').val());
	var distribut_second_rate = $.trim($('#distribut_second_rate').val());
	var distribut_third_rate  = $.trim($('#distribut_third_rate').val());		
	
	var rate = parseInt(distribut_first_rate) + parseInt(distribut_second_rate) + parseInt(distribut_third_rate);
	if(rate > 100)
	{
		layer.msg('三个分销商比例总和不得超过100%', {icon: 2,time: 2000});//alert('少年，邮箱不能为空！');		
		// alert('三个分销商比例总和不得超过100%');
		return false;
	}
	
	$('#handlepost').submit();
}
    
function qr_back_call_back(fileurl_tmp)
{
  $("#qr_back").val(fileurl_tmp);
  $("#qr_back_a").attr('href', fileurl_tmp);
  $("#qr_back_i").attr('onmouseover', "layer.tips('<img src="+fileurl_tmp+">',this,{tips: [1, '#fff']});");
}
function qr_big_back_call_back(fileurl_tmp)
{
  $("#qr_big_back").val(fileurl_tmp);
  $("#qr_big_back_a").attr('href', fileurl_tmp);
  $("#qr_big_back_i").attr('onmouseover', "layer.tips('<img src="+fileurl_tmp+">',this,{tips: [1, '#fff']});");
}
</script>
</body>
</html>