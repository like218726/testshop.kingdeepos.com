<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * $Author: xwy 2018-05-08
 */

namespace app\mobile\controller;

use app\common\logic\NewsLogic;
use think\Controller;
use think\Db;

class News extends MobileBase
{

    public $user_id = 0;

    public function _initialize()
    {
        parent::_initialize();
        if (session('?user')) {
            $user = session('user');
            $user = M('users')->where("user_id", $user['user_id'])->find();
            session('user', $user);  //覆盖session 中的 user
            $this->user_id = $user['user_id'];
            $this->assign('user_id', $this->user_id);
        } else {
            header("location:" . U('User/login'));
            exit;
        }
    }
    /*
     *
     * 新闻列表
     *
     */
    public function news_list()
    {
        $news_cat= Db::name('news_cat')
            ->where("show_in_nav",1)
            ->order('sort_order')
            ->select();
        $cat_id = I('cat_id/d',$news_cat[0]['cat_id']);
        $this->assign('news_cat',$news_cat);
        $this->assign('cat_id',$cat_id);
        return $this->fetch();
    }
    /*
     *
     * 新闻详情
     *
     */
    public function news_detail()
    {
        $article_id=I('article_id/d',0);
        $data = [
            'user_id' => $this->user_id,
            'id' => $article_id,
        ];
        $return = NewsLogic::news_detail($data);
        //上一篇
        $down = Db::name('news')
                ->field("article_id,title")
                ->where("article_id",'lt', $article_id)
                ->order('publish_time desc')
                ->find();
        //下一篇
        $up = Db::name('news')
                ->field("article_id,title")
                ->where("article_id",'gt', $article_id)
                ->order('publish_time desc')
                ->find();
        $like = Db::name('news')
                ->field("article_id,title,click,thumb,FROM_UNIXTIME(publish_time,'%Y-%m-%d') as time")
                ->where("cat_id", $return['result']['cat_id'])
                ->where("article_id","neq",$article_id)
                ->order('publish_time desc')
                ->select();
        $comment = Db::name('news_comment')
                ->alias('c')
                ->join('__USERS__ u','u.user_id=c.user_id','LEFT')
                ->join('news_like l','l.comment_id=c.comment_id and l.is_delete=0','LEFT')
                ->field("c.comment_id,head_pic,mobile,nickname,count(l.like_id) as like_num,l.like_id,TIMESTAMPDIFF(MINUTE,FROM_UNIXTIME(c.add_time,'%Y-%m-%d %H:%i:%s'),now()) as time,content")
                ->where("c.is_delete", 0)
                ->where("c.check_type", 1)
                ->where("article_id",$article_id)
                ->order('c.add_time desc')
                ->group('c.comment_id')
                ->limit(0,10)
                ->select();
        $collection=Db::name('news_collection')->where(['user_id'=>$this->user_id,'article_id'=>$article_id,'is_delete'=>0])->find();
        $this->assign('down',$down);
        $this->assign('up',$up);
        $this->assign('like',$like);
        $this->assign('comment',$comment);
        $this->assign('collection',$collection);
        $this->assign('news',$return['result']);
        return $this->fetch();
    }
    /*
     *
     * 添加新闻评论
     *
     */
    public function newsComment()
    {
        $article_id=I('article_id/d',0);
        if(get_magic_quotes_gpc()){
            $content=htmlspecialchars(I('content'));
        }else{
            $content=htmlspecialchars(addslashes(I('content')));
        }
        $data=['article_id'=>$article_id,'user_id'=>$this->user_id,'content'=>$content,'add_time'=>time()];
        $res=Db::name('news_comment')->insert($data);
        if($res){
            $return=['status' => 1, 'msg' => '评论成功！'];
        }else{
            $return=['status' => -1, 'msg' => '评论失败！'];
        }
        $this->ajaxReturn($return);
    }
    /*
     *
     * 点赞评论
     *
     */
    public function newsLike()
    {
        $comment_id=I('comment_id/d',0);
        $like=Db::name('news_like')->where(['user_id'=>$this->user_id,'comment_id'=>$comment_id])->find();
        if($like){
            $is_delete=$like['is_delete'] ? 0 : 1;
            $res=Db::name('news_like')->where(['user_id'=>$this->user_id,'comment_id'=>$comment_id])->update(['is_delete'=>$is_delete]);
            if($res){
                if($is_delete){
                    $return=['status' => 2, 'msg' =>'取消成功！'];
                }else{
                    $return=['status' => 1, 'msg' =>'点赞成功！'];
                }
            }else{
                $return=['status' => -1, 'msg' =>$is_delete ? '取消失败！' : '点赞失败！'];
            }
        }else{
            $data=['comment_id'=>$comment_id,'user_id'=>$this->user_id,'add_time'=>time()];
            $res=Db::name('news_like')->insert($data);
            if($res){
                $return=['status' => 1, 'msg' => '点赞成功！'];
            }else{
                $return=['status' => -1, 'msg' => '点赞失败！'];
            }
        }
        $this->ajaxReturn($return);
    }
    /*
     *
     * 收藏新闻
     *
     */
    public function newsCollection()
    {
        $article_id=I('article_id/d',0);
        $collection=Db::name('news_collection')->where(['user_id'=>$this->user_id,'article_id'=>$article_id])->find();
        if($collection){
            $is_delete=$collection['is_delete'] ? 0 : 1;
            $res=Db::name('news_collection')->where(['user_id'=>$this->user_id,'article_id'=>$article_id])->update(['is_delete'=>$is_delete]);
            if($res){
                if($is_delete){
                    $return=['status' => 2, 'msg' =>'取消成功！'];
                }else{
                    $return=['status' => 1, 'msg' =>'收藏成功！'];
                }
            }else{
                $return=['status' => -1, 'msg' =>$is_delete ? '取消失败！' : '收藏失败！'];
            }
        }else{
            $data=['article_id'=>$article_id,'user_id'=>$this->user_id,'add_time'=>time()];
            $res=Db::name('news_collection')->insert($data);
            if($res){
                $return=['status' => 1, 'msg' => '收藏成功！'];
            }else{
                $return=['status' => -1, 'msg' => '收藏失败！'];
            }
        }
        $this->ajaxReturn($return);
    }
}