<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 商业用途务必到官方购买正版授权, 使用盗版将严厉追究您的法律责任。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * $Author: IT宇宙人 2015-08-10 $
 */
namespace app\home\controller;

use app\common\logic\GoodsLogic;
use app\common\model\Coupon;
use think\Page;
use think\Verify;
use think\Db;
use think\Image;

class Index extends Base
{
//    首页
    public function index()
    {
		$edit_ad = input('edit_ad');
        $web_list = S('web_index_data');
        if(!$web_list){
        	$web_list = M('web')->where(array('web_show'=>1))->order('web_sort')->select();
        	if($web_list){
        		foreach ($web_list as $kb=>$vb){
        			$block_list =  M('web_block')->where(array('web_id'=>$vb['web_id']))->order('web_id')->select();
        			if(is_array($block_list) && !empty($block_list)) {
        				foreach ($block_list as $key => $val) {//将变量输出到页面
        					$val['block_info'] = unserialize($val['block_info']);
        					$web_list[$kb][$val['var_name']] = $val['block_info'];
        				}
        			}
        		}
        		S('web_index_data',$web_list);
        	}
        }
         //获取推荐商品
        $GoodsLogic = new GoodsLogic();
        $is_recommend = $GoodsLogic->getRecommendGoods(1,9);
        $this->assign('is_recommend', $is_recommend);
        $getLikeGoods = $GoodsLogic->getLikeGoods(1,6);
        $this->assign('getLikeGoods', $getLikeGoods);
        //获取好货上新
        $is_new = $GoodsLogic->getNewGoods(1,4);
        //优惠券
        $where = array('c.type' => 2,'c.status'=>1,'c.send_start_time'=>['elt',time()],'c.send_end_time'=>['egt',time()]);
        $order = ['c.id' => 'desc'];
        $coupon = (new Coupon())->alias('c')
            ->field('c.id,c.name,c.money,c.condition,c.use_type,gc.goods_id,gc.goods_category_id')
            ->join('__GOODS_COUPON__ gc', 'gc.coupon_id=c.id ','left')
            ->where($where)
            ->order($order)
            ->group('id')
            ->cache(true)
            ->limit(9)
            ->select();

        if($coupon) {
            $coupon = collection($coupon)->append(['coupon_img','goods_coupon', 'use_type_title'])->toArray();
        }

        //公告列表
        $ajax_notice_list = $this->ajax_notice_list();
        $this->assign('notice_list', $ajax_notice_list);
        $this->assign('couponList', $coupon);
        $this->assign('is_new', $is_new);
        $this->assign('web_list', $web_list);
		$this->assign('edit_ad', $edit_ad);
        return $this->fetch();
    }

    /**
     *  公告详情页
     */
    public function notice()
    {
        return $this->fetch();
    }
      /**公告列表
         * @return mixed
         */
        public function notice_list()
        {
            $ajax_notice_list = $this->ajax_notice_list();
           $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' =>$ajax_notice_list]);
        }

        /**
         * 分页公告列表
         * @return array|false|\PDOStatement|string|\think\Collection
         * @throws \think\db\exception\DataNotFoundException
         * @throws \think\db\exception\ModelNotFoundException
         * @throws \think\exception\DbException
         */
        public function ajax_notice_list()
        {
            //这里查属于哪个分类下面的文章，由于是每个客户分类都不一样，可删除，到时候这里填分类id
            $cat_id = 23;
            $p = input('p',1);
            $list = db('article')->field('title,article_id')->where(['cat_id'=>$cat_id,'is_open'=>1])->page($p,4)->cache(true)->select();
            return $list?$list:[];
        }
    public function qr_code_raw()
    {
        ob_end_clean();
        // 导入Vendor类库包 Library/Vendor/Zend/Server.class.php
        //http://www.tp-shop.cn/Home/Index/erweima/data/www.99soubao.com
        vendor('phpqrcode.phpqrcode');
        //import('Vendor.phpqrcode.phpqrcode');
        error_reporting(E_ERROR);
        $url = urldecode($_GET["data"]);
        \QRcode::png($url);
        exit;
    }


    /**
     * 猜你喜欢
     * @return array|mixed
     */
    public function ajaxLikePage()
    {
        //获取推荐商品
        $GoodsLogic = new GoodsLogic();
        $getLikeGoods = $GoodsLogic->getLikeGoods(input('p'),6);
        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' =>$getLikeGoods?$getLikeGoods:[]]);
    }

    /**
     * 用户分销二维码
     */
    public function qr_code()
    {
        ob_end_clean();
        vendor('topthink.think-image.src.Image');
        vendor('phpqrcode.phpqrcode');

        error_reporting(E_ERROR);
        $url = isset($_GET['data']) ? $_GET['data'] : '';
        $url = urldecode($url);
        $head_pic = input('get.head_pic', '');
        $back_img = input('get.back_img', '');
        $valid_date = input('get.valid_date', 0);
        
        $qr_code_path = './public/upload/qr_code/';
        if (!file_exists($qr_code_path)) {
            mkdir($qr_code_path);
        }
        
        /* 生成二维码 */
        $qr_code_file = $qr_code_path.time().rand(1, 10000).'.png';
        \QRcode::png($url, $qr_code_file, QR_ECLEVEL_M);
        
        /* 二维码叠加水印 */
        $QR = Image::open($qr_code_file);
        $QR_width = $QR->width();
        $QR_height = $QR->height();

        /* 添加背景图 */
        if ($back_img && file_exists($back_img)) {
            $back =Image::open($back_img);
            $back->thumb($QR_width, $QR_height, \think\Image::THUMB_CENTER)
             ->water($qr_code_file, \think\Image::WATER_NORTHWEST, 60);//->save($qr_code_file);
            $QR = $back;
        }

        /* 添加头像 */
        if ($head_pic) {
            //如果是网络头像
            if (strpos($head_pic, 'http') === 0) {
                //下载头像
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_URL, $head_pic); 
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
                $file_content = curl_exec($ch);
                curl_close($ch);
                //保存头像
                if ($file_content) {
                    $head_pic_path = $qr_code_path.time().rand(1, 10000).'.png';
                    file_put_contents($head_pic_path, $file_content);
                    $head_pic = $head_pic_path;
                }
            }
            //如果是本地头像
            if (file_exists($head_pic)) {
                $logo = Image::open($head_pic);
                $logo_width = $logo->height();
                $logo_height = $logo->width();
                $logo_qr_width = $QR_width / 5;
                $scale = $logo_width / $logo_qr_width;
                $logo_qr_height = $logo_height / $scale;
                $logo_file = $qr_code_path.time().rand(1, 10000);
                $logo->thumb($logo_qr_width, $logo_qr_height)->save($logo_file, null, 100);
                $QR = $QR->thumb($QR_width, $QR_height)->water($logo_file, \think\Image::WATER_CENTER);     
                unlink($logo_file);
            }
            if ($head_pic_path) {
                unlink($head_pic_path);
            }
        }
        
        if ($valid_date && strpos($url, 'weixin.qq.com') !== false) {
            $QR = $QR->text('有效时间 '.$valid_date, "./vendor/topthink/think-captcha/assets/zhttfs/1.ttf", 7, '#00000000', Image::WATER_SOUTH);
        }
        $QR->save($qr_code_file, null, 100);
        
        $qrHandle = imagecreatefromstring(file_get_contents($qr_code_file));
        unlink($qr_code_file); //删除二维码文件
        header("Content-type: image/png");
        imagepng($qrHandle);
        imagedestroy($qrHandle);
        exit;
    }

    // 验证码
    public function verify()
    {
        //验证码类型
        $type = I('get.type') ? I('get.type') : '';
        $fontSize = I('get.fontSize') ? I('get.fontSize') : '40';
        $length = I('get.length') ? I('get.length') : '4';

        $config = array(
            'fontSize' => $fontSize,
            'length' => $length,
            'useCurve' => true,
            'useNoise' => false,
        );
        $Verify = new Verify($config);
        $Verify->entry($type);
		exit();
    }


    /**
     * 店铺街
     */
    public function street()
    {
        $sc_id = I('get.sc_id/d');
        $province = I('get.province', 0);
        $city = I('get.city', 0);
        $order = I('order', 0);
        $key = I('q/s', '');
         
        $store_class = Db::name('store_class')->cache(true)->field('sc_id,sc_name')->where('')->select();
        $store_where = ['store_state' => 1,'deleted'=>0,'store_recommend'=>1];
        if ($sc_id) {
            $store_where['sc_id'] = $sc_id;
        }
        if ($province) {
            $store_where['province_id'] = $province;
        }
        if ($city) {
            $store_where['city_id'] = $city;
        }
        if($order){
            $orderBy[$order] = 'desc';
        }else{
            $orderBy = ['store_sort' => 'desc','store_recommend'=> 'desc'];
        }
        if(!empty($key)){
            $store_where['store_name'] = array('like' , "%$key%");
        }
        $store_count = Db::name('store')->alias('s')->where($store_where)->count();
        $page = new Page($store_count, 12);
        $store_list = Db::name('store')->field("store_id,store_name,seo_description,store_logo,store_banner")
            ->where($store_where)->order($orderBy)->limit($page->firstRow, $page->listRows)->select();
        if(is_array($store_list)){
        	foreach ($store_list as $key => $value) {
        		$goods_array['goods_list'] = Db::name('goods')->field("goods_id,goods_name,shop_price,is_virtual")
        		->where([ 'is_on_sale'=>1, 'goods_state'=>1,'store_id'=>$value['store_id']])->limit(3)->order('sort desc')->select();
        		$goods_array['goods_list']['goods_count'] = Db::name('goods')->where(['store_id'=>$value['store_id']])->count();
        		$store_list[$key]['goods_array'] = $goods_array;
        	}
        }
        $region = Db::name('region')->cache(true)->where("`level` = 1")->getField("id,name");
        $this->assign('province', $province);
        $this->assign('city', $city);
        $this->assign('region', $region);
        $this->assign('page', $page);
        $this->assign('store_list', $store_list);
        $this->assign('store_class', $store_class);//店铺分类
        return $this->fetch();
    }

    public function store_qrcode()
    {
        require_once 'vendor/phpqrcode/phpqrcode.php';

        error_reporting(E_ERROR);
        $store_id = I('store_id/d', 1);
        \QRcode::png(U('Mobile/Store/index', array('store_id' => $store_id), true, true));
    }

    /**
     * 使用步骤:
     * 1.将该函数的注释放开
     * 2.浏览器请求该函数, 将打印输出的SQL语句在MYSQL中执行即可清理数据
     *    以下变量: $database 是你的数据库名 
     * 3.执行完成之后将该函数注释起来
     * 注意: 如果执行该函数, 没有输出表名, 请检查你的数据库名是否正确
     * 访问形式  www.xxx.com/home/index/truncate_tables
     */
    function truncate_tables()
    {
        /*
        $tables = DB::query("show tables");
        $database = "tpshopbbc2.0";   //这里改成你的数据库名
        $k_name = "Tables_in_$database";
        $table = array('tp_admin','tp_config','tp_region','tp_system_module','tp_admin_role','tp_system_menu','tp_article_cat','tp_wx_user');        
        foreach ($tables as $key => $val) {
           if(!in_array($val[$k_name], $table)){
               echo 'truncate table'.$val[$k_name].";";
               echo "<br/>";         
           }
        }       
         */
    } 

    /**
     * 猜你喜欢
     * @author dyr
     */
    public function ajax_favorite()
    {
        $p = I('p', 0);
        $item = I('i', 5);//分页数
        $tpl = I('tpl');
        $goods_where = ['g.is_on_sale' => 1, 'g.goods_state' => 1,'g.is_virtual'=>['exp',' = 0 OR g.virtual_indate > '.time()]];
        $favourite_goods = Db::name('goods')->alias('g')->join('__STORE__ s' ,'g.store_id = s.store_id')
            ->field('g.*,s.store_name')
            ->where($goods_where)
            ->order('sort DESC')
            ->page($p, $item)
            ->select();
        $this->assign('favourite_goods', $favourite_goods);
        if ($tpl) {
            if (strstr($tpl,'.')||strstr($tpl,'/') || strstr($tpl,'\\')) {
                $this->error('非法模板名称');
            }
            return $this->fetch($tpl);
        } else {
            return $this->fetch();
        }
    }

}