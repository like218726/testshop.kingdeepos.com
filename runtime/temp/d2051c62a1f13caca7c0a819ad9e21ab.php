<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:35:"./application/admin/view/ad/ad.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<script src="/public/static/js/layer/laydate/laydate.js"></script>
<body style="background-color: #FFF; overflow: auto;"> 
<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="javascript:history.back();" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
      <div class="subject">
        <h3> 广告详情</h3>
        <h5>广告添加与管理</h5>
      </div>
    </div>
  </div>
    <!--表单数据-->
    <form method="post" id="handleposition" action="<?php echo U('Admin/Ad/adHandle'); ?>">  
    <div class="ncap-form-default">
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>广告名称：</label>
        </dt>
        <dd class="opt">
          <input type="text" placeholder="名称" class="input-txt" name="ad_name" value="<?php echo (isset($info['ad_name']) && ($info['ad_name'] !== '')?$info['ad_name']:'自定义广告名称'); ?>">
		  <span class="err" id="err_ad_name" style="color:#F00; display:none;">广告名称不能为空</span>                                                  
          <p class="notic"></p>
        </dd>
      </dl>	   
	  <dl class="row">
        <dt class="tit" colspan="2">
          <label>广告类型：</label>
        </dt>
        <dd class="opt">
          <?php if(\think\Request::instance()->param('is_app_ad') == 1): ?>
          			<select name="media_type" id="media_type" class="input-sm" class="form-control">
		                 <option value="3" <?php if($info['media_type'] == 3): ?>selected<?php endif; ?>>商品</option>                                             
		                 <option value="4" <?php if($info['media_type'] == 4): ?>selected<?php endif; ?>>分类</option>
		                 <option value="5" <?php if($info['media_type'] == 5): ?>selected<?php endif; ?>>Web链接</option>
		            </select>
          <?php else: ?>
	          	 <div id="gcategory">
		            <select name="media_type" class="input-sm" class="form-control">
		                 <option value="0">图片</option>                                             
		                 <!--<option value="1">flash</option>-->
		            </select>                   
		          </div>  
          <?php endif; ?>	 
        </dd>
      </dl> 
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>广告链接：</label>
        </dt>
        <dd class="opt" id="ad_link_dd">
        	<div id="link_url"> <!-- 网页链接 -->
        		  <input type="text" placeholder="广告链接" class="input-txt" name="ad_link" value="<?php echo $info['ad_link']; ?>">
				  <span class="err" id="err_ad_link" style="color:#F00; display:none;"></span>
		          <p class="notic"></p>
        	</div>
        	<div id="link_category"> <!-- 分类链接 -->
        		  <select name="cat_id1" id="cat_id1" onblur="get_category(this.value,'cat_id2','0');"  class="class-select valid">
	                <option value="0">请选择商品分类</option>                                      
	                     <?php if(is_array($cat_list) || $cat_list instanceof \think\Collection || $cat_list instanceof \think\Paginator): if( count($cat_list)==0 ) : echo "" ;else: foreach($cat_list as $k=>$v): ?>                                                                           
	                       <option value="<?php echo $v['id']; ?>" <?php if($v['id'] == $info[cat_id1]): ?>selected="selected"<?php endif; ?> >
	                            <?php echo $v['name']; ?>
	                       </option>
	                     <?php endforeach; endif; else: echo "" ;endif; ?>
	              </select>
	              <select name="cat_id2" id="cat_id2" onblur="get_category(this.value,'cat_id3','0');" class="class-select valid">
	                <option value="0">请选择商品分类</option>
	              </select>    
	              <select name="cat_id3" id="cat_id3" class="class-select valid">
	                <option value="0">请选择商品分类</option>
	              </select> 
        	</div>
        	<div id="link_goods"> <!-- 商品链接 -->
        		  <a id="add_type" class="ncap-btn" onclick="select_goods_dialog()">选择商品</a> 
        		  <span id="goods_name"><?php echo $info['goods_name']; ?></span>
        		  <input name="goods_id" id="goods_id" type="hidden" value="<?php echo $info['ad_link']; ?>">
        	</div> 
        </dd>
      </dl>
 	
      <?php if(\think\Request::instance()->param('is_app_ad') == 1): ?>
     		<input name="pid" type="hidden" value="<?php echo $info['pid']; ?>">
      <?php else: ?>
	  <dl class="row">
        <dt class="tit" colspan="2">
          <label>广告位置：</label>
        </dt>
        <dd class="opt">
          <div>
          		<select name="pid" class="input-sm" class="form-control">
	                <?php if(is_array($position) || $position instanceof \think\Collection || $position instanceof \think\Paginator): $i = 0; $__LIST__ = $position;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
	                       <option value="<?php echo $item['position_id']; ?>" <?php if($info[pid] == $item[position_id]): ?>selected<?php endif; ?>><?php echo $item['position_name']; ?></option>
	               <?php endforeach; endif; else: echo "" ;endif; ?>                  
	            </select> 
          </div>          
        </dd>
      </dl>    
       <?php endif; ?>       
	  <dl class="row">
        <dt class="tit">
          <label>开始日期：</label>
        </dt>
        <dd class="opt">
            <input type="text" class="input-txt" id="start_time" name="begin"  value="<?php echo (isset($info['start_time']) && ($info['start_time'] !== '')?$info['start_time']:'2016-01-01'); ?>"/>
          <span class="err"></span>
        </dd>
      </dl>    
	  <dl class="row">
        <dt class="tit">
          <label>结束时间：</label>
        </dt>
        <dd class="opt">
            <input type="text" class="input-txt" id="end_time" name="end"  value="<?php echo (isset($info['end_time']) && ($info['end_time'] !== '')?$info['end_time']:'2019-01-01'); ?>"/>
          <span class="err"></span>
        </dd>
      </dl>        
      
      <dl class="row">
        <dt class="tit">
          <label>广告图片</label>
        </dt>
        <dd class="opt">
          <div class="input-file-show">
                        <span class="show">
                            <a id="img_a" target="_blank" class="nyroModal" rel="gal" href="<?php echo $info['ad_code']; ?>">
                              <i id="img_i" class="fa fa-picture-o" onmouseover="layer.tips('<img src=<?php echo $info['ad_code']; ?>>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"></i>
                            </a>
                        </span>
           	            <span class="type-file-box">
                            <input type="text" id="ad_code" name="ad_code" value="<?php echo $info['ad_code']; ?>" class="type-file-text">
                            <input type="button" name="button" id="button1" value="选择上传..." class="type-file-button">
                            <input class="type-file-file" onClick="GetUploadify(1,'','ad','img_call_back')" size="30" hidefocus="true" nc_type="change_site_logo" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                        </span>
          </div>
          <span class="err"></span>
          <p class="notic">请上传图片格式文件,建议图片尺寸<?php echo \think\Request::instance()->param('suggestion'); ?>(宽*高, 如果不按要求上传图片将会导致前端广告显示不友好)</p>
        </dd>
      </dl>
      <?php if(\think\Request::instance()->param('is_app_ad') == 0): ?>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>背景颜色：</label>
        </dt>
        <dd class="opt">
          <input type="color" placeholder="背景颜色：" class="input-txt" name="bgcolor" value="<?php echo $info['bgcolor']; ?>"  />
		  <span class="err" id="err_bgcolor" style="color:#F00; display:none;"></span>
          <p class="notic"></p>
        </dd>
      </dl>   
      <?php endif; if(\think\Request::instance()->param('is_app_ad') == 0): ?>   		 		       
      <dl class="row">
        <dt class="tit">
          <label>默认排序：</label>
        </dt>
        <dd class="opt">
          <input type="text" placeholder="排序" name="orderby" value="<?php echo $info['orderby']; ?>" class="input-txt">
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <?php endif; ?>
      <div class="bot"><a href="JavaScript:void(0);" onclick="adsubmit()" class="ncap-btn-big ncap-btn-green" id="submitBtn">确认提交</a></div>
    </div>
       <input type="hidden" name="act" value="<?php echo $act; ?>">
       <input type="hidden" name="ad_id" value="<?php echo $info['ad_id']; ?>">
  </form>
</div>
 <script>
	 function adsubmit(){
		$('#handleposition').submit();
	}
	
	 function select_goods_dialog(){
		 var url = "/index.php?m=Admin&c=ad&a=search_goods";
         layer.open({
             type: 2,
             title: '选择商品',
             shadeClose: true,
             shade: 0.2,
             area: ['1020px', '75%'],
             content: url
         });
	 }
	 
	 
	function refresh_media_type(mediaType){
		if(mediaType > 2){ 
			 if(mediaType == 3){ 
				 //商品
				 $("#link_goods").show();
				 $("#link_goods").siblings().hide(); 
			 }else if(mediaType == 4){
				 //商品分类
				 $("#link_category").show(); 
				 $("#link_category").siblings().hide();
			 }else if(mediaType == 5){
				//商品分类
				 $("#link_url").show(); 
				 $("#link_url").siblings().hide();
			 } 
		 } 
	}
 
    $(document).ready(function(){
		$('#start_time').layDate();
		$('#end_time').layDate();
		
		$("#media_type").on("change",function(){ 
			 var mediaType = $('#media_type option:selected').val();
			 console.log(mediaType);
			 refresh_media_type(mediaType);
		});
		
		<?php if(\think\Request::instance()->param('is_app_ad') == 1): if($info['ad_id'] > 0): ?>
				var mtype = "<?php echo $info['media_type']; ?>";
				refresh_media_type(mtype);//如果是编辑
			<?php else: ?>
				refresh_media_type(3);//如果是编辑
		 	<?php endif; else: ?>
		 	refresh_media_type(5);//如果是编辑
		<?php endif; if($info['cat_id2'] > 0): ?>
			 // 商品分类第二个下拉菜单
			 get_category('<?php echo $info[cat_id1]; ?>','cat_id2','<?php echo $info[cat_id2]; ?>');
		<?php endif; if($info['cat_id3'] > 0): ?>
			// 商品分类第二个下拉菜单
			 get_category('<?php echo $info[cat_id2]; ?>','cat_id3','<?php echo $info[cat_id3]; ?>');
		<?php endif; ?>
		 
	});
    
    function goods_call_back(goodsId,goodsName){
    	//选中的商品 
    	$('#goods_name').html(goodsName);
    	$('#goods_id').val(goodsId);
    	console.log("goodsId : "+goodsId);
    	layer.closeAll('iframe');
    }
    
     function img_call_back(fileurl_tmp)
     {
       $("#ad_code").val(fileurl_tmp);
       $("#img_a").attr('href', fileurl_tmp);
       $("#img_i").attr('onmouseover', "layer.tips('<img src="+fileurl_tmp+">',this,{tips: [1, '#fff']});");
     }
 </script>
</body>
</html>