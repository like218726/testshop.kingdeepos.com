<?php
namespace app\shop\validate;
use think\Validate;
use think\Db;
class Shopper extends Validate
{
    // 验证规则
    protected $rule = [
        'shopper_name'              =>'require|unique:shopper',
        'user_name'                 =>'require|checkUserName',
    ];
    //错误信息
    protected $message  = [
        'shopper_name.require'                  => '门店职员必须',
        'shopper_name.unique'                   => '门店职员重复',
        'user_name.require'                     => '前台用户名必须',
    ];


    /**
     * 检查门店职员
     * @param $value|验证数据
     * @param $rule|验证规则
     * @param $data|全部数据
     * @return bool|string
     */
    protected function checkUserName($value, $rule ,$data)
    {
        $isEmail = check_email($value);
        $isMobile = check_mobile($value);
        if(!$isEmail && !$isMobile){
           return '前台用户名格式错误,应为手机或者邮箱';
        }else{
            if($isEmail){
                $user_where['email'] = $data['user_name'];
            }else{
                $user_where['mobile'] = $data['user_name'];
            }
            $user = Db::name('users')->field('password,user_id')->where($user_where)->find();
            if(empty($user)){
                return '前台用户名不存在';
            }else{
                $shopperCount = Db::name('shopper')->where("user_id", $user['user_id'])->count();
                if($shopperCount > 0){
                    return '该用户已经添加过门店管理员';
                }else{
                    return true;
                }
            }
        }
    }


}