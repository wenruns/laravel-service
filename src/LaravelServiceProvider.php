<?php

namespace WenRuns\Laravel;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Illuminate\Support\ServiceProvider;
use WenRuns\Laravel\Admin\Form\Field\MultiList\MultiList;

class LaravelServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(Laravel $extension)
    {
        $this->handle();

        if (!Laravel::boot()) {
            return;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'WenRuns');
            $this->loadViewsFrom($views.'/admin', 'WenAdmin');
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
            Form::extend('multiList', MultiList::class);
        });
    }
}
