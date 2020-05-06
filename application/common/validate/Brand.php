<?php
namespace app\common\validate;

use think\Validate;
//品牌验证器
class Brand extends validate
{

    protected $rule=[
        ['name' ,'require|unique:brand,name^cat_id1^cat_id2'],
        ['url' ,'url'],
        ['cat_id1','require'],
        ['cat_id2','require'],
        ['sort','number'],
        ['desc' ,'max:100']
    ];
    protected $message = [
        'name.require'      => '品牌名称必须',
        'name.unique'       => '品牌已经存在',
        'url.url'           => '品牌地址不是有效的URL地址',
        'cat_id1.require'    => '所属分类必须',
        'cat_id2.require'    => '所属分类必须选到第二级',
        'sort.number'       => '排序必须是数字',
        'desc.max'          => '品牌描述不得大于100个字节'
    ];
}