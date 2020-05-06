<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:48:"./application/seller/new/goods/ajaxSpecList.html";i:1587634376;}*/ ?>
<style>
    .nav-tabs {
    border-bottom: 1px solid #ddd;
    margin-top: 20px;
}
.nav {
    padding-left: 0;
    margin-bottom: 0;
    list-style: none;
    /*overflow: hidden;*/
}
.nav-tabs:before,.nav-tabs:after{
    display: table;
    clear: both;
    content: " ";
}
.nav>li {
    position: relative;
    display: block;
}
.nav-tabs>li {
    float: left;
    margin-bottom: -1px;
}
.nav>li>a {
    position: relative;
    display: block;
    padding: 10px 15px;
}
.nav-tabs>li>a {
    margin-right: 2px;
    line-height: 1.42857143;
    border: 1px solid transparent;
    border-radius: 4px 4px 0 0;
}
.nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
    color: #555;
    cursor: default;
    background-color: #fff;
    border: 1px solid #ddd;
    border-bottom-color: transparent;
}
.nscs-table-handle span a{
    cursor: pointer;
}
</style>
<ul class="nav nav-tabs">
    <?php if(is_array($specList) || $specList instanceof \think\Collection || $specList instanceof \think\Paginator): $i = 0; $__LIST__ = $specList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?>
    	<li id="<?php echo $list[id]; ?>" <?php if($i == 1): ?> class="active" <?php endif; ?>><a data-toggle="tab" href="javascript:void(0);" onclick="ajax_get_data(<?php echo $list[id]; ?>);"><?php echo $list['name']; ?></a></li>
    <?php endforeach; endif; else: echo "" ;endif; ?>
</ul>
<table class="ncsc-default-table" id="spec_item_table">
    <thead>
    <tr>
        <th class="w20"></th>
        <th class="w200 tl">规格名称</th>
        <th class="w300 tl">规格项</th>
        <th class="w100">操作</th>
    </tr>
    </thead>
    <tbody>
    <?php if(empty($specItemList) || (($specItemList instanceof \think\Collection || $specItemList instanceof \think\Paginator ) && $specItemList->isEmpty())): ?>
        <tr class="no-data-tr">
            <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span>暂无符合条件的数据记录</span></div></td>
        </tr>
    <?php else: if(is_array($specItemList) || $specItemList instanceof \think\Collection || $specItemList instanceof \think\Paginator): $i = 0; $__LIST__ = $specItemList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list2): $mod = ($i % 2 );++$i;?>
        <tr class="bd-line">
            <td></td>
            <td class="tl"><?php echo $specList[$list2[spec_id]][name]; ?></td>
            <td class="tl">
                <input type="text" class="txt w200"  value="<?php echo $list2['item']; ?>" name="item[<?php echo $list2['id']; ?>]"/>
                <span style="color:#F00; display:none;">请填写内容</span>
            </td>
            <td class="nscs-table-handle">
                <span><a class="btn-grapefruit delItem" data-id="<?php echo $list2['id']; ?>"><i class="icon-trash"></i>
                    <p>删除</p></a></span>
            </td>
        </tr>
    <?php endforeach; endif; else: echo "" ;endif; endif; ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="20">
            <div style="text-align: center;margin-top:20px;"><label class="submit-border">
                <input type="hidden" name="spec_id" value="<?php echo $spec_id; ?>" />
                <input id="submit" class="submit" value="提交" type="submit"></label>
            </div>
        </td>
    </tr>
    </tfoot>
</table>