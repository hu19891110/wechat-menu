<?php namespace addons\wechatmenu\model;

use think\Model;

class Menus extends Model
{

    public function children()
    {
        return $this->hasMany('addons\wechatmenu\model\Menus', 'fid');
    }

    public function parent()
    {
        return $this->belongsTo('addons\wechatmenu\model\Menus', 'fid');
    }

}