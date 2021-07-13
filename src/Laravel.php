<?php

namespace WenRuns\Laravel;

use Encore\Admin\Extension;
use Encore\Admin\Facades\Admin;

class Laravel extends Extension
{
    public $name = 'laravel-service';

    public $views = __DIR__ . '/../resources/views';

    public $assets = __DIR__ . '/../resources/assets';

    public $menu = [
        'title' => 'Laravel',
        'path' => 'laravel-service',
        'icon' => 'fa-gears',
    ];

    public static $hadLoadFiles = [];

    public static $assetLoadRoot = '/vendor/wenruns/laravel-service/';


    public static function loadJs($file)
    {
        if (in_array($file, self::$hadLoadFiles)) {
            return;
        }
        self::$hadLoadFiles[] = $file;
        Admin::js(self::makeAssetLoadUrl($file));
    }

    public static function loadCss($file)
    {
        if (in_array($file, self::$hadLoadFiles)) {
            return;
        }
        self::$hadLoadFiles[] = $file;
        Admin::css(self::makeAssetLoadUrl($file));
    }

    public static function makeAssetLoadUrl($file)
    {
        return self::$assetLoadRoot . trim($file, '/') . '?v=' . mt_rand(0, 9999);
    }
}