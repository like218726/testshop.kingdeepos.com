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
 * Author: lhb
 */

namespace app\common\logic;

use think\Db;
use think\Session;

class AdminLogic
{
    public function login($username, $password)
    {
        if (empty($username) || empty($password)) {
            return ['status' => 0, 'msg' => '请填写账号密码'];
        }

        Saas::instance()->ssoAdmin($username, $password);

        $condition['a.user_name'] = $username;
        $condition['a.password'] = encrypt($password);
        $admin = Db::name('admin')->alias('a')->join('__ADMIN_ROLE__ ar', 'a.role_id=ar.role_id')->where($condition)->find();
        if (!$admin) {
            return ['status' => 0, 'msg' => '账号密码不正确'];
        }

        $this->handleLogin($admin, $admin['act_list']);

        $url = session('from_url') ? session('from_url') : U('Admin/Index/index');
        return ['status' => 1, 'url' => $url];
    }

    /**
     * 删除一个月前的旧数据
     */
    private function deleteOldMsg()
    {
        $old_time = time() - 60 * 60 * 24 * 30;//30天以前的时间戳
        $oldMsgId = Db::name('message')->where('send_time', 'lt', $old_time)->getField('message_id', true);
        if($oldMsgId){
            $user_msg_del = Db::name('user_message')->where('message_id', 'IN', $oldMsgId)->delete();
            if ($user_msg_del !== false) {
                Db::name('message')->where('message_id', 'IN', $oldMsgId)->delete();
            }
        }
    }

    public function handleLogin($admin, $actList)
    {
        Db::name('admin')->where('admin_id', $admin['admin_id'])->save([
            'last_login' => time(),
            'last_ip' => request()->ip()
        ]);

        $this->sessionRoleRights($admin, $actList);

        session('admin_id', $admin['admin_id']);
        session('last_login_time', $admin['last_login']);
        session('last_login_ip', $admin['last_ip']);
        if($admin['site_id']){
            $region_id = db('admin_site')->where(['site_id'=>$admin['site_id']])->value('region_id');
            if($region_id){
                session('city_site', $region_id);
            }
        }

        adminLog('后台登录');
        $this->deleteOldMsg();
    }

    public function sessionRoleRights($admin, $actList)
    {
        if (Saas::instance()->isNormalUser()) {
            $roleRights = Saas::instance()->getRoleRights($actList);
        } else {
            $roleRights = $actList;
        }

        session('act_list', $roleRights);
    }

    public function logout($adminId)
    {
        session_unset();
        session_destroy();
        Session::clear();

        Saas::instance()->handleLogout($adminId);
    }
}






