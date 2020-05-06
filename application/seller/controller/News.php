<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * Author: 聂晓克      
 * Date: 2018-05-03
 */
namespace app\seller\controller;

use think\Page;
use app\admin\logic\NewsLogic;
use think\Db;
use think\AjaxPage;
use think\Session;

class News extends Base {

    public function index()
    {
   
        $cat_id = I('cat_id',0);
        $ArticleCat = new NewsLogic();
        $cats = $ArticleCat->article_cat_list(0,0,false);

        $admin_info=getAdminInfo(session('admin_id'));
        $this->assign('cats',$cats);
        $this->assign('cat_id',$cat_id);      
		return $this->fetch();
    }

    public function ajaxIndex() 
    {
        $Article =  M('news'); 
        $res = $list = array();
        $p = empty($_REQUEST['p']) ? 1 : $_REQUEST['p'];
        $size = empty($_REQUEST['size']) ? 6 : $_REQUEST['size'];
        $where = " 1 = 1 ";
        $keywords = trim(I('key_word'));
        $keywords && $where.=" and title like '%$keywords%' ";
        $cat_id = I('intro',0);
        $cat_id && $where.=" and cat_id = $cat_id ";
        $user_id = session('seller.user_id');
        $user_id && $where.=" and user_id = $user_id ";
        $res = $Article->where($where)->order('article_id desc')->page("$p,$size")->select();
        $count = $Article->where($where)->count();// 查询满足要求的总记录数        
        $Page = new AjaxPage($count, 10);
        $page = $Page->show();
        $ArticleCat = new NewsLogic();
        $cats = $ArticleCat->article_cat_list(0,0,false);
        if($res){
            foreach ($res as $val){
                $val['category'] = $cats[$val['cat_id']]['cat_name'];
                $val['add_time'] = date('Y-m-d H:i:s',$val['add_time']);                
                $list[] = $val;
            }
        }

        $news_tag=config('NEWS_TAG');
        foreach ($list as $k => $v) {

            if($v['tags'] !=''){
                $str='';
                $tmp=explode(',', $v[tags]);
                foreach ($tmp as $k2 => $v2) {
                    $str.='['.$news_tag[$v2].']'.' ';
                }
                $list[$k]['tags']=$str;
            }
        }

        $admin_info=getAdminInfo(session('admin_id'));
        $this->assign('cats',$cats);
        $this->assign('cat_id',$cat_id);
        $this->assign('newList',$list);// 赋值数据集
        $this->assign('page',$page);// 赋值分页输出  
        return $this->fetch();    
    }
    
    public function article()
    {
        $ArticleCat = new NewsLogic();
 		$act = I('GET.act','add');
        $info = array();
        $info['publish_time'] = time()+3600*24;
        if(I('GET.article_id')){
           $article_id = I('GET.article_id');
           $info = M('news')->where('article_id='.$article_id)->find();
        }
        if($info['tags']){
            $info['tags_arr']=explode(',', $info['tags']);
        }
        $tag=config('NEWS_TAG');
        $admin_info=getAdminInfo(session('admin_id'));

        $cats = $ArticleCat->article_cat_list(0,$info['cat_id']);
        $this->assign('cat_select',$cats);
        $this->assign('act',$act);
        $this->assign('info',$info);
        $this->assign('tags',$tag);
        $this->assign('role_id',$admin_info['role_id']);
        return $this->fetch();
    }

    
    public function aticleHandle()
    {
        $data = I('post.');
        $data['source'] = 1;
        $data['check_type'] = empty($data['check_type']) ? 0 : $data['check_type'];
        $data['publish_time'] = strtotime($data['publish_time']);
        $user_id = session('seller.user_id');
        
        $result = $this->validate($data, 'News.'.$data['act'], [], true);
        if ($result !== true) {
            $this->ajaxReturn(['status' => 0, 'msg' => array_shift($result), 'result' => $result]);
        }
        

        if($data['tags']){
            $data['tags']=implode(',', $data['tags']);
        }else{
            $data['tags']='';
        }
        
        if ($data['act'] == 'add') {
            $data['click'] = mt_rand(1000,1300);
            $data['add_time'] = time();
        	$data['user_id'] = $user_id;
            $r = M('news')->add($data);
        } elseif ($data['act'] == 'edit') {
            $data['check_type'] = 0;//编辑后得重新审核
            $r = M('news')->where('article_id='.$data['article_id'])->where('user_id='.$user_id)->save($data);
        } elseif ($data['act'] == 'del') {
        	$r = M('news')->where('article_id='.$data['article_id'])->where('user_id='.$user_id)->delete(); 	
        }
        
        if (!$r) {
            $this->ajaxReturn(['status' => -1, 'msg' => '操作失败', 'result' => '操作失败']);
        }

        $this->ajaxReturn(['status' => 1, 'msg' => '操作成功', 'result' => '操作成功']);
    }

}