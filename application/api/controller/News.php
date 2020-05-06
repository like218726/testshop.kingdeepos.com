<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\api\controller;
use app\common\logic\NewsLogic;
use think\Db;

/**
 * Description of moments
 *
 * @author Administrator
 */
class News extends Base
{


    /**
     * 新闻列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function newsList()
    {
        $page = input('page/d', 1);
        $cat_id = input('cat_id/d', 0);
        $where['publish_time'] = ['elt',time()];
        $where['is_open'] = 1;
        $where['check_type'] = 1;
        if($cat_id){
            $where['cat_id'] = $cat_id;
        }
        $list= \app\common\model\News::withCount(['newsComment'=>function($query){
            $query->where(['is_delete'=>0]);
        }])
            ->where($where)
            ->order('publish_time DESC')
            ->page($page,4)
            ->select();
        foreach($list as $k=>$v){
            $list[$k]['content'] =  '<p>'.cutstr_html(htmlspecialchars_decode($list[$k]['content']),60).'</p>';
            $list[$k]['time'] = $this->format_date($v['publish_time']);
            $list[$k]['comment'] = $v->news_comment_count;
            $list[$k]['cat_name'] = db('news_cat')->where(['cat_id'=>$v['cat_id']])->value('cat_name');
        }
        $this->ajaxReturn(['status' => 1, 'result' => $list]);

    }

    /**
     * 新闻详情
     */
    public function newsDetail()
    {
        if (!request()->isPost()) {
            $this->ajaxReturn(['status' => -1, 'msg' => '请用post请求！！']);
        }

        $data = [
            'user_id' => $this->user_id,
            'id' => I('news_id/d',0),
        ];
        $list = M('news')
            ->alias('n')
            ->join('__NEWS_CAT__ cat', 'cat.cat_id = n.cat_id', 'LEFT')
            ->field('article_id,title,click,thumb,description,tags,cat_name,publish_time,content')
            ->where(['is_open' => 1, 'article_id' => $data['id']])
            ->find();
        if ($list) {
            $list['content'] = htmlspecialchars_decode($list['content']);
            $list['time'] = date('Y-m-d', $list['publish_time']);
        }else{
            $list = [];
        }
        $return = array('status' => 1, 'msg' => '操作成功', 'result' => $list);

        $this->ajaxReturn($return);

    }






    /*
     *
     * 前端页面展示
     *
     */

    public function news_list()
    {
        return $this->fetch();
    }

    public function news_detail()
    {
        $data = [
            'article_id' => I('news_id/d',0),
        ];
        $news = M('news')->where($data)->find();
        $this->assign('news',$news);
        return $this->fetch();
    }

    function format_date($time){
        $t=time()-$time;
        $f=array(
            '31536000'=>'年',
            '2592000'=>'个月',
            '604800'=>'星期',
            '86400'=>'天',
            '3600'=>'小时',
            '60'=>'分钟',
            '1'=>'秒'
        );
        foreach ($f as $k=>$v)    {
            if (0 !=$c=floor($t/(int)$k)) {
                return $c.$v.'前';
            }
        }
    }

}
