<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 商业用途务必到官方购买正版授权, 使用盗版将严厉追究您的法律责任。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 */

namespace app\admin\validate;

use think\Validate;

class Role extends Validate
{
    //验证规则
    protected $rule = [
        'role_name' => 'require|unique:admin_role',
        'role_desc' => 'require',
        'act_list' => 'require'
    ];

    //错误消息
    protected $message = [
        'role_name.require' => '角色名称不能为空',
        'role_name.unique' => '角色名称已经存在',
        'role_desc.require' => '角色描述不能为空',
        'act_list.require' => '权限分配必须选择',
    ];

    //验证场景
    protected $scene = [
        'save' => ['role_name', 'role_desc', 'act_list'],
    ];

}
