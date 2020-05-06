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
 * Date: 2015-09-09
 */
namespace app\admin\logic;

use think\Model;
use think\Db;
/**
 * 分类逻辑定义
 * Class CatsLogic
 * @package Admin\Logic
 */
class ArticleCatLogic extends Model
{

    /**
     * 获得指定分类下的子分类的数组
     *
     * @access  public
     * @param   int     $cat_id     分类的ID
     * @param   int     $selected   当前选中分类的ID
     * @param   boolean $re_type    返回的类型: 值为真时返回下拉列表,否则返回数组
     * @param   int     $level      限定返回的级数。为0时返回所有级数
     * @return  mix
     */
    public function article_cat_list($cat_id = 0, $selected = 0, $re_type = true, $level = 0)
    {
        static $res = NULL;
    
        if ($res === NULL)
        {
            $data = false;//read_static_cache('art_cat_pid_releate');
            if ($data === false)
            {
            	$cat_type = I('cat_type');
            	if($cat_type == ""){
            		$where = " where 1=1 ";
            	}else{
            		$where = " where c.cat_type=".$cat_type;
            	}
                $sql = "SELECT c.*, COUNT(s.cat_id) AS has_children FROM __PREFIX__article_cat AS c".
                    " LEFT JOIN __PREFIX__article_cat AS s ON s.parent_id=c.cat_id $where ".
                    " GROUP BY c.cat_id  ORDER BY parent_id, sort_order";
                $res = DB::query($sql);
                //write_static_cache('art_cat_pid_releate', $res);
            }
            else
            {
                $res = $data;
            }
        }
    
        if (empty($res) == true)
        {
            return $re_type ? '' : array();
        }
    
        $options = $this->article_cat_options($cat_id, $res); // 获得指定分类下的子分类的数组
                
        /* 截取到指定的缩减级别 */
        if ($level > 0)
        {
            if ($cat_id == 0)
            {
                $end_level = $level;
            }
            else
            {
                $first_item = reset($options); // 获取第一个元素
                $end_level  = $first_item['level'] + $level;
            }
    
            /* 保留level小于end_level的部分 */
            foreach ($options AS $key => $val)
            {
                if ($val['level'] >= $end_level)
                {
                    unset($options[$key]);
                }
            }
        }
    
        $pre_key = 0;
        foreach ($options AS $key => $value)
        {
            $options[$key]['has_children'] = 1;
            if ($pre_key > 0)
            {
                if ($options[$pre_key]['cat_id'] == $options[$key]['parent_id'])
                {
                    $options[$pre_key]['has_children'] = 1;
                }
            }
            $pre_key = $key;
        }
    
        if ($re_type == true)
        {
            $select = '';
            foreach ($options AS $var)
            {
                $select .= '<option value="' . $var['cat_id'] . '" ';
                //$select .= ' cat_type="' . $var['cat_type'] . '" ';
                $select .= ($selected == $var['cat_id']) ? 'selected="selected"' : '';
                $select .= '>';
                if ($var['level'] > 0)
                {
                    $select .= str_repeat('&nbsp;', $var['level'] * 4);
                }
                $select .= htmlspecialchars(addslashes($var['cat_name'])) . '</option>';
            }
    
            return $select;
        }
        else
        {
            return $options;
        }
    }
    
    /**
     * 过滤和排序所有文章分类，返回一个带有缩进级别的数组
     *
     * @access  private
     * @param   int     $cat_id     上级分类ID
     * @param   array   $arr        含有所有分类的数组
     * @param   int     $level      级别
     * @return  void
     */
    public function article_cat_options($spec_cat_id, $arr)
    {	
        static $cat_options = array();
    
        if (isset($cat_options[$spec_cat_id]))
        {
            return $cat_options[$spec_cat_id];
        }
    
        if (!isset($cat_options[0]))
        {
            $level = $last_cat_id = 0;
            $options = $cat_id_array = $level_array = array();
            while (!empty($arr))
            {
                foreach ($arr AS $key => $value)
                {
                    $cat_id = $value['cat_id'];
                    if ($level == 0 && $last_cat_id == 0)
                    {
                        if ($value['parent_id'] > 0)
                        {
                            break;
                        }
    
                        $options[$cat_id]          = $value;
                        $options[$cat_id]['level'] = $level;
                        $options[$cat_id]['id']    = $cat_id;
                        $options[$cat_id]['name']  = $value['cat_name'];
                        unset($arr[$key]);
    
                        if ($value['has_children'] == 0)
                        {
                            continue;
                        }
                        $last_cat_id  = $cat_id;
                        $cat_id_array = array($cat_id);
                        $level_array[$last_cat_id] = ++$level;
                        continue;
                    }
    
                    if ($value['parent_id'] == $last_cat_id)
                    {
                        $options[$cat_id]          = $value;
                        $options[$cat_id]['level'] = $level;
                        $options[$cat_id]['id']    = $cat_id;
                        $options[$cat_id]['name']  = $value['cat_name'];
                        unset($arr[$key]);
    
                        if ($value['has_children'] > 0)
                        {
                            if (end($cat_id_array) != $last_cat_id)
                            {
                                $cat_id_array[] = $last_cat_id;
                            }
                            $last_cat_id    = $cat_id;
                            $cat_id_array[] = $cat_id;
                            $level_array[$last_cat_id] = ++$level;
                        }
                    }
                    elseif ($value['parent_id'] > $last_cat_id)
                    {
                        break;
                    }
                }
    
                $count = count($cat_id_array);
                if ($count > 1)
                {
                    $last_cat_id = array_pop($cat_id_array);
                }
                elseif ($count == 1)
                {
                    if ($last_cat_id != end($cat_id_array))
                    {
                        $last_cat_id = end($cat_id_array);
                    }
                    else
                    {
                        $level = 0;
                        $last_cat_id = 0;
                        $cat_id_array = array();
                        continue;
                    }
                }
    
                if ($last_cat_id && isset($level_array[$last_cat_id]))
                {
                    $level = $level_array[$last_cat_id];
                }
                else
                {
                    $level = 0;
                    break;
                }
            }
            $cat_options[0] = $options;
        }
        else
        {
            $options = $cat_options[0];
        }
    
        if (!$spec_cat_id)
        {
            return $options;
        }
        else
        {
            if (empty($options[$spec_cat_id]))
            {
                return array();
            }
    
            $spec_cat_id_level = $options[$spec_cat_id]['level'];
    
            foreach ($options AS $key => $value)
            {
                if ($key != $spec_cat_id)
                {
                    unset($options[$key]);
                }
                else
                {
                    break;
                }
            }
    
            $spec_cat_id_array = array();
            foreach ($options AS $key => $value)
            {
                if (($spec_cat_id_level == $value['level'] && $value['cat_id'] != $spec_cat_id) ||
                    ($spec_cat_id_level > $value['level']))
                {
                    break;
                }
                else
                {
                    $spec_cat_id_array[$key] = $value;
                }
            }
            $cat_options[$spec_cat_id] = $spec_cat_id_array;
    
            return $spec_cat_id_array;
        }
    }

    /**
     * @方法：将数据格式转换成树形结构数组
     * @param array $items 要进行转换的数组
     * return array $items 转换完成的数组
     */
    function getCatTree(Array $items) {
        $tree = array();
        foreach ($items as $item)
            if (isset($items[$item['pid']])) {
                $items[$item['pid']]['son'][] = &$items[$item['type_id']];
            } else {
                $tree[] = &$items[$item['type_id']];
            }
        return $tree;
    }

    /**
     * * 将树形结构数组输出
     * @param $items    要输出的数组
     * @param int $deep 顶级父节点id
     * @param int $type_id 已选中项
     * @return string
     */
    function exportTree($items, $deep = 0, $type_id = 0){
        foreach ($items as $item) {
            $select .= '<option value="' . $item['type_id'] . '" ';
            $select .= ($type_id == $item['type_id']) ? 'selected="selected">' : '>';
            if ($deep > 0) $select .= str_repeat('&nbsp;', $deep*4);
            $select .= '&nbsp;&nbsp;'.htmlspecialchars(addslashes($item['type_name'])).'</option>';
            if (!empty($item['son'])){
                $select .= $this->exportTree($item['son'], $deep+1,$type_id);
            }
        }
        return $select;
    }
}