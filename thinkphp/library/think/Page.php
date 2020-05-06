<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Think;

class Page{
    public $firstRow; // 起始行数
    public $listRows; // 列表每页显示行数
    public $parameter; // 分页跳转时要带的参数
    public $totalRows; // 总行数
    public $totalPages; // 分页总页面数
    public $rollPage   = 11;// 分页栏每页显示的页数
    public $lastSuffix = true; // 最后一页是否显示总页数

    public $p       = 'p'; //分页参数名
    public $rows  = 'listRows'; //每页显示行数参数名
    public $url     = ''; //当前链接URL
    public $nowPage = 1;

	// 分页显示定制
    private $config  = array(
        'header' => '<span class="rows">共 %TOTAL_ROW% 条记录</span>',
        /*
        'prev'   => '<<',
        'next'   => '>>',
        'first'  => '1...',
        'last'   => '...%TOTAL_PAGE%',
        */
        'prev'   => '上一页',
        'next'   => '下一页',
        'first'  => '首页',
        'last'   => '尾页',
        'theme'  => '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
    );

    /**
     * 架构函数
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows, $listRows=20, $parameter = array()) {
       // C('VAR_PAGE') && $this->p = C('VAR_PAGE'); //设置分页参数名称
        /* 基础设置 */
        $p = I('get.p');
        if(I('get.listRows')){
            $listRows = I('get.listRows',5);
        }
        $this->totalRows  = $totalRows; //设置总记录数
        $this->listRows   = $listRows;  //设置每页显示行数
        $this->parameter  = empty($parameter) ? input() : $parameter;
        $this->nowPage    = empty($p) ? 1 : intval($p);
//        $this->parameter  = empty($parameter) ? $_REQUEST : $parameter;
//        $this->nowPage    = empty($_REQUEST[$this->p]) ? 1 : intval($_REQUEST[$this->p]);        
        $this->nowPage    = $this->nowPage>0 ? $this->nowPage : 1;
        $this->firstRow   = $this->listRows * ($this->nowPage - 1);
        /* 计算分页信息 */
        $this->totalPages = ceil($this->totalRows / $this->listRows)>0 ? ceil($this->totalRows / $this->listRows) : 1; //总页数
        
    }

    /**
     * 定制分页链接设置
     * @param string $name  设置名称
     * @param string $value 设置值
     */
    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }

    /**
     * 生成链接URL
     * @param  integer $page 页码
     * @return string
     */
    public function url($page){

        $url =  str_replace(urlencode('[PAGE]'), $page, $this->url);
        return str_replace(urlencode('[ROWS]'), $this->listRows, $url);
    }

    /**
     * 组装分页链接
     * @return string
     */
    public function show() {
        if(0 == $this->totalRows) return '';

        /* 生成URL */
        $this->parameter[$this->p] = '[PAGE]';
        $this->parameter[$this->rows] = '[ROWS]';
        $this->url = U(ACTION_NAME, $this->parameter);
        if(!empty($this->totalPages) && $this->nowPage > $this->totalPages) {
            $this->nowPage = $this->totalPages;
        }

        /* 计算分页临时变量 */
        $now_cool_page      = $this->rollPage/2;
		$now_cool_page_ceil = ceil($now_cool_page);
		$this->lastSuffix && ($this->config['last'] = $this->totalPages);
        $this->config['last'] = '尾页';
        //上一页
        $up_row  = $this->nowPage - 1;
        $up_page = $up_row > 0 ? '<li id="example1_previous" class="paginate_button previous disabled"><a class="prev" href="' . $this->url($up_row) . '">' . $this->config['prev'] . '</a></li>' : '';

        //下一页
        $down_row  = $this->nowPage + 1;
        $down_page = ($down_row <= $this->totalPages) ? '<li id="example1_next" class="paginate_button next"><a class="next" href="' . $this->url($down_row) . '">' . $this->config['next'] . '</a></li>' : '';

        //第一页
        $the_first = '';
        if($this->totalPages > $this->rollPage && ($this->nowPage - $now_cool_page) >= 1){
            $the_first = '<li id="example1_previous" class="paginate_button previous disabled"><a class="first" href="' . $this->url(1) . '">' . $this->config['first'] . '</a></li>';
        }

        //最后一页
        $the_end = '';
        if($this->totalPages > $this->rollPage && ($this->nowPage + $now_cool_page) < $this->totalPages){
            $the_end = '<li id="example1_previous" class="paginate_button previous disabled"><a class="end" href="' . $this->url($this->totalPages) . '">' . $this->config['last'] . '</a></li>';
        }

        //数字连接
        $link_page = "";
        for($i = 1; $i <= $this->rollPage; $i++){
			if(($this->nowPage - $now_cool_page) <= 0 ){
				$page = $i;
			}elseif(($this->nowPage + $now_cool_page - 1) >= $this->totalPages){
				$page = $this->totalPages - $this->rollPage + $i;
			}else{
				$page = $this->nowPage - $now_cool_page_ceil + $i;
			}
            if($page > 0 && $page != $this->nowPage){
                if($page <= $this->totalPages){
                    $link_page .= '<li class="paginate_button"><a class="num" href="' . $this->url($page). '">' . $page . '</a></li>';
                }else{
                    break;
                }
            }else{
                if($page > 0 && $this->totalPages != 1){
//                    $link_page .= '<span class="current">' . $page . '</span>';
                    $link_page .= '<li class="paginate_button active"><a tabindex="0" data-dt-idx="1" aria-controls="example1" href="#" style="color: black">' . $page . '</a></li>';

                }
            }
        }

        //替换分页内容
        $page_str = str_replace(
            array('%HEADER%', '%NOW_PAGE%', '%UP_PAGE%', '%DOWN_PAGE%', '%FIRST%', '%LINK_PAGE%', '%END%', '%TOTAL_ROW%', '%TOTAL_PAGE%'),
            array($this->config['header'], $this->nowPage, $up_page, $down_page, $the_first, $link_page, $the_end, $this->totalRows, $this->totalPages),
            $this->config['theme']);
        /**
        * 自定义每页显示几条
         */
        $listRows_arr = [5,10,20,50,100,250,500];
        $listRows_str = '<li class="paginate_button previous "><select id="listRows" style="height: 30px;line-height:  30px;">';
        $page = I('get.p');
        foreach ($listRows_arr as $k=>$v){
            $selected = $this->listRows == $v?'selected':'';
            $listRows_str .= '<option value="'.$v.'" '.$selected.'><a class="num" data-p="'.$v.'" href="javascript:void(0)">'.$v.'条</a></option>';
        }
        $listRows_str .= '</select></li>';
        $rows = str_replace('listRows/'.$this->listRows,'listRows/' , $this->url($page?$page:1));
        $listRows_str .= " <script>$('#listRows').change(function () {var rows = $(this).val();var url='{$rows}'+rows;window.location.href=url.replace('.html',''); })</script>";
//        $listRows_str .= " <script>$('#listRows').change(function () {var rows = $(this).val();window.location.href=rows })</script>";
        /**
         * 自定义每页显示几条
         */
        if(!strstr($page_str,'li')){
            $listRows_str = "";
        }

        $listRows_str = $this->zn_decodes($listRows_str);
        $page_str = $this->zn_decodes($page_str);
        return "<div class='dataTables_paginate paging_simple_numbers'><ul class='pagination'>{$listRows_str}{$page_str}</ul></div>";
    }

    /**
     * 转乱码
     * @param $str
     * @return mixed
     */
    public function zn_decodes($str){
        if(strpos($str, '%')>0 || strpos($str, '%')===0 ){
            return $this->zn_decodes(urldecode($str));
        }
        return $str;
    }
}
