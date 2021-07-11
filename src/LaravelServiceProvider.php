<?php

namespace WenRuns\Laravel;

<<<<<<< HEAD
use Encore\Admin\Form;
use Illuminate\Support\ServiceProvider;
use WenRuns\Laravel\Admin\Form\Field\ApiSelect;
use WenRuns\Laravel\Admin\Form\Field\CheckboxTree;
use WenRuns\Laravel\Admin\Form\Field\InputSelect;
use WenRuns\Laravel\Admin\Form\Field\MultiCheckbox;
use WenRuns\Laravel\Admin\Form\Field\MultiList\MultiList;
use WenRuns\Laravel\Admin\Form\Field\Tabs;
=======
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Illuminate\Support\ServiceProvider;
use WenRuns\Laravel\Admin\Form\Field\MultiList\MultiList;
>>>>>>> adb22b581a67098d408abc23d6a26ccf56eef808

class LaravelServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(Laravel $extension)
    {
<<<<<<< HEAD
=======
        $this->handle();
>>>>>>> adb22b581a67098d408abc23d6a26ccf56eef808

        if (!Laravel::boot()) {
            return;
        }
<<<<<<< HEAD
        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'WenRuns');
            $this->loadViewsFrom($views . '/admin', 'WenAdmin');
=======

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'WenRuns');
            $this->loadViewsFrom($views.'/admin', 'WenAdmin');
>>>>>>> adb22b581a67098d408abc23d6a26ccf56eef808
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
<<<<<<< HEAD
                [$assets => public_path(trim(Laravel::$assetLoadRoot, '/'))],
=======
                [$assets => public_path('vendor/wenruns/laravel-service')],
>>>>>>> adb22b581a67098d408abc23d6a26ccf56eef808
                'laravel-service'
            );
        }

        $this->app->booted(function () {
<<<<<<< HEAD
            Laravel::routes(__DIR__ . '/../routes/web.php');
        });

        $this->handle();
=======
            Laravel::routes(__DIR__.'/../routes/web.php');
        });
>>>>>>> adb22b581a67098d408abc23d6a26ccf56eef808
    }


    public function handle()
    {
<<<<<<< HEAD
        Form::extend('multiList', MultiList::class);
        Form::extend('tabs', Tabs::class);
        Form::extend('apiSelect', ApiSelect::class);
        Form::extend('checkboxTree', CheckboxTree::class);
        Form::extend('inputSelect', InputSelect::class);
        Form::extend('multiCheckbox', MultiCheckbox::class);
=======
        Admin::booting(function () {
            Form::extend('multiList', MultiList::class);
        });
>>>>>>> adb22b581a67098d408abc23d6a26ccf56eef808
    }
}
