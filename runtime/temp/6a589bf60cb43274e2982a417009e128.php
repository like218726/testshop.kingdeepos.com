<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:43:"./application/admin/view/system/poster.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
    .span_1 {
        float: left;
        margin-left: 0px;
        height: 130px;
        line-height: 130px;
    }

    .span_1 ul {
        list-style: none;
        padding: 0px;
    }

    .span_1 ul li {
        border: 1px solid #CCC;
        height: 40px;
        padding: 0px 10px;
        margin-left: -1px;
        margin-top: -1px;
        line-height: 40px;
    }
    .tab-base a{
        height: 35px !important;
    }
    .ncap-form-default dd.opt input{
        /*margin-right: 10px;*/
    }
    .item-title h3{
        margin-top: 0;
    }
    .item-title .subject{
        padding: 0;
    }
    #myCanvas{
        padding-bottom: 200px;
    }
</style>

<body style="background-color: #FFF; overflow: auto;">

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>海报设置</h3>
                <h5>自定义海报设置</h5>
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
        <div class="bckopa-tips">
            <div class="title">
                <img src="/public/static/images/handd.png" alt="">
                <h4 title="提示相关设置操作时应注意的要点">操作提示</h4>
            </div>
            <ul>
                <li>自定义海报设置请注意上传的图片尺寸，避免影响海报的展示效果。</li>
            </ul>
        </div>
        <span title="收起提示" id="explanationZoom" style="display: block;"></span>
    </div>
    <div class="z_cont_wrap" style="overflow:auto;">


        <div class="z_form_wraps cont_fl">
        <form method="post" id="handlepost" action="<?php echo U('System/handle'); ?>">
            <input type="hidden" name="inc_type" value="<?php echo $inc_type; ?>">
            <div class="ncap-form-default" >
                <dl class="row imgtr">
                    <dt class="tit">
                        <label >海报名称</label>
                    </dt>
                    <dd class="opt">
                        <input type="text" maxlength="10" value="<?php echo $poster['poster_name']; ?>" name="poster_name" class="poster_name">
                    </dd>
                </dl>
                <dl class="row imgtr">
                    <dt class="tit">
                        <label for="background_img">背景图片</label>
                    </dt>
                    <dd class="opt">
                        <div class="input-file-show">
                        <span class="show">
                            <a id="img_a" class="nyroModal" rel="gal" href="<?php echo $poster['back_url']; ?>">
                                <i id="img_i" class="fa fa-picture-o" onmouseover="layer.tips('<img src=<?php echo $poster['back_url']; ?>>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"></i>
                            </a>
                        </span>
           	            <span class="type-file-box">
                            <input type="text"  name="background_img" id="background_img" value="<?php echo $poster['back_url']; ?>" class="type-file-text">
                            <input type="button" name="button" id="button1" value="选择上传..." class="type-file-button">
                            <input class="type-file-file" onClick="GetUploadifyPoster(1,'','poster','call_back');" size="30" hidefocus="true" nc_type="change_site_logo" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                        </span>
                        </div>
                        <span class="err"></span>
                        <p class="notic">自定义海报建议尺寸640*1008像素</p>
                    </dd>
                </dl>
                <dl class="row imgtr">
                    <dt class="tit">
                        <label for="background_img">模拟二维码</label>
                    </dt>
                    <dd class="opt">
                            <input type="checkbox" <?php if($poster['back_url'] != ''): ?>checked='checked'<?php endif; ?> class="enableQrcode"  style="margin-top: 5px"><span style="margin-left: 10px; vertical-align: top">启用</span>
                        <p class="notic">该二维码图片仅供调试作用</p>
                    </dd>
                </dl>
                <dl class="row imgtr">
                    <dt class="tit">
                        <label for="background_img">二维码位置</label>
                    </dt>
                    <dd class="opt">
                        <input type="text" value="0,0" onkeyup="keyup(this)" class="keyup_qrcode">
                        <p class="notic" style="color:red;">输入像素数值可移动模拟二维码在海报背景图上的位置</p>
                    </dd>
                </dl>
				<dl class="row imgtr">
                    <dt class="tit">
                        <label for="background_img">二维码大小</label>
                    </dt>
                    <dd class="opt">
                        <input type="text" value="" onkeyup="keyup_qrcode_size(this)" id="qrcode_size" >
                        <p class="notic" style="color:red;">数值越大，二维码图片越大</p>
                    </dd>
                </dl>
                <dl class="row imgtr upload-img" style="display: none;">
                    <dt class="tit">
                        <label for="background_img">图片尺寸</label>
                    </dt>
                    <dd class="opt">
                        <p class="notic" style="color:red;">当前上传的图片宽高像素为<span class="upload-width">0</span>*<span class="upload-height">0</span>,可根据该数据对二维码位置进行调整</p>
                    </dd>

                </dl>


                <!--<dl class="row imgtr">-->
                    <!--<dt class="tit">-->
                        <!--<label for="background_img">是否启用</label>-->
                    <!--</dt>-->
                    <!--<dd class="opt">-->
                       <!--<span style=" display: inline-block ">禁用：</span>  <input type="radio" style="margin-top: 0; " checked='checked' value="0" name="enabled"><span style=" display: inline-block;margin-left: 15px ">启用：</span><input type="radio" style="margin-top: 0; " <?php if($poster['enabled'] == 1): ?>checked='checked'<?php endif; ?> value="1" name="enabled">-->
                    <!--</dd>-->
                <!--</dl>-->
                <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="adsubmit();">确认提交</a></div>
            </div>
        </form>
        </div>
        <div class="z_user_wrap cont_fl">
            <div class="z_user_img">
                <!--  <img src="/public/static/images/z-ip6.png">-->
            </div>
            <div class="z_views_wrap" style="width: auto;height: auto">
                <div class="z_user_views" style="width: auto;height: auto;overflow: hidden;">

                </div>
            </div>
        </div>
    </div>



</div>

<div id="goTop"> <a href="JavaScript:void(0);" id="btntop"><i class="fa fa-angle-up"></i></a><a href="JavaScript:void(0);" id="btnbottom"><i class="fa fa-angle-down"></i></a></div>
</body>
<script>

    //全局背景图
    var img = new Image();
    //全局二维码模型
    var qrcode = new Image();
    //全局海报数据
    var data = {
        id:"<?php echo (isset($poster['id']) && ($poster['id'] !== '')?$poster['id']:0); ?>",
        poster_name:"",                                                       //海报名称
        canvas_width: "<?php echo (isset($poster['canvas_width']) && ($poster['canvas_width'] !== '')?$poster['canvas_width']:375); ?>",                   //画布宽度
        canvas_height: "<?php echo (isset($poster['canvas_height']) && ($poster['canvas_height'] !== '')?$poster['canvas_height']:675); ?>",                 //画布高度
        poster_width: "<?php echo (isset($poster['poster_width']) && ($poster['poster_width'] !== '')?$poster['poster_width']:0); ?>",                     //海报宽度
        poster_height: "<?php echo (isset($poster['poster_height']) && ($poster['poster_height'] !== '')?$poster['poster_height']:0); ?>",                   //海报高度
        back_url: "<?php echo (isset($poster['back_url']) && ($poster['back_url'] !== '')?$poster['back_url']:''); ?>",                            //海报路径
        canvas_x: "<?php echo (isset($poster['canvas_x']) && ($poster['canvas_x'] !== '')?$poster['canvas_x']:0); ?>",                             //canvas移动的x轴
        canvas_y: "<?php echo (isset($poster['canvas_y']) && ($poster['canvas_y'] !== '')?$poster['canvas_y']:0); ?>",                             //canvas移动的y轴
        enabled: 1,                                                           //是否启用海报
        qrcode_size: "<?php echo (isset($poster['qrcode_size']) && ($poster['qrcode_size'] !== '')?$poster['qrcode_size']:150); ?>",                     //二维码大小
    }


    $(function(){
        var canvas = document.createElement('canvas');
        canvas.id = "myCanvas";
        canvas.width = data.canvas_width;
        canvas.height = data.canvas_height;
        console.log(data);
        $('.z_user_views').append(canvas);

        //在事件外声明需要用到的变量
        //let ax,ay,x,y;
        var c = document.getElementById("myCanvas");
        var t = c.getContext("2d");

        //渲染海报
        if(data.id > 0){
            console.log(data.back_url)
            img.src=  data.back_url;
            qrcode.src=  '/public/images/poster/qrcode80.png';
            img.onload = function(){
                t.drawImage(img,0,0,data.poster_width,data.poster_height,0,0,data.canvas_width,data.canvas_height);
                t.drawImage(qrcode,data.canvas_x,data.canvas_y,data.qrcode_size,data.qrcode_size);
            }

            $('.keyup_qrcode').val(data.canvas_x+','+data.canvas_y);
            $('.upload-img').show();
            $('.upload-width').text( data.poster_width);
            $('.upload-height').text( data.poster_height);
            $('#qrcode_size').val(data.qrcode_size);
        }

//        c.onmousedown=function (e) {
//            c.onmousemove = function(e){
//                x= e.clientX -100;y=e.clientY - 162;
//                x < 295 ? x =e.clientX -100: x =295;
//                y < 587 ? y=e.clientY -162 : y=587;
//
//                //canvas移动的时候记录x和y轴数据
//                data.canvas_x = x;
//                data.canvas_y = y;
//                $('.keyup_qrcode').val(x+','+y);
//
//                //先清除之前的然后重新绘制
//                t.clearRect(0,0,canvas.width,canvas.height);
//                console.log(x,y)
//                t.drawImage(img,0,0,data.canvas_width,data.canvas_height);
//                t.drawImage(qrcode,x,y,80,80);
//            };
//            c.onmouseup = function(){
//                c.onmousemove = null;
//                c.onmouseup = null;
//            };
//        }

    })

    // 上传图片成功回调函数
    function call_back(fileurl_tmp,val,img_data){
        $('#myCanvas').attr('width',img_data.width);
        $('#myCanvas').attr('height',img_data.height);

        let cvs = document.getElementById("myCanvas");
        let cst = cvs.getContext("2d");

        data.canvas_width = (img_data.width > 680) ? 680 : img_data.width ;
        data.canvas_height = (img_data.height > 1008) ? 1008 : img_data.height;
        data.poster_width = img_data.width;
        data.poster_height = img_data.height;
        data.back_url = fileurl_tmp;

        $('.upload-img').show();
        $('.upload-width').text( data.poster_width);
        $('.upload-height').text( data.poster_height);

        cst.clearRect(0,0,cvs.width,cvs.height);

        //初始化到canvas上的背景海报
        img.src=  fileurl_tmp;
        img.onload = function(){
            cst.drawImage(img,0,0,img_data.width,img_data.height,0,0,data.canvas_width,data.canvas_height);
            //判断上传海报之前是否已启用二维码,true则二维码移动的位置记录在海报上
            qrcode.src && cst.drawImage(qrcode,data.canvas_x,data.canvas_y,data.qrcode_size,data.qrcode_size);
        }

        $("#background_img").val(fileurl_tmp);
        $("#img_a").attr('href', fileurl_tmp);
        $("#img_i").attr('onmouseover', "layer.tips('<img src="+fileurl_tmp+">',this,{tips: [1, '#fff']});");
    }



    //启用二维码模板
   $('.enableQrcode').click(function(){
       let cvs = document.getElementById("myCanvas");
       let cst = cvs.getContext("2d");
       if(!!$(this).attr('checked')){
           qrcode.src=  '/public/images/poster/qrcode80.png';
           qrcode.onload = function(){
               cst.drawImage(qrcode,data.canvas_x,data.canvas_y,data.qrcode_size,data.qrcode_size);
           }
       }else{
           //先清除之前的然后重新绘制
           cst.clearRect(0,0,data.canvas_width,data.canvas_height);
           if(data.back_url != ''){
               cst.drawImage(img,0,0,data.canvas_width,data.canvas_height);
           }
       }
   })

    function adsubmit(){
        if(!data.back_url){
            layer.alert('请上传海报背景图', {icon: 2});
            return false;
        }

        if(!$('.enableQrcode').attr('checked')){
            layer.alert('请启用二维码', {icon: 2});
            return false;
        }

        //data.enabled = $("input[name='enabled']:checked").val();
        data.poster_name = $("input[name='poster_name']").val();

        $.ajax({
            type: 'POST',
            url: "<?php echo U('Admin/System/poster_add'); ?>",
            data: data,
            success: function (res) {
                    layer.alert('操作成功', {icon: 1});
            }
        });
    }

    //手动输入x,y轴
    function keyup(obj){
        let cvs = document.getElementById("myCanvas");
        let cst = cvs.getContext("2d");

        let px = $(obj).val();
        let strs = px.split(","); //字符分割

        if(px.indexOf(',') == -1){
            $('.keyup_qrcode').val(strs[0]+','+ '0');
            return;
        }
        data.canvas_x = strs[0];
        data.canvas_y = strs[1] > 0?strs[1]:0;
		
		data.qrcode_size = $('#qrcode_size').val();

        cst.clearRect(0,0,cvs.width,cvs.height);
        img.src && cst.drawImage(img,0,0,data.canvas_width,data.canvas_height);
        qrcode.src && cst.drawImage(qrcode,strs[0],strs[1],data.qrcode_size,data.qrcode_size);
    }
	
	//手动输入二维码大小
    function keyup_qrcode_size(obj){
		data.qrcode_size = $(obj).val();
		let cvs = document.getElementById("myCanvas");
        let cst = cvs.getContext("2d");
		cst.clearRect(0,0,cvs.width,cvs.height);
        img.src && cst.drawImage(img,0,0,data.canvas_width,data.canvas_height);
        qrcode.src && cst.drawImage(qrcode,data.canvas_x,data.canvas_y,data.qrcode_size,data.qrcode_size);
    }
</script>
</html>