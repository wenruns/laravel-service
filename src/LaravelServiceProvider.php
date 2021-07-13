<?php

namespace WenRuns\Laravel;

use Encore\Admin\Form;
use Illuminate\Support\ServiceProvider;
use WenRuns\Laravel\Admin\Form\Field\ApiSelect;
use WenRuns\Laravel\Admin\Form\Field\CheckboxTree;
use WenRuns\Laravel\Admin\Form\Field\InputSelect;
use WenRuns\Laravel\Admin\Form\Field\MultiCheckbox;
use WenRuns\Laravel\Admin\Form\Field\MultiList\MultiList;
use WenRuns\Laravel\Admin\Form\Field\Tabs;


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
            $this->loadViewsFrom($views, 'WenRuns');
            $this->loadViewsFrom($views . '/admin', 'WenAdmin');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path(trim(Laravel::$assetLoadRoot, '/'))],
                'laravel-service'
            );
        }

        $this->app->booted(function () {
            Laravel::routes(__DIR__ . '/../routes/web.php');
        });

        $this->handle();
    }


    public function handle()
    {
        Form::extend('multiList', MultiList::class);
        Form::extend('wenTab', Tabs::class);
        Form::extend('apiSelect', ApiSelect::class);
        Form::extend('checkboxTree', CheckboxTree::class);
        Form::extend('inputSelect', InputSelect::class);
        Form::extend('multiCheckbox', MultiCheckbox::class);
    }
}
