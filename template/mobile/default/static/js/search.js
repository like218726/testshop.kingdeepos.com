var hotList=$('.hot_content > ul')
var hotLi=$('.hot_content > ul > li')
var keyList=$('.search_keywords > ul')
var keyLi=$('.search_keywords > ul > li')
var hotWidth=0
var keyWidth=0


window.onload=function(){
    hotWidth=0
    keyWidth=0
    for(var i=0;i<hotLi.length;i++){
        hotWidth += parseFloat(hotLi.eq(i).css('width'))+parseFloat(hotLi.eq(i).css('marginRight'))
    }
    hotList.css('width',(hotWidth+10)+'px')
    for(var i=0;i<keyLi.length;i++){
        keyWidth += parseFloat(keyLi.eq(i).css('width'))+parseFloat(keyLi.eq(i).css('marginRight'))
    }
    keyList.css('width',(keyWidth)+'px')
    document.querySelector('.hot_content').addEventListener('touchmove',function(e){

        e.preventDefault();

    });
    /*区域滚动效果*/
    /*条件：一个容器装着一个容器html结构*/
    /*找到大容器*/
    /*子容器大于父容器*/
    new IScroll(document.querySelector('.hot_content'),{
        scrollY:false,
        scrollX:true
    });

    document.querySelector('.search_keywords').addEventListener('touchmove',function(e){

        e.preventDefault();

    });
    /*区域滚动效果*/
    /*条件：一个容器装着一个容器html结构*/
    /*找到大容器*/
    /*子容器大于父容器*/
    new IScroll(document.querySelector('.search_keywords'),{
        scrollY:false,
        scrollX:true
    });
}

window.onresize=function(){
    hotWidth=0
    keyWidth=0
    for(var i=0;i<hotLi.length;i++){
        hotWidth += parseFloat(hotLi.eq(i).css('width'))+parseFloat(hotLi.eq(i).css('marginRight'))
    }
    hotList.css('width',(hotWidth)+'px')
    for(var i=0;i<keyLi.length;i++){
        keyWidth += parseFloat(keyLi.eq(i).css('width'))+parseFloat(keyLi.eq(i).css('marginRight'))
    }
    keyList.css('width',(keyWidth)+'px')

    // var $search_hot=$('search_hot')
    // $search_hot.css('height',($(window).height-$('header').height-$('.search_content').height-$('.search_content').css('marginTop'))+'px')
}
