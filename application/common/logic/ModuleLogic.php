<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 */

namespace app\common\logic;


class ModuleLogic
{
    /**
     * 所有模块
     * @var array
     */
    public $modules = [];

    /**
     * 可见模块
     * @var array
     */
    public $showModules = [];

    public function getModules($onlyShow = true)
    {
        if ($this->modules) {
            return $onlyShow ? $this->showModules : $this->modules;
        }

        $isShow = Saas::instance()->isBaseUser() ? 1 : 0;
        $modules = [
            [
                'name'  => 'admin', 'title' => '平台后台', 'show' => 1,
                'privilege' => [
                    'system'=>'系统设置','content'=>'内容管理','goods'=>'商品中心','member'=>'会员中心',
                    'order'=>'订单中心','marketing'=>'营销推广','tools'=>'插件工具','count'=>'统计报表',
                    'weixin'=>'微信管理','store'=>'店铺管理','distribut'=>'分销管理','maintenance'=>'运营'
                ],
            ],
            [
                'name'  => 'seller', 'title' => '商家后台(销售商)', 'show' => 1,
                'privilege' => [
                    'goods'=>'商品管理','order'=>'订单物流','promtion'=>'促销管理','store'=>'店铺管理',
                    'service'=>'售后服务','charts'=>'统计报表','mesaage'=>'客服消息','seller'=>'账号管理',
                    'finance'=>'财务管理','distribut'=>'分销管理','maintenance'=>'运营','alibaba'=>'1688管理',
					'supplier'=>'供应商管理'
                ]
            ],
            [
                'name'  => 'home', 'title' => 'PC端', 'show' => $isShow,
                'privilege' => ['basic' => '首页', 'goods' => '商品'],
            ],
            [
                'name'  => 'mobile', 'title' => '手机端','show' => $isShow,
                'privilege' => ['basic' => '基础功能'],
            ],
            [
                'name'  => 'api', 'title' => 'api接口', 'show' => $isShow,
                'privilege' => ['basic' => '基础功能'],
            ],
            [
                'name'  => 'shop', 'title' => '门店', 'show' => $isShow,
                'privilege' => ['basic' => '基础功能'],
            ],
			[
                'name'  => 'supplier', 'title' => '商家后台(供应商)', 'show' => 1,
                'privilege' => [
                    'supplier_goods'=>'商品管理','supplier_order'=>'订单物流','supplier_seller'=>'账号管理','supplier_store'=>'店铺管理',
                    'supplier_service'=>'售后服务','supplier_charts'=>'统计报表','supplier_alibaba'=>'1688管理',
					'supplier_dealer'=>'销售商管理'
                ]
            ]
        ];

        $this->modules = $modules;
        foreach ($modules as $key => $module) {
            if (!$module['show']) {
                unset($modules[$key]);
            }
        }
        $this->showModules = $modules;

        return $onlyShow ? $this->showModules : $this->modules;
    }

    public function getModule($moduleIdx, $onlyShow = true)
    {
        if (!self::isModuleExist($moduleIdx, $onlyShow)) {
            return [];
        }

        $modules = $this->getModules($onlyShow);
        return $modules[$moduleIdx];
    }

    public function isModuleExist($moduleIdx, $onlyShow = true)
    {
        return key_exists($moduleIdx, $this->getModules($onlyShow));
    }

    public function getPrivilege($moduleIdx, $onlyShow = true)
    {
        $modules = $this->getModules($onlyShow);
        return $modules[$moduleIdx]['privilege'];
    }
}