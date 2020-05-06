<?php
namespace app\seller\validate;
use think\Validate;
use think\Db;
class Goods extends Validate
{
    
    // 验证规则
    protected $rule = [
        'goods_id'             =>'checkGoodsId',
        'goods_name'           =>'require|min:3|max:150|unique:goods,goods_name^store_id',
        'goods_remark'         =>'max:420',
        'cat_id3'              => 'number|gt:0|checkCatId3',
        'goods_sn'             => 'unique:goods|max:20', // 更多 内置规则 http://www.kancloud.cn/manual/thinkphp5/129356
        'shop_price'           =>['regex'=>'([1-9]\d*(\.\d*[1-9])?)|(0\.\d*[1-9])', 'checkShopPrice'],
        'market_price'         =>'require|regex:\d{1,10}(\.\d{1,2})?$|checkMarketPrice',
        'weight'               =>'regex:\d{1,10}(\.\d{1,2})?$',
        'give_integral'        =>'regex:^\d+$',
        'exchange_integral'    =>'checkExchangeIntegral',
        'is_virtual'           =>'checkVirtualIndate',
        'distribut'            =>'checkDistribut',
        'is_free_shipping'     =>'require|checkShipping',
        'original_img'         =>'require',
		'cost_price'           =>'require|regex:\d{1,10}(\.\d{1,2})?$|checkCostPrice',
        'item'                 =>'checkBySave|checkBySupplierSave'
    ];
    //错误信息
    protected $message  = [
        'goods_name.require'                            => '商品名称必填',
        'goods_name.min'                                => '名称长度至少3个字符',
        'goods_name.max'                                => '名称长度至多50个汉字',
        'goods_name.unique'                             => '商品名称重复',
        'cat_id3.number'                                => '商品分类必须选择到三级',
        'cat_id3.gt'                                    => '商品分类必须选择到第三级',
        'cat_id3.checkCatId3'                           => '本店铺无此经营类目或在审核中',
        'goods_sn.unique'                               => '商品货号重复',
        'goods_sn.max'                                  => '商品货号超过长度限制',
        'goods_num.checkGoodsNum'                       => '抢购数量不能大于库存数量',
        'shop_price.regex'                              => '本店售价格式不对',
        'shop_price.checkShopPrice'                     => '本店售价不得低于供货价',
        'market_price.require'                          => '市场价格必填',
        'market_price.regex'                            => '市场价格式不对',
        'market_price.checkMarketPrice'                 => '市场价不得小于本店价',
        'weight.regex'                                  => '重量格式不对',
        'give_integral.regex'                           => '赠送积分必须是正整数',
        'exchange_integral.checkExchangeIntegral'       => '积分抵扣金额不能超过商品总额',
        'is_virtual.checkVirtualIndate'                 => '虚拟商品有效期不得小于当前时间',
        'distribut.checkDistribut'                      => '分销的分成金额不得超过商品金额的50%',
        'is_free_shipping.require'                      => '请选择商品是否包邮',
        'original_img.require'                          => '请上传商品图片',
        'cost_price.require'                            => '供货价必填a',
        'cost_price.checkCostPrice'                     => '供货价不得高于市场价',
        'cost_price.regex'                              => '供货价格式不对',
    ];
	
	protected $scene = [
        'save' => [
            'goods_id',
            'goods_name',
            'goods_remark',
            'cat_id3',
            'goods_sn',
            'shop_price',
            'market_price',
            'weight',
            'give_integral',
            'exchange_integral','
            is_virtual',
            'distribut',
            'is_free_shipping',
            'original_img',
            'item' => 'checkBySave'
        ],
		'supplier_save' => [
		    'goods_id',
            'goods_name',
            'goods_remark',
            'cat_id3',
            'goods_sn',
            'market_price'=>'require|regex:\d{1,10}(\.\d{1,2})?$',
            'weight',
            'is_free_shipping',
            'original_img',
            'cost_price',
            'item' => 'checkBySupplierSave'
        ]
    ];
     
    
    /**
     * 检查积分兑换
     * @author dyr
     * @return bool
     */
    protected function checkExchangeIntegral($value, $rule, $data)
    {
        if ($value > 0) {
            $goods = Db::name('goods')->where('goods_id', $data['goods_id'])->find();
            if (!empty($goods)) {
                if ($goods['prom_type'] > 0) {
                    return '该商品参与了其他活动。设置兑换积分无效，请设置为零';
                }
            }
        }
        $point_rate_value = tpCache('shopping.point_rate');
        if($data['item']){
            $count = count($data['item']);
            $item_arr = array_values($data['item']);
            $minPrice = $item_arr[0]['price'];
            for($i = 0;$i < ($count - 1) ;$i++){
                if($item_arr[$i+1]['price'] < $minPrice){
                    $minPrice = $item_arr[$i+1]['price'];
                }
            }
            $goods_price = $minPrice;
        }else{
            $goods_price = $data['shop_price'];
        }

        $point_rate_value = empty($point_rate_value) ? 0 : $point_rate_value;
        if ($value > ($goods_price * $point_rate_value)) {
            return '积分抵扣金额不能超过商品总额';
        } else {
            return true;
        }
    }
    /**
     * 检查分销金额
     * @author dyr
     * @return bool
     */
    protected function checkDistribut($value, $rule ,$data)
    {
        if ($value > ($data['shop_price'] / 2)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 检查经营类目
     * @author dyr
     * @return bool
     */
    protected function checkCatId3($value, $rule ,$data)
    {
        $store = Db::name('store')->where(['store_id' => $data['store_id']])->find();
        if ($store['bind_all_gc'] == 1) {
            return true;
        }
        $bindClass = Db::name('store_bind_class')->where(['class_1' => $data['cat_id1'], 'class_2' => $data['cat_id2'], 'class_3' => $data['cat_id3'], 'store_id' => $data['store_id']])->find();
        if (!$bindClass || $bindClass['state'] != 1) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 检查虚拟商品有效时间
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     */
    protected function  checkVirtualIndate($value,$rule,$data){
        $virtualIndate = strtotime($data['virtual_indate']);
        if($value==1 && time() > $virtualIndate){
            return false;
        }else{
            return true;
        }
    }
    /**
     * 检查是否有商品规格参加活动，若有则不能编辑商品
     * @param $value
     * @return bool
     */
    protected function checkGoodsId($value,$rule,$data){
        /* 规格问题，只要不删除正在参与活动的规格，就可以修改。->where('prom_type','gt',0)
         * */
        $spec_goods_price = Db::name('spec_goods_price')->where('goods_id',$value)->find();
        if($spec_goods_price){
            // 在操作的时候，不会删除正在参与的活动
            return true;
            //return '该商品规格：'.$spec_goods_price['key_name'].'正在参与活动，不能编辑该商品信息';
        }
        $goods= Db::name('goods')->where('goods_id',$value)->find();
        if($goods['prom_type'] > 0){
            // 无规格时，只要不添加规格，就允许改，
            if(isset($data['item']))
                return '该商品正在参与活动，不能添加规格';
            //return '该商品规格正在参与活动，不能编辑该商品信息';
        }
        return true;
    }

    /**
     * 检查售价
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     */
    protected function  checkShopPrice($value,$rule,$data){
        if($value < $data['cost_price']){
            return false;
        }else{
            return true;
        }
    } 

    /**
     * 检查市场价
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     */
    protected function  checkMarketPrice($value,$rule,$data){
        if($value < $data['shop_price']){
            return false;
        }else{
            return true;
        }
    } 
	
	/**
     * 检查供货价
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     */
    protected function  checkCostPrice($value,$rule,$data){
        if($value > $data['market_price']){
            return false;
        }else{
            return true;
        }
    }

    protected function checkShipping($value,$rule,$data){
        if($value == 0 && empty($data['template_id'])){
            return '请选择运费模板';
        }else{
            return true;
        }
    }

    /**
 * 正常商品检查规格
 * @author dyr
 * @return bool
 */
    protected function checkBySave($value, $rule ,$data)
    {
        foreach ($value as $key => $val) {
            if ($val['price'] == 0) {
                return '规格价格必填';
            }
        }
        return true;
    }

    /**
     * 供应商品检查规格
     * @author dyr
     * @return bool
     */
    protected function checkBySupplierSave($value, $rule ,$data)
    {
        foreach ($value as $key => $val) {
            if ($val['cost'] == 0) {
                return '规格成本价必填';
            }
        }
        return true;
    }

}