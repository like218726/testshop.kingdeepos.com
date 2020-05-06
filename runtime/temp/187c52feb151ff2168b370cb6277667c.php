<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:41:"./application/admin/view/wechat/menu.html";i:1587634376;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
        <h3>微信菜单管理</h3>
        <h5>微信菜单</h5>
      </div>
    </div>
  </div>
  <!-- 操作说明 -->
  <div id="explanation" class="explanation" style="color: rgb(44, 188, 163); background-color: rgb(237, 251, 248); width: 99%; height: 100%;">
    <div id="checkZoom" class="title"><i class="fa fa-lightbulb-o"></i>
      <h4 title="提示相关设置操作时应注意的要点">操作提示</h4>
      <span title="收起提示" id="explanationZoom" style="display: block;"></span>
    </div>
     <ul>
      <li>同微信公众号后台添加菜单一样,会员在此添加微信菜单</li>
      <li>如果需要添加小程序菜单，请把路径指定为首页： 复制这个pages/index/index/index</li>
    </ul>
  </div>
  <div class="flexigrid">
    <div class="mDiv">
      <div class="ftitle">
        <h3>菜单列表</h3>
        <h5></h5>
      </div>
      <div title="刷新数据" class="pReload"><i class="fa fa-refresh"></i></div>
	  <form class="navbar-form form-inline"  method="post" name="search-form2" id="search-form2">  
      <div class="sDiv">
      </div>
     </form>
    </div>
    <div class="hDiv">
      <div class="hDivBox">
        <table cellspacing="0" cellpadding="0">
          <thead>
	        	<tr>
	              <th class="sign" axis="col0">
	                <div style="width: 24px;"><i class="ico-check"></i></div>
	              </th>
	              <th align="left" abbr="order_sn" axis="col3" class="">
	                <div style="text-align: left; width: 320px;" class="">菜单名称</div>
	              </th>
	              <th align="center" abbr="article_time" axis="col6" class="">
	                <div style="text-align: left; width: 260px;" class="">菜单类型</div>
	              </th>
	              <th align="center" abbr="article_time" axis="col6" class="">
	                <div style="text-align: left; width: 360px;" class="">菜单URL</div>
	              </th>
	              <th style="width:100%" axis="col7">
	                <div></div>
	              </th>
	            </tr> 
	          </thead>
        </table>
      </div>
    </div>
    <div class="bDiv" style="height: auto;">
      <div id="flexigrid" cellpadding="0" cellspacing="0" border="0">
      <form action="" method="post" id="menuForm">
      <table>
		 	<tbody id="tbody">
				<?php if(is_array($p_lists) || $p_lists instanceof \think\Collection || $p_lists instanceof \think\Paginator): $i = 0; $__LIST__ = $p_lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?>
			  	<tr data-id="<?php echo $list['id']; ?>" class="odd  pmenu<?php echo $list['id']; ?> menu<?php echo $list['id']; ?>">
			        <td class="sign" axis="col0">
			          <div style="width: 24px;"><i class="ico-check" ></i></div>
			        </td>
			        <td align="left" abbr="username" axis="col3" class="">
			          <div style="text-align: left; width: 320px;" class="">
			          <input type="text" name="menu[<?php echo $list['id']; ?>][name]" value="<?php echo $list['name']; ?>" class="input-txt topmenu">
			          <a class="btn green" onclick="addcmenu(<?php echo $list['id']; ?>);"><i class="fa fa-plus"></i>添加</a>
			          <a class="btn red" onclick="delmenu(<?php echo $list['id']; ?>, false);"><i class="fa fa-trash-o"></i>删除</a>
			          </div>
			        </td>
			        <td align="left" abbr="article_time" axis="col6" class="">
			               <div style="text-align: left; width: 260px;" class="opt">
			       			<select name="menu[<?php echo $list['id']; ?>][type]" class="form-control">
								<option <?php if($list['type'] == 'view'): ?>selected<?php endif; ?> value="view">链接</option>
								<option <?php if($list['type'] == 'click'): ?>selected<?php endif; ?> value="click">触发关键字</option>
								<option <?php if($list['type'] == 'scancode_push'): ?>selected<?php endif; ?> value="scancode_push">扫码</option>
								<option <?php if($list['type'] == 'scancode_waitmsg'): ?>selected<?php endif; ?> value="scancode_waitmsg"> 扫码（等待信息）</option>
								<option <?php if($list['type'] == 'pic_sysphoto'): ?>selected<?php endif; ?> value="pic_sysphoto">系统拍照发图</option>
								<option <?php if($list['type'] == 'pic_photo_or_album'): ?>selected<?php endif; ?> value="pic_photo_or_album">拍照或者相册发图</option>
								<option <?php if($list['type'] == 'pic_weixin'): ?>selected<?php endif; ?> value="pic_weixin">微信相册发图</option>
								<option <?php if($list['type'] == 'location_select'): ?>selected<?php endif; ?> value="location_select">地理位置</option>
								<option <?php if($list['type'] == 'miniprogram'): ?>selected<?php endif; ?> value="miniprogram">小程序</option>
							</select>
			       		</div>
			          </td>
			          <td align="left" abbr="article_time" axis="col6" class="">
			               <div style="text-align: left; width: 360px;" class=""><input type="text" value="<?php echo $list['value']; ?>" style="width:300px" name="menu[<?php echo $list['id']; ?>][value]" class="input-txt"></div>
			          </td> 
			         <td align="" class="" style="width: 100%;">
			            <div>&nbsp;</div>
			          </td>
			          <input style="width: 100%" name="menu[<?php echo $list['id']; ?>][pid]" type="hidden" value="0">
			      </tr>
			       <!--父级操作-->
			       <?php if(is_array($c_lists) || $c_lists instanceof \think\Collection || $c_lists instanceof \think\Paginator): $i = 0; $__LIST__ = $c_lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$clist): $mod = ($i % 2 );++$i;if($clist['pid'] == $list['id']): ?>
			       			<tr class="odd  pmenu<?php echo $list['id']; ?> menu<?php echo $clist['id']; ?>">
			       					<td class="sign" axis="col0">
							          <div style="width: 24px;"><i class="fa fa-arrow-circle-right star-gray"></i></div>
							        </td>
			       					<td align="left" abbr="username" axis="col3" class="" <?php if($clist['pid'] > 0): ?>style="padding-left: 5em"<?php endif; ?>>
							          <div style="text-align: left; width: 320px;" class="">
							          <input type="text" name="menu[<?php echo $clist['id']; ?>][name]" value="<?php echo $clist['name']; ?>" class="input-txt"> 
							          <a class="btn red" onclick="delmenu(<?php echo $clist['id']; ?> , false);"><i class="fa fa-trash-o"></i>删除</a>
							          </div>
							        </td>
							        <td align="left" abbr="article_time" axis="col6" class="">
						               <div style="text-align: left; width: 260px;" class="opt">
										<select name="menu[<?php echo $clist['id']; ?>][type]" class="form-control">
											<option <?php if($clist['type'] == 'view'): ?>selected<?php endif; ?> value="view">链接</option>
											<option <?php if($clist['type'] == 'click'): ?>selected<?php endif; ?> value="click">触发关键字</option>
											<option <?php if($clist['type'] == 'scancode_push'): ?>selected<?php endif; ?> value="scancode_push">扫码</option>
											<option <?php if($clist['type'] == 'scancode_waitmsg'): ?>selected<?php endif; ?> value="scancode_waitmsg"> 扫码（等待信息）</option>
											<option <?php if($clist['type'] == 'pic_sysphoto'): ?>selected<?php endif; ?> value="pic_sysphoto">系统拍照发图</option>
											<option <?php if($clist['type'] == 'pic_photo_or_album'): ?>selected<?php endif; ?> value="pic_photo_or_album">拍照或者相册发图</option>
											<option <?php if($clist['type'] == 'pic_weixin'): ?>selected<?php endif; ?> value="pic_weixin">微信相册发图</option>
											<option <?php if($clist['type'] == 'location_select'): ?>selected<?php endif; ?> value="location_select">地理位置</option>
											<option <?php if($clist['type'] == 'miniprogram'): ?>selected<?php endif; ?> value="miniprogram">小程序</option>
										</select>
						       		</div>
						          </td>
						          <td align="left" abbr="article_time" axis="col6" class="">
						               <div style="text-align: left; width: 360px;" class=""><input type="text" value="<?php echo $clist['value']; ?>" name="menu[<?php echo $clist['id']; ?>][value]" style="width:300px"   class="input-txt"></div>
						          </td> 
						          <input style="width: 100%" name="menu[<?php echo $clist['id']; ?>][pid]" type="hidden" value="<?php echo $clist['pid']; ?>">
			       			</tr>
			       		<?php endif; endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
		    </tbody>
		</table>
		<div class="sDiv" style="float:left;margin-top:10px">
	        		<a class="btn green" onclick="addpmenu()"><i class="fa fa-plus"></i>添加一级菜单</a>
				    <a class="btn green" onclick="formSubmit()" style="float:right" >保存</a>
 		</div>
 		</form>
      </div>
      <div class="iDiv" style="display: none;"></div>
    </div>
    <!--分页位置--> 
   	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
	 
	 	//点击刷新数据
		$('.fa-refresh').click(function(){
			location.href = location.href;
		});
	 
		$('.ico-check ' , '.hDivBox').click(function(){
			$('tr' ,'.hDivBox').toggleClass('trSelected' , function(index,currentclass){
	    		var hasClass = $(this).hasClass('trSelected');
	    		$('tr' , '#flexigrid').each(function(){
	    			if(hasClass){
	    				$(this).addClass('trSelected');
	    			}else{
	    				$(this).removeClass('trSelected');
	    			}
	    		});  
	    	});
		});
	});
 
	var i  = <?php echo $max_id; ?>;
	//添加菜单
	function addpmenu(){
		var pmenu = $('#tbody .topmenu');
		if(pmenu.length >= 3){
			layer.alert('最多三个一级菜单', {icon: 2}); 
			return;
		}
		i++;
		var id = i;
		var tpl = $("#parent_menu_tpl").html();
		tpl = tpl.replace(/__id__/g,id).replace('<table>','').replace('</table>','').replace('<tbody>','').replace('</tbody>','');
		$('#tbody').append(tpl);
	}

	function addcmenu(pid){
		var cmenu = $('#tbody .pmenu'+pid);
		if(cmenu.length >= 6){
			layer.alert('一级菜单下最多5个二级菜单', {icon: 2});  //alert('一级菜单下最多5个二级菜单');
			return;
		}
		i++;
		var id = i;
		var tpl = $("#children_menu_tpl").html();
		tpl = tpl.replace(/__id__/g,id);
		tpl = tpl.replace(/__pid__/g,pid);
		tpl = tpl.replace('<table>','').replace('</table>','').replace('<tbody>','').replace('</tbody>','');
		$(cmenu.last()).after(tpl);
	}

	function delmenu(id , isNewAdd){
		layer.confirm("确定删除吗？", function(){
			if(isNewAdd){
				//删除子分类
				$('.pmenu'+id).remove();
				$('.menu'+id).remove();
				layer.closeAll();
			}else{
				$.ajax({
					url:'/index.php?m=Admin&c=Wechat&a=del_menu&id='+id,
					type:'get',
					success:function(data){
						layer.closeAll();
						if(data=='success'){
							//删除子分类
							$('.pmenu'+id).remove();
							$('.menu'+id).remove();
						}else{
							layer.msg('删除失败',{icon:2});
						}
					}
				});
			}
		});

	}
  
	function formSubmit(){
		var isFill = true;
		var len = $("#menuForm input").length;
		if(len <= 1){
			layer.msg('请添加菜单!',{icon:2});
			return;
		}
		
		$("#menuForm input").each(function(){
				if($(this).val() == ""){
					isFill = false;
					return false;
				}	  
		 }); 
		 
		 if(!isFill){
			 layer.msg('请将数据填充完整!',{icon:2});
			 return;
		 }
		 
		 $("#menuForm").submit();
	}
	 
</script>
 
<div id="children_menu_tpl" style="display:none">
   		<table>
   			<tbody>
   				<tr class="odd  pmenu__pid__  menu__id__">
   						<td class="sign" axis="col0">
				          <div style="width: 24px;"><i class="fa fa-arrow-circle-right star-gray"></i></div>
				        </td>
       					<td align="left" abbr="username" axis="col3" class="" style="padding-left: 5em">
				          <div style="text-align: left; width: 320px;" class="">
				          <input type="text" name="menu[__id__][name]" value="" class="input-txt"> 
				          <a class="btn red" onclick="delmenu(__id__ , true);"><i class="fa fa-trash-o"></i>删除</a>
				          </div>
				        </td>
				        <td align="left" abbr="article_time" axis="col6" class="">
			               <div style="text-align: left; width: 260px;" class="opt">
							<select name="menu[__id__][type]" class="form-control">
								<option value="view">链接</option>
								<option value="click">触发关键字</option>
								<option value="scancode_push">扫码</option>
								<option value="scancode_waitmsg"> 扫码（等待信息）</option>
								<option value="pic_sysphoto">系统拍照发图</option>
								<option value="pic_photo_or_album">拍照或者相册发图</option>
								<option value="pic_weixin">微信相册发图</option>
								<option value="location_select">地理位置</option>
								<option value="miniprogram">小程序</option>
							</select>
			       		</div>
			          </td>
			          <td align="left" abbr="article_time" axis="col6" class="">
			               <div style="text-align: left; width: 360px;" class=""><input type="text" value="" name="menu[__id__][value]" style="width:300px"   class="input-txt"></div>
			          </td> 
			          <input style="width: 100%" name="menu[__id__][pid]" type="hidden" value="__pid__">
       			</tr>
   			</tbody>
   		</table>
   	</div>
   	<div id="parent_menu_tpl" style="display:none">
	   	<table>
		   	<tbody>
				<tr class="odd  pmenu__id__ menu__id__">
			        <td class="sign" axis="col0">
			          <div style="width: 24px;"><i class="ico-check" ></i></div>
			        </td>
			        <td align="left" abbr="username" axis="col3" class="">
			          <div style="text-align: left; width: 320px;" class="">
			          <input type="text" name="menu[__id__][name]" value="" class="input-txt topmenu">
			          <a class="btn green" onclick="addcmenu(__id__);"><i class="fa fa-plus"></i>添加</a>
			          <a class="btn red" onclick="delmenu(__id__ , true);"><i class="fa fa-trash-o"></i>删除</a>
			          </div>
			        </td>
			        <td align="left" abbr="article_time" axis="col6" class="">
			               <div style="text-align: left; width: 260px;" class="opt">
			       			<select name="menu[__id__][type]" class="form-control">
								<option value="view">链接</option>
								<option value="click">触发关键字</option>
								<option value="scancode_push">扫码</option>
								<option value="scancode_waitmsg"> 扫码（等待信息）</option>
								<option value="pic_sysphoto">系统拍照发图</option>
								<option value="pic_photo_or_album">拍照或者相册发图</option>
								<option value="pic_weixin">微信相册发图</option>
								<option value="location_select">地理位置</option>
								<option value="miniprogram">小程序</option>
							</select>
			       		</div>
			          </td>
			          <td align="left" abbr="article_time" axis="col6" class="">
			               <div style="text-align: left; width: 360px;" class=""><input type="text" value="" style="width:300px" name="menu[__id__][value]" class="input-txt"></div>
			          </td> 
			         <td align="" class="" style="width: 100%;">
			            <div>&nbsp;</div>
			          </td>
			          <input style="width: 100%" name="menu[__id__][pid]" type="hidden" value="0">
				</tr>
			</tbody>
		</table>
   	</div>
</body>
</html>