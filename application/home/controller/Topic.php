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

class Topic extends Base
{
    /*
     * 专题列表
     */
    public function topicList()
    {
        $topicList = M('topic')->where("topic_state=2")->select();
        $this->assign('topicList', $topicList);
        return $this->fetch();
    }

    /*
     * 专题详情
     */
    public function detail()
    {
        $topic_id = I('topic_id/d', 1);
        $topic = D('topic')->where("topic_id", $topic_id)->find();
        $this->assign('topic', $topic);
        return $this->fetch();
    }

    public function info()
    {
        $topic_id = I('topic_id/d', 1);
        $topic = D('topic')->where("topic_id", $topic_id)->find();
        echo htmlspecialchars_decode($topic['topic_content']);
        exit;
    }
}