<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 商业用途务必到官方购买正版授权, 使用盗版将严厉追究您的法律责任。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * $Author: IT宇宙人 2015-08-10 $ 广告相关API
 */ 
namespace app\api\controller;
 
class Ad extends Base {
   
    public function ad_home()
    {
        /**
         * TPshop APP端广告位PID 区间是: 500 ~ 549
         * 首页: 500 -> 520
         * 分类: 531 ;  店铺街:532; 品牌街:533;   团购:534;  积分商城:535;
         * media_type: 3:商品;4:分类;5:url
         */
        $edit_ad = input('edit_ad');
        $this->assign('edit_ad', $edit_ad);
        return $this->fetch();
    }
    
    public function ad_category()
    {
        return $this->fetch();
    }
    
    public function ad_common()
    { 
        return $this->fetch();
    }
    
}