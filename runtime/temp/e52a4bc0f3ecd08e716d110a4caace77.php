<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:46:"./application/admin/view/user/momentsList.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<body style="background-color: rgb(255, 255, 255); overflow: auto; cursor: default; -moz-user-select: inherit;">
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>会员动态管理</h3>
                <h5>会员动态，评论与点赞</h5>
            </div>
            <ul class="tab-base nc-row">
                <li><a class="current" href="<?php echo U('user/momentsList'); ?>" data-state="" ><span>动态审核</span></a></li>
                <!-- <li><a href="<?php echo U('user/commentList'); ?>" data-state="0"  ><span>评论审核</span></a></li> -->
                <li><a href="<?php echo U('user/commentClassify'); ?>" data-state="0"  ><span>动态分类管理</span></a></li>
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
            <li>审核：通过审核科看到别人的动态（自己的动态自己看不需要通过审核）</li>
            <li>排序规则： 先到显示状态 -> 在按审核状态 ->最后到时间 都是升序（先来后到）</li>

        </ul>
    </div>
    <div class="flexigrid">
        <div class="mDiv">
            <div class="ftitle">
                <h3>会员动态列表</h3>
                <h5>(共<?php echo count($list); ?>条记录)</h5>
            </div>
            <div title="刷新数据" class="pReload"><i class="fa fa-refresh"></i></div>
            <form class="navbar-form form-inline" action="<?php echo U('User/momentsList'); ?>" method="post">
                <div class="sDiv">
                    <div class="sDiv2">
                        <select  name="status" class="select">
                            <option value="0">所有状态</option>
                            <option <?php if($_POST['status'] == 1): ?>selected<?php endif; ?> value="1">未审核</option>
                            <option <?php if($_POST['status'] == 2): ?>selected<?php endif; ?> value="2">通过</option>
                            <option <?php if($_POST['status'] == 3): ?>selected<?php endif; ?> value="3">不通过</option>
                            <option <?php if($_POST['status'] == 4): ?>selected<?php endif; ?> value="4">禁言</option>
                        </select>
                        <select  name="classify" class="select">
                            <option value="">所有类别</option>
                            <?php if(is_array($classify) || $classify instanceof \think\Collection || $classify instanceof \think\Paginator): $i = 0; $__LIST__ = $classify;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                <option <?php if($_POST['classify'] == $vo['classify_id']): ?>selected<?php endif; ?> value="<?php echo $vo['classify_id']; ?>"><?php echo $vo['name']; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                        <input type="text" size="30" name="keywords" class="qsbox" placeholder="<?php echo (isset($_POST['keywords']) && ($_POST['keywords'] !== '')?$_POST['keywords']:'搜索相关数据...'); ?>">
                        <input type="submit" class="btn" value="搜索">
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
                            <div style="width: 24px;" onclick="checked()"><i class="ico-check"></i></div>
                        </th>
                        <th align="left" abbr="moments_id" axis="col3" class="">
                            <div style="text-align: left; width: 50px;" class="">ID</div>
                        </th>
                        <th align="left" abbr="user_id" axis="col4" class="">
                            <div style="text-align: left; width: 100px;" class="">会员名称</div>
                        </th>
                        <th align="left" abbr="user_id" axis="col4" class="">
                            <div style="text-align: left; width: 200px;" class="">标题</div>
                        </th>
                        <th align="center" abbr="moments_content" axis="col5" class="">
                            <div style="text-align: center; width: 500px;" class="">内容</div>
                        </th>
                        <th align="center" abbr="status" axis="col6" class="">
                            <div style="text-align: center; width: 100px;" class="">审核状态</div>
                        </th>
                        <th align="left" abbr="user_id" axis="col4" class="">
                            <div style="text-align: left; width: 100px;" class="">是否推荐</div>
                        </th>
                        <th align="center" abbr="is_delete" axis="col6" class="">
                            <div style="text-align: center; width:80px;" class="">是否显示</div>
                        </th>
                        <th align="center" abbr="add_time" axis="col6" class="">
                            <div style="text-align: center; width: 150px;" class="">发表时间</div>
                        </th>
                        <th align="center" axis="col1" class="handle">
                            <div style="text-align: center; width: 220px;">操作</div>
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
                    <a onclick="act_submit(1)">
                        <div class="add" title="审核通过">
                            <span><i class="fa fa-check"></i>审核通过</span>
                        </div>
                    </a>
                </div>
                <div class="fbutton">
                    <a onclick="act_submit(2)">
                        <div class="add" title="审核拒绝">
                            <span><i class="fa fa-ban"></i>审核拒绝</span>
                        </div>
                    </a>
                </div>
                <!--<div class="fbutton">-->
                    <!--<a onclick="act_submit(-2)">-->
                        <!--<div class="add" title="无效作废">-->
                            <!--<span><i class="fa fa-close"></i>无效作废</span>-->
                        <!--</div>-->
                    <!--</a>-->
                <!--</div>-->
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="bDiv" style="height: auto;">
            <div id="flexigrid" cellpadding="0" cellspacing="0" border="0">
                <table>
                    <tbody>
                    <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $k=>$vo): ?>
                        <tr>
                            <td class="sign">
                                <div style="width: 24px;">
                                    <i class="ico-check">
                                        <input type="checkbox" style="display:none;" name="selected[]"
                                               value="<?php echo $vo['moments_id']; ?>">
                                    </i>
                                </div>
                            </td>
                            <td align="left" class="">
                                <div style="text-align: left; width: 50px;"><?php echo $vo['moments_id']; ?></div>
                            </td>
                            <td align="left" class="">
                                <div style="text-align: left; width: 100px;"><?php echo !empty($vo['nickname'])?$vo['nickname']:$vo['mobile']; ?></div>
                            </td>
                            <td align="left" class="">
                                <div style="text-align: left; width: 200px;"><?php echo $vo['title']; ?></div>
                            </td>
                            <td align="center" class="">
                                <div style="text-align: center; width: 500px;padding-left:100px;padding-right:100px;box-sizing:border-box;height:100%;">
                                    <?php echo $vo['moments_content']; ?>
                                </div>
                            </td>
                            <td align="center" class="">
                                <div style="text-align: center; width: 100px;">
                                    <!--<?php echo $status[$vo['status']]; ?>-->
                                    <?php if($vo['status'] == 1): ?>
                                        <span style="color:green"> <?php echo $status[$vo['status']]; ?> </span>
                                        <?php elseif($vo['status'] == 2): ?>
                                        <span style="color:red"> <?php echo $status[$vo['status']]; ?> </span>
                                        <?php else: ?>
                                        <span style="color:#0ba4da"> <?php echo $status[$vo['status']]; ?> </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                           <td align="left" class="">
                           		 <div style="text-align: center; width: 100px;">
                           		 
                           		     <?php if($vo[status] == 1): if($vo[is_top] == 1): ?>
						                      <span class="yes" onClick="changeTableVal('moments','moments_id','<?php echo $vo['moments_id']; ?>','is_top',this)" ><i class="fa fa-check-circle"></i>是</span>
						                      <?php else: ?>
						                      <span class="no" onClick="changeTableVal('moments','moments_id','<?php echo $vo['moments_id']; ?>','is_top',this)" ><i class="fa fa-ban"></i>否</span>
						                    <?php endif; else: ?>
                           		     	请先通过审核                           		     
                           		     <?php endif; ?>
                           		 
				                   
			                  </div>
                            </td>
                            <td align="center" class="">
                                <div style="text-align: center; width: 80px;<?php echo $vo['is_delete']==1?'color:red':''; ?>">
                                    <!--<?php echo $vo['is_delete']==1?'隐藏':'显示'; ?>-->
                                    <?php if($vo[is_delete] == 1): ?>
                                        <span class="no" onClick="isDelete('moments','moments_id','<?php echo $vo['moments_id']; ?>','is_delete',this)" ><i class="fa fa-ban"></i>否</span>
                                        <?php else: ?>
                                        <span class="yes" onClick="isDelete('moments','moments_id','<?php echo $vo['moments_id']; ?>','is_delete',this)" ><i class="fa  fa-check-circle"></i>是</span>
                                    <?php endif; ?>
                                </div>

                            </td>
                            <td align="center" class="">
                                <div style="text-align: center; width: 150px;"><?php echo date("Y-m-d H:i:s",$vo['add_time']); ?></div>
                            </td>
                            <td align="center" class="handle">
                                <div style="text-align: center;  min-width:100px;">
                                    <a href="<?php echo U('User/seeMoments',array('moments_id'=>$vo['moments_id'])); ?>" class="btn blue"><i class="fa fa-pencil-square-o"></i>查看</a>
                                    <a href="<?php echo U('User/seeComment',array('moments_id'=>$vo['moments_id'])); ?>" class="btn blue"><i class="fa fa-pencil-square-o"></i>评论(<spam style="color: red" ><?php echo $vo['comment']; ?></spam>)</a>
                                    <a class="btn blue" data-url="<?php echo U('User/talk'); ?>" onclick="talk(this)" data-id="<?php echo $vo['moments_id']; ?>"><?php if($vo['is_talk']): ?>解除禁言<?php else: ?>禁言<?php endif; ?></spam></a>
                                    <!--<a class="btn red"  href="javascript:void(0)" data-url="<?php echo U('User/levelHandle'); ?>" data-id="<?php echo $vo['level_id']; ?>" onClick="delfun(this)"><i class="fa fa-trash-o"></i>删除</a>-->
                                </div>
                            </td>
                            <td align="" class="" style="width: 100%;">
                                <div>&nbsp;</div>
                            </td>
                        </tr>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="iDiv" style="display: none;"></div>
        </div>
        <!--分页位置-->
        <?php echo $page; ?> </div>
</div>
<script>
    $(document).ready(function(){
        // $("input[name='box'][value=1]").attr("checked","checked");
        // 表格行点击选中切换
        $('#flexigrid > table>tbody >tr').click(function(){
            //点击选中,否则相反
            var checked = $(this).find('input').is(':checked');
            if(checked){
                $(this).find('input').attr("checked",null);
            }else{
                $(this).find('input').attr("checked","checked");
            }

            $(this).toggleClass('trSelected');
        });

        // 点击刷新数据
        $('.fa-refresh').click(function(){
            location.href = location.href;
        });

    });

    function checked(){
        $('input[type="checkbox"]').each(function(i,o){
            if($(o).is(':checked')){
                $(o).removeAttr('checked')
            }else{
                $(o).attr('checked','checked')
            }

        })
    }

    //批量操作提交
    function act_submit(wst) {
        var chks = [];
        $('input[name*=selected]').each(function(i,o){
            if($(o).is(':checked')){
                chks.push($(o).val());
            }
        })


        if(chks.length == 0){
            layer.alert('少年，请至少选择一项', {icon: 2});return;
        }
        var can_post = false;
        var remark = "审核通过";
            if(wst != 1 ){
            layer.prompt({title: '请填写备注', formType: 2}, function(text, index){
                layer.close(index);
                remark = text;
                audit(chks , wst ,  remark);
            });
        }else{
            audit(chks , wst ,  remark);
        }
    }

    function audit(chks , wst ,  remark){
        $.ajax({
            type: "POST",
            url: "/index.php?m=Admin&c=User&a=moments_update",//+tab,
            data: {id:chks,status:wst,remark:remark},
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

    function talk(obj) {
        // 删除按钮

            $.ajax({
                type: 'post',
                url: $(obj).attr('data-url'),
                data : {moments_id:$(obj).attr('data-id')},
                dataType: 'json',
                success: function (data) {
                    layer.closeAll();
                    if (data.status == 1) {
                        layer.msg(data.msg, {icon: 1});
                        $(obj).text(data.msg=='解禁成功'?'禁言':'解除禁言');
                    } else {
                        layer.alert(data.msg, {icon: 2});
                    }
                }
            })
    }

    function delfun(obj) {
        // 删除按钮
        layer.confirm('确认删除？', {
            btn: ['确定', '取消'] //按钮
        }, function () {
            $.ajax({
                type: 'post',
                url: $(obj).attr('data-url'),
                data : {act:'del',level_id:$(obj).attr('data-id')},
                dataType: 'json',
                success: function (data) {
                    layer.closeAll();
                    if (data.status == 1) {
                        layer.msg(data.msg, {icon: 1});
                        $(obj).parent().parent().parent().remove();
                    } else {
                        layer.alert(data.msg, {icon: 2});
                    }
                }
            })
        }, function () {
            layer.closeAll();
        });
    }
    // 修改指定表的指定字段值 包括有按钮点击切换是否(虚拟删除，1为删除，0未删除)
    function isDelete(table,id_name,id_value,field,obj)
    {
        var src = "";
        if($(obj).hasClass('no')) // 图片点击是否操作
        {
            //src = '/public/images/yes.png';
            $(obj).removeClass('no').addClass('yes');
            $(obj).html("<i class='fa fa-check-circle'></i>是");
            var value = 0;

        }else if($(obj).hasClass('yes')){ // 图片点击是否操作
            $(obj).removeClass('yes').addClass('no');
            $(obj).html("<i class='fa fa-ban'></i>否");
            var value = 1;
        }else{ // 其他输入框操作
            var value = $(obj).val();
        }

        $.ajax({
            url:"/index.php?m=Admin&c=Index&a=changeTableVal&table="+table+"&id_name="+id_name+"&id_value="+id_value+"&field="+field+'&value='+value,
            success: function(data){
                if(!$(obj).hasClass('no') && !$(obj).hasClass('yes'))
                    layer.msg('更新成功', {icon: 1});
            }
        });
    }
</script>
</body>
</html>