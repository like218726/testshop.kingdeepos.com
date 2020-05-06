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
 * $Author: wangqh 2018-04-10 $ 移动端相关协议
 */ 
namespace app\api\controller;
use  think\Db;
 
class Article extends Base {
   
    /**
     * @param doc_id agreement:用户服务协议, open_store:开店协议 
     * @return \think\mixed
     */
    public function service_agreement(){
        $doc_code = I('doc_code/s', '');
        $article = Db::name('system_article')->where('doc_code',$doc_code)->find();
        $this->assign("article" , $article);
        return $this->fetch();
    }

    /**
     * 保持与单商家一致加的
     * @return mixed
     */
    public function agreement(){
        $doc_code = I('doc_code','agreement');
        $article = db::name('system_article')->where('doc_code',$doc_code)->find();
        if(empty($article)) $this->error('抱歉，您访问的页面不存在！');
        $this->assign('article',$article);
        return $this->fetch('service_agreement');
    }

    /**
     * 用户开店时，入驻协议,
     * @return mixed
     */
    function shop_agreement(){
        return $this->fetch();
    }
}