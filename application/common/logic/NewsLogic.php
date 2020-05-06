<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * Author: xwy
 * Date: 2018-05-08
 */

namespace app\common\logic;

use app\common\model\News;
use app\common\model\NewsCat;
use think\Model;
use think\Db;

/**
 * Class
 * @package Home\Model
 */
class NewsLogic extends Model
{

    /**
     * 获取新闻列表
     * @param $data
     * @return array
     */
    public static function news_list($data)
    {
        $page = I('post.page/d', 1);//页数
        $limit = News::$LIMIT;//要显示的数量

        $list = M('news')
            ->alias('n')
            ->field("article_id,title,click,thumb,description,tags,link,FROM_UNIXTIME(publish_time,'%Y-%m-%d') as publish_time") //
            ->join('__NEWS_CAT__ cat', 'cat.cat_id = n.cat_id', 'LEFT')
            ->where(['is_open' => News::$STATUS_OPEN]) //'check_type' => News::$CHECK_PASS,
            ->limit(($page - 1) * $limit, $limit)
            ->order('publish_time desc')
            ->select();
        $data = PageLogic::getPage($list, $page);
        return $data;
    }
    /**
     * 获取新闻数据
     * @return mixed
     */
    public function moreNews($cat_id,$p = 1)
    {
        $limit = News::$LIMIT;//要显示的数量
        $where=$cat_id ? ['cat_id'=>$cat_id] :'';
        $list = M('news')
            ->field("article_id,title,click,thumb,description,tags,link,FROM_UNIXTIME(publish_time,'%Y-%m-%d') as publish_time") 
            ->where($where)
            ->where("check_type",1)
            ->limit(($p - 1) * $limit, $limit)
            ->order('publish_time desc')
            ->page($p, 10)
            ->select();
        return $list;
    }

    /**
     * 获取新闻详情
     * @param $data
     * @return array
     */
    public static function news_detail($data)
    {
        $list = M('news')
            ->alias('n')
            ->join('__NEWS_CAT__ cat', 'cat.cat_id = n.cat_id', 'LEFT')
            ->field('article_id,title,n.cat_id,click,thumb,description,tags,cat_name,publish_time,content')
            ->where(['is_open' => News::$OPEN_STATUS, 'article_id' => $data['id']]) //'open_type' => News::$OPEN_TYPE,
            ->find();
//      $list['addtime'] = date('Y-m-d',$list['addtime']);
        if ($list) {
            $list['content'] = htmlspecialchars_decode($list['content']);
            $list['time'] = date('Y-m-d H:i', $list['publish_time']);
        }
        if ($list) {
            return array('status' => 1, 'msg' => '操作成功', 'result' => $list);
        }
        return array('status' => 1, 'msg' => '操作成功', 'result' => array());

    }
    /**
     * 获取新闻数据
     * @return mixed
     */
    public function newsComment($cat_id,$p = 1)
    {
        $limit = News::$LIMIT;//要显示的数量
        $where=$cat_id ? ['cat_id'=>$cat_id] :'';
        $list = M('news')
            ->field("article_id,title,click,thumb,description,tags,link,FROM_UNIXTIME(publish_time,'%Y-%m-%d') as publish_time") 
            ->where($where)
            ->where("check_type",1)
            ->limit(($p - 1) * $limit, $limit)
            ->order('publish_time desc')
            ->page($p, 10)
            ->select();
        return $list;
    }

    /**
    *用户添加新闻
    *@param $data
    *@return array
    */
    public function addNews($data) 
    {
        $data['check_type'] = empty($data['check_type']) ? 0 : $data['check_type'];
        $data['user_id'] = session('user.user_id') ;
        $data['publish_time'] = time();
        $data['source'] = 2 ;
        $r = M('news')->add($data);

        if (!$r) {
            return ['status' => -1, 'msg' => '操作失败', 'result' => '操作失败'];
        }

        return ['status' => 1, 'msg' => '操作成功', 'result' => '操作成功'];
    }

     /**
     * 上传新闻图片
     *@return array
     */
    public function upload_img()
    {
        $path_array = array();
        $files = request()->file('thumb');
        foreach($files as $file) {

            $image_upload_limit_size = config('image_upload_limit_size');
            $validate = ['size' => $image_upload_limit_size, 'ext' => 'jpg,png,gif,jpeg'];
            $dir = UPLOAD_PATH.'news/';
            if (!($_exists = file_exists($dir))) {
                mkdir($dir);
            }
            $parentDir = date('Ymd');
            $info = $file->validate($validate)->move($dir, true);
            if ($info) {
                $thumb_path = '/' . $dir . $parentDir . '/' . $info->getFilename();
                array_push($path_array, $thumb_path);
            } else {
                return ['status' => -1, 'msg' => $file->getError()];
            }
        }
        return ['status' => 1, 'msg' => '上传成功', 'result' => $path_array];
    }

     /**
     * 用户获取发布新闻列表
     * @return mixed
     */
    public function userNews()
    {
        $user_id = session('user.user_id');
        $list = M('news')
            ->field("article_id,title,click,thumb,description,tags,link,FROM_UNIXTIME(publish_time,'%Y-%m-%d') as publish_time") 
            ->where('user_id', $user_id)
            ->order('publish_time desc')
            ->select();
        return $list;
    }
}