<?php
namespace app\seller\validate;
use think\Validate;
use think\Db;
class GroupBuy extends Validate
{
    // 验证规则
    protected $rule = [
        ['id','checkId'],
        ['title', 'require'],
        ['goods_id', 'require'],
        ['goods_name', 'require'],
        ['start_time','require'],
        ['end_time','require|checkEndTime'],
        ['intro','max:100'],
        ['team_goods_item','require|checkTeamGoodsItem'],
    ];
    //错误信息
    protected $message  = [
        'title.require'         => '团购标题必填',
        'start_time.require'    => '请选择开始时间',
        'end_time.require'      => '请选择结束时间',
        'end_time.checkEndTime' => '结束时间不能早于开始时间',
        'goods_name.require'    => '请填写商品名称',
        'goods_id.require'      => '请选择参与团购的商品',
        'intro.max'             => '活动介绍小于100字符',
        'team_goods_item.require'   => '请选择参与团购的商品',
    ];
    protected $scene = [
        'add' =>['title','goods_id','goods_name', 'start_time', 'end_time', 'intro', 'team_goods_item'],
        'edit' =>['title','goods_id','goods_name', 'start_time', 'end_time', 'intro', 'team_goods_item'],
        'del' =>['id'],
    ];

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

    //检测商品和价格
    protected function checkTeamGoodsItem($value){
        $regex = '([0-9]\d*(\.\d*[0-9])?)|(0\.\d*[1-9])';
        foreach($value as $item){
            if(!array_key_exists('price', $item)){
                return '团购价格必须填写';
            }
            if(!$this->regex($item['price'], $regex)){
                return '团购价格格式错误';
            }
            if($item['price'] > $item['goods_price']){
                return '团购价不能大于商品价格';
            }
            if($item['item_id'] > 0){
                //商品规格
                $spec_goods_price = Db::name("spec_goods_price")->field('key_name,price,store_count')->where(['item_id'=>$item['item_id']])->find();

                if($item['goods_num'] > $spec_goods_price['store_count']){
                    return $spec_goods_price['key_name'].'参与数量不能大于现有库存'.$spec_goods_price['store_count'];
                }
            }else{
                $goods = Db::name('goods')->field('goods_name,shop_price,store_count')->where('goods_id',$item['goods_id'])->find();

                if($item['goods_num'] > $goods['store_count']){
                    return $goods['goods_name'].'参与数量不能大于现有库存'.$goods['store_count'];
                }
            }
        }
        return true;
    }
    /**
     * 该活动是否可以编辑
     * @param $value|验证数据
     * @param $rule|验证规则
     * @param $data|全部数据
     * @return bool|string
     */
    protected function checkId($value, $rule ,$data)
    {
        $isHaveOrder = Db::name('order_goods')->where(['prom_type'=>2,'prom_id'=>$value])->find();
        if($isHaveOrder){
	 //清华说不要判断
//            return '该活动已有用户下单购买不能编辑';
            return true;
        }else{
            return true;
        }
    }
}