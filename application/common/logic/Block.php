<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: yhj
 * Date: 2018.9.28
 */

namespace app\common\logic;

use app\common\model\FlashSale;
use app\common\model\SpecGoodsPrice;
use think\Model;
use think\db;

/**
 * 自定义接口
 * Class Block
 * @package app\common\logic
 */
class Block extends Model
{

    /**
     * 商品列表板块参数设置
     * @param $data | ids 分类id|label 商品标签 | order 排序 | goods goods_ids
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function goods_list_block($data){
        $p = input('p/d',1);
        // 改为默认10个
        if(!isset($data['num']) or empty($data['num']) or $data['num'] < 2){
            $count = 4;
        }else{
            $count = $data['num'];
        }

        $where['a.is_on_sale'] = 1;
        if($data['ids']){
            $ids_arr = explode(',', $data['ids']);
            foreach($ids_arr as $k=>$v){
                if(empty($v)) unset($ids_arr[$k]);
            }
            if($ids_arr){
                $where_cat['is_show'] = 1;
                $where_cat['id'] = ['in', $ids_arr];
                $where_cat['parent_id'] = ['in', $ids_arr];
                $where_cat['level'] = 2; //查2级分类 即可 把1级分类转为2级一起查
                $ids_arr2 = Db::name('goods_category')->where($where_cat)->column('id');
                if($ids_arr2){
                    $ids_arr2 = array_merge($ids_arr, $ids_arr2);

                }
                $where['cat_id2'] = ['in', $ids_arr2];

            }
        }
        $data['label'] = trim($data['label']);
        if($data['label']){
            $where[$data['label']] = 1;
        }
        if($data['max_price'] && $data['min_price'] < $data['max_price']){
            $where['a.shop_price'] = [['egt', $data['min_price']],['elt', $data['max_price']]];
        }
        if($data['goods']){
            $goods_id_arr = explode(',', $data['goods']);
            $where['a.goods_id'] = ['in', $goods_id_arr];
            $count = count($goods_id_arr);
        }
        if($data['prom_type']){
            $where['a.prom_type'] = $data['prom_type'];
        }

        switch ($data['order']) {
            case '0':
                $order_str="num DESC"; // 为销量与虚拟销量累加
                break;

            case '1':
                $order_str="num ASC";
                break;

            case '2':
                $order_str="a.shop_price DESC";
                break;

            case '3':
                $order_str="a.shop_price ASC";
                break;

            case '4':
                $order_str="a.on_time DESC"; // on_time last_update
                break;

            case '5':
                $order_str="a.on_time ASC";
                break;
            case '6':
                $order_str="a.sort DESC";
                break;
            case '7':
                $order_str="a.sort ASC";
                break;
            default:
                $order_str="num DESC";
                break;
        }
        if($count>10){
            $goodsList = Db::name('goods')->where($where)->alias('a')
                ->join('__GOODS_LABEL__ b','a.label_id = b.label_id','left')->order($order_str)
                ->field('(a.sales_sum + a.virtual_sales_sum) as num,a.goods_id,a.goods_name,a.store_count,a.exchange_integral,a.is_virtual,a.is_recommend,a.is_new,a.sales_sum,a.virtual_sales_sum,a.prom_type,a.prom_id,a.store_id,b.label_name,a.original_img,a.shop_price')
                ->page($p,10)
                ->cache(true, TPSHOP_CACHE_TIME)
                ->select();
        }else{
            if(!isset($_GET['p']) || $p < 2){
                $goodsList = Db::name('goods')->where($where)->alias('a')
                    ->join('__GOODS_LABEL__ b','a.label_id = b.label_id','left')->order($order_str)
                    ->field('(a.sales_sum + a.virtual_sales_sum) as num,a.goods_id,a.goods_name,a.store_count,a.exchange_integral,a.is_virtual,a.is_recommend,a.is_new,a.sales_sum,a.virtual_sales_sum,a.prom_type,a.prom_id,a.store_id,b.label_name,a.original_img,a.shop_price')
                    ->limit(0,$count)
                    ->cache(true, TPSHOP_CACHE_TIME)
                    ->select();
            }else{
                $goodsList = [];
            }
        }

        foreach ($goodsList as $k => $v) {
            if(strpos($v['original_img'],'/public') === 0 ){
                if(!file_exists('.'.$v['original_img'])){
                    $goodsList[$k]['original_img'] = '/public/images/icon_goods_thumb_empty_300.png';
                }
            }elseif(empty($v['original_img'])){
                $goodsList[$k]['original_img'] = '/public/images/icon_goods_thumb_empty_300.png';
            }
            $goodsList[$k]['comment_count'] +=  $goodsList[$k]['virtual_comment_count'];
            $goodsList[$k]['activity'] = $this->check_activity($v);
        }
        return $goodsList;
    }

    public function check_activity($goods)
    {
        $data['prom_title'] = '';
        $data['prom_price'] = 0;
        $item_id =  SpecGoodsPrice::get(['goods_id'=>$goods['goods_id'],'prom_type'=>$goods['prom_type']],'',true);
        if($item_id){
            $goods['item_id'] = $item_id['item_id'];
        }
//        '0默认1抢购2团购3优惠促销4预售5虚拟(5其实没用)6拼团',
        switch ($goods['prom_type']){
            case 1:
                $result =  $this->activity($goods);
                $data['prom_price'] = 0;
                if($result){
                    $data['prom_title'] = '抢购';
                    $data['prom_price'] = ($result['shop_price']);
                }
//                $data['prom_title'] = '抢购';
                break;
            case 2:
                //团购
                $result =  $this->activity($goods);
                $data['prom_price'] = 0;
                if($result){
                    $data['prom_title'] = '团购';
                    $data['prom_price'] = ($result['shop_price']);
                }
//                $data['prom_title'] = '团购';
                break;
            case 3:
                $result =  $this->activity($goods);
                $data['prom_price'] = 0;
                if($result){
                    $data['prom_title'] = '优惠促销';
                    $data['prom_price'] = ($result['shop_price']);
                }
                //优惠促销
//                $data['prom_title'] = '优惠促销';
//                $data['prom_price'] = 0;
                break;
            case 4:
                //预售
                $result =  $this->activity($goods);
                $data['prom_price'] = 0;
                if($result){
                    $data['prom_title'] = '预售';
                    $data['prom_price'] = ($result['shop_price']);
                }
//                $data['prom_title'] = '预售';
//                $data['prom_price'] = 0;
                break;
            case 5:
                //虚拟

                $data['prom_title'] = '虚拟';
                $data['prom_price'] = 0;
                break;
            case 6:
                //拼团
                $result =  $this->activity($goods);
                $data['prom_price'] = 0;
                if($result){
                    $data['prom_title'] = '拼团';
                    $data['prom_price'] = ($result['shop_price']);
                }

//                $data['prom_price'] = 0;
                break;
        }
        return $data;
    }


    /**
     * @param $goods
     * @return bool
     */
    public function activity($goods){
        $goods_id = $goods['goods_id'];//商品id
        $item_id = $goods['item_id'];//规格id
        $Goods = new \app\common\model\Goods();
        $goods = $Goods::get($goods_id,'',true);
        $goodsPromFactory = new \app\common\logic\GoodsPromFactory();
        if ($goodsPromFactory->checkPromType($goods['prom_type'])) {
            //这里会自动更新商品活动状态，所以商品需要重新查询
            if($item_id){
                $specGoodsPrice = SpecGoodsPrice::get($item_id,'',true);
                $goodsPromLogic = $goodsPromFactory->makeModule($goods,$specGoodsPrice);
            }else{
                $goodsPromLogic = $goodsPromFactory->makeModule($goods,null);
            }
            //检查活动是否有效
            if($goodsPromLogic->checkActivityIsAble()){
                $goods = $goodsPromLogic->getActivityGoodsInfo();
                $goods['activity_is_on'] = 1;
                $goods['server_current_time'] = time();//服务器时间
                return $goods;
//                $this->ajaxReturn(['status'=>1,'msg'=>'该商品参与活动','result'=>['goods'=>$goods]]);
            }else{
                $goods['activity_is_on'] = 0;
                return false;
//                $this->ajaxReturn(['status'=>1,'msg'=>'该商品没有参与活动.','result'=>['goods'=>$goods]]);
            }
        }
        return false;
//        $this->ajaxReturn(['status'=>1,'msg'=>'该商品没有参与活动','result'=>['goods'=>$goods]]);
    }


    public function news_list($where_news,$num){
        $list = Db::view('news')
            ->view('newsCat','cat_name','newsCat.cat_id=news.cat_id','left')
            ->where($where_news)
            ->order('publish_time DESC')
            ->limit(0,$num)
            ->select();
        foreach ($list as $k => $v) {
            if(strpos($v['thumb'],'/public') === 0 ){
                if(!file_exists('.'.$v['thumb'])){
                    $list[$k]['thumb'] = '/public/images/icon_goods_thumb_empty_300.png';
                }
            }elseif(empty($v['thumb'])){
                $list[$k]['thumb'] = '/public/images/icon_goods_thumb_empty_300.png';
            }
        }
        return $list;
    }
    /**
     * 提交智能表单数据
     * @param $data
     * @return array
     */
    public function add_form($data){
        if(empty($data['timeid'])) return ['status'=>0,'msg'=>'timeid不能为空'];
        if(empty($data['form_name'])) return ['status'=>0,'msg'=>'form_name不能为空'];
        $arr = Db::name('form_config')->where('tpl_timeid', $data['timeid'])->find();
        if($arr){
            // 验证必填项
            $config_value = json_decode($arr['config_value'],true);
            if(empty($config_value['nav'])){
                return ['status'=>0,'msg'=>'表单未配置 config_value'];
            }
            $all_empty = true;
            foreach($config_value['nav'] as $k=>$v){
                $name = 'name'.$k;
                if($v['required'] == 1 && empty($data[$name])){
                    return ['status'=>0,'msg'=>$v['title'].'不能为空'];
                }
                if(isset($data[$name]) && !empty($data[$name])) $all_empty=false;
                // 输入框
                if($v['type'] == 0){
                    if($v['verify_type'] == 1 && !check_mobile($data[$name])){
                        // 手机号验证
                        return ['status'=>0,'msg'=>'手机号码格式不对'];
                    }elseif($v['verify_type'] == 2 && !check_email($data[$name])){
                        // 邮箱验证
                        return ['status'=>0,'msg'=>'邮箱格式不对'];
                    }
                    if($v['verify_type'] == 1 && !isset($data['mobile'])){
                        $data['mobile'] = $data[$name];
                    }
                }
                $title = 'title'.$k;
                $data[$title] = $v['title'];
            }
            if($all_empty) return ['status'=>0,'msg'=>'不能全部为空'];
            $data['submit_value'] = json_encode($data,JSON_UNESCAPED_UNICODE);
            if(!isset($data['tpl_timeid'])){
                $data['tpl_timeid'] = $data['timeid'];
            }
            $data['submit_time'] = time();
            $re = Db::name('form')->add($data);
            if($re){
                return ['status'=>1,'msg'=>'提交成功'];
            }else{
                return ['status'=>0,'msg'=>'提交失败'];
            }
        }else{
            return ['status'=>0,'msg'=>'表单不存在'];
        }
    }
}