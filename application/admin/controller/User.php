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
 * Author: 当燃      
 * Date: 2015-09-09
 */
namespace app\admin\controller;
use app\common\logic\MomentsLogic;
use app\common\model\UserLabel;
use app\common\model\Users;
use think\AjaxPage;
use think\Page;
use think\Loader;
use think\Db;
use app\admin\logic\UsersLogic;
use app\common\model\Moments;
use app\common\model\MomentsClassify;
use app\common\validate\MomentsClassify as ValiMomentsClassify;

class User extends Base {

    public function index(){
        return $this->fetch();
    }

    /**
     * 会员列表
     */
    public function ajaxindex(){
        // 搜索条件
        $condition = array();
        $user_id = input('user_id');
        if($user_id){
            $condition['user_id'] = $user_id;
        }else{
            $nickname = I('nickname');
            $account = I('account');
            $account ? $condition['email|mobile'] = ['like',"%$account%"] : false;
            $nickname ? $condition['nickname'] = ['like',"%$nickname%"] : false;
        }
        
        I('first_leader') && ($condition['first_leader'] = I('first_leader')); // 查看一级下线人有哪些
        I('second_leader') && ($condition['second_leader'] = I('second_leader')); // 查看二级下线人有哪些
        I('third_leader') && ($condition['third_leader'] = I('third_leader')); // 查看三级下线人有哪些
        $sort_order = I('order_by').' '.I('sort');

        $count = Db::name('users')->where($condition)->count();
        $Page  = new AjaxPage($count,10);
        $userList = Db::name('users')->where($condition)->order($sort_order)
            ->limit($Page->firstRow.','.$Page->listRows)->select();

        $user_id_arr = get_arr_column($userList, 'user_id');
        if(!empty($user_id_arr))
        {
            $first_leader = DB::query("select first_leader,count(1) as count  from __PREFIX__users where first_leader in(".  implode(',', $user_id_arr).")  group by first_leader");
            $first_leader = convert_arr_key($first_leader,'first_leader');
            
            $second_leader = DB::query("select second_leader,count(1) as count  from __PREFIX__users where second_leader in(".  implode(',', $user_id_arr).")  group by second_leader");
            $second_leader = convert_arr_key($second_leader,'second_leader');            
            
            $third_leader = DB::query("select third_leader,count(1) as count  from __PREFIX__users where third_leader in(".  implode(',', $user_id_arr).")  group by third_leader");
            $third_leader = convert_arr_key($third_leader,'third_leader');            
        }
        $this->assign('first_leader',$first_leader);
        $this->assign('second_leader',$second_leader);
        $this->assign('third_leader',$third_leader);
                                
        $show = $Page->show();
        $this->assign('pager',$Page);
        $this->assign('userList',$userList);
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('level',M('user_level')->getField('level_id,level_name'));
        return $this->fetch();
    }

    /**
     * 会员详细信息查看
     */
    public function detail(){

        if(IS_POST){
            //  会员信息编辑
//            $password = I('post.password');
//            $password2 = I('post.password2');
            $user_id = I('post.user_id');
//            if($password != '' && $password != $password2){
//                $this->ajaxReturn(['status'=>0,'msg'=>'两次输入密码不同']);
//            }
//            if($password == '' && $password2 == ''){
//                unset($_POST['password']);
//            }else{
//                $_POST['password'] = encrypt($_POST['password']);
//            }
            
            if(!empty($_POST['email']))
            {   $email = trim($_POST['email']);
                $c = M('users')->where("user_id != $user_id and email = '$email'")->count();
                $c && $this->ajaxReturn(['status'=>0,'msg'=>'邮箱不得和已有用户重复']);
            }            
            
            if(!empty($_POST['mobile']))
            {   $mobile = trim($_POST['mobile']);
                $c = M('users')->where("user_id != $user_id and mobile = '$mobile'")->count();
                $c && $this->ajaxReturn(['status'=>0,'msg'=>'手机号不得和已有用户重复']);
            }              

            $row = M('users')->where(array('user_id'=>$user_id))->save($_POST);
            if($row)
                $this->ajaxReturn(['status'=>1,'msg'=>'修改成功']);
            $this->ajaxReturn(['status'=>0,'msg'=>'未作内容修改或修改失败']);
        }
        $uid = I('id/d',0);
        $user = Users::get(['user_id' => $uid]);
        if(!$user) {
            $this->error('会员不存在');
        }
        $user['first_lower'] = M('users')->where("first_leader = {$user['user_id']}")->count();
        $user['second_lower'] = M('users')->where("second_leader = {$user['user_id']}")->count();
        $user['third_lower'] = M('users')->where("third_leader = {$user['user_id']}")->count();
 
        $this->assign('user',$user);
        return $this->fetch();
    }
    
    
    public function add_user(){
    	if(IS_POST){
    		$data = I('post.');
    		$data['is_distribut'] = 1; // 默认每个人都可以成为分销商
    		$user_obj = new UsersLogic();
            $res = $user_obj->addUser($data);
    		if($res['status'] == 1){
    			$this->success('添加成功',U('User/index'));exit;
    		}else{
    			$this->error('添加失败,'.$res['msg'],U('User/index'));
    		}
    	}
    	return $this->fetch();
    }

    public function export_user(){
    	$strTable ='<table width="500" border="1">';
    	$strTable .= '<tr>';
    	$strTable .= '<td style="text-align:center;font-size:12px;width:120px;">会员ID</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="100">会员昵称</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">会员等级</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">手机号</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">邮箱</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">注册时间</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">最后登陆</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">余额</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">积分</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">累计消费</td>';
    	$strTable .= '</tr>';
        $user_id_array = trim(I('user_id_array'),',');
        $where = [];
        if($user_id_array){
            $where = ['user_id'=>['in',$user_id_array]];
        }
    	$count = Db::name('users')->where($where)->count();
    	$p = ceil($count/5000);
    	for($i=0;$i<$p;$i++){
    		$start = $i*5000;
    		$end = ($i+1)*5000;
    		$userList = Db::name('users')->where($where)->order('user_id')->limit($start,5000)->select();
    		if(is_array($userList)){
    			foreach($userList as $k=>$val){
    				$strTable .= '<tr>';
    				$strTable .= '<td style="text-align:center;font-size:12px;">'.$val['user_id'].'</td>';
    				$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['nickname'].' </td>';
    				$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['level'].'</td>';
    				$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['mobile'].'</td>';
    				$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['email'].'</td>';
    				$strTable .= '<td style="text-align:left;font-size:12px;">'.date('Y-m-d H:i',$val['reg_time']).'</td>';
    				$strTable .= '<td style="text-align:left;font-size:12px;">'.date('Y-m-d H:i',$val['last_login']).'</td>';
    				$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['user_money'].'</td>';
    				$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['pay_points'].' </td>';
    				$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['total_amount'].' </td>';
    				$strTable .= '</tr>';
    			}
    			unset($userList);
    		}
    	}
    	$strTable .='</table>';
    	downloadExcel($strTable,'users_'.$i);
    	exit();
    }
    
    /**
     * 用户收货地址查看
     */
    public function address(){
        $uid = I('get.id');
        $lists = D('user_address')->where(array('user_id'=>$uid))->select();
        $regionList = Db::name('region')->cache(true)->getField('id,name');
        $this->assign('regionList',$regionList);
        $this->assign('lists',$lists);
        return $this->fetch();
    }

    /**
     * 删除会员
     */
    public function delete(){
        $uid = I('get.id');
        
        //先删除ouath_users表的关联数据
        M('OuathUsers')->where(array('user_id'=>$uid))->delete();
        $row = M('users')->where(array('user_id'=>$uid))->delete();
        if($row){
            $this->success('成功删除会员');
        }else{
            $this->error('操作失败');
        }
    }
    /**
     * 删除会员
     */
    public function ajax_delete(){
        $uid = I('id');
        if($uid){
            $row = M('users')->where(array('user_id'=>$uid))->delete();
            if($row !== false){
                //把关联的第三方账号删除
                M('OauthUsers')->where(array('user_id'=>$uid))->delete();
                $this->ajaxReturn(array('status' => 1, 'msg' => '删除成功', 'data' => ''));
            }else{
                $this->ajaxReturn(array('status' => 0, 'msg' => '删除失败', 'data' => ''));
            }
        }else{
            $this->ajaxReturn(array('status' => 0, 'msg' => '参数错误', 'data' => ''));
        }
    }

    /**
     * 账户资金记录
     */
    public function account_log(){
        $user_id = I('get.id');
        //获取类型
        $type = I('get.type');
        //获取记录总数
        $count = M('account_log')->where(array('user_id'=>$user_id))->count();
        $page = new Page($count);
        $lists  = M('account_log')->where(array('user_id'=>$user_id))->order('change_time desc')->limit($page->firstRow.','.$page->listRows)->select();

        $this->assign('user_id',$user_id);
        $this->assign('page',$page->show());
        $this->assign('lists',$lists);
        return $this->fetch();
    }

    /**
     * 账户资金调节
     */
    public function account_edit(){
        $user_id = I('uid/d',0);
        if(!($user_id > 0))
            $this->ajaxReturn(['status'=>0,'msg'=>"参数有误"]);
        $user = M('users')->field('user_id,user_money,frozen_money,pay_points,is_lock')->where('user_id',$user_id)->find();
        if(IS_POST){
            $desc = I('post.desc');
            if(!$desc)
                $this->ajaxReturn(['status'=>0,'msg'=>'请填写操作说明']);
            //加减用户资金
            $m_op_type = I('post.money_act_type');
            $user_money = I('post.user_money/f',0);
            $user_money =  $m_op_type ? $user_money : 0-$user_money;
            if (($user['user_money']+$user_money)<0){
                $this->ajaxReturn(['status'=>0,'msg'=>"用户剩余资金不足！！"]);
            }
            //加减用户积分
            $p_op_type = I('post.point_act_type');
            $pay_points = I('post.pay_points/d',0);
            $pay_points =  $p_op_type ? $pay_points : 0-$pay_points;
            if(($pay_points+$user['pay_points'])<0 ){
                $this->ajaxReturn(['status'=>0,'msg'=>'用户剩余积分不足！！']);
            }
            //加减冻结资金
            $f_op_type = I('post.frozen_act_type');
            $acquire_frozen_money = I('post.frozen_money/f',0);
            $revision_frozen_money = 0;
            if( $acquire_frozen_money != 0){    //有加减冻结资金的时候
                $revision_frozen_money =  $f_op_type ? $acquire_frozen_money : 0-$acquire_frozen_money;
                $frozen_money = $user['frozen_money']+$revision_frozen_money;    //计算用户被冻结的资金
                if($f_op_type==1 && $acquire_frozen_money > $user['user_money']){
                    $this->ajaxReturn(['status'=>0,'msg'=>'用户剩余资金不足！！']);
                }
                if($f_op_type==0 && $acquire_frozen_money > $user['frozen_money']){
                    $this->ajaxReturn(['status'=>0,'msg'=>'冻结的资金不足！！']);
                }
                $user_money = $f_op_type ? 0-$acquire_frozen_money : $acquire_frozen_money ;    //计算用户剩余资金
                M('users')->where('user_id',$user_id)->update(['frozen_money' => $frozen_money]);
            }

            if(accountLog($user_id,$user_money,$pay_points,$desc,0,0,'',$revision_frozen_money != 0?false:true)){            
                $this->ajaxReturn(['status'=>1,'msg'=>'操作成功','url'=>U("Admin/User/account_log",array('id'=>$user_id))]);
            }else{
                $this->ajaxReturn(['status'=>0,'msg'=>'操作失败']);
            }
            exit;
        }
        $this->assign('user',$user);
        return $this->fetch();
    }
    
    public function recharge(){
        $timegap = I('timegap');
    	$nickname = I('nickname');
    	$map = array();
    	if($timegap){
    		$gap = explode(',', $timegap);
    		$begin = $gap[0];
    		$end = $gap[1];
            $this->assign('start_time',$begin);
            $this->assign('end_time',$end);
    		$map['ctime'] = array('between',array(strtotime($begin),strtotime($end)));
    	}
    	if($nickname){
    		$map['nickname'] = array('like',"%$nickname%");
    	}
    	$count = M('recharge')->where($map)->count();
    	$page = new Page($count,20);
    	$lists  = M('recharge')->where($map)->order('ctime desc')->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('pager',$page);
    	$this->assign('page',$page->show());
    	$this->assign('lists',$lists);
    	return $this->fetch();
    }
    
    public function level(){
    	$act = I('get.act','add');
    	$this->assign('act',$act);
    	$level_id = I('get.level_id');
    	$level_info = array();
    	if($level_id){
    		$level_info = D('user_level')->where('level_id='.$level_id)->find();
    		$this->assign('info',$level_info);
    	}
    	return $this->fetch();
    }
    
    public function levelList(){
    	$Ad =  M('user_level');
        $p = $this->request->param('p');    	
        $res = $Ad->order('level_id')->page($p.',10')->select();
    	if($res){
    		foreach ($res as $val){
    			$list[] = $val;
    		}
    	}
    	$this->assign('list',$list);
    	$count = $Ad->count();
        if($count > 10){
            $Page = new Page($count,10);
            $show = $Page->show();
            $this->assign('page',$show);
        }
    	return $this->fetch();
    }

    /**
     * 会员等级添加编辑删除
     */
    public function levelHandle()
    {
        $data = I('post.');
        $userLevelValidate = Loader::validate('UserLevel');
        $return = ['status' => 0, 'msg' => '参数错误', 'result' => ''];//初始化返回信息
        if ($data['act'] == 'add') {
            if (!$userLevelValidate->batch()->check($data)) {
                $return = ['status' => 0, 'msg' => '添加失败', 'result' => $userLevelValidate->getError()];
            } else {
                $r = D('user_level')->add($data);
                if ($r !== false) {
                    $return = ['status' => 1, 'msg' => '添加成功', 'result' => $userLevelValidate->getError()];
                } else {
                    $return = ['status' => 0, 'msg' => '添加失败，数据库未响应', 'result' => ''];
                }
            }
        }
        if ($data['act'] == 'edit') {
            if (!$userLevelValidate->scene('edit')->batch()->check($data)) {
                $return = ['status' => 0, 'msg' => '编辑失败', 'result' => $userLevelValidate->getError()];
            } else {
                $r = D('user_level')->where('level_id=' . $data['level_id'])->save($data);
                if ($r !== false) {
                    $discount = $data['discount']/100;
                    D('users')->where(['level'=>$data['level_id']])->save(['discount'=>$discount]);
                    $return = ['status' => 1, 'msg' => '编辑成功', 'result' => $userLevelValidate->getError()];
                } else {
                    $return = ['status' => 0, 'msg' => '编辑失败，数据库未响应', 'result' => ''];
                }
            }
        }
        if ($data['act'] == 'del') {
            $r = D('user_level')->where('level_id=' . $data['level_id'])->delete();
            if ($r !== false) {
                $return = ['status' => 1, 'msg' => '删除成功', 'result' => ''];
            } else {
                $return = ['status' => 0, 'msg' => '删除失败，数据库未响应', 'result' => ''];
            }
        }
        $this->ajaxReturn($return);
    }
    /**
     * 搜索用户名
     */
    public function search_user()
    {
        $search_key = trim(I('search_key'));        
        if(strstr($search_key,'@'))    
        {
            $list = M('users')->where(" email like '%$search_key%' ")->select();        
            foreach($list as $key => $val)
            {
                echo "<option value='{$val['user_id']}'>{$val['email']}</option>";
            }                        
        }
        else
        {
            $list = M('users')->where(" mobile like '%$search_key%' ")->select();        
            foreach($list as $key => $val)
            {
                echo "<option value='{$val['user_id']}'>{$val['mobile']}</option>";
            }            
        } 
        exit;
    }
    
    /**
     * 分销树状关系
     */
    public function ajax_distribut_tree()
    {
          $list = M('users')->where("first_leader = 1")->select();
          return $this->fetch();
    }

    /**
     *
     * @time 2016/08/31
     * @author dyr
     * 发送站内信
     */
    public function sendMessage()
    {
        $user_id_array = I('get.user_id_array');
        $users = array();
        if (!empty($user_id_array)) {
            $users = M('users')->field('user_id,nickname')->where(array('user_id' => array('IN', $user_id_array)))->select();
        }
        $this->assign('users', $users);
        return $this->fetch(); 
    }

    /**
     * 发送系统消息
     */
    public function doSendMessage()
    {
        $message_content = I('post.text','');//内容
        $message_title = I('post.title', '');//标题
        $message_type = I('post.type', 0);//个体or全体
        $users = I('post.user/a');//个体id
        $message_val = ['name'=>'张三'];
        $send_data = array(
            'message_title' => $message_title,
            'message_content' => $message_content,
            'message_type' => $message_type,
            'users' => $users,
            'type' => 0, //0系统消息
            'message_val' => $message_val,
            'category' => 0,
            'mmt_code' => 'message_notice'
        );

        $messageLogic = new \app\common\logic\MessageNoticeLogic($send_data);
        $messageLogic->sendMessage();
        $this->ajaxReturn(['status'=>1]);
    }

    /**
     *
     * @time 2016/09/03
     * @author dyr
     * 发送邮件
     */
    public function sendMail()
    {
        $user_id_array = I('get.user_id_array');
        $users = array();
        if (!empty($user_id_array)) {
            $user_where = array(
                'user_id' => array('IN', $user_id_array),
                'email'=> array('neq','')
            );
            $users = M('users')->field('user_id,nickname,email')->where($user_where)->select();
        }
        $this->assign('smtp',tpCache('smtp'));
        $this->assign('users',$users);
        return $this->fetch();
    }

    /**
     * 发送邮箱
     * @author dyr
     * @time  2016/09/03
     */
    public function doSendMail()
    {
        $call_back = I('call_back');//回调方法
        $message = I('post.text');//内容
        $title = I('post.title');//标题
        $users = I('post.user/a');
        if (!empty($users)) {
            $user_id_array = implode(',', $users);
            $users = M('users')->field('email')->where(array('user_id' => array('IN', $user_id_array)))->select();
            $to = array();
            foreach ($users as $user) {
                if (check_email($user['email'])) {
                    $to[] = $user['email'];
                }
            }
            $res = send_email($to, $title, $message);
            echo "<script>parent.{$call_back}('{$res['status']}');</script>";
            exit();
        }
    }
    
     /**
     * 签到列表
     * @date 2017/09/28
     */
    public function signList() {    
        
        return $this->fetch();
	
    }
    
    
    /**
     * 会员签到 ajax
     * @date 2017/09/28
     */
    public function ajaxsignList() {
	
        $model = M('user_sign us');
        //A.查询条件
        ($nickname = I('nickname')) && $map['u.nickname'] = ['like', "%$nickname%"];
        ($mobile = I('mobile')) && $map['u.mobile'] = ['like', "%$mobile%"];
        $prefix = C('prefix');
        $field = [
            'us.*',
            'u.user_id',
            'u.nickname',
            'u.mobile',
        ];
        // B. 开始查询
        $model = $model
                ->field($field)
                ->join($prefix . 'users u  ', 'u.user_id = us.user_id', 'left')
                ->where($map)
                ->order('us.id DESC');
        // B.1 总数
        $tModel = clone $model;
        $count = $tModel->count();
        // B.2 开始分页
        $Page  = new AjaxPage($count,15);
        $show = $Page->show();
        $list = $model->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $this->assign('page', $show);
        $this->assign('pager', $Page);
        $this->assign('list', $list);
        return $this->fetch();
	
    }
    
    /**
     * 签到规则设置 
     * @date 2017/09/28
     */
    public function signRule() {
    
        $inc_type = 'sign';
        if (IS_POST) {
            $param = I('post.');
            unset($param['inc_type']);
            unset($param['form_submit']);
            tpCache($inc_type, $param);
            $this->success("操作成功", U('User/signRule', array('inc_type' => $inc_type)));
        }       
        $this->assign('inc_type',$inc_type);
	    $this->assign('config',tpCache($inc_type));//当前配置项
        return $this->fetch();  
     	      
    }


    /**
     * 会员标签列表
     */
    public function labels()
    {
        $p = input('p/d');
        $Label = new UserLabel();
        $label_list = $Label->order('label_order')->page($p, 10)->select();
        $this->assign('label_list', $label_list);
        $Page = new Page($Label->count(), 10);
        $this->assign('page', $Page);
        return $this->fetch();
    }

    /**
     * 添加、编辑页面
     */
    public function labelEdit()
    {
        $label_id = input('id/d');
        if ($label_id) {
            $Label = new UserLabel();
            $label = $Label->where('id', $label_id)->find();
            $this->assign('label', $label);
        }
        return $this->fetch();
    }

    /**
     * 会员标签添加编辑删除
     */
    public function label()
    {
        $label_info = input();
        $return = ['status' => 0, 'msg' => '参数错误', 'result' => ''];//初始化返回信息
        $userLabelValidate = Loader::validate('UserLabel');
        $UserLabel = new UserLabel();
        if (request()->isPost()) {
            if ($label_info['label_id']) {
                if (!$userLabelValidate->scene('edit')->batch()->check($label_info)) {
                    $return = ['status' => 0, 'msg' => '编辑失败', 'result' => $userLabelValidate->getError()];
                }else {
                    $UserLabel->where('id', $label_info['label_id'])->save($label_info);
                    $return = ['status' => 1, 'msg' => '编辑成功', 'result' => ''];
                }
            }else{
                if (!$userLabelValidate->batch()->check($label_info)) {
                    $return = ['status' => 0, 'msg' => '添加失败', 'result' => $userLabelValidate->getError()];
                } else {
                    $UserLabel->insert($label_info);
                    $return = ['status' => 1, 'msg' => '添加成功', 'result' => ''];
                }
            }
        }
        if (request()->isDelete()) {
            $UserLabel->where('id', $label_info['label_id'])->delete();
            $return = ['status' => 1, 'msg' => '删除成功', 'result' => ''];
        }
        $this->ajaxReturn($return);
    }

    /**、
     * 检测该下级是否一条线
     * @param $pid
     * @return array
     */
    function check_first($pid)
    {
        static $arr = [];
        static $i=0;
        $i++;
        $u = db('users')->where(['first_leader' => ['in', $pid]])->column('user_id');
        $arr = array_merge($arr, $u);
        if($i>1000) return $arr;
        if ($u) {
            $this->check_first($u);
        }
        return $arr;

    }

    /**
     * 检测更改上级
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function check_first_leader()
    {
        $mobile = input('first_leader_mobile');//手机号码
        $user_id = input('user_id');
        $type = input('type');
        if (IS_POST && $mobile && $user_id) {
            $Users = new \app\common\model\Users();
            $first_leader = $Users::get(['mobile' => $mobile, 'is_lock' => 0]); // 上级用户
            $users = db('users')->where(['user_id' => $user_id])->find(); // 自己
            if (in_array($first_leader['user_id'], $this->check_first($user_id)) || $first_leader['user_id'] == $user_id) {
                $this->ajaxReturn(['status' => 0, 'msg' => '该会员不符合更改上级条件', 'result' => []]);
            }
            if ($users['first_leader'] == $first_leader['user_id'] && $users['first_leader'] > 0) {
                $this->ajaxReturn(['status' => 0, 'msg' => '已经是您的上级', 'result' => []]);
            }
            if ($first_leader) {
                if ($type == 0) {
                    $this->ajaxReturn(['status' => 1, 'msg' => '符合更改上级', 'result' => []]);
                }

                //通过符合条件,更改用户上级
                db('users')->where(['user_id' => $user_id])->save(['first_leader' => $first_leader['user_id'], 'second_leader' => $first_leader['first_leader'], 'third_leader' => $first_leader['second_leader']]);
                db('users')->where(['first_leader' => $user_id])->save(['second_leader' => $first_leader['user_id'], 'third_leader' => $first_leader['first_leader']]);
                db('users')->where(['second_leader' => $user_id])->save(['third_leader' => $first_leader['user_id']]);

                //他上级分销的下线人数要加1
                Db::name('users')->where(array('user_id' => $first_leader['user_id']))->setInc('underling_number');
                Db::name('users')->where(array('user_id' => $first_leader['first_leader']))->setInc('underling_number');
                Db::name('users')->where(array('user_id' => $first_leader['second_leader']))->setInc('underling_number');

                //减少以前上级总人数的字段-1
                db('users')->where(['user_id' => $users['first_leader']])->setDec('underling_number');
                db('users')->where(['user_id' => $users['second_leader']])->setDec('underling_number');
                db('users')->where(['user_id' => $users['third_leader']])->setDec('underling_number');
                $this->ajaxReturn(['status' => 1, 'msg' => '更改上级成功', 'result' => $first_leader]);
            }
            $this->ajaxReturn(['status' => 0, 'msg' => '不存在该会员', 'result' => []]);
        }
    }

    public function momentsList(){
        $where='';
        $condition=array('0'=>'','1'=>'and m.status=0','2'=>'and m.status=1','3'=>'and m.status=2','4'=>'and is_talk=1');
        if (request()->isPost()) {
            if(I('classify')) {
                $classify_id = I('classify');
                $keywords = I('keywords');
                $where = "m.classify_id = {$classify_id} and (title like '%{$keywords}%' or moments_content like '%{$keywords}%')";
                $where.=$condition[I('status')];
            }else{
                $keywords=I('keywords');
                $where="(title like '%{$keywords}%' or moments_content like '%{$keywords}%')";
                $where.=$condition[I('status')];
            }
        }
        $moments = M('moments');
        $count = $moments->count();
        $Page = new Page($count);
        $show = $Page->show();
        $list = $moments->alias('m')->where($where)->limit($Page->firstRow,$Page->listRows)
            ->join('users s','s.user_id = m.user_id','LEFT')
            ->field("m.*,nickname,mobile")
            ->order('m.is_delete asc ,m.status asc,m.add_time desc')->select();
        foreach ($list as $k=>$v){
            $comment =  M('moments_comment')->where('moments_id',$v['moments_id'])->count('comment_id');
            $list[$k]['comment'] = $comment;
        }

        $classify=Db('moments_classify')->select();
        $this->assign('classify',$classify);
        $this->assign('page', $show);
        $this->assign('pager', $Page);
        $this->assign('list', $list);
        $this->assign('status', Moments::$STATUS);
        return $this->fetch();
    }

    /**
     *  更新动态审核状态
     */
    public function moments_update(){
        $status = I('status/d',0);
        $id = I('id/a');
        $remark = I('remark/s','');
        $ids = implode(',',$id);
        $where['moments_id'] = array('in',$ids);
        $r = Db::name('moments')->where($where)->update(['status'=>$status,'moments_remark'=>$remark]);
        if($r !== false){
            $this->ajaxReturn(array('status'=>1,'msg'=>"操作成功"),'JSON');
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>"操作失败"),'JSON');
        }
    }

    /**
     * 查看某一动态详情
     */
    public function seeMoments(){
        $id = I('post.moments_id/d',0);
        //获取该动态详情
        $this->getFindMoments($id);
        return $this->fetch();
    }

    /**
     * 获取该动态详情
     * @param $id
     */
    public function getFindMoments($id){
        $find = Db::name('moments')->alias('m')->join('users u','u.user_id = m.user_id','LEFT')->field("m.*,nickname,mobile")->find($id);

        if(strpos($find['moments_imgs'],'mp4')){
            $find['mp4'] = explode(',',$find['moments_imgs']);
            $find['img'] = array();
        }else{
            $find['img'] = explode(',',$find['moments_imgs']);
            $find['mp4'] = array();
        }
        $this->assign('vo',$find);
    }

    /**
     * 查看某一动态评论列表
     */
    public function seeComment(){
        $id = I('post.moments_id');
        //获取该动态详情
        $this->getFindMoments($id);
        //某动态获取评论列表
        $comment = M('moments_comment');
        $count = $comment->where('moments_id',$id)->count();
        $Page = new Page($count);
        $show = $Page->show();
        $list = $comment->alias('m')
            ->where('moments_id',$id)
            ->limit($Page->firstRow,$Page->listRows)
            ->join('__USERS__ u','u.user_id = m.user_id','LEFT')
            ->field("m.*,nickname,mobile")
            ->order('m.is_delete asc ,m.status asc,m.add_time asc')->select();

        $this->assign('page', $show);
        $this->assign('pager', $Page);
        $this->assign('list', $list);
        $this->assign('status', Moments::$STATUS);
        return $this->fetch();
    }

    /**
     *  更新审核状态
     */
    public function comment_update(){
        $status = I('status/d',0);
        $remark = I('remark/s','');
        $id = I('id/a');
        $ids = implode(',',$id);
        $where['comment_id'] = array('in',$ids);
        $r = Db::name('moments_comment')->where($where)->update(['status'=>$status,'comment_remark'=>$remark]);
        if($r !== false){
            $this->ajaxReturn(array('status'=>1,'msg'=>"操作成功"),'JSON');
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>"操作失败"),'JSON');
        }
    }

    /**
     * 查看某一动态点赞列表
     */
    public function seeLike(){
        $id = I('post.moments_id');
        //获取该动态详情
        $this->getFindMoments($id);
        //某动态获取评论列表
        $comment = M('moments_like');
        $count = $comment->count();
        $Page = new Page($count);
        $show = $Page->show();
        $list = $comment->alias('m')
            ->where('moments_id',$id)
            ->limit($Page->firstRow,$Page->listRows)
            ->join('__USERS__ u','u.user_id = m.user_id','LEFT')
            ->field("m.*,nickname,mobile")
            ->order('m.add_time asc')
            ->select();

        $this->assign('page', $show);
        $this->assign('pager', $Page);
        $this->assign('list', $list);
        $this->assign('status', Moments::$STATUS);
        return $this->fetch();
    }

    /**
     *动态分类管理列表
     **/
    public  function  commentClassify(){
        $model  = new MomentsClassify;
        $list   = $model->select();
        //dump($list);die;
        $this->assign('list',$list);
        return $this->fetch();
    }

    /*  *
     * 对动态分类的CURD
     * param   act
     */
    public function classify(){
        $act = I('act/s','');
        $classify_id = I('classify_id/d',0);
        if(IS_POST){
            if($act == 'del'){
                $res  = M('moments_classify')->where(['classify_id'=>$classify_id])->delete();
            }else {
                $valiclassify=new ValiMomentsClassify();
                $data = I('post.');
                if($valiclassify->check($data)) {
                    if ($classify_id >= 1) {
                        $res = MomentsClassify::where(['classify_id' => $classify_id])->save($data);
                    } else {
                        $res = M('moments_classify')->add($data);
                    }
                }else{
                    $this->ajaxReturn(array('status'=>0,'msg'=>$valiclassify->getError()),'JSON');
                }
            }
            if($res){
                $this->ajaxReturn(array('status'=>1,'msg'=>"操作成功"),'JSON');
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>"操作失败"),'JSON');
            }
        }

        if($classify_id>0){
            $info    = MomentsClassify::where(['classify_id'=>$classify_id])->select();
            $this->assign('info',$info[0]);
        }
        return $this->fetch();
    }

    public function talk(){
        $moments_id=I('moments_id/d');
        $result=MomentsLogic::talk($moments_id);
        $this->ajaxReturn($result);
    }
}