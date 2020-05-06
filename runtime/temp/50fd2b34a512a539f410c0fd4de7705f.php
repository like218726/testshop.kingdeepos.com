<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:46:"./application/admin/view/goods/_goodsType.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<body style="background-color: #FFF; overflow: auto;">
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="javascript:history.back();" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
      <div class="subject">
        <h3>模型管理 - 添加修改模型</h3>
        <h5>添加修改模型</h5>
      </div>
    </div>
  </div>
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="提示相关设置操作时应注意的要点">操作提示</h4>
      <span id="explanationZoom" title="收起提示"></span>
    </div>
    <ul>
      <li>商品模型是用来规定某一类商品共有规格和属性的集合，其中规格会影响商品价格，同一个商品不同的规格价格会不同，而属性仅仅是商品的属性特质展示</li>
      <li>商品模型用于管理某一类商品的规格、属性</li>
      <li>发布某一个商品时，选中商品分类,如果该商品分类绑定了某个商品模型,那么该商品就有这个模型的所有规格和属性</li>
    </ul>
  </div>
	<form id="addEditGoodsTypeForm">
    <div class="ncap-form-default">
<!--商品类型-->
      <dl class="row">
        <dt class="tit">
          <label for="t_mane"><em>*</em>模型名称:</label>
        </dt>
        <dd class="opt">
            <input type="text" value="<?php echo $goodsType['name']; ?>" name="name" class="input-txt" style="width:300px;"/>
            <span class="err" id="err_name" style="color:#F00; display:none;"></span>
            <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit" colspan="2">
          <label class="" for="s_sort"><em>*</em>绑定分类</label>
        </dt>
        <dd class="opt">

          <div id="gcategory">
              <div data-index="1">
                  <select  id="cat_index1_id1" onchange="get_category2(this.value,'cat_index1_id2','0',1);" class="class-select valid">
                      <option value="0">请选择商品分类</option>
                      <?php if(is_array($category_list) || $category_list instanceof \think\Collection || $category_list instanceof \think\Paginator): if( count($category_list)==0 ) : echo "" ;else: foreach($category_list as $k=>$v): ?>
                          <option value="<?php echo $v['id']; ?>">
                              <?php echo $v['name']; ?>
                          </option>
                      <?php endforeach; endif; else: echo "" ;endif; ?>
                  </select>
                  <select id="cat_index1_id2" onchange="get_category2(this.value,'cat_index1_id3','0');"  class="class-select valid">
                      <option value="0">请选择商品分类</option>
                  </select>
                  <select name="category_id[]" id="cat_index1_id3" class="class-select valid">
                      <option value="0">请选择商品分类</option>
                  </select>
                  <button type="button" onclick="delete_category(1)">解绑</button>
              </div>

          </div>

            <a id="add_category" class="ncap-btn" href="JavaScript:void(0);" onclick="add_category();"><i class="fa fa-plus"></i>新增绑定分类</a>
            <span class="err"  id="err_category_id" style="color:#F00; display:none;"></span>
          <!--<p class="notic"><strong style="color:orange;">因为模型可能比较多,所以进行归类标识, 仅仅用于编辑商品分类绑定模型时,快速搜索用.</strong></p>-->
        </dd>

      </dl>


<!--商品类型 end-->
<!--关联规格-->
<dl class="row">
        <dt class="tit">
          <label>规格设置</label>
        </dt>
        <dd class="opt">
          <div>
              <!-- 规格列表s-->
              <div class="flexigrid">
                  <div class="hDiv">
                      <div class="hDivBox">
                          <table cellpadding="0" cellspacing="0">
                              <thead>
                              <tr>
                                  <th axis="col3">
                                      <div style="text-align: center; width: 100px;">规格名称</div>
                                  </th>
                                  <th align="center" axis="col2">
                                      <div style="text-align: center; width: 60px;">排序</div>
                                  </th>
                                  <th align="center" axis="col5">
                                      <div style="text-align: center; width: 100px;">是否可上传规格图</div>
                                  </th>
                                  <th align="center" class="handle-s" axis="col1">
                                      <div style="text-align: center; width: 60px;">操作</div>
                                  </th>
                                  <th axis="col6">
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
                              <div class="add" title="新增规格" id="add_spec"><span><i class="fa fa-plus"></i>新增规格</span></div>
                          </div>
                      </div>
                      <div style="clear:both"></div>
                  </div>
                  <div class="bDiv tdDivs" style="height: auto;margin-bottom:20px;min-height:100px;">
                      <table class="table-bordered" cellpadding="0" cellspacing="0">
                          <tbody id="spec_list">
                          <?php if(is_array($specs) || $specs instanceof \think\Collection || $specs instanceof \think\Paginator): $spec_key = 0; $__LIST__ = $specs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods_type_spec): $mod = ($spec_key % 2 );++$spec_key;?>
                              <tr data-index="0">
                                  <input type="hidden" class="spec_id" name="spec_id[]" value="<?php echo $goods_type_spec['id']; ?>" />
                                  <td>
                                      <div style="width: 100px;"><input type="text" class="w80" name="spec_name[]" value="<?php echo $goods_type_spec['name']; ?>"></div>
                                  </td>
                                  <td>
                                      <div style="text-align: center; width: 60px;"><input type="text" name="spec_order[]" value="<?php echo $goods_type_spec['order']; ?>" class="w40" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')"></div>
                                  </td>
                                  <td>
                                      <div style="text-align: center; width: 100px;">
                                          <input type="hidden" name="spec_is_upload_image[]" value="<?php echo $goods_type_spec['is_upload_image']; ?>">
                                          <?php if($goods_type_spec['is_upload_image'] == 1): ?>
                                              <span class="yes is_upload_image"><i class="fa fa-check-circle"></i>是</span>
                                              <?php else: ?>
                                              <span class="no is_upload_image"><i class="fa fa-ban"></i>否</span>
                                          <?php endif; ?>
                                      </div>
                                  </td>
                                  <td>
                                  <div style="text-align: left; width: 575px;display: inline-block;padding-right: 35px;"> <a href="javascript:void(0);" class="btn red delete_spec" data-name="<?php echo $goods_type_spec['name']; ?>" data-id="<?php echo $goods_type_spec['id']; ?>"><i class="fa fa-trash-o"></i>删除</a></div>
                              </td>
                                  <td style="width: 100%;">
                                      <div>&nbsp;</div>
                                  </td>
                              </tr>
                          <?php endforeach; endif; else: echo "" ;endif; ?>
                          </tbody>
                      </table>
                  </div>
              </div>
              <span class="err"  id="err_spec_name" style="color:#F00; display:none;"></span>
              <!-- 规格列表e-->
          </div>
        </dd>

      </dl>
<!--关联规格 end-->
<div id="attr">
        <!--添加属性-->
        <dl class="row">
            <dt class="tit">添加属性</dt>
            <dd class="opt">
                <ul class="ncap-ajax-add" id="ul_attr">
                    <?php if(is_array($attributeList) || $attributeList instanceof \think\Collection || $attributeList instanceof \think\Paginator): if( count($attributeList)==0 ) : echo "" ;else: foreach($attributeList as $k=>$v): ?>
                        <li>
                            <input type="hidden" name="attr_input_type[]" value="1">
                            <input type="text" name="attr_id[]" value="<?php echo $v['attr_id']; ?>"  class="form-control" style="display:none;"/>
                            <label title="排序,最大值为999"><input type="text" value="<?php echo $v['order']; ?>"  name="order[]" class="w30" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/g,'')"></label>
                            <label><input type="text" class="w150" name="attr_name[]" value="<?php echo $v['attr_name']; ?>" placeholder="输入属性名称" /></label>
                            <label><input type="text" class="w300" name="attr_values[]" value="<?php echo $v['attr_values']; ?>" placeholder="输入属性可选值"></label>
                            <label>显示&nbsp;<input type="checkbox" name="attr_index[]" value="<?php echo $v['attr_index']; ?>" onclick="this.value=(this.value==0)?1:0" <?php if($v['attr_index'] == 1): ?>checked="checked"<?php endif; ?> /></label>
                            <label><a class="ncap-btn ncap-btn-red del_attr" href="JavaScript:void(0);">移除</a></label>
                        </li>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
                <a id="add_type" class="ncap-btn" href="JavaScript:void(0);" onclick="add_attribute(this);"><i class="fa fa-plus"></i>添加一个属性</a>
                <p class="notic">多个属性值需要用英文逗号","隔开,商家发布商品是即可下拉选择属性值</p>
                <span class="err"  id="err_attr_values" style="color:#F00; display:none;"></span>
                <span class="err"  id="err_attr_name" style="color:#F00; display:none;"></span>
            </dd>
        </dl>

        <!--添加属性 end-->
        <dl class="row">
            <dt class="tit">自定义属性</dt>
            <dd class="opt">
                <ul class="ncap-ajax-add" id="ul_attr">
                    <?php if(is_array($customerAttributeList) || $customerAttributeList instanceof \think\Collection || $customerAttributeList instanceof \think\Paginator): if( count($customerAttributeList)==0 ) : echo "" ;else: foreach($customerAttributeList as $k=>$v): ?>
                        <li>
                            <input type="hidden" name="attr_input_type[]" value="2">
                            <input type="text" style="display:none;" class="form-control" value="<?php echo $v['attr_id']; ?>" name="attr_id[]">
                            <label title="排序,最大值为999"><input type="text" value="<?php echo $v['order']; ?>" name="order[]" class="w30" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/g,'')"></label>
                            <label><input type="text" placeholder="输入属性名称" value="<?php echo $v['attr_name']; ?>" name="attr_name[]" class="w150"></label>
                            <label><input type="text" disabled="disabled" placeholder="" value="<?php echo $v['attr_values']; ?>" name="attr_values[]" class="w300"></label>
                            <label>显示&nbsp;<input type="checkbox" name="attr_index[]" value="<?php echo $v['attr_index']; ?>" onclick="this.value=(this.value==0)?1:0"  <?php if($v['attr_index'] == 1): ?>checked="checked"<?php endif; ?>></label>
                            <label><a href="JavaScript:void(0);" class="ncap-btn ncap-btn-red del_attr">移除</a></label>
                        </li>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
                <a id="add_type" class="ncap-btn" href="JavaScript:void(0);" onclick="add_customer_attribute(this);"><i class="fa fa-plus"></i>添加自定义属性</a>
                <p class="notic">商家发布商品时可自定义输入属性, 此属性不参与关键词搜索</p>
            </dd>
        </dl>
    </div>
<!--关联品牌-->
<dl class="row">
        <dt class="tit">
          <label>搜索品牌</label>
        </dt>
        <dd class="opt">
          <div>
              <select name="brand_cat_id1" id="brand_cat_id1" onchange="get_category2(this.value,'brand_cat_id2','0');brand_scroll(this);" class="class-select valid">
                <option value="0">请选择商品分类</option>
                     <?php if(is_array($category_list) || $category_list instanceof \think\Collection || $category_list instanceof \think\Paginator): if( count($category_list)==0 ) : echo "" ;else: foreach($category_list as $k=>$v): ?>
                       <option value="<?php echo $v['id']; ?>">
                            <?php echo $v['name']; ?>
                       </option>
                     <?php endforeach; endif; else: echo "" ;endif; ?>
              </select>
              <!--<select name="brand_cat_id2" id="brand_cat_id2" onchange="get_category(this.value,'brand_cat_id3','0');" class="class-select valid">-->
                <!--<option value="0">请选择商品分类</option>-->
              <!--</select>-->
              <!--<select name="brand_cat_id3" id="brand_cat_id3" class="form-control" class="class-select valid">-->
                <!--<option value="0">请选择商品分类</option>-->
              <!--</select>-->
              <p class="notic"><strong style="color:orange;">此处选择分类仅仅方便筛选以下品牌, 此分类与模型没有关联关系</strong> </p>
           </div>
          <div class="scrollbar-box ps-container ps-active-y">
            <div class="ncap-type-spec-list" id="ajax_brandList" class="ajax_bradnlist" style="height:160px;overflow: auto;"></div>
          </div>
        </dd>
      </dl>
<!--关联品牌 end-->



      <div class="bot"><a id="submitBtn" class="ncap-btn-big ncap-btn-green" href="JavaScript:void(0);" >确认提交</a></div>
    </div>
	 <input id="goods_type_id" type="hidden" name="id" value="<?php echo $goodsType['id']; ?>">
  </form>

<!--添加属性模板-->
<ul id="attribute_html" style="display:none;">
    <li>
        <input type="hidden" name="attr_input_type[]" value="1">
         <input type="text" style="display:none;" class="form-control" value="" name="attr_id[]">
         <label title="排序,最大值为999"><input type="text" value="0" name="order[]" class="w30" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/g,'')"></label>
         <label><input type="text" placeholder="输入属性名称" value="" name="attr_name[]" class="w150"></label>
         <label><input type="text" placeholder="输入属性可选值" value="" name="attr_values[]" class="w300"></label>
         <label>显示&nbsp;<input type="checkbox" name="attr_index[]" value="1" onclick="this.value=(this.value==0)?1:0"  checked="checked"></label>
         <label><a href="JavaScript:void(0);" class="ncap-btn ncap-btn-red del_attr">移除</a></label>
     </li>
</ul>
<!--添加属性模板end -->

<!--添加自定义属性模板-->
<ul id="custom_attribute_html" style="display:none;">
    <li>
    	 <input type="hidden" name="attr_input_type[]" value="2">
         <input type="text" style="display:none;" class="form-control" value="" name="attr_id[]">
         <label title="排序,最大值为999"><input type="text" value="0" name="order[]" class="w30" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/g,'')"></label>
         <label><input type="text" placeholder="输入属性名称" value="" name="attr_name[]" class="w150"></label>
         <label><input type="text" disabled="disabled" placeholder="" value="" name="attr_values[]" class="w300"></label>
         <label>显示&nbsp;<input type="checkbox" name="attr_index[]" value="1" onclick="this.value=(this.value==0)?1:0"  checked="checked"></label>
         <label><a href="JavaScript:void(0);" class="ncap-btn ncap-btn-red del_attr">移除</a></label>
     </li>
</ul>
<!--添加自定义属性模板end -->

</div>
<script>
    //保留checkbox数据完整性


    //提交
    $(function(){
        $(document).on("click",'#submitBtn',function(){
            var t1 = document.getElementById("attr").getElementsByTagName("input");
            for(i=0;i<t1.length;i++)
            {
                if(t1[i].type == "checkbox")
                {
                    if(!(t1[i].checked))
                    {
                        t1[i].checked = true;
                        t1[i].value = "0";
                    }
                }
            }
            $('#submitBtn').attr('disabled', true);
            $(".err").text('');
            $.ajax({
                type: "POST",
                url: "<?php echo url('Admin/GoodsType/edit'); ?>",
                data: $("#addEditGoodsTypeForm").serialize(),
                async:false,
                dataType: "json",
                error: function () {
                    layer.alert("服务器繁忙, 请联系管理员!");
                },
                success: function (data) {
                    if (data.status == 1) {
                        layer.msg(data.msg,{icon: 1,time: 2000},function(){
                            location.href = "<?php echo url('Goods/goodsTypeList'); ?>";
                        });
                    } else {
                        $('#submitBtn').attr('disabled',false);
                        $.each(data.result, function (index, item) {
                            $('span.err').show();
                            $('#err_'+index).text(item);
                        });
                        layer.msg(data.msg, {icon: 2,time: 3000});
                    }
                }
            });
        })
    })


// 将品牌滚动条里面的 对应分类移动到 最上面
//javascript:document.getElementById('category_id_3').scrollIntoView();
var brandScroll = 0;
function brand_scroll(o){
	var id = $(o).val();
	//if(!$('#category_id_'+id).is('h5')){
	//	return false;
	//}
	$('#ajax_brandList').scrollTop(-brandScroll);
	var sp_top = $('#category_id_'+id).offset().top; // 标题自身往上的 top
	var div_top = $('#ajax_brandList').offset().top; // div 自身往上的top
	$('#ajax_brandList').scrollTop(sp_top-div_top); // div 移动
	brandScroll = sp_top-div_top;
}

 // 将规格滚动条里面的 对应分类移动到 最上面
//javascript:document.getElementById('category_id_3').scrollIntoView();
var specScroll = 0;
function spec_scroll(o){
	var id = $(o).val();

	//if(!$('#categoryId'+id).is('h5')){
		//return false;
	//}
	$('#ajax_specList').scrollTop(-specScroll);
	var sp_top = $('#categoryId'+id).offset().top; // 标题自身往上的 top
	var div_top = $('#ajax_specList').offset().top; // div 自身往上的top
	$('#ajax_specList').scrollTop(sp_top-div_top); // div 移动
	specScroll = sp_top-div_top;
}


// 判断输入框是否为空
function checkgoodsTypeName(){
	var name = $("#addEditGoodsTypeForm").find("input[name='name']").val();
    if($.trim(name) == '')
	{
		$("#err_name").show();
		return false;
	}
	return true;
}

/** 以下是编辑时默认选中某个商品分类*/
$(document).ready(function(){
	getBrandList(0,0); // 默认查出所有品牌

});


/**
*获取筛选规格 查找某个分类下的所有Spec
* v 是分类id  l 是分类等级 比如 1级分类 2级分类 等
*/
function getSpecList(v,l)
{
	$.ajax({
		type : "GET",
		url:"/index.php?m=Admin&c=Goods&a=getSpecByCat&cat_id="+v+"&l="+l+"&type_id=<?php echo $goodsType[id]; ?>",//+tab,
		success: function(data)
		{
		   $("#ajax_specList").html('').append(data);
		}
	});
}

/**
*获取筛选品牌 查找某个分类下的所有品牌
* v 是分类id  l 是分类等级 比如 1级分类 2级分类 等
*/
function getBrandList(v,l)
{
		$.ajax({
			type : "GET",
			url:"/index.php?m=Admin&c=Goods&a=getBrandByCat&cat_id="+v+"&l="+l+"&type_id=<?php echo $goodsType[id]; ?>",//+tab,
			success: function(data)
			{
			   $("#ajax_brandList").html('').append(data);
			}
		});
}

// 添加一行属性
function add_attribute(obj)
{
  var attribute_html = $('#attribute_html').html();
  $(obj).siblings('ul').append(attribute_html);
}

//添加一行属性
function add_customer_attribute(obj)
{
  var custom_attribute_html = $('#custom_attribute_html').html();
  $(obj).siblings('ul').append(custom_attribute_html);
}

// 删除一个 属性
$(document).on("click",".del_attr",function(){
	var _this = this;
    layer.confirm('确定要删除吗？', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                //确定
                var attr_id = $(_this).parent().parent().find("input[name='attr_id\[\]']").val();
                $(_this).parent().parent().remove();
                layer.closeAll();
                if (attr_id == '')
                    return false;
                $.ajax({
                    type: "GET",
                    url: "/index.php?m=Admin&c=Goods&a=delGoodsAttribute&id=" + attr_id,
                    success: function (data) {
                    }
                });
            }, function (index) {
                layer.close(index);
            }
    );

});

$(function () {
    //将分类数据填充
    var html = "";
    if (eval(<?php echo json_encode($category_array); ?>)) {
        $("#gcategory").empty();
        $.each(eval(<?php echo json_encode($category_array); ?>),function (i,item) {
            html = "";
            html += "<div data-index=\""+i+"\">\n" ;
            html += "<select  id=\"cat_index"+i+"_id1\" onchange=\"get_category2(this.value,'cat_index"+i+"_id2','0',1);\" class=\"class-select valid\">\n" +
                "        <option value=\"0\">请选择商品分类</option>\n" ;
            $.each(eval(<?php echo json_encode($category_list); ?>),function (i2,item2) {
                if(item2.id == item.c1.id){
                    html += "<option value=\""+item2.id+"\" selected='selected'>" +item2.name+ "</option>" ;
                }else{
                    html += "<option value=\""+item2.id+"\">" +item2.name+ "</option>" ;
                }
            })
            html += "                  </select>\n" +
                "                  <select id=\"cat_index"+i+"_id2\" onchange=\"get_category2(this.value,'cat_index"+i+"_id3','0');\"  class=\"class-select valid\">\n" +
                "                      <option value=\"0\">请选择商品分类</option>\n" +
                "                  </select>\n" +
                "                  <select name=\"category_id[]\" id=\"cat_index"+i+"_id3\" class=\"class-select valid\">\n" +
                "                      <option value=\"0\">请选择商品分类</option>\n" +
                "                  </select>\n" +
                "                  <span id=\"err_cat_id"+ i +"\" style=\"color:#F00; display:none;\"></span><button type='button' data-category_id='"+ item.c3.id +"' onclick=\"delete_category("+ i +",this)\">解绑</button>\n" +
                "              </div>";
            $("#gcategory").append(html);
            get_category2(item.c1.id,"cat_index"+i+"_id2",item.c2.id);
            get_category2(item.c2.id,"cat_index"+i+"_id3",item.c3.id);
        }
    )
    }else{

    }



})

function add_category() {
    var category_length = $("#gcategory").children('div').length + 1;
    var html = "<div data-index=\""+category_length+"\">\n" +
        "                  <select  id=\"cat_index"+category_length+"_id1\" onchange=\"get_category2(this.value,'cat_index"+category_length+"_id2','0',1);\" class=\"class-select valid\">\n" +
        "                      <option value=\"0\">请选择商品分类</option>\n" ;

    $.each(eval(<?php echo json_encode($category_list); ?>),function (i,item) {
        html += "                          <option value=\""+item.id+"\">\n" +
            "                              "+item.name+"\n" +
            "                          </option>\n" ;
    })

    html += "                  </select>\n" +
        "                  <select id=\"cat_index"+category_length+"_id2\" onchange=\"get_category2(this.value,'cat_index"+category_length+"_id3','0');\"  class=\"class-select valid\">\n" +
        "                      <option value=\"0\">请选择商品分类</option>\n" +
        "                  </select>\n" +
        "                  <select name=\"category_id[]\" id=\"cat_index"+category_length+"_id3\" class=\"class-select valid\">\n" +
        "                      <option value=\"0\">请选择商品分类</option>\n" +
        "                  </select>\n" +
        "                  <span id=\"err_cat_id\" style=\"color:#F00; display:none;\"></span><button type='button' onclick=\"delete_category("+ category_length +")\">解绑</button>\n" +
        "              </div>";
    $("#gcategory").append(html);
}

function delete_category(i,obj=0){
    if (obj != 0) {
        var category_id = $(obj).data('category_id');
        $.ajax({
            type: "POST",
            url: '/index.php?m=Admin&c=GoodsType&a=canDeleteCategory',
            data: {category_id:category_id},
            dataType: "json",
            fail:function(){
                layer.open({icon: 2, content: '服务器繁忙'});
                return false;
            },
            success: function (data) {
                if (data.status == 0) {
                    layer.open({icon: 2, content: data.msg});
                    return false;
                }else{
                    $("div[data-index="+i+"]").remove();
                }
            }
        });
    }else{
        $("div[data-index="+i+"]").remove();
    }

}

//添加规格
$(document).on('click', '#add_spec', function () {
    var spec_list = $('#spec_list');
    var spec_length = spec_list.find('tr').length;
    if(spec_length >= 4){
        layer.open({icon: 2, content: '规格最多可添加4个'});
        return;
    }
    var spec_item_div = '<tr data-index='+spec_length+'> <td> <div style="width: 100px;"><input type="text" class="w80" name="spec_name[]" value=""></div> </td> ' +
        '<td> <div style="text-align: center; width: 60px;">' +
        '<input type="text" name="spec_order[]" value="" class="w40" onKeyUp="this.value=this.value.replace(/[^\\d.]/g,\'\')"></div> </td> ' +
        '<td> <div style="text-align: center; width: 100px;"> <input type="hidden" name="spec_is_upload_image[]" value="">' +
        '<span class="is_upload_image no"><i class="fa fa-ban"></i>否</span></div> </td> ' +
        '<td> <div style="text-align: left; width: 575px;display: inline-block;padding-right: 35px;"> ' +
        '<a href="javascript:void(0);" class="btn red delete_spec"><i class="fa fa-trash-o"></i>删除</a></div> </td></tr>';
    spec_list.append(spec_item_div);
});
//删除规格
$(document).on('click', '.delete_spec', function () {
    var obj = $(this);
    if (obj.data('id') > 0) {
        layer.open({
            content: '确认删除已存在的规格吗？'
            ,btn: ['确定', '取消']
            ,yes: function(index, layero){
                layer.close(index);
                $.ajax({
                    type: "POST",
                    url: '/index.php?m=Admin&c=GoodsType&a=deleteSpec',
                    data: {id: obj.data('id'),type_id:$("#goods_type_id").val()},
                    dataType: "json",
                    success: function (data) {
                        if (data.status == 1) {
                            obj.parent().parent().parent().remove();
                        } else {
                            layer.open({icon: 2, content: data.msg});
                        }
                    }
                });
            }
            ,btn2: function(index, layero){
                layer.close(index);
            }
            ,cancel: function(){
                //右上角关闭回调
                layer.close();
            }
        });
    } else {
        obj.parent().parent().parent().remove();
    }
});
//是否上传规格图
$(document).on('click', '.is_upload_image', function () {
    if($(this).hasClass('no')){
        $('.is_upload_image').each(function(i,o){
            $(o).removeClass('yes').addClass('no').html("<i class='fa fa-ban'></i>否");
            $(o).parent().find('input').val(0);
        })
        $(this).removeClass('no').addClass('yes').html("<i class='fa fa-check-circle'></i>是");
        $(this).parent().find('input').val(1);
    }else{
        $(this).removeClass('yes').addClass('no').html("<i class='fa fa-ban'></i>否");
        $(this).parent().find('input').val(0);
    }
});
function get_category2(id,next,select_id,k=0) {
    console.log(k)
    var url = '/index.php?m=Home&c=api&a=get_category&parent_id='+ id;
    $.ajax({
        type : "GET",
        url  : url,
        error: function(request) {
            alert("服务器繁忙, 请联系管理员!");
            return;
        },
        success: function(v) {
            v = "<option value='0'>请选择商品分类</option>" + v;
            if (k == 1) {
            if (next.substr(-1, 1) == 2) {
                    var num = next.substr(-1, 1);
                    var nextnext = parseInt(next.substr(-1, 1))+1;
                    $("#"+next.slice(0,-1)+nextnext).empty().html(v);
                }
            }
            $('#'+next).empty().html(v);
            (select_id > 0) && $('#'+next).val(select_id);//默认选中
        }
    });
}
</script>
</body>
</html>