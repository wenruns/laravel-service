<?php

namespace WenRuns\Laravel;

use Encore\Admin\Extension;

class Laravel extends Extension
{
    public $name = 'laravel-service';

    public $views = __DIR__.'/../resources/views';

    public $assets = __DIR__.'/../resources/assets';

    public $menu = [
        'title' => 'Laravel',
        'path'  => 'laravel-service',
        'icon'  => 'fa-gears',
    ];
}