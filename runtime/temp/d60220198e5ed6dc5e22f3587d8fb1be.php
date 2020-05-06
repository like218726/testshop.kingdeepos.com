<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:44:"./application/admin/view/wechat/setting.html";i:1587634376;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
 
<style type="text/css">
html, body {
	overflow: visible;
}
</style>  
<body style="background-color: #FFF; overflow: auto;">
<div id="toolTipLayer" style="position: absolute; z-index: 9999; display: none; visibility: visible; left: 95px; top: 573px;"></div>
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="javascript:history.back();" title="微信公众号配置"><i class="fa fa-arrow-circle-o-left"></i></a>
      <div class="subject">
        <h3>微信公众号配置</h3>
        <h5>配置微信公众号, token、Appid、AppSecret要与微信公众开放平台信息一致</h5>
      </div>
    </div>
  </div>
  <form class="form-horizontal" method="post" id="handlepost" action="">    
    <div class="ncap-form-default">
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>URL(服务器地址)</label>
        </dt>
        <dd class="opt">
          <input type="text" style="background-color: #d2d6de" readonly placeholder="请先以下信息保存后显示" value="<?php echo $apiurl; ?>" class="input-txt">
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>Token</label>
        </dt>
        <dd class="opt">
          <input type="text" name="w_token" id="w_token" value="<?php echo $wechat['w_token']; ?>" class="input-txt">
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>公众号名称</label>
        </dt>
        <dd class="opt">
         <input type="text" name="wxname" value="<?php echo $wechat['wxname']; ?>" class="input-txt">
          <span class="err"></span>
        </dd>
      </dl>    
	  <dl class="row">
        <dt class="tit">
          <label><em>*</em>公众号原始id</label> 
        </dt>
        <dd class="opt">
          <input type="text" class="input-txt" name="wxid" value="<?php echo $wechat['wxid']; ?>" />
        </dd>
      </dl>        
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>微信号</label> 
        </dt>
        <dd class="opt">
          <input type="text" name="weixin" value="<?php echo $wechat['weixin']; ?>" class="input-txt">
          <span class="err"></span>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label>头像地址</label>
        </dt>
        <dd class="opt">
          <div class="input-file-show">
              <span class="show">
                  <a id="img_a" target="_blank" class="nyroModal" rel="gal" href="<?php echo $wechat['headerpic']; ?>">
                    <i id="img_i" class="fa fa-picture-o" onmouseover="layer.tips('<img src=<?php echo $wechat['headerpic']; ?>>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"></i>
                  </a>
              </span>
              <span class="type-file-box">
                  <input type="text" id="headerpic" name="headerpic" value="<?php echo $wechat['headerpic']; ?>" class="type-file-text">
                  <input type="button" value="选择上传..." class="type-file-button">
                  <input class="type-file-file" onClick="GetUploadify(1,'','weixin','img_call_back')" size="30" hidefocus="true" nc_type="change_site_logo" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
              </span>
          </div>
          <span class="err"></span>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>AppID</label> 
        </dt>
        <dd class="opt">
          <input type="text" name="appid" size="30" value="<?php echo $wechat['appid']; ?>" class="input-txt">
          <span class="err"></span>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>AppSecret</label> 
        </dt>
        <dd class="opt">
          <input type="text" name="appsecret" value="<?php echo $wechat['appsecret']; ?>" class="input-txt">
          <span class="err"></span>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label>二维码</label>
        </dt>
        <dd class="opt">
          <div class="input-file-show">
              <span class="show">
                  <a id="img_a2" target="_blank" class="nyroModal" rel="gal" href="<?php echo $wechat['qr']; ?>">
                    <i id="img_i2" class="fa fa-picture-o" onmouseover="layer.tips('<img src=<?php echo $wechat['qr']; ?>>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"></i>
                  </a>
              </span>
              <span class="type-file-box">
                  <input type="text" id="qr" name="qr" value="<?php echo $wechat['qr']; ?>" class="type-file-text">
                  <input type="button" value="选择上传..." class="type-file-button">
                  <input class="type-file-file" onClick="GetUploadify(1,'','weixin','img_call_back2')" size="30" hidefocus="true" nc_type="change_site_logo" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
              </span>
          </div>
          <span class="err"></span>
          <p class="notic"> 1.此处上传公众号二维码,建议上传90*高90像素GIF\JPG\PNG格式图片。2.此处二维码将会在PC首页侧边栏(右下角)展示</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label>微信号类型</label> 
        </dt>
        <dd class="opt">
        <div class="sDiv2">	 
          <select name="type" class="small form-control">
              <option <?php if($wechat['type'] == 1): ?>selected<?php endif; ?> value="1">订阅号</option>
              <option <?php if($wechat['type'] == 2): ?>selected<?php endif; ?> value="2">认证订阅号</option>
              <option <?php if($wechat['type'] == 3): ?>selected<?php endif; ?> value="3">服务号</option>
              <option <?php if($wechat['type'] == 4): ?>selected<?php endif; ?> value="4">认证服务号</option>
          </select>
          </div>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label>微信接入状态</label> 
        </dt>
        <dd class="opt">
          <input type="radio" name="wait_access" value="0" <?php if($wechat['wait_access'] == 0): ?>checked="checked"<?php endif; ?>/> 等待接入
          <input type="radio" name="wait_access" value="1" <?php if($wechat['wait_access'] == 1): ?>checked="checked"<?php endif; ?>/> 已接入
          <p class="impor" style="color:red"><strong>重要:</strong>任何时候都要勾选"已接入",否则微信功能无法正常使用. 除非 , 在接入微信(开发 ->基本配置 -> 修改配置)提交url和token<br/>提示'url超时'或'token'错误时, 此处临时勾选为"等待接入",提交成功后改为"已接入"</p>
        </dd>
      </dl>
      <input type="hidden" name="id" value="<?php echo $wechat['id']; ?>">
      <div class="bot"><a href="JavaScript:void(0);" onClick="formSubmit()" class="ncap-btn-big ncap-btn-green" id="submitBtn">确认提交</a></div>
    </div>
  </form>
</div>
<script type="text/javascript">
  
$(document).ready(function(){
	
	$("#handlepost").validate({
		debug: false, //调试模式取消submit的默认提交功能   
		focusInvalid: false, //当为false时，验证无效时，没有焦点响应  
        onkeyup: false,   
        submitHandler: function(form){   //表单提交句柄,为一回调函数，带一个参数：form   
            form.submit();   //提交表单   
        },  
        ignore:":button",	//不验证的元素
        rules:{
        	w_token:{
        		required:true
        	},
        	wxname:{
        		required:true
        	},
        	wxid:{
        		required:true
        	},
        	weixin:{
        		required:true
        	},
        	appid:{
        		required:true
        	},
        	appsecret:{
        		required:true
        	}
        },
        messages:{
        	w_token:{
        		required:"请填写token"
        	},
        	wxname:{
        		required:"请填写公众号名称"
        	},
        	wxid:{
        		required:"请填写公众号原始id"
        	},
        	weixin:{
        		required:"请填写微信号"
        	},
        	appid:{
        		required:"请填写appid"
        	},
        	appsecret:{
        		required:"请填写AppSecret"
        	}
        }
	});
	
	 
});

function formSubmit(){
	$("#handlepost").submit();
}
function img_call_back(fileurl_tmp)
{
  $("#headerpic").val(fileurl_tmp);
  $("#img_a").attr('href', fileurl_tmp);
  $("#img_i").attr('onmouseover', "layer.tips('<img src="+fileurl_tmp+">',this,{tips: [1, '#fff']});");
}
function img_call_back2(fileurl_tmp)
{
  $("#qr").val(fileurl_tmp);
  $("#img_a2").attr('href', fileurl_tmp);
  $("#img_i2").attr('onmouseover', "layer.tips('<img src="+fileurl_tmp+">',this,{tips: [1, '#fff']});");
}
</script>
</body>
</html>