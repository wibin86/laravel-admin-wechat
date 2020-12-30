<?php


namespace Hanson\LaravelAdminWechat\Http\Controllers\Admin;


use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Hanson\LaravelAdminWechat\Actions\DeleteConfig;
use Hanson\LaravelAdminWechat\Models\WechatConfig;
use Illuminate\Support\Facades\Cache;

class ConfigController extends AdminController
{
    protected $title = '微信配置';

    protected $description = [
        'index' => '公众号/小程序 配置',
    ];

    protected function grid()
    {
        $grid = new Grid(new WechatConfig);
        $grid->actions(function ($actions){
            $actions->disableDelete();
            $actions->add(new DeleteConfig());
        });
        $grid->column('id', __('ID'))->sortable();
        $grid->column('name', '名称');
        $grid->column('type_readable', '类型');
        $grid->column('app_id', 'APP ID');
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    protected function form()
    {
        $form = new Form(new WechatConfig());
        $form->tools(function (Form\Tools $tools) {
            // 去掉`删除`按钮
            $tools->disableDelete();
        });
        $form->text('name', '名称')->required();
        $form->radio('type', '类型')->default(1)->options([1 => '公众号', 2 => '小程序']);
        $form->text('app_id', 'App id')->required();
        $form->text('secret', '秘钥')->required();
        $form->text('token', 'Token')->help('公众号才需填写');
        $form->text('aes_key', 'Aes Key')->help('公众号才需填写');

        $form->saved(function (Form $form) {
            Cache::forever('wechat.config.app_id.'.$form->model()->app_id, ['app_id' => $form->model()->app_id, 'secret' => $form->model()->secret, 'type' => $form->model()->type]);
        });

        return $form;
    }

    protected function detail($id)
    {
        $show = new Show(WechatConfig::findOrFail($id));
        $show->panel()->tools(function ($tools) {
            $tools->disableDelete();
        });
        $show->field('name', '名称');
        $show->field('type', '类型')->using([1 => '公众号', 2 => '小程序']);
        $show->field('app_id', 'id');
        $show->field('secret', '秘钥');
        $show->field('token', 'Token');
        $show->field('aes_key', 'Aes Key');

        return $show;
    }
}
