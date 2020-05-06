<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采用TP5助手函数可实现单字母函数M D U等,也可db::name方式,可双向兼容
 * ============================================================================
 * Author: dyr
 * Date: 2017-12-04
 */

namespace app\common\logic;

use app\common\model\Coupon;
use app\common\model\Goods;
use app\common\model\Users;
use app\common\util\TpshopException;
use think\Cache;
use think\Model;
use think\Db;

/**
 * 用户类
 * Class CatsLogic
 * @package Home\Logic
 */
class User
{
    private $user;
    public $coupon = [];

    public function setUserById($user_id)
    {
        $this->user = Users::get($user_id);
    }

    public function setUser($user){
        $this->user = $user;
    }


    /**
     * 记录用户浏览商品记录
     * @param Goods $goods
     */
    public function visitGoodsLog(Goods $goods)
    {
        if (!empty($this->user)) {
            $record = Db::name('goods_visit')->where(['user_id' => $this->user['user_id'], 'goods_id' => $goods['goods_id']])->find();
            if ($record) {
                Db::name('goods_visit')->where(['user_id' => $this->user['user_id'], 'goods_id' => $goods['goods_id']])->save(array('visit_time' => time()));
            } else {
                $visit = ['user_id' => $this->user['user_id'], 'goods_id' => $goods['goods_id'], 'visit_time' => time(),
                    'cat_id1' => $goods['cat_id1'], 'cat_id2' => $goods['cat_id2'], 'cat_id3' => $goods['cat_id3']];
                Db::name('goods_visit')->add($visit);
            }
        }
    }

    /**
     * @param $store
     * @return bool：收藏成功，取消收藏
     * @throws \think\Exception
     */
    public function collectStoreOrNo($store)
    {
        $store_collect_info = Db::name('store_collect')->where(['store_id' => $store['store_id'], 'user_id' => $this->user['user_id']])->find();
        if (empty($store_collect_info)) {
            //收藏
            $store_collect_data = array(
                'user_id' => $this->user['user_id'],
                'store_id' => $store['store_id'],
                'add_time' => time(),
                'store_name' => $store['store_name'],
                'user_name' => $this->user['user_id']['nickname']
            );
            Db::name('store_collect')->add($store_collect_data);
            Db::name('store')->where(array('store_id' => $store['store_id']))->setInc('store_collect');
            return true;
        } else {
            //取消收藏
            Db::name('store_collect')->where(['store_id' => $store['store_id'], 'user_id' => $this->user['user_id']])->delete();
            Db::name('store')->where(['store_id' => $store['store_id']])->setDec('store_collect');
            return false;
        }
    }

    /**
     * 领取优惠券
     * @param $coupon_id
     * @throws TpshopException
     */
    public function getCouponByID($coupon_id)
    {
        if (empty($this->user['user_id'])) {
            throw new TpshopException('领取优惠券', 0, ['status' => 0, 'msg' => '请先登录']);
        }
        $Coupon = new Coupon();
        $this->coupon = $coupon = $Coupon::get($coupon_id);
        if (empty($coupon) || $coupon['status'] != 1) {
            throw new TpshopException('领取优惠券', 0, ['status' => 0, 'msg' => '活动已结束或不存在，看下其他活动吧~']);
        }
        if ($coupon['send_end_time'] < time()) {
            throw new TpshopException('领取优惠券', 0, ['status' => 0, 'msg' => '抱歉，已经过了领取时间']);
        }
        if ($coupon['send_num'] >= $coupon['createnum'] && $coupon['createnum'] != 0) {
            throw new TpshopException('领取优惠券', 0, ['status' => 0, 'msg' => '来晚了，优惠券被抢完了']);
        }
        $user_coupon = Db::name('coupon_list')->field('id')->where(['cid' => $coupon_id, 'uid' => $this->user['user_id']])->find();
        if ($user_coupon) {
            throw new TpshopException('领取优惠券', 0, ['status' => 0, 'msg' => '您已领取过该优惠券', 'code' => 2]);
        }
        $data = ['uid' => $this->user['user_id'], 'cid' => $coupon_id, 'type' => 2, 'send_time' => time(), 'store_id' => $coupon['store_id']];
        Db::name('coupon_list')->insert($data);
        $coupon['send_num'] = $coupon['send_num'] + 1;
        $coupon->save();
    }

    /**
     * 获取优惠券信息
     * @return mixed
     */
    function getCouponInfo()
    {
        return $this->coupon;
    }

    /**
     * 绑定账号
     */
    public function checkOauthBind()
    {
        if (empty($this->user)) {
            throw new TpshopException('关联账号', 0, ['status' => 0, 'msg' => '账号不存在']);
        }
        $unique_id = I('unique_id');
        $reg_miniapp =  Cache::get('reg_miniapp');
        $unique_id = $reg_miniapp[$unique_id];
        addLog('thirdLogin',' 获取缓存绑定已有账号信息-checkOauthBind-'.I('unique_id'), $unique_id);
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false && ACTION_NAME != 'bind_account') {
            //微信小程序访问
//            if(!cache(I('unique_id'))  && !session('third_oauth')){
            if(!$unique_id  && !session('third_oauth')){
                throw new TpshopException('关联账号', 0, ['status' => 0, 'msg' => '登录超时']);
            }
//            if(cache(I('unique_id'))){
            if($unique_id){
                session('third_oauth',$unique_id);
            }
        }
        
        $thirdOauth = session('third_oauth');
        $thirdName = ['weixin' => '微信', 'qq' => 'QQ', 'alipay' => '支付宝', 'miniapp' => '微信小程序'];
        $openid = $thirdOauth['openid'];   //第三方返回唯一标识
        $unionid = $thirdOauth['unionid'];   //第三方返回唯一标识
        $oauth = $thirdOauth['oauth'];      //来源
        $oauthCN = $platform = $thirdName[$oauth];
        if ((empty($unionid) && empty($oauth)) || empty($openid)) {
            throw new TpshopException('关联账号', 0, ['status' => 0, 'msg' => '第三方平台参数有误[openid:' . $openid . ' , unionid:' . $unionid . ', oauth:' . $oauth . ']']);
        }
        //1.判断一个账号绑定多个QQ
        //2.判断一个QQ绑定多个账号
        if ($unionid) {
            //此oauth是否已经绑定过其他账号
            $thirdUser = Db::name('oauth_users')->where(['unionid' => $unionid, 'oauth' => $oauth])->find();
            if ($thirdUser && $this->user['user_id'] != $thirdUser['user_id']) {
                throw new TpshopException('关联账号', 0, ['status' => 0, 'msg' => '此' . $oauthCN . '已绑定其它账号', 'result' => ['unionid' => $unionid]]);
            }

            //1.2此账号是否已经绑定过其他oauth
            $thirdUser = Db::name('oauth_users')->where(['user_id' => $this->user['user_id'], 'oauth' => $oauth])->find();
            if ($thirdUser && $thirdUser['unionid'] != $unionid) {
                throw new TpshopException('关联账号', 0, ['status' => 0, 'msg' => '此账号已绑定其它' . $oauthCN . '账号', 'result' => ['unionid' => $unionid]]);
            }
        } else {
            //2.1此oauth是否已经绑定过其他账号
            $thirdUser = Db::name('oauth_users')->where(['openid' => $openid, 'oauth' => $oauth])->find();
            if ($thirdUser) {
                throw new TpshopException('关联账号', 0, ['status' => 0, 'msg' => '此' . $oauthCN . '已绑定其它账号', 'result' => ['openid' => $openid]]);
            }
            //2.2此账号是否已经绑定过其他oauth
            $thirdUser = Db::name('oauth_users')->where(['user_id' => $this->user['user_id'], 'oauth' => $oauth])->find();
            if ($thirdUser) {
                throw new TpshopException('关联账号', 0, ['此账号已绑定其它' . $oauthCN . '账号']);
            }
        }
    }

    public function oauthBind()
    {
        $thirdOauth = session('third_oauth');
        Db::name('oauth_users')->save(['oauth' => $thirdOauth['oauth'], 'openid' => $thirdOauth['openid'], 'user_id' => $this->user['user_id'], 'unionid' => $thirdOauth['unionid'], 'oauth_child' => $thirdOauth['oauth_child']]);
        $ruser['token'] = md5(time() . mt_rand(1, 999999999));
        $ruser['last_login'] = time();
        $this->user->token = md5(time() . mt_rand(1, 999999999));
        $this->user->last_login = time();
        if(isset($_SESSION['data']['head_pic'])){
            $this->user->head_pic = $_SESSION['data']['head_pic'];
        }
        $this->user->save();
        $user_array = $this->user->toArray();
        $oauth_users = Db::name('oauth_users')->where(['user_id' => $this->user['user_id'], 'oauth' => 'weixin', 'oauth_child' => 'mp'])->find();
        if ($oauth_users) {
            $user_array['open_id'] = $oauth_users['open_id'];
        }
        session('user', $user_array);
    }

    public function doLeader()
    {
//        $first_leader = cookie('first_leader');  //推荐人id
//        if ($first_leader) {
//            $this->user->first_leader = $first_leader;
//            $this->user->save();
//            $firstLeaderUser = Users::get(['user_id' => $first_leader]);
//            if ($firstLeaderUser) {
//                //他上线分销的下线人数要加1
//                $firstLeaderUser->underling_number = $firstLeaderUser->underling_number + 1;
//                $firstLeaderUser->save();
//                Db::name('users')->where(['user_id' => $firstLeaderUser['second_leader']])->setInc('underling_number');
//                Db::name('users')->where(['user_id' => $firstLeaderUser['third_leader']])->setInc('underling_number');
//            }
//        } else {
//            if ($this->user->first_leader != 0) {
//                $this->user->first_leader = 0;
//                $this->user->save();
//            }
//        }
    }

    public function refreshCookie()
    {
        setcookie('user_id', $this->user['user_id'], null, '/');
        setcookie('is_distribut', $this->user['is_distribut'], null, '/');
        $nick_name = empty($this->user['nickname']) ? $this->user['mobile'] : $this->user['nickname'];
        setcookie('uname', urlencode($nick_name), null, '/');
        setcookie('head_pic', urlencode($this->user['head_pic']), null, '/');
        setcookie('cn', 0, time() - 3600, '/');
    }

    public function getUser()
    {
        return $this->user;
    }
}