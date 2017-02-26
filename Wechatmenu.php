<?php namespace addons\wechatmenu;

use think\Db;
use Qsnh\think\Addons\Addons;

class Wechatmenu extends Addons
{

    public function up()
    {
        Db::execute('DROP TABLE IF EXISTS '.full_table('menus').';');
        Db::execute('
        CREATE TABLE '.full_table('menus').' (
        `id` int(11) unsigned not null AUTO_INCREMENT,
        `fid` int(11) unsigned not null,
        `menu_sort` smallint(5) unsigned default 0 not null comment "升序",
        `menu_name` varchar(32) not null default "" comment "菜单名",
        `menu_type` varchar(12) not null default "" comment "view链接click事件",
        `menu_value` varchar(255) not null default "" comment "菜单值",
        PRIMARY KEY(`id`)
        )ENGINE=MyISAM DEFAULT CHARSET=utf8;
        ');
    }

    public function down()
    {
        Db::execute('DROP TABLE IF EXISTS '.full_table('menus').';');
    }

    public function upgrade()
    {
        //
    }

}