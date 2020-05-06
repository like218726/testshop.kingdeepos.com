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
 * Author: 聂晓克     
 * Date: 2017-11-15
 */

namespace app\seller\controller;

use app\seller\logic\GoodsCategoryLogic;
use PHPExcel_IOFactory;
use PHPExcel_Cell;
use think\Db;
use think\Page;

//淘宝CSV导入功能
class Import extends Base{
	public function index(){
            
		$res=M('store_goods_class')->where('store_id= '.STORE_ID.' and parent_id=0 and is_show=1')->select();

		$GoodsCategoryLogic = new GoodsCategoryLogic();
        $GoodsCategoryLogic->setStore($this->storeInfo);
        $goodsCategoryLevelOne = $GoodsCategoryLogic->getStoreGoodsCategory();

        $this->assign('goodsCategoryLevelOne',$goodsCategoryLevelOne);//平台商品分类
		$this->assign('store_goods_class_list', $res);//店铺商品分类
		$this->assign('store_id',STORE_ID);
		return $this->fetch();
                
	}

	/*
	ajax返回平台商品分类 
	*/
	public function return_goods_category(){
		$parent_id = I('get.parent_id/d', '0'); //商品分类父id
        empty($parent_id) && exit('');

        $GoodsCategoryLogic = new GoodsCategoryLogic();
        $GoodsCategoryLogic->setStore($this->storeInfo);
        $list = $GoodsCategoryLogic->getStoreGoodsCategory($parent_id);

        foreach ($list as $k => $v) {
            $html .= "<option value='{$v['id']}' rel='{$v['commission']}'>{$v['name']}</option>";
        }
        exit($html);
	}

	//上传的csv文件及图片文件 返回数组结果
	public function upload_data(){
            
		$images = request()->file('images');//图片文件
		$file = request()->file('csv');//csv文件
		$data=I('post.');//表单数据

		//移动到框架应用根目录/public/uploads/csv目录下
		$path = 'public/upload/csv/';
		$arrimg=array();
		if (!file_exists($path)){
            mkdir($path);
        }

        //暂未对tbi文件进行验证 默认合法
        $result = $this->validate(
            ['file2' => $file], 
            ['file2'=>'fileSize:30000000|fileExt:csv'],
            ['file2.fileSize' => '上传csv文件过大', 'file2.fileExt' => '仅可上传csv文件']                    
           );

        if (true !== $result ) {            
            $this->error($result, U('Seller/import/index'));
        }

	    if($file){
	        $info = $file->move($path);
	        if($info){
	        	//上传成功
		        $csv=$info->getSaveName();
	        }else{
	            //上传失败
	            $this->error($file->getError(), U('Seller/import/index'));
	        }
	    }else{
	    	$this->error("导入csv文件失败", U('Seller/import/index'));
	    }


	    if($images){
		    foreach ($images as $k => $v) {
				$res=$v->move($path,'');
				$arrimg[$k]=$res->getSaveName();
			}
	    }else{
	    	$this->error("导入图片文件失败", U('Seller/import/index'));
	    }

	   	/*
	   	*path 上传文件路径
	    *csv  上传后的csv文件路径
	    *img  上传后的图片文件路径数组
	   	*form 提交的表单数据
	    */
	    $arr=array('path'=>$path,'csv'=>$csv,'img'=>$arrimg,'form'=>$data);
	    return $arr;exit();
            
	}

	/**
	 * 读取文件csv
	 * @param $csvFileName |文件名
	 * @param int $line |读取几行，默认全部读取
	 * @param int $offset |从第几行开始读，默认从第一行读取
	 * @return array|string
	 */
	public function getCsv($csvFileName, $line = 0, $offset = 0){
		$handle = fopen($csvFileName,'r');//打开文件，如果打开失败，本函数返回 FALSE。
		if(!$handle){
			return '文件打开失败';
		}
		//fgetcsv() 出错时返回 FALSE，包括碰到文件结束时。
		$i = 0;//用于记录while的循环次数，方便与$line,$offset比较
		$list = [];
		while($data = fgetcsv($handle)){
			$arr = [];
			//小于偏移量则不读取,但$i仍然需要自增
			if($i < $offset && $offset){
				$i++;
				continue;
			}
			//大于读取行数则退出
			if($i > $line && $line){
				break;
			}
			$i++;
			foreach ($data as $key => $value) {
				$content = iconv("gbk","utf-8//IGNORE",$value);//转化编码
				$arr[] = $content;//至于如何处理这个结果，需要根据实际情况而定
			}
			$list[] = $arr;
		}
		return $list;
	}

	//csv导入处理
	public function add_data(){
        
		$arr=$this->upload_data();
		$file=$arr['path'].$arr['csv'];
		$img=$arr['img'];
		$form=$arr['form'];

		$goods = $this->getCsv('./'.$file,0,3); //从第4行开始读取
		if(!is_array($goods) && !empty($goods)){
			$this->error("无数据或读取文件失败", U('Seller/import/index'));
		}

		//csv数据转换
		$param=array();
		foreach ($goods as $k => $v) {
			//tpshop数据字段 = 淘宝csv数据字段
			$param[$k]['goods_name']=$v[0];		//商品名称
			$param[$k]['cat_id1']=$form['goods_class_id1'];			//商品一级分类
			$param[$k]['cat_id2']=$form['goods_class_id2'];			//商品二级分类
			$param[$k]['cat_id3']=$form['goods_class_id3'];			//商品三级分类  
			$param[$k]['store_cat_id1']=$form['store_cat_id1'];		//本店一级分类    
			$param[$k]['store_cat_id2']=$form['store_cat_id2'];		//本店二级分类 
			$param[$k]['store_count']=empty($v[9])?10:$v[9];		//商品库存
			$param[$k]['on_time']=time();							//商品上架时间
			$param[$k]['market_price']=empty($v[7])?0:$v[7];		//市场价
			$param[$k]['shop_price']=empty($v[7])?0:$v[7];			//本店价

			$v[20] = htmlspecialchars($v['20']); 					// 商品详情处理
			$v[20] = str_replace("FILE:///c:\\详情图片请放在C盘根目录\\",'/'.$arr['path'],$v[20]); // 商品详情图片 /public/upload/csv/
			$param[$k]['goods_remark']=empty($v[57])?'':$v[57];		//商品简单描述
			$param[$k]['goods_content']=empty($v[20])?'':$v[20];	//商品详细描述
			$param[$k]['store_id']=STORE_ID;						//店铺id
			$param[$k]['is_new']=empty($v[3])?0:$v[3];				//是否新品
			$param[$k]['images']=empty($v[28])?'':$v[28];        	//相册图片 临时存储
		} 

		foreach ($param as $k => $v) {
			$arr3 = [];
			$arrs = explode('|;', $v['images']);
			foreach($arrs as $v){
				if(!empty($v)){
					$arr2 = explode(':',$v);
					$arr3[] = '/'.$arr['path'].$arr2[0].'.tbi';
				}
			}
			$param[$k]['images'] = $arr3;
		}

		//生成上传图片地址数组  图片名=>图片地址
		foreach ($img as $k => $v){
			$img[str_replace('.tbi','', $v)]='/'.$arr['path'].$v;//添加关联元素
			unset($img[$k]);//删除索引元素
		}

		// 图片全路径，上面已带好,不用过滤非选的图片
        /*foreach ($param as $k => $v){
            foreach ($v['images'] as $k2 => $v2) {
                //淘宝的图片存储格式替换为图片地址形式
                $param[$k]['images'][$k2]=$img[substr($v2,0,strpos($v2,':'))];
            }
        }*/

        //数据插入
        $add=0;
        foreach ($param as $k => $v) {
            if($v['images']){
                $v['original_img']=$v['images'][0];//没有主图时默认取相册图片第一张
            }
            $goods_id=M('goods')->add($v);//goods表插入主体数据
            if($goods_id){
				$goods_sn = "TP".str_pad($goods_id,7,"0",STR_PAD_LEFT);
				Db::name('goods')->where('goods_id',$goods_id)->update(['goods_sn'=>$goods_sn]);
                if($v['images']){
                    foreach ($v['images'] as $k2 => $v2) {
                        $res=M('goods_images')->add(array('goods_id'=>$goods_id,'image_url'=>$v2));//goods_image表插入商品图片
                        if(!$res) continue;
                    }
                }
            }else{
                $add+=1;//统计插入失败次数
                continue;//某次循环数据插入失败时跳出当前循环执行下一个
            }
        } 

        if($add==count($param)){
            $this->error("商品添加失败", U('Seller/import/index'));
        }else{
            $this->success("商品添加成功", U('Seller/Goods/goodsList'));
        }
        
	}

	/**
	 * 淘宝导入时不用
	 * csv文件转码为utf8
	 * @param  string 文件路径
	 * @return resource  打开文件后的资源类型
	 */
	private function fopen_utf8($filename){  
        $encoding='';  
        $handle = fopen($filename, 'r');  
        $bom = fread($handle, 2);   
        rewind($handle);  
       
        if($bom === chr(0xff).chr(0xfe)  || $bom === chr(0xfe).chr(0xff)){   
            $encoding = 'UTF-16';  
        } else {  
            $file_sample = fread($handle, 1000) + 'e';    
            rewind($handle);  
            $encoding = mb_detect_encoding($file_sample , 'UTF-8, UTF-7, ASCII, EUC-JP,SJIS, eucJP-win, SJIS-win, JIS, ISO-2022-JP');  
        }  
        if ($encoding){  
            stream_filter_append($handle, 'convert.iconv.'.$encoding.'/UTF-8');  
        }  
        return ($handle);  
    } 

	/**
	 * 淘宝导入时不用
	 * @param $string
	 * @param string $delimiter
	 * @param string $enclosure
	 * @return array|mixed
	 */
	private function str_getcsv($string, $delimiter=',', $enclosure='"'){ 
        $fp = fopen('php://temp/', 'r+');
        fputs($fp, $string);
        rewind($fp);
        while($t = fgetcsv($fp, strlen($string), $delimiter, $enclosure)) {
            $r[] = $t;
        }
        if(count($r) == 1) 
            return current($r);
        return $r;
    }

    //excel导入/导出首页
    public function excel_index(){
    	$res=M('store_goods_class')->where('store_id= '.STORE_ID.' and parent_id=0 and is_show=1')->select();

		$GoodsCategoryLogic = new GoodsCategoryLogic();
        $GoodsCategoryLogic->setStore($this->storeInfo);
        $goodsCategoryLevelOne = $GoodsCategoryLogic->getStoreGoodsCategory();

        $this->assign('goodsCategoryLevelOne',$goodsCategoryLevelOne);//平台商品分类
		$this->assign('store_goods_class_list', $res);//店铺商品分类
		$this->assign('store_id',STORE_ID);
    	return $this->fetch();
    }

    //excel导入处理
    public function excel_import(){	
    	$file=request()->file('excel');
    	$images=request()->file('images');
    	$form=I('post.');//表单数据
        if(!$form['goods_class_id1'] || !$form['goods_class_id2'] || !$form['goods_class_id3']){
            $this->error('请先选择商品分类');
        }
    	$path = 'public/upload/excel/';
		if (!file_exists($path)){
            mkdir($path);
        }

    	$result = $this->validate(	//验证excel文件
            ['file' => $file], 
            ['file'=>'fileSize:1500000|fileExt:xls'],
            ['file.fileSize' => '上传excel文件过大','file.fileExt'=>'仅能上传excel文件xls格式']
        );

        $result2 = $this->validate(	//验证图片
            ['file' => $images], 
            ['file'=>'fileSize:600000|fileExt:jpg,png,jpeg'],
            ['file.fileSize' => '上传图片过大','file.fileExt'=>'仅能上传图片文件']                    
        );

        if (true !== $result ) {            
            $this->error($result, U('Seller/import/excel_index'));
        }
        if (true !== $result2 ) {            
            $this->error($result2, U('Seller/import/excel_index'));
        }

        if($file){
	        $info = $file->move($path);
	        if($info){
	        	//上传成功
		        $excel=$info->getSaveName();
	        }else{
	            //上传失败
	            $this->error($file->getError(), U('Seller/import/excel_index'));
	        }
	    }else{
	    	$this->error("导入excel文件失败", U('Seller/import/excel_index'));
	    }

	    $arrimg=array();
	    if($images){
		    foreach ($images as $k => $v){
				$res=$v->move($path,'');
				$arrimg[$k]=$res->getSaveName();
			}
	    }

	    //导入的excel数据处理开始
    	$excel=$path.$excel;
    	$arr=$this->importExcel($excel);//

    	//excel模板头数组
        $excel_model=array('编号','商品名','库存','市场价','本店价','成本价','简单描述','详细描述');
    	$excel_title=$arr[1];//excel头部标题部分
    	if($excel_title!==$excel_model){
    		$this->error('excel数据格式错误,请下载并参照excel模板',U('Seller/import/excel_index'));
    	}
    	unset($arr[1]);

    	$goods_sn=array();
        foreach ($arr as $k => $v) {
            if(!$v[3] || !(is_numeric(trim($v[3])))){	//判断市场价
                $this->error('编号为'.$v[0].'市场价不能为空且仅能设为数字',U('Seller/import/excel_index'));
                break;
            }
            if(!$v[4] || !(is_numeric(trim($v[4])))){	//判断本店价
                $this->error('编号为'.$v[0].'本店价不能为空且仅能设为数字',U('Seller/import/excel_index'));
                break;
            }
            if($v[2] && !(is_numeric(trim($v[2])))){
                $this->error('编号为'.$v[0].'库存仅能设为数字',U('Seller/import/excel_index'));
                break;
            }
            if($v[0]){
                $goods_sn[]=$v[0];	//记录填写了的货号
            }
        }

    	//判断导入的货号是否有重复
		if (count($goods_sn) != count(array_unique($goods_sn))) {
            // 获取去掉重复数据的数组
            $unique_arr = array_unique ( $goods_sn );
            // 获取重复数据的数组
            $repeat_arr = array_diff_assoc ( $goods_sn, $unique_arr );
		   $this->error(implode(',',$repeat_arr)."商品货号不能重复", U('Seller/import/excel_index'));
		}

		$store_goods_sn=Db::name('goods')->where('goods_sn !=""')->getField('goods_sn',true);

		$same=array_intersect($store_goods_sn,$goods_sn);
		if($same){
			$this->error(implode(',',$same).'商品货号与商城已有商品存在重复',U('Seller/import/excel_index'));
		}

		foreach ($arrimg as $k => $v) {
			$tmp=substr(strrev($v),(strpos(strrev($v),".")+1),strlen($v));
			foreach ($arr as $k2 => $v2) {
				$tmp2=strrev($v2[0]);
				if(($tmp2) && ($tmp2==$tmp)){
					$arr[$k2][8]=$path.$v;
					continue;
				}
			}		
		}	


		//excel数据转换
		$param=array();
		foreach ($arr as $k => $v) {
			//tpshop数据字段 = excel数据字段
			$param[$k]['goods_sn']=$v[0];		//商品货号
			$param[$k]['goods_name']=$v[1];		//商品名称
			$param[$k]['cat_id1']=$form['goods_class_id1'];			//商品一级分类
			$param[$k]['cat_id2']=$form['goods_class_id2'];			//商品二级分类
			$param[$k]['cat_id3']=$form['goods_class_id3'];			//商品三级分类  
			$param[$k]['store_cat_id1']=$form['store_cat_id1'];		//本店一级分类    
			$param[$k]['store_cat_id2']=$form['store_cat_id2'];		//本店二级分类 
			$param[$k]['store_count']=$v[2];	//商品库存  
			$param[$k]['on_time']=time();		//商品上架时间
			$param[$k]['market_price']=$v[3];	//市场价
			$param[$k]['shop_price']=$v[4];		//本店价
			if($v[5]){
				$param[$k]['cost_price']=$v[5];	//成本价
			}
			$param[$k]['goods_remark']=$v[6];	//商品简单描述
			$param[$k]['goods_content']=$v[7];	//商品详细描述
			$param[$k]['store_id']=STORE_ID;	//店铺id
			$param[$k]['images']='/'.$v[8];                       
		} 

        //数据插入
        $add=0;
        foreach ($param as $k => $v) {
            if($v['images']){
                $v['original_img']=$v['images'];//没有主图时默认设为主图
            }
            $goods_id=M('goods')->add($v);//goods表插入主体数据
            if($goods_id){
                if($v['images']){
                    $res=M('goods_images')->add(array('goods_id'=>$goods_id,'image_url'=>$v['images']));//goods_image表插入商品图片
                }
            }else{
                $add+=1;//统计插入失败次数
                continue;//某次循环数据插入失败时跳出当前循环执行下一个
            }
        } 

        if($add==count($param)){
            $this->error("商品excel导入失败", U('Seller/import/index'));
        }else{
            $this->success("商品excel导入成功", U('Seller/Goods/goodsList'));
        }
    }

    public function importExcel($file){
	    require_once './vendor/PHPExcel/PHPExcel.php';
	    require_once './vendor/PHPExcel/PHPExcel/IOFactory.php';
	    require_once './vendor/PHPExcel/PHPExcel/Reader/Excel5.php';

	    $objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format
	    $objPHPExcel = $objReader->load($file);

	    $sheet = $objPHPExcel->getSheet(0);
	    $highestRow = $sheet->getHighestRow(); // 取得总行数
	    $highestColumn = $sheet->getHighestColumn(); // 取得总列数
	    $objWorksheet = $objPHPExcel->getActiveSheet();
	 
	    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	    $excelData = array();
	    for ($row = 1; $row <= $highestRow; $row++) {
	        for ($col = 0; $col < $highestColumnIndex; $col++) {
	            $excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
	        }
	    }
	    return $excelData;
	}
}