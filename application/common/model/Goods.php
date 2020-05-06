<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: dyr
 * Date: 2016-08-23
 */

namespace app\common\model;

use think\Model;
use think\Db;
use think\Url;

/**
 * @package Home\Model
 */
class Goods extends Model
{

    public function SpecGoodsPrice()
    {
        return $this->hasMany('SpecGoodsPrice','goods_id','goods_id');
    }
    public function FlashSale()
    {
        return $this->hasOne('FlashSale','id','prom_id');
    }

    public function PromGoods()
    {
        return $this->hasOne('PromGoods','id','prom_id')->cache(true,10);
    }
    public function GroupBuy()
    {
        return $this->hasOne('GroupBuy','id','prom_id');
    }
    public function store()
    {
        return $this->hasOne('store','store_id','store_id');
    }

    public function brand()
    {
        return $this->hasOne('brand','id','brand_id')->bind(['brand_name' => 'name']);
    }

    public function goodsImages()
    {
        return $this->hasMany('GoodsImages','goods_id','goods_id')->order("img_sort asc");
    }

    public function specImage(){
        return $this->hasMany('SpecImage', 'goods_id', 'goods_id');
    }

    public function goodsAttr()
    {
        return $this->hasMany('goodsAttr','goods_id','goods_id');
    }

    public function goodsClass1()
    {
        return $this->hasOne('GoodsCategory','id','cat_id1');
    }
    public function goodsClass2()
    {
        return $this->hasOne('GoodsCategory','id','cat_id2');
    }
    public function goodsClass3()
    {
        return $this->hasOne('GoodsCategory','id','cat_id3');
    }


    public function getDiscountAttr($value, $data)
    {
        if ($data['market_price'] == 0) {
            $discount = 10;
        } else {
            $discount = round($data['shop_price'] / $data['market_price'], 2) * 10;
        }
        return $discount;
    }

    /**
     * 获取商品评价
     * 好评数差评数中评数及其百分比,和总评数
     * @param $value
     * @param $data
     * @return array|false|\PDOStatement|string|Model
     */
    public function getCommentStatisticsAttr($value, $data)
    {
        $comment_where = ['is_show' => 1,'goods_id' => $data['goods_id'],'user_id'=>['gt',0],'deleted'=>0]; //公共条件
        $field = "sum(case when img !='' and img not like 'N;%' then 1 else 0 end) as img_sum,"
            ."sum(case when goods_rank >= 4 and goods_rank <= 5 then 1 else 0 end) as high_sum," .
            "sum(case when goods_rank >= 3 and goods_rank <4 then 1 else 0 end) as center_sum," .
            "sum(case when goods_rank < 3 then 1 else 0 end) as low_sum,count(comment_id) as total_sum" ;
        $comment_statistics = Db::name('comment')->field($field)->where($comment_where)->group('goods_id')->find();
        if($comment_statistics){
            $comment_statistics['high_rate'] = ceil($comment_statistics['high_sum'] / $comment_statistics['total_sum'] * 100); // 好评率
            $comment_statistics['center_rate'] = ceil($comment_statistics['center_sum'] / $comment_statistics['total_sum'] * 100); // 好评率
            $comment_statistics['low_rate'] = ceil($comment_statistics['low_sum'] / $comment_statistics['total_sum'] * 100); // 好评率
        }else{
            $comment_statistics = ['img_sum'=>0,'high_sum' => 0, 'high_rate' => 100, 'center_sum' => 0, 'center_rate' => 0, 'low_sum' => 0, 'low_rate' => 0, 'total_sum' => 0];
        }
        return $comment_statistics;
    }

    /**
     * 获取评论中的印象统计
     */
    public function getCommentPointAttr($value, $data)
    {
        $comments = Db::name('comment')->field('count(comment_id) AS c,impression')->where(['deleted'=>0,'goods_id'=>$data['goods_id']])->group('impression')->select();
        $res = array();
        foreach($comments as $key){
            if($key['impression']){
                $impression_array = explode(',',$key['impression']);
                foreach($impression_array as $k){
                    if(array_key_exists($k,$res)){
                        $res[$k] = $res[$k] + $key['c'];
                    }else{
                        $res[$k] = $key['c'];
                    }
                }
            }
        }
        return $res;
    }

    /**
     * 构建APP端二维码URL
     * @param $value
     * @param $data
     * @return string
     */
    public function getAppUrlAttr($value, $data)
    {
        $mobile_url = Url::build('Mobile/Goods/goodsInfo', ['id' => $data['goods_id']], false, true);
        $mobile_url_code = urlencode($mobile_url);
        $store_logo = tpCache('shop_info.store_logo');
        $head_pic = "http://{$_SERVER['HTTP_HOST']}/".$store_logo;
        $goods_app_url = Url::build('Home/Index/qr_code', ['data' => $mobile_url_code,'head_pic'=>$head_pic], false);
        return $goods_app_url;
    }


    /**
     * 手机商品描述
     * @param $value
     * @param $data
     * @return string
     */
    public function getMobileContentHtmlAttr($value, $data)
    {
        $mobile_body_array = unserialize($data['mobile_content']);
        $mobile_body = '';
        if (is_array($mobile_body_array)) {
            foreach ($mobile_body_array as $val) {
                switch ($val['type']) {
                    case 'text':
                        $mobile_body .= '<div>' . $val['value'] . '</div>';
                        break;
                    case 'image':
                        $mobile_body .= '<img src="' . $val['value'] . '">';
                        break;
                }
            }
        }
        return $mobile_body;
    }
    public function getMobileContentAttr($value, $data)
    {
        return unserialize($data['mobile_content']);
    }
    public function getMobileBodyAttr($value, $data)
    {
        if ($data['mobile_content'] != '') {
            $mobile_content = unserialize($data['mobile_content']);
            if (is_array($mobile_content)) {
                $body = '[';
                foreach ($mobile_content as $val ) {
                    $body .= '{"type":"' .$val['type']. '","value":"' .$val['value']. '"},';
                }
                $r_trim_body = rtrim($body, ',') . ']';
                return trim($r_trim_body);
            }else{
                return '';
            }
        }else{
            return '';
        }
    }


    //获取商品规格
    public function getSpecAttr($value, $data)
    {
        $spec_goods_price_key = db('spec_goods_price')->where("goods_id", $data['goods_id'])->column('key');
        if($spec_goods_price_key){
            $spec_goods_price_key_str = implode('_', $spec_goods_price_key);
            $spec_goods_price_key_arr = explode('_', $spec_goods_price_key_str);
            $spec_goods_price_key_arr = array_unique($spec_goods_price_key_arr);
            $spec_item_list = db('spec_item')->where('id', 'IN', $spec_goods_price_key_arr)->select();
            $spec_ids = get_arr_column($spec_item_list, 'spec_id');
            $spec_list = db('spec')->where('id', 'IN', $spec_ids)->order('`order` desc, id asc')->select();
            foreach($spec_list as $spec_key=>$spec_val){
                foreach($spec_item_list as $spec_item_key=>$spec_item_val){
                    if($spec_val['id'] == $spec_item_val['spec_id']){
                        $spec_list[$spec_key]['spec_item'][] = $spec_item_val;
                    }
                }
            }
            return $spec_list;
        }
        return [];
    }

    /**
     * 获取商家绑定平台的商品
     */
    public function getBindPlatformGoodsAttr($value, $data){
        $platformGoods =  db('store_bind_platform_goods')->where(['store_id'=>STORE_ID,'goods_id'=>$data['goods_id'],'id_delete'=>0])->find();
        return $platformGoods?$platformGoods:false;
    }

}