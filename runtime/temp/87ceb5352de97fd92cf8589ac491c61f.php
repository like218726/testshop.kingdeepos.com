<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:49:"./application/admin/view/goods/ajaxGoodsList.html";i:1587634374;}*/ ?>
<table>
       <tbody>
       <?php if(empty($goodsList) || (($goodsList instanceof \think\Collection || $goodsList instanceof \think\Paginator ) && $goodsList->isEmpty())): ?>
           <tr>
               <td class="no-data" align="center" axis="col0" colspan="50">
                   <i class="fa fa-exclamation-circle"></i>没有符合条件的记录
               </td>
           </tr>
           <?php else: if(is_array($goodsList) || $goodsList instanceof \think\Collection || $goodsList instanceof \think\Paginator): $i = 0; $__LIST__ = $goodsList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?>
              <tr>
                <td align="center" axis="col0">
                      <div style="width: 65px;">
                          <?php if($list['goods_state'] != '1' and $list['supplier_goods_status'] == '0'): ?>
                              <input type="checkbox" name="goods_id[]" value="<?php echo $list['goods_id']; ?>"/>
                          <?php endif; ?>
                          <?php echo $list['goods_id']; ?>
                      </div>
                </td>                
                <td align="center" axis="col0">
                  <div style="text-align: left; width: 300px;"><?php echo getSubstr($list['goods_name'],0,33); ?></div>
                </td>
                <td align="center" axis="col0">
                  <div style="text-align: center; width: 100px;"><?php echo $list['goods_sn']; ?></div>
                </td>
                <td align="center" axis="col0">
                  <div style="text-align: center; width: 200px;"><?php echo $catList[$list[cat_id1]][name]; ?></div>
                </td>
                <td align="center" axis="col0">
					<?php if($list[purpose] == 1): ?>
						<input style="width: 56px;" class="txt-cen" type="text" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')"  onpaste="this.value=this.value.replace(/[^\d.]/g,'')" onchange="updateSort2('goods','goods_id','<?php echo $list['goods_id']; ?>','shop_price',this)" size="4" value="<?php echo $list['shop_price']; ?>" />
					<?php else: ?>
						<div style="text-align: center; width: 50px;">-</div>
					<?php endif; ?>
				</td>
				<td align="center" axis="col0">
					<div style="text-align: center; width: 50px;"><?php echo $list['cost_price']; ?></div>
                </td>
                <td align="center" axis="col0">
					<?php if($list[purpose] == 1): ?>
						<div style="text-align: center; width: 50px;"><?php echo $list['store_count']; ?></div>
					<?php else: ?>
						<div style="text-align: center; width: 50px;">-</div>
					<?php endif; ?>
                </td>
                  <td align="center" axis="col0">
                      <div style="text-align: center; width: 100px;"><?php echo date('Y-m-d',$list['on_time']); ?></div>
                  </td>
<!--                <td align="center" axis="col0">
                  <div style="text-align: center; width: 50px;">
                    <?php if($list[is_recommend] == 1): ?>
                      <span class="yes" onClick="changeTableVal('goods','goods_id','<?php echo $list['goods_id']; ?>','is_recommend',this)" ><i class="fa fa-check-circle"></i>是</span>
                      <?php else: ?>
                      <span class="no" onClick="changeTableVal('goods','goods_id','<?php echo $list['goods_id']; ?>','is_recommend',this)" ><i class="fa fa-ban"></i>否</span>
                    <?php endif; ?>
                  </div>
                </td>
                <td align="center" axis="col0">
                  <div style="text-align: center; width: 50px;">
                    <?php if($list[is_new] == 1): ?>
                      <span class="yes" onClick="changeTableVal('goods','goods_id','<?php echo $list['goods_id']; ?>','is_new',this)" ><i class="fa fa-check-circle"></i>是</span>
                      <?php else: ?>
                      <span class="no" onClick="changeTableVal('goods','goods_id','<?php echo $list['goods_id']; ?>','is_new',this)" ><i class="fa fa-ban"></i>否</span>
                    <?php endif; ?>
                  </div>
                </td>
                <td align="center" axis="col0">
                  <div style="text-align: center; width: 50px;">
                    <?php if($list[is_hot] == 1): ?>
                      <span class="yes" onClick="changeTableVal('goods','goods_id','<?php echo $list['goods_id']; ?>','is_hot',this)" ><i class="fa fa-check-circle"></i>是</span>
                      <?php else: ?>
                      <span class="no" onClick="changeTableVal('goods','goods_id','<?php echo $list['goods_id']; ?>','is_hot',this)" ><i class="fa fa-ban"></i>否</span>
                    <?php endif; ?>
                  </div>
                </td>-->
                <td align="center" axis="col0">
                  <div style="text-align: center; width: 50px;">
					<?php if($list['purpose'] == 1): if($list[is_on_sale] == 0): ?>下架<?php endif; if($list[is_on_sale] == 1): ?>出售中<?php endif; if($list[is_on_sale] == 2): ?>违规下架<?php endif; else: if($list[is_supply] == 0): ?>下架<?php endif; if($list[is_supply] == 1): ?>供应中<?php endif; if($list[is_supply] == 2): ?>违规下架<?php endif; endif; ?>
                  </div>
                </td>    
                <td align="center" axis="col0">
                    <div style="text-align: center; width: 50px;">
                      <?php if($list[supplier_goods_status] == 1): ?>
                        待同意
                      <?php else: if($list[goods_state] == 0): ?>待审核<?php endif; if($list[goods_state] == 1): ?>通过<?php endif; if($list[goods_state] == 2): ?>未通过<?php endif; endif; ?>
                    </div>
                </td>
				<td align="center" axis="col0">
                  <div style="text-align: center; width: 50px;"><?php echo $list[purpose]==1?'销售' : '供应'; ?></div>
                </td> 
                <td align="center" axis="col0">
                  <div style="text-align: center; width: 100px;"><?php echo $store_list[$list[store_id]]; ?></div>
                </td>  
                <td align="center" axis="col0">
                  <div style="text-align: center; width: 50px;"><?php echo $store_type[$list[is_own_shop]]; ?></div>
                </td>   
                 <td>
				 <?php if($list[purpose] == 1): ?>
						<input style="width: 86px;" class="txt-cen" type="text" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" onpaste="this.value=this.value.replace(/[^\d]/g,'')" onchange="updateSort2('goods','goods_id','<?php echo $list['goods_id']; ?>','virtual_sales_sum',this)" size="4" value="<?php echo $list['virtual_sales_sum']; ?>" />
					<?php else: ?>
						<div style="text-align: center; width: 80px;">-</div>
					<?php endif; ?>
                
            </td>                                                     
                <td align="center" axis="col0">
                  <div style="text-align: center; width: 120px; max-width:120px;">
                  	<?php if($list[goods_state] < 3 and $list['is_on_sale'] < 2 and $list['is_supply'] < 2): ?>
                  	<a class="btn red" href="javascript:void(0);" onclick="takeoff(this)" goods_id="<?php echo $list['goods_id']; ?>" goods_sn="<?php echo $list['goods_sn']; ?>" goods_name="<?php echo $list['goods_name']; ?>">
                  	<i class="fa fa-ban"></i>下架</a><?php endif; ?>
                  	<a class="btn blue" target="_blank"  href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$list['goods_id'],'identity'=>'admin')); ?>"><i class="fa fa-search"></i>查看</a>
                  </div>
                </td>
                <td align="" class="" style="width: 100%;">
                  <div>&nbsp;</div>
                </td>
              </tr>
            <?php endforeach; endif; else: echo "" ;endif; if(\think\Request::instance()->param('goods_state') != '1'): ?>
            <tr class="spe_select">
	            <td colspan="14">
                    <div class="col-sm-3 form-inline">
                        	全选
                        <input type="checkbox" onclick="$('input[name=\'goods_id\[\]\']').prop('checked', this.checked);">
                            <select id="func_id" class="form-control" style="width: 120px;" onchange="fuc_change(this);">
                                <option value="-1">请选择操作</option>
                   <!--             <option value="0">推荐</option>
                                <option value="1">新品</option>
                                <option value="2">热卖</option>-->
                                <option value="3">审核商品</option>
                            </select>
                            <select id="state_id" class="form-control" style="display: none" onchange="state_change(this);">
                                <option value="">请选择操作类型</option>
                                <?php if(is_array($goods_state) || $goods_state instanceof \think\Collection || $goods_state instanceof \think\Paginator): if( count($goods_state)==0 ) : echo "" ;else: foreach($goods_state as $key=>$vo): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $goods_state[$key]; ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select> 
                        <a id="act_button" href="javascript:;" onclick="act_submit();" style="color:#FFF;" class="ncap-btn-mini ncap-btn-green disabled"><i class="fa"></i> 确定操作</a>
                    </div>                
                </td>
            </tr>
           <?php endif; endif; ?>
          </tbody>
        </table>
        <!--分页位置--> <?php echo $page; ?>
		<script>
            // 点击分页触发的事件
            $(".pagination  a").click(function(){
                cur_page = $(this).data('p');
                ajax_get_table('search-form2',cur_page);
            });
        </script>        