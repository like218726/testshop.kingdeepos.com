<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:43:"./application/admin/view/user/signList.html";i:1587634376;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<body style="background-color: rgb(255, 255, 255); overflow: auto; cursor: default; -moz-user-select: inherit;">
    <div id="append_parent"></div>
    <div id="ajaxwaitid"></div>
    <div class="page">
        <div class="fixed-bar">
            <div class="item-title">
                <div class="subject">
                    <h3>签到管理</h3>
                    <h5>会员签到及管理设置</h5>
                </div>
                <ul class="tab-base nc-row">
                    <li><a class="current" href="<?php echo U('user/signList'); ?>" data-state="" ><span>签到列表</span></a></li>
                    <li><a href="<?php echo U('user/signRule'); ?>" data-state="0"  ><span>签到规则</span></a></li>      
                </ul>
            </div>
        </div>
        <!-- 操作说明 -->
        <div id="explanation" class="explanation" style="color: rgb(44, 188, 163); background-color: rgb(237, 251, 248); width: 99%; height: 100%;">
            <div id="checkZoom" class="title"><i class="fa fa-lightbulb-o"></i>
                <h4 title="提示相关设置操作时应注意的要点">操作提示</h4>
                <span title="收起提示" id="explanationZoom" style="display: block;"></span>
            </div>
            <ul>
                <li>签到列表：签到人员记录，签到记录最多保持一个月</li>
                <li>签到规则：可设置功能开启关闭，签到积分和连续签到额外奖励</li>

            </ul>
        </div>
        <div class="flexigrid">
            <div class="mDiv">
                <div class="ftitle">
                    <h3>签到列表</h3>
                    <h5></h5>
                </div>
                <form action="" id="search-form2" class="navbar-form form-inline" method="post" onsubmit="return false">
                    <div class="sDiv">
                        <div class="sDiv2">          
                            <input type="text" size="30" name="mobile" class="qsbox" placeholder="搜索用户手机号...">
                            <input type="button" onclick="ajax_get_table('search-form2', 1)" class="btn" value="搜索">
                        </div>
                    </div>
                </form>
            </div>
            <div class="hDiv">
                <div class="hDivBox">
                    <table cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th align="left" abbr="article_title" axis="col6" class="">
                        <div style="text-align: left; width:50px;" class="" >签到id</div>
                        </th>
                        <th align="left" abbr="ac_id" axis="col4" class="">
                        <div style="text-align: left; width: 150px;" class="" >用户名称</div>
                        </th>
                        <th align="left" abbr="ac_id" axis="col4" class="">
                        <div style="text-align: left; width: 150px;" class="" >手机号码</div>
                        </th>
                        <th align="center" abbr="article_show" axis="col6" class="">
                        <div style="text-align: center; width: 100px;" class="" >累计签到天数</div>
                        </th>
                        <th align="center" abbr="article_time" axis="col6" class="">
                        <div style="text-align: center; width: 100px;" class="" >连续签到天数</div>
                        </th>
                        <th align="center" abbr="article_time" axis="col6" class="">
                        <div style="text-align: center; width: 100px;" class="">最后签到时间</div>
                        </th>              
                        <th align="center" abbr="article_time" axis="col6" class="">
                        <div style="text-align: center; width: 500px;" class="" >历史签到</div>
                        </th>  
                        <th align="center" abbr="article_time" axis="col6" class="">
                        <div style="text-align: center; width: 100px;" class="" >本月积分</div>
                        </th>     
                        <th align="center" abbr="article_time" axis="col6" class="">
                        <div style="text-align: center; width: 100px;" class="">累计签到积分</div>
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
                <!--ajax 返回 --> 
                <div id="ajax_return" cellpadding="0" cellspacing="0" border="0"></div>      
            </div>

        </div>
    </div>
    <script>
        $(document).ready(function() {

            // 表格行点击选中切换
            $('#ajax_return').on('click', 'table>tbody >tr', function() {
                if (!$(this).hasClass('spe_select')) {
                    $(this).toggleClass('trSelected');

                    var checked = $(this).hasClass('trSelected');
                    $(this).find('input[type="checkbox"]').attr('checked', checked);
                }
            });
            // 刷选条件 鼠标 移动进去 移出 样式
            $(".hDivBox > table > thead > tr > th").mousemove(function() {
                $(this).addClass('thOver');
            }).mouseout(function() {
                $(this).removeClass('thOver');
            });

            // 复选框事件 不联动父类
            $('#ajax_return').on('click', 'table>tbody >tr input', function(e) {
                //alert('bbb');
                e.stopPropagation();
            })

        });
    </script>
    <script>
        $(document).ready(function() {
            // ajax 加载签到列表
            ajax_get_table('search-form2', 1);

        });

       

        // ajax 抓取页面 form 为表单id  page 为当前第几页
        function ajax_get_table(form, page) {
            cur_page = page; //当前页面 保存为全局变量
            $.ajax({
                type: "POST",
                url: "/index.php?m=Admin&c=user&a=ajaxsignList&p=" + page, //+tab,
                data: $('#' + form).serialize(), // 你的formid
                success: function(data) {
                    $("#ajax_return").html('');
                    $("#ajax_return").append(data);
                }
            });
        }

        function get_sign_list(obj) {
            var url = $(obj).attr('data-state');
            $('.tab-base').find('a').removeClass('current')
            $(obj).addClass('current');
        }






    </script>
</body>
</html>