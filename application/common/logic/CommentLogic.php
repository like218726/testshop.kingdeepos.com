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
 * Author: dyr
 * Date: 2016-08-09
 */

namespace app\common\logic;

use app\common\model\Comment;
use think\AjaxPage;
use think\Model;
use think\Db;

/**
 * 回复
 * Class CatsLogic
 * @package common\Logic
 */
class CommentLogic extends Model
{
	
	public function getCommentInfo($comment_id)
	{
		$comment_info = M('comment')->where(array('comment_id'=>$comment_id))->find();
        if($comment_info){
            $reply = $this->getReplyPage($comment_info['comment_id']);
            return array('comment_info'=>$comment_info,'reply'=>$reply);
        }else{
            return '';
        }

	}
	
    /**
     * 获取评论数
     * @param type $user_id
     * @return type
     */
    public function getAllTypeCommentNum($user_id)
    {
        //已评价
        $data['had'] = $this->getHadCommentNum($user_id);

        //待评价
        $data['no'] = $this->getWaitCommentNum($user_id);

        //服务评价
        $data['serve'] = $this->getWaitServiceCommentNum($user_id);

        return $data;
    }

    /**
     * 添加商品评论
     * @param $add
     * @return array
     */
    public function addGoodsComment($add)
    {
        if(empty($add['goods_rank'])){
            return array('status'=>-1, 'msg'=>'请给商品评分!');
        }
        if (!$add['order_id'] || !$add['goods_id']) {
            return array('status'=>-1, 'msg'=>'非法操作');
        }

        //检查订单是否已完成
        $order = M('order')->where(['order_id' => $add['order_id'], 'user_id' => $add['user_id']])->find();
        if ($order['order_status'] != 2) {
            return ['status'=>-1, 'msg'=>'该笔订单还未完成'];
        }

        //检查是否已评论过
        $goods = M('comment')->where(['rec_id' => $add['rec_id']])->find();
        if ($goods) {
            return ['status'=>-1, 'msg'=>'您已经评论过该商品'];
        }
        
        if (!isset($add['store_id']) || !$add['store_id']) {
            $add['store_id'] = M('order')->where(['order_id' => $add['order_id']])->getField('store_id');
        }
        
        if ($add['spec_key_name'] === '') {
            $order_goods = M('order_goods')->where('rec_id', $add['rec_id'])->find();
            $add['spec_key_name'] = $order_goods['spec_key_name'] ?: '';
        }

        $row = M('comment')->add($add);
        if (!$row) {
            return ['status'=>-1,'msg'=>'评论失败'];
        }
        
        //更新订单商品表状态
        M('order_goods')->where(['rec_id'=>$add['rec_id']])->save(['is_comment'=>1]);
        M('goods')->where(['goods_id'=>$add['goods_id']])->setInc('comment_count',1); // 评论数加一
        //
        // 查看这个订单是否全部已经评论,如果全部评论了 修改整个订单评论状态
        $comment_count = M('order_goods')->where(['order_id' => $add['order_id'], 'is_comment' => 0])->count();
        if ($comment_count == 0) {
            // 如果所有的商品都已经评价了 订单状态改成已评价
            M('order')->where("order_id ='{$add['order_id']}'")->save(['order_status' => 4]);
        }

        return ['status'=>1,'msg'=>'评论成功'];
    }

    /**
     * 添加服务评论
     * @param $user_id
     * @param $order_id
     * @param $store_id
     * @param $seller_score     卖家服务分数（0~5）(order_comment表)
     * @param $logistics_score  物流服务分数（0~5）(order_comment表)
     * @param $describe_score   描述服务分数（0~5）(order_comment表)
     * @return array
     */
    public function addServiceComment($user_id, $order_id, $store_id, $seller_score, $logistics_score, $describe_score)
    {
        if (!$order_id) {
            return ['status' => -1, 'msg' => '订单id不为空'];
        }
        
        if (!$seller_score || !$logistics_score || !$describe_score) {
            return ['status' => -1, 'msg' => '评分不能为空'];
        }
        
        if (!$store_id) {
            $store_id = M('order')->where(['order_id' => $order_id])->getField('store_id');
        }

        $score['seller_score'] = $seller_score;
        $score['describe_score'] = $describe_score;
        $score['logistics_score'] = $logistics_score;
        $score['order_id'] = $order_id;
        $score['user_id'] = $user_id;
        $score['store_id'] = $store_id;
        $score['commemt_time'] = time();

        $usersLogic = new UsersLogic;
        $usersLogic->save_store_score($user_id, $order_id, $store_id, $score);

        return ['status' => 1, 'msg' => '评论成功'];
    }

    /**
     * 添加商品和服务评价
     * @param array $data 评论相关数据
     * @return type|array|bool
     */
    public function addGoodsAndServiceComment($data)
    { 
        // 晒图片
        $img = $this->uploadCommentImgFile('comment_img_file');
        if ($img['status'] !== 1) {
            return $img;
        }
        $add['store_id']    = $data['store_id'];
        $add['order_sn']    = $data['order_sn'];
        $add['rec_id']      = $data['rec_id'];
        $add['goods_id']    = $data['goods_id'] ?: 0;
        $add['order_id']    = $data['order_id'] ?: 0;
        $add['user_id']     = $data['user_id'] ?: 0;
        $add['goods_rank']  = $data['goods_rank'] ?: 0;
        $add['content']     = $data['content'] ?: '';
        $add['img']         = $img['result'] ? serialize($img['result']) : ($data['img'] ? serialize($data['img']) : ''); //兼顾小程序图片上传
        $add['add_time']    = time();
        $add['ip_address']  = getIP();
        $add['is_anonymous'] = $data['is_anonymous'] ? 1 : 0;
        $add['spec_key_name'] = $data['spec_key_name'] ?: '';
        $add['impression']  = $data['impression'] ?: '';
        $add['zan_num']     = 0;
        $add['reply_num']   = 0;
        $add['parent_id']   = 0;

        //添加评论
        $return = $this->addGoodsComment($add);
        if ($return['status'] != 1) {
            return $return;
        }
        
        //添加服务评论
        if ($data['seller_score'] && $data['logistics_score'] &&  $data['describe_score']) {
            $return = $this->addServiceComment($data['user_id'], $data['order_id'], $data['store_id'], $data['seller_score'], $data['logistics_score'], $data['describe_score']);
        }
        
        return $return;
    }  
    
    /**
     * 获取服务评论（目前是未评论列表）
     * @param type $user_id
     */
    public function getServiceComment($user_id, $p = 1)
    {
        $comment_list = $this->getServiceCommentQuery(1, $user_id, $p);
        $list = [];
        foreach ($comment_list as $v) {
            $index = $v['order_id'].','.$v['store_name'];
            $list[$index][] = $v;
        }
        $stores = [];
        foreach ($list as $k => $s) {
            $index = explode(',', $k, 2);
            $stores[] = ['store_name' => $index[1], 'order_list' => $s];
        }

        return $stores;
    }

    /**
     * 获取服务评论结果
     * @param int $queryType: 0: 获取数量， 1:获取列表
     * @param type $user_id
     * @param type $p
     */
    public function getServiceCommentQuery($queryType, $user_id, $p = 1)
    {
        $query = M('order_goods')->alias('og')
                ->join('__ORDER__ o', 'o.order_id = og.order_id AND o.deleted = 0 AND o.order_status in (2,4)')
                ->join('__STORE__ s', 's.store_id = o.store_id')
                ->where("og.is_send = 1 and o.is_comment = 0 and o.user_id = $user_id");
                
        if ($queryType) {
            return $query->field('o.order_amount,o.add_time,o.order_sn,og.order_id,og.goods_id,og.goods_name,
                       o.store_id,o.goods_price,og.goods_num,og.is_comment,og.rec_id,s.store_name')
                    ->order('o.order_id', 'desc')
                    ->page($p, 10)
                    ->select();
        }
        
        return $query->group('o.order_id')->count();
    }

    /**
     * 获取评论列表
     * @param $user_id 用户id
     * @param int $status 状态 0 未评论 1 已评论 ,其他 全部
     * @return mixed
     */
    public function getComment($user_id, $status = 2)
    {
        $comment_count = $this->getCommentNum($user_id, $status);
        $page = new \think\Page($comment_count,10);
        $comment_list = $this->getCommentList($user_id, $status, $page->firstRow, $page->listRows);
        $return['result'] = $comment_list;
        $return['page'] = $page; //分页
        return $return;
    }
    
    /**
     * 获取评论查询数
     * @param $user_id 用户id
     * @param int $status 状态 0 未评论 1 已评论 ,其他 全部
     * @return mixed
     */
    public function getCommentNum($user_id, $status = 2)
    {
        return $this->getCommentQuery(0, $user_id, $status);
    }
    
    public function getCommentList($user_id, $status = 2, $firstRow = 1, $listRows = 10)
    {
        return $this->getCommentQuery(1, $user_id, $status, $firstRow, $listRows);
    }
    
    /**
     * 获取评论查询结果
     * @param $user_id 用户id
     * @param $queryType: 0: 获取数量， 1:获取列表
     * @param int $status 状态 0 未评论 1 已评论 ,其他 全部
     * @return mixed
     */
    public function getCommentQuery($queryType, $user_id, $status = 2, $firstRow = 1, $listRows = 10)
    {
        $comment_where = ['og.is_send'=> ['in', [1,3]],'o.user_id'=>$user_id];
        switch($status){
            case 0: $comment_where['og.is_comment'] = 0;break;
            case 1: $comment_where['og.is_comment'] = 1;break;
        }

        $query = M('order_goods')->alias('og')
            ->join('__ORDER__ o',"o.order_id = og.order_id AND o.user_id=$user_id AND o.deleted = 0 AND o.order_status IN (2,4)")
            ->join('__COMMENT__ c',"c.rec_id = og.rec_id", 'LEFT')  //要查看评论详情，得连表找出评论ID
            ->where($comment_where);
        
        if ($queryType) {
            return $query->field('og.*,og.is_comment as goods_comment,o.*,o.is_comment as is_service_comment,c.comment_id')
                    ->order('o.order_id', 'desc')
                    ->limit($firstRow, $listRows)
                    ->select();
        }


        
        return $query->count();
    }

    /**
     * 把回复树状数组转换成二维数组
     * @param $comment_id 回复id
     * @param int $item_num 条数
     * @return array
     */
    public function getReplyListToArray($comment_id, $item_num = 0)
    {
        $reply_tree = $this->getReplyList($comment_id);
        if (empty($reply_tree)) {
            return $reply_tree;
        }
        $reply_flat_list = $this->treeToArray($reply_tree);
        if ($item_num == 0 || count($reply_flat_list) <= $item_num) {
            $res = $reply_flat_list;
        } else {
            $res = array_slice($reply_flat_list, 0, $item_num);
        }
        return $res;
    }

    /**
     * 回复分页
     * @param $comment_id
     * @param int $page
     * @param int $item_num
     * @return mixed
     */
    public function getReplyPage($comment_id, $page = 0, $item_num = 20)
    {
        $reply_tree = $this->getReplyList($comment_id);
        $reply_flat_list = $this->treeToArray($reply_tree);
        $count = count($reply_flat_list);
        $list['list'] = array_slice($reply_flat_list, $page * $item_num, $item_num);
        $list['count'] = $count;
        return $list;
    }

    /**
     * 将树状数组转换二维数组
     * @param $tree
     * @return array
     */
    public function treeToArray($tree)
    {
        $list = array();
        foreach ($tree as $key) {
            $node = $key['children'];
            unset($key['children']);
            $list[] = $key;
            if ($node) $list = array_merge($list, $this->treeToArray($node));
        }
        return $list;
    }

    /**
     * 根据评论id获取评论下的所有回复
     * @param $comment_id
     * @param int $parent_id
     * @param array $result
     * @return array
     */
    private function getReplyList($comment_id, $parent_id = 0, &$result = array())
    {
        $reply_where = array(
            'parent_id' => $comment_id,
            'deleted' => 0,
        );
        $arr = M('comment')->where($reply_where)->order('add_time desc')->select();
        if (empty($arr)) {
            return array();
        }
        foreach ($arr as $cm) {
            $thisArr =& $result[];
            $cm['children'] = $this->getReplylist($cm['comment_id'], $cm['reply_id'], $thisArr);
            $thisArr = $cm;
        }
        return $result;
    }
    
    /**
     * 获取已评论数
     * @param type $user_id
     * @return type
     */
    public function getHadCommentNum($user_id)
    {
        return $this->getCommentNum($user_id, 1);
    }
    
    /**
     * 获取未(待)评论数
     */
    public function getWaitCommentNum($user_id)
    {
        return $this->getCommentNum(intval($user_id), 0);
    }
    
    /**
     * 获取未(待)服务评论数
     */
    public function getWaitServiceCommentNum($user_id)
    {
        return $this->getServiceCommentQuery(0, $user_id);
    }
    
    /**
     * 上传评论图片
     * @return type
     */
    public function uploadCommentImgFile($name)
    {
        $comment_img = [];
        if ($_FILES[$name]['tmp_name'][0]) {
            $files = request()->file($name);
            if (is_object($files)) {
                $files = [$files];
            }
            $image_upload_limit_size = config('image_upload_limit_size');
            $validate = ['size'=>$image_upload_limit_size,'ext'=>'jpg,png,gif,jpeg'];
            $dir = 'public/upload/comment/';
            if (!($_exists = file_exists($dir))) {
                mkdir($dir);
            }
            $parentDir = date('Ymd');
            foreach($files as $file){
                $info = $file->validate($validate)->rule(function ($file) {
                    return  md5(mt_rand());
                })->move($dir.$parentDir);
                if($info) {
                    $filename = $info->getFilename();
                    $new_name = '/'.$dir.$parentDir.'/'.$filename;
                    $comment_img[] = $new_name;
                } else {
                    return ['status' => -1, 'msg' => $file->getError()];
                }
            }
            //$comments = serialize($comment_img); // 上传的图片文件
        }
        return ['status' => 1, 'msg' => '上传成功', 'result' => $comment_img];
    }

    /**
     * @param $goods_id
     * @param $commentType
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsComment($goods_id, $commentType){
        if ($commentType == 5) {
            $where = array(
                'c.is_show' => 1,
                'c.goods_id' => $goods_id,
                'c.parent_id' => 0,
                'c.img' => ["exp", "!='' and c.img NOT LIKE 'N;%'"],
                'c.deleted' => 0
            );
        } else {
            $typeArr = array('1' => '0,1,2,3,4,5', '2' => '4,5', '3' => '3', '4' => '0,1,2');
            $where = array(
                'c.is_show' => 1,
                'c.goods_id' => $goods_id,
                'c.parent_id' => 0,
                'floor(c.goods_rank)' => ["IN", $typeArr[$commentType]],
                'c.deleted' => 0
            );
        }
        $count = db('comment')->alias('c')->where($where)->count();
        $page = new AjaxPage($count, 10);
        $show = $page->show();
        $list = db('comment')->alias('c')
            ->field("u.head_pic,u.nickname,c.add_time,c.spec_key_name,c.content,
                    c.impression,c.comment_id,c.zan_num,c.is_anonymous,c.reply_num,c.goods_rank,
                    c.img,c.parent_id,o.pay_time,o.pay_time as seller_comment")
            ->join('__USERS__ u', 'u.user_id = c.user_id', 'LEFT')
            ->join('__ORDER__ o ', 'o.order_id = c.order_id', 'LEFT')
            ->where($where)
            ->order("c.add_time desc")
            ->limit($page->firstRow . ',' . $page->listRows)->select();
        $reply_logic = new ReplyLogic();
        foreach ($list as $k => $v) {
            $list[$k]['img'] = unserialize($v['img']); // 晒单图片
            $list[$k]['parent_id'] = $reply_logic->getReplyListToArray($v['comment_id'], 5);
            $list[$k]['seller_comment'] =  Db::name('comment')->where(['goods_id' => $goods_id, 'parent_id' => $list[$k]['comment_id']])->order("add_time desc")->select();
            if($v['is_anonymous'] == 1){
                $list[$k]['nickname'] =  mb_substr($v['nickname'], 0, 3,'utf-8') . '***';
            }
        }
        return ['list'=>$list,'page'=>$show];
    }


    /**
     * 后去店铺评价或者商品评价
     * @param $store_id
     * @param $goods_id 0  大于0则看商品的
     * @param $commentType
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getShopGoodsComment($store_id, $goods_id=0, $commentType){
        if ($commentType == 5) {
            $where = array(
                'c.is_show' => 1,
                'c.parent_id' => 0,
                'c.img' => ["exp", "!='' and c.img NOT LIKE 'N;%'"],
                'c.deleted' => 0
            );
        } else {
            $typeArr = array('1' => '0,1,2,3,4,5', '2' => '4,5', '3' => '3', '4' => '0,1,2');
            $where = array(
                'c.is_show' => 1,
                'c.parent_id' => 0,
                'floor(c.goods_rank)' => ["IN", $typeArr[$commentType]],
                'c.deleted' => 0
            );
        }
        $where['s.store_id'] = $store_id;
        if($goods_id){
            $where['c.goods_id'] = $goods_id;
        }
        $count = db('comment')->alias('c') ->join('__SHOP__ s', 's.shop_id = c.shop_id', 'LEFT')->where($where)->count();
        $page = new AjaxPage($count, 3);
        $list = (new Comment())->alias('c')
            ->field("u.head_pic,u.nickname,c.add_time,c.spec_key_name,c.content,
                    c.impression,c.comment_id,c.zan_num,c.is_anonymous,c.reply_num,c.goods_rank,
                    c.img,c.parent_id,o.pay_time,o.pay_time as seller_comment,c.store_id")
            ->join('__STORE__ s', 's.store_id = c.store_id', 'LEFT')
            ->join('__USERS__ u', 'u.user_id = c.user_id', 'LEFT')
            ->join('__ORDER__ o ', 'o.order_id = c.order_id', 'LEFT')
            ->where($where)
            ->order("c.add_time desc")
            ->limit($page->firstRow . ',' . $page->listRows)->select();
        if($list){
            $reply_logic = new ReplyLogic();
            foreach ($list as $k => $v) {
                $list[$k]['img'] = unserialize($v['img']); // 晒单图片
                $list[$k]['parent_id'] = $reply_logic->getReplyListToArray($v['comment_id'], 5);
                $where_c = ['parent_id' => $list[$k]['comment_id'],'store_id'=>$store_id];
                if($goods_id){
                    $where_c['goods_id'] = $goods_id;
                }
                $list[$k]['seller_comment'] =  Db::name('comment')->where($where_c)->order("add_time desc")->select();
                if($v['is_anonymous'] == 1){
                    $list[$k]['nickname'] =  mb_substr($v['nickname'], 0, 1,'utf-8') . '***';
                }
            }
        }
        $result['list'] = $list;
        $result['nav'] = $this->getShopCommentStatistics($store_id,$goods_id);
        return $result;
    }


    /**
     * 店铺或者商品评价总数
     * @param int $store_id
     * @param int $goods_id
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getShopCommentStatistics($store_id=0, $goods_id=0)
    {
        $comment_where = ['is_show' => 1,'user_id'=>['gt',0],'deleted'=>0]; //公共条件
        $field = "sum(case when img !='' and img not like 'N;%' then 1 else 0 end) as img_sum,"
            ."sum(case when goods_rank >= 4 and goods_rank <= 5 then 1 else 0 end) as high_sum," .
            "sum(case when goods_rank >= 3 and goods_rank <4 then 1 else 0 end) as center_sum," .
            "sum(case when goods_rank < 3 then 1 else 0 end) as low_sum,count(comment_id) as total_sum,".
            "FORMAT(sum(goods_rank)/count(comment_id),1) as goods_colligate_score" ;
        if($store_id){
            $comment_where['store_id'] = $store_id;
            $group = 'store_id';
        }
        if($goods_id){
            $comment_where['goods_id'] = $goods_id;
            $group = 'goods_id';
        }
        $comment_statistics = Db::name('comment')->field($field)->where($comment_where)->group($group)->cache('store_comment'.$store_id,3600)->find();
        if($comment_statistics){
            $comment_statistics['high_rate'] = ceil($comment_statistics['high_sum'] / $comment_statistics['total_sum'] * 100); // 好评率
            $comment_statistics['center_rate'] = ceil($comment_statistics['center_sum'] / $comment_statistics['total_sum'] * 100); // 好评率
            $comment_statistics['low_rate'] = ceil($comment_statistics['low_sum'] / $comment_statistics['total_sum']* 100); // 好评率
        }else{
            $comment_statistics = ['img_sum'=>0,'high_sum' => 0, 'high_rate' => 100, 'center_sum' => 0, 'center_rate' => 0, 'low_sum' => 0, 'low_rate' => 0, 'total_sum' => 0];
        }
        $comment_statistics['colligate_rate'] = round(($comment_statistics['high_rate'] + $comment_statistics['center_rate'] + $comment_statistics['low_rate'])/3/20,1);
        $store = db('store')->field('store_desccredit,store_servicecredit,store_deliverycredit')->find($store_id);
        $comment_statistics['colligate_score'] = number_format(($store['store_desccredit'] + $store['store_servicecredit'] + $store['store_deliverycredit'])/3,1);
        return array_merge($comment_statistics,$store);
    }

}