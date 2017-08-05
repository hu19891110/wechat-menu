<?php namespace addons\wechatmenu\hook\model;

class Hook
{

    /**
     * 注册Wefee二级菜单
     * @return void
     */
    public function appBegin()
    {
        $menus = config('WEFEE_SECOND_MENUS');
        $menus = $menus?:[];
        $our = [
            [
                'title' => '公众号菜单',
                'href' => aurl('WechatMenu'),
            ]
        ];
        $menus = array_merge($menus, $our);
        config(['WEFEE_SECOND_MENUS' => $menus]);
    }

}