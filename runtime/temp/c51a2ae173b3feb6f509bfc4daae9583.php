<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:44:"./template/pc/rainbow/goods/ajaxComment.html";i:1587634420;}*/ ?>
<?php if(is_array($commentlist) || $commentlist instanceof \think\Collection || $commentlist instanceof \think\Paginator): $i = 0; $__LIST__ = $commentlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
<div class="people-comment">
    <div class="deta-descri p">
        <div class="person-ph-name">
            <div class="per-img-n p">
                <div class="img-aroun"><img src="<?php echo (isset($v['head_pic']) && ($v['head_pic'] !== '')?$v['head_pic']:'/template/pc/rainbow/static/images/defaultface_user_small.png'); ?>"/></div>
                <div class="menb-name">
                    <?php if($v['is_anonymous'] == 0): ?>
                    匿名用户
                    <?php else: ?>
                    <?php echo $v['nickname']; endif; ?>
                </div>
            </div>
            <!--<p class="member">金牌会员</p>-->
        </div>
        <div class="person-com">
            <div class="lifr4 p">
                <div class="dstar start5">
                    <i class="start start<?php echo floor($v['goods_rank']); ?>"></i>
                </div>
                <div class="star-aftr">
                    <?php $impression_arr= explode(',',$v['impression']);
                        if(empty($v['impression'])){
                        }else{
                            foreach($impression_arr as $key)
                            {
                            echo "<a>".$key."</a>";
                            }
                        }
                     ?>
                    <!--<a href="javascript:void(0);">非常漂亮</a>-->
                </div>
            </div>
            <div class="lifr4 comfis p">
                <span class="faisf"><?php echo htmlspecialchars_decode($v['content']); ?></span>
            </div>
            <div class="lifr4 requiimg p">
                <ul class="comment_images" id="comment_images_<?php echo $v[comment_id]; ?>">
                    <?php if(is_array($v['img']) || $v['img'] instanceof \think\Collection || $v['img'] instanceof \think\Paginator): if( count($v['img'])==0 ) : echo "" ;else: foreach($v['img'] as $key=>$v2): ?>
                        <a><li><img data-original="<?php echo $v2; ?>" src="<?php echo $v2; ?>"/></li></a>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
                <script>
                    var viewer = new Viewer(document.getElementById('comment_images_<?php echo $v[comment_id]; ?>'), {
                        url: 'data-original',
                        show: function() {
                            $('.soubao-sidebar').hide();
                        },
                        hide: function() {
                            $('.soubao-sidebar').show();
                        }
                    });
                </script>
            </div>
            <div class="lifr4 bolist p">
                <span><?php echo date("Y-m-d H:i:s",$v['pay_time']); ?></span>
                <span><?php echo htmlspecialchars_decode($v['spec_key_name']); ?></span>
                <span>购买<?php echo round(($v['add_time']-$v['pay_time'])/3600/24); ?>天后<?php echo count($v['parent_id']); ?>评价</span>
                <!--<span>来自Android客户端</span>-->
            </div>
        </div>
        <div class="g_come">
            <?php if($v['user_id'] != $user['user_id']): ?>
                <a href="javascript:void(0);"><i class="detai-ico c-cen" onclick="comment(this)"></i><?php echo (isset($v['reply_num']) && ($v['reply_num'] !== '')?$v['reply_num']:0); ?></a>
            <?php endif; ?>
            <a href="javascript:void(0);" onclick="zan(this);"  data-comment-id="<?php echo $v['comment_id']; ?>">
                <i class="detai-ico z-ten"></i><span id="span_zan_<?php echo $v['comment_id']; ?>" data-io="<?php echo (isset($v['zan_num']) && ($v['zan_num'] !== '')?$v['zan_num']:0); ?>"><?php echo (isset($v['zan_num']) && ($v['zan_num'] !== '')?$v['zan_num']:0); ?></span>
            </a>
        </div>
    </div>
    <div class="reply-textarea">
        <div class="reply-arrow"><b class="layer1"></b><b class="layer2"></b></div>
        <div class="inner">
            <textarea class="reply-input J-reply-input" data-id="replytext_<?php echo $v['comment_id']; ?>" placeholder="回复 <?php echo $v['nick_name']; ?>：" maxlength="120"></textarea>
            <div class="operate-box">
                <span class="txt-countdown">还可以输入<em>120</em>字</span>
                <a class="reply-submit J-submit-reply J-submit-reply-lz" href="javascript:void(0);" target="_self">提交</a>
            </div>
        </div>
    </div>
    <!-- 商家回复-s -->
    <?php if(is_array($v['seller_comment']) || $v['seller_comment'] instanceof \think\Collection || $v['seller_comment'] instanceof \think\Paginator): $k = 0; $__LIST__ = $v['seller_comment'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sc): $mod = ($k % 2 );++$k;?>
    <div class="comment-replylist">
        <div class="comment-reply-item hide" style="display: block;">
            <div class="reply-infor clearfix">
                <div class="main">
                            <span class="user-name" style="color: red;">商家回复
                            </span> ：
                    <span class="words"><?php echo $sc['content']; ?></span>
                </div>
                <div class="side">
                    <span class="date"><?php echo date('Y-m-d H:i:s',$sc['add_time']); ?></span>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; endif; else: echo "" ;endif; ?>
    <!-- 商家回复-d -->
    <!--用户回复评论-s-->
    <div class="comment-replylist">
        <?php if(is_array($v['parent_id']) || $v['parent_id'] instanceof \think\Collection || $v['parent_id'] instanceof \think\Paginator): $k = 0; $__LIST__ = $v['parent_id'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$reply_list): $mod = ($k % 2 );++$k;if($k < 6): ?>
        <div class="comment-reply-item hide" data-vender="0" data-name="<?php echo $reply_list['user_name']; ?>" data-uid="" style="display: block;">
            <div class="reply-infor clearfix">
                <div class="main">
                  <span class="user-name"><?php echo $reply_list['user_name']; if(strtoupper($reply_list['to_name']) != ''): ?>&nbsp;<font style="color: #1a2226">回复</font>&nbsp;<?php echo $reply_list['to_name']; endif; ?>
                  </span> ：
                    <span class="words"><?php echo $reply_list['content']; ?></span>
                </div>
                <div class="side">
                    <span class="date"><?php echo date('Y-m-d H:i:s',$reply_list['reply_time']); ?></span>
                </div>
            </div>
            <div class="comment-operate">
                <a class="reply J-reply-trigger" href="javascript:;" target="_self">回复</a>
                <div class="reply-textarea">
                    <div class="reply-arrow"><b class="layer1"></b><b class="layer2"></b></div>
                    <div class="inner">
                        <textarea class="reply-input J-reply-input" data-id="replytext_<?php echo $v['comment_id']; ?>" placeholder="回复<?php echo $reply_list['user_name']; ?>：" maxlength="120"></textarea>
                        <div class="operate-box">
                            <span class="txt-countdown">还可以输入<em>120</em>字</span>
                            <a class="reply-submit J-submit-reply J-submit-reply-lz" href="javascript:void(0);" data-id="<?php echo $reply_list['reply_id']; ?>" onclick="" target="_self">提交</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <?php endif; endforeach; endif; else: echo "" ;endif; if($v['reply_num'] > 5): ?>
        <div class="view-all-reply show">
            <a href="<?php echo U('Home/Goods/reply',array('comment_id'=>$v['comment_id'])); ?>" class="view-link reply">查看全部回复</a>
        </div>
        <?php endif; ?>
    </div>
    <!--用户回复评论-e-->
</div>
<?php endforeach; endif; else: echo "" ;endif; ?>
<div class="operating fixed" id="bottom">
    <div class="fn_page clearfix">
        <?php echo $page; ?>
    </div>
</div>
<script>
    function comment(obj){
        // console.log($(obj).parent().parent())
        $(obj).parent().parent().parent().siblings('.reply-textarea').show()
    }
</script>
