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
 * Author: 当燃      
 * Date: 2015-09-22
 */
 
namespace app\seller\controller;
use app\common\logic\EditorLogic;
use common\util\File;
use think\Request;
use think\Db;
use app\seller\logic\AdminLogic;

class Uploadify extends Base{
	
	private $sub_name = array('date', 'Y/m-d');
	private $savePath = 'temp/';
	
	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set("Asia/Shanghai");
		$this->savePath = I('savepath','temp').'/';
		error_reporting(E_ERROR | E_WARNING);
		header("Content-Type: text/html; charset=utf-8");
	}
	
    public function upload(){
        $func = I('func');
        $path =  I('path','temp');// I('path','temp');
        $onlyUpload = I('onlyUpload/d' , 0); //只显示上传"本地上传" tab
		$image_upload_limit_size = config('image_upload_limit_size');
        $fileType = I('fileType','Images');  //上传文件类型，视频，图片
        if($fileType == 'Flash'){
            $upload = U('Uploadify/videoUp',array('savepath'=>$path,'pictitle'=>'banner','dir'=>'video'));
        }else{
            $upload = U('Uploadify/imageUp',array('savepath'=>$path,'pictitle'=>'banner','dir'=>'images'));
        }
        $albumList = M('StoreAlbum')->where(['store_id' => STORE_ID])->order("sort asc")->select();
        /* 获取文件列表 */
        $adminLogc = new AdminLogic();
        foreach ($albumList as $k => $v){
            $filePath = UPLOAD_PATH.'store/'.session('store_id').'/goods/album_'.$v['id'];
            $imageList = $adminLogc->getfiles($filePath, 'png|jpg|jpeg|gif|bmp', '' ,true);
            $albumList[$k]['count'] = count($imageList);
        }
        
        $info = array(
        	'num'=> I('num/d'),
            'fileType'=> $fileType,
            'title' => '',       	
            'upload' =>$upload,
        	'fileList'=>U('Uploadify/fileList',array('path'=>$path)),
            'size' => $image_upload_limit_size/(1024 * 1024).'M',
            'type' =>'jpg,png,gif,jpeg',
            'input' => I('input'),
            'func' => empty($func) ? 'undefined' : $func,
        );
        
        $this->assign('info',$info);
        $this->assign('albumList',$albumList);
        $this->assign('onlyUpload',$onlyUpload);
        
        return $this->fetch();
    }
    
    /**
     * 删除上传的图片
     * @throws \think\Exception
     */
    public function delupload(){
        $action = I('action','del');
        $filename= I('filename');
        $filenames= I('filenames/a');
        $store_id = session('store_id');
         
        if($filenames && count($filenames) > 0){
            //如果是数组就批量删除
            foreach ($filenames as $k  => $v){
                $result = $this->deleteImage($v , $store_id , $action );
            }
        }else{
            //删除单个文件
            $result = $this->deleteImage($filename , $store_id , $action );
        }
        echo $result;
        exit; 
        
    }
    
    /**
     * 删除文件
     * @param unknown $filename
     * @param unknown $store_id
     * @param unknown $action
     * @return number
     */
    private function deleteImage($filename , $store_id , $action ){
        
        $del_key = $filename;

        $filename= empty($filename) ? I('url') : $filename;
        $filename= str_replace('../','',$filename);
        $filename= trim($filename,'.');
        $filename= trim($filename,'/');
        if($action=='del' && !empty($filename) && file_exists($filename)){
            //modify by wangqh fix: 文件路径存在多个.时无法截取后缀问题 @{
            $pos = strripos($filename,'.');
            $filetype = substr($filename, $pos);
            // }

            $phpfile = strtolower(strstr($filename,'.php'));  //排除PHP文件
            $erasable_type = C('erasable_type');  //可删除文件
            if(!in_array($filetype,$erasable_type) || $phpfile){
                exit;
            }
            if(unlink($filename)){
                Db::name('store_extend')->where(['store_id'=>$store_id])->setDec('pic_num',1);
                Db::name('image_extend')->where(['img_url'=>$del_key])->delete();//删除image_extend表关联的数据
                return 1;
            }else{
                return 0;
            }
            exit;
        }
    }
    
    public function fileList(){
    	/* 判断类型 */
    	$type = I('type','Images');
    	if('undefined' == $type){$type = 'Images';}
    	$album_id = I('album_id/d' , 0);
    	// $path = 'goods_other_album';// I('path','temp'); //默认存储在store->goods目录下goods

    	switch ($type){
    		/* 列出图片 */
    		case 'Images' : $allowFiles = 'png|jpg|jpeg|gif|bmp';break;
    		case 'Flash' : $allowFiles = 'flash|swf';break;
    		/* 列出文件 */
    		default : $allowFiles = '.+';
    	}
    	// 获取本店下所有图片
    	$path = UPLOAD_PATH.'store/'.session('store_id').'/';
    	
    	if($album_id < 1){//如果没指定相册, 默认显示第一个
    	    $album_id = M('StoreAlbum')->where(['store_id'=>session('store_id')])->order('sort','asc')->getField('id');
    	}
    	
    	if($album_id > 0){
    	    $path .= "goods_other_album/album_$album_id";
    	}
    	 
    	$listSize = 100000;
    	$key = empty($_GET['key']) ? '' : $_GET['key'];
    	
    	/* 获取参数 */
    	$size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
    	$start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
    	$end = $start + $size;
    	
    	/* 获取文件列表 */
    	$adminLogc = new AdminLogic();
    	$files = $adminLogc->getfiles($path, $allowFiles, $key, false);
    	
    	if (!count($files)) {
    		echo json_encode(array(
    				"state" => "没有相关文件",
    				"list" => array(),
    				"start" => $start,
				'path' =>$path,
    				"total" => count($files)
    		));
    		exit;
    	}
    	
    	/* 获取指定范围的列表 */
    	$len = count($files);
    	$urls = array();
    	for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
    		$list[] = $files[$i];
    		array_push($urls , $files[$i]['url']);
    	}
    	 
    	$where = implode($urls, ",");
    	$extends = M('Image_extend')->where('img_url' , 'in' , $where)->getField("img_url , cn_name , en_name" , true);
    	$returnList = array();
    	foreach ($list as $k =>$v){
    	    $extends[$v['url']]['cn_name'] && $list[$k]['name'] = $extends[$v['url']]['cn_name'];  
    	}
    	
    	/* 返回数据 */
    	$result = json_encode(array(
    			"state" => "SUCCESS",
    			"list" => $list,
    			"start" => $start,
    			"total" => count($files)
    	));
 
    	echo $result;
    }

    
    
    public function index(){
    	$CONFIG2 = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("./public/plugins/Ueditor/php/config.json")), true);
    	$action = $_GET['action'];
    
    	switch ($action) {
    		case 'config':
    			$result =  json_encode($CONFIG2);
    			break;
    			/* 上传图片 */
    		case 'uploadimage':
    			$fieldName = $CONFIG2['imageFieldName'];
    			$result = $this->imageUp();
    			break;
    			/* 上传涂鸦 */
    		case 'uploadscrawl':
    			$config = array(
    			"pathFormat" => $CONFIG2['scrawlPathFormat'],
    			"maxSize" => $CONFIG2['scrawlMaxSize'],
    			"allowFiles" => $CONFIG2['scrawlAllowFiles'],
    			"oriName" => "scrawl.png"
    					);
    					$fieldName = $CONFIG2['scrawlFieldName'];
    					$base64 = "base64";
    					$result = $this->upBase64($config,$fieldName);
    					break;
    					/* 上传视频 */
    		case 'uploadvideo':
    			$fieldName = $CONFIG2['videoFieldName'];
    			$result = $this->upFile($fieldName);
    			break;
    			/* 上传文件 */
    		case 'uploadfile':
    			$fieldName = $CONFIG2['fileFieldName'];
    			$result = $this->upFile($fieldName);
    			break;
    			/* 列出图片 */
    		case 'listimage':
    			$allowFiles = $CONFIG2['imageManagerAllowFiles'];
    			$listSize = $CONFIG2['imageManagerListSize'];
    			$path = $CONFIG2['imageManagerListPath'];
    			$get = $_GET;
    			$result =$this->fileList2($allowFiles,$listSize,$get);
    			break;
    			/* 列出文件 */
    		case 'listfile':
    			$allowFiles = $CONFIG2['fileManagerAllowFiles'];
    			$listSize = $CONFIG2['fileManagerListSize'];
    			$path = $CONFIG2['fileManagerListPath'];
    			$get = $_GET;
    			$result = $this->fileList2($allowFiles,$listSize,$get);
    			break;
    			/* 抓取远程文件 */
    		case 'catchimage':
    			$config = array(
    			"pathFormat" => $CONFIG2['catcherPathFormat'],
    			"maxSize" => $CONFIG2['catcherMaxSize'],
    			"allowFiles" => $CONFIG2['catcherAllowFiles'],
    			"oriName" => "remote.png"
    					);
    					$fieldName = $CONFIG2['catcherFieldName'];
    					/* 抓取远程图片 */
    					$list = array();
    					isset($_POST[$fieldName]) ? $source = $_POST[$fieldName] : $source = $_GET[$fieldName];
    
    					foreach($source as $imgUrl){
    						$info = json_decode($this->saveRemote($config,$imgUrl),true);
    						array_push($list, array(
    						"state" => $info["state"],
    						"url" => $info["url"],
    						"size" => $info["size"],
    						"title" => htmlspecialchars($info["title"]),
    						"original" => htmlspecialchars($info["original"]),
    						"source" => htmlspecialchars($imgUrl)
    						));
    					}
    
    					$result = json_encode(array(
    							'state' => count($list) ? 'SUCCESS':'ERROR',
    							'list' => $list
    					));
    					break;
    		default:
    			$result = json_encode(array(
    			'state' => '请求地址出错'
    					));
    					break;
    	}
    
    	/* 输出结果 */
    	if(isset($_GET["callback"])){
    		if(preg_match("/^[\w_]+$/", $_GET["callback"])){
    			echo htmlspecialchars($_GET["callback"]).'('.$result.')';
    		}else{
    			echo json_encode(array(
    					'state' => 'callback参数不合法'
    			));
    		}
    	}else{
    		echo $result;
    	}
    }
    
    //上传文件
    private function upFile($fieldName){
    	$file = request()->file('file');
    
    	if(empty($file)) $file = request()->file('upfile');
    	
    	$result = $this->validate(
    			['file' => $file],
    			['file'=>'image|fileSize:40000000|fileExt:jpg,jpeg,gif,png'],
    			['file.image' => '上传文件必须为图片','file.fileSize' => '上传文件过大','file.fileExt'=>'上传文件后缀名必须为jpg,jpeg,gif,png']
    	);
    	
    	if (true !== $result || !$file) {
    		$state = "ERROR" . $result;
    		return json_encode(['state' =>$state]);
    	}else{
    		// 移动到框架应用根目录/public/uploads/ 目录下
    		$savePath = 'store/'.session('store_id').'/'.$this->savePath.'/';
    		// 使用自定义的文件保存规则
    		$info = $file->rule(function ($file) {
    			return  md5(mt_rand());
    		})->move('public/upload/'.$this->savePath);
    	}
    	if($info){
    		$data = array(
    				'state' => 'SUCCESS',
    				'url' => '/public/upload/'.$this->savePath.$info->getSaveName(),
    				'title' => $info->getFilename(),
    				'original' => $info->getFilename(),
    				'type' => '.' . $info->getExtension(),
    				'size' => $info->getSize(),
    		);
    		//图片加水印
    		if($this->savePath=='goods/'){
        		$imgresource = ".".$data['url'];
        		$image = \think\Image::open($imgresource);
        		$water = tpCache('water');
        		//$image->open($imgresource);
        		$return_data['mark_type'] = $water['mark_type'];
        		if($water['is_mark']==1 && $image->width()>$water['mark_width'] && $image->height()>$water['mark_height']){
        			if($water['mark_type'] == 'text'){
        				//$image->text($water['mark_txt'],'./hgzb.ttf',20,'#000000',9)->save($imgresource);
        				$ttf = './hgzb.ttf';
        				if (file_exists($ttf)) {
        					$size = $water['mark_txt_size'] ? $water['mark_txt_size'] : 30;
        					$color = $water['mark_txt_color'] ?: '#000000';
        					if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
        						$color = '#000000';
        					}
        					$transparency = intval((100 - $water['mark_degree']) * (127/100));
        					$color .= dechex($transparency);
        					$image->open($imgresource)->text($water['mark_txt'], $ttf, $size, $color, $water['sel'])->save($imgresource);
        					$return_data['mark_txt'] = $water['mark_txt'];
        				}
        			}else{
        				//$image->water(".".$water['mark_img'],9,$water['mark_degree'])->save($imgresource);
        				$waterPath = "." . $water['mark_img'];
        				$quality = $water['mark_quality'] ? $water['mark_quality'] : 80;
        				$waterTempPath = dirname($waterPath).'/temp_'.basename($waterPath);
        				$image->open($waterPath)->save($waterTempPath, null, $quality);
        				$image->open($imgresource)->water($waterTempPath, $water['sel'], $water['mark_degree'])->save($imgresource);
        				@unlink($waterTempPath);
        			}
        		}
        	}
    	}else{
    		$data = array('state' => 'ERROR'.$file->getError());
    	}
    	return json_encode($data);
    }
    
    //列出图片
    private function fileList2($allowFiles,$listSize,$get){
    	$type = I('type','Images');
    	$album_id =I('album_id/d',0);
    	
    	switch ($type){
    		/* 列出图片 */
    		case 'Images' : $allowFiles = 'png|jpg|jpeg|gif|bmp';break;
    		case 'Flash' : $allowFiles = 'flash|swf';break;
    		/* 列出文件 */
    		default : $allowFiles = '.+';
    	}
    	
    	$path = UPLOAD_PATH.'store/'.session('store_id').'/goods/album_'.$album_id;//直接读取相册目录图片 $CONFIG2['imageManagerListPath'];; './'.UPLOAD_PATH.'store/'.session('store_id').'/'.$this->savePath;
    	$listSize = 100000;
    	$key = empty($_GET['key']) ? '' : $_GET['key'];
    	/* 获取参数 */
    	$size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
    	$start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
    	$end = $start + $size;
    	/* 获取文件列表 */
    	$adminLogc = new AdminLogic();
    	$files = $adminLogc->getfiles($path, $allowFiles, $key, false);
    	if (!count($files)) {
    		echo json_encode(array(
    				"state" => "没有相关文件",
    				"list" => array(),
    				"start" => $start,
    				"total" => count($files)
    		));
    		exit;
    	}
    	
    	/* 获取指定范围的列表 */
    	$urls = array();
    	$len = count($files);
    	for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
    		$list[] = $files[$i];
    		array_push($urls, $files[$i]['url']);
    	}
    	
    	$where = implode($urls, ",");
    	$extends = M('Image_extend')->where('img_url' , 'in' , $where)->getField("img_url , cn_name , en_name" , true);
    	$returnList = array();
    	foreach ($list as $k =>$v){
    	    $extends[$v['url']]['cn_name'] && $list[$k]['name'] = $extends[$v['url']]['cn_name'];
    	}
    	/* 返回数据 */
    	$result = json_encode(array(
    			"state" => "SUCCESS",
    			"list" => $list,
    			"start" => $start,
    			"total" => count($files)
    	));
    
    	return $result;
    }
    
    //抓取远程图片
    private function saveRemote($config,$fieldName){
    	$imgUrl = htmlspecialchars($fieldName);
    	$imgUrl = str_replace("&amp;","&",$imgUrl);
    
    	//http开头验证
    	if(strpos($imgUrl,"http") !== 0){
    		$data=array(
    				'state' => '链接不是http链接',
    		);
    		return json_encode($data);
    	}
    	//获取请求头并检测死链
    	$heads = get_headers($imgUrl);
    	if(!(stristr($heads[0],"200") && stristr($heads[0],"OK"))){
    		$data=array(
    				'state' => '链接不可用',
    		);
    		return json_encode($data);
    	}
    	//格式验证(扩展名验证和Content-Type验证)
    	$fileType = strtolower(strrchr($imgUrl,'.'));
    	if(!in_array($fileType,$config['allowFiles']) || stristr($heads['Content-Type'],"image")){
    		$data=array(
    				'state' => '链接contentType不正确',
    		);
    		return json_encode($data);
    	}
    
    	//打开输出缓冲区并获取远程图片
    	ob_start();
    	$context = stream_context_create(
    			array('http' => array(
    					'follow_location' => false // don't follow redirects
    			))
    	);
    	readfile($imgUrl,false,$context);
    	$img = ob_get_contents();
    	ob_end_clean();
    	preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/",$imgUrl,$m);
    
    	$dirname = './public/upload/remote/';
    	$file['oriName'] = $m ? $m[1] : "";
    	$file['filesize'] = strlen($img);
    	$file['ext'] = strtolower(strrchr($config['oriName'],'.'));
    	$file['name'] = uniqid().$file['ext'];
    	$file['fullName'] = $dirname.$file['name'];
    	$fullName = $file['fullName'];
    
    	//检查文件大小是否超出限制
    	if($file['filesize'] >= ($config["maxSize"])){
    		$data=array(
    				'state' => '文件大小超出网站限制',
    		);
    		return json_encode($data);
    	}
    
    	//创建目录失败
    	if(!file_exists($dirname) && !mkdir($dirname,0777,true)){
    		$data=array(
    				'state' => '目录创建失败',
    		);
    		return json_encode($data);
    	}else if(!is_writeable($dirname)){
    		$data=array(
    				'state' => '目录没有写权限',
    		);
    		return json_encode($data);
    	}
		// 文件格式判断
		strstr(strtolower($fullName),'.php') && exit('文件格式不对');
    
    	//移动文件
    	if(!(file_put_contents($fullName, $img) && file_exists($fullName))){ //移动失败
    		$data=array(
    				'state' => '写入文件内容错误',
    		);
    		return json_encode($data);
    	}else{ //移动成功
    		$data=array(
    				'state' => 'SUCCESS',
    				'url' => substr($file['fullName'],1),
    				'title' => $file['name'],
    				'original' => $file['oriName'],
    				'type' => $file['ext'],
    				'size' => $file['filesize'],
    		);
    	}
    
    	return json_encode($data);
    }
    
    /*
     * 处理base64编码的图片上传
    * 例如：涂鸦图片上传
    */
    private function upBase64($config,$fieldName){
    	$base64Data = $_POST[$fieldName];
    	$img = base64_decode($base64Data);
    
    	$dirname = './public/upload/scrawl/';
    	$file['filesize'] = strlen($img);
    	$file['oriName'] = $config['oriName'];
    	$file['ext'] = strtolower(strrchr($config['oriName'],'.'));
    	$file['name'] = uniqid().$file['ext'];
    	$file['fullName'] = $dirname.$file['name'];
    	$fullName = $file['fullName'];
    
    	//检查文件大小是否超出限制
    	if($file['filesize'] >= ($config["maxSize"])){
    		$data=array(
    				'state' => '文件大小超出网站限制',
    		);
    		return json_encode($data);
    	}
    
    	//创建目录失败
    	if(!file_exists($dirname) && !mkdir($dirname,0777,true)){
    		$data=array(
    				'state' => '目录创建失败',
    		);
    		return json_encode($data);
    	}else if(!is_writeable($dirname)){
    		$data=array(
    				'state' => '目录没有写权限',
    		);
    		return json_encode($data);
    	}
		// 文件格式判断
		strstr(strtolower($fullName),'.php') && exit('文件格式不对');
    
    	//移动文件
    	if(!(file_put_contents($fullName, $img) && file_exists($fullName))){ //移动失败
    		$data=array(
    				'state' => '写入文件内容错误',
    		);
    	}else{ //移动成功
    		$data=array(
    				'state' => 'SUCCESS',
    				'url' => substr($file['fullName'],1),
    				'title' => $file['name'],
    				'original' => $file['oriName'],
    				'type' => $file['ext'],
    				'size' => $file['filesize'],
    		);
    	}
    
    	return json_encode($data);
    }
    
    /**
     * @function imageUp
     */
    public function imageUp()
    {
    	//上传图片框中的描述表单名称，
    	$pictitle = I('pictitle');
    	$album_id = I('album_id');    //相册

    	$dir = I('dir');
        $store_id = session('store_id');
        $pic_num = Db::name('store_extend')->where(['store_id'=>$store_id])->getField('pic_num');  //查找店铺已经传了多少图片
        $sg_album_limit = Db::name('store_grade')->where(['sg_id'=>$this->storeInfo['grade_id']])->getField('sg_album_limit');  //查找店铺已经传了多少图片
    if ($pic_num >= $sg_album_limit && $sg_album_limit>0)
            $this->ajaxReturn(['state'=>'当前允许上传图片数量已到达店铺等级限制的【'.$sg_album_limit.'张】'],'json');
    	$title = htmlspecialchars($pictitle , ENT_QUOTES);
    	$path = htmlspecialchars($dir, ENT_QUOTES);
    	// 获取表单上传文件
    	$file = request()->file('file');
    	if(empty($file)) $file = request()->file('upfile');

    	$result = $this->validate(
    			['file' => $file],
    			['file'=>'image|fileSize:40000000|fileExt:jpg,jpeg,gif,png'],
    			['file.image' => '上传文件必须为图片','file.fileSize' => '上传文件过大','file.fileExt'=>'上传文件后缀名必须为jpg,jpeg,gif,png']
    	);

    	$upload_max_filesize = @ini_get('file_uploads') ? ini_get('upload_max_filesize') :'unknown';
    	if (true !== $result || !$file) {
    		$state = "ERROR : $result";
    	}elseif (!$file){
            $state = "ERROR 图片过大, 最大不能超过: $upload_max_filesize";
        } else {
    		$savePath = 'store/'.$store_id.'/'.$this->savePath;
    		$ossConfig = tpCache('oss');
    		$ossSupportPath = ['goods', 'water','brand','goods_album']; // 增加品牌和相册也上传oss
    		if (in_array(I('savepath'), $ossSupportPath) && $ossConfig['oss_switch']) {
    			//商品图片可选择存放在oss
    			$object = 'public/upload/'.$savePath.md5(time().mt_rand(1,999999999)).'.'.pathinfo($file->getInfo('name'), PATHINFO_EXTENSION);
    			$ossClient = new \app\common\logic\OssLogic;
    			$return_url = $ossClient->uploadFile($file->getRealPath(), $object);
    			if (!$return_url) {
    				$state = "ERROR" . $ossClient->getError();
    				$return_url = '';
    			} else {
    				$state = "SUCCESS";
    			}
    			@unlink($file->getRealPath());
				$return_data['url'] = $return_url;
    		} else {
    		    //用户选择了相册则保存到相册中
    		    if($album_id > 0){
    		        $savePath .= "album_$album_id/";
    		    }

    			// 移动到框架应用根目录/public/uploads/ 目录下
    			$info = $file->rule(function ($file) {
    				return  md5(mt_rand()); // 使用自定义的文件保存规则
    			})->move('public/upload/'.$savePath);

    			$cn_name =$file->getInfo('name');

    			$en_name = $info->getSaveName();

    			if ($info) {
    				$state = "SUCCESS";
    			} else {
    				$state = "ERROR" . $file->getError();
    			}
    			$return_url = '/public/upload/'.$savePath.$info->getSaveName();
				$return_data['url'] = $return_url;
                $pos = strripos($return_url,'.');
                $filetype = substr($return_url, $pos);
                if (I('savepath') =='goods' || I('savepath') =='goods_album' && $filetype != '.gif') {  //只有商品图和相册才打水印，GIF格式不打水印
                    $editor = new EditorLogic();
                    $editor->waterImage(".".$return_url);  //水印
                }
				//将图片名称保存到ImageExtend
				M('ImageExtend')->save(['img_url'=>$return_data['url'] , 'cn_name'=>$cn_name , 'en_name'=>$en_name]);
    		}
    	}

    	$return_data['title'] = $title;
    	$return_data['original'] = '';
    	$return_data['state'] = $state;
    	$return_data['path'] = $path;

		if($ossConfig['oss_switch']) {
			Db::name('store_extend')->where(['store_id'=>$store_id])->setInc('pic_num',1);
			$this->ajaxReturn($return_data,'json');
		}
		if($state == 'SUCCESS'){
			if($this->savePath=='goods/'){

        		$imgresource = ".".$return_url;

        		require_once 'vendor/topthink/think-image/src/Image.php';
        		require_once 'vendor/topthink/think-image/src/image/Exception.php';
        		if(strstr(strtolower($imgresource),'.gif'))
        		{
        		    require_once 'vendor/topthink/think-image/src/image/gif/Encoder.php';
        		    require_once 'vendor/topthink/think-image/src/image/gif/Decoder.php';
        		    require_once 'vendor/topthink/think-image/src/image/gif/Gif.php';
        		}

        		$image = \think\Image::open($imgresource);
                $pos = strripos($return_url,'.');
                $filetype = substr($return_url, $pos);
        		$water = tpCache('water');
        		$return_data['mark_type'] = $water['mark_type'];
                //gif格式的不加水印
        		if($filetype != '.gif' &&$water['is_mark']==1 && $image->width()>$water['mark_width'] && $image->height()>$water['mark_height']){
        			if($water['mark_type'] == 'text'){
        				//$image->text($water['mark_txt'],'./hgzb.ttf',20,'#000000',9)->save($imgresource);
        				$ttf = './hgzb.ttf';
        				if (file_exists($ttf)) {
        					$size = $water['mark_txt_size'] ? $water['mark_txt_size'] : 30;
        					$color = $water['mark_txt_color'] ?: '#000000';
        					if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
        						$color = '#000000';
        					}
        					$transparency = intval((100 - $water['mark_degree']) * (127/100));
        					$color .= dechex($transparency);
        					$image->text($water['mark_txt'], $ttf, $size, $color, $water['sel'])->save($imgresource);
        					$return_data['mark_txt'] = $water['mark_txt'];
        				}
        			}else{
        				$waterPath = "." . $water['mark_img'];
        				$image->water($waterPath, $water['sel'], $water['mark_degree'])->save($imgresource);
        			}
        		}
        	}
        }
        Db::name('store_extend')->where(['store_id'=>$store_id])->setInc('pic_num',1);
    	$this->ajaxReturn($return_data,'json');
    }

    /**
     * 上传视频
     */
    public function videoUp()
    {
        $pictitle = I('pictitle');
        $dir = I('dir');
        $store_id = session('store_id');
        $title = htmlspecialchars($pictitle , ENT_QUOTES);
        $path = htmlspecialchars($dir, ENT_QUOTES);
        // 获取表单上传文件
        $file = request()->file('file');
        if (empty($file)) {
            $file = request()->file('upfile');
        }
        $result = $this->validate(
            ['file' => $file],
            ['file'=>'fileSize:40000000|fileExt:mp4,3gp,flv,avi,wmv'],
            ['file.fileSize' => '上传文件过大','file.fileExt'=>'上传文件后缀名必须为mp4,3gp,flv,avi,wmv']
        );
        if (true !== $result || !$file) {
            $state = "ERROR" . $result;
        } else {
            // 移动到框架应用根目录/public/uploads/ 目录下
            $new_path = 'store/'.$store_id.'/'.$this->savePath;
            // 使用自定义的文件保存规则
            $info = $file->rule(function ($file) {
                return  md5(mt_rand());
            })->move(UPLOAD_PATH.$new_path);
            if ($info) {
                $state = "SUCCESS";
            } else {
                $state = "ERROR" . $file->getError();
            }
            $return_data['url'] = '/'.UPLOAD_PATH.$new_path.$info->getSaveName();
        }

        $return_data['title'] = $title;
        $return_data['original'] = '';
        $return_data['state'] = $state;
        $return_data['path'] = $path;
        $this->ajaxReturn($return_data);
    }
}