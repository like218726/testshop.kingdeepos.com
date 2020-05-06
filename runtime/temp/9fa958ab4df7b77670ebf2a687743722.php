<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:43:"./application/admin/view/index/welcome.html";i:1587634374;}*/ ?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="/public/static/css/index.css" rel="stylesheet" type="text/css">
    <link href="/public/static/css/perfect-scrollbar.min.css" rel="stylesheet" type="text/css">
    <link href="/public/static/css/purebox.css" rel="stylesheet" type="text/css">
    <link href="/public/static/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="/public/static/js/jquery.js"></script>
    <script src="/public/static/js/layer/laydate/laydate.js"></script>
    <script type="text/javascript" src="/public/static/js/jquery-ui/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/public/static/js/jquery.cookie.js"></script>
    <script src="/public/static/js/echarts/echarts-all.js"></script>
    <style>
        .contentWarp_item .section_select .item_comment{
            height: 180px;
        }
        .contentWarp_item .section_select .item {
            height: 180px;
            min-width: 270px;
        }
        .warpper i{
            margin-top: 0 !important;
        }
        @media only screen and (min-width: 900px) and (max-width: 1761px) {
        	.contentWarp_item{
        		margin-right: 1%;
        	}
        	.contentWarp_item .section_select .icon img{
        		max-width: 74px;
        		max-height: 74px;
        	}
        }
        .sc_warp div{
            height: 66px;
            line-height: 30px;
            text-align: center;
        }
        .sc_warp div p:nth-child(1){
            font-size: 28px;
            color: #333;
            font-weight: 600;
        }
        .sc_warp div p:nth-child(2){
            font-size: 14px;
            color: #666;
            line-height: 50px;
        }
    </style>
</head>
<body class="iframe_body">
<div class="warpper">
    <div class="title">管理中心</div>
    <div class="content start_content">
        <div class="contentWarp">
            <div class="contentWarp_item clearfix">
                <div class="section_select">
                    <div class="item item_price">
                        <i class="icon"><img src="/public/static/images/1.png" width="71" height="74"></i>
                        <div class="desc">
                            <div class="tit"><?php echo $count['new_order']; ?><span class="unit">(个)</span></div>
                            <span>今日新增订单数</span>
                        </div>
                    </div>
                    <div class="item item_order">
                        <i class="icon"><img src="/public/static/images/2.png"></i>
                        <div class="desc">
                            <div class="tit"><?php echo $count['new_users']; ?><span class="unit">(位)</span></div>
                            <span>今日新增会员数</span>
                        </div>
                        <i class="icon"></i>
                    </div>
                    <div class="item item_comment">
                        <i class="icon"><img src="/public/static/images/3.png" width="90" height="86"></i>
                        <div class="desc">
                            <div class="tit"><?php echo $count['comment']; ?><span class="unit">(条)</span></div>
                            <span>今日待审评论数</span>
                        </div>
                    </div>
                    <div class="item item_flow">
                        <i class="icon"><img src="/public/static/images/4.png" width="86"></i>
                        <div class="desc">
                            <div class="tit"><?php echo $count['today_login']; ?><span class="unit">(次)</span></div>
                            <span>今日访问量</span>
                        </div>
                        <i class="icon"></i>
                    </div>
                </div>
            </div>
            <div class="contentWarp_item clearfix">
                <div class="section_order_select">
                    <ul>
                        <!--<li>-->
                            <!--<a style="cursor: default;">-->
                                <!--<i class="ice ice_w"></i>-->
                                <!--<div class="t">待处理订单</div>-->
                                <!--<span class="number"><?php echo $count['handle_order']; ?></span>-->
                            <!--</a>-->
                        <!--</li>-->
                        <li>
                            <a style="cursor: default;" href="<?php echo U('Store/apply_list'); ?>">
                                <i class="ice ice_w"></i>
                                <div class="t">开店审核</div>
                                <span class="number"><?php echo $count['store']; ?></span>
                            </a>
                        </li>
                        <li>
                            <a style="cursor: default;" href="<?php echo U('Store/apply_class_list'); ?>">
                                <i class="ice ice_f"></i>
                                <div class="t">类目申请</div>
                                <span class="number"><?php echo $count['bind_class']; ?></span>
                            </a>
                        </li>
                        <li>
                            <a style="cursor: default;" href="<?php echo U('Goods/brandList'); ?>">
                                <i class="ice ice_y"></i>
                                <div class="t">品牌申请</div>
                                <span class="number"><?php echo $count['brand']; ?></span>
                            </a>
                        </li>
                        <li>
                            <a style="cursor: default;" href="<?php echo U('Goods/goodsList'); ?>">
                                <i class="ice ice_q"></i>
                                <div class="t">商品数量</div>
                                <span class="number"><?php echo $count['goods']; ?></span>
                            </a>
                        </li>
                        <li>
                            <a style="cursor: default;" href="<?php echo U('Article/articleList'); ?>">
                                <i class="ice ice_w ice_i"></i>
                                <div class="t">文章数量</div>
                                <span class="number"><?php echo $count['article']; ?></span>
                            </a>
                        </li>
                        <li>
                            <a style="cursor: default;" href="<?php echo U('User/index'); ?>">
                                <i class="ice ice_f ice_j"></i>
                                <div class="t">会员总数</div>
                                <span class="number"><?php echo $count['users']; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="clear"></div>
                <div class="section section_order_count">
                    <div class="sc_title" style="padding: 15px 0;">
                        <i class="sc_icon"></i>
                        <h3>个人会员信息</h3>
                        <p>单位（位）</p>
                    </div>
                    <div class="sc_warp" style="display: flex;justify-content: space-around;align-items: center;height: 127px;padding: 0 50px">
                        <div>
                            <p><?php echo $count['new_users']; ?></p>
                            <p>今日新增</p>
                        </div>
                        <div>
                            <p><?php echo $count['yesterday_users']; ?></p>
                            <p>昨日新增</p>
                        </div>
                        <div>
                            <p><?php echo $count['month_users']; ?></p>
                            <p>本月新增</p>
                        </div>
                        <div>
                            <p><?php echo $count['users']; ?></p>
                            <p>会员总数</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--订单统计-->
        <div class="order order_statistical">
            <div class="sc_title">
                <i class="sc_icon"></i>
                <h3>订单统计</h3>
                <ul>
                    <li class="select" time_type="1">七天</li>
                    <li time_type="2">一月</li>
                    <li time_type="3">半年</li>
                </ul>
            </div>
            <div id="statistics" style="width:100%;height: 345px;">

            </div>
        </div>
        <!--销售统计-->
        <div class="sales order_statistical">
            <div class="sc_title">
                <i class="sc_icon"></i>
                <h3>销售统计</h3>
                <ul>
                    <li class="select" time_type="1">七天</li>
                    <li time_type="2">一月</li>
                    <li time_type="3">半年</li>
                </ul>
            </div>
            <div id="sales" style="width:100%;height: 345px;">

            </div>
        </div>
        <div class="contentWarp">
            <div class="section system_section" style="float: none;width: inherit;">
                <div class="system_section_con">
                    <div class="sc_title" style="padding: 26px 0 14px;border-bottom: 1px solid #e4eaec;">
                        <i class="sc_icon"></i>
                        <h3>系统信息</h3>
                    </div>
                    <div class="sc_warp" id="system_warp" style="display: block;padding-bottom: 30px;">
                        <table cellpadding="0" cellspacing="0" class="system_table">
                            <tbody><tr>
                                <td class="gray_bg">服务器操作系统:</td>
                                <td><?php echo $sys_info['os']; ?></td>
                                <td class="gray_bg">服务器域名/IP:</td>
                                <td><?php echo $sys_info['domain']; ?> [ <?php echo $sys_info['ip']; ?> ]</td>
                            </tr>
                            <tr>
                                <td class="gray_bg">服务器环境:</td>
                                <td><?php echo $sys_info['web_server']; ?></td>
                                <td class="gray_bg">PHP 版本:</td>
                                <td><?php echo $sys_info['phpv']; ?></td>
                            </tr>
                            <tr>
                                <td class="gray_bg">Mysql 版本:</td>
                                <td><?php echo $sys_info['mysql_version']; ?></td>
                                <td class="gray_bg">GD 版本:</td>
                                <td><?php echo $sys_info['gdinfo']; ?></td>
                            </tr>
                            <tr>
                                <td class="gray_bg">文件上传限制:</td>
                                <td><?php echo $sys_info['fileupload']; ?></td>
                                <td class="gray_bg">最大占用内存:</td>
                                <td><?php echo $sys_info['memory_limit']; ?></td>
                            </tr>
                            <tr>
                                <td class="gray_bg">最大执行时间:</td>
                                <td><?php echo $sys_info['max_ex_time']; ?></td>
                                <td class="gray_bg">安全模式:</td>
                                <td><?php echo $sys_info['safe_mode']; ?></td>
                            </tr>
                            <tr>
                                <td class="gray_bg">软件版本:</td>
                                <td><?php echo $sys_info['sys_version']; ?></td>
                                <td class="gray_bg">Curl支持:</td>
                                <td><?php echo $sys_info['curl']; ?></td>
                            </tr>
                            </tbody></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="footer" style="position: static; bottom: 0px; font-size:14px;">
    <p><b>如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷. 版权所有 © 2012-2018 <?php echo (isset($tpshop_config['shop_info_store_title']) && ($tpshop_config['shop_info_store_title'] !== '')?$tpshop_config['shop_info_store_title']:'深圳搜豹网络有限公司'); ?>，并保留所有权利。</b></p>
</div>
<script type="text/javascript">
    $(function(){
        $("*[data-toggle='tooltip']").tooltip({
            position: {
                my: "left top+5",
                at: "left bottom"
            }
        });
    });
</script>
<script type="text/javascript" src="/public/static/js/jquery.purebox.js"></script>
<script type="text/javascript" src="/public/static/js/echart/echarts.min.js"></script>
<script type="text/javascript">
    //set_statistical_chart(".section_order_count .filter_date a:first", "order", "week"); //初始设置
    //set_statistical_chart(".section_total_count .filter_date a:first", "sale", "week"); //初始设置
    $(document).ready(function(){
        var orderObject = <?php echo $orderObject; ?>;
        orderHistogram(orderObject);
        salesHistogram(orderObject);
    })
    function set_statistical_chart(obj, type, date)
    {
        var obj = $(obj);
        obj.addClass("active");
        obj.siblings().removeClass("active");

        $.ajax({
            type:'get',
            url:'index.php',
            data:'act=&type='+type+'&date='+date,
            dataType:'json',
            success:function(data){
                if(type == 'order'){
                    var div_id = "order_main";
                }
                if(type == 'sale'){
                    var div_id = "total_main";
                }
                var myChart = echarts.init(document.getElementById(div_id));
                myChart.setOption(data);
            }
        })
    }

//    统计图表切换
    $(".order.order_statistical li").click(function(){
        $(this).siblings().removeClass("select");
        $(this).addClass("select");
        var time_type =$(this).attr('time_type');
        $.ajax({
            type : 'post',
            url:"<?php echo U('Admin/Index/get_order_statistic'); ?>",
            data : {time_type:time_type},
            dataType : 'json',
            success : function(data){
                orderHistogram(data);
            }
        })

    })
    $(".sales.order_statistical li").click(function(){
        $(this).siblings().removeClass("select");
        $(this).addClass("select");
        var time_type =$(this).attr('time_type');
        $.ajax({
            type : 'post',
            url:"<?php echo U('Admin/Index/get_order_statistic'); ?>",
            data : {time_type:time_type},
            dataType : 'json',
            success : function(data){
                salesHistogram(data);
            }
        })
    })

    // 订单统计图表
    function orderHistogram(orderObject) {
        var res = orderObject;
        var myChart = echarts.init(document.getElementById('statistics'),'macarons');
        option = {
            tooltip : {
                trigger: 'axis'
            },
            toolbox: {
                show : true,
                feature : {
                    mark : {show: true},
                    dataView : {show: true, readOnly: false},
                    magicType : {show: true, type: ['line', 'bar']},
                    restore : {show: true},
                    saveAsImage : {show: true}
                }
            },
            calculable : true,
            xAxis : [
                {
                    type : 'category',
                    data :  res.time
                }
            ],
            yAxis : [
                {
                    type : 'value',
                    name : '',
                    axisLabel : {
                        formatter: '{value}'
                    }
                },
            ],
            //折线
            series : [
                {
                    name:'',
                    type:'bar',
                    data:res.order,
                    symbol: 'circle',     //设定为实心点
                    symbolSize: 10,   //设定实心点的大小
                    lineStyle:{
                      normal:{
                          color:"#3B8CFF",
                          width:3,
                      }
                    },
                    itemStyle:{
                        normal:{
                            color:"#3B8CFF",
                        },
                    },
                    markPoint : {
                        data : [
                            {type : 'max', name: '最大值'},
                            {type : 'min', name: '最小值'}
                        ]
                    },
                    markLine : {
                        data : [
                            {type : 'average', name: '平均值'}
                        ]
                    }
                },
            ]
        };
        myChart.setOption(option);
    }

    // 销售统计图表
    function salesHistogram(salesObject) {
        var res = salesObject;
        var myChartsales = echarts.init(document.getElementById('sales'),'macarons');
        option = {
            tooltip : {
                trigger: 'axis'
            },
            toolbox: {
                show : true,
                feature : {
                    mark : {show: true},
                    dataView : {show: true, readOnly: false},
                    magicType : {show: true, type: ['line', 'bar']},
                    restore : {show: true},
                    saveAsImage : {show: true}
                }
            },
            calculable : true,
            xAxis : [
                {
                    type : 'category',
                    data :  res.time
                }
            ],
            yAxis : [
                {
                    type : 'value',
                    name : '',
                    axisLabel : {
                        formatter: '{value}'
                    }
                },
            ],
            //折线
            series : [
                {
                    name:'',
                    type:'bar',
                    data:res.amount,
                    symbol: 'circle',     //设定为实心点
                    symbolSize: 10,   //设定实心点的大小
                    lineStyle:{
                        normal:{
                            color:"#75C04C",
                            width:3,
                        },
                    },
                    itemStyle:{
                        normal:{
                            color:"#75C04C",
                        },
                    },
                    markPoint : {
                        data : [
                            {type : 'max', name: '最大值'},
                            {type : 'min', name: '最小值'}
                        ]
                    },
                    markLine : {
                        data : [
                            {type : 'average', name: '平均值'}
                        ]
                    }
                },
            ]
        };
        myChartsales.setOption(option);
    }

</script>
</body>

</html>
