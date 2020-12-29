<?php


namespace Hanson\LaravelAdminWechat\Http\Controllers\Admin\OfficialAccount;


use Encore\Admin\Form;
use Hanson\LaravelAdminWechat\Facades\ConfigService;
use Hanson\LaravelAdminWechat\Http\Controllers\Admin\BaseController;
use Hanson\LaravelAdminWechat\Models\WechatConfig;

class MenuController extends BaseController
{
    protected $title = '菜单';

    protected function grid(bool $show = true)
    {
        if (!$show) {
            $app = ConfigService::getInstanceByAppId(request('app_id'));
            return $app->menu->create(json_decode(request('menu'), true)['menu']['button']);
        }

        $config = ConfigService::getCurrent();

        $app = ConfigService::getAdminCurrentApp();

        $menu = $app->menu->current();

        $form = new Form(new WechatConfig());

        $form->setAction('/'. config('admin.route.prefix') . '/wechat/official-account/menu');
        $selfmenuInfo = null;
        //修改sub_button结构
        if (isset($menu['selfmenu_info'])){
            foreach ($menu['selfmenu_info']['button'] as &$item){
                if (array_key_exists('sub_button', $item)){
                    $item['sub_button'] = $item['sub_button']['list'];
                }
            }
            $selfmenuInfo = $menu['selfmenu_info'];
        }
        $form->wechatMenu('menu', $config->name)->default($selfmenuInfo);
        $form->hidden('app_id')->default($config->app_id);

        $form->disableViewCheck()->disableEditingCheck()->disableCreatingCheck()->disableReset();

        return $form;
    }

    public function store()
    {
        $result = $this->grid(false);

        if ($result['errcode'] == 0) {
            admin_toastr('修改成功', 'success');
        } else {
            admin_toastr($result['errmsg'], 'error');
        }

        return redirect()->route('admin.wechat.menu');
    }
}
