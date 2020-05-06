<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:45:"./application/admin/view/system/shopping.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<style>
    .ncap-form-default dt.tit {
        min-width: 264px;
    }
</style>
<body style="background-color: #FFF; overflow: auto;">
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
            <span id="explanationZoom" title="收起提示"></span> </div>
        <ul>
            <li>网站全局基本设置，商城及其他模块相关内容在其各自栏目设置项内进行操作。</li>
        </ul>
    </div>
    <form method="post" enctype="multipart/form-data" name="form1" action="<?php echo U('System/handle'); ?>">
        <input type="hidden" name="form_submit" value="ok" />
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="point_rate">积分换算比例</label>
                </dt>
                <dd class="opt">
                    <input type="radio" id="point_rate" name="point_rate" value="1"  <?php if($config[point_rate] == 1): ?> checked <?php endif; ?> >1元 = 1积分  &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="point_rate" value="10" <?php if($config[point_rate] == 10): ?> checked <?php endif; ?> >1元 = 10积分  &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="point_rate" value="100"<?php if($config[point_rate] == 100): ?> checked <?php endif; ?> >1元 = 100积分
                    <p class="notic">积分换算比例</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>赠送积分比例</label>
                </dt>
                <dd class="opt">
                    <input pattern="^\d{1,}$" name="point_send_limit" value="<?php echo (isset($config['point_send_limit']) && ($config['point_send_limit'] !== '')?$config['point_send_limit']:'50'); ?>" class="input-txt" type="text">
                    <span class="err">%</span>
                    <p class="notic">发布商品, 赠送积分限制: 100表示不限制, 50时购买该商品赠送的积分所抵扣金额不能超过商品价格的50%</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>最低使用限制</label>
                </dt>
                <dd class="opt">
                    <input pattern="^\d{1,}$" name="point_min_limit" value="<?php echo (isset($config['point_min_limit']) && ($config['point_min_limit'] !== '')?$config['point_min_limit']:'0'); ?>" class="input-txt" type="text">
                    <p class="notic">购买商品, 使用积分时: 0表示不限制, 大于0时, 用户积分小于该值将不能使用积分</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>使用比例</label>
                </dt>
                <dd class="opt">
                    <input pattern="^\d{1,}$" name="point_use_percent" value="<?php echo (isset($config['point_use_percent']) && ($config['point_use_percent'] !== '')?$config['point_use_percent']:'50'); ?>" class="input-txt" type="text">
                    <span class="err">%</span>
                    <p class="notic">购买商品, 使用积分时: 100时不限制, 为0时不能使用积分, 50时积分抵扣金额不能超过该笔订单应付金额的50%</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="distribut_date">发货后多少天自动收货</label>
                </dt>
                <dd class="opt">
                    <select name="auto_confirm_date" id="distribut_date">
                        <?php $__FOR_START_376284728__=1;$__FOR_END_376284728__=31;for($i=$__FOR_START_376284728__;$i < $__FOR_END_376284728__;$i+=1){ ?>
                            <option value="<?php echo $i; ?>" <?php if($config[auto_confirm_date] == $i): ?>selected="selected"<?php endif; ?>><?php echo $i; ?>天</option>
                        <?php } ?>
                    </select>
                    <p class="notic">发货后多少天自动收货</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="auto_transfer_date">收货多少天后订单结算</label>
                </dt>
                <dd class="opt">
                    <select  name="auto_transfer_date" id="auto_transfer_date">
                        <?php $__FOR_START_1980069272__=1;$__FOR_END_1980069272__=31;for($i=$__FOR_START_1980069272__;$i < $__FOR_END_1980069272__;$i+=1){ ?>
                            <option value="<?php echo $i; ?>" <?php if($config[auto_transfer_date] == $i): ?>selected="selected"<?php endif; ?>><?php echo $i; ?>天</option>
                        <?php } ?>
                    </select>
                    <p class="notic">收货多少天后,订单结算金额转入卖家平台预存款</p>
                </dd>
            </dl>
            
            <dl class="row">
                <dt class="tit">
                    <label for="distribut_date">申请售后时间段</label>
                </dt>
                <dd class="opt">
                    <select name="auto_service_date" id="auto_service_date">
                        <?php $__FOR_START_1594139749__=1;$__FOR_END_1594139749__=31;for($i=$__FOR_START_1594139749__;$i < $__FOR_END_1594139749__;$i+=1){ ?>
                            <option value="<?php echo $i; ?>" <?php if($config[auto_service_date] == $i): ?>selected="selected"<?php endif; ?>><?php echo $i; ?>天</option>
                        <?php } ?>
                    </select>
                    <p class="notic">申请售后的时间段（交易完成多少天内），换货或维修</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">购买积分商品,是否必须使用积分</dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="integral_use_enable1" class="cb-enable <?php if($config[integral_use_enable] == 1): ?>selected<?php endif; ?>">是</label>
                        <label for="integral_use_enable0" class="cb-disable <?php if($config[integral_use_enable] == 0): ?>selected<?php endif; ?>">否</label>
                        <input id="integral_use_enable1" name="integral_use_enable" checked="checked" value="1" type="radio">
                        <input id="integral_use_enable0" name="integral_use_enable" value="0" type="radio">
                    </div>
                    <p class="notic">用户购买积分商品,结算方式是否必须使用积分,是为必须按照商品规定的积分兑换支付积分，否为可以不适用积分而使用金额购买商品</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="point_rate">减库存的时机</label>
                </dt>
                <dd class="opt">
                    <input type="radio" name="reduce" value="1" <?php if($config[reduce] != 2): ?> checked <?php endif; ?>>下单成功时  &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="reduce" value="2" <?php if($config[reduce] == 2): ?> checked <?php endif; ?>>支付成功时  &nbsp;&nbsp;&nbsp;&nbsp;
                    <p class="notic">减库存的时机</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>拼团下单后未支付多久时间后才能让后续的人下单</label>
                </dt>
                <dd class="opt">
                    <input pattern="^\d{1,}$" name="team_order_limit_time" value="<?php echo (isset($config['team_order_limit_time']) && ($config['team_order_limit_time'] !== '')?$config['team_order_limit_time']:'1800'); ?>" class="input-txt" type="text">秒
                    <span class="err">%</span>
                    <!--<p class="notic">秒</p>-->
                </dd>
            </dl>
            <div class="bot" style="padding-left: 17.5%">
                <input type="hidden" name="inc_type" value="<?php echo $inc_type; ?>">
                <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()">确认提交</a>
            </div>
        </div>
    </form>
</div>
<div id="goTop"> <a href="JavaScript:void(0);" id="btntop"><i class="fa fa-angle-up"></i></a><a href="JavaScript:void(0);" id="btnbottom"><i class="fa fa-angle-down"></i></a></div>
</body>
</html>