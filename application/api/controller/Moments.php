<?php


namespace app\api\controller;

use app\common\validate\Moments as ValiMoment;
use app\common\logic\MomentsLogic;

class Moments extends Base
{
    public function addLike()
    {
        if (!request()->isPost()) {
            $this->ajaxReturn(['status' => -101, 'msg' => '请用post请求！！']);
        }
        $data = [
            'user_id' => $this->user_id,
            'moments_id' => I('post.moments_id/d'),
            'add_time' => time(),
        ];

        // 数据验证
        $validate = new ValiMoment();//\think\Loader::validate('Moments');

        if (!$validate->batch()->scene('addLike')->check($data)) {
            $this->ajaxReturn(['status' => -1, 'msg' => implode(',',$validate->getError())]);
        }
        $return = MomentsLogic::add_like($data);
        $this->ajaxReturn($return);

    }

    /**
     * 評論某条动态
     */
    public function addComment(){
        if (!request()->isPost()) {
            $this->ajaxReturn(['status' => -101, 'msg' => '请用post请求！！']);
        }
        $data = [
            'user_id' => $this->user_id,
            'moments_id' => I('moments_id/d',0),
            'pid' => I('pid/d',0),
            'p_name' => I('p_name',''),
            'comment_content' => I('comment_content'),
            'add_time' => time(),
        ];

        // 数据验证
        $validate = new ValiMoment();//\think\Loader::validate('Moments');

        if (!$validate->batch()->scene('addComment')->check($data)) {
            $this->ajaxReturn(['status' => -1, 'msg' => implode(',',$validate->getError())]);
        }

        $return = MomentsLogic::add_comment($data,$this->user);
        $this->ajaxReturn($return);
    }

    public function addClick(){
        $moments_id=I('moments_id/d');
        $data=array('moments_id'=>$moments_id);
        MomentsLogic::add_click($data);
    }
}