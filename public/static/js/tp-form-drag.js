/**
 * Created by soubao on 2018/8/24.
 */
/*用自定义属性data-eidtid记录区分打开的diy编辑触发器*/
var dataEidtid = ['tpd-singleText', 'tpd-multiText', 'tpd-radioButton', 'tpd-checkBox', 'tpd-dropDown', 'tpd-picture'];
var diyHtml = {
    'tpd-singleText': ['<div class="tpd-singleText-cont"><h3 class="singleText-cont-title TextName">单文本</h3><p class="singleText-cont-p"></p><div class="singleText-cont-input cont-list"><input type="text" data-required="" /></div></div>'],
    'tpd-multiText': ['<div class="tpd-multiText-cont"><h3 class="multiText-cont-title TextName">多文本</h3><div class="multiText-cont-textarea cont-list" ><textarea ></textarea></div> </div>'],
    'tpd-radioButton': ['<div class="tpd-radioButton-cont"><h3 class="radioButton-cont-title TextName">单选按钮</h3><div class="radioButton-cont-list cont-list"><div class="radioButton-cont-radio"><input type="radio" checked="checked" /><label>手机</label></div><div class="radioButton-cont-radio"><input type="radio" /><label>电脑</label></div><div class="radioButton-cont-radio"><input type="radio" /><label>冰箱</label></div>'],
    'tpd-checkBox': ['<div class="tpd-checkBox-cont"><h3 class="checkBox-cont-title TextName">复选按钮</h3><div class="checkBox-cont-list cont-list"><div class="checkBox-cont-check"><input type="checkbox" checked="checked" /><label>手机</label></div><div class="checkBox-cont-check"><input type="checkbox" checked="checked" /><label>电脑</label></div><div class="checkBox-cont-check"><input type="checkbox" /><label>冰箱</label></div>'],
    'tpd-dropDown': ['<div class="tpd-dropDown-cont"><div class="tpd-dropDown-style"><div class="tpd-cstyle-select tpd-cdiy-font28"><h3 class="dropDown-cont-title TextName">下拉框</h3><div class="tpd-select-wrap"><span>手机号码</span><ul class="tpd-select-list cont-list"><li singleTex-li="手机号码">手机号码</li><li singleTex-li="电话号码">电话号码</li><li singleTex-li="邮政编码">邮政编码</li></ul></div></div></div></div>'],
    'tpd-picture': ['<div class="tpd-picture-cont"><h3 class="picture-cont-title TextName">图片</h3><div class="picture-cont-img cont-list"><a href="">+<span>上传图片<span></a>']
}
//手机视图容器
var objH = (function getEle() {
    var obj = {};
    obj.viewScroll = $('.tpd-mobile-scroll'); //手机视图滚动元素
    //手机视图容器元素
    obj.view = $('#tpd-mobile-views');
    obj.x0 = obj.view.offset().left;
    obj.y0 = obj.view.offset().top;
    obj.x1 = obj.x0 + 375;
    obj.y1 = obj.y0 + 667;
    obj.y2 = obj.y1 - 40;
    obj.y3 = obj.y0 + 40;
    //滚动条滚动标记
    obj.tog = 0;
    //拖拽模块
    obj.dragM = null;
    obj.dragM2 = null;
    return obj;
})();

/*添加模块式拖拽*/
function dragAdd() {
    $('.tpd-tool-list>li').mousedown(function (e) {
        var _this = this; //保存选择元素
        $('.tpd-tool-list>li').removeClass('draging-li');//去掉其它标记
        $(this).addClass('draging-li');//自己增加标记
        $('body').append('<div class="drag-module" id="drag-module"></div>'); //创建拖拽代替模块
        var __dragM__ = $('#drag-module');
        var xS = $(this).offset().left;
        var yS = $(this).offset().top - $(document).scrollTop();
        __dragM__.css({'left': xS, 'top': yS});
        var offsetX = e.pageX - xS;
        var offsetY = e.pageY - yS - $(document).scrollTop();
        $(document).mousemove(function (e) {
            __dragM__.css({'left': e.pageX - offsetX, 'top': e.pageY - $(document).scrollTop() - offsetY});
            if (e.pageX > objH.x0 && e.pageX < objH.x1 && e.pageY > objH.y0 && e.pageY < objH.y1) {
                isCollision(objH.view, e.pageY, e.pageY + 84, 1);
                //鼠标驱动滚动条判断
                judgePos(e.pageY, objH.viewScroll, objH.view);
            } else {
                $('#guide-box').remove();
            }
        });
        $(document).mouseup(function (e) {
            $(document).unbind('mousemove').unbind('mouseup');
            if (e.pageX > objH.x0 && e.pageX < objH.x1 && e.pageY > objH.y0 && e.pageY < objH.y1) {
                objH.view.find('.tpd-edits-hidden').removeClass('tpd-editing');
                //  创建模块
                createModule($(_this).attr('data-eidtid'));
            }
            __dragM__.remove();
        });
        return false;
    });
}

dragAdd();

/*拖拽时创建模板*/
function createModule(eidtid) {
    var html = diyHtml[dataEidtid[eidtid]][0];
    var box = $('#guide-box');
    if (eidtid == 10) { //搜索栏 和头部
//      $('.tpdm-head-wrap>.tpdm-head-js').addClass('tpdm-head-scale').html(html);
    } else {
        var ele = $('<div class="tpd-edits-hidden tpd-editing" data-eidtid="' + eidtid + '"><div class="js-code-wrap"></div><i class="tpd-editing-close"></i><div class="tpd-edit-module"></div></div>');
        ele.find('.js-code-wrap').html(html);
        box.after(ele);
    }
    box.remove();
    $('.tpd-diy-js').removeClass('diy-ac');
    $('.tpd-diy-js.' + dataEidtid[eidtid]).addClass('diy-ac');
    dragModlueEdit(ele)
    sort();//排序
}

var dragM = '<div class="tpd-guide-box" id="guide-box">模块将移动到此处</div>';

/*碰撞检测*/
function isCollision(ele, h1, h2, type) {
    if ($('#guide-box').length < 1) {
        ele.append(dragM);
    }
    ele.find('.tpd-edits-hidden').each(function () {
        var t = $(this).offset().top;
        var b = t + $(this).height();
        if (h1 > b || h2 < t) {
            if ($(this).index() == ele.find('.tpd-edits-hidden').length - 1 && type) {
                $('#guide-box').remove();
                $(this).after(dragM);
                return false;
            }
        } else {
            $('#guide-box').remove();
            if (h1 - t > $(this).height() / 2) {
                $(this).after(dragM);
            } else {
                $(this).before(dragM);
            }
            return false;
        }
    })
}

/*判断鼠标位置*/
function judgePos(pageY, scroll, view) {
    if (pageY > objH.y2) {
        if (objH.tog == 1) {
            return false;
        }
        objH.tog = 1;
    } else if (pageY < objH.y3) {
        if (objH.tog == -1) {
            return false;
        }
        objH.tog = -1;
    } else {
        if (objH.tog == 0) {
            return false;
        }
        objH.tog = 0;
    }
    scrollMove(objH.tog, scroll, view);
}

/*鼠标驱动滚动条*/
function scrollMove(tog, scroll, view) {
    if (tog == 0) {
        scroll.stop();
        return false;
    } else if (tog == 1) {
        scroll.stop().animate({scrollTop: view.height()}, 2000);
    } else if (tog == -1) {
        scroll.stop().animate({scrollTop: 0}, 2000);
    }
}

//点击摸个模块，获取赋值修改
function dragModlueEdit(e) {
    var id = $(e).attr('data-eidtid');
    var title = $(e).find('h3').text();
    var p = $(e).find('h3').next().text();
    var value = $(e).find('.cont-list').children();
    var input = $(e).find('.cont-list').children().eq(0);
    var placeholder = input.attr('placeholder');
    if(input.attr('data-required')){
        $('.check')[id].checked=true;
    }else{
        $('.check')[id].checked=false;
    }
    $('.diy-ac').find('.allInputs input').val(title);
    switch (Number(id)){
        case 0:
            $('.diy-ac').find('.tpd-select-wrap').val(input.attr('data-format'));
            $('.diy-ac').find('.tpd-singleTex-textarea textarea').val(p);
            $('.diy-ac').find('.singleTex-Tips input').val(placeholder);
            var format = $('.diy-ac').find('.tpd-cstyle-select .tpd-select-list li');
            $.each(format,function () {
                if($(this).attr('singletex-li') == input.attr('data-required-format')){
                    $('.diy-ac').find('.tpd-cstyle-select span').text($(this).text());
                }
            });
            break;
        case 1:
            $('.diy-ac').find('.tpd-singleTex-textarea textarea').val(p);
            $('.diy-ac').find('.singleTex-Tips input').val(placeholder);
            // $('.diy-ac').find('.tpd-cstyle-select span').text(input.attr('data-required-title'));
            break;
        case 2:
            //按钮
            $.each(value,function (i) {
                $('.diy-ac').find('.select-list').children().eq(i).find('.tpd-radioButton-input input').val($(this).find('label').text());
                var va = $('.diy-ac').find('.select-list').children().eq(i).find('.tpd-radioButton-input input').val();
                if(!va){
                    $('.diy-ac').find('.select-list').append($('.diy-ac').find('.select-list').children().eq(i-1).clone());
                    $('.diy-ac').find('.select-list').children().eq(i).find('.tpd-radioButton-input input').val($(this).find('label').text());
                }
            });
            break;
        case 3:
            //复选项
            $.each(value,function (i) {
                $('.diy-ac').find('.select-list').children().eq(i).find('.tpd-radioButton-input input').val($(this).find('label').text());
                var va = $('.diy-ac').find('.select-list').children().eq(i).find('.tpd-radioButton-input input').val();
                if(!va){
                   $('.diy-ac').find('.select-list').append($('.diy-ac').find('.select-list').children().eq(i-1).clone());
                    $('.diy-ac').find('.select-list').children().eq(i).find('.tpd-radioButton-input input').val($(this).find('label').text());
                }
            });
            break;
        case 4:
            //下拉项
            $.each(value,function (i) {
                $('.diy-ac').find('.select-list').children().eq(i).find('input').val($(this).text());
                var va = $('.diy-ac').find('.select-list').children().eq(i).find('input').val();
                if(!va){
                    $('.diy-ac').find('.select-list').append($('.diy-ac').find('.select-list').children().eq(i-1).clone());
                    $('.diy-ac').find('.select-list').children().eq(i).find('input').val($(this).text());
                }
            });
            break;
        case 5:
            //图片\
            console.log(input.attr('data-upload-num'))
            if(input.attr('data-upload-num')==1){
               $('.upNum')[0].checked=true;
            }else{
                $('.upNum')[0].checked=false;
            }

            break;

    }
}


/*站内板块拖拽*/
function dragModlue() {
    objH.view.on('mouseenter', '.tpd-edits-hidden', function (e) {
        $(this).addClass('tpd-edit-hover').siblings().removeClass('tpd-edit-hover');
    });
    objH.view.on('mouseleave', '.tpd-edits-hidden', function (e) {
        $(this).removeClass('tpd-edit-hover');
    });
    objH.view.on('mousedown', '.tpd-edits-hidden', function (e) {
        //判断触发事件
        if (e.target == $(this).find('.tpd-editing-close')[0]) { //删除事件
            var del = $('#delete_template_id').val();
            if(del){
                del = del.split(',');
            }else {
                del = new Array();
            }
            del.push($(this).find('.cont-list').children().eq(0).attr('data-unit'));
            del = del.join(',');
            $('#delete_template_id').val(del);
            $(this).remove();
            return;
        }
        //添加编辑标记
        if ($(this).hasClass('tpd-editing')) {
            $(this).removeClass('tpd-editing');
            $('.tpd-diy-js').removeClass('diy-ac');
//          $('.tpd-tool-list>li').removeClass('draging-li');
        } else {
            $(this).addClass('tpd-editing').siblings().removeClass('tpd-editing');
            $('.tpd-diy-js').removeClass('diy-ac');
            $('.tpd-diy-js.' + dataEidtid[$(this).attr('data-eidtid')]).addClass('diy-ac');
            dragModlueEdit($(this))
//          $('.tpd-tool-list>li').removeClass('draging-li');
//          $('.tpd-tool-list>li[data-eidtid="'+editid+'"]').addClass('draging-li');
        }
        //创建拖拽代替模块
        $('body').append('<div class="drag-module2 hide" id="drag-module2"></div>');
        $(this).after(dragM);
        $('#guide-box').addClass('hide');
        objH.dragM2 = $('#drag-module2');
        var _this = this; //保存选择元素
        var yE = 0;
        $(document).mousemove(function (e) {
            $('#guide-box').removeClass('hide');
            $(_this).addClass('hide tpd-editing');  //拖拽移动的时候 保持触发的原始转态并隐藏
            yE = e.pageY - $(document).scrollTop() - 30;
            //防止移动出边界
            if (yE <= objH.y0 - $(document).scrollTop()) yE = objH.y0 - $(document).scrollTop();
            if (yE + 60 >= objH.y1 - $(document).scrollTop()) yE = objH.y1 - $(document).scrollTop() - 60;
            objH.dragM2.removeClass('hide').css({'top': yE});
            isCollision(objH.view, e.pageY, e.pageY + 60);
            //鼠标驱动滚动条判断
            judgePos(e.pageY, objH.viewScroll, objH.view);
        });
        $(document).mouseup(function (e) {
            $(document).unbind('mousemove').unbind('mouseup');
            $('#guide-box').after($(_this).removeClass('hide')).remove();
            objH.dragM2.remove();
            sort();//排序
        });
        return false;
    })
}

//排序
function sort(){
    var le = $('.tpd-edits-hidden').length;
    $('.tpd-edits-hidden').each(function (i,o) {
        $(this).find('.cont-list').children().eq(0).attr('data-sort',le - i);
    })
}

/*限制输入文本框的长度-s*/
$('body').on('keyup', '.tpd-singleTex-textarea textarea,.tpd-singleTex-input input', function () {
    if($(this).val().length < Number($(this).attr('data-maxlength'))){
        $(this).next().find('.singleOne').text($(this).val().length);
    }
});
$('body').on('blur', '.tpd-singleTex-textarea textarea,.tpd-singleTex-input input', function () {
    var maxlength = $(this).attr('data-maxlength');
    if ($(this).val().length > maxlength) {
        var val = $(this).val().substring(0, maxlength);
        $(this).val(val);
        $(this).next().find('.singleOne').text(maxlength);
    }
});
//	【所有拖拽组件】name编辑
$('body').on('blur', '.allInputs-name input', function () {
    var title = $(this).parents('.tpd-singleTex-title').find('.allInputs input');
    var ThisVar = $(this).val();
    if (ThisVar == '') {
        return false;
    }

    title.attr("name", ThisVar)


});
//	【所有拖拽组件】标题编辑
$('body').on('blur', '.allInputs input', function () {
    var ThisVar = $(this).val();
    if (ThisVar == '') {
        return false;
    }
    $(this).attr("name", ThisVar);
    $('.tpd-edits-hidden.tpd-editing').find(".TextName").text(ThisVar);

});
//  【所有拖拽组件】输入框提示编辑
$('body').on('blur', '.singleTex-Tips input', function () {
    var ThisVar = $(this).val();
    $('.tpd-edits-hidden.tpd-editing').find(".singleText-cont-input input,.multiText-cont-textarea textarea").attr("placeholder", ThisVar);
});
/*限制输入文本框的长度-e*/
dragModlue();
//可预约时间s
//时间插件
function selectTime() {
    $(document).on("click", ".tpd-selectTime-close", function () {
        $(this).parents(".tpd-selectTime-wrap").animate({"bottom": "-231px"}, 300);
    })
    //点击展开选择时间
    $(document).on("click", ".timeAppointment-select input", function () {
        $(".tpd-selectTime-wrap").animate({"bottom": "0px"}, 300);
        switch ($(this).attr('name')){
            case 'work_am_start_time':
                $('.scrollbar li').removeClass('prohibit');
                break;
            case 'work_am_end_time':
                $('.scrollbar li').each(function () {
                    if($(this).text() < $("input[name='work_am_start_time']").val()){
                        $(this).addClass('prohibit');
                    }
                });
                break;
            case 'work_pm_start_time':
                $('.scrollbar li').each(function () {
                    if($(this).text() < $("input[name='work_am_end_time']").val()){
                        $(this).addClass('prohibit');
                    }
                });
                break;
            case 'work_pm_end_time':
                $('.scrollbar li').each(function () {
                    if($(this).text() < $("input[name='work_pm_start_time']").val()){
                        $(this).addClass('prohibit');
                    }
                });
                break;
            case 'weekend_am_start_time':
                $('.scrollbar li').removeClass('prohibit');
                break;
            case 'weekend_am_end_time':
                $('.scrollbar li').each(function () {
                    if($(this).text() < $("input[name='weekend_am_start_time']").val()){
                        $(this).addClass('prohibit');
                    }
                });
                break;
            case 'weekend_pm_start_time':
                $('.scrollbar li').each(function () {
                    if($(this).text() < $("input[name='weekend_am_end_time']").val()){
                        $(this).addClass('prohibit');
                    }
                });
                break;
            case 'weekend_pm_end_time':
                $('.scrollbar li').each(function () {
                    if($(this).text() < $("input[name='weekend_pm_start_time']").val()){
                        $(this).addClass('prohibit');
                    }
                });
                break;
        }

        that = $(this);
    })
    //点击关闭选择时间
    $(document).on("click", ".tpd-selectTime-ul ul li", function () {
        var timeVar = $(this).text();
        if($(this).attr('class')=='prohibit'){
            return false;
        }
        that.val(timeVar);
        $(".tpd-selectTime-wrap").animate({"bottom": "-231px"}, 300);
    })
}

selectTime();
$(document).on("click", ".timeAppointment-list-ul .timeAppointment-list", function () {
    if ($(this).hasClass("timechenk")) {
        $(this).removeClass("timechenk");
    } else {
        $(this).addClass("timechenk");
    }
    //控制全选按钮是否全选或者没选
    var allCheckNum = $(this).parent(".timeAppointment-list-ul").find(".timeAppointment-list").length;
    var checkedNum = $(this).parent(".timeAppointment-list-ul").find(".timechenk").length

    if (allCheckNum == checkedNum) {
        $(this).parent(".timeAppointment-list-ul").siblings(".timeAppointment-list-checkbox").children("input").prop("checked", "checked");
    } else {
        $(this).parent(".timeAppointment-list-ul").siblings(".timeAppointment-list-checkbox").children("input").prop("checked", "");
    }
})
//点击全选和反选
$(document).on("click", ".timeAppointment-list-checkbox input", function () {
    if ($(this).prop("checked") == true) {
        $(this).parent().siblings(".timeAppointment-list-ul").find(".timeAppointment-list").addClass("timechenk");
    } else {
        $(this).parent().siblings(".timeAppointment-list-ul").find(".timeAppointment-list").removeClass("timechenk");
    }
});
//可预约时间d
//可预约天数s
//天数加减
function daysAdd(n) {
    var num = parseInt(Number($('#daysNumber').val()));
    var maxnum = parseInt($('#daysNumber').attr('max'));
    if (maxnum > 200) {
        maxnum = 200;
    }
    num += n;
    num <= 0 ? num = 1 : num;
    if (num >= maxnum) {
        num = maxnum;
    }
    $('#daysNumber').val(num);
}
//可预约天数d
//可预约人数s
function numbersAdd() {
    var num = parseInt(Number($('#numbers').val()));
   $('#numbers').val(num<=0?1:num);
}

// 配套设施s
function opfAcilities() {
    /*【配套设施】删除*/
    var navsUl = $('.facilities-list-wrap');
    var length;
    var html = '<div class="facilities-list">' +
        // '<input class="facilities-checkbox" type="checkbox"  />' +
        '<input type="text" class="facilities-text" placeholder="建议2~6个字" /><span class="facilities-close" >x</span></div>';
    navsUl.on('click', '.facilities-close', function () {
        length = $('.facilities-list-wrap .facilities-list').length;
        if (length < 3) return;
        var li = $(this).parents('.facilities-list');
        li.remove();
    });
    /*【配套设施】添加*/
    $('.tpd-facilities-wrap .tpd-cadd-project').click(function () {
        var aLi = $('.facilities-list-wrap .facilities-list');
        length = aLi.length;
        if (length > 9) return;
        navsUl.append(html);
    });
}

opfAcilities();
// 配套设施d
//单行文本s
/*【单文本】下拉列表 修改赋值格式*/
$('.tpd-txtnav-style').find('.tpd-select-wrap').click(function (e) {
    $(this).toggleClass('tpd-select-ac');
    var target = $(e.target);
    if (target.is($(this).find('li'))) {
        var type = target.attr('singletex-li');
        var input = $('.tpd-edits-hidden.tpd-editing').find(".singleText-cont-input input");
        input.attr('data-format',type);
        input.attr('data-required-title',target.text());
        $(this).find('span').html(target.text());
    }
});


/*必填*/
$('.check').click(function () {
    // var input = $('.tpd-edits-hidden.tpd-editing').find(".singleText-cont-input input");
    var input = $('.tpd-edits-hidden.tpd-editing').find(".cont-list").children().eq(0);
    if($(this)[0].checked){
        input.attr('data-required','required');
    }else{
        input.attr('data-required','');
    }
});

/*仅允许上传一张图片*/
$('.upNum').click(function () {
    var input = $('.tpd-edits-hidden.tpd-editing').find(".cont-list").children().eq(0);
    if($(this)[0].checked){
        input.attr('data-upload-num',1)
    }else{
        input.attr('data-upload-num',0)
    }

});
/*仅允许拍照上传 */
$('.photo').click(function () {
    var input = $('.tpd-edits-hidden.tpd-editing').find(".cont-list").children().eq(0);
    if($(this)[0].checked){
        input.attr('data-photo',1)
    }else{
        input.attr('data-photo',0)
    }

});
//【单文本】描述信息编辑
$('body').on('blur', '.tpd-singleText .tpd-singleTex-textarea textarea', function () {
    var textareaVar = $(this).val()
    if (textareaVar == '') {
        $('.tpd-edits-hidden.tpd-editing').find(".tpd-singleText-cont .singleText-cont-p").css("display", "none");
    } else {
        $('.tpd-edits-hidden.tpd-editing').find(".tpd-singleText-cont .singleText-cont-p").css("display", "block");
        $('.tpd-edits-hidden.tpd-editing').find(".tpd-singleText-cont .singleText-cont-p").text(textareaVar);
    }
});
//单行文本d
//单选按钮组s
function opradios() {
    /*【单选按钮】删除*/
    var navsUl = $('.tpd-radio-wrap');
    var winUl = '.tpd-edits-hidden.tpd-editing';
    var length;
    var mun = 3;
    navsUl.on('click', '.tpd-btn-del', function () {
        mun--;
        length = $('.tpd-radio-wrap .tpd-radioButton-li').length;
        var k = $(this).parents('.tpd-radioButton-li').index();
        if (length < 2) return;
        var li = $(this).parents('.tpd-radioButton-li');
        $(winUl).find(".radioButton-cont-list").find(".radioButton-cont-radio").eq(k).remove();
        li.remove();
    });
    /*【单选按钮】添加*/
    $('.tpd-radioButton .tpd-cadd-project').click(function () {
        mun++;
        var html = '<div class="tpd-radioButton-li"><div class="tpd-radioButton-radio"><input type="radio" name="radio" value="" /><label></label></div><div class="tpd-radioButton-input"><input type="text" name=""  placeholder="选项' + mun + '" value="选项' + mun + '" /><label></label></div><div class="tpd-btn-del"></div></div>';
        var winHtml = '<div class="radioButton-cont-radio"><input type="radio"><label>选项' + mun + '</label></div>';
        var aLi = $('.tpd-radio-wrap .tpd-radioButton-li');
        length = aLi.length;
        if (length > 9) return;
        navsUl.append(html);
        $(winUl).find(".radioButton-cont-list").append(winHtml);
    });
//	    把选项的值添加到表单内容里面
    $('body').on('blur', '.tpd-radioButton-li .tpd-radioButton-input input', function () {
        var index = $(this).parents(".tpd-radioButton-li").index();
        var radioVar = $(this).val();
        if (radioVar == '') {
            return false;
        }
        $('.tpd-edits-hidden.tpd-editing').find(".radioButton-cont-list .radioButton-cont-radio").eq(index).find("label").text(radioVar);
    });
}

opradios();
//单选按钮组d
//复选按钮组s
function opcheckBoxs() {
    var navsUl = $('.tpd-checkBox-wrap');
    var winUl = '.tpd-edits-hidden.tpd-editing';
    var length;
    var mun = 3;
    navsUl.on('click', '.tpd-btn-del', function () {
        mun--;
        length = $('.tpd-checkBox-wrap .tpd-checkButton-li').length;
        var k = $(this).parents('.tpd-checkButton-li').index();
        if (length < 2) return;
        var li = $(this).parents('.tpd-checkButton-li');
        $(winUl).find(".checkBox-cont-list").find(".checkBox-cont-check").eq(k).remove();
        li.remove();
    });
    /*【复选按钮】添加*/
    $('.tpd-checkBox .tpd-cadd-project').click(function () {
        mun++;
        var html = '<div class="tpd-checkButton-li"><div class="tpd-checkBox-btn"><input type="checkbox" value="" checked="checked"><label></label></div><div class="tpd-radioButton-input"><input type="text" name="" value="选项' + mun + '" placeholder="选项' + mun + '"><label></label></div><div class="tpd-btn-del"></div></div>';
        var winHtml = '<div class="checkBox-cont-check"><input type="checkbox"><label>选项' + mun + '</label></div>'
        var aLi = $('.tpd-checkBox-wrap .tpd-checkButton-li');
        length = aLi.length;
        if (length > 9) return;
        navsUl.append(html);
        $(winUl).find(".checkBox-cont-list").append(winHtml);
    });
    //	    把选项的值添加到表单内容里面
    $('body').on('blur', '.tpd-checkButton-li .tpd-radioButton-input input', function () {
        var index = $(this).parents(".tpd-checkButton-li").index();
        var radioVar = $(this).val();
        if (radioVar == '') {
            return false;
        }
        $('.tpd-edits-hidden.tpd-editing').find(".checkBox-cont-list .checkBox-cont-check").eq(index).find("label").text(radioVar);
    });
}

opcheckBoxs();
//复选按钮组d
//下拉列表s
function opdropDown() {
    /*【下拉列表】删除*/
    var navsUl = $('.tpd-dropDown-list');
    var winUl = '.tpd-edits-hidden.tpd-editing';
    var length;
    var mun = 3;
    navsUl.on('click', '.tpd-btn-del', function () {
        mun--;
        length = $('.tpd-dropDown-list .tpd-singleTex-input').length;
        var k = $(this).parents('.tpd-singleTex-input').index()
        if (length < 2) return;
        var li = $(this).parents('.tpd-singleTex-input');
        $(winUl).find(".tpd-select-list").find("li").eq(k).remove();
        li.remove();
    });
    /*【下拉列表】添加*/
    $('.tpd-dropDown .tpd-cadd-project').click(function () {
        mun++;
        var html = '<div class="tpd-singleTex-input"><input type="text" name="" value="选项' + mun + '" placeholder="选项' + mun + '" /><div class="tpd-btn-del"></div></div>';
        var winHtml = '<li dropDown-li="">选项'+mun+'</li>'
        var aLi = $('.tpd-dropDown-list .tpd-singleTex-input');
        length = aLi.length;
        if (length > 9) return;
        navsUl.append(html);
        $(winUl).find(".tpd-select-list").append(winHtml);
    });
    //	    把选项的值添加到表单内容里面
    $('body').on('blur', '.tpd-dropDown-list .tpd-singleTex-input input', function () {
        var index = $(this).parents(".tpd-singleTex-input").index();
        var radioVar = $(this).val();
        if (radioVar == '') {
            return false;
        }
        $('.tpd-edits-hidden.tpd-editing').find(".tpd-select-list li").eq(index).text(radioVar).parents(".tpd-select-wrap").find("span").eq(index).text(radioVar);
    });
}

opdropDown();
//下拉列表d
