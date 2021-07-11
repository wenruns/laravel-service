<?php

namespace WenRuns\Laravel;

use Encore\Admin\Extension;
<<<<<<< HEAD
use Encore\Admin\Facades\Admin;
=======
>>>>>>> adb22b581a67098d408abc23d6a26ccf56eef808

class Laravel extends Extension
{
    public $name = 'laravel-service';

<<<<<<< HEAD
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
=======
    public $views = __DIR__.'/../resources/views';

    public $assets = __DIR__.'/../resources/assets';

    public $menu = [
        'title' => 'Laravel',
        'path'  => 'laravel-service',
        'icon'  => 'fa-gears',
    ];
>>>>>>> adb22b581a67098d408abc23d6a26ccf56eef808
}