<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 商业用途务必到官方购买正版授权, 使用盗版将严厉追究您的法律责任。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * $Author: IT宇宙人 2015-08-10 $
 */
namespace app\mobile\controller;

use think\Db;
use app\common\model\WxNews;
 
class Article extends MobileBase
{
    /**
     * 文章内容页
     */
    public function detail()
    {
        $article_id = input('article_id/d', 1);
        $article = Db::name('article')->where("article_id", $article_id)->find();
        $this->assign('article', $article);
        return $this->fetch();
    }

    public function news()
    {
        $id = input('id');
        if (!$news = WxNews::get($id)) {
            $this->error('文章不存在了~', null, '', 100);
        }

        $news->content = htmlspecialchars_decode($news->content);
        $this->assign('news', $news);
        return $this->fetch();
    }
    
    public function agreement(){
    	$doc_code = I('doc_code','agreement');
    	$article = db::name('system_article')->where('doc_code',$doc_code)->find();
    	if(empty($article)) $this->error('抱歉，您访问的页面不存在！');
    	$this->assign('article',$article);
    	return $this->fetch();
    }

    /**
     * 用户帮助
     * @return mixed
     */
    public function userHelpList()
    {
        $cat_id = input('cat_id');
        if($cat_id){
            $where['cat_id'] = $cat_id;
        }
        $where['is_open'] = 1;
        $list = Db::name('article')->where($where)->order('cat_id asc,article_id desc')->column('article_id,cat_id,title');
        if($list){
            $list = array_values($list);
        }
        $this->assign('list', $list);
        return $this->fetch("user/userHelpList");
    }

    /**
     * 帮助详情
     * @return mixed
     */
    public function userHelpInfo()
    {
        $article_id = input('article_id');
        if($article_id){
            $where['article_id'] = $article_id;
        }
        $article = Db::name('article')->where($where)->find();
        $this->assign('article', $article);
        return $this->fetch("user/userHelpInfo");
    }
}