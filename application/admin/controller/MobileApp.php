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
 * 移动app类
 * Date: 2017-6-5
 */

namespace app\admin\controller;
  
class MobileApp extends Base 
{
    
    public function basic()
    {
        $inc_type =  'app';
        $param = I('post.'); 
     
        unset($param['inc_type']);
        unset($param['form_submit']);
        if(IS_POST){
            tpCache($inc_type,$param);
        }  
        $this->assign('inc_type', $inc_type);
        $this->assign('config', tpCache($inc_type));//当前配置项
        return $this->fetch();
    }
    
    /**
     * Android管理面板
     * @return \think\mixed
     */
    public function android_panel()
    {
		$inc_type =  'android';
		$this->assign('inc_type', $inc_type);
		$this->assign('config', tpCache($inc_type));//当前配置项
		return $this->fetch();
    }
    
     /**
     * IOS管理面板
     * @return \think\mixed
     */
    public function ios_panel()
    {
        $param = I('post.');
		$inc_type = 'ios';
		unset($param['inc_type']);
        unset($param['form_submit']);
          
        if(IS_POST){
            tpCache($inc_type,$param);
        }
        
        $this->assign('inc_type', $inc_type);
        $this->assign('config', tpCache($inc_type));//当前配置项
        return $this->fetch();
    }

    /**
     * 小程序管理面板
     * @return \think\mixed
     */
    public function mini_app()
    {
        $param = I('post.');
        $inc_type = 'miniApp';
        unset($param['inc_type']);
        unset($param['form_submit']);

        if(IS_POST){
            tpCache($inc_type,$param);
        }

        $this->assign('inc_type', $inc_type);
        $this->assign('config', tpCache($inc_type));//当前配置项
        return $this->fetch();
    }


    /**
     * 修改配置
     */
    public function handle()
    {
        $param = I('post.');
		$inc_type = $param['inc_type'];
        
        $file = request()->file('app_path');
        if ($file) {
            $result = $this->validate(
                ['android_app' => $file], 
                ['android_app'=>'image','android_app'=>'fileSize:40000000','android_app'=>'fileExt:apk,ipa,pxl,deb'],
                ['android_app.image' => '上传文件必须为图片','android_app.fileSize' => '上传文件过大','android_app.fileExt'=>'文件格式不正确']                
            );
            if (true !== $result) {        
                return $this->error('上传文件出错：'.$result, url('MobileApp/android_panel'));
            }
            $savePath = 'public/upload/appfile/';
            $saveName = 'android_'.$param['app_version'].'_'.date('ymd_His').'.'.pathinfo($file->getInfo('name'), PATHINFO_EXTENSION);
            $info = $file->move($savePath, $saveName);
            if (!$info) {
                return $this->error('文件保存出错', url('MobileApp/android_panel'));
            }
            $return_url = $savePath.$info->getSaveName();
            tpCache($inc_type, ['app_path' => $return_url]);
        }
        
        tpCache($inc_type, ['app_version' => $param['app_version']]);
        tpCache($inc_type, ['is_audit' => $param['is_audit']]);
        tpCache($inc_type, ['app_log' => $param['app_log']]);
        
        if (!$file) {
            return $this->success("保存成功，但是没有文件上传", url('MobileApp/android_panel'));
        }
		return $this->success("操作成功", url('MobileApp/android_panel'));
    }
}