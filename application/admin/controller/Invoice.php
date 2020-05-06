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
 * 发票控制器
 * Author: 545
 * Date: 2017-10-23
 */

namespace app\admin\controller;

use think\AjaxPage;
use think\Db;
use think\Page;

class Invoice extends Base {
    /*
     * 初始化操作
     */

    public function _initialize() {
        parent::_initialize();
        C('TOKEN_ON', false); // 关闭表单令牌验证
    }

    /*
     * 发票列表
     */
    public function index() {
        
        //待开发票
        $this->assign('wait',  M('invoice')->where(['status'=>0])->count());
        //累计开发票数
        $this->assign('total',  M('invoice')->where(['status'=>1])->count());
        return $this->fetch();
        
    }

    /**
     * 发票列表 ajax
     * @date 2017/10/23
     */
    public function ajaxindex() {
        
        $M = M('invoice');
        $begin = I("start_time");
        $end   = I("end_time");
        if(!empty($begin)&&!empty($end)){
            $this->assign('start_time', $begin);
            $this->assign('end_time', $end);
            $map['ctime'] = array('between', array(strtotime($begin), strtotime($end)+86399));
        }
        $status=I('status');
        ($status>=0) && $map['status'] = I('status');
        // B. 开始查询
        $count = $M->where($map)->count();
        // B.2 开始分页
        $Page = new AjaxPage($count, 15);
        $show = $Page->show();
        $list = $M->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->order('invoice_id desc')->select();
        //C.1 联表
        foreach ($list as $key => $val) {
            $val['nickname'] = M('users')->cache(true)
                ->where('user_id = ' . $val['user_id'])
                ->getfield('nickname');
            $val['order_sn'] = M('order')->cache(true)
                ->where('order_id = ' . $val['order_id'])
                ->getfield('order_sn');
            $val['store_name'] = M('store')->cache(true)
                ->where('store_id = ' . $val['store_id'])
                ->getfield('store_name');
            //未作废的订单才加入列表
            (!empty($val['order_sn']))&& $invoice_list[] = $val;
        }
        $this->assign('page', $show);
        $this->assign('pager', $Page);
        $this->assign('list', $invoice_list);
        return $this->fetch();
        
    }

    //开票时间
    function changetime(){
        
        $invoice_id=I('invoice_id');
        empty($invoice_id)&&$this->ajaxReturn(['status' => -1, 'msg' => '', 'result' =>''] );
        $map['invoice_id']=$invoice_id;
        (M('invoice')->where($map)->save(['ctime'=>time()]))?$status=1:$status=-1;
        $result = ['status' => $status, 'msg' => '', 'result' =>''];
        $this->ajaxReturn($result);
        
    }

    public function export_invoice()
    {
        
        $list = Db::name('invoice')->order('invoice_id')->select();
        if(count($list)>0){
            foreach ($list as $key => $val) {
                $val['nickname'] = M('users')->cache(true)
                    ->where('user_id = ' . $val['user_id'])
                    ->getfield('nickname');
                $val['order_sn'] = M('order')->cache(true)
                    ->where('order_id = ' . $val['order_id'])
                    ->getfield('order_sn');
                $val['store_name'] = M('store')->cache(true)
                    ->where('store_id = ' . $val['store_id'])
                    ->getfield('store_name');
                $invoice_list[] = $val;
            }
            $strTable ='<table width="500" border="1">';
            $strTable .= '<tr>';
            $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">发票编号</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="100">订单编号</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">用户名</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">店铺</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">发票类型</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">开票金额</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">抬头</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">发票内容</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">发票税率</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">纳税人识别号</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">状态</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">创建时间</td>';
            $strTable .= '<td style="text-align:center;font-size:12px;" width="*">开票时间</td>';
            $strTable .= '</tr>';
            foreach($invoice_list as $k=>$val){
                $strTable .= '<tr>';
                $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['invoice_id'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['order_sn'].' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['nickname'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['store_name'].'</td>';
                if($val['invoice_type']==1){ $invoice_type="电子发票";} elseif($val['invoice_type']==2){$invoice_type="增值税发票";}else{$invoice_type="普通发票";}
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$invoice_type .'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['invoice_money'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['invoice_title'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['invoice_desc'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['invoice_rate'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['taxpayer'].'</td>';
                if($val['status']==1){ $status="已开";} elseif($val['status']==2){$status="作废";}else{$status="待开";}
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$status.'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.date("Y-m-d H:i:s",$val['atime']).'</td>';
                ($val['status']==1)?$ctime=date("Y-m-d H:i:s",$val['ctime']):$ctime="";
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$ctime.'</td>';
                $strTable .= '</tr>';
            }
            $strTable .='</table>';
            unset($invoice_list);
            downloadExcel($strTable,'invoice');
            exit();
        }
        
    }
}
