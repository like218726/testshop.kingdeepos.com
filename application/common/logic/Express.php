<?php
namespace  app\common\logic;


class Express 
{
    
    protected $logistics_id;
    protected $customer;
    protected $key;
    protected $config = array();
    protected $EBusinessID ;
    protected $AppKey ;
    protected $ReqURL='http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx';
    
    function __construct($logistics_id)
    {
        $this->config = tpCache('express');
        $this->logistics_id=$logistics_id;
        $this->CheckConfig();
    }
    
    
    /**
     * 
     * 获取物流信息
     * $data['num] 订单号
     * $data['coding] 快递公司的编码
     * @param array $data  */
    public  function getExpressList($data)
    {
        if($this->logistics_id==1){//快递100
            $postData['com'] = $data['coding'];
            $postData['num'] = $data['num'];
            $postData['from'] = '';
            $postData['phone'] = '';
            $postData['to'] = '';
            $postData['resultv2'] = 1;
            $result = $this->GetData($postData);
        }elseif ($this->logistics_id==2){//快递鸟 "{'OrderCode':'','ShipperCode':'YTO','LogisticCode':'12345678'}"
            $postData['OrderCode']      = date('YmdHis');
            $postData['ShipperCode']    = $data['coding'];
            $postData['LogisticCode']   = $data['num'];
            $result = $this->getOrderTracesByJson($postData);
        }
        return $result;
    }
    
    
    protected function CheckConfig() 
    {
        if($this->logistics_id==1){//快递100
            //检测配置信息是完善
            if($this->config['kd100_key']=='' ||$this->config['kd100_key']=='kd100_customer'){
                return false;
            }
            $this->customer=$this->config['kd100_customer'];
            $this->key=$this->config['kd100_key'];
        }elseif ($this->logistics_id==2){//快递鸟
            $this->EBusinessID = $this->config['kdniao_id'];
            $this->AppKey = $this->config['kdniao_key'];
        }
    }
    
    
    /*==================================快递100=====================================*/
    /* *
     *$data['com] 查询的快递公司的编码， 一律用小写字母
     * $data['num] 查询的快递单号， 单号的最大长度是32个字符
     */
    protected  function GetData($data){
        $Resdata['customer'] = $this->customer;  
        $Resdata['param'] = $this->CheckData($data);
        $Resdata['sign']  = strtoupper($this->getSign($Resdata['param']));
        $PostData = $this->getPostData($Resdata);
        $url = "http://poll.kuaidi100.com/poll/query.do";
        return  $this->curl($url, $PostData);
    }
    
    
    private function CheckData($data)
    {
        if($data['com']==''|| $data['num']=='')
        {
            return  false;
        }
        return  json_encode($data);
    }
    
    
    
    
    
    protected function  getSign($data)
    {
        return  md5($data.$this->key.$this->customer);
    }
    
    
    protected function  getPostData($data)
    {
        
        $str = '';
        foreach ($data as $k => $v)
        {
            $str .= "$k=".urlencode($v)."&";	
        }
        return  substr($str, 0, -1);
    }
    
    
    
    protected function curl($url,$data){
        $ch = curl_init();
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	$result = curl_exec($ch);
    	$data = str_replace("\"", '"', $result );
    	$data = json_decode($data,true);
    	if($data['status']==200){
    	    return ['code'=>200,'data'=>$data['data']];
    	}else {
    	    return ['code'=>0,'msg'=>$data['message']];
    	}
    }
    
 
    
    
    /*==================================快递鸟=====================================*/
    
    /**
     * Json方式 查询订单物流轨迹
     * 
     *data = "{'OrderCode':'','ShipperCode':'YTO','LogisticCode':'12345678'}"
     */
    protected  function getOrderTracesByJson($data){
    	$requestData= json_encode($data);
    	
    	$datas = array(
            'EBusinessID' => $this->EBusinessID,
            'RequestType' => '1002',
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );
        $datas['DataSign'] = $this->encrypt($requestData, $this->AppKey);
    	$result = $this->sendPost($this->ReqURL, $datas);	
    	
    	$result = json_decode($result,true);
    	if($result['State']==3 || $result['State']==2){
    	    return ['code'=>200,'data'=>$result['Traces']];
    	}else {
    	    return ['code'=>0,'msg'=>$result['Reason']];
    	}
    }
    
    /**
     *  post提交数据
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据
     * @return url响应返回的html
     */
    protected function sendPost($url, $datas) {
        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if(empty($url_info['port']))
        {
            $url_info['port']=80;
        }
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd);
    
        return $gets;
    }
    
    /**
     * 电商Sign签名生成
     * @param data 内容
     * @param appkey Appkey
     * @return DataSign签名
     */
    protected function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }
    
    
    
    
    
}