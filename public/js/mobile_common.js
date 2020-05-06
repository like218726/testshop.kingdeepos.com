
/**
 * 将商品加入购物车
 * @param goods_id|商品id
 * @param num|商品数量
 * @constructor
 */
function AjaxAddCart(goods_id, num) {
	var form = $("#buy_goods_form");
	var cart_quantity = $('#tp_cart_info');
	var data;//post数据
	if (form.length > 0) {
		data = form.serialize();
	} else {
		data = {goods_id: goods_id, goods_num: num};
	}
	$.ajax({
		type: "POST",
		url: "/index.php?m=Mobile&c=Cart&a=ajaxAddCart",
		data: data,
		dataType: 'json',
		success: function (data) {
			// 加入购物车后再跳转到 购物车页面
			if (form.length > 0) {
				if (data.status == '-101') {
					layer.open({
						content: data.msg,
						btn: ['去登录', '取消'],
						shadeClose: false,
						yes: function () {
							location.href = "/index.php?m=Mobile&c=User&a=Login";
						}, no: function () {
							layer.closeAll();
						}
					});
					return false;
				}
				if (data.status == 0) {
					layer.open({content: data.msg, time: 2,end:function(){
						if(!$.isEmptyObject(data.result)){
							if(!$.isEmptyObject(data.result.url)){
								location.href = data.result.url;
								return false;
							}
						}
					}});
					return false;
				}
				var cart_num = parseInt(cart_quantity.html()) + parseInt($('#number').val());
				cart_quantity.html(cart_num);
				layer.open({
					content: '添加成功！',
					btn: ['再逛逛', '去购物车'],
					shadeClose: false,
					yes: function () {
						ajax_header_cart();
						layer.closeAll();
					}, no: function () {
						location.href = "/index.php?m=Mobile&c=Cart&a=index";
					}
				});
			} else {
				if (data.status <= 0) {
					if(!$.isEmptyObject(data.result)){
						if(!$.isEmptyObject(data.result.url)){
							location.href = data.result.url;
							return false;
						}
					}
					layer.open({content: data.msg, time: 2});
					return false;
				}
				var cart_num = parseInt(cart_quantity.html()) + parseInt(num);
				cart_quantity.html(cart_num);
				layer.open({content: data.msg, time: 1});
			}
		}
	});
}

//购买兑换商品
function buyIntegralGoods(goods_id, num){
	var form = $("#buy_goods_form");
	var data;//post数据
	if(getCookie('user_id') == ''){
		layer.open({
			content: '兑换积分商品必须先登录',
			btn: ['去登录', '取消'],
			shadeClose: false,
			yes: function () {
				location.href = "/index.php?m=Mobile&c=User&a=Login";
			}, no: function () {
				layer.closeAll();
			}
		});
		return;
	}
	if (form.length > 0) {
		data = form.serialize();
	} else {
		data = {goods_id: goods_id, goods_num: num};
	}
	$.ajax({
		type: "POST",
		url: "/index.php?m=Mobile&c=Cart&a=buyIntegralGoods",
		data: data,
		dataType: 'json',
		success: function (data) {
			if(data.status == 1){
				location.href = data.result.url;
			}else{
				if(!$.isEmptyObject(data.result)){
					if(!$.isEmptyObject(data.result.url)){
						location.href = data.result.url;
					}
				}else{
					layer.open({content: data.msg, time: 1});
				}
			}
		}
	});
}

function checkMobile(tel) {
	//var reg = /(^1[3|4|5|7|8][0-9]{9}$)/;
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

function showCheckoutOther(obj)
{
	var otherParent = obj.parentNode;
	otherParent.className = (otherParent.className=='checkout_other') ? 'checkout_other2' : 'checkout_other';
	var spanzi = obj.getElementsByTagName('span')[0];
	spanzi.className= spanzi.className == 'right_arrow_flow' ? 'right_arrow_flow2' : 'right_arrow_flow';
}

function setGoodsTab(name,cursel,n){
	$('html,body').animate({'scrollTop':0},600);
	for(i=1;i<=n;i++){
		var menu=document.getElementById(name+i);
		var con=document.getElementById("user_"+name+"_"+i);
		menu.className=i==cursel?"on":"";
		con.style.display=i==cursel?"block":"none";
	}
}

// 获取活动剩余天数 小时 分钟
//倒计时js代码精确到时分秒，使用方法：注意 var EndTime= new Date('2013/05/1 10:00:00'); //截止时间 这一句，特别是 '2013/05/1 10:00:00' 这个js日期格式一定要注意，否则在IE6、7下工作计算不正确哦。
//js代码如下：
function GetRTime(end_time){
	// var EndTime= new Date('2016/05/1 10:00:00'); //截止时间 前端路上 http://www.51xuediannao.com/qd63/
	var EndTime= new Date(end_time); //截止时间 前端路上 http://www.51xuediannao.com/qd63/
	var NowTime = new Date();
	var t =EndTime.getTime() - NowTime.getTime();
	/*var d=Math.floor(t/1000/60/60/24);
	 t-=d*(1000*60*60*24);
	 var h=Math.floor(t/1000/60/60);
	 t-=h*60*60*1000;
	 var m=Math.floor(t/1000/60);
	 t-=m*60*1000;
	 var s=Math.floor(t/1000);*/

	var d=Math.floor(t/1000/60/60/24);
	var h=Math.floor(t/1000/60/60%24);
	var m=Math.floor(t/1000/60%60);
	var s=Math.floor(t/1000%60);
	if(s >= 0){
		var data =  d + '天' + h + '小时' + m + '分' +s+'秒';
	}
	return data;
}

// 点击收藏商品
function collect_goods(goods_id){
	$.ajax({
		type : "GET",
		dataType: "json",
		url:"/index.php?m=Mobile&c=goods&a=collect_goods&goods_id="+goods_id,//+tab,
		success: function(data){
			alert(data.msg);
		}
	});
}	