<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:52:"./application/seller/new/store/goods_class_info.html";i:1587634378;}*/ ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>商家中心</title>
<link href="/public/static/css/base.css" rel="stylesheet" type="text/css">
<link href="/public/static/css/seller_center.css" rel="stylesheet" type="text/css">
<link href="/public/static/font/css/font-awesome.min.css" rel="stylesheet" />
<script type="text/javascript" src="/public/static/js/jquery.js"></script>
<script type="text/javascript" src="/public/static/js/layer/layer.js"></script>
</head>
<body>
<div class="w340">
<div class="eject_con">
  <div id="warning" class="alert alert-error"></div>
  <form id="handlepost" method="post" target="_parent" action="">
        <dl>
      <dt><i class="required">*</i>分类名称：</dt>
      <dd>
        <input class="text w200" type="text" name="cat_name" id="cat_name" value="<?php echo $info['cat_name']; ?>">
      </dd>
    </dl>
    <dl>
      <dt>上级分类：</dt>
      <dd>
		<select name="parent_id" id="parent_id" value="<?php echo $menu['parent_id']; ?>">	
			<option value="0">顶级菜单</option>		
			<?php if(is_array($parent) || $parent instanceof \think\Collection || $parent instanceof \think\Paginator): if( count($parent)==0 ) : echo "" ;else: foreach($parent as $key=>$v): ?>
				<option value="<?php echo $v['cat_id']; ?>" <?php if($v[cat_id] == $info[parent_id]): ?> selected="selected"<?php endif; ?>>&nbsp;&nbsp;|--<?php echo $v['cat_name']; ?></option>
			<?php endforeach; endif; else: echo "" ;endif; ?>
		</select>      
      </dd>
    </dl>
    <dl>
      <dt>显示状态：</dt>
      <dd>
        <label>
          <input type="radio" name="is_show" value="1" <?php if($info[is_show] == 1): ?>checked<?php endif; ?>>
          是</label>
        <label>
          <input type="radio" name="is_show" value="0" <?php if($info[is_show] == 0): ?>checked<?php endif; ?>>
          否</label>
      </dd>
    </dl>
        <dl>
      <dt>是否导航显示：</dt>
      <dd>
        <label>
          <input type="radio" name="is_nav_show" value="1" <?php if($info[is_nav_show] == 1): ?>checked<?php endif; ?>>
          是</label>
        <label>
          <input type="radio" name="is_nav_show" value="0" <?php if($info[is_nav_show] == 0): ?>checked<?php endif; ?>>
          否</label>
      </dd>
    </dl>
        <dl>
      <dt>是否首页推荐：</dt>
      <dd>
        <label>
          <input type="radio" name="is_recommend" value="1" <?php if($info[is_recommend] == 1): ?>checked<?php endif; ?>>
          是</label>
        <label>
          <input type="radio" name="is_recommend" value="0" <?php if($info[is_recommend] == 0): ?>checked<?php endif; ?>>
          否</label>
      </dd>
    </dl>
    <dl>
      <dt>排序：</dt>
      <dd>
        <input class="text w60" type="text" name="cat_sort" value="<?php echo (isset($info['cat_sort']) && ($info['cat_sort'] !== '')?$info['cat_sort']:10); ?>">
      </dd>
    </dl>
    <div class="bottom">
        <label class="submit-border"><input type="hidden" name="cat_id" value="<?php echo $info['cat_id']; ?>">
        <input type="button" onclick="dataSave()" class="submit" value="提交"></label>
    </div>
  </form>
</div>
</div>	
<script type="text/javascript">
	function dataSave(){
		if($('input[name="cat_name"]').val() == ''){
			layer.msg('分类名称不能为空', {icon: 3});
			return;
		}
		$.ajax({
			url : "<?php echo U('Store/goods_class_save'); ?>",
			data : $('#handlepost').serialize(),
			type : 'post',
			dataType : 'json',
			success :function(data){
				if(data.status==1){
					window.parent.call_back(1);	
				}else{
					window.parent.call_back(0);
				}						
			}			
		});		
	}
</script>
</body>
</html>
