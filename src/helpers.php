<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2021/7/4
 * Time: 15:48
 */

use \WenRuns\Laravel\Button;

if (!function_exists('buttons')) {
    function buttons(array $buttons, \Closure $clusre = null, $toString = true)
    {
        $html = $toString ? '' : [];
        if (isset($buttons[0])) {
            foreach ($buttons as $key => $item) {
                if (is_array($item)) {
                    $button = new Button(\Illuminate\Support\Arr::get($item, 'text'), $item);
                } else {
                    $button = new Button($item);
                }
                if (is_callable($clusre)) {
                    call_user_func($clusre, $button);
                }
                if ($toString) {
                    $html .= $button->render();
                } else {
                    $html[$key] = $button;
                }
            }
        } else {
            $button = new Button(\Illuminate\Support\Arr::get($buttons, 'text'), $buttons);
            if (is_callable($clusre)) {
                call_user_func($clusre, $button);
            }
            if ($toString) {
                $html = $button->render();
            } else {
                $html = $button;
            }
        }
        return $html;
    }
}

if (!function_exists('emptyPage')) {
    function emptyPage($selector = '.table-wrap.table-main')
    {
        \WenRuns\Laravel\Admin\GridService::showEmptyPage($selector);
    }
}