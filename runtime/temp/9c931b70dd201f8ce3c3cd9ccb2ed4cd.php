<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:35:"./template/pc/rainbow/user/reg.html";i:1587634424;s:76:"/home/wwwroot/testshop.kingdeepos.com/template/pc/rainbow/public/footer.html";i:1587634420;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">   
    <title>注册-<?php echo $tpshop_config['shop_info_store_title']; ?></title>
    <link rel="shortcut  icon" type="image/x-icon" href="<?php echo (isset($tpshop_config['shop_info_store_ico']) && ($tpshop_config['shop_info_store_ico'] !== '')?$tpshop_config['shop_info_store_ico']:'/public/static/images/logo/storeico_default.png'); ?>" media="screen"/>
    <meta name="keywords" content="<?php echo $tpshop_config['shop_info_store_keyword']; ?>" />
    <meta name="description" content="<?php echo $tpshop_config['shop_info_store_desc']; ?>" />
    <link href="/template/pc/rainbow/static/css/reg3.css" rel="stylesheet" /> 
    <script type="text/javascript" src="/public/js/jquery-1.10.2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/template/pc/rainbow/static/css/tpshop.css" />
	<script src="/public/js/layer/layer.js"></script><!--弹窗js 参考文档 http://layer.layui.com/-->
    <script src="/public/js/md5.min.js"></script>
    <script src="/public/js/global.js"></script>
</head>
<body>
    <div class="regcon">
        <a class="m-fnlogoa fn-fl" href="/"><img src="<?php echo (isset($tpshop_config['shop_info_store_logo']) && ($tpshop_config['shop_info_store_logo'] !== '')?$tpshop_config['shop_info_store_logo']:'/public/static/images/logo/pc_home_logo_default.png'); ?>" style="width: 159px;height: 58px;"/></a>
        <span class="m-fntit">欢迎注册</span>
        <div class="ui_tab">
            <ul class="ui_tab_nav regnav">
                <li class="uli <?php if($_GET['t'] == ''): ?>active<?php endif; ?> "><a href="<?php echo U('Home/User/reg'); ?>" >手机注册</a></li>
                <li class="uli <?php if($_GET['t'] == 'email'): ?>active<?php endif; ?> "><a href="<?php echo U('Home/User/reg',array('t'=>'email')); ?>">邮箱注册</a></li>
                <li class="no fn-fr loginbtn">我已注册，马上<a href="<?php echo U('Home/User/login'); ?>">登录></a></li>
            </ul>
            
<?php if($_GET['t'] == ''): ?>    
		<form id="reg_form2" onsubmit="return false">
			<input type="hidden" name="scene" value="1">
            <input type="hidden" name="auth_code" value="<?php echo \think\Config::get('AUTH_CODE'); ?>"/>
            <div class="ui_tab_content">
                <div class="m-fnbox ui_panel" style="display: block;">
                    <div class="fnlogin clearfix">
                    
                        <div class="line">
                            <label class="linel"><em>*</em><span class="dt">手机号码：</span></label>
                            <div class="liner">
                                <input type="text" class="inp fmobile J_cellphone" placeholder="请输入手机号码"  name="username" id="username" required="" maxlength="11"/>
                                <p class="fn-fl errorbox v-txt" id="err_username">手机号码输入不正确</p>
                            </div>
                        </div>
                        <div class="line">
                            <label class="linel"><em>*</em><span class="dt">图像验证码：</span></label>
                            <div class="liner">
                                <input type="text" class="inp imgcode J_imgcode" placeholder="图像验证码" id="verify_code2" name="verify_code" required=""/>
                                <img width="100" height="35" src="/index.php/Home/User/verify/type/user_reg.html" id="reflsh_code2" class="po-ab to0">
                                <a><img onclick="verify('reflsh_code2')" src="/template/pc/rainbow/static/images/chg_image.png" class="ma-le-210 verifyImg"></a>
                                <p class="fn-fl errorbox v-txt" id="err_verify_code">请输入验证码</p>
                            </div>
                            <div id="show-voice" class="show-voice"></div>
                        </div>
                   <?php if($tpshop_config['sms_regis_sms_enable'] == 1): ?>
                        <div class="line">
                            <label class="linel"><em>*</em><span class="dt">手机验证码：</span></label>
                            <div class="liner">
                                <input type="text" class="inp imgcode J_imgcode" placeholder="手机验证码" id="code" name="code" required=""/>                                
                                <button class="fn-fl icode" onclick="send_sms_reg_code()" type="button" id="count_down">发送</button>
                                <p class="fn-fl errorbox v-txt" id="err_code">验证码输入不正确</p>
                            </div>
                            <div id="show-voice" class="show-voice"></div>
                        </div>
                   <?php endif; ?>
                        <div class="line">
                            <label class="linel"><em>*</em><span class="dt">设置密码：</span></label>
                            <div class="liner">
                                <input type="password" id="password" class="inp fpass J_password" placeholder="6-16位大小写英文字母、数字或符号的组合" autocomplete="off" maxlength="16"  value="" required=""/>
                                <input name="password" value="" type="hidden"/>
                                <p class="fn-fl noticebox v-txt2" id="err_password">6-16位字符，建议由字母、数字和符号两种以上组合</p>
                            </div>
                        </div>
                        <div class="line">
                            <label class="linel"><em>*</em><span class="dt">确认密码：</span></label>
                            <div class="liner">
                                <input type="password" id="password2" class="inp fsecpass J_password2" placeholder="请再次输入密码" autocomplete="off" maxlength="16"  required="" value=""/>
                                <input name="password2" value="" type="hidden"/>
                                <p class="fn-fl errorbox v-txt" id="err_password2">两次输入密码不一致</p>
                            </div>
                        </div>
                        <div class="line liney clearfix">
                            <label class="linel">&nbsp;</label>
                            <div class="liner">
                                <div class="clearfix checkcon">
                                    <p class="fn-fl checktxt"><input type="checkbox" class="iyes fn-fl J_protocal" checked />
                                    <span class="fn-fl">我已阅读并同意</span><a class="itxt fn-fl" href="<?php echo U('Home/Article/agreement',['doc_code'=>'agreement']); ?>" target="_blank">《服务协议》</a>
                                    </p>
                                      <p class="fn-fl errorbox v-txt" id="protocalBox"></p>
                                </div>
                                <a id="submit" class="regbtn J_btn_agree" href="javascript:void(0);" onClick="$('#reg_form2').submit();">同意协议并注册</a>
                                <p class="v-txt" id="err_check_code"><span class="fnred">请勾选</span>我已阅读并同意<a class="itxt" href="<?php echo U('Home/Article/agreement',['doc_code'=>'agreement']); ?>" target="_blank">《服务协议》</a></p>
                        </div>
                    </div>
                    </div>
                    </div>
            </div>
            </form>
<?php endif; if($_GET['t'] == 'email'): ?>    
		<form id="reg_form2" onsubmit="return false;">
            <input type="hidden" name="auth_code" value="<?php echo \think\Config::get('AUTH_CODE'); ?>"/>
            <div class="ui_tab_content">
                <div class="m-fnbox ui_panel" style="display: block;">
                    <div class="fnlogin clearfix">
                    
                        <div class="line">
                            <label class="linel"><em>*</em><span class="dt">邮箱：</span></label>
                            <div class="liner">
                                <input type="text" class="inp J_cellphone" placeholder="请输入邮箱"  name="username" id="username" required=""/>
                                <p class="fn-fl errorbox v-txt" id="err_username">邮箱输入不正确</p>
                            </div>
                        </div>
                        <div class="line">
                            <label class="linel"><em>*</em><span class="dt">图像验证码：</span></label>
                            <div class="liner">
                                <input type="text" class="inp imgcode J_imgcode" placeholder="图像验证码" id="verify_code2" name="verify_code" required=""/>
                                <img width="100" height="35" src="/index.php/Home/User/verify/type/user_reg.html" id="reflsh_code2" class="po-ab to0">
                                <a><img onclick="verify('reflsh_code2')" src="/template/pc/rainbow/static/images/chg_image.png" class="ma-le-210 verifyImg"></a>
                                <p class="fn-fl errorbox v-txt" id="err_verify_code">请输入验证码</p>
                            </div>
                            <div id="show-voice" class="show-voice"></div>
                        </div>
                        <?php if($regis_smtp_enable == 1): ?>
                            <div class="line">
                                <label class="linel"><em>*</em><span class="dt">邮箱验证码：</span></label>
                                <div class="liner">
                                    <input type="text" class="inp imgcode J_imgcode" placeholder="邮箱验证码" id="code" name="code" required=""/>
                                    <button class="fn-fl icode" onclick="send_smtp_reg_code()" type="button" id="count_down">发送</button>
                                    <p class="fn-fl errorbox v-txt" id="err_code">验证码输入不正确</p>
                                </div>
                                <div id="show-voice" class="show-voice"></div>
                            </div>
                        <?php endif; ?>
                        <div class="line">
                            <label class="linel"><em>*</em><span class="dt">设置密码：</span></label>
                            <div class="liner">
                                <input type="password" class="inp fpass J_password" placeholder="6-16位大小写英文字母、数字或符号的组合" autocomplete="off" maxlength="16"  id="password" value="" required=""/>
                                <input name="password" type="hidden" value=""/>
                                <p class="fn-fl noticebox v-txt2" id="err_password">6-16位字符，建议由字母、数字和符号两种以上组合</p>
                            </div>
                        </div>
                        <div class="line">
                            <label class="linel"><em>*</em><span class="dt">确认密码：</span></label>
                            <div class="liner">
                                <input type="password" class="inp fsecpass J_password2" placeholder="请再次输入密码" autocomplete="off" maxlength="16" id="password2" required="" value=""/>
                                <input name="password2" type="hidden" value=""/>
                                <p class="fn-fl errorbox v-txt" id="err_password2">两次输入密码不一致</p>
                            </div>
                        </div>
                        <div class="line liney clearfix">
                            <label class="linel">&nbsp;</label>
                            <div class="liner">
                                <div class="clearfix checkcon">
                                    <p class="fn-fl checktxt"><input type="checkbox" class="iyes fn-fl J_protocal" checked />
                                    <span class="fn-fl">我已阅读并同意</span><a class="itxt fn-fl" href="<?php echo U('Home/Article/agreement',['doc_code'=>'agreement']); ?>" target="_blank">《服务协议》</a>
                                    </p>
                                      <p class="fn-fl errorbox v-txt" id="protocalBox"></p>
                                </div>
                                <a id="submit" class="regbtn J_btn_agree" href="javascript:void(0);" onClick="$('#reg_form2').submit();">同意协议并注册</a>
                                <p class="v-txt"><span class="fnred">请勾选</span>我已阅读并同意<a class="itxt" href="<?php echo U('Home/Article/agreement',['doc_code'=>'agreement']); ?>" target="_blank">《服务协议》</a></p>
                        </div>
                    </div>
                    </div>
                    </div>
            </div>
            </form>
<?php endif; ?>
            
        </div>
    </div>    
	<div class="footer p">
    <div class="mod_service_inner">
        <div class="w1224">
            <ul>
                <li>
                    <div class="mod_service_unit">
                        <h5 class="mod_service_duo">多</h5>
                        <p>品类齐全，轻松购物</p>
                    </div>
                </li>
                <li>
                    <div class="mod_service_unit">
                        <h5 class="mod_service_kuai">快</h5>
                        <p>多仓直发，极速配送</p>
                    </div>
                </li>
                <li>
                    <div class="mod_service_unit">
                        <h5 class="mod_service_hao">好</h5>
                        <p>正品行货，精致服务</p>
                    </div>
                </li>
                <li>
                    <div class="mod_service_unit">
                        <h5 class="mod_service_sheng">省</h5>
                        <p>天天低价，畅选无忧</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="w1224">
        <div class="footer-ewmcode">
		    <div class="foot-list-fl">
                <div class="foot-list-wrap p">
                    <?php
                                   
                                $md5_key = md5("select * from `__PREFIX__article_cat`  where cat_type = 1  order by cat_id asc limit 5  ");
                                $result_name = $sql_result_v = S("sql_".$md5_key);
                                if(empty($sql_result_v))
                                {                            
                                    $result_name = $sql_result_v = \think\Db::query("select * from `__PREFIX__article_cat`  where cat_type = 1  order by cat_id asc limit 5  "); 
                                    S("sql_".$md5_key,$sql_result_v,31104000);
                                }    
                              foreach($sql_result_v as $k=>$v): ?>
                        <ul>
                            <li class="foot-th">
                                <?php echo $v[cat_name]; ?>
                            </li>
                            <?php
                                   
                                $md5_key = md5("select * from `__PREFIX__article` where cat_id = $v[cat_id]  and is_open=1 limit 5 ");
                                $result_name = $sql_result_v2 = S("sql_".$md5_key);
                                if(empty($sql_result_v2))
                                {                            
                                    $result_name = $sql_result_v2 = \think\Db::query("select * from `__PREFIX__article` where cat_id = $v[cat_id]  and is_open=1 limit 5 "); 
                                    S("sql_".$md5_key,$sql_result_v2,31104000);
                                }    
                              foreach($sql_result_v2 as $k2=>$v2): ?>
                                <li>
                                    <a href="<?php echo U('Home/Article/detail',array('article_id'=>$v2[article_id])); ?>"><?php echo $v2[title]; ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endforeach; ?>
                </div>
		        <div class="friendship-links p">
                    <span>友情链接 : </span>
                    <div class="links-wrap-h p">
                    <?php
                                   
                                $md5_key = md5("select * from `__PREFIX__friend_link` where is_show=1");
                                $result_name = $sql_result_v = S("sql_".$md5_key);
                                if(empty($sql_result_v))
                                {                            
                                    $result_name = $sql_result_v = \think\Db::query("select * from `__PREFIX__friend_link` where is_show=1"); 
                                    S("sql_".$md5_key,$sql_result_v,31104000);
                                }    
                              foreach($sql_result_v as $k=>$v): ?>
                   	 	 <a href="<?php echo $v[link_url]; ?>" <?php if($v['target'] == 1): ?>target="_blank"<?php endif; ?> ><?php echo $v[link_name]; ?></a>
                    <?php endforeach; ?>
                    </div>
                </div>
		    </div>
			<div class="right-contact-us">
				<h3 class="title">客服热线（9:00-22:00）</h3>
				<span class="phone"><?php echo $tpshop_config['shop_info_phone']; ?></span>
				<p class="tips">官方微信</p>
				<div class="qr-code-list clearfix">
					<!--<a class="qr-code" href="javascript:;"><img src="/template/pc/rainbow/static/images/qrcode.png" alt="" /></a>-->
					<a class="qr-code qr-code-tpshop" href="javascript:;">
						<img src="<?php echo (isset($tpshop_config['shop_info_weixin_qrcode']) && ($tpshop_config['shop_info_weixin_qrcode'] !== '')?$tpshop_config['shop_info_weixin_qrcode']:'/template/pc/rainbow/static/images/qrcode.png'); ?>" alt="" />
					</a>
				</div>
			</div>
		    <!--<div class="QRcode-fr">
		        <ul>
		            <li class="foot-th">客户端</li>
		            <li><img src="/template/pc/rainbow/static/images/qrcode.png"/></li>
		        </ul>
		        <ul>
		            <li class="foot-th">微信</li>
		            <li><img src="/template/pc/rainbow/static/images/qrcode.png"/></li>
		        </ul>
		    </div>-->
		</div>
		<div class="mod_copyright p">
		    <div class="grid-top">
                <?php
                                   
                                $md5_key = md5("SELECT * FROM `__PREFIX__navigation` where is_show = 1 AND position = 2 ORDER BY `sort` DESC");
                                $result_name = $sql_result_vv = S("sql_".$md5_key);
                                if(empty($sql_result_vv))
                                {                            
                                    $result_name = $sql_result_vv = \think\Db::query("SELECT * FROM `__PREFIX__navigation` where is_show = 1 AND position = 2 ORDER BY `sort` DESC"); 
                                    S("sql_".$md5_key,$sql_result_vv,31104000);
                                }    
                              foreach($sql_result_vv as $kk=>$vv): ?>
                    <a href="<?php echo $vv[url]; ?>" <?php if($vv[is_new] == 1): ?> target="_blank" <?php endif; ?> ><?php echo $vv[name]; ?></a><span>|</span>
                <?php endforeach; ?>
		    </div>
		    <p>Copyright © 2016-2025 <?php echo (isset($tpshop_config['shop_info_store_name']) && ($tpshop_config['shop_info_store_name'] !== '')?$tpshop_config['shop_info_store_name']:'TPshop商城'); ?> 版权所有 保留一切权利 备案号:<a href="http://www.beian.miit.gov.cn" ><?php echo $tpshop_config['shop_info_record_no']; ?></a></p>
		    <p class="mod_copyright_auth">
		        <a class="mod_copyright_auth_ico mod_copyright_auth_ico_1" href="" target="_blank">经营性网站备案中心</a>
		        <a class="mod_copyright_auth_ico mod_copyright_auth_ico_2" href="" target="_blank">可信网站信用评估</a>
		        <a class="mod_copyright_auth_ico mod_copyright_auth_ico_3" href="" target="_blank">网络警察提醒你</a>
		        <a class="mod_copyright_auth_ico mod_copyright_auth_ico_4" href="" target="_blank">诚信网站</a>
		        <a class="mod_copyright_auth_ico mod_copyright_auth_ico_5" href="" target="_blank">中国互联网举报中心</a>
		    </p>
		</div>
    </div>
</div>
<script>
    // 延时加载二维码图片
    jQuery(function($) {
        $('img[img-url]').each(function() {
            var _this = $(this),
                    url = _this.attr('img-url');
            _this.attr('src',url);
        });
    });
</script>
<script>

    $(document).ready(function(){
		 $('input').click(function(){
		      $(this).siblings('p').hide();
		 });
	});
    $(function () {
        $(document).on("click", '#submit', function (e) {
            var isSubmit = check_submit();
            if(isSubmit){
                $.ajax({
                    type : "POST",
                    url:"<?php echo U('Home/User/reg'); ?>",
                    dataType: "json",
                    data: $('#reg_form2').serialize(),
                    success: function(data){
                        if(data.status == 1){
                            layer.msg(data.msg, {icon: 1},function(){
                                window.location.href = "<?php echo U('Home/Index/index'); ?>";
                            });
                        }else{
                            layer.alert(data.msg, {icon: 2},function(index){
                                $('.verifyImg').trigger('click');
                                layer.close(index);
                            });
                        }
                    }
                });
            }
        })
    })
 
	// 普通 图形验证码 
    function verify(id){
        $('#'+id).attr('src','/index.php?m=Home&c=User&a=verify&type=user_reg&r='+Math.random());
    }
    function check_submit(){
        var username = $('input[name="username"]').val();
        var password = $('#password').val();
        var password2 = $('#password2').val();
        var verify_code = $('input[name="verify_code"]').val();		
        var agree = $('input[type="checkbox"]:checked').val();
        var error = '';

		$("p[id^='err_']").each(function(){
			$(this).hide();
		});			
		
	   (username == '') && $('#err_username').show();
	   ($.trim($('#code').val()) == '') && $('#err_code').show();
	   (password == '' || password.length < 6) && $('#err_password').show();
	   (password2 != password) && $('#err_password2').show();
	   (verify_code == '') && $('#err_verify_code').show();
        (agree != 'on') && $('#err_check_code').show();
		if($('#username').hasClass('fmobile')){
			if(!checkMobile(username)){
				$('#err_username').show();
			}
		}else{
			if(!checkEmail(username)){
				$('#err_username').show();
			}
		}
	   if($("p[id^='err_']:visible").length > 0 ) {
           return false;
       }else{
           return true;
       }
    }
	// 电子邮件注册  和 手机号码注册 切换
    function reg_tab(id,id2){
        $('#'+id).addClass('ema-tab');
        $('#'+id2).removeClass('ema-tab');
        $('#'+id+'_div').show();
        $('#'+id2+'_div').hide();
    }
	// 发送手机短信
    function send_sms_reg_code(){
        var mobile = $('input[name="username"]').val();
        var verify_code = $('input[name="verify_code"]').val();
        if(!checkMobile(mobile)){
            layer.alert('请输入正确的手机号码', {icon: 2});//alert('请输入正确的手机号码');
            return;
        }
        if(verify_code == ''){
            layer.alert('请输入图像验证码', {icon: 2});//alert('请输入正确的手机号码');
            return;
        }
        var url = "/index.php?m=Home&c=Api&a=send_validate_code&scene=1&type=mobile&mobile="+mobile+"&verify_code="+verify_code;
        $.ajax({
            type : "GET",
            url:url,
            dataType: "json",
            success: function(data){
                if(data.status == 1){
                	$('#count_down').attr("disabled","disabled");
    				intAs = <?php echo $sms_time_out; ?>; // 手机短信超时时间
                    jsInnerTimeout('count_down',intAs);
                    layer.alert(data.msg, {icon: 1});
                }else{
                	layer.alert(data.msg, {icon: 2});
                }
            }
        });
    }

    // 发送邮箱
    function send_smtp_reg_code(){
        var email = $('input[name="username"]').val();
        var verify_code = $('input[name="verify_code"]').val();
        if(!checkEmail(email)){
            layer.alert('请输入正确的邮箱', {icon: 2});//alert('请输入正确的手机号码');
            return;
        }
        if(verify_code == ''){
            layer.alert('请输入图像验证码', {icon: 2});//alert('请输入正确的手机号码');
            return;
        }
        $.ajax({
            type : "POST",
            url:"<?php echo U('Home/Api/send_validate_code'); ?>",
            data : {type:'email',send:email,scene:1,verify_code:verify_code},// 你的formid
            dataType: "json",
            success: function(data){
                if(data.status == 1){
                    $('#count_down').attr("disabled","disabled");
                    intAs = <?php echo $sms_time_out; ?>; // 发送邮箱超时时间
                    jsInnerTimeout('count_down',intAs);
                    layer.alert(data.msg, {icon: 1});
                }else{
                    layer.alert(data.msg, {icon: 2});
                }
            }
        });
    }

    $('#count_down').removeAttr("disabled");
    //倒计时函数
    function jsInnerTimeout(id,intAs)
    {
        var codeObj=$("#"+id);
        //var intAs = parseInt(codeObj.attr("IntervalTime"));

        intAs--;
        if(intAs<=-1)
        {
            codeObj.removeAttr("disabled");
//            codeObj.attr("IntervalTime",60);
            codeObj.text("发送");
            return true;
        }

        codeObj.text(intAs+'秒');
//        codeObj.attr("IntervalTime",intAs);

        setTimeout("jsInnerTimeout('"+id+"',"+intAs+")",1000);
    };
    
    
    function checkMobile(tel) {
        var reg = /^1[0-9]{10}$/;
        if (reg.test(tel)) {
            return true;
        }else{
            return false;
        };
    }
    
    function checkEmail(str){
        var reg = /^([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
        if(reg.test(str)){
            return true;
        }else{
            return false;
        }
    }
    $(document).on('keyup', '#password', function() {
        var password = md5($("input[name='auth_code']").val() + this.value);
        $('input[name="password"]').val(password);
    })
    $(document).on('keyup', '#password2', function() {
        var password2 = md5($("input[name='auth_code']").val() + this.value);
        $('input[name="password2"]').val(password2);
    })
</script>
</body> 
</html>
