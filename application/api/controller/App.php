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
 */ 
namespace app\api\controller;

/**
 * Description of App
 *
 */
class App extends Base
{
    /**
     * 获取最新的app
     */
    public function latest_old()
    {
        $inVersion = input('get.version', '0');
        if ($inVersion === null || $inVersion === '') {
            $this->ajaxReturn(['status' => -1, 'msg' => 'app版本号无效']);
        }
        
        $app = tpCache('mobile_app'); // android
        if (strnatcasecmp($app['android_app_version'], $inVersion) > 0) {
            $this->ajaxReturn(['status' => 1, 'msg' => '有新版本', 'result' => [
                'new' => 1,
                'url' => SITE_URL.'/'.$app['android_app_path'],
                'log' => $app['android_app_log']
            ]]);
        }
        
        $this->ajaxReturn(['status' => 1, 'msg' => '无新版本', 'result' => ['new' => 0]]);
    }

    /**
     * 获取最新的app 这个不行，就用上个
     */
    public function latest()
    {
        $inVersion = input('get.version', '0');
        if ($inVersion === null || $inVersion === '') {
            $this->ajaxReturn(['status' => -1, 'msg' => 'app版本号无效']);
        }

        $app = tpCache('android'); // mobile_app
        if (strnatcasecmp($app['app_version'], $inVersion) > 0) { //android_app_version
            $this->ajaxReturn(['status' => 1, 'msg' => '有新版本', 'result' => [
                'new' => 1,
                'url' => SITE_URL.'/'.$app['app_path'], // android_app_path
                'log' => $app['app_log'] // android_app_log
            ]]);
        }

        $this->ajaxReturn(['status' => 1,'msg' => '无新版本', 'result' => ['new' => 0]]);
    }
    
     /**
     *  搬过来的  获取最新的app
     */
    public function latest_s()
    {
        $inVersion = input('get.version', '0');
        if ($inVersion === null || $inVersion === '') {
            $this->ajaxReturn(['status' => -1, 'msg' => 'app版本号无效']);
        }
        $app = input('app');
        $app = tpCache($app);
        if (strnatcasecmp($app['app_version'], $inVersion) > 0) {
            $this->ajaxReturn(['status' => 1, 'msg' => '有新版本', 'result' => [
                'status' => $app['is_audit'],
                'new' => 1,
                'url' => SITE_URL.'/'.$app['app_path'],
                'log' => $app['app_log']
            ]]);
        }
        
        $this->ajaxReturn(['status' => 1, 'msg' => '无新版本', 'result' => ['new' => 0]]);
    }
        /**
     * 获取审核状态
     */
    public function audit(){
        $app = input('app');
        $app = tpCache($app);
        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $app]);
    }
       /**
     * 查看小程序是否审核
     */
    public function mini_app()
    {
        $app = tpCache('miniApp.is_audit');
        $this->ajaxReturn(['status' => 1, 'msg' => '无新版本', 'result' => ['status' => $app]]);
    }
    
}
