<?php

namespace WenRuns\Laravel;

use Encore\Admin\Facades\Admin;
use Illuminate\Support\ServiceProvider;

class LaravelServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(Laravel $extension)
    {
        if (!Laravel::boot()) {
            return;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'laravel-service');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/wenruns/laravel-service')],
                'laravel-service'
            );
        }

        $this->app->booted(function () {
            Laravel::routes(__DIR__.'/../routes/web.php');
        });
    }


    public function handle()
    {
        Admin::booting(function () {
            Admin::js('vendor/wenruns/laravel-service/layer/layer.js');
            Admin::js('vendor/wenruns/laravel-service/layer/mobile/layer.js');
            Admin::css('vendor/wenruns/laravel-service/layer/mobile/need/layer.css');
        });
    }
}
