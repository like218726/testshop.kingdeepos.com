<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:45:"./application/admin/view/block/form_list.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<script type="text/javascript" src="/public/static/js/layer/laydate/laydate.js"></script>
<body style="background-color: rgb(255, 255, 255); overflow: auto; cursor: default; -moz-user-select: inherit;">
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>智能表单管理</h3>
                <h5>用户提交表单的管理</h5>
            </div>
        </div>
    </div>
    <!-- 操作说明 -->
    <div id="explanation" class="explanation">
        <div class="bckopa-tips">
            <div class="title">
                <img src="/public/static/images/handd.png" alt="">
                <h4 title="提示相关设置操作时应注意的要点">操作提示</h4>
            </div>
            <ul>
                <li>智能表单管理, 可以根据手机号，提交时间查询.</li>
            </ul>
        </div>
        <span title="收起提示" id="explanationZoom" style="display: block;"></span>
    </div>
    <div class="flexigrid">
        <div class="mDiv">
            <div class="ftitle">
                <h3>表单列表</h3>
                <h5>(共<span id="user_count">0</span>条记录)</h5>
            </div>
            <a href="" class="refresh-date"><div title="刷新数据" class="pReload"><i class="fa fa-refresh"></i></div></a>
            <form class="navbar-form form-inline" method="post"  id="search-form2">
                <input type="hidden" name="order_by" value="form_id">
                <input type="hidden" name="sort" value="desc">
                <input type="hidden" name="tpl_timeid" value="<?php echo $tpl_timeid; ?>" id="tpl_timeid">
                <div class="sDiv">

                    <div class="sDiv2">
                        <select class="select sDiv3" style="border:none;"  onchange="select_form_config(this)">
                            <option value="0">请选择表单名称</option>
                            <?php if(is_array($form_config) || $form_config instanceof \think\Collection || $form_config instanceof \think\Paginator): $i = 0; $__LIST__ = $form_config;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                                <option value="<?php echo $v['tpl_timeid']; ?>" <?php if($tpl_timeid == $v['tpl_timeid']): ?>selected<?php endif; ?>><?php echo $v['form_name']; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </div>
                    <div class="sDiv2">
                        <input type="text" size="30" id="start_time" name="start_time" value="<?php echo $start_time; ?>" class="qsbox"  placeholder="开始时间">
                    </div>
                    <div class="sDiv2">
                        <input type="text" size="30" id="end_time" name="end_time" value="<?php echo $end_time; ?>" class="qsbox"  placeholder="结束时间">
                    </div>


                    <div class="sDiv2">
                        <input type="text" name="mobile" id="mobile" size="30" class="qsbox" placeholder="请输入手机号码"
                               value="">
                        <input type="button" class="btn" onclick="ajax_get_table('search-form2',1)" value="搜索">
                    </div>
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
                        <th align="left" abbr="form_id" axis="col3" class="">
                            <div style="text-align: center; width: 40px;" class="">ID</div>
                        </th>
                        <!--<th align="left" abbr="mobile" axis="col4" class="">
                            <div style="text-align: center; width: 120px;" class="">手机号</div>
                        </th>-->
                        <?php if(is_array($name_list) || $name_list instanceof \think\Collection || $name_list instanceof \think\Paginator): $i = 0; $__LIST__ = $name_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>

                            <th align="center" axis="col5" class="">
                                <div style="text-align: center; width: 100px;" class=""><?php echo $v['title']; ?></div>
                            </th>

                        <?php endforeach; endif; else: echo "" ;endif; ?>

                        <th align="center" abbr="submit_time" axis="col6" class="">
                            <div style="text-align: center; width: 120px;" class="">提交日期</div>
                        </th>
                        <th align="center" axis="col1" class="handle">
                            <div style="text-align: center; width: 150px;">操作</div>
                        </th>
                        <th style="width:100%" axis="col7">
                            <div></div>
                        </th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="bDiv" style="height: auto;" id="ajax_return">
        </div>
    </div>
</div>
<script>
    function select_form_config(obj){
        var v = $(obj).val();
        if(v!=0){
            location.href = '/index.php/Admin/Block/form_list?tpl_timeid='+v;
        }
    }
    $(document).ready(function(){
        $('#start_time').layDate();
        $('#end_time').layDate();

        // 点击刷新数据
        var ssort = 'sdesc';
        var on_sclick = 0;
        $('.hDivBox > table>thead>tr>th').hover(
                function () {
                    if(typeof($(this).attr('abbr')) == "undefined"){
                        return false;
                    }
                    $(this).addClass('thOver');
                    if($(this).hasClass('sorted')){
                        if(ssort == 'sdesc'){
                            $(this).find('div').removeClass('sdesc');
                            $(this).find('div').addClass('sasc');
                        }else{
                            $(this).find('div').removeClass('sasc');
                            $(this).find('div').addClass('sdesc');
                        }
                    }else{
                        $(this).find('div').addClass(ssort);
                    }
                }, function () {
                    if(typeof($(this).attr('abbr')) == "undefined"){
                        return false;
                    }
                    if(on_sclick == 0){
                        if($(this).hasClass('sorted')){
                            if(ssort == 'sdesc'){
                                $(this).find('div').removeClass('sasc');
                                $(this).find('div').addClass('sdesc');
                            }else{
                                $(this).find('div').removeClass('sdesc');
                                $(this).find('div').addClass('sasc');
                            }
                        }else{
                            $(this).find('div').removeClass(ssort);
                        }
                    }
                    $(this).removeClass("thOver");
                    on_sclick = 0;
                }
        );
        $('.hDivBox > table>thead>tr>th').click(function(){
            if(typeof($(this).attr('abbr')) == "undefined"){
                return false;
            }
            if($(this).hasClass('sorted')){
                $(this).find('div').removeClass(ssort);
                if(ssort == 'sdesc'){
                    ssort = 'sasc';
                }else{
                    ssort = 'sdesc';
                }
                $(this).find('div').addClass(ssort);
                on_sclick = 1;
            }else{
                $('.hDivBox > table>thead>tr>th').removeClass('sorted');
                $('.hDivBox > table>thead>tr>th').find('div').removeClass(ssort);
                $(this).addClass('sorted');
                $(this).find('div').addClass(ssort);
                var hDivBox_th_index = $(this).index();
                var flexigrid_tr =   $('#flexigrid > table>tbody>tr')
                flexigrid_tr.each(function(){
                    $(this).find('td').removeClass('sorted');
                    $(this).children('td').eq(hDivBox_th_index).addClass('sorted');
                });
            }
            sort($(this).attr('abbr'));
        });

        $('.fa-refresh').click(function(){
            location.href = location.href;
        });
        ajax_get_table('search-form2',1);

    });
    //选中全部
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

    // ajax 抓取页面
    function ajax_get_table(tab,page){

        cur_page = page; //当前页面 保存为全局变量
        $.ajax({
            type : "POST",
            url:"/index.php/Admin/Block/ajax_form_list/p/"+page,//+tab,
            data : $('#'+tab).serialize(),// 你的formid
            success: function(data){
                $("#ajax_return").html('');
                $("#ajax_return").append(data);
            }
        });
    }



    // 点击排序
    function sort(field)
    {
        $("input[name='order_by']").val(field);
        var v = $("input[name='sort']").val() == 'desc' ? 'asc' : 'desc';
        $("input[name='sort']").val(v);
        ajax_get_table('search-form2',cur_page);
    }
    /**
     * 回调函数
     */
    function call_back(v) {
        layer.closeAll();
        if (v == 1) {
            layer.msg('发送成功',{icon:1});
        } else {
            layer.msg('发送失败',{icon:2});
        }
    }

    function exportUser() {
        $('input[name="user_ids"]').val('');
        $('#search-form2').attr('action',"<?php echo U('User/export_user'); ?>")
        var selected_ids = '';
        $('.trSelected' , '#flexigrid').each(function(i){
            selected_ids += $(this).data('id')+',';
        });
        if(selected_ids != ''){
            $('input[name="user_ids"]').val(selected_ids.substring(0,selected_ids.length-1));
        }
        $('#search-form2').submit();
    }
</script>
<style>
    .atxt,.flexigrid .bDiv a{
        color: #555;
        text-decoration:none;
    }
</style>
</body>
</html>