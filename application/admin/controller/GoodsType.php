<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/3
 * Time: 16:44
 */

namespace app\admin\controller;

use app\admin\model\BrandType;
use app\admin\model\GoodsAttribute;
use app\admin\model\GoodsCategory;
use app\admin\model\GoodsType as GoodsTypeModel;
use app\admin\model\Spec;
use app\common\model\SpecGoodsPrice;
use think\Db;
use think\Exception;
use think\Loader;


class GoodsType extends Base
{
    /**
     * 商品模型添加及编辑
     */
    public function edit()
    {
        $type_id = input('id/d', 0);
        $brand_id_array = input('brand_id/a', []);
        $goods_category_array = input('category_id/a', []);//三级分类

        $spec_name_array = input('spec_name/a', []);//规格
        $spec_id_array = input('spec_id/a', []);//规格id数组
        $spec_order_array = input('spec_order/a', []);//规格排序数组
        $spec_is_upload_image_array = input('spec_is_upload_image/a', []);//规格是否上传规格图数组

        $attr_name_array = input('attr_name/a', []);//属性名数组
        $attr_values_array = input('attr_values/a', []);//属性值数组
        $attr_index_array = input('attr_index/a', []);//属性显示
        $attr_input_type_array = input('attr_input_type/a', []);//是否自定义属性
        $order_array = input('order/a', []);//属性排序数组
        $attr_id_array = input('attr_id/a', []);//属性id数组

        $validate = Loader::validate('GoodsType');// 数据验证
        if (!$validate->batch()->check(input('post.'))) {
            $error = $validate->getError();
            $error_msg = array_values($error);
            $return_arr = ['status' => 0, 'msg' => $error_msg[0], 'result' => $error];
            $this->ajaxReturn($return_arr);
        }
        $str = input('id')?'修改':'新添加';
        adminLog("管理员".$str."了商品类型 ".input('name').'id为'.input('id'));
        //模型名称
        //开启事务
        Db::startTrans();
        try {
            $GoodsTypeModel = new GoodsTypeModel();
            if ($type_id) {
                $GoodsTypeModel->save(['name' => input('name')], ['id' => $type_id]);
            } else {
                $GoodsTypeModel->save(['name' => input('name')]);
                $type_id = $GoodsTypeModel->id;
            }

            //绑定分类
            $GoodsCategory = new GoodsCategory();
            //先将之前的绑定清空
            $GoodsCategory->save(['type_id' => 0], ['type_id' => $type_id]);
            $GoodsCategory->where('id', 'in', $goods_category_array)->save(['type_id' => $type_id]);

            //规格设置
            $Spec = new Spec();
            $spec_list = [];
            foreach ($spec_name_array as $k => $v) {
                $attribute = array(
//                    'type_id' => $type_id,
                    'name' => $v,
                    'order' => $spec_order_array[$k],
                    'is_upload_image' => $spec_is_upload_image_array[$k]
                );
                if (empty($spec_id_array[$k])) {
                    $spec_list[] = $attribute;
                } else {
                    $attribute['id'] = $spec_id_array[$k];
                    $spec_list[] = $attribute;
                }
            }
            $spec_id_list = $Spec->saveAll($spec_list, true);
            $spec_type_list = [];
            foreach ($spec_id_list as $k => $v){
                $spec_type_list[$k]['type_id'] = $type_id;
                $spec_type_list[$k]['spec_id'] = $v->id;
            }
            model('spec_type')->where('type_id',$type_id)->delete();
            model('spec_type')->saveAll($spec_type_list);

            //处理商品属性
            $GoodsAttribute = new GoodsAttribute();
            $attr_name_list = array();
            foreach ($attr_name_array as $k => $v) {
                $attr_values_array[$k] = str_replace('_', '', $attr_values_array[$k]); // 替换特殊字符
                $attr_values_array[$k] = str_replace('@', '', $attr_values_array[$k]); // 替换特殊字符
                $attr_values_array[$k] = trim($attr_values_array[$k]);
                $attr_index_array[$k] = $attr_index_array[$k] ? $attr_index_array[$k] : 0; // 是否关键字检索
                $attr_input_type_array[$k] = $attr_input_type_array[$k] ? $attr_input_type_array[$k] : 0; // 是否自定义属性
                $attribute = array(
                    'attr_name' => $v,
                    'type_id' => $type_id,
                    'attr_index' => $attr_index_array[$k],
                    'attr_values' => $attr_values_array[$k],
                    'attr_input_type' => $attr_input_type_array[$k],
                    'order' => $order_array[$k],
                );
                if (empty($attr_id_array[$k])) {
                    $attr_name_list[] = $attribute;
                } else {
                    $attribute['attr_id'] = $attr_id_array[$k];
                    $GoodsAttribute->update($attribute);
                }
            }
            if (count($attr_name_list) > 0) {
                // 插入属性
                $GoodsAttribute->insertAll($attr_name_list);
            }


            // 类型品牌对应关系表
            $brand_id_list = array();
            foreach ($brand_id_array as $k => $v) {
                $brand_id_list[] = array('type_id' => $type_id, 'brand_id' => $v);
            }

            model('brand_type')->where("type_id", $type_id)->delete(); // 先把类型规格 表对应的 删除掉 然后再重新添加
            if (count($brand_id_list) > 0) {
                model('brand_type')->insertAll($brand_id_list);
            }
            Db::commit();
            $this->ajaxReturn(['status'=>1,'msg'=>'操作成功']);
        }catch (Exception $e){
            Db::rollback();
            $this->error('服务器繁忙，请重试');
        }
    }

    /**
     * 添加修改页面
     */
    public function index()
    {
        $type_id = input('id/d')?input('id/d'):0;
        if ($type_id) {
            $GoodsType = model('goods_type')->where('id',$type_id)->find();
            $this->assign('goodsType',$GoodsType);

            $goods_category = model('goods_category')->field('id,name,parent_id')->where('type_id',$type_id)->select();
            //查出3级分类
            $category_array = [];
            foreach ($goods_category as $k => $v){
            $category_array[$k]['c3'] = $v;
            $category_array[$k]['c2'] = model('goods_category')->field('id,name,parent_id')->where('id',$v['parent_id'])->find();
            $category_array[$k]['c1'] = model('goods_category')->field('id,name,parent_id')->where('id', $category_array[$k]['c2']['parent_id'])->find();
            }
            $this->assign('category_array',$category_array);
            //规格
            $specs = db('spec_type')->where('type_id',$type_id)->select();
            foreach ($specs as $k => $v){
                $spec = db('spec')->where('id',$v['spec_id'])->find();
                $specs[$k]['id'] = $spec['id'];
                $specs[$k]['name'] = $spec['name'];
                $specs[$k]['order'] = $spec['order'];
                $specs[$k]['is_upload_image'] = $spec['is_upload_image'];
            }


            //属性
            $attributes = model('goods_attribute')->where('type_id',$type_id)->where('attr_input_type',1)->select();
            $customerAttributeList = model('goods_attribute')->where(["type_id" => $type_id, 'attr_input_type'=>2])->select();//自定义属性

            $this->assign('specs',$specs);
            $this->assign('attributeList',$attributes);
            $this->assign('customerAttributeList', $customerAttributeList);
        }
        //一级分类
        $category_list = model('goods_category')->where('parent_id','=',0)->select();
        $this->assign('category_list',$category_list);

        return $this->fetch('goods/_goodsType');

    }

    public function deleteSpec()
    {
        $id = input('id/d');
        $type_id = input('type_id/d');
        $item = input('item');
        if (empty($id)) {
            $this->ajaxReturn(['status' => 0, 'msg' => '参数错误']);
        }
        $item_id = db('spec_item')->where([ 'spec_id' => $id])->getField('id');
        $c = model("SpecGoodsPrice")->where(" `key` REGEXP :id1 OR `key` REGEXP :id2 OR `key` REGEXP :id3 or `key` = :id4")->bind(['id1' => '^' . $item_id . '_', 'id2' => '_' . $item_id . '_', 'id3' => '_' . $item_id . '$', 'id4' => $item_id])->count(); // 其他商品用到这个规格不得删除
        if ($c) {
            $this->ajaxReturn(['status' => 0, 'msg' => '当前有商品在使用该规格，不能删除']);
        }
        $spec = Spec::get($id);
        if(empty($spec)){
            $this->ajaxReturn(['status' => 0, 'msg' => '参数错误']);
        }
        db('spec_type')->where('type_id',$type_id)->where('spec_id',$id)->delete();
        db('spec')->where('id', $id)->delete();
        $this->ajaxReturn(['status' => 1, 'msg' => '删除成功']);
    }

    //分类是否可以删除
    public function canDeleteCategory()
    {
        $category_id = input('category_id/d');
        $goods = db('goods')->where('cat_id3',$category_id)->count();
        if ($goods != 0) {
            $this->ajaxReturn(['status'=>0,'msg'=>'该分类已经绑定商品，不能解绑']);
        }
        $this->ajaxReturn(['status'=>1,'msg'=>'解除绑定成功']);
    }
}