<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6
 * Time: 9:32
 */

namespace app\admin\model;


use think\Model;

class AdminSite extends Model
{
    public function admin()
    {
        return $this->hasOne('admin','site_id','site_id');
    }

    public function region()
    {
        return $this->hasOne('region','id','region_id');
    }

}