<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:49:"./application/admin/view/goods/getBrandByCat.html";i:1587634374;}*/ ?>
<?php if(is_array($goods_category_list) || $goods_category_list instanceof \think\Collection || $goods_category_list instanceof \think\Paginator): if( count($goods_category_list)==0 ) : echo "" ;else: foreach($goods_category_list as $k=>$v): ?>
  <dl>
    <dt style="display: block;" id="category_id_<?php echo $v[id]; ?>"><?php echo $v[name]; ?><input class="checkBrandAll" type="checkbox"></dt>
    <dd>
        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $k2=>$v2): if($v[id] == $v2['cat_id1']): ?>          
          <label style="display: inline-block;" for="brand_id_<?php echo $v2['id']; ?>">                      
	         <input type="checkbox" id="brand_id_<?php echo $v2['id']; ?>" class="brand_change_default_submit_value"  name="brand_id[]" value="<?php echo $v2['id']; ?>" <?php if(($v2['type_id'] != null) and ($type_id == $v2['type_id'])): ?> checked="checked"<?php endif; ?> />&nbsp;&nbsp;<?php echo $v2['name']; ?>             
          </label>
        <?php endif; endforeach; endif; else: echo "" ;endif; ?>
    </dd>
  </dl>
<?php endforeach; endif; else: echo "" ;endif; ?>
<script>
    $(function(){
        $(document).on("click",'.checkBrandAll',function(){
            if($(this).is(':checked')){
                $(this).parent().parent().find('dd').find('input[type=checkbox]').attr("checked","checked");
            }else{
                $(this).parent().parent().find('dd').find('input[type=checkbox]').removeAttr("checked");
            }
        })
    })
</script>