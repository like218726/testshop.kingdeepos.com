<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:51:"./application/admin/view/system/mp_center_menu.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
	<link rel="stylesheet" type="text/css" href="/public/static/css/base.css"/>
	<link rel="stylesheet" type="text/css" href="/public/bootstrap/css/bootstrap.min.css"/>
	<link rel="stylesheet" type="text/css" href="/public/bootstrap/css/bootstrap-colorpicker.css"/>
	<link rel="stylesheet" type="text/css" href="/public/static/css/personal.css"/>
<style>
	.colorpicker-alpha {
		display: none !important;
	}
	.z_cont_wrap{
		margin-bottom: 40px;
	}
	.z_cont_wrap{
		overflow: initial;
	}
	.clboth{
		clear: both;
	}
	.h-slc{
		padding: 6px 8px;
		border-radius: 4px !important;
		border: 1px solid #dddddd;
		width: 34px;
		height: 34px;
		display: inline-block;
		margin-right: 10px;
		font-size: 14px;
		text-align: center;
		background-color: #eee;
	}
	.igac{
		display: inline-block;
		cursor: pointer;
		height: 16px;
		vertical-align: text-top;
		width: 16px;
	}
	.b-red{
		border:1px solid #ff0000;
	}
</style>
	<body>
	<div class="z_cont_wrap">
		<div class="z_title_name"> 个人中心设置 <span style="color: red">只能用于app</span></div>
		<div class="z_user_wrap cont_fl">
			<div class="z_user_img">
				<img src="/public/static/images/z-ip6.png" />
			</div>
			<div class="z_views_wrap">
				<div class="z_user_views">
					<div class="z_user_vs">
						<div class="z_user_head">
							<div class="z_user_top">
								<div class="user_top_tx">
									<img src="/public/static/images/zTX.png" />
								</div>
								<div class="user_top_name">
									<a href="">会员名称</a>
								</div>
								<div class="user_top_title">
									<a href="">砖石会员</a>
								</div>
							</div>
							<div class="z_user_down">
								<div class="user_down_bg"></div>
								<ul>
									<li>
										<a href="">
											<div class="user_down_dev">1980.00</div>
											<div class="user_down_name">余额</div>
										</a>
									</li>
									<li>
										<a href="">
											<div class="user_down_dev">2886.00</div>
											<div class="user_down_name">积分</div>
										</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="z_user_li_wrap">
							<div class="z_user_list border10" id="menu_item_id_1">
								<div class="user_list_cont padd10">
									<div class="cont_fl">
										<img src="/public/static/images/mlist.png"/>
										<span>我的订单</span>
									</div>
									<div class="cont_fr">
										<span>
											查看全部
										</span>
										<i class="zMright"></i>
									</div>
								</div>
								<div class="z_user_nav">
									<ul>
										<li>
											<a href="">
												<img src="/public/static/images/q1.png" />
												<p>待付款</p>
											</a>
										</li>
										<li>
											<a href="">
												<img src="/public/static/images/q2.png" />
												<p>待收货</p>
											</a>
										</li>
										<li>
											<a href="">
												<img src="/public/static/images/q3.png" />
												<p>待评价</p>
											</a>
										</li>
										<li>
											<a href="">
												<img src="/public/static/images/q4.png" />
												<p>售后服务</p>
											</a>
										</li>
									</ul>
								</div>
							</div>
							<div id="menu_div">

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<form id="menu">
			<div class="z_user_right cont_fl">
				<div class="user_right_color" style="display: none;">
					<p class="cont_fl">颜色选择：</p>
					<div id="cp2" class="input-group colorpicker-component cont_fl" title="Using input value">
					  <input type="hidden" name="header_background" id="textNavBgcolor" value="<?php echo (isset($tpshop_config['basic_header_background']) && ($tpshop_config['basic_header_background'] !== '')?$tpshop_config['basic_header_background']:'#DD0F20'); ?>"/>
					  <!--<span class="input-group-addon"><i></i></span>-->
						<span class="h-slc">
							<i class="igac" style="background-color: #f53f3f" data-color="f53f3f"></i>
						</span>
						<span class="h-slc">
							<i class="igac" style="background-color: #de3ec2" data-color="de3ec2"></i>
						</span>
						<span class="h-slc">
							<i class="igac" style="background-color: #f77339" data-color="f77339"></i>
						</span>
						<span class="h-slc">
							<i class="igac" style="background-color: #eab22c" data-color="eab22c"></i>
						</span>
						<span class="h-slc">
							<i class="igac" style="background-color: #2dc265" data-color="2dc265"></i>
						</span>
						<span class="h-slc">
							<i class="igac" style="background-color: #28d2b0" data-color="28d2b0"></i>
						</span>
						<span class="h-slc">
							<i class="igac" style="background-color: #22a1dc" data-color="22a1dc"></i>
						</span>
						<span class="h-slc">
							<i class="igac" style="background-color: #3c5be2" data-color="3c5be2"></i>
						</span>
						<span class="h-slc">
							<i class="igac" style="background-color: #a530cb" data-color="a530cb"></i>
						</span>
					</div>

				</div>
				<div class="user_right_list">
					<ul>
						<?php if(is_array($menu_list) || $menu_list instanceof \think\Collection || $menu_list instanceof \think\Paginator): $i = 0; $__LIST__ = $menu_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?>
							<li data-menu-id="<?php echo $menu['menu_id']; ?>">
								<input name="menu[<?php echo $key; ?>][menu_id]" type="hidden" value="<?php echo $menu['menu_id']; ?>">
								<input name="menu[<?php echo $key; ?>][is_show]" type="hidden" value="<?php echo $menu['is_show']; ?>">
								<input name="menu[<?php echo $key; ?>][is_tab]" type="hidden" value="<?php echo $menu['is_tab']; ?>">
								<div class="right_list_btn cont_fl">
									<div class="right_list_name cont_fl">
										<?php echo $menu['default_name']; ?>
									</div>
									<div class="right_list_kg cont_fl <?php if($menu['is_show'] == 1): ?>right_click<?php endif; ?>">
										<div class="right_list_clase">
										</div>
									</div>
								</div>
								<div class="right_list_title cont_fl">
									<span>标题</span>
									<input type="text" name="menu[<?php echo $key; ?>][menu_name]" value="<?php echo $menu['menu_name']; ?>">
								</div>
								<div class="right_list_qk  cont_fl">
									<div class="right_list_name cont_fl">
										切块
									</div>
									<div class="right_list_kg cont_fl <?php if($menu['is_tab'] == 1): ?>right_click<?php endif; ?>">
										<div class="right_list_clase">

										</div>
									</div>
								</div>
							</li>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</div>
				<a href="JavaScript:void(0);" id="submit" class="ncap-btn-big ncap-btn-green" style="height: 36px;">立即更新</a>
			</div>
		</form>
		<div class="clboth"></div>
	</div>
	</body>
	<script src="/public/bootstrap/js/bootstrap-colorpicker.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			$('#cp2, #cp3a, #cp3b').colorpicker({'preferredFormat': 'hex'});
			init_menu();
		});
		$(document).on("click","#submit",function  () {
			$.ajax({
				type: "post",
				data: $('#menu').serialize(),
				dataType: 'json',
				url: "<?php echo U('System/mp_center_menu_save'); ?>",
				success: function (data) {
					if (data.status == 1) {
						location.reload();
					} else {
						layer.msg(data.msg, {icon: 2, time: 2000});
					}
				}
			})
		})

		$(document).on("click",".right_list_btn .right_list_kg",function  () {
			if ($(this).hasClass("right_click")) {
				$(this).removeClass("right_click");
				$(this).parents("li").find("input[name$='[is_show]']").val(0);
			}else{
				$(this).addClass("right_click");
				$(this).parents("li").find("input[name$='[is_show]']").val(1);
			}
			init_menu();
		})
		$(document).on("click",".right_list_qk .right_list_kg",function  () {
			if ($(this).hasClass("right_click")) {
				$(this).removeClass("right_click");
				$(this).parents("li").find("input[name$='[is_tab]']").val(0);
			}else{
				$(this).addClass("right_click");
				$(this).parents("li").find("input[name$='[is_tab]']").val(1);
			}
			init_menu();
		})
		function init_menu()
		{
			var menu_list = $('.user_right_list ul').find('li');
			// console.log(menu_list);
			var html = '';
			$.each(menu_list, function (index, item) {
				var menu_id = parseInt($(item).find("input[name$='[menu_id]']").val());
				var is_show = parseInt($(item).find("input[name$='[is_show]']").val());
				var is_tab = parseInt($(item).find("input[name$='[is_tab]']").val());
				var menu_name = $(item).find("input[name$='[menu_name]']").val();
				// switch(menu_id)
				// {
				// 	case 1:
				// 		if(is_show){
				// 			$('#menu_item_id_'+menu_id).show();
				// 		}else{
				// 			$('#menu_item_id_'+menu_id).hide();
				// 		}
				// 		if(is_tab){
				// 			$('#menu_item_id_'+menu_id).addClass("border10");
				// 		}else{
				// 			$('#menu_item_id_'+menu_id).removeClass("border10");
				// 		}
				// 		break;
				// 	default:
							var tab_html = '';
							if(is_tab){
								tab_html = 'border10';
							}
							if(is_show){
								html += '<div class="z_user_list '+tab_html+'" id="menu_item_id_'+menu_id+'"> <div class="user_list_cont padd10"> <div class="cont_fl"> ' +
										'<img src="/public/static/images/w'+menu_id+'.png"/><span>'+menu_name+'</span> </div> <div class="cont_fr"> ' +
										'<i class="zMright"></i> </div> </div> </div>';
							}
				// }
			});
			$('#menu_div').empty().html(html);
		}
		// var colordata = document.querySelector('#textNavBgcolor');
		// colordata.onchange = function(e) {
		//     var bgcolor= this.value;
		//     $('.z_user_head').css("background-color",bgcolor);
		// }
		$('#cp2').on('click','.h-slc',function () {
		    var bgc = '#'+$(this).children('.igac').attr('data-color');
            $(this).addClass('b-red').siblings().removeClass('b-red')
			$('#textNavBgcolor').val(bgc)
            $('.z_user_head').css("background-color",$('#textNavBgcolor').val());
        })
        $('.z_user_head').css("background-color",$('#textNavBgcolor').val());
		$('#cp2').children('.h-slc').each(function () {
            var bgc = '#'+$(this).children('.igac').attr('data-color');
            if(bgc == $('#textNavBgcolor').val()){
                $(this).addClass('b-red');
			}
        })
	</script>
</html>
