<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-4-27
 * Time: 上午10:12
 * @author 郑钟良<zzl@ourstu.com>
 */


return array(
    //模块名
    'name' => 'Banner',
    //别名
    'alias' => 'Banner',
    //版本号
    'version' => '2.3.1',
    //是否商业模块,1是，0，否
    'is_com' => 0,
    //是否显示在导航栏内？  1是，0否
    'show_nav' => 1,
    //模块描述
    'summary' => '资讯模块，包含资讯和案例',
    //开发者
    'developer' => '嘉农',
    //开发者网站
    'website' => '',
    //前台入口，可用U函数
    'entry' => 'Banner/index/index',

    'admin_entry' => 'Admin/Banner/index',

    'icon' => 'rss-sign',

    'can_uninstall' => 1

);