<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/18
 * Time: 14:39
 */

namespace app\seller\logic;
use app\admin\logic\StoreLogic;
use think\Model;
use think\Db;

class AdminLogic extends Model{

    /**
     * 修改店铺管理员密码（既是会员密码）
     * @param string $data
     * @return array
     */
    public function alterAdminPassword($data=''){
        $seller = session('seller');//修改密码
        if ($data['seller_id'] == $seller['seller_id'] || $seller['is_admin'] == 1) {
            $old_password = trim(I('old_password'));
            $new_password = trim(I('new_password'));
            if ($old_password == $new_password) {
                return ['status' =>-1,'msg'=>"两次密码一致"];
            } else {
                if (M('users')->where(array('user_id' => $data['user_id'], 'password' => encrypt($old_password)))->count() > 0) {
                    $r = M('users')->where(array('user_id' => $data['user_id']))->save(array('password' => encrypt($new_password)));
                    if ($r !== false) {
                        M('seller')->where(['seller_id'=>$data['seller_id']])->save(array('enabled' => $data['enabled'],'group_id'=>$data['group_id']));
                        return ['status' =>1, 'msg'=>"密码修改成功", 'url'=>U('Admin/index')];
                    } else {
                        return ['status' =>-1, 'msg'=>"密码修改失败",];
                    }
                } else {
                    return ['status' =>-1,'msg'=>"原密码错误",];
                }
            }
        } else {
            return ['status' =>-1,'msg'=>"非法操作,只能修改自己的密码",];
        }
    }

    /**
     * 添加店铺管理员
     * @param $data
     * @return array
     */
    public function addStoreAdmin($data){
        if(empty($data['seller_name']) || empty($data['user_name']) || empty($data['password']) ){
            return ['status' =>-1,'msg'=>"所有信息为必填",];
        }
        //验证商家后台登陆账号是否有同名
        if (M('seller')->where("seller_name", $data['seller_name'])->count()) {
            return ['status' =>-1,'msg'=>"此登陆账号名已被注册，请更换"];
        }
        $uname = check_email($data['user_name']) ? 'email' : 'mobile';

        //查找验证绑定用户
        $userinfo = M('users')->field('password,user_id')->where([$uname=>$data['user_name']])->find();
        if (empty($userinfo)) {
            return ['status' =>-1,'msg'=>"请先注册前台会员",];
        } elseif ($userinfo['password'] != encrypt($data['password'])) {
            return ['status' =>-1,'msg'=>"登陆密码错误",];
        } else {
            if (M('seller')->where("user_id", $userinfo['user_id'])->count()) {
                return ['status' =>-1,'msg'=>"该用户已经添加过店铺管理员",];
            }
            $data['password'] = encrypt($data['password']);
            $data['user_id'] = $userinfo['user_id'];
            $data['store_id'] = STORE_ID;
            $data['add_time'] = time();
            unset($data['seller_id']);
            $r = M('seller')->add($data);
            if ($r !== false) {
                return ['status' =>1, 'msg'=>"添加成功", 'url'=>U('Admin/index')];
            } else {
                return ['status' =>-1, 'msg'=>"添加失败",];
            }
        }
    }

    /**
     * 店铺APP登录
     * @param $seller_name
     * @param $password
     * @return array
     */
    public function sellerApiLogin($seller_name,$password){
        if (!empty($seller_name) && !empty($password)) {
            $seller = Db::name('seller')->where(array('seller_name' => $seller_name))->find();
            if ($seller) {
                $store = Db::name('store')->where(array('store_id'=>$seller['store_id'],'store_state'=>1))->find();
                if(!$store) return ['status' => 0, 'msg' => '店铺已关闭，请联系平台客服'];
                if($store['store_end_time']>0 && $store['store_end_time']<time()){
                    Db::name('store')->where(array('store_id'=>$seller['store_id']))->save(array('store_state'=>0));
                    Db::name('goods')->where(array('store_id'=>$seller['store_id']))->save(array('is_on_sale'=>0));
                    return ['status' => 0, 'msg' => '店铺已到期自动关闭，请联系平台客服'];
                }

                $user = Db::name('users')->where(['user_id' => $seller['user_id'],'password' => encrypt($password)])->find();
                if ($user) {
                    if ($seller['is_admin'] == 0 && $seller['enabled'] == 1) {
                        return ['status' => 0, 'msg' => '该账户还没启用激活'];
                    }
                    if ($seller['group_id'] > 0) {
                        $group = Db::name('seller_group')->where(array('group_id' => $seller['group_id']))->find();
                        $seller['act_limits'] = $group['act_limits'];
                        $seller['smt_limits'] = $group['smt_limits'];
                    } else {
                        $seller['act_limits'] = 'all';
                        $seller['smt_limits'] = 'all';
                    }
                    $user['token'] = md5(time().mt_rand(1,999999999));
                    $data = ['token' => $user['token'], 'last_login_time' => time()];
                    Db::name('seller')->where(array('seller_id' => $seller['seller_id']))->save($data);
                    return ['status' => 1, 'msg' => '登录成功', 'data'=>$seller];
                } else {
                    return ['status' => 0, 'msg' => '账号密码不正确'];
                }
            } else {
                return ['status' => 0, 'msg' => '账号不存在'];
            }
        } else {
            return ['status' => 0, 'msg' => '请填写账号密码'];
        }
    }

    /**
     *  商家登录后 处理相关操作
     */
    public function login_task()
    {
        
        // 多少天后自动分销记录自动分成
        if(file_exists(APP_PATH.'common/logic/DistributLogic.php')){
            $distributLogic = new \app\common\logic\DistributLogic();
            $distributLogic->autoConfirm(STORE_ID); // 自动确认分成
        }

        // 商家结算
        $storeLogic = new StoreLogic();
        $storeLogic->auto_transfer(STORE_ID); // 自动结算

    }
    
    /**
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param array $files
     * @return array
     */
    public function getfiles($path, $allowFiles, $key, $showDetail=false ,&$files = array() ){
        if (!is_dir($path)) return null;
        if(substr($path, strlen($path) - 1) != '/') $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                $cnFile = iconv("gb2312","utf-8",$file);
                if (is_dir($path2)) {
                    $this->getfiles($path2, $allowFiles, $key, $showDetail , $files);
                } else {
                    if (preg_match("/\.(".$allowFiles.")$/i", $file) && preg_match("/.*". $key .".*/i", $file)) {
                        
                        $fileInfo = array(
                            'url'=> '/'.$path.$cnFile,
                            'name'=> $cnFile,
                            'mtime'=> filemtime($path2)
                        );
                        
                        if($showDetail === true){
                            //将获取图片信息保存到goods_images中
                            $imageSize= getimagesize($path2);
                            $filesize = filesize($path2);
                            $space =((int)($filesize/10))/100;//文件大小
                            if($imageSize !== false){
                                $sizeWhStr = $imageSize[3];
                                if(preg_match_all('/\d+/',$sizeWhStr,$arr)){
                                    $width = $arr[0][0];
                                    $height = $arr[0][1];
                                    $size = $width.'x'.$height;   //图片尺寸
                                }else{
                                    $size = '0x0';
                                }
                            } 
                            
                            $fileInfo['size'] = $size;
                            $fileInfo['space'] = $space;
                        } 
                        
                        $files[] = $fileInfo;
                    }
                }
            }
        }
        return $files;
    }
}