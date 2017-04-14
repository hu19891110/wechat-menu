<?php namespace addons\wechatmenu\controller;

use think\Validate;
use app\wefee\Tree;
use app\common\controller\Base;
use addons\wechatmenu\model\Menus;

class Index extends Base
{

    public function index()
    {
        $menus = Menus::where(['fid' => 0])->order('menu_sort', 'asc')->select();

        $menu = null;
        if (request()->param('menu_id')) {
            $menu = Menus::get(request()->param('menu_id'));
        }

        $title = '自定义微信菜单';

        return view(VIEW_PATH . '/index/index.html', compact('title', 'menu', 'menus'));
    }

    public function create()
    {
        $data = request()->only([
            'fid', 'menu_name', 'menu_type', 'menu_value',
        ]);

        $data = $this->validator($data);

        Menus::create($data);

        $this->success('操作成功');
    }

    public function update()
    {
        $menu = Menus::get(request()->param('menu_id'));

        if (! $menu) {
            $this->error('菜单不存在');
        }

        $data = $this->validator();

        $menu->save($data);

        $this->success('操作成功');
    }

    protected function validator()
    {
        $validator = new Validate([
            'fid|父菜单'         => 'require',
            'menu_sort|排序'     => 'require',
            'menu_name|菜单名'   => 'require',
            'menu_type|菜单类型' => 'require',
            'menu_value|菜单值'  => 'require',
        ]);

        $data = request()->only([
            'fid', 'menu_sort', 'menu_name', 'menu_type', 'menu_value',
        ]);

        if (! $validator->check($data)) {
            $this->error($validator->getError());
        }

        return $data;
    }

    public function delete()
    {
        $menu = Menus::get(request()->param('menu_id'));

        if ($menu->children()->count() > 0) {
            Menus::where(['fid' => $menu->id])->delete();
        }

        $menu->delete();

        $this->success('删除成功');
    }

    public function sync()
    {
        $menus = Menus::where(['fid' => 0])->order('menu_sort', 'asc')->select();

        if (! $menus) {
            return json(['error' => '请先添加菜单！']);
        }

        $container = [];

        foreach ($menus as $menu) {
            if ($menu->children()->count() == 0) {
                $key = $menu->menu_type == 'view' ? 'url' : 'click';
                $container[] = [
                    'name' => $menu->menu_name,
                    'type' => $menu->menu_type,
                    $key => $menu->menu_value,
                ];
                continue;
            }

            $tmp = [
                'name' => $menu->menu_name,
                'sub_button' => [],
            ];

            foreach ($menu->children()->order('menu_sort', 'asc')->select() as $item) {
                $key = $item->menu_type == 'view' ? 'url' : 'click';
                $subTemp = [
                    'name' => $item->menu_name,
                    'type' => $item->menu_type,
                    $key   => $item->menu_value,
                ];
                $tmp['sub_button'][] = $subTemp;
            }

            $container[] = $tmp;
        }

        try {
            $response = Tree::wechat()->menu->add($container);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        if ($response->errcode == 0) {
            return json(['success' => '推送成功']);
        }

        return json(['error' => $response->errmsg]);
    }

}