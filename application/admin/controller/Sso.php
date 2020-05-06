<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采有最新thinkphp5助手函数特性实现函数简写方式M D U等,也可db::name方式,可双向兼容
 * ============================================================================
 * @Author: lhb
 */

namespace app\admin\controller;

use think\Controller;
use app\common\logic\Saas;

class Sso extends Controller
{
    public function logout()
    {
        $ssoToken = input('sso_token', '');

        $return = Saas::instance()->ssoLogout($ssoToken);

        ajaxReturn($return);
    }
}