<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:52:"./application/seller/new/goods/ajax_spec_select.html";i:1587634376;}*/ ?>
<?php
    function in_arr($k,$arr){
        if(in_array($k,$arr)){
            return 'ok';
        }
        return 'no';
    }
    function checkbox_checked($k,$arr){
        if(in_array($k,$arr)){
            return 'checked';
        }
        return '';
    }
?>
<style>
    .ys-btn-close {
   
    }
    .add-pro-box {
        height: 35px;
        margin-right: 60px;
    }
    .upload_image {
        position: absolute;
        right: -46px;
        top:-8px;
    }
</style>
<?php if(\think\Request::instance()->param('root_goods_id') == 0): ?>
	<table class="table table-bordered" id="goods_spec_table1">
		<tr>
			<td colspan="2"><b>商品规格:</b></td>
		</tr>
		<?php if(is_array($specList) || $specList instanceof \think\Collection || $specList instanceof \think\Paginator): if( count($specList)==0 ) : echo "" ;else: foreach($specList as $k=>$vo): ?>
			<tr>
				<td style="line-height: 35px;"><?php echo $vo[name]; ?>:</td>
				<td>
					<div>
						<?php if(is_array($vo[spec_item]) || $vo[spec_item] instanceof \think\Collection || $vo[spec_item] instanceof \think\Paginator): if( count($vo[spec_item])==0 ) : echo "" ;else: foreach($vo[spec_item] as $k2=>$vo2): ?>

							<div class="add-pro-box box-<?php echo in_arr($k2,$items_ids); ?> box-<?php echo in_arr($k2,$items_ids); ?>-<?php echo $k; ?>" >
								<input type="checkbox" <?php echo checkbox_checked($k2,$items_ids); ?> class="in-<?php echo in_arr($k2,$items_ids); ?>" data-spec_item="<?php echo $vo2; ?>"  data-spec_id='<?php echo $vo[id]; ?>' data-item_id='<?php echo $k2; ?>' data-k="<?php echo $k; ?>"
									   style="width: 12px;height: 12px;margin-top: -3px;-webkit-appearance: none;border: none;outline: none;background: url('/public/images/noselect.png') no-repeat;margin-right: 5px"><?php echo $vo2; ?>
								<div class="ys-btn-close ys-btn-close_name" data-item-id="<?php echo $k2; ?>" >×</div>



								<span data-img_id="<?php echo $k2; ?>" is_upload_image="<?php echo $vo['is_upload_image']; ?>" class="upload_image">
                        <?php if($vo['is_upload_image'] == 1): ?>
                            <img width="35" height="35" src="<?php echo (isset($specImageList[$k2]) && ($specImageList[$k2] !== '')?$specImageList[$k2]:'/public/images/add-button.jpg'); ?>" id="item_img_<?php echo $k2; ?>" onclick="GetUploadify0('<?php echo $k2; ?>');"/>
                            <?php if($specImageList[$k2]): ?>
                                 <div style="right:-16px;top:9px" class="ys-btn-close ys-btn-close_img" onclick="deleteItemImage('<?php echo $k2; ?>');">×</div>
                            <?php endif; ?>
                            <input type="hidden" name="item_img[<?php echo $k2; ?>]" value="<?php echo $specImageList[$k2]; ?>" />
                        <?php endif; ?>
                    </span>

							</div>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</div>
					<div class="add-pro-box2">
						<input type="text" maxlength="20" data-spec_id="<?php echo $vo[id]; ?>" name="spec_item" placeholder="规格值名称" class="form-control" style="width:80px;vertical-align: middle;display: initial;">
						&nbsp;&nbsp;
						<a href="javascript:void(0);"  onclick="addSpecItem(this)">添加规格</a>
						<a href="javascript:void(0);"  onclick="show_or_hide(this)" data-f="0" class="show_or_hide" data-spec_id='<?php echo $vo[id]; ?>' data-k="<?php echo $k; ?>" style="margin-left: 15px;display: none;">显示规格</a>
					</div>
				</td>
			</tr>
		<?php endforeach; endif; else: echo "" ;endif; ?>
	</table>
	<?php else: ?>
	<table class="table table-bordered" id="goods_spec_table1">
		<tr>
			<td colspan="2"><b>商品规格:</b></td>
		</tr>
		<?php if(is_array($specList) || $specList instanceof \think\Collection || $specList instanceof \think\Paginator): if( count($specList)==0 ) : echo "" ;else: foreach($specList as $k=>$vo): ?>
			<tr>
				<td style="line-height: 35px;"><?php echo $vo[name]; ?>:</td>
				<td>
					<div>
						<?php if(is_array($vo[spec_item]) || $vo[spec_item] instanceof \think\Collection || $vo[spec_item] instanceof \think\Paginator): if( count($vo[spec_item])==0 ) : echo "" ;else: foreach($vo[spec_item] as $k2=>$vo2): ?>

							<div class="add-pro-box box-<?php echo in_arr($k2,$items_ids); ?> box-<?php echo in_arr($k2,$items_ids); ?>-<?php echo $k; ?>" >
								<input type="checkbox" <?php echo checkbox_checked($k2,$items_ids); ?> class="in-<?php echo in_arr($k2,$items_ids); ?>" data-spec_item="<?php echo $vo2; ?>"  data-spec_id='<?php echo $vo[id]; ?>' data-item_id='<?php echo $k2; ?>' data-k="<?php echo $k; ?>"
									   style="width: 12px;height: 12px;margin-top: -3px;-webkit-appearance: none;border: none;outline: none;background: url('/public/images/noselect.png') no-repeat;margin-right: 5px"><?php echo $vo2; ?>


								<span data-img_id="<?php echo $k2; ?>" is_upload_image="<?php echo $vo['is_upload_image']; ?>" class="upload_image">
                        <?php if($vo['is_upload_image'] == 1): ?>
                            <img width="35" height="35" src="<?php echo (isset($specImageList[$k2]) && ($specImageList[$k2] !== '')?$specImageList[$k2]:'/public/images/add-button.jpg'); ?>" id="item_img_<?php echo $k2; ?>" onclick="GetUploadify0('<?php echo $k2; ?>');"/>
                            <?php if($specImageList[$k2]): ?>
                                 <div style="right:-16px;top:9px" class="ys-btn-close ys-btn-close_img" onclick="deleteItemImage('<?php echo $k2; ?>');">×</div>
                            <?php endif; ?>
                            <input type="hidden" name="item_img[<?php echo $k2; ?>]" value="<?php echo $specImageList[$k2]; ?>" />
                        <?php endif; ?>
                    </span>

							</div>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</div>
					<div class="add-pro-box2">
						<a href="javascript:void(0);"  onclick="show_or_hide(this)" data-f="0" class="show_or_hide" data-spec_id='<?php echo $vo[id]; ?>' data-k="<?php echo $k; ?>" style="margin-left: 15px;display: none;">显示规格</a>
					</div>
				</td>
			</tr>
		<?php endforeach; endif; else: echo "" ;endif; ?>
	</table>
<?php endif; ?>
<div id="goods_spec_table2"> <!--ajax 返回 规格对应的库存--> </div>

<script>
var root_goods_id = '<?php echo (\think\Request::instance()->param('root_goods_id') ?: 0); ?>';
    function  set_val(name,val){
        localStorage.setItem(name,val);
    }
    function get_val(name){
        return localStorage.getItem(name)
    }
    function show_or_hide(o){
        return;
        console.time('show_or_hide:')
        var fs = $(o).attr('data-spec_id');
        var f = $(o).attr('data-f');
        var k = $(o).attr('data-k');


        $("#goods_spec_table1  input[type=checkbox]").each(function () {
            if ($(this).prop('checked')) {
                $(this).parent().show();
            }else{
                if(k == $(this).attr('data-k')){
                    if(f==0){
                        $(this).parent().show();
                    }else{
                        $(this).parent().hide();
                    }
                }
            }
        });
        if(f==0){
            f=1
            //$(".box-no-"+k).show();
            $(o).html('隐藏规格')
            set_val(fs,1)
        }else{
            set_val(fs,0)
            f=0
            //$(".box-no-"+k).hide();
            $(o).html('显示规格')
        }
        $(o).attr('data-f',f);
        console.log('show_or_hide',2)
        console.timeEnd('show_or_hide:')
    }
    var fn = function (m) {
        console.log(m)
        if($(m).prop('checked')){
            $(m).css('backgroundImage',"url('/public/images/seclect.png')")
        }else {
            $(m).css('backgroundImage',"url('/public/images/noselect.png')")
        }
    }
    function text_blur(o){
        var val=$(o).val();
        if(isNaN(val)){
            $(o).val('0');
        }else{
            if(val<0){
                $(o).val('0');
            }else{
                val=Math.round(val*100)/100;
                $(o).val(val);
            }
        }
    }

	//删除规格值
	$(function () {
		$(document).on('click', '.spec_item_del', function () {
			$(this).parents('tr').remove();
		})

        $(document).on('click', '#batch-ok-btn', function () {
            var $_goodSpec=$('#goods_spec_table2');
            $_goodSpec.find('.batch-fill-text1').val($('#batch-fill-text1').val());
			$('#batch-fill-text2').length && $_goodSpec.find('.batch-fill-text2').val($('#batch-fill-text2').val());
            $_goodSpec.find('.batch-fill-text3').val($('#batch-fill-text3').val());
			$('#batch-fill-text4').length && $_goodSpec.find('.batch-fill-text4').val($('#batch-fill-text4').val());
        })


	})
    function show_init(){
        $(".add-pro-box").show();
        return;
        console.time('show_init:')
        console.log('show_init',1)
        $(".show_or_hide").each(function(){
            var o = this;
            var fs = $(o).attr('data-spec_id');
            var fsv = get_val(fs)
            if(fsv == 1 || fsv == null){
                set_val(fs,1)
                $(o).attr('data-f',0)
                show_or_hide(o)
            }else{
                $(o).attr('data-f',1)
                show_or_hide(o)
            }
        })
        console.log('show_init',2)
        console.timeEnd('show_init:')
    }
	$(function () {
		$(".ys-btn-close_name").click(function () {
			var spec_item_id = $(this).attr('data-item-id');
			var button = $('input[data-item_id='+spec_item_id+']');
			var spec_id = button.attr('data-spec_id');
			var spec_item = button.attr('data-spec_item');
			// console.log(spec_item);
            // console.log(spec_id);
			$.ajax({
				type: 'POST',
				data: {'spec_item': spec_item, 'spec_id': spec_id},
				dataType: 'json',
				url: "/index.php/Seller/Goods/delSpecItem",
				success: function (data) {
					if (data.status < 0) {
						layer.alert(data.msg, {icon: 2});
					} else {
						ajaxGetSpecAttr();
					}
				}
			});
		});
        //ajaxGetSpecInput(); // 初始化选中
        $(".box-no").hide();
	})
    // 添加规格 data-spec_item
	function addSpecItem(obj){
        console.time('addSpecItem:')
        console.log('addSpecItem',1)
		var spec_item = $(obj).siblings('input[name="spec_item"]').val();
		spec_item = $.trim(spec_item);
		var spec_id = $(obj).siblings('input[name="spec_item"]').data('spec_id');
		if(spec_item.length == 0)
		{
			layer.alert('请输入规格值', {icon: 2});  //alert('删除失败');
			return false;
		}
		
		$.ajax({
				type:'POST',
				data:{'spec_item':spec_item,'spec_id':spec_id},
				dataType:'json',
				url:"/index.php?m=Seller&c=Goods&a=addSpecItem",
				success:function(data){
					   if(data.status < 0)
					   {
						   layer.alert(data.msg, {icon: 2}); 
					   }else{
						   ajaxGetSpecAttr(spec_item);
					   }						   
				}
		});

	}

    // 上传规格图片
    function GetUploadify0(k){
        cur_item_id = k; //当前规格图片id 声明成全局 供后面回调函数调用
        GetUploadify3(1,'','goods','call_back3');
    }
    
    
    // 上传规格图片成功回调函数
    function call_back3(fileurl_tmp){
        $("#item_img_"+cur_item_id).attr('src',fileurl_tmp); //  修改图片的路径
        $("input[name='item_img["+cur_item_id+"]']").val(fileurl_tmp); // 输入框保存一下 方便提交
        var html = "<img width=\"35\" height=\"35\" src=\""+fileurl_tmp+"\" id=\"item_img_"+cur_item_id+"\" onclick=\"GetUploadify0('"+cur_item_id+"');\"/>\n" +
            "\n" +
            "                            <div style=\"right:-16px;top:9px\" class=\"ys-btn-close ys-btn-close_img\" onclick=\"deleteItemImage('"+cur_item_id+"');\"\">×</div>\n" +
            "\n" +
            "                        <input type=\"hidden\" name=\"item_img["+cur_item_id+"]\" value=\""+fileurl_tmp+"\" />";
        $("span[data-img_id="+cur_item_id+"]").html(html)
    }

    function deleteItemImage(cur_item_id){
        var html = " <img width=\"35\" height=\"35\" src=\"/public/images/add-button.jpg\" id=\"item_img_"+cur_item_id+"\" onclick=\"GetUploadify0('"+cur_item_id+"');\"/>\n" +
            "             <input type=\"hidden\" name=\"item_img["+cur_item_id+"]\" value=\"<?php echo $specImageList["+cur_item_id+"]; ?>\" />";
        $("span[data-img_id="+cur_item_id+"]").html(html)
    }
    
	if (root_goods_id == 0) {
   // 按钮切换 class
	$("#ajax_spec_data input[type=checkbox]").click(function () {
		var goods_id = $("input[name='goods_id']").val();
		// if (input[type=checkbox].hasClass('btn-success')) {
        //     input[type=checkbox].removeClass('btn-success');
        //     input[type=checkbox].addClass('btn-default');
		// }
		// else {
		// 	button.removeClass('btn-default');
		// 	button.addClass('btn-success');
		// }
		ajaxGetSpecInput();
	});
}
	

/**
*  点击商品规格处罚 下面输入框显示
*/
function ajaxGetSpecInput(spec_item) {
    if(spec_item){
        $('[data-spec_item="'+spec_item+'"]').parent().show();
        $('[data-spec_item="'+spec_item+'"]').prop('checked',true).css('backgroundImage',"url('/public/images/seclect.png')")
    }
	var spec_arr = {};// 用户选择的规格数组
	// 选中了哪些属性
	$("#goods_spec_table1  input[type=checkbox]").each(function () {
		console.log(1111)
		if ($(this).prop('checked')) {
			var spec_id = $(this).data('spec_id');
			var item_id = $(this).data('item_id');
			if (!spec_arr.hasOwnProperty(spec_id))
				spec_arr[spec_id] = [];
			spec_arr[spec_id].push(item_id);
            $(this).css('backgroundImage',"url('/public/images/seclect.png')")
		}else{
            $(this).css('backgroundImage',"url('/public/images/noselect.png')")
        }
	});
	if (spec_arr.length > 4) {
		layer.msg('商品至多不能超过4种规格',{icon:2});
		$(this).removeClass('btn-success');
		$(this).addClass('btn-default');
		return false;
	}

    console.log(spec_arr,'ajaxGetSpecInput')
	ajaxGetSpecInput2(spec_arr); // 显示下面的输入框

}

    $(function () {
if (root_goods_id == 0) {
        $(document).on("click", '.delete_item', function (e) {
            if($(this).text() == '无效'){
                $(this).parent().parent().find('input').attr('disabled','disabled');
                $(this).text('有效');
            }else{
                $(this).text('无效');
                $(this).parent().parent().find('input').removeAttr('disabled');
            }
        })
		}
    })
	
	
/**
* 根据用户选择的不同规格选项 
* 返回 不同的输入框选项
*/
function ajaxGetSpecInput2(spec_arr) {
    console.log('ajaxGetSpecInput2 1')
	var goods_id = $("input[name='goods_id']").val();
	$.ajax({
		type: 'POST',
		data: {spec_arr: spec_arr, goods_id: goods_id, purpose: <?php echo \think\Request::instance()->param('purpose'); ?>, root_goods_id: root_goods_id},
		url: "/index.php/Seller/Goods/ajaxGetSpecInput",
		dataType:'json',
		success: function (data) {
			if(data.status == 0){
				layer.alert(data.msg);
			}else{
				$("#goods_spec_table2").empty().html(data.result);
			}
			hbdyg();  // 合并单元格
            $("#spec_input_tab").find('tr').each(function (index, item) {
                var price = $(this).find("input[name$='[price]']").val();
                var store_count = $(this).find("input[name$='[store_count]']").val();
                if(store_count == 0 && price == 0){
                    //$(this).find(".delete_item").trigger('click');
                }
            });
            show_init();
            console.timeEnd('addSpecItem:')
		}
	});
}
	
 // 合并单元格
 function hbdyg() {
            var tab = document.getElementById("spec_input_tab"); //要合并的tableID
            var maxCol = 2, val, count, start;  //maxCol：合并单元格作用到多少列  
            if (tab != null) {
                for (var col = maxCol - 1; col >= 0; col--) {
                    count = 1;
                    val = "";
                    for (var i = 0; i < tab.rows.length; i++) {
                        if (val == tab.rows[i].cells[col].innerHTML) {
                            count++;
                        } else {
                            if (count > 1) { //合并
                                start = i - count;
                                tab.rows[start].cells[col].rowSpan = count;
                                for (var j = start + 1; j < i; j++) {
                                    tab.rows[j].cells[col].style.display = "none";
                                }
                                count = 1;
                            }
                            val = tab.rows[i].cells[col].innerHTML;
                        }
                    }
                    if (count > 1) { //合并，最后几行相同的情况下
                        start = i - count;
                        tab.rows[start].cells[col].rowSpan = count;
                        for (var j = start + 1; j < i; j++) {
                            tab.rows[j].cells[col].style.display = "none";
                        }
                    }
                }
            }
        }
</script> 