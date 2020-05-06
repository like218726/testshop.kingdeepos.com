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
 * Author: IT宇宙人      
 * Date: 2017-05-09
 */

namespace app\admin\controller;
use think\Db;
use think\Cache;
use think\Config;
class Table extends Base{
		
    
    /*
     * 示例访问     
     * http://www.xxx.com/admin/Table/move_3month/keys/5f5249996df1fa0476c104583d54517b
     */
    public function _initialize() {
        parent::_initialize();
        ini_set("max_execution_time", '0');  //脚本永不过时 
        header("Content-type: text/html; charset=utf-8");
        //if($_GET['keys'] != md5('www.tpshop.cn'))
            //exit('秘钥错误');         
    }    
    
        /**
         * 将三个月的表数据移动至 今年内的表数据
         */
	public function move_3month()
	{                               
            // 移动数据 到今年表
            $page_size =  100000; // 每次插入条数            
            while(true)
            {
                $r = 0; //每一次插入影响行数
                // 订单表
                $order_id = M('order_this_year')->max('order_id');
                // 如果id为空 可能是今年刚创建的表
                if(empty($order_id))                
                {
                    $this_year_time = strtotime(date('Y-01-01')); // 获取今年的时间戳
                    $order_id = M('order')->where("add_time >= $this_year_time")->min('order_id');
                    if(empty($order_id))
                        break; // 如果今年还没有订单数据 则跳出                    
                    $order_id -= 1; // 往前退一位, 后面查询 > $order_id 的时候能查询到自己 
                }                                    
                $r += Db::execute("insert into __PREFIX__order_this_year select * from __PREFIX__order where order_id  > $order_id limit $page_size");
                                
                // 订单商品表            
                $rec_id = M('order_goods_this_year')->max('rec_id'); 
                if(empty($rec_id))
                {
                    $rec_id = M('order_goods')->where("order_id  > $order_id")->min('rec_id');
                    $rec_id -= 1; 
                }   
                $r += Db::execute("insert into __PREFIX__order_goods_this_year select * from __PREFIX__order_goods where rec_id  > $rec_id limit $page_size");
                
                // 订单操作日志表
                $action_id = M('order_action_this_year')->max('action_id');
                if(empty($action_id))
                {
                    $action_id = M('order_action')->where("order_id  > $order_id")->min('action_id');
                    $action_id -= 1; 
                }                
                $r += Db::execute("insert into __PREFIX__order_action_this_year select * from __PREFIX__order_action where action_id  > $action_id limit $page_size");
                
                // 发货单表
                $id = M('delivery_doc_this_year')->max('id'); 
                if(empty($id))
                {
                   $id = M('delivery_doc')->where("order_id  > $order_id")->min('id');
                   $id -= 1;
                }
                $r += Db::execute("insert into __PREFIX__delivery_doc_this_year select * from __PREFIX__delivery_doc where id  > $id limit $page_size");
                
                // 分销分成记录表
                $id = M('rebate_log_this_year')->max('id');
                if(empty($id))
                {
                   $id = M('rebate_log')->where("order_id  > $order_id")->min('id');
                   $id -= 1;
                }               
                $r += Db::execute("insert into __PREFIX__rebate_log_this_year select * from __PREFIX__rebate_log where id  > $id limit $page_size");
                
                // 没有影响行数 说明没有数据可以插入 跳出循环
                if($r == 0)
                    break;
                echo "插入 $r 行".PHP_EOL;
            }
            
            // 删除三个月以前的数据
            $before_3month = strtotime("-3 month");
            $order_id = M('order')->where("add_time < $before_3month")->max('order_id');
            if($order_id > 0)
            {
                M('order')->where("order_id <= $order_id ")->delete();
                M('order_goods')->where("order_id <= $order_id ")->delete();
                M('order_action')->where("order_id <= $order_id ")->delete();
                M('delivery_doc')->where("order_id <= $order_id ")->delete();
                M('rebate_log')->where("order_id <= $order_id ")->delete();            
            }
                // 如果是新的一年的新一天
                // 创建新的表
                if(date('m-d') == '01-01')
                {
                    $this->create_table();
                }               
                // 更新索引表
                $this->update_table_index();
                echo date('Y-m-d H:i:s')."执行:success !".PHP_EOL;
	}
        /**
         * 跨年度创建新表
         * 此定时任务需要在 凌晨过了 12点 也就是第二天执行
         * 如果刚好碰到新年的 1月1号
         */
        public function create_table()
        {                          
            $last_year = date('Y')-1;
            $this_year_time = strtotime(date('Y-01-01')); // 获取今年的时间戳
            $t = Db::query("show tables like '%__PREFIX__order_$last_year'");
            // 存在这个年限的表说明可能已经执行过了, 不再执行
            if(count($t) > 0)
                return false;
            
            $order_id = M('order')->where("add_time >= $this_year_time")->min('order_id'); // 获取今年的第一笔订单id
                                                                                                
            // 第一步由于是凌晨过后执行的, 有可能把今年新的一年的订单也放入了 tp_order_this_year 了, 要删掉
            // 第二步先改表名 把今年内的 改成指定年的, 比如 改成tp_order_2017
            // 第三步再重新创建一张表结构一模一样的tp_order_this_year 表
            
            // 订单表
            $order_id && M('order_this_year')->where("order_id >= $order_id ")->delete();
            Db::execute("rename table __PREFIX__order_this_year to __PREFIX__order_$last_year");
            Db::execute("create table __PREFIX__order_this_year like __PREFIX__order");
            
            // 订单商品表
            $order_id && M('order_goods_this_year')->where("order_id >= $order_id ")->delete();
            Db::execute("rename table __PREFIX__order_goods_this_year to __PREFIX__order_goods_$last_year");
            Db::execute("create table __PREFIX__order_goods_this_year like __PREFIX__order_goods");
            
            //  订单日志操作表
            $order_id && M('order_action_this_year')->where("order_id >= $order_id ")->delete();
            Db::execute("rename table __PREFIX__order_action_this_year to __PREFIX__order_action_$last_year");
            Db::execute("create table __PREFIX__order_action_this_year like __PREFIX__order_action");
            
            //  发货单表
            $order_id && M('delivery_doc_this_year')->where("order_id >= $order_id ")->delete();
            Db::execute("rename table __PREFIX__delivery_doc_this_year to __PREFIX__delivery_doc_$last_year");
            Db::execute("create table __PREFIX__delivery_doc_this_year like __PREFIX__delivery_doc");
            
            //分销分成记录表
            $order_id && M('rebate_log_this_year')->where("order_id >= $order_id ")->delete();
            Db::execute("rename table __PREFIX__rebate_log_this_year to __PREFIX__rebate_log_$last_year");
            Db::execute("create table __PREFIX__rebate_log_this_year like __PREFIX__rebate_log");            
            echo "执行创建年度表....".PHP_EOL;
            
            // 把今天新的记录搬到刚刚新建的this_yeay 表里面去
            $this->move_3month(); 
        }
        
        /**
         * 生成触发器代码
         * 主从同步 主机的 binlog_format 建议使用 STATEMENT 这样log 日志会小很多, 但是 从库也要有触发器
         * 在执行创建触发器的时候 如果已经配置了主从, 主里面添加触发器从库里面自动加上
         */
        public function trigger()
        {
            $prefix =  C('database.prefix');            
            $table = ['order','order_goods','order_action','delivery_doc','rebate_log'];
            
            foreach ($table as $key => $val)
            {                 
                $val = $prefix.$val;
                $full_fields = Db::query("show fields from $val");
                $primary = $full_fields[0]['Field']; // 获取表id 主键, 一般第一个字段就是                                
                $insert_fields = $update_fields = '';  // 插入字段 和 更字段
                
                // 插入字段
                foreach($full_fields as $k => $v){
                    $insert_fields .= "new.{$v['Field']},";
                }
                $insert_fields = substr($insert_fields,0, -1);
                // 新增触发器        
                echo $insert_trigger = "
                                delimiter $$
                                drop trigger if exists `insert_$val`$$
                                create 
                                    trigger `insert_$val` after insert on `$val` 
                                    for each row begin
                                       insert  into {$val}_this_year values($insert_fields);
                                    end;
                                $$
                                delimiter ;";                
                echo PHP_EOL;                      
                
                // 删除触发器
                /*
                 * 暂时不做删除触发器, 这几张表不涉及删除
                echo $delete_trigger = "
                                delimiter $$
                                drop trigger if exists `delete_$val`$$
                                create 
                                    trigger `delete_$val` after delete on `$val` 
                                    for each row begin
                                        delete from `{$val}_this_year` where $primary = old.$primary;
                                    end;
                                $$
                                delimiter ;";                
                echo PHP_EOL;                 
                 */

                // 修改字段
                foreach($full_fields as $k => $v){
                    $update_fields .= "{$v['Field']} = new.{$v['Field']},";
                }
                $update_fields = substr($update_fields,0, -1);                
                // 修改触发器                
                echo $update_trigger = "
                                delimiter $$
                                drop trigger if exists `update_$val`$$
                                create 
                                    trigger `update_$val` after update on `$val` 
                                    for each row begin
                                        update {$val}_this_year set $update_fields where $primary = new.$primary;
                                    end;
                                $$
                                delimiter ;";                    
                echo PHP_EOL;                
            }
        }
        /**
         * 更新索引表
         */
        function update_table_index()
        {            
            $prefix =  C('database.prefix');    
            $years = buyYear();
             Db::execute("truncate table __PREFIX__table_index");
            foreach($years as $key => $val)
            {   
                $data = Db::execute("show tables like '%__PREFIX__order{$key}%'");
                if(empty($data))
                    continue;
                $order_min = M('order'.$key)->order("order_id asc")->find();                                            
                $order_max = M('order'.$key)->order("order_id desc")->find();            
                $index_data = [
                    'name'=>'order'.$key,
                    'min_id'=>$order_min['order_id'],
                    'max_id'=>$order_max['order_id'],
                    'min_order_sn'=>substr($order_min['order_sn'],0,14),
                    'max_order_sn'=>substr($order_max['order_sn'],0,14),
                ];
                if($key == '')
                    $max_id = 999999999; // order表的最大 id 默认为 10个亿-1 为9位数
                M('table_index')->insert($index_data);
            }
        }
        
        function  test()
        {
            //$table_index = M('table_index')->cache(true)->select();
            //echo getTabNameByOrderId('301');
            $table_index = getTabByTime('2017-03-01 00:43:00');
            print_r($table_index);
        }
}
/**
create table tp_order_this_year like tp_order;
create table tp_order_goods_this_year like tp_order_goods;
create table tp_order_action_this_year like tp_order_action;
create table tp_delivery_doc_this_year like tp_delivery_doc;
create table tp_rebate_log_this_year like tp_rebate_log; 
show tables like '%this_year%';
  
CREATE TABLE `tp_table_index` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '索引表自增id',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '表名字',
  `min_id` int(11) NOT NULL DEFAULT '0' COMMENT '表最小id',
  `max_id` int(11) NOT NULL DEFAULT '0' COMMENT '表最大id',
  `min_order_sn` varchar(20) NOT NULL DEFAULT '' COMMENT '最小订单编号',
  `max_order_sn` varchar(20) DEFAULT '' COMMENT '最大订单编号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8
 */