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
 * Date: 2018-04-26
 */

namespace app\common\logic;


use app\common\model\Moments;

use Org\Util\Date;
use think\Db;

/**
 * Class MomentsLogic
 * @package Common\Logic
 */
class MomentsLogic
{

    public static $user_id;
    public static $limit = 20;

    /**
     * 朋友圈
     * @param $data
     * @return array
     */
    public static function select_moments($data)
    {
      
        $page = I('post.page/d', 1);//页数
        $type = I('type');
        $city = I('city');
        $keywords = I('keywords','');
        $limit = self::$limit;//要显示的数量
        
        if(!empty($keywords)){
            $where = "is_delete =".Moments::$DETELE_NO." and status =" .Moments::$STATUS_SUCCESS." and (m.moments_content like '%$keywords%' or u.nickname like '%$keywords%')";
            $bind = [];
        }else{
            //根据不同的user_id查询
            if($data['user_id']){
                $where = "is_delete=:delete and status=:status or (m.user_id=:id and status=:userStatus and is_delete=:userDelete)";
                $bind = ['delete' => Moments::$DETELE_NO, 'status' => Moments::$STATUS_SUCCESS,'id' => $data['user_id'], 'userStatus' => Moments::$STATUS_WAIT, 'userDelete' => Moments::$DETELE_NO];
            }else{
                $where = "is_delete=:delete and status=:status ";
                $bind = ['delete' => Moments::$DETELE_NO, 'status' => Moments::$STATUS_SUCCESS];
            }
        }
        
        //列表数据判断是否该用户已关注
        $is_attention = Db::name('user_attention')->where(['user_id'=>$data['user_id']])->getField('att_user_id',true);
        $order = 'm.add_time desc';
        //动态类型   
        if($type == 1){
            //推荐
            $bind = array();  
            $where = "is_top=:isTop and is_delete=:is_delete";
            $bind['isTop'] = 1;
            $bind['is_delete']=0;
        }

        if($type == 2){
            //热帖
            $bind = array('is_delete'=>0);
            $where = "is_delete=:is_delete";
            $order ='m.click DESC,m.add_time DESC';
        }

        if($type == 3){
            //同城
            $city_id = Db::name('region')->where(['name'=>$city])->getField('id');
            $city_id = $city_id?:Db::name('region')->where(['name'=>['like',"%$city%"]])->find()['id'];
            $where=array();
            $where = "is_delete=:delete and status=:status and city_id=:CityId  and is_delete=:is_delete";
            $bind = ['delete' => Moments::$DETELE_NO, 'status' => Moments::$STATUS_SUCCESS ,  'CityId' => $city_id,'is_delete'=>0];
        }

        if($type == 4){
            //关注
           $bind = array();     
           $id = '';
           if($is_attention){
               foreach ($is_attention as $v){
                   $id .= ','.$v;
               }
           }
           if($id){
               $where = "m.user_id in (".trim($id,',').")" ;
           }else{
               $where = "m.user_id = 0";
           }
        }

        $select = M('moments')
            ->alias('m')
            ->join('__USERS__ u', 'u.user_id = m.user_id')
            ->join('moments_classify c','c.classify_id = m.classify_id')
            ->where($where)
            ->where(['m.status' => 1,'m.is_delete'=>0])
            ->bind($bind)
            ->field('moments_id,moments_imgs,moments_content,like_sum,add_time,u.head_pic,u.nickname,m.user_id,u.mobile,m.title,c.name,m.click,m.is_talk')
            ->limit(($page - 1) * $limit, $limit)
            ->order($order)
            ->select();
        foreach ($select as $k => $v) {
            //被关注的用户
            if(!empty($is_attention)){
                if(in_array($v['user_id'], $is_attention)){
                    $select[$k]['attention'] = 1;
                }else{
                    $select[$k]['attention'] = 0;
                }
            }else{
                $select[$k]['attention'] = 0;
            }

         
            
            if(strpos($v['moments_imgs'],'mp4')){
                $select[$k]['moments_imgs'] = array();
                $select[$k]['moments_mp4'] = array_filter(explode(',', $v['moments_imgs']));
            }else{
                $select[$k]['moments_imgs'] = array_filter(explode(',', $v['moments_imgs']));
                $select[$k]['moments_mp4'] = array();
            }

            $select[$k]['time'] = MomentsLogic::getTime($v['add_time']);
            $select[$k]['user'] = $data['user_id'] == $v['user_id'] ? 1 : 0;
            $getLike = M('moments_like')
                ->alias('l')
                ->where(['l.user_id' => $data['user_id'], 'moments_id' => $v['moments_id']])
                ->field('like_id')
                ->order('add_time asc')
                ->find();
            //根据不同的user_id查询
            if($data['user_id']){
                $where = "(c.user_id=:id and c.status=:userStatus and c.moments_id=:userMoments_id and is_delete=:userDelete)or is_delete=:delete and c.moments_id=:moments_id and c.status=:status";
                $bind = ['id' => $data['user_id'], 'userStatus' => Moments::$STATUS_WAIT, 'userMoments_id' => $v['moments_id'],
                    'userDelete' => Moments::$DETELE_NO, 'delete' => Moments::$DETELE_NO, 'moments_id' => $v['moments_id'], 'status' => Moments::$STATUS_SUCCESS];
            }else{
                $where = "is_delete=:delete and c.moments_id=:moments_id and c.status=:status ";
                $bind = [ 'delete' => Moments::$DETELE_NO, 'moments_id' => $v['moments_id'], 'status' => Moments::$STATUS_SUCCESS];
            }
            $getComment = M('moments_comment')
                ->alias('c')
                ->where($where)
                ->bind($bind)
                ->join('__USERS__ u', 'u.user_id = c.user_id')
                ->field('comment_id,comment_content,p_name,pid,c.user_id,nickname,head_pic,add_time')
                ->order('add_time asc')
                ->limit(3)
                ->select();

            $select[$k]['comment'] = $getComment;
            $select[$k]['like'] = $getLike ? 1 : 0;
        }
        $data = MomentsLogic::momentsPage($select, $page);

        return $data;

    }


    /**
     * 查看某人或者自己动态（朋友圈）
     * @param $data
     * @return array
     */
    public static function see_all_moments($data)
    {
        try {
            $page = I('post.page/d', 1);//页数
            $limit = self::$limit;//要显示的数量

            if ($data['type'] == 1) {
                $where = "m.user_id=:id and is_delete=:userDelete";
                $bind = ['id' => $data['user_id'], 'userDelete' => Moments::$DETELE_NO];
            } else {
                $where = "m.user_id=:id and is_delete=:userDelete and status=:status";
                $bind = ['id' => $data['user_id'], 'userDelete' => Moments::$DETELE_NO, 'status' => Moments::$STATUS_SUCCESS];
            }
            $select = M('moments')
                ->alias('m')
                ->join('__USERS__ u', 'u.user_id = m.user_id', 'LEFT')
                ->join('tp_moments_classify c','c.classify_id = m.classify_id')
                ->where($where)
                ->bind($bind)
                ->field('moments_id,moments_imgs,moments_content,like_sum,add_time,u.head_pic,u.nickname,u.user_id,c.name,m.title,m.is_talk')
                ->limit(($page - 1) * $limit, $limit)
                ->order('add_time desc')
                ->select();
            $year=Date('Y',time());
            $day=$month=0;
            foreach ($select as $k => $v) {
                $item_year = Date('Y',$v['add_time']);
                if($item_year == $year){
                    $select[$k]['year']=$item_year;
                    $year--;
                }
                if($day!=date('d',$v['add_time']) or $month !=date('m',$v['add_time'])){
                    $select[$k]['month'] = date('m',$v['add_time']);
                    $select[$k]['day'] = date('d',$v['add_time']);
                    $day=date('d',$v['add_time']);
                    $month=date('m',$v['add_time']);
                }
                $select[$k]['time'] = date("n.d", $v['add_time']);
//                $select[$k]['month'] = date('m',$v['add_time']);
//                $select[$k]['day'] = date('d',$v['add_time']);

                if(strpos($v['moments_imgs'],'mp4')){
                    $select[$k]['moments_imgs'] = array();
                    $select[$k]['moments_mp4'] = array_filter(explode(',', $v['moments_imgs']));
                }else{
                    $select[$k]['moments_imgs'] = array_filter(explode(',', $v['moments_imgs']));
                    $select[$k]['moments_mp4'] = array();
                }

                $select[$k]['img_count'] = count(explode(',', $v['moments_imgs']));
            }
            $data = MomentsLogic::momentsPage($select, $page);

            return $data;
        } catch (\Exception $e) {
            //            return array('status' => -1, 'msg' => $e->getMessage(), 'result' => array());
            return array('status' => -1, 'msg' => '执行失败', 'result' => array());

        }


    }

    /**
     * 查看自己或者某条动态信息（朋友圈）
     * @param $data
     * @return array
     */
    public static function see_find_moments($data)
    {
        try {
            if ($data['type'] == 1) {
                $where = "m.user_id=:id and is_delete=:userDelete and moments_id=:moments_id";
                $bind = ['id' => $data['user_id'], 'userDelete' => Moments::$DETELE_NO, 'moments_id' => $data['moments_id']];
            } else {
                $where = "m.user_id=:id and is_delete=:userDelete and moments_id=:moments_id and m.status=:status";
                $bind = ['id' => $data['user_id'], 'userDelete' => Moments::$DETELE_NO, 'moments_id' => $data['moments_id'], 'status' => Moments::$STATUS_SUCCESS];
            }
            
            //列表数据判断是否该用户已关注
            $is_attention = Db::name('user_attention')->where(['user_id'=>$data['order_user_id'],'att_user_id'=>$data['user_id']])->count();
            
            
            //获取某个动态
            $select = M('moments')
                ->alias('m')
                ->join('__USERS__ u', 'u.user_id = m.user_id', 'LEFT')
                ->join('tp_moments_classify c','c.classify_id=m.classify_id')
                ->where($where)
                ->bind($bind)
                ->field('m.moments_id,moments_imgs,moments_content,like_sum,add_time,u.head_pic,u.user_id,u.nickname,add_time,m.click,c.name,m.is_talk,title')
                ->order('add_time desc')
                ->find();
            if (!$select) {
                return array('status' => -1, 'msg' => '没有该动态', 'result' => array());
            }
//            $select['moments_imgs'] = array_filter(explode(',', $select['moments_imgs']));
            if(strpos($select['moments_imgs'],'mp4')){
                $select['moments_mp4'] = array_filter(explode(',', $select['moments_imgs']));
                $select['moments_imgs'] = array();
            }else{
                $select['moments_mp4'] = array();
                $select['moments_imgs'] = array_filter(explode(',', $select['moments_imgs']));
            }
            $select['time'] = date("Y-m-d H:i:s", $select['add_time']);
            $select['user'] = $data['order_user_id'] == $select['user_id'] ? 1 : 0;
            $select['attention'] = $is_attention?1:0;
//            'l.user_id' => $data['user_id'],
            //获取动态的点赞
            $getLike = M('moments_like')
                ->alias('l')
                ->where(['moments_id' => $data['moments_id']])
                ->join('__USERS__ u', 'u.user_id = l.user_id', 'LEFT')
                ->field('like_id,nickname,head_pic,l.user_id')
                ->order('add_time asc')
                ->select();
            $select['like'] = 0;
            foreach ($getLike as $k => $v) {
                $select['like'] = $data['order_user_id'] == $v['user_id'] ? 1 : 0;
//                $getLike[$k]['user_id']=null;
            }

            if ($data['order_user_id']) {
                $whereComment = "(c.user_id=:id and c.status=:userStatus and c.moments_id=:userMoments_id and is_delete=:userDelete)or is_delete=:delete and c.moments_id=:moments_id and c.status=:status";
                $bindComment = ['id' => $data['order_user_id'], 'userStatus' => Moments::$STATUS_WAIT, 'userMoments_id' => $data['moments_id'], 'userDelete' => Moments::$DETELE_NO,
                    'delete' => Moments::$DETELE_NO, 'moments_id' => $data['moments_id'], 'status' => Moments::$STATUS_SUCCESS];
            } else {
                $whereComment = "is_delete=:delete and c.moments_id=:moments_id and c.status=:status";
                $bindComment = ['delete' => Moments::$DETELE_NO, 'moments_id' => $data['moments_id'], 'status' => Moments::$STATUS_SUCCESS];
            }

            //获取动态的评论
            $getComment = M('moments_comment')
                ->alias('c')
                ->where($whereComment)
                ->bind($bindComment)
                ->join('__USERS__ u', 'u.user_id = c.user_id')
                ->field('comment_id,comment_content,p_name,pid,c.user_id,nickname,head_pic,add_time,u.user_id')
                ->order('add_time asc')
                ->select();
            $select['comment_sum'] = count($getComment);
            foreach ($getComment as $k => $v) {
                $getComment[$k]['time'] = date("Y-m-d H:i:s", $v['add_time']);
            }
            if ($select) {
                $select['like_arr'] = $getLike;
                $select['comment'] = $getComment;
            }
        } catch (\Exception $e) {
            return array('status' => -1, 'msg' => '执行失败', 'result' => array());
        }
        return array('status' => 1, 'msg' => '操作成功', 'result' => $select);

    }


    /**
     * 添加动态（朋友圈）
     * @param $data
     * @return array
     */
    public static function add_moments($data)
    {
        $city = I('city');
        $city_id = Db::name('region')->where(['name'=>['like',"%$city%"]])->getField('id');       
        

            //普通用户
        $data['city_id'] = $city_id;

        
        $add = Db::name('moments')->insert($data);
        if ($add) {
            return array('status' => 1, 'msg' => '操作成功', 'result' => '');
        }
        return array('status' => -1, 'msg' => '操作失败', 'result' => '');

    }

    /**
     * 刪除动态（朋友圈）
     * @param $data
     * @return array
     */
    public static function del_moments($data)
    {
        $where = ['moments_id' => $data['moments_id'], 'is_delete' => Moments::$DETELE_NO];
        if (!Db::name('moments')->where($where)->find()) {
            return array('status' => -1, 'msg' => '不存在该动态', 'result' => '');
        }
        $save = Db::name('moments')->where(['user_id' => $data['user_id'], 'moments_id' => $data['moments_id']])->save(['is_delete' => Moments::$DETELE_YES]);
        if ($save) {
            return array('status' => 1, 'msg' => '操作成功', 'result' => '');
        }
        return array('status' => -1, 'msg' => '操作失败', 'result' => '');

    }


    /**
     * 添加和取消点赞动态
     * @param $data
     * @return array
     * @throws \think\Exception
     */
    public static function add_like($data)
    {
        $where = ['moments_id' => $data['moments_id'], 'is_delete' => Moments::$DETELE_NO];
        if (!$check = Db::name('moments')->where($where)->find()) {
            return array('status' => -1, 'msg' => '不存在该动态', 'result' => '');
        }

        $find = Db::name('moments_like')
            ->where("user_id=:uid and moments_id=:mid")
            ->bind(['uid' => $data['user_id'], 'mid' => $data['moments_id']])
            ->find();
        if ($find) {
            $add = Db::name('moments_like')->delete($find['like_id']);
            Db::name('moments')->where($where)->setDec('like_sum');
            if ($add) {
                return array('status' => 0, 'msg' => '取消成功', 'result' => '');
            }
        } else {
            $add = Db::name('moments_like')->insert($data);
            Db::name('moments')->where($where)->setInc('like_sum');
            if ($add) {
                return array('status' => 1, 'msg' => '点赞成功', 'result' => '');
            }
        }

        return array('status' => -1, 'msg' => '操作失败', 'result' => '');

    }

    public static function talk($moments_id){
        $where = ['moments_id' => $moments_id, 'is_delete' => Moments::$DETELE_NO];
        if (!$check = Db::name('moments')->where($where)->find()) {
            return array('status' => -1, 'msg' => '不存在该动态', 'result' => '');
        }
        $talk=Db('moments')->where($where)->find();
        if($talk['is_talk']){
            Db('moments')->where($where)->save(['is_talk'=>0]);
            return array('status'=>1,'msg'=>'解禁成功');
        }else{
            Db('moments')->where($where)->save(['is_talk'=>1]);
            return array('status'=>1,'msg'=>'禁言成功');
        }
    }

    public static function add_click($data)
    {
        if (!$check = Db::name('moments')->where($data)->find()) {
            return array('status' => -1, 'msg' => '不存在该动态', 'result' => '');
        }

        Db::name('moments')->where($data)->setInc('click',1);
    }
    /**
     * 添加评论
     * @param $data
     * @return array
     * @throws \think\Exception
     */
    public static function add_comment($data, $user)
    {
        $where = ['moments_id' => $data['moments_id'], 'is_delete' => Moments::$DETELE_NO,'is_talk'=>0];
        if (!$check = Db::name('moments')->where($where)->find()) {
            return array('status' => -1, 'msg' => '不存在该动态', 'result' => '');
        }
        $data['user_id']=$user['user_id'];
        $add = Db::name('moments_comment')->insertGetId($data);
        $data['nickname'] = $user['nickname'];
        $data['head_pic'] = $user['head_pic'];
        $data['time'] = date("Y-m-d H:i:s",$data['add_time']);
        $data['moments_id'] = $data['moments_id'];
        $data['comment_id'] = $add;
        if ($add) {
            return array('status' => 1, 'msg' => '操作成功', 'result' => $data);
        }
        return array('status' => -1, 'msg' => '操作失败', 'result' => array());
    }

    /**
     * 刪除评论
     * @param $data
     * @return array
     */
    public static function del_comment($data)
    {
        $where = ['moments_id' => $data['moments_id'], 'is_delete' => Moments::$DETELE_NO];
        if (!Db::name('moments')->where($where)->find()) {
            return array('status' => -1, 'msg' => '不存在该动态', 'result' => '');
        }
        $save = Db::name('moments_comment')
            ->where(['user_id' => $data['user_id'], 'moments_id' => $data['moments_id'], 'comment_id' => $data['comment_id']])
            ->save(['is_delete' => Moments::$DETELE_YES]);
        if ($save) {
            return array('status' => 1, 'msg' => '操作成功', 'result' => '');
        }
        return array('status' => -1, 'msg' => '操作失败', 'result' => '');

    }


    /**
     * 统计未读数量
     * @param $data
     * @return array
     */
    public static function count_unread($data)
    {
        MomentsLogic::$user_id = $data['user_id'];

        $moments = Db::name('moments')->where(['user_id' => $data['user_id']])->column('moments_id');
        $where['moments_id'] = array('in', $moments);
        $where['is_read'] = Moments::$READ_NO;
        $like = Db::name('moments_like')->where($where)->count('like_id');

//        $comment =  Db::query("SELECT COUNT(comment_id) AS tp_count FROM `tp_moments_comment` WHERE `moments_id` IN (".implode(',',$moments).") AND `is_read` = ".Moments::$READ_NO." AND `is_delete` = ".Moments::$DETELE_NO." AND `status` = ".Moments::$STATUS_SUCCESS." OR (`user_id` = ".$data['user_id']." AND `status` = ".Moments::$STATUS_WAIT." AND `is_delete` = ".Moments::$DETELE_NO.") LIMIT 1");
        $where['is_delete'] = Moments::$DETELE_NO;
        $where['status'] = Moments::$STATUS_SUCCESS;
        $comment = Db::name('moments_comment')
            ->alias('m')
            ->where($where)
            ->whereOr(function ($query) {
                $query->where(['m.user_id' => MomentsLogic::$user_id, 'status' => Moments::$STATUS_WAIT, 'is_delete' => Moments::$DETELE_NO]);
            })
            ->order('add_time asc')->count('comment_id');
//        echo Db::name('moments_comment')->getLastSql();
        return array('status' => 1, 'msg' => '操作成功', 'result' => $like + $comment);
    }


    /**
     * 查看未读信息
     * @param $data
     * @return array
     * @throws \think\Exception
     */
    public static function read_message($data)
    {
        MomentsLogic::$user_id = $data['user_id'];
        $page = I('post.page/d', 1);//页数
        $limit = MomentsLogic::$limit;//要显示的数量
//        $moments = Db::name('moments')->where(['user_id' => $data['user_id']])->column('moments_id');
//        $where['moments_id'] = array('in', $moments);
        $where['m.is_read'] = Moments::$READ_NO;
        $where['m.user_id'] = array('neq', $data['user_id']);
        $where['o.user_id'] = array('eq', $data['user_id']);
        $like = Db::name('moments_like')
            ->alias('m')
            ->join('__USERS__ u', 'u.user_id = m.user_id', 'LEFT')
            ->join('__MOMENTS__ o', 'o.moments_id = m.moments_id', 'LEFT')
            ->field('like_id,nickname,head_pic,m.add_time,moments_imgs,moments_content')
            ->where($where)
            ->limit(($page - 1) * $limit, $limit)
            ->order('m.add_time asc')
            ->select();
        foreach ($like as $k => $v) {
            $like[$k]['time'] = date('n月d日 H:s', $v['add_time']);
            $like[$k]['moments_imgs'] = array_filter(explode(',', $v['moments_imgs']));
        }
        $where['m.is_delete'] = Moments::$DETELE_NO;
        $where['m.status'] = Moments::$STATUS_SUCCESS;
        $comment = Db::name('moments_comment')
            ->alias('m')
            ->join('__USERS__ u', 'u.user_id = m.user_id', 'LEFT')
            ->join('__MOMENTS__ o', 'o.moments_id = m.moments_id', 'LEFT')
            ->field('comment_content,p_name,pid,m.user_id,nickname,head_pic,m.add_time,moments_imgs,moments_content')
            ->limit(($page - 1) * $limit, $limit)
            ->where($where)
//            ->whereOr(function ($query) {
//                $query->where(['m.user_id' => MomentsLogic::$user_id, 'status' => Moments::$STATUS_WAIT, 'is_delete' => Moments::$DETELE_NO, 'is_read' => Moments::$READ_NO]);
//            })
            ->order('m.add_time asc')->select();
        foreach ($comment as $k => $v) {
            $comment[$k]['time'] = date('n月d日 H:s', $v['add_time']);
            $comment[$k]['moments_imgs'] = array_filter(explode(',', $v['moments_imgs']));
        }
        $arr = array_merge($like, $comment);
        $select = MomentsLogic::maopao($arr);
        $data = MomentsLogic::momentsPage($select, $page);
        return $data;
//        return array('status' => 1, 'msg' => '操作成功', 'result' => $data);
    }


    //下面的这个执行效率更高
    public static function maopao($arr)
    {
        // 进行第一层遍历
        for ($i = 0, $k = count($arr); $i < $k; $i++) {
            // 进行第二层遍历 将数组中每一个元素都与外层元素比较
            // 这里的i+1意思是外层遍历当前元素往后的
            for ($j = $i + 1; $j < $k; $j++) {
                // 内外层两个数比较
                if ($arr[$i]['add_time'] < $arr[$j]['add_time']) {
                    // 先把其中一个数组赋值给临时变量
                    $temp = $arr[$j];
                    // 交换位置
                    $arr[$j] = $arr[$i];
                    // 再从临时变量中赋值回来
                    $arr[$i] = $temp;
                }
            }
        }
        // 返回排序后的数组
        return $arr;
    }


    /**
     * 全部更新未读信息为已读(清空)
     * @param $table
     * @param $data
     * @throws \think\Exception
     */
    public static function read_update($data)
    {
        $moments = Db::name('moments')->where(['user_id' => $data['user_id']])->column('moments_id');
        $w['moments_id'] = array('in', $moments);
        //更新全部未读数据
        Db::name('moments_like')->where($w)->update(['is_read' => 1]);
        Db::name('moments_comment')->where($w)->update(['is_read' => 1]);
        return ['status' => 1, 'msg' => '操作成功'];
    }


    /**
     * 上传朋友圈图片，兼容小程序
     * @return array
     */
    public static function uploadMomentsImg()
    {
        $return_imgs = '';
        //限制上传数量
        if (count($_FILES['moments_imgs']['name']) > 9) {
            return ['status' => -1, 'msg' => '超出了最大上传数量'];
        }
        if ($_FILES['moments_imgs']['tmp_name']) {
            $files = request()->file("moments_imgs");
            if (is_object($files)) {
                $files = [$files]; //可能是一张图片，小程序情况
            }
            $image_upload_limit_size = config('image_upload_limit_size');
            $validate = ['size' => $image_upload_limit_size, 'ext' => 'jpg,png,gif,jpeg,mp4'];
            $dir = UPLOAD_PATH . 'moments/';
            if (!($_exists = file_exists($dir))) {
                $isMk = mkdir($dir,0777);
            }
            $parentDir = date('Ymd');
            foreach ($files as $key => $file) {
                $info = $file->rule($parentDir)->validate($validate)->move($dir, true);
                if ($info) {
                    $filename = $info->getFilename();
                    $new_name = '/' . $dir . $parentDir . '/' . $filename;
                    $return_imgs[] = $new_name;
                } else {
                    return ['status' => -1, 'msg' => $file->getError()];//上传错误提示错误信息
                }
            }
            if (!empty($return_imgs)) {
                $return_imgs = implode(',', $return_imgs);// 上传的图片文件
            }
        }

        return ['status' => 1, 'msg' => '操作成功', 'result' => $return_imgs];
    }




    /**
     * 分页
     * @param $result
     * @param $page
     * @return array
     */
    public static function momentsPage($result, $page)
    {
        $return = $result;   //保存原来的数据
        $result['page'] = $page;     //分页数据处理
        if ($result['page'] == 1 && $result[0] || $result['page'] > 1) {
            //分页显
            if ($result[0]) {
                return array('status' => 1, 'msg' => '获取成功', 'result' => $return);
            } else {
                return array('status' => 1, 'msg' => '已加载完毕', 'result' => array());
            }
        } else if (count($return) == 0) {
            return array('status' => 1, 'msg' => '还没有相关信息', 'result' => array());
        } else {
            return array('status' => -1, 'msg' => '获取失败');
        }
    }

    //转换时分秒
    public static function getTime($t)
    {
        $old_t = time() - $t;
        if ($old_t >= 0 && $old_t < 60) {
            return $old_t . "秒前";
        } elseif ($old_t >= 60 && $old_t < 3600) {
            return floor($old_t / 60) . "分钟前";
        } elseif ($old_t >= 3600 && $old_t < 86400) {
            return floor($old_t / 3600) . "小时前";
        } elseif ($old_t >= 86400 && $old_t < 172800) {
            return "昨天";
        } elseif ($old_t >= 172800 && $old_t < 2592000) {
            return floor($old_t / 86400) . "天前";
        } else {
            return date("Y-m-d H:i:s", $t);
        }
    }


}