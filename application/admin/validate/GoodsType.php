<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/8
 * Time: 11:22
 */

namespace app\admin\validate;


use think\Validate;

class GoodsType extends Validate
{
    protected $rule = [
        'name' => 'require|min:1|max:60',
        'category_id' => 'require|checkCategoryId',
        'attr_values' => 'checkAttrValues',
        'attr_name' => 'checkAttrName',
        'spec_name' => 'checkSpecName'
    ];

    protected $message = [
        'name.require' => '模型名称必填',
        'name.min' => '模型名称长度至少3个字符',
        'name.max' => '模型名称长度至多20个汉字',
        'category_id.require' => '必须绑定到一个三级分类上'
    ];

    protected function checkCategoryId($value, $rule, $data)
    {
        if (in_array(0,$value)) {
            return '请绑定至三级分类';
        }
        if (count($value) != count(array_unique($value))) {
            return '请勿选择同样的分类';
        }

        if ($data['id']) {
            //查出该模型对应所有分类、查看传过来的多有分类包不包含之前的分类
            $category_type_id  = db('goods_category')->where('type_id',$data['id'])->field('id')->select();
            $category_id_list = array_column($category_type_id,'id');//该类型下面之前绑定的分类id
            $diff = array_diff($category_id_list,$data['category_id']);
            if (count($diff) != 0) {//将之前有的id和现在没有的id提取出来查看下面是否有商品
                $is_goods = db('goods')->where('is_on_sale',1)->where('cat_id3','in',implode(',',$diff))->field('cat_id3')->find();
                if ($is_goods) {
                    $category = db('goods_category')->where('id',$is_goods['cat_id3'])->field('name')->find();
                    return $category['name'].'分类下已有商品，不能编辑';
                }
            }


            if (model('goods_category')->where('id', 'in', $value)->where("type_id<>0 && type_id<>{$data['id']}")->count() != 0) {
                $category_list = model('goods_category')->where('id', 'in', $value)->where("type_id<>0 && type_id<>{$data['id']}")->field('name,type_id')->select();
                $return_name = '';
                foreach ($category_list as $v){
                    $return_name .= '【'.$v->name.'分类已绑定模型ID:'.$v->type_id.'】';
                }
                return $return_name;
            }
        }else{
            if (model('goods_category')->where('id', 'in', $value)->where("type_id<>0")->count() != 0) {
                $category_list = model('goods_category')->where('id', 'in', $value)->where("type_id<>0")->field('name,type_id')->select();
                $return_name = '';
                foreach ($category_list as $v){
                    $return_name .= '【'.$v->name.'分类已绑定模型ID:'.$v->type_id.'】';
                }
                return $return_name;
            }
        }




        return true;
    }

    protected function checkAttrValues($value ,$rule ,$data)
    {
        $arr = [];
        foreach ($value as $k => $v){
            $array = explode(',',$v);
            if (in_array('',$array)) {
                return '属性值不能为空或逗号后面必须加属性值';
            }
            $arr = array_merge($arr,$array);
        }
        if (count($arr) != count(array_unique($arr))) {
            return '属性值不能重复';
        }
        return true;
    }
    protected function checkSpecName($value)
    {
        foreach ($value as $v){
            if ($v == '') {
                return '规格名称不能为空';
            }
        }
        if (count($value) != count(array_unique($value))) {
            return '规格名称不能重复';
        }
        return true;
    }

    protected function checkAttrName($value)
    {
        if (in_array('',$value)) {
            return '属性名不能为空';
        }
        if (count($value) != count(array_unique($value))) {
            return '属性名不能重复';
        }
        return true;
    }


}