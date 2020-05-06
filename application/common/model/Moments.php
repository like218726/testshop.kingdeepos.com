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
 * Author: JY
 * Date: 2015-09-09
 */
namespace app\common\model;
use think\Model;
class Moments extends Model {

    public static $STATUS_SUCCESS = 1;
    public static $STATUS_WAIT = 0;
    public static $STATUS_FAIL = 2;
    public static $DETELE_YES = 1;
    public static $DETELE_NO = 0;
    public static $READ_YES = 1;
    public static $READ_NO = 0;
    
    
    public static $STATUS = array(0=>'审核中',1=>'通过',2=>'不通过');
    
    
    public function MomentsClassify()
    {
        return $this->hasOne(MomentsClassify::class);
    }
    
    public function users(){
        return $this->hasOne('users','user_id','user_id');
    }

}
