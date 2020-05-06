$(function () {
    var store_id = $("input[name='store_id']").val();
    var token = getCookie('token');
    var html = '/index.php/';
    var url = {
        'getStoreGoods':html+'api/store/storeGoodsList',
        'storeInfo':html+'api/store/storeInfo',
        'storeComment':html+'api/store/storeComment',
        'addCart':html+'api/cart/addCart',
        'CartList':html+'api/cart/CartList',
        'changeNum':html+'api/cart/changeNum',
        'delCart':html+'api/cart/delCart',
        'cart2':html+'Mobile/Cart/cart2/store_id/'+store_id,
        'goodsInfo':html+'Mobile/Goods/goodsInfo/store_id/'+store_id+'/id/',
        'collectStoreOrNo':html+'api/store/collectStoreOrNo',
        'storeCouponLis':html+'api/Activity/store_coupon_list',
        'get_coupon':html+'api/Activity/get_coupon',
    };
    CartList();
    getStoreGoods();
    storeInfo();
    storeComment();
    storeCouponLis();


    // 购物车展开
    $('.cart-icon').click(function(){
        $('.scl').toggleClass('on');
    });
    //导航切换
    $(".cont2 .tab-nav").on('click','li',function(){
        var index = $(".cont2 .tab-nav li").index(this);
        $(".cont2 .tab-nav li").removeClass("on");
        $(this).addClass("on");
        $('.cont2 .tab-box .box1').removeClass('active');
        $('.cont2 .tab-box .box1').eq(index).addClass('active');
    });
    $('.left-tab').on('click',' li',function () {
        var index = $(".left-tab li").index(this);
        $('#right_list').children().css('display','none');
        $('#right_list').children().eq(index).css('display','block');
        $(".left-tab li").removeClass("on");
        $(this).addClass("on");
        $('.tab-box .right').css('dispaly','none');
    });

    /**
     * 领取优惠券
     * */
    $('.quan-list').on('click','.btn',function () {
        console.log(12312)
        var coupon_id = $(this).parents('.list').data('id');
        var send_num = $(this).parents('.list').data('send_num');
        var isget = $(this).parents('.list').data('isget');
        var createnum = $(this).parents('.list').data('createnum');
        console.log(send_num)
        console.log(createnum)
        console.log(isget)
        if(send_num >= createnum || isget==1){
            return false;
        }
        $.ajax({
            type: "POST",
            url: url.get_coupon,
            dataType: 'json',
            data: {token: token,coupon_id: coupon_id},
            success: function (data) {
                console.log(data)
                if(data.status ==1){

                }

                layer.open({content:data.msg,skin: 'msg',time:2});
            }
        });

    });

    /**
     * 关注
     * */
    $('.set-up').on('click',function () {
        $.ajax({
            type: "POST",
            url: url.collectStoreOrNo,
            dataType: 'json',
            data: {token: token,store_id: store_id},
            success: function (data) {
                if(data.status ==1){
                    if( $('.set-up').attr('data-status') == 0){
                        //改变收藏
                        $('.set-up').attr('data-status',1);
                        $('.set-up').css('background-image','url(/template/mobile/default/static/images/o2o/collect_fill.png)');
                    }else{
                        $('.set-up').attr('data-status',0);
                        $('.set-up').css('background-image','url(/template/mobile/default/static/images/o2o/dpsy-icon03.png)');
                    }
                }
                layer.open({content:data.msg,skin: 'msg',time:2});
            }
        });

    });
    //加法
    $('#right_list').on('click','.add',function () {
        if($(this).parents('li').find('.spec_select').length){
            //有规格进来
            $(this).parents('li').find('.spec_select').show();
            $(this).parents('li').find('.spec_background').show();
            return false;
        }
        var goods_id = $(this).parents('li').data('goods-id');
        var index = $(this).parents('.right').index();
        addCart($(this),index,goods_id,1,0,store_id)
    });
    //购物车加法
    $('.scl-list').on('click','.add',function () {
        var li = $(this).parents('li');
        var item_id = li.data('item-id');
        var goods_id = li.data('goods-id');
        var index = '';
        var goods_index = '';
        //获取下标
        $.each($('#right_list').find('.right'),function (k,v) {
            $.each( $(v).find('li'),function (kk,vv) {
                if(goods_id == $(vv).data('goods-id')){
                    index = k;
                    goods_index = kk;
                    return false;
                }
            });
        });
        addCart($(this),index,goods_id,1,item_id,store_id,goods_index)
    });

    /**选着多规格*/
    $('#right_list').on('click','.sepc-commit',function () {
        var spec = [];
        var item_id = 0;
        var li = $(this).parents('li');
        $.each($(this).parent().find('.bgspec'),function (k,v) {
            spec.push($(v).data('id'))
        });
        spec.sort();
        var spec_str = spec.join('_');
        var spec_goods_price = JSON.parse(li.find("input[name='spec_goods_price']").val());
        var goods_id = li.data('goods-id');
        $.each(spec_goods_price,function (k,v) {
           if(v['key'] == spec_str){
               item_id =  v['item_id'];
               return false;
           }
        });
        var index = li.parents('.right').index();

        addCart($(this),index,goods_id,1,item_id,store_id);
    });

    /**添加购物车统一调用*/
    function addCart(obj,index,goods_id,goods_num,item_id,store_id,goods_index){
        $.ajax({
            type: "POST",
            url: url.addCart,
            dataType: 'json',
            data: {token: token,goods_id: goods_id,goods_num: goods_num,item_id: item_id,store_id: store_id},
            success: function (data) {
                if(data.status >0){
                    var num = Number($(obj).parents('li').find('.number').text());
                    if(item_id > 0){
                        if($(obj).parents('li').find('.sd-price').length>0){
                            var price = Number($(obj).parents('li').find('.sd-price').text());
                        }else{
                            var price = Number($(obj).parents('li').find('.money').children().last().text());
                        }
                    }else{
                        var price = Number($(obj).parents('li').find('.money').children().last().text());
                    }
                    num ++;
                    if(num == 1){
                        $(obj).parents('li').find('.number').show();
                        $(obj).parents('li').find('.subduction').show();
                    }
                    $(obj).parents('li').find('.number').text(num);
                    $('#right_list').find('.right').eq(index).find('li').eq(goods_index).find('.number').text(num);
                    changeLeftTab(index,1);
                    changeCart(1,price);
                    CartList();

                }
                layer.open({content:data.msg,skin: 'msg',time:2});

            }
        });
    }

    /**
     * 改变商品数量
     * */
    function changeNum(id,goods_num){
        $.ajax({
            type: "POST",
            url: url.changeNum,
            dataType: 'json',
            data: {token: token,cart: {id:id,goods_num:goods_num,selected:1}},
            success: function (data) {
                if(!data.status){
                    layer.open({
                        content: data.msg
                        ,skin: 'msg'
                        ,time: 2 //2秒后自动关闭
                    });
                }
            }
        });
    }

    /**
     * 减法
     */
    $('#right_list').on('click','.subduction',function () {
        var li = $(this).parents('li');
        if(li.find('.spec_select').length){
            layer.open({content: '多规格商品只能在购物车删除',skin: 'msg', time: 3});
            return false;
        }
        var index = li.parents('.right').index();
        subduction($(this),index)
    });

    /**
     * 减法
     */
    $('.scl-list').on('click','.subduction',function () {
        var li = $(this).parents('li');
        var goods_id = li.data('goods-id');
        var index = '';
        var goods_index = '';
        //获取下标
        $.each($('#right_list').find('.right'),function (k,v) {
            $.each( $(v).find('li'),function (kk,vv) {
                if(goods_id == $(vv).data('goods-id')){
                    index = k;
                    goods_index = kk;
                    return false;
                }
            });
        });
        subduction($(this),index,goods_index)
    });

    function subduction(obj,index,goods_index){
        var li = $(obj).parents('li');
        var goods_id = li.data('goods-id');
        var num = Number(li.find('.number').text());
        var price = Number(li.find('.money').children().last().text());
        num--;
        if(num >= 1){
            li.find('.number').text(num);
            $('#right_list').find('.right').eq(index).find('li').eq(goods_index).find('.number').text(num);
        }else{
            li.find('.number').text(0);
            li.find('.number').hide();
            li.find('.subduction').hide();
            $('#right_list').find('.right').eq(index).find('li').eq(goods_index).find('.number').text(0);
            $('#right_list').find('.right').eq(index).find('li').eq(goods_index).find('.number').hide();
            $('#right_list').find('.right').eq(index).find('li').eq(goods_index).find('.subduction').hide();
        }
        changeNum($('#'+goods_id).data('id'),num);
        changeLeftTab(index,-1);
        changeCart(-1,price);
        CartList();

    }


    /**评论*/
    $('.eva-num').on('click','span',function () {
        commentType = $(this).data('id');
        $('.eva-num').children().removeClass('eva-act');
        $(this).addClass('eva-act');
        page = 1;
        storeComment(commentType,1);
    });
    /**
     * 提交订单
     * */
    $('.shop-cart').on('click','.btn',function () {
        window.location.href = url.cart2;
        console.log($(this))
    });

    $('.del').on('click',function () {
        var cart_list = $('.scl-list').find('li');
            if(!cart_list.length){
                return false;
            }
        //询问框
        layer.open({
            content: '您确定要清空购物车？'
            ,btn: ['确定', '不要']
            ,yes: function(index){
                var ids = [];
                $.each(cart_list,function (k,v) {
                    ids.push($(v).data('id'));
                });
                delCart(ids.join(','));
            }
        });
    });

    /**
     *  清空购物车
     **/
    function delCart(ids='') {
        $.ajax({
            type: "POST",
            url: url.delCart,
            dataType: 'json',
            data: {token: token,ids: ids},
            success: function (data) {
                if(data.status == 1){
                    initial();
                    layer.open({
                        content: data.msg
                        ,skin: 'msg'
                        ,time: 2 //2秒后自动关闭
                    });
                }
            }
        });
    }

    /**
     *  初始化购物车和商品
     **/
    function initial() {
        $('#scl-num').text('(已选择0件商品)');
        $('.mark').removeClass('on');
        $('.scl-list').html('');
        $('.shop-cart .cart-icon').find('span').hide();
        $('.shop-cart .shop-num').hide();
        $('.left-tab').find('.num').remove();
        $('.number').text(0);
        $('.shop-num').hide();
        $('.shop-num').find('p').children().last().text(0);
        $('.shop-cart-num').hide();
        $('.shop-cart-num').text(0);
        $('.subduction').hide();
    }
    $('.mark ').on('click',function () {
        // $('.mark').removeClass('on');
    });

    /**改变左边商品分类*/
    function changeLeftTab(index,num) {
        var lis = $('.left-tab').children().eq(index);
        var html = '<a class="num">'+num+'</a>';
        if(lis.find('.num').length){
            if(num >= 1){
                lis.find('.num').text(Number(lis.find('.num').text()) + Number(num));
            }else{
                lis.find('.num').text(Number(lis.find('.num').text())-Math.abs(num));
            }
        }else{
            lis.append(html);
        }
        if(Number(lis.find('a').text()) == 0){
            lis.find('.num').remove();
        }
    };

    //购物车数量
    function changeCart(num,price) {
        var cart_num = Number($('.shop-cart .cart-icon').find('span').text());
        var cart_price = Number($('.shop-cart .shop-num').find('p').children().last().text());
        if(num > 0){
            $('.shop-cart .cart-icon').find('span').text(cart_num+Number(num));
            $('.left-tab').children().eq(0).find('.num').text(cart_num+Number(num));
            $('.shop-cart .shop-num').find('p').eq(0).children().last().text((cart_price + price).toFixed(2))
        }else{
            $('.shop-cart .cart-icon').find('span').text(cart_num - Math.abs(num));
            $('.left-tab').children().eq(0).find('.num').text(cart_num - Math.abs(num));
            $('.shop-cart .shop-num').find('p').eq(0).children().last().text((cart_price - price).toFixed(2))
        }
        //显示隐藏购物车数量
        if($('.shop-cart .cart-icon').find('span').text()>=1){
            $('.shop-cart .cart-icon').find('span').show();
            $('.shop-cart .shop-num').show();
        }else{
            $('.shop-cart .cart-icon').find('span').hide();
            $('.shop-cart .shop-num').hide();
        }

    }
    //获取店铺商品列表
    function getStoreGoods() {
        $.ajax({
            type: "get",
            url: url.getStoreGoods,
            dataType: 'json',
            data: {token: token,store_id: store_id},
            success: function (data) {
                if(data.status == 1){
                    var cat_html ='';
                    var j = 0;
                    $.each(data.result, function(i, item){
                        var on = j==0?'on':'';
                        cat_html += "<li class='"+on+"' id="+item['cat_id']+"><a href='#aaa'>"+item['cat_name']+"</a>";
                        if(item['total_num']){
                            cat_html += "<a class='num'>"+item['total_num']+"</a>";
                        }
                        cat_html += "</li>";
                        j++;
                        right_list(on,item['goods'])
                    });
                    $('.left-tab').html(cat_html);
                }
            }
        });
    }

    //遍历商品
    function right_list(on,goods) {
        on = on?'':'display:none';

        var html = '' +
            '<div class="right" style="'+on+'">' +
            '<div class="right-tab">' +
            //'<p>"+item['cat_name']+"</p>'+
            '<p></p>'+
            '<ul class="right-list">';
        $.each(goods, function(i, item){
            var support =item['own_name']?'':'none';
            var select_num =item['select_num']?'':'none';
            html += '<li data-goods-id="'+item['goods_id']+'">' +
            '<a href="'+url.goodsInfo+item['goods_id']+'"><img src="'+item['original_img']+'" alt=""> </a>' +
            '<div class="li-right">' +
            '<a href="'+url.goodsInfo+item['goods_id']+'"><p class="p1">'+item['goods_name']+'<span class="self-support" style="display: '+support+'">'+item['own_name']+'</span></p></a>' +
            // '<p class="p2">此处填写其他信息，可不填</p>' +
            // '<p class="p2"><span>月售'+item['sales_sum']+'</span><span class="hpl">好评率98%</span></p>' +
            '<p class="p2"><span>已售出'+item['sales_sum']+'</span><span class="hpl">库存：'+item['store_count']+'</span></p>' +
            '<div class="num">' +
            '<p class="money"><span>￥</span><span>'+item['shop_price']+'</span></p>' +
            '<div>' +
            '<span class="subduction" style="display: '+select_num+'"></span>' +
            '<span class="number" style="display: '+select_num+'">'+item['select_num']+'</span>' +
            '<span class="add"></span></div></div></div>';
            //遍历规格
            html += eachSpec(item);
             html += '</li>';

        });

        html += '</ul></div></div>';

        $('#right_list').append(html);

    }
    //获取店铺信息
    function storeInfo() {
        $.ajax({
            type: "post",
            url: url.storeInfo,
            dataType: 'json',
            data: {token: token,store_id: store_id},
            success: function (data) {
                if(data.status == 1){
                    $('.wrap .cont1 .box1 .right').append('<p>'+data.result.store_name+'</p>');
                    $('.wrap .cont1 .box1 .right').append('<p>销量：'+data.result.store_sales+'单</p>');
                    $('.shop-box1-list').children().eq(0).find('span').text(data.result.store_name);
                    $('.shop-box1-list').children().eq(1).find('span').last().text(data.result.store_phone);
                    $('.shop-box1-list').children().eq(2).find('span').last().text(data.result.store_address);
                    $('.shop-box1-list').children().eq(2).find('span').last().text(data.result.store_address);
                    $('.cont1').find('.box1 img').attr('src',data.result.store_logo);
                    if(data.result.is_collect){
                        //改变收藏
                       $('.set-up').attr('data-status',1);
                       $('.set-up').css('background-image','url(/template/mobile/default/static/images/o2o/collect_fill.png)');
                    }else{
                        $('.set-up').attr('data-status',0);
                        $('.set-up').css('background-image','url(/template/mobile/default/static/images/o2o/dpsy-icon03.png)');
                    }
                }
            }
        });
    }

    //获取店铺评论
    var  page = 1;
    function storeComment(commentType=1,type=0) {
        $.ajax({
            type: "post",
            url: url.storeComment,
            dataType: 'json',
            data: {token: token,store_id: store_id,commentType:commentType,p:page},
            success: function (data) {
                if(data.status == 1){
                    $('.evaluate .eva-all').find('i').text(data.result.nav.total_sum);
                    // $('.eva-t-right').find('p').last().find('span').last().text(data.result.nav.total_sum+'条');
                    $('.evaluate .eva-good').find('i').text(data.result.nav.high_sum);
                    $('.evaluate .eva-medium').find('i').text(data.result.nav.center_sum);
                    $('.evaluate .eva-bad').find('i').text(data.result.nav.low_sum);
                    $('.evaluate .eva-pic').find('i').text(data.result.nav.img_sum);
                    $('.eva-t-left').find('span').eq(0).text(data.result.nav.colligate_score);
                    $('.eva-t-right').children().eq(0).children().eq(1).html(score(data.result.nav.store_desccredit));
                    $('.eva-t-right').children().eq(0).children().eq(2).text(data.result.nav.store_desccredit);
                    $('.eva-t-right').children().eq(1).children().eq(1).html(score(data.result.nav.store_servicecredit));
                    $('.eva-t-right').children().eq(1).children().eq(2).text(data.result.nav.store_servicecredit);
                    $('.eva-t-right').children().eq(2).children().eq(1).html(score(data.result.nav.store_deliverycredit));
                    $('.eva-t-right').children().eq(2).children().eq(2).text(data.result.nav.store_deliverycredit);
                    var html ='';
                    $.each(data.result.list, function(i, item){
                     html += '<div class="list">' +
                        '<img src="__STATIC__/images/o2o/test.png" alt="" class="portrait">' +
                        '<div class="list-right">' +
                        '<p class="p1">' +
                        '<span>'+item['nickname']+'</span><span>'+getTime(item['add_time'])+'</span>' +
                        '</p>' +
                        '<p class="list-stars">';
                        html += score(item['goods_rank']);
                        html += '</p>' +
                        '<p class="p2">'+item['content']+'</p>' +
                        '<div class="img">';
                         $.each(item['img'], function(ii, img){
                             html += '<img src="'+img+'" alt="">';
                         });

                        html +=  '</div>' +
                        '</div>' +
                        '</div>';
                    });

                    if(type){
                        $('.eva-list').html(html);
                    }else{
                        $('.eva-list').append(html);
                    }

                }
            }
        });
    }

    var one = 1;
    function CartList() {
        $.ajax({
            type: "post",
            url: url.CartList,
            dataType: 'json',
            data: {token: token,store_id: store_id},
            success: function (data) {
                if(data.status == 1){
                    if(data.result.cartList){
                        var html = '';
                        var price = 0;
                        var num = 0;
                        $('#scl-num').text('（已选'+data.result.cartList.length+'件商品）');
                        $.each(data.result.cartList, function(i, item){
                            html += '<li id="'+item['goods_id']+'" data-item-id="'+item['item_id']+'" data-id="'+item['id']+'" data-goods-id="'+item['goods_id']+'">' +
                                '<label>'+item['goods_name']+item['spec_key_name']+'</label>' +
                                '<div class="money"><span>￥</span><span>'+item['goods_price']+'</span></div>' +
                                '<div class="add-sub">' +
                                '<span class="subduction"></span>' +
                                '<span class="number">'+item['goods_num']+'</span>' +
                                '<span class="add"></span>' +
                                '</div>' +
                                '</li>';
                            num = num + Number(item['goods_num']);
                            price = price + (Number(item['goods_price']) * Number(item['goods_num']));
                        });
                        $('.scl-list').html(html);
                        if(one){
                            changeCart(num,price);
                            one = 0;
                        }
                    }
                }
            }
        });
    }

    //初始化已选的商品
    // function setGoodsnum(){
    //     var allGoods = $('#right_list').find('.right');
    //     var cartList = $('.scl-list').find('li');
    //     var price = 0;
    //     var num = 0;
    //     $.each(cartList,function (cart_k,cart_v) {
    //         $.each(allGoods,function (goods_k,goods_v) {
    //             var goods = $(goods_v).find('li');
    //             $.each(goods,function (g_k,g_v) {
    //                 if($(g_v).data('goods-id') == $(cart_v).data('goods-id') ){


    //                     num = num + Number($(cart_v).find('.number').text());
    //                     price = price + (Number($(cart_v).find('.money').children().last().text()) * $(cart_v).find('.number').text());
    //                 }
    //
    //             });
    //         });
    //     });
    //     changeCart(num,price)
    //
    // }

    function storeCouponLis(){

        $.ajax({
            type: "post",
            url: url.storeCouponLis,
            dataType: 'json',
            data: {token: token,store_id: store_id},
            success: function (data) {
                if(data.status == 1){
                    if(data.result.coupon_list){
                        $('.more').text(data.result.coupon_list.length+'个优惠券');
                        var html = '';
                        var html_str= [];
                        $.each(data.result.coupon_list, function(i, item){
                            var condition = '';
                            if(item['condition'] > 0){
                                condition = '满'+parseInt(item['condition'])+'可用';
                                html_str.push('满'+parseInt(item['condition'])+'减'+parseInt(item['money'])) ;
                            }
                            var background_image = 'url(/template/mobile/default/static/images/o2o/dpsy-icon07.png)';

                            if(item['send_num'] >= item['createnum']){
                                background_image = 'url(/template/mobile/default/static/images/o2o/coupon_end.png)';
                            }
                            if(item['isget']){
                                background_image = 'url(/template/mobile/default/static/images/o2o/coupon_user.png)';
                            }
                            html += '<div class="list" style="background-image: '+background_image+'" data-isget="'+item['isget']+'" data-id="'+item['id']+'" data-send_num="'+item['send_num']+'" data-createnum="'+item['createnum']+'"><div class="left"><span>￥</span><span>'+parseInt(item['money'])+'</span></div><div class="right">' +
                                '<p style="font-size: .65rem">'+item['name']+'</p><p style="transform: scale(.8);margin-left: -.5rem;">'+condition+'</p><p>'+getTime(item['use_start_time'],'ymd','.')+'-'+getTime(item['use_end_time'],'ymd','.')+'</p></div><div class="btn"></div></div>';
                        });
                        $('.quan-list').html(html);
                        $('#man_all').append(html_str.join(','));
                        $('#man_all').show();

                    }
                }
            }
        });
    }

//关闭规格弹窗
    $(document).on('click','.spec-close,.sepc-commit',()=>{
        $('.spec_background,.spec_select').hide();
    });

    /**
     * 切换规格
     * */
    $('#right_list').on('click','.sb-item',function(){
        $(this).addClass('bgspec').siblings().removeClass('bgspec');
        var spec = [];
        var key_name = '';
        var price = 0;
        var li = $(this).parents('li');
        $.each($(this).parents('.spec_select').find('.bgspec'),function (k,v) {
            spec.push($(v).data('id'))
        });
        spec.sort();
        var spec_str = spec.join('_');
        console.log(spec_str)
        var spec_goods_price = JSON.parse(li.find("input[name='spec_goods_price']").val());
        $.each(spec_goods_price,function (k,v) {
            if(v['key'] == spec_str){
                key_name =  v['key_name'];
                price =  v['price'];
                return false;
            }
        });
        $(this).parents('.spec_select').find('.sd-selected').text('已选：'+key_name);
        $(this).parents('.spec_select').find('.sd-price').text(price);
    });

    //遍历规格
    function eachSpec(spec) {
        if(spec['spec_goods_price'][0]){
            var html = '<input type="hidden" name="spec_goods_price" value=\''+JSON.stringify(spec['spec_goods_price'])+'\' ><div class="spec_background" style="display: none"></div>' +
                '<div class="spec_select" style="display: none">' +
                '<div class="spec-close">×</div>' +
                '<div class="spec-top">' +
                '<div class="spec-order-img"><img src="" alt=""></div>' +
                '<div class="sepc-detail">' +
                '<p class="sd-title">'+spec['goods_name']+'</p>' +
                '<p class="sd-selected">已选：'+spec['spec_goods_price'][0]['key_name']+'</p>' +
                '<div class="sd-wrap"><span class="sd-em">￥</span><span class="sd-price">'+spec['spec_goods_price'][0]['price']+'</span></div>' +
                '</div>' +
                '</div>';
            $.each(spec['spec_list'], function(i, item) {
                html +=  '<div class="spec-bottom">' +
                    '<p class="sb-title">'+item['name']+'</p>'
                    $.each(item['spec_item'], function(k, v) {
                        var bgspec = k==0?'bgspec':'';
                        html +=  '<span class="sb-item '+bgspec+'" data-id="'+v['id']+'" data-spec-id="'+v['spec_id']+'">'+v['item']+'</span>';
                    });
                html += '</div>';
            });
            html +=  '<span class="sepc-commit">选好了</span>' +
                '</div>';

            return html;
        }
        return '';
    }










    //判断是否登录
    function is_login() {
            var user_id = getCookie('user_id');
    }
    var commentType = 1 ;
    function ajax_sourch_submit() {
        if($('.evaluate').attr('class') == 'box1 evaluate active'){
            //加载评论分页
            commentType = $('.eva-num').find('.eva-act').data('id');
            page += 1;
            storeComment(commentType,0);
        }
    }
    function getTime(t,hms='',s='-') {
        // 比如需要这样的格式 yyyy-MM-dd hh:mm:ss
        var date = new Date(t* 1000);
        console.log(date)
        Y = date.getFullYear() + s;
        M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + s;
        D = date.getDate();
        h =  ' '+date.getHours();
        m = ':'+date.getMinutes();
        s = ':'+date.getSeconds();
        // console.log(Y+M+D+h+m+s);
// 输出结果：2014-04-23 18:55:49
        switch (hms)
        {
            case 'ymdhms':
                return Y+M+D+h+m+s;
                break;
            case 'ymd':
                return Y+M+D;
                break;
            default:
                return Y+M+D+h+m;
        }


    }


    //评分
    function score(num){
        var on = '<i class="stars-on"></i>';
        var half = '<i class="stars-half"></i>';
        var off = '<i class="stars-off"></i>';
        var html = '';
        for (var i=1;i<= 5;i++){
            if(num >= i){
                html +=  on;
            }else{
                if(i-1 < num &&　num < i){
                    html +=  half;
                }else{
                    html +=  off;
                }
            }
        }

       return html;
    }






    //滚动加载更多
    $(window).scroll(
        function() {
            var scrollTop =parseInt($(this).scrollTop());
            var scrollHeight = parseInt($(document).height());
            var windowHeight = parseInt($(this).height());
            if (scrollTop + windowHeight >= scrollHeight-20) {
                ajax_sourch_submit();//调用加载更多
            }
        }
    );

});