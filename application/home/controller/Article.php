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
namespace app\home\controller;
use  think\Db;
class Article extends Base
{

    public function index()
    {
        $article_id = I('article_id/d', 38);
        $article = D('article')->where("article_id", $article_id)->find();
        $this->assign('article', $article);
        return $this->fetch();
    }

    /**
     * 文章内列表页
     */
    public function articleList()
    {
        $article_cat = M('ArticleCat')->where("parent_id  = 0")->select();
        $this->assign('article_cat', $article_cat);
        return $this->fetch();
    }

    /**
     * 文章内容页
     */
    public function detail()
    {
        $article_id = I('article_id/d', 1);
        $article = M('article')->where("article_id", $article_id)->find();
        if ($article) {
            $parent = M('article_cat')->where("cat_id", $article['cat_id'])->find();
            $this->assign('cat_name', $parent['cat_name']);
            $this->assign('article', $article);
        }
        return $this->fetch();
    }

    /**
     * 获取帮助分类
     * @return mixed
     */
    public function help(){
        $helpType = Db::name('help_type')->where(['pid' => 0,'is_show'=>1])->select();  //获取顶级分类
        $this->assign('helpType',$helpType);
    	return $this->fetch();
    }

    /**
     * 获取帮助文章
     */
    public function getHelpArticle(){
        $type_id = I('post.type_id/d', 0);
        if($type_id >0 ){  //有ID获取ID下的文章
            $article = Db::name('help')->where("type_id", $type_id)->select();
            $this->ajaxReturn(['status'=>1,'msg'=>'获取成功！','data'=>$article]);
        }else{  //没ID获取常见问题文章
            $article = Db::name('help')->where(['is_show'=>1])->order('help_sort')->limit(10)->select();
            $this->ajaxReturn(['status'=>2,'msg'=>'获取成功！','data'=>$article]);
        }
    }

    public function helpInfo(){
    	$article_id = I('help_id/d', 1);
        $helpType = Db::name('help_type')->where(['pid' => 0,'is_show'=>1])->select();
    	$article = Db::name('help')->where("help_id", $article_id)->find();
        /** 模板上没用到，不知道前面为什么要查他，先注释
          if($article){
            $type_name = Db::name('help_type')->where("type_id", $article['type_id'])->getField('type_name');
            $this->assign('type_name', $type_name);
          }
         */
        $this->assign('article', $article);
        $this->assign('helpType',$helpType);
    	return $this->fetch();
    }
    
    
    /**
     * 获取服务协议
     * @return mixed
     */
    public function agreement(){
    	$doc_code = I('doc_code','agreement');
    	$article = Db::name('system_article')->where('doc_code',$doc_code)->find();
    	if(empty($article)) $this->error('抱歉，您访问的页面不存在！');
    	$this->assign('article',$article);
    	return $this->fetch();
    }
}