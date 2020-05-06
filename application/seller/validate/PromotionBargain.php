<?php
namespace app\seller\validate;
use think\Validate;
use think\Db;
class PromotionBargain extends Validate
{
    // 验证规则
    protected $rule = [
        ['id','checkId'],
        ['title', 'require|checkTitle'],
        ['goods_id', 'require'],
        ['buy_limit','require|gt:0|checkLimit'],
        ['start_time','require'],
        ['end_time','require|checkEndTime'],
        ['cut_statr_range','require|egt:0'],
        ['cut_end_range','require|egt:0|checkCutEndRange'],
        ['order_overtime','require|egt:10'],
        ['cut_limit','require'],
        ['team_goods_item','require|checkTeamGoodsItem'],
//        ['goods_num','require|gt:0|checkGoodsNum'],

    ];
    //错误信息
    /**
     * 检查限购数量
     * @param $value|验证数据
     * @param $rule|验证规则
     * @param $data|全部数据
     * @return bool|string
     */
    protected function checkLimit($value, $rule ,$data)
    {
        foreach($data['team_goods_item'] as $item){
            if($value > $item['goods_num']){
                return '活动数量不能超过参与商品数量';
            }
        }

        return true;
    }

    protected function checkTitle($value, $rule ,$data)
    {

        $find = db('promotion_bargain')->where(['title'=>$value,'deleted'=>0,'id'=>['neq',$data['id']]])->value('title');
        if($find){
            return '已存在相同抢购标题';
        }
        return true;
    }
    protected $message  = [
        'title.require'         => '抢购标题必填',
        'title.unique'      => '已存在相同抢购标题',
        'goods_id.require'      => '请选择参与砍价的商品',
//        'goods_num.require'     => '请填写参加抢购数量',
//        'goods_num.gt'          => '抢购数量必须是大于0的数字',
        'buy_limit.require'     => '请填写限购数量',
        'buy_limit.gt'          => '限购数量必须是大于0的数字',
        'start_time.require'    => '请选择开始时间',
        'end_time.require'      => '请选择结束时间',
        'end_time.checkEndTime' => '结束时间不能早于开始时间',
        'cut_statr_range.require'      => '请填写砍价金额范围',
        'cut_statr_range.egt'          => '砍价金额范围必须是大于等于0的数字',
        'cut_end_range.require'        => '请填写砍价金额范围',
        'cut_end_range.egt'            => '砍价金额范围必须是大于等于0的数字',
        'order_overtime.require'      => '请填写订单超时时间',
        'order_overtime.egt'          => '订单超时时间必须是大于等于10分钟',
        'cut_limit.require'      => '请填写砍价限制',
        'team_goods_item.require'   => '请选择参与砍价的商品',

    ];

    //检测商品和价格
    protected function checkTeamGoodsItem($value, $rule ,$data){
        $regex = '([0-9]\d*(\.\d*[0-9])?)|(0\.\d*[1-9])';
        foreach($value as $item){
            if(!array_key_exists('end_price', $item)){
                return '最低价格必须填写';
            }
            if(!$this->regex($item['end_price'], $regex)){
                return '最低价格格式错误';
            }
            if($item['end_price'] > $item['start_price']){
                return '最低价格不能大于商品价格';
            }
            if($item['item_id'] > 0){
                //商品规格
                $spec_goods_price = Db::name("spec_goods_price")->field('key_name,price,store_count')->where(['item_id'=>$item['item_id']])->find();
                if($item['end_price'] > $spec_goods_price['price']){
                    return $spec_goods_price['key_name'].'最低价格必须低于商品价格'.$spec_goods_price['price'];
                }
                if($data['cut_end_range'] > $spec_goods_price['price']){
                    return '砍价金额范围不能大于商品价格';
                }
                if($item['goods_num'] > $spec_goods_price['store_count']){
                    return $spec_goods_price['key_name'].'参与数量不能大于现有库存'.$spec_goods_price['store_count'];
                }
            }else{
                $goods = Db::name('goods')->field('goods_name,shop_price,store_count')->where('goods_id',$item['goods_id'])->find();
                if($item['end_price'] > $goods['shop_price']){
                    return $goods['goods_name'].'最低价格必须低于单买价格'. $goods['shop_price'];
                }
                if($item['goods_num'] > $goods['store_count']){
                    return $goods['goods_name'].'参与数量不能大于现有库存'.$goods['store_count'];
                }
                if($data['cut_end_range'] > $goods['shop_price']){
                    return '砍价金额范围不能大于商品价格';
                }
            }
        }
        return true;
    }

    /**
     * 检查结束时间
     * @param $value|验证数据
     * @param $rule|验证规则
     * @param $data|全部数据
     * @return bool|string
     */
    protected function checkEndTime($value, $rule ,$data)
    {
        return ($value < $data['start_time']) ? false : true;
    }
    /**
     * 检查价格
     * @param $value|验证数据
     * @param $rule|验证规则
     * @param $data|全部数据
     * @return bool|string
     */
    protected function checkPrice($value, $rule ,$data)
    {
        if($data['item_id'] > 0){
            //商品规格
            $price = Db::name("spec_goods_price")->where(['item_id'=>$data['item_id']])->getField('price');
        }else{
            $price = Db::name('goods')->where('goods_id',$data['goods_id'])->getField('shop_price');
        }
        if($value < $data['end_price']){
            return '初始价格不得低于商品底价';
        }
        return ($value > $price) ? false : true;
    }
    /**
     * 检查参与数量
     * @param $value|验证数据
     * @param $rule|验证规则
     * @param $data|全部数据
     * @return bool|string
     */
//    protected function checkGoodsNum($value, $rule ,$data)
//    {
//        if($value == 0){
//            return '数量不能为零';
//        }
//        $status = 0;
//        if(!$data['team_goods_item']){
//            return '请先添加砍价商品，根据商品库存来填写数量';
//        }
//        foreach($data['team_goods_item'] as $item){
//            if($item['item_id'] > 0){
//                //商品规格
//                $store_count = Db::name("spec_goods_price")->field('key_name,price')->where(['item_id'=>$item['item_id']])->value('store_count');
//            }else{
//                $store_count = Db::name('goods')->field('goods_name,shop_price')->where('goods_id',$item['goods_id'])->value('store_count');
//            }
//            if($value > $store_count){
//                $status = 1;
//            }
//        }
//
//        return $status ? '参与砍价数量不能大于库存数量' : true;
//
//    }
    /**
     * 该活动是否可以编辑
     * @param $value|验证数据
     * @param $rule|验证规则
     * @param $data|全部数据
     * @return bool|string
     */
    protected function checkId($value, $rule ,$data)
    {
        $isHaveOrder = Db::name('bargain_first')->where(['bargain_id'=>$value])->find();
        if($isHaveOrder){
            return '该活动已有用户下单购买不能编辑';
        }else{
            return true;
        }
    }


    /**
     * 判断砍价金额范围
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    protected function checkCutEndRange($value, $rule , $data){
        if($value <= $data['cut_statr_range']){
            return '砍价金额范围必须后面的大于前面';
        }
        return true;
    }



}