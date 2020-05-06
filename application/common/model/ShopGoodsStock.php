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
 * Author: IT宇宙人
 * Date: 2015-09-09
 */
namespace app\common\model;

use think\Db;
use think\Model;
use app\common\logic\CartLogic;

class ShopGoodsStock extends Model
{
    private $user_id=0;
    private $shop_id=0;
    public function setUserID($user_id){
        $this->user_id=$user_id;
    }
    public function setShopID($shop_id){
        $this->shop_id=$shop_id;
    }
    /*
     * 门店商品对应规格信息
     * @return $this
     */
    public function spec(){
        return $this->hasOne('spec_goods_price','item_id','item_id')->field('spec_img,price');
    }

    public function getKeyArrAttr($value, $data)
    {
        if ($data['key'] != '') {
            return explode('_', $data['key']);
        } else {
            return '';
        }
    }

    public function getGoodsCartInfoAttr($value, $data)
    {
        $cartLogic = new CartLogic();
        $cartLogic->setUserId($this->user_id);
        $cartLogic->setShopId($this->shop_id);
        $cartList = $cartLogic->getCartList(1); // 获取用户选中的购物车商品
        foreach ($cartList as $key=>$value){
            if ($data['sgs_id'] == $value['sgs_id']){
                $goods_num =  $value['goods_num'];
                $cart_id =  $value['id'];
            }
        }
        $res = [
            'goods_num' =>$goods_num ? $goods_num :0,
            'cart_id'   =>$cart_id    ?$cart_id:0,
        ];
        return $res;
    }
}
