/*
 * Public js
 */
//导航颜色
$(function(){
    var $_header=$('header');
	$(window).scroll(function(){
          var hei = $(window).scrollTop();
   	  	  if(hei > $_header.height()){
			  $_header.addClass('headerbg');
   	  	  }else{
			  $_header.removeClass('headerbg');
   	  	  };
	});
});

//回到顶部
$(function(){
	$("footer .comebackTop").click(function () {
	        var speed=300;//滑动的速度
	        $('body,html').animate({ scrollTop: 0 }, speed);
	        return false;
	});
});


//底部导航
$(function(){
	$(".footer ul li a").click(function () {
	        $(this).addClass('yello').parent().siblings().find('a').removeClass('yello')
	});
});

//radio选中
$(function(){
	$('.radio .che').click(function(){
		$(this).toggleClass('check_t');
	})
})
$(function(){
	$('.radio .all').click(function(){
		$(this).siblings().toggleClass('check_t');
	})
})


$(function(){
	//头部菜单
	$('.classreturn .menu a:last').unbind('click').click(function(e){
		$('.tpnavf').toggle();
		e.stopPropagation();
	});
	$('body').click(function(){
		$('.tpnavf').hide();
	});
	//左侧导航
	$('.classlist ul li').click(function(){
		$(this).addClass('red').siblings().removeClass('red');
	});
})

//黑色遮罩层-隐藏
function undercover(){
	$('.mask-filter-div').hide();
}
//黑色遮罩层-显示
function cover(){
	$('.mask-filter-div').show();
}
//action文件导航切换
$(function(){
	$('.paihang-nv ul li').click(function(){
		$(this).addClass('ph').siblings().removeClass('ph');
	})
})
/**
 * 留言字数限制
 * tea ：要限制数字的class名
 * nums ：允许输入的最大值
 * zero ：输入时改变数值的ID
 */
function checkfilltextarea(tea,nums){
    var len = $(tea).val().length;
    if(len > nums){
        $(tea).val($(tea).val().substring(0,nums));
    }
    var num = nums - len;
    num <= 0 ? $("#zero").text(0): $("#zero").text(num);  //防止出现负数
}

/**
 * 加减数量
 * n 点击一次要改变多少
 * maxnum 允许的最大数量(库存)
 * number ，input的id
 */
function altergoodsnum(n){
    var num = parseInt($('#number').val());
    var maxnum = parseInt($('#number').attr('max'));
	if(isNaN(num)){
		num = 1;
	}
	if(isNaN(maxnum)){
		maxnum = 1;
	}
	if(maxnum > 200){
		maxnum = 200;
	}
    num += n;
    num <= 0 ? num = 1 :  num;
    if(num >= maxnum){
        $(this).addClass('no-mins');
        num = maxnum;
    }
    $('#store_count').text(maxnum-num); //更新库存数量
    $('#number').val(num)
}