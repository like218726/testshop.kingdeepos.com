<?php
namespace app\common\validate;
use think\Validate;
use think\Db;
class Cart extends Validate
{
    // 验证规则
    protected $rule = [
        'user_note'                 =>'checkUserNote',
        'take_time'                 =>'checkTakeTime',
        'mobile'                 =>'checkMobile',
    ];
    //错误信息
    protected $message  = [
    ];


    /**
     * 检查用户备注
     * @param $value|验证数据
     * @param $rule|验证规则
     * @param $data|全部数据
     * @return bool|string
     */
    protected function checkUserNote($value, $rule ,$data)
    {
        foreach($value as $k => $val){
            $note_len = strlen($val);
            if ($note_len > 50) {
                return '留言长度最多为50个字符！';
            }
        }
        return true;
    }

    protected function checkTakeTime($value,$rule,$data)
    {
        if (!empty($data['shopid']) && $value < time()) {
            return '自提时间不能小于当前时间';
        }
        return true;
    }

    protected function checkMobile($value,$rule,$data)
    {
        if ($data['shop_id'] && !(check_mobile($value) || check_telephone($value))) {
            return '请输入正确的手机号';
        }
        return true;
    }

}