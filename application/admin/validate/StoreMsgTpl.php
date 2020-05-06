<?php
namespace app\admin\validate;
use think\Validate;
//消息模板验证器
class StoreMsgTpl extends Validate
{
    protected $rule=[
        'smt_code'                     => 'require',
        'smt_name'                     => 'require',
        'smt_message_content'          => 'require',
        'smt_short_content'            => 'require',
        'smt_mail_subject'             => 'require',
        'smt_mail_content'             => 'require',
    ];
    protected $message = [
        'smt_code.require'             => '模板编号必填',
        'smt_name.require'             => '模板名称必填',
        'smt_message_content.require'  => '站内信消息内容必填',
        'smt_short_content.require'    => '短信接收内容必填',
        'smt_mail_subject.require'     => '邮件标题必填',
        'smt_mail_content.require'     => '邮件内容必填',
    ];
    protected $scene = [
        'add'  => ['smt_code','smt_name','smt_message_content','smt_short_content','smt_mail_subject','smt_mail_subject'],
        'edit' => ['smt_name','smt_message_content','smt_short_content','smt_mail_subject','smt_mail_subject'],
        'del'  => ['smt_code'],
    ];

}