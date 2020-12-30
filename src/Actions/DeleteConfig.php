<?php

namespace Hanson\LaravelAdminWechat\Actions;

use Encore\Admin\Actions\RowAction;
use Hanson\LaravelAdminWechat\Models\WechatConfig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class DeleteConfig extends RowAction
{
    public $name = '删除';

    public function handle(Model $model)
    {
        $key = config('admin.extensions.wechat.admin_current_key', 'wechat.admin.current');
        Cache::forget($key);
        Cache::forget('wechat.config.app_id.' . $model->app_id);
        WechatConfig::where('id', $model->id)->delete();
        return $this->response()->success('删除成功')->refresh();
    }

    public function dialog()
    {
        $this->confirm('确定删除？');
    }
}
