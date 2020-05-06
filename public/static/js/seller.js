$(function() {
	$('.index-sitemap > a').bind("click",
	function() {
		$(".sitemap-menu-arrow").slideDown("slow");
		$(".sitemap-menu").slideDown("slow");
	});
	$('.add-quickmenu > a').bind("click",
	function() {
		$(".sitemap-menu-arrow").slideDown("slow");
		$(".sitemap-menu").slideDown("slow");
	});
	$('#closeSitemap').bind("click",
	function() {
		$(".sitemap-menu-arrow").slideUp("fast");
		$(".sitemap-menu").slideUp("fast");
	});
});

//头部导航下拉菜单相关
$(function() {
		$(".ncsc-nav dl").hover(function() {
		$(this).addClass("hover");
	},
	function() {
		$(this).removeClass("hover");
	});

});

//返回到顶部
$(function() {
    backTop=function (btnId){
		var btn=document.getElementById(btnId);
		if(btn == null || btn == undefined)return;
		var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
		window.onscroll=set;
		btn.onclick=function (){
			btn.style.display="none";
			window.onscroll=null;
			this.timer=setInterval(function(){
			    scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
				scrollTop-=Math.ceil(scrollTop*0.1);
				if(scrollTop==0) clearInterval(btn.timer,window.onscroll=set);
				if (document.documentElement.scrollTop > 0) document.documentElement.scrollTop=scrollTop;
				if (document.body.scrollTop > 0) document.body.scrollTop=scrollTop;
			},10);
		};
		function set(){
		    scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
		    btn.style.display=scrollTop?'block':"none";
		}
	};
	backTop('gotop');
});

(function($) {
//凸显触及图片样式
	$.fn.jfade = function(settings) {

		var defaults = {
			start_opacity: "1",
			high_opacity: "1",
			low_opacity: ".1",
			timing: "500"
		};
		var settings = $.extend(defaults, settings);
		settings.element = $(this);

		//set opacity to start
		$(settings.element).css("opacity", settings.start_opacity);
		//mouse over
		$(settings.element).hover(

		//mouse in
		function() {
			$(this).stop().animate({
				opacity: settings.high_opacity
			},
			settings.timing); //100% opacity for hovered object
			$(this).siblings().stop().animate({
				opacity: settings.low_opacity
			},
			settings.timing); //dimmed opacity for other objects
		},

		//mouse out
		function() {
			$(this).stop().animate({
				opacity: settings.start_opacity
			},
			settings.timing); //return hovered object to start opacity
			$(this).siblings().stop().animate({
				opacity: settings.start_opacity
			},
			settings.timing); // return other objects to start opacity
		});
		return this;
	}
})(jQuery);


(function($) {
	$.fn.charCount = function(options){
		// default configuration properties
		var defaults = {	
			allowed: 140,		
			warning: 25,
			css: 'counter',
			counterElement: 'span',
			counterContainerID:'',
			cssWarning: 'warning',
			cssExceeded: 'exceeded',
			firstCounterText: '',
			endCounterText: '',
			errorCounterText: '',
			errortype: 'positive'	// positive or negative
		}; 
		var options = $.extend(defaults, options); 
		
		function calculate(obj){
			var count = $(obj).val().length;
			var counterText = options.firstCounterText;
			var _css = '';
			containerObj = $("#"+options.counterContainerID);
			var available = options.allowed - count;
			if(available <= options.warning && available >= 0){
				_css = options.cssWarning;
			}
			if(available < 0){
				if (options.errortype == 'positive')available = -available;
				counterText = options.errorCounterText;
				_css = options.cssExceeded;
			} else {
				counterText = options.firstCounterText;
			}
			$(containerObj).children().html(counterText +'<em class="'+ _css +'">'+ available +'</em>'+ options.endCounterText);
		};
		this.each(function() {
			$("#"+options.counterContainerID).append('<'+ options.counterElement +' class="' + options.css + '"></'+ options.counterElement +'>');
			calculate(this);
			$(this).keyup(function(){calculate(this)});
			$(this).change(function(){calculate(this)});
			$(this).focus(function(){calculate(this)});
		});
	};

})(jQuery);
