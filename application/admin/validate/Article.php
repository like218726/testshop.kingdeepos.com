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
 */

namespace app\admin\validate;

use think\Validate;

/**
 * Description of Article
 *
 * @author Administrator
 */
class Article extends Validate
{
    //验证规则
    protected $rule = [
        'title'     => 'require|checkEmpty',
        'cat_id'    => 'require|checkEmpty',
        'content'   => 'require|checkContent',
        'link'      => 'url'
    ];

    //错误消息
    protected $message = [
        'title'    => '标题不能为空',
        'content'  => '内容不能为空',
        'cat_id.require'   => '所属分类缺少参数',
        'cat_id.checkEmpty' => '所属分类必须选择',
        'article_id.checkArtcileId' => '系统预定义的文章不能删除',
        'link.url' => '链接格式错误'
    ];

    //验证场景
    protected $scene = [
        'add'  => ['title', 'cat_id', 'content','link'],
        'edit' => ['title', 'cat_id', 'content','link'],
        'del'  => ['article_id']
    ];

    protected function checkEmpty($value)
    {
        if (is_string($value)) {
            $value = trim($value);
        }
        if (empty($value)) {
            return false;
        }
        return true;
    }

    protected function checkContent($value)
    {
        $value = strip_tags($value);
        $value = str_replace('&nbsp;', '', $value);
        $value = trim($value);
        if (empty($value)) {
            return false;
        }
        return true;
    }

}
