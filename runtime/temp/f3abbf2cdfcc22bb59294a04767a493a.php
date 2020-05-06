<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:45:"./application/admin/view/goods/goodsList.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
        <h3>商品管理</h3>
        <h5>商城所有商品索引及管理</h5>
      </div>
      <ul class="tab-base nc-row">
      <li><a class="current" href="javascript:;" data-state="" onclick="get_goods_list(this)"><span>所有商品</span></a></li>
      <li><a href="javascript:;" data-state="0" onclick="get_goods_list(this)"><span>等待审核</span></a></li>
      <li><a href="javascript:;" data-state="1" onclick="get_goods_list(this)"><span>审核通过</span></a></li>
      <li><a href="javascript:;" data-state="2" onclick="get_goods_list(this)"><span>审核失败</span></a></li>
      <li><a href="javascript:;" data-state="3" onclick="get_goods_list(this)"><span>违规下架</span></a></li>
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
      <li>平台可以强制下架商家违规的产品，对于商家发布的商品需要审核.</li>
      <li>上架，当商品处于非上架状态时，前台将不能浏览该商品，店主可控制商品上架状态</li>
      <li>违规下架，当商品处于违规下架状态时，前台将不能购买该商品，只有管理员可控制商品违规下架状态，并且商品只有重新编辑后才能上架</li>
      <li>商品状态为“待同意”时，表示供应商修改了源供应商品的必要供货数据（如成本价），需要销售商同意后，才能进入审核状态</li>
    </ul>
  </div>
  <div class="flexigrid">
    <div class="mDiv">
      <div class="ftitle">
        <h3>商品列表</h3>
        <h5></h5>
      </div>
	<form action="" id="search-form2" class="navbar-form form-inline" method="post" onsubmit="return false">
      <div class="sDiv">
        <div class="sDiv2">  
		  <select name="purpose" id="purpose" class="select">
            <option value="">商品用途</option>
                <option value="1">销售商品</option>
                <option value="2">供应商品</option>
          </select>
          <select name="cat_id" id="cat_id" class="select">
            <option value="">所有分类</option>
            <?php if(is_array($categoryList) || $categoryList instanceof \think\Collection || $categoryList instanceof \think\Paginator): if( count($categoryList)==0 ) : echo "" ;else: foreach($categoryList as $k=>$v): ?>
                <option value="<?php echo $v['id']; ?>"> <?php echo $v['name']; ?></option>
            <?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
          <select name="brand_id" id="brand_id" class="select">
            <option value="">所有品牌</option>
                <?php if(is_array($brandList) || $brandList instanceof \think\Collection || $brandList instanceof \think\Paginator): if( count($brandList)==0 ) : echo "" ;else: foreach($brandList as $k=>$v): ?>
                   <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
          <input type="hidden" name="goods_state" id="goods_state">
          <input type="hidden" name="is_on_sale" id="is_on_sale">
          <!--排序规则-->
          <input type="hidden" name="orderby1" value="goods_id" />
          <input type="hidden" name="orderby2" value="desc" />
          <input type="text" size="30" name="key_word" class="qsbox" placeholder="搜索词...">
          <input type="button" onclick="ajax_get_table('search-form2',1)" class="btn" value="搜索">
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
                <div style="text-align: left; width:65px;" class="" onclick="sort('goods_id');">商品id</div>
              </th>
              <th align="left" abbr="ac_id" axis="col4" class="">
                <div style="text-align: left; width: 300px;" class="" onclick="sort('goods_name');">商品名称</div>
              </th>
              <th align="center" abbr="article_show" axis="col6" class="">
                <div style="text-align: center; width: 100px;" class="" onclick="sort('goods_sn');">货号</div>
              </th>
              <th align="center" abbr="article_time" axis="col6" class="">
                <div style="text-align: center; width: 200px;" class="" onclick="sort('cat_id');">分类</div>
              </th>
              <th align="center" abbr="article_time" axis="col6" class="">
                <div style="text-align: center; width: 50px;" class="" onclick="sort('shop_price');">价格</div>
              </th>
			  <th align="center" abbr="article_time" axis="col6" class="">
                <div style="text-align: center; width: 50px;" class="">供货价</div>
              </th>
              <th align="center" abbr="article_time" axis="col6" class="">
                <div style="text-align: center; width: 50px;" class="">库存</div>
              </th>
                <th align="center" abbr="article_time" axis="col6" class="">
                    <div style="text-align: center; width: 100px;" onclick="sort('on_time');">上架时间</div>
                </th>
                <!--    <th align="center" abbr="article_time" axis="col6" class="">
                      <div style="text-align: center; width: 50px;" class="" onclick="sort('is_recommend');">推荐</div>
                    </th>
                    <th align="center" abbr="article_time" axis="col6" class="">
                      <div style="text-align: center; width: 50px;" class="" onclick="sort('is_new');">新品</div>
                    </th>
                    <th align="center" abbr="article_time" axis="col6" class="">
                      <div style="text-align: center; width: 50px;" class="" onclick="sort('is_hot');">热卖</div>
                    </th>  -->
              <th align="center" abbr="article_time" axis="col6" class="">
                <div style="text-align: center; width: 50px;" class="" onclick="sort('is_on_sale');">商品状态</div>
              </th>
              <th align="center" abbr="article_time" axis="col6" class="">
                <div style="text-align: center; width: 50px;" class="" onclick="sort('goods_state');">审核状态</div>
              </th>
			  <th align="center" abbr="article_time" axis="col6" class="">
                <div style="text-align: center; width: 50px;" class="">用途</div>
              </th>
              <th align="center" abbr="article_time" axis="col6" class="">
                <div style="text-align: center; width: 100px;" class="">店铺名称</div>
              </th>  
              <th align="center" abbr="article_time" axis="col6" class="">
                <div style="text-align: center; width: 50px;" class="">店铺类型</div>
              </th>                                        
              <th align="center" abbr="article_time" axis="col6" class="">
                <div style="text-align: center; width: 120px;">操作</div>
              </th>
              <th style="width:100%" axis="col7">
                <div></div>
              </th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
    <div class="tDiv">
        <div class="tDiv2">
            <div class="fbutton">
                <a href="<?php echo U('Admin/Goods/initGoodsSearchWord'); ?>">
                    <div class="add" title="初始化商品搜索关键词">
                        <span><i class="fa fa-plus"></i>初始化商品搜索关键词</span>
                    </div>
                </a>
            </div>
            <div class="fbutton">
                <a href="<?php echo U('Admin/Goods/initGoodsImg'); ?>">
                    <div class="add" title="刷新上架商品的缩略图">
                        <span><i class="fa fa-plus2"></i>刷新上架商品缩略图</span>
                    </div>
                </a>
            </div>

        </div>
      <div style="clear:both"></div>
    </div>
    <div class="bDiv" style="height: auto;">
     <!--ajax 返回 --> 
      <div id="ajax_return" cellpadding="0" cellspacing="0" border="0"></div>      
    </div>

     </div>
</div>
<script>
    $(document).ready(function(){	
	
		 // 表格行点击选中切换
		$('#ajax_return').on('click','table>tbody >tr',function(){
            if(!$(this).hasClass('spe_select')){
                $(this).toggleClass('trSelected');

                var checked = $(this).hasClass('trSelected');
                $(this).find('input[type="checkbox"]').attr('checked',checked);
            }
		});
		// 刷选条件 鼠标 移动进去 移出 样式
		$(".hDivBox > table > thead > tr > th").mousemove(function(){
			$(this).addClass('thOver');
		}).mouseout(function(){
			$(this).removeClass('thOver');
		});
		
		// 复选框事件 不联动父类
		$('#ajax_return').on('click','table>tbody >tr input',function(e){			
			//alert('bbb');
			e.stopPropagation();
		})		
		
	});
</script>
<script>
    $(document).ready(function () {
        // ajax 加载商品列表
        ajax_get_table('search-form2', 1);

    });

    // ajax 抓取页面 form 为表单id  page 为当前第几页
    function ajax_get_table(form, page) {
        cur_page = page; //当前页面 保存为全局变量
        $.ajax({
            type: "POST",
            url: "/index.php?m=Admin&c=goods&a=ajaxGoodsList&p=" + page,//+tab,
            data: $('#' + form).serialize(),// 你的formid
            success: function (data) {
                $("#ajax_return").html('');
                $("#ajax_return").append(data);
            }
        });
    }

    // 点击排序
    function sort(field) {
        $("input[name='orderby1']").val(field);
        var v = $("input[name='orderby2']").val() == 'desc' ? 'asc' : 'desc';
        $("input[name='orderby2']").val(v);
        ajax_get_table('search-form2', cur_page);
    }

    // 删除操作
    function del(id) {
        layer.confirm('确定要删除吗？', {
                    btn: ['确定','取消'] //按钮
                }, function(){
                    // 确定
                    layer.closeAll();
                    $.ajax({
                        url: "/index.php?m=Admin&c=goods&a=delGoods&id=" + id,
                        success: function (v) {
                            var v = eval('(' + v + ')');
                            if (v.hasOwnProperty('status') && (v.status == 1))
                                ajax_get_table('search-form2', cur_page);
                            else
                                layer.msg(v.msg, {icon: 2, time: 1000}); //alert(v.msg);
                        }
                    });
                }, function(index){
                    layer.close(index);
                }
        );
    }

    //获取选中商品id
    function get_select_goods_id_str() {
        if ($('input[name="goods_id\[\]"]:checked').length == 0)
            return false;
        var goods_arr = Array();
        $('input[name="goods_id\[\]"]:checked').each(function () {
            goods_arr.push($(this).val());
        });
        var goods_id_str = goods_arr.join(',');
        return goods_id_str
    }

    act = '';//操作变量
    //批量操作
    function fuc_change(obj) {
        var fuc_val = $(obj).children('option:selected').val();
        if (fuc_val == 0) {
            //推荐
            act = 'recommend';
            $('#act_button').removeClass('disabled');
            reset_state();
        } else if (fuc_val == 1) {
            act = 'new';
            $('#act_button').removeClass('disabled');
            reset_state();
            //新品
        } else if (fuc_val == 2) {
            act = 'hot';
            $('#act_button').removeClass('disabled');
            reset_state();
            //热卖
        } else if (fuc_val == 3) {
            act = 'examine';
            $('#state_id').show();
            $('#act_button').addClass('disabled');
            $("#state_id option:first").prop("selected", 'selected');
            //审核商品
        } else {
            act = '';
            $('#act_button').addClass('disabled');
            reset_state();
            //恢复默认
        }
    }

    //重置审核操作
    function reset_state() {
        $("#state_id option:first").prop("selected", 'selected');
        $('#state_id').hide();
    }

    //审核操作
    function state_change(obj) {
        var state_val = $(obj).children('option:selected').val();
        if (state_val == '') {
            $('#act_button').addClass('disabled');
        } else {
            $('#act_button').removeClass('disabled');
        }
    }

    //批量操作提交
    function act_submit() {
        var ids = get_select_goods_id_str();
        if (ids == false) {
            layer.alert('请勾选要操作的商品', {icon: 2});
            return;
        }
        var fun_id = $('#func_id').find("option:selected").val();
        var goods_state = $('#state_id').children('option:selected').val();
        if(fun_id == 3 && goods_state == 2){
            layer.prompt({title: '请输入操作备注(<b style="color:red;">必填</b>)', formType: 2}, function(text, index){
        		layer.close(index);
        		request_net(ids, text);
            });
        }else{
            request_net(ids , '无备注');
        }
    }
    
    function request_net(ids , text){
    	if(text == ""){
    		layer.alert('请填写备注', {icon: 2,time: 3000});
    	}else {
    		var goods_state = $('#state_id').children('option:selected').val();
            $.ajax({
                type: "POST",
                url: "/index.php?m=Admin&c=goods&a=act",//+tab,
                data: {act: act,goods_state:goods_state,goods_ids: ids, reason: text},
                dataType: 'json',
                success: function (data) {
                    if(data.status == 1){
                        layer.alert(data.msg, {
                            icon: 1,
                            closeBtn: 0
                        }, function(){
                            window.location.reload();
                        });
                    }else{
                        layer.alert(data.msg, {icon: 2,time: 3000});
                    }

                },
                error:function(){
                    layer.alert('网络异常', {icon: 2,time: 3000});
                }
            });
        }
    }

    function get_goods_list(obj){
    	 var state = $(obj).attr('data-state');
    	 $('.tab-base').find('a').removeClass('current')
    	 $(obj).addClass('current');
    	 if(state == '3') {
    		 $("#is_on_sale").val(2);
    		 $("#goods_state").val('');
    	 }else{
    		 $("#is_on_sale").val('');
    		 $("#goods_state").val(state);
    	 }
    	 ajax_get_table('search-form2', 1);
    }

    function takeoff(obj){
    	var reasonhtml = '<div class="dialog_body" style="position: relative;">';
    	reasonhtml += '<div class="dialog_content" style="margin: 0px; padding: 0px;">';
    	reasonhtml += '<div class="ncap-form-default">';
    	reasonhtml += '<dl class="row">';
    	reasonhtml += '<dt class="tit">违规商品货号</dt><dd class="opt">'+$(obj).attr('goods_sn')+'</dd></dl>';
    	reasonhtml += '<dl class="row">';
    	reasonhtml += '<dt class="tit">违规商品名称</dt><dd class="opt">'+$(obj).attr('goods_name')+'</dd></dl>';
 		reasonhtml += '<dl class="row"><dt class="tit">';
	    reasonhtml += '<label for="close_reason">违规下架理由</label>';
	    reasonhtml += '<input type="hidden" id="take_goods_id" value="'+$(obj).attr('goods_id')+'"></dt>';
	    reasonhtml += '<dd class="opt">';
	    reasonhtml += ' <textarea rows="6" class="tarea" cols="60" name="close_reason" id="close_reason"></textarea>';
	    reasonhtml += ' </dd></dl>';
	    reasonhtml += '<div class="bot"><a href="javascript:void(0);" onclick="takeoff_goods();" class="ncap-btn-big ncap-btn-green" nctype="btn_submit">确认提交</a></div>';
	    reasonhtml += '</div></div></div>'
    	layer.open({
	   		  type: 1,
	   		  title:'违规下架理由',
	   		  skin: 'layui-layer-rim', //加上边框
	   		  area: ['620px', '340px'], //宽高
	   		  content: reasonhtml
    	});
    }
    
    function takeoff_goods(){
          $.ajax({
              type: "POST",
              url: "/index.php?m=Admin&c=goods&a=act",//+tab,
              data: {act:'takeoff',is_on_sale:2,goods_ids:$('#take_goods_id').val(),reason:$('#close_reason').val()},
              dataType: 'json',
              success: function (data) {
                  if(data.status == 1){
                      layer.alert(data.msg, {
                          icon: 1, closeBtn: 0
                      }, function(){
                          window.location.reload();
                      });
                  }else{
                      layer.alert(data.msg, {icon: 2,time: 3000});
                  }
              },
              error:function(){
                  layer.alert('网络异常', {icon: 2,time: 3000});
              }
          }); 	
    }
</script>
</body>
</html>