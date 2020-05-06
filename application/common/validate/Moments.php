<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * Author: xwy
 * Date: 2018-04-26
 */

namespace app\common\validate;

use think\Validate;

/**
 * 发朋友圈
 * Class Moments
 * @package app\api\validate
 */
class Moments extends Validate
{
    protected $rule = [
//        'user_id' => 'require|number',
        'moments_id' => 'require|number',
        'comment_id' => 'require|number',
        'moments_content' => 'max:1000',
        'pid' => 'require|number',
        'p_name' => 'require|max:30',
        'comment_content' => 'require|max:1000',

    ];

    protected $message = [
//        'user_id.require' => '缺少用户id',
        'moments_id.require' => '缺少动态id',
        'comment_id.require' => '缺少评论id',
        'pid.require' => '缺少回复对方id',
        'p_name.require' => '缺少回复对方名字',
        'moments_content.require' => '缺少发表内容',
        'comment_content.require' => '缺少评论内容',
        'moments_content.max' => '发表内容不能超过255个字',
        'comment_content.max' => '评论内容不能超过255个字',
        'p_name.max' => '回复对方名字不能超过10个字',
    ];

    protected $scene = [
        'momentsList' => ['user_id'],
        'addLike' => ['user_id', 'moments_id'],
        'addMoments' => ['user_id', 'moments_content'],
        'delMoments' => ['user_id', 'moments_id'],
        'addComment' => ['user_id', 'moments_id', 'pid', 'comment_content'],
        'delComment' => ['user_id', 'moments_id', 'comment_id'],

    ];

}