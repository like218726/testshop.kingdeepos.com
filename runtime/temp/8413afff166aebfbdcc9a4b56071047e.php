<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:50:"./application/admin/view/promotion\index_list.html";i:1588218725;s:72:"D:\www\testshop.kingdeepos.com\application\admin\view\public\layout.html";i:1588218727;}*/ ?>
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
<body style="background-color: rgb(255, 255, 255); overflow: auto; cursor: default; -moz-user-select: inherit;">
<style>
	.menu{
		overflow: hidden;
		margin-bottom: 52px;
	}
	.menu .content .title{
		font-size: 16px;
		color: #1e1e1e;
		border-left: 3px solid #148eff;
		padding-left: 12px;
	}
	.menu .content p span{
		font-size:12px;
		color: #999999;
		margin-left: 16px;
	}
	.menu .content ul li{
		width: 244px;
		height: 48px;
		background-color: #f7f6f6;
		border-radius: 3px;
		float: left;
		margin-right: 27px;
		margin-top: 23px;
		padding: 18px;
		position: relative;
	}
	.menu .content ul img{
		width: 50px;
		height: 50px;
	}
	.menu .content ul p{
		position: absolute;
		color: #1e1e1e;
		font-size: 16px;
		top: 20px;
		left: 84px;
	}
	.menu .content ul span{
		position: absolute;
		color: #999;
		font-size: 12px;
		top: 50px;
		left: 84px;
	}
</style>
<div class="marketing_menu page">
	<div class="menu">
		<div class="content">
			<p class="title">常用促销<span>吸粉、老客带新客，提高下单转化率</span></p>
			<ul>
				<a href="<?php echo U('Promotion/flash_sale'); ?>">
					<li>
						<img src="/public/static/images/purchase.png" alt="">
						<p>抢购/秒杀</p>
						<span>快速抢购引导顾客更多消费</span>
					</li>
				</a>
				<a href="<?php echo U('Promotion/group_buy_list'); ?>">
					<li>
						<img src="/public/static/images/group.png" alt="">
						<p>团购</p>
						<span>批量促销清理库存"</span>
					</li>
				</a>
				<a href="<?php echo U('Promotion/prom_goods_list'); ?>">
					<li>
						<img src="/public/static/images/preferential.png" alt="">
						<p>优惠促销</p>
						<span>对商品本身进行的促销方式</span>
					</li>
				</a>
				<a href="<?php echo U('Promotion/prom_order_list'); ?>">
					<li>
						<img src="/public/static/images/promotion.png" alt="">
						<p>订单促销</p>
						<span>对用户订单进行优惠的促销方式</span>
					</li>
				</a>
				<a href="<?php echo U('PreSell/index'); ?>">
					<li>
						<img src="/public/static/images/presell.png" alt="">
						<p>预售</p>
						<span>新品销售预估和预热</span>
					</li>
				</a>
				<!--单商家有，多商家没有，先隐藏-->
				<!--<a href="<?php echo U('Combination/index'); ?>">-->
					<!--<li>-->
						<!--<img src="/public/static/images/match_purchase.png" alt="">-->
						<!--<p>搭配购</p>-->
						<!--<span>捆绑销售，以货带货</span>-->
					<!--</li>-->
				<!--</a>-->
				<a href="<?php echo U('Promotion/bargain_list'); ?>">
					<li>
						<img src="/public/static/images/bargaining.png" alt="">
						<p>砍价</p>
						<span>拉新引流邀请好友低价购买</span>
					</li>
				</a>
			</ul>
		</div>
	</div>

	<div class="menu">
		<div class="content">
			<p class="title">拼团购<span>最火爆的引流、拉新和促销的方式</span></p>
			<ul>
				<a href="<?php echo U('Team/index'); ?>">
					<li>
						<img src="/public/static/images/share.png" alt="">
						<p>分享团</p>
						<span>拼团基础版，引流传播利器</span>
					</li>
				</a>
				<a href="<?php echo U('Team/index'); ?>">
					<li>
						<img src="/public/static/images/commission_delegation.png" alt="">
						<p>佣金团</p>
						<span>团长赚拼团佣金，积极性更高</span>
					</li>
				</a>
				<a href="<?php echo U('Team/index'); ?>">
					<li>
						<img src="/public/static/images/lucky_draw.png" alt="">
						<p>抽奖团</p>
						<span>设置不同奖品达到趣味营销效果</span>
					</li>
				</a>
			</ul>
		</div>
	</div>
	<div class="menu">
		<div class="content">
			<p class="title">优惠劵<span>最经典和通俗易懂的营销手段</span></p>
			<ul>
				<a href="<?php echo U('Coupon/index'); ?>">
					<li>
						<img src="/public/static/images/coupons.png" alt="">
						<p>优惠劵</p>
						<span>不同类型优惠劵针对性营销</span>
					</li>
				</a>
				<a href="<?php echo U('Coupon/noob'); ?>">
					<li>
						<img src="/public/static/images/coupons.png" alt="">
						<p>新人优惠券</p>
						<span>新人专属优惠劵针对性营销</span>
					</li>
				</a>
				<!--单商家有，多商家没有，先隐藏-->
				<!--<a href="#">-->
					<!--<li>-->
						<!--<img src="/public/static/images/gifts.png" alt="">-->
						<!--<p>新人好礼</p>-->
						<!--<span>促进新用户下单和留存</span>-->
					<!--</li>-->
				<!--</a>-->
				<!--<a href="<?php echo U('Coupon/send_list'); ?>">-->
					<!--<li>-->
						<!--<img src="/public/static/images/record.png" alt="">-->
						<!--<p>发放记录</p>-->
						<!--<span>查看优惠劵发放使用记录</span>-->
					<!--</li>-->
				<!--</a>-->
			</ul>
		</div>
	</div>
	<div style="display: none" class="menu">
		<div class="content">
			<p class="title">互动营销<span>各种特色互动功能助力营销</span></p>
			<ul>
				<a href="#">
					<li>
						<img src="/public/static/images/golden_eggs.png" alt="">
						<p>砸金蛋</p>
						<span>砸开金蛋中惊喜大奖</span>
					</li>
				</a>
				<a href="#">
					<li>
						<img src="/public/static/images/cards.png" alt="">
						<p>猜单双</p>
						<span>用户通过选择单双数开启奖励</span>
					</li>
				</a>
				<a href="#">
					<li>
						<img src="/public/static/images/love.png" alt="">
						<p>爱心助力</p>
						<span>邀请好友、陌生人助力获得奖励</span>
					</li>
				</a>
				<a href="#">
					<li>
						<img src="/public/static/images/red_envelope.png" alt="">
						<p>拆红包</p>
						<span>邀请好友拆分红包，引流拉新</span>
					</li>
				</a>
				<a href="#">
					<li>
						<img src="/public/static/images/scraping.png" alt="">
						<p>刮刮卡（待开发）</p>
						<span>通俗易懂的互动营销方式</span>
					</li>
				</a>
				<a href="#">
					<li>
						<img src="/public/static/images/rotary.png" alt="">
						<p>大转盘抽奖（待开发）</p>
						<span>有趣好玩的大转盘抽奖</span>
					</li>
				</a>
			</ul>
		</div>
	</div>
</div>
<script>

</script>
</body>
</html>